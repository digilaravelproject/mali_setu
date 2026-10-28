<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cast;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ConnectionRequest;
use App\Models\Education;
use App\Models\MatrimonyProfile;
use App\Models\Payment;
use App\Models\SubCast;
use App\Models\Transaction;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MatrimonyManagementController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    /**
     * Display all matrimony profiles
     */
    public function index(Request $request)
    {
        $query = MatrimonyProfile::with(['user', 'matrimonyTransactions']);

        // Filter by approval status
        if ($request->has('approval_status') && $request->approval_status !== '') {
            $query->where('approval_status', $request->approval_status);
        }

        // Filter by the gender saved inside personal_details.
        if ($request->filled('gender')) {
            $query->adminGender($request->string('gender')->toString());
        }

        // Filter by age range
        if ($request->has('age_min') && $request->age_min !== '') {
            $query->where('age', '>=', $request->age_min);
        }
        if ($request->has('age_max') && $request->age_max !== '') {
            $query->where('age', '<=', $request->age_max);
        }

        // Search by candidate name or the owning user account.
        if ($request->filled('search')) {
            $query->adminSearch($request->string('search')->toString());
        }

        $profiles = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => MatrimonyProfile::count(),
            'approved' => MatrimonyProfile::where('approval_status', 'approved')->count(),
            'pending' => MatrimonyProfile::where('approval_status', 'pending')->count(),
            'rejected' => MatrimonyProfile::where('approval_status', 'rejected')->count(),
            'connections' => ConnectionRequest::where('status', 'accepted')->count(),
            'active_connections' => ConnectionRequest::where('status', 'accepted')->count(),
            'total_connections' => ConnectionRequest::count(),
        ];

        return view('admin.matrimony.index', compact('profiles', 'stats'));
    }

    /**
     * Display pending profile moderations
     */
    public function moderation(Request $request)
    {
        $query = MatrimonyProfile::with(['user', 'matrimonyTransactions']);

        // Search functionality
        if ($request->filled('search')) {
            $query->adminSearch($request->string('search')->toString());
        }

        $pendingProfiles = $query->orderByRaw("FIELD(approval_status, 'pending', 'approved', 'rejected')")
            ->latest()
            ->paginate(15);

        $stats = [
            'pending_count' => MatrimonyProfile::where('approval_status', 'pending')->count(),
            'approved_today' => MatrimonyProfile::where('approval_status', 'approved')
                ->whereDate('updated_at', today())->count(),
            'rejected_today' => MatrimonyProfile::where('approval_status', 'rejected')
                ->whereDate('updated_at', today())->count(),
        ];

        return view('admin.matrimony.moderation', compact('pendingProfiles', 'stats'));
    }

    /**
     * Approve matrimony profile
     */
    public function approve(Request $request, $profileId)
    {
        try {
            $profile = MatrimonyProfile::findOrFail($profileId);

            $profile->update([
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            try {
                $this->notifications->notifyMatrimonyApproved($profile->fresh());
            } catch (\Throwable $notificationError) {
                Log::error('Matrimony profile approved but its notification failed', [
                    'profile_id' => $profile->id,
                    'user_id' => $profile->user_id,
                    'error' => $notificationError->getMessage(),
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile approved successfully!',
                ]);
            }

            return redirect()->back()->with('success', 'Profile approved successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve profile: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to approve profile.');
        }
    }

    /**
     * Reject matrimony profile
     */
    public function reject(Request $request, $profileId)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            $profile = MatrimonyProfile::findOrFail($profileId);

            $profile->update([
                'approval_status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            try {
                $this->notifications->notifyMatrimonyRejected(
                    $profile->fresh(),
                    $request->rejection_reason
                );
            } catch (\Throwable $notificationError) {
                Log::error('Matrimony profile rejected but its notification failed', [
                    'profile_id' => $profile->id,
                    'user_id' => $profile->user_id,
                    'error' => $notificationError->getMessage(),
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile rejected successfully!',
                ]);
            }

            return redirect()->back()->with('success', 'Profile rejected successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject profile: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to reject profile.');
        }
    }

    /**
     * Show form to create matrimony profile
     */
    public function create()
    {
        // Get all users who don't have a matrimony profile already
        $users = User::whereDoesntHave('matrimonyProfile')->orderBy('name')->get();

        // Fetch active castes and subcasts
        $castes = Cast::where('is_active', true)->orderBy('name')->get();
        $subcastes = SubCast::where('is_active', true)->orderBy('name')->get();
        $educations = Education::where('is_active', true)->orderBy('highest_qualification')->get();

        return view('admin.matrimony.create', compact('users', 'castes', 'subcastes', 'educations'));
    }

    /**
     * Store new matrimony profile
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id|unique:matrimony_profiles,user_id',
            'age' => 'required|integer|min:18|max:100',
            'gender' => 'required|string|in:male,female,other,Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'time_of_birth' => 'nullable|string',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'complexion' => 'nullable|string|max:50',
            'physical_status' => 'nullable|string|max:50',
            'approval_status' => 'required|in:pending,approved,rejected',
            'profile_expires_at' => 'nullable|date',

            'personal_details' => 'required|array',
            'family_details' => 'required|array',
            'education_details' => 'required|array',
            'professional_details' => 'required|array',
            'lifestyle_details' => 'nullable|array',
            'location_details' => 'required|array',
            'partner_preferences' => 'required|array',
            'privacy_settings' => 'nullable|array',
            'photos' => 'required|array|min:2|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Upload photos if any
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                $photoPaths[] = $photoFile->store('matrimony/photos', 'public');
            }
        }

        $personal = $request->personal_details;
        $personal['gender'] = $request->gender;
        $personal['photos'] = $photoPaths;

        $first = $personal['first_name'] ?? '';
        $middle = $personal['middle_name'] ?? '';
        $last = $personal['last_name'] ?? '';
        $personal['name'] = trim($first.' '.($middle ? $middle.' ' : '').$last);

        // Ensure front-end keys compatibility
        $personal['dob'] = $request->date_of_birth;
        $personal['date_of_birth'] = $request->date_of_birth;
        $personal['language'] = $personal['mother_tongue'] ?? '';
        $personal['religion'] = ['Hindu', $personal['caste'] ?? ''];
        $personal['star_details'] = [
            $personal['star'] ?? '',
            $personal['raasi'] ?? '',
            'manglik-'.strtolower($personal['manglik'] ?? 'no'),
        ];
        $personal['refferal_name'] = $personal['referral_name'] ?? '';

        // Synchronize family details compatibility
        $family = $request->family_details;
        $family['father'] = $family['father_occupation'] ?? '';
        $family['mother'] = $family['mother_occupation'] ?? '';
        $family['father_occupation'] = $family['father_occupation'] ?? '';
        $family['mother_occupation'] = $family['mother_occupation'] ?? '';
        $family['family_type'] = $family['family_type'] ?? '';
        $family['family_class'] = $family['family_class'] ?? '';
        $family['family_value'] = $family['family_value'] ?? '';

        // Synchronize education details compatibility
        $education = $request->education_details;
        $education['college'] = $education['college_name'] ?? '';
        $education['college_name'] = $education['college_name'] ?? '';

        // Synchronize professional details compatibility
        $professional = $request->professional_details;
        $professional['job_title'] = $professional['occupation'] ?? '';
        $professional['occupation'] = $professional['occupation'] ?? '';
        $professional['company'] = $professional['company_name'] ?? '';
        $professional['company_name'] = $professional['company_name'] ?? '';
        $professional['employment_type'] = $personal['employment_type'] ?? '';
        $professional['annual_income'] = $personal['annual_income'] ?? '';

        $profile = MatrimonyProfile::create([
            'user_id' => $request->user_id,
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'complexion' => $request->complexion,
            'physical_status' => $request->physical_status,
            'personal_details' => $personal,
            'family_details' => $family,
            'education_details' => $education,
            'professional_details' => $professional,
            'lifestyle_details' => $request->lifestyle_details ?? [],
            'location_details' => $request->location_details,
            'partner_preferences' => $request->partner_preferences,
            'privacy_settings' => $request->privacy_settings ?? [],
            'approval_status' => $request->approval_status,
            'profile_expires_at' => $request->profile_expires_at,
        ]);

        // Update user type to matrimony if needed
        $user = User::find($request->user_id);
        if ($user && (empty($user->user_type) || $user->user_type != 'matrimony')) {
            $user->update(['user_type' => 'matrimony']);
        }

        return redirect()->route('admin.matrimony.show', $profile->id)
            ->with('success', 'Matrimony profile created successfully.');
    }

    /**
     * Edit matrimony profile
     */
    public function edit($id)
    {
        $profile = MatrimonyProfile::findOrFail($id);
        $castes = Cast::where('is_active', true)->orderBy('name')->get();
        $subcastes = SubCast::where('is_active', true)->orderBy('name')->get();
        $educations = Education::where('is_active', true)->orderBy('highest_qualification')->get();

        return view('admin.matrimony.edit', compact('profile', 'castes', 'subcastes', 'educations'));
    }

    /**
     * Update matrimony profile
     */
    public function update(Request $request, $id)
    {
        $profile = MatrimonyProfile::findOrFail($id);

        $request->validate([
            'age' => 'required|integer|min:18|max:100',
            'gender' => 'required|string|in:male,female,other,Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'time_of_birth' => 'nullable|string',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'complexion' => 'nullable|string|max:50',
            'physical_status' => 'nullable|string|max:50',
            'approval_status' => 'required|in:pending,approved,rejected',
            'profile_expires_at' => 'nullable|date',

            'personal_details' => 'required|array',
            'family_details' => 'required|array',
            'education_details' => 'required|array',
            'professional_details' => 'required|array',
            'lifestyle_details' => 'nullable|array',
            'location_details' => 'required|array',
            // 'partner_preferences' => 'required|array',
            'privacy_settings' => 'nullable|array',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Keep existing photos if no new photos are uploaded
        $existingPersonal = $profile->personal_details ?? [];
        $existingPhotos = $existingPersonal['photos'] ?? [];

        // Upload photos if any
        $photoPaths = $existingPhotos;
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                $photoPaths[] = $photoFile->store('matrimony/photos', 'public');
            }
        }

        // Handle deleted photos if admin removes them
        if ($request->has('deleted_photos')) {
            foreach ($request->deleted_photos as $delPhoto) {
                if (($key = array_search($delPhoto, $photoPaths)) !== false) {
                    unset($photoPaths[$key]);
                    // Delete actual file
                    if (\Storage::disk('public')->exists($delPhoto)) {
                        \Storage::disk('public')->delete($delPhoto);
                    }
                }
            }
            $photoPaths = array_values($photoPaths); // re-index
        }

        if (count($photoPaths) < 2) {
            return redirect()->back()->withInput()->withErrors(['photos' => 'At least 2 photos are required.']);
        }

        $personal = $request->personal_details;
        $personal['gender'] = $request->gender;
        $personal['photos'] = $photoPaths;

        $first = $personal['first_name'] ?? '';
        $middle = $personal['middle_name'] ?? '';
        $last = $personal['last_name'] ?? '';
        $personal['name'] = trim($first.' '.($middle ? $middle.' ' : '').$last);

        // Ensure front-end keys compatibility
        $personal['dob'] = $request->date_of_birth;
        $personal['date_of_birth'] = $request->date_of_birth;
        $personal['language'] = $personal['mother_tongue'] ?? '';
        $personal['religion'] = ['Hindu', $personal['caste'] ?? ''];
        $personal['star_details'] = [
            $personal['star'] ?? '',
            $personal['raasi'] ?? '',
            'manglik-'.strtolower($personal['manglik'] ?? 'no'),
        ];
        $personal['refferal_name'] = $personal['referral_name'] ?? '';

        // Synchronize family details compatibility
        $family = $request->family_details;
        $family['father'] = $family['father_occupation'] ?? '';
        $family['mother'] = $family['mother_occupation'] ?? '';
        $family['father_occupation'] = $family['father_occupation'] ?? '';
        $family['mother_occupation'] = $family['mother_occupation'] ?? '';
        $family['family_type'] = $family['family_type'] ?? '';
        $family['family_class'] = $family['family_class'] ?? '';
        $family['family_value'] = $family['family_value'] ?? '';

        // Preserve other existing family details keys if present in DB
        $existingFamily = $profile->family_details ?? [];
        foreach (['father_name', 'mother_name', 'about_family', 'no_of_brothers', 'no_of_sisters', 'siblings'] as $k) {
            if (! isset($family[$k]) && isset($existingFamily[$k])) {
                $family[$k] = $existingFamily[$k];
            }
        }

        // Synchronize education details compatibility
        $education = $request->education_details;
        $education['college'] = $education['college_name'] ?? '';
        $education['college_name'] = $education['college_name'] ?? '';
        $existingEducation = $profile->education_details ?? [];
        if (! isset($education['passing_year']) && isset($existingEducation['passing_year'])) {
            $education['passing_year'] = $existingEducation['passing_year'];
        }

        // Synchronize professional details compatibility
        $professional = $request->professional_details;
        $professional['job_title'] = $professional['occupation'] ?? '';
        $professional['occupation'] = $professional['occupation'] ?? '';
        $professional['company'] = $professional['company_name'] ?? '';
        $professional['company_name'] = $professional['company_name'] ?? '';
        $professional['employment_type'] = $personal['employment_type'] ?? '';
        $professional['annual_income'] = $personal['annual_income'] ?? '';

        $profile->update([
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'complexion' => $request->complexion,
            'physical_status' => $request->physical_status,
            'personal_details' => $personal,
            'family_details' => $family,
            'education_details' => $education,
            'professional_details' => $professional,
            'lifestyle_details' => $request->lifestyle_details ?? $profile->lifestyle_details ?? [],
            'location_details' => $request->location_details,
            'partner_preferences' => $request->partner_preferences ?? $profile->partner_preferences ?? [],
            'privacy_settings' => $request->privacy_settings ?? $profile->privacy_settings ?? [],
            'approval_status' => $request->approval_status,
            'profile_expires_at' => $request->profile_expires_at,
        ]);

        return redirect()->route('admin.matrimony.show', $profile->id)
            ->with('success', 'Matrimony profile updated successfully.');
    }

    /**
     * View profile details
     */
    public function show($id)
    {
        $profile = MatrimonyProfile::with([
            'user',
            'sentConnectionRequests.receiver',
            'receivedConnectionRequests.sender',
            'matrimonyTransactions',
        ])->findOrFail($id);

        return view('admin.matrimony.show', compact('profile'));
    }

    /**
     * Suspend profile
     */
    public function suspend($id)
    {
        $profile = MatrimonyProfile::findOrFail($id);
        $profile->update(['status' => 'suspended']);

        return redirect()->back()->with('success', 'Profile suspended successfully!');
    }

    /**
     * Activate profile
     */
    public function activate($id)
    {
        $profile = MatrimonyProfile::findOrFail($id);
        $profile->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Profile activated successfully!');
    }

    /**
     * View connection requests
     */
    public function connections(Request $request)
    {
        $query = ConnectionRequest::with(['sender.matrimonyProfile', 'receiver.matrimonyProfile']);

        $this->applyConnectionFilters($query, $request);

        $connections = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => ConnectionRequest::count(),
            'sent' => ConnectionRequest::whereNotNull('sender_id')->count(),
            'received' => ConnectionRequest::whereNotNull('receiver_id')->count(),
            'pending' => ConnectionRequest::where('status', 'pending')->count(),
            'accepted' => ConnectionRequest::where('status', 'accepted')->count(),
            'rejected' => ConnectionRequest::where('status', 'rejected')->count(),
            'removed' => ConnectionRequest::where('status', 'removed')->count(),
        ];

        $memberIds = ConnectionRequest::query()
            ->select('sender_id as user_id')
            ->union(ConnectionRequest::query()->select('receiver_id as user_id'));
        $members = User::query()->with('matrimonyProfile')->whereIn('id', $memberIds)->orderBy('name')->get(['id', 'name', 'phone']);

        return view('admin.matrimony.connections', compact('connections', 'stats', 'members'));
    }

    public function exportConnections(Request $request)
    {
        $query = ConnectionRequest::with(['sender.matrimonyProfile', 'receiver.matrimonyProfile']);
        $this->applyConnectionFilters($query, $request);

        return response()->streamDownload(function () use ($query) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'Sender', 'Sender Mobile', 'Receiver', 'Receiver Mobile', 'Sent On', 'Status', 'Responded On']);
            foreach ($query->latest()->cursor() as $connection) {
                fputcsv($output, [
                    $connection->id,
                    $connection->sender?->matrimonyProfile?->candidate_name ?: $connection->sender?->name,
                    $connection->sender?->phone,
                    $connection->receiver?->matrimonyProfile?->candidate_name ?: $connection->receiver?->name,
                    $connection->receiver?->phone,
                    $connection->created_at?->format('Y-m-d H:i:s'),
                    $connection->status,
                    $connection->responded_at?->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($output);
        }, 'matrimony-requests-'.now()->format('Y-m-d-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    private function applyConnectionFilters($query, Request $request): void
    {
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('from_member')) {
            $query->where('sender_id', $request->integer('from_member'));
        }

        if ($request->filled('to_member')) {
            $query->where('receiver_id', $request->integer('to_member'));
        }

        foreach (['sender_gender' => 'sender', 'receiver_gender' => 'receiver'] as $field => $relation) {
            if ($request->filled($field)) {
                $gender = $request->string($field)->toString();
                $query->whereHas($relation.'.matrimonyProfile', fn ($profile) => $profile->adminGender($gender));
            }
        }

        if ($request->filled('age_range') && preg_match('/^(\d{2})-(\d{2})$/', $request->string('age_range')->toString(), $ages)) {
            $query->where(function ($builder) use ($ages) {
                $builder->whereHas('sender.matrimonyProfile', fn ($profile) => $profile->whereBetween('age', [(int) $ages[1], (int) $ages[2]]))
                    ->orWhereHas('receiver.matrimonyProfile', fn ($profile) => $profile->whereBetween('age', [(int) $ages[1], (int) $ages[2]]));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        if (! $request->filled('search')) {
            return;
        }

        $search = trim($request->string('search')->toString());
        $terms = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $phoneDigits = preg_replace('/\D+/', '', $search);

        foreach ($terms as $term) {
            $query->where(function ($builder) use ($term, $phoneDigits) {
                foreach (['sender', 'receiver'] as $relation) {
                    $method = $relation === 'sender' ? 'whereHas' : 'orWhereHas';
                    $builder->{$method}($relation, function ($user) use ($term, $phoneDigits) {
                        $user->where(function ($fields) use ($term, $phoneDigits) {
                            $fields->where('name', 'like', '%'.$term.'%')
                                ->orWhere('phone', 'like', '%'.$term.'%')
                                ->orWhere('email', 'like', '%'.$term.'%');

                            if (strlen($phoneDigits) >= 4) {
                                $fields->orWhereRaw(
                                    "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, '+', ''), ' ', ''), '-', ''), '(', ''), ')', ''), '.', '') LIKE ?",
                                    ['%'.$phoneDigits.'%']
                                );
                            }
                        })->orWhereHas('matrimonyProfile', function ($profile) use ($term) {
                            $profile->where('personal_details', 'like', '%'.$term.'%');
                        });
                    });
                }
            });
        }
    }

    /**
     * View chat conversations
     */
    public function chats(Request $request)
    {
        $query = ChatConversation::with(['user1', 'user2', 'messages' => function ($q) {
            $q->latest()->limit(1);
        }]);

        $conversations = $query->latest('last_message_at')->paginate(20);

        $stats = [
            'total_conversations' => ChatConversation::count(),
            'active_today' => ChatConversation::whereDate('last_message_at', today())->count(),
            'total_messages' => ChatMessage::count(),
            'messages_today' => ChatMessage::whereDate('created_at', today())->count(),
        ];

        return view('admin.matrimony.chats', compact('conversations', 'stats'));
    }

    /**
     * Delete profile and all associated payment and transaction data
     */
    public function destroy($id)
    {
        $profile = MatrimonyProfile::findOrFail($id);
        $userId = $profile->user_id;

        // Delete associated matrimony transactions
        $matrimonyTransactions = Transaction::where('user_id', $userId)
            ->where('purpose', 'matrimony_profile')
            ->get();

        $transactionIds = $matrimonyTransactions->pluck('id')->toArray();
        $razorpayOrderIds = $matrimonyTransactions->pluck('razorpay_order_id')->filter()->toArray();
        $razorpayPaymentIds = $matrimonyTransactions->pluck('razorpay_payment_id')->filter()->toArray();

        // Delete payment records corresponding to matrimony_subscription or matching transaction/order/payment IDs
        Payment::where('user_id', $userId)
            ->where(function ($query) use ($transactionIds, $razorpayOrderIds, $razorpayPaymentIds) {
                $query->where('payment_type', 'matrimony_subscription');
                if (! empty($transactionIds)) {
                    $query->orWhereIn('transaction_id', $transactionIds);
                }
                if (! empty($razorpayOrderIds)) {
                    $query->orWhereIn('order_id', $razorpayOrderIds);
                }
                if (! empty($razorpayPaymentIds)) {
                    $query->orWhereIn('payment_id', $razorpayPaymentIds);
                }
            })
            ->delete();

        // Delete the transactions themselves
        Transaction::where('user_id', $userId)
            ->where('purpose', 'matrimony_profile')
            ->delete();

        // Clean up profile photos from storage if available
        if ($profile->personal_details && is_array($profile->personal_details) && ! empty($profile->personal_details['photos'])) {
            foreach ($profile->personal_details['photos'] as $photo) {
                if ($photo && Storage::disk('public')->exists($photo)) {
                    Storage::disk('public')->delete($photo);
                }
            }
        }

        // Clean up connection requests involving this matrimony profile user
        ConnectionRequest::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->delete();

        // Delete profile
        $profile->delete();

        return redirect()->back()->with('success', 'Matrimony profile and all associated payment data deleted successfully!');
    }
}
