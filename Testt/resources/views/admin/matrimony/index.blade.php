@extends('admin.layouts.app')

@section('title', 'Matrimony Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Matrimony Profile Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.matrimony.moderation') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-eye"></i> Content Moderation
                        </a>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $stats['total'] ?? 0 }}</h3>
                                    <p>Total Profiles</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $stats['approved'] ?? 0 }}</h3>
                                    <p>Approved</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $stats['pending'] ?? 0 }}</h3>
                                    <p>Pending</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $stats['connections'] ?? 0 }}</h3>
                                    <p>Connections</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-link"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form method="GET" action="{{ route('admin.matrimony.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <select name="approval_status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="gender" class="form-select">
                                        <option value="">All Genders</option>
                                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Search profiles..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Profiles Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Age</th>
                                    <th>Location</th>
                                    <th>Education</th>
                                    <th>Family Details</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($profiles ?? [] as $profile)
                                <tr>
                                    <td>{{ $profile->id }}</td>
                                    <td>
                                        <strong>{{ $profile->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $profile->user->email }}</small>
                                    </td>
                                    <td>{{ $profile->age ?? 'N/A' }}</td>
                                    <td>
                                        @if($profile->location_details && is_array($profile->location_details))
                                            {{ $profile->location_details['city'] ?? 'N/A' }}, {{ $profile->location_details['state'] ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($profile->education_details && is_array($profile->education_details))
                                            {{ $profile->education_details['highest_qualification'] ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($profile->family_details && is_array($profile->family_details))
                                            <div class="text-xs">
                                                <strong>Father:</strong> {{ $profile->family_details['father_name'] ?? 'N/A' }}<br>
                                                <strong>Mother:</strong> {{ $profile->family_details['mother_name'] ?? 'N/A' }}<br>
                                                <strong>Siblings:</strong> {{ $profile->family_details['siblings'] ?? 'N/A' }}<br>
                                                <strong>Type:</strong> {{ $profile->family_details['family_type'] ?? 'N/A' }}
                                            </div>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($profile->approval_status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($profile->approval_status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $profile->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.matrimony.show', $profile->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($profile->approval_status == 'pending')
                                                <!-- Approve Form -->
                                                <form method="POST" action="{{ route('admin.matrimony.approve', $profile->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            onclick="return confirm('Are you sure you want to approve this matrimony profile?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                
                                                <!-- Reject Form -->
                                                <form method="POST" action="{{ route('admin.matrimony.reject', $profile->id) }}" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="rejection_reason" id="rejection_reason_{{ $profile->id }}">
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="var reason = prompt('Please provide a reason for rejection:'); if (reason && reason.trim() !== '') { document.getElementById('rejection_reason_{{ $profile->id }}').value = reason.trim(); return true; } else { return false; }">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No matrimony profiles found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if(isset($profiles))
                    <div class="d-flex justify-content-center">
                        {{ $profiles->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this matrimony profile?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="approveForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection