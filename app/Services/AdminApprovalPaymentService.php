<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessPlan;
use App\Models\MatrimonyPlan;
use App\Models\MatrimonyProfile;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminApprovalPaymentService
{
    public function approveMatrimony(MatrimonyProfile $profile, MatrimonyPlan $plan, int $adminId): Payment
    {
        return DB::transaction(function () use ($profile, $plan, $adminId) {
            $profile = MatrimonyProfile::query()->lockForUpdate()->findOrFail($profile->id);
            $this->ensureNoCompletedPayment($profile);
            $this->ensureActivePlan($plan);

            $approvedAt = now();
            $profile->update([
                'approval_status' => 'approved',
                'approved_at' => $approvedAt,
                'approved_by' => $adminId,
                'profile_expires_at' => $approvedAt->copy()->addYears((int) $plan->duration_years),
            ]);

            return $this->createPayment(
                $profile->user_id,
                'matrimony_subscription',
                $plan->price,
                $plan->id,
                $plan->plan_name ?: 'Matrimony plan',
                $plan->duration_years,
                $adminId
            );
        });
    }

    public function approveBusiness(Business $business, BusinessPlan $plan, int $adminId): Payment
    {
        return DB::transaction(function () use ($business, $plan, $adminId) {
            $business = Business::query()->lockForUpdate()->findOrFail($business->id);
            $this->ensureNoCompletedPayment($business);
            $this->ensureActivePlan($plan);

            $approvedAt = now();
            $business->update([
                'verification_status' => 'approved',
                'verified_at' => $approvedAt,
                'verified_by' => $adminId,
                'subscription_status' => 'active',
                'subscription_expires_at' => $approvedAt->copy()->addYears((int) $plan->duration_years),
            ]);

            return $this->createPayment(
                $business->user_id,
                'business_registration',
                $plan->price,
                $plan->id,
                $plan->company_type,
                $plan->duration_years,
                $adminId
            );
        });
    }

    private function ensureNoCompletedPayment(MatrimonyProfile|Business $record): void
    {
        $paymentStatus = $record instanceof MatrimonyProfile
            ? $record->matrimony_payment_status
            : $record->business_payment_status;

        if ($paymentStatus === 'paid') {
            throw ValidationException::withMessages([
                'payment' => 'A successful payment already exists for this user.',
            ]);
        }
    }

    private function ensureActivePlan(MatrimonyPlan|BusinessPlan $plan): void
    {
        if (! $plan->active || (int) $plan->duration_years < 1) {
            throw ValidationException::withMessages([
                'plan_id' => 'The selected subscription plan is not active.',
            ]);
        }
    }

    private function createPayment(
        int $userId,
        string $paymentType,
        mixed $amount,
        int $planId,
        string $planName,
        int $durationYears,
        int $adminId
    ): Payment {
        $reference = 'ADMIN-'.now()->format('YmdHis').'-'.Str::upper(Str::random(8));

        $this->completeTransaction(
            $userId,
            $paymentType === 'matrimony_subscription' ? 'matrimony_profile' : 'business_registration',
            $reference,
            $amount,
            $planId,
            $planName,
            $durationYears,
            $adminId
        );

        return Payment::create([
            'user_id' => $userId,
            'payment_id' => $reference,
            'order_id' => $reference,
            'payment_type' => $paymentType,
            'amount' => $amount,
            'currency' => 'INR',
            'status' => 'completed',
            'payment_method' => 'other',
            'metadata' => [
                'approved_by_admin' => true,
                'approved_by' => $adminId,
                'plan_id' => $planId,
                'plan_name' => $planName,
                'duration_years' => $durationYears,
            ],
            'description' => $planName.' - approved with payment by admin',
            'paid_at' => now(),
            'receipt_number' => $reference,
        ]);
    }

    /**
     * Reconcile the legacy transactions ledger with the completed admin payment.
     * Reuse the latest failed/pending attempt for this user and purpose so an
     * abandoned gateway attempt does not remain failed after admin approval.
     */
    private function completeTransaction(
        int $userId,
        string $purpose,
        string $reference,
        mixed $amount,
        int $planId,
        string $planName,
        int $durationYears,
        int $adminId
    ): Transaction {
        $transaction = Transaction::query()
            ->where('user_id', $userId)
            ->where('purpose', $purpose)
            ->whereIn('status', ['pending', 'failed'])
            ->latest('id')
            ->lockForUpdate()
            ->first();

        $metadata = array_merge($transaction?->metadata ?? [], [
            'approved_by_admin' => true,
            'approved_by' => $adminId,
            'plan_id' => $planId,
            'plan_name' => $planName,
            'duration_years' => $durationYears,
        ]);

        $values = [
            'user_id' => $userId,
            'amount' => $amount,
            'currency' => 'INR',
            'purpose' => $purpose,
            'razorpay_payment_id' => $reference,
            'status' => 'completed',
            'subscription_period' => $durationYears * 12,
            'metadata' => $metadata,
        ];

        if ($transaction) {
            $transaction->update($values);

            return $transaction->fresh();
        }

        return Transaction::create($values + ['razorpay_order_id' => $reference]);
    }
}
