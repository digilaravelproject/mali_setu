<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatrimonyProfile;
use App\Models\ConnectionRequest;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MatrimonyController extends Controller
{
    /**
     * Create matrimony profile
     */
    public function createProfile(Request $request)
    {
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
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('matrimony/photos', 'public');
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

        return response()->json([
            'success' => true,
            'message' => 'Matrimony profile created successfully',
            'data' => ['profile' => $profile]
        ], 201);
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

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => ['profile' => $profile]
        ]);
    }

    /**
     * Search matrimony profiles
     */
    public function searchProfiles(Request $request)
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

    /**
     * Get single profile details
     */
    public function showProfile($id)
    {
        $profile = MatrimonyProfile::with('user')
            ->where('approval_status', 'approved')
            ->findOrFail($id);

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

        return response()->json([
            'success' => true,
            'message' => 'Connection request sent successfully',
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
            ->with('receiver')
            ->orderBy('created_at', 'desc')
            ->get();

        $receivedRequests = ConnectionRequest::where('receiver_id', $request->user()->id)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'sent_requests' => $sentRequests,
                'received_requests' => $receivedRequests
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
            ->with(['user1', 'user2', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

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
            ->orderBy('created_at', 'asc')
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