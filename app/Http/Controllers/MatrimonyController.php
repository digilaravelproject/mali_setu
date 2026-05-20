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

        $request->validate([
            'age'                          => 'required|integer|min:18|max:100',
            'height'                       => 'nullable|string|max:10',
            'weight'                       => 'nullable|string|max:10',
            'complexion'                   => 'nullable|string|max:50',
            'physical_status'              => 'nullable|string|max:50',
            // personal_details fields
            'gender'                       => 'required|in:male,female,other',
            'date_of_birth'                => 'required|date',
            'marital_status'               => 'required|string',
            'mother_tongue'                => 'required|string|max:100',
            'religion'                     => 'required|string|max:100',
            'caste'                        => 'required|string|max:100',
            'sub_caste'                    => 'nullable|string|max:100',
            'profile_created_by'           => 'required|string|max:100',
            // family_details
            'father_name'                  => 'nullable|string|max:200',
            'father_occupation'            => 'nullable|string|max:200',
            'mother_name'                  => 'nullable|string|max:200',
            'mother_occupation'            => 'nullable|string|max:200',
            'no_of_brothers'               => 'nullable|integer|min:0',
            'no_of_sisters'                => 'nullable|integer|min:0',
            'family_type'                  => 'nullable|string|max:100',
            'family_status'                => 'nullable|string|max:100',
            'family_values'                => 'nullable|string|max:100',
            'about_family'                 => 'nullable|string|max:1000',
            // education_details
            'highest_qualification'        => 'required|string|max:200',
            'college_name'                 => 'nullable|string|max:300',
            'passing_year'                 => 'nullable|integer|min:1980|max:2030',
            // professional_details
            'occupation'                   => 'required|string|max:200',
            'company_name'                 => 'nullable|string|max:300',
            'annual_income'                => 'nullable|string|max:100',
            'employment_type'              => 'nullable|string|max:100',
            // location_details
            'country'                      => 'required|string|max:100',
            'state'                        => 'required|string|max:100',
            'city'                         => 'required|string|max:100',
            'pincode'                      => 'nullable|digits:6',
            // lifestyle_details
            'diet'                         => 'nullable|string|max:50',
            'smoking'                      => 'nullable|string|max:50',
            'drinking'                     => 'nullable|string|max:50',
            'hobbies'                      => 'nullable|string|max:500',
            // partner_preferences
            'pref_age_min'                 => 'nullable|integer|min:18',
            'pref_age_max'                 => 'nullable|integer|max:100',
            'pref_height_min'              => 'nullable|string|max:10',
            'pref_religion'                => 'nullable|string|max:100',
            'pref_caste'                   => 'nullable|string|max:100',
            'pref_education'               => 'nullable|string|max:200',
            'pref_income'                  => 'nullable|string|max:100',
            'pref_location'                => 'nullable|string|max:200',
            'about_partner'                => 'nullable|string|max:1000',
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

        $personalDetails = [
            'gender'            => $request->gender,
            'date_of_birth'     => $request->date_of_birth,
            'marital_status'    => $request->marital_status,
            'mother_tongue'     => $request->mother_tongue,
            'religion'          => $request->religion,
            'caste'             => $request->caste,
            'sub_caste'         => $request->sub_caste,
            'profile_created_by'=> $request->profile_created_by,
            'about_me'          => $request->about_me,
            'photos'            => $photoPaths,
        ];

        $familyDetails = [
            'father_name'       => $request->father_name,
            'father_occupation' => $request->father_occupation,
            'mother_name'       => $request->mother_name,
            'mother_occupation' => $request->mother_occupation,
            'no_of_brothers'    => $request->no_of_brothers,
            'no_of_sisters'     => $request->no_of_sisters,
            'family_type'       => $request->family_type,
            'family_class'      => $request->family_status,
            'family_value'      => $request->family_values,
            'about_family'      => $request->about_family,
        ];

        $educationDetails = [
            'highest_qualification' => $request->highest_qualification,
            'college_name'          => $request->college_name,
            'passing_year'          => $request->passing_year,
        ];

        $professionalDetails = [
            'occupation'       => $request->occupation,
            'company_name'     => $request->company_name,
            'annual_income'    => $request->annual_income,
            'employment_type'  => $request->employment_type,
        ];

        $locationDetails = [
            'country' => $request->country,
            'state'   => $request->state,
            'city'    => $request->city,
            'pincode' => $request->pincode,
        ];

        $lifestyleDetails = [
            'diet'    => $request->diet,
            'smoking' => $request->smoking,
            'drinking'=> $request->drinking,
            'hobbies' => $request->hobbies,
        ];

        $partnerPreferences = [
            'age_min'   => $request->pref_age_min,
            'age_max'   => $request->pref_age_max,
            'height_min'=> $request->pref_height_min,
            'religion'  => $request->pref_religion,
            'caste'     => $request->pref_caste,
            'education' => $request->pref_education,
            'income'    => $request->pref_income,
            'location'  => $request->pref_location,
            'about_partner' => $request->about_partner,
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

        $user->update(['user_type' => 'matrimony']);

        return redirect()->route('matrimony.index')
            ->with('success', 'Matrimony profile created! Please subscribe to a plan to activate it.');
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

        $request->validate([
            'age'                   => 'required|integer|min:18|max:100',
            'height'                => 'nullable|string|max:10',
            'weight'                => 'nullable|string|max:10',
            'highest_qualification' => 'required|string|max:200',
            'occupation'            => 'required|string|max:200',
            'country'               => 'required|string|max:100',
            'state'                 => 'required|string|max:100',
            'city'                  => 'required|string|max:100',
            'photos'                => 'nullable|array',
            'photos.*'              => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPaths = $profile->personal_details['photos'] ?? [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('matrimony/photos', 'public');
            }
        }

        $profile->update([
            'age'              => $request->age,
            'height'           => $request->height,
            'weight'           => $request->weight,
            'complexion'       => $request->complexion,
            'physical_status'  => $request->physical_status,
            'personal_details' => array_merge($profile->personal_details ?? [], [
                'marital_status'    => $request->marital_status,
                'mother_tongue'     => $request->mother_tongue,
                'religion'          => $request->religion,
                'caste'             => $request->caste,
                'sub_caste'         => $request->sub_caste,
                'about_me'          => $request->about_me,
                'photos'            => $photoPaths,
            ]),
            'family_details' => array_merge($profile->family_details ?? [], [
                'father_name'       => $request->father_name,
                'father_occupation' => $request->father_occupation,
                'mother_name'       => $request->mother_name,
                'mother_occupation' => $request->mother_occupation,
                'no_of_brothers'    => $request->no_of_brothers,
                'no_of_sisters'     => $request->no_of_sisters,
                'family_type'       => $request->family_type,
                'family_class'      => $request->family_status,
                'family_value'      => $request->family_values,
                'about_family'      => $request->about_family,
            ]),
            'education_details' => array_merge($profile->education_details ?? [], [
                'highest_qualification' => $request->highest_qualification,
                'college_name'          => $request->college_name,
                'passing_year'          => $request->passing_year,
            ]),
            'professional_details' => array_merge($profile->professional_details ?? [], [
                'occupation'      => $request->occupation,
                'company_name'    => $request->company_name,
                'annual_income'   => $request->annual_income,
                'employment_type' => $request->employment_type,
            ]),
            'location_details' => [
                'country' => $request->country,
                'state'   => $request->state,
                'city'    => $request->city,
                'pincode' => $request->pincode,
            ],
            'lifestyle_details' => [
                'diet'    => $request->diet,
                'smoking' => $request->smoking,
                'drinking'=> $request->drinking,
                'hobbies' => $request->hobbies,
            ],
            'partner_preferences' => [
                'age_min'      => $request->pref_age_min,
                'age_max'      => $request->pref_age_max,
                'height_min'   => $request->pref_height_min,
                'religion'     => $request->pref_religion,
                'caste'        => $request->pref_caste,
                'education'    => $request->pref_education,
                'income'       => $request->pref_income,
                'location'     => $request->pref_location,
                'about_partner'=> $request->about_partner,
            ],
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

        if ($request->filled('age_min')) $query->where('age', '>=', $request->age_min);
        if ($request->filled('age_max')) $query->where('age', '<=', $request->age_max);
        if ($request->filled('gender')) $query->where('personal_details->gender', $request->gender);
        if ($request->filled('religion')) $query->where('personal_details->religion', $request->religion);
        if ($request->filled('caste')) $query->where('personal_details->caste', $request->caste);
        if ($request->filled('city')) $query->where('location_details->city', $request->city);
        if ($request->filled('state')) $query->where('location_details->state', $request->state);
        if ($request->filled('marital_status')) $query->where('personal_details->marital_status', $request->marital_status);
        if ($request->filled('education')) $query->where('education_details->highest_qualification', $request->education);

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

        return view('matrimony.requests', compact('sentRequests', 'receivedRequests'));
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

            // Activate matrimony profile expiry
            $profile = MatrimonyProfile::where('user_id', Auth::id())->first();
            if ($profile) {
                $profile->update([
                    'profile_expires_at' => now()->addMonths($transaction->subscription_period ?? 12),
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Payment verified! Your profile is now active.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
