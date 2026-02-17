@extends('admin.layouts.app')

@section('title', 'Analytics')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Analytics Dashboard</h1>
        <div class="d-sm-flex">
            <select class="form-control mr-2" id="periodSelect" onchange="changePeriod()">
                <option value="7" {{ $period == 7 ? 'selected' : '' }}>Last 7 days</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 days</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last 90 days</option>
                <option value="365" {{ $period == 365 ? 'selected' : '' }}>Last year</option>
            </select>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- User Registrations Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">User Registrations Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="userRegistrationsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Trend Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Revenue Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business and Matrimony Charts -->
    <div class="row">
        <!-- Business Registrations Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">Business Registrations</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="businessRegistrationsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matrimony Profiles Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-info">Matrimony Profiles</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="matrimonyProfilesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution Charts -->
    <div class="row">
        <!-- User Type Distribution -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">User Type Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="userTypeChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($userTypeDistribution as $type)
                            <span class="mr-2">
                                <i class="fas fa-circle" style="color: {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'][$loop->index % 4] }}"></i>
                                {{ ucfirst($type->user_type) }}: {{ $type->count }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Distribution -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Payment Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="paymentDistributionChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($paymentDistribution as $payment)
                            <span class="mr-2">
                                <i class="fas fa-circle" style="color: {{ ['#e74a3b', '#f39c12', '#2ecc71'][$loop->index % 3] }}"></i>
                                {{ ucfirst(str_replace('_', ' ', $payment->purpose)) }}: ₹{{ number_format($payment->total, 2) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Summary Statistics (Last {{ $period }} days)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h4 class="text-primary">{{ $userRegistrations->sum('count') }}</h4>
                            <p class="text-muted">New Users</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-success">₹{{ number_format($revenueTrends->sum('total'), 2) }}</h4>
                            <p class="text-muted">Revenue Generated</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-warning">{{ $businessRegistrations->sum('count') }}</h4>
                            <p class="text-muted">New Businesses</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-info">{{ $matrimonyProfiles->sum('count') }}</h4>
                            <p class="text-muted">Matrimony Profiles</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// User Registrations Chart
const userRegistrationsCtx = document.getElementById('userRegistrationsChart').getContext('2d');
const userRegistrationsChart = new Chart(userRegistrationsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($userRegistrations->pluck('date')->map(function($date) { return date('M d', strtotime($date)); })) !!},
        datasets: [{
            label: 'User Registrations',
            data: {!! json_encode($userRegistrations->pluck('count')) !!},
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Revenue Trend Chart
const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
const revenueTrendChart = new Chart(revenueTrendCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($revenueTrends->pluck('date')->map(function($date) { return date('M d', strtotime($date)); })) !!},
        datasets: [{
            label: 'Revenue (₹)',
            data: {!! json_encode($revenueTrends->pluck('total')) !!},
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₹' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Business Registrations Chart
const businessRegistrationsCtx = document.getElementById('businessRegistrationsChart').getContext('2d');
const businessRegistrationsChart = new Chart(businessRegistrationsCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($businessRegistrations->pluck('date')->map(function($date) { return date('M d', strtotime($date)); })) !!},
        datasets: [{
            label: 'Business Registrations',
            data: {!! json_encode($businessRegistrations->pluck('count')) !!},
            backgroundColor: '#f6c23e',
            borderColor: '#f6c23e',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Matrimony Profiles Chart
const matrimonyProfilesCtx = document.getElementById('matrimonyProfilesChart').getContext('2d');
const matrimonyProfilesChart = new Chart(matrimonyProfilesCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($matrimonyProfiles->pluck('date')->map(function($date) { return date('M d', strtotime($date)); })) !!},
        datasets: [{
            label: 'Matrimony Profiles',
            data: {!! json_encode($matrimonyProfiles->pluck('count')) !!},
            backgroundColor: '#36b9cc',
            borderColor: '#36b9cc',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// User Type Distribution Chart
const userTypeCtx = document.getElementById('userTypeChart').getContext('2d');
const userTypeChart = new Chart(userTypeCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($userTypeDistribution->pluck('user_type')->map(function($type) { return ucfirst($type); })) !!},
        datasets: [{
            data: {!! json_encode($userTypeDistribution->pluck('count')) !!},
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Payment Distribution Chart
const paymentDistributionCtx = document.getElementById('paymentDistributionChart').getContext('2d');
const paymentDistributionChart = new Chart(paymentDistributionCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($paymentDistribution->pluck('purpose')->map(function($purpose) { return ucfirst(str_replace('_', ' ', $purpose)); })) !!},
        datasets: [{
            data: {!! json_encode($paymentDistribution->pluck('total')) !!},
            backgroundColor: ['#e74a3b', '#f39c12', '#2ecc71'],
            hoverBackgroundColor: ['#c0392b', '#e67e22', '#27ae60'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Period change function
function changePeriod() {
    const period = document.getElementById('periodSelect').value;
    window.location.href = '{{ route("admin.analytics") }}?period=' + period;
}
</script>
@endpush