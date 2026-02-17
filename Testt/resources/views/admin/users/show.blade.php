@extends('admin.layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">User Details - {{ $user->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- User Basic Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $user->phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>User Type:</strong></td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($user->user_type) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Verification Status:</strong></td>
                                            <td>
                                                @if($user->caste_verification_status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($user->caste_verification_status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email Verified:</strong></td>
                                            <td>
                                                @if($user->email_verified_at)
                                                    <span class="badge bg-success">Yes</span>
                                                    <small class="text-muted d-block">{{ $user->email_verified_at->format('M d, Y H:i') }}</small>
                                                @else
                                                    <span class="badge bg-danger">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Joined:</strong></td>
                                            <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ $user->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Caste Certificate Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Caste Certificate</h5>
                                </div>
                                <div class="card-body">
                                    @if($user->casteCertificate)
                                        <table class="table table-borderless">

                                            <tr>
                                                <td><strong>Uploaded:</strong></td>
                                                <td>{{ $user->casteCertificate->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            @if($user->casteCertificate->verified_at)
                                            <tr>
                                                <td><strong>Verified At:</strong></td>
                                                <td>{{ $user->casteCertificate->verified_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            @endif
                                            @if($user->casteCertificate->admin_notes)
                                            <tr>
                                                <td><strong>Admin Notes:</strong></td>
                                                <td>{{ $user->casteCertificate->admin_notes }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td><strong>Certificate:</strong></td>
                                                <td>
                                                    <a href="{{ Storage::url($user->casteCertificate->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i> View Certificate
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        @if($user->caste_verification_status == 'pending')
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-success btn-sm" onclick="verifyCertificate({{ $user->id }})">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="rejectCertificate({{ $user->id }})">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>
                                        @endif
                                    @else
                                        <p class="text-muted">No caste certificate uploaded</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Information based on user type -->
                    <div class="row mt-4">
                        @if($user->business)
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Business Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Business Name:</strong></td>
                                                    <td>{{ $user->business->business_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Business Type:</strong></td>
                                                    <td>{{ ucfirst($user->business->business_type) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Category:</strong></td>
                                                    <td>{{ $user->business->category->name ?? 'N/A' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Verification Status:</strong></td>
                                                    <td>
                                                        @if($user->business->verification_status == 'approved')
                                                            <span class="badge bg-success">Approved</span>
                                                        @elseif($user->business->verification_status == 'pending')
                                                            <span class="badge bg-warning">Pending</span>
                                                        @else
                                                            <span class="badge bg-danger">Rejected</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Subscription:</strong></td>
                                                    <td>
                                                        <span class="badge bg-info">{{ ucfirst($user->business->subscription_status) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Created:</strong></td>
                                                    <td>{{ $user->business->created_at->format('M d, Y') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.businesses.show', $user->business->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> View Business Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($user->matrimonyProfile)
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Matrimony Profile</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Age:</strong></td>
                                                    <td>{{ $user->matrimonyProfile->age ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Height:</strong></td>
                                                    <td>{{ $user->matrimonyProfile->height ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Education:</strong></td>
                                                    <td>
                                                        @if($user->matrimonyProfile->education_details && is_array($user->matrimonyProfile->education_details))
                                                            {{ $user->matrimonyProfile->education_details['highest_qualification'] ?? 'N/A' }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Approval Status:</strong></td>
                                                    <td>
                                                        @if($user->matrimonyProfile->approval_status == 'approved')
                                                            <span class="badge bg-success">Approved</span>
                                                        @elseif($user->matrimonyProfile->approval_status == 'pending')
                                                            <span class="badge bg-warning">Pending</span>
                                                        @else
                                                            <span class="badge bg-danger">Rejected</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Profile Expires:</strong></td>
                                                    <td>{{ $user->matrimonyProfile->profile_expires_at ? $user->matrimonyProfile->profile_expires_at->format('M d, Y') : 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Created:</strong></td>
                                                    <td>{{ $user->matrimonyProfile->created_at->format('M d, Y') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.matrimony.show', $user->matrimonyProfile->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> View Profile Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> Edit User
                                        </a>
                                        @if($user->caste_verification_status == 'pending')
                                            <button type="button" class="btn btn-success" onclick="verifyUser({{ $user->id }})">
                                                <i class="fas fa-check"></i> Verify User
                                            </button>
                                            <button type="button" class="btn btn-danger" onclick="rejectUser({{ $user->id }})">
                                                <i class="fas fa-times"></i> Reject User
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-warning" onclick="suspendUser({{ $user->id }})">
                                            <i class="fas fa-ban"></i> Suspend User
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="deleteUser({{ $user->id }})">
                                            <i class="fas fa-trash"></i> Delete User
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verify User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to verify this user?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="verifyForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">Verify</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function verifyUser(userId) {
    document.getElementById('verifyForm').action = `/admin/users/${userId}/verify`;
    new bootstrap.Modal(document.getElementById('verifyModal')).show();
}

function rejectUser(userId) {
    if(confirm('Are you sure you want to reject this user?')) {
        fetch(`/admin/users/${userId}/reject-certificate`, {
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
            alert('An error occurred while rejecting the user.');
        });
    }
}

function suspendUser(userId) {
    if(confirm('Are you sure you want to suspend this user?')) {
        fetch(`/admin/users/${userId}/suspend`, {
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
            alert('An error occurred while suspending the user.');
        });
    }
}

function deleteUser(userId) {
    if(confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch(`/admin/users/${userId}`, {
            method: 'DELETE',
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
                window.location.href = '{{ route("admin.users.index") }}';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
             console.error('Error:', error);
             alert('An error occurred while deleting the user.');
         });
     }
}

function verifyCertificate(userId) {
    if(confirm('Are you sure you want to approve this certificate?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}/approve-certificate`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectCertificate(userId) {
    const reason = prompt('Please enter rejection reason:');
    if(reason) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}/reject-certificate`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const reasonField = document.createElement('input');
        reasonField.type = 'hidden';
        reasonField.name = 'admin_notes';
        reasonField.value = reason;
        form.appendChild(reasonField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection