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

<div class="container text-start">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            
            <!-- Welcome Alert Banner (Screenshot 3 style) -->
            <div class="text-center p-4 rounded-4 bg-light border-warning border mb-5 shadow-sm" style="border: 2.5px solid #ffb300 !important; background-color: #fffbeb !important;">
                <div class="text-warning mb-3" style="font-size:3.5rem;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <h4 class="fw-bold text-dark mb-2">Business Premium Subscription Required</h4>
                <p class="text-secondary mb-0">Your business <strong>"{{ $business->business_name }}"</strong> is listed but needs an active subscription plan to list products, service suites, and manage job openings. Select a plan below to activate instant access.</p>
            </div>

            <!-- Plans Selector Grid -->
            <h4 class="fw-bold text-center text-dark mb-4">Select Your Subscription Plan</h4>
            <div class="row g-4 justify-content-center mb-5 text-start">
                @if($plans->isEmpty())
                    <div class="col-12 text-center py-4 bg-light rounded-4 border">
                        <p class="text-muted mb-0">No active plans are currently configured for your business type ({{ $business->business_type }}).</p>
                    </div>
                @else
                    @foreach($plans as $plan)
                        <div class="col-md-4">
                            <div class="card h-100 border-0 rounded-4 shadow-sm text-center p-4 relative bg-white border" style="border: 2px solid rgba(13, 148, 136, 0.1) !important;">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-3">{{ $plan->company_type }}</h5>
                                        <div class="my-4">
                                            <h2 class="fw-extrabold mb-0" style="color: #ff4757 !important;">₹{{ number_format($plan->price, 0) }}</h2>
                                            <small class="text-muted">for {{ $plan->duration_years }} year(s)</small>
                                        </div>
                                        <p class="small text-secondary mb-4">{{ $plan->description ?? 'List products, publish active jobs, accept applicants, and get verified.' }}</p>
                                    </div>
                                    <button class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold shadow-sm" style="background-color: #ff4757 !important; border-color: #ff4757 !important;" onclick="startRazorpayPayment({{ $plan->id }}, {{ $plan->price }})">
                                        Select Plan <i class="fa-solid fa-arrow-right ms-1"></i>
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
