@extends('admin.layouts.app')

@php($editing = $member->exists)
@php($selectedPermissions = old('permissions', $editing ? $member->permissions : []))
@section('title', $editing ? 'Edit Team Member' : 'Add Team Member')
@section('page-title', $editing ? 'Edit Team Member' : 'Add Team Member')

@push('styles')
<style>
    .permission-card { border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; height:100%; }
    .permission-row { padding:14px 16px; border-top:1px solid #eef0f3; }
    .permission-row:first-child { border-top:0; }
    .permission-description { font-size:.79rem; color:#6b7280; line-height:1.35; }
    .permission-switches { min-width:175px; }
</style>
@endpush

@section('content')
<div class="text-muted small mb-3"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Home</a> <span class="mx-2">›</span> <a href="{{ route('admin.teams.index') }}" class="text-decoration-none">Teams</a> <span class="mx-2">›</span> {{ $editing ? 'Edit' : 'Add' }}</div>

<form method="POST" action="{{ $editing ? route('admin.teams.update', $member) : route('admin.teams.store') }}">
    @csrf @if($editing) @method('PUT') @endif
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Team Member Details</h5></div>
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">First Name <span class="text-danger">*</span></label><input name="first_name" value="{{ old('first_name', $member->first_name) }}" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label">Last Name <span class="text-danger">*</span></label><input name="last_name" value="{{ old('last_name', $member->last_name) }}" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label">Email / Username <span class="text-danger">*</span></label><input type="email" name="email" value="{{ old('email', $member->user?->email) }}" class="form-control" required></div>
            </div>
            @unless($editing)<div class="alert alert-info mt-3 mb-0"><i class="fas fa-envelope me-2"></i>After saving, login details will be emailed automatically. The initial password will be <strong>firstname@123</strong>.</div>@endunless
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div><h5 class="mb-1">Permissions</h5><div class="text-muted small">View allows read-only pages. Manage additionally allows create, edit, approve, delete, export, refund, or settings changes described below.</div></div>
        <div><button type="button" id="selectAllPermissions" class="btn btn-sm btn-outline-primary">Select All</button> <button type="button" id="clearAllPermissions" class="btn btn-sm btn-outline-secondary">Clear All</button></div>
    </div>

    <div class="row g-3">
        @foreach($permissionGroups as $groupName => $modules)
        <div class="col-xl-6">
            <div class="permission-card bg-white">
                <div class="px-3 py-3 bg-light fw-bold"><i class="fas fa-shield-alt text-primary me-2"></i>{{ $groupName }}</div>
                @foreach($modules as $key => $permission)
                <div class="permission-row d-flex justify-content-between gap-3">
                    <div><div class="fw-semibold">{{ $permission['label'] }}</div><div class="permission-description">{{ $permission['description'] }}</div></div>
                    <div class="permission-switches">
                        <div class="form-check form-check-inline"><input class="form-check-input permission-checkbox view-checkbox" type="checkbox" name="permissions[]" value="{{ $key }}.view" id="{{ $key }}_view" @checked(in_array($key.'.view', $selectedPermissions))><label class="form-check-label small" for="{{ $key }}_view">View</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input permission-checkbox manage-checkbox" type="checkbox" name="permissions[]" value="{{ $key }}.manage" id="{{ $key }}_manage" data-view-id="{{ $key }}_view" @checked(in_array($key.'.manage', $selectedPermissions))><label class="form-check-label small" for="{{ $key }}_manage">Manage</label></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <div class="card mt-3"><div class="card-body text-end"><a href="{{ route('admin.teams.index') }}" class="btn btn-light border me-2">Cancel</a><button class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>{{ $editing ? 'Update Team Member' : 'Save & Send Email' }}</button></div></div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const permissions = document.querySelectorAll('.permission-checkbox');
    document.getElementById('selectAllPermissions').addEventListener('click', () => permissions.forEach(item => item.checked = true));
    document.getElementById('clearAllPermissions').addEventListener('click', () => permissions.forEach(item => item.checked = false));
    document.querySelectorAll('.manage-checkbox').forEach(item => item.addEventListener('change', function () {
        if (this.checked) document.getElementById(this.dataset.viewId).checked = true;
    }));
});
</script>
@endpush
