@extends('admin.layouts.app')

@section('title', 'Job Applications')
@section('page-title', 'Job Applications')

@section('content')
<div class="text-muted small mb-3"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Home</a> <span class="mx-2">›</span> Jobs &amp; Careers <span class="mx-2">›</span> Applications</div>
<div class="row g-3 mb-3">
    @foreach([['total','Total Applications','users','danger'],['pending','Pending','clock','warning'],['reviewed','Reviewed','eye','info'],['accepted','Accepted','user-check','success']] as [$key,$label,$icon,$color])
    <div class="col-md-3 col-6"><div class="card h-100"><div class="card-body text-center"><i class="fas fa-{{ $icon }} text-{{ $color }} fs-4"></i><div class="fs-4 fw-bold">{{ $counts[$key] }}</div><div class="small text-muted">{{ $label }}</div></div></div></div>
    @endforeach
</div>
<div class="card"><div class="card-body">
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4"><select name="job_id" class="form-select"><option value="">All Jobs</option>@foreach($jobs as $job)<option value="{{ $job->id }}" @selected((string)request('job_id') === (string)$job->id)>{{ $job->title }}</option>@endforeach</select></div>
        <div class="col-md-2"><select name="status" class="form-select"><option value="">All Status</option>@foreach(['pending','reviewed','accepted','rejected'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div class="col-md-5"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or email..."></div>
        <div class="col-md-1 d-grid"><button class="btn btn-outline-dark"><i class="fas fa-filter"></i></button></div>
    </form>
    <div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>#</th><th>Candidate</th><th>Job</th><th>Applied On</th><th>Status</th><th>Resume</th><th class="text-end">Action</th></tr></thead><tbody>
    @forelse($applications as $application)<tr>
        <td>{{ $applications->firstItem() + $loop->index }}</td><td><div class="fw-semibold">{{ $application->user?->name ?? 'Deleted user' }}</div><small class="text-muted">{{ $application->user?->email }}</small></td>
        <td><div>{{ $application->jobPosting?->title ?? 'Deleted job' }}</div><small class="text-muted">{{ $application->jobPosting?->business?->business_name }}</small></td>
        <td>{{ $application->applied_at?->format('d M Y') }}</td>
        <td><span class="badge bg-{{ ['pending'=>'warning text-dark','reviewed'=>'info','accepted'=>'success','rejected'=>'danger'][$application->status] ?? 'secondary' }}">{{ ucfirst($application->status) }}</span></td>
        <td>@if($application->resume_url)<a class="btn btn-sm btn-outline-primary" href="{{ asset('storage/'.$application->resume_url) }}" target="_blank"><i class="fas fa-file-download"></i></a>@else—@endif</td>
        <td class="text-end text-nowrap"><form method="POST" action="{{ route('admin.jobs.applications.status', $application) }}" class="d-inline-flex gap-1">@csrf @method('PATCH')<select name="status" class="form-select form-select-sm" onchange="this.form.submit()">@foreach(['pending','reviewed','accepted','rejected'] as $status)<option value="{{ $status }}" @selected($application->status === $status)>{{ ucfirst($status) }}</option>@endforeach</select></form> <form method="POST" action="{{ route('admin.jobs.applications.destroy', $application) }}" class="d-inline" onsubmit="return confirm('Delete this application?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form></td>
    </tr>@empty<tr><td colspan="7" class="text-center py-5 text-muted">No applications found.</td></tr>@endforelse
    </tbody></table></div>
    <div class="d-flex justify-content-between align-items-center"><span class="small text-muted">Showing {{ $applications->firstItem() ?? 0 }} to {{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} entries</span>{{ $applications->links('pagination::bootstrap-5') }}</div>
</div></div>
@endsection
