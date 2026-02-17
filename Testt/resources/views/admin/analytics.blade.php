@extends('admin.layouts.app')

@section('title', 'Analytics')
@section('page-title', 'Analytics & Reports')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Analytics Dashboard</h2>
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="dateRange" id="today" checked>
                <label class="btn btn-outline-primary" for="today">Today</label>
                
                <input type="radio" class="btn-check" name="dateRange" id="week">
                <label class="btn btn-outline-primary" for="week">This Week</label>
                
                <input type="radio" class="btn-check" name="dateRange" id="month">
                <label class="btn btn-outline-primary" for="month">This Month</label>
                
                <input type="radio" class="btn-check" name="dateRange" id="year">
                <label class="btn btn-outline-primary" for="year">This Year</label>
            </div>
        </div>
    </div>
</div>

<!-- Key Metrics Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon users">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="ms-3">
                    <h3 class="stats-number">{{ $analytics['growth_rate'] ?? '12.5' }}%</h3>
                    <p class="stats-label">Growth Rate</p>
                    <span class="stats-change positive">
                        <i class="fas fa-arrow-up"></i> +2.3% from last month
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon business">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="ms-3">
                    <h3 class="stats-number">{{ number_format($analytics['page_views'] ?? 45230) }}</h3>
                    <p class="stats-label">Page Views</p>
                    <span class="stats-change positive">
                        <i class="fas fa-arrow-up"></i> +15.2% from last week
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon matrimony">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="ms-3">
                    <h3 class="stats-number">{{ $analytics['avg_session'] ?? '4m 32s' }}</h3>
                    <p class="stats-label">Avg. Session</p>
                    <span class="stats-change positive">
                        <i class="fas fa-arrow-up"></i> +8.1% improvement
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon payments">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="ms-3">
                    <h3 class="stats-number">{{ $analytics['conversion_rate'] ?? '3.2' }}%</h3>
                    <p class="stats-label">Conversion Rate</p>
                    <span class="stats-change positive">
                        <i class="fas fa-arrow-up"></i> +0.5% this month
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- User Registration Trends -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>
                    User Registration Trends
                </h5>
            </div>
            <div class="card-body">
                <canvas id="userRegistrationChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Revenue Breakdown -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Revenue Breakdown
                </h5>
            </div>
            <div class="card-body">
                <canvas id="revenueBreakdownChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Business Analytics -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-building me-2"></i>
                    Business Registration Analytics
                </h5>
            </div>
            <div class="card-body">
                <canvas id="businessAnalyticsChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    Top Locations
                </h5>
            </div>
            <div class="card-body">
                @foreach($analytics['top_locations'] ?? [
                    ['city' => 'Mumbai', 'count' => 245, 'percentage' => 35],
                    ['city' => 'Delhi', 'count' => 189, 'percentage' => 27],
                    ['city' => 'Bangalore', 'count' => 156, 'percentage' => 22],
                    ['city' => 'Pune', 'count' => 98, 'percentage' => 14],
                    ['city' => 'Others', 'count' => 67, 'percentage' => 2]
                ] as $location)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="fw-semibold">{{ $location['city'] }}</div>
                        <small class="text-muted">{{ $location['count'] }} users</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold">{{ $location['percentage'] }}%</div>
                        <div class="progress" style="width: 60px; height: 4px;">
                            <div class="progress-bar bg-primary" style="width: {{ $location['percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Matrimony Analytics -->
<div class="row mb-4">
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-heart me-2"></i>
                    Matrimony Success Rate
                </h5>
            </div>
            <div class="card-body">
                <canvas id="matrimonySuccessChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-comments me-2"></i>
                    Chat Activity
                </h5>
            </div>
            <div class="card-body">
                <canvas id="chatActivityChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Payment Analytics -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>
                    Payment Analytics
                </h5>
                <button class="btn btn-primary btn-sm" onclick="exportReport()">
                    <i class="fas fa-download me-2"></i>
                    Export Report
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <h4 class="text-success">₹{{ number_format($analytics['total_payments'] ?? 125000) }}</h4>
                        <p class="text-muted mb-0">Total Payments</p>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <h4 class="text-primary">{{ $analytics['successful_payments'] ?? 95 }}%</h4>
                        <p class="text-muted mb-0">Success Rate</p>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <h4 class="text-warning">₹{{ number_format($analytics['avg_transaction'] ?? 850) }}</h4>
                        <p class="text-muted mb-0">Avg. Transaction</p>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <h4 class="text-info">{{ $analytics['refund_rate'] ?? 2.1 }}%</h4>
                        <p class="text-muted mb-0">Refund Rate</p>
                    </div>
                </div>
                <canvas id="paymentTrendsChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// User Registration Chart
const userRegCtx = document.getElementById('userRegistrationChart').getContext('2d');
const userRegistrationChart = new Chart(userRegCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($analytics['user_reg_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
        datasets: [{
            label: 'New Users',
            data: {!! json_encode($analytics['user_reg_data'] ?? [65, 78, 90, 81, 95, 110]) !!},
            backgroundColor: 'rgba(182, 19, 21, 0.8)',
            borderColor: '#b61315',
            borderWidth: 1,
            borderRadius: 4
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
                beginAtZero: true
            }
        }
    }
});

// Revenue Breakdown Chart
const revenueCtx = document.getElementById('revenueBreakdownChart').getContext('2d');
const revenueBreakdownChart = new Chart(revenueCtx, {
    type: 'doughnut',
    data: {
        labels: ['Business Subscriptions', 'Matrimony Profiles', 'Donations', 'Other'],
        datasets: [{
            data: {!! json_encode($analytics['revenue_breakdown'] ?? [45, 30, 15, 10]) !!},
            backgroundColor: [
                '#b61315',
                '#F59E0B',
                '#10B981',
                '#6B7280'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Business Analytics Chart
const businessCtx = document.getElementById('businessAnalyticsChart').getContext('2d');
const businessAnalyticsChart = new Chart(businessCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($analytics['business_labels'] ?? ['Week 1', 'Week 2', 'Week 3', 'Week 4']) !!},
        datasets: [{
            label: 'New Businesses',
            data: {!! json_encode($analytics['business_data'] ?? [12, 19, 15, 25]) !!},
            borderColor: '#b61315',
            backgroundColor: 'rgba(182, 19, 21, 0.1)',
            tension: 0.4,
            fill: true
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
                beginAtZero: true
            }
        }
    }
});

// Matrimony Success Chart
const matrimonyCtx = document.getElementById('matrimonySuccessChart').getContext('2d');
const matrimonySuccessChart = new Chart(matrimonyCtx, {
    type: 'radar',
    data: {
        labels: ['Profile Views', 'Connection Requests', 'Accepted Requests', 'Chat Initiated', 'Successful Matches'],
        datasets: [{
            label: 'Success Metrics',
            data: {!! json_encode($analytics['matrimony_success'] ?? [85, 70, 45, 60, 25]) !!},
            borderColor: '#b61315',
            backgroundColor: 'rgba(182, 19, 21, 0.2)',
            pointBackgroundColor: '#b61315'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            r: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});

// Chat Activity Chart
const chatCtx = document.getElementById('chatActivityChart').getContext('2d');
const chatActivityChart = new Chart(chatCtx, {
    type: 'bar',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Messages',
            data: {!! json_encode($analytics['chat_activity'] ?? [120, 150, 180, 200, 170, 220, 190]) !!},
            backgroundColor: 'rgba(245, 158, 11, 0.8)',
            borderColor: '#F59E0B',
            borderWidth: 1
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
                beginAtZero: true
            }
        }
    }
});

// Payment Trends Chart
const paymentCtx = document.getElementById('paymentTrendsChart').getContext('2d');
const paymentTrendsChart = new Chart(paymentCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($analytics['payment_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
        datasets: [{
            label: 'Successful Payments',
            data: {!! json_encode($analytics['successful_payment_data'] ?? [15000, 22000, 18000, 25000, 28000, 32000]) !!},
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Failed Payments',
            data: {!! json_encode($analytics['failed_payment_data'] ?? [800, 1200, 900, 1100, 1000, 1300]) !!},
            borderColor: '#EF4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            }
        },
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

// Date range filter
document.querySelectorAll('input[name="dateRange"]').forEach(radio => {
    radio.addEventListener('change', function() {
        console.log('Date range changed to:', this.id);
        // Here you would make AJAX calls to update all charts
        updateAllCharts(this.id);
    });
});

function updateAllCharts(range) {
    // This would typically make AJAX calls to get new data
    console.log('Updating charts for range:', range);
}

function exportReport() {
    // This would generate and download a report
    alert('Report export functionality would be implemented here');
}
</script>
@endpush