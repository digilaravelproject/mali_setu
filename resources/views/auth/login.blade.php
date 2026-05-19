<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — Mali Setu</title>
    <meta name="description" content="Access your Mali Setu account. Connect, serve, and grow with your community.">

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
            --glass: rgba(255, 255, 255, 0.85);
            --border-glow: rgba(173, 20, 87, 0.15);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #fff0f5 0%, #ffe4e1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2d3436;
            padding: 20px;
        }

        /* Bootstrap Overrides */
        .text-primary { color: var(--primary) !important; }
        .bg-primary { background-color: var(--primary) !important; }

        .auth-container {
            max-width: 1000px;
            width: 100%;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(173, 20, 87, 0.1);
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .auth-banner {
            background: linear-gradient(135deg, #ad1457, #5c0529);
            color: #fff;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .auth-banner::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }

        .auth-banner::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 122, 89, 0.1);
            border-radius: 50%;
            bottom: -50px;
            left: -50px;
        }

        .auth-form-side {
            padding: 50px;
        }

        .form-control-lg {
            border-radius: 12px;
            font-size: 1rem;
            padding: 14px 20px;
            border: 1.5px solid #e2e8f0;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .form-control-lg:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--border-glow);
            background: #fff;
        }

        .btn-primary {
            background: var(--primary) !important;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.05rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(173, 20, 87, 0.2);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            z-index: 10;
        }

        .back-home:hover {
            color: var(--primary-dark);
            transform: translateX(-3px);
        }

        .brand-logo {
            height: 50px;
            width: auto;
            margin-bottom: 30px;
        }

        .social-btn {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: 0.3s;
            background: #fff;
            color: #4a5568;
            text-decoration: none;
        }

        .social-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        @media (max-width: 991px) {
            .auth-banner {
                display: none !important;
            }
            .auth-form-side {
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>

<a href="{{ url('/') }}" class="back-home">
    <i class="fa-solid fa-arrow-left"></i> Back to Home
</a>

<div class="auth-container">
    <div class="row g-0">
        <!-- Banner Side -->
        <div class="col-lg-5 auth-banner">
            <div>
                <img src="{{ asset('landing_page_logo.jpeg') }}" alt="MaliSetu Logo" class="brand-logo rounded-3 shadow-sm bg-white p-2">
                <h2 class="fw-bold mt-4">Welcome back to Mali Setu</h2>
                <p class="opacity-75 mt-3">Access India's premier community bridge designed to connect verified businesses, matrimony seekers, and dedicated volunteers.</p>
            </div>
            
            <div class="border-top border-white border-opacity-10 pt-4 mt-5">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-shield-halved text-warning fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">100% Trust & Verification</h6>
                        <small class="opacity-50">Every profile is manually audited by community admins.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Side -->
        <div class="col-lg-7 auth-form-side">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">Sign In</h3>
                <span class="text-muted small">New user? <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Create Account</a></span>
            </div>

            <!-- Notifications / Success status -->
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

            <form action="{{ route('login') }}" method="POST" class="mt-4">
                @csrf
                
                <!-- Email Input -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Email Address</label>
                    <div class="position-relative">
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            class="form-control form-control-lg @error('email') is-invalid @enderror" 
                            placeholder="Enter your email" 
                            required 
                            autocomplete="email"
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small fw-bold text-secondary mb-0">Password</label>
                        <a href="{{ route('password.request') }}" class="small text-primary text-decoration-none fw-bold">Forgot password?</a>
                    </div>
                    <div class="position-relative">
                        <input 
                            type="password" 
                            name="password" 
                            class="form-control form-control-lg @error('password') is-invalid @enderror" 
                            placeholder="Min 8 characters" 
                            required
                        >
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="mb-4 form-check d-flex align-items-center gap-2">
                    <input type="checkbox" name="remember" class="form-check-input shadow-none cursor-pointer" id="rememberMe">
                    <label class="form-check-label small text-secondary cursor-pointer" for="rememberMe">Remember me on this device</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">
                    Sign In <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                </button>
            </form>

            <!-- Social Authentication Divider -->
            <div class="text-center my-4 position-relative">
                <hr class="text-muted opacity-25">
                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-secondary small">Or connect with</span>
            </div>

            <!-- Social Logins -->
            <div class="row g-3">
                <div class="col-sm-6">
                    <a href="#" class="social-btn">
                        <i class="fa-brands fa-google text-danger fs-5"></i> Google
                    </a>
                </div>
                <div class="col-sm-6">
                    <a href="#" class="social-btn">
                        <i class="fa-brands fa-apple text-dark fs-5"></i> Apple ID
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
