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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary: #ff4757;
            --primary-dark: #ff2a3b;
            --primary-rgb: 255,71,87;
            --accent: #ff7a59;
            --light-bg: #f4f3f0;
            --glass: #ffffff;
            --border-glow: rgba(255, 71, 87, 0.1);
            --sidebar-width: 280px;
        }

        /* Custom scrollbar for scrollable menus */
        .sidebar-menu-wrapper::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-menu-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-menu-wrapper::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 4px;
        }
        .sidebar-menu-wrapper::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
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

        /* Main Content Area Styling */
        .main-content {
            margin-left: 0 !important;
            flex-grow: 1;
            width: 100%;
            min-width: 0;
            padding: 110px 40px 40px 40px; /* Offset the 70px fixed header with top padding */
            transition: all 0.3s ease;
        }

        @media (max-width: 991px) {
            .main-content {
                padding: 90px 15px 90px 15px !important; /* Top padding for fixed navbar, bottom padding for mobile bottom bar */
                width: 100%;
                min-width: 0;
            }
            /* Fix Bootstrap row overflow mismatches on mobile viewports */
            .main-content .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            .main-content .col-12 {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }

        /* Desktop Navbar Menu Styles */
        .nav-link-navbar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            color: #475569;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.3s ease;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .nav-link-navbar:hover {
            color: var(--primary);
            background: rgba(255, 71, 87, 0.05);
        }

        .nav-link-navbar.active {
            color: #fff !important;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%) !important;
            box-shadow: 0 4px 12px rgba(255, 71, 87, 0.15) !important;
        }

        /* Custom Hover Dropdown for Navbar */
        .dropdown-custom {
            position: relative;
        }

        .dropdown-menu-navbar {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(10px);
            background: #fff;
            border-radius: 12px;
            border: 1px solid rgba(255, 71, 87, 0.08);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            min-width: 220px;
            display: none;
            flex-direction: column;
            padding: 8px;
            z-index: 1030;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .dropdown-custom:hover .dropdown-menu-navbar {
            display: flex;
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .dropdown-item-navbar {
            padding: 10px 16px;
            color: #475569;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 0.88rem;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .dropdown-item-navbar:hover {
            background: rgba(255, 71, 87, 0.05);
            color: var(--primary);
        }

        .dropdown-item-navbar.active {
            background: rgba(255, 71, 87, 0.08);
            color: var(--primary);
            font-weight: 600;
        }

        /* Mobile Bottom Bar Styling */
        .mobile-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 65px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 71, 87, 0.08);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.04);
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1040;
            padding-bottom: env(safe-area-inset-bottom);
        }

        .mobile-tab-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #8a90a2;
            text-decoration: none;
            font-size: 0.72rem;
            font-weight: 600;
            gap: 4px;
            flex: 1;
            height: 100%;
            transition: all 0.2s ease;
        }

        .mobile-tab-item i {
            font-size: 1.25rem;
            transition: transform 0.2s ease;
        }

        .mobile-tab-item:hover, .mobile-tab-item.active {
            color: var(--primary);
        }

        .mobile-tab-item.active i {
            transform: scale(1.1);
        }

        /* Bottom Drawer for Mobile */
        .mobile-drawer-backdrop {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(19, 22, 34, 0.5);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 1045;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-drawer-backdrop.show {
            display: block;
            opacity: 1;
        }

        .mobile-bottom-drawer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.15);
            z-index: 1050;
            padding: 20px 24px;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.32, 0.94, 0.6, 1);
            max-height: 80vh;
            overflow-y: auto;
        }

        .mobile-bottom-drawer.show {
            transform: translateY(0);
        }

        .drawer-handle {
            width: 40px;
            height: 5px;
            background: #cbd5e1;
            border-radius: 5px;
            margin: 0 auto 15px auto;
        }

        .drawer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding-bottom: 10px;
        }

        .drawer-menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .drawer-menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            color: #475569;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }

        .drawer-menu-item:hover, .drawer-menu-item.active {
            background: rgba(255, 71, 87, 0.05);
            color: var(--primary);
        }

        /* Header Card */
        .welcome-banner {
            background: linear-gradient(135deg, #ff4757 0%, #ff7a59 100%);
            color: #fff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(255, 71, 87, 0.15);
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

        .glass-card {
            background: #ffffff;
            border: 1px solid #e9e8e4;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.02);
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.04);
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

        /* Buttons consistent with active dashboard color scheme */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%) !important;
            border: none !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(255, 71, 87, 0.15) !important;
            transition: all 0.2s ease-in-out !important;
        }

        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%) !important;
            box-shadow: 0 6px 15px rgba(255, 71, 87, 0.25) !important;
            color: #fff !important;
        }

        .btn-outline-primary, .btn-outline-teal, .btn-outline-secondary {
            color: var(--primary) !important;
            border-color: var(--primary) !important;
            background-color: transparent !important;
            transition: all 0.2s ease-in-out !important;
        }

        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active,
        .btn-outline-teal:hover, .btn-outline-teal:focus, .btn-outline-teal:active,
        .btn-outline-secondary:hover, .btn-outline-secondary:focus, .btn-outline-secondary:active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%) !important;
            border-color: transparent !important;
            color: #fff !important;
        }

        .btn-teal {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #fff !important;
            transition: all 0.2s ease-in-out !important;
        }

        .btn-teal:hover, .btn-teal:focus, .btn-teal:active {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
            color: #fff !important;
        }

        /* Active Tab Pills */
        .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
            background-color: var(--primary) !important;
            color: #fff !important;
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
                padding: 20px 15px;
            }
            .dashboard-navbar {
                padding: 12px 18px;
                /* margin-bottom: 25px; */
                border-radius: 12px;
            }
            .navbar-brand-title {
                font-size: 1.1rem;
            }
            .welcome-banner {
                padding: 25px 20px;
                border-radius: 18px;
                margin-bottom: 25px;
            }
            .welcome-banner h1 {
                font-size: 1.6rem !important;
            }
            .profile-photo-circle {
                width: 90px;
                height: 90px;
            }
            .glass-card {
                padding: 20px;
                border-radius: 16px;
                margin-bottom: 20px;
            }
            .metric-icon {
                width: 45px;
                height: 45px;
                font-size: 18px;
                margin-bottom: 12px;
            }
        }

        @media (max-width: 576px) {
            .dashboard-navbar {
                padding: 10px 12px;
            }
            .navbar-brand-title {
                font-size: 0.95rem;
            }
            .navbar-dropdown-btn span {
                display: none; /* Hide name on super small screens to prevent overflow */
            }
            .navbar-dropdown-btn {
                padding: 8px;
                border-radius: 50%;
                width: 38px;
                height: 38px;
                justify-content: center;
            }
            .navbar-dropdown-btn i.fa-chevron-down {
                display: none;
            }
            .welcome-banner {
                padding: 20px 15px;
                border-radius: 14px;
            }
            .welcome-banner h1 {
                font-size: 1.4rem !important;
            }
            .welcome-banner p {
                font-size: 0.85rem !important;
            }
            .profile-photo-circle {
                width: 70px;
                height: 70px;
            }
        }

        /* Top Navbar Styling */
        .dashboard-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 71, 87, 0.08);
            padding: 15px 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1020; /* Ensure dropdown displays in front of all content */
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
            background: rgba(255, 71, 87, 0.1);
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

        .nav-link {
            color: #000000 !important;
        }

    </style>
    @yield('styles')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<div class="dashboard-wrapper">
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

@include('layouts.mobile_nav')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Global jQuery Form Validation Script
    $(document).ready(function() {
        // Disable native browser validation tooltips (bubbles)
        $('form').attr('novalidate', 'novalidate');

        // Monitor all form submits
        $(document).on('submit', 'form', function(e) {
            let form = $(this);

            // Bypass validation for delete confirmation forms or clear/reset filter forms
            const methodInput = form.find('input[name="_method"]');
            if (methodInput.length && methodInput.val().toUpperCase() === 'DELETE') {
                return;
            }
            if (form.attr('id') === 'logout-form') {
                return;
            }

            // Remove existing error states
            form.find('.invalid-feedback-custom').remove();
            form.find('.is-invalid-custom').removeClass('is-invalid-custom').css('border-color', '');

            let isValid = true;
            let firstInvalidEl = null;

            // Loop over all required fields
            form.find('input[required], textarea[required], select[required]').each(function() {
                let el = $(this);
                let val = el.val();

                // Check if empty or is unchecked checkbox/radio
                let isFieldInvalid = false;
                if (el.is(':checkbox') || el.is(':radio')) {
                    let name = el.attr('name');
                    if (name && form.find(`input[name="${name}"]:checked`).length === 0) {
                        isFieldInvalid = true;
                    }
                } else if (!val || val.toString().trim() === '') {
                    isFieldInvalid = true;
                }

                // If input type is email, check email format
                if (!isFieldInvalid && el.attr('type') === 'email') {
                    let emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailReg.test(val.toString().trim())) {
                        isValid = false;
                        el.addClass('is-invalid-custom').css('border-color', '#dc3545');
                        let container = el;
                        if (el.parent('.input-group').length) {
                            container = el.parent('.input-group');
                        }
                        if (!container.siblings('.invalid-feedback-custom').length) {
                            $('<div class="invalid-feedback-custom text-danger small mt-1 fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i> Please enter a valid email address.</div>').insertAfter(container);
                        }
                        if (!firstInvalidEl) {
                            firstInvalidEl = el;
                        }
                    }
                }

                if (isFieldInvalid) {
                    isValid = false;
                    el.addClass('is-invalid-custom').css('border-color', '#dc3545');

                    if (!firstInvalidEl) {
                        firstInvalidEl = el;
                    }

                    // Find a user-friendly label name
                    let fieldLabel = '';
                    let placeholder = el.attr('placeholder');
                    
                    // Try to find matching label using "for" or sibling
                    let id = el.attr('id');
                    let labelEl = id ? form.find(`label[for="${id}"]`) : [];
                    if (!labelEl.length) {
                        labelEl = el.closest('.mb-3, .mb-4, .form-group').find('label');
                    }
                    
                    if (labelEl.length) {
                        fieldLabel = labelEl.first().text().replace('*', '').trim();
                    } else if (placeholder) {
                        fieldLabel = placeholder.trim();
                    } else {
                        fieldLabel = el.attr('name') ? el.attr('name').replace('_', ' ').trim() : 'This field';
                    }

                    // Clean label value
                    if (fieldLabel.toLowerCase().endsWith('optional')) {
                        fieldLabel = fieldLabel.replace(/optional/gi, '').replace(/[()]/g, '').trim();
                    }
                    if (fieldLabel.length > 30) {
                        fieldLabel = 'This field';
                    }

                    let container = el;
                    if (el.parent('.input-group').length) {
                        container = el.parent('.input-group');
                    }

                    // Append error message below the field if it doesn't already exist
                    if (!container.siblings('.invalid-feedback-custom').length) {
                        $('<div class="invalid-feedback-custom text-danger small mt-1 fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i> ' + fieldLabel + ' is required.</div>').insertAfter(container);
                    }
                }
            });

            // Prevent form submit if invalid
            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();

                // Focus/scroll to first invalid element
                if (firstInvalidEl) {
                    $('html, body').animate({
                        scrollTop: firstInvalidEl.offset().top - 120
                    }, 200);
                    firstInvalidEl.focus();
                }
            }
        });

        // Real-time clearance of error messages on value input
        $(document).on('input change keyup', 'form input, form textarea, form select', function() {
            let el = $(this);
            let val = el.val();
            let isCheckboxOrRadio = el.is(':checkbox') || el.is(':radio');
            
            let isValOk = true;
            if (isCheckboxOrRadio) {
                let name = el.attr('name');
                if (name && el.closest('form').find(`input[name="${name}"]:checked`).length > 0) {
                    isValOk = true;
                } else {
                    isValOk = false;
                }
            } else if (!val || val.toString().trim() === '') {
                isValOk = false;
            }

            if (isValOk) {
                el.removeClass('is-invalid-custom').css('border-color', '');
                let container = el;
                if (el.parent('.input-group').length) {
                    container = el.parent('.input-group');
                }
                container.siblings('.invalid-feedback-custom').remove();
            }
        });
    });
</script>
<script>
    // Global Confirmation Dialog for Deletions
    document.addEventListener('submit', function (event) {
        const form = event.target;
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput && methodInput.value.toUpperCase() === 'DELETE') {
            event.preventDefault();

            if (form.dataset.confirmed === 'true') {
                form.submit();
                return;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this deletion!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ff4757',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    background: 'rgba(255, 255, 255, 0.95)',
                    backdrop: `rgba(92, 5, 41, 0.2)`
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
            } else {
                if (confirm('Are you sure you want to delete this record?')) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            }
        }
    });

    // Toggle Navbar Dropdown
    function toggleNavbarDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('navbarDropdownMenu');
        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    }

    // Toggle Sidebar Dropdown
    function toggleSidebarDropdown(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const parent = event.currentTarget.closest('.has-dropdown');
        if (parent) {
            parent.classList.toggle('open');
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
        window.location.href = `/dashboard/business/browse?category_id=${categoryId}`;
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

    // Toggle Mobile Bottom Drawers
    function toggleMobileBusinessDrawer(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const drawer = document.getElementById('mobileBusinessDrawer');
        const backdrop = document.getElementById('mobileDrawerBackdrop');
        const show = drawer && !drawer.classList.contains('show');
        closeAllMobileDrawers();
        if (show && drawer && backdrop) {
            drawer.classList.add('show');
            backdrop.classList.add('show');
        }
    }

    function toggleMobileMatrimonyDrawer(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const drawer = document.getElementById('mobileMatrimonyDrawer');
        const backdrop = document.getElementById('mobileDrawerBackdrop');
        const show = drawer && !drawer.classList.contains('show');
        closeAllMobileDrawers();
        if (show && drawer && backdrop) {
            drawer.classList.add('show');
            backdrop.classList.add('show');
        }
    }

    function toggleMobileMoreDrawer(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const drawer = document.getElementById('mobileMoreDrawer');
        const backdrop = document.getElementById('mobileDrawerBackdrop');
        const show = drawer && !drawer.classList.contains('show');
        closeAllMobileDrawers();
        if (show && drawer && backdrop) {
            drawer.classList.add('show');
            backdrop.classList.add('show');
        }
    }

    function closeAllMobileDrawers() {
        const drawers = document.querySelectorAll('.mobile-bottom-drawer');
        const backdrop = document.getElementById('mobileDrawerBackdrop');
        drawers.forEach(drawer => drawer.classList.remove('show'));
        if (backdrop) {
            backdrop.classList.remove('show');
        }
    }
</script>

@php
    $userIsBlogger = auth()->check() && auth()->user()->user_type === 'bloger';
    $allDonationCauses = $userIsBlogger ? collect() : \App\Models\DonationCause::where('status', 'active')
        ->orderByRaw("CASE WHEN urgency = 'high' THEN 1 ELSE 2 END")
        ->latest()
        ->get();
    $webSuggestedCause = $allDonationCauses->first();
@endphp

<!-- Modal 1: Donation Causes List -->
@if($allDonationCauses->count() > 0)
<div class="modal fade" id="donationCausesListModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3 overflow-hidden text-start" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(15px);">
            <div class="modal-header border-0 pb-0">
                <div class="text-start">
                    <span class="badge bg-danger bg-opacity-10 text-danger py-1.5 px-3 rounded-pill fw-bold text-uppercase mb-2" style="font-size:0.75rem;"><i class="fa-solid fa-circle-exclamation me-1"></i> Make an Impact</span>
                    <h5 class="fw-bold mb-0 text-dark">Support a Noble Cause Before You Go</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body py-3" style="max-height: 60vh; overflow-y: auto;">
                <div class="row">
                    @foreach($allDonationCauses as $cause)
                        <div class="col-md-6 col-12 mb-4">
                            <div class="card h-100 border rounded-4 shadow-sm bg-white overflow-hidden hover-card transition" style="transition: all 0.3s ease;">
                                <div class="position-relative">
                                    @if($cause->image_url)
                                        <img src="{{ asset('storage/' . $cause->image_url) }}" alt="{{ $cause->title }}" style="height: 160px; width: 100%; object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center" style="height: 160px; width: 100%; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: #fff;">
                                            <i class="fa-solid fa-hands-holding fs-1"></i>
                                        </div>
                                    @endif
                                    <span class="position-absolute top-0 end-0 m-3 badge bg-danger bg-opacity-95 text-white rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="font-size:0.65rem;">{{ $cause->urgency }}</span>
                                </div>
                                <div class="card-body p-4 text-start d-flex flex-column justify-content-between" style="min-height: 250px;">
                                    <div>
                                        <span class="text-muted small fw-semibold text-uppercase" style="font-size:0.75rem;">{{ $cause->organization ?? 'Mali Community Fund' }}</span>
                                        <h6 class="fw-bold text-dark mt-1 mb-2">{{ $cause->title }}</h6>
                                        <p class="text-secondary small mb-3" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ strip_tags($cause->description) }}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <!-- Progress Bar -->
                                        <div class="small fw-semibold text-secondary mb-1">Raised Progress: <span class="text-primary">{{ round($cause->progress_percentage) }}%</span></div>
                                        <div class="progress rounded-pill mb-2" style="height: 6px;">
                                            <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $cause->progress_percentage }}%; background: var(--primary);"></div>
                                        </div>
                                        <div class="d-flex justify-content-between text-muted small mb-3" style="font-size:0.75rem;">
                                            <span>Raised: <strong class="text-dark">₹{{ number_format($cause->raised_amount, 0) }}</strong></span>
                                            <span>Target: <strong class="text-dark">₹{{ number_format($cause->target_amount, 0) }}</strong></span>
                                        </div>
                                        
                                        <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold" style="background-color: var(--primary) !important; border-color: var(--primary) !important;" onclick="openDonationPaymentForm({{ json_encode($cause) }})">Donate</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" id="causes-modal-skip-btn">Just Sign Out</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Donation Payment Form -->
<div class="modal fade" id="donationPaymentFormModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3 overflow-hidden text-start" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(15px);">
            <div class="modal-header border-0 pb-0">
                <div class="text-start">
                    <span class="badge bg-primary bg-opacity-10 text-primary py-1.5 px-3 rounded-pill fw-bold text-uppercase mb-2" style="font-size:0.75rem;"><i class="fa-solid fa-circle-exclamation me-1"></i> Make an Impact</span>
                    <h5 class="fw-bold mb-0 text-dark">Enter Contribution Details</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body py-3">
                <!-- Selected Cause Details Card (Top) -->
                <div class="card border-0 rounded-4 p-3 mb-4 bg-light text-start shadow-sm" id="pay-selected-cause-card">
                    <div class="d-flex align-items-start gap-3">
                        <div id="pay-cause-img-placeholder"></div>
                        <div class="flex-grow-1">
                            <span class="text-muted small fw-semibold" id="pay-cause-org"></span>
                            <h6 class="fw-bold text-dark mt-0.5 mb-2" id="pay-cause-title"></h6>
                            <p class="text-secondary small mb-3" id="pay-cause-desc" style="line-height: 1.5;"></p>
                            
                            <!-- Progress Bar -->
                            <div class="small fw-semibold text-secondary mb-1">Raised Progress: <span class="text-primary" id="pay-cause-progress-text">0%</span></div>
                            <div class="progress rounded-pill mb-1" style="height: 6px;">
                                <div class="progress-bar rounded-pill" role="progressbar" style="width: 0%; background: var(--primary);" id="pay-cause-progress-bar"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted small" style="font-size:0.75rem;">
                                <span>Raised: <strong class="text-dark" id="pay-cause-raised">₹0</strong></span>
                                <span>Target: <strong class="text-dark" id="pay-cause-target">₹0</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Checkout Form -->
                <form id="webDonationCheckoutForm">
                    @csrf
                    <input type="hidden" id="donation_cause_id">
                    
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Select Contribution Amount *</label>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold" onclick="selectDonationAmount(100)">₹100</button>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold" onclick="selectDonationAmount(250)">₹250</button>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold" onclick="selectDonationAmount(500)">₹500</button>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold" onclick="selectDonationAmount(1000)">₹1000</button>
                        </div>
                        <input type="number" id="donation_amount" class="form-control" placeholder="Enter custom amount (Min. ₹1)" required min="1">
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Supportive Message <span class="text-muted small">(Optional)</span></label>
                        <textarea id="donation_message" class="form-control" rows="2" placeholder="Write a blessing or supportive note..."></textarea>
                    </div>

                    <div class="mb-3 text-start form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="donation_anonymous">
                        <label class="form-check-label fw-semibold text-secondary small" for="donation_anonymous">Make my donation anonymous</label>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light me-auto" onclick="goBackToCausesList()"><i class="fa-solid fa-arrow-left me-1"></i> Back to Causes</button>
                <button type="button" class="btn btn-primary px-4 fw-bold" id="donation-modal-submit-btn" style="background-color: var(--primary) !important;" onclick="submitWebDonation()">Proceed to Donate</button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    // Exit Intent & Logout Suggestion Scripting
    let isLogoutTriggered = false;
    const hasSuggestedCause = @json($allDonationCauses->count() > 0);
    let selectedCauseForDonation = null;

    // Trigger overlay suggestion modal (Causes list)
    function triggerDonationSuggestionModal(isLogout = false) {
        isLogoutTriggered = isLogout;
        
        if (!hasSuggestedCause) {
            if (isLogout) {
                executeLogout();
            }
            return;
        }

        const closeBtn = document.getElementById('causes-modal-close-btn');
        const skipBtn = document.getElementById('causes-modal-skip-btn');
        
        if (isLogout) {
            if (closeBtn) closeBtn.style.display = 'none';
            if (skipBtn) {
                skipBtn.innerText = 'Just Sign Out';
                skipBtn.onclick = executeLogout;
            }

            const modalEl = document.getElementById('donationCausesListModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        } 
        // else {
        //     if (closeBtn) closeBtn.style.display = 'block';
        //     if (skipBtn) {
        //         skipBtn.innerText = 'Close';
        //         skipBtn.onclick = function() {
        //             const modalEl = document.getElementById('donationCausesListModal');
        //             const modal = bootstrap.Modal.getInstance(modalEl);
        //             if (modal) modal.hide();
        //         };
        //     }
        // }
    }

    function openDonationPaymentForm(causeJson) {
        selectedCauseForDonation = causeJson;
        
        // Hide causes list modal
        const listModalEl = document.getElementById('donationCausesListModal');
        const listModal = bootstrap.Modal.getInstance(listModalEl);
        if (listModal) {
            listModal.hide();
        }
        
        // Populate cause details at the top of the payment form modal
        document.getElementById('donation_cause_id').value = causeJson.id;
        document.getElementById('pay-cause-title').innerText = causeJson.title;
        document.getElementById('pay-cause-org').innerText = causeJson.organization || 'Mali Community Fund';
        
        const cleanDesc = causeJson.description.replace(/(<([^>]+)>)/gi, "");
        document.getElementById('pay-cause-desc').innerText = cleanDesc.substring(0, 150) + (cleanDesc.length > 150 ? '...' : '');
        
        // Image
        const imgPlaceholder = document.getElementById('pay-cause-img-placeholder');
        if (causeJson.image_url) {
            imgPlaceholder.innerHTML = `<img src="/storage/${causeJson.image_url}" class="rounded-3" style="width: 54px; height: 54px; min-width: 54px; object-fit: cover;">`;
        } else {
            imgPlaceholder.innerHTML = `<div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; min-width: 54px;"><i class="fa-solid fa-hands-holding fs-4"></i></div>`;
        }
        
        // Progress
        const target = parseFloat(causeJson.target_amount) || 0;
        const raised = parseFloat(causeJson.raised_amount) || 0;
        const progress = target > 0 ? Math.min(100, Math.round((raised / target) * 100)) : 0;
        
        document.getElementById('pay-cause-progress-text').innerText = `${progress}%`;
        document.getElementById('pay-cause-progress-bar').style.width = `${progress}%`;
        document.getElementById('pay-cause-raised').innerText = `₹${raised.toLocaleString('en-IN')}`;
        document.getElementById('pay-cause-target').innerText = `₹${target.toLocaleString('en-IN')}`;
        
        // Configure close/back buttons based on context
        const payCloseBtn = document.getElementById('pay-modal-close-btn');
        if (isLogoutTriggered) {
            if (payCloseBtn) payCloseBtn.style.display = 'none';
        } else {
            if (payCloseBtn) {
                payCloseBtn.style.display = 'block';
                payCloseBtn.onclick = function() {
                    const payModalEl = document.getElementById('donationPaymentFormModal');
                    const payModal = bootstrap.Modal.getInstance(payModalEl);
                    if (payModal) payModal.hide();
                };
            }
        }
        
        // Show payment form modal
        setTimeout(() => {
            const payModalEl = document.getElementById('donationPaymentFormModal');
            const payModal = new bootstrap.Modal(payModalEl);
            payModal.show();
        }, 400);
    }

    function goBackToCausesList() {
        const payModalEl = document.getElementById('donationPaymentFormModal');
        const payModal = bootstrap.Modal.getInstance(payModalEl);
        if (payModal) {
            payModal.hide();
        }
        
        setTimeout(() => {
            const listModalEl = document.getElementById('donationCausesListModal');
            const listModal = new bootstrap.Modal(listModalEl);
            listModal.show();
        }, 400);
    }

    function executeLogout() {
        const form = document.getElementById('logout-form');
        if (form) {
            form.dataset.confirmed = 'true'; // Bypass delete confirmations interception
            form.submit();
        }
    }

    function selectDonationAmount(amount) {
        document.getElementById('donation_amount').value = amount;
    }

    function submitWebDonation() {
        const causeId = document.getElementById('donation_cause_id').value;
        const amount = document.getElementById('donation_amount').value;
        const message = document.getElementById('donation_message').value;
        const anonymous = document.getElementById('donation_anonymous').checked ? 1 : 0;

        if (!amount || amount <= 0) {
            Swal.fire('Error', 'Donation amount is required and must be greater than 0.', 'error');
            return;
        }

        const submitBtn = document.getElementById('donation-modal-submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

        fetch('/dashboard/donations/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                cause_id: causeId,
                amount: amount,
                message: message,
                anonymous: anonymous
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Initialize Razorpay
                const options = {
                    key: data.key,
                    amount: data.amount * 100,
                    currency: data.currency,
                    name: 'Mali Setu Noble Cause',
                    description: `Support: ${data.cause_title}`,
                    order_id: data.order_id,
                    handler: function (response) {
                        // Verify signature
                        fetch('/dashboard/donations/verify', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_signature: response.razorpay_signature,
                                donation_id: data.donation_id
                            })
                        })
                        .then(res => res.json())
                        .then(verifyData => {
                            if (verifyData.success) {
                                Swal.fire({
                                    title: 'Thank You!',
                                    text: 'Your noble contribution was received successfully. God bless you!',
                                    icon: 'success',
                                    confirmButtonText: isLogoutTriggered ? 'Proceed to Sign Out' : 'Close',
                                    confirmButtonColor: '#ff4757',
                                    allowOutsideClick: false
                                }).then(() => {
                                    if (isLogoutTriggered) {
                                        executeLogout();
                                    } else {
                                        const modalEl = document.getElementById('donationPaymentFormModal');
                                        const modal = bootstrap.Modal.getInstance(modalEl);
                                        if (modal) modal.hide();
                                    }
                                });
                            } else {
                                Swal.fire('Error', verifyData.message || 'Signature verification failed', 'error');
                            }
                        });
                    },
                    theme: {
                        color: '#ff4757'
                    }
                };
                const rzp = new Razorpay(options);
                rzp.on('payment.failed', function (response){
                    Swal.fire('Payment Failed', response.error.description || 'Contribution could not be completed.', 'error');
                });
                rzp.open();
            } else {
                Swal.fire('Error', data.message || 'Failed to initiate order', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Proceed to Donate';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // First visit auto open flow
        if (!sessionStorage.getItem('donation_causes_prompted')) {
            setTimeout(() => {
                triggerDonationSuggestionModal(false);
                sessionStorage.setItem('donation_causes_prompted', 'true');
            }, 1200);
        }

        // Intercept logout form submission
        const logoutForm = document.getElementById('logout-form');
        if (logoutForm) {
            logoutForm.addEventListener('submit', function(e) {
                // Check if already bypassed
                if (logoutForm.dataset.confirmed === 'true') {
                    return;
                }
                e.preventDefault();
                triggerDonationSuggestionModal(true);
            });
        }

        // Exit intent detection (cursor leaves viewport top)
        let exitIntentFired = false;
        document.addEventListener('mouseleave', function(e) {
            if (e.clientY < 50 && !exitIntentFired) {
                exitIntentFired = true;
                triggerDonationSuggestionModal(false);
            }
        });

        // Sidebar Scroll Position Preservation
        const sidebarWrapper = document.querySelector('.sidebar-menu-wrapper');
        if (sidebarWrapper) {
            // Restore scroll position
            const savedScroll = localStorage.getItem('sidebarScrollPosition');
            if (savedScroll !== null) {
                sidebarWrapper.scrollTop = parseFloat(savedScroll);
            }

            // Save scroll position on scroll
            sidebarWrapper.addEventListener('scroll', function() {
                localStorage.setItem('sidebarScrollPosition', sidebarWrapper.scrollTop);
            });

            // Save scroll position when any link inside sidebar is clicked
            const navLinks = sidebarWrapper.querySelectorAll('.nav-link-custom');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    localStorage.setItem('sidebarScrollPosition', sidebarWrapper.scrollTop);
                });
            });
        }

        // Initialize Bootstrap Tooltips Globally
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Background Geolocation Tracking for Authenticated Users
        @auth
            @if(!session('current_location_fetched'))
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        
                        fetch('{{ route('dashboard.update-location') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                latitude: lat,
                                longitude: lon
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.updated) {
                                // Reload page to update calculated business distances on relevant pages
                                const currentUrl = window.location.href;
                                if (currentUrl.includes('dashboard') || currentUrl.includes('business')) {
                                    window.location.reload();
                                }
                            }
                        })
                        .catch(err => console.error('Error updating location:', err));
                    }, function(error) {
                        console.warn('Geolocation error:', error.message);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 8000,
                        maximumAge: 0
                    });
                }
            @endif
        @endauth
    });
</script>
@yield('scripts')
</body>
</html>
