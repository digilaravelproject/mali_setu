<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseMessagingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FcmTokenController extends Controller
{
    public function __construct(private readonly FirebaseMessagingService $firebase) {}

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token' => ['required', 'string', 'max:4096'],
        ]);

        $request->user()->forceFill(['fcm_token' => $validated['fcm_token']])->save();

        return response()->json([
            'success' => true,
            'message' => 'FCM token updated successfully.',
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->user()->forceFill(['fcm_token' => null])->save();

        return response()->json([
            'success' => true,
            'message' => 'FCM token removed successfully.',
        ]);
    }

    public function test(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token' => ['required', 'string', 'max:4096'],
            'title' => ['sometimes', 'string', 'max:255'],
            'message' => ['sometimes', 'string', 'max:1000'],
            'data' => ['sometimes', 'array'],
        ]);

        $logContext = [
            'token_fingerprint' => substr(hash('sha256', $validated['fcm_token']), 0, 12),
            'title' => $validated['title'] ?? 'Mali Setu Test Notification',
        ];

        Log::info('Test push API request received', $logContext);

        try {
            $result = $this->firebase->send(
                $validated['fcm_token'],
                $validated['title'] ?? 'Mali Setu Test Notification',
                $validated['message'] ?? 'Firebase push notifications are working correctly.',
                array_merge($validated['data'] ?? [], ['is_test' => '1']),
                false,
            );

            Log::info('Test push API request succeeded', $logContext + [
                'firebase_response' => $result,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test push notification sent successfully.',
                'data' => ['firebase_message_name' => $result['name'] ?? null],
            ]);
        } catch (\Throwable $e) {
            Log::error('Test push API request failed', $logContext + [
                'error' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Test push notification could not be sent.',
                'error' => $e->getMessage(),
            ], 502);
        }
    }
}
