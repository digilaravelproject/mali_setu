<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebasePhoneAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhoneOtpController extends Controller
{
    public function __construct(private readonly FirebasePhoneAuthService $firebase) {}

    public function send(Request $request): JsonResponse
    {
        return $this->sendCode($request, 'OTP sent successfully.');
    }

    public function resend(Request $request): JsonResponse
    {
        return $this->sendCode($request, 'OTP resent successfully.');
    }

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_info' => ['required', 'string', 'max:4096'],
            'otp' => ['required', 'digits:6'],
        ]);

        try {
            $result = $this->firebase->verifyCode($validated['session_info'], $validated['otp']);

            return response()->json([
                'success' => true,
                'message' => 'Phone number verified successfully.',
                'data' => [
                    'verified' => true,
                    'phone_number' => $result['phoneNumber'] ?? null,
                    'firebase_uid' => $result['localId'] ?? null,
                    'firebase_id_token' => $result['idToken'] ?? null,
                    'expires_in' => isset($result['expiresIn']) ? (int) $result['expiresIn'] : null,
                    'is_new_user' => (bool) ($result['isNewUser'] ?? false),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    private function sendCode(Request $request, string $message): JsonResponse
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'regex:/^\+[1-9]\d{7,14}$/'],
            'recaptcha_token' => ['required', 'string', 'max:4096'],
        ]);

        try {
            $result = $this->firebase->sendVerificationCode(
                $validated['phone_number'],
                $validated['recaptcha_token']
            );

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => ['session_info' => $result['sessionInfo'] ?? null],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'OTP could not be sent.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
