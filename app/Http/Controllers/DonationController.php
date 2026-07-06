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
use App\Services\CCAvenue;
use Exception;

class DonationController extends Controller
{
    private $ccavenue;

    public function __construct(CCAvenue $ccavenue)
    {
        $this->ccavenue = $ccavenue;
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
     * Create CCAvenue Order for Donation on the Web
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

            // Generate unique local Order ID for CCAvenue
            $orderId = 'DON-' . time() . '-' . mt_rand(1000, 9999);

            // Create donation record
            $donation = Donation::create([
                'user_id' => $user->id,
                'cause_id' => $cause->id,
                'amount' => $request->amount,
                'currency' => 'INR',
                'razorpay_order_id' => $orderId,
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
                'razorpay_order_id' => $orderId,
                'status' => 'pending',
                'metadata' => json_encode([
                    'donation_id' => $donation->id,
                    'cause_id' => $cause->id,
                    'cause_title' => $cause->title
                ])
            ]);

            // Prepare CCAvenue parameters
            $params = [
                'order_id' => $orderId,
                'amount' => number_format($request->amount, 2, '.', ''),
                'currency' => 'INR',
                'redirect_url' => route('ccavenue.callback'),
                'cancel_url' => route('ccavenue.callback'),
                'language' => 'EN',
                'billing_name' => $user->name ?? '',
                'billing_email' => $user->email ?? '',
                'billing_tel' => $user->phone ?? ''
            ];

            return response()->json([
                'success' => true,
                'payment_way' => 'ccavenue',
                'payment_url' => $this->ccavenue->getPaymentUrl(),
                'encRequest' => $this->ccavenue->encrypt($params),
                'access_code' => $this->ccavenue->getAccessCode(),
                'donation_id' => $donation->id,
                'cause_title' => $cause->title,
                'organization' => $cause->organization
            ]);

        } catch (Exception $e) {
            Log::error('CCAvenue Web donation initiation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate donation payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify payment signature for Donation on the Web (Legacy Razorpay route)
     */
    public function verifyPayment(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'This payment verification endpoint is deprecated. Payment is processed via callback.'
        ], 400);
    }
}
