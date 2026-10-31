@extends('admin.layouts.app')

@php($editing = $member->exists)
@php($selectedPermissions = old('permissions', $selectedPermissions))
@section('title', $editing ? 'Edit Team Member' : 'Add Team Member')
@section('page-title', $editing ? 'Edit Team Member' : 'Add Team Member')

@push('styles')
<style>
    .permission-group-title { color:#84144f; font-size:.85rem; font-weight:800; letter-spacing:.06em; text-transform:uppercase; margin:24px 0 10px; }
    .permission-menu { border:1px solid #e2e8f0; border-radius:12px; background:#fff; margin-bottom:12px; overflow:hidden; }
    .permission-menu-header { display:flex; align-items:center; gap:12px; padding:14px 16px; background:#f8fafc; cursor:pointer; }
    .permission-menu-header:hover { background:#f3f4f6; }
    .permission-menu-icon { width:36px; height:36px; border-radius:9px; background:#f9e8f1; color:#84144f; display:flex; align-items:center; justify-content:center; }
    .permission-menu-toggle { margin-left:auto; transition:transform .2s ease; }
    .permission-menu-header[aria-expanded="true"] .permission-menu-toggle { transform:rotate(180deg); }
    .permission-section { border-top:1px solid #edf0f3; }
    .permission-section-title { background:#fff7fb; color:#6d123f; font-weight:700; padding:10px 18px; font-size:.9rem; }
    .permission-action { display:grid; grid-template-columns:28px minmax(180px, .8fr) 1.5fr; align-items:start; gap:8px; padding:11px 18px; border-top:1px solid #f1f3f5; }
    .permission-action:first-of-type { border-top:0; }
    .permission-action:hover { background:#fcfcfd; }
    .permission-description { font-size:.8rem; color:#6b7280; line-height:1.4; }
    .permission-kind { font-size:.66rem; padding:3px 7px; border-radius:10px; background:#eef2ff; color:#4f46e5; margin-left:6px; }
    @media(max-width:767px) { .permission-action { grid-template-columns:28px 1fr; } .permission-description { grid-column:2; } }
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
        <div><h5 class="mb-1">Menu, Submenu &amp; Action Permissions</h5><div class="text-muted small">Select each page and action this member may see and use. Menu visibility is calculated from the selected permissions.</div></div>
        <div><button type="button" id="selectAllPermissions" class="btn btn-sm btn-outline-primary">Select All</button> <button type="button" id="clearAllPermissions" class="btn btn-sm btn-outline-secondary">Clear All</button></div>
    </div>

    <div class="card"><div class="card-body p-3 p-md-4">
        @foreach($permissionGroups as $groupName => $menus)
            <div class="permission-group-title">{{ $groupName }}</div>
            @foreach($menus as $module => $menu)
                @php($menuPermissionKeys = collect($menu['sections'])->flatten(1)->pluck('key')->all())
                @php($menuSelected = count(array_intersect($menuPermissionKeys, $selectedPermissions)) > 0)
                <div class="permission-menu">
                    <div class="permission-menu-header" data-bs-toggle="collapse" data-bs-target="#permission_{{ $module }}" aria-expanded="{{ $menuSelected ? 'true' : 'false' }}">
                        <div class="form-check m-0" onclick="event.stopPropagation()"><input type="checkbox" class="form-check-input menu-select" data-module="{{ $module }}" title="Select all {{ $menu['label'] }} permissions"></div>
                        <div class="permission-menu-icon"><i class="fas fa-{{ $menu['icon'] }}"></i></div>
                        <div><div class="fw-bold">Menu: {{ $menu['label'] }}</div><div class="small text-muted"><span class="selected-count" data-module="{{ $module }}">{{ count(array_intersect($menuPermissionKeys, $selectedPermissions)) }}</span> of {{ count($menuPermissionKeys) }} permissions selected</div></div>
                        <i class="fas fa-chevron-down permission-menu-toggle"></i>
                    </div>
                    <div id="permission_{{ $module }}" class="collapse {{ $menuSelected ? 'show' : '' }}">
                        @foreach($menu['sections'] as $sectionName => $actions)
                            <div class="permission-section">
                                <div class="permission-section-title d-flex align-items-center"><span>Submenu/Page: {{ $sectionName }}</span><button type="button" class="btn btn-link btn-sm ms-auto py-0 section-select" data-section="{{ Str::slug($module.'_'.$sectionName, '_') }}">Select submenu</button></div>
                                @foreach($actions as $action)
                                    @php($fieldId = 'permission_'.str_replace('.', '_', $action['key']))
                                    <label class="permission-action" for="{{ $fieldId }}">
                                        <input class="form-check-input permission-checkbox module-{{ $module }} section-{{ Str::slug($module.'_'.$sectionName, '_') }}" type="checkbox" name="permissions[]" value="{{ $action['key'] }}" id="{{ $fieldId }}" @checked(in_array($action['key'], $selectedPermissions))>
                                        <div class="fw-semibold small">{{ $action['label'] }}<span class="permission-kind">{{ $action['level'] === 'view' ? 'PAGE' : 'ACTION' }}</span></div>
                                        <div class="permission-description">{{ $action['description'] }}</div>
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endforeach
    </div></div>

    <div class="card mt-3"><div class="card-body text-end"><a href="{{ route('admin.teams.index') }}" class="btn btn-light border me-2">Cancel</a><button class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>{{ $editing ? 'Update Team Member' : 'Save & Send Email' }}</button></div></div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const permissions = document.querySelectorAll('.permission-checkbox');
    const refreshCounts = () => document.querySelectorAll('.menu-select').forEach(menu => {
        const moduleItems = [...document.querySelectorAll('.module-' + menu.dataset.module)];
        const selected = moduleItems.filter(item => item.checked).length;
        menu.checked = selected === moduleItems.length && selected > 0;
        menu.indeterminate = selected > 0 && selected < moduleItems.length;
        document.querySelector('.selected-count[data-module="' + menu.dataset.module + '"]').textContent = selected;
    });
    document.getElementById('selectAllPermissions').addEventListener('click', () => { permissions.forEach(item => item.checked = true); refreshCounts(); });
    document.getElementById('clearAllPermissions').addEventListener('click', () => { permissions.forEach(item => item.checked = false); refreshCounts(); });
    document.querySelectorAll('.menu-select').forEach(menu => menu.addEventListener('change', function () { document.querySelectorAll('.module-' + this.dataset.module).forEach(item => item.checked = this.checked); refreshCounts(); }));
    document.querySelectorAll('.section-select').forEach(button => button.addEventListener('click', function () { const items = [...document.querySelectorAll('.section-' + this.dataset.section)]; const select = items.some(item => !item.checked); items.forEach(item => item.checked = select); this.textContent = select ? 'Clear submenu' : 'Select submenu'; refreshCounts(); }));
    permissions.forEach(item => item.addEventListener('change', refreshCounts));
    refreshCounts();
});
</script>
@endpush
