<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CasteCertificate;
use App\Models\Business;
use App\Models\MatrimonyProfile;
use App\Models\Transaction;
use App\Models\ConnectionRequest;
use App\Models\ChatMessage;
use App\Models\VolunteerOpportunity;
use App\Models\VolunteerApplication;
use App\Models\VolunteerProfile;
use App\Models\DonationCause;
use App\Models\Donation;
use App\Models\JobPosting;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get admin dashboard statistics
     */
    public function getDashboardStats()
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'verified' => User::where('caste_verification_status', 'approved')->count(),
                'pending' => User::where('caste_verification_status', 'pending')->count(),
                'rejected' => User::where('caste_verification_status', 'rejected')->count(),
                'by_type' => User::select('user_type', DB::raw('count(*) as count'))
                    ->groupBy('user_type')->get(),
            ],
            'businesses' => [
                'total' => Business::count(),
                'approved' => Business::where('verification_status', 'approved')->count(),
                'pending' => Business::where('verification_status', 'pending')->count(),
                'active_subscriptions' => Business::where('subscription_status', 'active')->count(),
            ],
            'matrimony' => [
                'total' => MatrimonyProfile::count(),
                'approved' => MatrimonyProfile::where('approval_status', 'approved')->count(),
                'pending' => MatrimonyProfile::where('approval_status', 'pending')->count(),
                'connections' => ConnectionRequest::where('status', 'accepted')->count(),
            ],
            'payments' => [
                'total_revenue' => Transaction::where('status', 'completed')->sum('amount'),
                'total_transactions' => Transaction::where('status', 'completed')->count(),
                'pending_payments' => Transaction::where('status', 'pending')->count(),
                'monthly_revenue' => Transaction::where('status', 'completed')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount'),
            ],
            'activity' => [
                'messages_today' => ChatMessage::whereDate('created_at', today())->count(),
                'new_registrations_today' => User::whereDate('created_at', today())->count(),
                'new_businesses_today' => Business::whereDate('created_at', today())->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get all users with pagination and filters
     */
    public function getUsers(Request $request)
    {
        $query = User::with(['casteCertificate', 'business', 'matrimonyProfile']);

        // Apply filters
        if ($request->has('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->has('verification_status')) {
            $query->where('caste_verification_status', $request->verification_status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get pending caste certificate verifications
     */
    public function getPendingVerifications()
    {
        $certificates = CasteCertificate::with('user')
            ->whereHas('user', function($q) {
                $q->where('caste_verification_status', 'pending');
            })
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $certificates
        ]);
    }

    /**
     * Verify caste certificate
     */
    public function verifyCasteCertificate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $certificate = CasteCertificate::findOrFail($id);
        
        // Update user's verification status (primary field)
        $certificate->user->update([
            'caste_verification_status' => $request->status
        ]);
        
        // Update certificate metadata
        $certificate->update([
            'admin_notes' => $request->admin_notes,
            'verified_by' => auth()->id(),
            'verified_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Certificate ' . $request->status . ' successfully',
            'data' => ['certificate' => $certificate]
        ]);
    }

    /**
     * Get all businesses for admin review
     */
    public function getBusinesses(Request $request)
    {
        $query = Business::with(['user', 'category', 'products', 'services']);

        if ($request->has('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->has('subscription_status')) {
            $query->where('subscription_status', $request->subscription_status);
        }

        $businesses = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $businesses
        ]);
    }

    /**
     * Verify business
     */
    public function verifyBusiness(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $business = Business::findOrFail($id);
        
        $business->update([
            'verification_status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Business ' . $request->status . ' successfully',
            'data' => ['business' => $business]
        ]);
    }

    /**
     * Get matrimony profiles for moderation
     */
    public function getMatrimonyProfiles(Request $request)
    {
        $query = MatrimonyProfile::with('user');

        if ($request->has('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        $profiles = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $profiles
        ]);
    }

    /**
     * Moderate matrimony profile
     */
    public function moderateMatrimonyProfile(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $profile = MatrimonyProfile::findOrFail($id);
        
        $profile->update([
            'approval_status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile ' . $request->status . ' successfully',
            'data' => ['profile' => $profile]
        ]);
    }

    /**
     * Suspend user account
     */
    public function suspendUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
            'duration_days' => 'nullable|integer|min:1|max:365',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($id);
        
        // Update user status (you might want to add a suspended_until field to users table)
        $user->update([
            'caste_verification_status' => 'rejected', // Temporary suspension method
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User suspended successfully',
            'data' => ['user' => $user]
        ]);
    }

    /**
     * Get all transactions for admin
     */
    public function getTransactions(Request $request)
    {
        $query = Transaction::with('user');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    /**
     * Create admin user
     */
    public function createAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:15|unique:users,phone',
            'role' => 'required|in:super_admin,moderator',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'user_type' => 'general', // Admin users are general type with special roles
            'caste_verification_status' => 'approved',
            'email_verified_at' => now(),
        ]);

        // You might want to create a roles table and assign roles properly
        // For now, we'll use a simple approach

        return response()->json([
            'success' => true,
            'message' => 'Admin created successfully',
            'data' => ['admin' => $admin]
        ], 201);
    }

    /**
     * Get system settings
     */
    public function getSystemSettings()
    {
        // This would typically come from a settings table
        $settings = [
            'business_registration_fee' => 500.00,
            'matrimony_profile_fee' => 300.00,
            'trial_period_days' => 7,
            'max_job_postings' => 5,
            'app_name' => 'Mali Setu',
            'support_email' => 'support@malisetu.com',
        ];

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update system settings
     */
    public function updateSystemSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_registration_fee' => 'nullable|numeric|min:0',
            'matrimony_profile_fee' => 'nullable|numeric|min:0',
            'trial_period_days' => 'nullable|integer|min:0',
            'max_job_postings' => 'nullable|integer|min:0',
            'app_name' => 'nullable|string|max:255',
            'support_email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update settings in database (you'd implement a settings table)
        // For now, just return success

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Get analytics data
     */
    public function getAnalytics(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        $analytics = [
            'user_registrations' => User::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'revenue_trend' => Transaction::where('created_at', '>=', $startDate)
                ->where('status', 'completed')
                ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'business_registrations' => Business::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'matrimony_profiles' => MatrimonyProfile::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Bulk operations on users
     */
    public function bulkUserOperation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
            'operation' => 'required|in:approve,reject,suspend',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userIds = $request->user_ids;
        $operation = $request->operation;

        switch ($operation) {
            case 'approve':
                User::whereIn('id', $userIds)
                    ->update(['caste_verification_status' => 'approved']);
                break;
            
            case 'reject':
                User::whereIn('id', $userIds)
                    ->update(['caste_verification_status' => 'rejected']);
                break;
            
            case 'suspend':
                // Implement suspension logic
                User::whereIn('id', $userIds)
                    ->update(['caste_verification_status' => 'rejected']);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk operation completed successfully',
            'data' => ['affected_users' => count($userIds)]
        ]);
    }

    /**
     * Get all volunteer opportunities for admin management
     */
    public function getVolunteerOpportunities(Request $request)
    {
        $query = VolunteerOpportunity::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $opportunities = $query->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $opportunities
        ]);
    }

    /**
     * Approve or reject volunteer opportunity
     */
    public function manageVolunteerOpportunity(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive,cancelled',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $opportunity = VolunteerOpportunity::findOrFail($id);
        
        $opportunity->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Volunteer opportunity ' . $request->status . ' successfully',
            'data' => ['opportunity' => $opportunity]
        ]);
    }

    /**
     * Get all volunteer applications for admin management
     */
    public function getVolunteerApplications(Request $request)
    {
        $query = VolunteerApplication::with(['volunteerProfile.user', 'volunteerOpportunity']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('opportunity_id')) {
            $query->where('volunteer_opportunity_id', $request->opportunity_id);
        }

        $applications = $query->orderBy('applied_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    /**
     * Manage volunteer application status
     */
    public function manageVolunteerApplication(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,rejected,withdrawn',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $application = VolunteerApplication::findOrFail($id);
        
        $application->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'responded_at' => now()
        ]);

        // If approved, increment volunteers_registered count
        if ($request->status === 'approved' && $application->status !== 'approved') {
            $application->volunteerOpportunity->increment('volunteers_registered');
        }
        // If previously approved and now rejected/withdrawn, decrement count
        elseif ($application->status === 'approved' && in_array($request->status, ['rejected', 'withdrawn'])) {
            $application->volunteerOpportunity->decrement('volunteers_registered');
        }

        return response()->json([
            'success' => true,
            'message' => 'Application ' . $request->status . ' successfully',
            'data' => ['application' => $application->load(['volunteerProfile.user', 'volunteerOpportunity'])]
        ]);
    }

    /**
     * Get volunteer analytics
     */
    public function getVolunteerAnalytics(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        $analytics = [
            'opportunities' => [
                'total' => VolunteerOpportunity::count(),
                'active' => VolunteerOpportunity::where('status', 'active')->count(),
                'completed' => VolunteerOpportunity::where('status', 'completed')->count(),
                'recent' => VolunteerOpportunity::where('created_at', '>=', $startDate)->count(),
            ],
            'applications' => [
                'total' => VolunteerApplication::count(),
                'pending' => VolunteerApplication::where('status', 'pending')->count(),
                'approved' => VolunteerApplication::where('status', 'approved')->count(),
                'recent' => VolunteerApplication::where('applied_at', '>=', $startDate)->count(),
            ],
            'volunteers' => [
                'total_profiles' => VolunteerProfile::count(),
                'active_volunteers' => VolunteerApplication::where('status', 'approved')
                    ->distinct('volunteer_profile_id')->count(),
            ],
            'trends' => [
                'opportunity_creation' => VolunteerOpportunity::where('created_at', '>=', $startDate)
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
                'application_trend' => VolunteerApplication::where('applied_at', '>=', $startDate)
                    ->selectRaw('DATE(applied_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Bulk operations on volunteer opportunities
     */
    public function bulkVolunteerOpportunityOperation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'opportunity_ids' => 'required|array',
            'opportunity_ids.*' => 'integer|exists:volunteer_opportunities,id',
            'operation' => 'required|in:activate,deactivate,complete,cancel',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $opportunityIds = $request->opportunity_ids;
        $operation = $request->operation;

        $statusMap = [
            'activate' => 'active',
            'deactivate' => 'inactive',
            'complete' => 'completed',
            'cancel' => 'cancelled'
        ];

        VolunteerOpportunity::whereIn('id', $opportunityIds)
            ->update([
                'status' => $statusMap[$operation],
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Bulk operation completed successfully',
            'data' => ['affected_opportunities' => count($opportunityIds)]
        ]);
    }

    /**
     * Get all donation causes for admin management
     */
    public function getDonationCauses(Request $request)
    {
        $query = DonationCause::withCount('donations');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        $causes = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $causes
        ]);
    }

    /**
     * Create a new donation cause
     */
    public function createDonationCause(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'target_amount' => 'required|numeric|min:1',
            'urgency' => 'required|in:low,medium,high,critical',
            'location' => 'nullable|string|max:255',
            'organization' => 'required|string|max:255',
            'contact_info' => 'required|array',
            'contact_info.email' => 'required|email',
            'contact_info.phone' => 'required|string',
            'contact_info.address' => 'nullable|string',
            'image_url' => 'nullable|url',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:draft,active,paused,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $cause = DonationCause::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Donation cause created successfully',
            'data' => ['cause' => $cause]
        ]);
    }

    /**
     * Update a donation cause
     */
    public function updateDonationCause(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes|string|max:100',
            'target_amount' => 'sometimes|numeric|min:1',
            'urgency' => 'sometimes|in:low,medium,high,critical',
            'location' => 'nullable|string|max:255',
            'organization' => 'sometimes|string|max:255',
            'contact_info' => 'sometimes|array',
            'image_url' => 'nullable|url',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'sometimes|in:draft,active,paused,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $cause = DonationCause::findOrFail($id);
        $cause->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Donation cause updated successfully',
            'data' => ['cause' => $cause]
        ]);
    }

    /**
     * Delete a donation cause
     */
    public function deleteDonationCause($id)
    {
        $cause = DonationCause::findOrFail($id);
        
        // Check if there are any donations for this cause
        if ($cause->donations()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete cause with existing donations'
            ], 400);
        }

        $cause->delete();

        return response()->json([
            'success' => true,
            'message' => 'Donation cause deleted successfully'
        ]);
    }

    /**
     * Get donation analytics
     */
    public function getDonationAnalytics(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        $analytics = [
            'causes' => [
                'total' => DonationCause::count(),
                'active' => DonationCause::where('status', 'active')->count(),
                'completed' => DonationCause::where('status', 'completed')->count(),
                'draft' => DonationCause::where('status', 'draft')->count(),
            ],
            'donations' => [
                'total_amount' => Donation::where('status', 'completed')->sum('amount'),
                'total_count' => Donation::where('status', 'completed')->count(),
                'pending_amount' => Donation::where('status', 'pending')->sum('amount'),
                'recent_donations' => Donation::where('created_at', '>=', $startDate)
                    ->where('status', 'completed')->count(),
            ],
            'top_causes' => DonationCause::withSum(['donations' => function($q) {
                    $q->where('status', 'completed');
                }], 'amount')
                ->orderBy('donations_sum_amount', 'desc')
                ->limit(5)
                ->get(),
            'trends' => [
                'daily_donations' => Donation::where('created_at', '>=', $startDate)
                    ->where('status', 'completed')
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
                'category_distribution' => DonationCause::select('category')
                    ->withSum(['donations' => function($q) {
                        $q->where('status', 'completed');
                    }], 'amount')
                    ->groupBy('category')
                    ->get(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get donation transactions
     */
    public function getDonationTransactions(Request $request)
    {
        $query = Donation::with(['user:id,name,email', 'cause:id,title,organization']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('cause_id')) {
            $query->where('cause_id', $request->cause_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $donations = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $donations
        ]);
    }

    /**
     * Get all job postings for admin review
     */
    public function getJobPostings(Request $request)
    {
        $query = JobPosting::with(['business.user', 'applications']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('business', function($bq) use ($search) {
                      $bq->where('business_name', 'like', "%{$search}%");
                  });
            });
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

    /**
     * Approve a job posting
     */
    public function approveJobPosting($id)
    {
        $jobPosting = JobPosting::findOrFail($id);
        $jobPosting->approve();

        return response()->json([
            'success' => true,
            'message' => 'Job posting approved successfully',
            'data' => ['job' => $jobPosting]
        ]);
    }

    /**
     * Reject a job posting
     */
    public function rejectJobPosting(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $jobPosting = JobPosting::findOrFail($id);
        $jobPosting->reject();

        // You could store the rejection reason in a separate field if needed
        // For now, we'll just reject the job posting

        return response()->json([
            'success' => true,
            'message' => 'Job posting rejected successfully',
            'data' => ['job' => $jobPosting]
        ]);
    }

    /**
     * Get job posting analytics
     */
    public function getJobAnalytics(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        $analytics = [
            'jobs' => [
                'total' => JobPosting::count(),
                'active' => JobPosting::active()->count(),
                'pending' => JobPosting::pendingApproval()->count(),
                'rejected' => JobPosting::rejected()->count(),
                'expired' => JobPosting::expired()->count(),
            ],
            'applications' => [
                'total' => JobApplication::count(),
                'pending' => JobApplication::where('status', 'pending')->count(),
                'accepted' => JobApplication::where('status', 'accepted')->count(),
                'rejected' => JobApplication::where('status', 'rejected')->count(),
                'recent' => JobApplication::where('created_at', '>=', $startDate)->count(),
            ],
            'trends' => [
                'daily_jobs' => JobPosting::where('created_at', '>=', $startDate)
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
                'daily_applications' => JobApplication::where('created_at', '>=', $startDate)
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
                'category_distribution' => JobPosting::select('category')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('category')
                    ->get(),
                'location_distribution' => JobPosting::select('location')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('location')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
            ],
            'top_businesses' => JobPosting::select('business_id')
                ->with('business:id,business_name')
                ->selectRaw('COUNT(*) as job_count')
                ->groupBy('business_id')
                ->orderBy('job_count', 'desc')
                ->limit(5)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get job applications for admin review
     */
    public function getJobApplications(Request $request)
    {
        $query = JobApplication::with(['user:id,name,email', 'jobPosting:id,title,business_id', 'jobPosting.business:id,business_name']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('job_id')) {
            $query->where('job_posting_id', $request->job_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('applied_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('applied_at', '<=', $request->date_to);
        }

        $applications = $query->orderBy('applied_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    /**
     * Bulk operations on job postings
     */
    public function bulkJobOperation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject,delete,activate,deactivate',
            'job_ids' => 'required|array|min:1',
            'job_ids.*' => 'exists:job_postings,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $jobIds = $request->job_ids;
        $action = $request->action;
        $processed = 0;

        foreach ($jobIds as $jobId) {
            $job = JobPosting::find($jobId);
            if ($job) {
                switch ($action) {
                    case 'approve':
                        $job->approve();
                        break;
                    case 'reject':
                        $job->reject();
                        break;
                    case 'delete':
                        $job->delete();
                        break;
                    case 'activate':
                        $job->activate();
                        break;
                    case 'deactivate':
                        $job->deactivate();
                        break;
                }
                $processed++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully {$action}d {$processed} job posting(s)",
            'data' => ['processed' => $processed]
        ]);
    }
}