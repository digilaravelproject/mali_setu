<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('subscriptions:send-reminders', function () {
    $this->info('Starting subscription expiry reminders...');

    app(\App\Services\SubscriptionReminderService::class)->sendReminders();

    $this->info('Subscription expiry reminders completed.');
})->purpose('Send subscription expiry reminder emails to users.');

Artisan::command('payment:pending-reminders', function () {
    $this->info('Starting pending payment reminders check...');

    // 1. Business Check (Created at least 11 mins ago)
    $businesses = \App\Models\Business::where('created_at', '<=', now()->subMinutes(11))
        ->where('created_at', '>=', now()->subHours(24))
        ->get();

    foreach ($businesses as $business) {
        $isPaid = $business->subscription_status === 'active' && 
                  $business->subscription_expires_at && 
                  \Carbon\Carbon::parse($business->subscription_expires_at)->isFuture();

        if (!$isPaid) {
            $alreadySent = \App\Models\Notification::where('type', 'payment_pending_reminder')
                ->where('related_type', \App\Models\Business::class)
                ->where('related_id', $business->id)
                ->exists();

            if (!$alreadySent) {
                $this->info("Sending pending payment reminder for Business ID: {$business->id}");
                try {
                    app(\App\Services\NotificationService::class)->createNotification(
                        $business->user_id,
                        'payment_pending_reminder',
                        'Business Listing Subscription Payment Pending',
                        'You created your business listing "' . $business->business_name . '", but the subscription payment is pending. Please complete your payment to activate and verify your business listing.',
                        ['business_id' => $business->id],
                        '/dashboard/business/subscription',
                        \App\Models\Notification::PRIORITY_HIGH,
                        $business,
                        ['in_app', 'email']
                    );
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder for Business ID {$business->id}: " . $e->getMessage());
                }
            }
        }
    }

    // 2. Matrimony Check (Created at least 11 mins ago)
    $profiles = \App\Models\MatrimonyProfile::where('created_at', '<=', now()->subMinutes(11))
        ->where('created_at', '>=', now()->subHours(24))
        ->get();

    foreach ($profiles as $profile) {
        $isPaid = $profile->profile_expires_at && 
                  \Carbon\Carbon::parse($profile->profile_expires_at)->isFuture();

        if (!$isPaid) {
            $alreadySent = \App\Models\Notification::where('type', 'payment_pending_reminder')
                ->where('related_type', \App\Models\MatrimonyProfile::class)
                ->where('related_id', $profile->id)
                ->exists();

            if (!$alreadySent) {
                $this->info("Sending pending payment reminder for Matrimony Profile ID: {$profile->id}");
                try {
                    app(\App\Services\NotificationService::class)->createNotification(
                        $profile->user_id,
                        'payment_pending_reminder',
                        'Matrimony Profile Subscription Payment Pending',
                        'You created your matrimony profile, but the subscription payment is pending. Please complete your payment to activate and verify your matrimony profile.',
                        ['profile_id' => $profile->id],
                        '/dashboard/matrimony/subscription',
                        \App\Models\Notification::PRIORITY_HIGH,
                        $profile,
                        ['in_app', 'email']
                    );
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder for Matrimony Profile ID {$profile->id}: " . $e->getMessage());
                }
            }
        }
    }

    $this->info('Pending payment reminders check completed.');
})->purpose('Send payment pending reminder emails to users after 11 minutes.');
