@extends('admin.layouts.app')

@section('title', 'Matrimony Subscriptions')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Matrimony Subscriptions</h3>
            <div>
                <a href="{{ route('admin.payments.exportMatrimony') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-download"></i> Export CSV
                </a>
                <a href="{{ route('admin.payments.exportMatrimonyXlsx') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($transactions->count())
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Plan</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $t)
                            <tr>
                                <td>{{ $t->id }}</td>
                                <td>{{ $t->user->name ?? 'N/A' }}<br><small>{{ $t->user->email ?? '' }}</small></td>
                                <td>₹{{ number_format($t->amount,2) }}</td>
                                <td>{{ ucfirst($t->status) }}</td>
                                <td>{{ $t->metadata['plan_id'] ?? '-' }}</td>
                                <td>{{ $t->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('admin.payments.transaction.show', $t->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">{{ $transactions->links() }}</div>
            @else
                <p class="text-center">No matrimony subscription transactions found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
