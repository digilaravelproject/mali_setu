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
