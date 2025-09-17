<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class NotificationService
{
    /**
     * Create and send a notification
     */
    public function createNotification(
        $userId,
        string $type,
        string $title,
        string $message,
        array $data = [],
        string $actionUrl = null,
        string $priority = Notification::PRIORITY_MEDIUM,
        $relatedModel = null,
        array $channels = ['in_app']
    ) {
        try {
            // Create the notification
            $notification = Notification::createNotification(
                $userId,
                $type,
                $title,
                $message,
                $data,
                $actionUrl,
                $priority,
                $relatedModel
            );

            // Send through requested channels
            foreach ($channels as $channel) {
                $this->sendNotification($notification, $channel);
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to create notification', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'type' => $type
            ]);
            throw $e;
        }
    }

    /**
     * Send notification through specific channel
     */
    public function sendNotification(Notification $notification, string $channel)
    {
        try {
            switch ($channel) {
                case Notification::CHANNEL_EMAIL:
                    $this->sendEmailNotification($notification);
                    break;
                case Notification::CHANNEL_PUSH:
                    $this->sendPushNotification($notification);
                    break;
                case Notification::CHANNEL_SMS:
                    $this->sendSmsNotification($notification);
                    break;
                case Notification::CHANNEL_IN_APP:
                default:
                    // In-app notifications are already created in database
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id,
                'channel' => $channel
            ]);
        }
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(Notification $notification)
    {
        try {
            $user = $notification->user;
            
            if (!$user || !$user->email) {
                Log::warning('Cannot send email notification - user or email not found', [
                    'notification_id' => $notification->id
                ]);
                return;
            }

            // For now, we'll just log the email sending
            // In a real implementation, you would use Laravel's Mail facade
            Log::info('Email notification sent', [
                'notification_id' => $notification->id,
                'user_email' => $user->email,
                'subject' => $notification->title
            ]);

            $notification->markEmailSent();
        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id
            ]);
        }
    }

    /**
     * Send push notification
     */
    private function sendPushNotification(Notification $notification)
    {
        try {
            $user = $notification->user;
            
            if (!$user) {
                Log::warning('Cannot send push notification - user not found', [
                    'notification_id' => $notification->id
                ]);
                return;
            }

            // For now, we'll just log the push notification
            // In a real implementation, you would integrate with Firebase FCM
            Log::info('Push notification sent', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'title' => $notification->title
            ]);

            $notification->markPushSent();
        } catch (\Exception $e) {
            Log::error('Failed to send push notification', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id
            ]);
        }
    }

    /**
     * Send SMS notification
     */
    private function sendSmsNotification(Notification $notification)
    {
        try {
            $user = $notification->user;
            
            if (!$user || !$user->phone) {
                Log::warning('Cannot send SMS notification - user or phone not found', [
                    'notification_id' => $notification->id
                ]);
                return;
            }

            // For now, we'll just log the SMS sending
            // In a real implementation, you would integrate with an SMS service
            Log::info('SMS notification sent', [
                'notification_id' => $notification->id,
                'user_phone' => $user->phone,
                'message' => $notification->message
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send SMS notification', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id
            ]);
        }
    }

    /**
     * Send bulk notifications to multiple users
     */
    public function sendBulkNotifications(
        array $userIds,
        string $type,
        string $title,
        string $message,
        array $data = [],
        string $actionUrl = null,
        string $priority = Notification::PRIORITY_MEDIUM,
        array $channels = ['in_app']
    ) {
        $notifications = [];
        
        foreach ($userIds as $userId) {
            try {
                $notification = $this->createNotification(
                    $userId,
                    $type,
                    $title,
                    $message,
                    $data,
                    $actionUrl,
                    $priority,
                    null,
                    $channels
                );
                $notifications[] = $notification;
            } catch (\Exception $e) {
                Log::error('Failed to create bulk notification for user', [
                    'error' => $e->getMessage(),
                    'user_id' => $userId
                ]);
            }
        }

        return $notifications;
    }

    /**
     * Notification helpers for specific events
     */

    public function notifyRegistrationApproved(User $user)
    {
        return $this->createNotification(
            $user->id,
            Notification::TYPE_REGISTRATION_APPROVED,
            'Registration Approved!',
            'Congratulations! Your registration has been approved. You can now access all features.',
            ['user_type' => $user->user_type],
            '/profile',
            Notification::PRIORITY_HIGH,
            $user,
            ['in_app', 'email']
        );
    }

    public function notifyRegistrationRejected(User $user, string $reason = '')
    {
        $message = 'Your registration has been rejected.';
        if ($reason) {
            $message .= ' Reason: ' . $reason;
        }
        $message .= ' You can reapply after 30 days.';

        return $this->createNotification(
            $user->id,
            Notification::TYPE_REGISTRATION_REJECTED,
            'Registration Rejected',
            $message,
            ['reason' => $reason],
            '/profile',
            Notification::PRIORITY_HIGH,
            $user,
            ['in_app', 'email']
        );
    }

    public function notifyBusinessVerified($business)
    {
        return $this->createNotification(
            $business->user_id,
            Notification::TYPE_BUSINESS_VERIFIED,
            'Business Verified!',
            'Your business "' . $business->business_name . '" has been verified and is now live.',
            ['business_id' => $business->id],
            '/business/manage',
            Notification::PRIORITY_HIGH,
            $business,
            ['in_app', 'email']
        );
    }

    public function notifyConnectionRequest($matrimonyProfile, $requesterProfile)
    {
        return $this->createNotification(
            $matrimonyProfile->user_id,
            Notification::TYPE_CONNECTION_REQUEST,
            'New Connection Request',
            $requesterProfile->user->name . ' has sent you a connection request.',
            [
                'requester_id' => $requesterProfile->user_id,
                'requester_name' => $requesterProfile->user->name
            ],
            '/matrimony/requests',
            Notification::PRIORITY_MEDIUM,
            $requesterProfile,
            ['in_app', 'push']
        );
    }

    public function notifyJobApplication($jobPosting, $applicant)
    {
        return $this->createNotification(
            $jobPosting->business->user_id,
            Notification::TYPE_JOB_APPLICATION,
            'New Job Application',
            $applicant->name . ' has applied for "' . $jobPosting->title . '".',
            [
                'job_id' => $jobPosting->id,
                'applicant_id' => $applicant->id,
                'applicant_name' => $applicant->name
            ],
            '/business/jobs/' . $jobPosting->id . '/applications',
            Notification::PRIORITY_MEDIUM,
            $jobPosting,
            ['in_app', 'email']
        );
    }

    public function notifyVolunteerApplication($opportunity, $volunteer)
    {
        return $this->createNotification(
            1, // Assuming admin user ID is 1
            Notification::TYPE_VOLUNTEER_APPLICATION,
            'New Volunteer Application',
            $volunteer->name . ' has applied for "' . $opportunity->title . '".',
            [
                'opportunity_id' => $opportunity->id,
                'volunteer_id' => $volunteer->id,
                'volunteer_name' => $volunteer->name
            ],
            '/admin/volunteers/applications',
            Notification::PRIORITY_MEDIUM,
            $opportunity,
            ['in_app']
        );
    }

    public function notifyDonationReceived($donation, $cause)
    {
        return $this->createNotification(
            $donation->user_id,
            Notification::TYPE_DONATION_RECEIVED,
            'Donation Confirmed',
            'Thank you for your donation of ₹' . number_format($donation->amount) . ' to "' . $cause->title . '".',
            [
                'donation_id' => $donation->id,
                'cause_id' => $cause->id,
                'amount' => $donation->amount
            ],
            '/donations/history',
            Notification::PRIORITY_MEDIUM,
            $donation,
            ['in_app', 'email']
        );
    }

    public function notifyPaymentSuccess($transaction)
    {
        return $this->createNotification(
            $transaction->user_id,
            Notification::TYPE_PAYMENT_SUCCESS,
            'Payment Successful',
            'Your payment of ₹' . number_format($transaction->amount) . ' for ' . $transaction->purpose . ' was successful.',
            [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'purpose' => $transaction->purpose
            ],
            '/payments/history',
            Notification::PRIORITY_MEDIUM,
            $transaction,
            ['in_app', 'email']
        );
    }

    public function notifyPaymentFailed($transaction)
    {
        return $this->createNotification(
            $transaction->user_id,
            Notification::TYPE_PAYMENT_FAILED,
            'Payment Failed',
            'Your payment of ₹' . number_format($transaction->amount) . ' for ' . $transaction->purpose . ' failed. Please try again.',
            [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'purpose' => $transaction->purpose
            ],
            '/payments/retry',
            Notification::PRIORITY_HIGH,
            $transaction,
            ['in_app', 'email']
        );
    }

    public function notifyAdminAnnouncement(array $userIds, string $title, string $message, array $data = [])
    {
        return $this->sendBulkNotifications(
            $userIds,
            Notification::TYPE_ADMIN_ANNOUNCEMENT,
            $title,
            $message,
            $data,
            null,
            Notification::PRIORITY_HIGH,
            ['in_app', 'email', 'push']
        );
    }

    public function notifyNewMessage($chatConversation, $sender, $receiver)
    {
        return $this->createNotification(
            $receiver->id,
            Notification::TYPE_NEW_MESSAGE,
            'New Message',
            $sender->name . ' sent you a message.',
            [
                'conversation_id' => $chatConversation->id,
                'sender_id' => $sender->id,
                'sender_name' => $sender->name
            ],
            '/chat/' . $chatConversation->id,
            Notification::PRIORITY_MEDIUM,
            $chatConversation,
            ['in_app', 'push']
        );
    }

    /**
     * Clean up old notifications
     */
    public function cleanupOldNotifications($days = 30)
    {
        try {
            $deleted = Notification::deleteOldNotifications($days);
            Log::info('Cleaned up old notifications', ['deleted_count' => $deleted]);
            return $deleted;
        } catch (\Exception $e) {
            Log::error('Failed to cleanup old notifications', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}