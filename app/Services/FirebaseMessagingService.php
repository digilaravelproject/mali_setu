<?php

namespace App\Services;

use App\Models\SystemSetting;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FirebaseMessagingService
{
    public function __construct(private readonly FirebaseCredentialService $credentialService) {}

    public function send(string $deviceToken, string $title, string $body, array $data = []): array
    {
        if (! SystemSetting::bool('push_notifications_enabled', true)) {
            throw new \RuntimeException('Push notifications are disabled globally.');
        }

        $credentials = $this->credentialService->credentials();
        $accessToken = $this->accessToken($credentials);
        $url = "https://fcm.googleapis.com/v1/projects/{$credentials['project_id']}/messages:send";
        $payload = [
            'message' => [
                'token' => $deviceToken,
                'notification' => ['title' => $title, 'body' => $body],
                'data' => $this->stringifyData($data),
                'android' => ['priority' => 'high'],
                'apns' => ['payload' => ['aps' => ['sound' => 'default']]],
            ],
        ];

        $response = $this->http()->withToken($accessToken)->post($url, $payload);
        if ($response->failed()) {
            throw new \RuntimeException('Firebase rejected the notification: '.$response->json('error.message', $response->body()));
        }

        return $response->json();
    }

    private function accessToken(array $credentials): string
    {
        $cacheKey = 'firebase_access_token_'.sha1($credentials['client_email'].'|'.$credentials['project_id']);

        return Cache::remember($cacheKey, now()->addMinutes(50), function () use ($credentials) {
            $now = time();
            $assertion = JWT::encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => $credentials['token_uri'],
                'iat' => $now,
                'exp' => $now + 3600,
            ], $credentials['private_key'], 'RS256');

            $response = $this->http()->asForm()->post($credentials['token_uri'], [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $assertion,
            ]);

            if ($response->failed() || ! $response->json('access_token')) {
                throw new \RuntimeException('Unable to authenticate with Firebase: '.$response->json('error_description', $response->body()));
            }

            return $response->json('access_token');
        });
    }

    private function stringifyData(array $data): array
    {
        return collect($data)->mapWithKeys(function ($value, $key) {
            return [(string) $key => is_scalar($value) || $value === null
                ? (string) $value
                : json_encode($value, JSON_UNESCAPED_SLASHES)];
        })->all();
    }

    private function http(): PendingRequest
    {
        return Http::acceptJson()->timeout(15)->retry(2, 250);
    }
}
