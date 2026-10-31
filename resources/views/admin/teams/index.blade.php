@extends('admin.layouts.app')

@section('title', 'Teams')
@section('page-title', 'Teams')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Home</a> <span class="mx-2">›</span> Teams</div>
    <a href="{{ route('admin.teams.create') }}" class="btn btn-primary"><i class="fas fa-user-plus me-2"></i>Add Team Member</a>
</div>

<div class="card">
    <div class="card-header"><h5 class="mb-0">Admin Team Members</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>#</th><th>Team Member</th><th>Email / Username</th><th>Assigned Access</th><th>Status</th><th>Added On</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($members as $member)
                    <tr>
                        <td>{{ $members->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold">{{ $member->first_name }} {{ $member->last_name }}</td>
                        <td>{{ $member->user->email }}</td>
                        <td>
                            @php($menuLabels = \App\Support\AdminPermissionRegistry::menuLabelsFor($member->permissions ?? []))
                            @foreach(array_slice($menuLabels, 0, 3) as $menuLabel)<span class="badge bg-light text-dark border me-1 mb-1">{{ $menuLabel }}</span>@endforeach
                            @if(count($menuLabels) > 3)<span class="badge bg-secondary">+{{ count($menuLabels) - 3 }}</span>@endif
                            @if(count($menuLabels) === 0)<span class="text-muted small">No access</span>@endif
                        </td>
                        <td><span class="badge bg-{{ $member->is_active ? 'success' : 'secondary' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>{{ $member->created_at->format('d M Y') }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.teams.edit', $member) }}" class="btn btn-sm btn-primary" title="Edit permissions"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.teams.toggle', $member) }}" method="POST" class="d-inline">@csrf @method('PATCH')<button class="btn btn-sm btn-warning" title="{{ $member->is_active ? 'Deactivate' : 'Activate' }}"><i class="fas fa-{{ $member->is_active ? 'ban' : 'check' }}"></i></button></form>
                            <form action="{{ route('admin.teams.destroy', $member) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this team member and revoke their login?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" title="Remove"><i class="fas fa-trash"></i></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-5"><i class="fas fa-users text-muted fs-2 mb-3 d-block"></i><span class="text-muted">No team members added yet.</span></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $members->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
