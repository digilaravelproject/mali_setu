<?php

namespace App\Services;

use App\Exceptions\FirebaseMessagingException;
use App\Models\SystemSetting;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseMessagingService
{
    public function __construct(private readonly FirebaseCredentialService $credentialService) {}

    public function send(
        string $deviceToken,
        string $title,
        string $body,
        array $data = [],
        bool $respectGlobalSetting = true,
    ): array {
        if ($respectGlobalSetting && ! SystemSetting::bool('push_notifications_enabled', true)) {
            Log::warning('Firebase push skipped because push notifications are globally disabled', [
                'token_fingerprint' => substr(hash('sha256', $deviceToken), 0, 12),
            ]);

            throw new \RuntimeException('Push notifications are disabled globally.');
        }

        $credentials = null;
        $response = null;
        $tokenFingerprint = substr(hash('sha256', $deviceToken), 0, 12);

        try {
            $credentials = $this->credentialService->credentials();
            $accessToken = $this->accessToken($credentials);
            $url = "https://fcm.googleapis.com/v1/projects/{$credentials['project_id']}/messages:send";
            $data = $this->canonicalData($data);
            $payload = ['message' => $this->messagePayload($deviceToken, $title, $body, $data)];

            Log::info('Firebase push send started', [
                'project_id' => $credentials['project_id'],
                'token_fingerprint' => $tokenFingerprint,
                'title' => $title,
                'is_test' => ($data['is_test'] ?? null) === '1',
            ]);

            $response = $this->http()->withToken($accessToken)->post($url, $payload);
            if ($response->failed()) {
                $firebaseResponse = $response->json() ?: [];
                $errorDetails = collect(data_get($firebaseResponse, 'error.details', []))
                    ->first(fn ($detail) => is_array($detail) && isset($detail['errorCode']));

                throw new FirebaseMessagingException(
                    $response->json('error.message', 'Firebase rejected the notification.'),
                    $errorDetails['errorCode'] ?? $response->json('error.status'),
                    $credentials['project_id'],
                    $response->status(),
                    $firebaseResponse,
                );
            }

            $firebaseResponse = $response->json();
            Log::info('Firebase push send succeeded', [
                'project_id' => $credentials['project_id'],
                'token_fingerprint' => $tokenFingerprint,
                'http_status' => $response->status(),
                'firebase_response' => $firebaseResponse,
            ]);

            return $firebaseResponse;
        } catch (\Throwable $e) {
            Log::error('Firebase push send failed', [
                'project_id' => $credentials['project_id'] ?? null,
                'token_fingerprint' => $tokenFingerprint,
                'http_status' => $response?->status(),
                'firebase_response' => $response?->json() ?: $response?->body(),
                'error' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            throw $e;
        }
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

    private function canonicalData(array $data): array
    {
        $imageUrl = $this->imageUrl($data);
        $nestedData = data_get($data, 'message.data');
        if (is_array($nestedData)) {
            $data = array_merge($nestedData, $data);
        }

        // A full HTTP v1 example may be pasted into the admin payload editor.
        // Treat its platform sections as configuration hints, never as client
        // data, and never allow a pasted token to replace/expose the recipient.
        unset($data['message'], $data['notification'], $data['android'], $data['apns'], $data['token']);

        $entityId = $data['id'] ?? $data['lead_id'] ?? $data['business_id']
            ?? $data['product_id'] ?? $data['service_id'] ?? $data['profile_id']
            ?? $data['notification_id'] ?? '';

        $canonical = array_merge($data, [
            'type' => (string) ($data['type'] ?? $data['action_key'] ?? 'notification'),
            'action' => (string) ($data['action'] ?? 'details'),
            'id' => (string) $entityId,
            'click_action' => (string) ($data['click_action'] ?? 'FLUTTER_NOTIFICATION_CLICK'),
        ]);

        if ($imageUrl) {
            // Flutter reads these in the foreground; native FCM configuration
            // below uses the same URL in background and terminated states.
            $canonical['image'] = $imageUrl;
            $canonical['image_url'] = $imageUrl;
        }

        return $canonical;
    }

    private function messagePayload(string $deviceToken, string $title, string $body, array $data): array
    {
        $imageUrl = $this->imageUrl($data);
        $channelId = app()->bound('config')
            ? (string) config('services.firebase.notification_channel', 'fcm_push_channel')
            : 'fcm_push_channel';
        $notification = ['title' => $title, 'body' => $body];
        $androidNotification = [
            'channel_id' => $channelId,
            'sound' => 'default',
            'click_action' => (string) $data['click_action'],
        ];
        $apns = [
            'headers' => [
                'apns-priority' => '10',
                'apns-push-type' => 'alert',
            ],
            'payload' => [
                'aps' => [
                    'sound' => 'default',
                    'badge' => 1,
                ],
            ],
        ];

        if ($imageUrl) {
            $notification['image'] = $imageUrl;
            $androidNotification['image'] = $imageUrl;
            $apns['payload']['aps']['mutable-content'] = 1;
            $apns['fcm_options'] = ['image' => $imageUrl];
        }

        return [
            'token' => $deviceToken,
            'notification' => $notification,
            'data' => $this->stringifyData($data),
            'android' => [
                'priority' => 'HIGH',
                'notification' => $androidNotification,
            ],
            'apns' => $apns,
        ];
    }

    private function imageUrl(array $data): ?string
    {
        $candidates = [
            $data['image_url'] ?? null,
            $data['image'] ?? null,
            $data['notification_image'] ?? null,
            data_get($data, 'notification.image'),
            data_get($data, 'android.notification.image'),
            data_get($data, 'apns.fcm_options.image'),
            data_get($data, 'message.notification.image'),
            data_get($data, 'message.android.notification.image'),
            data_get($data, 'message.apns.fcm_options.image'),
        ];

        foreach ($candidates as $candidate) {
            if (! is_string($candidate)) {
                continue;
            }
            $candidate = trim($candidate);
            if (filter_var($candidate, FILTER_VALIDATE_URL)
                && strtolower((string) parse_url($candidate, PHP_URL_SCHEME)) === 'https') {
                return $candidate;
            }
        }

        return null;
    }

    private function http(): PendingRequest
    {
        return Http::acceptJson()->timeout(15)->retry(2, 250, null, false);
    }
}
