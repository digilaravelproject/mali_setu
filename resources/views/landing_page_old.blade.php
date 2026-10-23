<!doctype html>
<html lang="en">
<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Mali Setu — Connect, Serve, Grow</title>
		<meta name="description" content="Mali Setu - Business directory, matrimony services, volunteering, and community support.">

		<!-- Bootstrap -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
		<!-- Font Awesome -->
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
		<!-- AOS -->
		<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
		<!-- Swiper -->
		<link href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" rel="stylesheet">

		<style>
				:root{ --primary:#0b66c3; --accent:#ff7a59; --muted:#6c757d; --glass: rgba(255,255,255,0.06); }
				*{box-sizing:border-box}
				body{font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; color:#222; margin:0; -webkit-font-smoothing:antialiased}
				a {text-decoration:none}
				.hero{background:linear-gradient(180deg, rgba(11,102,195,0.96) 0%, rgba(6,42,84,0.94) 100%); color:#fff}
				.search-card{backdrop-filter: blur(6px); background: var(--glass); border-radius:14px; box-shadow:0 12px 36px rgba(2,32,71,0.08)}
				.category-card{transition:transform .18s ease, box-shadow .18s ease; border-radius:12px}
				.category-card:hover{transform:translateY(-6px); box-shadow:0 16px 40px rgba(2,32,71,0.06)}
				.cta-gradient{background:linear-gradient(90deg,var(--primary), var(--accent)); color:#fff}
				.stat{font-weight:700; color:var(--primary); font-size:1.6rem}
				.profile-card{border-radius:10px; box-shadow:0 8px 28px rgba(2,32,71,0.06)}
				footer a{color:rgba(255,255,255,0.9)}
				footer a:hover{text-decoration:underline}
				.feature-icon{width:56px;height:56px;border-radius:12px;background:#f6fbff;display:flex;align-items:center;justify-content:center}
				.text-muted-2{color:#6b7280}
				@media (max-width:576px){ .hero h1{font-size:1.45rem} }
		</style>
</head>
<body>

<!-- HERO -->
<header class="hero py-5">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-lg-7 text-white" data-aos="fade-right">
				<span class="badge bg-white text-primary px-3 py-2 rounded-pill">Discover Local Services</span>
				<h1 class="display-5 fw-bold mt-3">Mali Setu — Empowering Communities & Connecting Services</h1>
				<p class="lead text-white-75">Find trusted businesses, matrimony profiles, volunteer opportunities, and community services across India.</p>

				<div class="card p-3 search-card mt-4">
					<form class="row g-2 align-items-center">
						<div class="col-12 col-md-6">
							<input type="search" class="form-control form-control-lg" placeholder="Search for services, businesses or people" aria-label="Search">
						</div>
						<div class="col-6 col-md-3">
							<select class="form-select form-select-lg">
								<option>All categories</option>
								<option>Business</option>
								<option>Matrimony</option>
								<option>Volunteering</option>
								<option>Services</option>
							</select>
						</div>
						<div class="col-6 col-md-3 d-grid">
							<button class="btn btn-lg" style="background:var(--primary); color:#fff; border:none">Search <i class="fa-solid fa-magnifying-glass ms-2"></i></button>
						</div>
					</form>
					<div class="d-flex gap-3 mt-3 flex-wrap small text-white-50">
						<div><i class="fa-solid fa-shield-check text-success me-1"></i> Verified Businesses</div>
						<div><i class="fa-solid fa-hands-helping me-1"></i> Community Verified</div>
						<div><i class="fa-solid fa-star text-warning me-1"></i> Trusted</div>
					</div>
				</div>
			</div>

			<div class="col-lg-5 mt-4 mt-lg-0" data-aos="fade-left">
				<div class="position-relative">
					<img loading="eager" src="https://images.unsplash.com/photo-1506806732259-39c2d0268443?q=80&w=1400&auto=format&fit=crop" alt="Indian marketplace" class="img-fluid rounded-4 shadow-lg hero-img">
					<div class="position-absolute top-0 end-0 m-3 p-3 bg-white rounded-3" style="width:220px; box-shadow:0 8px 22px rgba(2,32,71,0.08)">
						<div class="d-flex align-items-center">
							<img src="https://randomuser.me/api/portraits/men/45.jpg" alt="vendor" width="44" class="rounded-circle me-3 vendor-img">
							<div>
								<div class="small text-muted">Featured Business</div>
								<div class="fw-bold">Anmol Kirana</div>
							</div>
						</div>
						<div class="mt-2 small text-muted">Verified • 4.8 <i class="fa-solid fa-star text-warning"></i></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>

<!-- FEATURES -->
<section class="py-5">
	<div class="container">
		<div class="text-center mb-4">
			<h3>Why Mali Setu</h3>
			<p class="text-muted-2">A single platform to discover, connect and grow — for businesses, volunteers, and families.</p>
		</div>
		<div class="row g-4">
			<div class="col-md-4">
				<div class="d-flex gap-3 align-items-start">
					<div class="feature-icon"><i class="fa-solid fa-location-dot text-primary"></i></div>
					<div>
						<h5 class="mb-1">Local Discovery</h5>
						<p class="small text-muted-2 mb-0">Find trusted services and vendors in your city with verified listings.</p>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="d-flex gap-3 align-items-start">
					<div class="feature-icon"><i class="fa-solid fa-users-line text-primary"></i></div>
					<div>
						<h5 class="mb-1">Community Driven</h5>
						<p class="small text-muted-2 mb-0">Community reviews and volunteer-run initiatives keep quality and trust high.</p>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="d-flex gap-3 align-items-start">
					<div class="feature-icon"><i class="fa-solid fa-shield-halved text-primary"></i></div>
					<div>
						<h5 class="mb-1">Safe & Secure</h5>
						<p class="small text-muted-2 mb-0">Profiles and businesses are verified to ensure safety for all users.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- CATEGORIES -->
<section class="py-5 bg-light">
	<div class="container">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h3 class="mb-0">Explore Categories</h3>
			<a href="#" class="text-decoration-none">View all <i class="fa-solid fa-arrow-right ms-1"></i></a>
		</div>
		<div class="row g-3">
			@php $cats=['Restaurants','Plumbers','Doctors','Groceries','Education','Events','Repair','Beauty'] @endphp
			@foreach($cats as $cat)
			<div class="col-6 col-md-4 col-lg-3">
				<div class="p-3 category-card bg-white h-100 d-flex align-items-center gap-3">
					<div class="bg-light rounded-3 p-3"><i class="fa-solid fa-briefcase fa-lg text-primary"></i></div>
					<div>
						<div class="fw-bold">{{ $cat }}</div>
						<div class="small text-muted-2">120+ listings</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>
</section>

<!-- MATRIMONY -->
<section class="py-5">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-up">
				<h3>Matrimony Services</h3>
				<p class="text-muted-2">Secure and verified matrimony profiles with privacy controls and trusted matches.</p>
				<div class="d-flex gap-3 flex-wrap">
					<a href="#" class="btn btn-outline-primary">Browse Profiles</a>
					<a href="#" class="btn btn-primary" style="background:var(--primary); border:none">Create Profile</a>
				</div>
			</div>
			<div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
				<div class="row g-3">
					@for($i=0;$i<4;$i++)
					<div class="col-6">
						<div class="profile-card p-3 text-center bg-white">
							<img loading="lazy" src="https://randomuser.me/api/portraits/men/{{40+$i}}.jpg" class="img-fluid rounded-3 mb-2" alt="profile">
							<div class="fw-bold">Ramesh</div>
							<div class="small text-muted-2">Age 28 • Engineer</div>
						</div>
					</div>
					@endfor
				</div>
			</div>
		</div>
	</div>
</section>

<!-- BUSINESS DIRECTORY -->
<section class="py-5 bg-light">
	<div class="container">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h3 class="mb-0">Featured Businesses</h3>
			<a href="#" class="text-decoration-none">Browse all businesses</a>
		</div>
		<div class="row g-4">
			@for($i=0;$i<6;$i++)
			<div class="col-md-6 col-lg-4">
				<div class="card h-100">
					<img loading="lazy" src="https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.0.3&s={{ $i }}" class="card-img-top" alt="business">
					<div class="card-body">
						<h5 class="card-title">Business {{ $i+1 }}</h5>
						<p class="card-text text-muted small">Category • Location</p>
						<div class="d-flex justify-content-between align-items-center mt-3">
							<div><span class="fw-bold">4.7</span> <small class="text-muted">(220)</small></div>
							<a href="#" class="btn btn-sm btn-outline-primary">View</a>
						</div>
					</div>
				</div>
			</div>
			@endfor
		</div>
	</div>
</section>

<!-- VOLUNTEER & COMMUNITY -->
<section class="py-5">
	<div class="container">
		<div class="row g-4 align-items-center">
			<div class="col-lg-6" data-aos="fade-right">
				<h3>Volunteer & Support Community</h3>
				<p class="text-muted-2">Participate in local drives, events and causes to help your neighbourhood thrive.</p>
				<a href="#" class="btn btn-outline-primary">See Opportunities</a>
			</div>
			<div class="col-lg-6" data-aos="fade-left">
				<div class="row g-3">
					<div class="col-6">
						<div class="p-3 bg-white rounded-3 text-center">
							<i class="fa-solid fa-hand-holding-heart fa-2x text-primary"></i>
							<div class="fw-bold mt-2">Food Drives</div>
						</div>
					</div>
					<div class="col-6">
						<div class="p-3 bg-white rounded-3 text-center">
							<i class="fa-solid fa-seedling fa-2x text-success"></i>
							<div class="fw-bold mt-2">Tree Plantation</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- PLANS -->
<section class="py-5 bg-light">
	<div class="container">
		<div class="text-center mb-4">
			<h3>Plans for Businesses</h3>
			<p class="text-muted-2">Flexible plans to help local businesses get discovered and trusted.</p>
		</div>
		<div class="row g-4">
			<div class="col-md-4">
				<div class="card h-100 text-center p-4">
					<div class="h1 mb-2">Free</div>
					<div class="small text-muted mb-3">Basic listing</div>
					<ul class="text-start small">
						<li>Free listing</li>
						<li>Community reviews</li>
						<li>Basic analytics</li>
					</ul>
					<a href="#" class="btn btn-outline-primary mt-3">Get Started</a>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card h-100 text-center p-4 border-primary">
					<div class="h1 mb-2">Pro</div>
					<div class="small text-muted mb-3">Featured listing</div>
					<ul class="text-start small">
						<li>Priority placement</li>
						<li>Enhanced profile</li>
						<li>Advanced analytics</li>
					</ul>
					<a href="#" class="btn btn-primary mt-3" style="background:var(--primary); border:none">Choose Pro</a>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card h-100 text-center p-4">
					<div class="h1 mb-2">Enterprise</div>
					<div class="small text-muted mb-3">Custom solutions</div>
					<ul class="text-start small">
						<li>Dedicated support</li>
						<li>Bulk onboarding</li>
						<li>Custom integrations</li>
					</ul>
					<a href="#" class="btn btn-outline-primary mt-3">Contact Sales</a>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- FAQ -->
<section class="py-5">
	<div class="container">
		<div class="text-center mb-4">
			<h3>Frequently Asked Questions</h3>
		</div>
		<div class="row g-3">
			<div class="col-lg-8 mx-auto">
				<div class="accordion" id="faq">
					<div class="accordion-item">
						<h2 class="accordion-header" id="q1">
							<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#c1">How do I list my business?</button>
						</h2>
						<div id="c1" class="accordion-collapse collapse show" data-bs-parent="#faq">
							<div class="accordion-body">Sign up, claim your business profile and verify with documents or community validation.</div>
						</div>
					</div>
					<div class="accordion-item">
						<h2 class="accordion-header" id="q2">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c2">Is matrimony data private?</button>
						</h2>
						<div id="c2" class="accordion-collapse collapse" data-bs-parent="#faq">
							<div class="accordion-body">Yes — profiles have privacy controls and we never share contact details without consent.</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- BLOG -->
<section class="py-5 bg-light">
	<div class="container">
		<div class="text-center mb-4">
			<h3>From Our Blog</h3>
		</div>
		<div class="row g-4">
			@for($i=0;$i<3;$i++)
			<div class="col-md-4">
				<div class="card h-100">
					<img loading="lazy" src="https://images.unsplash.com/photo-1506765515384-028b60a970df?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.0.3&s={{ $i+10 }}" class="card-img-top" alt="blog">
					<div class="card-body">
						<h5 class="card-title">How to grow your local business</h5>
						<p class="small text-muted">Insights and stories from small business owners across India.</p>
						<a href="#" class="small">Read more →</a>
					</div>
				</div>
			</div>
			@endfor
		</div>
	</div>
</section>

<!-- TEAM & NEWSLETTER -->
<section class="py-5">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-lg-6" data-aos="fade-right">
				<h3>Meet the Team</h3>
				<p class="text-muted-2">Small, local-first team building tools for communities.</p>
				<div class="d-flex gap-3 mt-3">
					@for($i=0;$i<3;$i++)
					<div class="text-center">
						<img loading="lazy" src="https://randomuser.me/api/portraits/men/{{50+$i}}.jpg" width="72" class="rounded-circle mb-2" alt="team">
						<div class="fw-bold">Member {{ $i+1 }}</div>
						<div class="small text-muted-2">Role</div>
					</div>
					@endfor
				</div>
			</div>
			<div class="col-lg-6" data-aos="fade-left">
				<h5>Stay updated</h5>
				<p class="text-muted-2">Subscribe to receive product updates, community stories and tips.</p>
				<form class="row g-2">
					<div class="col-8">
						<input type="email" class="form-control" placeholder="Your email">
					</div>
					<div class="col-4 d-grid">
						<button class="btn btn-primary">Subscribe</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<!-- CONTACT & FOOTER -->
<footer class="py-5 bg-dark text-white">
	<div class="container">
		<div class="row">
			<div class="col-md-4 mb-4">
				<h5>Mali Setu</h5>
				<p class="text-muted">Connecting people, businesses, and volunteers to build stronger communities.</p>
			</div>
			<div class="col-md-2 mb-4">
				<h6>Company</h6>
				<ul class="list-unstyled small">
					<li><a href="#">About</a></li>
					<li><a href="#">Careers</a></li>
					<li><a href="#">Blog</a></li>
				</ul>
			</div>
			<div class="col-md-3 mb-4">
				<h6>Support</h6>
				<ul class="list-unstyled small">
					<li><a href="/privacy-policy">Privacy Policy</a></li>
					<li><a href="/terms-and-conditions">Terms & Conditions</a></li>
					<li><a href="#">Help Center</a></li>
				</ul>
			</div>
			<div class="col-md-3 mb-4">
				<h6>Contact</h6>
				<p class="small text-muted mb-1">support@malisetu.org</p>
				<p class="small text-muted">+91 98765 43210</p>
			</div>
		</div>
		<div class="d-flex justify-content-between pt-3 border-top border-secondary mt-3 small">
			<div>&copy; {{ date('Y') }} Mali Setu. All rights reserved.</div>
			<div>
				<a href="/privacy-policy" class="me-3">Privacy Policy</a>
				<a href="/terms-and-conditions">Terms & Conditions</a>
			</div>
		</div>
	</div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

<script>
	AOS.init({ duration:700, once:true });

	// Robust image fallback: set all images to fallback SVG if they fail
	const imgFallback = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='500'%3E%3Crect width='100%25' height='100%25' fill='%23e9ecef'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%23777' font-size='20'%3EImage%20Unavailable%3C/text%3E%3C/svg%3E";
	document.addEventListener('DOMContentLoaded', () => {
		document.querySelectorAll('img').forEach(img => {
			img.setAttribute('loading', img.getAttribute('loading') || 'lazy');
			img.addEventListener('error', () => { if(img.src !== imgFallback) img.src = imgFallback; });
		});
	});

	// Counters
	function initCounters(){
		const stats = document.querySelectorAll('.stat');
		const ease = t => 1 - Math.pow(1 - t, 3);
		stats.forEach(el => {
			const target = +el.getAttribute('data-target');
			let started = false;
			const animate = () => {
				if (started) return; started = true;
				const duration = 1400; const start = performance.now();
				const step = (ts) => {
					const t = Math.min(1, (ts - start)/duration);
					el.textContent = Math.floor(ease(t) * target).toLocaleString();
					if(t < 1) requestAnimationFrame(step);
				};
				requestAnimationFrame(step);
			};
			const io = new IntersectionObserver(entries => { if(entries[0].isIntersecting) { animate(); io.disconnect(); } }, {threshold:0.6});
			io.observe(el);
		});
	}
	document.addEventListener('DOMContentLoaded', initCounters);

	// Swiper (testimonials) - kept for compatibility if added later
	try { const swiper = new Swiper('.mySwiper', { loop:true, slidesPerView:1, spaceBetween:20, pagination:{el:'.swiper-pagination', clickable:true}, breakpoints:{768:{slidesPerView:2}} }); } catch(e){}
</script>

</body>
</html>
