<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Exception;

class SocialAuthController extends Controller
{
    // Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $email = $googleUser->getEmail();
            $user = User::where('email', $email)->first();

            // API Flow
            if ($request->expectsJson() || $request->wantsJson()) {
                if ($user) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                } else {
                    $user = User::create([
                        'email' => $email,
                        'name' => $googleUser->getName(),
                        'google_id' => $googleUser->getId(),
                        'password' => bcrypt(Str::random(12)),
                        'caste_verification_status' => 'pending',
                    ]);
                    $this->sendWelcomeEmail($user);
                }

                Auth::login($user);
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'message' => 'Google login successful',
                    'user' => $user,
                    'token' => $token,
                ]);
            }

            // Web Flow
            if (!$user) {
                // Auto-register user with user type general
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'google_id' => $googleUser->getId(),
                    'user_type' => 'general',
                    'password' => bcrypt(Str::random(16)),
                    'caste_verification_status' => 'pending',
                ]);
                $this->sendWelcomeEmail($user);

                return redirect()->route('register')->withErrors([
                    'email' => 'Account does not exist. Please complete your registration.'
                ]);
            }

            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            if ($user->caste_verification_status === 'pending' || $user->caste_verification_status === 'rejected') {
                return redirect()->route('login')->withErrors([
                    'email' => 'Your caste verification status is not approved yet.'
                ]);
            }

            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Successfully logged in with Google!');
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

            $user = User::firstOrCreate(
                ['email' => $fbUser->getEmail()],
                [
                    'name' => $fbUser->getName(),
                    'password' => bcrypt(Str::random(12)),
                    'user_type' => 'general',
                    'caste_verification_status' => 'pending',
                ]
            );

            if ($user->wasRecentlyCreated) {
                $this->sendWelcomeEmail($user);
            }

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

    private function sendWelcomeEmail(User $user): void
    {
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable $exception) {
            Log::warning('Social registration welcome email failed', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
