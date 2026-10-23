@extends('admin.layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Transaction #{{ $transaction->id }}</h3>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">User</dt>
                <dd class="col-sm-9">{{ $transaction->user->name ?? 'N/A' }} &lt;{{ $transaction->user->email ?? '' }}&gt;</dd>

                <dt class="col-sm-3">Amount</dt>
                <dd class="col-sm-9">₹{{ number_format($transaction->amount,2) }}</dd>

                <dt class="col-sm-3">Purpose</dt>
                <dd class="col-sm-9">{{ $transaction->purpose }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{{ ucfirst($transaction->status) }}</dd>

                <dt class="col-sm-3">Razorpay Order ID</dt>
                <dd class="col-sm-9">{{ $transaction->razorpay_order_id }}</dd>

                <dt class="col-sm-3">Razorpay Payment ID</dt>
                <dd class="col-sm-9">{{ $transaction->razorpay_payment_id }}</dd>

                <dt class="col-sm-3">Metadata</dt>
                <dd class="col-sm-9"><pre>{{ json_encode($transaction->metadata, JSON_PRETTY_PRINT) }}</pre></dd>

                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</dd>
            </dl>

            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
