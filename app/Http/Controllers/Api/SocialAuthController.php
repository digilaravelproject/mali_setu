<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;

class SocialAuthController extends Controller
{
    // Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // Handle Google callback
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            } else {
                $user = User::create([
                    'email' => $googleUser->getEmail(),
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(12)),
                    'caste_verification_status' => 'approved',
                ]);
            }

            Auth::login($user);

            $token = $user->createToken('auth_token')->plainTextToken;

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Google login successful',
                    'user' => $user,
                    'token' => $token,
                ]);
            }

            return redirect()->route('dashboard')->with('success', 'Google login successful');
        } catch (Exception $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return redirect()->route('login')->withErrors(['error' => 'Google Authentication failed: ' . $e->getMessage()]);
        }
    }

    // Redirect to Facebook
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    // Handle Facebook callback
    public function handleFacebookCallback(Request $request)
    {
        try {
            $fbUser = Socialite::driver('facebook')->stateless()->user();

            $user = User::updateOrCreate(
                ['email' => $fbUser->getEmail()],
                [
                    'name' => $fbUser->getName(),
                    'password' => bcrypt(Str::random(12)),
                ]
            );

            Auth::login($user);

            $token = $user->createToken('auth_token')->plainTextToken;

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Facebook login successful',
                    'user' => $user,
                    'token' => $token,
                ]);
            }

            return redirect()->route('dashboard')->with('success', 'Facebook login successful');
        } catch (Exception $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return redirect()->route('login')->withErrors(['error' => 'Facebook Authentication failed: ' . $e->getMessage()]);
        }
    }
}
