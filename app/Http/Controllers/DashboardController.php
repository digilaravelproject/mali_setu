<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Mail\PasswordChangedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    /**
     * Show User Dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user()->load([
            'casteCertificate',
            'business',
            'matrimonyProfile',
            'volunteer',
            'donations',
            'jobApplications'
        ]);

        // Copy exact payment/profile checks from API profile endpoint
        $user->is_matrimony = $user->matrimonyProfile ? true : false;
        $user->is_business = $user->business ? true : false;

        $matrimonyPayment = Transaction::where('user_id', $user->id)
            ->where('purpose', 'matrimony_profile')
            ->whereNotNull('razorpay_payment_id')
            ->latest()
            ->first();
        $user->has_matrimony_payment = !is_null($matrimonyPayment);

        $businessPayment = Transaction::where('user_id', $user->id)
            ->where('purpose', 'business_registration')
            ->whereNotNull('razorpay_payment_id')
            ->latest()
            ->first();
        $user->has_business_payment = !is_null($businessPayment);

        // Community stats for overview visual metrics
        $stats = [
            'total_users' => User::count(),
            'verified_users' => User::where('caste_verification_status', 'approved')->count(),
            'my_donations_sum' => $user->donations()->where('status', 'completed')->sum('amount') ?? 0,
            'my_applications_count' => $user->jobApplications()->count() ?? 0,
        ];

        return view('dashboard.dashboard', compact('user', 'stats'));
    }

    /**
     * Handle Profile Update Request
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone'            => 'required|string|max:15|unique:users,phone,' . $user->id,
            'age'              => 'nullable|integer|min:18|max:100',
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
            'photo'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only([
            'name', 'email', 'phone', 'age', 'occupation', 'company_name',
            'dept_name', 'dob', 'designation', 'address', 'nearby_location',
            'pincode', 'road_number', 'state', 'city', 'sector', 'district',
            'village', 'destination'
        ]);

        // File upload handling for user photo (Premium single/multiple support)
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if (!empty($user->photo)) {
                foreach (explode(',', $user->photo) as $p) {
                    $p = trim($p);
                    if ($p && Storage::disk('public')->exists($p)) {
                        Storage::disk('public')->delete($p);
                    }
                }
            }

            $file = $request->file('photo');
            $fileName = 'profile/photos/' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Ensure folders exist
            if (!Storage::disk('public')->exists('profile/photos')) {
                Storage::disk('public')->makeDirectory('profile/photos');
            }

            Storage::disk('public')->put($fileName, file_get_contents($file));
            $data['photo'] = $fileName;
        }

        $user->update($data);

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
    }

    /**
     * Handle Password Change Request
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Try sending notification mail (do not crash on mail failure)
        try {
            Mail::to($user->email)->send(new PasswordChangedMail($user, $request->password));
        } catch (\Throwable $mailEx) {
            \Log::warning('Web password change email failed', [
                'user_id' => $user->id,
                'error'   => $mailEx->getMessage()
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Password updated successfully!');
    }

    /**
     * Handle Account Deletion Request
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        try {
            // Delete associated files
            if ($user->cast_certificate && Storage::disk('public')->exists($user->cast_certificate)) {
                Storage::disk('public')->delete($user->cast_certificate);
            }

            if (!empty($user->photo)) {
                foreach (explode(',', $user->photo) as $p) {
                    $p = trim($p);
                    if ($p && Storage::disk('public')->exists($p)) {
                        Storage::disk('public')->delete($p);
                    }
                }
            }

            // Revoke tokens and delete user
            $user->tokens()->delete();
            $user->delete();

            // Log out and invalidate session
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Your account has been successfully deleted.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete account. ' . $e->getMessage()]);
        }
    }
}
