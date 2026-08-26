<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessPlan;
use App\Models\Donation;
use App\Models\MatrimonyPlan;
use App\Models\MatrimonyProfile;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CCAvenuePaymentService
{
    /**
     * Create the local audit row before the customer leaves for CCAvenue.
     */
    public function createPendingPayment(Transaction $transaction): Payment
    {
        $status = match ($transaction->status) {
            'completed' => 'completed',
            'failed' => 'failed',
            'refunded' => 'refunded',
            default => 'pending',
        };

        return Payment::firstOrCreate(
            ['transaction_id' => (string) $transaction->id],
            [
                'user_id' => $transaction->user_id,
                // CCAvenue does not issue a tracking ID until it processes the order.
                'payment_id' => $transaction->razorpay_payment_id
                    ?: $this->pendingPaymentId($transaction->razorpay_order_id),
                'order_id' => $transaction->razorpay_order_id,
                'payment_type' => $this->paymentType($transaction),
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'payment_method' => 'other',
                'status' => $status,
                'paid_at' => $status === 'completed' ? $transaction->updated_at : null,
                'metadata' => [
                    'ccavenue_order_id' => $transaction->razorpay_order_id,
                    'subscription_period' => $transaction->subscription_period,
                    'original_purpose' => $transaction->purpose,
                ],
            ]
        );
    }

    /**
     * Apply an encrypted CCAvenue callback idempotently.
     *
     * @return array{transaction: Transaction, payment: Payment, status: string}
     */
    public function processCallback(array $params): array
    {
        $orderId = trim((string) ($params['order_id'] ?? ''));
        if ($orderId === '') {
            throw ValidationException::withMessages(['order_id' => 'CCAvenue response is missing order_id.']);
        }

        return DB::transaction(function () use ($params, $orderId) {
            $transaction = Transaction::where('razorpay_order_id', $orderId)
                ->lockForUpdate()
                ->first();

            if (! $transaction) {
                throw ValidationException::withMessages(['order_id' => 'Transaction record not found.']);
            }

            $this->validateSuccessfulAmount($transaction, $params);
            $callbackStatus = $this->callbackStatus($params['order_status'] ?? null);
            $wasCompleted = $transaction->status === 'completed';
            $payment = Payment::where('transaction_id', (string) $transaction->id)
                ->lockForUpdate()
                ->first() ?? $this->createPendingPayment($transaction);

            $commonPaymentData = [
                'order_id' => $orderId,
                'payment_type' => $this->paymentType($transaction),
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'payment_method' => $this->paymentMethod($params['payment_mode'] ?? ''),
                'razorpay_response' => $params,
            ];

            // Heal older partially-processed callbacks, but never activate twice or
            // downgrade an order which has already reached its final success state.
            if ($wasCompleted) {
                if ($callbackStatus === 'completed') {
                    $trackingId = trim((string) ($params['tracking_id'] ?? $transaction->razorpay_payment_id ?? ''));
                    $payment->update($commonPaymentData + [
                        'payment_id' => $trackingId !== '' ? $trackingId : $payment->payment_id,
                        'status' => 'completed',
                        'paid_at' => $payment->paid_at ?? now(),
                    ]);
                }

                return [
                    'transaction' => $transaction,
                    'payment' => $payment->fresh(),
                    'status' => 'completed',
                ];
            }

            if ($callbackStatus === 'completed') {
                $trackingId = trim((string) ($params['tracking_id'] ?? ''));
                if ($trackingId === '') {
                    throw ValidationException::withMessages(['tracking_id' => 'Successful CCAvenue response is missing tracking_id.']);
                }

                $transaction->update([
                    'razorpay_payment_id' => $trackingId,
                    'status' => 'completed',
                ]);
                $payment->update($commonPaymentData + [
                    'payment_id' => $trackingId,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);
                $this->completePurpose($transaction);
            } elseif ($callbackStatus === 'failed') {
                $transaction->update(['status' => 'failed']);
                $payment->update($commonPaymentData + ['status' => 'failed']);
                $this->failDonation($transaction);
            } else {
                // Awaited/pending payments can be confirmed by a later callback.
                $payment->update($commonPaymentData + ['status' => 'pending']);
            }

            return [
                'transaction' => $transaction->fresh(),
                'payment' => $payment->fresh(),
                'status' => $callbackStatus,
            ];
        }, 3);
    }

    private function validateSuccessfulAmount(Transaction $transaction, array $params): void
    {
        if ($this->callbackStatus($params['order_status'] ?? null) !== 'completed') {
            return;
        }

        $amount = $params['amount'] ?? null;
        $currency = strtoupper(trim((string) ($params['currency'] ?? '')));
        if (! is_numeric($amount) || abs((float) $amount - (float) $transaction->amount) > 0.009) {
            throw ValidationException::withMessages(['amount' => 'CCAvenue amount does not match the order.']);
        }
        if ($currency !== strtoupper($transaction->currency)) {
            throw ValidationException::withMessages(['currency' => 'CCAvenue currency does not match the order.']);
        }
    }

    private function callbackStatus(?string $status): string
    {
        return match (strtolower(trim((string) $status))) {
            'success' => 'completed',
            'failure', 'failed', 'aborted', 'invalid', 'cancelled', 'canceled' => 'failed',
            default => 'pending',
        };
    }

    private function paymentType(Transaction $transaction): string
    {
        return match ($transaction->purpose) {
            'business_registration' => 'business_registration',
            'matrimony_profile' => 'matrimony_subscription',
            'donation' => 'donation',
            default => 'other',
        };
    }

    private function paymentMethod(string $method): string
    {
        $method = strtolower($method);

        return match (true) {
            str_contains($method, 'upi') => 'upi',
            str_contains($method, 'card'), str_contains($method, 'credit'), str_contains($method, 'debit') => 'card',
            str_contains($method, 'net'), str_contains($method, 'banking') => 'netbanking',
            str_contains($method, 'wallet') => 'wallet',
            default => 'other',
        };
    }

    private function pendingPaymentId(string $orderId): string
    {
        return 'pending:'.$orderId;
    }

    private function completePurpose(Transaction $transaction): void
    {
        if ($transaction->purpose === 'business_registration') {
            $business = Business::where('user_id', $transaction->user_id)->latest()->first();
            $business?->update([
                'subscription_status' => 'active',
                'subscription_expires_at' => now()->addMonths($this->subscriptionMonths($transaction)),
            ]);

            return;
        }

        if ($transaction->purpose === 'matrimony_profile') {
            $profile = MatrimonyProfile::where('user_id', $transaction->user_id)->first();
            $profile?->update([
                'profile_expires_at' => now()->addMonths($this->subscriptionMonths($transaction)),
                'approval_status' => 'approved',
            ]);

            return;
        }

        if ($transaction->purpose === 'donation') {
            $donation = Donation::where('razorpay_order_id', $transaction->razorpay_order_id)->first();
            if ($donation) {
                $donation->update([
                    'razorpay_payment_id' => $transaction->razorpay_payment_id,
                    'status' => 'completed',
                    'payment_method' => 'ccavenue',
                ]);
                $donation->cause?->updateRaisedAmount();
            }
        }
    }

    private function subscriptionMonths(Transaction $transaction): int
    {
        $planId = $transaction->metadata['plan_id'] ?? null;

        if ($planId) {
            $plan = $transaction->purpose === 'business_registration'
                ? BusinessPlan::find($planId)
                : MatrimonyPlan::find($planId);

            if ($plan && (int) $plan->duration_years > 0) {
                return (int) $plan->duration_years * 12;
            }
        }

        return max(1, (int) ($transaction->subscription_period
            ?? ($transaction->purpose === 'matrimony_profile' ? 12 : 1)));
    }

    private function failDonation(Transaction $transaction): void
    {
        if ($transaction->purpose === 'donation') {
            Donation::where('razorpay_order_id', $transaction->razorpay_order_id)
                ->where('status', '!=', 'completed')
                ->update(['status' => 'failed']);
        }
    }
}
