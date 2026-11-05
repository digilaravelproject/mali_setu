@extends('admin.layouts.app')
@section('title','Talukas')
@section('page-title','Location - Talukas')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h3 class="fw-bold mb-1">Talukas</h3><p class="text-muted mb-0">Add and manage talukas under a district.</p></div><span class="badge text-bg-light border fs-6">{{ $talukas->total() }}</span></div>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<div class="card mb-4"><div class="card-body"><form method="POST" action="{{ route('admin.locations.talukas.store') }}" class="row g-2 align-items-end">@csrf<div class="col-md-5"><label class="form-label">District</label><select name="district_id" class="form-select" required><option value="">Select district</option>@foreach($districts as $district)<option value="{{ $district->id }}" @selected(old('district_id')==$district->id)>{{ $district->name }}</option>@endforeach</select></div><div class="col-md-3"><label class="form-label">Taluka name</label><input name="name" value="{{ old('name') }}" class="form-control" required></div><div class="col-md-2"><label class="form-check"><input class="form-check-input" name="is_active" type="checkbox" value="1" checked> Active</label></div><div class="col-md-2"><button class="btn btn-primary w-100"><i class="fas fa-plus me-1"></i>Add</button></div></form></div></div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th style="width:70px">#</th><th>District</th><th>Taluka</th><th style="width:150px">Cities / Villages</th><th style="width:140px">Status</th><th class="text-end" style="width:140px">Actions</th></tr></thead>
            <tbody>
                @forelse($talukas as $taluka)
                    @php($formId = 'taluka-update-'.$taluka->id)
                    <tr>
                        <td>{{ $talukas->firstItem()+$loop->index }}</td>
                        <td>
                            <form id="{{ $formId }}" method="POST" action="{{ route('admin.locations.talukas.update',$taluka) }}">@csrf @method('PUT')</form>
                            <select form="{{ $formId }}" name="district_id" class="form-select" required>@foreach($districts as $district)<option value="{{ $district->id }}" @selected($district->id===$taluka->district_id)>{{ $district->name }}</option>@endforeach</select>
                        </td>
                        <td><input form="{{ $formId }}" name="name" value="{{ $taluka->name }}" class="form-control bg-white" required readonly onclick="this.readOnly=false;this.select()" title="Click the name to edit"></td>
                        <td><span class="badge text-bg-light border fs-6">{{ $taluka->localities_count }}</span></td>
                        <td><label class="form-check mb-0"><input form="{{ $formId }}" class="form-check-input" name="is_active" type="checkbox" value="1" @checked($taluka->is_active)> Active</label></td>
                        <td class="text-end"><div class="d-inline-flex gap-2"><button form="{{ $formId }}" class="btn btn-sm btn-outline-primary" title="Save"><i class="fas fa-save"></i></button><form method="POST" action="{{ route('admin.locations.talukas.destroy',$taluka) }}" onsubmit="return confirm('Delete this taluka and all its cities/villages?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button></form></div></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">No talukas found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $talukas->links() }}</div>
@endsection
