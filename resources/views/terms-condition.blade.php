<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->page_name ?? 'Terms & Conditions — Mali Setu' }}</title>
    <meta name="description" content="Mali Setu community terms of service, membership rules, and community guidelines.">

    <!-- Bootstrap & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root { 
            --primary: #84144f; 
            --primary-dark: #630837;
            --accent: #aa1262; 
            --light-bg: #f4f3f0;
            --glass: rgba(255, 255, 255, 0.95);
        }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #2d3436; 
            background-color: var(--light-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Navbar */
        .navbar {
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.9) !important;
        }
        .nav-link {
            color: #4a5568 !important;
            font-weight: 600;
        }
        .nav-link:hover {
            color: var(--primary) !important;
        }

        /* Content Container */
        .page-card {
            background: #ffffff;
            border: 1px solid rgba(255, 71, 87, 0.08);
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 15px 35px rgba(255, 71, 87, 0.03);
            margin: 60px auto;
            max-width: 900px;
            width: 100%;
        }

        .btn-primary { 
            background-color: var(--primary) !important; 
            border: none; 
            padding: 12px 28px; 
            border-radius: 12px; 
            font-weight: 700; 
            transition: 0.3s; 
        }
        .btn-primary:hover { 
            background-color: var(--primary-dark) !important; 
        }

        footer { 
            background: #1c000a; 
            border-top: 5px solid var(--primary);
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
            <span class="ms-2 fw-extrabold text-primary fs-4">Mali<span style="color:var(--accent)">Setu</span></span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3" href="{{ url('/#directory') }}"><i class="fa-solid fa-store text-primary me-1"></i> Directory</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="{{ url('/#matrimony') }}"><i class="fa-solid fa-heart text-danger me-1"></i> Matrimony</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="{{ url('/#plans') }}"><i class="fa-solid fa-gem text-warning me-1"></i> Pricing Plans</a></li>
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

<!-- CONTENT -->
<div class="container flex-grow-1 d-flex align-items-center">
    <div class="page-card text-start">
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3 fw-bold text-uppercase"><i class="fa-solid fa-file-signature me-1"></i> Terms & Guidelines</span>
        <h2 class="fw-extrabold text-dark mb-4">{{ $page->page_name ?? 'Terms of Service' }}</h2>
        <hr class="my-4 opacity-10">
        <div class="text-secondary" style="line-height: 1.8;">
            {!! $page->clean_description ?? '' !!}
        </div>
    </div>
</div>

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
                    <li class="mb-2"><a href="{{ url('/#directory') }}" class="text-white text-decoration-none hover-text-primary">Business Directory</a></li>
                    <li class="mb-2"><a href="{{ url('/#matrimony') }}" class="text-white text-decoration-none hover-text-primary">Matrimony</a></li>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>