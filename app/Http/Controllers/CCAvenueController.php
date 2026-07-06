<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Business;
use App\Models\MatrimonyProfile;
use App\Services\CCAvenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CCAvenueController extends Controller
{
    private $ccavenue;

    public function __construct(CCAvenue $ccavenue)
    {
        $this->ccavenue = $ccavenue;
    }

    public function handleCallback(Request $request)
    {
        if (!$request->has('encResp')) {
            Log::error('CCAvenue callback missing encResp');
            return redirect()->route('dashboard')->with('error', 'Payment response missing.');
        }

        try {
            $decryptedParams = $this->ccavenue->decrypt($request->encResp);
            Log::info('CCAvenue Callback Params:', $decryptedParams);

            $orderId = $decryptedParams['order_id'] ?? null;
            $trackingId = $decryptedParams['tracking_id'] ?? null; // CCAvenue transaction ID
            $orderStatus = $decryptedParams['order_status'] ?? null;

            if (!$orderId) {
                Log::error('CCAvenue callback response missing order_id');
                return redirect()->route('dashboard')->with('error', 'Invalid payment response.');
            }

            $isSuccess = (strcasecmp($orderStatus, 'Success') === 0);

            // 1. Donation Payment Flow
            if (strpos($orderId, 'DON-') === 0) {
                $donation = Donation::where('razorpay_order_id', $orderId)->first();
                if (!$donation) {
                    Log::error('Donation not found for order ID: ' . $orderId);
                    return redirect()->route('dashboard')->with('error', 'Donation record not found.');
                }

                if ($isSuccess) {
                    $donation->update([
                        'razorpay_payment_id' => $trackingId,
                        'status' => 'completed',
                        'payment_method' => 'ccavenue'
                    ]);

                    $transaction = Transaction::where('razorpay_order_id', $orderId)->first();
                    if ($transaction) {
                        $transaction->update([
                            'razorpay_payment_id' => $trackingId,
                            'status' => 'completed'
                        ]);
                    }

                    $donation->cause->updateRaisedAmount();
                    return redirect()->route('dashboard')->with('success', 'Thank you! Your donation was completed successfully.');
                } else {
                    $donation->update(['status' => 'failed']);
                    $transaction = Transaction::where('razorpay_order_id', $orderId)->first();
                    if ($transaction) {
                        $transaction->update(['status' => 'failed']);
                    }
                    return redirect()->route('dashboard')->with('error', 'Donation payment failed/cancelled.');
                }
            }

            // 2. Business Plan Payment Flow
            if (strpos($orderId, 'BIZ-') === 0) {
                $transaction = Transaction::where('razorpay_order_id', $orderId)->first();
                if (!$transaction) {
                    Log::error('Business transaction not found for order ID: ' . $orderId);
                    return redirect()->route('dashboard.business.index')->with('error', 'Transaction record not found.');
                }

                if ($isSuccess) {
                    $transaction->update([
                        'razorpay_payment_id' => $trackingId,
                        'status' => 'completed',
                    ]);

                    // Create Payment record
                    $existingPayment = Payment::where('transaction_id', $transaction->id)->first();
                    if (!$existingPayment) {
                        Payment::create([
                            'user_id' => $transaction->user_id,
                            'transaction_id' => $transaction->id,
                            'payment_id' => $trackingId,
                            'order_id' => $orderId,
                            'payment_type' => 'business_registration',
                            'amount' => $transaction->amount,
                            'currency' => $transaction->currency,
                            'payment_method' => 'ccavenue',
                            'status' => 'completed',
                            'metadata' => json_encode([
                                'ccavenue_order_id' => $orderId,
                                'subscription_period' => $transaction->subscription_period ?? 1,
                                'original_purpose' => $transaction->purpose
                            ]),
                            'paid_at' => now(),
                            'razorpay_response' => json_encode($decryptedParams)
                        ]);
                    }

                    // Activate business
                    $business = Business::where('user_id', $transaction->user_id)
                        ->latest()
                        ->first();

                    if ($business) {
                        $expiresAt = now()->addMonths($transaction->subscription_period ?? 1);
                        $business->update([
                            'subscription_status' => 'active',
                            'subscription_expires_at' => $expiresAt,
                        ]);
                    }

                    return redirect()->route('dashboard.business.index')->with('success', 'Subscription activated successfully!');
                } else {
                    $transaction->update(['status' => 'failed']);
                    return redirect()->route('dashboard.business.index')->with('error', 'Business subscription payment failed/cancelled.');
                }
            }

            // 3. Matrimony Plan Payment Flow
            if (strpos($orderId, 'MAT-') === 0) {
                $transaction = Transaction::where('razorpay_order_id', $orderId)->first();
                if (!$transaction) {
                    Log::error('Matrimony transaction not found for order ID: ' . $orderId);
                    return redirect()->route('matrimony.index')->with('error', 'Transaction record not found.');
                }

                if ($isSuccess) {
                    $transaction->update([
                        'razorpay_payment_id' => $trackingId,
                        'status' => 'completed',
                    ]);

                    // Create Payment record
                    $existingPayment = Payment::where('transaction_id', $transaction->id)->first();
                    if (!$existingPayment) {
                        Payment::create([
                            'user_id' => $transaction->user_id,
                            'transaction_id' => $transaction->id,
                            'payment_id' => $trackingId,
                            'order_id' => $orderId,
                            'payment_type' => 'matrimony_subscription',
                            'amount' => $transaction->amount,
                            'currency' => $transaction->currency,
                            'payment_method' => 'ccavenue',
                            'status' => 'completed',
                            'metadata' => json_encode([
                                'ccavenue_order_id' => $orderId,
                                'subscription_period' => $transaction->subscription_period ?? 12,
                                'original_purpose' => $transaction->purpose
                            ]),
                            'paid_at' => now(),
                            'razorpay_response' => json_encode($decryptedParams)
                        ]);
                    }

                    // Activate matrimony profile
                    $profile = MatrimonyProfile::where('user_id', $transaction->user_id)->first();
                    if ($profile) {
                        $profile->update([
                            'profile_expires_at' => now()->addMonths($transaction->subscription_period ?? 12),
                            'approval_status' => 'approved',
                        ]);
                    }

                    return redirect()->route('matrimony.index')->with('success', 'Matrimony plan activated successfully!');
                } else {
                    $transaction->update(['status' => 'failed']);
                    return redirect()->route('matrimony.index')->with('error', 'Matrimony subscription payment failed/cancelled.');
                }
            }

            Log::warning('Unknown CCAvenue order prefix: ' . $orderId);
            return redirect()->route('dashboard')->with('error', 'Payment processed, but order type is unrecognized.');

        } catch (\Exception $e) {
            Log::error('CCAvenue Callback Exception: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'An error occurred while processing the payment response: ' . $e->getMessage());
        }
    }
}
