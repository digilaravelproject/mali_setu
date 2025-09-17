@extends('admin.layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Payment Details</h4>
                    <div>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Payments
                        </a>
                        @if($payment->status === 'completed')
                            <button type="button" class="btn btn-warning" onclick="processRefund({{ $payment->id }})">
                                <i class="fas fa-undo"></i> Process Refund
                            </button>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Payment Information -->
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="mb-0">Payment Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Transaction ID:</strong></td>
                                            <td>{{ $payment->transaction_id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Gateway Payment ID:</strong></td>
                                            <td>{{ $payment->gateway_payment_id ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Amount:</strong></td>
                                            <td>
                                                <span class="h5 text-success">₹{{ number_format($payment->amount, 2) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Currency:</strong></td>
                                            <td>{{ strtoupper($payment->currency) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Method:</strong></td>
                                            <td>
                                                <span class="badge badge-info">{{ ucfirst($payment->payment_method ?? 'N/A') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Type:</strong></td>
                                            <td>
                                                <span class="badge badge-secondary">{{ ucfirst($payment->payment_type ?? 'N/A') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($payment->status === 'completed')
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($payment->status === 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($payment->status === 'failed')
                                                    <span class="badge badge-danger">Failed</span>
                                                @elseif($payment->status === 'refunded')
                                                    <span class="badge badge-info">Refunded</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ ucfirst($payment->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $payment->created_at->format('M d, Y H:i:s') }}</td>
                                        </tr>
                                        @if($payment->completed_at)
                                        <tr>
                                            <td><strong>Completed:</strong></td>
                                            <td>{{ $payment->completed_at->format('M d, Y H:i:s') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Information -->
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="mb-0">User Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $payment->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $payment->user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $payment->user->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>User Type:</strong></td>
                                            <td>
                                                <span class="badge badge-primary">{{ ucfirst($payment->user->user_type) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>User ID:</strong></td>
                                            <td>{{ $payment->user->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Actions:</strong></td>
                                            <td>
                                                <a href="{{ route('admin.users.show', $payment->user->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-user"></i> View User
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Business Information (if applicable) -->
                    @if($payment->business)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="mb-0">Business Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Business Name:</strong></td>
                                                    <td>{{ $payment->business->business_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Business Type:</strong></td>
                                                    <td>{{ ucfirst($payment->business->business_type) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Business ID:</strong></td>
                                                    <td>{{ $payment->business->id }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Verification Status:</strong></td>
                                                    <td>
                                                        @if($payment->business->verification_status === 'approved')
                                                            <span class="badge badge-success">Approved</span>
                                                        @elseif($payment->business->verification_status === 'pending')
                                                            <span class="badge badge-warning">Pending</span>
                                                        @else
                                                            <span class="badge badge-danger">Rejected</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Actions:</strong></td>
                                                    <td>
                                                        <a href="{{ route('admin.businesses.show', $payment->business->id) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-building"></i> View Business
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Refund Information (if applicable) -->
                    @if($payment->status === 'refunded')
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-warning">
                                <div class="card-header">
                                    <h5 class="mb-0 text-white">Refund Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Refund Amount:</strong></td>
                                            <td><span class="h6 text-danger">₹{{ number_format($payment->refund_amount ?? 0, 2) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Refund Reason:</strong></td>
                                            <td>{{ $payment->refund_reason ?? 'N/A' }}</td>
                                        </tr>
                                        @if($payment->refunded_at)
                                        <tr>
                                            <td><strong>Refunded At:</strong></td>
                                            <td>{{ $payment->refunded_at->format('M d, Y H:i:s') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Additional Details -->
                    @if($payment->description || $payment->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="mb-0">Additional Details</h5>
                                </div>
                                <div class="card-body">
                                    @if($payment->description)
                                    <div class="mb-3">
                                        <strong>Description:</strong>
                                        <p class="text-muted">{{ $payment->description }}</p>
                                    </div>
                                    @endif
                                    @if($payment->notes)
                                    <div class="mb-3">
                                        <strong>Notes:</strong>
                                        <p class="text-muted">{{ $payment->notes }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Refund</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="refundForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="refund_amount">Refund Amount:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₹</span>
                            </div>
                            <input type="number" class="form-control" id="refund_amount" name="refund_amount" 
                                   step="0.01" max="{{ $payment->amount }}" value="{{ $payment->amount }}" required>
                        </div>
                        <small class="form-text text-muted">Maximum refund amount: ₹{{ number_format($payment->amount, 2) }}</small>
                    </div>
                    <div class="form-group">
                        <label for="refund_reason">Reason for Refund:</label>
                        <textarea class="form-control" id="refund_reason" name="refund_reason" 
                                  rows="4" required placeholder="Please provide a reason for the refund..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Process Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function processRefund(paymentId) {
    $('#refundForm').attr('action', `/admin/payments/${paymentId}/refund`);
    $('#refundModal').modal('show');
}

$('#refundForm').on('submit', function(e) {
    e.preventDefault();
    
    if (confirm('Are you sure you want to process this refund? This action cannot be undone.')) {
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#refundModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error processing refund. Please try again.');
            }
        });
    }
});
</script>
@endsection