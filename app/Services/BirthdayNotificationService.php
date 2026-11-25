<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class BirthdayNotificationService
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function birthdayUsers(): Collection
    {
        return User::query()
            ->whereNotNull('dob')
            ->whereMonth('dob', now()->month)
            ->whereDay('dob', now()->day)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function sendToday(): int
    {
        $sent = 0;
        foreach ($this->birthdayUsers() as $user) {
            $alreadySent = Notification::query()
                ->where('user_id', $user->id)
                ->where('type', Notification::TYPE_BIRTHDAY_WISH)
                ->whereDate('created_at', today())
                ->exists();

            if ($alreadySent) {
                continue;
            }

            $this->notifications->createNotification(
                $user->id,
                Notification::TYPE_BIRTHDAY_WISH,
                'Happy Birthday, {{user_name}}! 🎉',
                'The Mali Setu family wishes you a wonderful birthday filled with happiness and success.',
                ['birthday' => optional($user->dob)->format('Y-m-d')],
                '/dashboard',
                Notification::PRIORITY_HIGH,
                $user,
                ['in_app', 'push']
            );
            $sent++;
        }

        return $sent;
    }
}
