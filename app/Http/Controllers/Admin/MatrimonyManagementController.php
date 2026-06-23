<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatrimonyProfile;
use App\Models\ConnectionRequest;
use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Http\Request;

class MatrimonyManagementController extends Controller
{
    /**
     * Display all matrimony profiles
     */
    public function index(Request $request)
    {
        $query = MatrimonyProfile::with(['user']);
        
        // Filter by approval status
        if ($request->has('approval_status') && $request->approval_status !== '') {
            $query->where('approval_status', $request->approval_status);
        }
        
        // Filter by age range
        if ($request->has('age_min') && $request->age_min !== '') {
            $query->where('age', '>=', $request->age_min);
        }
        if ($request->has('age_max') && $request->age_max !== '') {
            $query->where('age', '<=', $request->age_max);
        }
        
        // Search by user name
        if ($request->has('search') && $request->search !== '') {
            $query->whereHas('user', function($userQuery) use ($request) {
                $userQuery->where('name', 'like', '%' . $request->search . '%')
                         ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $profiles = $query->latest()->paginate(20);
        
        $stats = [
            'total' => MatrimonyProfile::count(),
            'approved' => MatrimonyProfile::where('approval_status', 'approved')->count(),
            'pending' => MatrimonyProfile::where('approval_status', 'pending')->count(),
            'rejected' => MatrimonyProfile::where('approval_status', 'rejected')->count(),
            'active_connections' => ConnectionRequest::where('status', 'accepted')->count(),
            'total_connections' => ConnectionRequest::count()
        ];
        
        return view('admin.matrimony.index', compact('profiles', 'stats'));
    }
    
    /**
     * Display pending profile moderations
     */
    public function moderation(Request $request)
    {
        $query = MatrimonyProfile::with(['user'])
            ->where('approval_status', 'pending');
            
        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $query->whereHas('user', function($userQuery) use ($request) {
                $userQuery->where('name', 'like', '%' . $request->search . '%')
                         ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $pendingProfiles = $query->latest()->paginate(15);
        
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
                'approved_by' => auth()->id()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile approved successfully!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Profile approved successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve profile: ' . $e->getMessage()
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
                'rejection_reason' => 'required|string|max:500'
            ]);
            
            $profile = MatrimonyProfile::findOrFail($profileId);
            
            $profile->update([
                'approval_status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile rejected successfully!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Profile rejected successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject profile: ' . $e->getMessage()
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
        $castes = \App\Models\Cast::where('is_active', true)->orderBy('name')->get();
        $subcastes = \App\Models\SubCast::where('is_active', true)->orderBy('name')->get();

        return view('admin.matrimony.create', compact('users', 'castes', 'subcastes'));
    }

    /**
     * Store new matrimony profile
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id|unique:matrimony_profiles,user_id',
            'age' => 'required|integer|min:18|max:100',
            'gender' => 'required|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'time_of_birth' => 'nullable|string',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'complexion' => 'nullable|string|max:50',
            'physical_status' => 'nullable|string|max:50',
            'approval_status' => 'required|in:pending,approved,rejected',
            
            'personal_details' => 'required|array',
            'family_details' => 'required|array',
            'education_details' => 'required|array',
            'professional_details' => 'required|array',
            'lifestyle_details' => 'nullable|array',
            'location_details' => 'required|array',
            'partner_preferences' => 'required|array',
            'privacy_settings' => 'nullable|array',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
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

        $profile = MatrimonyProfile::create([
            'user_id' => $request->user_id,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'time_of_birth' => $request->time_of_birth,
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'complexion' => $request->complexion,
            'physical_status' => $request->physical_status,
            'personal_details' => $personal,
            'family_details' => $request->family_details,
            'education_details' => $request->education_details,
            'professional_details' => $request->professional_details,
            'lifestyle_details' => $request->lifestyle_details ?? [],
            'location_details' => $request->location_details,
            'partner_preferences' => $request->partner_preferences,
            'privacy_settings' => $request->privacy_settings ?? [],
            'approval_status' => $request->approval_status,
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
        $castes = \App\Models\Cast::where('is_active', true)->orderBy('name')->get();
        $subcastes = \App\Models\SubCast::where('is_active', true)->orderBy('name')->get();

        return view('admin.matrimony.edit', compact('profile', 'castes', 'subcastes'));
    }

    /**
     * Update matrimony profile
     */
    public function update(Request $request, $id)
    {
        $profile = MatrimonyProfile::findOrFail($id);

        $request->validate([
            'age' => 'required|integer|min:18|max:100',
            'gender' => 'required|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'time_of_birth' => 'nullable|string',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'complexion' => 'nullable|string|max:50',
            'physical_status' => 'nullable|string|max:50',
            'approval_status' => 'required|in:pending,approved,rejected',
            
            'personal_details' => 'required|array',
            'family_details' => 'required|array',
            'education_details' => 'required|array',
            'professional_details' => 'required|array',
            'lifestyle_details' => 'nullable|array',
            'location_details' => 'required|array',
            'partner_preferences' => 'required|array',
            'privacy_settings' => 'nullable|array',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
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

        $personal = $request->personal_details;
        $personal['gender'] = $request->gender;
        $personal['photos'] = $photoPaths;

        $profile->update([
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'time_of_birth' => $request->time_of_birth,
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'complexion' => $request->complexion,
            'physical_status' => $request->physical_status,
            'personal_details' => $personal,
            'family_details' => $request->family_details,
            'education_details' => $request->education_details,
            'professional_details' => $request->professional_details,
            'lifestyle_details' => $request->lifestyle_details ?? [],
            'location_details' => $request->location_details,
            'partner_preferences' => $request->partner_preferences,
            'privacy_settings' => $request->privacy_settings ?? [],
            'approval_status' => $request->approval_status,
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
            'receivedConnectionRequests.sender'
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
        $query = ConnectionRequest::with(['sender.user', 'receiver.user']);
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        $connections = $query->latest()->paginate(20);
        
        $stats = [
            'total' => ConnectionRequest::count(),
            'pending' => ConnectionRequest::where('status', 'pending')->count(),
            'accepted' => ConnectionRequest::where('status', 'accepted')->count(),
            'rejected' => ConnectionRequest::where('status', 'rejected')->count(),
        ];
        
        return view('admin.matrimony.connections', compact('connections', 'stats'));
    }
    
    /**
     * View chat conversations
     */
    public function chats(Request $request)
    {
        $query = ChatConversation::with(['user1', 'user2', 'messages' => function($q) {
            $q->latest()->limit(1);
        }]);
        
        $conversations = $query->latest('last_message_at')->paginate(20);
        
        $stats = [
            'total_conversations' => ChatConversation::count(),
            'active_today' => ChatConversation::whereDate('last_message_at', today())->count(),
            'total_messages' => \App\Models\ChatMessage::count(),
            'messages_today' => \App\Models\ChatMessage::whereDate('created_at', today())->count(),
        ];
        
        return view('admin.matrimony.chats', compact('conversations', 'stats'));
    }
    
    /**
     * Delete profile
     */
    public function destroy($id)
    {
        $profile = MatrimonyProfile::findOrFail($id);
        $profile->delete();
        
        return redirect()->back()->with('success', 'Profile deleted successfully!');
    }
}