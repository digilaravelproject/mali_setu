<?php

namespace App\Http\Controllers\Api;

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
     * Get all donation causes with filters
     */
    public function getCauses(Request $request)
    {
        try {
            $query = DonationCause::query();

            // Apply filters
            if ($request->has('category') && $request->category) {
                $query->where('category', $request->category);
            }

            if ($request->has('urgency') && $request->urgency) {
                $query->where('urgency', $request->urgency);
            }

            if ($request->has('location') && $request->location) {
                $query->where('location', 'like', '%' . $request->location . '%');
            }

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            } else {
                // Default to active causes only
                $query->where('status', 'active');
            }

            // Search by title or organization
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('organization', 'like', '%' . $request->search . '%');
                });
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            if (in_array($sortBy, ['created_at', 'target_amount', 'raised_amount', 'urgency'])) {
                $query->orderBy($sortBy, $sortOrder);
            }

            $causes = $query->withCount('donations')
                           ->with(['donations' => function($q) {
                               $q->where('status', 'completed');
                           }])
                           ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $causes
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching donation causes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch donation causes'
            ], 500);
        }
    }

    /**
     * Get a specific donation cause
     */
    public function getCause($id)
    {
        try {
            $cause = DonationCause::with(['donations' => function($q) {
                $q->where('status', 'completed')
                  ->where('anonymous', false)
                  ->with('user:id,name')
                  ->latest()
                  ->limit(10);
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $cause
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Donation cause not found'
            ], 404);
        }
    }

    /**
     * Create a donation order
     */
    public function createDonationOrder(Request $request)
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
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $cause = DonationCause::findOrFail($request->cause_id);

            // Check if cause is active
            if (!$cause->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'This donation cause is no longer active'
                ], 400);
            }

            // Create Razorpay order
            $orderData = [
                'amount' => $request->amount * 100, // Convert to paise
                'currency' => 'INR',
                'receipt' => 'donation_' . time() . '_' . $user->id,
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
                'data' => [
                    'donation_id' => $donation->id,
                    'order_id' => $razorpayOrder['id'],
                    'amount' => $request->amount,
                    'currency' => 'INR',
                    'key' => config('services.razorpay.key'),
                    'cause' => [
                        'id' => $cause->id,
                        'title' => $cause->title,
                        'organization' => $cause->organization
                    ]
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error creating donation order: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create donation order'
            ], 500);
        }
    }

    /**
     * Verify donation payment
     */
    public function verifyDonationPayment(Request $request)
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
                'message' => 'Validation failed',
                'errors' => $validator->errors()
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

            // Generate receipt (placeholder for now)
            $receiptUrl = $this->generateDonationReceipt($donation);
            $donation->update(['receipt_url' => $receiptUrl]);

            return response()->json([
                'success' => true,
                'message' => 'Donation completed successfully',
                'data' => [
                    'donation' => $donation->load('cause'),
                    'receipt_url' => $receiptUrl
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error verifying donation payment: ' . $e->getMessage());
            
            // Update donation as failed
            if (isset($donation)) {
                $donation->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed'
            ], 400);
        }
    }

    /**
     * Get user's donation history
     */
    public function getDonationHistory(Request $request)
    {
        try {
            $user = Auth::user();
            
            $donations = $user->donations()
                            ->with('cause:id,title,organization,image_url')
                            ->orderBy('created_at', 'desc')
                            ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $donations
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching donation history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch donation history'
            ], 500);
        }
    }

    /**
     * Get donation analytics for user
     */
    public function getDonationAnalytics(Request $request)
    {
        try {
            $user = Auth::user();
            
            $totalDonated = $user->completedDonations()->sum('amount');
            $donationCount = $user->completedDonations()->count();
            $causesSupported = $user->completedDonations()->distinct('cause_id')->count();
            
            // Monthly donation trend (last 6 months)
            $monthlyTrend = $user->completedDonations()
                               ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(amount) as total')
                               ->where('created_at', '>=', now()->subMonths(6))
                               ->groupBy('year', 'month')
                               ->orderBy('year', 'desc')
                               ->orderBy('month', 'desc')
                               ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_donated' => $totalDonated,
                    'donation_count' => $donationCount,
                    'causes_supported' => $causesSupported,
                    'monthly_trend' => $monthlyTrend
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching donation analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch donation analytics'
            ], 500);
        }
    }

    /**
     * Generate donation receipt (placeholder)
     */
    private function generateDonationReceipt($donation)
    {
        // This is a placeholder - in a real implementation, you would generate a PDF receipt
        $receiptId = 'RECEIPT_' . $donation->id . '_' . time();
        return "receipts/donations/{$receiptId}.pdf";
    }

    /**
     * Download donation receipt
     */
    public function downloadReceipt($donationId)
    {
        try {
            $user = Auth::user();
            $donation = $user->donations()->with('cause')->findOrFail($donationId);

            if (!$donation->receipt_url || $donation->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Receipt not available'
                ], 404);
            }

            // In a real implementation, you would return the actual PDF file
            return response()->json([
                'success' => true,
                'data' => [
                    'receipt_url' => $donation->receipt_url,
                    'donation' => $donation
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found'
            ], 404);
        }
    }
}
