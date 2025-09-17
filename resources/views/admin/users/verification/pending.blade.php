@extends('admin.layouts.app')

@section('title', 'Pending Verifications')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pending Verifications</h1>
        <div class="d-sm-flex">
            <span class="badge badge-warning badge-pill mr-2" style="font-size: 14px;">
                {{ $stats['pending_count'] ?? 0 }} Pending
            </span>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Caste Certificates
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['caste_certificates'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Business Registrations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['businesses'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Matrimony Profiles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['matrimony_profiles'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Verifications Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Pending Caste Certificate Verifications</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Actions:</div>
                    <a class="dropdown-item" href="#" onclick="bulkApprove()">Bulk Approve</a>
                    <a class="dropdown-item" href="#" onclick="bulkReject()">Bulk Reject</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" onclick="exportList()">Export List</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(count($pending_verifications) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="verificationsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>User Type</th>
                                <th>Certificate</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pending_verifications as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" class="verification-checkbox" value="{{ $user->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <div class="icon-circle bg-primary">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $user->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email ?? 'N/A' }}</td>
                                <td>{{ $user->phone ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-secondary">{{ ucfirst($user->user_type ?? 'general') }}</span>
                                </td>
                                <td>
                                    @if($user->casteCertificate && $user->casteCertificate->file_path)
                                        <a href="#" class="btn btn-outline-primary btn-sm" onclick="viewCertificate('{{ $user->casteCertificate->file_path }}')"
                                           data-toggle="tooltip" title="View Certificate">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <div class="text-xs text-muted mt-1">{{ $user->casteCertificate->admin_notes ?? 'Certificate uploaded' }}</div>
                                    @else
                                        <span class="text-muted">No Certificate</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-xs text-gray-500">{{ $user->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-success btn-sm" 
                                                onclick="approveVerification({{ $user->id }})" 
                                                data-toggle="tooltip" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="rejectVerification({{ $user->id }})" 
                                                data-toggle="tooltip" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm" 
                                                onclick="viewUserDetails({{ $user->id }})" 
                                                data-toggle="tooltip" title="View User Details">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($pending_verifications, 'links'))
                    <div class="d-flex justify-content-center">
                        {{ $pending_verifications->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-gray-600">No Pending Verifications</h5>
                    <p class="text-gray-500">All caste certificates have been processed.</p>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Certificate Viewer Modal -->
<div class="modal fade" id="certificateModal" tabindex="-1" role="dialog" aria-labelledby="certificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="certificateModalLabel">Caste Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="certificateContent">
                    <!-- Certificate content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="downloadCertificate()">Download</button>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectionModalLabel">Rejection Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectionForm">
                    <div class="form-group">
                        <label for="rejectionReason">Please provide a reason for rejection:</label>
                        <textarea class="form-control" id="rejectionReason" rows="4" 
                                  placeholder="Enter the reason for rejecting this certificate..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmRejection()">Reject Certificate</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentVerificationId = null;
let currentCertificatePath = null;

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Toggle select all checkboxes
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.verification-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// View certificate
function viewCertificate(filePath) {
    currentCertificatePath = filePath;
    const fileExtension = filePath.split('.').pop().toLowerCase();
    
    let content = '';
    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
        content = `<img src="/storage/${filePath}" class="img-fluid" alt="Certificate">`;
    } else if (fileExtension === 'pdf') {
        content = `<embed src="/storage/${filePath}" type="application/pdf" width="100%" height="500px">`;
    } else {
        content = `<p class="text-muted">Cannot preview this file type. <a href="/storage/${filePath}" target="_blank">Download to view</a></p>`;
    }
    
    document.getElementById('certificateContent').innerHTML = content;
    const modal = new bootstrap.Modal(document.getElementById('certificateModal'));
    modal.show();
}

// Download certificate
function downloadCertificate() {
    if (currentCertificatePath) {
        window.open(`/storage/${currentCertificatePath}`, '_blank');
    }
}

// Approve verification
function approveVerification(verificationId) {
    if (confirm('Are you sure you want to approve this certificate?')) {
        // Implement approval logic
        fetch(`/admin/users/${verificationId}/approve-certificate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('An error occurred while processing the request.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing the request.');
        });
    }
}

// Reject verification
function rejectVerification(verificationId) {
    currentVerificationId = verificationId;
    const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
    modal.show();
}

// Confirm rejection with reason
function confirmRejection() {
    const reason = document.getElementById('rejectionReason').value.trim();
    
    if (!reason) {
        alert('Please provide a reason for rejection.');
        return;
    }
    
    // Implement rejection logic
    fetch(`/admin/users/${currentVerificationId}/reject-certificate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ admin_notes: reason })
    })
    .then(response => {
        if (response.ok) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('rejectionModal'));
            modal.hide();
            location.reload();
        } else {
            alert('An error occurred while processing the request.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing the request.');
    });
}

// View user details
function viewUserDetails(userId) {
    window.open(`/admin/users/${userId}`, '_blank');
}

// Bulk operations
function bulkApprove() {
    const selectedIds = getSelectedVerificationIds();
    
    if (selectedIds.length === 0) {
        alert('Please select at least one verification to approve.');
        return;
    }
    
    if (confirm(`Are you sure you want to approve ${selectedIds.length} selected verifications?`)) {
        // Implement bulk approval logic
        console.log('Bulk approve:', selectedIds);
        alert('Bulk approval functionality will be implemented.');
    }
}

function bulkReject() {
    const selectedIds = getSelectedVerificationIds();
    
    if (selectedIds.length === 0) {
        alert('Please select at least one verification to reject.');
        return;
    }
    
    if (confirm(`Are you sure you want to reject ${selectedIds.length} selected verifications?`)) {
        // Implement bulk rejection logic
        console.log('Bulk reject:', selectedIds);
        alert('Bulk rejection functionality will be implemented.');
    }
}

function exportList() {
    // Implement export functionality
    alert('Export functionality will be implemented.');
}

// Helper function to get selected verification IDs
function getSelectedVerificationIds() {
    const checkboxes = document.querySelectorAll('.verification-checkbox:checked');
    return Array.from(checkboxes).map(checkbox => checkbox.value);
}
</script>
@endpush