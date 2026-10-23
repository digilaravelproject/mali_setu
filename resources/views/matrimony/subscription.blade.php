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
            <div class="card premium-gradient-card p-4 p-md-5 mb-5 text-start d-none">
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

            @if($user->matrimonyProfile)
                @php
                    $profile = $user->matrimonyProfile;
                    $pd = $profile->personal_details ?? [];
                    $fd = $profile->family_details ?? [];
                    $ed = $profile->education_details ?? [];
                    $pro = $profile->professional_details ?? [];
                    $ld = $profile->location_details ?? [];
                    
                    $profilePhoto = !empty($pd['photos'][0]) ? asset('storage/' . $pd['photos'][0]) : asset('default-avatar.png');
                @endphp
                
                <!-- Profile Details Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-5" style="background: #fff; border: 1px solid rgba(132, 20, 79, 0.1) !important;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-auto text-center mb-3 mb-md-0">
                                <img src="{{ $profilePhoto }}" alt="Profile Photo" class="rounded-4" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #84144f;">
                            </div>
                            <div class="col-md">
                                <span class="badge bg-secondary mb-2 text-uppercase" style="background-color: #84144f !important; font-size: 0.75rem;">Your Matrimony Profile</span>
                                <h3 class="fw-bold text-dark mb-1">{{ $pd['name'] ?? $user->name }}</h3>
                                <p class="text-secondary mb-2">
                                    {{ $profile->age ?? 'N/A' }} Yrs • {{ ucfirst($profile->gender ?? $pd['gender'] ?? 'N/A') }} • {{ $profile->height ?? 'N/A' }} • {{ $profile->weight ?? 'N/A' }}
                                </p>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="badge rounded-pill px-3 py-1.5 small text-dark" style="background-color: rgba(132, 20, 79, 0.08); border: 1px solid rgba(132, 20, 79, 0.15);">
                                        <strong>Caste:</strong> {{ $pd['caste'] ?? 'N/A' }} ({{ $pd['sub_caste'] ?? 'N/A' }})
                                    </span>
                                    <span class="badge rounded-pill px-3 py-1.5 small text-dark" style="background-color: rgba(132, 20, 79, 0.08); border: 1px solid rgba(132, 20, 79, 0.15);">
                                        <strong>Location:</strong> {{ $ld['city'] ?? 'N/A' }}, {{ $ld['state'] ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-auto text-md-end mt-3 mt-md-0">
                                <a href="{{ route('matrimony.edit') }}" class="btn btn-outline-primary rounded-3 btn-sm" style="color: #84144f; border-color: #84144f;">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Profile
                                </a>
                            </div>
                        </div>
                        
                        <hr class="my-4 opacity-10">
                        
                        <div class="row g-3">
                            <div class="col-sm-6 col-md-3">
                                <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.75rem;">Education</div>
                                <div class="text-dark fw-semibold">{{ $ed['highest_qualification'] ?? 'N/A' }}</div>
                                <div class="text-secondary small">{{ $ed['college_name'] ?? '' }}</div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.75rem;">Profession</div>
                                <div class="text-dark fw-semibold">{{ $pro['occupation'] ?? 'N/A' }}</div>
                                <div class="text-secondary small">{{ $pro['employment_type'] ?? '' }}</div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.75rem;">Annual Income</div>
                                <div class="text-dark fw-semibold">{{ $pro['annual_income'] ?? 'N/A' }}</div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.75rem;">Profile Status</div>
                                <div>
                                    @if($profile->approval_status === 'approved')
                                        <span class="badge bg-success rounded-pill px-3 py-1"><i class="fa-solid fa-check-circle me-1"></i> Verified</span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="fa-solid fa-clock me-1"></i> Pending Approval</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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

            <!-- Skip and Proceed Flow -->
            <div class="text-center mt-4">
                <p class="text-secondary small mb-3">Not ready to subscribe yet? You can start with your trial console first.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-5 py-3 rounded-3 fw-bold border-2" style="color: #84144f; border-color: #84144f !important;">
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
