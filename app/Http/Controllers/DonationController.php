<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationCause;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Exception;

class DonationController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    /**
     * Suggest the latest active or urgent donation cause for the overlay
     */
    public function suggestCause()
    {
        try {
            // Prioritize urgent active causes, else get latest active cause
            $cause = DonationCause::where('status', 'active')
                ->orderByRaw("CASE WHEN urgency = 'high' THEN 1 ELSE 2 END")
                ->latest()
                ->first();

            if (!$cause) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active donation causes available'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'cause' => [
                    'id' => $cause->id,
                    'title' => $cause->title,
                    'description' => \Str::limit(strip_tags($cause->description), 150),
                    'organization' => $cause->organization,
                    'target_amount' => $cause->target_amount,
                    'raised_amount' => $cause->raised_amount,
                    'progress' => $cause->progress_percentage,
                    'image' => $cause->image_url ? asset('storage/' . $cause->image_url) : null
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch suggested cause: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create Razorpay Order for Donation on the Web
     */
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cause_id' => 'required|exists:donation_causes,id',
            'amount' => 'required|numeric|min:1|max:100000',
            'message' => 'nullable|string|max:500',
            'anonymous' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $user = Auth::user();
            $cause = DonationCause::findOrFail($request->cause_id);

            if ($cause->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'This donation cause is no longer active'
                ], 400);
            }

            // Create Razorpay order
            $orderData = [
                'amount' => $request->amount * 100, // in paise
                'currency' => 'INR',
                'receipt' => 'donation_web_' . time() . '_' . $user->id,
                'notes' => [
                    'cause_id' => $cause->id,
                    'user_id' => $user->id,
                    'purpose' => 'donation'
                ]
            ];

            $razorpayOrder = $this->razorpay->order->create($orderData);

            // Create donation record
            $donation = Donation::create([
                'user_id' => $user->id,
                'cause_id' => $cause->id,
                'amount' => $request->amount,
                'currency' => 'INR',
                'razorpay_order_id' => $razorpayOrder['id'],
                'status' => 'pending',
                'message' => $request->message,
                'anonymous' => $request->get('anonymous', false)
            ]);

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'currency' => 'INR',
                'purpose' => 'donation',
                'razorpay_order_id' => $razorpayOrder['id'],
                'status' => 'pending',
                'metadata' => json_encode([
                    'donation_id' => $donation->id,
                    'cause_id' => $cause->id,
                    'cause_title' => $cause->title
                ])
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $razorpayOrder['id'],
                'amount' => $request->amount,
                'currency' => 'INR',
                'key' => config('services.razorpay.key'),
                'donation_id' => $donation->id,
                'cause_title' => $cause->title,
                'organization' => $cause->organization
            ]);

        } catch (Exception $e) {
            Log::error('Razorpay Web donation order failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate donation payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify payment signature for Donation on the Web
     */
    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'donation_id' => 'required|exists:donations,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $donation = Donation::findOrFail($request->donation_id);
            
            // Verify signature
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $this->razorpay->utility->verifyPaymentSignature($attributes);

            // Update donation
            $donation->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'status' => 'completed',
                'payment_method' => 'razorpay'
            ]);

            // Update transaction
            $transaction = Transaction::where('razorpay_order_id', $request->razorpay_order_id)->first();
            if ($transaction) {
                $transaction->update([
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'status' => 'completed'
                ]);
            }

            // Update cause raised amount
            $donation->cause->updateRaisedAmount();

            return response()->json([
                'success' => true,
                'message' => 'Donation completed successfully!'
            ]);

        } catch (Exception $e) {
            Log::error('Error verifying web donation payment: ' . $e->getMessage());
            
            if (isset($donation)) {
                $donation->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }
}
