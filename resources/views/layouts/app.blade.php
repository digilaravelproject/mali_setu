<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard — Mali Setu')</title>
    <meta name="description" content="Manage your Mali Setu profile, settings, and community modules.">

    <!-- Bootstrap & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #ad1457;
            --primary-dark: #7f0037;
            --primary-rgb: 173,20,87;
            --accent: #ff7a59;
            --light-bg: #fff5f8;
            --glass: rgba(255, 255, 255, 0.95);
            --border-glow: rgba(173, 20, 87, 0.15);
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light-bg);
            color: #2d3436;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Bootstrap Overrides */
        .text-primary { color: var(--primary) !important; }
        .bg-primary { background-color: var(--primary) !important; }

        /* Premium Dashboard Layout */
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #ad1457 0%, #5c0529 100%);
            color: #fff;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            top: 0; bottom: 0; left: 0;
            z-index: 100;
            box-shadow: 10px 0 30px rgba(173, 20, 87, 0.05);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 30px;
            text-decoration: none;
            color: #fff;
        }

        .sidebar-logo {
            height: 40px;
            width: auto;
            border-radius: 8px;
            background: #fff;
            padding: 4px;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }

        .nav-item {
            margin-bottom: 8px;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 20px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .nav-link-custom:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
            transform: translateX(4px);
        }

        .nav-link-custom.active {
            color: #fff;
            background: rgba(255,255,255,0.15);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .logout-btn {
            background: rgba(255, 74, 74, 0.1);
            color: #ff7878;
        }

        .logout-btn:hover {
            background: #ff4a4a;
            color: #fff;
        }

        /* Content Area Styling */
        .main-content {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            padding: 40px;
            transition: all 0.3s ease;
        }

        /* Header Card */
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(173, 20, 87, 0.15);
            margin-bottom: 35px;
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
            top: -50px; right: -50px;
        }

        /* Glass Cards */
        .glass-card {
            background: var(--glass);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(173, 20, 87, 0.02);
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            box-shadow: 0 15px 35px rgba(173, 20, 87, 0.05);
        }

        .metric-icon {
            width: 55px; height: 55px;
            border-radius: 14px;
            background: rgba(var(--primary-rgb), 0.08);
            color: var(--primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #4a5568;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 18px;
            border: 1.5px solid #e2e8f0;
            background: #fff;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--border-glow);
        }

        .profile-photo-circle {
            width: 110px; height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        /* Buttons consistent with sidebar primary color */
        .btn-primary {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #fff !important;
        }

        .btn-outline-primary {
            color: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        /* Pagination styling (Bootstrap-compatible) */
        .pagination {
            margin: 0;
            padding: 0.5rem;
            display: flex;
            gap: 6px;
            list-style: none;
        }

        .pagination .page-link {
            color: var(--primary);
            border: 1px solid rgba(0,0,0,0.08);
            padding: 6px 10px;
            border-radius: 6px;
            background: #fff;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .pagination .page-link:hover {
            background: rgba(var(--primary-rgb), 0.06);
            color: var(--primary);
        }

        .badge-verified {
            background-color: #2ec4b6;
            color: #fff;
            font-size: 0.8rem;
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-type {
            background-color: var(--accent);
            color: #fff;
            font-size: 0.8rem;
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 700;
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }

        /* Top Navbar Styling */
        .dashboard-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(173, 20, 87, 0.08);
            border-radius: 16px;
            padding: 15px 30px;
            margin-bottom: 35px;
            box-shadow: 0 10px 30px rgba(173, 20, 87, 0.03);
        }

        .navbar-brand-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d3748;
        }

        .navbar-user-dropdown {
            position: relative;
        }

        .navbar-dropdown-btn {
            background: rgba(var(--primary-rgb), 0.05);
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 8px 18px;
            border-radius: 50px;
            transition: all 0.3s ease;
            color: var(--primary);
            font-weight: 600;
        }

        .navbar-dropdown-btn:hover {
            background: rgba(173, 20, 87, 0.1);
        }

        .navbar-dropdown-menu {
            position: absolute;
            top: 120%;
            right: 0;
            background: #fff;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            min-width: 220px;
            display: none;
            flex-direction: column;
            padding: 8px 0;
            z-index: 1000;
        }

        .navbar-dropdown-menu.show {
            display: flex;
        }

        .navbar-dropdown-item {
            padding: 10px 20px;
            color: #495057;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .navbar-dropdown-item:hover {
            background: rgba(var(--primary-rgb), 0.04);
            color: var(--primary);
        }
    </style>
    @yield('styles')
</head>
<body>

<div class="dashboard-wrapper">
    @include('layouts.sidebar')

    <!-- Main Content Area -->
    <main class="main-content">
        @include('layouts.navbar')

        <!-- Toast / Alerts Display -->
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between" role="alert" style="background: rgba(46,196,182,0.1); color: #2ec4b6;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-check fs-5"></i>
                    <span class="small fw-semibold">{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-4 shadow-sm p-3 mb-4" role="alert" style="background: rgba(255,74,74,0.1); color: #ff4a4a;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fa-solid fa-circle-exclamation fs-5"></i>
                    <strong class="small fw-bold">Please correct the following errors:</strong>
                </div>
                <ul class="mb-0 ps-3 small fw-semibold">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle Navbar Dropdown
    function toggleNavbarDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('navbarDropdownMenu');
        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('navbarDropdownMenu');
        if (dropdown && dropdown.classList.contains('show')) {
            const isClickInside = dropdown.contains(event.target) || event.target.closest('.navbar-dropdown-btn');
            if (!isClickInside) {
                dropdown.classList.remove('show');
            }
        }
    });

    // Dropdown Tab Select Handler
    function selectDropdownTab(tabId) {
        const targetPanel = document.getElementById(`tab-${tabId}`);
        if (!targetPanel) {
            window.location.href = `/dashboard?tab=${tabId}`;
            return;
        }

        // Toggle tab links in sidebar (deactivate them)
        document.querySelectorAll('.nav-link-custom').forEach(link => {
            link.classList.remove('active');
        });

        // Hide all main tab panels
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.remove('active');
        });

        // Show targeted panel
        targetPanel.classList.add('active');

        // Close navbar dropdown
        const dropdown = document.getElementById('navbarDropdownMenu');
        if (dropdown) {
            dropdown.classList.remove('show');
        }
    }

    // Switch sidebar tab
    function switchTab(tabId, el) {
        const targetPanel = document.getElementById(`tab-${tabId}`);
        if (!targetPanel) {
            window.location.href = `/dashboard?tab=${tabId}`;
            return;
        }

        // Toggle tab links
        document.querySelectorAll('.nav-link-custom').forEach(link => {
            link.classList.remove('active');
        });
        if (el) {
            el.classList.add('active');
        }

        // Toggle panel visibility
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.remove('active');
        });
        
        targetPanel.classList.add('active');

        // If switching back to business, show list view by default
        if (tabId === 'business') {
            toggleBusinessSection('list');
        }
    }

    // Helper to dynamically select business category from main dashboard
    function selectCategoryOnDashboard(categoryId) {
        const directorySection = document.getElementById('directory-listings-section');
        if (!directorySection) {
            window.location.href = `/dashboard?browse_category_id=${categoryId}`;
            return;
        }
        
        if (typeof fetchCategoryBusinesses === 'function') {
            fetchCategoryBusinesses(categoryId);
        }
    }

    // Business modular sub-view toggling
    function toggleBusinessSection(viewName) {
        const listSec = document.getElementById('business-list-view');
        const setupSec = document.getElementById('business-setup-section');
        const consoleSec = document.getElementById('business-console-section');
        const editSec = document.getElementById('business-edit-section');

        if (listSec) listSec.style.display = 'none';
        if (setupSec) setupSec.style.display = 'none';
        if (consoleSec) consoleSec.style.display = 'none';
        if (editSec) editSec.style.display = 'none';

        if (viewName === 'list' && listSec) {
            listSec.style.display = 'block';
        } else if (viewName === 'setup' && setupSec) {
            setupSec.style.display = 'block';
        } else if (viewName === 'console' && consoleSec) {
            consoleSec.style.display = 'block';
        } else if (viewName === 'edit' && editSec) {
            editSec.style.display = 'block';
        }
    }

    function openEditJobModal(job) {
        document.getElementById('edit_job_title').value = job.title;
        document.getElementById('edit_job_location').value = job.location;
        document.getElementById('edit_job_employment_type').value = job.employment_type;
        document.getElementById('edit_job_experience_level').value = job.experience_level;
        document.getElementById('edit_job_category').value = job.category;
        document.getElementById('edit_job_salary_range').value = job.salary_range;
        document.getElementById('edit_job_description').value = job.description;
        document.getElementById('edit_job_requirements').value = job.requirements;
        
        const formAction = `/dashboard/business/jobs/${job.id}/update`;
        document.getElementById('editJobForm').setAttribute('action', formAction);
        
        const modal = new bootstrap.Modal(document.getElementById('editJobModal'));
        modal.show();
    }

    function openModerateModal(applicationId, status) {
        document.getElementById('moderate_status').value = status;
        document.getElementById('moderate_status_text').innerText = status.toUpperCase();
        
        const formAction = `/dashboard/business/applications/${applicationId}/status`;
        document.getElementById('moderateForm').setAttribute('action', formAction);
        
        const modal = new bootstrap.Modal(document.getElementById('moderateModal'));
        modal.show();
    }
</script>
@yield('scripts')
</body>
</html>
