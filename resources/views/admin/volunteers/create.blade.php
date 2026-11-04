@extends('admin.layouts.app')
@section('title','Add Volunteer')
@section('page-title','Add Volunteer')
@section('content')
<div class="d-flex justify-content-between mb-3"><div><h3 class="fw-bold">Add Volunteer</h3><p class="text-muted">Create a volunteer account with a unique referral code.</p></div><a href="{{ route('admin.volunteers.index') }}" class="btn btn-outline-secondary align-self-start">Back</a></div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('admin.volunteers.store') }}" class="card"><div class="card-body row g-3">@csrf
 <div class="col-md-6"><label class="form-label">Full Name *</label><input name="name" value="{{ old('name') }}" class="form-control" required></div><div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
 <div class="col-md-6"><label class="form-label">Mobile *</label><input name="phone" value="{{ old('phone') }}" class="form-control" required></div><div class="col-md-3"><label class="form-label">Status *</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div><div class="col-md-3"><label class="form-label">Commission Points *</label><input type="number" name="commission_points" value="{{ old('commission_points',2) }}" min="0" max="100" class="form-control" required></div>
 <div class="col-md-6"><label class="form-label">Skills</label><input name="skills" value="{{ old('skills') }}" class="form-control" placeholder="Communication, coordination"></div><div class="col-md-6"><label class="form-label">Availability</label><input name="availability" value="{{ old('availability') }}" class="form-control"></div>
 <div class="col-md-6"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required></div><div class="col-md-6"><label class="form-label">Confirm Password *</label><input type="password" name="password_confirmation" class="form-control" required></div>
</div><div class="card-footer bg-white text-end"><button class="btn btn-primary px-4">Create Volunteer</button></div></form>
@endsection
