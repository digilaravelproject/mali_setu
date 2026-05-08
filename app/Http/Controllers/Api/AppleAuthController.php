<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AppleAuthController extends Controller
{
    public function login(Request $request)
    {
        try {
    
            $request->validate([
                'apple_id' => 'required|string',
                'email'     => 'required|email',
                'latitude'  => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);
    
            $user = User::where('email', $request->email)
                        ->orWhere('apple_id', $request->apple_id)
                        ->first();
    
            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No account found. Please register first.'
                ], 404);
            }
    
            // Link apple_id if not already linked
            if (empty($user->apple_id)) {
                $user->update([
                    'apple_id' => $request->apple_id
                ]);
            }

            // Update latitude and longitude if provided
            if ($request->has('latitude') || $request->has('longitude')) {
                $user->update([
                    'latitude'  => $request->latitude ?? $user->latitude,
                    'longitude' => $request->longitude ?? $user->longitude,
                ]);
            }
    
            // OPTIONAL: revoke old tokens (recommended)
            $user->tokens()->delete();
    
            // Create token (same as screenshot)
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'status'  => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token'   => $token,
                    'token_type' => 'Bearer'
                ],
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
    
            return response()->json([
                'status'  => false,
                'message' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
    
            \Log::error($e);
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
}
