<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatrimonyProfile;
use App\Models\ConnectionRequest;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\Cast;
use App\Models\SubCast;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;

class MatrimonyController extends Controller
{
    /**
     * @var \App\Services\NotificationService
     */
    protected $notifications;

    public function __construct(NotificationService $notifications)
    {
        $this->notifications = $notifications;
    }
    /**
     * Create matrimony profile
     */
    public function createProfile(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'age' => 'required|integer|min:18|max:100',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'complexion' => 'nullable|string|max:50',
            'physical_status' => 'nullable|string|max:50',
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user already has a matrimony profile
        $existingProfile = MatrimonyProfile::where('user_id', $request->user()->id)->first();
        if ($existingProfile) {
            return response()->json([
                'success' => false,
                'message' => 'User already has a matrimony profile'
            ], 400);
        }
    
        // Handle photo uploads
        $photoPaths = [];
        if (!empty($request->personal_details['photos'])) {
            foreach ($request->personal_details['photos'] as $photo) {
        
                // Check if base64 image
                if (preg_match('/^data:image\/(\w+);base64,/', $photo, $type)) {
        
                    $imageType = $type[1]; // jpg, png, jpeg, webp, etc
                    $photo = substr($photo, strpos($photo, ',') + 1);
                    $photo = base64_decode($photo);
        
                    if ($photo === false) {
                        continue;
                    }
        
                    $fileName = 'matrimony/photos/' . Str::uuid() . '.' . $imageType;
        
                    Storage::disk('public')->put($fileName, $photo);
        
                    $photoPaths[] = $fileName;
                }
            }
        }

        $profile = MatrimonyProfile::create([
            'user_id' => $request->user()->id,
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'complexion' => $request->complexion,
            'physical_status' => $request->physical_status,
            'personal_details' => $request->personal_details,
            'family_details' => $request->family_details,
            'education_details' => $request->education_details,
            'professional_details' => $request->professional_details,
            'lifestyle_details' => $request->lifestyle_details ?? [],
            'location_details' => $request->location_details,
            'partner_preferences' => $request->partner_preferences,
            'privacy_settings' => $request->privacy_settings ?? [],
            'approval_status' => 'pending',
        ]);

        // Store photo paths if any
        if (!empty($photoPaths)) {
            $profile->update([
                'personal_details' => array_merge($profile->personal_details, ['photos' => $photoPaths])
            ]);
        }
        
        if (empty($user->user_type) || $user->user_type != 'matrimony') {
            $user->update([
                'user_type' => 'matrimony'
            ]);
        }

        // Email: matrimony profile created
        $this->notifications->createNotification(
            $user->id,
            Notification::TYPE_MATRIMONY_APPROVED,
            'Matrimony profile created',
            'Your matrimony profile has been created and is pending admin approval.',
            ['profile_id' => $profile->id],
            '/matrimony/profile',
            Notification::PRIORITY_MEDIUM,
            $profile,
            ['in_app', 'email']
        );

        return response()->json([
            'success' => true,
            'message' => 'Matrimony profile created successfully',
            'data' => ['profile' => $profile]
        ], 201);
    }

    /**
     * Get all casts
     */
    public function getCasts(Request $request)
    {
        try {
            $casts = Cast::where('is_active', true)
                // ->with('subCasts')
                ->latest()
                ->get();

            if ($casts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No casts found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Casts fetched successfully',
                'data' => [
                    'casts' => $casts,
                    'count' => $casts->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Get casts API error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /**
     * Get sub-casts by cast ID
     */
    public function getSubCasts(Request $request, $castId)
    {
        try {
            // Validate cast ID
            if (!is_numeric($castId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid cast ID',
                    'errors' => ['cast_id' => ['Cast ID must be numeric']]
                ], 422);
            }

            // Check if cast exists and is active
            $cast = Cast::find($castId);
            if (!$cast || !$cast->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cast not found or inactive'
                ], 404);
            }

            // Fetch sub-casts for this cast
            $subCasts = SubCast::where('cast_id', $castId)
                ->where('is_active', true)
                ->latest()
                ->get();

            if ($subCasts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No sub-casts found for this cast'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sub-casts fetched successfully',
                'data' => [
                    'cast' => $cast,
                    'sub_casts' => $subCasts,
                    'count' => $subCasts->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Get sub-casts API error', [
                'cast_id' => $castId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /**
     * Get user's matrimony profile
     */
    public function getProfile(Request $request)
    {
        $profile = MatrimonyProfile::where('user_id', $request->user()->id)
            ->with('user')
            ->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'No matrimony profile found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['profile' => $profile]
        ]);
    }

    /**
     * Update matrimony profile
     */
    public function updateProfile(Request $request)
    {
        $profile = MatrimonyProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'No matrimony profile found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'age' => 'sometimes|integer|min:18|max:100',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'complexion' => 'nullable|string|max:50',
            'physical_status' => 'nullable|string|max:50',
            'personal_details' => 'sometimes|array',
            'family_details' => 'sometimes|array',
            'education_details' => 'sometimes|array',
            'professional_details' => 'sometimes|array',
            'lifestyle_details' => 'nullable|array',
            'location_details' => 'sometimes|array',
            'partner_preferences' => 'sometimes|array',
            'privacy_settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $profile->update($request->only([
            'age', 'height', 'weight', 'complexion', 'physical_status',
            'personal_details', 'family_details', 'education_details',
            'professional_details', 'lifestyle_details', 'location_details',
            'partner_preferences', 'privacy_settings'
        ]));

        // Email: matrimony profile updated
        $this->notifications->createNotification(
            $request->user()->id,
            Notification::TYPE_PROFILE_UPDATE,
            'Matrimony profile updated',
            'Your matrimony profile details have been updated.',
            ['profile_id' => $profile->id],
            '/matrimony/profile',
            Notification::PRIORITY_LOW,
            $profile,
            ['in_app', 'email']
        );

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => ['profile' => $profile]
        ]);
    }

    /**
     * Search matrimony profiles
     */
    public function searchProfiles_old(Request $request)
    {
        $query = MatrimonyProfile::with('user')
            ->where('approval_status', 'approved');

        // Exclude current user's profile if authenticated
        if ($request->user()) {
            $query->where('user_id', '!=', $request->user()->id);
        }

        // Apply filters
        if ($request->has('age_min')) {
            $query->where('age', '>=', $request->age_min);
        }

        if ($request->has('age_max')) {
            $query->where('age', '<=', $request->age_max);
        }

        if ($request->has('education')) {
            $query->whereJsonContains('education_details->qualification', $request->education);
        }

        if ($request->has('occupation')) {
            $query->whereJsonContains('professional_details->occupation', $request->occupation);
        }

        if ($request->has('location')) {
            $query->whereJsonContains('location_details->current_city', $request->location);
        }

        $profiles = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $profiles
        ]);
    }

    public function searchProfiles(Request $request)
    {
        $query = MatrimonyProfile::query();

        //Basic Details
        if ($request->filled('age_min') || $request->filled('age_max')) {
            $query->whereBetween('age', [$request->age_min ?? 18, $request->age_max ?? 100]);
        }

        if ($request->filled('marital_status')) {
            $query->whereJsonContains('personal_details->marital_status', $request->marital_status);
        }

        if ($request->filled('profile_created_by')) {
            $query->whereJsonContains('personal_details->profile_created_by', $request->profile_created_by);
        }

        if ($request->filled('language')) {
            $query->whereJsonContains('personal_details->language', $request->language);
        }

        if ($request->filled('height')) {
            $query->where('height', $request->height);
        }

        if ($request->filled('physical_status')) {
            $query->where('physical_status', $request->physical_status);
        }

        //Profesional details
        if ($request->filled('annual_income')) {
            $query->whereBetween('personal_details->annual_income', [0, $request->annual_income]);
        }

        if ($request->filled('education')) {
            $query->whereJsonContains('education_details->highest_qualification', $request->education);
        }

        if ($request->filled('employment_type')) {
            $query->whereJsonContains('personal_details->employment_type', $request->employment_type);
        }

        //Family Details
        if ($request->filled('family_status')) {
            $query->whereJsonContains('family_details->family_class', $request->family_status);
        }

        if ($request->filled('family_type')) {
            $query->whereJsonContains('personal_details->family_type', $request->family_type);
        }

        if ($request->filled('family_value')) {
            $query->whereJsonContains('family_details->family_value', $request->family_value);
        }

        //Location Details
        if ($request->filled('country')) {
            $query->whereJsonContains('location_details->country', $request->country);
        }

        if ($request->filled('citizenship')) {
            $query->whereJsonContains('personal_details->citizenship', $request->citizenship);
        }

        //Lifestyle Details
        if ($request->filled('diet')) {
            $query->whereJsonContains('lifestyle_details->diet', $request->diet);
        }

        if ($request->filled('smoking')) {
            $query->whereJsonContains('lifestyle_details->smoking', $request->smoking);
        }

        if ($request->filled('drinking')) {
            $query->whereJsonContains('lifestyle_details->drinking', $request->drinking);
        }

        //Profile type
        if ($request->filled('photo')) {
             $query->where('photo', '!=', '');
        }

        //Recently created profile
        if ($request->filled('created_at')) {
            switch ($request->created_at) {
                case 'all':
                    // no filter applied
                    break;

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

        foreach (['location', 'caste', 'education', 'occupation', 'gender'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->$field);
            }
        }

        $query->where('approval_status', 'approved');

        $results = $query->latest()->paginate($request->size ?? 20);

        return response()->json(['success' => true, 'data' => $results]);
    }

    /**
     * Get single profile details
     */
    public function showProfile(Request $request, $id)
    {
        $profile = MatrimonyProfile::with('user')
            // ->where('approval_status', 'approved')
            ->findOrFail($id);
            
        $authUserId = $request->user()->id;
        
        if(!empty($profile)){
            
                $usr_id = $profile->id; // paginator items are objects, not arrays
            
                $connection = DB::table('connection_requests')
                    ->where(function ($q) use ($authUserId, $usr_id) {
                        $q->where('sender_id', $authUserId)
                          ->where('receiver_id', $usr_id);
                    })
                    ->orWhere(function ($q) use ($authUserId, $usr_id) {
                        $q->where('sender_id', $usr_id)
                          ->where('receiver_id', $authUserId);
                    })
                    ->first();
                    
                if ($connection) {
                    $profile->connection_status = $connection->status;
                } else {
                    $profile->connection_status = 'not_connected';
                }
        }

        return response()->json([
            'success' => true,
            'data' => ['profile' => $profile]
        ]);
    }

    /**
     * Send connection request
     */
    public function sendConnectionRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if request already exists
        $existingRequest = ConnectionRequest::where('sender_id', $request->user()->id)
            ->where('receiver_id', $request->receiver_id)
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Connection request already sent'
            ], 400);
        }

        $connectionRequest = ConnectionRequest::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Email: new connection request (receiver)
        $receiverProfile = MatrimonyProfile::where('user_id', $request->receiver_id)->first();
        $senderProfile = MatrimonyProfile::where('user_id', $request->user()->id)->first();

        if ($receiverProfile && $senderProfile) {
            $this->notifications->notifyConnectionRequest($receiverProfile, $senderProfile);
        }

        return response()->json([
            'success' => true,
            'message' => 'Connection request sent successfully',
            'data' => ['request' => $connectionRequest]
        ], 201);
    }
    
    /**
     * Send connection request
     */
    public function sendRemoveUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if request already exists
        $existingRequest = ConnectionRequest::where('sender_id', $request->user()->id)
            ->where('receiver_id', $request->receiver_id)
            ->where('status', 'removed')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'This user already removed from your end'
            ], 400);
        }

        $connectionRequest = ConnectionRequest::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'status' => 'removed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User removed successfully',
            'data' => ['request' => $connectionRequest]
        ], 201);
    }

    /**
     * Respond to connection request
     */
    public function respondToConnectionRequest(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,rejected',
            'response_message' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $connectionRequest = ConnectionRequest::where('id', $id)
            ->where('receiver_id', $request->user()->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $connectionRequest->update([
            'status' => $request->status,
            'response_message' => $request->response_message,
            'responded_at' => now(),
        ]);

        // If accepted, create chat conversation
        if ($request->status === 'accepted') {
            ChatConversation::firstOrCreate([
                'user1_id' => min($connectionRequest->sender_id, $connectionRequest->receiver_id),
                'user2_id' => max($connectionRequest->sender_id, $connectionRequest->receiver_id),
            ]);

            // Email: connection request accepted (sender)
            $senderProfile = MatrimonyProfile::where('user_id', $connectionRequest->sender_id)->first();
            $receiverProfile = MatrimonyProfile::where('user_id', $connectionRequest->receiver_id)->first();

            if ($senderProfile && $receiverProfile) {
                $this->notifications->createNotification(
                    $connectionRequest->sender_id,
                    Notification::TYPE_CONNECTION_ACCEPTED,
                    'Connection request accepted',
                    optional($receiverProfile->user)->name . ' has accepted your connection request.',
                    [
                        'receiver_id' => $connectionRequest->receiver_id,
                        'request_id' => $connectionRequest->id,
                    ],
                    '/matrimony/requests',
                    Notification::PRIORITY_MEDIUM,
                    $connectionRequest,
                    ['in_app', 'email']
                );
            }
        } else {
            // Email: connection request rejected (sender)
            $receiverProfile = MatrimonyProfile::where('user_id', $connectionRequest->receiver_id)->first();

            $this->notifications->createNotification(
                $connectionRequest->sender_id,
                Notification::TYPE_CONNECTION_REJECTED,
                'Connection request declined',
                optional($receiverProfile->user)->name . ' has declined your connection request.',
                [
                    'receiver_id' => $connectionRequest->receiver_id,
                    'request_id' => $connectionRequest->id,
                ],
                '/matrimony/requests',
                Notification::PRIORITY_LOW,
                $connectionRequest,
                ['in_app', 'email']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Connection request ' . $request->status . ' successfully',
            'data' => ['request' => $connectionRequest]
        ]);
    }

    /**
     * Get user's connection requests
     */
    public function getConnectionRequests(Request $request)
    {
        $sentRequests = ConnectionRequest::where('sender_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $receivedRequests = ConnectionRequest::where('receiver_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        if(!empty($sentRequests)){
            foreach ($sentRequests as $sentRequest) {
                $sentRequest->receiver_profile = MatrimonyProfile::where(
                    'user_id',
                    $sentRequest->receiver_id
                )->first();
            }
        }
        
        if(!empty($receivedRequests)){
            foreach ($receivedRequests as $receivedRequest) {
                $receivedRequest->sender_profile = MatrimonyProfile::where(
                    'user_id',
                    $receivedRequest->sender_id
                )->first();
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'sent_requests' => $sentRequests,
                'received_requests' => $receivedRequests
            ]
        ]);
    }
    
    /**
     * Get user's connection requests
     */
    public function getConnectedUsers(Request $request)
    {
        $userId = $request->user()->id;

        /**
         * Get accepted connections where user is sender OR receiver
         */
        $connections = ConnectionRequest::where('status', 'accepted')
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        if(count($connections) == 0){
            return response()->json([
                'success' => false,
                'message' => 'Connected users not found'
            ], 422);
        }
        
        /**
         * Attach connected user's matrimony profile
         */
        if(count($connections) > 0){
            foreach ($connections as $connection) {
                
                
            
                // If logged-in user is sender → get receiver profile
                if ($connection->sender_id == $userId) {
                    $connection->connected_profile = MatrimonyProfile::where(
                        'user_id',
                        $connection->receiver_id
                    )->first();
                } else {
                    $connection->connected_profile = MatrimonyProfile::where(
                        'user_id',
                        $connection->sender_id
                    )->first();
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'connected_users' => $connections
            ]
        ]);
    }

    /**
     * Get user's chat conversations
     */
    public function getConversations(Request $request)
    {
        $userId = $request->user()->id;
        
        $conversations = ChatConversation::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();
            
        /**
         * Attach connected user's matrimony profile
         */
        if(count($conversations) > 0){
            foreach ($conversations as $conversation) {
            
                // If logged-in user is sender → get receiver profile
                if ($conversation->user1_id == $userId) {
                    $u_id = $conversation->user2_id;
                } else {
                    $u_id = $conversation->user1_id;
                }
                
                $conversation->user1 = MatrimonyProfile::where(
                    'user_id',
                    $u_id
                )->first();
            }
        }

        return response()->json([
            'success' => true,
            'data' => ['conversations' => $conversations]
        ]);
    }

    /**
     * Get messages for a conversation
     */
    public function getMessages(Request $request, $conversationId)
    {
        $userId = $request->user()->id;
        
        $conversation = ChatConversation::where('id', $conversationId)
            ->where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->firstOrFail();

        $messages = ChatMessage::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('id', 'desc')
            ->paginate(50);

        // Mark messages as read
        ChatMessage::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'data' => [
                'conversation' => $conversation,
                'messages' => $messages
            ]
        ]);
    }

    /**
     * Send message
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|integer|exists:chat_conversations,id',
            'message_text' => 'required|string|max:1000',
            'message_type' => 'nullable|in:text,image,file',
            'attachment' => 'nullable|file|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->id;
        
        // Verify user is part of conversation
        $conversation = ChatConversation::where('id', $request->conversation_id)
            ->where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->firstOrFail();

        // Handle attachment upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat/attachments', 'public');
        }

        $message = ChatMessage::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => $userId,
            'message_text' => $request->message_text,
            'message_type' => $request->message_type ?? 'text',
            'attachment_path' => $attachmentPath,
        ]);

        // Update conversation last message time
        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => ['message' => $message->load('sender')]
        ], 201);
    }
}