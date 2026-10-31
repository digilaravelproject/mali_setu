<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminTeamInvitationMail;
use App\Models\AdminTeamMember;
use App\Models\User;
use App\Support\AdminPermissionRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TeamManagementController extends Controller
{
    public function index()
    {
        $members = AdminTeamMember::with('user')
            ->where('created_by', auth()->id())
            ->latest()
            ->paginate(15);

        return view('admin.teams.index', compact('members'));
    }

    public function create()
    {
        return view('admin.teams.form', [
            'member' => new AdminTeamMember,
            'permissionGroups' => AdminPermissionRegistry::groups(),
            'selectedPermissions' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $plainPassword = preg_replace('/\s+/', '', $data['first_name']).'@123';

        $member = DB::transaction(function () use ($data, $plainPassword) {
            $user = User::create([
                'name' => $data['first_name'].' '.$data['last_name'],
                'email' => $data['email'],
                'phone' => $this->uniqueInternalPhone(),
                'password' => $plainPassword,
                'user_type' => 'general',
                'caste_verification_status' => 'approved',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            return AdminTeamMember::create([
                'user_id' => $user->id,
                'created_by' => auth()->id(),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'permissions' => $this->normalizePermissions($data['permissions']),
                'is_active' => true,
            ])->load('user');
        });

        try {
            Mail::to($member->user->email)->send(new AdminTeamInvitationMail($member, $plainPassword));
        } catch (\Throwable $exception) {
            Log::error('Admin team invitation email failed', [
                'team_member_id' => $member->id,
                'email' => $member->user->email,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('admin.teams.index')
                ->with('success', 'Team member created successfully.')
                ->with('warning', 'The invitation email could not be sent. Please check the mail configuration.');
        }

        return redirect()->route('admin.teams.index')->with('success', 'Team member created and login details emailed successfully.');
    }

    public function edit(AdminTeamMember $team)
    {
        $team = $this->ownedMember($team);

        return view('admin.teams.form', [
            'member' => $team->load('user'),
            'permissionGroups' => AdminPermissionRegistry::groups(),
            'selectedPermissions' => AdminPermissionRegistry::expandLegacyPermissions($team->permissions ?? []),
        ]);
    }

    public function update(Request $request, AdminTeamMember $team)
    {
        $team = $this->ownedMember($team);
        $data = $this->validated($request, $team);

        DB::transaction(function () use ($team, $data) {
            $team->user->update([
                'name' => $data['first_name'].' '.$data['last_name'],
                'email' => $data['email'],
            ]);
            $team->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'permissions' => $this->normalizePermissions($data['permissions']),
            ]);
        });

        return redirect()->route('admin.teams.index')->with('success', 'Team member permissions updated.');
    }

    public function toggle(AdminTeamMember $team)
    {
        $team = $this->ownedMember($team);
        $team->update(['is_active' => ! $team->is_active]);

        return back()->with('success', $team->is_active ? 'Team member activated.' : 'Team member deactivated.');
    }

    public function destroy(AdminTeamMember $team)
    {
        $team = $this->ownedMember($team);
        DB::transaction(fn () => $team->user->delete());

        return back()->with('success', 'Team member removed.');
    }

    private function validated(Request $request, ?AdminTeamMember $member = null): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($member?->user_id)],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['required', 'string', Rule::in($this->allPermissionKeys())],
        ]);
    }

    private function normalizePermissions(array $permissions): array
    {
        return AdminPermissionRegistry::withPageDependencies($permissions);
    }

    private function allPermissionKeys(): array
    {
        return AdminPermissionRegistry::keys();
    }

    private function ownedMember(AdminTeamMember $member): AdminTeamMember
    {
        abort_unless($member->created_by === auth()->id(), 404);

        return $member;
    }

    private function uniqueInternalPhone(): string
    {
        do {
            $phone = 'TEAM'.Str::upper(Str::random(16));
        } while (User::where('phone', $phone)->exists());

        return $phone;
    }
}
