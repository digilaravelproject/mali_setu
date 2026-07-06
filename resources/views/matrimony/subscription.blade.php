@extends('layouts.app')

@section('content')
<style>
    .premium-gradient-card {
        background: linear-gradient(135deg, #84144f 0%, #aa1262 100%);
        border-radius: 24px;
        color: white;
        box-shadow: 0 15px 35px rgba(132, 20, 79, 0.15);
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
    .plan-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid rgba(255, 71, 87, 0.08) !important;
        border-radius: 20px;
        text-align: center;
        background: rgba(255, 255, 255, 0.8);
    }
    .plan-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 38px rgba(255, 71, 87, 0.08);
        border-color: var(--primary) !important;
    }
    .feature-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 71, 87, 0.08);
        color: var(--primary);
    }
</style>

<div class="container text-start">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            
            <!-- Welcome Banner -->
            <div class="card premium-gradient-card p-4 p-md-5 mb-5 text-start">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge bg-white bg-opacity-20 text-white mb-3 px-3 py-2 rounded-pill fw-bold text-uppercase" style="backdrop-filter: blur(10px); color: #000000 !important;">Premium Membership</span>
                        <h2 class="fw-bold mb-2">Connect with Your Perfect Life Partner</h2>
                        <p class="opacity-90 mb-0">Select a premium membership plan to get your profile approved instantly, view contact details, and send unlimited connection requests.</p>
                    </div>
                    <div class="col-md-4 text-end d-none d-md-block">
                        <div class="fs-1 text-white opacity-25" style="font-size: 6rem !important;"><i class="fa-solid fa-heart"></i></div>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="row g-4 mb-5 text-start">
                <div class="col-md-4">
                    <div class="glass-card p-4 h-100 d-flex gap-3">
                        <div class="feature-icon flex-shrink-0 mt-1"><i class="fa-solid fa-circle-check"></i></div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Instant Approval</h6>
                            <p class="text-secondary small mb-0">Skip the standard verification queue and get approved instantly by the support team.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card p-4 h-100 d-flex gap-3">
                        <div class="feature-icon flex-shrink-0 mt-1"><i class="fa-solid fa-comments"></i></div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Unlimited Chats</h6>
                            <p class="text-secondary small mb-0">Initiate seamless active conversations and chat with your accepted matrimony matches.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card p-4 h-100 d-flex gap-3">
                        <div class="feature-icon flex-shrink-0 mt-1"><i class="fa-solid fa-users"></i></div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Advanced Filters</h6>
                            <p class="text-secondary small mb-0">Filter candidates dynamically by caste, sub-caste, diet, education level, or coordinates.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plans Grid -->
            <h4 class="fw-bold text-center text-dark mb-4">Choose Your Premium Plan</h4>
            <div class="row g-4 justify-content-center mb-5 text-start">
                @if($plans->isEmpty())
                    <div class="col-12 text-center py-4 bg-light rounded-4 border">
                        <p class="text-muted mb-0">No active plans are currently configured for Matrimony profiles.</p>
                    </div>
                @else
                    @foreach($plans as $plan)
                        <div class="col-md-4">
                            <div class="card plan-card h-100 border-0 p-4 text-center bg-white shadow-sm relative">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-3">{{ $plan->plan_name }}</h5>
                                        <div class="my-4">
                                            <h1 class="fw-extrabold text-primary mb-0 font-monospace" style="color: #84144f !important;">₹{{ number_format($plan->price, 0) }}</h1>
                                            <small class="text-muted">Subscription valid for {{ $plan->duration_years }} Year(s)</small>
                                        </div>
                                        <hr class="opacity-10 my-4">
                                        <ul class="list-unstyled text-start mb-4">
                                            <li class="mb-2.5 small text-secondary"><i class="fa-solid fa-circle-check text-primary me-2" style="color: #84144f !important;"></i> Direct profile highlights</li>
                                            <li class="mb-2.5 small text-secondary"><i class="fa-solid fa-circle-check text-primary me-2" style="color: #84144f !important;"></i> View matches contact phone</li>
                                            <li class="mb-2.5 small text-secondary"><i class="fa-solid fa-circle-check text-primary me-2" style="color: #84144f !important;"></i> Unlimited matches requests</li>
                                            <li class="mb-2.5 small text-secondary"><i class="fa-solid fa-circle-check text-primary me-2" style="color: #84144f !important;"></i> Verified member badge</li>
                                        </ul>
                                    </div>
                                    <button class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm" style="background-color: #84144f !important; border-color: #84144f !important;" onclick="startMatrimonyPayment({{ $plan->id }}, {{ $plan->price }})">
                                        Subscribe Now <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Skip and Proceed Flow -->
            <div class="text-center mt-4">
                <p class="text-secondary small mb-3">Not ready to subscribe yet? You can start with your trial console first.</p>
                <a href="{{ route('matrimony.index') }}" class="btn btn-outline-secondary px-5 py-3 rounded-3 fw-bold border-2" style="color: #84144f; border-color: #84144f !important;">
                    Skip & Go to Dashboard <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
                </a>
            </div>

        </div>
    </div>
</div>

<!-- CCAvenue Checkout Integration -->
<script>
function startMatrimonyPayment(planId, price) {
    fetch("{{ route('matrimony.subscribe') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: JSON.stringify({ plan_id: planId })
    }).then(r => r.json()).then(data => {
        if (!data.success) { alert(data.message || "Failed to create order."); return; }
        redirectToCCAvenue(data);
    }).catch(() => alert("Payment initialization failed."));
}
</script>
@endsection
