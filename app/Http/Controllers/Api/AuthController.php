<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Mail\PasswordChangedMail;
use Carbon\Carbon;


class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'             => 'required|string|max:255',
                'email'            => 'required|string|email|max:255|unique:users',
                'age'              => 'nullable|integer|min:18|max:100', // optional, must be 18-100 if provided
                'phone'            => 'required|string|max:15|unique:users',
                'cast_certificate' => 'nullable|string',
                'occupation'       => 'nullable|string|max:255',
                'occupation'       => 'nullable|string|max:255',
                'occupation'       => 'nullable|string|max:255',
                'occupation'       => 'nullable|string|max:255',
                'company_name'     => 'nullable|string|max:255',
                'dept_name'        => 'nullable|string|max:255',
                'dob' => 'nullable|date_format:d/m/Y',
                'designation'      => 'nullable|string|max:255',

                'reffral_code'     => 'nullable|string|max:50',
                'address'          => 'nullable|string|max:500',
                'nearby_location'  => 'nullable|string|max:255',
                'pincode'          => 'nullable|digits:6', // Indian pincode format
                'road_number'      => 'nullable|string|max:50',
                'state'            => 'nullable|string|max:100',
                'city'             => 'nullable|string|max:100',
                'sector'           => 'nullable|string|max:100',
                'district'         => 'nullable|string|max:100',
                'village'          => 'nullable|string|max:100',
                'destination'      => 'nullable|string|max:255',
                'latitude'         => 'nullable|numeric|between:-90,90',
                'longitude'        => 'nullable|numeric|between:-180,180',
                'password'         => 'required|string|min:8|confirmed',
                'user_type'        => 'required|in:general,business,matrimony,volunteer',
                'term_condition'   => 'accepted', // must be checked (true/1)
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $cast_certificate = '';

            if ($request->cast_certificate) {
                $data = $request->cast_certificate;

                // strip the "data:..." part
                $data = preg_replace('#^data:.*?;base64,#', '', $data);
                $fileData = base64_decode($data);

                $fileName = uniqid() . '.pdf';

                // Ensure the folder exists
                if (!Storage::disk('public')->exists('certificates')) {
                    Storage::disk('public')->makeDirectory('certificates');
                }

                // Save file in storage/app/public/certificates
                $rev = Storage::disk('public')->put('certificates/' . $fileName, $fileData);

                // Save path in DB
                $cast_certificate = 'certificates/' . $fileName;
            }

            if ($request->filled('dob')) {
                try {
                    // Convert dd/mm/yyyy to yyyy-mm-dd
                    $request->merge([
                        'dob' => Carbon::createFromFormat('d/m/Y', $request->dob)->format('Y-m-d')
                    ]);
                } catch (\Exception $e) {
                    // Handle invalid date format if needed
                    return response()->json([
                        'error' => 'Invalid date format. Expected dd/mm/yyyy.'
                    ], 422);
                }
            }

            $user = User::create([
                'name'                   => $request->name,
                'email'                  => $request->email,
                'phone'                  => $request->phone,
                'password'               => Hash::make($request->password),
                // optional fields
                'age'                    => $request->age,
                'occupation'             => $request->occupation,
                'company_name'           => $request->company_name,
                'dept_name'              => $request->dept_name,
                'dob'                    => $request->dob,
                'designation'            => $request->designation,
                'reffral_code'           => $request->reffral_code,
                'address'                => $request->address,
                'nearby_location'        => $request->nearby_location,
                'pincode'                => $request->pincode,
                'road_number'            => $request->road_number,
                'state'                  => $request->state,
                'city'                   => $request->city,
                'sector'                 => $request->sector,
                'district'               => $request->district,                'village'                 => $request->village,                'destination'            => $request->destination,
                'latitude'               => $request->latitude,
                'longitude'              => $request->longitude,
                'cast_certificate'       => $cast_certificate,
                'user_type'              => $request->user_type,
                'caste_verification_status' => 'approved'
            ]);
            
            // ✅ NEW: Send welcome email (do not break registration on mail failure)
            try {
                Mail::to($user->email)->send(new WelcomeMail($user));
            } catch (\Throwable $mailEx) {
                // Optional: log it if you use logging
                \Log::warning('Welcome email failed to send', [
                    'user_id' => $user->id,
                    'error'   => $mailEx->getMessage(),
                ]);
                // Do not return error; registration succeeded
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register the user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        
        // Prevent when cast certificate is in pending
        if ($user->caste_verification_status == 'pending' || $user->caste_verification_status == 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Cast verification status is not approved'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user profile
     */
    public function profile_old(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user()->load(['casteCertificate', 'business', 'matrimonyProfile'])
            ]
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load([
            'casteCertificate',
            'business',
            'matrimonyProfile'
        ]);

        $transaction = null;

        // Check if matrimony profile exists
        $user->is_matrimony = $user->matrimonyProfile ? true : false;

        $user->is_business = $user->business ? true : false;

        /*
        |--------------------------------------------------------------------------
        | Check Matrimony Payment
        |--------------------------------------------------------------------------
        */
        $matrimonyPayment = Transaction::where('user_id', $user->id)
            ->where('purpose', 'matrimony_profile')
            ->whereNotNull('razorpay_payment_id')
            ->latest()
            ->first();

        $user->has_matrimony_payment = !is_null($matrimonyPayment);

        /*
        |--------------------------------------------------------------------------
        | Check Business Payment
        |--------------------------------------------------------------------------
        */
        $businessPayment = Transaction::where('user_id', $user->id)
            ->where('purpose', 'business_registration')
            ->whereNotNull('razorpay_payment_id')
            ->latest()
            ->first();

        $user->has_business_payment = !is_null($businessPayment);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * Update user profile
     */

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'age'              => 'nullable|integer|min:18|max:100',
            'phone'            => 'required|string|max:15|unique:users,phone,' . $user->id,
            'cast_certificate' => 'nullable|string',
            'occupation'       => 'nullable|string|max:255',
            'company_name'     => 'nullable|string|max:255',
            'dept_name'        => 'nullable|string|max:255',
            'dob'              => 'nullable|date',
            'designation'      => 'nullable|string|max:255',
            'address'          => 'nullable|string|max:500',
            'nearby_location'  => 'nullable|string|max:255',
            'pincode'          => 'nullable|digits:6',
            'road_number'      => 'nullable|string|max:50',
            'state'            => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
            'sector'           => 'nullable|string|max:100',
            'district'         => 'nullable|string|max:100',
            'village'          => 'nullable|string|max:100',
            'destination'      => 'nullable|string|max:255',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
            ], 422);
        }
    
        $photoPaths = [];

        if ($request->photos) {
            foreach ($request->photos as $base64Image) {
        
                if (str_contains($base64Image, 'base64,')) {
                    $base64Image = explode('base64,', $base64Image)[1];
                }
        
                $imageData = base64_decode($base64Image);
        
                $fileName = 'profile/photos/' . uniqid() . '.jpg';
        
                Storage::disk('public')->put($fileName, $imageData);
        
                $photoPaths[] = $fileName;
            }
        }

        $data = $request->only([
            'name', 'email', 'age', 'phone', 'cast_certificate', 'occupation',
            'company_name', 'dept_name', 'dob', 'designation',
            'address', 'nearby_location', 'pincode', 'road_number',
            'state', 'city', 'sector', 'district', 'village', 'destination',
            'latitude', 'longitude'
        ]);
        
        if (!empty($photoPaths)) {
            $data['photo'] = implode(',', $photoPaths);
        }
        
        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => ['user' => $user]
        ]);
    }


    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // --- Send notification email ---
        try {
            // ✅ Secure default: do NOT include the plaintext password
            Mail::to($user->email)->send(new PasswordChangedMail($user, $request->password));

            // ❌ If you INSIST on sending the new password (not recommended), use this instead:
            // Mail::to($user->email)->send(new PasswordChangedMail($user, $request->password));
        } catch (\Throwable $mailEx) {
            \Log::warning('Password change email failed to send', [
                'user_id' => $user->id,
                'error'   => $mailEx->getMessage(),
            ]);
            // Don't fail the API response just because email failed
        }

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }
}
