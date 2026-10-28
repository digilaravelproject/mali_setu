<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class FirebasePhoneAuthService
{
    public function sendVerificationCode(string $phoneNumber, string $recaptchaToken): array
    {
        $apiKey = config('services.firebase.web_api_key');
        if (! $apiKey) {
            throw new \RuntimeException('Firebase phone authentication is not configured.');
        }

        $response = $this->http()->post(
            'https://identitytoolkit.googleapis.com/v1/accounts:sendVerificationCode?key='.$apiKey,
            ['phoneNumber' => $phoneNumber, 'recaptchaToken' => $recaptchaToken]
        );

        if ($response->failed()) {
            throw new \RuntimeException($response->json('error.message', 'Firebase could not send the OTP.'));
        }

        return $response->json();
    }

    private function http(): PendingRequest
    {
        return Http::acceptJson()->timeout(15)->retry(2, 250, null, false);
    }
}
