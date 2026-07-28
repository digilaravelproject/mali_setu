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
            return redirect()->route('payment.redirect-back', ['status' => 'failed', 'message' => 'Payment response missing.']);
        }

        try {
            $decryptedParams = $this->ccavenue->decrypt($request->encResp);
            Log::info('CCAvenue Callback Params:', $decryptedParams);

            $orderId = $decryptedParams['order_id'] ?? null;
            $trackingId = $decryptedParams['tracking_id'] ?? null; // CCAvenue transaction ID
            $orderStatus = $decryptedParams['order_status'] ?? null;

            if (!$orderId) {
                Log::error('CCAvenue callback response missing order_id');
                return redirect()->route('payment.redirect-back', ['status' => 'failed', 'message' => 'Invalid payment response.']);
            }

            $isSuccess = (strcasecmp($orderStatus, 'Success') === 0);

            // 1. Donation Payment Flow
            if (strpos($orderId, 'DON-') === 0) {
                $donation = Donation::where('razorpay_order_id', $orderId)->first();
                if (!$donation) {
                    Log::error('Donation not found for order ID: ' . $orderId);
                    return redirect()->route('payment.redirect-back', ['order_id' => $orderId, 'status' => 'failed', 'message' => 'Donation record not found.']);
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
                    return redirect()->route('payment.redirect-back', ['order_id' => $orderId, 'status' => 'success']);
                } else {
                    $donation->update(['status' => 'failed']);
                    $transaction = Transaction::where('razorpay_order_id', $orderId)->first();
                    if ($transaction) {
                        $transaction->update(['status' => 'failed']);
                    }
                    return redirect()->route('payment.redirect-back', ['order_id' => $orderId, 'status' => 'failed', 'message' => 'Donation payment failed/cancelled.']);
                }
            }

            // 2. Business Plan Payment Flow
            if (strpos($orderId, 'BIZ-') === 0) {
                $transaction = Transaction::where('razorpay_order_id', $orderId)->first();
                if (!$transaction) {
                    Log::error('Business transaction not found for order ID: ' . $orderId);
                    return redirect()->route('payment.redirect-back', ['order_id' => $orderId, 'status' => 'failed', 'message' => 'Transaction record not found.']);
                }

                if ($isSuccess) {
                    $transaction->update([
                        'razorpay_payment_id' => $trackingId,
                        'status' => 'completed',
                    ]);

                    // Map payment method
                    $rawPaymentMethod = strtolower($decryptedParams['payment_mode'] ?? '');
                    $paymentMethod = 'other';
                    if (strpos($rawPaymentMethod, 'upi') !== false) {
                        $paymentMethod = 'upi';
                    } elseif (strpos($rawPaymentMethod, 'card') !== false || strpos($rawPaymentMethod, 'credit') !== false || strpos($rawPaymentMethod, 'debit') !== false) {
                        $paymentMethod = 'card';
                    } elseif (strpos($rawPaymentMethod, 'net') !== false || strpos($rawPaymentMethod, 'banking') !== false) {
                        $paymentMethod = 'netbanking';
                    } elseif (strpos($rawPaymentMethod, 'wallet') !== false) {
                        $paymentMethod = 'wallet';
                    }

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
                            'payment_method' => $paymentMethod,
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

                    return redirect()->route('payment.redirect-back', ['order_id' => $orderId, 'status' => 'success']);
                } else {
                    $transaction->update(['status' => 'failed']);
                    return redirect()->route('payment.redirect-back', ['order_id' => $orderId, 'status' => 'failed', 'message' => 'Business subscription payment failed/cancelled.']);
                }
            }

            // 3. Matrimony Plan Payment Flow
            if (strpos($orderId, 'MAT-') === 0) {
                $transaction = Transaction::where('razorpay_order_id', $orderId)->first();
                if (!$transaction) {
                    Log::error('Matrimony transaction not found for order ID: ' . $orderId);
                    return redirect()->route('payment.redirect-back', ['order_id' => $orderId, 'status' => 'failed', 'message' => 'Transaction record not found.']);
                }

                if ($isSuccess) {
                    $transaction->update([
                        'razorpay_payment_id' => $trackingId,
                        'status' => 'completed',
                    ]);

                    // Map payment method
                    $rawPaymentMethod = strtolower($decryptedParams['payment_mode'] ?? '');
                    $paymentMethod = 'other';
                    if (strpos($rawPaymentMethod, 'upi') !== false) {
                        $paymentMethod = 'upi';
                    } elseif (strpos($rawPaymentMethod, 'card') !== false || strpos($rawPaymentMethod, 'credit') !== false || strpos($rawPaymentMethod, 'debit') !== false) {
                        $paymentMethod = 'card';
                    } elseif (strpos($rawPaymentMethod, 'net') !== false || strpos($rawPaymentMethod, 'banking') !== false) {
                        $paymentMethod = 'netbanking';
                    } elseif (strpos($rawPaymentMethod, 'wallet') !== false) {
                        $paymentMethod = 'wallet';
                    }

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
                            'payment_method' => $paymentMethod,
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

                    return redirect()->route('payment.redirect-back', ['order_id' => $orderId, 'status' => 'success']);
                } else {
                    $transaction->update(['status' => 'failed']);
                    return redirect()->route('payment.redirect-back', ['order_id' => $orderId, 'status' => 'failed', 'message' => 'Matrimony subscription payment failed/cancelled.']);
                }
            }

            Log::warning('Unknown CCAvenue order prefix: ' . $orderId);
            return redirect()->route('payment.redirect-back', ['order_id' => $orderId, 'status' => 'failed', 'message' => 'Payment processed, but order type is unrecognized.']);

        } catch (\Exception $e) {
            Log::error('CCAvenue Callback Exception: ' . $e->getMessage());
            return redirect()->route('payment.redirect-back', ['status' => 'failed', 'message' => 'An error occurred while processing the payment response: ' . $e->getMessage()]);
        }
    }

    public function paymentRedirectBack(Request $request)
    {
        $orderId = $request->query('order_id');
        $transaction = null;
        $status = $request->query('status', 'failed');
        $message = $request->query('message');

        if ($orderId) {
            $transaction = Transaction::where('razorpay_order_id', $orderId)->first();
            if ($transaction && $transaction->status === 'completed') {
                $status = 'success';
            }
        }

        // Set default friendly success/error messages if not set in query
        if (!$message) {
            if ($status === 'success') {
                if ($transaction && $transaction->purpose === 'donation') {
                    $message = 'Thank you! Your donation was completed successfully.';
                } elseif ($transaction && $transaction->purpose === 'business_registration') {
                    $message = 'Your business subscription has been activated successfully!';
                } elseif ($transaction && $transaction->purpose === 'matrimony_profile') {
                    $message = 'Your matrimony plan has been activated successfully!';
                } else {
                    $message = 'Payment completed successfully!';
                }
            } else {
                $message = 'Payment failed or was cancelled.';
            }
        }

        if ($status === 'success') {
            if ($orderId) {
                return redirect()->route('payment.summary', ['order_id' => $orderId]);
            }
            session()->flash('success', $message);
        } else {
            session()->flash('error', $message);
        }

        // Determine final destination based on orderId prefix
        if ($orderId) {
            if (strpos($orderId, 'MAT-') === 0) {
                return redirect()->route('matrimony.index');
            } elseif (strpos($orderId, 'BIZ-') === 0) {
                return redirect()->route('dashboard.business.index');
            } elseif (strpos($orderId, 'DON-') === 0) {
                return redirect()->route('login');
            }
        }

        // Fallback
        return redirect()->route('dashboard');
    }

    public function paymentStatus(Request $request, $order_id = null)
    {
        $status = $request->query('status');
        $message = $request->query('message');
        $transaction = null;

        if ($order_id) {
            $transaction = Transaction::where('razorpay_order_id', $order_id)->first();
            if ($transaction) {
                if ($transaction->status === 'completed') {
                    $status = 'completed';
                } elseif ($transaction->status === 'failed') {
                    $status = 'failed';
                }
                
                if (!$message) {
                    if ($status === 'completed') {
                        if ($transaction->purpose === 'donation') {
                            $message = 'Thank you! Your donation was completed successfully.';
                        } elseif ($transaction->purpose === 'business_registration') {
                            $message = 'Your business subscription has been activated successfully!';
                        } elseif ($transaction->purpose === 'matrimony_profile') {
                            $message = 'Your matrimony plan has been activated successfully!';
                        } else {
                            $message = 'Your payment was completed successfully.';
                        }
                    } elseif ($status === 'failed') {
                        $message = 'Your payment failed or was cancelled.';
                    } else {
                        $message = 'Your payment status is pending verification.';
                    }
                }
            }
        }

        if (!$status && $message) {
            $status = 'failed';
        }

        return view('payment.status', [
            'status' => $status ?? 'pending',
            'message' => $message ?? 'No payment details available.',
            'transaction' => $transaction,
            'order_id' => $order_id
        ]);
    }

    public function paymentSummary(Request $request, $order_id)
    {
        $transaction = Transaction::where('razorpay_order_id', $order_id)->first();

        if (!$transaction) {
            return redirect()->route('dashboard')->with('error', 'Transaction not found.');
        }

        if ($transaction->status !== 'completed') {
            return redirect()->route('dashboard')->with('error', 'Transaction is not completed.');
        }

        $details = [];
        $purpose = $transaction->purpose;
        $paymentMethod = 'Online Payment';

        if ($purpose === 'business_registration') {
            $business = Business::where('user_id', $transaction->user_id)->latest()->first();
            $payment = Payment::where('transaction_id', $transaction->id)->first();
            if ($payment && $payment->payment_method) {
                $paymentMethod = $payment->payment_method;
            }
            $details = [
                'type' => 'business',
                'title' => 'Business Subscription',
                'name' => $business ? $business->business_name : 'N/A',
                'period' => $transaction->subscription_period ? ($transaction->subscription_period / 12) . ' Year(s)' : 'N/A',
                'expires_at' => $business && $business->subscription_expires_at 
                    ? \Carbon\Carbon::parse($business->subscription_expires_at)->format('d M Y, h:i A') 
                    : 'N/A',
            ];
        } elseif ($purpose === 'matrimony_profile') {
            $profile = MatrimonyProfile::where('user_id', $transaction->user_id)->first();
            $pd = $profile ? ($profile->personal_details ?? []) : [];
            $payment = Payment::where('transaction_id', $transaction->id)->first();
            if ($payment && $payment->payment_method) {
                $paymentMethod = $payment->payment_method;
            }
            $details = [
                'type' => 'matrimony',
                'title' => 'Matrimony Premium Plan',
                'name' => !empty($pd['name']) ? $pd['name'] : ($profile ? $profile->first_name . ' ' . $profile->last_name : 'N/A'),
                'period' => $transaction->subscription_period ? ($transaction->subscription_period / 12) . ' Year(s)' : 'N/A',
                'expires_at' => $profile && $profile->profile_expires_at 
                    ? \Carbon\Carbon::parse($profile->profile_expires_at)->format('d M Y, h:i A') 
                    : 'N/A',
            ];
        } elseif ($purpose === 'donation') {
            $donation = Donation::where('razorpay_order_id', $order_id)->first();
            if ($donation && $donation->payment_method) {
                $paymentMethod = $donation->payment_method;
            }
            $details = [
                'type' => 'donation',
                'title' => 'Cause Donation',
                'cause_title' => $donation && $donation->cause ? $donation->cause->title : ($transaction->metadata['cause_title'] ?? 'N/A'),
                'organization' => $donation && $donation->cause ? $donation->cause->organization : 'N/A',
                'is_anonymous' => $donation ? $donation->anonymous : false,
                'message' => $donation ? $donation->message : null,
            ];
        }

        return view('payment.summary', [
            'transaction' => $transaction,
            'details' => $details,
            'payment_method' => $paymentMethod,
            'order_id' => $order_id
        ]);
    }

    public function paymentSummaryBack(Request $request, $order_id)
    {
        $transaction = Transaction::where('razorpay_order_id', $order_id)->first();

        if (!$transaction) {
            return redirect()->route('dashboard');
        }

        $purpose = $transaction->purpose;
        $message = 'Payment completed successfully!';
        $routeName = 'dashboard';

        if ($purpose === 'business_registration') {
            $message = 'Your business subscription has been activated successfully!';
            $routeName = 'dashboard.business.index';
        } elseif ($purpose === 'matrimony_profile') {
            $message = 'Your matrimony plan has been activated successfully!';
            $routeName = 'matrimony.index';
        } elseif ($purpose === 'donation') {
            $message = 'Thank you! Your donation was completed successfully.';
            $routeName = 'dashboard';
        }

        return redirect()->route($routeName)->with('success', $message);
    }
}
