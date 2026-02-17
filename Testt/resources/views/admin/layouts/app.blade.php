<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Mali Setu</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #b61315;
            --secondary-color: #F59E0B;
            --sidebar-width: 280px;
            --header-height: 70px;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, #8b0000 100%);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin: 0.25rem 1rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .top-header {
            background: white;
            height: var(--header-height);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: between;
            padding: 0 2rem;
        }
        
        .content-area {
            padding: 2rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .stats-icon.users { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stats-icon.business { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stats-icon.matrimony { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stats-icon.payments { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }
        
        .stats-label {
            color: #6b7280;
            font-size: 0.875rem;
            margin: 0;
        }
        
        .stats-change {
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .stats-change.positive { color: #10b981; }
        .stats-change.negative { color: #ef4444; }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #8b0000;
            border-color: #8b0000;
        }
        
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: #f8fafc;
            border: none;
            font-weight: 600;
            color: #374151;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <i class="fas fa-heart me-2"></i>
                Mali Setu
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.analytics') }}" class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    Analytics
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('heroes.index') }}" 
                class="nav-link {{ request()->routeIs('heroes.*') ? 'active' : '' }}">
                    <i class="fas fa-image"></i>
                    <span>Homepage Heroes</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    User Management
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.users.verification.pending') }}" class="nav-link {{ request()->routeIs('admin.users.verification.*') ? 'active' : '' }}">
                    <i class="fas fa-certificate"></i>
                    Verifications
                </a>
            </div>

            <div class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}"
                   data-bs-toggle="collapse" href="#plansMenu" role="button" aria-expanded="{{ request()->routeIs('admin.plans.*') ? 'true' : 'false' }}" aria-controls="plansMenu">
                    <span><i class="fas fa-list-alt"></i> Manage Plans</span>
                    <i class="fas fa-chevron-down"></i>
                </a>

                <div class="collapse {{ request()->routeIs('admin.plans.*') ? 'show' : '' }}" id="plansMenu">
                    <div class="nav-item" style="margin-left: .6rem;">
                        <a href="{{ route('admin.plans.business.index') }}" class="nav-link {{ request()->routeIs('admin.plans.business.*') ? 'active' : '' }}">
                            <i class="fas fa-briefcase"></i>
                            Business Plans
                        </a>
                    </div>

                    <div class="nav-item" style="margin-left: .6rem;">
                        <a href="{{ route('admin.plans.matrimony.index') }}" class="nav-link {{ request()->routeIs('admin.plans.matrimony.*') ? 'active' : '' }}">
                            <i class="fas fa-user-friends"></i>
                            Matrimony Plans
                        </a>
                    </div>
                </div>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.category.index') }}" class="nav-link {{ request()->routeIs('admin.category.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    Category
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.businesses.index') }}" class="nav-link {{ request()->routeIs('admin.businesses.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    Businesses
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.volunteers.index') }}" class="nav-link {{ request()->routeIs('admin.volunteers.*') ? 'active' : '' }}">
                    <i class="fas fa-hands-helping"></i>
                    Volunteers
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.donations.index') }}" class="nav-link {{ request()->routeIs('admin.donations.*') ? 'active' : '' }}">
                    <i class="fas fa-heart"></i>
                    Donations
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.matrimony.index') }}" class="nav-link {{ request()->routeIs('admin.matrimony.*') ? 'active' : '' }}">
                    <i class="fas fa-heart"></i>
                    Matrimony
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.casts.index') }}" class="nav-link {{ request()->routeIs('admin.casts.*') ? 'active' : '' }}">
                    <i class="fas fa-sitemap"></i>
                    Manage Casts
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i>
                    Payments
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
            </div>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="mb-0 ms-3">@yield('page-title', 'Dashboard')</h4>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle fa-lg"></i>
                    <span class="ms-2">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>
        
        <!-- Content Area -->
        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <!-- jQuery (required for legacy code) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
        
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>