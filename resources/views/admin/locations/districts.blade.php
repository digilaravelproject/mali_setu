@extends('admin.layouts.app')
@section('title','Districts')
@section('page-title','Location - Districts')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h3 class="fw-bold mb-1">Districts</h3><p class="text-muted mb-0">Add and manage districts.</p></div><span class="badge text-bg-light border fs-6">{{ $districts->total() }}</span></div>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<div class="card mb-4"><div class="card-body"><form method="POST" action="{{ route('admin.locations.districts.store') }}" class="row g-2 align-items-end">@csrf<div class="col-md-8"><label class="form-label">District name</label><input name="name" value="{{ old('name') }}" class="form-control" maxlength="120" required placeholder="e.g. Pune"></div><div class="col-md-2"><label class="form-check"><input class="form-check-input" name="is_active" type="checkbox" value="1" checked> Active</label></div><div class="col-md-2"><button class="btn btn-primary w-100"><i class="fas fa-plus me-1"></i>Add</button></div></form></div></div>
<div class="card mb-4"><div class="card-body"><form method="GET" class="row g-2 align-items-end"><div class="col-md-7"><label class="form-label">Search district</label><input name="search" value="{{ request('search') }}" class="form-control" placeholder="District name"></div><div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">All statuses</option><option value="active" @selected(request('status')==='active')>Active</option><option value="inactive" @selected(request('status')==='inactive')>Inactive</option></select></div><div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-1"></i>Filter</button><a href="{{ route('admin.locations.districts.index') }}" class="btn btn-outline-secondary" title="Clear filters"><i class="fas fa-times"></i></a></div></form></div></div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:80px">#</th>
                    <th>Name</th>
                    <th style="width:160px">Talukas</th>
                    <th style="width:160px">Status</th>
                    <th class="text-end" style="width:150px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($districts as $district)
                    @php($formId = 'district-update-'.$district->id)
                    <tr>
                        <td>{{ $districts->firstItem()+$loop->index }}</td>
                        <td>
                            <form id="{{ $formId }}" method="POST" action="{{ route('admin.locations.districts.update',$district) }}">@csrf @method('PUT')</form>
                            <input form="{{ $formId }}" name="name" value="{{ $district->name }}" class="form-control bg-white" required readonly onclick="this.readOnly=false;this.select()" title="Click the name to edit">
                        </td>
                        <td><span class="badge text-bg-light border fs-6">{{ $district->talukas_count }}</span></td>
                        <td><label class="form-check mb-0"><input form="{{ $formId }}" class="form-check-input" name="is_active" type="checkbox" value="1" @checked($district->is_active)> Active</label></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <button form="{{ $formId }}" class="btn btn-sm btn-outline-primary" title="Save"><i class="fas fa-save"></i></button>
                                <form method="POST" action="{{ route('admin.locations.districts.destroy',$district) }}" onsubmit="return confirm('Delete this district and all child locations?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No districts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $districts->links() }}</div>
@endsection
