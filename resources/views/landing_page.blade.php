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
        .business-wreath {
            position: absolute;
            top: -20px;
            left: -20px;
            width: 420px;
            height: 420px;
            border: 4px dashed var(--primary);
            border-radius: 50%;
            animation: rotateWreath 40s linear infinite;
            z-index: 2;
        }
        .business-wreath::before {
            content: '💼';
            position: absolute;
            top: 20px; left: 50px;
            font-size: 24px;
        }
        .business-wreath::after {
            content: '🤝';
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
            overflow: hidden;
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

        /* Dynamic Dual-Mode Toggle styles */
        .mode-toggle-container {
            margin-top: 25px;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            position: relative;
            z-index: 100;
        }
        .mode-toggle-pill {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 10px 30px rgba(255, 71, 87, 0.08);
            border-radius: 50px;
            padding: 4px;
            display: flex;
            position: relative;
            width: 320px;
            height: 50px;
        }
        .mode-toggle-slider {
            position: absolute;
            top: 4px;
            bottom: 4px;
            left: 4px;
            width: calc(50% - 4px);
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 50px;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1;
            box-shadow: 0 4px 15px rgba(255, 71, 87, 0.25);
        }
        .mode-toggle-pill.business-active .mode-toggle-slider {
            left: calc(50% - 0px);
        }
        .mode-toggle-pill.matrimony-active .mode-toggle-slider {
            left: 4px;
        }
        .btn-mode-toggle {
            flex: 1;
            background: transparent;
            border: none;
            color: #4a5568;
            font-weight: 750;
            font-size: 0.95rem;
            cursor: pointer;
            z-index: 2;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-mode-toggle.active {
            color: #ffffff !important;
        }
        .btn-mode-toggle:focus {
            outline: none;
        }

        /* SPA Section Visibility Control */
        .mode-section {
            display: none;
            opacity: 0;
            transform: translateY(15px);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }
        .mode-section.active-mode {
            display: block;
            animation: sectionFadeIn 0.5s forwards cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes sectionFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hero Swiper Carousel Styles */
        .hero-swiper {
            width: 100%;
            height: auto;
        }
        .hero-slide {
            width: 100%;
        }
        .swiper-slide.hidden-slide {
            display: none !important;
        }
        .business-hero-slide {
            background-size: cover !important;
            background-position: center !important;
            position: relative;
            border-radius: 40px;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.08);
            margin: 15px 0;
            min-height: 480px;
            display: flex;
            align-items: center;
        }
        .business-hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(28, 0, 10, 0.9) 0%, rgba(28, 0, 10, 0.5) 100%);
            z-index: 1;
        }
        .business-hero-content {
            position: relative;
            z-index: 2;
            color: #ffffff;
            padding: 60px 40px;
            text-align: left;
        }
        .hero-swiper-pagination {
            position: absolute;
            bottom: 15px !important;
            z-index: 10;
        }
        .hero-swiper-pagination .swiper-pagination-bullet {
            width: 10px;
            height: 10px;
            background: rgba(255, 71, 87, 0.3);
            opacity: 1;
            transition: all 0.3s;
        }
        .hero-swiper-pagination .swiper-pagination-bullet-active {
            width: 30px;
            background: var(--primary) !important;
            border-radius: 5px;
        }

        /* 8-Column Grid layout for categories */
        @media (min-width: 992px) {
            .row-cols-lg-8 > * {
                flex: 0 0 auto;
                width: 12.5% !important;
            }
        }

        /* Plan Tabs Styles */
        .plan-tabs .nav-link {
            background: #ffffff;
            color: #4a5568;
            font-weight: 700;
            border-radius: 50px;
            padding: 10px 24px;
            border: 1px solid rgba(0,0,0,0.08);
            margin: 0 6px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            transition: all 0.3s;
        }
        .plan-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 8px 20px rgba(255, 71, 87, 0.2);
            border-color: transparent;
        }

        /* Interactive Blogs Styles */
        #landingBlogTabs.nav-pills .nav-link {
            border: 2px solid var(--primary) !important;
            color: var(--primary) !important;
            background: transparent !important;
            transition: all 0.3s ease;
            font-weight: 700;
        }
        #landingBlogTabs.nav-pills .nav-link.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%) !important;
            color: white !important;
            border-color: transparent !important;
            box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3) !important;
        }
        #landingBlogTabs.nav-pills .nav-link:hover:not(.active) {
            background: rgba(255, 71, 87, 0.05) !important;
            color: var(--primary) !important;
        }

        .blog-card {
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .blog-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(255, 71, 87, 0.08);
        }
        .blog-image-wrapper {
            position: relative;
            height: 220px;
            overflow: hidden;
        }
        .blog-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .blog-card:hover .blog-image {
            transform: scale(1.06);
        }
        .blog-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            color: var(--primary);
            font-weight: 700;
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 50px;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .blog-content {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .blog-meta {
            font-size: 0.8rem;
            color: #718096;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .blog-title {
            font-size: 1.25rem;
            font-weight: 750;
            color: #2d3436;
            margin-bottom: 12px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.3s;
            text-decoration: none;
        }
        .blog-title:hover {
            color: var(--primary);
        }
        .blog-desc {
            font-size: 0.9rem;
            color: #4a5568;
            margin-bottom: 20px;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .blog-footer {
            border-top: 1px solid #edf2f7;
            padding-top: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .blog-author {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .blog-author-img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid var(--primary);
        }
        .blog-author-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #2d3436;
        }
        .blog-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .blog-action-btn {
            background: transparent;
            border: none;
            color: #a0aec0;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 50px;
        }
        .blog-action-btn:hover {
            background: #f7fafc;
            color: var(--primary);
        }
        .blog-action-btn.liked {
            color: var(--primary) !important;
        }
        .blog-action-btn.liked i {
            animation: heartBeat 0.3s;
        }

        .rounded-pill {
            color: #ffffff !important;
        }
        @keyframes heartBeat {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        /* Horizontal Scrolling Category Tabs on Landing Page */
        #landingBlogTabs {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            justify-content: start !important;
            padding: 8px 16px 16px 16px !important;
            -webkit-overflow-scrolling: touch;
            max-width: 100%;
            scrollbar-width: thin;
            scrollbar-color: var(--primary) rgba(0, 0, 0, 0.05);
        }
        #landingBlogTabs::-webkit-scrollbar {
            height: 6px !important;
            display: block !important;
        }
        #landingBlogTabs::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05) !important;
            border-radius: 10px !important;
        }
        #landingBlogTabs::-webkit-scrollbar-thumb {
            background: var(--primary) !important;
            border-radius: 10px !important;
        }
        #landingBlogTabs::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark, #ff2a3b) !important;
        }
        #landingBlogTabs .nav-item {
            flex-shrink: 0 !important;
            white-space: nowrap !important;
        }
        #landingBlogTabs .nav-link {
            border: 2px solid var(--primary) !important;
            color: var(--primary) !important;
            background: transparent !important;
            font-weight: 700;
            transition: all 0.3s ease;
            user-select: none;
        }
        #landingBlogTabs .nav-link.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%) !important;
            color: white !important;
            border-color: transparent !important;
            box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3) !important;
        }
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

    $banners = \App\Models\HomepageHero::all();
    $blogs = \App\Models\Blog::with(['user', 'category'])->withCount('likes')->where('is_active', true)->latest()->get();
    $activeCategories = \App\Models\BlogCategory::active()->whereHas('blogs', function($q) { $q->where('is_active', true); })->get();
    if ($activeCategories->isEmpty()) {
        $legacyNames = \App\Models\Blog::where('is_active', true)
            ->whereNotNull('blog_type')
            ->where('blog_type', '!=', '')
            ->distinct()
            ->pluck('blog_type');
        $activeCategories = $legacyNames->map(function($name) {
            return (object) ['id' => $name, 'name' => $name];
        });
    }
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
                <li class="nav-item"><a class="nav-link px-3" href="#matrimony-how"><i class="fa-solid fa-heart text-danger me-1"></i> Matrimony</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#business-how"><i class="fa-solid fa-store text-primary me-1"></i> Business</a></li>
                
                <?php /*<li class="nav-item"><a class="nav-link px-3" href="#plans"><i class="fa-solid fa-gem text-warning me-1"></i> Pricing Plans</a></li> */?>
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
<header class="hero p-0">
    <div class="swiper heroSwiper">
        <div class="swiper-wrapper">
            <!-- Slide 1: Current Matrimony Banner -->
            <div class="swiper-slide hero-slide slide-matrimony">
                <div class="floral-bg"></div>
                <div class="container position-relative" style="z-index: 2; padding: 60px 0 80px;">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-7 text-start">
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-3 fw-bold shadow-sm"><i class="fa-solid fa-award me-1 text-primary"></i> Premium Matrimony & Directory Platform</span>
                            <h1 class="display-4 fw-extrabold mb-4 milan-title">FIND YOUR <span class="text-primary">PERFECT</span><br>LIFE PARTNER</h1>
                            <p class="lead mb-4 text-secondary" style="font-weight: 500;">We bring people together. Love unites them...<br>Discover and connect with verified matrimony profiles and business catalogs within the Mali Community.</p>
                            <div class="d-flex gap-3 mt-4">
                                <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 shadow-sm"><i class="fa-solid fa-heart me-2"></i> Find Match</a>
                                <a href="#matrimony-how" class="btn btn-outline-primary btn-lg px-4"><i class="fa-solid fa-compass me-2"></i> How It Works</a>
                            </div>
                        </div>
                        
                        <!-- Traditional couple visual frame enclosed in a gorgeous flower wreath border -->
                        <div class="col-lg-5 text-center position-relative">
                            <div class="couple-wrapper">
                                <div class="flower-wreath"></div>
                                <div class="wreath-sparkle"></div>
                                <img src="{{ asset('couple_wedding.jpg') }}" class="couple-circle floating" alt="Traditional Indian Couple">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2: New Business Banner (designed like matrimony banner) -->
            <div class="swiper-slide hero-slide slide-business">
                <div class="floral-bg" style="background-image: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); opacity: 0.85;"></div>
                <div class="container position-relative" style="z-index: 2; padding: 60px 0 80px;">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-7 text-start">
                            <span class="badge bg-primary text-white px-3 py-2 rounded-pill mb-3 fw-bold shadow-sm" style="background-color: var(--primary) !important;"><i class="fa-solid fa-store me-1"></i> Verified Business Directory</span>
                            <h1 class="display-4 fw-extrabold mb-4 milan-title" style="color: #0f172a;"><span class="text-primary" style="color: var(--primary) !important;">GROW & EMPOWER</span><br>YOUR BUSINESS</h1>
                            <p class="lead mb-4 text-secondary" style="font-weight: 500;">Scale your community reach with ease...<br>Discover and connect with trusted regional community vendors, trusted specialists, and agricultural entrepreneurs.</p>
                            <div class="d-flex gap-3 mt-4">
                                <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 shadow-sm" style="background-color: var(--primary) !important;"><i class="fa-solid fa-store me-2"></i> List Business</a>
                                <a href="#business-how" class="btn btn-outline-primary btn-lg px-4" style="border-color: var(--primary) !important; color: var(--primary) !important;"><i class="fa-solid fa-compass me-2"></i> How It Works</a>
                            </div>
                        </div>
                        
                        <!-- Circular business image in a gorgeous gear/wreath border -->
                        <div class="col-lg-5 text-center position-relative">
                            <div class="couple-wrapper">
                                <div class="business-wreath"></div>
                                <div class="wreath-sparkle" style="box-shadow: 0 0 30px rgba(59, 130, 246, 0.35);"></div>
                                <img src="{{ asset('business_img.png') }}" class="couple-circle floating" alt="Business Wreath Frame" style="border-color: #fff; box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15);">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pagination -->
        <div class="swiper-pagination hero-swiper-pagination"></div>
    </div>
</header>

<!-- STATS -->
<section class="py-5 bg-white" style="margin-top: -30px; position: relative; z-index: 10;">
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
<?php /*<div class="mode-section business-section">
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

                // Fetch up to 15 categories and fill up with beautiful dummies to keep the 8-8 row visually perfect!
                $categoriesList = $categories->take(15)->all();
                if (count($categoriesList) < 15) {
                    $dummyCategoryNames = ['Healthcare', 'Food & Beverages', 'Beauty Spa', 'Repair & Service', 'Packers & Movers', 'Gym', 'Education', 'Wedding Planning', 'AC Service', 'Hospitals', 'Hotels', 'Pet Shops', 'Rent & Hire', 'Dentists', 'Courier Service'];
                    $index = 0;
                    while (count($categoriesList) < 15 && $index < count($dummyCategoryNames)) {
                        $dummyName = $dummyCategoryNames[$index++];
                        $exists = false;
                        foreach ($categoriesList as $existing) {
                            if (stripos($existing->name, $dummyName) !== false) {
                                $exists = true;
                                break;
                            }
                        }
                        if (!$exists) {
                            $dummyObj = new stdClass();
                            $dummyObj->name = $dummyName;
                            $categoriesList[] = $dummyObj;
                        }
                    }
                }
            @endphp

            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-8 g-4 pt-3 justify-content-center">
                @foreach($categoriesList as $idx => $cat)
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
                            $matchedColor = $colors[$idx % count($colors)];
                        }
                    @endphp
                    <div class="col" data-aos="zoom-in" data-aos-delay="{{ 50 * ($idx + 1) }}">
                        <a href="{{ route('login') }}" class="justdial-cat-item">
                            <div class="justdial-cat-circle">
                                @if(!empty($cat->photo) && file_exists(public_path('storage/' . $cat->photo)))
                                    <img src="{{ asset('storage/' . $cat->photo) }}" style="width: 48px; height: 48px; object-fit: contain;">
                                @else
                                    <i class="fa-solid {{ $matchedIcon }} {{ $matchedColor }} justdial-cat-icon"></i>
                                @endif
                            </div>
                            <div class="justdial-cat-label">{{ $cat->name }}</div>
                        </a>
                    </div>
                @endforeach
                
                <!-- View All Card (Item 16) -->
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
</div> */?>

<!-- FEATURED MATRIMONY PROFILES -->
<?php /*<div class="mode-section matrimony-section active-mode">
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
</div> */?>

<!-- FEATURED BUSINESSES -->
<?php /*<div class="mode-section business-section">
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
</div> */?>

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

<!-- HOW IT WORKS (MATRIMONY) -->
<div id="matrimony-how">
    <section class="py-5 bg-light">
        <div class="container py-4 text-center">
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold text-uppercase">Find Someone Special</span>
                <h2 class="display-5 fw-extrabold text-dark">How Matrimony Works</h2>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-3 how-it-works-step" data-aos="fade-up" data-aos-delay="100">
                    <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; border: 2px solid var(--primary);">
                        <i class="fa-solid fa-user-plus text-primary fs-3"></i>
                    </div>
                    <h5 class="fw-bold">Create Account</h5>
                    <p class="text-secondary small">Register for free and set up your comprehensive matrimonial profile.</p>
                </div>
                <div class="col-md-3 how-it-works-step" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; border: 2px solid var(--primary);">
                        <i class="fa-solid fa-users-viewfinder text-primary fs-3"></i>
                    </div>
                    <h5 class="fw-bold">Browse Profiles</h5>
                    <p class="text-secondary small">Filter verified profiles and search for your ideal life partner.</p>
                </div>
                <div class="col-md-3 how-it-works-step" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; border: 2px solid var(--primary);">
                        <i class="fa-solid fa-heart-pulse text-primary fs-3"></i>
                    </div>
                    <h5 class="fw-bold">Connect</h5>
                    <p class="text-secondary small">Send private connection requests to profiles that interest you.</p>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; border: 2px solid var(--primary);">
                        <i class="fa-solid fa-comments text-primary fs-3"></i>
                    </div>
                    <h5 class="fw-bold">Interact</h5>
                    <p class="text-secondary small">Securely chat, engage families, and begin your journey together.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- HOW IT WORKS (BUSINESS & FLOW) -->
<div id="business-how">
    <section class="py-5 bg-white border-top">
        <div class="container py-4 text-center">
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold text-uppercase">Empower Local Business</span>
                <h2 class="display-5 fw-extrabold text-dark">How Business Directory Works & Its Flow</h2>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-3 how-it-works-step" data-aos="fade-up" data-aos-delay="100">
                    <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; border: 2px solid var(--primary);">
                        <i class="fa-solid fa-store text-primary fs-3"></i>
                    </div>
                    <h5 class="fw-bold">1. Register Business</h5>
                    <p class="text-secondary small">Set up your business profile, upload a store logo, and add contact details.</p>
                </div>
                <div class="col-md-3 how-it-works-step" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; border: 2px solid var(--primary);">
                        <i class="fa-solid fa-file-invoice-dollar text-primary fs-3"></i>
                    </div>
                    <h5 class="fw-bold">2. Choose Listing Plan</h5>
                    <p class="text-secondary small">Subscribe to the best plan (Proprietary/LLP, Private, or Public Ltd).</p>
                </div>
                <div class="col-md-3 how-it-works-step" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; border: 2px solid var(--primary);">
                        <i class="fa-solid fa-boxes-packing text-primary fs-3"></i>
                    </div>
                    <h5 class="fw-bold">3. Catalog Products & Jobs</h5>
                    <p class="text-secondary small">Publish products, list services, and post community job opportunities.</p>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; border: 2px solid var(--primary);">
                        <i class="fa-solid fa-handshake-angle text-primary fs-3"></i>
                    </div>
                    <h5 class="fw-bold">4. Verification & Growth</h5>
                    <p class="text-secondary small">Get verified using Caste Certificates and grow within the community.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- SUBSCRIPTION PRICING PLANS -->
<?php /*<section class="py-5 bg-white" id="plans">
    <div class="container py-4 text-center">
        
        <!-- Matrimony Plans (Only in Matrimony Mode) -->
        <div class="mode-section matrimony-section active-mode">
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
        </div>

        <!-- Business Plans (Only in Business Mode) -->
        <div class="mode-section business-section">
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold text-uppercase">Premium Business Plans</span>
                <h2 class="display-5 fw-extrabold text-dark">Business Listing Subscriptions</h2>
                <p class="text-secondary">Broadcast catalogs and lists. Scale up community reaches dynamically.</p>
            </div>

            @if($businessPlans->count() > 0)
                <!-- Tabs Navigation -->
                <ul class="nav nav-pills justify-content-center mb-5 plan-tabs" id="businessPlanTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="llp-tab" data-bs-toggle="pill" data-bs-target="#llp-plans" type="button" role="tab" aria-controls="llp-plans" aria-selected="true"><i class="fa-solid fa-people-group me-2"></i> Proprietary /Partnership - LLP</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="private-tab" data-bs-toggle="pill" data-bs-target="#private-plans" type="button" role="tab" aria-controls="private-plans" aria-selected="false"><i class="fa-solid fa-building me-2"></i> Private Ltd</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="public-tab" data-bs-toggle="pill" data-bs-target="#public-plans" type="button" role="tab" aria-controls="public-plans" aria-selected="false"><i class="fa-solid fa-city me-2"></i> Public Ltd</button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="businessPlanTabContent">
                    <!-- LLP Plans Tab -->
                    <div class="tab-pane fade show active" id="llp-plans" role="tabpanel" aria-labelledby="llp-tab">
                        <div class="row g-4 justify-content-center">
                            @foreach($businessPlans->where('company_type', 'Proprietary /Partnership - LLP') as $plan)
                                <div class="col-md-4 col-lg-3" data-aos="fade-up">
                                    <div class="plan-card">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-0">LLP Plan</h5>
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
                        </div>
                    </div>

                    <!-- Private Plans Tab -->
                    <div class="tab-pane fade" id="private-plans" role="tabpanel" aria-labelledby="private-tab">
                        <div class="row g-4 justify-content-center">
                            @foreach($businessPlans->where('company_type', 'Private Ltd') as $plan)
                                <div class="col-md-4 col-lg-3" data-aos="fade-up">
                                    <div class="plan-card">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-0">Private Ltd Plan</h5>
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
                        </div>
                    </div>

                    <!-- Public Plans Tab -->
                    <div class="tab-pane fade" id="public-plans" role="tabpanel" aria-labelledby="public-tab">
                        <div class="row g-4 justify-content-center">
                            @foreach($businessPlans->where('company_type', 'Public Ltd') as $plan)
                                <div class="col-md-4 col-lg-3" data-aos="fade-up">
                                    <div class="plan-card">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-0">Public Ltd Plan</h5>
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
                        </div>
                    </div>
                </div>
            @else
                <div class="col-12">
                    <p class="text-secondary small">No active business listing plans found in database.</p>
                </div>
            @endif
        </div>

    </div>
</section> */?>

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
                <div class="swiper-slide">
                    <div class="glass-card text-center p-5">
                        <i class="fa-solid fa-quote-left fs-1 text-primary opacity-20 mb-4"></i>
                        <p class="fs-5 text-secondary" style="font-style: italic;">"Registered my agricultural startup on the Mali Setu Business directory and gained over 200 community clients within three months. Absolutely game-changing!"</p>
                        <h6 class="fw-bold mt-4 mb-0">Rahul Mali</h6>
                        <span class="small text-muted">Agro-Entrepreneur, Bangalore</span>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="glass-card text-center p-5">
                        <i class="fa-solid fa-quote-left fs-1 text-primary opacity-20 mb-4"></i>
                        <p class="fs-5 text-secondary" style="font-style: italic;">"We connected through Mali Setu's verified matrimony. The profile verification process is top-notch, ensuring we interacted with genuine families."</p>
                        <h6 class="fw-bold mt-4 mb-0">Priyanka & Amit</h6>
                        <span class="small text-muted">Happily Married, Mumbai</span>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="glass-card text-center p-5">
                        <i class="fa-solid fa-quote-left fs-1 text-primary opacity-20 mb-4"></i>
                        <p class="fs-5 text-secondary" style="font-style: italic;">"Listing our logistics firm under the LLP plan has given us great exposure. We now recruit local talent and provide services within the community."</p>
                        <h6 class="fw-bold mt-4 mb-0">Karan Solanki</h6>
                        <span class="small text-muted">Logistics LLP Partner, Nagpur</span>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="glass-card text-center p-5">
                        <i class="fa-solid fa-quote-left fs-1 text-primary opacity-20 mb-4"></i>
                        <p class="fs-5 text-secondary" style="font-style: italic;">"As a boutique designer, finding verified community wholesalers was a huge challenge until Mali Setu directory came along. A wonderful initiative!"</p>
                        <h6 class="fw-bold mt-4 mb-0">Sneha Mali</h6>
                        <span class="small text-muted">Boutique Owner, Indore</span>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination mt-5 position-relative"></div>
        </div>
    </div>
</section>

<!-- COMMUNITY BLOGS & INSIGHTS -->
<?php /*<section class="py-3 bg-white" id="blogs-section">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold text-uppercase">Community Hub</span>
            <h2 class="display-5 fw-extrabold text-dark">Latest Blogs & Insights</h2>
            <p class="text-secondary">Explore expert insights, success guides, and inspiring community articles.</p>
        </div>

        <!-- Dynamic Category Tabs -->
        @if(!$activeCategories->isEmpty())
            <ul class="nav nav-pills justify-content-center mb-5 gap-2" id="landingBlogTabs" role="tablist">
                @foreach($activeCategories as $index => $cat)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }} rounded-pill px-4 fw-bold shadow-sm" id="landing-tab-{{ Str::slug($cat->name) }}" data-bs-toggle="pill" data-bs-target="#landing-pane-{{ Str::slug($cat->name) }}" type="button" role="tab" aria-controls="landing-pane-{{ Str::slug($cat->name) }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            {{ $cat->name }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="landingBlogTabContent">
                @foreach($activeCategories as $index => $cat)
                    @php
                        $catBlogs = $blogs->filter(function($blog) use ($cat) {
                            return $blog->blog_type == $cat->id || (is_string($blog->blog_type) && trim($blog->blog_type) === $cat->name);
                        })->take(12);
                    @endphp
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="landing-pane-{{ Str::slug($cat->name) }}" role="tabpanel" aria-labelledby="landing-tab-{{ Str::slug($cat->name) }}">
                        <div class="swiper blogsSwiper py-3">
                            <div class="swiper-wrapper">
                                @if($catBlogs->count() > 0)
                                    @foreach($catBlogs as $blog)
                                        @php
                                            $mediaList = is_array($blog->media_path) ? $blog->media_path : json_decode($blog->media_path, true);
                                            $singlePath = is_array($mediaList) ? ($mediaList[0] ?? null) : $blog->media_path;
                                            
                                            $coverPhoto = $singlePath 
                                                ? asset('storage/' . $singlePath) 
                                                : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=600&q=80';
                                            
                                            $isLiked = false;
                                            if (auth()->check()) {
                                                $isLiked = $blog->likedBy(auth()->id());
                                            } else {
                                                $isLiked = \App\Models\BlogLike::where('blog_id', $blog->id)->where('session_id', session()->getId())->exists();
                                            }
                                        @endphp
                                        <div class="swiper-slide h-auto">
                                            <a href="{{ route('blogs.public.show', $blog->id) }}" class="text-decoration-none text-dark h-100 d-block">
                                                <div class="blog-card h-100">
                                                    <div class="blog-image-wrapper">
                                                        <span class="blog-badge">{{ $blog->category->name ?? $blog->blog_type }}</span>
                                                        
                                                        @if(is_array($mediaList) && count($mediaList) > 1)
                                                            <div id="landingCardCarousel-{{ $blog->id }}" class="carousel slide carousel-fade h-100 w-100" data-bs-ride="carousel" data-bs-interval="3000">
                                                                <div class="carousel-indicators" style="bottom: 10px; margin-bottom: 0; z-index: 15;">
                                                                    @foreach($mediaList as $mIdx => $mPath)
                                                                        <button type="button" data-bs-target="#landingCardCarousel-{{ $blog->id }}" data-bs-slide-to="{{ $mIdx }}" class="{{ $mIdx === 0 ? 'active' : '' }}" aria-current="{{ $mIdx === 0 ? 'true' : 'false' }}" style="width: 8px; height: 8px; border-radius: 50%; margin: 0 4px; background-color: #fff; border: 1px solid rgba(0,0,0,0.25);"></button>
                                                                    @endforeach
                                                                </div>
                                                                <div class="carousel-inner h-100">
                                                                    @foreach($mediaList as $mIdx => $mPath)
                                                                        @php
                                                                            $ext = strtolower(pathinfo($mPath, PATHINFO_EXTENSION));
                                                                            $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                                                        @endphp
                                                                        <div class="carousel-item h-100 {{ $mIdx === 0 ? 'active' : '' }}">
                                                                            @if($isVid)
                                                                                <video src="{{ asset('storage/' . $mPath) }}" muted loop playsinline autoplay class="w-100 h-100 object-fit-cover"></video>
                                                                            @else
                                                                                <img src="{{ asset('storage/' . $mPath) }}" alt="Media slide" class="w-100 h-100 object-fit-cover">
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @else
                                                            @php
                                                                $ext = strtolower(pathinfo($singlePath, PATHINFO_EXTENSION));
                                                                $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                                            @endphp
                                                            @if($isVid)
                                                                <video src="{{ asset('storage/' . $singlePath) }}" muted loop playsinline autoplay class="blog-image w-100 h-100 object-fit-cover"></video>
                                                            @else
                                                                <img src="{{ $coverPhoto }}" class="blog-image" alt="{{ $blog->title }}">
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="blog-content">
                                                        <div>
                                                            <div class="blog-meta">
                                                                <span><i class="fa-regular fa-calendar me-1"></i> {{ $blog->created_at->format('M d, Y') }}</span>
                                                                <span><i class="fa-regular fa-clock me-1"></i> 5 min read</span>
                                                            </div>
                                                            <span class="blog-title d-block">{{ $blog->title }}</span>
                                                            <p class="blog-desc text-secondary">{{ strip_tags($blog->description) }}</p>
                                                        </div>
                                                        <div class="blog-footer">
                                                            <div class="blog-author">
                                                                <img src="{{ $blog->user->avatar ? asset('storage/' . $blog->user->avatar) : asset('default-avatar.png') }}" class="blog-author-img" alt="Author">
                                                                <span class="blog-author-name">{{ $blog->user->name }}</span>
                                                            </div>
                                                            <div class="blog-actions d-flex align-items-center gap-2">
                                                                <span class="text-muted small d-inline-flex align-items-center gap-1" title="Views" style="font-weight:600; font-size:0.8rem;">
                                                                    <i class="fa-regular fa-eye"></i> {{ $blog->views_count }}
                                                                </span>
                                                                <button class="blog-action-btn {{ $isLiked ? 'liked' : '' }}" onclick="toggleBlogLike(event, this, {{ $blog->id }})">
                                                                    <i class="{{ $isLiked ? 'fa-solid text-danger' : 'fa-regular' }} fa-heart"></i>
                                                                    <span class="likes-count">{{ $blog->likes_count }}</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="swiper-slide text-center py-5 w-100">
                                        <div class="text-secondary opacity-60 mb-3"><i class="fa-solid fa-note-sticky fs-1"></i></div>
                                        <p class="text-secondary">No community blogs published in this category.</p>
                                    </div>
                                @endif
                            </div>
                            <!-- Pagination Bullets -->
                            <div class="swiper-pagination blogs-swiper-pagination mt-4 position-relative"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 w-100">
                <div class="text-secondary opacity-60 mb-3"><i class="fa-solid fa-note-sticky fs-1"></i></div>
                <p class="text-secondary">No community blogs published at this time.</p>
            </div>
        @endif
        
        <div class="text-center mt-5">
            <a href="{{ route('blogs.index') }}" class="btn btn-outline-primary px-4 py-2.5 rounded-pill shadow-sm" style="font-weight: 700;"><i class="fa-solid fa-book-open me-2"></i> Browse All Blog Articles</a>
        </div>
    </div>
</section> */?>

<!-- CALL TO ACTION -->
<section class="py-3 bg-white">
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

    // Hero Swiper Autoplay Config (loop: true for infinite auto scrolling)
    const heroSwiper = new Swiper(".heroSwiper", {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".hero-swiper-pagination",
            clickable: true,
        },
        spaceBetween: 0,
        speed: 800
    });

    // Testimonial Swiper Config
    new Swiper(".testimonialSwiper", {
        slidesPerView: 1,
        spaceBetween: 30,
        autoplay: {
            delay: 4500,
            disableOnInteraction: false,
        },
        pagination: { el: ".swiper-pagination", clickable: true },
        breakpoints: { 768: { slidesPerView: 2 } },
        speed: 600
    });

    // AJAX Blog Liking logic (Available publicly for guests and registered members)
    function toggleBlogLike(event, button, blogId) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }

        button.disabled = true;

        fetch(`/blogs/${blogId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("HTTP error " + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const icon = button.querySelector('i');
                const likesCountSpan = button.querySelector('.likes-count');

                if (data.liked) {
                    button.classList.add('liked');
                    icon.className = 'fa-solid fa-heart text-danger';
                } else {
                    button.classList.remove('liked');
                    icon.className = 'fa-regular fa-heart';
                }

                likesCountSpan.textContent = data.likes_count;
            }
        })
        .catch(error => {
            console.error('Failed to like/unlike blog:', error);
        })
        .finally(() => {
            button.disabled = false;
        });
    }

    // Blogs Swiper Config with observers to support Swiper instances inside Bootstrap Tabs
    document.querySelectorAll(".blogsSwiper").forEach(el => {
        new Swiper(el, {
            slidesPerView: 1,
            spaceBetween: 20,
            observer: true,
            observeParents: true,
            pagination: { 
                el: el.querySelector(".blogs-swiper-pagination"), 
                clickable: true 
            },
            breakpoints: {
                768: { slidesPerView: 2, spaceBetween: 24 },
                992: { slidesPerView: 3, spaceBetween: 30 }
            },
            speed: 600
        });
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
    document.addEventListener('DOMContentLoaded', () => {
        initCounters();

        // Mouse drag scrolling & dynamic centering for horizontal category tabs
        const tabEl = document.getElementById('landingBlogTabs');
        if (tabEl) {
            let isDown = false;
            let startX;
            let scrollLeft;

            tabEl.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - tabEl.offsetLeft;
                scrollLeft = tabEl.scrollLeft;
                tabEl.style.cursor = 'grabbing';
            });
            tabEl.addEventListener('mouseleave', () => {
                isDown = false;
                tabEl.style.cursor = 'grab';
            });
            tabEl.addEventListener('mouseup', () => {
                isDown = false;
                tabEl.style.cursor = 'grab';
            });
            tabEl.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - tabEl.offsetLeft;
                const walk = (x - startX) * 1.5; // multiplier
                tabEl.scrollLeft = scrollLeft - walk;
            });
            tabEl.style.cursor = 'grab';

            // Dynamically center tabs if they fit within the viewport, otherwise align left
            const adjustTabsAlignment = () => {
                if (tabEl.scrollWidth > tabEl.clientWidth) {
                    tabEl.style.setProperty('justify-content', 'flex-start', 'important');
                } else {
                    tabEl.style.setProperty('justify-content', 'center', 'important');
                }
            };

            // Run alignment checks on load and window resize
            adjustTabsAlignment();
            window.addEventListener('resize', adjustTabsAlignment);
        }
    });

    // Robust Image Fallback
    const fallback = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25'%3E%3Crect width='100%25' height='100%25' fill='%23f1f1f1'/%3E%3C/svg%3E";
    document.querySelectorAll('img').forEach(img => {
        img.onerror = () => { if(img.src !== fallback) img.src = fallback; };
    });
</script>

</body>
</html>