<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\WelcomeMail;
use App\Mail\SendOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;

class WebAuthController extends Controller
{
    /**
     * Show Web Login Form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle Web Login Request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Mirror API check: block pending/rejected cast certs
            if ($user->caste_verification_status === 'pending' || $user->caste_verification_status === 'rejected') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your caste verification status is not approved yet.',
                ])->withInput($request->only('email'));
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Show Web Registration Form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle Web Registration Request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'            => 'required|string|in:Mr.,Mrs.,Ms.,Dr.',
            'first_name'       => 'required|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'required|string|email|max:255|unique:users,email',
            'phone'            => 'required|string|max:15|unique:users,phone',
            'dob'              => 'required|date',
            'cast_certificate' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'address'          => 'required|string|max:500',
            'pincode'          => 'required|digits:6',
            'state'            => 'required|string|max:100',
            'city'             => 'required|string|max:100',
            'country'          => 'required|string|max:100',
            'taluka'           => 'nullable|string|max:100',
            'village'          => 'required|string|max:100',
            'user_type'        => 'required|in:general,business,matrimony,volunteer',
            'occupation'       => 'required|string|max:255',
            'company_name'     => 'nullable|string|max:255',
            'dept_name'        => 'nullable|string|max:255',
            'designation'      => 'nullable|string|max:255',
            'password'         => 'required|string|min:8|confirmed',
            'term_condition'   => 'accepted',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Handle caste certificate upload
            $cast_certificate = '';
            if ($request->hasFile('cast_certificate')) {
                $file = $request->file('cast_certificate');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                if (!Storage::disk('public')->exists('certificates')) {
                    Storage::disk('public')->makeDirectory('certificates');
                }
                Storage::disk('public')->putFileAs('certificates', $file, $fileName);
                $cast_certificate = 'certificates/' . $fileName;
            }

            // Concatenate title, first, middle, last name into name
            $name = trim($request->title . ' ' . $request->first_name . ' ' . ($request->middle_name ? $request->middle_name . ' ' : '') . $request->last_name);

            $user = User::create([
                'name'                   => $name,
                'email'                  => $request->email,
                'phone'                  => $request->phone,
                'dob'                    => $request->dob,
                'cast_certificate'       => $cast_certificate,
                'address'                => $request->address,
                'pincode'                => $request->pincode,
                'state'                  => $request->state,
                'city'                   => $request->city,
                'country'                => $request->country,
                'taluka'                 => $request->taluka,
                'village'                => $request->village,
                'occupation'             => $request->occupation,
                'company_name'           => $request->company_name,
                'dept_name'              => $request->dept_name,
                'designation'            => $request->designation,
                'password'               => Hash::make($request->password),
                'user_type'              => $request->user_type,
                'caste_verification_status' => 'approved', // mirror the API default
            ]);

            // Fire the Welcome Email (do not crash registration on fail)
            try {
                Mail::to($user->email)->send(new WelcomeMail($user));
            } catch (\Throwable $mailEx) {
                \Log::warning('Web welcome email failed to send', [
                    'user_id' => $user->id,
                    'error'   => $mailEx->getMessage(),
                ]);
            }

            // Log the user in directly after registering
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome to Mali Setu.');

        } catch (\Exception $e) {
            \Log::error('Web registration failure: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to register. Please try again. ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show Forgot Password Form (Unified Step-by-Step wizard)
     */
    public function showForgotPassword()
    {
        return view('auth.forgot_password');
    }

    /**
     * Step 1: Send OTP to User's Email (AJAX supported)
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $otp = random_int(100000, 999999);
            $user->otp = (string)$otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(10);
            $user->save();

            try {
                Mail::to($user->email)->send(new SendOtpMail($otp));
            } catch (\Throwable $e) {
                $user->otp = null;
                $user->otp_expires_at = null;
                $user->save();

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP email: ' . $e->getMessage()
                ], 500);
            }
        } else {
            // Keep user experience safe but check existence internally
            return response()->json([
                'success' => false,
                'message' => 'No user account found with that email address.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'A 6-digit verification OTP has been sent to your email.'
        ]);
    }

    /**
     * Step 2: Verify OTP (AJAX supported)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->otp || $user->otp !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code.'
            ], 400);
        }

        if (!empty($user->otp_expires_at) && Carbon::parse($user->otp_expires_at)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'This OTP has expired. Please request a new one.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully. You can now reset your password.'
        ]);
    }

    /**
     * Step 3: Reset Password (AJAX supported)
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'otp'      => 'required|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->otp || $user->otp !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired session. Please start over.'
            ], 400);
        }

        if (!empty($user->otp_expires_at) && Carbon::parse($user->otp_expires_at)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'The OTP session has expired. Please start over.'
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successful! You can now log in.'
        ]);
    }

    /**
     * Handle Web Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
