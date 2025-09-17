@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <h3 class="stats-number">{{ $stats['total_users'] ?? 0 }}</h3>
                    <p class="stats-label">Total Users</p>
                    <span class="stats-change positive">
                        <i class="fas fa-arrow-up"></i> +{{ $stats['new_users_today'] ?? 0 }} today
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon business">
                    <i class="fas fa-building"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <h3 class="stats-number">{{ $stats['total_businesses'] ?? 0 }}</h3>
                    <p class="stats-label">Businesses</p>
                    <span class="stats-change positive">
                        <i class="fas fa-arrow-up"></i> +{{ $stats['new_businesses_today'] ?? 0 }} today
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon matrimony">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <h3 class="stats-number">{{ $stats['total_matrimony_profiles'] ?? 0 }}</h3>
                    <p class="stats-label">Matrimony Profiles</p>
                    <span class="stats-change positive">
                        <i class="fas fa-arrow-up"></i> +{{ $stats['new_matrimony_today'] ?? 0 }} today
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon payments">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="ms-3 flex-grow-1">
                    <h3 class="stats-number">₹{{ number_format($stats['total_revenue'] ?? 0) }}</h3>
                    <p class="stats-label">Total Revenue</p>
                    <span class="stats-change positive">
                        <i class="fas fa-arrow-up"></i> ₹{{ number_format($stats['revenue_today'] ?? 0) }} today
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Chart -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Revenue Overview
                </h5>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="revenueFilter" id="revenue7days" checked>
                    <label class="btn btn-outline-primary btn-sm" for="revenue7days">7 Days</label>
                    
                    <input type="radio" class="btn-check" name="revenueFilter" id="revenue30days">
                    <label class="btn btn-outline-primary btn-sm" for="revenue30days">30 Days</label>
                    
                    <input type="radio" class="btn-check" name="revenueFilter" id="revenue90days">
                    <label class="btn btn-outline-primary btn-sm" for="revenue90days">90 Days</label>
                </div>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Quick Stats
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Pending Verifications</span>
                    <span class="badge bg-warning">{{ $stats['pending_verifications'] ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Active Subscriptions</span>
                    <span class="badge bg-success">{{ $stats['active_subscriptions'] ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Pending Payments</span>
                    <span class="badge bg-danger">{{ $stats['pending_payments'] ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Chat Messages Today</span>
                    <span class="badge bg-info">{{ $stats['messages_today'] ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Connection Requests</span>
                    <span class="badge bg-primary">{{ $stats['connection_requests'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activities -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Recent Activities
                </h5>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            @forelse($recent_activities ?? [] as $activity)
                            <tr>
                                <td class="border-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="activity-icon me-3">
                                            @if($activity['type'] == 'user_registration')
                                                <i class="fas fa-user-plus text-success"></i>
                                            @elseif($activity['type'] == 'business_registration')
                                                <i class="fas fa-building text-primary"></i>
                                            @elseif($activity['type'] == 'payment')
                                                <i class="fas fa-credit-card text-warning"></i>
                                            @elseif($activity['type'] == 'matrimony')
                                                <i class="fas fa-heart text-danger"></i>
                                            @else
                                                <i class="fas fa-info-circle text-info"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ $activity['title'] }}</div>
                                            <small class="text-muted">{{ $activity['description'] }}</small>
                                        </div>
                                        <div class="text-muted small">
                                            {{ $activity['time'] }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="border-0 py-4 text-center text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <div>No recent activities</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending Verifications -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-certificate me-2"></i>
                    Pending Verifications
                </h5>
                <a href="{{ route('admin.users.verification.pending') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($pending_verifications ?? [] as $verification)
                    <div class="list-group-item border-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <img src="{{ $verification['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($verification['name']) }}" 
                                     class="rounded-circle" width="40" height="40" alt="Avatar">
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $verification['name'] }}</div>
                                <small class="text-muted">{{ $verification['email'] }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning">Pending</span>
                                <div class="small text-muted mt-1">{{ $verification['submitted_at'] }}</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item border-0 text-center py-4">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <div class="text-muted">All verifications are up to date</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chart_data['labels'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($chart_data['revenue'] ?? [1200, 1900, 3000, 5000, 2000, 3000, 4500]) !!},
            borderColor: '#b61315',
            backgroundColor: 'rgba(182, 19, 21, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#b61315',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.1)'
                },
                ticks: {
                    callback: function(value) {
                        return '₹' + value.toLocaleString();
                    }
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        elements: {
            point: {
                hoverBackgroundColor: '#b61315'
            }
        }
    }
});

// Revenue filter buttons
document.querySelectorAll('input[name="revenueFilter"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Here you would typically make an AJAX call to get new data
        console.log('Filter changed to:', this.id);
        // For demo purposes, we'll just update the chart with sample data
        updateRevenueChart(this.id);
    });
});

function updateRevenueChart(filter) {
    let newData, newLabels;
    
    switch(filter) {
        case 'revenue7days':
            newLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            newData = [1200, 1900, 3000, 5000, 2000, 3000, 4500];
            break;
        case 'revenue30days':
            newLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            newData = [15000, 22000, 18000, 25000];
            break;
        case 'revenue90days':
            newLabels = ['Month 1', 'Month 2', 'Month 3'];
            newData = [80000, 95000, 110000];
            break;
    }
    
    revenueChart.data.labels = newLabels;
    revenueChart.data.datasets[0].data = newData;
    revenueChart.update();
}
</script>
@endpush