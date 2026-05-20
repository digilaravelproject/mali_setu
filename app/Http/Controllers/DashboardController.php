<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Mail\PasswordChangedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    /**
     * Show User Dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user()->load([
            'casteCertificate',
            'business.products',
            'business.services',
            'business.category',
            'matrimonyProfile',
            'volunteer',
            'donations',
            'jobApplications'
        ]);

        // Copy exact payment/profile checks from API profile endpoint
        $user->is_matrimony = $user->matrimonyProfile ? true : false;
        $user->is_business = $user->business ? true : false;

        $matrimonyPayment = Transaction::where('user_id', $user->id)
            ->where('purpose', 'matrimony_profile')
            ->whereNotNull('razorpay_payment_id')
            ->latest()
            ->first();
        $user->has_matrimony_payment = !is_null($matrimonyPayment);

        $businessPayment = Transaction::where('user_id', $user->id)
            ->where('purpose', 'business_registration')
            ->whereNotNull('razorpay_payment_id')
            ->latest()
            ->first();
        $user->has_business_payment = !is_null($businessPayment);

        // Community stats for overview visual metrics
        $stats = [
            'total_users' => User::count(),
            'verified_users' => User::where('caste_verification_status', 'approved')->count(),
            'my_donations_sum' => $user->donations()->where('status', 'completed')->sum('amount') ?? 0,
            'my_applications_count' => $user->jobApplications()->count() ?? 0,
            'businesses_count' => \App\Models\Business::count(),
            'products_count' => \App\Models\Product::count(),
            'services_count' => \App\Models\Service::count(),
            'jobs_count' => \App\Models\JobPosting::count(),
        ];

        // Fetch categories, plans, and job postings for business module
        $categories = \App\Models\BusinessCategory::where('is_active', true)->get();
        $plans = \App\Models\BusinessPlan::where('active', true)->get();
        
        $jobs = [];
        if ($user->business) {
            $jobs = \App\Models\JobPosting::where('business_id', $user->business->id)
                ->with(['applications.user'])
                ->latest()
                ->get();
        }

        return view('dashboard.dashboard', compact('user', 'stats', 'categories', 'plans', 'jobs'));
    }

    /**
     * Handle Profile Update Request
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone'            => 'required|string|max:15|unique:users,phone,' . $user->id,
            'age'              => 'nullable|integer|min:18|max:100',
            'occupation'       => 'nullable|string|max:255',
            'company_name'     => 'nullable|string|max:255',
            'dept_name'        => 'nullable|string|max:255',
            'dob'              => 'nullable|date',
            'designation'      => 'nullable|string|max:255',
            'address'          => 'nullable|string|max:500',
            'nearby_location'  => 'nullable|string|max:255',
            'pincode'          => 'nullable|digits:6',
            'road_number'      => 'nullable|string|max:50',
            'state'            => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
            'sector'           => 'nullable|string|max:100',
            'district'         => 'nullable|string|max:100',
            'village'          => 'nullable|string|max:100',
            'destination'      => 'nullable|string|max:255',
            'photo'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only([
            'name', 'email', 'phone', 'age', 'occupation', 'company_name',
            'dept_name', 'dob', 'designation', 'address', 'nearby_location',
            'pincode', 'road_number', 'state', 'city', 'sector', 'district',
            'village', 'destination'
        ]);

        // File upload handling for user photo (Premium single/multiple support)
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if (!empty($user->photo)) {
                foreach (explode(',', $user->photo) as $p) {
                    $p = trim($p);
                    if ($p && Storage::disk('public')->exists($p)) {
                        Storage::disk('public')->delete($p);
                    }
                }
            }

            $file = $request->file('photo');
            $fileName = 'profile/photos/' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Ensure folders exist
            if (!Storage::disk('public')->exists('profile/photos')) {
                Storage::disk('public')->makeDirectory('profile/photos');
            }

            Storage::disk('public')->put($fileName, file_get_contents($file));
            $data['photo'] = $fileName;
        }

        $user->update($data);

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
    }

    /**
     * Handle Password Change Request
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Try sending notification mail (do not crash on mail failure)
        try {
            Mail::to($user->email)->send(new PasswordChangedMail($user, $request->password));
        } catch (\Throwable $mailEx) {
            \Log::warning('Web password change email failed', [
                'user_id' => $user->id,
                'error'   => $mailEx->getMessage()
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Password updated successfully!');
    }

    /**
     * Handle Account Deletion Request
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        try {
            // Delete associated files
            if ($user->cast_certificate && Storage::disk('public')->exists($user->cast_certificate)) {
                Storage::disk('public')->delete($user->cast_certificate);
            }

            if (!empty($user->photo)) {
                foreach (explode(',', $user->photo) as $p) {
                    $p = trim($p);
                    if ($p && Storage::disk('public')->exists($p)) {
                        Storage::disk('public')->delete($p);
                    }
                }
            }

            // Revoke tokens and delete user
            $user->tokens()->delete();
            $user->delete();

            // Log out and invalidate session
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Your account has been successfully deleted.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete account. ' . $e->getMessage()]);
        }
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
            // Handle multiple photo uploads
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

            $business = \App\Models\Business::create([
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

            // Update user_type to business if general or blank
            if (empty($user->user_type) || $user->user_type != 'business') {
                $user->update(['user_type' => 'business']);
            }

            // Create notification
            app(\App\Services\NotificationService::class)->createNotification(
                $user->id,
                \App\Models\Notification::TYPE_BUSINESS_VERIFIED,
                'Business registered',
                'Your business "' . $business->business_name . '" has been registered and is pending verification.',
                ['business_id' => $business->id],
                '/business/manage',
                \App\Models\Notification::PRIORITY_MEDIUM,
                $business,
                ['in_app', 'email']
            );

            return redirect()->route('dashboard')->with('success', 'Business registered successfully! Enjoy your 30-day trial.');

        } catch (\Exception $e) {
            \Log::error('Web Business registration failure: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to register business: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Update Business Profile Details
     */
    public function updateBusiness(Request $request)
    {
        $user = Auth::user();
        $business = \App\Models\Business::where('user_id', $user->id)->firstOrFail();

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
                // Delete old business photos
                if ($business->photo) {
                    foreach (explode(',', $business->photo) as $oldP) {
                        $oldP = trim($oldP);
                        if ($oldP && Storage::disk('public')->exists($oldP)) {
                            Storage::disk('public')->delete($oldP);
                        }
                    }
                }

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

            return redirect()->route('dashboard')->with('success', 'Business profile updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Web Business update failure: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to update business: ' . $e->getMessage()]);
        }
    }

    /**
     * Create Razorpay Order for Business Plan Subscription
     */
    public function createBusinessOrder(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:business_plans,id',
        ]);

        $plan = \App\Models\BusinessPlan::find($request->plan_id);
        if (!$plan || !$plan->active) {
            return response()->json([
                'success' => false,
                'message' => 'Selected plan is not available'
            ], 404);
        }

        $amount = $plan->price; // rupees
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

            $transaction = \App\Models\Transaction::create([
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
     * Verify Razorpay Payment Signature and Activate Business Subscription
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
            // Verify signature
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

            // Get transaction
            $transaction = \App\Models\Transaction::where('id', $request->transaction_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            // Update transaction
            $transaction->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'status' => 'completed',
            ]);

            // Create payment record if not exists
            $existingPayment = \App\Models\Payment::where('transaction_id', $transaction->id)->first();
            if (!$existingPayment) {
                \App\Models\Payment::create([
                    'user_id' => $request->user()->id,
                    'transaction_id' => $transaction->id,
                    'payment_id' => $request->razorpay_payment_id,
                    'order_id' => $request->razorpay_order_id,
                    'payment_type' => 'business_registration',
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'payment_method' => 'razorpay',
                    'status' => 'completed',
                    'metadata' => [
                        'razorpay_order_id' => $request->razorpay_order_id,
                        'subscription_period' => $transaction->subscription_period ?? 1,
                        'original_purpose' => $transaction->purpose
                    ],
                    'paid_at' => now(),
                    'razorpay_response' => [
                        'razorpay_payment_id' => $request->razorpay_payment_id,
                        'razorpay_order_id' => $request->razorpay_order_id,
                        'razorpay_signature' => $request->razorpay_signature
                    ]
                ]);
            }

            // Activate business subscription status
            $business = \App\Models\Business::where('user_id', $transaction->user_id)
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

        $business = \App\Models\Business::where('id', $request->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        \App\Models\Product::create([
            'business_id' => $request->business_id,
            'name' => $request->name,
            'description' => $request->description,
            'cost' => $request->cost,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('dashboard')->with('success', 'Product added successfully!');
    }

    /**
     * Delete Business Product
     */
    public function deleteProduct($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $business = \App\Models\Business::where('id', $product->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();
        return redirect()->route('dashboard')->with('success', 'Product deleted successfully!');
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

        $business = \App\Models\Business::where('id', $request->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        \App\Models\Service::create([
            'business_id' => $request->business_id,
            'name' => $request->name,
            'description' => $request->description,
            'cost' => $request->cost,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('dashboard')->with('success', 'Service added successfully!');
    }

    /**
     * Delete Business Service
     */
    public function deleteService($id)
    {
        $service = \App\Models\Service::findOrFail($id);
        $business = \App\Models\Business::where('id', $service->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($service->image_path && Storage::disk('public')->exists($service->image_path)) {
            Storage::disk('public')->delete($service->image_path);
        }

        $service->delete();
        return redirect()->route('dashboard')->with('success', 'Service deleted successfully!');
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

        $business = \App\Models\Business::where('id', $request->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        \App\Models\JobPosting::create([
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
            'status' => 'approved', // Automatically approve or match backend status
            'expires_at' => now()->addDays(30),
        ]);

        return redirect()->route('dashboard')->with('success', 'Job posting created successfully!');
    }

    /**
     * Update an existing Job Listing
     */
    public function updateJob(Request $request, $id)
    {
        $jobPosting = \App\Models\JobPosting::findOrFail($id);
        $business = \App\Models\Business::where('id', $jobPosting->business_id)
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

        return redirect()->route('dashboard')->with('success', 'Job posting updated successfully!');
    }

    /**
     * Delete a Job Posting
     */
    public function deleteJob($id)
    {
        $jobPosting = \App\Models\JobPosting::findOrFail($id);
        $business = \App\Models\Business::where('id', $jobPosting->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $jobPosting->delete();
        return redirect()->route('dashboard')->with('success', 'Job posting deleted successfully!');
    }

    /**
     * Toggle Active State of a Job Posting
     */
    public function toggleJob($id)
    {
        $jobPosting = \App\Models\JobPosting::findOrFail($id);
        $business = \App\Models\Business::where('id', $jobPosting->business_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $jobPosting->update([
            'is_active' => !$jobPosting->is_active
        ]);

        return redirect()->route('dashboard')->with('success', 'Job active status toggled successfully!');
    }

    /**
     * Update Candidate Job Application Status (Review, Accept, Reject)
     */
    public function updateApplicationStatus(Request $request, $id)
    {
        $application = \App\Models\JobApplication::with('jobPosting.business')->findOrFail($id);
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

        // Send Status Updated Notification
        $statusMessageMap = [
            'accepted' => 'Congratulations! Your application for "' . $application->jobPosting->title . '" has been accepted.',
            'rejected' => 'Your application for "' . $application->jobPosting->title . '" has been rejected.',
            'reviewed' => 'Your application for "' . $application->jobPosting->title . '" has been reviewed.',
        ];

        try {
            app(\App\Services\NotificationService::class)->createNotification(
                $application->user_id,
                \App\Models\Notification::TYPE_JOB_APPLICATION_STATUS,
                'Job application status: ' . ucfirst($request->status),
                $statusMessageMap[$request->status] ?? 'Your job application status has been updated.',
                [
                    'job_id' => $application->jobPosting->id,
                    'application_id' => $application->id,
                    'status' => $request->status,
                ],
                '/jobs/my-applications',
                \App\Models\Notification::PRIORITY_MEDIUM,
                $application,
                ['in_app', 'email']
            );
        } catch (\Exception $notifEx) {
            \Log::warning('Web application status notification failed: ' . $notifEx->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Candidate application status updated successfully!');
    }

    /**
     * Delete the user's business
     */
    public function deleteBusiness()
    {
        $user = Auth::user();
        $business = \App\Models\Business::where('user_id', $user->id)->firstOrFail();

        try {
            // Delete associated products
            foreach ($business->products as $product) {
                if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                    Storage::disk('public')->delete($product->image_path);
                }
                $product->delete();
            }

            // Delete associated services
            foreach ($business->services as $service) {
                if ($service->image_path && Storage::disk('public')->exists($service->image_path)) {
                    Storage::disk('public')->delete($service->image_path);
                }
                $service->delete();
            }

            // Delete associated job postings
            foreach ($business->jobPostings ?? [] as $job) {
                $job->delete();
            }

            // Delete business photos
            if ($business->photo) {
                foreach (explode(',', $business->photo) as $oldP) {
                    $oldP = trim($oldP);
                    if ($oldP && Storage::disk('public')->exists($oldP)) {
                        Storage::disk('public')->delete($oldP);
                    }
                }
            }

            $business->delete();

            // Reset user's user_type if desired, or keep as business
            if ($user->user_type === 'business') {
                $user->update(['user_type' => 'general']);
            }

            return redirect()->route('dashboard')->with('success', 'Business deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Web Business deletion failure: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete business: ' . $e->getMessage()]);
        }
    }
}
