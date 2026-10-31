<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    /**
     * Show admin login form
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if user is admin (you might want to add an is_admin field or role system)
            if ($user->user_type === 'admin' || $user->isAdminTeamMember()) {
                $request->session()->regenerate();

                return redirect()->intended(route($this->landingRoute($user)));
            } else {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'You do not have admin access.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function landingRoute($user): string
    {
        $modules = [
            'dashboard' => 'admin.dashboard',
            'analytics' => 'admin.analytics',
            'homepage_heroes' => 'heroes.index',
            'users' => 'admin.users.index',
            'bloggers' => 'admin.bloggers.index',
            'plans' => 'admin.plans.business.index',
            'categories' => 'admin.category.index',
            'businesses' => 'admin.businesses.index',
            'jobs' => 'admin.jobs.index',
            'matrimony' => 'admin.matrimony.index',
            'casts' => 'admin.casts.index',
            'payments' => 'admin.payments.index',
            'blogs' => 'admin.blogs.index',
            'education' => 'admin.educations.index',
            'donations' => 'admin.donations.index',
            'volunteers' => 'admin.volunteers.index',
            'reports' => 'admin.reports',
            'settings' => 'admin.settings',
        ];

        foreach ($modules as $module => $route) {
            if ($user->canAccessAdminModule($module)) {
                return $route;
            }
        }

        return 'admin.profile';
    }
}
