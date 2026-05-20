@extends('layouts.app')

@section('content')
<style>
    /* Premium Hover Scale Micro-Animations */
    .hover-scale {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hover-scale:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 12px 24px rgba(173, 20, 87, 0.12) !important;
    }
    
    /* Elegant Dynamic Category HSL Cards */
    .category-grid-card {
        border-radius: 16px;
        padding: 20px;
        text-align: left;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border: 1px solid rgba(0,0,0,0.03);
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .category-icon {
        font-size: 1.8rem;
        margin-bottom: 12px;
    }
    .category-title {
        font-size: 0.95rem;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-truncate: ellipsis;
    }
    .category-subtitle {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    /* Slick Directory Business Cards */
    .directory-biz-card {
        background: var(--glass);
        border: 1px solid rgba(173, 20, 87, 0.08);
        border-radius: 20px;
        padding: 22px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 6px 15px rgba(0,0,0,0.02);
    }
    .biz-thumbnail {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        object-fit: cover;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .biz-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        background: rgba(173, 20, 87, 0.06);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }

    /* Glassmorphic Business Popup Invite */
    .modal-glassmorphic {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    /* Premium Banner Slider styling */
    .banner-slide-wrapper {
        border-radius: 24px;
        overflow: hidden;
    }
</style>

<div class="row g-4">
    <!-- Main Content Panel -->
    <main class="col-12 px-md-4 py-4">

        <!-- Tab 1: Overview -->
        <div class="tab-panel active" id="tab-overview">
            
            <!-- Welcome Banner -->
            <div class="welcome-banner mb-4">
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

            <!-- Platform Stats Grid (Only displays counts representing currently logged-in user!) -->
            <div class="row g-3 mb-4 text-start">
                <div class="col-md col-sm-6 col-12">
                    <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid var(--primary);">
                        <div class="metric-icon mx-auto text-primary mb-2" style="width:36px; height:36px; line-height:36px; font-size: 14px;"><i class="fa-solid fa-briefcase"></i></div>
                        <h6 class="text-secondary fw-semibold mb-1 small">Your Business</h6>
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

            <!-- Autoplay Promotional Banners Carousel -->
            @if($banners && $banners->count() > 0)
                <div id="homepageBannersCarousel" class="carousel slide mb-4 rounded-4 overflow-hidden shadow-sm" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach($banners as $index => $banner)
                            <button type="button" data-bs-target="#homepageBannersCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach($banners as $index => $banner)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" data-bs-interval="4500">
                                <div class="banner-slide-wrapper position-relative" style="height: 250px; background: url('{{ asset('storage/' . $banner->image_path) }}') center/cover no-repeat;">
                                    <div class="banner-overlay position-absolute w-100 h-100 top-0 start-0 d-flex flex-column justify-content-end p-4 text-start" style="background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.75) 100%);">
                                        <h3 class="fw-bold text-white mb-2">{{ $banner->title }}</h3>
                                        @if($banner->url)
                                            <a href="{{ $banner->url }}" target="_blank" class="btn btn-primary btn-sm rounded-pill px-4 align-self-start fw-semibold shadow-sm">Explore More <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#homepageBannersCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#homepageBannersCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            @endif

            <!-- Search Business input bar -->
            <div class="glass-card mb-4 text-start">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h5 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-magnifying-glass text-primary me-2"></i> Search Verified Business Directory</h5>
                        <p class="text-secondary small mb-0">Browse through active directories, verify services or look up agricultural vendors.</p>
                    </div>
                    <div class="col-md-5 mt-3 mt-md-0">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" id="business-search-input" class="form-control border-start-0" placeholder="Search by name, city, state or keywords...">
                            <button class="btn btn-primary px-4" type="button" onclick="performBusinessSearch()"><i class="fa-solid fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attractive Dynamic HSL Categories Grid -->
            <div class="glass-card mb-4 text-start">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                        <i class="fa-solid fa-tags fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Explore Business Categories</h5>
                        <p class="text-secondary small mb-0">Select any industry below to display active businesses and products.</p>
                    </div>
                </div>
                <div class="row g-3 pt-2">
                    @foreach($categories->take(10) as $index => $cat)
                        <div class="col-6 col-sm-4 col-md-3">
                            <div onclick="selectCategoryOnDashboard({{ $cat->id }})" class="category-grid-card hover-scale cursor-pointer" style="background: linear-gradient(135deg, hsl({{ (36 * $index) % 360 }}, 80%, 96%) 0%, hsl({{ (36 * $index) % 360 }}, 80%, 91%) 100%); border-left: 5px solid hsl({{ (36 * $index) % 360 }}, 80%, 40%);">
                                <div>
                                    <div class="category-icon" style="color: hsl({{ (36 * $index) % 360 }}, 80%, 40%);"><i class="fa-solid fa-tag"></i></div>
                                    <h6 class="category-title text-dark fw-bold mb-0">{{ $cat->name }}</h6>
                                </div>
                                <span class="category-subtitle text-secondary mt-3">Browse Listings</span>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- View All card -->
                    <div class="col-6 col-sm-4 col-md-3">
                        <div data-bs-toggle="modal" data-bs-target="#allCategoriesModal" class="category-grid-card hover-scale cursor-pointer text-center justify-content-center align-items-center" style="background: linear-gradient(135deg, #f3f3f3 0%, #e9e9e9 100%); border-left: 5px solid #6c757d; min-height: 125px;">
                            <div class="category-icon text-secondary mb-1"><i class="fa-solid fa-list-ul"></i></div>
                            <h6 class="category-title text-dark fw-bold mb-0">View All</h6>
                            <span class="category-subtitle text-secondary">Show All sectors</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active directory results pane (by default loads 10 featured businesses) -->
            <div class="glass-card mb-4 text-start" id="directory-listings-section">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-success bg-opacity-10 text-success p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                            <i class="fa-solid fa-store fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark" id="directory-panel-title">Active verified Directories</h5>
                            <p class="text-secondary small mb-0" id="directory-panel-subtitle">Displaying 10 newly registered and verified business houses.</p>
                        </div>
                    </div>
                    <!-- Clear filter option -->
                    <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="clear-search-filter" onclick="clearDirectorySearch()" style="display: none;"><i class="fa-solid fa-rotate-left"></i> Reset Filter</button>
                </div>

                <!-- Business Grid -->
                <div class="row g-4" id="directory-listings-grid">
                    @if($featuredBusinesses && $featuredBusinesses->count() > 0)
                        @foreach($featuredBusinesses as $biz)
                            <div class="col-md-6 col-12">
                                <div class="directory-biz-card text-start">
                                    <div class="row align-items-start g-3 text-start">
                                        <div class="col-auto text-start">
                                            @if($biz->photo)
                                                <img src="{{ asset('storage/' . trim(explode(',', $biz->photo)[0])) }}" alt="Logo" class="biz-thumbnail">
                                            @else
                                                <div class="biz-placeholder"><i class="fa-solid fa-store"></i></div>
                                            @endif
                                        </div>
                                        <div class="col text-start">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <h6 class="fw-bold mb-0 text-dark">{{ $biz->business_name }}</h6>
                                                <span class="badge bg-light text-primary border small">{{ $biz->business_type }}</span>
                                            </div>
                                            <div class="text-secondary small mb-2 mt-1"><i class="fa-solid fa-tag me-1 text-primary"></i> {{ $biz->category->name ?? 'Agriculture' }}</div>
                                            <p class="text-secondary small mb-3">{{ Str::limit($biz->description, 110) }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="border-top pt-3 mt-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                        <div class="small text-muted"><i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ $biz->city }}, {{ $biz->state }}</div>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="small text-secondary"><i class="fa-solid fa-box-open text-primary me-1"></i> {{ $biz->products->count() }} items</span>
                                            <span class="small text-secondary"><i class="fa-solid fa-gears text-primary me-1"></i> {{ $biz->services->count() }} service</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 pt-3 border-top d-flex gap-2">
                                        @if($biz->contact_phone)
                                            <a href="tel:{{ $biz->contact_phone }}" class="btn btn-light btn-sm flex-fill rounded-3 text-dark small fw-semibold"><i class="fa-solid fa-phone me-1 text-success"></i> Call</a>
                                        @endif
                                        @if($biz->contact_email)
                                            <a href="mailto:{{ $biz->contact_email }}" class="btn btn-light btn-sm flex-fill rounded-3 text-dark small fw-semibold"><i class="fa-solid fa-envelope me-1 text-primary"></i> Mail</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center py-5">
                            <p class="text-secondary small mb-0">No active verified businesses listed at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Access Section -->
            <div class="row g-4 mb-4 text-start">
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
                        <a href="{{ route('dashboard.business.index') }}" class="btn btn-outline-dark btn-sm w-100 py-2 mt-4 rounded-3 cursor-pointer fw-semibold text-center">
                            Access Listings <i class="fa-solid fa-arrow-right ms-1 text-primary"></i>
                        </a>
                    </div>
                </div>

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
                                <a href="{{ route('dashboard.business.index') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> List New Business</a>
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

<!-- Modal: View All Business Categories -->
<div class="modal fade" id="allCategoriesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">All Business Categories</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-secondary small mb-4">Click any sector below to filter directory listings immediately.</p>
                <div class="row g-2">
                    @foreach($categories as $index => $cat)
                        <div class="col-md-4 col-6">
                            <button onclick="selectCategoryOnDashboard({{ $cat->id }}); bootstrap.Modal.getInstance(document.getElementById('allCategoriesModal')).hide();" class="btn btn-outline-primary w-100 py-2.5 rounded-3 text-start d-flex align-items-center justify-content-between px-3" style="border-color: rgba(173,20,87,0.15); color: #2d3436;">
                                <span class="small fw-semibold text-truncate"><i class="fa-solid fa-tag text-primary me-2"></i> {{ $cat->name }}</span>
                                <i class="fa-solid fa-chevron-right text-primary opacity-50"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Premium business setup creation invite popup (Screenshot 4 style) -->
@if(!$user->is_business)
<div class="modal fade" id="businessSetupInviteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-glassmorphic border-0 p-3 text-center">
            <div class="modal-body p-4">
                <div class="text-primary mb-4 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: rgba(173,20,87,0.08); border-radius: 50%; font-size: 2.2rem;">
                    <i class="fa-solid fa-briefcase text-primary"></i>
                </div>
                <h3 class="fw-bold text-dark mb-2">Establish Your Enterprise</h3>
                <p class="text-secondary small px-2 mb-4">You currently do not have any registered business under your name. Join our elite local vendor registry today! List your inventory catalogs, broadcast active service packages, and publish local jobs to recruit community talents.</p>
                
                <div class="d-flex flex-column gap-2 mt-4">
                    <a href="{{ route('dashboard.business.index') }}?register=true" class="btn btn-primary py-2.5 rounded-3 fw-bold shadow-sm"><i class="fa-solid fa-plus me-1"></i> Register Business Now</a>
                    <button type="button" class="btn btn-link text-decoration-none text-muted small mt-2" data-bs-dismiss="modal">Later, Show Overview</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    // Search directory listings via AJAX
    function performBusinessSearch() {
        const query = document.getElementById('business-search-input').value.trim();
        if (!query) return;

        showLoaderGrid();
        
        fetch(`/dashboard/businesses/search?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderDirectoryBusinesses(data.businesses);
                    document.getElementById('directory-panel-title').innerText = "Search Results";
                    document.getElementById('directory-panel-subtitle').innerText = `Displaying business listings matching "${query}".`;
                    document.getElementById('clear-search-filter').style.display = 'inline-block';
                    scrollToDirectory();
                }
            })
            .catch(err => {
                console.error(err);
                alert("Search lookup failed. Please try again.");
            });
    }

    // Category selection triggers AJAX directory updates
    function fetchCategoryBusinesses(categoryId) {
        showLoaderGrid();
        
        fetch(`/dashboard/businesses/category/${categoryId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderDirectoryBusinesses(data.businesses);
                    document.getElementById('directory-panel-title').innerText = "Category Listings";
                    document.getElementById('directory-panel-subtitle').innerText = "Displaying verified businesses matching category filters.";
                    document.getElementById('clear-search-filter').style.display = 'inline-block';
                    scrollToDirectory();
                }
            })
            .catch(err => {
                console.error(err);
                alert("Failed to filter category directories.");
            });
    }

    // Helper functions for directory rendering
    function showLoaderGrid() {
        document.getElementById('directory-listings-grid').innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-secondary small mt-2 mb-0">Searching directories...</p>
            </div>
        `;
    }

    function renderDirectoryBusinesses(businesses) {
        const grid = document.getElementById('directory-listings-grid');
        if (!businesses || businesses.length === 0) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <p class="text-secondary small mb-0">No matching business listings found.</p>
                </div>
            `;
            return;
        }

        let html = '';
        businesses.forEach(biz => {
            const photoEl = biz.photo 
                ? `<img src="${biz.photo}" alt="Logo" class="biz-thumbnail">` 
                : `<div class="biz-placeholder"><i class="fa-solid fa-store"></i></div>`;
            
            const callBtn = biz.contact_phone 
                ? `<a href="tel:${biz.contact_phone}" class="btn btn-light btn-sm flex-fill rounded-3 text-dark small fw-semibold"><i class="fa-solid fa-phone me-1 text-success"></i> Call</a>` 
                : '';
                
            const mailBtn = biz.contact_email 
                ? `<a href="mailto:${biz.contact_email}" class="btn btn-light btn-sm flex-fill rounded-3 text-dark small fw-semibold"><i class="fa-solid fa-envelope me-1 text-primary"></i> Mail</a>` 
                : '';

            html += `
                <div class="col-md-6 col-12">
                    <div class="directory-biz-card text-start">
                        <div class="row align-items-start g-3 text-start">
                            <div class="col-auto text-start">
                                ${photoEl}
                            </div>
                            <div class="col text-start">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h6 class="fw-bold mb-0 text-dark">${biz.business_name}</h6>
                                    <span class="badge bg-light text-primary border small">${biz.business_type}</span>
                                </div>
                                <div class="text-secondary small mb-2 mt-1"><i class="fa-solid fa-tag me-1 text-primary"></i> ${biz.category_name}</div>
                                <p class="text-secondary small mb-3">${biz.description ? biz.description.substring(0, 110) + '...' : ''}</p>
                            </div>
                        </div>
                        
                        <div class="border-top pt-3 mt-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="small text-muted"><i class="fa-solid fa-location-dot me-1 text-primary"></i> ${biz.city}, ${biz.state}</div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="small text-secondary"><i class="fa-solid fa-box-open text-primary me-1"></i> ${biz.products_count} items</span>
                                <span class="small text-secondary"><i class="fa-solid fa-gears text-primary me-1"></i> ${biz.services_count} service</span>
                            </div>
                        </div>
                        
                        <div class="mt-3 pt-3 border-top d-flex gap-2">
                            ${callBtn}
                            ${mailBtn}
                        </div>
                    </div>
                </div>
            `;
        });
        grid.innerHTML = html;
    }

    function clearDirectorySearch() {
        window.location.reload();
    }

    function scrollToDirectory() {
        document.getElementById('directory-listings-section').scrollIntoView({ behavior: 'smooth' });
    }

    // Startup configuration & Tab URL Preloaders
    document.addEventListener('DOMContentLoaded', () => {
        // Parse tabs from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('tab')) {
            selectDropdownTab(urlParams.get('tab'));
        }

        // Trigger Category loading if requested via query parameter
        if (urlParams.has('browse_category_id')) {
            fetchCategoryBusinesses(urlParams.get('browse_category_id'));
        }

        // Pincode lookups inside Profile edit coordinates form
        const pincodeInput = document.querySelector('input[name="pincode"]');
        if (pincodeInput) {
            pincodeInput.addEventListener('input', function() {
                const pincode = this.value.trim();
                if (pincode.length === 6 && /^\d+$/.test(pincode)) {
                    pincodeInput.classList.add('is-valid');
                    
                    fetch(`https://api.postalpincode.in/pincode/${pincode}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data && data[0] && data[0].Status === 'Success') {
                                const postOffice = data[0].PostOffice[0];
                                const state = postOffice.State;
                                const city = postOffice.District; 
                                
                                const stateInput = document.querySelector('input[name="state"]');
                                const cityInput = document.querySelector('input[name="city"]');
                                const districtInput = document.querySelector('input[name="district"]');
                                
                                if (stateInput) {
                                    stateInput.value = state;
                                    stateInput.classList.add('is-valid');
                                }
                                if (cityInput) {
                                    cityInput.value = city;
                                    cityInput.classList.add('is-valid');
                                }
                                if (districtInput) {
                                    districtInput.value = city;
                                    districtInput.classList.add('is-valid');
                                }
                            }
                        })
                        .catch(err => console.error('Pincode fetch coordinate error:', err));
                }
            });
        }

        // Show Business Setup Invite Popup (Screenshot 4) once per session
        @if(!$user->is_business)
            if (!sessionStorage.getItem('dismissed_biz_invite_modal')) {
                setTimeout(() => {
                    const inviteModal = new bootstrap.Modal(document.getElementById('businessSetupInviteModal'));
                    inviteModal.show();
                    
                    // Mark dismissed so it doesn't spam the user in the current session
                    document.getElementById('businessSetupInviteModal').addEventListener('hidden.bs.modal', () => {
                        sessionStorage.setItem('dismissed_biz_invite_modal', 'true');
                    });
                }, 1200);
            }
        @endif
    });
</script>
@endsection
