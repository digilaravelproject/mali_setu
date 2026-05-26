@extends('layouts.app')

@section('content')
<style>
.matri-stat-card { border-radius: 16px; padding: 20px; border-left: 4px solid var(--primary); background: rgba(255,255,255,0.7); }
.profile-status-badge { padding: 6px 16px; border-radius: 50px; font-size: 0.78rem; font-weight: 700; }
.quick-action-card { border-radius: 16px; padding: 20px; text-align: center; background: rgba(255,255,255,0.7); border: 1px solid rgba(173,20,87,0.08); transition: all 0.3s ease; cursor: pointer; }
.quick-action-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(173,20,87,0.1); }
.photo-thumb { width: 70px; height: 70px; border-radius: 12px; object-fit: cover; }
.photo-thumb-placeholder { width: 70px; height: 70px; border-radius: 12px; background: #fff5f8; border: 1px solid rgba(173, 20, 87, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; }
.plan-card { border-radius: 20px; padding: 28px; background: rgba(255,255,255,0.8); border: 2px solid rgba(173,20,87,0.1); text-align: center; }
.plan-card:hover { border-color: var(--primary); }
.profile-action-btn { border-radius: 12px; font-weight: 700; font-size: 0.82rem; padding: 8px 18px; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 6px; }
.profile-action-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 12px rgba(173, 20, 87, 0.15); }
</style>

<div class="py-4">

    {{-- Session alerts --}}
    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first() }}
        </div>
    @endif

    {{-- Welcome Banner --}}
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge-type mb-3">Matrimony Portal</span>
                <h1 class="fw-bold mb-2">Your Matrimony Dashboard</h1>
                <p class="opacity-75 mb-0">Find your perfect life partner from within the verified community. Browse, connect, and chat — all in one place.</p>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow" style="width:90px;height:90px;font-size:2.5rem;color:var(--primary);">
                    <i class="fa-solid fa-heart"></i>
                </div>
            </div>
        </div>
    </div>

    @if(!$profile)
    {{-- NO PROFILE: CTA --}}
    <div class="glass-card text-center py-5 mb-4">
        <div class="mb-4" style="font-size:4rem;color:var(--primary);"><i class="fa-solid fa-heart-circle-plus"></i></div>
        <h3 class="fw-bold mb-2">Create Your Matrimony Profile</h3>
        <p class="text-secondary mb-4 px-md-5">You haven't set up your matrimony profile yet. Create one to start connecting with verified community members looking for a life partner.</p>
        <a href="{{ route('matrimony.create') }}" class="btn btn-primary btn-lg rounded-3 px-5 fw-bold shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> Create Profile Now
        </a>
    </div>

    @else
    {{-- HAS PROFILE: Stats + Actions --}}

    {{-- Status Bar --}}
    <div class="glass-card mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-1 text-dark">Profile Status</h5>
            @if($profile->approval_status === 'approved')
                <span class="profile-status-badge bg-success text-white"><i class="fa-solid fa-circle-check me-1"></i> Approved & Active</span>
            @elseif($profile->approval_status === 'pending')
                <span class="profile-status-badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> Pending Admin Approval</span>
            @else
                <span class="profile-status-badge bg-danger text-white"><i class="fa-solid fa-ban me-1"></i> Rejected</span>
            @endif
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('matrimony.edit') }}" class="btn btn-outline-primary btn-sm rounded-3 px-3">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Profile
            </a>
            <form action="{{ route('matrimony.delete') }}" method="POST">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm rounded-3 px-3">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="matri-stat-card">
                <div class="text-primary mb-1"><i class="fa-solid fa-paper-plane"></i></div>
                <h6 class="text-secondary small mb-1">Sent Requests</h6>
                <h4 class="fw-bold mb-0">{{ $sentRequests->count() }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="matri-stat-card">
                <div class="text-primary mb-1"><i class="fa-solid fa-inbox"></i></div>
                <h6 class="text-secondary small mb-1">Pending Received</h6>
                <h4 class="fw-bold mb-0">{{ $receivedRequests->count() }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="matri-stat-card">
                <div class="text-primary mb-1"><i class="fa-solid fa-comments"></i></div>
                <h6 class="text-secondary small mb-1">Conversations</h6>
                <h4 class="fw-bold mb-0">{{ $conversations->count() }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="matri-stat-card">
                <div class="text-primary mb-1"><i class="fa-solid fa-crown"></i></div>
                <h6 class="text-secondary small mb-1">Subscription</h6>
                <h4 class="fw-bold mb-0 small">{{ $hasPaid ? 'Active' : 'Free' }}</h4>
            </div>
        </div>
    </div>

    {{-- Profile Summary --}}
    <div class="glass-card mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                @if(!empty($profile->personal_details['photos'][0]))
                    <img src="{{ asset('storage/' . $profile->personal_details['photos'][0]) }}" class="photo-thumb">
                @else
                    <div class="photo-thumb-placeholder">
                        <i class="fa-solid fa-user"></i>
                    </div>
                @endif
            </div>
            <div class="col">
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <div class="d-flex flex-wrap gap-3 text-secondary small mb-2">
                    <span><i class="fa-solid fa-cake-candles me-1 text-primary"></i> {{ $profile->age }} yrs</span>
                    @if($profile->height)
                        <span><i class="fa-solid fa-ruler me-1 text-primary"></i> {{ $profile->height }} ft</span>
                    @endif
                    @if(!empty($profile->location_details['city']) || !empty($profile->location_details['state']))
                        <span><i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ $profile->location_details['city'] ?? '' }}{{ !empty($profile->location_details['city']) && !empty($profile->location_details['state']) ? ', ' : '' }}{{ $profile->location_details['state'] ?? '' }}</span>
                    @endif
                    @if(!empty($profile->education_details['highest_qualification']))
                        <span><i class="fa-solid fa-graduation-cap me-1 text-primary"></i> {{ $profile->education_details['highest_qualification'] }}</span>
                    @endif
                    @if(!empty($profile->professional_details['occupation']))
                        <span><i class="fa-solid fa-briefcase me-1 text-primary"></i> {{ $profile->professional_details['occupation'] }}</span>
                    @endif
                </div>
                <div class="d-flex gap-2 flex-wrap mt-3">
                    <a href="{{ route('matrimony.browse') }}" class="btn btn-primary btn-sm profile-action-btn"><i class="fa-solid fa-magnifying-glass me-1"></i> Browse Profiles</a>
                    <a href="{{ route('matrimony.requests') }}" class="btn btn-outline-primary btn-sm profile-action-btn"><i class="fa-solid fa-paper-plane me-1"></i> Requests</a>
                    <a href="{{ route('matrimony.conversations') }}" class="btn btn-outline-secondary btn-sm profile-action-btn"><i class="fa-solid fa-comments me-1"></i> Messages</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending received requests --}}
    @if($receivedRequests->count() > 0)
    <div class="glass-card mb-4">
        <h5 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-inbox me-2"></i> Pending Connection Requests</h5>
        @foreach($receivedRequests as $req)
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom pb-3 mb-3">
                <div class="d-flex align-items-center gap-3">
                    @php $sp = $req->sender->matrimonyProfile ?? null; @endphp
                    @if($sp && !empty($sp->personal_details['photos'][0]))
                        <img src="{{ asset('storage/' . $sp->personal_details['photos'][0]) }}" style="width:45px;height:45px;border-radius:10px;object-fit:cover;">
                    @else
                        <div class="bg-light text-primary d-flex align-items-center justify-content-center rounded-3" style="width:45px;height:45px;"><i class="fa-solid fa-user"></i></div>
                    @endif
                    <div>
                        <div class="fw-bold text-dark small">{{ $req->sender->name }}</div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ $req->message ?? 'Would like to connect with you.' }}</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('matrimony.request.respond', $req->id) }}" method="POST" class="d-inline">
                        @csrf <input type="hidden" name="status" value="accepted">
                        <button class="btn btn-success btn-sm rounded-3"><i class="fa-solid fa-check me-1"></i> Accept</button>
                    </form>
                    <form action="{{ route('matrimony.request.respond', $req->id) }}" method="POST" class="d-inline">
                        @csrf <input type="hidden" name="status" value="rejected">
                        <button class="btn btn-outline-danger btn-sm rounded-3"><i class="fa-solid fa-times me-1"></i> Reject</button>
                    </form>
                    @if($sp)
                        <a href="{{ route('matrimony.show', $sp->id) }}" class="btn btn-outline-secondary btn-sm rounded-3">View</a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- Subscription Plans (show if not paid) --}}
    @if(!$hasPaid && $plans->count() > 0)
    <div class="glass-card mb-4" id="plans-section">
        <h5 class="fw-bold mb-2 text-primary"><i class="fa-solid fa-crown me-2"></i> Subscribe to a Premium Plan</h5>
        <p class="text-secondary small mb-4">Upgrade to a premium plan to get your profile approved faster, access advanced filters, and send unlimited connection requests.</p>
        <div class="row g-4">
            @foreach($plans as $plan)
            <div class="col-md-4">
                <div class="plan-card">
                    <h5 class="fw-bold mb-3">{{ $plan->plan_name }}</h5>
                    <div class="my-3"><h2 class="fw-bold text-primary mb-0">₹{{ number_format($plan->price, 0) }}</h2>
                    <small class="text-muted">for {{ $plan->duration_years }} year(s)</small></div>
                    <button class="btn btn-primary w-100 py-2 rounded-3 fw-bold" onclick="startMatrimonyPayment({{ $plan->id }}, {{ $plan->price }})">
                        <i class="fa-solid fa-crown me-1"></i> Subscribe Now
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif {{-- end if profile --}}
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function startMatrimonyPayment(planId, price) {
    fetch("{{ route('matrimony.subscribe') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: JSON.stringify({ plan_id: planId })
    }).then(r => r.json()).then(data => {
        if (!data.success) { alert(data.message || "Failed to create order."); return; }
        const rzp = new Razorpay({
            key: data.key_id, amount: data.amount, currency: data.currency,
            name: "Mali Setu Matrimony", description: "Premium Matrimony Plan",
            order_id: data.order_id,
            handler: function(response) {
                fetch("{{ route('matrimony.verify-payment') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    body: JSON.stringify({ ...response, transaction_id: data.transaction_id })
                }).then(r => r.json()).then(v => {
                    if (v.success) { alert("Subscription activated!"); window.location.reload(); }
                    else alert(v.message);
                });
            },
            prefill: { name: "{{ $user->name }}", email: "{{ $user->email }}", contact: "{{ $user->phone }}" },
            theme: { color: "#ad1457" }
        });
        rzp.open();
    }).catch(() => alert("Payment initialization failed."));
}
</script>
@endsection
