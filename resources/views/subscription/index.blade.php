@extends('layouts.app')

@section('title', 'Subscription History — Mali Setu')

@section('content')
<div class="container-fluid py-2">
    <!-- Welcome Header -->
    <div class="welcome-banner mb-4">
        <h1 class="fw-bold mb-2">My Subscriptions & Payments</h1>
        <p class="lead mb-0 text-white-50">Audit and view all your active services, plan receipts, and community support histories.</p>
    </div>

    <!-- Filter Card -->
    <div class="glass-card mb-4">
        <form method="GET" action="{{ route('subscriptions.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="status" class="form-label">Payment Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="purpose" class="form-label">Plan / Purpose</label>
                <select name="purpose" id="purpose" class="form-select">
                    <option value="all" {{ request('purpose') == 'all' ? 'selected' : '' }}>All Plans</option>
                    <option value="business_registration" {{ request('purpose') == 'business_registration' ? 'selected' : '' }}>Business Registration</option>
                    <option value="matrimony_profile" {{ request('purpose') == 'matrimony_profile' ? 'selected' : '' }}>Matrimony Profile</option>
                    <option value="donation" {{ request('purpose') == 'donation' ? 'selected' : '' }}>Donations</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_range" class="form-label">Timeframe</label>
                <select name="date_range" id="date_range" class="form-select">
                    <option value="all" {{ request('date_range') == 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="30_days" {{ request('date_range') == '30_days' ? 'selected' : '' }}>Past 30 Days</option>
                    <option value="6_months" {{ request('date_range') == '6_months' ? 'selected' : '' }}>Past 6 Months</option>
                    <option value="12_months" {{ request('date_range') == '12_months' ? 'selected' : '' }}>Past 12 Months</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold">
                    <i class="fa-solid fa-filter me-2"></i> Apply Filters
                </button>
                <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary py-2.5 rounded-3 fw-bold">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Listings -->
    <div class="glass-card p-0 overflow-hidden shadow-sm">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between bg-white bg-opacity-50">
            <h5 class="fw-bold mb-0 text-dark">Transaction Ledger</h5>
            <span class="badge bg-primary py-2 px-3 rounded-pill fw-semibold">{{ $transactions->total() }} Total Record(s)</span>
        </div>

        @if($transactions->isEmpty())
            <div class="text-center py-5 my-3">
                <div class="metric-icon mx-auto mb-3">
                    <i class="fa-solid fa-receipt fs-3"></i>
                </div>
                <h4 class="fw-bold text-dark">No Transactions Found</h4>
                <p class="text-muted mb-0">We couldn't find any subscription or payment history matching your filters.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 900px;">
                    <thead class="table-light text-uppercase fs-7 fw-bold text-secondary">
                        <tr>
                            <th class="ps-4 py-3">Date</th>
                            <th class="py-3">Transaction ID / Reference</th>
                            <th class="py-3">Purpose / Item</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="pe-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $tx)
                            <tr style="transition: background-color 0.2s;">
                                <td class="ps-4 fw-semibold text-dark">
                                    {{ $tx->created_at->format('M d, Y') }}
                                    <span class="d-block small text-muted font-monospace">{{ $tx->created_at->format('H:i A') }}</span>
                                </td>
                                <td class="text-secondary font-monospace">
                                    #{{ $tx->id }}
                                    <span class="d-block small text-muted font-monospace">{{ $tx->razorpay_order_id ?? 'Direct Reference' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">
                                        @if($tx->purpose === 'business_registration')
                                            <i class="fa-solid fa-briefcase text-primary me-2"></i> Business Premium Plan
                                        @elseif($tx->purpose === 'matrimony_profile')
                                            <i class="fa-solid fa-heart text-danger me-2"></i> Matrimony Premium Plan
                                        @else
                                            <i class="fa-solid fa-hand-holding-dollar text-success me-2"></i> Community Donation
                                        @endif
                                    </span>
                                    @if($tx->subscription_period)
                                        <span class="d-block small text-muted mt-0.5">Duration: {{ $tx->subscription_period }} Month(s)</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-dark fs-6">
                                    {{ $tx->currency }} {{ number_format($tx->amount, 2) }}
                                </td>
                                <td class="text-center">
                                    @if($tx->status === 'completed')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 py-2 px-3 rounded-pill fw-bold">
                                            <i class="fa-solid fa-check-circle me-1"></i> Completed
                                        </span>
                                    @elseif($tx->status === 'pending')
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 py-2 px-3 rounded-pill fw-bold">
                                            <i class="fa-solid fa-clock me-1"></i> Pending
                                        </span>
                                    @elseif($tx->status === 'failed')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 py-2 px-3 rounded-pill fw-bold">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> Failed
                                        </span>
                                    @elseif($tx->status === 'refunded')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 py-2 px-3 rounded-pill fw-bold">
                                            <i class="fa-solid fa-arrow-rotate-left me-1"></i> Refunded
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('subscriptions.show', $tx->id) }}" class="btn btn-sm btn-outline-primary rounded-2 py-1.5 px-3 fw-semibold">
                                            <i class="fa-solid fa-circle-info me-1"></i> View Details
                                        </a>
                                        @if($tx->status === 'completed')
                                            <a href="{{ route('subscriptions.invoice', $tx->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-2 py-1.5 px-2">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Grid -->
            @if($transactions->hasPages())
                <div class="p-4 border-top d-flex align-items-center justify-content-between bg-light bg-opacity-50">
                    <span class="small text-muted">Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} records</span>
                    <div>
                        {{ $transactions->links() }}
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
