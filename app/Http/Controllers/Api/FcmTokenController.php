<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
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
}
