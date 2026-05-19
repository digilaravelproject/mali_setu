<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — Mali Setu</title>
    <meta name="description" content="Manage your Mali Setu profile, settings, and community modules.">

    <!-- Bootstrap & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #ad1457;
            --primary-dark: #7f0037;
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
            background: radial-gradient(circle at top right, #d81b60, #4a0022);
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
            background: #ffe4e8;
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
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebarMenu">
        <div>
            <a href="{{ url('/') }}" class="sidebar-brand">
                <img src="{{ asset('landing_page_logo.jpeg') }}" alt="MaliSetu Logo" class="sidebar-logo">
                <h5 class="fw-bold mb-0">Mali Setu</h5>
            </a>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a class="nav-link-custom active" onclick="switchTab('overview', this)">
                        <i class="fa-solid fa-gauge"></i> Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-custom" onclick="switchTab('edit-profile', this)">
                        <i class="fa-solid fa-user-pen"></i> Edit Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-custom" onclick="switchTab('change-password', this)">
                        <i class="fa-solid fa-key"></i> Security
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-custom" onclick="switchTab('features', this)">
                        <i class="fa-solid fa-circle-nodes"></i> My Feature Nodes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-custom" onclick="switchTab('danger-zone', this)">
                        <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="submit" class="nav-link-custom logout-btn w-100 border-0 text-start">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Toast / Alerts Display -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tab 1: Overview -->
        <div class="tab-panel active" id="tab-overview">
            <div class="welcome-banner">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge-type mb-3">{{ ucfirst($user->user_type) }} Profile</span>
                        <h1 class="fw-bold mb-2">Hello, {{ $user->name }}!</h1>
                        <p class="opacity-75 mb-0">Welcome back to your Mali Setu dashboard. Manage your settings, browse matched profiles, and engage with the community.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-4 mt-md-0">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Photo" class="profile-photo-circle">
                        @else
                            <div class="profile-photo-circle bg-white d-inline-flex align-items-center justify-content-center text-primary fs-2 fw-bold" style="width: 110px; height: 110px;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats grid -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="glass-card text-center">
                        <div class="metric-icon mx-auto"><i class="fa-solid fa-id-card"></i></div>
                        <h6 class="text-secondary fw-semibold">Verification</h6>
                        <h4 class="fw-bold mt-2">
                            @if($user->caste_verification_status === 'approved')
                                <span class="badge bg-success small"><i class="fa-solid fa-check-double me-1"></i> Approved</span>
                            @else
                                <span class="badge bg-warning text-dark small"><i class="fa-solid fa-hourglass-start me-1"></i> Pending</span>
                            @endif
                        </h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card text-center">
                        <div class="metric-icon mx-auto"><i class="fa-solid fa-handshake-angle"></i></div>
                        <h6 class="text-secondary fw-semibold">Matched Roles</h6>
                        <h4 class="fw-bold mt-2 text-primary">{{ ucfirst($user->user_type) }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card text-center">
                        <div class="metric-icon mx-auto"><i class="fa-solid fa-wallet"></i></div>
                        <h6 class="text-secondary fw-semibold">Matrimony Pay</h6>
                        <h4 class="fw-bold mt-2">
                            @if($user->has_matrimony_payment)
                                <span class="badge bg-teal" style="background:#00b4d8"><i class="fa-solid fa-crown me-1"></i> Premium</span>
                            @else
                                <span class="badge bg-secondary small">Free tier</span>
                            @endif
                        </h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card text-center">
                        <div class="metric-icon mx-auto"><i class="fa-solid fa-briefcase"></i></div>
                        <h6 class="text-secondary fw-semibold">Business Listing</h6>
                        <h4 class="fw-bold mt-2">
                            @if($user->is_business)
                                <span class="badge bg-success small">Registered</span>
                            @else
                                <span class="badge bg-light text-muted border small">No listing</span>
                            @endif
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Profile Summary -->
            <div class="glass-card">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-user me-2 text-primary"></i> Profile Information</h5>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="border-bottom pb-2">
                            <small class="text-secondary">Full Name</small>
                            <p class="mb-0 fw-semibold">{{ $user->name }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="border-bottom pb-2">
                            <small class="text-secondary">Email Address</small>
                            <p class="mb-0 fw-semibold">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="border-bottom pb-2">
                            <small class="text-secondary">Phone Number</small>
                            <p class="mb-0 fw-semibold">{{ $user->phone }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="border-bottom pb-2">
                            <small class="text-secondary">Member Since</small>
                            <p class="mb-0 fw-semibold">{{ $user->created_at->format('d M, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Edit Profile -->
        <div class="tab-panel" id="tab-edit-profile">
            <div class="glass-card">
                <h4 class="fw-bold mb-2">Edit Profile Settings</h4>
                <p class="text-secondary small mb-5">Update your contact information, location coordinates, and occupation parameters. Ensure all details are accurate.</p>

                <form action="{{ route('dashboard.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row align-items-center mb-5">
                        <div class="col-auto">
                            @if($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo" class="profile-photo-circle">
                            @else
                                <div class="profile-photo-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary fs-1 fw-bold" style="width: 100px; height: 100px;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Update Profile Photo</label>
                            <input type="file" name="photo" class="form-control">
                            <small class="text-muted">JPEG, PNG or WEBP formats up to 2MB allowed.</small>
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-4 border-bottom pb-2">Personal Parameters</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" value="{{ old('age', $user->age) }}" class="form-control" min="18" max="100">
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" value="{{ old('dob', $user->dob) }}" class="form-control">
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-4 border-bottom pb-2 mt-3">Employment Details</h5>
                    
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" value="{{ old('occupation', $user->occupation) }}" class="form-control">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}" class="form-control">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" value="{{ old('designation', $user->designation) }}" class="form-control">
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-4 border-bottom pb-2 mt-3">Address & Coordinates</h5>
                    
                    <div class="row">
                        <div class="col-md-8 mb-4">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-control" placeholder="House/Flat No, Apartment, Street">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label">Pincode (Indian Pincode)</label>
                            <input type="text" name="pincode" value="{{ old('pincode', $user->pincode) }}" class="form-control" maxlength="6">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" value="{{ old('city', $user->city) }}" class="form-control">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label">District</label>
                            <input type="text" name="district" value="{{ old('district', $user->district) }}" class="form-control">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" value="{{ old('state', $user->state) }}" class="form-control">
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Save Profile Modifications <i class="fa-solid fa-floppy-disk ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab 3: Change Password -->
        <div class="tab-panel" id="tab-change-password">
            <div class="glass-card">
                <h4 class="fw-bold mb-2">Security & Credentials</h4>
                <p class="text-secondary small mb-5">Change your system password. Once changed, your credentials will be updated instantly and a confirmation email will be sent.</p>

                <form action="{{ route('dashboard.password.change') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Min 8 characters" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat new password" required>
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Update Credentials <i class="fa-solid fa-shield-halved ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab 4: Features Details -->
        <div class="tab-panel" id="tab-features">
            <div class="glass-card">
                <h4 class="fw-bold mb-2"><i class="fa-solid fa-circle-nodes text-primary me-2"></i> Profile Specific Nodes</h4>
                <p class="text-secondary small mb-4">Here is the active information synced to your selected profile category (<strong>{{ ucfirst($user->user_type) }}</strong>).</p>

                @if($user->user_type === 'general')
                    <div class="alert alert-info border-0 rounded-4 p-4">
                        <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-info me-2"></i> Community Seeker Info</h6>
                        <p class="small mb-0">As a General Member, you get access to browse premium wedding directories, find active volunteers, review certified community doctors, and inspect regional business vendors. To expand your actions, you can list a local business or upgrade profiles.</p>
                    </div>
                @elseif($user->user_type === 'business')
                    <div class="p-4 rounded-4 bg-light mb-4">
                        <h5 class="fw-bold mb-3">Your Business Enterprise Status</h5>
                        @if($user->is_business)
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success p-3 rounded-3"><i class="fa-solid fa-store fs-3"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $user->business->name }}</h6>
                                    <p class="small text-muted mb-0">Active and synced under community registry.</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center p-3">
                                <p class="text-muted small mb-3">You haven't listed a business under your profile yet.</p>
                                <a href="#" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> List New Business</a>
                            </div>
                        @endif
                    </div>
                @elseif($user->user_type === 'matrimony')
                    <div class="p-4 rounded-4 bg-light mb-4">
                        <h5 class="fw-bold mb-3">Matrimony Seeker Account</h5>
                        @if($user->is_matrimony)
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-3"><i class="fa-solid fa-heart fs-3"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1">Caste-verified Seeker Profile Active</h6>
                                    <p class="small text-muted mb-0">Your profile is visible to other verified premium seekers.</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center p-3">
                                <p class="text-muted small mb-3">You haven't initialized your privacy-first matrimony profile yet.</p>
                                <a href="#" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-heart-circle-plus me-1"></i> Initialize Seeker Profile</a>
                            </div>
                        @endif
                    </div>
                @elseif($user->user_type === 'volunteer')
                    <div class="alert alert-success border-0 rounded-4 p-4">
                        <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-check me-2"></i> Dedicated Social Service Profile</h6>
                        <p class="small mb-0">Your volunteer account allows you to participate in active community events, coordinate food/support drives, and assist the foundation with certifications. Complete your detailed location metrics to get matched tasks.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab 5: Danger Zone -->
        <div class="tab-panel" id="tab-danger-zone">
            <div class="glass-card border-danger border-opacity-25" style="border: 2px solid rgba(255, 74, 74, 0.2)">
                <h4 class="fw-bold mb-2 text-danger">Danger Zone</h4>
                <p class="text-secondary small mb-5">Permanent account deletion. This action cannot be undone and will purge all your profile data, matrimony logs, business listings, and uploaded media.</p>

                <div class="alert alert-danger border-0 rounded-4 p-4 mb-4">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-2"></i> Critical Warning</h6>
                    <p class="small mb-0">Once deleted, your account references are fully unlinked. Your volunteer logs and donation histories will be archived anonymously and cannot be reclaimed.</p>
                </div>

                <button class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    Permanently Delete Account <i class="fa-solid fa-trash-can ms-2"></i>
                </button>
            </div>
        </div>
    </main>
</div>

<!-- Double-Confirmation Delete Account Bootstrap Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-4" style="font-size: 4rem;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Are you absolutely sure?</h4>
                <p class="text-secondary small px-3">This action is permanent and completely irreversible. All your profile information, matrimony matches, caste verification details, and local directory listings will be permanently lost.</p>
            </div>
            <div class="modal-footer border-0 d-flex gap-2 justify-content-center pb-4">
                <button type="button" class="btn btn-light rounded-3 px-4 py-2" data-bs-dismiss="modal">Cancel, Keep Account</button>
                <form action="{{ route('dashboard.account.delete') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-3 px-4 py-2">Confirm Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Tab switching logic
    function switchTab(tabId, el) {
        // Toggle tab links
        document.querySelectorAll('.nav-link-custom').forEach(link => {
            link.classList.remove('active');
        });
        el.classList.add('active');

        // Toggle panel visibility
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.remove('active');
        });
        
        const targetPanel = document.getElementById(`tab-${tabId}`);
        if(targetPanel) {
            targetPanel.classList.add('active');
        }
    }
</script>
</body>
</html>
