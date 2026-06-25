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
     * Show User Dashboard Overview
     */
    public function index(Request $request)
    {
        if (Auth::user()->user_type === 'bloger') {
            return redirect()->route('blogs.index');
        }

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

        // Stats specific to the logged-in user's business
        $userBusiness = $user->business;
        $stats = [
            'total_users' => User::count(),
            'verified_users' => User::where('caste_verification_status', 'approved')->count(),
            'my_donations_sum' => $user->donations()->where('status', 'completed')->sum('amount') ?? 0,
            'my_applications_count' => $user->jobApplications()->count() ?? 0,
            'businesses_count' => $userBusiness ? 1 : 0,
            'products_count' => $userBusiness ? $userBusiness->products()->count() : 0,
            'services_count' => $userBusiness ? $userBusiness->services()->count() : 0,
            'jobs_count' => $userBusiness ? \App\Models\JobPosting::where('business_id', $userBusiness->id)->count() : 0,
        ];

        // Fetch categories & active banner heroes
        $categories = \App\Models\BusinessCategory::where('is_active', true)->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        $banners = \App\Models\HomepageHero::all();
        $featuredBusinesses = \App\Models\Business::where('verification_status', 'approved')
            ->with(['category', 'products', 'services'])
            ->latest()
            ->take(10)
            ->get();

        foreach ($featuredBusinesses as $b) {
            $b->distance = $this->calculateDistance($user->latitude, $user->longitude, $b->latitude, $b->longitude);
        }

        return view('dashboard.dashboard', compact('user', 'stats', 'categories', 'banners', 'featuredBusinesses'));
    }

    /**
     * AJAX Search for active directory businesses
     */
    public function searchDirectoryBusinesses(Request $request)
    {
        $query = $request->query('q');
        $user = Auth::user();
        
        $businesses = \App\Models\Business::where('verification_status', 'approved')
            ->where(function($q) use ($query) {
                $q->where('business_name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('city', 'like', "%{$query}%")
                  ->orWhere('state', 'like', "%{$query}%");
            })
            ->with(['category', 'products', 'services'])
            ->latest()
            ->get()
            ->map(function($b) use ($user) {
                return [
                    'id' => $b->id,
                    'business_name' => $b->business_name,
                    'business_type' => $b->business_type,
                    'category_name' => $b->category ? $b->category->name : 'N/A',
                    'description' => $b->description,
                    'contact_phone' => $b->contact_phone,
                    'contact_email' => $b->contact_email,
                    'city' => $b->city,
                    'state' => $b->state,
                    'address' => $b->address,
                    'pincode' => $b->pincode,
                    'website' => $b->website,
                    'photo' => $b->photo ? asset('storage/' . trim(explode(',', $b->photo)[0])) : null,
                    'products_count' => $b->products->count(),
                    'services_count' => $b->services->count(),
                    'opening_time' => $b->opening_time,
                    'closing_time' => $b->closing_time,
                    'distance' => $this->calculateDistance($user->latitude, $user->longitude, $b->latitude, $b->longitude),
                ];
            });

        return response()->json([
            'success' => true,
            'businesses' => $businesses
        ]);
    }

    /**
     * AJAX Fetch active directory businesses in a specific category
     */
    public function getCategoryBusinesses($id)
    {
        $user = Auth::user();
        $businesses = \App\Models\Business::where('verification_status', 'approved')
            ->where('category_id', $id)
            ->with(['category', 'products', 'services'])
            ->latest()
            ->get()
            ->map(function($b) use ($user) {
                return [
                    'id' => $b->id,
                    'business_name' => $b->business_name,
                    'business_type' => $b->business_type,
                    'category_name' => $b->category ? $b->category->name : 'N/A',
                    'description' => $b->description,
                    'contact_phone' => $b->contact_phone,
                    'contact_email' => $b->contact_email,
                    'city' => $b->city,
                    'state' => $b->state,
                    'address' => $b->address,
                    'pincode' => $b->pincode,
                    'website' => $b->website,
                    'photo' => $b->photo ? asset('storage/' . trim(explode(',', $b->photo)[0])) : null,
                    'products_count' => $b->products->count(),
                    'services_count' => $b->services->count(),
                    'opening_time' => $b->opening_time,
                    'closing_time' => $b->closing_time,
                    'distance' => $this->calculateDistance($user->latitude, $user->longitude, $b->latitude, $b->longitude),
                ];
            });

        return response()->json([
            'success' => true,
            'businesses' => $businesses
        ]);
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
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->only([
            'name', 'email', 'phone', 'age', 'occupation', 'company_name',
            'dept_name', 'dob', 'designation', 'address', 'nearby_location',
            'pincode', 'road_number', 'state', 'city', 'sector', 'district',
            'village', 'destination', 'latitude', 'longitude'
        ]);

        if ($request->filled('name') && $request->name !== $user->name) {
            $data['status'] = 'inactive';
        }

        if ($request->hasFile('photo')) {
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
     * AJAX Update User Geolocation Coordinates
     */
    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        $lat = round(floatval($request->latitude), 6);
        $lon = round(floatval($request->longitude), 6);
        
        $oldLat = $user->latitude ? round(floatval($user->latitude), 6) : null;
        $oldLon = $user->longitude ? round(floatval($user->longitude), 6) : null;
        
        $updated = false;
        if (is_null($oldLat) || is_null($oldLon) || abs($oldLat - $lat) > 0.0001 || abs($oldLon - $lon) > 0.0001) {
            $user->update([
                'latitude' => $lat,
                'longitude' => $lon,
            ]);
            $updated = true;
        }

        session(['current_location_fetched' => true]);

        return response()->json([
            'success' => true,
            'updated' => $updated,
            'latitude' => $lat,
            'longitude' => $lon,
        ]);
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

            $user->tokens()->delete();
            $user->delete();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Your account has been successfully deleted.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete account. ' . $e->getMessage()]);
        }
    }

    /**
     * Display Privacy Policy inside authenticated layout
     */
    public function privacyPolicy()
    {
        $page = \App\Models\Page::where('page_type', 'privacy_policy')
                    ->where('status', 1)
                    ->first();

        return view('dashboard.page', compact('page'));
    }

    /**
     * Display Terms & Conditions inside authenticated layout
     */
    public function termsConditions()
    {
        $page = \App\Models\Page::where('page_type', 'terms_condition')
                    ->where('status', 1)
                    ->first();

        return view('dashboard.page', compact('page'));
    }

    /**
     * Display Contact Support / Us inside authenticated layout
     */
    public function contactSupport()
    {
        $page = \App\Models\Page::where('page_type', 'contact_us')
                    ->where('status', 1)
                    ->first();

        return view('dashboard.page', compact('page'));
    }

    /**
     * Display User's Applied Jobs catalog
     */
    public function appliedJobs(Request $request)
    {
        $applications = \App\Models\JobApplication::where('user_id', Auth::id())
            ->with(['jobPosting.business'])
            ->latest('applied_at')
            ->paginate(10, ['*'], 'applications_page');

        $allJobs = \App\Models\JobPosting::where('is_active', true)
            ->where('status', 'approved')
            ->with('business')
            ->latest()
            ->paginate(10, ['*'], 'jobs_page');

        return view('dashboard.applied_jobs', compact('applications', 'allJobs'));
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
