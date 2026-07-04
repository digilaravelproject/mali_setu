@extends('layouts.app')

@section('content')
<div class="row g-4">
    <main class="col-12 px-md-4 py-4">
        
        <!-- Welcome banner -->
        <div class="welcome-banner mb-4 d-none">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge-type mb-3">Enterprise Dashboard</span>
                    <h1 class="fw-bold mb-2">Manage Your Business Portal</h1>
                    <p class="opacity-75 mb-0">List and edit your enterprise, showcase your inventory catalog, advertise premium service packages, and publish active job postings.</p>
                </div>
                <div class="col-md-4 text-md-end mt-4 mt-md-0">
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Photo" class="profile-photo-circle">
                    @else
                        <img src="{{ asset('default-avatar.png') }}" alt="Profile Photo" class="profile-photo-circle" style="width: 110px; height: 110px; object-fit: cover;">
                    @endif
                </div>
            </div>
        </div>

        <!-- Metric Stats Grid -->
        <div class="row g-3 mb-4 text-start">
            <div class="col-md col-sm-6 col-12">
                <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid var(--primary);">
                    <div class="metric-icon mx-auto text-primary mb-2" style="width:36px; height:36px; line-height:36px; font-size: 14px;"><i class="fa-solid fa-briefcase"></i></div>
                    <h6 class="text-secondary fw-semibold mb-1 small">Your Business</h6>
                    <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $stats['businesses_count'] }}</h4>
                </div>
            </div>
            <div class="col-md col-sm-6 col-12">
                <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid var(--primary);">
                    <div class="metric-icon mx-auto text-primary mb-2" style="width:36px; height:36px; line-height:36px; font-size: 14px;"><i class="fa-solid fa-box-open"></i></div>
                    <h6 class="text-secondary fw-semibold mb-1 small">Products Listed</h6>
                    <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $stats['products_count'] }}</h4>
                </div>
            </div>
            <div class="col-md col-sm-6 col-12">
                <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid var(--primary);">
                    <div class="metric-icon mx-auto text-primary mb-2" style="width:36px; height:36px; line-height:36px; font-size: 14px;"><i class="fa-solid fa-gears"></i></div>
                    <h6 class="text-secondary fw-semibold mb-1 small">Services Suites</h6>
                    <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $stats['services_count'] }}</h4>
                </div>
            </div>
            <div class="col-md col-sm-6 col-12">
                <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid var(--primary);">
                    <div class="metric-icon mx-auto text-primary mb-2" style="width:36px; height:36px; line-height:36px; font-size: 14px;"><i class="fa-solid fa-user-tie"></i></div>
                    <h6 class="text-secondary fw-semibold mb-1 small">Jobs Published</h6>
                    <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $stats['jobs_count'] }}</h4>
                </div>
            </div>
        </div>

        <!-- Main business view area -->
        <div class="glass-card text-start">
            
            <!-- 1. Business List View -->
            <div id="business-list-view">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1 text-primary"><i class="fa-solid fa-briefcase me-2"></i> Manage Business</h4>
                        <p class="text-secondary small mb-0">Overview of all registered businesses under your account.</p>
                    </div>
                    <a href="{{ route('dashboard.business.create') }}" class="btn btn-primary btn-sm rounded-3">
                        <i class="fa-solid fa-plus me-1"></i> Register New Business
                    </a>
                </div>

                @if($businesses->isNotEmpty())
                    <div class="table-responsive bg-white rounded-4 shadow-sm border p-2 text-start">
                        <table class="table align-middle mb-0" style="min-width: 900px;">
                            <thead>
                                <tr class="bg-light">
                                    <th class="border-0 rounded-start">Business Name</th>
                                    <th class="border-0">Type & Category</th>
                                    <th class="border-0 text-center">Products</th>
                                    <th class="border-0 text-center">Services</th>
                                    <th class="border-0 text-center">Active Jobs</th>
                                    <th class="border-0 text-center">Subscription</th>
                                    <th class="border-0 text-center"> Verification Status</th>
                                    <th class="border-0 rounded-end text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($businesses as $biz)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3">
                                                    <i class="fa-solid fa-store fs-5"></i>
                                                </div>
                                                <div>
                                                    <strong class="text-dark">{{ $biz->business_name }}</strong>
                                                    <div class="text-muted small"><i class="fa-solid fa-location-dot me-1"></i> {{ $biz->city }}, {{ $biz->state }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $biz->business_type }}</span>
                                            <div class="text-muted small mt-1">{{ $biz->category->name ?? 'Agriculture' }}</div>
                                        </td>
                                        <td class="text-center font-monospace fw-bold text-secondary">
                                            {{ $biz->products->count() }}
                                        </td>
                                        <td class="text-center font-monospace fw-bold text-secondary">
                                            {{ $biz->services->count() }}
                                        </td>
                                        <td class="text-center font-monospace fw-bold text-secondary">
                                            {{ $biz->jobPostings ? $biz->jobPostings->count() : 0 }}
                                        </td>
                                        <td class="text-center">
                                            @if($biz->subscription_status === 'active')
                                                <span class="badge bg-success bg-opacity-10 text-success py-1 px-2.5 rounded-pill"><i class="fa-solid fa-circle-check me-1"></i> Active</span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning py-1 px-2.5 rounded-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i> Trial/Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($biz->verification_status === 'approved')
                                                <span class="badge bg-success bg-opacity-10 text-success py-1 px-2.5 rounded-pill"><i class="fa-solid fa-circle-check me-1"></i> Approved</span>
                                            @elseif($biz->verification_status === 'rejected')
                                                <span class="badge bg-danger bg-opacity-10 text-danger py-1 px-2.5 rounded-pill"><i class="fa-solid fa-xmark me-1"></i> Rejected</span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning py-1 px-2.5 rounded-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i> Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <a href="{{ route('dashboard.business.index', ['business_id' => $biz->id]) }}" class="btn btn-outline-success btn-sm rounded-3 @if($activeBusiness && $activeBusiness->id == $biz->id) active bg-success text-white @endif">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="{{ route('dashboard.business.edit', ['business_id' => $biz->id]) }}" class="btn btn-outline-primary btn-sm rounded-3">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <form action="{{ route('dashboard.business.delete') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="business_id" value="{{ $biz->id }}">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 bg-light rounded-4 border">
                        <div class="text-secondary mb-3 fs-1"><i class="fa-solid fa-briefcase"></i></div>
                        <h5 class="fw-bold">No Business Registered Yet</h5>
                        <p class="text-secondary small mb-4">Register your business directory today to display products, offer services, and recruit local talent.</p>
                        <a href="{{ route('dashboard.business.create') }}" class="btn btn-primary rounded-3 px-4 py-2">
                            <i class="fa-solid fa-plus me-1"></i> Register New Business
                        </a>
                    </div>
                @endif
            </div>

            <!-- 2. Business Console Section -->
            @if($user->is_business && $activeBusiness)
                <div id="business-console-section" style="display: none;">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-light btn-sm rounded-3" onclick="toggleBusinessSection('list')">
                                <i class="fa-solid fa-arrow-left"></i> Back to List
                            </button>
                            <h5 class="fw-bold mb-0 text-dark">Business Console: <span class="text-primary">{{ $activeBusiness->business_name }}</span></h5>
                        </div>
                        <div>
                            <a href="{{ route('dashboard.business.show', $activeBusiness->id) }}" class="btn btn-outline-primary btn-sm rounded-3" target="_blank">
                                <i class="fa-solid fa-square-arrow-up-right me-1"></i> View Public Profile
                            </a>
                        </div>
                    </div>

                    @if($activeBusiness->subscription_status !== 'active')
                        <!-- Subscription Required View -->
                        <div class="text-center p-4 rounded-4 bg-light border-warning border mb-4">
                            <div class="text-warning mb-3" style="font-size:3rem;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                            <h5 class="fw-bold">Business Premium Subscription Required</h5>
                            <p class="text-secondary small mb-4">Your business <strong>"{{ $activeBusiness->business_name }}"</strong> is listed but needs an active subscription plan to list products, service suites, and manage job openings. Select a plan below to activate instant access.</p>
                        </div>

                        <h5 class="fw-bold mb-4 text-center">Select Your Subscription Plan</h5>
                        @php
                            $displayPlans = collect($plans ?? []);
                            if ($activeBusiness && $activeBusiness->business_type) {
                                $businessType = trim(str_replace(' ', '', $activeBusiness->business_type));
                                $displayPlans = $displayPlans->filter(function($p) use ($businessType) {
                                    $planType = trim(str_replace(' ', '', $p->company_type ?? ''));
                                    return $planType === $businessType;
                                });
                            } elseif (request()->has('type') && request('type')) {
                                $displayPlans = $displayPlans->filter(function($p) { return ($p->company_type ?? null) == request('type'); });
                            }
                        @endphp

                        <div class="row g-4 justify-content-center">
                            @if($displayPlans->isEmpty())
                                <div class="col-12 text-center">
                                    <p class="text-muted">No plans available for the selected business type.</p>
                                </div>
                            @endif
                            @foreach($displayPlans as $plan)
                                <div class="col-md-4">
                                    <div class="card h-100 border-0 rounded-4 shadow-sm text-center p-4 relative bg-white border" style="border: 2px solid rgba(13, 148, 136, 0.1) !important;">
                                        <div class="card-body">
                                            <h5 class="fw-bold mb-3">{{ $plan->company_type }}</h5>
                                            <div class="my-4">
                                                <h2 class="fw-extrabold text-primary mb-0">₹{{ number_format($plan->price, 0) }}</h2>
                                                <small class="text-muted">for {{ $plan->duration_years }} year(s)</small>
                                            </div>
                                            <p class="small text-secondary mb-4">{{ $plan->description ?? 'List products, publish active jobs, accept applicants, and get verified.' }}</p>
                                            <button class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold shadow-sm" onclick="startRazorpayPayment({{ $plan->id }}, {{ $plan->price }})">
                                                Select Plan <i class="fa-solid fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Razorpay SDK & Loader/Modal JS script -->
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
                                                    window.location.reload();
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

                    @else
                        <!-- Active Premium Console Content -->
                        <div class="row align-items-center mb-4 p-3 rounded-4 bg-success bg-opacity-10 mx-0 text-start">
                            <div class="col-auto">
                                <div class="bg-success text-white p-3 rounded-3"><i class="fa-solid fa-store fs-3"></i></div>
                            </div>
                            <div class="col text-start">
                                <h5 class="fw-bold mb-1">{{ $activeBusiness->business_name }}</h5>
                                <p class="small text-secondary mb-0">Premium Business Profile is active. Subscription valid until: <strong>{{ \Carbon\Carbon::parse($activeBusiness->subscription_expires_at)->format('d M, Y') }}</strong></p>
                            </div>
                            <div class="col-auto text-end">
                                <span class="badge bg-success py-2 px-3"><i class="fa-solid fa-circle-check me-1"></i> Active Premium</span>
                            </div>
                        </div>

                        <!-- Inner Navigation Tabs -->
                        <ul class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded-3" id="business-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#inner-profile" type="button" role="tab"><i class="fa-solid fa-address-card me-1"></i> Profile Info</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#inner-products" type="button" role="tab"><i class="fa-solid fa-box me-1"></i> Products</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#inner-services" type="button" role="tab"><i class="fa-solid fa-server me-1"></i> Services</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#inner-jobs" type="button" role="tab"><i class="fa-solid fa-briefcase me-1"></i> Jobs Hub</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="business-tabs-content">
                            <!-- Inner Tab 1: Profile -->
                            <div class="tab-pane fade show active p-2" id="inner-profile" role="tabpanel">
                                <div class="alert alert-info py-2 px-3 small rounded-3 mb-3 text-start"><i class="fa-solid fa-circle-info me-1"></i> This profile information is displayed in the public local directories.</div>
                                
                                <div class="row text-start">
                                    <div class="col-md-6 mb-3"><strong>Business Name:</strong> <span class="text-secondary">{{ $activeBusiness->business_name }}</span></div>
                                    <div class="col-md-6 mb-3"><strong>Business Type:</strong> <span class="text-secondary">{{ $activeBusiness->business_type }}</span></div>
                                    <div class="col-md-6 mb-3"><strong>Category:</strong> <span class="text-secondary">{{ $activeBusiness->category->name ?? 'N/A' }}</span></div>
                                    <div class="col-md-6 mb-3"><strong>Contact Email:</strong> <span class="text-secondary">{{ $activeBusiness->contact_email ?? 'N/A' }}</span></div>
                                    <div class="col-md-6 mb-3"><strong>Contact Phone:</strong> <span class="text-secondary">{{ $activeBusiness->contact_phone ?? 'N/A' }}</span></div>
                                    <div class="col-md-6 mb-3"><strong>Website:</strong> <span class="text-secondary">{{ $activeBusiness->website ?? 'N/A' }}</span></div>
                                    <div class="col-md-6 mb-3"><strong>Timings:</strong> <span class="text-secondary">{{ $activeBusiness->opening_time ?? '09:00' }} - {{ $activeBusiness->closing_time ?? '21:00' }}</span></div>
                                    <div class="col-md-12 mb-3"><strong>Full Address:</strong> <span class="text-secondary">{{ $activeBusiness->address }}, {{ $activeBusiness->city }}, {{ $activeBusiness->district }}, {{ $activeBusiness->state }} - {{ $activeBusiness->pincode }}</span></div>
                                    <div class="col-md-12 mb-3"><strong>Description:</strong> <p class="text-secondary mt-1 small bg-light p-3 rounded-3">{{ $activeBusiness->description }}</p></div>
                                </div>
                                
                                @if($activeBusiness->photo)
                                    <div class="d-flex gap-2 mt-2 flex-wrap">
                                        @foreach(explode(',', $activeBusiness->photo) as $img)
                                            @if(trim($img))
                                                <img src="{{ asset('storage/' . trim($img)) }}" class="rounded shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Inner Tab 2: Products -->
                            <div class="tab-pane fade p-2 text-start" id="inner-products" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0">Active Products Catalog</h5>
                                    <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fa-solid fa-plus me-1"></i> Add New Product</button>
                                </div>

                                @if($activeBusiness->products && count($activeBusiness->products) > 0)
                                    <div class="table-responsive">
                                        <table class="table align-middle" style="min-width: 600px;">
                                            <thead>
                                                <tr>
                                                    <th>Image</th>
                                                    <th>Product Name</th>
                                                    <th>Cost</th>
                                                    <th>Description</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($activeBusiness->products as $prod)
                                                    <tr>
                                                        <td>
                                                            @if($prod->image_path)
                                                                <img src="{{ asset('storage/' . $prod->image_path) }}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                                            @else
                                                                <div class="bg-light text-secondary rounded shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="fa-solid fa-box"></i></div>
                                                            @endif
                                                        </td>
                                                        <td><strong class="text-dark">{{ $prod->name }}</strong></td>
                                                        <td><span class="text-primary fw-bold">₹{{ $prod->cost ?? '0.00' }}</span></td>
                                                        <td><span class="text-secondary small">{{ Str::limit($prod->description, 50) }}</span></td>
                                                        <td>
                                                            <form action="{{ route('dashboard.business.products.delete', $prod->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-3"><i class="fa-solid fa-trash-can"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5 bg-light rounded-4 w-100">
                                        <p class="text-secondary small mb-0">No products added yet. Click "Add New Product" to populate your catalog.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Inner Tab 3: Services -->
                            <div class="tab-pane fade p-2 text-start" id="inner-services" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0">Active Services Suite</h5>
                                    <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#addServiceModal"><i class="fa-solid fa-plus me-1"></i> Add New Service</button>
                                </div>

                                @if($activeBusiness->services && count($activeBusiness->services) > 0)
                                    <div class="table-responsive">
                                        <table class="table align-middle" style="min-width: 600px;">
                                            <thead>
                                                <tr>
                                                    <th>Image</th>
                                                    <th>Service Name</th>
                                                    <th>Rate / Cost</th>
                                                    <th>Description</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($activeBusiness->services as $serv)
                                                    <tr>
                                                        <td>
                                                            @if($serv->image_path)
                                                                <img src="{{ asset('storage/' . $serv->image_path) }}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                                            @else
                                                                <div class="bg-light text-secondary rounded shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="fa-solid fa-server"></i></div>
                                                            @endif
                                                        </td>
                                                        <td><strong class="text-dark">{{ $serv->name }}</strong></td>
                                                        <td><span class="text-primary fw-bold">₹{{ $serv->cost ?? '0.00' }}</span></td>
                                                        <td><span class="text-secondary small">{{ Str::limit($serv->description, 50) }}</span></td>
                                                        <td>
                                                            <form action="{{ route('dashboard.business.services.delete', $serv->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-3"><i class="fa-solid fa-trash-can"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5 bg-light rounded-4 w-100">
                                        <p class="text-secondary small mb-0">No services added yet. Click "Add New Service" to list your offerings.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Inner Tab 4: Jobs Hub -->
                            <div class="tab-pane fade p-2 text-start" id="inner-jobs" role="tabpanel">
                                <!-- Job Analytics Dashboard Component -->
                                @if($jobAnalytics)
                                    <div class="row g-3 mb-4 text-start">
                                        <!-- 1. Stats Row -->
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                                            <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid #3b82f6; border-radius: 12px; margin-bottom: 0;">
                                                <div class="mx-auto text-primary mb-2" style="width:36px; height:36px; display: flex; align-items: center; justify-content: center; background: rgba(59, 130, 246, 0.1); border-radius: 50%; font-size: 14px;"><i class="fa-solid fa-briefcase"></i></div>
                                                <h6 class="text-secondary fw-semibold mb-1 small" style="font-size: 0.75rem;">Total Jobs</h6>
                                                <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $jobAnalytics['total_jobs'] }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                                            <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid #10b981; border-radius: 12px; margin-bottom: 0;">
                                                <div class="mx-auto text-success mb-2" style="width:36px; height:36px; display: flex; align-items: center; justify-content: center; background: rgba(16, 185, 129, 0.1); border-radius: 50%; font-size: 14px;"><i class="fa-solid fa-circle-check"></i></div>
                                                <h6 class="text-secondary fw-semibold mb-1 small" style="font-size: 0.75rem;">Active Jobs</h6>
                                                <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $jobAnalytics['active_jobs'] }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                                            <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid #f59e0b; border-radius: 12px; margin-bottom: 0;">
                                                <div class="mx-auto text-warning mb-2" style="width:36px; height:36px; display: flex; align-items: center; justify-content: center; background: rgba(245, 158, 11, 0.1); border-radius: 50%; font-size: 14px;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                                                <h6 class="text-secondary fw-semibold mb-1 small" style="font-size: 0.75rem;">Pending Jobs</h6>
                                                <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $jobAnalytics['pending_jobs'] }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                                            <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid #8b5cf6; border-radius: 12px; margin-bottom: 0;">
                                                <div class="mx-auto mb-2" style="width:36px; height:36px; display: flex; align-items: center; justify-content: center; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; border-radius: 50%; font-size: 14px;"><i class="fa-solid fa-users"></i></div>
                                                <h6 class="text-secondary fw-semibold mb-1 small" style="font-size: 0.75rem;">Applications</h6>
                                                <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $jobAnalytics['total_applications'] }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                                            <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid #ec4899; border-radius: 12px; margin-bottom: 0;">
                                                <div class="mx-auto mb-2" style="width:36px; height:36px; display: flex; align-items: center; justify-content: center; background: rgba(236, 72, 153, 0.1); color: #ec4899; border-radius: 50%; font-size: 14px;"><i class="fa-solid fa-hourglass-half"></i></div>
                                                <h6 class="text-secondary fw-semibold mb-1 small" style="font-size: 0.75rem;">Pending App</h6>
                                                <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $jobAnalytics['pending_applications'] }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                                            <div class="glass-card text-center p-3 h-100 d-flex flex-column justify-content-center" style="border-left: 4px solid #14b8a6; border-radius: 12px; margin-bottom: 0;">
                                                <div class="mx-auto mb-2" style="width:36px; height:36px; display: flex; align-items: center; justify-content: center; background: rgba(20, 184, 166, 0.1); color: #14b8a6; border-radius: 50%; font-size: 14px;"><i class="fa-solid fa-circle-check"></i></div>
                                                <h6 class="text-secondary fw-semibold mb-1 small" style="font-size: 0.75rem;">Accepted App</h6>
                                                <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $jobAnalytics['accepted_applications'] }}</h4>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. Application Progress Distribution & Recent applications -->
                                    <div class="row g-4 mb-4">
                                        <div class="col-md-5 col-12 text-start">
                                            <div class="glass-card p-4 h-100 d-flex flex-column border" style="border-radius: 16px; margin-bottom: 0;">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <h6 class="fw-bold text-dark mb-0">Application Progress</h6>
                                                    <span class="badge bg-light text-primary border px-2.5 py-1">Lifetime</span>
                                                </div>
                                                
                                                @php
                                                    $successRate = 0;
                                                    if ($jobAnalytics['total_applications'] > 0) {
                                                        $successRate = round(($jobAnalytics['accepted_applications'] / $jobAnalytics['total_applications']) * 100);
                                                    }
                                                @endphp
                                                <div class="d-flex align-items-center justify-content-center gap-4 my-auto flex-wrap">
                                                    <!-- Circle chart -->
                                                    <div class="position-relative d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px; background: radial-gradient(closest-side, white 79%, transparent 80% 100%), conic-gradient(var(--primary) {{ $successRate }}%, #f1f5f9 0);">
                                                        <span class="fw-extrabold text-primary font-monospace fs-4">{{ $successRate }}%</span>
                                                        <span class="text-secondary small position-absolute" style="bottom: 22px; font-size: 0.65rem;">Success</span>
                                                    </div>
                                                    <!-- Legend -->
                                                    <div class="d-flex flex-column gap-2 text-start">
                                                        <div class="d-flex align-items-center gap-2 small">
                                                            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#10b981;"></span>
                                                            <strong class="text-dark">Accepted:</strong>
                                                            <span class="text-secondary">{{ $jobAnalytics['accepted_applications'] }}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 small">
                                                            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#f59e0b;"></span>
                                                            <strong class="text-dark">Pending:</strong>
                                                            <span class="text-secondary">{{ $jobAnalytics['pending_applications'] }}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 small">
                                                            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#3b82f6;"></span>
                                                            <strong class="text-dark">Total:</strong>
                                                            <span class="text-secondary">{{ $jobAnalytics['total_applications'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-7 col-12 text-start">
                                            <div class="glass-card p-4 h-100 border" style="border-radius: 16px; margin-bottom: 0;">
                                                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-users-viewfinder text-primary me-1"></i> Recent Applications</h6>
                                                <hr class="mt-0 mb-3 opacity-10">

                                                @if(count($jobAnalytics['recent_applications']) > 0)
                                                    <div class="d-flex flex-column gap-2.5">
                                                        @foreach($jobAnalytics['recent_applications'] as $app)
                                                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border-start border-3 border-primary">
                                                                <div class="text-start">
                                                                    <strong class="text-dark small" style="font-size: 0.85rem;">{{ $app->user->name }}</strong>
                                                                    <div class="text-muted small" style="font-size: 0.75rem;">Applied for: <span class="fw-semibold text-secondary">{{ $app->jobPosting->title }}</span></div>
                                                                </div>
                                                                <div>
                                                                    <span class="badge {{ $app->status === 'accepted' ? 'bg-success' : ($app->status === 'rejected' ? 'bg-danger' : 'bg-warning') }} small">{{ ucfirst($app->status) }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-center py-4 text-muted small">
                                                        <i class="fa-solid fa-users-slash opacity-50 fs-4 mb-2 d-block"></i> No recent applications found
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0">Enterprise Job Postings</h5>
                                    <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#addJobModal"><i class="fa-solid fa-plus me-1"></i> Post New Job</button>
                                </div>

                                @if(count($jobs) > 0)
                                    @foreach($jobs as $job)
                                        <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white border">
                                            <div class="row align-items-start text-start">
                                                <div class="col text-start">
                                                    <h6 class="fw-bold mb-1 text-dark">{{ $job->title }}</h6>
                                                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                                        <span class="badge bg-light text-secondary border small">{{ ucfirst($job->employment_type) }}</span>
                                                        <span class="badge bg-light text-secondary border small">{{ ucfirst($job->experience_level) }}</span>
                                                        <span class="badge bg-light text-secondary border small"><i class="fa-solid fa-location-dot me-1"></i> {{ $job->location }}</span>
                                                        <span class="text-primary small fw-semibold">₹{{ $job->salary_range ?? 'Not specified' }}</span>
                                                    </div>
                                                    <p class="small text-secondary mb-3">{{ Str::limit($job->description, 180) }}</p>
                                                    
                                                    <!-- Candidates list -->
                                                    @if(count($job->applications) > 0)
                                                        <h6 class="small fw-bold border-top pt-3 mb-3 text-dark"><i class="fa-solid fa-users me-1 text-primary"></i> Applicants ({{ count($job->applications) }})</h6>
                                                        <div class="list-group list-group-flush">
                                                            @foreach($job->applications as $app)
                                                                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0 py-2.5">
                                                                    <div class="text-start">
                                                                        <strong class="text-dark small">{{ $app->user->name }}</strong> 
                                                                        <span class="text-secondary small">({{ $app->user->phone }})</span>
                                                                        @if($app->resume_path)
                                                                            <a href="{{ asset('storage/' . $app->resume_path) }}" class="btn btn-link btn-sm p-0 ms-2 text-decoration-none small text-danger" target="_blank"><i class="fa-solid fa-file-pdf"></i> Resume</a>
                                                                        @endif
                                                                        @if($app->employer_notes)
                                                                            <div class="text-muted small italic mt-1">Note: {{ $app->employer_notes }}</div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        @if($app->status === 'pending')
                                                                            <button class="btn btn-success btn-xs py-1 px-2 rounded-2 small" onclick="openModerateModal({{ $app->id }}, 'accepted')"><i class="fa-solid fa-check"></i> Accept</button>
                                                                            <button class="btn btn-danger btn-xs py-1 px-2 rounded-2 small" onclick="openModerateModal({{ $app->id }}, 'rejected')"><i class="fa-solid fa-times"></i> Reject</button>
                                                                        @else
                                                                            <span class="badge {{ $app->status === 'accepted' ? 'bg-success' : 'bg-danger' }} small">{{ ucfirst($app->status) }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="small text-muted border-top pt-3"><i class="fa-solid fa-users-slash me-1"></i> No candidate applications received yet.</div>
                                                    @endif
                                                </div>
                                                <div class="col-auto text-end">
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-outline-secondary btn-sm rounded-3" onclick="openEditJobModal({{ json_encode($job) }})"><i class="fa-solid fa-pen-to-square"></i></button>
                                                        <form action="{{ route('dashboard.business.jobs.toggle', $job->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-info btn-sm rounded-3"><i class="fa-solid {{ $job->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i></button>
                                                        </form>
                                                        <form action="{{ route('dashboard.business.jobs.delete', $job->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-3"><i class="fa-solid fa-trash-can"></i></button>
                                                        </form>
                                                    </div>
                                                    <div class="mt-3">
                                                        @if($job->is_active)
                                                            <span class="badge bg-success small"><i class="fa-solid fa-circle-check"></i> Active</span>
                                                        @else
                                                            <span class="badge bg-secondary small">Inactive</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-5 bg-light rounded-4 w-100">
                                        <p class="text-secondary small mb-0">No job postings created yet. Click "Post New Job" to recruit talent.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </main>
</div>

<!-- Modal: Add Product -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.business.products.add') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if($user->is_business && $activeBusiness)
                        <input type="hidden" name="business_id" value="{{ $activeBusiness->id }}">
                    @endif
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold small">Product Name *</label>
                        <input type="text" name="name" class="form-control @error('name', 'product') is-invalid @enderror" required placeholder="E.g. High Quality Seeds" value="{{ old('name') }}">
                        @error('name', 'product')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold small">Cost (in INR) *</label>
                        <input type="number" name="cost" class="form-control @error('cost', 'product') is-invalid @enderror" min="0" step="0.01" required placeholder="E.g. 299" value="{{ old('cost') }}">
                        @error('cost', 'product')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold small">Description *</label>
                        <textarea name="description" class="form-control @error('description', 'product') is-invalid @enderror" rows="3" required placeholder="Product specifications and details...">{{ old('description') }}</textarea>
                        @error('description', 'product')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold small">Product Image *</label>
                        <input type="file" name="image" class="form-control @error('image', 'product') is-invalid @enderror" accept="image/*" required>
                        @error('image', 'product')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Service -->
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">Add New Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.business.services.add') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if($user->is_business && $activeBusiness)
                        <input type="hidden" name="business_id" value="{{ $activeBusiness->id }}">
                    @endif
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold small">Service Name *</label>
                        <input type="text" name="name" class="form-control @error('name', 'service') is-invalid @enderror" required placeholder="E.g. Drip Irrigation Installation" value="{{ old('name') }}">
                        @error('name', 'service')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold small">Service Charge / Cost *</label>
                        <input type="number" name="cost" class="form-control @error('cost', 'service') is-invalid @enderror" min="0" step="0.01" required placeholder="E.g. 1500" value="{{ old('cost') }}">
                        @error('cost', 'service')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold small">Description *</label>
                        <textarea name="description" class="form-control @error('description', 'service') is-invalid @enderror" rows="3" required placeholder="Service timeline and information...">{{ old('description') }}</textarea>
                        @error('description', 'service')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold small">Service Image *</label>
                        <input type="file" name="image" class="form-control @error('image', 'service') is-invalid @enderror" accept="image/*" required>
                        @error('image', 'service')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Job -->
<!-- Modal: Add Job -->
<div class="modal fade" id="addJobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0 pb-0">
                <div class="text-start">
                    <h5 class="fw-bold mb-1" style="color: var(--primary);">Create Job Posting</h5>
                    <p class="text-secondary small mb-0">Job Details / Fill in the details to create your job posting</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.business.jobs.add') }}" method="POST" id="addJobForm">
                @csrf
                <div class="modal-body py-3" style="max-height: 70vh; overflow-y: auto;">
                    @if($user->is_business && $activeBusiness)
                        <input type="hidden" name="business_id" value="{{ $activeBusiness->id }}">
                    @endif

                    <!-- BASIC INFORMATION -->
                    <h6 class="fw-bold mb-3 pb-1 border-bottom" style="color: var(--primary); font-size: 0.95rem; letter-spacing: 0.5px;">BASIC INFORMATION</h6>
                    
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold">Job Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title', 'job') is-invalid @enderror" placeholder="Job title" value="{{ old('title') }}">
                        @error('title', 'job')
                            <div class="text-danger small mt-1">Please enter job title</div>
                        @else
                            @if($errors->job->any() && empty(old('title')))
                                <div class="text-danger small mt-1">Please enter job title</div>
                            @endif
                        @enderror
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold">Job Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description', 'job') is-invalid @enderror" rows="4" placeholder="Describe the role, responsibility">{{ old('description') }}</textarea>
                        @error('description', 'job')
                            <div class="text-danger small mt-1">Please enter job description</div>
                        @else
                            @if($errors->job->any() && empty(old('description')))
                                <div class="text-danger small mt-1">Please enter job description</div>
                            @endif
                        @enderror
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold">Requirements <span class="text-danger">*</span></label>
                        <textarea name="requirements" class="form-control @error('requirements', 'job') is-invalid @enderror" rows="4" placeholder="List the required skills, qualifications">{{ old('requirements') }}</textarea>
                        @error('requirements', 'job')
                            <div class="text-danger small mt-1">Please enter requirements</div>
                        @else
                            @if($errors->job->any() && empty(old('requirements')))
                                <div class="text-danger small mt-1">Please enter requirements</div>
                            @endif
                        @enderror
                    </div>

                    <!-- JOB DETAILS -->
                    <h6 class="fw-bold mt-4 mb-3 pb-1 border-bottom" style="color: var(--primary); font-size: 0.95rem; letter-spacing: 0.5px;">JOB DETAILS</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label fw-semibold">Salary Range <span class="text-danger">*</span></label>
                            <input type="text" name="salary_range" class="form-control @error('salary_range', 'job') is-invalid @enderror" placeholder="₹5,000 - ₹50,000 per month" value="{{ old('salary_range') }}">
                            @error('salary_range', 'job')
                                <div class="text-danger small mt-1">Salary range is required</div>
                            @else
                                @if($errors->job->any() && empty(old('salary_range')))
                                    <div class="text-danger small mt-1">Salary range is required</div>
                                @endif
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label fw-semibold">Job Type <span class="text-danger">*</span></label>
                            <select name="job_type" class="form-select @error('job_type', 'job') is-invalid @enderror">
                                <option value="">Enter Your Job type</option>
                                <option value="Full-Time" {{ old('job_type') == 'Full-Time' ? 'selected' : '' }}>Full-Time</option>
                                <option value="Part-Time" {{ old('job_type') == 'Part-Time' ? 'selected' : '' }}>Part-Time</option>
                                <option value="Contract" {{ old('job_type') == 'Contract' ? 'selected' : '' }}>Contract</option>
                                <option value="Freelance" {{ old('job_type') == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                                <option value="Internship" {{ old('job_type') == 'Internship' ? 'selected' : '' }}>Internship</option>
                            </select>
                            @error('job_type', 'job')
                                <div class="text-danger small mt-1">Please select job type</div>
                            @else
                                @if($errors->job->any() && empty(old('job_type')))
                                    <div class="text-danger small mt-1">Please select job type</div>
                                @endif
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control @error('location', 'job') is-invalid @enderror" placeholder="Enter Your Location" value="{{ old('location') }}">
                            @error('location', 'job')
                                <div class="text-danger small mt-1">Location is required</div>
                            @else
                                @if($errors->job->any() && empty(old('location')))
                                    <div class="text-danger small mt-1">Location is required</div>
                                @endif
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label fw-semibold">Level <span class="text-danger">*</span></label>
                            <select name="experience_level" class="form-select @error('experience_level', 'job') is-invalid @enderror">
                                <option value="">Enter Your Level</option>
                                <option value="entry" {{ old('experience_level') == 'entry' ? 'selected' : '' }}>Entry Level</option>
                                <option value="junior" {{ old('experience_level') == 'junior' ? 'selected' : '' }}>Junior</option>
                                <option value="mid" {{ old('experience_level') == 'mid' ? 'selected' : '' }}>Mid Level</option>
                                <option value="senior" {{ old('experience_level') == 'senior' ? 'selected' : '' }}>Senior</option>
                                <option value="executive" {{ old('experience_level') == 'executive' ? 'selected' : '' }}>Executive</option>
                            </select>
                            @error('experience_level', 'job')
                                <div class="text-danger small mt-1">Please select experience level</div>
                            @else
                                @if($errors->job->any() && empty(old('experience_level')))
                                    <div class="text-danger small mt-1">Please select experience level</div>
                                @endif
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label fw-semibold">Employment Type <span class="text-danger">*</span></label>
                            <select name="employment_type" class="form-select @error('employment_type', 'job') is-invalid @enderror">
                                <option value="">Enter Your Employment type</option>
                                <option value="full-time" {{ old('employment_type') == 'full-time' ? 'selected' : '' }}>Full-Time</option>
                                <option value="part-time" {{ old('employment_type') == 'part-time' ? 'selected' : '' }}>Part-Time</option>
                                <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                                <option value="freelance" {{ old('employment_type') == 'freelance' ? 'selected' : '' }}>Freelance</option>
                                <option value="internship" {{ old('employment_type') == 'internship' ? 'selected' : '' }}>Internship</option>
                            </select>
                            @error('employment_type', 'job')
                                <div class="text-danger small mt-1">Please select employment type</div>
                            @else
                                @if($errors->job->any() && empty(old('employment_type')))
                                    <div class="text-danger small mt-1">Please select employment type</div>
                                @endif
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label fw-semibold">Job Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category', 'job') is-invalid @enderror">
                                <option value="">Enter Your Job category</option>
                                <option value="Agriculture" {{ old('category') == 'Agriculture' ? 'selected' : '' }}>Agriculture</option>
                                <option value="Technology" {{ old('category') == 'Technology' ? 'selected' : '' }}>Technology</option>
                                <option value="Retail & Trade" {{ old('category') == 'Retail & Trade' ? 'selected' : '' }}>Retail & Trade</option>
                                <option value="Services" {{ old('category') == 'Services' ? 'selected' : '' }}>Services</option>
                                <option value="Manufacturing" {{ old('category') == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                <option value="Education" {{ old('category') == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Healthcare" {{ old('category') == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                <option value="Construction" {{ old('category') == 'Construction' ? 'selected' : '' }}>Construction</option>
                                <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category', 'job')
                                <div class="text-danger small mt-1">Please select job category</div>
                            @else
                                @if($errors->job->any() && empty(old('category')))
                                    <div class="text-danger small mt-1">Please select job category</div>
                                @endif
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label fw-semibold">Application Deadline <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                <input type="date" name="application_deadline" class="form-control @error('application_deadline', 'job') is-invalid @enderror" value="{{ old('application_deadline') }}">
                            </div>
                            @error('application_deadline', 'job')
                                <div class="text-danger small mt-1">Please enter Application Deadline</div>
                            @else
                                @if($errors->job->any() && empty(old('application_deadline')))
                                    <div class="text-danger small mt-1">Please enter Application Deadline</div>
                                @endif
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label fw-semibold">Job Expiry Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                <input type="date" name="expires_at" class="form-control @error('expires_at', 'job') is-invalid @enderror" value="{{ old('expires_at') }}">
                            </div>
                            @error('expires_at', 'job')
                                <div class="text-danger small mt-1">Please enter Job Expiry Date</div>
                            @else
                                @if($errors->job->any() && empty(old('expires_at')))
                                    <div class="text-danger small mt-1">Please enter Job Expiry Date</div>
                                @endif
                            @enderror
                        </div>
                    </div>

                    <!-- BENEFITS & PERKS -->
                    <h6 class="fw-bold mt-4 mb-3 pb-1 border-bottom" style="color: var(--primary); font-size: 0.95rem; letter-spacing: 0.5px;">Benefits & Perks</h6>

                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold">Add Benefit</label>
                        <div class="d-flex gap-2">
                            <input type="text" id="add-benefit-input" class="form-control" placeholder="e.g., Health insurance">
                            <button type="button" class="btn btn-primary" onclick="addCustomBenefit()"><i class="fa-solid fa-plus"></i></button>
                        </div>
                        
                        <!-- Added Benefits Pills -->
                        <div class="d-flex flex-wrap gap-2 mt-3" id="added-benefits-pills">
                            <!-- Populated by JavaScript -->
                        </div>
                        <div id="hidden-benefits-inputs"></div>
                    </div>

                    <div class="mb-4 text-start">
                        <label class="form-label fw-semibold text-secondary small mb-2">Quick Add Benefits:</label>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $quickBenefits = ['Health insurance', 'Flexible working hours', 'Paid time off', 'Remote work options', 'Professional development', 'Stock options', 'Performance Bonus', 'Gym Membership', 'Free Meals'];
                            @endphp
                            @foreach($quickBenefits as $qb)
                                <button type="button" class="btn btn-sm py-1.5 px-3 rounded-pill" style="background: rgba(255, 71, 87, 0.05); color: var(--primary); border: 1px solid rgba(255,71,87,0.15); font-size: 0.82rem; font-weight: 600;" onclick="selectQuickBenefit('{{ addslashes($qb) }}')">
                                    {{ $qb }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- SKILLS REQUIRED -->
                    <h6 class="fw-bold mt-4 mb-3 pb-1 border-bottom" style="color: var(--primary); font-size: 0.95rem; letter-spacing: 0.5px;">Skills Required</h6>

                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold">Add Skill</label>
                        <div class="d-flex gap-2">
                            <input type="text" id="add-skill-input" class="form-control" placeholder="e.g., Teaching">
                            <button type="button" class="btn btn-primary" onclick="addCustomSkill()"><i class="fa-solid fa-plus"></i></button>
                        </div>
                        
                        <!-- Added Skills Pills -->
                        <div class="d-flex flex-wrap gap-2 mt-3" id="added-skills-pills">
                            <!-- Populated by JavaScript -->
                        </div>
                        <div id="hidden-skills-inputs"></div>
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold text-secondary small mb-2">Quick Add Skills:</label>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $quickSkills = ['PHP', 'Laravel', 'MySQL', 'REST API', 'Flutter', 'Dart', 'Android', 'React Native', 'Java', 'Python', 'Node.js', 'UI/UX Design'];
                            @endphp
                            @foreach($quickSkills as $qs)
                                <button type="button" class="btn btn-sm py-1.5 px-3 rounded-pill" style="background: rgba(255, 71, 87, 0.05); color: var(--primary); border: 1px solid rgba(255,71,87,0.15); font-size: 0.82rem; font-weight: 600;" onclick="selectQuickSkill('{{ addslashes($qs) }}')">
                                    {{ $qs }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold" style="background-color: var(--primary) !important;">Post Job Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Job -->
<div class="modal fade" id="editJobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">Update Job Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editJobForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Job Title *</label>
                            <input type="text" name="title" id="edit_job_title" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Location *</label>
                            <input type="text" name="location" id="edit_job_location" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Employment Type *</label>
                            <select name="employment_type" id="edit_job_employment_type" class="form-select" required>
                                <option value="full-time">Full-Time</option>
                                <option value="part-time">Part-Time</option>
                                <option value="contract">Contract</option>
                                <option value="freelance">Freelance</option>
                                <option value="internship">Internship</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Experience Level *</label>
                            <select name="experience_level" id="edit_job_experience_level" class="form-select" required>
                                <option value="entry">Entry Level</option>
                                <option value="junior">Junior</option>
                                <option value="mid">Mid Level</option>
                                <option value="senior">Senior</option>
                                <option value="executive">Executive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Job Sector / Category *</label>
                            <input type="text" name="category" id="edit_job_category" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Salary Range</label>
                            <input type="text" name="salary_range" id="edit_job_salary_range" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Job Description *</label>
                        <textarea name="description" id="edit_job_description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Requirements *</label>
                        <textarea name="requirements" id="edit_job_requirements" class="form-control" rows="3" required></textarea>
                    </div>
                    <input type="hidden" name="job_type" value="Full-Time">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Moderate Candidate Applicant Status -->
<div class="modal fade" id="moderateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">Moderate Job Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="moderateForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="status" id="moderate_status">
                    <p class="text-secondary small mb-3 text-start">You are updating the applicant's status to: <strong class="text-primary" id="moderate_status_text"></strong>. You can optionally add notes or comments for the candidate.</p>
                    <div class="mb-3 text-start">
                        <label class="form-label">Employer Notes / Feedback</label>
                        <textarea name="employer_notes" class="form-control" rows="4" placeholder="E.g. We loved your interview! We will send the onboarding details shortly."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm & Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleBusinessSection(section) {
        document.getElementById('business-list-view').style.display = 'none';
        
        const consoleSection = document.getElementById('business-console-section');
        if (consoleSection) consoleSection.style.display = 'none';

        if (section === 'list') {
            document.getElementById('business-list-view').style.display = 'block';
        } else if (section === 'console' && consoleSection) {
            consoleSection.style.display = 'block';
        }
    }

    function openEditJobModal(job) {
        document.getElementById('edit_job_title').value = job.title;
        document.getElementById('edit_job_location').value = job.location;
        document.getElementById('edit_job_employment_type').value = job.employment_type;
        document.getElementById('edit_job_experience_level').value = job.experience_level;
        document.getElementById('edit_job_category').value = job.category;
        document.getElementById('edit_job_salary_range').value = job.salary_range;
        document.getElementById('edit_job_description').value = job.description;
        document.getElementById('edit_job_requirements').value = job.requirements;
        
        const form = document.getElementById('editJobForm');
        form.action = `/dashboard/business/jobs/${job.id}/update`;
        
        const modal = new bootstrap.Modal(document.getElementById('editJobModal'));
        modal.show();
    }

    function openModerateModal(appId, status) {
        document.getElementById('moderate_status').value = status;
        document.getElementById('moderate_status_text').innerText = status.toUpperCase();
        
        const form = document.getElementById('moderateForm');
        form.action = `/dashboard/business/applications/${appId}/status`;
        
        const modal = new bootstrap.Modal(document.getElementById('moderateModal'));
        modal.show();
    }

    // Perks / Benefits Arrays Management
    let selectedBenefits = [];
    
    function renderBenefits() {
        const container = document.getElementById('added-benefits-pills');
        const inputsContainer = document.getElementById('hidden-benefits-inputs');
        container.innerHTML = '';
        inputsContainer.innerHTML = '';
        
        selectedBenefits.forEach((benefit, idx) => {
            // Pill element
            const pill = document.createElement('span');
            pill.className = 'badge rounded-pill d-inline-flex align-items-center gap-2 py-1.5 px-3';
            pill.style.background = 'var(--primary)';
            pill.style.color = '#fff';
            pill.style.fontSize = '0.82rem';
            pill.style.fontWeight = '600';
            pill.innerHTML = `${benefit} <i class="fa-solid fa-circle-xmark text-white-50 cursor-pointer" onclick="removeBenefit(${idx})"></i>`;
            container.appendChild(pill);
            
            // Hidden input for submission
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'benefits[]';
            input.value = benefit;
            inputsContainer.appendChild(input);
        });
    }
    
    function addCustomBenefit() {
        const input = document.getElementById('add-benefit-input');
        const val = input.value.trim();
        if (val && !selectedBenefits.includes(val)) {
            selectedBenefits.push(val);
            input.value = '';
            renderBenefits();
        }
    }
    
    function selectQuickBenefit(benefit) {
        if (!selectedBenefits.includes(benefit)) {
            selectedBenefits.push(benefit);
            renderBenefits();
        }
    }
    
    function removeBenefit(index) {
        selectedBenefits.splice(index, 1);
        renderBenefits();
    }

    // Skills Arrays Management
    let selectedSkills = [];
    
    function renderSkills() {
        const container = document.getElementById('added-skills-pills');
        const inputsContainer = document.getElementById('hidden-skills-inputs');
        container.innerHTML = '';
        inputsContainer.innerHTML = '';
        
        selectedSkills.forEach((skill, idx) => {
            // Pill element
            const pill = document.createElement('span');
            pill.className = 'badge rounded-pill d-inline-flex align-items-center gap-2 py-1.5 px-3';
            pill.style.background = 'var(--primary)';
            pill.style.color = '#fff';
            pill.style.fontSize = '0.82rem';
            pill.style.fontWeight = '600';
            pill.innerHTML = `${skill} <i class="fa-solid fa-circle-xmark text-white-50 cursor-pointer" onclick="removeSkill(${idx})"></i>`;
            container.appendChild(pill);
            
            // Hidden input for submission
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'skills_required[]';
            input.value = skill;
            inputsContainer.appendChild(input);
        });
    }
    
    function addCustomSkill() {
        const input = document.getElementById('add-skill-input');
        const val = input.value.trim();
        if (val && !selectedSkills.includes(val)) {
            selectedSkills.push(val);
            input.value = '';
            renderSkills();
        }
    }
    
    function selectQuickSkill(skill) {
        if (!selectedSkills.includes(skill)) {
            selectedSkills.push(skill);
            renderSkills();
        }
    }
    
    function removeSkill(index) {
        selectedSkills.splice(index, 1);
        renderSkills();
    }

    document.addEventListener('DOMContentLoaded', () => {
        @if(request()->filled('business_id') && $user->is_business)
            toggleBusinessSection('console');
        @else
            toggleBusinessSection('list');
        @endif

        // Named error bags auto reopen modal
        @if($errors->product->any())
            toggleBusinessSection('console');
            // Switch to products tab
            var triggerProd = document.querySelector('#products-tab');
            if (triggerProd) {
                var tabProd = new bootstrap.Tab(triggerProd);
                tabProd.show();
            }
            var productModal = new bootstrap.Modal(document.getElementById('addProductModal'));
            productModal.show();
        @endif

        @if($errors->service->any())
            toggleBusinessSection('console');
            // Switch to services tab
            var triggerServ = document.querySelector('#services-tab');
            if (triggerServ) {
                var tabServ = new bootstrap.Tab(triggerServ);
                tabServ.show();
            }
            var serviceModal = new bootstrap.Modal(document.getElementById('addServiceModal'));
            serviceModal.show();
        @endif

        @if($errors->job->any())
            toggleBusinessSection('console');
            // Switch to jobs tab
            var triggerJobs = document.querySelector('#jobs-tab');
            if (triggerJobs) {
                var tabJobs = new bootstrap.Tab(triggerJobs);
                tabJobs.show();
            }
            var addJobModal = new bootstrap.Modal(document.getElementById('addJobModal'));
            addJobModal.show();
        @endif
    });
</script>
@endsection
