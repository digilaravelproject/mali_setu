<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MatrimonyProfile;
use App\Models\MatrimonyPlan;
use App\Models\ConnectionRequest;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Cast;
use App\Models\SubCast;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MatrimonyController extends Controller
{
    /** My Matrimony Profile Hub */
    public function index()
    {
        $user = Auth::user()->load(['matrimonyProfile']);
        $profile = $user->matrimonyProfile;

        $sentRequests = $receivedRequests = $conversations = collect();
        if ($profile) {
            $sentRequests = ConnectionRequest::where('sender_id', $user->id)
                ->with(['receiver.matrimonyProfile'])->latest()->get();
            $receivedRequests = ConnectionRequest::where('receiver_id', $user->id)
                ->where('status', 'pending')
                ->with(['sender.matrimonyProfile'])->latest()->get();
            $conversations = ChatConversation::where('user1_id', $user->id)
                ->orWhere('user2_id', $user->id)
                ->with(['latestMessage'])->latest('last_message_at')->take(5)->get();
        }

        $plans = MatrimonyPlan::where('active', true)->get();
        $matrimonyPayment = Transaction::where('user_id', $user->id)
            ->where('purpose', 'matrimony_profile')
            ->whereNotNull('razorpay_payment_id')
            ->latest()->first();
        $hasPaid = !is_null($matrimonyPayment);

        return view('matrimony.index', compact('user', 'profile', 'plans', 'hasPaid', 'sentRequests', 'receivedRequests', 'conversations'));
    }

    /** Show profile creation form */
    public function create()
    {
        $user = Auth::user();
        if ($user->matrimonyProfile) {
            return redirect()->route('matrimony.index')->with('success', 'You already have a matrimony profile.');
        }
        $casts = Cast::where('is_active', true)->get();
        return view('matrimony.create', compact('casts'));
    }

    /** Store new matrimony profile */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->matrimonyProfile) {
            return redirect()->route('matrimony.index')->with('success', 'Profile already exists.');
        }

        // Calculate age automatically from Date of Birth
        $age = null;
        if ($request->filled('date_of_birth')) {
            try {
                $dob = \Carbon\Carbon::parse($request->date_of_birth);
                $age = $dob->age;
            } catch (\Exception $e) {
                // Ignore parsing errors, let validation handle it
            }
        }
        $request->merge(['age' => $age]);

        $request->validate([
            'first_name'                   => 'required|string|max:100',
            'middle_name'                  => 'nullable|string|max:100',
            'last_name'                    => 'required|string|max:100',
            'profile_created_by'           => 'required|string|max:100',
            'gender'                       => 'required|in:male,female',
            'date_of_birth'                => 'required|date',
            'age'                          => 'required|integer|min:18|max:100',
            'height'                       => 'nullable|string|max:10',
            'weight'                       => 'nullable|string|max:10',
            'complexion'                   => 'nullable|string|max:50',
            'marital_status'               => 'nullable|string|max:100',
            'physical_status'              => 'nullable|string|max:50',
            'mother_tongue'                => 'nullable|string|max:100',
            'citizenship'                  => 'nullable|string|max:100',
            'blood_group'                  => 'nullable|string|max:20',
            'referral_name'                => 'nullable|string|max:200',
            
            // religious horoscope
            'caste'                        => 'required|string|max:100',
            'sub_caste'                    => 'required|string|max:100',
            'star'                         => 'nullable|string|max:100',
            'raasi'                        => 'nullable|string|max:100',
            'manglik'                      => 'nullable|string|max:50',
            'dosh'                         => 'nullable|string|max:50',

            // family_details & Lifestyle
            'family_type'                  => 'required|string|max:100',
            'family_status'                => 'nullable|string|max:100',
            'family_values'                => 'nullable|string|max:100',
            'father_occupation'            => 'nullable|string|max:200',
            'mother_occupation'            => 'nullable|string|max:200',
            'diet'                         => 'nullable|string|max:50',
            'smoking'                      => 'nullable|string|max:50',
            'drinking'                     => 'nullable|string|max:50',

            // education_details & Career
            'highest_qualification'        => 'required|string|max:200',
            'college_name'                 => 'nullable|string|max:300',
            'occupation'                   => 'nullable|string|max:200',
            'employment_type'              => 'required|string|max:100',
            'company_name'                 => 'nullable|string|max:300',
            'annual_income'                => 'nullable|string|max:100',

            // location_details
            'address'                      => 'nullable|string|max:500',
            'pincode'                      => 'required|digits:6',
            'country'                      => 'required|string|max:100',
            'state'                        => 'required|string|max:100',
            'city'                         => 'required|string|max:100',
            'taluka'                       => 'nullable|string|max:100',
            'village'                      => 'nullable|string|max:100',
            'latitude'                     => 'nullable|numeric|between:-90,90',
            'longitude'                    => 'nullable|numeric|between:-180,180',

            // photos
            'photos'                       => 'nullable|array',
            'photos.*'                     => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('matrimony/photos', 'public');
                $photoPaths[] = $path;
            }
        }

        $fullName = trim($request->first_name . ' ' . ($request->middle_name ? $request->middle_name . ' ' : '') . $request->last_name);

        $personalDetails = [
            'first_name'         => $request->first_name,
            'middle_name'        => $request->middle_name,
            'last_name'          => $request->last_name,
            'name'               => $fullName,
            'gender'             => $request->gender,
            'dob'                => $request->date_of_birth,
            'marital_status'     => $request->marital_status,
            'language'           => $request->mother_tongue,
            'mother_tongue'      => $request->mother_tongue,
            'religion'           => ['Hindu', $request->caste],
            'caste'              => $request->caste,
            'sub_caste'          => $request->sub_caste,
            'profile_created_by' => $request->profile_created_by,
            'citizenship'        => $request->citizenship ?? 'Indian',
            'blood_group'        => $request->blood_group,
            'refferal_name'      => $request->referral_name,
            'referral_name'      => $request->referral_name,
            'star_details'       => [
                $request->star,
                $request->raasi,
                'manglik-' . strtolower($request->manglik ?? 'no')
            ],
            'dosh'               => $request->dosh ?? 'No',
            'about_me'           => $request->about_me,
            'photos'             => $photoPaths,
        ];

        $familyDetails = [
            'father'            => $request->father_occupation,
            'mother'            => $request->mother_occupation,
            'father_name'       => $request->father_name,
            'father_occupation' => $request->father_occupation,
            'mother_name'       => $request->mother_name,
            'mother_occupation' => $request->mother_occupation,
            'no_of_brothers'    => 0,
            'no_of_sisters'     => 0,
            'siblings'          => 0,
            'family_type'       => $request->family_type,
            'family_class'      => $request->family_status,
            'family_value'      => $request->family_values,
            'about_family'      => $request->about_family,
        ];

        $educationDetails = [
            'highest_qualification' => $request->highest_qualification,
            'college'               => $request->college_name,
            'college_name'          => $request->college_name,
            'passing_year'          => null,
        ];

        $professionalDetails = [
            'occupation'       => $request->occupation,
            'company_name'     => $request->company_name,
            'annual_income'    => $request->annual_income,
            'employment_type'  => $request->employment_type,
        ];

        $locationDetails = [
            'address'   => $request->address,
            'country'   => $request->country,
            'state'     => $request->state,
            'city'      => $request->city,
            'pincode'   => $request->pincode,
            'taluka'    => $request->taluka,
            'village'   => $request->village,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ];

        $lifestyleDetails = [
            'diet'    => $request->diet,
            'smoking' => $request->smoking,
            'drinking'=> $request->drinking,
            'hobbies' => null,
        ];

        $partnerPreferences = [
            'age_min'   => 18,
            'age_max'   => 40,
            'height_min'=> null,
            'religion'  => 'Hindu',
            'caste'     => null,
            'education' => null,
            'income'    => null,
            'location'  => null,
            'about_partner' => null,
        ];

        $profile = MatrimonyProfile::create([
            'user_id'             => $user->id,
            'age'                 => $request->age,
            'height'              => $request->height,
            'weight'              => $request->weight,
            'complexion'          => $request->complexion,
            'physical_status'     => $request->physical_status,
            'personal_details'    => $personalDetails,
            'family_details'      => $familyDetails,
            'education_details'   => $educationDetails,
            'professional_details'=> $professionalDetails,
            'lifestyle_details'   => $lifestyleDetails,
            'location_details'    => $locationDetails,
            'partner_preferences' => $partnerPreferences,
            'privacy_settings'    => [],
            'approval_status'     => 'pending',
        ]);

        $user->update([
            'user_type' => 'matrimony',
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('matrimony.subscription')
            ->with('success', 'Matrimony profile created! Please select a subscription plan or skip to proceed.');
    }

    /**
     * Show Post-Registration Matrimony Subscription Plan Selection Screen
     */
    public function selectSubscription(Request $request)
    {
        $user = Auth::user();
        $plans = MatrimonyPlan::where('active', true)->get();
        return view('matrimony.subscription', compact('user', 'plans'));
    }

    /** Show edit form */
    public function edit()
    {
        $user = Auth::user();
        $profile = MatrimonyProfile::where('user_id', $user->id)->firstOrFail();
        $casts = Cast::where('is_active', true)->get();
        return view('matrimony.edit', compact('profile', 'casts'));
    }

    /** Update profile */
    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = MatrimonyProfile::where('user_id', $user->id)->firstOrFail();

        // Calculate age automatically from Date of Birth
        $age = null;
        if ($request->filled('date_of_birth')) {
            try {
                $dob = \Carbon\Carbon::parse($request->date_of_birth);
                $age = $dob->age;
            } catch (\Exception $e) {
                // Ignore parsing errors, let validation handle it
            }
        }
        $request->merge(['age' => $age]);

        $request->validate([
            'first_name'                   => 'required|string|max:100',
            'middle_name'                  => 'nullable|string|max:100',
            'last_name'                    => 'required|string|max:100',
            'profile_created_by'           => 'required|string|max:100',
            'gender'                       => 'required|in:male,female',
            'date_of_birth'                => 'required|date',
            'age'                          => 'required|integer|min:18|max:100',
            'height'                       => 'nullable|string|max:10',
            'weight'                       => 'nullable|string|max:10',
            'complexion'                   => 'nullable|string|max:50',
            'marital_status'               => 'nullable|string|max:100',
            'physical_status'              => 'nullable|string|max:50',
            'mother_tongue'                => 'nullable|string|max:100',
            'citizenship'                  => 'nullable|string|max:100',
            'blood_group'                  => 'nullable|string|max:20',
            'referral_name'                => 'nullable|string|max:200',
            
            // religious horoscope
            'caste'                        => 'required|string|max:100',
            'sub_caste'                    => 'required|string|max:100',
            'star'                         => 'nullable|string|max:100',
            'raasi'                        => 'nullable|string|max:100',
            'manglik'                      => 'nullable|string|max:50',
            'dosh'                         => 'nullable|string|max:50',

            // family_details & Lifestyle
            'family_type'                  => 'required|string|max:100',
            'family_status'                => 'nullable|string|max:100',
            'family_values'                => 'nullable|string|max:100',
            'father_occupation'            => 'nullable|string|max:200',
            'mother_occupation'            => 'nullable|string|max:200',
            'diet'                         => 'nullable|string|max:50',
            'smoking'                      => 'nullable|string|max:50',
            'drinking'                     => 'nullable|string|max:50',

            // education_details & Career
            'highest_qualification'        => 'required|string|max:200',
            'college_name'                 => 'nullable|string|max:300',
            'occupation'                   => 'nullable|string|max:200',
            'employment_type'              => 'required|string|max:100',
            'company_name'                 => 'nullable|string|max:300',
            'annual_income'                => 'nullable|string|max:100',

            // location_details
            'address'                      => 'nullable|string|max:500',
            'pincode'                      => 'required|digits:6',
            'country'                      => 'required|string|max:100',
            'state'                        => 'required|string|max:100',
            'city'                         => 'required|string|max:100',
            'taluka'                       => 'nullable|string|max:100',
            'village'                      => 'nullable|string|max:100',
            'latitude'                     => 'nullable|numeric|between:-90,90',
            'longitude'                    => 'nullable|numeric|between:-180,180',

            // photos
            'photos'                       => 'nullable|array',
            'photos.*'                     => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPaths = $request->existing_photos ?? [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('matrimony/photos', 'public');
            }
        }
        $photoPaths = array_slice($photoPaths, 0, 5);

        $fullName = trim($request->first_name . ' ' . ($request->middle_name ? $request->middle_name . ' ' : '') . $request->last_name);

        $personalDetails = [
            'first_name'         => $request->first_name,
            'middle_name'        => $request->middle_name,
            'last_name'          => $request->last_name,
            'name'               => $fullName,
            'gender'             => $request->gender,
            'dob'                => $request->date_of_birth,
            'marital_status'     => $request->marital_status,
            'language'           => $request->mother_tongue,
            'mother_tongue'      => $request->mother_tongue,
            'religion'           => ['Hindu', $request->caste],
            'caste'              => $request->caste,
            'sub_caste'          => $request->sub_caste,
            'profile_created_by' => $request->profile_created_by,
            'citizenship'        => $request->citizenship ?? 'Indian',
            'blood_group'        => $request->blood_group,
            'referral_name'      => $request->referral_name,
            'star_details'       => [
                $request->star,
                $request->raasi,
                'manglik-' . strtolower($request->manglik ?? 'no')
            ],
            'dosh'               => $request->dosh ?? 'No',
            'about_me'           => $request->about_me,
            'photos'             => $photoPaths,
        ];

        $familyDetails = [
            'father'            => $request->father_occupation,
            'mother'            => $request->mother_occupation,
            'father_occupation' => $request->father_occupation,
            'mother_occupation' => $request->mother_occupation,
            'no_of_brothers'    => 0,
            'no_of_sisters'     => 0,
            'siblings'          => 0,
            'family_type'       => $request->family_type,
            'family_class'      => $request->family_status,
            'family_value'      => $request->family_values,
        ];

        $educationDetails = [
            'highest_qualification' => $request->highest_qualification,
            'college'               => $request->college_name,
            'college_name'          => $request->college_name,
            'passing_year'          => null,
        ];

        $professionalDetails = [
            'occupation'       => $request->occupation,
            'company_name'     => $request->company_name,
            'annual_income'    => $request->annual_income,
            'employment_type'  => $request->employment_type,
        ];

        $locationDetails = [
            'address'   => $request->address,
            'country'   => $request->country,
            'state'     => $request->state,
            'city'      => $request->city,
            'pincode'   => $request->pincode,
            'taluka'    => $request->taluka,
            'village'   => $request->village,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ];

        $lifestyleDetails = [
            'diet'    => $request->diet,
            'smoking' => $request->smoking,
            'drinking'=> $request->drinking,
            'hobbies' => null,
        ];

        $profile->update([
            'age'                  => $age,
            'height'               => $request->height,
            'weight'               => $request->weight,
            'complexion'           => $request->complexion,
            'physical_status'      => $request->physical_status,
            'personal_details'     => $personalDetails,
            'family_details'       => $familyDetails,
            'education_details'    => $educationDetails,
            'professional_details' => $professionalDetails,
            'lifestyle_details'    => $lifestyleDetails,
            'location_details'     => $locationDetails,
            'partner_preferences'  => $profile->partner_preferences ?? [],
        ]);

        $user->update([
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('matrimony.index')->with('success', 'Profile updated successfully!');
    }

    /** Delete profile */
    public function destroy()
    {
        $user = Auth::user();
        $profile = MatrimonyProfile::where('user_id', $user->id)->firstOrFail();
        // Remove photos
        foreach ($profile->personal_details['photos'] ?? [] as $photo) {
            if (Storage::disk('public')->exists($photo)) {
                Storage::disk('public')->delete($photo);
            }
        }
        $profile->delete();
        return redirect()->route('matrimony.index')->with('success', 'Matrimony profile deleted.');
    }

    /** Browse/Search approved profiles */
    public function browse(Request $request)
    {
        $user = Auth::user();
        $query = MatrimonyProfile::where('approval_status', 'approved')
            ->where('user_id', '!=', $user->id)
            ->with('user');

        // 1. Basic Details
        if ($request->filled('gender')) {
            $query->where('personal_details->gender', $request->gender);
        }
        if ($request->filled('age_min') || $request->filled('age_max')) {
            $query->whereBetween('age', [$request->age_min ?? 18, $request->age_max ?? 100]);
        }
        if ($request->filled('height_min') || $request->filled('height_max')) {
            $min = $request->filled('height_min') ? floatval($request->height_min) : 4.0;
            $max = $request->filled('height_max') ? floatval($request->height_max) : 7.0;
            $query->where(function($q) use ($min, $max) {
                $q->whereRaw('CAST(height AS DECIMAL(3,1)) BETWEEN ? AND ?', [$min, $max]);
            });
        }
        if ($request->filled('profile_created_by') && $request->profile_created_by !== 'Any') {
            $query->where('personal_details->profile_created_by', $request->profile_created_by);
        }
        if ($request->filled('marital_status') && $request->marital_status !== 'Any') {
            $query->where('personal_details->marital_status', $request->marital_status);
        }
        if ($request->filled('language') && $request->language !== 'Any') {
            $query->where('personal_details->mother_tongue', $request->language);
        }
        if ($request->filled('physical_status') && $request->physical_status !== "Doesn't Matter") {
            $query->where('personal_details->physical_status', $request->physical_status);
        }

        // 2. Professional Details
        if ($request->filled('employment_type') && $request->employment_type !== 'Any') {
            $query->where('professional_details->employment_type', $request->employment_type);
        }
        if ($request->filled('occupation') && $request->occupation !== 'Any') {
            $query->where('professional_details->occupation', $request->occupation);
        }

        // 3. Religion Details
        if ($request->filled('manglik') && $request->manglik !== "Doesn't Matter") {
            $query->whereJsonContains('personal_details->star_details', 'manglik-' . strtolower($request->manglik));
        }
        if ($request->filled('dosh') && $request->dosh !== "Doesn't Matter") {
            $query->where('personal_details->dosh', $request->dosh);
        }

        // 4. Family Details
        if ($request->filled('family_type') && $request->family_type !== "Doesn't Matter") {
            $query->where('family_details->family_type', $request->family_type);
        }
        if ($request->filled('family_value') && $request->family_value !== "Doesn't Matter") {
            $query->where('family_details->family_value', $request->family_value);
        }
        if ($request->filled('family_class') && $request->family_class !== "Doesn't Matter") {
            $query->where('family_details->family_class', $request->family_class);
        }

        // 5. Location Details
        if ($request->filled('country') && $request->country !== 'Any') {
            $query->where('location_details->country', $request->country);
        }
        if ($request->filled('state') && $request->state !== 'Any') {
            $query->where('location_details->state', $request->state);
        }
        if ($request->filled('city') && $request->city !== 'Any') {
            $query->where('location_details->city', $request->city);
        }

        // 6. Lifestyle Details
        if ($request->filled('diet') && $request->diet !== 'Any') {
            $query->where('lifestyle_details->diet', $request->diet);
        }
        if ($request->filled('smoking') && $request->smoking !== 'Any') {
            $query->where('lifestyle_details->smoking', $request->smoking);
        }
        if ($request->filled('drinking') && $request->drinking !== 'Any') {
            $query->where('lifestyle_details->drinking', $request->drinking);
        }

        // 7. Profile Type
        if ($request->filled('photo') && $request->photo === 'yes') {
            $query->whereJsonLength('personal_details->photos', '>', 0);
        }

        // 8. Recently Created
        if ($request->filled('created_at')) {
            switch ($request->created_at) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'last_7_days':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case 'last_30_days':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
                case 'one_week':
                    $query->whereBetween('created_at', [now()->subWeek(), now()]);
                    break;
                case 'one_month':
                    $query->whereBetween('created_at', [now()->subMonth(), now()]);
                    break;
            }
        }

        $profiles = $query->latest()->paginate(12)->withQueryString();

        // Attach connection status for each profile
        $myId = $user->id;
        foreach ($profiles as $p) {
            $conn = ConnectionRequest::where(function($q) use ($myId, $p) {
                $q->where('sender_id', $myId)->where('receiver_id', $p->user_id);
            })->orWhere(function($q) use ($myId, $p) {
                $q->where('sender_id', $p->user_id)->where('receiver_id', $myId);
            })->orderBy('id', 'desc')->first();
            $p->my_connection_status = $conn ? $conn->status : 'none';
            
            if ($p->my_connection_status === 'accepted') {
                $conv = ChatConversation::where(function($q) use ($myId, $p) {
                    $q->where('user1_id', min($myId, $p->user_id))
                      ->where('user2_id', max($myId, $p->user_id));
                })->first();
                $p->my_conversation_id = $conv ? $conv->id : null;
            }
        }

        return view('matrimony.browse', compact('profiles', 'user'));
    }

    /** View single profile */
    public function show($id)
    {
        $user = Auth::user();
        $profile = MatrimonyProfile::with('user')->findOrFail($id);

        $myId = $user->id;
        $conn = ConnectionRequest::where(function($q) use ($myId, $profile) {
            $q->where('sender_id', $myId)->where('receiver_id', $profile->user_id);
        })->orWhere(function($q) use ($myId, $profile) {
            $q->where('sender_id', $profile->user_id)->where('receiver_id', $myId);
        })->orderBy('id', 'desc')->first();

        $connectionStatus = $conn ? $conn->status : 'none';
        $connectionId = $conn ? $conn->id : null;

        // Find conversation if accepted
        $conversation = null;
        if ($connectionStatus === 'accepted') {
            $conversation = ChatConversation::where(function($q) use ($myId, $profile) {
                $q->where('user1_id', min($myId, $profile->user_id))
                  ->where('user2_id', max($myId, $profile->user_id));
            })->first();
        }

        return view('matrimony.show', compact('profile', 'connectionStatus', 'connectionId', 'conversation', 'user'));
    }

    /** Send connection request */
    public function sendRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message'     => 'nullable|string|max:500',
        ]);

        $existing = ConnectionRequest::where('sender_id', Auth::id())
            ->where('receiver_id', $request->receiver_id)
            ->first();

        if ($existing) {
            return back()->withErrors(['error' => 'Connection request already sent.']);
        }

        ConnectionRequest::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'Connection request sent successfully!');
    }

    /** View all requests */
    public function requests()
    {
        $user = Auth::user();
        $sentRequests = ConnectionRequest::where('sender_id', $user->id)
            ->with(['receiver.matrimonyProfile'])->latest()->get();
        $receivedRequests = ConnectionRequest::where('receiver_id', $user->id)
            ->with(['sender.matrimonyProfile'])->latest()->get();

        // Let's ensure conversations exist for all accepted requests!
        $acceptedSent = $sentRequests->where('status', 'accepted');
        $acceptedReceived = $receivedRequests->where('status', 'accepted');
        
        foreach ($acceptedSent as $req) {
            ChatConversation::firstOrCreate([
                'user1_id' => min($user->id, $req->receiver_id),
                'user2_id' => max($user->id, $req->receiver_id),
            ]);
        }
        foreach ($acceptedReceived as $req) {
            ChatConversation::firstOrCreate([
                'user1_id' => min($user->id, $req->sender_id),
                'user2_id' => max($user->id, $req->sender_id),
            ]);
        }

        // Fetch conversations to link directly to chat
        $conversations = ChatConversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->get()
            ->keyBy(function($conv) use ($user) {
                return (int)$conv->user1_id === (int)$user->id ? (int)$conv->user2_id : (int)$conv->user1_id;
            });

        return view('matrimony.requests', compact('sentRequests', 'receivedRequests', 'conversations'));
    }

    /** Accept or Reject a connection request */
    public function respondRequest(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $conn = ConnectionRequest::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $conn->update([
            'status'       => $request->status,
            'responded_at' => now(),
        ]);

        if ($request->status === 'accepted') {
            ChatConversation::firstOrCreate([
                'user1_id' => min($conn->sender_id, $conn->receiver_id),
                'user2_id' => max($conn->sender_id, $conn->receiver_id),
            ]);
        }

        return back()->with('success', 'Request ' . $request->status . ' successfully!');
    }

    /** List all conversations */
    public function conversations()
    {
        $user = Auth::user();
        $conversations = ChatConversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with(['user1.matrimonyProfile', 'user2.matrimonyProfile', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('matrimony.conversations', compact('conversations', 'user'));
    }

    /** Show chat for a specific conversation */
    public function chat($conversationId)
    {
        $user = Auth::user();
        $conversation = ChatConversation::where('id', $conversationId)
            ->where(function($q) use ($user) {
                $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })->with(['user1.matrimonyProfile', 'user2.matrimonyProfile'])->firstOrFail();

        $messages = ChatMessage::where('conversation_id', $conversationId)
            ->with('sender')->orderBy('id', 'asc')->get();

        // Mark unread messages as read
        ChatMessage::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $otherUser = $conversation->user1_id === $user->id ? $conversation->user2 : $conversation->user1;

        return view('matrimony.chat', compact('conversation', 'messages', 'otherUser', 'user'));
    }

    /** AJAX: Send a message */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|integer|exists:chat_conversations,id',
            'message_text'    => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $conversation = ChatConversation::where('id', $request->conversation_id)
            ->where(function($q) use ($user) {
                $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })->firstOrFail();

        $message = ChatMessage::create([
            'conversation_id' => $request->conversation_id,
            'sender_id'       => $user->id,
            'message_text'    => $request->message_text,
            'message_type'    => 'text',
        ]);

        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender'),
        ]);
    }

    /** Fetch latest messages (AJAX polling) */
    public function fetchMessages(Request $request, $conversationId)
    {
        $user = Auth::user();
        $after = $request->get('after_id', 0);

        $messages = ChatMessage::where('conversation_id', $conversationId)
            ->where('id', '>', $after)
            ->with('sender')
            ->orderBy('id', 'asc')
            ->get();

        ChatMessage::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'messages' => $messages]);
    }

    /** Create Razorpay Order for matrimony plan */
    public function createOrder(Request $request)
    {
        $request->validate(['plan_id' => 'required|integer|exists:matrimony_plans,id']);

        $plan = MatrimonyPlan::findOrFail($request->plan_id);
        if (!$plan->active) {
            return response()->json(['success' => false, 'message' => 'Plan not available.'], 404);
        }

        try {
            $razorpay = new \Razorpay\Api\Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
            $order = $razorpay->order->create([
                'receipt'         => 'matrimony_' . $plan->id . '_' . time(),
                'amount'          => $plan->price * 100,
                'currency'        => 'INR',
                'payment_capture' => 1,
            ]);

            $transaction = Transaction::create([
                'user_id'             => Auth::id(),
                'amount'              => $plan->price,
                'currency'            => 'INR',
                'purpose'             => 'matrimony_profile',
                'razorpay_order_id'   => $order['id'],
                'status'              => 'pending',
                'subscription_period' => intval($plan->duration_years) * 12,
                'meta'                => json_encode(['plan_id' => $plan->id]),
            ]);

            return response()->json([
                'success'        => true,
                'order_id'       => $order['id'],
                'amount'         => $order['amount'],
                'currency'       => $order['currency'],
                'transaction_id' => $transaction->id,
                'key_id'         => env('RAZORPAY_KEY_ID'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Verify Razorpay Payment */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
            'transaction_id'      => 'required|integer|exists:transactions,id',
        ]);

        try {
            $razorpay = new \Razorpay\Api\Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
            $razorpay->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);

            $transaction = Transaction::where('id', $request->transaction_id)
                ->where('user_id', Auth::id())->firstOrFail();
            $transaction->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'status'              => 'completed',
            ]);

            // Activate matrimony profile expiry & auto-approve
            $profile = MatrimonyProfile::where('user_id', Auth::id())->first();
            if ($profile) {
                $profile->update([
                    'profile_expires_at' => now()->addMonths($transaction->subscription_period ?? 12),
                    'approval_status'    => 'approved',
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Payment verified! Your profile is now active.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
