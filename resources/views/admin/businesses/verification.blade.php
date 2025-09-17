@extends('admin.layouts.app')

@section('title', 'Business Verification')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Pending Business Verifications</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.businesses.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Businesses
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['pending_count'] ?? 0 }}</h4>
                                            <p class="mb-0">Pending Verifications</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['approved_today'] ?? 0 }}</h4>
                                            <p class="mb-0">Approved Today</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['rejected_today'] ?? 0 }}</h4>
                                            <p class="mb-0">Rejected Today</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-times fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Form -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Search by business name or owner..." 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('admin.businesses.verification') }}" class="btn btn-secondary">
                                    <i class="fas fa-refresh"></i> Clear Filters
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Pending Businesses Table -->
                    @if($pendingBusinesses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Business Name</th>
                                        <th>Owner</th>
                                        <th>Email</th>
                                        <th>Business Type</th>
                                        <th>Category</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingBusinesses as $business)
                                    <tr>
                                        <td>
                                            <strong>{{ $business->business_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $business->business_description }}</small>
                                        </td>
                                        <td>{{ $business->user->name }}</td>
                                        <td>{{ $business->user->email }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ ucfirst($business->business_type) }}</span>
                                        </td>
                                        <td>
                                            @if($business->category)
                                                {{ $business->category->name }}
                                            @else
                                                <span class="text-muted">No Category</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $business->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $business->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.businesses.show', $business->id) }}" 
                                                   class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="approveBusiness({{ $business->id }})" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="rejectBusiness({{ $business->id }})" title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $pendingBusinesses->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Pending Verifications</h5>
                            <p class="text-muted">All business verifications have been processed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Business Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Business</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to approve this business?</p>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3"
                                  placeholder="Add any notes about the approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Business</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Business Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Business</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required
                                  placeholder="Please provide a detailed reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Business</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function approveBusiness(businessId) {
    const form = document.getElementById('approveForm');
    form.action = `/admin/businesses/${businessId}/approve`;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function rejectBusiness(businessId) {
    const form = document.getElementById('rejectForm');
    form.action = `/admin/businesses/${businessId}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush