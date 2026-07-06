@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="d-sm-flex">
            <a href="{{ route('admin.analytics') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-chart-line fa-sm text-white-50"></i> View Analytics
            </a>
        </div>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row">
        <!-- Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <a href="{{ route('admin.users.index') }}" class="text-decoration-none">Total Users</a>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['users']['total'] }}</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">{{ $stats['users']['verified'] }}</span> verified,
                                <span class="text-warning">{{ $stats['users']['pending'] }}</span> pending
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Businesses Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <a href="{{ route('admin.businesses.index') }}" class="text-decoration-none">Businesses</a>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['businesses']['total'] }}</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">{{ $stats['businesses']['approved'] }}</span> approved,
                                <span class="text-warning">{{ $stats['businesses']['pending'] }}</span> pending
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matrimony Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <a href="{{ route('admin.matrimony.index') }}" class="text-decoration-none">Matrimony Profiles</a>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['matrimony']['total'] }}</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">{{ $stats['matrimony']['connections'] }}</span> connections made
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <?php /*<div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($stats['payments']['total_revenue'], 2) }}</div>
                            <div class="text-xs text-muted mt-1">
                                {{ $stats['payments']['total_transactions'] }} transactions
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>*/ ?>
    </div>

    <!-- Pending Verifications Alert -->
    @if($pendingVerifications['caste_certificates'] > 0 || $pendingVerifications['businesses'] > 0 || $pendingVerifications['matrimony_profiles'] > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Pending Verifications</h4>
                <p class="mb-0">
                    You have 
                    @if($pendingVerifications['caste_certificates'] > 0)
                        <strong>{{ $pendingVerifications['caste_certificates'] }}</strong> caste certificates,
                    @endif
                    @if($pendingVerifications['businesses'] > 0)
                        <strong>{{ $pendingVerifications['businesses'] }}</strong> businesses,
                    @endif
                    @if($pendingVerifications['matrimony_profiles'] > 0)
                        <strong>{{ $pendingVerifications['matrimony_profiles'] }}</strong> matrimony profiles
                    @endif
                    waiting for verification.
                </p>
                <hr>
                <p class="mb-0">
                    <a class="btn btn-warning btn-sm" href="{{ route('admin.users.verification.pending') }}" role="button">
                        <i class="fas fa-eye"></i> Review Pending Items
                    </a>
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Subscription Expiry Reminders Card -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow mb-4 border-left-danger text-dark">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-danger d-flex align-items-center">
                        <i class="fas fa-bell" style="margin-right: 8px;"></i> Subscription Expiry Reminders (Due Today)
                    </h6>
                    <span class="badge bg-danger text-white rounded-pill" style="font-size: 0.85rem; padding: 5px 10px;">{{ count($expiryReminders) }}</span>
                </div>
                <div class="card-body">
                    @if(count($expiryReminders) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>User Name</th>
                                        <th>Contact (Phone)</th>
                                        <th>Business/Profile Name</th>
                                        <th>Expiry Date</th>
                                        <th>Reminder Stage</th>
                                        <th class="text-center" style="width: 180px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiryReminders as $reminder)
                                    <tr>
                                        <td>
                                            @if(($reminder['type'] ?? '') === 'Matrimony')
                                                <span class="badge badge-info text-black" style="font-size: 0.8rem; padding: 4px 8px;">Matrimony</span>
                                            @else
                                                <span class="badge badge-success text-black" style="font-size: 0.8rem; padding: 4px 8px;">Business</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $reminder['user_name'] }}</strong></td>
                                        <td>{{ $reminder['user_phone'] }}</td>
                                        <td>{{ $reminder['entity_name'] }}</td>
                                        <td><span class="text-danger font-weight-bold">{{ \Carbon\Carbon::parse($reminder['expiry_date'])->format('M d, Y') }}</span></td>
                                        <td><span class="badge badge-warning text-black">{{ $reminder['stage_label'] }}</span></td>
                                        <td class="text-center">
                                            @if($reminder['wa_link'] !== '#')
                                                <a href="{{ $reminder['wa_link'] }}" target="_blank" class="btn btn-success btn-sm d-inline-flex align-items-center text-white" style="background-color: #25D366; border-color: #25D366; padding: 4px 10px; font-weight: 500; border-radius: 4px; transition: all 0.2s ease-in-out;">
                                                    <i class="fab fa-whatsapp" style="margin-right: 6px; font-size: 0.95rem;"></i> Send Reminder
                                                </a>
                                            @else
                                                <span class="text-muted small">No Phone</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                            <p class="mb-0">No subscription expiry reminders are due today.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Recent Users -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Users</h6>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">View All</a>
                </div>
                <div class="card-body">
                    @if($recentUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            <span class="badge badge-secondary text-black">{{ ucfirst($user->user_type) }}</span>
                                        </td>
                                        <td>
                                            @if($user->caste_verification_status === 'approved')
                                                <span class="badge badge-success text-black">Verified</span>
                                            @elseif($user->caste_verification_status === 'pending')
                                                <span class="badge badge-warning text-black">Pending</span>
                                            @else
                                                <span class="badge badge-danger" style="color: red !important;">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent users found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Businesses -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Recent Businesses</h6>
                    <a href="{{ route('admin.businesses.index') }}" class="btn btn-success btn-sm">View All</a>
                </div>
                <div class="card-body">
                    @if($recentBusinesses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Business</th>
                                        <th>Owner</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBusinesses as $business)
                                    <tr>
                                        <td>{{ $business->business_name }}</td>
                                        <td>{{ $business->user->name }}</td>
                                        <td>
                                            @if($business->verification_status === 'approved')
                                                <span class="badge badge-success text-black">Approved</span>
                                            @elseif($business->verification_status === 'pending')
                                                <span class="badge badge-warning text-black">Pending</span>
                                            @else
                                                <span class="badge badge-danger" style="color: red !important;">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ $business->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent businesses found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">Recent Transactions</h6>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-warning btn-sm">View All</a>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Purpose</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->user->name }}</td>
                                        <td>
                                            <span class="badge badge-info text-black">{{ ucfirst(str_replace('_', ' ', $transaction->purpose)) }}</span>
                                        </td>
                                        <td>₹{{ number_format($transaction->amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-success text-black">{{ ucfirst($transaction->status) }}</span>
                                        </td>
                                        <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent transactions found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.users.verification.pending') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-user-check"></i><br>
                                Verify Users
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.businesses.index') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-building"></i><br>
                                Manage Businesses
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.matrimony.index') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-heart"></i><br>
                                Matrimony Profiles
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.settings') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-cog"></i><br>
                                System Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh dashboard every 5 minutes
setTimeout(function(){
    location.reload();
}, 300000);
</script>
@endpush