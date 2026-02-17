@extends('admin.layouts.app')

@section('title', 'Volunteer Profile')

@section('content')
<div class="content-area">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('admin.volunteers.index') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Volunteers
            </a>
            <h2 class="mb-4">
                <i class="fas fa-user-tie me-2"></i>
                Volunteer Profile
            </h2>
        </div>
    </div>

    <div class="row">
        <!-- Volunteer Info -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Name</label>
                        <p class="mb-0"><strong>{{ $volunteer->user->name }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Email</label>
                        <p class="mb-0">
                            <a href="mailto:{{ $volunteer->user->email }}">{{ $volunteer->user->email }}</a>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Phone</label>
                        <p class="mb-0">{{ $volunteer->user->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Location</label>
                        <p class="mb-0">{{ $volunteer->location ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Status</label>
                        <p class="mb-0">
                            @if($volunteer->status === 'active')
                                <span class="badge bg-success">Approved</span>
                            @elseif($volunteer->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($volunteer->status === 'inactive')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Volunteer Details -->
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Skills & Experience</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Skills</label>
                        <p class="mb-0">{{ $volunteer->skills ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Experience</label>
                        <p class="mb-0">{{ $volunteer->experience ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Availability</label>
                        <p class="mb-0">{{ $volunteer->availability ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small">Bio</label>
                        <p class="mb-0">{{ $volunteer->bio ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Interests</h5>
                </div>
                <div class="card-body">
                    @if($volunteer->interests && is_array($volunteer->interests))
                        <div>
                            @foreach($volunteer->interests as $interest)
                                <span class="badge bg-primary me-2 mb-2">{{ $interest }}</span>
                            @endforeach
                        </div>
                    @else
                        <p>No interests defined</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    @if($volunteer->status === 'pending')
                        <button type="button" class="btn btn-success me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#approveModal"
                                data-volunteer-id="{{ $volunteer->id }}"
                                data-volunteer-name="{{ $volunteer->user->name }}">
                            <i class="fas fa-thumbs-up me-1"></i> Approve
                        </button>
                        <button type="button" class="btn btn-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#rejectModal"
                                data-volunteer-id="{{ $volunteer->id }}"
                                data-volunteer-name="{{ $volunteer->user->name }}">
                            <i class="fas fa-thumbs-down me-1"></i> Reject
                        </button>
                    @endif
                    <button type="button" class="btn btn-outline-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-circle" style="color: #b61315;"></i>
                        </div>
                        <div>
                            <p class="mb-0"><strong>Created</strong></p>
                            <small class="text-muted">{{ $volunteer->created_at->format('M d, Y \a\t h:i A') }}</small>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-circle" style="color: #b61315;"></i>
                        </div>
                        <div>
                            <p class="mb-0"><strong>Last Updated</strong></p>
                            <small class="text-muted">{{ $volunteer->updated_at->format('M d, Y \a\t h:i A') }}</small>
                        </div>
                    </div>
                </div>
            </div>
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Volunteer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.volunteers.destroy', $volunteer->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to delete this volunteer profile? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Delete
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
