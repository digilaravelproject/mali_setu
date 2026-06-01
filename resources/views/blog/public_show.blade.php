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

        /* .rounded-pill {
            color: #ffffff !important;
        } */
        @keyframes heartBeat {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        .btn-like-large {
            background: rgba(220, 53, 69, 0.1); 
            color: #dc3545; 
            transition: all 0.2s ease;
            border: none;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 700;
        }
        .btn-like-large:hover {
            transform: scale(1.05);
            background: #dc3545 !important;
            color: #fff !important;
        }
        .btn-like-large.liked {
            background: #dc3545;
            color: #fff;
        }

        .btn-views-count {
            background: rgba(74, 85, 104, 0.08);
            color: #4a5568;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: default;
        }
        #comments-container::-webkit-scrollbar {
            width: 6px;
        }
        #comments-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        #comments-container::-webkit-scrollbar-thumb {
            background: #ff4757;
            border-radius: 10px;
        }
        #comments-container::-webkit-scrollbar-thumb:hover {
            background: #ff2a3b;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top navbar-light py-3 shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img 
                src="{{ asset('landing_page_logo.jpeg') }}" 
                alt="MaliSetu Logo"
                style="height:55px; width:auto; object-fit:contain; border-radius: 8px;"
            >
            <span class="ms-2 fw-extrabold text-primary fs-4" style="font-weight: 800;">Mali<span style="color:var(--accent)">Setu</span></span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3" href="{{ url('/#matrimony') }}"><i class="fa-solid fa-heart text-danger me-1"></i> Matrimony</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="{{ url('/#business') }}"><i class="fa-solid fa-store text-primary me-1"></i> Business</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="{{ url('/#plans') }}"><i class="fa-solid fa-gem text-warning me-1"></i> Pricing Plans</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="{{ url('contact-us') }}"><i class="fa-solid fa-envelope text-info me-1"></i> Contact Us</a></li>
                @auth
                    <li class="nav-item ms-lg-3"><a class="btn btn-primary shadow-sm px-4 text-white" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a></li>
                @else
                    <li class="nav-item ms-lg-3"><a class="btn btn-primary shadow-sm px-4 text-white" href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket me-2"></i>Sign In</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- MAIN CONTAINER -->
<div class="container py-5">
    <!-- Back Link -->
    <div class="mb-4">
        <a href="{{ url('/') }}#blogs-section" class="text-primary text-decoration-none fw-bold small">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Homepage
        </a>
    </div>

    <div class="row g-4">
        <!-- Main Article Column -->
        <div class="col-lg-8">
            <article class="glass-card p-0 overflow-hidden" style="background: #fff;">
                <!-- Article Header Media -->
                @if($blog->media_path)
                    @php
                        $mediaList = is_array($blog->media_path) ? $blog->media_path : json_decode($blog->media_path, true);
                    @endphp
                    <div class="article-media-wrapper position-relative" style="max-height: 480px; overflow: hidden; background: #000;">
                        @if(is_array($mediaList) && count($mediaList) > 1)
                            <div id="detailCarousel-{{ $blog->id }}" class="carousel slide carousel-fade h-100 w-100" data-bs-ride="carousel" data-bs-interval="3000">
                                <div class="carousel-inner h-100" style="max-height: 480px;">
                                    @foreach($mediaList as $idx => $mPath)
                                        @php
                                            $ext = strtolower(pathinfo($mPath, PATHINFO_EXTENSION));
                                            $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                        @endphp
                                        <div class="carousel-item h-100 {{ $idx === 0 ? 'active' : '' }}">
                                            @if($isVid)
                                                <video src="{{ asset('storage/' . $mPath) }}" controls muted loop playsinline class="d-block w-100 h-100 object-fit-contain" style="max-height: 480px;"></video>
                                            @else
                                                <img src="{{ asset('storage/' . $mPath) }}" alt="Media slide" class="d-block w-100 h-100 object-fit-cover" style="max-height: 480px; object-position: center;">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#detailCarousel-{{ $blog->id }}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#detailCarousel-{{ $blog->id }}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        @else
                            @php
                                $singlePath = is_array($mediaList) ? ($mediaList[0] ?? null) : $blog->media_path;
                            @endphp
                            @if($singlePath)
                                @php
                                    $ext = strtolower(pathinfo($singlePath, PATHINFO_EXTENSION));
                                    $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                @endphp
                                @if($isVid)
                                    <video src="{{ asset('storage/' . $singlePath) }}" controls class="w-100 h-100 object-fit-contain" style="max-height:480px;"></video>
                                @else
                                    <img src="{{ asset('storage/' . $singlePath) }}" alt="{{ $blog->title }}" class="w-100 h-100 object-fit-cover" style="max-height:480px; object-position: center;">
                                @endif
                            @endif
                        @endif
                    </div>
                @else
                    <div class="article-media-wrapper position-relative" style="max-height: 280px; overflow: hidden; background: #ffe4ec;">
                        <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80" alt="{{ $blog->title }}" class="w-100 h-100 object-fit-cover" style="max-height:280px;">
                    </div>
                @endif

                <!-- Article Body -->
                <div class="p-4 p-md-5">
                    <!-- Category/Tags -->
                    <div class="mb-3 d-flex flex-wrap gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-bold">
                            {{ $blog->category->name ?? $blog->blog_type }}
                        </span>
                        @if($blog->tags)
                            @foreach($blog->tags as $tag)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill small">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="fw-black text-dark mb-4" style="font-size: 2.2rem; line-height: 1.3; font-weight: 800;">
                        {{ $blog->title }}
                    </h1>

                    <!-- Author & Metadata Card -->
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-4 mb-4 flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width:48px; height:48px; border-radius:50%; font-size:1.1rem;">
                                {{ strtoupper(substr($blog->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $blog->user->name ?? 'Anonymous' }}</h6>
                                <span class="small text-muted"><i class="fa-regular fa-clock me-1"></i>Published {{ $blog->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Views Count Display Side-by-Side -->
                            <div class="btn-views-count shadow-sm">
                                <i class="fa-regular fa-eye fa-lg"></i>
                                <span class="views-count-num">{{ $blog->views_count }}</span>
                            </div>

                            <!-- Public Like Button -->
                            <button class="btn btn-like-large px-4 py-2 rounded-pill border-0 d-flex align-items-center gap-2 shadow-sm {{ $isLiked ? 'liked' : '' }}" onclick="toggleBlogPublicLike(this, {{ $blog->id }})">
                                <i class="fa-{{ $isLiked ? 'solid' : 'regular' }} fa-heart fa-lg"></i>
                                <span class="likes-count fw-bold">{{ $blog->likes_count }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Article Content -->
                    <div class="article-content text-secondary" style="font-size: 1.1rem; line-height: 1.8;">
                        {!! nl2br(e($blog->description)) !!}
                    </div>
                </div>
            </article>

            <!-- Comments & Discussion Module -->
            <div class="glass-card mt-4 overflow-hidden p-0" style="border-radius: 24px; background: #fff; border: 1px solid rgba(0,0,0,0.03) !important;">
                <div class="bg-white border-0 pt-4 px-4 pb-2">
                    <h5 class="fw-extrabold text-dark mb-0" style="font-weight: 800; font-size: 1.25rem;">
                        <i class="fa-solid fa-comments me-2 text-primary"></i>Discussion (<span id="total-comment-count">{{ $blog->allComments()->count() }}</span>)
                    </h5>
                </div>
                <div class="p-4">
                    <!-- Write Comment Section -->
                    @auth
                        <div class="d-flex mb-4">
                            <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width:40px; height:40px; border-radius:50%; flex-shrink: 0; font-size: 0.95rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <form id="main-comment-form">
                                    @csrf
                                    <div class="form-group mb-2">
                                        <textarea id="main-comment-textarea" class="form-control border-0 bg-light p-3" rows="3" placeholder="Write a comment..." style="border-radius: 16px; resize: none; font-size: 0.95rem;" required></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 0.85rem; transition: transform 0.2s;">
                                            <i class="fa-solid fa-paper-plane me-1"></i>Publish Comment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="bg-light p-4 rounded-4 text-center mb-4">
                            <p class="text-muted mb-2 small"><i class="fa-solid fa-lock me-2 text-primary"></i>You must be logged in to participate in the discussion.</p>
                            <a href="{{ route('login') }}" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">Sign In</a>
                        </div>
                    @endauth

                    <hr class="opacity-10 my-4">

                    <!-- Comments List Container -->
                    <div id="comments-container" class="d-flex flex-column gap-4" style="max-height: 480px; overflow-y: auto; padding-right: 8px;">
                        @forelse($blog->comments as $comment)
                            <div class="comment-wrapper" id="comment-{{ $comment->id }}">
                                <!-- Parent Comment Card -->
                                <div class="d-flex align-items-start p-3 rounded-4 comment-card" style="background: #f8f9fa;">
                                    <div class="avatar bg-secondary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width:38px; height:38px; border-radius:50%; font-size: 0.9rem; flex-shrink:0;">
                                        {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">{{ $comment->user->name ?? 'Anonymous' }}</h6>
                                            <span class="text-muted small comment-time" style="font-size: 0.75rem;">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-secondary mb-2 style-comment-text small" style="white-space: pre-line;">{{ $comment->comment }}</p>
                                        
                                        <!-- Actions Row -->
                                        <div class="d-flex gap-3 align-items-center">
                                            <!-- Reply button (Only for blog owner on other users' comments) -->
                                            @auth
                                                @if($blog->user_id === auth()->id() && $comment->user_id !== auth()->id())
                                                    <button class="btn btn-sm btn-link text-primary p-0 fw-bold text-decoration-none btn-comment-reply" data-id="{{ $comment->id }}" style="font-size: 0.8rem;">
                                                        <i class="fa-solid fa-reply me-1"></i>Reply
                                                    </button>
                                                @endif
                                                <!-- Delete button (If comment author or blog owner) -->
                                                @if($comment->user_id === auth()->id() || $blog->user_id === auth()->id())
                                                    <button class="btn btn-sm btn-link text-danger p-0 fw-bold text-decoration-none btn-comment-delete ms-auto" data-id="{{ $comment->id }}" style="font-size: 0.8rem;">
                                                        <i class="fa-solid fa-trash me-1"></i>Delete
                                                    </button>
                                                @endif
                                            @endauth
                                        </div>

                                        <!-- Reply Form Container -->
                                        <div class="reply-form-container mt-3 d-none" id="reply-form-{{ $comment->id }}">
                                            <form class="inline-reply-form" data-parent-id="{{ $comment->id }}">
                                                @csrf
                                                <div class="d-flex">
                                                    <textarea class="form-control border-0 bg-white p-2.5 small" rows="2" placeholder="Write a reply..." style="border-radius: 12px; resize: none; font-size: 0.85rem; box-shadow: 0 0 10px rgba(0,0,0,0.03);" required></textarea>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-3 py-1.5 ms-2 align-self-end fw-bold" style="font-size: 0.8rem;">Reply</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Replies List (Nested) -->
                                <div class="replies-wrapper ms-5 mt-3 d-flex flex-column gap-3" id="replies-container-{{ $comment->id }}" style="border-left: 2px dashed #e2e8f0; padding-left: 1rem;">
                                    @foreach($comment->replies as $reply)
                                        <div class="d-flex align-items-start p-3 rounded-4 reply-card" id="comment-{{ $reply->id }}" style="background: rgba(255, 71, 87, 0.03); border: 1px solid rgba(255, 71, 87, 0.05);">
                                            <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width:34px; height:34px; border-radius:50%; font-size: 0.8rem; flex-shrink:0;">
                                                {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">{{ $reply->user->name ?? 'Anonymous' }}</h6>
                                                        @if($reply->user_id === $blog->user_id)
                                                            <span class="badge bg-primary text-white" style="font-size: 0.65rem; border-radius: 4px; padding: 2px 6px;">Author</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-muted small" style="font-size: 0.72rem;">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-secondary mb-1 style-comment-text small" style="white-space: pre-line;">{{ $reply->comment }}</p>
                                                
                                                @auth
                                                    @if($reply->user_id === auth()->id() || $blog->user_id === auth()->id())
                                                        <div class="text-end">
                                                            <button class="btn btn-sm btn-link text-danger p-0 fw-bold text-decoration-none btn-comment-delete" data-id="{{ $reply->id }}" style="font-size: 0.75rem;">
                                                                <i class="fa-solid fa-trash me-1"></i>Delete
                                                            </button>
                                                        </div>
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div id="no-comments-placeholder" class="text-center py-5">
                                <i class="fa-solid fa-comments fa-3x opacity-20 mb-3 text-secondary"></i>
                                <p class="text-muted small mb-0">No comments yet. Share your thoughts above!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Related Articles -->
        <div class="col-lg-4">
            <!-- Share Card -->
            <div class="glass-card mb-4" style="background: #fff;">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-share-nodes me-2 text-primary"></i>Share Article</h5>
                <p class="text-muted small">Spread the word about this community story.</p>
                <div class="d-flex gap-2">
                    <button onclick="copyArticleUrl()" class="btn btn-light rounded-circle shadow-sm" style="width: 44px; height: 44px;" title="Copy Link"><i class="fa-solid fa-link"></i></button>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($blog->title . ' - ' . url()->current()) }}" target="_blank" class="btn btn-light rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px; color:#25D366;" title="Share on WhatsApp"><i class="fa-brands fa-whatsapp fa-lg"></i></a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($blog->title) }}" target="_blank" class="btn btn-light rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px; color:#1DA1F2;" title="Share on X"><i class="fa-brands fa-x-twitter fa-lg"></i></a>
                </div>
            </div>

            <!-- Related Articles Card -->
            <div class="glass-card shadow-sm p-4" style="background: #fff;">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-fire me-2 text-primary"></i>Keep Reading</h5>
                <hr class="mt-0 mb-3 opacity-10">

                <div class="d-flex flex-column gap-3">
                    @foreach($related as $rel)
                        <div class="d-flex align-items-center gap-3 py-2 border-bottom border-light last-border-0">
                            @if($rel->media_path)
                                @php
                                    $relMediaList = is_array($rel->media_path) ? $rel->media_path : json_decode($rel->media_path, true);
                                    $relSinglePath = is_array($relMediaList) ? ($relMediaList[0] ?? null) : $rel->media_path;
                                @endphp
                                @if($relSinglePath)
                                    <div style="width: 70px; height: 70px; border-radius: 12px; overflow: hidden; flex-shrink: 0;">
                                        @php
                                            $ext = strtolower(pathinfo($relSinglePath, PATHINFO_EXTENSION));
                                            $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                        @endphp
                                        @if($isVid)
                                            <video src="{{ asset('storage/' . $relSinglePath) }}" muted class="w-100 h-100 object-fit-cover"></video>
                                        @else
                                            <img src="{{ asset('storage/' . $relSinglePath) }}" alt="{{ $rel->title }}" class="w-100 h-100 object-fit-cover">
                                        @endif
                                    </div>
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="width: 70px; height: 70px; border-radius: 12px; flex-shrink: 0;">
                                        <i class="fa-solid fa-newspaper opacity-20"></i>
                                    </div>
                                @endif
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="width: 70px; height: 70px; border-radius: 12px; flex-shrink: 0;">
                                    <i class="fa-solid fa-newspaper opacity-20"></i>
                                </div>
                            @endif
                            <div class="overflow-hidden">
                                <h6 class="fw-bold text-dark mb-1 text-truncate" style="font-size: 0.95rem;">
                                    <a href="{{ route('blogs.public.show', $rel->id) }}" class="text-decoration-none text-dark hover-primary">{{ $rel->title }}</a>
                                </h6>
                                <span class="small text-muted d-block" style="font-size: 0.8rem;">{{ $rel->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="py-5 text-white mt-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="small opacity-50 mb-0">&copy; 2026 Mali Setu Foundation. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                <a href="{{ url('/') }}" class="text-white small opacity-50 text-decoration-none hover-text-primary">Back to Homepage</a>
            </div>
        </div>
    </div>
</footer>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function copyArticleUrl() {
        navigator.clipboard.writeText(window.location.href);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Article link copied to clipboard!',
            showConfirmButton: false,
            timer: 3000,
            background: 'rgba(255, 255, 255, 0.95)',
        });
    }

    function toggleBlogPublicLike(button, blogId) {
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
                    icon.className = 'fa-solid fa-heart fa-lg';
                } else {
                    button.classList.remove('liked');
                    icon.className = 'fa-regular fa-heart fa-lg';
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

    // ── Blog Comments AJAX Logic ──────────────────────────────────────────

    // Toggle Reply Form
    document.addEventListener('click', function (e) {
        if (e.target && (e.target.classList.contains('btn-comment-reply') || e.target.closest('.btn-comment-reply'))) {
            e.preventDefault();
            const btn = e.target.classList.contains('btn-comment-reply') ? e.target : e.target.closest('.btn-comment-reply');
            const commentId = btn.getAttribute('data-id');
            const formContainer = document.getElementById(`reply-form-${commentId}`);
            if (formContainer) {
                formContainer.classList.toggle('d-none');
                if (!formContainer.classList.contains('d-none')) {
                    formContainer.querySelector('textarea').focus();
                }
            }
        }
    });

    // Submit Main Comment
    const mainCommentForm = document.getElementById('main-comment-form');
    if (mainCommentForm) {
        mainCommentForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const textarea = document.getElementById('main-comment-textarea');
            const commentText = textarea.value.trim();
            const btn = this.querySelector('button[type="submit"]');

            if (!commentText) return;

            btn.disabled = true;
            const originalBtnContent = btn.innerHTML;
            btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin me-1"></i>Publishing...`;

            fetch(`/dashboard/blogs/{{ $blog->id }}/comments`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ comment: commentText })
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;

                if (data.success) {
                    textarea.value = '';
                    // Remove empty placeholder if present
                    const placeholder = document.getElementById('no-comments-placeholder');
                    if (placeholder) {
                        placeholder.remove();
                    }

                    // Render new comment card dynamically
                    const commentHtml = `
                        <div class="comment-wrapper" id="comment-${data.data.id}">
                            <div class="d-flex align-items-start p-3 rounded-4 comment-card" style="background: #f8f9fa;">
                                <div class="avatar bg-secondary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width:38px; height:38px; border-radius:50%; font-size: 0.9rem; flex-shrink:0;">
                                    ${data.data.user.initial}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">${data.data.user.name}</h6>
                                        <span class="text-muted small comment-time" style="font-size: 0.75rem;">${data.data.created_at}</span>
                                    </div>
                                    <p class="text-secondary mb-2 style-comment-text small" style="white-space: pre-line;">${data.data.comment}</p>
                                    <div class="d-flex gap-3 align-items-center">
                                        <button class="btn btn-sm btn-link text-danger p-0 fw-bold text-decoration-none btn-comment-delete ms-auto" data-id="${data.data.id}" style="font-size: 0.8rem;">
                                            <i class="fa-solid fa-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                    <div class="reply-form-container mt-3 d-none" id="reply-form-${data.data.id}">
                                        <form class="inline-reply-form" data-parent-id="${data.data.id}">
                                            @csrf
                                            <div class="d-flex">
                                                <textarea class="form-control border-0 bg-white p-2.5 small" rows="2" placeholder="Write a reply..." style="border-radius: 12px; resize: none; font-size: 0.85rem; box-shadow: 0 0 10px rgba(0,0,0,0.03);" required></textarea>
                                                <button type="submit" class="btn btn-primary rounded-pill px-3 py-1.5 ms-2 align-self-end fw-bold" style="font-size: 0.8rem;">Reply</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="replies-wrapper ms-5 mt-3 d-flex flex-column gap-3" id="replies-container-${data.data.id}" style="border-left: 2px dashed #e2e8f0; padding-left: 1rem;"></div>
                        </div>
                    `;
                    document.getElementById('comments-container').insertAdjacentHTML('afterbegin', commentHtml);
                    updateCommentCounts(1);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000,
                        background: 'rgba(255, 255, 255, 0.95)'
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: data.message || 'Failed to post comment.' });
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong.' });
            });
        });
    }

    // Submit Inline Reply
    document.addEventListener('submit', function (e) {
        if (e.target && e.target.classList.contains('inline-reply-form')) {
            e.preventDefault();
            const form = e.target;
            const parentId = form.getAttribute('data-parent-id');
            const textarea = form.querySelector('textarea');
            const replyText = textarea.value.trim();
            const btn = form.querySelector('button[type="submit"]');

            if (!replyText) return;

            btn.disabled = true;
            const originalBtnContent = btn.innerHTML;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i>`;

            fetch(`/dashboard/blogs/{{ $blog->id }}/comments`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    comment: replyText,
                    parent_id: parentId
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;

                if (data.success) {
                    textarea.value = '';
                    document.getElementById(`reply-form-${parentId}`).classList.add('d-none');

                    // Render new nested reply card dynamically
                    const replyHtml = `
                        <div class="d-flex align-items-start p-3 rounded-4 reply-card" id="comment-${data.data.id}" style="background: rgba(255, 71, 87, 0.03); border: 1px solid rgba(255, 71, 87, 0.05);">
                            <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width:34px; height:34px; border-radius:50%; font-size: 0.8rem; flex-shrink:0;">
                                ${data.data.user.initial}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">${data.data.user.name}</h6>
                                        <span class="badge bg-primary text-white" style="font-size: 0.65rem; border-radius: 4px; padding: 2px 6px;">Author</span>
                                    </div>
                                    <span class="text-muted small" style="font-size: 0.72rem;">${data.data.created_at}</span>
                                </div>
                                <p class="text-secondary mb-1 style-comment-text small" style="white-space: pre-line;">${data.data.comment}</p>
                                <div class="text-end">
                                    <button class="btn btn-sm btn-link text-danger p-0 fw-bold text-decoration-none btn-comment-delete" data-id="${data.data.id}" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-trash me-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById(`replies-container-${parentId}`).insertAdjacentHTML('beforeend', replyHtml);
                    updateCommentCounts(1);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000,
                        background: 'rgba(255, 255, 255, 0.95)'
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: data.message || 'Failed to post reply.' });
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong.' });
            });
        }
    });

    // Delete Comment
    document.addEventListener('click', function (e) {
        if (e.target && (e.target.classList.contains('btn-comment-delete') || e.target.closest('.btn-comment-delete'))) {
            e.preventDefault();
            const btn = e.target.classList.contains('btn-comment-delete') ? e.target : e.target.closest('.btn-comment-delete');
            const commentId = btn.getAttribute('data-id');
            const commentEl = document.getElementById(`comment-${commentId}`);

            if (!commentEl) return;

            Swal.fire({
                title: 'Delete Comment?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff4757',
                cancelButtonColor: '#718096',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/dashboard/blogs/comments/${commentId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Animate and remove
                            commentEl.style.transition = 'all 0.4s ease';
                            commentEl.style.opacity = '0';
                            commentEl.style.transform = 'translateY(20px)';
                            setTimeout(() => {
                                commentEl.remove();
                                // If no comments left, show placeholder
                                const container = document.getElementById('comments-container');
                                if (container && container.children.length === 0) {
                                    container.innerHTML = `
                                        <div id="no-comments-placeholder" class="text-center py-5">
                                            <i class="fa-solid fa-comments fa-3x opacity-20 mb-3 text-secondary"></i>
                                            <p class="text-muted small mb-0">No comments yet. Share your thoughts above!</p>
                                        </div>
                                    `;
                                }
                            }, 400);

                            updateCommentCounts(-1);

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 3000,
                                background: 'rgba(255, 255, 255, 0.95)'
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Unauthorized', text: data.message || 'Failed to delete.' });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' });
                    });
                }
            });
        }
    });

    // Helper to update badges
    function updateCommentCounts(change) {
        const countBadge = document.getElementById('total-comment-count');
        if (countBadge) {
            const current = parseInt(countBadge.textContent) || 0;
            countBadge.textContent = Math.max(0, current + change);
        }
    }
</script>

</body>
</html>
