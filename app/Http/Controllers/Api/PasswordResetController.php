<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Step 1: Send OTP to user's email and save to users.otp (+ expiry).
     */
    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $data['email'])->first();

        // For security, don't reveal if email exists; but we will still proceed accordingly.
        if ($user) {
            $otp = random_int(100000, 999999); // 6-digit
            $user->otp = (string)$otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(10); // optional column
            $user->save();

            try {
                Mail::to($user->email)->send(new SendOtpMail($otp));
            } catch (\Throwable $e) {
                // If mail fails, clear OTP to avoid a stranded code
                $user->otp = null;
                $user->otp_expires_at = null;
                $user->save();

                return response()->json([
                    'error' => $e,
                    'success' => false,
                    'message' => 'Failed to send OTP email.',
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'OTP has been sent.',
        ]);
    }

    /**
     * Step 2: Verify OTP (email + otp).
     */
    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !$user->otp || $user->otp !== $data['otp']) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP.'],
            ]);
        }

        if (!empty($user->otp_expires_at) && Carbon::parse($user->otp_expires_at)->isPast()) {
            throw ValidationException::withMessages([
                'otp' => ['OTP has expired. Please request a new one.'],
            ]);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'OTP verified. You can now reset your password.',
        ]);
    }

    /**
     * Step 3: Reset password (email + otp + password/confirmation).
     * Re-validates OTP here to avoid needing a separate reset token.
     */
    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email'                 => 'required|email',
            'otp'                   => 'required|digits:6',
            'password'              => 'required|string|min:8|confirmed',
            // expects "password_confirmation" as well
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !$user->otp || $user->otp !== $data['otp']) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP.'],
            ]);
        }

        if (!empty($user->otp_expires_at) && Carbon::parse($user->otp_expires_at)->isPast()) {
            throw ValidationException::withMessages([
                'otp' => ['OTP has expired. Please request a new one.'],
            ]);
        }

        $user->password = Hash::make($data['password']);
        // clear otp after successful reset
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'Password reset successful. You can now log in.',
        ]);
    }
}
