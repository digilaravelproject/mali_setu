@extends('admin.layouts.app')

@section('title', 'Business Jobs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title">Jobs for {{ $business->business_name }}</h3>
                        <small class="text-muted">Owner: {{ $business->user->name }} ({{ $business->user->email }})</small>
                    </div>
                    <div class="card-tools">
                        <a href="{{ route('admin.businesses.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to businesses
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <span class="badge bg-success">Verified</span>
                        <span class="badge bg-info">Jobs: {{ $business->jobPostings->count() }}</span>
                        <span class="badge bg-secondary">Applications: {{ $business->jobPostings->sum('applications_count') }}</span>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($business->jobPostings->isEmpty())
                        <div class="alert alert-warning">No job postings found for this business.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Job Title</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Posted</th>
                                        <th>Expires</th>
                                        <th>Applications</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($business->jobPostings as $job)
                                        <tr>
                                            <td>{{ $job->id }}</td>
                                            <td>
                                                <strong>{{ $job->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($job->description, 80) }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = 'secondary';
                                                    if ($job->status === 'approved') $statusClass = 'success';
                                                    elseif ($job->status === 'pending') $statusClass = 'warning';
                                                    elseif ($job->status === 'rejected') $statusClass = 'danger';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($job->status) }}</span>
                                            </td>
                                            <td>{{ ucfirst($job->job_type ?? $job->employment_type ?? 'N/A') }}</td>
                                            <td>{{ optional($job->created_at)->format('M d, Y') }}</td>
                                            <td>{{ optional($job->expires_at)->format('M d, Y') ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $job->applications_count }}</span>
                                                @if($job->applications_count > 0)
                                                    <div>
                                                        <a class="small" data-bs-toggle="collapse" href="#jobApplicants{{ $job->id }}" role="button" aria-expanded="false" aria-controls="jobApplicants{{ $job->id }}">
                                                            View applicants
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($job->status !== 'approved')
                                                        <form action="{{ route('admin.businesses.jobs.approve', $job->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this job posting?')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif

                                                    @if($job->status !== 'rejected')
                                                        <form action="{{ route('admin.businesses.jobs.reject', $job->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this job posting?')">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @if($job->applications_count > 0)
                                            <tr class="collapse" id="jobApplicants{{ $job->id }}">
                                                <td colspan="8">
                                                    <strong>Applicants for &ldquo;{{ $job->title }}&rdquo;</strong>
                                                    <ul class="list-group list-group-flush mt-2">
                                                        @foreach($job->applications as $application)
                                                            <li class="list-group-item">
                                                                <div class="d-flex justify-content-between align-items-start">
                                                                    <div>
                                                                        <strong>{{ $application->user->name ?? 'Unknown' }}</strong>
                                                                        <br>
                                                                        <small>{{ $application->user->email ?? 'No email' }}</small>
                                                                    </div>
                                                                    <span class="badge bg-{{ $application->status === 'accepted' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }}">
                                                                        {{ ucfirst($application->status) }}
                                                                    </span>
                                                                </div>
                                                                @if($application->cover_letter)
                                                                    <div class="mt-2"><small>{{ Str::limit($application->cover_letter, 140) }}</small></div>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
