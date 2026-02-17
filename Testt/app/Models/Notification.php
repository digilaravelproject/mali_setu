<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'priority',
        'channel',
        'is_read',
        'read_at',
        'email_sent',
        'email_sent_at',
        'push_sent',
        'push_sent_at',
        'related_type',
        'related_id'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'email_sent' => 'boolean',
        'push_sent' => 'boolean',
        'read_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'push_sent_at' => 'datetime'
    ];

    protected $dates = [
        'read_at',
        'email_sent_at',
        'push_sent_at'
    ];

    // Notification types constants
    const TYPE_REGISTRATION_APPROVED = 'registration_approved';
    const TYPE_REGISTRATION_REJECTED = 'registration_rejected';
    const TYPE_BUSINESS_VERIFIED = 'business_verified';
    const TYPE_BUSINESS_REJECTED = 'business_rejected';
    const TYPE_MATRIMONY_APPROVED = 'matrimony_approved';
    const TYPE_MATRIMONY_REJECTED = 'matrimony_rejected';
    const TYPE_CONNECTION_REQUEST = 'connection_request';
    const TYPE_CONNECTION_ACCEPTED = 'connection_accepted';
    const TYPE_CONNECTION_REJECTED = 'connection_rejected';
    const TYPE_JOB_APPLICATION = 'job_application';
    const TYPE_JOB_APPLICATION_STATUS = 'job_application_status';
    const TYPE_VOLUNTEER_APPLICATION = 'volunteer_application';
    const TYPE_VOLUNTEER_APPROVED = 'volunteer_approved';
    const TYPE_DONATION_RECEIVED = 'donation_received';
    const TYPE_PAYMENT_SUCCESS = 'payment_success';
    const TYPE_PAYMENT_FAILED = 'payment_failed';
    const TYPE_ADMIN_ANNOUNCEMENT = 'admin_announcement';
    const TYPE_PROFILE_UPDATE = 'profile_update';
    const TYPE_NEW_MESSAGE = 'new_message';

    // Priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Channels
    const CHANNEL_IN_APP = 'in_app';
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_PUSH = 'push';
    const CHANNEL_SMS = 'sms';

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model (polymorphic relationship)
     */
    public function related(): MorphTo
    {
        return $this->morphTo('related');
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for specific notification type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific priority
     */
    public function scopeOfPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for recent notifications
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => Carbon::now()
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Mark email as sent
     */
    public function markEmailSent()
    {
        $this->update([
            'email_sent' => true,
            'email_sent_at' => Carbon::now()
        ]);
    }

    /**
     * Mark push notification as sent
     */
    public function markPushSent()
    {
        $this->update([
            'push_sent' => true,
            'push_sent_at' => Carbon::now()
        ]);
    }

    /**
     * Get formatted time ago
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Check if notification is urgent
     */
    public function isUrgent()
    {
        return $this->priority === self::PRIORITY_URGENT;
    }

    /**
     * Check if notification is high priority
     */
    public function isHighPriority()
    {
        return in_array($this->priority, [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }

    /**
     * Get notification icon based on type
     */
    public function getIconAttribute()
    {
        $icons = [
            self::TYPE_REGISTRATION_APPROVED => 'check-circle',
            self::TYPE_REGISTRATION_REJECTED => 'x-circle',
            self::TYPE_BUSINESS_VERIFIED => 'briefcase',
            self::TYPE_BUSINESS_REJECTED => 'briefcase',
            self::TYPE_MATRIMONY_APPROVED => 'heart',
            self::TYPE_MATRIMONY_REJECTED => 'heart',
            self::TYPE_CONNECTION_REQUEST => 'user-plus',
            self::TYPE_CONNECTION_ACCEPTED => 'user-check',
            self::TYPE_CONNECTION_REJECTED => 'user-x',
            self::TYPE_JOB_APPLICATION => 'file-text',
            self::TYPE_JOB_APPLICATION_STATUS => 'file-text',
            self::TYPE_VOLUNTEER_APPLICATION => 'hand',
            self::TYPE_VOLUNTEER_APPROVED => 'hand',
            self::TYPE_DONATION_RECEIVED => 'gift',
            self::TYPE_PAYMENT_SUCCESS => 'credit-card',
            self::TYPE_PAYMENT_FAILED => 'credit-card',
            self::TYPE_ADMIN_ANNOUNCEMENT => 'megaphone',
            self::TYPE_PROFILE_UPDATE => 'user',
            self::TYPE_NEW_MESSAGE => 'message-circle'
        ];

        return $icons[$this->type] ?? 'bell';
    }

    /**
     * Get notification color based on type and priority
     */
    public function getColorAttribute()
    {
        if ($this->isUrgent()) {
            return 'red';
        }

        if ($this->isHighPriority()) {
            return 'orange';
        }

        $colors = [
            self::TYPE_REGISTRATION_APPROVED => 'green',
            self::TYPE_REGISTRATION_REJECTED => 'red',
            self::TYPE_BUSINESS_VERIFIED => 'green',
            self::TYPE_BUSINESS_REJECTED => 'red',
            self::TYPE_MATRIMONY_APPROVED => 'green',
            self::TYPE_MATRIMONY_REJECTED => 'red',
            self::TYPE_CONNECTION_REQUEST => 'blue',
            self::TYPE_CONNECTION_ACCEPTED => 'green',
            self::TYPE_CONNECTION_REJECTED => 'red',
            self::TYPE_DONATION_RECEIVED => 'purple',
            self::TYPE_PAYMENT_SUCCESS => 'green',
            self::TYPE_PAYMENT_FAILED => 'red'
        ];

        return $colors[$this->type] ?? 'gray';
    }

    /**
     * Create a new notification
     */
    public static function createNotification($userId, $type, $title, $message, $data = [], $actionUrl = null, $priority = self::PRIORITY_MEDIUM, $relatedModel = null)
    {
        $notification = self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
            'priority' => $priority,
            'related_type' => $relatedModel ? get_class($relatedModel) : null,
            'related_id' => $relatedModel ? $relatedModel->id : null
        ]);

        return $notification;
    }

    /**
     * Bulk mark notifications as read
     */
    public static function markMultipleAsRead($notificationIds, $userId)
    {
        return self::where('user_id', $userId)
            ->whereIn('id', $notificationIds)
            ->update([
                'is_read' => true,
                'read_at' => Carbon::now()
            ]);
    }

    /**
     * Get unread count for user
     */
    public static function getUnreadCount($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Delete old notifications
     */
    public static function deleteOldNotifications($days = 30)
    {
        return self::where('created_at', '<', Carbon::now()->subDays($days))
            ->where('is_read', true)
            ->delete();
    }
}
