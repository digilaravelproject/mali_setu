<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReferralReportExport;
use App\Http\Controllers\Controller;
use App\Mail\VolunteerCreatedMail;
use App\Models\District;
use App\Models\Locality;
use App\Models\Referral;
use App\Models\Taluka;
use App\Models\User;
use App\Models\VolunteerProfile;
use App\Services\NotificationService;
use App\Services\ReferralService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class VolunteerManagementController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    /**
     * Display all volunteers
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->whereIn('user_type', ['general', 'matrimony', 'business', 'volunteer'])
            ->with(['volunteer', 'volunteerReferralProfile.district', 'volunteerReferralProfile.taluka', 'volunteerReferralProfile.locality'])
            ->withCount('referralsMade');

        if ($request->get('user_type') === 'volunteer') {
            $query->whereHas('volunteer');
        } elseif ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }
        if ($request->filled('status')) {
            $status = $request->status;
            $query->where(fn ($q) => $q
                ->whereHas('volunteer', fn ($volunteer) => $volunteer->where('status', $status))
                ->orWhere(fn ($withoutVolunteer) => $withoutVolunteer->whereDoesntHave('volunteer')->where('status', $status)));
        }
        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('reffral_code', 'like', "%{$term}%"));
        }
        foreach (['district_id', 'taluka_id', 'locality_id'] as $field) {
            if ($request->filled($field)) {
                $query->whereHas('volunteerReferralProfile', fn ($q) => $q->where($field, $request->integer($field)));
            }
        }

        match ($request->get('registered_sort')) {
            'low_high' => $query->orderBy('referrals_made_count'),
            'high_low' => $query->orderByDesc('referrals_made_count'),
            default => $query->latest(),
        };

        $volunteers = $query->paginate(25)->withQueryString();
        $counts = ['all' => User::whereIn('user_type', ['general', 'matrimony', 'business', 'volunteer'])->count()];
        foreach (['general', 'matrimony', 'business'] as $type) {
            $counts[$type] = User::where('user_type', $type)->count();
        }
        $counts['volunteer'] = User::whereHas('volunteer')->count();

        return view('admin.volunteers.index', [
            'volunteers' => $volunteers,
            'counts' => $counts,
            'districts' => District::where('is_active', true)->orderBy('name')->get(),
            'talukas' => Taluka::where('is_active', true)->orderBy('name')->get(),
            'localities' => Locality::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.volunteers.create');
    }

    public function store(Request $request, ReferralService $referrals)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255', 'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone', 'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive', 'commission_points' => 'required|integer|min:0|max:100',
            'skills' => 'nullable|string|max:255', 'availability' => 'nullable|string|max:255',
        ]);

        $user = DB::transaction(function () use ($data, $referrals) {
            $user = User::create([
                'name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'],
                'password' => Hash::make($data['password']), 'user_type' => 'volunteer',
                'status' => $data['status'], 'caste_verification_status' => 'approved',
            ]);
            VolunteerProfile::create([
                'user_id' => $user->id, 'skills' => $data['skills'] ?? null,
                'availability' => $data['availability'] ?? null, 'status' => $data['status'],
            ]);
            $referrals->activateVolunteer($user)->update(['commission_points' => $data['commission_points'], 'is_active' => $data['status'] === 'active']);

            return $user;
        });

        try {
            Mail::to($user->email)->send(new VolunteerCreatedMail($user, $data['password'], (int) $data['commission_points']));
        } catch (\Throwable $e) {
            Log::warning('Volunteer welcome email failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('admin.volunteers.index', ['user_type' => 'volunteer'])->with('success', 'Volunteer added successfully.');
    }

    public function toggle(User $user)
    {
        abort_unless(in_array($user->user_type, ['general', 'matrimony', 'business', 'volunteer'], true), 404);
        $volunteer = $user->volunteer;
        $currentStatus = $volunteer?->status ?? $user->status;
        $active = $currentStatus !== 'active';
        if ($volunteer) {
            $volunteer->update(['status' => $active ? 'active' : 'inactive']);
            $user->volunteerReferralProfile?->update(['is_active' => $active]);
        }
        if (! $volunteer || $user->user_type === 'volunteer') {
            $user->update(['status' => $active ? 'active' : 'inactive']);
        }

        return back()->with('success', 'Status updated successfully.');
    }

    public function updateCommission(Request $request, User $user, ReferralService $referrals)
    {
        $data = $request->validate(['commission_points' => 'required|integer|min:0|max:100']);
        $referrals->activateVolunteer($user)->update($data);

        return back()->with('success', 'Commission points updated.');
    }

    public function allocateLocation(Request $request, User $user, ReferralService $referrals)
    {
        $data = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'taluka_id' => 'nullable|exists:talukas,id',
            'locality_id' => 'nullable|exists:localities,id',
        ]);
        if (! empty($data['taluka_id'])) {
            abort_unless(Taluka::whereKey($data['taluka_id'])->where('district_id', $data['district_id'])->exists(), 422, 'Taluka does not belong to the district.');
        }
        if (! empty($data['locality_id'])) {
            abort_unless(! empty($data['taluka_id']) && Locality::whereKey($data['locality_id'])->where('taluka_id', $data['taluka_id'])->exists(), 422, 'City/village does not belong to the taluka.');
        }
        $referrals->activateVolunteer($user)->update($data);

        return back()->with('success', 'Volunteer location allocated.');
    }

    public function registeredUsers(User $user)
    {
        $referrals = $user->referralsMade()->with('referredUser')->latest()->paginate(25);

        return view('admin.volunteers.registered-users', compact('user', 'referrals'));
    }

    public function destroyUser(User $user)
    {
        abort_unless(in_array($user->user_type, ['general', 'matrimony', 'business', 'volunteer'], true), 404);
        $user->delete();

        return redirect()->route('admin.volunteers.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Display pending volunteers for verification
     */
    public function verification(Request $request)
    {
        $query = VolunteerProfile::with('user')
            ->where('status', 'pending');

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $query->where(function ($q) use ($request) {
                $q->where('location', 'like', '%'.$request->search.'%')
                    ->orWhere('skills', 'like', '%'.$request->search.'%')
                    ->orWhereHas('user', function ($userQuery) use ($request) {
                        $userQuery->where('name', 'like', '%'.$request->search.'%')
                            ->orWhere('email', 'like', '%'.$request->search.'%');
                    });
            });
        }

        $pendingVolunteers = $query->latest()->paginate(15);

        $stats = [
            'pending_count' => VolunteerProfile::where('status', 'pending')->count(),
            'approved_today' => VolunteerProfile::where('status', 'active')
                ->whereDate('updated_at', today())->count(),
            'rejected_today' => VolunteerProfile::where('status', 'inactive')
                ->whereDate('updated_at', today())->count(),
        ];

        return view('admin.volunteers.verification', compact('pendingVolunteers', 'stats'));
    }

    /**
     * Display pending volunteers (alias for verification)
     */
    public function pending(Request $request)
    {
        return $this->verification($request);
    }

    /**
     * Approve volunteer
     */
    public function approve(Request $request, $volunteerId)
    {
        try {
            $volunteer = VolunteerProfile::findOrFail($volunteerId);

            $volunteer->update([
                'status' => 'active',
            ]);
            $volunteer->user?->volunteerReferralProfile?->update(['is_active' => true]);
            if ($volunteer->user?->user_type === 'volunteer') {
                $volunteer->user->update(['status' => 'active']);
            }

            try {
                $this->notifications->notifyVolunteerApproved($volunteer->fresh());
            } catch (\Throwable $notificationError) {
                Log::error('Volunteer approved but its notification failed', [
                    'volunteer_id' => $volunteer->id,
                    'user_id' => $volunteer->user_id,
                    'error' => $notificationError->getMessage(),
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Volunteer approved successfully!',
                ]);
            }

            return redirect()->back()->with('success', 'Volunteer approved successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve volunteer: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to approve volunteer.');
        }
    }

    /**
     * Reject volunteer
     */
    public function reject(Request $request, $volunteerId)
    {
        try {
            \Log::info('Volunteer rejection request', [
                'volunteer_id' => $volunteerId,
                'request_data' => $request->all(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            $volunteer = VolunteerProfile::findOrFail($volunteerId);

            \Log::info('Volunteer found for rejection', [
                'volunteer_id' => $volunteerId,
                'current_status' => $volunteer->status,
            ]);

            $volunteer->update([
                'status' => 'inactive',
                'rejection_reason' => $request->rejection_reason,
            ]);
            $volunteer->user?->volunteerReferralProfile?->update(['is_active' => false]);
            if ($volunteer->user?->user_type === 'volunteer') {
                $volunteer->user->update(['status' => 'inactive']);
            }

            try {
                $this->notifications->notifyVolunteerRejected(
                    $volunteer->fresh(),
                    $request->rejection_reason
                );
            } catch (\Throwable $notificationError) {
                Log::error('Volunteer rejected but its notification failed', [
                    'volunteer_id' => $volunteer->id,
                    'user_id' => $volunteer->user_id,
                    'error' => $notificationError->getMessage(),
                ]);
            }

            \Log::info('Volunteer rejected successfully', [
                'volunteer_id' => $volunteerId,
                'rejection_reason' => $request->rejection_reason,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Volunteer rejected successfully!',
                ]);
            }

            return redirect()->back()->with('success', 'Volunteer rejected successfully!');
        } catch (ValidationException $e) {
            \Log::error('Volunteer rejection validation failed', [
                'volunteer_id' => $volunteerId,
                'validation_errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: '.implode(', ', $e->validator->errors()->all()),
                ], 422);
            }

            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to reject volunteer', [
                'volunteer_id' => $volunteerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject volunteer: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to reject volunteer: '.$e->getMessage());
        }
    }

    /**
     * View volunteer details
     */
    public function show($id)
    {
        $volunteer = VolunteerProfile::with('user')->findOrFail($id);

        return view('admin.volunteers.show', compact('volunteer'));
    }

    /**
     * Delete volunteer
     */
    public function destroy(Request $request, $id)
    {
        try {
            $volunteer = VolunteerProfile::findOrFail($id);

            \Log::info('Deleting volunteer', [
                'volunteer_id' => $id,
                'user_id' => $volunteer->user_id,
            ]);

            $volunteer->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Volunteer deleted successfully!',
                ]);
            }

            return redirect()->route('admin.volunteers.index')->with('success', 'Volunteer deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to delete volunteer', [
                'volunteer_id' => $id,
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete volunteer: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete volunteer.');
        }
    }

    public function referralReports(Request $request)
    {
        $query = $this->referralReportQuery($request);
        $summaryQuery = clone $query;
        $reports = $query->latest()->paginate(25)->withQueryString();

        return view('admin.volunteers.referral-reports', [
            'reports' => $reports,
            'totalReferrals' => (clone $summaryQuery)->count(),
            'activeReferrals' => (clone $summaryQuery)->where('referrals.status', 'active')->count(),
            'totalCommission' => (clone $summaryQuery)->sum('referrals.commission_points'),
            'volunteerUsers' => User::whereHas('volunteerReferralProfile')->orderBy('name')->get(['id', 'name', 'reffral_code']),
            'districts' => District::orderBy('name')->get(),
            'talukas' => Taluka::orderBy('name')->get(),
            'localities' => Locality::orderBy('name')->get(),
        ]);
    }

    public function exportReferralExcel(Request $request)
    {
        return Excel::download(new ReferralReportExport($this->referralReportQuery($request)->latest()->get()), 'referral-report-'.now()->format('Y-m-d').'.xlsx');
    }

    public function exportReferralPdf(Request $request)
    {
        $reports = $this->referralReportQuery($request)->latest()->get();

        return Pdf::loadView('admin.volunteers.referral-report-pdf', compact('reports'))
            ->setPaper('a4', 'landscape')->download('referral-report-'.now()->format('Y-m-d').'.pdf');
    }

    private function referralReportQuery(Request $request)
    {
        $query = Referral::query()->with([
            'referrer.volunteerReferralProfile.district', 'referrer.volunteerReferralProfile.taluka',
            'referrer.volunteerReferralProfile.locality', 'referredUser',
        ]);
        if ($request->filled('volunteer_id')) {
            $query->where('referrer_user_id', $request->integer('volunteer_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }
        if ($request->filled('user_type')) {
            $query->whereHas('referredUser', fn ($q) => $q->where('user_type', $request->user_type));
        }
        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(fn ($q) => $q->where('referral_code_used', 'like', "%{$term}%")
                ->orWhereHas('referrer', fn ($u) => $u->where('name', 'like', "%{$term}%")->orWhere('reffral_code', 'like', "%{$term}%"))
                ->orWhereHas('referredUser', fn ($u) => $u->where('name', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%")));
        }
        foreach (['district_id', 'taluka_id', 'locality_id'] as $field) {
            if ($request->filled($field)) {
                $query->whereHas('referrer.volunteerReferralProfile', fn ($q) => $q->where($field, $request->integer($field)));
            }
        }

        return $query;
    }
}
