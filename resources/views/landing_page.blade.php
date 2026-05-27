<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mali Setu — Connect, Serve, Grow</title>
    <meta name="description" content="Mali Setu - Business directory, matrimony services, volunteering, and community support.">

    <!-- Bootstrap & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root { 
            --primary: #ff4757; 
            --primary-dark: #ff2a3b;
            --accent: #ff7a59; 
            --accent-dark: #e05e3d;
            --light-bg: #f4f3f0;
            --glass: rgba(255, 255, 255, 0.9);
            --gold: #d4af37;
        }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #2d3436; 
            background-color: #fff;
            overflow-x: hidden; 
        }
        
        /* Premium Background Decorations */
        .floral-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.08;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath d='M50 0 C55 20, 65 30, 80 30 C65 30, 55 40, 50 60 C45 40, 35 30, 20 30 C35 30, 45 20, 50 0 Z' fill='%23ff4757'/%3E%3Cpath d='M0 50 C5 60, 15 65, 30 65 C15 65, 5 70, 0 85 C-5 70, -15 65, -30 65 C-15 65, -5 60, 0 50 Z' fill='%23ff4757'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1;
        }
        
        /* Bootstrap Helper Overrides */
        .text-primary { color: var(--primary) !important; }
        .bg-primary { background-color: var(--primary) !important; }
        .border-primary { border-color: var(--primary) !important; }
        
        /* Navbar Upgrade */
        .navbar {
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.9) !important;
            transition: all 0.3s ease;
        }
        .nav-link {
            color: #4a5568 !important;
            font-weight: 600;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: var(--primary) !important;
        }
        
        /* Hero Styling (Milan Matrimony Reference) */
        .hero { 
            background: linear-gradient(135deg, #fff2f6 0%, #ffe4ec 100%); 
            padding: 100px 0 140px;
            position: relative;
            overflow: hidden;
            border-bottom-left-radius: 60px;
            border-bottom-right-radius: 60px;
        }

        .milan-title {
            color: var(--primary-dark);
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1px;
        }
        .milan-title span {
            color: var(--accent);
            position: relative;
        }

        /* glowing floral circle for couple */
        .couple-wrapper {
            position: relative;
            display: inline-block;
            margin-top: 20px;
        }
        .couple-circle {
            width: 380px;
            height: 380px;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid #fff;
            box-shadow: 0 20px 40px rgba(255, 71, 87, 0.15);
            position: relative;
            z-index: 5;
        }
        .flower-wreath {
            position: absolute;
            top: -20px;
            left: -20px;
            width: 420px;
            height: 420px;
            border: 4px dashed var(--gold);
            border-radius: 50%;
            animation: rotateWreath 40s linear infinite;
            z-index: 2;
        }
        .flower-wreath::before {
            content: '🌸';
            position: absolute;
            top: 20px; left: 50px;
            font-size: 24px;
        }
        .flower-wreath::after {
            content: '🌺';
            position: absolute;
            bottom: 30px; right: 60px;
            font-size: 24px;
        }
        .wreath-sparkle {
            position: absolute;
            top: -15px;
            left: -15px;
            width: 410px;
            height: 410px;
            border-radius: 50%;
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.35);
            z-index: 1;
            pointer-events: none;
        }

        @keyframes rotateWreath {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Search Widget Styled to Milan Banner Spec */
        .search-widget {
            background: #ffffff;
            border: 2px dashed var(--primary);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(255, 71, 87, 0.08);
            position: relative;
            z-index: 10;
        }
        .search-widget-title {
            color: var(--primary);
            font-weight: 800;
            font-size: 1.25rem;
            margin-bottom: 20px;
            text-align: left;
            border-bottom: 1.5px solid #ffe4e8;
            padding-bottom: 10px;
        }

        /* Categories Section Circular Grid */
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
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-bottom: 12px;
        }
        .justdial-cat-item:hover .justdial-cat-circle {
            transform: translateY(-6px) scale(1.06);
            border-color: var(--primary);
            box-shadow: 0 10px 25px rgba(255, 71, 87, 0.16);
            background: var(--light-bg);
        }
        .justdial-cat-icon {
            font-size: 1.8rem;
        }
        .justdial-cat-label {
            font-size: 0.9rem;
            font-weight: 700;
            color: #2d3436;
            line-height: 1.3;
            max-width: 120px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Modern Glass Cards */
        .glass-card {
            background: var(--glass);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(255, 71, 87, 0.08);
        }

        .stat-card { 
            border-radius: 20px; 
            background: #fff; 
            padding: 25px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.02); 
            border-bottom: 4px solid var(--primary);
        }
        .stat-value { font-size: 2.8rem; font-weight: 800; color: var(--primary); line-height: 1; }

        .floating { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        /* How it works steps styling */
        .how-it-works-step { position: relative; }
        .how-it-works-step::after {
            content: ''; position: absolute; top: 22%; right: -50%;
            width: 80%; height: 2px; border-top: 2px dashed var(--primary);
            opacity: 0.3;
            z-index: 1;
        }
        @media (max-width: 991px) { .how-it-works-step::after { display: none; } }

        .btn-primary { 
            background-color: var(--primary) !important; 
            border: none; 
            padding: 14px 32px; 
            border-radius: 14px; 
            font-weight: 700; 
            transition: 0.3s; 
        }
        .btn-primary:hover { 
            background-color: var(--primary-dark) !important; 
            transform: translateY(-2px); 
            box-shadow: 0 10px 20px rgba(255, 71, 87, 0.2); 
        }
        .btn-outline-primary { 
            border-color: var(--primary) !important; 
            color: var(--primary) !important; 
            padding: 14px 32px; 
            border-radius: 14px; 
            font-weight: 700;
            background: transparent !important;
            transition: 0.3s; 
        }
        .btn-outline-primary:hover { 
            background-color: var(--primary) !important; 
            color: #fff !important; 
        }
        .btn-warning { 
            background-color: var(--accent) !important; 
            border: none !important; 
            color: #fff !important; 
            padding: 14px 32px; 
            border-radius: 14px; 
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-warning:hover {
            background-color: var(--accent-dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 122, 89, 0.2);
        }

        /* Glassmorphic Membership Plans */
        .plan-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-radius: 30px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.03);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .plan-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 30px 60px rgba(255, 71, 87, 0.12);
            border-color: var(--primary);
        }
        .plan-price {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary);
            margin: 20px 0;
        }
        .plan-badge {
            position: absolute;
            top: 20px; right: -30px;
            background: var(--accent);
            color: #fff;
            transform: rotate(45deg);
            padding: 6px 35px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        footer { 
            background: #1c000a; 
            border-top: 5px solid var(--primary);
        }

        /* Swiper overrides */
        .swiper-pagination-bullet-active { background: var(--primary) !important; }
    </style>
</head>
<body>

@php
    // Fetch categories, plans, statistics, and featured entries dynamically directly from the database!
    $categories = \App\Models\BusinessCategory::where('is_active', true)->get();
    $matrimonyPlans = \App\Models\MatrimonyPlan::where('active', true)->get();
    $businessPlans = \App\Models\BusinessPlan::where('active', true)->get();
    
    $totalUsers = \App\Models\User::count();
    $totalVerifiedProfiles = \App\Models\MatrimonyProfile::where('approval_status', 'approved')->count();
    $totalVerifiedBusinesses = \App\Models\Business::where('verification_status', 'approved')->count();
    $totalSuccessStories = \App\Models\ConnectionRequest::where('status', 'accepted')->count() + 15; // realistic starting point
    $totalActiveMembers = \App\Models\User::where('status', 'active')->count() ?: $totalUsers;

    $featuredProfiles = \App\Models\MatrimonyProfile::where('approval_status', 'approved')->latest()->take(4)->get();
    $featuredBusinesses = \App\Models\Business::where('verification_status', 'approved')->latest()->take(4)->get();
@endphp

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top navbar-light py-3 shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img 
                src="{{ asset('landing_page_logo.jpeg') }}" 
                alt="MaliSetu Logo"
                style="height:55px; width:auto; object-fit:contain; border-radius: 8px;"
            >
            <span class="ms-2 fw-extrabold text-primary fs-4">Mali<span style="color:var(--accent)">Setu</span></span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3" href="#directory"><i class="fa-solid fa-store text-primary me-1"></i> Directory</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#matrimony"><i class="fa-solid fa-heart text-danger me-1"></i> Matrimony</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#plans"><i class="fa-solid fa-gem text-warning me-1"></i> Pricing Plans</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="{{ url('contact-us') }}"><i class="fa-solid fa-envelope text-info me-1"></i> Contact Us</a></li>
                @auth
                    <li class="nav-item ms-lg-3"><a class="btn btn-primary shadow-sm px-4" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a></li>
                @else
                    <li class="nav-item ms-lg-3"><a class="btn btn-primary shadow-sm px-4" href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket me-2"></i>Sign In</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<header class="hero">
    <div class="floral-bg"></div>
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 text-start" data-aos="fade-right">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-3 fw-bold shadow-sm"><i class="fa-solid fa-award me-1 text-primary"></i> Premium Matrimony & Directory Platform</span>
                <h1 class="display-4 fw-extrabold mb-4 milan-title">FIND YOUR <span class="text-primary">PERFECT</span><br>LIFE PARTNER</h1>
                <p class="lead mb-4 text-secondary" style="font-weight: 500;">We bring people together. Love unites them...<br>Discover and connect with verified matrimony profiles and business catalogs within the Mali Community.</p>

                <!-- Matrimony Search Widget -->
                <?php /*<div class="search-widget mt-4">
                    <div class="search-widget-title"><i class="fa-solid fa-magnifying-glass me-2"></i> Find Your Perfect Match</div>
                    <form class="row g-3" action="{{ route('register') }}" method="GET">
                        <div class="col-md-4 col-6">
                            <label class="small fw-bold text-secondary mb-1">Searching for</label>
                            <select name="user_type" class="form-select border-1 shadow-none rounded-3 py-2.5">
                                <option value="matrimony">Bride (Women)</option>
                                <option value="matrimony">Groom (Men)</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-6">
                            <label class="small fw-bold text-secondary mb-1">Age should be</label>
                            <div class="d-flex align-items-center gap-1">
                                <select class="form-select border-1 shadow-none rounded-3 py-2.5" style="font-size:0.85rem;">
                                    <option>21</option><option>23</option><option>25</option>
                                </select>
                                <span class="small text-muted">to</span>
                                <select class="form-select border-1 shadow-none rounded-3 py-2.5" style="font-size:0.85rem;">
                                    <option>28</option><option>30</option><option>35</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <label class="small fw-bold text-secondary mb-1">Location</label>
                            <select class="form-select border-1 shadow-none rounded-3 py-2.5">
                                <option>Rajkot</option>
                                <option>Mumbai</option>
                                <option>Pune</option>
                                <option>Nashik</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-6">
                            <label class="small fw-bold text-secondary mb-1">Religion</label>
                            <select class="form-select border-1 shadow-none rounded-3 py-2.5">
                                <option>Hindu</option>
                                <option>Sikh</option>
                                <option>Jain</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-6">
                            <label class="small fw-bold text-secondary mb-1">Disease</label>
                            <select class="form-select border-1 shadow-none rounded-3 py-2.5">
                                <option>Diabetes</option>
                                <option>None</option>
                            </select>
                        </div>
                        <div class="col-12 d-grid mt-4">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold rounded-3 py-3"><i class="fa-solid fa-heart me-1"></i> Let's Start Registration</button>
                        </div>
                    </form>
                </div> */?>
            </div>
            
            <!-- Traditional couple visual frame enclosed in a gorgeous flower wreath border -->
            <div class="col-lg-5 text-center position-relative" data-aos="zoom-in">
                <div class="couple-wrapper">
                    <div class="flower-wreath"></div>
                    <div class="wreath-sparkle"></div>
                    <img src="{{ asset('couple_wedding.jpg') }}" class="couple-circle floating" alt="Traditional Indian Couple">
                </div>
            </div>
        </div>
    </div>
</header>

<!-- STATS -->
<section class="py-5 bg-white" style="margin-top: -60px; position: relative; z-index: 10;">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-md-2.4 col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card text-center">
                    <div class="stat-value mb-2"><span class="stat" data-target="{{ $totalUsers }}">0</span></div>
                    <p class="text-muted fw-bold mb-0" style="font-size:0.85rem;">Registered Users</p>
                </div>
            </div>
            <div class="col-md-2.4 col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card text-center">
                    <div class="stat-value mb-2"><span class="stat" data-target="{{ $totalVerifiedProfiles }}">0</span></div>
                    <p class="text-muted fw-bold mb-0" style="font-size:0.85rem;">Verified Profiles</p>
                </div>
            </div>
            <div class="col-md-2.4 col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card text-center">
                    <div class="stat-value mb-2"><span class="stat" data-target="{{ $totalVerifiedBusinesses }}">0</span></div>
                    <p class="text-muted fw-bold mb-0" style="font-size:0.85rem;">Verified Businesses</p>
                </div>
            </div>
            <?php /*<div class="col-md-2.4 col-md-3 col-6" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card text-center">
                    <div class="stat-value mb-2"><span class="stat" data-target="{{ $totalSuccessStories }}">0</span></div>
                    <p class="text-muted fw-bold mb-0" style="font-size:0.85rem;">Success Stories</p>
                </div>
            </div> */?>
            <div class="col-md-2.4 col-md-3 col-6" data-aos="fade-up" data-aos-delay="500">
                <div class="stat-card text-center">
                    <div class="stat-value mb-2"><span class="stat" data-target="{{ $totalActiveMembers }}">0</span></div>
                    <p class="text-muted fw-bold mb-0" style="font-size:0.85rem;">Active Members</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CATEGORIES -->
<section class="py-5 bg-light" id="directory">
    <div class="container py-4 text-center">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold text-uppercase">Explore Directory</span>
            <h2 class="display-5 fw-extrabold text-dark">Popular Business Categories</h2>
            <p class="text-secondary">Direct redirect to matching category business listing under active verified catalogs.</p>
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

        <div class="row row-cols-2 row-cols-md-4 g-4 pt-3 justify-content-center">
            @foreach($categories->take(7) as $index => $cat)
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
                @endphp
                <div class="col" data-aos="zoom-in" data-aos-delay="{{ 100 * ($index + 1) }}">
                    <a href="{{ route('login') }}" class="justdial-cat-item">
                        <div class="justdial-cat-circle">
                            <i class="fa-solid {{ $matchedIcon }} {{ $matchedColor }} justdial-cat-icon"></i>
                        </div>
                        <div class="justdial-cat-label">{{ $cat->name }}</div>
                    </a>
                </div>
            @endforeach
            
            <!-- View All Card -->
            <div class="col" data-aos="zoom-in" data-aos-delay="800">
                <a href="{{ route('login') }}" class="justdial-cat-item">
                    <div class="justdial-cat-circle" style="background: #f8fafc;">
                        <i class="fa-solid fa-arrow-right-long text-secondary justdial-cat-icon"></i>
                    </div>
                    <div class="justdial-cat-label">Explore More</div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED MATRIMONY PROFILES -->
<section class="py-5 bg-white" id="matrimony">
    <div class="container py-4 text-center">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold text-uppercase">Matrimony Featured</span>
            <h2 class="display-5 fw-extrabold text-dark">Featured Profiles</h2>
            <p class="text-secondary">Discover verified life partners actively looking for matrimonial matches.</p>
        </div>

        <div class="row g-4 justify-content-center">
            @if($featuredProfiles->count() > 0)
                @foreach($featuredProfiles as $index => $profile)
                    @php
                        $photo = (!empty($profile->personal_details['photos']) && !empty($profile->personal_details['photos'][0])) 
                            ? asset('storage/' . $profile->personal_details['photos'][0]) 
                            : asset('default-avatar.png');
                    @endphp
                    <div class="col-md-3 col-sm-6 text-start" data-aos="fade-up" data-aos-delay="{{ 100 * ($index + 1) }}">
                        <div class="glass-card text-center p-3">
                            <img src="{{ $photo }}" class="img-fluid rounded-4 mb-3 shadow-sm border" style="height: 220px; width: 100%; object-fit: cover;" alt="Member">
                            <h6 class="fw-bold mb-1 text-dark">{{ $profile->user->name }}</h6>
                            <p class="small text-muted mb-2">{{ $profile->age }} yrs • {{ $profile->gender }}</p>
                            <span class="badge bg-light text-primary border small">{{ $profile->professional_details['occupation'] ?? 'Professional' }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center py-4">
                    <p class="text-secondary">No featured matrimonial seekers active at this time.</p>
                </div>
            @endif
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('register') }}" class="btn btn-primary shadow-sm"><i class="fa-solid fa-user-plus me-1"></i> Register Free Seeker Account</a>
        </div>
    </div>
</section>

<!-- FEATURED BUSINESSES -->
<section class="py-5 bg-light">
    <div class="container py-4 text-center">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold text-uppercase">Enterprise Spotlights</span>
            <h2 class="display-5 fw-extrabold text-dark">Featured Businesses</h2>
            <p class="text-secondary">Connect with active regional community vendors and trusted specialists.</p>
        </div>

        <div class="row g-4 justify-content-center">
            @if($featuredBusinesses->count() > 0)
                @foreach($featuredBusinesses as $index => $biz)
                    @php
                        $photo = $biz->photo 
                            ? asset('storage/' . trim(explode(',', $biz->photo)[0])) 
                            : null;
                    @endphp
                    <div class="col-md-3 col-sm-6 text-start" data-aos="fade-up" data-aos-delay="{{ 100 * ($index + 1) }}">
                        <div class="glass-card text-center p-3">
                            @if($photo)
                                <img src="{{ $photo }}" class="img-fluid rounded-4 mb-3 shadow-sm border" style="height: 180px; width: 100%; object-fit: cover;" alt="Logo">
                            @else
                                <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center mb-3" style="height: 180px;"><i class="fa-solid fa-store fs-1"></i></div>
                            @endif
                            <h6 class="fw-bold mb-1 text-dark">{{ $biz->business_name }}</h6>
                            <p class="small text-muted mb-2">{{ $biz->city }}, {{ $biz->state }}</p>
                            <span class="badge bg-light text-primary border small">{{ $biz->category->name ?? 'Agriculture' }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center py-4">
                    <p class="text-secondary">No featured verified businesses cataloged at this time.</p>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="py-5 bg-white">
    <div class="container py-4 text-center">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold text-uppercase">Why Choose Us</span>
            <h2 class="display-5 fw-extrabold text-dark">Trustworthy Community Network</h2>
            <p class="text-secondary">Why Mali Setu is the most preferred platform for matrimonials and local business directories.</p>
        </div>
        <div class="row g-4 text-start">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="glass-card p-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center mb-4" style="width:50px; height:50px;">
                        <i class="fa-solid fa-shield-halved fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark">100% Manual Audits</h5>
                    <p class="text-secondary small mb-0">Every profile and business is manually audited using Caste Certificate files to ensure absolute verification.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="glass-card p-4">
                    <div class="bg-accent bg-opacity-10 text-accent rounded-3 d-flex align-items-center justify-content-center mb-4" style="width:50px; height:50px; background:rgba(255,122,89,0.1); color:#ff7a59;">
                        <i class="fa-solid fa-lock fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Strict Privacy Controls</h5>
                    <p class="text-secondary small mb-0">Highly customizable privacy settings to keep contact details visible only to verified connection requests.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="glass-card p-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center mb-4" style="width:50px; height:50px;">
                        <i class="fa-solid fa-handshake-angle fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Unified Framework</h5>
                    <p class="text-secondary small mb-0">List job requirements, advertise service catalogues, pay invoices, and find partners on one community bridge.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="py-5 bg-light">
    <div class="container py-4 text-center">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold text-uppercase">FIND SOMEONE SPECIAL</span>
            <h2 class="display-5 fw-extrabold text-dark">How It Works</h2>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-3 how-it-works-step" data-aos="fade-up" data-aos-delay="100">
                <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; border: 2px solid var(--primary);">
                    <i class="fa-solid fa-user-plus text-primary fs-3"></i>
                </div>
                <h5 class="fw-bold">Create Account</h5>
                <p class="text-secondary small">Register for free and set up your comprehensive profile.</p>
            </div>
            <div class="col-md-3 how-it-works-step" data-aos="fade-up" data-aos-delay="200">
                <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; border: 2px solid var(--primary);">
                    <i class="fa-solid fa-users-viewfinder text-primary fs-3"></i>
                </div>
                <h5 class="fw-bold">Browse Profiles</h5>
                <p class="text-secondary small">Choose the plans and search for the perfect life partner.</p>
            </div>
            <div class="col-md-3 how-it-works-step" data-aos="fade-up" data-aos-delay="300">
                <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; border: 2px solid var(--primary);">
                    <i class="fa-solid fa-heart-pulse text-primary fs-3"></i>
                </div>
                <h5 class="fw-bold">Connect</h5>
                <p class="text-secondary small">Select and connect with matches that interest you.</p>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; border: 2px solid var(--primary);">
                    <i class="fa-solid fa-comments text-primary fs-3"></i>
                </div>
                <h5 class="fw-bold">Interact</h5>
                <p class="text-secondary small">Become a premium member and start conversations.</p>
            </div>
        </div>
    </div>
</section>

<!-- SUBSCRIPTION PRICING PLANS -->
<section class="py-5 bg-white" id="plans">
    <div class="container py-4 text-center">
        
        <!-- Matrimony Plans -->
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold text-uppercase">Premium Matrimony Plans</span>
            <h2 class="display-5 fw-extrabold text-dark">Matrimony Plans That Suit Your Journey</h2>
            <p class="text-secondary">Pruned glassmorphic plans loaded dynamically from the database.</p>
        </div>
        <div class="row g-4 justify-content-center mb-5">
            @if($matrimonyPlans->count() > 0)
                @foreach($matrimonyPlans as $plan)
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="plan-card">
                            <div>
                                <h5 class="fw-bold text-dark mb-0">{{ $plan->plan_name }}</h5>
                                <div class="plan-price">₹{{ number_format($plan->price, 0) }}</div>
                                <p class="text-muted small">Per User / {{ $plan->duration_years }} Year(s)</p>
                            </div>
                            <hr class="my-4 opacity-10">
                            <ul class="list-unstyled mb-4 small text-secondary text-start">
                                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Fast Approvals</li>
                                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Unlimited Messages</li>
                                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Private Matches lookup</li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm w-100 py-2.5 rounded-3 text-white">Start Now</a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <p class="text-secondary small">No active matrimony pricing plans found in database.</p>
                </div>
            @endif
        </div>

        <!-- Business Plans -->
        <div class="text-center mb-5 mt-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold text-uppercase">Premium Business Plans</span>
            <h2 class="display-5 fw-extrabold text-dark">Business Listing Subscriptions</h2>
            <p class="text-secondary">Broadcast catalogs and lists. Scale up community reaches dynamically.</p>
        </div>
        <div class="row g-4 justify-content-center">
            @if($businessPlans->count() > 0)
                @foreach($businessPlans as $plan)
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="plan-card">
                            <div>
                                <h5 class="fw-bold text-dark mb-0">{{ $plan->company_type }} Plan</h5>
                                <div class="plan-price">₹{{ number_format($plan->price, 0) }}</div>
                                <p class="text-muted small">Per Store / {{ $plan->duration_years }} Year(s)</p>
                            </div>
                            <hr class="my-4 opacity-10">
                            <ul class="list-unstyled mb-4 small text-secondary text-start">
                                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> List Infinite Services</li>
                                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Catalog Products</li>
                                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Post Job Openings</li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm w-100 py-2.5 rounded-3">Register Store</a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <p class="text-secondary small">No active business listing plans found in database.</p>
                </div>
            @endif
        </div>

    </div>
</section>

<!-- SUCCESS STORIES / TESTIMONIALS -->
<section class="py-5 bg-light">
    <div class="container py-4 text-center">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold text-uppercase">Success Stories</span>
            <h2 class="display-6 fw-extrabold">Community Voices & Success Stories</h2>
        </div>
        <div class="swiper testimonialSwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="glass-card text-center p-5">
                        <i class="fa-solid fa-quote-left fs-1 text-primary opacity-20 mb-4"></i>
                        <p class="fs-5 text-secondary" style="font-style: italic;">"Mali Setu helped me find a trusted civil contractor within our community. The service was seamless and the verification gave me peace of mind."</p>
                        <h6 class="fw-bold mt-4 mb-0">Sanjay Kumar</h6>
                        <span class="small text-muted">Business Owner, Pune</span>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="glass-card text-center p-5">
                        <i class="fa-solid fa-quote-left fs-1 text-primary opacity-20 mb-4"></i>
                        <p class="fs-5 text-secondary" style="font-style: italic;">"Finding a life partner who shares our cultural values was easier than expected. I'm thankful to the team for their dedicated support."</p>
                        <h6 class="fw-bold mt-4 mb-0">Meena K.</h6>
                        <span class="small text-muted">Parent, Nashik</span>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination mt-5 position-relative"></div>
        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-5 bg-white">
    <div class="container text-center">
        <div class="p-5 rounded-5 bg-primary text-white text-center position-relative overflow-hidden shadow-lg" data-aos="zoom-in">
            <div class="position-relative z-3">
                <h2 class="display-5 fw-extrabold mb-3">Ready to Join Your Community?</h2>
                <p class="lead opacity-80 mb-5" style="font-weight: 500;">Join thousands of community members today and experience growth together.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-5 shadow-sm">Join as Member</a>
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 text-primary shadow-sm" style="font-weight: 700;">List Business</a>
                </div>
            </div>
            <!-- Decorative outline circle background -->
            <div class="position-absolute top-0 end-0 bg-white opacity-10 rounded-circle" style="width: 350px; height: 350px; margin-top: -150px; margin-right: -150px; border: 4px dashed #fff;"></div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="py-5 text-white">
    <div class="container py-4">
        <div class="row g-4 text-start">
            <div class="col-lg-4">
                <a href="{{ url('/') }}" class="d-flex align-items-center text-white text-decoration-none mb-4">
                    <img src="{{ asset('landing_page_logo.jpeg') }}" alt="MaliSetu Logo" style="height:45px; width:auto; border-radius:6px; background:#fff; padding:2px;">
                    <span class="ms-2 fw-bold fs-4">Mali Setu</span>
                </a>
                <p class="opacity-60">Empowering India's vibrant Mali community through a digital bridge of services, verified matrimony, and support modules.</p>
                <div class="d-flex gap-3 mt-4">
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width:36px; height:36px; display:flex; align-items:center; justify-content:center;"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width:36px; height:36px; display:flex; align-items:center; justify-content:center;"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width:36px; height:36px; display:flex; align-items:center; justify-content:center;"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-md-2 offset-lg-1">
                <h6 class="fw-bold mb-4 text-white text-uppercase tracking-wider">Platform</h6>
                <ul class="list-unstyled opacity-75">
                    <li class="mb-2"><a href="#directory" class="text-white text-decoration-none hover-text-primary">Business Directory</a></li>
                    <li class="mb-2"><a href="#matrimony" class="text-white text-decoration-none hover-text-primary">Matrimony</a></li>
                    <li class="mb-2"><a href="{{ route('register') }}" class="text-white text-decoration-none hover-text-primary">Join Today</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <h6 class="fw-bold mb-4 text-white text-uppercase tracking-wider">Company</h6>
                <ul class="list-unstyled opacity-75">
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none hover-text-primary">About Us</a></li>
                    <li class="mb-2"><a href="{{ url('contact-us') }}" class="text-white text-decoration-none hover-text-primary">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold mb-4 text-white text-uppercase tracking-wider">Contact Support</h6>
                <p class="small opacity-60 mb-1">Email us at:</p>
                <p class="fw-bold mb-3">help@malisetu.org</p>
                <p class="small opacity-60 mb-1">Call support:</p>
                <p class="fw-bold">+91 98765 43210</p>
            </div>
        </div>
        <hr class="my-5 opacity-10">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="small opacity-50 mb-0">&copy; 2026 Mali Setu Foundation. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                <a href="{{ url('privacy-policy') }}" class="text-white small opacity-50 me-4 text-decoration-none hover-text-primary">Privacy Policy</a>
                <a href="{{ url('terms-condition') }}" class="text-white small opacity-50 text-decoration-none hover-text-primary">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

<script>
    AOS.init({ duration: 800, once: true });

    // Swiper Config
    new Swiper(".testimonialSwiper", {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: { el: ".swiper-pagination", clickable: true },
        breakpoints: { 768: { slidesPerView: 2 } }
    });

    // Numbers Counter Logic
    function initCounters() {
        const stats = document.querySelectorAll('.stat');
        stats.forEach(el => {
            const target = +el.getAttribute('data-target');
            let count = 0;
            const update = () => {
                const speed = target / 100;
                if (count < target) {
                    count += speed;
                    el.innerText = Math.ceil(count).toLocaleString();
                    setTimeout(update, 10);
                } else {
                    el.innerText = target.toLocaleString();
                }
            };
            
            const io = new IntersectionObserver(entries => {
                if (entries[0].isIntersecting) {
                    update();
                    io.disconnect();
                }
            });
            io.observe(el);
        });
    }
    document.addEventListener('DOMContentLoaded', initCounters);

    // Robust Image Fallback
    const fallback = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25'%3E%3Crect width='100%25' height='100%25' fill='%23f1f1f1'/%3E%3C/svg%3E";
    document.querySelectorAll('img').forEach(img => {
        img.onerror = () => { if(img.src !== fallback) img.src = fallback; };
    });
</script>

</body>
</html>