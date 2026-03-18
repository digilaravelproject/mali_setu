<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionReminderService
{
    /**
     * Send subscription expiry reminders based on configured offsets.
     *
     * This sends email + in-app notifications using the existing notification template.
     */
    public function sendReminders(): void
    {
        $today = Carbon::now()->startOfDay();

        // Define the reminder stages relative to the subscription expiry date.
        $reminderStages = [
            'before_3_months' => -3,
            'before_2_months' => -2,
            'before_1_month'  => -1,
            'on_date'         => 0,
            'after_1_month'   => 1,
            'after_2_months'  => 2,
            'after_3_months'  => 3,
            'after_4_months'  => 4,
            'after_5_months'  => 5,
            'after_6_months'  => 6,
        ];

        // Load businesses whose subscription expiry is within the window we care about.
        $earliest = $today->copy()->subMonthsNoOverflow(6)->startOfDay();
        $latest = $today->copy()->addMonthsNoOverflow(3)->endOfDay();

        $businesses = Business::with('user')
            ->whereNotNull('subscription_expires_at')
            ->whereBetween('subscription_expires_at', [$earliest, $latest])
            ->get();

        foreach ($businesses as $business) {
            if (!$business->user) {
                continue;
            }

            $expiry = Carbon::parse($business->subscription_expires_at)->startOfDay();

            foreach ($reminderStages as $stageKey => $monthDelta) {

                $sendAt = $expiry->copy()->addMonthsNoOverflow($monthDelta);

                // Only send on the exact day
                if (!$sendAt->isSameDay($today)) {
                    continue;
                }

                // Avoid duplicate reminders for the same stage
                $alreadySent = Notification::where('user_id', $business->user_id)
                    ->where('type', Notification::TYPE_SUBSCRIPTION_EXPIRY_REMINDER)
                    ->where('related_type', Business::class)
                    ->where('related_id', $business->id)
                    ->where('data->reminder_stage', $stageKey)
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                $this->sendReminder($business, $expiry, $stageKey);
            }
        }
    }

    protected function sendReminder(Business $business, Carbon $expiry, string $stageKey): void
    {
        $user = $business->user;
        $expiryDate = $expiry->format('M d, Y');

        $stageLabels = [
            'before_3_months' => '3 months before expiry',
            'before_2_months' => '2 months before expiry',
            'before_1_month'  => '1 month before expiry',
            'on_date'         => 'on expiry date',
            'after_1_month'   => '1 month after expiry',
            'after_2_months'  => '2 months after expiry',
            'after_3_months'  => '3 months after expiry',
            'after_4_months'  => '4 months after expiry',
            'after_5_months'  => '5 months after expiry',
            'after_6_months'  => '6 months after expiry',
        ];

        $title = "Subscription expiring on {$expiryDate}";

        if (str_starts_with($stageKey, 'before_')) {
            $message = "Your subscription for \"{$business->business_name}\" will expire on {$expiryDate}. This is your reminder {$stageLabels[$stageKey]}. Please renew to avoid interruption.";
        } elseif ($stageKey === 'on_date') {
            $message = "Your subscription for \"{$business->business_name}\" expires today ({$expiryDate}). Please renew to keep your listing active.";
        } else {
            $message = "Your subscription for \"{$business->business_name}\" expired on {$expiryDate}. This is your reminder {$stageLabels[$stageKey]}. Please renew to continue enjoying the service.";
        }

        $notificationService = app(NotificationService::class);
        $notificationService->createNotification(
            $user->id,
            Notification::TYPE_SUBSCRIPTION_EXPIRY_REMINDER,
            $title,
            $message,
            [
                'business_id' => $business->id,
                'expiry_date' => $expiryDate,
                'reminder_stage' => $stageKey,
            ],
            '/business/manage',
            Notification::PRIORITY_HIGH,
            $business,
            ['in_app', 'email']
        );

        Log::info('Sent subscription expiry reminder', [
            'user_id' => $user->id,
            'business_id' => $business->id,
            'stage' => $stageKey,
            'expiry_date' => $expiryDate,
        ]);
    }
}
