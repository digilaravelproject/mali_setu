@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Management</h1>
        <div class="d-sm-flex">
            <a href="{{ route('admin.users.verification.pending') }}" class="btn btn-warning btn-sm shadow-sm mr-2">
                <i class="fas fa-clock fa-sm text-white-50"></i> Pending Verifications
            </a>
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
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Verified Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['verified_users'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Verification
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_verification'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_today'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Search Users</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Name, email, phone..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="user_type">User Type</label>
                            <select class="form-control" id="user_type" name="user_type">
                                <option value="">All Types</option>
                                <option value="individual" {{ request('user_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="business" {{ request('user_type') == 'business' ? 'selected' : '' }}>Business</option>
                                <option value="volunteer" {{ request('user_type') == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                                <option value="donor" {{ request('user_type') == 'donor' ? 'selected' : '' }}>Donor</option>
                                <option value="matrimony" {{ request('user_type') == 'matrimony' ? 'selected' : '' }}>Matrimony</option>
                                <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="verification_status">Verification</label>
                            <select class="form-control" id="verification_status" name="verification_status">
                                <option value="">All Status</option>
                                <option value="verified" {{ request('verification_status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="pending" {{ request('verification_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="rejected" {{ request('verification_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Account Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Users</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Actions:</div>
                    <a class="dropdown-item" href="#" onclick="exportUsers()">Export Users</a>
                    <a class="dropdown-item" href="#" onclick="bulkActions()">Bulk Actions</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" onclick="refreshData()">Refresh Data</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(count($users) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>User</th>
                                <th>Contact</th>
                                <th>Type</th>
                                <th>Verification</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <div class="icon-circle bg-primary">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <div>{{ $user->email }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->phone }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ ucfirst($user->user_type) }}</span>
                                </td>
                                <td>
                                    @if($user->caste_certificate_verified)
                                        <span class="badge badge-success">Verified</span>
                                    @elseif($user->caste_certificate_path)
                                        <span class="badge badge-warning">Pending</span>
                                    @else
                                        <span class="badge badge-secondary">Not Submitted</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($user->status == 'inactive')
                                        <span class="badge badge-secondary">Inactive</span>
                                    @else
                                        <span class="badge badge-danger">Suspended</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-xs text-gray-500">{{ $user->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-info btn-sm" 
                                                onclick="viewUser({{ $user->id }})" 
                                                data-toggle="tooltip" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm" 
                                                onclick="editUser({{ $user->id }})" 
                                                data-toggle="tooltip" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if($user->status == 'active')
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="suspendUser({{ $user->id }})" 
                                                    data-toggle="tooltip" title="Suspend User">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-success btn-sm" 
                                                    onclick="activateUser({{ $user->id }})" 
                                                    data-toggle="tooltip" title="Activate User">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($users, 'links'))
                    <div class="d-flex justify-content-center">
                        {{ $users->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-gray-400 mb-3"></i>
                    <h5 class="text-gray-600">No Users Found</h5>
                    <p class="text-gray-500">No users match your current search criteria.</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                        <i class="fas fa-undo"></i> Reset Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">User Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="userDetails">
                    <!-- User details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="editCurrentUser()">Edit User</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentUserId = null;

// Initialize tooltips
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// Toggle select all checkboxes
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// View user details
function viewUser(userId) {
    window.location.href = `/admin/users/${userId}`;
}

// Display user details in modal
function displayUserDetails(user) {
    const detailsHtml = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="font-weight-bold">Personal Information</h6>
                <p><strong>Name:</strong> ${user.name}</p>
                <p><strong>Email:</strong> ${user.email}</p>
                <p><strong>Phone:</strong> ${user.phone}</p>
                <p><strong>User Type:</strong> ${user.user_type}</p>
                <p><strong>Status:</strong> <span class="badge badge-${user.status === 'active' ? 'success' : 'danger'}">${user.status}</span></p>
            </div>
            <div class="col-md-6">
                <h6 class="font-weight-bold">Account Information</h6>
                <p><strong>Joined:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
                <p><strong>Last Updated:</strong> ${new Date(user.updated_at).toLocaleDateString()}</p>
                <p><strong>Email Verified:</strong> ${user.email_verified_at ? 'Yes' : 'No'}</p>
                <p><strong>Certificate Verified:</strong> ${user.caste_certificate_verified ? 'Yes' : 'No'}</p>
            </div>
        </div>
    `;
    
    document.getElementById('userDetails').innerHTML = detailsHtml;
}

// Edit user
function editUser(userId) {
    window.location.href = `/admin/users/${userId}/edit`;
}

// Edit current user from modal
function editCurrentUser() {
    if (currentUserId) {
        editUser(currentUserId);
        $('#userModal').modal('hide');
    }
}

// Suspend user
function suspendUser(userId) {
    if (confirm('Are you sure you want to suspend this user?')) {
        updateUserStatus(userId, 'suspended');
    }
}

// Activate user
function activateUser(userId) {
    if (confirm('Are you sure you want to activate this user?')) {
        updateUserStatus(userId, 'active');
    }
}

// Update user status
function updateUserStatus(userId, status) {
    const action = status === 'active' ? 'activate' : 'suspend';
    
    fetch(`/admin/users/${userId}/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating user status.');
    });
}

// Export users
function exportUsers() {
    // Implement export functionality
    alert('Export functionality will be implemented.');
}

// Bulk actions
function bulkActions() {
    const selectedIds = getSelectedUserIds();
    
    if (selectedIds.length === 0) {
        alert('Please select at least one user.');
        return;
    }
    
    // Implement bulk actions
    alert(`Bulk actions for ${selectedIds.length} selected users will be implemented.`);
}

// Refresh data
function refreshData() {
    location.reload();
}

// Helper function to get selected user IDs
function getSelectedUserIds() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    return Array.from(checkboxes).map(checkbox => checkbox.value);
}
</script>
@endpush