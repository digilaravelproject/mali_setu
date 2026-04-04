<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\JobPosting;
use App\Models\Product;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BusinessManagementController extends Controller
{
    /**
     * Display all businesses
     */
    public function index(Request $request)
    {
        $query = Business::with(['user', 'category', 'products', 'services'])
            ->withCount([
                'businessRegistrationTransactions as completed_business_registrations_count' => function ($query) {
                    $query->where('status', 'completed');
                },
                'businessRegistrationTransactions as pending_business_registrations_count' => function ($query) {
                    $query->where('status', 'pending');
                },
                'jobPostings'
            ]);
        
        // Filter by verification status
        if ($request->has('verification_status') && $request->verification_status !== '') {
            $query->where('verification_status', $request->verification_status);
        }
        
        // Filter by business type
        if ($request->has('business_type') && $request->business_type !== '') {
            $query->where('business_type', $request->business_type);
        }
        
        // Filter by category
        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }
        
        // Search by business name or owner name
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('business_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $businesses = $query->latest()->paginate(20);
        
        $categories = BusinessCategory::where('is_active', true)->get();

        $businessTypes = Business::select('business_type')
            ->distinct()
            ->orderBy('business_type')
            ->pluck('business_type')
            ->toArray();
        
        $stats = [
            'total' => Business::count(),
            'approved' => Business::where('verification_status', 'approved')->count(),
            'pending' => Business::where('verification_status', 'pending')->count(),
            'rejected' => Business::where('verification_status', 'rejected')->count(),
            'by_type' => Business::selectRaw('business_type, COUNT(*) as count')
                ->groupBy('business_type')
                ->pluck('count', 'business_type')
                ->toArray()
        ];
        
        return view('admin.businesses.index', compact('businesses', 'categories', 'stats', 'businessTypes'));
    }
    
    /**
     * Display pending business verifications
     */
    public function verification(Request $request)
    {
        $query = Business::with(['user', 'category', 'products', 'services'])
            ->withCount([
                'businessRegistrationTransactions as completed_business_registrations_count' => function ($query) {
                    $query->where('status', 'completed');
                },
                'businessRegistrationTransactions as pending_business_registrations_count' => function ($query) {
                    $query->where('status', 'pending');
                },
            ])
            ->where('verification_status', 'pending');
            
        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('business_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $pendingBusinesses = $query->latest()->paginate(15);
        
        $stats = [
            'pending_count' => Business::where('verification_status', 'pending')->count(),
            'approved_today' => Business::where('verification_status', 'approved')
                ->whereDate('updated_at', today())->count(),
            'rejected_today' => Business::where('verification_status', 'rejected')
                ->whereDate('updated_at', today())->count(),
        ];
        
        return view('admin.businesses.verification', compact('pendingBusinesses', 'stats'));
    }
    
    /**
     * Display pending businesses (alias for verification)
     */
    public function pending(Request $request)
    {
        return $this->verification($request);
    }
    
    /**
     * Approve business
     */
    public function approve(Request $request, $businessId)
    {
        try {
            $business = Business::findOrFail($businessId);

            $paymentCompleted = Transaction::where('user_id', $business->user_id)
                ->where('purpose', 'business_registration')
                ->where('status', 'completed')
                ->exists();

            if (! $paymentCompleted) {
                $message = 'Payment is pending for this business. Approval is not allowed until payment is complete.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }
                return redirect()->back()->with('error', $message);
            }

            $business->update([
                'verification_status' => 'approved',
                'verified_at' => now(),
                'verified_by' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Business approved successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Business approved successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve business: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to approve business.');
        }
    }
    
    /**
     * Reject business
     */
    public function reject(Request $request, $businessId)
    {
        try {
            // Log incoming request data for debugging
            \Log::info('Business rejection request', [
                'business_id' => $businessId,
                'request_data' => $request->all(),
                'rejection_reason' => $request->rejection_reason
            ]);
            
            $request->validate([
                'rejection_reason' => 'required|string|max:500'
            ]);
            
            $business = Business::findOrFail($businessId);
            
            \Log::info('Business found for rejection', [
                'business_id' => $businessId,
                'current_status' => $business->verification_status
            ]);
            
            $business->update([
                'verification_status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'verified_at' => now(),
                'verified_by' => auth()->id()
            ]);
            
            \Log::info('Business rejected successfully', [
                'business_id' => $businessId,
                'rejection_reason' => $request->rejection_reason
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Business rejected successfully!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Business rejected successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Business rejection validation failed', [
                'business_id' => $businessId,
                'validation_errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to reject business', [
                'business_id' => $businessId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject business: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to reject business: ' . $e->getMessage());
        }
    }
    
    /**
     * View business jobs
     */
    public function jobs($id)
    {
        $business = Business::with([
            'user',
            'category',
            'jobPostings' => function ($query) {
                $query->with(['applications.user'])->withCount('applications')->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        if ($business->verification_status !== 'approved') {
            return redirect()->route('admin.businesses.index')
                ->with('error', 'Jobs can only be viewed for approved businesses.');
        }

        return view('admin.businesses.jobs', compact('business'));
    }

    /**
     * Approve business job posting
     */
    public function approveJob(Request $request, $jobId)
    {
        $job = JobPosting::findOrFail($jobId);
        $job->update(['status' => 'approved', 'is_active' => true]);

        return redirect()->back()->with('success', 'Job posting approved successfully.');
    }

    /**
     * Reject business job posting
     */
    public function rejectJob(Request $request, $jobId)
    {
        $job = JobPosting::findOrFail($jobId);
        $job->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Job posting rejected successfully.');
    }
    
    /**
     * View business details
     */
    public function show($id)
    {
        $business = Business::with([
            'user', 
            'category', 
            'products', 
            'services', 
            'locations',
            'reviews.user'
        ])
        ->withCount([
            'businessRegistrationTransactions as completed_business_registrations_count' => function ($query) {
                $query->where('status', 'completed');
            },
            'businessRegistrationTransactions as pending_business_registrations_count' => function ($query) {
                $query->where('status', 'pending');
            },
        ])
        ->findOrFail($id);
        
        return view('admin.businesses.show', compact('business'));
    }

    /**
     * Show edit form for a business
     */
    public function edit($id)
    {
        $business = Business::findOrFail($id);
        $categories = BusinessCategory::where('is_active', true)->get();

        $baseTypes = [
            'Public Ltd',
            'Private Ltd',
            'Proprietary /Partnership - LLP'
        ];

        $existingTypes = Business::select('business_type')
            ->distinct()
            ->orderBy('business_type')
            ->pluck('business_type')
            ->toArray();

        $businessTypes = array_values(array_unique(array_merge($baseTypes, $existingTypes)));

        return view('admin.businesses.edit', compact('business', 'categories', 'businessTypes'));
    }

    /**
     * Update business details
     */
    public function update(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:business_categories,id',
            'description' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'website' => 'nullable|url',
        ]);

        $business->update($validated);

        return redirect()->route('admin.businesses.show', $business->id)
            ->with('success', 'Business updated successfully.');
    }
    
    /**
     * Suspend business
     */
    public function suspend(Request $request, $id)
    {
        try {
            $business = Business::findOrFail($id);
            
            // Log current status for debugging
            \Log::info('Suspending business', [
                'business_id' => $id,
                'current_status' => $business->status,
                'verification_status' => $business->verification_status
            ]);
            
            $updated = $business->update(['status' => 'suspended']);
            
            // Refresh the model to get updated data
            $business->refresh();
            
            // Log after update
            \Log::info('Business status after update', [
                'business_id' => $id,
                'new_status' => $business->status,
                'update_result' => $updated
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Business suspended successfully!',
                    'new_status' => $business->status
                ]);
            }
            
            return redirect()->back()->with('success', 'Business suspended successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to suspend business', [
                'business_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to suspend business: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to suspend business: ' . $e->getMessage());
        }
    }
    
    /**
     * Activate business
     */
    public function activate(Request $request, $id)
    {
        try {
            $business = Business::findOrFail($id);
            
            // Log current status for debugging
            \Log::info('Activating business', [
                'business_id' => $id,
                'current_status' => $business->status,
                'verification_status' => $business->verification_status
            ]);
            
            $updated = $business->update(['status' => 'active']);
            
            // Refresh the model to get updated data
            $business->refresh();
            
            // Log after update
            \Log::info('Business status after activation', [
                'business_id' => $id,
                'new_status' => $business->status,
                'update_result' => $updated
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Business activated successfully!',
                    'new_status' => $business->status
                ]);
            }
            
            return redirect()->back()->with('success', 'Business activated successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to activate business', [
                'business_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to activate business: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to activate business: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete business
     */
    public function destroy(Request $request, $id)
    {
        try {
            $business = Business::findOrFail($id);
            $business->delete();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Business deleted successfully!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Business deleted successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete business: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete business.');
        }
    }
    
    /**
     * Manage business categories
     */
    public function categories()
    {
        $categories = BusinessCategory::withCount('businesses')->paginate(20);
        
        return view('admin.businesses.categories', compact('categories'));
    }
    
    /**
     * Create new category
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:business_categories',
            'description' => 'nullable|string|max:500'
        ]);
        
        BusinessCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true
        ]);
        
        return redirect()->back()->with('success', 'Category created successfully!');
    }
}