@extends('layouts.app')

@section('content')
<style>
    .premium-gradient-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        border-radius: 24px;
        color: white;
        box-shadow: 0 15px 35px rgba(13, 148, 136, 0.18);
        border: none;
        overflow: hidden;
        position: relative;
    }
    .premium-gradient-card::after {
        content: '';
        position: absolute;
        width: 250px;
        height: 250px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        top: -60px;
        right: -60px;
    }
    .premium-gradient-card::before {
        content: '';
        position: absolute;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        bottom: -30px;
        left: -30px;
    }
    .pricing-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid rgba(13, 148, 136, 0.08) !important;
        border-radius: 20px;
    }
    .pricing-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 38px rgba(0, 0, 0, 0.06);
        border-color: var(--primary) !important;
    }
    .feature-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(13, 148, 136, 0.08);
        color: var(--primary);
    }
    .skip-btn {
        transition: all 0.25s ease;
    }
    .skip-btn:hover {
        background: #f1f5f9;
        transform: scale(1.02);
    }
</style>

<div class="container py-4 text-start">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Welcome Banner -->
            <div class="card premium-gradient-card p-4 p-md-5 mb-5 text-start">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge bg-white bg-opacity-20 text-white mb-3 px-3 py-2 rounded-pill fw-bold text-uppercase" style="backdrop-filter: blur(10px);">Premium Catalog Setup</span>
                        <h2 class="fw-bold mb-2">Grow Your Business on Mali Setu</h2>
                        <p class="opacity-90 mb-0">Select a premium membership plan to list products, advertise specialized service packages, list regional jobs, and acquire Caste-verified business badges.</p>
                    </div>
                    <div class="col-md-4 text-end d-none d-md-block">
                        <div class="fs-1 text-white opacity-25" style="font-size: 6rem !important;"><i class="fa-solid fa-gem"></i></div>
                    </div>
                </div>
            </div>

            <!-- Value Proposition Features -->
            <div class="row g-4 mb-5 text-start">
                <div class="col-md-4">
                    <div class="glass-card p-4 h-100 d-flex gap-3">
                        <div class="feature-icon flex-shrink-0 mt-1"><i class="fa-solid fa-box-open"></i></div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Product Catalog</h6>
                            <p class="text-secondary small mb-0">List and advertise your products with rich descriptions, pricing, and visual thumbnails.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card p-4 h-100 d-flex gap-3">
                        <div class="feature-icon flex-shrink-0 mt-1"><i class="fa-solid fa-gears"></i></div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Services Suite</h6>
                            <p class="text-secondary small mb-0">Present custom services Packages, consulting sessions, or agricultural bookings to matches.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card p-4 h-100 d-flex gap-3">
                        <div class="feature-icon flex-shrink-0 mt-1"><i class="fa-solid fa-user-tie"></i></div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Local Job Boards</h6>
                            <p class="text-secondary small mb-0">Publish job postings, track applicant credentials, and recruit skilled local talent directly.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plans Selector Grid -->
            <h4 class="fw-bold text-center text-dark mb-4">Choose Your Premium Plan</h4>
            <div class="row g-4 justify-content-center mb-5 text-start">
                @if($plans->isEmpty())
                    <div class="col-12 text-center py-4 bg-light rounded-4 border">
                        <p class="text-muted mb-0">No active plans are currently configured for your business type ({{ $business->business_type }}).</p>
                    </div>
                @else
                    @foreach($plans as $plan)
                        <div class="col-md-5">
                            <div class="card pricing-card h-100 border-0 p-4 text-center bg-white shadow-sm relative">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-3">{{ $plan->company_type }} Premium</h5>
                                        <div class="my-4">
                                            <h1 class="fw-extrabold text-primary mb-0 font-monospace">₹{{ number_format($plan->price, 0) }}</h1>
                                            <small class="text-muted">Subscription valid for {{ $plan->duration_years }} Year(s)</small>
                                        </div>
                                        <hr class="opacity-10 my-4">
                                        <ul class="list-unstyled text-start mb-4">
                                            <li class="mb-2.5 small text-secondary"><i class="fa-solid fa-circle-check text-primary me-2"></i> Showcase Enterprise Catalogs</li>
                                            <li class="mb-2.5 small text-secondary"><i class="fa-solid fa-circle-check text-primary me-2"></i> Showcase Services & Packages</li>
                                            <li class="mb-2.5 small text-secondary"><i class="fa-solid fa-circle-check text-primary me-2"></i> Unlimited Job Openings & Recruits</li>
                                            <li class="mb-2.5 small text-secondary"><i class="fa-solid fa-circle-check text-primary me-2"></i> Premium Directory badge</li>
                                        </ul>
                                    </div>
                                    <button class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm" onclick="startRazorpayPayment({{ $plan->id }}, {{ $plan->price }})">
                                        Activate Premium Plan <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Skip and Proceed Flow -->
            <div class="text-center mt-4">
                <p class="text-secondary small mb-3">Not ready to subscribe yet? You can start with your trial console dashboard first.</p>
                <a href="{{ route('dashboard.business.index') }}" class="btn btn-outline-secondary skip-btn px-5 py-3 rounded-3 fw-bold border-2">
                    Skip & Go to Dashboard <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
                </a>
            </div>

        </div>
    </div>
</div>

<!-- Razorpay Checkout Integration -->
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
                            window.location.href = "{{ route('dashboard.business.index') }}";
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
                    color: "#0d9488"
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
@endsection
