@extends('admin.layouts.app')

@php($editing = $job->exists)
@section('title', $editing ? 'Edit Job' : 'Add Job')
@section('page-title', $editing ? 'Edit Job' : 'Create Job')

@section('content')
<div class="text-muted small mb-3"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Home</a> <span class="mx-2">›</span> Jobs &amp; Careers <span class="mx-2">›</span> {{ $editing ? 'Edit Job' : 'Create Job' }}</div>
<div class="card mx-auto" style="max-width:950px">
    <form method="POST" action="{{ $editing ? route('admin.jobs.update', $job) : route('admin.jobs.store') }}">
        @csrf @if($editing) @method('PUT') @endif
        <div class="card-body p-4">
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <div class="mb-3"><label class="form-label">Job Title <span class="text-danger">*</span></label><input name="title" value="{{ old('title', $job->title) }}" class="form-control" required></div>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Business / Company <span class="text-danger">*</span></label><select name="business_id" class="form-select" required><option value="">Select Business</option>@foreach($businesses as $business)<option value="{{ $business->id }}" @selected((string)old('business_id', $job->business_id) === (string)$business->id)>{{ $business->business_name }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Category</label><input name="category" value="{{ old('category', $job->category) }}" class="form-control" placeholder="e.g. IT & Software"></div>
                <div class="col-md-4"><label class="form-label">Job Type <span class="text-danger">*</span></label><input name="job_type" value="{{ old('job_type', $job->job_type) }}" class="form-control" placeholder="Full Time" required></div>
                <div class="col-md-4"><label class="form-label">Employment Type</label><select name="employment_type" class="form-select"><option value="">Select type</option>@foreach(['full-time','part-time','contract','freelance','internship'] as $type)<option value="{{ $type }}" @selected(old('employment_type', $job->employment_type) === $type)>{{ str($type)->headline() }}</option>@endforeach</select></div>
                <div class="col-md-4"><label class="form-label">Experience Level</label><select name="experience_level" class="form-select"><option value="">Select level</option>@foreach(['entry','junior','mid','senior','executive'] as $level)<option value="{{ $level }}" @selected(old('experience_level', $job->experience_level) === $level)>{{ ucfirst($level) }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Location <span class="text-danger">*</span></label><input name="location" value="{{ old('location', $job->location) }}" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Salary (Optional)</label><input name="salary_range" value="{{ old('salary_range', $job->salary_range) }}" class="form-control" placeholder="₹ 3,00,000 - ₹ 6,00,000"></div>
            </div>
            <div class="mt-3"><label class="form-label">Job Description <span class="text-danger">*</span></label><textarea name="description" rows="4" class="form-control" required>{{ old('description', $job->description) }}</textarea></div>
            <div class="mt-3"><label class="form-label">Requirements <span class="text-danger">*</span></label><textarea name="requirements" rows="4" class="form-control" required>{{ old('requirements', $job->requirements) }}</textarea></div>
            <div class="row g-3 mt-0">
                <div class="col-md-6"><label class="form-label">Skills (comma separated)</label><input name="skills_required_text" value="{{ old('skills_required_text', is_array($job->skills_required) ? implode(', ', $job->skills_required) : '') }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Benefits (comma separated)</label><input name="benefits_text" value="{{ old('benefits_text', is_array($job->benefits) ? implode(', ', $job->benefits) : '') }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Application Deadline</label><input type="date" name="application_deadline" value="{{ old('application_deadline', $job->application_deadline?->format('Y-m-d')) }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Expires At <span class="text-danger">*</span></label><input type="date" name="expires_at" value="{{ old('expires_at', $job->expires_at?->format('Y-m-d')) }}" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Approval Status</label><select name="status" class="form-select">@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected(old('status', $job->status ?: 'pending') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Visibility</label><select name="is_active" class="form-select"><option value="1" @selected((string)old('is_active', $job->exists ? (int)$job->is_active : 1) === '1')>Active</option><option value="0" @selected((string)old('is_active', $job->exists ? (int)$job->is_active : 1) === '0')>Hidden</option></select></div>
            </div>
        </div>
        <div class="card-footer bg-white text-end p-3"><a href="{{ route('admin.jobs.index') }}" class="btn btn-light border me-2">Cancel</a><button class="btn btn-primary px-5">Save Job</button></div>
    </form>
</div>
@endsection
