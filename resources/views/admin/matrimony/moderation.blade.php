@extends('admin.layouts.app')

@section('title', 'Matrimony Profile Moderation')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Matrimony Profile Moderation</h1>
        <div class="d-sm-flex">
            <span class="badge badge-warning badge-pill mr-2" style="font-size: 14px;">
                {{ $stats['pending_count'] ?? 0 }} Pending
            </span>
            <a href="{{ route('admin.matrimony.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Matrimony
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
                                Pending Profiles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_count'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heart fa-2x text-gray-300"></i>
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
                                Approved Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved_today'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected_today'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Profiles Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Pending Matrimony Profile Approvals</h6>
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
            @if(count($pendingProfiles) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="profilesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>User</th>
                                <th>Age</th>
                                <th>Location</th>
                                <th>Education</th>
                                <th>Family Details</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingProfiles as $profile)
                            <tr>
                                <td>
                                    <input type="checkbox" class="profile-checkbox" value="{{ $profile->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <div class="icon-circle bg-primary">
                                                <i class="fas fa-heart text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $profile->user->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $profile->user->email ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $profile->id }}</div>
                                        </div>
                                    </div>
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
                                    <div class="text-xs text-gray-500">{{ $profile->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $profile->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Approve Form -->
                                        <form method="POST" action="{{ route('admin.matrimony.approve', $profile->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" 
                                                    onclick="return confirm('Are you sure you want to approve this matrimony profile?')" 
                                                    data-toggle="tooltip" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Reject Form -->
                                        <form method="POST" action="{{ route('admin.matrimony.reject', $profile->id) }}" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="rejection_reason" id="rejection_reason_{{ $profile->id }}">
                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                    onclick="var reason = prompt('Please provide a reason for rejection:'); if (reason && reason.trim() !== '') { document.getElementById('rejection_reason_{{ $profile->id }}').value = reason.trim(); return true; } else { return false; }" 
                                                    data-toggle="tooltip" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        
                                        <a href="{{ route('admin.matrimony.show', $profile->id) }}" class="btn btn-info btn-sm" 
                                           data-toggle="tooltip" title="View Profile Details">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($pendingProfiles, 'links'))
                    <div class="d-flex justify-content-center">
                        {{ $pendingProfiles->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-heart fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No Pending Profiles</h5>
                    <p class="text-gray-400">All matrimony profiles have been reviewed.</p>
                    <a href="{{ route('admin.matrimony.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to All Profiles
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.profile-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function bulkApprove() {
    const selectedProfiles = getSelectedProfiles();
    if (selectedProfiles.length === 0) {
        alert('Please select profiles to approve.');
        return;
    }
    
    if (confirm(`Are you sure you want to approve ${selectedProfiles.length} selected profiles?`)) {
        // Implement bulk approve functionality
        alert('Bulk approve functionality will be implemented.');
    }
}

function bulkReject() {
    const selectedProfiles = getSelectedProfiles();
    if (selectedProfiles.length === 0) {
        alert('Please select profiles to reject.');
        return;
    }
    
    const reason = prompt('Please provide a reason for bulk rejection:');
    if (reason && reason.trim() !== '') {
        if (confirm(`Are you sure you want to reject ${selectedProfiles.length} selected profiles?`)) {
            // Implement bulk reject functionality
            alert('Bulk reject functionality will be implemented.');
        }
    }
}

function exportList() {
    // Implement export functionality
    alert('Export functionality will be implemented.');
}

function getSelectedProfiles() {
    const checkboxes = document.querySelectorAll('.profile-checkbox:checked');
    return Array.from(checkboxes).map(checkbox => checkbox.value);
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection