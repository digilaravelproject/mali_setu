<?php

namespace App\Services;

use App\Models\User;
use App\Models\Webinar;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DailyMeetingService
{
    public function isConfigured(): bool
    {
        return filled(config('services.daily.api_key'));
    }

    public function credentials(Webinar $webinar, User $user, bool $isHost): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Daily.co is not configured. Add DAILY_API_KEY to the server environment.');
        }

        $room = $this->ensureRoom($webinar);
        $expiresAt = min($webinar->ends_at->copy()->addHour()->timestamp, now()->addDay()->timestamp);
        $response = $this->client()->post('meeting-tokens', [
            'properties' => [
                'room_name' => $room['name'],
                'is_owner' => $isHost,
                'user_name' => $user->name,
                'user_id' => (string) $user->getKey(),
                'exp' => $expiresAt,
            ],
        ])->throw()->json();

        return [
            'provider' => 'daily',
            'room_url' => $room['url'],
            'room_name' => $room['name'],
            'token' => $response['token'] ?? throw new RuntimeException('Daily.co did not return a meeting token.'),
            'role' => $isHost ? 'host' : 'participant',
            'display_name' => $user->name,
            'expires_at' => $expiresAt,
        ];
    }

    private function ensureRoom(Webinar $webinar): array
    {
        $name = $webinar->daily_room_name ?: 'mali-setu-webinar-'.$webinar->getKey();
        $response = $this->client()->get('rooms/'.$name);

        if ($response->status() === 404) {
            $response = $this->client()->post('rooms', [
                'name' => $name,
                'privacy' => 'private',
                'properties' => [
                    'exp' => $webinar->ends_at->copy()->addDay()->timestamp,
                    'enable_chat' => true,
                    'enable_screenshare' => true,
                    'start_video_off' => false,
                    'start_audio_off' => false,
                ],
            ]);
        }

        $room = $response->throw()->json();
        $webinar->forceFill([
            'daily_room_name' => $room['name'] ?? $name,
            'daily_room_url' => $room['url'] ?? null,
        ])->save();

        return ['name' => $room['name'] ?? $name, 'url' => $room['url'] ?? $webinar->daily_room_url];
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('services.daily.api_url'), '/'))
            ->withToken((string) config('services.daily.api_key'))
            ->acceptJson()
            ->asJson()
            ->timeout((int) config('services.daily.timeout', 15));
    }
}
