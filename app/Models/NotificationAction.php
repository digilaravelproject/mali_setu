<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationAction extends Model
{
    protected $fillable = [
        'key', 'name', 'description', 'title_template', 'body_template',
        'action_url', 'available_variables', 'is_enabled', 'push_enabled',
    ];

    protected function casts(): array
    {
        return [
            'available_variables' => 'array',
            'is_enabled' => 'boolean',
            'push_enabled' => 'boolean',
        ];
    }

    public static function defaults(): array
    {
        return [
            ['key' => Notification::TYPE_REGISTRATION_APPROVED, 'name' => 'Registration approved', 'title_template' => 'Registration Approved!', 'body_template' => 'Congratulations {{user_name}}! Your registration has been approved.', 'action_url' => '/profile', 'available_variables' => ['user_name', 'user_type']],
            ['key' => Notification::TYPE_REGISTRATION_REJECTED, 'name' => 'Registration rejected', 'title_template' => 'Registration Rejected', 'body_template' => 'Your registration has been rejected. {{reason}}', 'action_url' => '/profile', 'available_variables' => ['user_name', 'reason']],
            ['key' => Notification::TYPE_BUSINESS_VERIFIED, 'name' => 'Business verified', 'title_template' => 'Business Verified!', 'body_template' => 'Your business has been verified and is now live.', 'action_url' => '/business/manage', 'available_variables' => ['user_name', 'business_id']],
            ['key' => Notification::TYPE_BUSINESS_REJECTED, 'name' => 'Business rejected', 'title_template' => 'Business Rejected', 'body_template' => 'Your business verification was rejected. {{reason}}', 'action_url' => '/business/manage', 'available_variables' => ['user_name', 'reason']],
            ['key' => Notification::TYPE_MATRIMONY_APPROVED, 'name' => 'Matrimony profile approved', 'title_template' => 'Matrimony Profile Approved', 'body_template' => 'Your matrimony profile has been approved.', 'action_url' => '/dashboard/matrimony', 'available_variables' => ['user_name', 'profile_id']],
            ['key' => Notification::TYPE_MATRIMONY_REJECTED, 'name' => 'Matrimony profile rejected', 'title_template' => 'Matrimony Profile Rejected', 'body_template' => 'Your matrimony profile was rejected. {{reason}}', 'action_url' => '/dashboard/matrimony', 'available_variables' => ['user_name', 'reason']],
            ['key' => Notification::TYPE_MATRIMONY_PENDING, 'name' => 'Matrimony profile pending', 'title_template' => 'Matrimony profile pending approval', 'body_template' => 'Your matrimony profile has been created and is pending admin approval.', 'action_url' => '/dashboard/matrimony', 'available_variables' => ['user_name', 'profile_id']],
            ['key' => Notification::TYPE_MATRIMONY_PROFILE_CREATED, 'name' => 'New matrimony profile', 'title_template' => 'New matrimony profile created', 'body_template' => 'A new matrimony profile is available to view.', 'action_url' => '/dashboard/matrimony/browse', 'available_variables' => ['user_name', 'profile_id']],
            ['key' => Notification::TYPE_CONNECTION_REQUEST, 'name' => 'Connection request', 'title_template' => 'New Connection Request', 'body_template' => '{{requester_name}} has sent you a connection request.', 'action_url' => '/matrimony/requests', 'available_variables' => ['user_name', 'requester_name', 'requester_id']],
            ['key' => Notification::TYPE_CONNECTION_ACCEPTED, 'name' => 'Connection accepted', 'title_template' => 'Connection Request Accepted', 'body_template' => 'Your connection request has been accepted.', 'action_url' => '/matrimony/connections', 'available_variables' => ['user_name']],
            ['key' => Notification::TYPE_CONNECTION_REJECTED, 'name' => 'Connection rejected', 'title_template' => 'Connection Request Declined', 'body_template' => 'Your connection request was declined.', 'action_url' => '/matrimony/requests', 'available_variables' => ['user_name']],
            ['key' => Notification::TYPE_JOB_APPLICATION, 'name' => 'Job application received', 'title_template' => 'New Job Application', 'body_template' => '{{applicant_name}} submitted a new job application.', 'action_url' => null, 'available_variables' => ['user_name', 'job_id', 'applicant_name', 'applicant_id']],
            ['key' => Notification::TYPE_JOB_APPLICATION_STATUS, 'name' => 'Job application status', 'title_template' => 'Job Application Updated', 'body_template' => 'The status of your job application has changed.', 'action_url' => '/jobs/my-applications', 'available_variables' => ['user_name', 'job_id', 'status']],
            ['key' => Notification::TYPE_VOLUNTEER_APPLICATION, 'name' => 'Volunteer application', 'title_template' => 'New Volunteer Application', 'body_template' => '{{volunteer_name}} submitted a volunteer application.', 'action_url' => '/admin/volunteers/applications', 'available_variables' => ['user_name', 'volunteer_name', 'volunteer_id', 'opportunity_id']],
            ['key' => Notification::TYPE_VOLUNTEER_APPROVED, 'name' => 'Volunteer approved', 'title_template' => 'Volunteer Application Approved', 'body_template' => 'Your volunteer application has been approved.', 'action_url' => '/volunteer/applications', 'available_variables' => ['user_name', 'opportunity_id']],
            ['key' => Notification::TYPE_DONATION_RECEIVED, 'name' => 'Donation received', 'title_template' => 'Donation Confirmed', 'body_template' => 'Thank you for your donation of ₹{{amount}}.', 'action_url' => '/donations/history', 'available_variables' => ['user_name', 'amount', 'donation_id', 'cause_id']],
            ['key' => Notification::TYPE_PAYMENT_SUCCESS, 'name' => 'Payment successful', 'title_template' => 'Payment Successful', 'body_template' => 'Your payment of ₹{{amount}} for {{purpose}} was successful.', 'action_url' => '/payments/history', 'available_variables' => ['user_name', 'amount', 'purpose', 'transaction_id']],
            ['key' => Notification::TYPE_PAYMENT_FAILED, 'name' => 'Payment failed', 'title_template' => 'Payment Failed', 'body_template' => 'Your payment of ₹{{amount}} for {{purpose}} failed. Please try again.', 'action_url' => '/payments/retry', 'available_variables' => ['user_name', 'amount', 'purpose', 'transaction_id']],
            ['key' => Notification::TYPE_ADMIN_ANNOUNCEMENT, 'name' => 'Admin announcement', 'title_template' => 'Mali Setu Announcement', 'body_template' => 'We have a new update for you.', 'action_url' => null, 'available_variables' => ['user_name']],
            ['key' => Notification::TYPE_PROFILE_UPDATE, 'name' => 'Profile update', 'title_template' => 'Profile Updated', 'body_template' => 'Your profile was updated successfully.', 'action_url' => '/profile', 'available_variables' => ['user_name']],
            ['key' => Notification::TYPE_NEW_MESSAGE, 'name' => 'New chat message', 'title_template' => 'New Message', 'body_template' => '{{sender_name}} sent you a message.', 'action_url' => null, 'available_variables' => ['user_name', 'sender_name', 'sender_id', 'conversation_id']],
            ['key' => Notification::TYPE_SUBSCRIPTION_EXPIRY_REMINDER, 'name' => 'Subscription expiry reminder', 'title_template' => 'Subscription Expiry Reminder', 'body_template' => 'Your subscription will expire soon.', 'action_url' => '/dashboard/subscriptions', 'available_variables' => ['user_name', 'days_remaining', 'expiry_date']],
        ];
    }

    public static function ensureDefaults(): void
    {
        $definitions = static::defaults();
        $existingKeys = static::query()->whereIn('key', array_column($definitions, 'key'))->pluck('key')->all();

        foreach ($definitions as $definition) {
            if (in_array($definition['key'], $existingKeys, true)) {
                continue;
            }
            static::query()->firstOrCreate(['key' => $definition['key']], $definition);
        }
    }

    public function isSystemAction(): bool
    {
        return in_array($this->key, array_column(static::defaults(), 'key'), true);
    }

    public function render(string $template, array $data): string
    {
        return preg_replace_callback('/\{\{\s*([A-Za-z0-9_.-]+)\s*\}\}/', function (array $matches) use ($data) {
            $value = data_get($data, $matches[1], '');

            return is_scalar($value) ? (string) $value : '';
        }, $template) ?? $template;
    }
}
