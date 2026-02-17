<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Get user notifications with pagination
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:50',
            'type' => 'sometimes|string',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'is_read' => 'sometimes|boolean',
            'recent_days' => 'sometimes|integer|min:1|max:365'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $perPage = $request->input('per_page', 20);
            
            $query = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->has('type')) {
                $query->ofType($request->input('type'));
            }

            if ($request->has('priority')) {
                $query->ofPriority($request->input('priority'));
            }

            if ($request->has('is_read')) {
                if ($request->input('is_read')) {
                    $query->read();
                } else {
                    $query->unread();
                }
            }

            if ($request->has('recent_days')) {
                $query->recent($request->input('recent_days'));
            }

            $notifications = $query->paginate($perPage);

            // Get unread count
            $unreadCount = Notification::getUnreadCount($user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $notifications->items(),
                    'pagination' => [
                        'current_page' => $notifications->currentPage(),
                        'last_page' => $notifications->lastPage(),
                        'per_page' => $notifications->perPage(),
                        'total' => $notifications->total(),
                        'from' => $notifications->firstItem(),
                        'to' => $notifications->lastItem()
                    ],
                    'unread_count' => $unreadCount
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch notifications', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications'
            ], 500);
        }
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        try {
            $user = Auth::user();
            $unreadCount = Notification::getUnreadCount($user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $unreadCount
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get unread count', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $notification = Notification::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'data' => [
                    'notification' => $notification,
                    'unread_count' => Notification::getUnreadCount($user->id)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read', ['error' => $e->getMessage(), 'notification_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $notification = Notification::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->markAsUnread();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as unread',
                'data' => [
                    'notification' => $notification,
                    'unread_count' => Notification::getUnreadCount($user->id)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as unread', ['error' => $e->getMessage(), 'notification_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as unread'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            
            $updated = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => "Marked {$updated} notifications as read",
                'data' => [
                    'updated_count' => $updated,
                    'unread_count' => 0
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read'
            ], 500);
        }
    }

    /**
     * Mark multiple notifications as read
     */
    public function markMultipleAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_ids' => 'required|array|min:1',
            'notification_ids.*' => 'integer|exists:notifications,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $notificationIds = $request->input('notification_ids');
            
            $updated = Notification::markMultipleAsRead($notificationIds, $user->id);

            return response()->json([
                'success' => true,
                'message' => "Marked {$updated} notifications as read",
                'data' => [
                    'updated_count' => $updated,
                    'unread_count' => Notification::getUnreadCount($user->id)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark multiple notifications as read', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read'
            ], 500);
        }
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $notification = Notification::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
                'data' => [
                    'unread_count' => Notification::getUnreadCount($user->id)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete notification', ['error' => $e->getMessage(), 'notification_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification'
            ], 500);
        }
    }

    /**
     * Delete multiple notifications
     */
    public function destroyMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_ids' => 'required|array|min:1',
            'notification_ids.*' => 'integer|exists:notifications,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $notificationIds = $request->input('notification_ids');
            
            $deleted = Notification::where('user_id', $user->id)
                ->whereIn('id', $notificationIds)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Deleted {$deleted} notifications",
                'data' => [
                    'deleted_count' => $deleted,
                    'unread_count' => Notification::getUnreadCount($user->id)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete multiple notifications', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notifications'
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStatistics()
    {
        try {
            $user = Auth::user();
            
            $stats = [
                'total' => Notification::where('user_id', $user->id)->count(),
                'unread' => Notification::where('user_id', $user->id)->unread()->count(),
                'read' => Notification::where('user_id', $user->id)->read()->count(),
                'recent_7_days' => Notification::where('user_id', $user->id)->recent(7)->count(),
                'recent_30_days' => Notification::where('user_id', $user->id)->recent(30)->count(),
                'by_priority' => [
                    'urgent' => Notification::where('user_id', $user->id)->ofPriority('urgent')->count(),
                    'high' => Notification::where('user_id', $user->id)->ofPriority('high')->count(),
                    'medium' => Notification::where('user_id', $user->id)->ofPriority('medium')->count(),
                    'low' => Notification::where('user_id', $user->id)->ofPriority('low')->count()
                ],
                'by_type' => Notification::where('user_id', $user->id)
                    ->selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type')
                    ->toArray()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get notification statistics', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get notification statistics'
            ], 500);
        }
    }

    /**
     * Get notification preferences (placeholder for future implementation)
     */
    public function getPreferences()
    {
        try {
            $user = Auth::user();
            
            // This would typically come from a user_notification_preferences table
            $preferences = [
                'email_notifications' => true,
                'push_notifications' => true,
                'sms_notifications' => false,
                'notification_types' => [
                    'registration_updates' => true,
                    'business_updates' => true,
                    'matrimony_updates' => true,
                    'job_updates' => true,
                    'volunteer_updates' => true,
                    'donation_updates' => true,
                    'payment_updates' => true,
                    'admin_announcements' => true
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $preferences
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get notification preferences', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get notification preferences'
            ], 500);
        }
    }

    /**
     * Update notification preferences (placeholder for future implementation)
     */
    public function updatePreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
            'notification_types' => 'sometimes|array',
            'notification_types.*' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // This would typically update a user_notification_preferences table
            // For now, we'll just return success
            
            return response()->json([
                'success' => true,
                'message' => 'Notification preferences updated successfully',
                'data' => $request->all()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update notification preferences', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification preferences'
            ], 500);
        }
    }
}
