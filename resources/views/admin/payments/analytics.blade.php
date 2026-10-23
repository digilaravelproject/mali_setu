@extends('admin.layouts.app')

@section('title', 'Payment Analytics')
@section('page-title', 'Payment Analytics')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Revenue Over Time (Last {{ $period }} days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">Summary</div>
                <div class="card-body">
                    <p><strong>Total Revenue:</strong> ₹{{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
                    <p><strong>Monthly Growth:</strong> {{ $stats['monthly_growth'] ?? 0 }}%</p>
                    <p><strong>Avg Transaction:</strong> ₹{{ number_format($stats['avg_transaction'] ?? 0, 2) }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Top Paying Users</div>
                <div class="card-body">
                    @if($topUsers && $topUsers->count())
                        <ul class="list-group list-group-flush">
                            @foreach($topUsers as $user)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $user->user->name ?? 'N/A' }}</strong>
                                        <div class="small text-muted">{{ $user->user->email ?? '' }}</div>
                                    </div>
                                    <span>₹{{ number_format($user->total_paid,2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No data</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Payment Types</div>
                <div class="card-body">
                    @if(isset($paymentTypes) && $paymentTypes->count())
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Type</th><th>Count</th><th>Total</th></tr>
                            </thead>
                            <tbody>
                                @foreach($paymentTypes as $pt)
                                    <tr>
                                        <td>{{ $pt->payment_type }}</td>
                                        <td>{{ $pt->count }}</td>
                                        <td>₹{{ number_format($pt->total,2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No data</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Recent Transactions</div>
                <div class="card-body">
                    @if(isset($revenueData) && $revenueData->count())
                        <table class="table table-sm">
                            <thead><tr><th>Date</th><th>Amount</th></tr></thead>
                            <tbody>
                                @foreach($revenueData as $row)
                                    <tr>
                                        <td>{{ $row->date }}</td>
                                        <td>₹{{ number_format($row->total,2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No recent transactions</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const revenueLabels = {!! json_encode($revenueData->pluck('date')) !!};
const revenueValues = {!! json_encode($revenueData->pluck('total')) !!};

const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: revenueLabels,
        datasets: [{
            label: 'Revenue',
            data: revenueValues,
            backgroundColor: 'rgba(59,130,246,0.2)',
            borderColor: 'rgba(59,130,246,1)',
            fill: true,
            tension: 0.2
        }]
    },
    options: {
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endpush
