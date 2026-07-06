<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Business;
use App\Models\MatrimonyProfile;
use App\Models\BusinessPlan;
use App\Models\MatrimonyPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\CCAvenue;

class PaymentController extends Controller
{
    private $ccavenue;

    public function __construct(CCAvenue $ccavenue)
    {
        $this->ccavenue = $ccavenue;
    }

    /**
     * Create CCAvenue order
     */
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|in:business_registration,matrimony_profile,donation',
            'subscription_period' => 'nullable|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
            ], 422);
        }

        try {
            $prefix = match($request->purpose) {
                'business_registration' => 'BIZ-',
                'matrimony_profile' => 'MAT-',
                'donation' => 'DON-',
                default => 'GEN-'
            };
            $orderId = $prefix . time() . '-' . mt_rand(1000, 9999);

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'amount' => $request->amount,
                'currency' => 'INR',
                'purpose' => $request->purpose,
                'razorpay_order_id' => $orderId,
                'status' => 'pending',
                'subscription_period' => $request->subscription_period,
            ]);

            // Prepare CCAvenue parameters
            $params = [
                'order_id' => $orderId,
                'amount' => number_format($request->amount, 2, '.', ''),
                'currency' => 'INR',
                'redirect_url' => route('ccavenue.callback'),
                'cancel_url' => route('ccavenue.callback'),
                'language' => 'EN',
                'billing_name' => $request->user()->name ?? '',
                'billing_email' => $request->user()->email ?? '',
                'billing_tel' => $request->user()->phone ?? ''
            ];

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'payment_way' => 'ccavenue',
                    'payment_url' => $this->ccavenue->getPaymentUrl(),
                    'encRequest' => $this->ccavenue->encrypt($params),
                    'access_code' => $this->ccavenue->getAccessCode(),
                    'order_id' => $orderId,
                    'amount' => $request->amount,
                    'currency' => 'INR',
                    'transaction_id' => $transaction->id
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('CCAvenue order creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create order for business subscription based on selected plan
     */
    public function createBusinessOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|exists:business_plans,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
            ], 422);
        }

        $plan = BusinessPlan::find($request->plan_id);
        if (!$plan || !$plan->active) {
            return response()->json([
                'success' => false,
                'message' => 'Selected plan is not available'
            ], 404);
        }

        $amount = $plan->price; // rupees
        $subscriptionMonths = intval($plan->duration_years) * 12;

        try {
            $orderId = 'BIZ-' . time() . '-' . mt_rand(1000, 9999);

            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'amount' => $amount,
                'currency' => 'INR',
                'purpose' => 'business_registration',
                'razorpay_order_id' => $orderId,
                'status' => 'pending',
                'subscription_period' => $subscriptionMonths,
                'meta' => json_encode(['plan_id' => $plan->id])
            ]);

            // Prepare CCAvenue parameters
            $params = [
                'order_id' => $orderId,
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'INR',
                'redirect_url' => route('ccavenue.callback'),
                'cancel_url' => route('ccavenue.callback'),
                'language' => 'EN',
                'billing_name' => $request->user()->name ?? '',
                'billing_email' => $request->user()->email ?? '',
                'billing_tel' => $request->user()->phone ?? ''
            ];

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'payment_way' => 'ccavenue',
                    'payment_url' => $this->ccavenue->getPaymentUrl(),
                    'encRequest' => $this->ccavenue->encrypt($params),
                    'access_code' => $this->ccavenue->getAccessCode(),
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'currency' => 'INR',
                    'transaction_id' => $transaction->id
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('CCAvenue business order creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create order for matrimony subscription based on selected plan
     */
    public function createMatrimonyOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|exists:matrimony_plans,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
            ], 422);
        }

        $plan = MatrimonyPlan::find($request->plan_id);
        if (!$plan || !$plan->active) {
            return response()->json([
                'success' => false,
                'message' => 'Selected plan is not available'
            ], 404);
        }

        $amount = $plan->price;
        $subscriptionMonths = intval($plan->duration_years) * 12;

        try {
            $orderId = 'MAT-' . time() . '-' . mt_rand(1000, 9999);

            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'amount' => $amount,
                'currency' => 'INR',
                'purpose' => 'matrimony_profile',
                'razorpay_order_id' => $orderId,
                'status' => 'pending',
                'subscription_period' => $subscriptionMonths,
                'meta' => json_encode(['plan_id' => $plan->id])
            ]);

            // Prepare CCAvenue parameters
            $params = [
                'order_id' => $orderId,
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'INR',
                'redirect_url' => route('ccavenue.callback'),
                'cancel_url' => route('ccavenue.callback'),
                'language' => 'EN',
                'billing_name' => $request->user()->name ?? '',
                'billing_email' => $request->user()->email ?? '',
                'billing_tel' => $request->user()->phone ?? ''
            ];

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'payment_way' => 'ccavenue',
                    'payment_url' => $this->ccavenue->getPaymentUrl(),
                    'encRequest' => $this->ccavenue->encrypt($params),
                    'access_code' => $this->ccavenue->getAccessCode(),
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'currency' => 'INR',
                    'transaction_id' => $transaction->id
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('CCAvenue matrimony order creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify payment and update transaction
     */
    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|integer|exists:transactions,id',
            'razorpay_payment_id' => 'nullable|string',
            'razorpay_order_id' => 'nullable|string',
            'razorpay_signature' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
            ], 422);
        }

        try {
            // Get transaction
            $transaction = Transaction::where('id', $request->transaction_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            if ($transaction->status === 'completed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified successfully',
                    'data' => ['transaction' => $transaction]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment is ' . $transaction->status,
                'status' => $transaction->status
            ], 400);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found or unauthorized'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment based on purpose
     */
    private function processPaymentPurpose($transaction)
    {
        switch ($transaction->purpose) {
            case 'business_registration':
                $this->activateBusinessSubscription($transaction);
                break;
            
            case 'matrimony_profile':
                $this->activateMatrimonyProfile($transaction);
                break;
            
            case 'donation':
                // Handle donation processing
                Log::info('Donation processed for user: ' . $transaction->user_id);
                break;
        }
    }

    /**
     * Activate business subscription
     */
    private function activateBusinessSubscription($transaction)
    {
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
    }

    /**
     * Activate matrimony profile
     */
    private function activateMatrimonyProfile($transaction)
    {
        $profile = MatrimonyProfile::where('user_id', $transaction->user_id)->first();
        
        if ($profile) {
            $expiresAt = now()->addMonths($transaction->subscription_period ?? 1);
            
            $profile->update([
                'profile_expires_at' => $expiresAt,
            ]);
        }
    }

    /**
     * Get user's transaction history
     */
    public function getTransactions(Request $request)
    {
        $transactions = Transaction::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    /**
     * Get single transaction details
     */
    public function getTransaction(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => ['transaction' => $transaction]
        ]);
    }

    /**
     * Initiate refund
     */
    public function initiateRefund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|integer|exists:transactions,id',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
            ], 422);
        }

        $transaction = Transaction::where('id', $request->transaction_id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'completed')
            ->firstOrFail();

        try {
            $refund = $this->razorpay->refund->create([
                'payment_id' => $transaction->razorpay_payment_id,
                'amount' => $transaction->amount * 100, // Convert to paise
                'notes' => [
                    'reason' => $request->reason,
                    'user_id' => $request->user()->id
                ]
            ]);

            $transaction->update([
                'status' => 'refunded',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund initiated successfully',
                'data' => [
                    'refund_id' => $refund['id'],
                    'status' => $refund['status']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Refund initiation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate refund'
            ], 500);
        }
    }

    /**
     * Get payment statistics for admin
     */
    public function getPaymentStats()
    {
        $stats = [
            'total_transactions' => Transaction::count(),
            'completed_transactions' => Transaction::where('status', 'completed')->count(),
            'total_revenue' => Transaction::where('status', 'completed')->sum('amount'),
            'business_payments' => Transaction::where('purpose', 'business_registration')
                ->where('status', 'completed')->count(),
            'matrimony_payments' => Transaction::where('purpose', 'matrimony_profile')
                ->where('status', 'completed')->count(),
            'donations' => Transaction::where('purpose', 'donation')
                ->where('status', 'completed')->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Webhook handler for Razorpay events
     */
    public function webhook(Request $request)
    {
        // $webhookSignature = $request->header('X-Razorpay-Signature');
        $webhookBody = $request->getContent();

        $webhookSignature = hash_hmac('sha256', $webhookBody, env('RAZORPAY_WEBHOOK_SECRET'));
        //b613679a0814d9ec772f95d778c35fc5ff1697c493715653c6c712144292c5ad
        
        try {
            $this->razorpay->utility->verifyWebhookSignature(
                $webhookBody,
                $webhookSignature,
                env('RAZORPAY_WEBHOOK_SECRET')
            );

            $event = json_decode($webhookBody, true);
            
            // Handle different webhook events
            switch ($event['event']) {
                case 'payment.captured':
                    $this->handlePaymentCaptured($event['payload']['payment']['entity']);
                    break;
                
                case 'payment.failed':
                    $this->handlePaymentFailed($event['payload']['payment']['entity']);
                    break;
                
                case 'refund.processed':
                    $this->handleRefundProcessed($event['payload']['refund']['entity']);
                    break;
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Webhook verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }
    

    private function handlePaymentCaptured($payment)
    {
        $transaction = Transaction::where('razorpay_payment_id', $payment['id'])->first();
        if ($transaction && $transaction->status === 'pending') {
            $transaction->update(['status' => 'completed']);
            $this->processPaymentPurpose($transaction);
        }
    }

    private function handlePaymentFailed($payment)
    {
        $transaction = Transaction::where('razorpay_payment_id', $payment['id'])->first();
        if ($transaction) {
            $transaction->update(['status' => 'failed']);
        }
    }

    private function handleRefundProcessed($refund)
    {
        $transaction = Transaction::where('razorpay_payment_id', $refund['payment_id'])->first();
        if ($transaction) {
            $transaction->update(['status' => 'refunded']);
        }
    }
}