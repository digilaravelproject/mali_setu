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
            --primary: #ad1457; 
            --primary-dark: #7f0037;
            --accent: #ff7a59; 
            --light-bg: #fff5f8;
            --glass: rgba(255, 255, 255, 0.1);
        }
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: #2d3436; overflow-x: hidden; }
        
        /* Bootstrap Helper Overrides for Robust Pink Theme */
        .text-primary { color: var(--primary) !important; }
        .bg-primary { background-color: var(--primary) !important; }
        .border-primary { border-color: var(--primary) !important; }
        
        /* Glassmorphism & Custom Elements */
        .hero { 
            background: radial-gradient(circle at top right, #d81b60, #4a0022); 
            padding: 120px 0 160px;
            clip-path: ellipse(150% 100% at 50% 0%);
            color: #fff;
        }

        .search-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .category-card {
            border: none;
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            text-align: center;
            padding: 2.5rem 1.5rem;
        }
        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(173, 20, 87, 0.15);
        }

        .icon-circle {
            width: 70px; height: 70px;
            border-radius: 20px;
            background: #ffe4e8;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            color: var(--primary);
            font-size: 28px;
            transition: 0.3s;
        }
        .category-card:hover .icon-circle { background: var(--primary); color: #fff; }

        .stat-card { border-radius: 20px; background: #fff; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .stat-value { font-size: 2.5rem; font-weight: 800; color: var(--primary); line-height: 1; }

        .floating { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .how-it-works-step { position: relative; }
        .how-it-works-step::after {
            content: ''; position: absolute; top: 15%; right: -50%;
            width: 80%; height: 2px; border-top: 2px dashed #cbd5e0;
            z-index: -1;
        }
        @media (max-width: 991px) { .how-it-works-step::after { display: none; } }

        .swiper-pagination-bullet-active { background: var(--primary) !important; }
        footer { background: #210210; }
        
        .btn-primary { background-color: var(--primary) !important; border: none; padding: 12px 30px; border-radius: 12px; font-weight: 600; transition: 0.3s; }
        .btn-primary:hover { background-color: var(--primary-dark) !important; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(173, 20, 87, 0.15); }
        .btn-outline-primary { border-color: var(--primary) !important; color: var(--primary) !important; padding: 12px 30px; border-radius: 12px; transition: 0.3s; }
        .btn-outline-primary:hover { background-color: var(--primary) !important; color: #fff !important; }
        .btn-warning { background-color: var(--accent); border: none; color: #fff; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top navbar-light bg-white py-3 shadow-sm">
    <div class="container">
        <!-- <a class="navbar-brand fw-bold text-primary fs-3" href="#">Mali<span style="color:var(--accent)">Setu</span></a> -->

        <a class="navbar-brand" href="#">
            <img 
                src="{{ asset('landing_page_logo.jpeg') }}" 
                alt="MaliSetu Logo"
                style="height:60px; width:auto; object-fit:contain;"
            >
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3" href="#directory">Directory</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#matrimony">Matrimony</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#community">Community</a></li>
                @auth
                    <li class="nav-item ms-lg-3"><a class="btn btn-primary" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a></li>
                @else
                    <li class="nav-item ms-lg-3"><a class="btn btn-primary" href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket me-2"></i>Sign In</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<header class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7" data-aos="fade-right">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-3 fw-bold">India's Leading Community Platform</span>
                <h1 class="display-3 fw-bold mb-4">Empowering the <span class="text-warning">Mali Community</span> Digitally</h1>
                <p class="lead mb-5 opacity-75">Connect with verified businesses, discover life partners, and participate in community-driven growth across the nation.</p>

                <div class="search-card mt-4">
                    <form class="row g-3">
                        <div class="col-md-5">
                            <label class="small opacity-75 mb-1">What are you looking for?</label>
                            <input type="text" class="form-control form-control-lg border-0 shadow-none" placeholder="e.g. CA, Doctor, Wedding Venue">
                        </div>
                        <div class="col-md-4">
                            <label class="small opacity-75 mb-1">In which city?</label>
                            <select class="form-select form-select-lg border-0 shadow-none">
                                <option>Mumbai</option>
                                <option>Pune</option>
                                <option>Delhi</option>
                                <option>Nashik</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-grid align-items-end">
                            <button class="btn btn-warning btn-lg fw-bold">Search Now</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center" data-aos="zoom-in">
                <img src="https://images.unsplash.com/photo-1528605248644-14dd04022da1?auto=format&fit=crop&q=80&w=800" class="img-fluid rounded-5 shadow-lg floating" alt="Community Interaction">
            </div>
        </div>
    </div>
</header>

<!-- STATS -->
<section class="py-5 bg-light" style="margin-top: -80px;">
    <div class="container position-relative">
        <div class="row g-4">
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card text-center">
                    <div class="stat-value mb-2"><span class="stat" data-target="15000">0</span>+</div>
                    <p class="text-muted fw-bold mb-0">Active Members</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card text-center">
                    <div class="stat-value mb-2"><span class="stat" data-target="1200">0</span>+</div>
                    <p class="text-muted fw-bold mb-0">Verified Businesses</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card text-center">
                    <div class="stat-value mb-2"><span class="stat" data-target="500">0</span>+</div>
                    <p class="text-muted fw-bold mb-0">Success Stories</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card text-center">
                    <div class="stat-value mb-2"><span class="stat" data-target="85">0</span>+</div>
                    <p class="text-muted fw-bold mb-0">Cities Served</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CATEGORIES -->
<section class="py-5" id="directory">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h6 class="text-primary fw-bold text-uppercase tracking-widest">Explore Directory</h6>
            <h2 class="display-5 fw-bold">Popular Business Categories</h2>
        </div>
        <div class="row g-4">
            <!-- Rendered 8 cards to ensure length -->
            <div class="col-6 col-md-3">
                <div class="category-card">
                    <div class="icon-circle"><i class="fa-solid fa-stethoscope"></i></div>
                    <h5 class="fw-bold">Doctors</h5>
                    <p class="small text-muted mb-0">240+ Specialists</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="category-card">
                    <div class="icon-circle"><i class="fa-solid fa-scale-balanced"></i></div>
                    <h5 class="fw-bold">Legal</h5>
                    <p class="small text-muted mb-0">110+ Advocates</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="category-card">
                    <div class="icon-circle"><i class="fa-solid fa-utensils"></i></div>
                    <h5 class="fw-bold">Restaurants</h5>
                    <p class="small text-muted mb-0">350+ Venues</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="category-card">
                    <div class="icon-circle"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h5 class="fw-bold">Education</h5>
                    <p class="small text-muted mb-0">180+ Tutors</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="category-card">
                    <div class="icon-circle"><i class="fa-solid fa-hammer"></i></div>
                    <h5 class="fw-bold">Construction</h5>
                    <p class="small text-muted mb-0">95+ Experts</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="category-card">
                    <div class="icon-circle"><i class="fa-solid fa-camera"></i></div>
                    <h5 class="fw-bold">Events</h5>
                    <p class="small text-muted mb-0">150+ Services</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="category-card">
                    <div class="icon-circle"><i class="fa-solid fa-car-side"></i></div>
                    <h5 class="fw-bold">Logistics</h5>
                    <p class="small text-muted mb-0">60+ Vendors</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="category-card">
                    <div class="icon-circle"><i class="fa-solid fa-plus"></i></div>
                    <h5 class="fw-bold">View More</h5>
                    <p class="small text-muted mb-0">50+ More categories</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">Simple to Start</h2>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-4 how-it-works-step">
                <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                    <span class="fs-3 fw-bold text-primary">01</span>
                </div>
                <h5>Register Profile</h5>
                <p class="text-muted">Create your free account as a user or business owner.</p>
            </div>
            <div class="col-md-4 how-it-works-step">
                <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                    <span class="fs-3 fw-bold text-primary">02</span>
                </div>
                <h5>Verify Details</h5>
                <p class="text-muted">Our community admins verify information to ensure trust.</p>
            </div>
            <div class="col-md-4">
                <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                    <span class="fs-3 fw-bold text-primary">03</span>
                </div>
                <h5>Grow Together</h5>
                <p class="text-muted">Access services, find partners, and contribute back.</p>
            </div>
        </div>
    </div>
</section>

<!-- MATRIMONY -->
<section class="py-5" id="matrimony">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-5" data-aos="fade-up">
                <h6 class="text-primary fw-bold text-uppercase mb-3">Matrimony</h6>
                <h2 class="display-5 fw-bold mb-4">Find Your Perfect Life Partner</h2>
                <p class="text-muted mb-4">Privacy-first matrimony profiles tailored for our community. Every profile is manually verified to ensure safety and authenticity.</p>
                <ul class="list-unstyled mb-5">
                    <li class="mb-3"><i class="fa-solid fa-circle-check text-success me-2"></i> Private Contact Details</li>
                    <li class="mb-3"><i class="fa-solid fa-circle-check text-success me-2"></i> Community Verification</li>
                    <li class="mb-3"><i class="fa-solid fa-circle-check text-success me-2"></i> Free Registration</li>
                </ul>
                <div class="d-flex gap-3">
                    <a href="#directory" class="btn btn-primary">Browse Profiles</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">Register Free</a>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="profile-card p-3 bg-white shadow-sm rounded-4 text-center">
                            <img src="https://randomuser.me/api/portraits/men/44.jpg" class="img-fluid rounded-4 mb-3" alt="Member">
                            <h6 class="fw-bold mb-1">Rajesh Mali</h6>
                            <p class="small text-muted mb-0">Software Engineer • 28 yrs</p>
                        </div>
                    </div>
                    <div class="col-6 mt-5">
                        <div class="profile-card p-3 bg-white shadow-sm rounded-4 text-center">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" class="img-fluid rounded-4 mb-3" alt="Member">
                            <h6 class="fw-bold mb-1">Priya Mali</h6>
                            <p class="small text-muted mb-0">Chartered Accountant • 26 yrs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">Community Voices</h2>
        </div>
        <div class="swiper testimonialSwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="bg-white p-5 rounded-4 shadow-sm text-center">
                        <i class="fa-solid fa-quote-left fs-1 text-primary opacity-25 mb-4"></i>
                        <p class="fs-5 text-muted">"Mali Setu helped me find a trusted civil contractor within our community. The service was seamless and the verification gave me peace of mind."</p>
                        <h6 class="fw-bold mt-4 mb-0">Sanjay Kumar</h6>
                        <span class="small text-muted">Business Owner, Pune</span>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="bg-white p-5 rounded-4 shadow-sm text-center">
                        <i class="fa-solid fa-quote-left fs-1 text-primary opacity-25 mb-4"></i>
                        <p class="fs-5 text-muted">"Finding a life partner who shares our cultural values was easier than expected. I'm thankful to the team for their dedicated support."</p>
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
<section class="py-5">
    <div class="container">
        <div class="p-5 rounded-5 bg-primary text-white text-center position-relative overflow-hidden" data-aos="zoom-in">
            <div class="position-relative z-1">
                <h2 class="display-5 fw-bold mb-3">Ready to Join Your Community?</h2>
                <p class="lead opacity-75 mb-5">Join thousands of members today and experience growth together.</p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-5">Join as Member</a>
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 text-primary">List Business</a>
                </div>
            </div>
            <!-- Decorative circle -->
            <div class="position-absolute top-0 end-0 bg-white opacity-10 rounded-circle" style="width: 300px; height: 300px; margin-top: -150px; margin-right: -150px;"></div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="py-5 text-white">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-4">
                <h3 class="fw-bold mb-4">Mali Setu</h3>
                <p class="opacity-50">Empowering India's vibrant Mali community through a digital bridge of services, connections, and volunteer support.</p>
                <div class="d-flex gap-3 mt-4">
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-md-2 offset-lg-1">
                <h6 class="fw-bold mb-4">Platform</h6>
                <ul class="list-unstyled opacity-75">
                    <li class="mb-2"><a href="#" class="text-white">Business Directory</a></li>
                    <li class="mb-2"><a href="#" class="text-white">Matrimony</a></li>
                    <li class="mb-2"><a href="#" class="text-white">Volunteering</a></li>
                    <li class="mb-2"><a href="#" class="text-white">Community Blog</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <h6 class="fw-bold mb-4">Company</h6>
                <ul class="list-unstyled opacity-75">
                    <li class="mb-2"><a href="#" class="text-white">About Us</a></li>
                    <li class="mb-2"><a href="#" class="text-white">Impact</a></li>
                    <li class="mb-2"><a href="#" class="text-white">Careers</a></li>
                    <li class="mb-2"><a href="{{ url('contact-us') }}" class="text-white">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold mb-4">Contact Support</h6>
                <p class="small opacity-50 mb-1">Email us at:</p>
                <p class="fw-bold mb-3">help@malisetu.org</p>
                <p class="small opacity-50 mb-1">Call us:</p>
                <p class="fw-bold">+91 98765 43210</p>
            </div>
        </div>
        <hr class="my-5 opacity-10">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="small opacity-50 mb-0">&copy; 2026 Mali Setu Foundation. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="{{ url('privacy-policy') }}" class="text-white small opacity-50 me-4">
                    Privacy Policy
                </a>

                <a href="{{ url('terms-condition') }}" class="text-white small opacity-50">
                    Terms of Service
                </a>

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