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
            'casteCertificate'
        ]);

        $businesses = Business::where('user_id', $user->id)
            ->with(['products', 'services', 'category'])
            ->get();

        $user->is_business = $businesses->isNotEmpty();

        $activeBusiness = null;
        if ($request->filled('business_id')) {
            $activeBusiness = $businesses->where('id', $request->business_id)->first();
        }
        if (!$activeBusiness) {
            $activeBusiness = $businesses->first();
        }

        $businessPayment = null;
        if ($activeBusiness) {
            $businessPayment = Transaction::where('user_id', $user->id)
                ->where('purpose', 'business_registration')
                ->whereNotNull('razorpay_payment_id')
                ->latest()
                ->first();
        }
        $user->has_business_payment = !is_null($businessPayment);

        $categories = BusinessCategory::where('is_active', true)->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        $plans = BusinessPlan::where('active', true)->get();
        
        $jobs = [];
        if ($activeBusiness) {
            $jobs = JobPosting::where('business_id', $activeBusiness->id)
                ->with(['applications.user'])
                ->latest()
                ->get();
        }

        // Stats specific to the active business
        $stats = [
            'businesses_count' => $businesses->count(),
            'products_count' => $activeBusiness ? $activeBusiness->products()->count() : 0,
            'services_count' => $activeBusiness ? $activeBusiness->services()->count() : 0,
            'jobs_count' => $activeBusiness ? JobPosting::where('business_id', $activeBusiness->id)->count() : 0,
        ];

        // Job Analytics Integration (Component 3)
        $jobAnalytics = null;
        if ($activeBusiness) {
            $businessId = $activeBusiness->id;
            $jobAnalytics = [
                'total_jobs' => JobPosting::where('business_id', $businessId)->count(),
                'active_jobs' => JobPosting::where('business_id', $businessId)->where('is_active', true)->count(),
                'pending_jobs' => JobPosting::where('business_id', $businessId)->where('status', 'pending')->count(),
                'total_applications' => JobApplication::whereHas('jobPosting', function($q) use ($businessId) {
                    $q->where('business_id', $businessId);
                })->count(),
                'pending_applications' => JobApplication::whereHas('jobPosting', function($q) use ($businessId) {
                    $q->where('business_id', $businessId);
                })->where('status', 'pending')->count(),
                'accepted_applications' => JobApplication::whereHas('jobPosting', function($q) use ($businessId) {
                    $q->where('business_id', $businessId);
                })->where('status', 'accepted')->count(),
                'recent_applications' => JobApplication::with(['user', 'jobPosting'])
                    ->whereHas('jobPosting', function($q) use ($businessId) {
                        $q->where('business_id', $businessId);
                    })
                    ->latest('applied_at')
                    ->limit(5)
                    ->get()
            ];
        }

        return view('business.index', compact('user', 'businesses', 'activeBusiness', 'stats', 'categories', 'plans', 'jobs', 'jobAnalytics'));
    }

    /**
     * Show Business Registration Form
     */
    public function create()
    {
        $categories = BusinessCategory::where('is_active', true)->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        return view('business.create', compact('categories'));
    }

    /**
     * Register a new Business Profile
     */
    public function registerBusiness(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required',
            'category_id' => 'required|integer|exists:business_categories,id',
            'description' => 'required|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
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
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            \App\Jobs\SendPaymentPendingEmail::dispatch('business', $business->id)->delay(now()->addMinutes(11));

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

            return redirect()->route('dashboard.business.subscription', ['business_id' => $business->id])->with('success', 'Business registered successfully! Please select a subscription plan or skip to proceed.');

        } catch (\Exception $e) {
            \Log::error('Web Business registration failure: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to register business: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show Post-Registration Subscription Plan Selection Screen
     */
    public function selectSubscription(Request $request)
    {
        $user = Auth::user();
        $businessId = $request->query('business_id');
        
        $business = Business::where('user_id', $user->id)
            ->when($businessId, function($q) use ($businessId) {
                return $q->where('id', $businessId);
            })
            ->firstOrFail();

        $plans = BusinessPlan::where('active', true)->get();

        // Filter plans by business type if business exists
        if ($business && $business->business_type) {
            $businessType = trim(str_replace(' ', '', $business->business_type));
            $plans = $plans->filter(function($p) use ($businessType) {
                $planType = trim(str_replace(' ', '', $p->company_type ?? ''));
                return $planType === $businessType;
            });
        }

        return view('business.subscription', compact('user', 'business', 'plans'));
    }

    /**
     * Show Business Editing Form
     */
    public function edit(Request $request)
    {
        $user = Auth::user();
        $businessId = $request->query('business_id');
        if ($businessId) {
            $business = Business::where('user_id', $user->id)->where('id', $businessId)->firstOrFail();
        } else {
            $business = Business::where('user_id', $user->id)->firstOrFail();
        }
        $categories = BusinessCategory::where('is_active', true)->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        return view('business.edit', compact('business', 'categories'));
    }

    /**
     * Update Business Profile Details
     */
    public function updateBusiness(Request $request)
    {
        $user = Auth::user();
        $businessId = $request->input('business_id');
        if ($businessId) {
            $business = Business::where('user_id', $user->id)->where('id', $businessId)->firstOrFail();
        } else {
            $business = Business::where('user_id', $user->id)->firstOrFail();
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
            'district' => 'nullable|string|max:100',
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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
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
                'opening_time', 'closing_time', 'latitude', 'longitude'
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
        $categories = BusinessCategory::where('is_active', true)->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        
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

        $user = Auth::user();
        if ($user) {
            foreach ($businesses as $b) {
                $b->distance = $this->calculateDistance($user->latitude, $user->longitude, $b->latitude, $b->longitude);
            }
        }

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

        $user = Auth::user();
        if ($user) {
            $business->distance = $this->calculateDistance($user->latitude, $user->longitude, $business->latitude, $business->longitude);
        }

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
            $ccavenue = app(\App\Services\CCAvenue::class);
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
                'payment_way' => 'ccavenue',
                'payment_url' => $ccavenue->getPaymentUrl(),
                'encRequest' => $ccavenue->encrypt($params),
                'access_code' => $ccavenue->getAccessCode(),
                'transaction_id' => $transaction->id
            ], 201);

        } catch (\Exception $e) {
            \Log::error('CCAvenue web business order initiation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Razorpay Payment Signature (Legacy Razorpay route)
     */
    public function verifyBusinessPayment(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'This payment verification endpoint is deprecated. Payment is processed via callback.'
        ], 400);
    }

    /**
     * Add Business Product
     */
    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|integer|exists:businesses,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'image.required' => 'Product image is required.',
            'name.required' => 'Product name is required.',
            'description.required' => 'Product description is required.',
            'cost.required' => 'Product cost is required.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'product')->withInput();
        }

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

        return redirect()->route('dashboard.business.index', ['business_id' => $request->business_id])->with('success', 'Product added successfully!');
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
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|integer|exists:businesses,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'image.required' => 'Service image is required.',
            'name.required' => 'Service name is required.',
            'description.required' => 'Service description is required.',
            'cost.required' => 'Service charge is required.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'service')->withInput();
        }

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

        return redirect()->route('dashboard.business.index', ['business_id' => $request->business_id])->with('success', 'Service added successfully!');
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
     * Apply for a Job Listing on the Web
     */
    public function applyJob(Request $request)
    {
        $request->validate([
            'job_posting_id' => 'required|exists:job_postings,id',
            'cover_letter' => 'required|string|max:2000',
            'resume' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,bmp,webp,svg|max:5120', // 5MB max
            'additional_info' => 'nullable|string|max:1000'
        ]);

        $jobPosting = JobPosting::findOrFail($request->job_posting_id);

        // Check if user is business owner of this job posting (they can't apply to their own job)
        if ($jobPosting->business && $jobPosting->business->user_id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot apply to your own job opening!']);
        }

        // Check if user has already applied
        if ($jobPosting->hasUserApplied(Auth::id())) {
            return back()->withErrors(['error' => 'You have already applied for this job!']);
        }

        // Upload resume
        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
        }

        $application = JobApplication::create([
            'user_id' => Auth::id(),
            'job_posting_id' => $request->job_posting_id,
            'cover_letter' => $request->cover_letter,
            'resume_url' => $resumePath,
            'additional_info' => $request->additional_info,
            'status' => 'pending',
            'applied_at' => now()
        ]);

        try {
            // Push Notification to Applicant
            app(\App\Services\NotificationService::class)->createNotification(
                Auth::id(),
                Notification::TYPE_JOB_APPLICATION,
                'Job application submitted',
                'You have applied for "' . $jobPosting->title . '" at ' . optional($jobPosting->business)->business_name . '.',
                [
                    'job_id' => $jobPosting->id,
                    'application_id' => $application->id,
                ],
                '/jobs/applications',
                Notification::PRIORITY_MEDIUM,
                $application,
                ['in_app', 'email']
            );

            // Push Notification to Business Owner
            if ($jobPosting->business && $jobPosting->business->user_id) {
                app(\App\Services\NotificationService::class)->notifyJobApplication($jobPosting, Auth::user());
            }
        } catch (\Exception $e) {
            \Log::warning('Web Job Application notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Your application has been submitted successfully!');
    }

    /**
     * Post a new Job Listing
     */
    public function addJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|integer|exists:businesses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'salary_range' => 'required|string|max:100',
            'job_type' => 'required|string|max:50',
            'location' => 'required|string|max:255',
            'experience_level' => 'required|in:entry,junior,mid,senior,executive',
            'employment_type' => 'required|in:full-time,part-time,contract,freelance,internship',
            'category' => 'required|string|max:100',
            'application_deadline' => 'required|date',
            'expires_at' => 'required|date',
            'skills_required' => 'nullable|array',
            'benefits' => 'nullable|array',
        ], [
            'title.required' => 'Please enter job title',
            'description.required' => 'Please enter job description',
            'requirements.required' => 'Please enter requirements',
            'salary_range.required' => 'Salary range is required',
            'job_type.required' => 'Please select job type',
            'location.required' => 'Location is required',
            'experience_level.required' => 'Please select experience level',
            'employment_type.required' => 'Please select employment type',
            'category.required' => 'Please select job category',
            'application_deadline.required' => 'Please enter Application Deadline',
            'expires_at.required' => 'Please enter Job Expiry Date'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'job')->withInput();
        }

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
            'application_deadline' => $request->application_deadline,
            'expires_at' => $request->expires_at,
            'skills_required' => $request->skills_required ?? [],
            'benefits' => $request->benefits ?? [],
        ]);

        return redirect()->route('dashboard.business.index', ['business_id' => $request->business_id])->with('success', 'Job posting created successfully!');
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
    public function deleteBusiness(Request $request)
    {
        $user = Auth::user();
        $businessId = $request->input('business_id');
        if ($businessId) {
            $business = Business::where('user_id', $user->id)->where('id', $businessId)->firstOrFail();
        } else {
            $business = Business::where('user_id', $user->id)->firstOrFail();
        }
 
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
 
            $hasOtherBusiness = Business::where('user_id', $user->id)->exists();
            if (!$hasOtherBusiness && $user->user_type === 'business') {
                $user->update(['user_type' => 'general']);
            }
 
            return redirect()->route('dashboard.business.index')->with('success', 'Business deleted successfully.');
 
        } catch (\Exception $e) {
            \Log::error('Web Business deletion failure: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete business: ' . $e->getMessage()]);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (is_null($lat1) || is_null($lon1) || is_null($lat2) || is_null($lon2)) {
            return null;
        }

        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 1); // 1 decimal place e.g. 5.4 km
    }
}
