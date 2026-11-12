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
                                        <th>Payment</th>
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
                                            <span class="badge badge-info text-black">{{ ucfirst($business->business_type) }}</span>
                                            <br>
                                            @if($business->verification_status === 'approved')
                                                <span class="badge bg-success text-white mt-1">Approved</span>
                                            @elseif($business->verification_status === 'rejected')
                                                <span class="badge bg-danger text-white mt-1">Rejected</span>
                                            @else
                                                <span class="badge bg-warning text-dark mt-1">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($business->category)
                                                {{ $business->category->name }}
                                            @else
                                                <span class="text-muted">No Category</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($business->business_payment_status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($business->business_payment_status === 'pending')
                                                <span class="badge bg-warning">Payment Pending</span>
                                            @else
                                                <span class="badge bg-secondary">Not Paid</span>
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
                                                <a href="{{ route('admin.businesses.edit', $business->id) }}" class="btn btn-sm btn-warning text-white" title="Edit business">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($business->verification_status === 'approved')
                                                    <button type="button" class="btn btn-sm btn-success" disabled title="Approved">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @elseif($business->business_payment_status === 'paid')
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="approveBusiness({{ $business->id }})" title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-secondary" title="Payment pending. Cannot approve." disabled>
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                @if($business->businessRegistrationPayments->isEmpty())
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                            onclick="approveBusinessWithPayment({{ $business->id }}, @js($business->business_name))"
                                                            title="Approve with payment">
                                                        <i class="fas fa-money-check-alt"></i>
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="rejectBusiness({{ $business->id }})" title="Reject"
                                                        {{ $business->verification_status === 'rejected' ? 'disabled' : '' }}>
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                
                                                <!-- Delete Form -->
                                                <form action="{{ route('admin.businesses.destroy', $business->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete business"
                                                            onclick="return confirm('Are you sure you want to delete this business? All business listings and associated payment data will be permanently deleted.');">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $pendingBusinesses->appends(request()->query())->links() }}
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

<div class="modal fade" id="approveWithPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve with Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveWithPaymentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p id="approveWithPaymentMessage"></p>
                    <div class="alert alert-warning mb-3">
                        This will add a completed payment entry and fully approve the business. The subscription expiry date will be calculated from the selected plan.
                    </div>
                    <label for="businessPlanId" class="form-label">Subscription plan</label>
                    <select id="businessPlanId" name="plan_id" class="form-select" required>
                        <option value="">Select a plan</option>
                        @foreach($businessPlans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->company_type }} — {{ $plan->duration_years }} year(s) — ₹{{ number_format($plan->price, 2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Payment &amp; Approve</button>
                </div>
            </form>
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

function approveBusinessWithPayment(businessId, businessName) {
    document.getElementById('approveWithPaymentForm').action = `/admin/businesses/${businessId}/approve-with-payment`;
    document.getElementById('approveWithPaymentMessage').textContent = `Do you want to add a payment entry and fully approve ${businessName}?`;
    new bootstrap.Modal(document.getElementById('approveWithPaymentModal')).show();
}
</script>
@endpush
