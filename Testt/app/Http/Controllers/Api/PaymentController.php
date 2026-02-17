<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Business;
use App\Models\MatrimonyProfile;
use App\Models\BusinessPlan;
use App\Models\MatrimonyPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
    }

    /**
     * Create Razorpay order
     */
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|in:business_registration,matrimony_profile,donation',
            'subscription_period' => 'nullable|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $orderData = [
                'receipt' => 'order_' . time(),
                'amount' => $request->amount * 100, // Convert to paise
                'currency' => 'INR',
                'payment_capture' => 1
            ];

            $razorpayOrder = $this->razorpay->order->create($orderData);

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'amount' => $request->amount,
                'currency' => 'INR',
                'purpose' => $request->purpose,
                'razorpay_order_id' => $razorpayOrder['id'],
                'status' => 'pending',
                'subscription_period' => $request->subscription_period,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $razorpayOrder['id'],
                    'amount' => $razorpayOrder['amount'],
                    'currency' => $razorpayOrder['currency'],
                    'transaction_id' => $transaction->id,
                    'key_id' => env('RAZORPAY_KEY_ID')
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order'
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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
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
            $orderData = [
                'receipt' => 'business_plan_' . $plan->id . '_' . time(),
                'amount' => $amount * 100,
                'currency' => 'INR',
                'payment_capture' => 1
            ];

            $razorpayOrder = $this->razorpay->order->create($orderData);

            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'amount' => $amount,
                'currency' => 'INR',
                'purpose' => 'business_registration',
                'razorpay_order_id' => $razorpayOrder['id'],
                'status' => 'pending',
                'subscription_period' => $subscriptionMonths,
                'meta' => json_encode(['plan_id' => $plan->id])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $razorpayOrder['id'],
                    'amount' => $razorpayOrder['amount'],
                    'currency' => $razorpayOrder['currency'],
                    'transaction_id' => $transaction->id,
                    'key_id' => env('RAZORPAY_KEY_ID')
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Razorpay business order creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order'
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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
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
            $orderData = [
                'receipt' => 'matrimony_plan_' . $plan->id . '_' . time(),
                'amount' => $amount * 100,
                'currency' => 'INR',
                'payment_capture' => 1
            ];

            $razorpayOrder = $this->razorpay->order->create($orderData);

            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'amount' => $amount,
                'currency' => 'INR',
                'purpose' => 'matrimony_profile',
                'razorpay_order_id' => $razorpayOrder['id'],
                'status' => 'pending',
                'subscription_period' => $subscriptionMonths,
                'meta' => json_encode(['plan_id' => $plan->id])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $razorpayOrder['id'],
                    'amount' => $razorpayOrder['amount'],
                    'currency' => $razorpayOrder['currency'],
                    'transaction_id' => $transaction->id,
                    'key_id' => env('RAZORPAY_KEY_ID')
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Razorpay matrimony order creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order'
            ], 500);
        }
    }

    /**
     * Verify payment and update transaction
     */
    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'transaction_id' => 'required|integer|exists:transactions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verify signature
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $this->razorpay->utility->verifyPaymentSignature($attributes);

            // Get transaction
            $transaction = Transaction::where('id', $request->transaction_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            // Update transaction
            $transaction->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'status' => 'completed',
            ]);

            // Process based on purpose
            $this->processPaymentPurpose($transaction);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'data' => ['transaction' => $transaction]
            ]);

        } catch (\Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage());
            
            // Update transaction as failed
            if (isset($transaction)) {
                $transaction->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed'
            ], 400);
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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
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