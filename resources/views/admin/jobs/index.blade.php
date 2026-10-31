@extends('admin.layouts.app')

@section('title', 'All Jobs')
@section('page-title', 'All Jobs (Across All Businesses)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Home</a> <span class="mx-2">›</span> Jobs &amp; Careers <span class="mx-2">›</span> All Jobs</div>
    <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Job</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-lg-2 col-md-4"><select name="category" class="form-select"><option value="">All Categories</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-4"><select name="location" class="form-select"><option value="">All Locations</option>@foreach($locations as $location)<option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-4"><select name="employment_type" class="form-select"><option value="">All Job Types</option>@foreach(['full-time','part-time','contract','freelance','internship'] as $type)<option value="{{ $type }}" @selected(request('employment_type') === $type)>{{ str($type)->headline() }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-4"><select name="status" class="form-select"><option value="">All Status</option>@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
            <div class="col-lg-3 col-md-6"><div class="input-group"><span class="input-group-text bg-white"><i class="fas fa-search"></i></span><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Search jobs by title, company or keyword..."></div></div>
            <div class="col-lg-1 col-md-2 d-grid"><button class="btn btn-outline-dark"><i class="fas fa-filter me-1"></i>Filter</button></div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>#</th><th>Job Title</th><th>Business / Company</th><th>Location</th><th>Type</th><th>Applications</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>{{ $jobs->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold">{{ $job->title }}</td>
                        <td>{{ $job->business?->business_name ?? '—' }}</td>
                        <td>{{ $job->location }}</td>
                        <td>{{ str($job->employment_type ?: $job->job_type)->headline() }}</td>
                        <td><a href="{{ route('admin.jobs.applications', ['job_id' => $job->id]) }}" class="text-decoration-none">{{ $job->applications_count }}</a></td>
                        <td><span class="badge bg-{{ $job->status === 'approved' ? 'success' : ($job->status === 'pending' ? 'warning text-dark' : 'danger') }}">{{ ucfirst($job->status) }}</span>@unless($job->is_active)<span class="badge bg-secondary ms-1">Hidden</span>@endunless</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.jobs.toggle', $job) }}" method="POST" class="d-inline">@csrf @method('PATCH')<button class="btn btn-sm btn-info text-white" title="{{ $job->is_active ? 'Hide' : 'Show' }}"><i class="fas fa-{{ $job->is_active ? 'eye-slash' : 'eye' }}"></i></button></form>
                            <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this job and its applications?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button></form>
                        </td>
                    </tr>
                @empty<tr><td colspan="8" class="text-center py-5 text-muted">No jobs found.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center"><span class="small text-muted">Showing {{ $jobs->firstItem() ?? 0 }} to {{ $jobs->lastItem() ?? 0 }} of {{ $jobs->total() }} entries</span>{{ $jobs->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection
