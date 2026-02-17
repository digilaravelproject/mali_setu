@extends('admin.layouts.app')

@section('title', 'Payment Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Payment Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.payments.analytics') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> Analytics
                        </a>
                        <a href="{{ route('admin.payments.export') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                        <a href="{{ route('admin.payments.exportXlsx') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['total'] ?? 0 }}</h4>
                                            <p class="mb-0">Total Payments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-credit-card fa-2x"></i>
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
                                            <h4>₹{{ number_format($stats['total_amount'] ?? 0, 2) }}</h4>
                                            <p class="mb-0">Total Amount</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-rupee-sign fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['pending'] ?? 0 }}</h4>
                                            <p class="mb-0">Pending</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
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
                                            <h4>{{ $stats['failed'] ?? 0 }}</h4>
                                            <p class="mb-0">Failed</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-times fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="payment_method" class="form-select">
                                    <option value="">All Methods</option>
                                    <option value="razorpay" {{ request('payment_method') == 'razorpay' ? 'selected' : '' }}>Razorpay</option>
                                    <option value="paytm" {{ request('payment_method') == 'paytm' ? 'selected' : '' }}>Paytm</option>
                                    <option value="upi" {{ request('payment_method') == 'upi' ? 'selected' : '' }}>UPI</option>
                                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Search by transaction ID, user email..." 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary w-100">
                                    <i class="fas fa-refresh"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Payments Table -->
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                    <tr>
                                        <td>
                                            <strong>{{ $payment->transaction_id ?? 'N/A' }}</strong>
                                            @if($payment->gateway_payment_id)
                                                <br><small class="text-muted">{{ $payment->gateway_payment_id }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $payment->user->name }}
                                            <br>
                                            <small class="text-muted">{{ $payment->user->email }}</small>
                                        </td>
                                        <td>
                                            <strong>₹{{ number_format($payment->amount, 2) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $payment->currency }}</small>
                                        </td>
                                        <td>
                                            @if($payment->payment_method)
                                                <span class="badge badge-info">{{ ucfirst($payment->payment_method) }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                            @if($payment->payment_gateway)
                                                <br><small class="text-muted">{{ ucfirst($payment->payment_gateway) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $payment->purpose ?? 'General' }}
                                            @if($payment->description)
                                                <br><small class="text-muted">{{ Str::limit($payment->description, 30) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->status === 'completed')
                                                <span class="badge badge-success">Completed</span>
                                            @elseif($payment->status === 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($payment->status === 'failed')
                                                <span class="badge badge-danger">Failed</span>
                                            @elseif($payment->status === 'refunded')
                                                <span class="badge badge-info">Refunded</span>
                                            @elseif($payment->status === 'cancelled')
                                                <span class="badge badge-secondary">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $payment->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.payments.show', $payment->id) }}" 
                                                   class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($payment->status === 'completed')
                                                    <button type="button" class="btn btn-sm btn-warning" 
                                                            onclick="refundPayment({{ $payment->id }})" title="Refund">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                @endif
                                                @if($payment->status === 'pending')
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="updateStatus({{ $payment->id }}, 'completed')" title="Mark Completed">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="updateStatus({{ $payment->id }}, 'failed')" title="Mark Failed">
                                                        <i class="fas fa-times"></i>
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
                        <div class="d-flex justify-content-center">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Payments Found</h5>
                            <p class="text-muted">No payment transactions match your current filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Refund Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="refundForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="refund_reason" class="form-label">Refund Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="refund_reason" name="refund_reason" rows="3" required
                                  placeholder="Please provide a reason for the refund..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="refund_amount" class="form-label">Refund Amount</label>
                        <input type="number" class="form-control" id="refund_amount" name="refund_amount" 
                               step="0.01" placeholder="Leave empty for full refund">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Process Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Payment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                <input type="hidden" id="new_status" name="status">
                <div class="modal-body">
                    <p id="statusMessage"></p>
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"
                                  placeholder="Add any notes about this status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function refundPayment(paymentId) {
    const form = document.getElementById('refundForm');
    form.action = `/admin/payments/${paymentId}/refund`;
    new bootstrap.Modal(document.getElementById('refundModal')).show();
}

function updateStatus(paymentId, status) {
    const form = document.getElementById('statusForm');
    const statusInput = document.getElementById('new_status');
    const message = document.getElementById('statusMessage');
    
    form.action = `/admin/payments/${paymentId}/update-status`;
    statusInput.value = status;
    
    if (status === 'completed') {
        message.textContent = 'Are you sure you want to mark this payment as completed?';
    } else if (status === 'failed') {
        message.textContent = 'Are you sure you want to mark this payment as failed?';
    }
    
    new bootstrap.Modal(document.getElementById('statusModal')).show();
}
</script>
@endpush