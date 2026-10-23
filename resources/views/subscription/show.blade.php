@extends('layouts.app')

@section('title', 'Payment Audit Detail — Mali Setu')

@section('content')
<div class="container-fluid py-2">
    <div class="mb-4">
        <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary rounded-3 py-2 px-3 fw-bold btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Ledger
        </a>
    </div>

    <div class="row">
        <!-- Main Audit Details -->
        <div class="col-lg-8">
            <div class="glass-card shadow-sm mb-4">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                    <div>
                        <span class="text-uppercase small text-muted fw-bold">Transaction Reference</span>
                        <h3 class="fw-bold text-dark mb-0 font-monospace">#{{ $transaction->id }}</h3>
                    </div>
                    <div>
                        @if($transaction->status === 'completed')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 py-2.5 px-4 rounded-pill fw-bold fs-7">
                                <i class="fa-solid fa-circle-check me-1"></i> Completed
                            </span>
                        @elseif($transaction->status === 'pending')
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 py-2.5 px-4 rounded-pill fw-bold fs-7">
                                <i class="fa-solid fa-clock me-1"></i> Pending
                            </span>
                        @elseif($transaction->status === 'failed')
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 py-2.5 px-4 rounded-pill fw-bold fs-7">
                                <i class="fa-solid fa-circle-xmark me-1"></i> Failed
                            </span>
                        @elseif($transaction->status === 'refunded')
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 py-2.5 px-4 rounded-pill fw-bold fs-7">
                                <i class="fa-solid fa-arrow-rotate-left me-1"></i> Refunded
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Summary Grid -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-sm-12">
                        <div class="p-3 rounded-4 bg-light bg-opacity-50 border">
                            <span class="small text-muted d-block mb-1 font-semibold">Payment Purpose</span>
                            <span class="fw-bold text-dark fs-5">
                                @if($transaction->purpose === 'business_registration')
                                    <i class="fa-solid fa-briefcase text-primary me-2"></i> Business Registration
                                @elseif($transaction->purpose === 'matrimony_profile')
                                    <i class="fa-solid fa-heart text-danger me-2"></i> Matrimony Profile
                                @else
                                    <i class="fa-solid fa-hand-holding-dollar text-success me-2"></i> Donation Support
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="p-3 rounded-4 bg-light bg-opacity-50 border">
                            <span class="small text-muted d-block mb-1 font-semibold">Amount Captured</span>
                            <span class="fw-bold text-dark fs-5">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Transaction Details Block -->
                <h5 class="fw-bold text-dark mb-3">Audit Logs & Technical Spec</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <tbody>
                            <tr>
                                <td class="bg-light fw-semibold text-secondary w-30">Transaction Initiated</td>
                                <td class="text-dark font-semibold">{{ $transaction->created_at->format('M d, Y — h:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="bg-light fw-semibold text-secondary">Last Sync Status Update</td>
                                <td class="text-dark font-semibold">{{ $transaction->updated_at->format('M d, Y — h:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="bg-light fw-semibold text-secondary">Razorpay Order Reference</td>
                                <td class="text-dark font-monospace">{{ $transaction->razorpay_order_id ?? 'N/A (Direct payment/donation)' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-light fw-semibold text-secondary">Razorpay Payment Reference</td>
                                <td class="text-dark font-monospace">
                                    {{ $transaction->razorpay_payment_id ?? $payment->payment_id ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-light fw-semibold text-secondary">Billing Mode / Method</td>
                                <td class="text-dark font-semibold text-capitalize">
                                    {{ $payment->payment_method ?? 'Razorpay Gateway Integration' }}
                                </td>
                            </tr>
                            @if($transaction->subscription_period)
                                <tr>
                                    <td class="bg-light fw-semibold text-secondary">Subscription Terms</td>
                                    <td class="text-dark font-semibold">
                                        {{ $transaction->subscription_period }} Month(s) Premium access terms.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Extra Custom Fields / Metadata -->
                @php
                    $meta = null;
                    if ($transaction->metadata) {
                        $meta = is_array($transaction->metadata) ? $transaction->metadata : json_decode($transaction->metadata, true);
                    }
                    if (!$meta && isset($transaction->meta)) {
                        $meta = is_array($transaction->meta) ? $transaction->meta : json_decode($transaction->meta, true);
                    }
                @endphp

                @if($meta && count($meta) > 0)
                    <h5 class="fw-bold text-dark mb-3">Additional Billing Properties</h5>
                    <div class="row g-3">
                        @foreach($meta as $key => $val)
                            <div class="col-md-6 col-sm-12">
                                <div class="p-2.5 rounded-3 bg-light bg-opacity-25 border text-truncate">
                                    <span class="small text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;">{{ str_replace('_', ' ', $key) }}</span>
                                    <span class="font-semibold text-dark">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Actions & Validity Check -->
        <div class="col-lg-4">
            <div class="glass-card shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-3">Billing Status Summary</h5>
                <div class="d-grid gap-3 mb-4">
                    <!-- Validity status -->
                    @if($transaction->status === 'completed' && $transaction->subscription_period)
                        <div class="p-3.5 rounded-4 text-center {{ $statusActive ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-20' : 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20' }}">
                            <div class="fs-4 mb-1">
                                <i class="fa-solid {{ $statusActive ? 'fa-shield-halved' : 'fa-hourglass-end' }}"></i>
                            </div>
                            <h6 class="fw-bold mb-1">{{ $statusActive ? 'Active Subscription' : 'Expired Plan' }}</h6>
                            <p class="small mb-0 opacity-75">
                                @if($startDate)
                                    Term: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                                @endif
                            </p>
                        </div>
                    @endif

                    <!-- Invoice Action Buttons -->
                    @if($transaction->status === 'completed')
                        <a href="{{ route('subscriptions.invoice', $transaction->id) }}" target="_blank" class="btn btn-primary py-3 rounded-3 fw-bold">
                            <i class="fa-solid fa-print me-2"></i> Print Official Receipt
                        </a>
                    @endif
                </div>

                <div class="border-top pt-3">
                    <h6 class="fw-bold text-dark small mb-2"><i class="fa-solid fa-circle-info me-1 text-primary"></i> Need support with this order?</h6>
                    <p class="small text-muted mb-0">For issues regarding refunds, failed sync processing, or plan upgrades, please get in touch with support at <a href="mailto:support@malisetu.org" class="text-primary font-semibold text-decoration-none">support@malisetu.org</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
