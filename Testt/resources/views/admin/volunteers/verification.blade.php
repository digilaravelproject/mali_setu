@extends('admin.layouts.app')

@section('title', 'Pending Volunteer Verification')

@section('content')
<div class="content-area">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="fas fa-check-square me-2"></i>
                Pending Volunteer Verification
            </h2>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stats-label">Pending</p>
                            <p class="stats-number">{{ $stats['pending_count'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stats-label">Approved Today</p>
                            <p class="stats-number">{{ $stats['approved_today'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stats-label">Rejected Today</p>
                            <p class="stats-number">{{ $stats['rejected_today'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Volunteers Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pending Verification</h5>
            <a href="{{ route('admin.volunteers.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-list me-1"></i>
                View All
            </a>
        </div>
        <div class="card-body">
            <!-- Search -->
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by name, email, location, or skills..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>

            @if($pendingVolunteers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Location</th>
                                <th>Skills</th>
                                <th>Availability</th>
                                <th>Applied</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingVolunteers as $volunteer)
                            <tr>
                                <td>
                                    <strong>{{ $volunteer->user->name }}</strong>
                                </td>
                                <td>{{ $volunteer->user->email }}</td>
                                <td>{{ $volunteer->location ?? 'N/A' }}</td>
                                <td>
                                    <small>{{ Str::limit($volunteer->skills ?? 'N/A', 25) }}</small>
                                </td>
                                <td>
                                    <small class="badge bg-info">{{ $volunteer->availability ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <small>{{ $volunteer->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.volunteers.show', $volunteer->id) }}" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        
                                        <button type="button" class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal"
                                                data-volunteer-id="{{ $volunteer->id }}"
                                                data-volunteer-name="{{ $volunteer->user->name }}">
                                            <i class="fas fa-thumbs-up"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal"
                                                data-volunteer-id="{{ $volunteer->id }}"
                                                data-volunteer-name="{{ $volunteer->user->name }}">
                                            <i class="fas fa-thumbs-down"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $pendingVolunteers->links('pagination::bootstrap-4') }}
            @else
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Great! All volunteers have been verified.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Volunteer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to approve <strong id="volunteerNameApprove"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Volunteer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Provide a reason for rejecting <strong id="volunteerNameReject"></strong>:</p>
                    <textarea name="rejection_reason" class="form-control" rows="4" 
                              placeholder="Enter rejection reason..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Handle approve modal
    const approveModalEl = document.getElementById('approveModal');
    approveModalEl.addEventListener('show.bs.modal', function (e) {
        const volunteerId = e.relatedTarget.getAttribute('data-volunteer-id');
        const volunteerName = e.relatedTarget.getAttribute('data-volunteer-name');
        
        document.getElementById('volunteerNameApprove').textContent = volunteerName;
        document.getElementById('approveForm').action = `/admin/volunteers/${volunteerId}/approve`;
    });

    // Handle reject modal
    const rejectModalEl = document.getElementById('rejectModal');
    rejectModalEl.addEventListener('show.bs.modal', function (e) {
        const volunteerId = e.relatedTarget.getAttribute('data-volunteer-id');
        const volunteerName = e.relatedTarget.getAttribute('data-volunteer-name');
        
        document.getElementById('volunteerNameReject').textContent = volunteerName;
        document.getElementById('rejectForm').action = `/admin/volunteers/${volunteerId}/reject`;
    });
</script>
@endsection
