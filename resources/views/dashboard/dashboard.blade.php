@extends('layouts.app')

@section('content')
<style>
    /* Premium Hover Scale Micro-Animations */
    .hover-scale {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hover-scale:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 12px 24px rgba(255, 71, 87, 0.12) !important;
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
        transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.28s ease;
        box-shadow: 0 10px 30px rgba(18,38,63,0.06);
    }
    .category-grid-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 50px rgba(18,38,63,0.12);
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
        background: #ffffff;
        border: 1px solid rgba(255, 71, 87, 0.08);
        border-radius: 20px;
        padding: 22px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.07);
        transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.28s ease;
    }
    .directory-biz-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(255, 71, 87, 0.12);
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
        background: rgba(255, 71, 87, 0.06);
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
 
    /* Premium Justdial-Style Categories Circle Grid */
    .justdial-cat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none !important;
    }
    .justdial-cat-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: var(--cat-bg, #ffffff);
        border: 1px solid rgba(0, 0, 0, 0.04);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.32s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.32s ease, background 0.25s ease;
        overflow: hidden;
    }
    .justdial-cat-item:hover .justdial-cat-circle {
        transform: translateY(-6px) scale(1.06);
        border-color: var(--primary) !important;
        box-shadow: 0 14px 36px rgba(255, 71, 87, 0.14);
        background: var(--primary) !important;
    }
    .justdial-cat-item:hover .justdial-cat-icon {
        color: #ffffff !important;
    }
    .justdial-cat-icon {
        font-size: 1.5rem;
        transition: color 0.3s ease;
    }
    .justdial-cat-label {
        margin-top: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #2d3436;
        line-height: 1.3;
        max-width: 110px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        height: 2.6em;
    }
    @media (min-width: 992px) {
        .row-cols-lg-8 > * {
            flex: 0 0 auto !important;
            width: 12.5% !important;
        }
    }

    /* Custom Quick Access Cards matching Screenshot 3 */
    .quick-access-card {
        background: #ffffff;
        border: 1px solid #e9e8e4;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.02);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .quick-access-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 35px rgba(255, 71, 87, 0.08);
        border-color: rgba(255, 71, 87, 0.15);
    }
    .quick-icon-square {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    .quick-badge {
        background-color: #f1f3f5;
        color: #495057;
        border: 1px solid rgba(0, 0, 0, 0.04);
        font-size: 0.72rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .quick-btn {
        background-color: #ffffff;
        border: 1.5px solid #ced4da;
        color: #2d3436;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 700;
        padding: 11px 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .quick-btn:hover {
        background-color: #f8f9fa;
        border-color: #2d3436;
        color: #2d3436;
    }
    .quick-btn i {
        color: var(--primary) !important;
        transition: transform 0.2s ease;
    }
    .quick-btn:hover i {
        transform: translateX(4px);
    }

    @media (max-width: 576px) {
        .quick-access-card {
            padding: 15px !important;
            border-radius: 14px !important;
        }
        .quick-access-card h5 {
            font-size: 0.85rem !important;
            margin-top: 10px !important;
            margin-bottom: 4px !important;
        }
        .quick-access-card p {
            font-size: 0.68rem !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 3 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            line-height: 1.35 !important;
            height: 2.8rem !important;
            margin-bottom: 0 !important;
        }
        .quick-icon-square {
            width: 36px !important;
            height: 36px !important;
            border-radius: 6px !important;
        }
        .quick-icon-square i {
            font-size: 0.85rem !important;
        }
        .quick-badge {
            font-size: 0.6rem !important;
            padding: 3px 8px !important;
        }
        .quick-btn {
            font-size: 0.72rem !important;
            padding: 8px 12px !important;
            margin-top: 15px !important;
            border-radius: 8px !important;
            gap: 4px !important;
        }
    }
</style>

<div class="row g-4">
    <!-- Main Content Panel -->
    <main class="col-12 px-md-4 py-4">

        <!-- Tab 1: Overview -->
        <div class="tab-panel active" id="tab-overview">
            
            <!-- Welcome Banner -->
            <div class="welcome-banner mb-4 text-start shadow-sm border border-white border-opacity-10" style="background: linear-gradient(135deg, #ff4757 0%, #ff7a59 100%);">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge bg-white bg-opacity-20 text-black mb-3 px-3 py-1.5 rounded-pill fw-bold text-uppercase small"><i class="fa-solid fa-crown me-1 text-warning"></i> {{ ucfirst($user->user_type) }} Profile</span>
                        <h1 class="fw-extrabold text-white mb-2 fs-2">Hello, {{ $user->name }}!</h1>
                        <p class="opacity-90 mb-0 font-medium small" style="line-height:1.6;">Welcome back to your premium Mali Setu workspace. Explore community directories, active life partners, list your business, and interact with verified members.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-4 mt-md-0">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Photo" class="profile-photo-circle" style="border: 4px solid rgba(255,255,255,0.25); box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                        @else
                            <img src="{{ asset('default-avatar.png') }}" alt="Profile Photo" class="profile-photo-circle" style="width: 110px; height: 110px; object-fit: cover; border: 4px solid rgba(255,255,255,0.25); box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                        @endif
                    </div>
                </div>
            </div>

            <!-- Platform Stats Grid -->
            <?php /*<div class="row g-3 mb-4 text-start">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="glass-card p-4 h-100 d-flex align-items-center gap-3 hover-scale" style="border-left: 5px solid #2563eb; background: #ffffff;">
                        <div class="rounded-4 d-flex align-items-center justify-content-center shrink-0" style="width:50px; height:50px; background: rgba(37,99,235,0.08); color: #2563eb; font-size: 20px;">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div>
                            <h6 class="text-secondary fw-semibold mb-1 small" style="font-size:0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Your Business</h6>
                            <h4 class="fw-extrabold mb-0 text-dark font-monospace" style="font-size:1.6rem;">{{ $stats['businesses_count'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="glass-card p-4 h-100 d-flex align-items-center gap-3 hover-scale" style="border-left: 5px solid #ea580c; background: #ffffff;">
                        <div class="rounded-4 d-flex align-items-center justify-content-center shrink-0" style="width:50px; height:50px; background: rgba(234,88,12,0.08); color: #ea580c; font-size: 20px;">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <div>
                            <h6 class="text-secondary fw-semibold mb-1 small" style="font-size:0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Products Listed</h6>
                            <h4 class="fw-extrabold mb-0 text-dark font-monospace" style="font-size:1.6rem;">{{ $stats['products_count'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="glass-card p-4 h-100 d-flex align-items-center gap-3 hover-scale" style="border-left: 5px solid #16a34a; background: #ffffff;">
                        <div class="rounded-4 d-flex align-items-center justify-content-center shrink-0" style="width:50px; height:50px; background: rgba(22,163,74,0.08); color: #16a34a; font-size: 20px;">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                        </div>
                        <div>
                            <h6 class="text-secondary fw-semibold mb-1 small" style="font-size:0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Your Services</h6>
                            <h4 class="fw-extrabold mb-0 text-dark font-monospace" style="font-size:1.6rem;">{{ $stats['services_count'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="glass-card p-4 h-100 d-flex align-items-center gap-3 hover-scale" style="border-left: 5px solid #0d9488; background: #ffffff;">
                        <div class="rounded-4 d-flex align-items-center justify-content-center shrink-0" style="width:50px; height:50px; background: rgba(13,148,136,0.08); color: #0d9488; font-size: 20px;">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <div>
                            <h6 class="text-secondary fw-semibold mb-1 small" style="font-size:0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Open Positions</h6>
                            <h4 class="fw-extrabold mb-0 text-dark font-monospace" style="font-size:1.6rem;">{{ $stats['jobs_count'] }}</h4>
                        </div>
                    </div>
                </div>
            </div> */?>

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
                                        <?php /*@if($banner->url)
                                            <a href="{{ $banner->url }}" target="_blank" class="btn btn-primary btn-sm rounded-pill px-4 align-self-start fw-semibold shadow-sm">Explore More <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i></a>
                                        @endif */?>
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
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-magnifying-glass text-primary me-2"></i> Search Verified Business Directory</h5>
                        <p class="text-secondary small mb-0">Browse through active directories, verify services or look up agricultural vendors.</p>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
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
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                        <i class="fa-solid fa-tags fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Explore Business Categories</h5>
                        <p class="text-secondary small mb-0">Select any industry below to display active businesses and products.</p>
                    </div>
                </div>

                @php
                    $iconMap = [
                        'Healthcare' => 'fa-user-doctor text-danger',
                        'Beauty' => 'fa-spa text-purple',
                        'Beauty Spa' => 'fa-spa text-purple',
                        'Food' => 'fa-bowl-food text-warning',
                        'Restaurants' => 'fa-bowl-food text-warning',
                        'Repair & Service' => 'fa-screwdriver-wrench text-secondary',
                        'Repair' => 'fa-screwdriver-wrench text-secondary',
                        'Packers & Movers' => 'fa-truck text-primary',
                        'Gym' => 'fa-dumbbell text-success',
                        'Education' => 'fa-graduation-cap text-info',
                        'AC Service' => 'fa-wind text-info',
                        'Hotels' => 'fa-hotel text-primary',
                        'Wedding Planning' => 'fa-heart text-danger',
                        'Hospitals' => 'fa-hospital text-danger',
                        'Rent & Hire' => 'fa-key text-warning',
                        'Contractors' => 'fa-helmet-safety text-warning',
                        'Pet Shops' => 'fa-paw text-success',
                        'PG/Hostels' => 'fa-bed text-primary',
                        'Dentists' => 'fa-tooth text-info',
                        'Loans' => 'fa-money-bill-wave text-success',
                        'Event Organisers' => 'fa-calendar-days text-danger',
                        'Driving Schools' => 'fa-car text-secondary',
                        'Courier Service' => 'fa-box-open text-primary',
                    ];
                @endphp

                <div class="row row-cols-3 row-cols-sm-4 row-cols-md-6 row-cols-lg-6 g-3 pt-2 justify-content-center">
                    @foreach($categories->take(11) as $index => $cat)
                        @php
                            $matchedIcon = 'fa-tags';
                            $matchedColor = 'text-primary';
                            foreach($iconMap as $key => $val) {
                                if (stripos($cat->name, $key) !== false) {
                                    $parts = explode(' ', $val);
                                    $matchedIcon = $parts[0];
                                    $matchedColor = $parts[1] ?? 'text-primary';
                                    break;
                                }
                            }
                            if ($matchedIcon === 'fa-tags') {
                                $colors = ['text-primary', 'text-success', 'text-warning', 'text-danger', 'text-info', 'text-purple'];
                                $matchedColor = $colors[$index % count($colors)];
                            }

                            // Dynamic translucent background tints matching the icon color
                            $bgMap = [
                                'text-danger' => 'rgba(220, 53, 69, 0.06)',
                                'text-purple' => 'rgba(111, 66, 193, 0.06)',
                                'text-warning' => 'rgba(255, 193, 7, 0.06)',
                                'text-secondary' => 'rgba(108, 117, 125, 0.06)',
                                'text-primary' => 'rgba(255, 71, 87, 0.06)',
                                'text-success' => 'rgba(40, 167, 69, 0.06)',
                                'text-info' => 'rgba(13, 202, 240, 0.06)',
                            ];
                            $matchedBg = $bgMap[$matchedColor] ?? 'rgba(255, 71, 87, 0.06)';
                        @endphp
                        <div class="col">
                            <div onclick="selectCategoryOnDashboard({{ $cat->id }})" class="justdial-cat-item">
                                <div class="justdial-cat-circle" style="--cat-bg: {{ $matchedBg }};">
                                    @if(!empty($cat->photo) && file_exists(public_path('storage/' . $cat->photo)))
                                        <img src="{{ asset('storage/' . $cat->photo) }}" style="width: 42px; height: 42px; object-fit: contain;">
                                    @else
                                        <i class="fa-solid {{ $matchedIcon }} {{ $matchedColor }} justdial-cat-icon"></i>
                                    @endif
                                </div>
                                <div class="justdial-cat-label">{{ $cat->name }}</div>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- View All card -->
                    <div class="col">
                        <div data-bs-toggle="modal" data-bs-target="#allCategoriesModal" class="justdial-cat-item">
                            <div class="justdial-cat-circle" style="background: #f8fafc;">
                                <i class="fa-solid fa-list-ul text-secondary justdial-cat-icon"></i>
                            </div>
                            <div class="justdial-cat-label">Popular Categories</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active directory results pane (by default loads 10 featured businesses) -->
            <?php /*<div class="glass-card mb-4 text-start" id="directory-listings-section">
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
                                        <div class="small text-muted d-flex align-items-center gap-1 flex-wrap" style="max-width: 100%;">
                                            @php
                                                $fullAddress = trim(($biz->address ? $biz->address . ', ' : '') . $biz->city . ', ' . $biz->state . ($biz->pincode ? ' - ' . $biz->pincode : ''));
                                            @endphp
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $fullAddress }}" style="cursor: help;">
                                                <i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ Str::limit($fullAddress, 40, '...') }}
                                            </span>
                                            @if(isset($biz->distance))
                                                <span class="badge bg-light text-secondary border small text-nowrap"><i class="fa-solid fa-route text-danger me-1"></i> {{ $biz->distance }} km away</span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="small text-secondary"><i class="fa-solid fa-box-open text-primary me-1"></i> {{ $biz->products->count() }} items</span>
                                            <span class="small text-secondary"><i class="fa-solid fa-gears text-primary me-1"></i> {{ $biz->services->count() }} service</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 pt-3 border-top d-flex gap-2">
                                        @if($biz->contact_phone)
                                            <button onclick="showCallModal('{{ $biz->contact_phone }}', '{{ addslashes($biz->business_name) }}')" class="btn btn-light btn-sm flex-fill rounded-3 text-dark small fw-semibold"><i class="fa-solid fa-phone me-1 text-success"></i> Call</button>
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
            </div> */?>

            <!-- Quick Access Section -->
            <div class="row g-4 mb-4 text-start">
                <!-- Matrimony Seeker Profile Card -->
                <div class="col-6 col-md-6 col-lg-4">
                    <div class="quick-access-card">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="quick-icon-square" style="background-color: #ff4757;">
                                    <i class="fa-solid fa-heart fs-5 text-white"></i>
                                </div>
                                @if($user->is_matrimony)
                                    <span class="quick-badge" style="background-color: #d1fae5; color: #065f46;">Registered</span>
                                @else
                                    <span class="quick-badge">No Seeker Account</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-dark mt-3 mb-2" style="font-size: 1.25rem;">Matrimony Seeker Profile</h5>
                            <p class="text-secondary small mb-0" style="line-height: 1.6;">Manage your matrimony seeker profile, upload caste certificate, search verified community partners, and interact via private messages.</p>
                        </div>
                        <a href="{{ route('matrimony.index') }}" class="quick-btn mt-4">
                            Access Profile <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Business Directory Card -->
                <div class="col-6 col-md-6 col-lg-4">
                    <div class="quick-access-card">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="quick-icon-square" style="background-color: #ff4757;">
                                    <i class="fa-solid fa-briefcase fs-5 text-white"></i>
                                </div>
                                @if($user->is_business)
                                    <span class="quick-badge" style="background-color: #d1fae5; color: #065f46;">Registered</span>
                                @else
                                    <span class="quick-badge">No Listing</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-dark mt-3 mb-2" style="font-size: 1.25rem;">Business Directory</h5>
                            <p class="text-secondary small mb-0" style="line-height: 1.6;">List your own business, showcase catalogs, active services, publish job requirements, and hire local talent.</p>
                        </div>
                        <a href="{{ route('dashboard.business.index') }}" class="quick-btn mt-4">
                            Access Listings <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Blog Portal Card -->
                <div class="col-6 col-md-6 col-lg-4">
                    <div class="quick-access-card">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="quick-icon-square" style="background-color: #6f42c1;">
                                    <i class="fa-solid fa-blog fs-5 text-white"></i>
                                </div>
                                @if($user->user_type === 'bloger')
                                    <span class="quick-badge" style="background-color: #f3e8ff; color: #6b21a8;">Blogger</span>
                                @else
                                    <span class="quick-badge">General Member</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-dark mt-3 mb-2" style="font-size: 1.25rem;">Blog Portal</h5>
                            <p class="text-secondary small mb-0" style="line-height: 1.6;">Read inspiring stories, view community updates, write articles, or engage with other users' blogs through comments and replies.</p>
                        </div>
                        <a href="{{ route('blogs.index') }}" class="quick-btn mt-4">
                            Access Blog <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Summary -->
            <?php /*<div class="glass-card text-start">
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
            </div> */?>
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
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo" class="profile-photo-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <img src="{{ asset('default-avatar.png') }}" alt="Photo" class="profile-photo-circle" style="width: 100px; height: 100px; object-fit: cover;">
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
                            <div class="input-group">
                                <input type="text" name="pincode" value="{{ old('pincode', $user->pincode) }}" class="form-control" maxlength="6" id="profilePincodeInput">
                                <button class="btn btn-outline-secondary bg-white border-start-0" type="button" id="get_profile_location_btn" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;" title="Fetch My Current Coordinates">
                                    <i class="fa-solid fa-location-dot text-primary"></i>
                                </button>
                            </div>
                            <input type="hidden" name="latitude" id="profileLatitudeInput" value="{{ old('latitude', $user->latitude) }}">
                            <input type="hidden" name="longitude" id="profileLongitudeInput" value="{{ old('longitude', $user->longitude) }}">
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
            <div class="modal-body text-center">
                <p class="text-secondary small mb-4 text-start">Click any sector below to filter directory listings immediately.</p>
                <div class="row row-cols-3 row-cols-sm-4 row-cols-md-6 row-cols-lg-8 g-4 pt-2 justify-content-center">
                    @foreach($categories as $index => $cat)
                        @php
                            $matchedIcon = 'fa-tags';
                            $matchedColor = 'text-primary';
                            foreach($iconMap as $key => $val) {
                                if (stripos($cat->name, $key) !== false) {
                                    $parts = explode(' ', $val);
                                    $matchedIcon = $parts[0];
                                    $matchedColor = $parts[1] ?? 'text-primary';
                                    break;
                                }
                            }
                            if ($matchedIcon === 'fa-tags') {
                                $colors = ['text-primary', 'text-success', 'text-warning', 'text-danger', 'text-info', 'text-purple'];
                                $matchedColor = $colors[$index % count($colors)];
                            }

                            $bgMap = [
                                'text-danger' => 'rgba(220, 53, 69, 0.06)',
                                'text-purple' => 'rgba(111, 66, 193, 0.06)',
                                'text-warning' => 'rgba(255, 193, 7, 0.06)',
                                'text-secondary' => 'rgba(108, 117, 125, 0.06)',
                                'text-primary' => 'rgba(255, 71, 87, 0.06)',
                                'text-success' => 'rgba(40, 167, 69, 0.06)',
                                'text-info' => 'rgba(13, 202, 240, 0.06)',
                            ];
                            $matchedBg = $bgMap[$matchedColor] ?? 'rgba(255, 71, 87, 0.06)';
                        @endphp
                        <div class="col">
                            <div onclick="selectCategoryOnDashboard({{ $cat->id }}); bootstrap.Modal.getInstance(document.getElementById('allCategoriesModal')).hide();" class="justdial-cat-item">
                                <div class="justdial-cat-circle" style="--cat-bg: {{ $matchedBg }};">
                                    @if(!empty($cat->photo) && file_exists(public_path('storage/' . $cat->photo)))
                                        <img src="{{ asset('storage/' . $cat->photo) }}" style="width: 42px; height: 42px; object-fit: contain;">
                                    @else
                                        <i class="fa-solid {{ $matchedIcon }} {{ $matchedColor }} justdial-cat-icon"></i>
                                    @endif
                                </div>
                                <div class="justdial-cat-label">{{ $cat->name }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for displaying business phone number -->
<div class="modal fade" id="callBusinessModal" tabindex="-1" aria-labelledby="callBusinessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg text-center p-3" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-success mb-4 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: rgba(25, 135, 84, 0.1); border-radius: 50%; font-size: 2.2rem;">
                    <i class="fa-solid fa-phone text-success"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1" id="modal-business-name">Business Name</h3>
                <p class="text-secondary small mb-4">Contact Phone Number</p>
                <div class="p-3 bg-light rounded-3 mb-4">
                    <h2 class="fw-bold text-success mb-0 font-monospace" id="modal-business-phone">+91 00000 00000</h2>
                </div>
                
                <div class="d-flex gap-2">
                    <button onclick="copyToClipboard()" class="btn btn-outline-secondary py-2.5 rounded-3 fw-semibold flex-fill"><i class="fa-solid fa-copy me-1"></i> Copy Number</button>
                    <a id="modal-business-phone-link" href="#" class="btn btn-success py-2.5 rounded-3 fw-bold flex-fill"><i class="fa-solid fa-phone me-1"></i> Call Now</a>
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
                <div class="text-primary mb-4 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: rgba(255,71,87,0.08); border-radius: 50%; font-size: 2.2rem;">
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

    function showCallModal(phone, name) {
        document.getElementById('modal-business-name').innerText = name;
        document.getElementById('modal-business-phone').innerText = phone;
        document.getElementById('modal-business-phone-link').setAttribute('href', 'tel:' + phone);
        const modal = new bootstrap.Modal(document.getElementById('callBusinessModal'));
        modal.show();
    }

    function copyToClipboard() {
        const phone = document.getElementById('modal-business-phone').innerText;
        navigator.clipboard.writeText(phone).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Phone number copied to clipboard!',
                showConfirmButton: false,
                timer: 3000,
                background: 'rgba(255, 255, 255, 0.95)'
            });
        }).catch(err => {
            console.error("Copy failed", err);
        });
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
                ? `<button onclick="showCallModal('${biz.contact_phone}', '${biz.business_name.replace(/'/g, "\\'")}')" class="btn btn-light btn-sm flex-fill rounded-3 text-dark small fw-semibold"><i class="fa-solid fa-phone me-1 text-success"></i> Call</button>` 
                : '';
                
            const mailBtn = biz.contact_email 
                ? `<a href="mailto:${biz.contact_email}" class="btn btn-light btn-sm flex-fill rounded-3 text-dark small fw-semibold"><i class="fa-solid fa-envelope me-1 text-primary"></i> Mail</a>` 
                : '';

            const fullAddress = [biz.address, biz.city, biz.state].filter(Boolean).join(', ') + (biz.pincode ? ' - ' + biz.pincode : '');
            const shortAddress = fullAddress.length > 40 ? fullAddress.substring(0, 40) + '...' : fullAddress;

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
                            <div class="small text-muted d-flex align-items-center gap-1 flex-wrap" style="max-width: 100%;">
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="${fullAddress}" style="cursor: help;">
                                    <i class="fa-solid fa-location-dot me-1 text-primary"></i> ${shortAddress}
                                </span>
                                ${biz.distance !== null && biz.distance !== undefined ? `<span class="badge bg-light text-secondary border small text-nowrap"><i class="fa-solid fa-route text-danger me-1"></i> ${biz.distance} km away</span>` : ''}
                            </div>
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

        // Re-initialize tooltips for newly rendered elements
        setTimeout(() => {
            var tooltipTriggerList = [].slice.call(grid.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }, 150);
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
        const pincodeInput = document.querySelector('#profilePincodeInput');
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

        const getProfileLocBtn = document.getElementById('get_profile_location_btn');
        if (getProfileLocBtn) {
            getProfileLocBtn.addEventListener('click', function() {
                const icon = getProfileLocBtn.querySelector('i');
                const oldClass = icon.className;
                icon.className = 'fa-solid fa-spinner fa-spin text-primary';
                
                navigator.geolocation.getCurrentPosition(function(position) {
                    icon.className = 'fa-solid fa-circle-check text-success';
                    document.getElementById('profileLatitudeInput').value = position.coords.latitude;
                    document.getElementById('profileLongitudeInput').value = position.coords.longitude;
                    alert('GPS Coordinates fetched successfully: ' + position.coords.latitude.toFixed(4) + ', ' + position.coords.longitude.toFixed(4));
                }, function(error) {
                    icon.className = oldClass;
                    alert('Geolocation Error: ' + error.message);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
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
