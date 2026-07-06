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
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
            ], 422);
        }

        try {
            $user = Auth::user();

            // If request is unauthenticated, return 401 instead of throwing a fatal error
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login and include a valid API token.'
                ], 401);
            }
            $cause = DonationCause::findOrFail($request->cause_id);

            // Check if cause is active
            if (!$cause->is_active) {
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
                'data' => [
                    'payment_way' => 'ccavenue',
                    'payment_url' => $this->ccavenue->getPaymentUrl(),
                    'encRequest' => $this->ccavenue->encrypt($params),
                    'access_code' => $this->ccavenue->getAccessCode(),
                    'donation_id' => $donation->id,
                    'order_id' => $orderId,
                    'amount' => $request->amount,
                    'currency' => 'INR',
                    'cause' => [
                        'id' => $cause->id,
                        'title' => $cause->title,
                        'organization' => $cause->organization
                    ]
                ]
            ]);

        } catch (Exception $e) {
            Log::error('CCAvenue api donation initiation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify donation payment
     */
    public function verifyDonationPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'donation_id' => 'required|exists:donations,id',
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
            $donation = Donation::findOrFail($request->donation_id);
            
            if ($donation->status === 'completed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Donation completed successfully',
                    'data' => [
                        'donation' => $donation->load('cause'),
                        'receipt_url' => $donation->receipt_url
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Donation is ' . $donation->status,
                'status' => $donation->status
            ], 400);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 500);
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
