@extends('layouts.app')

@section('content')

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

            <!-- Platform Stats Grid -->
            <div class="row g-3 mb-4">
                <div class="col-md col-sm-6 col-12">
                    <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid var(--primary);">
                        <div class="metric-icon mx-auto text-primary mb-2" style="width:36px; height:36px; line-height:36px; font-size: 14px;"><i class="fa-solid fa-users"></i></div>
                        <h6 class="text-secondary fw-semibold mb-1 small">Total Members</h6>
                        <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $stats['total_users'] }}</h4>
                    </div>
                </div>
                <div class="col-md col-sm-6 col-12">
                    <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid var(--primary);">
                        <div class="metric-icon mx-auto text-primary mb-2" style="width:36px; height:36px; line-height:36px; font-size: 14px;"><i class="fa-solid fa-briefcase"></i></div>
                        <h6 class="text-secondary fw-semibold mb-1 small">Businesses</h6>
                        <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $stats['businesses_count'] }}</h4>
                    </div>
                </div>
                <div class="col-md col-sm-6 col-12">
                    <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid var(--primary);">
                        <div class="metric-icon mx-auto text-primary mb-2" style="width:36px; height:36px; line-height:36px; font-size: 14px;"><i class="fa-solid fa-box-open"></i></div>
                        <h6 class="text-secondary fw-semibold mb-1 small">Products Listed</h6>
                        <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $stats['products_count'] }}</h4>
                    </div>
                </div>
                <div class="col-md col-sm-6 col-12">
                    <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid var(--primary);">
                        <div class="metric-icon mx-auto text-primary mb-2" style="width:36px; height:36px; line-height:36px; font-size: 14px;"><i class="fa-solid fa-gears"></i></div>
                        <h6 class="text-secondary fw-semibold mb-1 small">Services</h6>
                        <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $stats['services_count'] }}</h4>
                    </div>
                </div>
                <div class="col-md col-sm-6 col-12">
                    <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid var(--primary);">
                        <div class="metric-icon mx-auto text-primary mb-2" style="width:36px; height:36px; line-height:36px; font-size: 14px;"><i class="fa-solid fa-user-tie"></i></div>
                        <h6 class="text-secondary fw-semibold mb-1 small">Jobs Open</h6>
                        <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $stats['jobs_count'] }}</h4>
                    </div>
                </div>
            </div>

            <!-- Clickable Categories Grid -->
            <div class="glass-card mb-4 text-start">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                        <i class="fa-solid fa-tags fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Business Categories</h5>
                        <p class="text-secondary small mb-0">Explore sectors or click a category to register/edit a business under that catalog.</p>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-2 pt-2">
                    @foreach($categories as $cat)
                        <div class="col">
                            <button onclick="selectCategoryOnDashboard({{ $cat->id }})" class="btn btn-outline-primary w-100 py-2.5 rounded-3 text-start d-flex align-items-center justify-content-between px-3 hover-scale cursor-pointer bg-white" style="border-color: rgba(173,20,87,0.2); color: #2d3436; transition: all 0.2s ease;">
                                <span class="small fw-semibold text-truncate"><i class="fa-solid fa-tag text-primary me-2 opacity-75"></i> {{ $cat->name }}</span>
                                <i class="fa-solid fa-circle-chevron-right text-primary opacity-50 fs-6"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Access Section -->
            <div class="row g-4 mb-4 text-start">
                <!-- 1. Matrimony Access -->
                <div class="col-md-4">
                    <div class="glass-card h-100 d-flex flex-column justify-content-between hover-scale" style="border-top: 5px solid #ff7a59; transition: all 0.3s ease;">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-accent bg-opacity-10 text-accent p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width:40px; height:40px; background: rgba(255,122,89,0.1); color: #ff7a59;">
                                    <i class="fa-solid fa-heart fs-5"></i>
                                </div>
                                @if($user->has_matrimony_payment)
                                    <span class="badge py-1 px-2.5 rounded-pill text-white small" style="background:#00b4d8"><i class="fa-solid fa-crown me-1"></i> Premium</span>
                                @else
                                    <span class="badge bg-secondary py-1 px-2.5 rounded-pill text-white small">Free Tier</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Matrimony Seeker</h5>
                            <p class="text-secondary small mb-0">Find and connect with verified matches in your community. Set up matchmaking filters and express interest safely.</p>
                        </div>
                        <button onclick="selectDropdownTab('features')" class="btn btn-outline-dark btn-sm w-100 py-2 mt-4 rounded-3 cursor-pointer fw-semibold">
                            Access Listings <i class="fa-solid fa-arrow-right ms-1 text-primary"></i>
                        </button>
                    </div>
                </div>

                <!-- 2. Business Access -->
                <div class="col-md-4">
                    <div class="glass-card h-100 d-flex flex-column justify-content-between hover-scale" style="border-top: 5px solid #ad1457; transition: all 0.3s ease;">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                                    <i class="fa-solid fa-briefcase fs-5"></i>
                                </div>
                                @if($user->is_business)
                                    <span class="badge bg-success py-1 px-2.5 rounded-pill text-white small"><i class="fa-solid fa-check-double me-1"></i> Registered</span>
                                @else
                                    <span class="badge bg-light text-muted border py-1 px-2.5 rounded-pill small">No Listing</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Business Directory</h5>
                            <p class="text-secondary small mb-0">List your own business, showcase catalogs, active services, publish job requirements, and hire local talent.</p>
                        </div>
                        <button onclick="switchTab('business', document.querySelector('.sidebar a[onclick*=\'business\']'))" class="btn btn-outline-dark btn-sm w-100 py-2 mt-4 rounded-3 cursor-pointer fw-semibold">
                            Access Listings <i class="fa-solid fa-arrow-right ms-1 text-primary"></i>
                        </button>
                    </div>
                </div>

                <!-- 3. Volunteer Access -->
                <div class="col-md-4">
                    <div class="glass-card h-100 d-flex flex-column justify-content-between hover-scale" style="border-top: 5px solid #2ec4b6; transition: all 0.3s ease;">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-success bg-opacity-10 text-success p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width:40px; height:40px; background: rgba(46,196,182,0.1); color: #2ec4b6;">
                                    <i class="fa-solid fa-handshake-angle fs-5"></i>
                                </div>
                                @if($user->volunteer)
                                    <span class="badge bg-success py-1 px-2.5 rounded-pill text-white small">Active</span>
                                @else
                                    <span class="badge bg-light text-muted border py-1 px-2.5 rounded-pill small">Not Joined</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Volunteer Services</h5>
                            <p class="text-secondary small mb-0">Participate in community service drives, local relief campaigns, and coordinate events directly with coordinators.</p>
                        </div>
                        <button onclick="selectDropdownTab('features')" class="btn btn-outline-dark btn-sm w-100 py-2 mt-4 rounded-3 cursor-pointer fw-semibold">
                            Access Listings <i class="fa-solid fa-arrow-right ms-1 text-primary"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Profile Summary -->
            <div class="glass-card text-start">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-user me-2 text-primary"></i> Profile Information</h5>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="border-bottom pb-2">
                            <small class="text-secondary">Full Name</small>
                            <p class="mb-0 fw-semibold text-dark">{{ $user->name }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="border-bottom pb-2">
                            <small class="text-secondary">Email Address</small>
                            <p class="mb-0 fw-semibold text-dark">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="border-bottom pb-2">
                            <small class="text-secondary">Phone Number</small>
                            <p class="mb-0 fw-semibold text-dark">{{ $user->phone }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="border-bottom pb-2">
                            <small class="text-secondary">Member Since</small>
                            <p class="mb-0 fw-semibold text-dark">{{ $user->created_at->format('d M, Y') }}</p>
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

        <!-- Tab: Business Module -->
        <div class="tab-panel" id="tab-business">
            <div class="glass-card">
                
                <!-- 1. Business List View -->
                <div id="business-list-view">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-1 text-primary"><i class="fa-solid fa-briefcase me-2"></i> Manage Business</h4>
                            <p class="text-secondary small mb-0">Overview of all registered businesses under your account.</p>
                        </div>
                        @if(!$user->is_business)
                            <button class="btn btn-primary btn-sm rounded-3 cursor-pointer" onclick="toggleBusinessSection('setup')">
                                <i class="fa-solid fa-plus me-1"></i> Register New Business
                            </button>
                        @endif
                    </div>

                    @if($user->is_business)
                        <div class="table-responsive bg-white rounded-4 shadow-sm border p-2 text-start">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="border-0 rounded-start">Business Name</th>
                                        <th class="border-0">Type & Category</th>
                                        <th class="border-0 text-center">Products</th>
                                        <th class="border-0 text-center">Services</th>
                                        <th class="border-0 text-center">Active Jobs</th>
                                        <th class="border-0 text-center">Subscription</th>
                                        <th class="border-0 rounded-end text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3">
                                                    <i class="fa-solid fa-store fs-5"></i>
                                                </div>
                                                <div>
                                                    <strong class="text-dark">{{ $user->business->business_name }}</strong>
                                                    <div class="text-muted small"><i class="fa-solid fa-location-dot me-1"></i> {{ $user->business->city }}, {{ $user->business->state }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $user->business->business_type }}</span>
                                            <div class="text-muted small mt-1">{{ $user->business->category->name ?? 'Agriculture' }}</div>
                                        </td>
                                        <td class="text-center font-monospace fw-bold text-secondary">
                                            {{ $user->business->products->count() }}
                                        </td>
                                        <td class="text-center font-monospace fw-bold text-secondary">
                                            {{ $user->business->services->count() }}
                                        </td>
                                        <td class="text-center font-monospace fw-bold text-secondary">
                                            {{ count($jobs) }}
                                        </td>
                                        <td class="text-center">
                                            @if($user->business->subscription_status === 'active')
                                                <span class="badge bg-success bg-opacity-10 text-success py-1 px-2.5 rounded-pill"><i class="fa-solid fa-circle-check me-1"></i> Active</span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning py-1 px-2.5 rounded-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i> Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <button class="btn btn-outline-success btn-sm rounded-3 cursor-pointer" onclick="toggleBusinessSection('console')" title="View Details">
                                                    <i class="fa-solid fa-eye me-1"></i> View
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm rounded-3 cursor-pointer" onclick="toggleBusinessSection('edit')" title="Edit Profile">
                                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                                </button>
                                                <form action="{{ route('dashboard.business.delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you absolutely sure you want to permanently delete this business? All products, services, jobs, and applicants will be lost forever.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 cursor-pointer" title="Delete Business">
                                                        <i class="fa-solid fa-trash-can me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 bg-light rounded-4 border">
                            <div class="text-secondary mb-3 fs-1"><i class="fa-solid fa-briefcase"></i></div>
                            <h5 class="fw-bold">No Business Listed Yet</h5>
                            <p class="text-secondary small mb-4">Register your business directory today to display products, offer services, and recruit local talent.</p>
                            <button class="btn btn-primary rounded-3 px-4 py-2 cursor-pointer" onclick="toggleBusinessSection('setup')">
                                <i class="fa-solid fa-plus me-1"></i> Register New Business
                            </button>
                        </div>
                    @endif
                </div>

                <!-- 2. Business Setup Section -->
                <div id="business-setup-section" style="display: none;">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-light btn-sm rounded-3 cursor-pointer" onclick="toggleBusinessSection('list')">
                                <i class="fa-solid fa-arrow-left"></i> Back
                            </button>
                            <h5 class="fw-bold mb-0 text-dark">Initial Business Setup</h5>
                        </div>
                    </div>
                    <form action="{{ route('dashboard.business.register') }}" method="POST" enctype="multipart/form-data" class="text-start">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Business Name *</label>
                                <input type="text" name="business_name" class="form-control" placeholder="E.g. Mali Agri Services" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Business Type *</label>
                                <select name="business_type" class="form-select" required>
                                    <option value="Retailer">Retailer</option>
                                    <option value="Wholesaler">Wholesaler</option>
                                    <option value="Manufacturer">Manufacturer</option>
                                    <option value="Service Provider">Service Provider</option>
                                    <option value="Distributor">Distributor</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Business Category *</label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Email</label>
                                <input type="email" name="contact_email" class="form-control" placeholder="business@example.com">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Phone</label>
                                <input type="text" name="contact_phone" class="form-control" placeholder="Phone number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Website URL</label>
                                <input type="url" name="website" class="form-control" placeholder="https://example.com">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Opening Time</label>
                                <input type="time" name="opening_time" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Closing Time</label>
                                <input type="time" name="closing_time" class="form-control">
                            </div>
                        </div>

                        <h6 class="fw-bold text-primary mt-3 mb-3 border-bottom pb-2">Business Address Details</h6>

                        <div class="mb-3">
                            <label class="form-label">Address Line *</label>
                            <input type="text" name="address" class="form-control" placeholder="Building, Street, Landmark" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pincode *</label>
                                <input type="text" name="pincode" class="form-control" placeholder="6-digit pincode" maxlength="6" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country *</label>
                                <input type="text" name="country" value="India" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">State *</label>
                                <input type="text" name="state" class="form-control" placeholder="State" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City *</label>
                                <input type="text" name="city" class="form-control" placeholder="City" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">District *</label>
                                <input type="text" name="district" class="form-control" placeholder="District" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Taluka</label>
                                <input type="text" name="taluka" class="form-control" placeholder="Taluka">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Village</label>
                                <input type="text" name="village" class="form-control" placeholder="Village">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Description *</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Describe your business and services..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Upload Business Photos</label>
                            <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted">You can select multiple files.</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-3 shadow-sm fw-bold cursor-pointer">
                            Create Business Profile <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>

                <!-- 3. Business Console Section -->
                @if($user->is_business)
                    <div id="business-console-section" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-light btn-sm rounded-3 cursor-pointer" onclick="toggleBusinessSection('list')">
                                    <i class="fa-solid fa-arrow-left"></i> Back to List
                                </button>
                                <h5 class="fw-bold mb-0 text-dark">Business Management Console</h5>
                            </div>
                        </div>

                        @if($user->business->subscription_status !== 'active')
                            <!-- Subscription Required View -->
                            <div class="text-center p-4 rounded-4 bg-light border-warning border mb-4">
                                <div class="text-warning mb-3" style="font-size:3rem;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                                <h5 class="fw-bold">Business Premium Subscription Required</h5>
                                <p class="text-secondary small mb-4">Your business <strong>"{{ $user->business->business_name }}"</strong> is listed but needs an active subscription plan to list products, service suites, and manage job openings. Select a plan below to activate instant access.</p>
                            </div>

                            <h5 class="fw-bold mb-4 text-center">Select Your Subscription Plan</h5>
                            <div class="row g-4 justify-content-center">
                                @foreach($plans as $plan)
                                    <div class="col-md-4">
                                        <div class="card h-100 border-0 rounded-4 shadow-sm text-center p-4 relative" style="background: rgba(255,255,255,0.7); backdrop-filter:blur(5px); border: 2px solid rgba(173, 20, 87, 0.1) !important;">
                                            <div class="card-body">
                                                <h5 class="fw-bold mb-3">{{ $plan->company_type }}</h5>
                                                <div class="my-4">
                                                    <h2 class="fw-extrabold text-primary mb-0">₹{{ number_format($plan->price, 0) }}</h2>
                                                    <small class="text-muted">for {{ $plan->duration_years }} year(s)</small>
                                                </div>
                                                <p class="small text-secondary mb-4">{{ $plan->description ?? 'List products, publish active jobs, accept applicants, and get verified.' }}</p>
                                                <button class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold shadow-sm cursor-pointer" onclick="startRazorpayPayment({{ $plan->id }}, {{ $plan->price }})">
                                                    Select Plan <i class="fa-solid fa-arrow-right ms-1"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Razorpay SDK & Loader/Modal JS script -->
                            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                            <script>
                                function startRazorpayPayment(planId, price) {
                                    const csrfToken = '{{ csrf_token() }}';
                                    
                                    fetch("{{ route('dashboard.business.subscribe') }}", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json",
                                            "X-CSRF-TOKEN": csrfToken
                                        },
                                        body: JSON.stringify({ plan_id: planId })
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (!data.success) {
                                            alert(data.message || "Failed to create Razorpay Order.");
                                            return;
                                        }

                                        const options = {
                                            key: data.key_id,
                                            amount: data.amount,
                                            currency: data.currency,
                                            name: "Mali Setu Enterprise",
                                            description: "Business Premium Plan Activation",
                                            order_id: data.order_id,
                                            handler: function (response) {
                                                // Verify payment signature
                                                fetch("{{ route('dashboard.business.verify-payment') }}", {
                                                    method: "POST",
                                                    headers: {
                                                        "Content-Type": "application/json",
                                                        "X-CSRF-TOKEN": csrfToken
                                                    },
                                                    body: JSON.stringify({
                                                        razorpay_payment_id: response.razorpay_payment_id,
                                                        razorpay_order_id: response.razorpay_order_id,
                                                        razorpay_signature: response.razorpay_signature,
                                                        transaction_id: data.transaction_id
                                                    })
                                                })
                                                .then(verifyRes => verifyRes.json())
                                                .then(verifyData => {
                                                    if (verifyData.success) {
                                                        alert("Subscription activated successfully!");
                                                        window.location.reload();
                                                    } else {
                                                        alert(verifyData.message || "Payment verification failed.");
                                                    }
                                                })
                                                .catch(err => {
                                                    console.error(err);
                                                    alert("Payment verification request failed.");
                                                });
                                            },
                                            prefill: {
                                                name: "{{ $user->name }}",
                                                email: "{{ $user->email }}",
                                                contact: "{{ $user->phone }}"
                                            },
                                            theme: {
                                                color: "#ad1457"
                                            }
                                        };

                                        const rzp = new Razorpay(options);
                                        rzp.open();
                                    })
                                    .catch(err => {
                                        console.error(err);
                                        alert("Failed to initialize transaction. Please try again.");
                                    });
                                }
                            </script>

                        @else
                            <!-- Active Premium Console Content -->
                            <div class="row align-items-center mb-4 p-3 rounded-4 bg-success bg-opacity-10 mx-0 text-start">
                                <div class="col-auto">
                                    <div class="bg-success text-white p-3 rounded-3"><i class="fa-solid fa-store fs-3"></i></div>
                                </div>
                                <div class="col text-start">
                                    <h5 class="fw-bold mb-1">{{ $user->business->business_name }}</h5>
                                    <p class="small text-secondary mb-0">Premium Business Profile is active. Subscription valid until: <strong>{{ \Carbon\Carbon::parse($user->business->subscription_expires_at)->format('d M, Y') }}</strong></p>
                                </div>
                                <div class="col-auto text-end">
                                    <span class="badge bg-success py-2 px-3"><i class="fa-solid fa-circle-check me-1"></i> Active Premium</span>
                                </div>
                            </div>

                            <!-- Inner Navigation Tabs -->
                            <ul class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded-3" id="business-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#inner-profile" type="button" role="tab"><i class="fa-solid fa-address-card me-1"></i> Profile Info</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#inner-products" type="button" role="tab"><i class="fa-solid fa-box me-1"></i> Products</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#inner-services" type="button" role="tab"><i class="fa-solid fa-server me-1"></i> Services</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#inner-jobs" type="button" role="tab"><i class="fa-solid fa-briefcase me-1"></i> Jobs Hub</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="business-tabs-content">
                                <!-- Inner Tab 1: Profile -->
                                <div class="tab-pane fade show active p-2" id="inner-profile" role="tabpanel">
                                    <div class="alert alert-info py-2 px-3 small rounded-3 mb-3 text-start"><i class="fa-solid fa-circle-info me-1"></i> This profile information is displayed in the public local directories.</div>
                                    
                                    <div class="row text-start">
                                        <div class="col-md-6 mb-3"><strong>Business Name:</strong> <span class="text-secondary">{{ $user->business->business_name }}</span></div>
                                        <div class="col-md-6 mb-3"><strong>Business Type:</strong> <span class="text-secondary">{{ $user->business->business_type }}</span></div>
                                        <div class="col-md-6 mb-3"><strong>Category:</strong> <span class="text-secondary">{{ $user->business->category->name ?? 'N/A' }}</span></div>
                                        <div class="col-md-6 mb-3"><strong>Contact Email:</strong> <span class="text-secondary">{{ $user->business->contact_email ?? 'N/A' }}</span></div>
                                        <div class="col-md-6 mb-3"><strong>Contact Phone:</strong> <span class="text-secondary">{{ $user->business->contact_phone ?? 'N/A' }}</span></div>
                                        <div class="col-md-6 mb-3"><strong>Website:</strong> <span class="text-secondary">{{ $user->business->website ?? 'N/A' }}</span></div>
                                        <div class="col-md-6 mb-3"><strong>Timings:</strong> <span class="text-secondary">{{ $user->business->opening_time ?? '09:00' }} - {{ $user->business->closing_time ?? '21:00' }}</span></div>
                                        <div class="col-md-12 mb-3"><strong>Full Address:</strong> <span class="text-secondary">{{ $user->business->address }}, {{ $user->business->city }}, {{ $user->business->district }}, {{ $user->business->state }} - {{ $user->business->pincode }}</span></div>
                                        <div class="col-md-12 mb-3"><strong>Description:</strong> <p class="text-secondary mt-1 small bg-light p-3 rounded-3">{{ $user->business->description }}</p></div>
                                    </div>
                                    
                                    @if($user->business->photo)
                                        <div class="d-flex gap-2 mt-2">
                                            @foreach(explode(',', $user->business->photo) as $img)
                                                @if(trim($img))
                                                    <img src="{{ asset('storage/' . trim($img)) }}" class="rounded shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Inner Tab 2: Products -->
                                <div class="tab-pane fade p-2 text-start" id="inner-products" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="fw-bold mb-0">Active Products Catalog</h5>
                                        <button class="btn btn-primary btn-sm rounded-3 cursor-pointer" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fa-solid fa-plus me-1"></i> Add New Product</button>
                                    </div>

                                    @if($user->business->products && count($user->business->products) > 0)
                                        <div class="table-responsive">
                                            <table class="table align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Product Name</th>
                                                        <th>Cost</th>
                                                        <th>Description</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->business->products as $prod)
                                                        <tr>
                                                            <td>
                                                                @if($prod->image_path)
                                                                    <img src="{{ asset('storage/' . $prod->image_path) }}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                                                @else
                                                                    <div class="bg-light text-secondary rounded shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="fa-solid fa-box"></i></div>
                                                                @endif
                                                            </td>
                                                            <td><strong class="text-dark">{{ $prod->name }}</strong></td>
                                                            <td><span class="text-primary fw-bold">₹{{ $prod->cost ?? '0.00' }}</span></td>
                                                            <td><span class="text-secondary small">{{ Str::limit($prod->description, 50) }}</span></td>
                                                            <td>
                                                                <form action="{{ route('dashboard.business.products.delete', $prod->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 cursor-pointer"><i class="fa-solid fa-trash-can"></i></button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5 bg-light rounded-4 w-100">
                                            <p class="text-secondary small mb-0">No products added yet. Click "Add New Product" to populate your catalog.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Inner Tab 3: Services -->
                                <div class="tab-pane fade p-2 text-start" id="inner-services" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="fw-bold mb-0">Active Services Suite</h5>
                                        <button class="btn btn-primary btn-sm rounded-3 cursor-pointer" data-bs-toggle="modal" data-bs-target="#addServiceModal"><i class="fa-solid fa-plus me-1"></i> Add New Service</button>
                                    </div>

                                    @if($user->business->services && count($user->business->services) > 0)
                                        <div class="table-responsive">
                                            <table class="table align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Service Name</th>
                                                        <th>Rate / Cost</th>
                                                        <th>Description</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->business->services as $serv)
                                                        <tr>
                                                            <td>
                                                                @if($serv->image_path)
                                                                    <img src="{{ asset('storage/' . $serv->image_path) }}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                                                @else
                                                                    <div class="bg-light text-secondary rounded shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="fa-solid fa-server"></i></div>
                                                                @endif
                                                            </td>
                                                            <td><strong class="text-dark">{{ $serv->name }}</strong></td>
                                                            <td><span class="text-primary fw-bold">₹{{ $serv->cost ?? '0.00' }}</span></td>
                                                            <td><span class="text-secondary small">{{ Str::limit($serv->description, 50) }}</span></td>
                                                            <td>
                                                                <form action="{{ route('dashboard.business.services.delete', $serv->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 cursor-pointer"><i class="fa-solid fa-trash-can"></i></button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5 bg-light rounded-4 w-100">
                                            <p class="text-secondary small mb-0">No services added yet. Click "Add New Service" to list your offerings.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Inner Tab 4: Jobs Hub -->
                                <div class="tab-pane fade p-2 text-start" id="inner-jobs" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="fw-bold mb-0">Enterprise Job Postings</h5>
                                        <button class="btn btn-primary btn-sm rounded-3 cursor-pointer" data-bs-toggle="modal" data-bs-target="#addJobModal"><i class="fa-solid fa-plus me-1"></i> Post New Job</button>
                                    </div>

                                    @if(count($jobs) > 0)
                                        @foreach($jobs as $job)
                                            <div class="card border-0 rounded-4 shadow-sm p-4 mb-4" style="background: rgba(255,255,255,0.7)">
                                                <div class="row align-items-start text-start">
                                                    <div class="col text-start">
                                                        <h6 class="fw-bold mb-1">{{ $job->title }}</h6>
                                                        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                                            <span class="badge bg-light text-secondary border small">{{ ucfirst($job->employment_type) }}</span>
                                                            <span class="badge bg-light text-secondary border small">{{ ucfirst($job->experience_level) }}</span>
                                                            <span class="badge bg-light text-secondary border small"><i class="fa-solid fa-location-dot me-1"></i> {{ $job->location }}</span>
                                                            <span class="text-primary small fw-semibold">₹{{ $job->salary_range ?? 'Not specified' }}</span>
                                                        </div>
                                                        <p class="small text-secondary mb-3">{{ Str::limit($job->description, 180) }}</p>
                                                        
                                                        <!-- Candidates list -->
                                                        @if(count($job->applications) > 0)
                                                            <h6 class="small fw-bold border-top pt-3 mb-3"><i class="fa-solid fa-users me-1 text-primary"></i> Applicants ({{ count($job->applications) }})</h6>
                                                            <div class="list-group list-group-flush">
                                                                @foreach($job->applications as $app)
                                                                    <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0 py-2.5">
                                                                        <div class="text-start">
                                                                            <strong class="text-dark small">{{ $app->user->name }}</strong> 
                                                                            <span class="text-secondary small">({{ $app->user->phone }})</span>
                                                                            @if($app->resume_path)
                                                                                <a href="{{ asset('storage/' . $app->resume_path) }}" class="btn btn-link btn-sm p-0 ms-2 text-decoration-none small" target="_blank"><i class="fa-solid fa-file-pdf text-danger"></i> Resume</a>
                                                                            @endif
                                                                            @if($app->employer_notes)
                                                                                <div class="text-muted small italic mt-1">Note: {{ $app->employer_notes }}</div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            @if($app->status === 'pending')
                                                                                <button class="btn btn-success btn-xs py-1 px-2 rounded-2 small cursor-pointer" onclick="openModerateModal({{ $app->id }}, 'accepted')"><i class="fa-solid fa-check"></i> Accept</button>
                                                                                <button class="btn btn-danger btn-xs py-1 px-2 rounded-2 small cursor-pointer" onclick="openModerateModal({{ $app->id }}, 'rejected')"><i class="fa-solid fa-times"></i> Reject</button>
                                                                            @else
                                                                                <span class="badge {{ $app->status === 'accepted' ? 'bg-success' : 'bg-danger' }} small">{{ ucfirst($app->status) }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="small text-muted border-top pt-3"><i class="fa-solid fa-users-slash me-1"></i> No candidate applications received yet.</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-auto text-end">
                                                        <div class="d-flex gap-2">
                                                            <button class="btn btn-outline-secondary btn-sm rounded-3 cursor-pointer" onclick="openEditJobModal({{ json_encode($job) }})"><i class="fa-solid fa-pen-to-square"></i></button>
                                                            <form action="{{ route('dashboard.business.jobs.toggle', $job->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-info btn-sm rounded-3 cursor-pointer"><i class="fa-solid {{ $job->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i></button>
                                                            </form>
                                                            <form action="{{ route('dashboard.business.jobs.delete', $job->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 cursor-pointer"><i class="fa-solid fa-trash-can"></i></button>
                                                            </form>
                                                        </div>
                                                        <div class="mt-3">
                                                            @if($job->is_active)
                                                                <span class="badge bg-success small"><i class="fa-solid fa-circle-check"></i> Active</span>
                                                            @else
                                                                <span class="badge bg-secondary small">Inactive</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-5 bg-light rounded-4 w-100">
                                            <p class="text-secondary small mb-0">No job postings created yet. Click "Post New Job" to recruit talent.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- 4. Business Edit Section -->
                    <div id="business-edit-section" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-light btn-sm rounded-3 cursor-pointer" onclick="toggleBusinessSection('list')">
                                    <i class="fa-solid fa-arrow-left"></i> Back to List
                                </button>
                                <h5 class="fw-bold mb-0 text-dark">Edit Business Profile Info</h5>
                            </div>
                        </div>
                        <form action="{{ route('dashboard.business.update') }}" method="POST" enctype="multipart/form-data" class="text-start">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Business Name *</label>
                                    <input type="text" name="business_name" class="form-control" value="{{ $user->business->business_name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Business Type *</label>
                                    <select name="business_type" class="form-select" required>
                                        <option value="Retailer" {{ $user->business->business_type == 'Retailer' ? 'selected' : '' }}>Retailer</option>
                                        <option value="Wholesaler" {{ $user->business->business_type == 'Wholesaler' ? 'selected' : '' }}>Wholesaler</option>
                                        <option value="Manufacturer" {{ $user->business->business_type == 'Manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                        <option value="Service Provider" {{ $user->business->business_type == 'Service Provider' ? 'selected' : '' }}>Service Provider</option>
                                        <option value="Distributor" {{ $user->business->business_type == 'Distributor' ? 'selected' : '' }}>Distributor</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Business Category *</label>
                                    <select name="category_id" class="form-select" required>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $user->business->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Email</label>
                                    <input type="email" name="contact_email" class="form-control" value="{{ $user->business->contact_email }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="text" name="contact_phone" class="form-control" value="{{ $user->business->contact_phone }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Website URL</label>
                                    <input type="url" name="website" class="form-control" value="{{ $user->business->website }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Opening Time</label>
                                    <input type="time" name="opening_time" class="form-control" value="{{ $user->business->opening_time }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Closing Time</label>
                                    <input type="time" name="closing_time" class="form-control" value="{{ $user->business->closing_time }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address Line *</label>
                                <input type="text" name="address" class="form-control" value="{{ $user->business->address }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pincode *</label>
                                    <input type="text" name="pincode" class="form-control" value="{{ $user->business->pincode }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Country *</label>
                                    <input type="text" name="country" value="{{ $user->business->country ?? 'India' }}" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State *</label>
                                    <input type="text" name="state" class="form-control" value="{{ $user->business->state }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City *</label>
                                    <input type="text" name="city" class="form-control" value="{{ $user->business->city }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">District *</label>
                                    <input type="text" name="district" class="form-control" value="{{ $user->business->district }}" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Taluka</label>
                                    <input type="text" name="taluka" class="form-control" value="{{ $user->business->taluka }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Village</label>
                                    <input type="text" name="village" class="form-control" value="{{ $user->business->village }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description *</label>
                                <textarea name="description" class="form-control" rows="3" required>{{ $user->business->description }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Update Business Photos</label>
                                <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                                <small class="text-muted">This will replace current images.</small>
                            </div>

                            @if($user->business->photo)
                                <div class="d-flex gap-2 mb-4">
                                    @foreach(explode(',', $user->business->photo) as $img)
                                        @if(trim($img))
                                            <img src="{{ asset('storage/' . trim($img)) }}" class="rounded shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 cursor-pointer">Update Profile Info</button>
                        </form>
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

<!-- Modal: Add Product -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.business.products.add') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if($user->is_business)
                        <input type="hidden" name="business_id" value="{{ $user->business->id }}">
                    @endif
                    <div class="mb-3 text-start">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="E.g. High Quality Seeds">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Cost (in INR) *</label>
                        <input type="number" name="cost" class="form-control" min="0" step="0.01" required placeholder="E.g. 299">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Product specifications and details..."></textarea>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Service -->
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">Add New Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.business.services.add') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if($user->is_business)
                        <input type="hidden" name="business_id" value="{{ $user->business->id }}">
                    @endif
                    <div class="mb-3 text-start">
                        <label class="form-label">Service Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="E.g. Drip Irrigation Installation">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Service Charge / Cost *</label>
                        <input type="number" name="cost" class="form-control" min="0" step="0.01" required placeholder="E.g. 1500">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Service timeline and information..."></textarea>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Service Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Job -->
<div class="modal fade" id="addJobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">Post a New Job Opening</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.business.jobs.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if($user->is_business)
                        <input type="hidden" name="business_id" value="{{ $user->business->id }}">
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Job Title *</label>
                            <input type="text" name="title" class="form-control" required placeholder="E.g. Senior Agronomist">
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Location *</label>
                            <input type="text" name="location" class="form-control" required placeholder="E.g. Pune, Maharashtra">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Employment Type *</label>
                            <select name="employment_type" class="form-select" required>
                                <option value="full-time">Full-Time</option>
                                <option value="part-time">Part-Time</option>
                                <option value="contract">Contract</option>
                                <option value="freelance">Freelance</option>
                                <option value="internship">Internship</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Experience Level *</label>
                            <select name="experience_level" class="form-select" required>
                                <option value="entry">Entry Level</option>
                                <option value="junior">Junior</option>
                                <option value="mid">Mid Level</option>
                                <option value="senior">Senior</option>
                                <option value="executive">Executive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Job Sector / Category *</label>
                            <input type="text" name="category" class="form-control" required placeholder="E.g. Agriculture, Retail">
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Salary Range</label>
                            <input type="text" name="salary_range" class="form-control" placeholder="E.g. 40k - 50k / Month">
                        </div>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Job Description *</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Day-to-day duties and goals..."></textarea>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Requirements *</label>
                        <textarea name="requirements" class="form-control" rows="3" required placeholder="Skills, certifications, and qualification criteria..."></textarea>
                    </div>
                    <input type="hidden" name="job_type" value="Full-Time">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Publish Job Opening</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Job -->
<div class="modal fade" id="editJobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">Update Job Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editJobForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Job Title *</label>
                            <input type="text" name="title" id="edit_job_title" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Location *</label>
                            <input type="text" name="location" id="edit_job_location" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Employment Type *</label>
                            <select name="employment_type" id="edit_job_employment_type" class="form-select" required>
                                <option value="full-time">Full-Time</option>
                                <option value="part-time">Part-Time</option>
                                <option value="contract">Contract</option>
                                <option value="freelance">Freelance</option>
                                <option value="internship">Internship</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Experience Level *</label>
                            <select name="experience_level" id="edit_job_experience_level" class="form-select" required>
                                <option value="entry">Entry Level</option>
                                <option value="junior">Junior</option>
                                <option value="mid">Mid Level</option>
                                <option value="senior">Senior</option>
                                <option value="executive">Executive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Job Sector / Category *</label>
                            <input type="text" name="category" id="edit_job_category" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Salary Range</label>
                            <input type="text" name="salary_range" id="edit_job_salary_range" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Job Description *</label>
                        <textarea name="description" id="edit_job_description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Requirements *</label>
                        <textarea name="requirements" id="edit_job_requirements" class="form-control" rows="3" required></textarea>
                    </div>
                    <input type="hidden" name="job_type" value="Full-Time">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Moderate Candidate Applicant Status -->
<div class="modal fade" id="moderateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">Moderate Job Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="moderateForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="status" id="moderate_status">
                    <p class="text-secondary small mb-3 text-start">You are updating the applicant's status to: <strong class="text-primary" id="moderate_status_text"></strong>. You can optionally add notes or comments for the candidate.</p>
                    <div class="mb-3 text-start">
                        <label class="form-label">Employer Notes / Feedback</label>
                        <textarea name="employer_notes" class="form-control" rows="4" placeholder="E.g. We loved your interview! We will send the onboarding details shortly."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm & Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
