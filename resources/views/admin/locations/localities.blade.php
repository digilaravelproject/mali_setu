@extends('admin.layouts.app')
@section('title','Cities / Villages')
@section('page-title','Location - City / Village')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h3 class="fw-bold mb-1">Cities / Villages</h3><p class="text-muted mb-0">Add and manage cities or villages under a taluka.</p></div><span class="badge text-bg-light border fs-6">{{ $localities->total() }}</span></div>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<div class="card mb-4"><div class="card-body"><form method="POST" action="{{ route('admin.locations.localities.store') }}" class="row g-2 align-items-end">@csrf<div class="col-md-4"><label class="form-label">District / Taluka</label><select name="taluka_id" class="form-select" required><option value="">Select taluka</option>@foreach($talukas as $taluka)<option value="{{ $taluka->id }}" @selected(old('taluka_id')==$taluka->id)>{{ $taluka->district?->name }} › {{ $taluka->name }}</option>@endforeach</select></div><div class="col-md-3"><label class="form-label">Name</label><input name="name" value="{{ old('name') }}" class="form-control" required></div><div class="col-md-2"><label class="form-label">Type</label><select name="type" class="form-select"><option value="city">City</option><option value="village">Village</option></select></div><div class="col-md-1"><label class="form-check"><input class="form-check-input" name="is_active" type="checkbox" value="1" checked> Active</label></div><div class="col-md-2"><button class="btn btn-primary w-100"><i class="fas fa-plus me-1"></i>Add</button></div></form></div></div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th style="width:65px">#</th><th>District</th><th>Taluka</th><th>Name</th><th style="width:130px">Type</th><th style="width:130px">Status</th><th class="text-end" style="width:140px">Actions</th></tr></thead>
            <tbody>
                @forelse($localities as $locality)
                    @php($formId = 'locality-update-'.$locality->id)
                    <tr>
                        <td>{{ $localities->firstItem()+$loop->index }}</td>
                        <td>{{ $locality->taluka?->district?->name ?: '—' }}</td>
                        <td>
                            <form id="{{ $formId }}" method="POST" action="{{ route('admin.locations.localities.update',$locality) }}">@csrf @method('PUT')</form>
                            <select form="{{ $formId }}" name="taluka_id" class="form-select" required>@foreach($talukas as $taluka)<option value="{{ $taluka->id }}" @selected($taluka->id===$locality->taluka_id)>{{ $taluka->name }}</option>@endforeach</select>
                        </td>
                        <td><input form="{{ $formId }}" name="name" value="{{ $locality->name }}" class="form-control bg-white" required readonly onclick="this.readOnly=false;this.select()" title="Click the name to edit"></td>
                        <td><select form="{{ $formId }}" name="type" class="form-select"><option value="city" @selected($locality->type==='city')>City</option><option value="village" @selected($locality->type==='village')>Village</option></select></td>
                        <td><label class="form-check mb-0"><input form="{{ $formId }}" class="form-check-input" name="is_active" type="checkbox" value="1" @checked($locality->is_active)> Active</label></td>
                        <td class="text-end"><div class="d-inline-flex gap-2"><button form="{{ $formId }}" class="btn btn-sm btn-outline-primary" title="Save"><i class="fas fa-save"></i></button><form method="POST" action="{{ route('admin.locations.localities.destroy',$locality) }}" onsubmit="return confirm('Delete this city/village?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button></form></div></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">No cities or villages found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $localities->links() }}</div>
@endsection
