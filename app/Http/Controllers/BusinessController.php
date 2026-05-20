<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\BusinessPlan;
use App\Models\Product;
use App\Models\Service;
use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\BusinessReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    /**
     * Show Business Management Console
     */
    public function index(Request $request)
    {
        $user = Auth::user()->load([
            'casteCertificate',
            'business.products',
            'business.services',
            'business.category'
        ]);

        $user->is_business = $user->business ? true : false;

        $businessPayment = Transaction::where('user_id', $user->id)
            ->where('purpose', 'business_registration')
            ->whereNotNull('razorpay_payment_id')
            ->latest()
            ->first();
        $user->has_business_payment = !is_null($businessPayment);

        $categories = BusinessCategory::where('is_active', true)->get();
        $plans = BusinessPlan::where('active', true)->get();
        
        $jobs = [];
        if ($user->business) {
            $jobs = JobPosting::where('business_id', $user->business->id)
                ->with(['applications.user'])
                ->latest()
                ->get();
        }

        // Stats specific to the logged-in user's business
        $userBusiness = $user->business;
        $stats = [
            'businesses_count' => $userBusiness ? 1 : 0,
            'products_count' => $userBusiness ? $userBusiness->products()->count() : 0,
            'services_count' => $userBusiness ? $userBusiness->services()->count() : 0,
            'jobs_count' => $userBusiness ? JobPosting::where('business_id', $userBusiness->id)->count() : 0,
        ];

        return view('business.index', compact('user', 'stats', 'categories', 'plans', 'jobs'));
    }

    /**
     * Show Business Registration Form
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->business) {
            return redirect()->route('dashboard.business.index')->with('success', 'You already have a registered business.');
        }
        $categories = BusinessCategory::where('is_active', true)->get();
        return view('business.create', compact('categories'));
    }

    /**
     * Register a new Business Profile
     */
    public function registerBusiness(Request $request)
    {
        $user = Auth::user();
        if ($user->business) {
            return redirect()->route('dashboard.business.index')->with('success', 'You already have a registered business.');
        }

        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required',
            'category_id' => 'required|integer|exists:business_categories,id',
            'description' => 'required|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'village' => 'nullable|string|max:100',
            'taluka' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'required|string|max:100',
            'pincode' => 'required|digits:6',
            'website' => 'nullable|url',
            'opening_time' => 'nullable',
            'closing_time' => 'nullable',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        try {
            $photoPaths = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                    if (!Storage::disk('public')->exists('business/photos')) {
                        Storage::disk('public')->makeDirectory('business/photos');
                    }
                    Storage::disk('public')->putFileAs('business/photos', $file, $fileName);
                    $photoPaths[] = 'business/photos/' . $fileName;
                }
            }

            $business = Business::create([
                'user_id' => $user->id,
                'business_name' => $request->business_name,
                'business_type' => $request->business_type,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'country' => $request->country,
                'state' => $request->state,
                'district' => $request->district,
                'village' => $request->village,
                'taluka' => $request->taluka,
                'address' => $request->address,
                'city' => $request->city,
                'pincode' => $request->pincode,
                'website' => $request->website,
                'opening_time' => $request->opening_time,
                'closing_time' => $request->closing_time,
                'verification_status' => 'pending',
                'subscription_status' => 'trial',
                'photo' => !empty($photoPaths) ? implode(', ', $photoPaths) : null,
            ]);

            if (empty($user->user_type) || $user->user_type != 'business') {
                $user->update(['user_type' => 'business']);
            }

            try {
                app(\App\Services\NotificationService::class)->createNotification(
                    $user->id,
                    Notification::TYPE_BUSINESS_VERIFIED,
                    'Business registered',
                    'Your business "' . $business->business_name . '" has been registered and is pending verification.',
                    ['business_id' => $business->id],
                    '/business/manage',
                    Notification::PRIORITY_MEDIUM,
                    $business,
                    ['in_app', 'email']
                );
            } catch (\Exception $e) {
                \Log::warning('Business registration notification failed: ' . $e->getMessage());
            }

            return redirect()->route('dashboard.business.index')->with('success', 'Business registered successfully! Enjoy your 30-day trial.');

        } catch (\Exception $e) {
            \Log::error('Web Business registration failure: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to register business: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show Business Editing Form
     */
    public function edit()
    {
        $user = Auth::user();
        $business = Business::where('user_id', $user->id)->firstOrFail();
        $categories = BusinessCategory::where('is_active', true)->get();
        return view('business.edit', compact('business', 'categories'));
    }

    /**
     * Update Business Profile Details
     */
    public function updateBusiness(Request $request)
    {
        $user = Auth::user();
        $business = Business::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required',
            'category_id' => 'required|integer|exists:business_categories,id',
            'description' => 'required|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'village' => 'nullable|string|max:100',
            'taluka' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'required|string|max:100',
            'pincode' => 'required|digits:6',
            'website' => 'nullable|url',
            'opening_time' => 'nullable',
            'closing_time' => 'nullable',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        try {
            $photoPaths = $business->photo ? explode(', ', $business->photo) : [];
            if ($request->hasFile('photos')) {
                // If user uploads new photos, we append or replace them.
                // Let's replace them in the standard way.
                if ($business->photo) {
                    foreach (explode(',', $business->photo) as $oldP) {
                        $oldP = trim($oldP);
                        if ($oldP && Storage::disk('public')->exists($oldP)) {
                            Storage::disk('public')->delete($oldP);
                        }
                    }
                }

                $photoPaths = [];
                foreach ($request->file('photos') as $file) {
                    $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                    if (!Storage::disk('public')->exists('business/photos')) {
                        Storage::disk('public')->makeDirectory('business/photos');
                    }
                    Storage::disk('public')->putFileAs('business/photos', $file, $fileName);
                    $photoPaths[] = 'business/photos/' . $fileName;
                }

                $business->update([
                    'photo' => implode(', ', $photoPaths)
                ]);
            }

            $business->update($request->only([
                'business_name', 'business_type', 'category_id', 'description',
                'contact_phone', 'contact_email', 'country', 'state', 'district',
                'village', 'taluka', 'address', 'city', 'pincode', 'website',
                'opening_time', 'closing_time'
            ]));

            return redirect()->route('dashboard.business.index')->with('success', 'Business profile updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Web Business update failure: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to update business: ' . $e->getMessage()]);
        }
    }

    /**
     * Browse approved businesses (Directory)
     */
    public function browse(Request $request)
    {
        $categories = BusinessCategory::where('is_active', true)->get();
        
        $query = Business::where('verification_status', 'approved')
            ->with(['category', 'products', 'services']);

        // Quick Search Filters
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('business_name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('state', 'like', "%{$q}%");
            });
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Location Filter (City/State)
        if ($request->filled('location')) {
            $loc = $request->location;
            $query->where(function($sub) use ($loc) {
                $sub->where('city', 'like', "%{$loc}%")
                    ->orWhere('state', 'like', "%{$loc}%");
            });
        }

        $businesses = $query->latest()->paginate(12)->withQueryString();

        return view('business.browse', compact('businesses', 'categories'));
    }

    /**
     * Show Public Business Profile Details
     */
    public function show($id)
    {
        $business = Business::with([
            'category', 
            'products', 
            'services', 
            'jobPostings' => function($q) {
                $q->where('is_active', true)->where('status', 'approved');
            }, 
            'reviews' => function($q) {
                $q->where('status', 'approved')->with('user')->latest();
            }
        ])->findOrFail($id);

        // Calculate average rating
        $avgRating = $business->reviews->avg('rating') ?? 0;

        return view('business.show', compact('business', 'avgRating'));
    }

    /**
     * Store Business Review (One review per user per business)
     */
    public function storeReview(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'rating'      => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Check if user already reviewed this business
        $existing = BusinessReview::where('business_id', $request->business_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['error' => 'You have already submitted a review for this business.']);
        }

        BusinessReview::create([
            'business_id' => $request->business_id,
            'user_id'     => $user->id,
            'rating'      => $request->rating,
            'review_text' => $request->review_text,
            'status'      => 'approved', // Auto-approved on web for better user interaction, or standard pending
        ]);

        return back()->with('success', 'Your review has been submitted successfully!');
    }

    /**
     * Create Razorpay Order for Business Plan Subscription
     */
    public function createBusinessOrder(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:business_plans,id',
        ]);

        $plan = BusinessPlan::find($request->plan_id);
        if (!$plan || !$plan->active) {
            return response()->json([
                'success' => false,
                'message' => 'Selected plan is not available'
            ], 404);
        }

        $amount = $plan->price; 
        $subscriptionMonths = intval($plan->duration_years) * 12;

        try {
            $razorpay = new \Razorpay\Api\Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
            $orderData = [
                'receipt' => 'business_plan_' . $plan->id . '_' . time(),
                'amount' => $amount * 100,
                'currency' => 'INR',
                'payment_capture' => 1
            ];

            $razorpayOrder = $razorpay->order->create($orderData);

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
                'order_id' => $razorpayOrder['id'],
                'amount' => $razorpayOrder['amount'],
                'currency' => $razorpayOrder['currency'],
                'transaction_id' => $transaction->id,
                'key_id' => env('RAZORPAY_KEY_ID')
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Razorpay web business order creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Razorpay Payment Signature
     */
    public function verifyBusinessPayment(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'transaction_id' => 'required|integer|exists:transactions,id',
        ]);

        $transaction = null;
        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $razorpay = new \Razorpay\Api\Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
            try {
                $razorpay->utility->verifyPaymentSignature($attributes);
            } catch (\Exception $signatureError) {
                \Log::error('Razorpay signature verification failed on web: ' . $signatureError->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Payment signature verification failed. Invalid credentials.'
                ], 400);
            }

            $transaction = Transaction::where('id', $request->transaction_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $transaction->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'status' => 'completed',
            ]);

            $existingPayment = Payment::where('transaction_id', $transaction->id)->first();
            if (!$existingPayment) {
                Payment::create([
                    'user_id' => $request->user()->id,
                    'transaction_id' => $transaction->id,
                    'payment_id' => $request->razorpay_payment_id,
                    'order_id' => $request->razorpay_order_id,
                    'payment_type' => 'business_registration',
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'payment_method' => 'razorpay',
                    'status' => 'completed',
                    'metadata' => json_encode([
                        'razorpay_order_id' => $request->razorpay_order_id,
                        'subscription_period' => $transaction->subscription_period ?? 1,
                        'original_purpose' => $transaction->purpose
                    ]),
                    'paid_at' => now(),
                    'razorpay_response' => json_encode([
                        'razorpay_payment_id' => $request->razorpay_payment_id,
                        'razorpay_order_id' => $request->razorpay_order_id,
                        'razorpay_signature' => $request->razorpay_signature
                    ])
                ]);
            }

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

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and subscription activated successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Razorpay verification error: ' . $e->getMessage());
            if ($transaction) {
                $transaction->update(['status' => 'failed']);
            }
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Add Business Product
     */
    public function addProduct(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $business = Business::where('id', $request->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'business_id' => $request->business_id,
            'name' => $request->name,
            'description' => $request->description,
            'cost' => $request->cost,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('dashboard.business.index')->with('success', 'Product added successfully!');
    }

    /**
     * Delete Business Product
     */
    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $business = Business::where('id', $product->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();
        return redirect()->route('dashboard.business.index')->with('success', 'Product deleted successfully!');
    }

    /**
     * Add Business Service
     */
    public function addService(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $business = Business::where('id', $request->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        Service::create([
            'business_id' => $request->business_id,
            'name' => $request->name,
            'description' => $request->description,
            'cost' => $request->cost,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('dashboard.business.index')->with('success', 'Service added successfully!');
    }

    /**
     * Delete Business Service
     */
    public function deleteService($id)
    {
        $service = Service::findOrFail($id);
        $business = Business::where('id', $service->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($service->image_path && Storage::disk('public')->exists($service->image_path)) {
            Storage::disk('public')->delete($service->image_path);
        }

        $service->delete();
        return redirect()->route('dashboard.business.index')->with('success', 'Service deleted successfully!');
    }

    /**
     * Post a new Job Listing
     */
    public function addJob(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'salary_range' => 'nullable|string|max:100',
            'job_type' => 'required|string|max:50',
            'location' => 'required|string|max:255',
            'experience_level' => 'required|in:entry,junior,mid,senior,executive',
            'employment_type' => 'required|in:full-time,part-time,contract,freelance,internship',
            'category' => 'required|string|max:100',
        ]);

        $business = Business::where('id', $request->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        JobPosting::create([
            'business_id' => $request->business_id,
            'title' => $request->title,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'salary_range' => $request->salary_range,
            'job_type' => $request->job_type,
            'location' => $request->location,
            'experience_level' => $request->experience_level,
            'employment_type' => $request->employment_type,
            'category' => $request->category,
            'is_active' => true,
            'status' => 'approved', 
            'expires_at' => now()->addDays(30),
        ]);

        return redirect()->route('dashboard.business.index')->with('success', 'Job posting created successfully!');
    }

    /**
     * Update an existing Job Listing
     */
    public function updateJob(Request $request, $id)
    {
        $jobPosting = JobPosting::findOrFail($id);
        $business = Business::where('id', $jobPosting->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'salary_range' => 'nullable|string|max:100',
            'job_type' => 'required|string|max:50',
            'location' => 'required|string|max:255',
            'experience_level' => 'required|in:entry,junior,mid,senior,executive',
            'employment_type' => 'required|in:full-time,part-time,contract,freelance,internship',
            'category' => 'required|string|max:100',
        ]);

        $jobPosting->update($request->only([
            'title', 'description', 'requirements', 'salary_range', 'job_type',
            'location', 'experience_level', 'employment_type', 'category'
        ]));

        return redirect()->route('dashboard.business.index')->with('success', 'Job posting updated successfully!');
    }

    /**
     * Delete a Job Posting
     */
    public function deleteJob($id)
    {
        $jobPosting = JobPosting::findOrFail($id);
        $business = Business::where('id', $jobPosting->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $jobPosting->delete();
        return redirect()->route('dashboard.business.index')->with('success', 'Job posting deleted successfully!');
    }

    /**
     * Toggle Active State of a Job Posting
     */
    public function toggleJob($id)
    {
        $jobPosting = JobPosting::findOrFail($id);
        $business = Business::where('id', $jobPosting->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $jobPosting->update([
            'is_active' => !$jobPosting->is_active
        ]);

        return redirect()->route('dashboard.business.index')->with('success', 'Job active status toggled successfully!');
    }

    /**
     * Update Candidate Job Application Status
     */
    public function updateApplicationStatus(Request $request, $id)
    {
        $application = JobApplication::with('jobPosting.business')->findOrFail($id);
        if ($application->jobPosting->business->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:reviewed,accepted,rejected',
            'employer_notes' => 'nullable|string|max:1000'
        ]);

        $application->update([
            'status' => $request->status,
            'employer_notes' => $request->employer_notes,
            'reviewed_at' => now()
        ]);

        $statusMessageMap = [
            'accepted' => 'Congratulations! Your application for "' . $application->jobPosting->title . '" has been accepted.',
            'rejected' => 'Your application for "' . $application->jobPosting->title . '" has been rejected.',
            'reviewed' => 'Your application for "' . $application->jobPosting->title . '" has been reviewed.',
        ];

        try {
            app(\App\Services\NotificationService::class)->createNotification(
                $application->user_id,
                Notification::TYPE_JOB_APPLICATION_STATUS,
                'Job application status: ' . ucfirst($request->status),
                $statusMessageMap[$request->status] ?? 'Your job application status has been updated.',
                [
                    'job_id' => $application->jobPosting->id,
                    'application_id' => $application->id,
                    'status' => $request->status,
                ],
                '/jobs/my-applications',
                Notification::PRIORITY_MEDIUM,
                $application,
                ['in_app', 'email']
            );
        } catch (\Exception $notifEx) {
            \Log::warning('Web application status notification failed: ' . $notifEx->getMessage());
        }

        return redirect()->route('dashboard.business.index')->with('success', 'Candidate application status updated successfully!');
    }

    /**
     * Delete the user's business
     */
    public function deleteBusiness()
    {
        $user = Auth::user();
        $business = Business::where('user_id', $user->id)->firstOrFail();

        try {
            foreach ($business->products as $product) {
                if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                    Storage::disk('public')->delete($product->image_path);
                }
                $product->delete();
            }

            foreach ($business->services as $service) {
                if ($service->image_path && Storage::disk('public')->exists($service->image_path)) {
                    Storage::disk('public')->delete($service->image_path);
                }
                $service->delete();
            }

            foreach ($business->jobPostings ?? [] as $job) {
                $job->delete();
            }

            if ($business->photo) {
                foreach (explode(',', $business->photo) as $oldP) {
                    $oldP = trim($oldP);
                    if ($oldP && Storage::disk('public')->exists($oldP)) {
                        Storage::disk('public')->delete($oldP);
                    }
                }
            }

            $business->delete();

            if ($user->user_type === 'business') {
                $user->update(['user_type' => 'general']);
            }

            return redirect()->route('dashboard.business.index')->with('success', 'Business deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Web Business deletion failure: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete business: ' . $e->getMessage()]);
        }
    }
}
