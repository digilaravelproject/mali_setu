@extends('admin.layouts.app')

@section('title', 'Business Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Business Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.businesses.create') }}" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-plus"></i> Add Business
                        </a>
                        <a href="{{ route('admin.businesses.verification') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-clock"></i> Pending Verification ({{ $stats['pending'] }})
                        </a>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $stats['total'] }}</h3>
                                    <p>Total Businesses</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $stats['approved'] }}</h3>
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
                                    <h3>{{ $stats['pending'] }}</h3>
                                    <p>Pending</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $stats['rejected'] }}</h3>
                                    <p>Rejected</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form method="GET" action="{{ route('admin.businesses.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <select name="verification_status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('verification_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('verification_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('verification_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="business_type" class="form-select">
                                        <option value="">All Types</option>
                                        @foreach($businessTypes as $type)
                                            <option value="{{ $type }}" {{ request('business_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Search businesses..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Businesses Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Business Name</th>
                                    <th>Owner</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($businesses as $business)
                                <tr>
                                    <td>{{ $business->id }}</td>
                                    <td>
                                        <strong>{{ $business->business_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($business->description, 50) }}</small>
                                    </td>
                                    <td>
                                        {{ $business->user->name }}
                                        <br>
                                        <small class="text-muted">{{ $business->user->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($business->business_type) }}</span>
                                    </td>
                                    <td>{{ $business->category->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($business->verification_status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($business->verification_status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($business->completed_business_registrations_count > 0)
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($business->pending_business_registrations_count > 0)
                                            <span class="badge bg-warning">Payment Pending</span>
                                        @else
                                            <span class="badge bg-secondary">Not Paid</span>
                                        @endif
                                    </td>
                                    <td>{{ $business->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.businesses.show', $business->id) }}" class="btn btn-sm btn-info" title="View business">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.businesses.edit', $business->id) }}" class="btn btn-sm btn-warning text-white" title="Edit business">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($business->verification_status === 'approved')
                                                <a href="{{ route('admin.businesses.jobs', $business->id) }}" class="btn btn-sm btn-primary" title="View jobs for this business">
                                                    <i class="fas fa-briefcase"></i>
                                                    <?php /*@if($business->job_postings_count > 0)
                                                        <span class="badge bg-light text-dark ms-1">{{ $business->job_postings_count }}</span>
                                                    @endif*/ ?>
                                                </a>
                                            @endif
                                            @if($business->verification_status == 'pending')
                                                @if($business->completed_business_registrations_count > 0)
                                                    <!-- Approve Business Form -->
                                                    <form action="{{ route('admin.businesses.approve', $business->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to approve this business?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-secondary" disabled title="Cannot approve until payment is complete">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal"
                                                    data-action="{{ route('admin.businesses.reject', $business->id) }}"
                                                    data-business-name="{{ $business->business_name }}"
                                                    title="Reject business"
                                                    onclick="setRejectForm(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No businesses found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $businesses->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
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
                <h5 class="modal-title">Approve Business</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this business?</p>
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
                <h5 class="modal-title">Reject Business</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p id="rejectModalMessage">Are you sure you want to reject this business? This action will set the business status to Rejected.</p>
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

@section('scripts')
<script>
    function setRejectForm(button) {
        var action = button.dataset.action;
        var businessName = button.dataset.businessName;
        var form = document.getElementById('rejectForm');
        var message = document.getElementById('rejectModalMessage');
        var reasonField = document.getElementById('rejection_reason');

        if (form) {
            form.action = action || form.action;
        }
        if (message) {
            message.textContent = 'Are you sure you want to reject "' + businessName + '"? This will set the business status to Rejected.';
        }
        if (reasonField) {
            reasonField.value = '';
        }
    }
</script>
@endsection