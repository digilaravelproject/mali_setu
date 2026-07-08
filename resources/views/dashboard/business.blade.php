@extends('layouts.app')

@section('content')
<div class="row g-4">
    <!-- Sidebar / Nav Tab indicator equivalent but on separate route -->
    <main class="col-12 px-md-4 py-4">
        
        <!-- Welcome banner -->
        <div class="welcome-banner mb-4 text-start shadow-sm border border-white border-opacity-10 d-none" style="background: linear-gradient(135deg, #84144f 0%, #aa1262 100%);">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge bg-white bg-opacity-20 text-black mb-3 px-3 py-1.5 rounded-pill fw-bold text-uppercase small"><i class="fa-solid fa-store me-1 text-warning"></i> Enterprise Dashboard</span>
                    <h1 class="fw-extrabold text-white mb-2 fs-2">Manage Your Business Portal</h1>
                    <p class="opacity-90 mb-0 font-medium small" style="line-height:1.6;">List and edit your enterprise, showcase your inventory catalog, advertise premium service packages, and publish active job postings.</p>
                </div>
                <div class="col-md-4 text-md-end mt-4 mt-md-0">
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Photo" class="profile-photo-circle" style="border: 4px solid rgba(255,255,255,0.25); box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                    @else
                        <img src="{{ asset('default-avatar.png') }}" alt="Profile Photo" class="profile-photo-circle" style="width: 110px; height: 110px; object-fit: cover; border: 4px solid rgba(255,255,255,0.25); box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                    @endif
                </div>
            </div>
        </div>

        <!-- Metric Stats Grid -->
        <div class="row g-3 mb-4 text-start">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="glass-card p-4 h-100 d-flex align-items-center gap-3 hover-scale" style="border-left: 5px solid #2563eb; background: #ffffff;">
                    <div class="rounded-4 d-flex align-items-center justify-content-center shrink-0" style="width:50px; height:50px; background: rgba(37,99,235,0.08); color: #2563eb; font-size: 20px;">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary fw-semibold mb-1 small" style="font-size:0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Your Business</h6>
                        <h4 class="fw-extrabold mb-0 text-dark font-monospace" style="font-size:1.6rem;">{{ $stats['businesses_count'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="glass-card p-4 h-100 d-flex align-items-center gap-3 hover-scale" style="border-left: 5px solid #ea580c; background: #ffffff;">
                    <div class="rounded-4 d-flex align-items-center justify-content-center shrink-0" style="width:50px; height:50px; background: rgba(234,88,12,0.08); color: #ea580c; font-size: 20px;">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary fw-semibold mb-1 small" style="font-size:0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Products Listed</h6>
                        <h4 class="fw-extrabold mb-0 text-dark font-monospace" style="font-size:1.6rem;">{{ $stats['products_count'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="glass-card p-4 h-100 d-flex align-items-center gap-3 hover-scale" style="border-left: 5px solid #16a34a; background: #ffffff;">
                    <div class="rounded-4 d-flex align-items-center justify-content-center shrink-0" style="width:50px; height:50px; background: rgba(22,163,74,0.08); color: #16a34a; font-size: 20px;">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary fw-semibold mb-1 small" style="font-size:0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Services Suites</h6>
                        <h4 class="fw-extrabold mb-0 text-dark font-monospace" style="font-size:1.6rem;">{{ $stats['services_count'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="glass-card p-4 h-100 d-flex align-items-center gap-3 hover-scale" style="border-left: 5px solid #0d9488; background: #ffffff;">
                    <div class="rounded-4 d-flex align-items-center justify-content-center shrink-0" style="width:50px; height:50px; background: rgba(13,148,136,0.08); color: #0d9488; font-size: 20px;">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary fw-semibold mb-1 small" style="font-size:0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Jobs Published</h6>
                        <h4 class="fw-extrabold mb-0 text-dark font-monospace" style="font-size:1.6rem;">{{ $stats['jobs_count'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main business view area -->
        <div class="glass-card text-start">
            
            <!-- 1. Business List / Registry Portal View -->
            <div id="business-list-view">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1 text-primary"><i class="fa-solid fa-briefcase me-2"></i> Manage Business</h4>
                        <p class="text-secondary small mb-0">Overview of all registered businesses under your account.</p>
                    </div>
                    @if(!$user->is_business)
                        <button class="btn btn-primary btn-sm rounded-3 cursor-pointer" onclick="toggleBusinessSection('setup')">
                            <i class="fa-solid fa-plus me-1"></i> Register New Business
                        </button>
                    @endif
                </div>

                @if($user->is_business)
                    <div class="table-responsive bg-white rounded-4 shadow-sm border p-2 text-start">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr class="bg-light">
                                    <th class="border-0 rounded-start">Business Name</th>
                                    <th class="border-0">Type & Category</th>
                                    <th class="border-0 text-center">Products</th>
                                    <th class="border-0 text-center">Services</th>
                                    <th class="border-0 text-center">Active Jobs</th>
                                    <th class="border-0 text-center">Subscription</th>
                                    <th class="border-0 rounded-end text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3">
                                                <i class="fa-solid fa-store fs-5"></i>
                                            </div>
                                            <div>
                                                <strong class="text-dark">{{ $user->business->business_name }}</strong>
                                                <div class="text-muted small"><i class="fa-solid fa-location-dot me-1"></i> {{ $user->business->city }}, {{ $user->business->state }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $user->business->business_type }}</span>
                                        <div class="text-muted small mt-1">{{ $user->business->category->name ?? 'Agriculture' }}</div>
                                    </td>
                                    <td class="text-center font-monospace fw-bold text-secondary">
                                        {{ $user->business->products->count() }}
                                    </td>
                                    <td class="text-center font-monospace fw-bold text-secondary">
                                        {{ $user->business->services->count() }}
                                    </td>
                                    <td class="text-center font-monospace fw-bold text-secondary">
                                        {{ count($jobs) }}
                                    </td>
                                    <td class="text-center">
                                         @if($user->business->subscription_status === 'active')
                                             <span class="badge bg-success bg-opacity-10 text-success py-1 px-2.5 rounded-pill"><i class="fa-solid fa-circle-check me-1"></i> Active</span>
                                         @else
                                             <span class="badge bg-warning bg-opacity-10 text-warning py-1 px-2.5 rounded-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i> Trial/Inactive</span>
                                             <div class="mt-1">
                                                 <a href="{{ route('dashboard.business.subscription', ['business_id' => $user->business->id]) }}" class="text-decoration-none small text-primary fw-bold" style="font-size: 0.72rem;">
                                                     <i class="fa-solid fa-arrow-up-right-from-square me-0.5"></i> Get Subscription
                                                 </a>
                                             </div>
                                         @endif
                                     </td>
                                     <td class="text-center">
                                         <div class="d-flex gap-2 justify-content-center">
                                             @if($user->business->subscription_status !== 'active')
                                                 <a href="{{ route('dashboard.business.subscription', ['business_id' => $user->business->id]) }}" class="btn btn-outline-warning btn-sm rounded-3 cursor-pointer" title="Get Subscription">
                                                     <i class="fa-solid fa-credit-card"></i>
                                                 </a>
                                             @endif
                                             <button class="btn btn-outline-success btn-sm rounded-3 cursor-pointer" onclick="toggleBusinessSection('console')" title="View Details">
                                                 <i class="fa-solid fa-eye"></i>
                                             </button>
                                             <button class="btn btn-outline-primary btn-sm rounded-3 cursor-pointer" onclick="toggleBusinessSection('edit')" title="Edit Profile">
                                                 <i class="fa-solid fa-pen-to-square"></i>
                                             </button>
                                             <form action="{{ route('dashboard.business.delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you absolutely sure you want to permanently delete this business? All products, services, jobs, and applicants will be lost forever.');">
                                                 @csrf
                                                 @method('DELETE')
                                                 <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 cursor-pointer" title="Delete Business">
                                                     <i class="fa-solid fa-trash-can"></i>
                                                 </button>
                                             </form>
                                         </div>
                                     </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 bg-light rounded-4 border">
                        <div class="text-secondary mb-3 fs-1"><i class="fa-solid fa-briefcase"></i></div>
                        <h5 class="fw-bold">No Business Registered Yet</h5>
                        <p class="text-secondary small mb-4">Register your business directory today to display products, offer services, and recruit local talent.</p>
                        <button class="btn btn-primary rounded-3 px-4 py-2 cursor-pointer" onclick="toggleBusinessSection('setup')">
                            <i class="fa-solid fa-plus me-1"></i> Register New Business
                        </button>
                    </div>
                @endif
            </div>

            <!-- 2. Business Setup Section -->
            <div id="business-setup-section" style="display: none;">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-light btn-sm rounded-3 cursor-pointer" onclick="toggleBusinessSection('list')">
                            <i class="fa-solid fa-arrow-left"></i> Back
                        </button>
                        <h5 class="fw-bold mb-0 text-dark">Initial Business Setup</h5>
                    </div>
                </div>
                <form id="dashboardBusinessCreateForm" action="{{ route('dashboard.business.register') }}" method="POST" enctype="multipart/form-data" class="text-start">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Business Name *</label>
                            <input type="text" name="business_name" class="form-control" placeholder="E.g. Mali Agri Services" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Business Type *</label>
                            <select id="business_type_select" name="business_type" class="form-select" required>
                                <option value="Proprietary /Partnership - LLP">Proprietary /Partnership - LLP</option>
                                <option value="Private Ltd">Private Ltd</option>
                                <option value="Public Ltd">Public Ltd</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Business Category *</label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" name="contact_email" class="form-control" placeholder="business@example.com">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Phone</label>
                            <input type="text" name="contact_phone" class="form-control" placeholder="Phone number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Website URL</label>
                            <input type="url" name="website" class="form-control" placeholder="https://example.com">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Opening Time</label>
                            <input type="time" name="opening_time" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Closing Time</label>
                            <input type="time" name="closing_time" class="form-control">
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mt-3 mb-3 border-bottom pb-2">Business Address Details</h6>

                    <div class="mb-3">
                        <label class="form-label">Address Line *</label>
                        <input type="text" name="address" class="form-control" placeholder="Building, Street, Landmark" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pincode *</label>
                            <input type="text" name="pincode" class="form-control" placeholder="6-digit pincode" maxlength="6" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country *</label>
                            <input type="text" name="country" value="India" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">State *</label>
                            <input type="text" name="state" class="form-control" placeholder="State" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City *</label>
                            <input type="text" name="city" class="form-control" placeholder="City" required>
                        </div>
                    </div>

                    <div class="row">
                        <input type="hidden" name="district" value="">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Taluka</label>
                            <input type="text" name="taluka" class="form-control" placeholder="Taluka">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Village</label>
                            <input type="text" name="village" class="form-control" placeholder="Village">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Describe your business and services..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Upload Business Photos</label>
                        <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                        <small class="text-muted">You can select multiple files.</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-3 shadow-sm fw-bold cursor-pointer">
                        Create Business Profile <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>

            <!-- 3. Business Console Section -->
            @if($user->is_business)
                <div id="business-console-section" style="display: none;">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-light btn-sm rounded-3 cursor-pointer" onclick="toggleBusinessSection('list')">
                                <i class="fa-solid fa-arrow-left"></i> Back to List
                            </button>
                            <h5 class="fw-bold mb-0 text-dark">Business Management Console</h5>
                        </div>
                    </div>

                    @if($user->business->subscription_status !== 'active')
                        <!-- Subscription Required View -->
                        <div class="text-center p-4 rounded-4 bg-light border-warning border mb-4">
                            <div class="text-warning mb-3" style="font-size:3rem;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                            <h5 class="fw-bold">Business Premium Subscription Required</h5>
                            <p class="text-secondary small mb-4">Your business <strong>"{{ $user->business->business_name }}"</strong> is listed but needs an active subscription plan to list products, service suites, and manage job openings. Select a plan below to activate instant access.</p>
                        </div>

                        <h5 class="fw-bold mb-4 text-center">Select Your Subscription Plan</h5>
                        @php
                            $displayPlans = collect($plans ?? []);
                            if ($user->business && $user->business->business_type) {
                                $businessType = trim(str_replace(' ', '', $user->business->business_type));
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
                                    <div class="card h-100 border-0 rounded-4 shadow-sm text-center p-4 relative" style="background: rgba(255,255,255,0.7); backdrop-filter:blur(5px); border: 2px solid rgba(255, 71, 87, 0.1) !important;">
                                        <div class="card-body">
                                            <h5 class="fw-bold mb-3">{{ $plan->company_type }}</h5>
                                            <div class="my-4">
                                                <h2 class="fw-extrabold text-primary mb-0">₹{{ number_format($plan->price, 0) }}</h2>
                                                <small class="text-muted">for {{ $plan->duration_years }} year(s)</small>
                                            </div>
                                            <p class="small text-secondary mb-4">{{ $plan->description ?? 'List products, publish active jobs, accept applicants, and get verified.' }}</p>
                                            <button class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold shadow-sm cursor-pointer" onclick="startRazorpayPayment({{ $plan->id }}, {{ $plan->price }})">
                                                Select Plan <i class="fa-solid fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>                        <!-- CCAvenue Checkout Integration -->
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
                                        alert(data.message || "Failed to create Order.");
                                        return;
                                    }
                                    redirectToCCAvenue(data);
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
                                <h5 class="fw-bold mb-1">{{ $user->business->business_name }}</h5>
                                <p class="small text-secondary mb-0">Premium Business Profile is active. Subscription valid until: <strong>{{ \Carbon\Carbon::parse($user->business->subscription_expires_at)->format('d M, Y') }}</strong></p>
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
                                    <div class="col-md-6 mb-3"><strong>Business Name:</strong> <span class="text-secondary">{{ $user->business->business_name }}</span></div>
                                    <div class="col-md-6 mb-3"><strong>Business Type:</strong> <span class="text-secondary">{{ $user->business->business_type }}</span></div>
                                    <div class="col-md-6 mb-3"><strong>Category:</strong> <span class="text-secondary">{{ $user->business->category->name ?? 'N/A' }}</span></div>
                                    <div class="col-md-6 mb-3"><strong>Contact Email:</strong> <span class="text-secondary">{{ $user->business->contact_email ?? 'N/A' }}</span></div>
                                    <div class="col-md-6 mb-3"><strong>Contact Phone:</strong> <span class="text-secondary">{{ $user->business->contact_phone ?? 'N/A' }}</span></div>
                                    <div class="col-md-6 mb-3"><strong>Website:</strong> <span class="text-secondary">{{ $user->business->website ?? 'N/A' }}</span></div>
                                    <div class="col-md-6 mb-3"><strong>Timings:</strong> <span class="text-secondary">{{ $user->business->opening_time ?? '09:00' }} - {{ $user->business->closing_time ?? '21:00' }}</span></div>
                                    <div class="col-md-12 mb-3"><strong>Full Address:</strong> <span class="text-secondary">{{ $user->business->address }}, {{ $user->business->city }}, {{ $user->business->district }}, {{ $user->business->state }} - {{ $user->business->pincode }}</span></div>
                                    <div class="col-md-12 mb-3"><strong>Description:</strong> <p class="text-secondary mt-1 small bg-light p-3 rounded-3">{{ $user->business->description }}</p></div>
                                </div>
                                
                                @if($user->business->photo)
                                    <div class="d-flex gap-2 mt-2">
                                        @foreach(explode(',', $user->business->photo) as $img)
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
                                    <button class="btn btn-primary btn-sm rounded-3 cursor-pointer" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fa-solid fa-plus me-1"></i> Add New Product</button>
                                </div>

                                @if($user->business->products && count($user->business->products) > 0)
                                    <div class="table-responsive">
                                        <table class="table align-middle">
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
                                                @foreach($user->business->products as $prod)
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
                                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 cursor-pointer"><i class="fa-solid fa-trash-can"></i></button>
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
                                    <button class="btn btn-primary btn-sm rounded-3 cursor-pointer" data-bs-toggle="modal" data-bs-target="#addServiceModal"><i class="fa-solid fa-plus me-1"></i> Add New Service</button>
                                </div>

                                @if($user->business->services && count($user->business->services) > 0)
                                    <div class="table-responsive">
                                        <table class="table align-middle">
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
                                                @foreach($user->business->services as $serv)
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
                                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 cursor-pointer"><i class="fa-solid fa-trash-can"></i></button>
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
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0">Enterprise Job Postings</h5>
                                    <button class="btn btn-primary btn-sm rounded-3 cursor-pointer" data-bs-toggle="modal" data-bs-target="#addJobModal"><i class="fa-solid fa-plus me-1"></i> Post New Job</button>
                                </div>

                                @if(count($jobs) > 0)
                                    @foreach($jobs as $job)
                                        <div class="card border-0 rounded-4 shadow-sm p-4 mb-4" style="background: rgba(255,255,255,0.7)">
                                            <div class="row align-items-start text-start">
                                                <div class="col text-start">
                                                    <h6 class="fw-bold mb-1">{{ $job->title }}</h6>
                                                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                                        <span class="badge bg-light text-secondary border small">{{ ucfirst($job->employment_type) }}</span>
                                                        <span class="badge bg-light text-secondary border small">{{ ucfirst($job->experience_level) }}</span>
                                                        <span class="badge bg-light text-secondary border small"><i class="fa-solid fa-location-dot me-1"></i> {{ $job->location }}</span>
                                                        <span class="text-primary small fw-semibold">₹{{ $job->salary_range ?? 'Not specified' }}</span>
                                                    </div>
                                                    <p class="small text-secondary mb-3">{{ Str::limit($job->description, 180) }}</p>
                                                    
                                                    <!-- Candidates list -->
                                                    @if(count($job->applications) > 0)
                                                        <h6 class="small fw-bold border-top pt-3 mb-3"><i class="fa-solid fa-users me-1 text-primary"></i> Applicants ({{ count($job->applications) }})</h6>
                                                        <div class="list-group list-group-flush">
                                                            @foreach($job->applications as $app)
                                                                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0 py-2.5">
                                                                    <div class="text-start">
                                                                        <strong class="text-dark small">{{ $app->user->name }}</strong> 
                                                                        <span class="text-secondary small">({{ $app->user->phone }})</span>
                                                                        @if($app->resume_path)
                                                                            <a href="{{ asset('storage/' . $app->resume_path) }}" class="btn btn-link btn-sm p-0 ms-2 text-decoration-none small" target="_blank"><i class="fa-solid fa-file-pdf text-danger"></i> Resume</a>
                                                                        @endif
                                                                        @if($app->employer_notes)
                                                                            <div class="text-muted small italic mt-1">Note: {{ $app->employer_notes }}</div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        @if($app->status === 'pending')
                                                                            <button class="btn btn-success btn-xs py-1 px-2 rounded-2 small cursor-pointer" onclick="openModerateModal({{ $app->id }}, 'accepted')"><i class="fa-solid fa-check"></i> Accept</button>
                                                                            <button class="btn btn-danger btn-xs py-1 px-2 rounded-2 small cursor-pointer" onclick="openModerateModal({{ $app->id }}, 'rejected')"><i class="fa-solid fa-times"></i> Reject</button>
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
                                                        <button class="btn btn-outline-secondary btn-sm rounded-3 cursor-pointer" onclick="openEditJobModal({{ json_encode($job) }})"><i class="fa-solid fa-pen-to-square"></i></button>
                                                        <form action="{{ route('dashboard.business.jobs.toggle', $job->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-info btn-sm rounded-3 cursor-pointer"><i class="fa-solid {{ $job->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i></button>
                                                        </form>
                                                        <form action="{{ route('dashboard.business.jobs.delete', $job->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 cursor-pointer"><i class="fa-solid fa-trash-can"></i></button>
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

                <!-- 4. Business Edit Section -->
                <div id="business-edit-section" style="display: none;">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-light btn-sm rounded-3 cursor-pointer" onclick="toggleBusinessSection('list')">
                                <i class="fa-solid fa-arrow-left"></i> Back to List
                            </button>
                            <h5 class="fw-bold mb-0 text-dark">Edit Business Profile Info</h5>
                        </div>
                    </div>
                    <form id="dashboardBusinessEditForm" action="{{ route('dashboard.business.update') }}" method="POST" enctype="multipart/form-data" class="text-start">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Business Name *</label>
                                <input type="text" name="business_name" class="form-control" value="{{ $user->business->business_name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Business Type *</label>
                                <select id="business_type_select" name="business_type" class="form-select" required>
                                    <option value="Proprietary /Partnership - LLP" {{ $user->business->business_type == 'Proprietary /Partnership - LLP' ? 'selected' : '' }}>Proprietary /Partnership - LLP</option>
                                    <option value="Private Ltd" {{ $user->business->business_type == 'Private Ltd' ? 'selected' : '' }}>Private Ltd</option>
                                    <option value="Public Ltd" {{ $user->business->business_type == 'Public Ltd' ? 'selected' : '' }}>Public Ltd</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Business Category *</label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $user->business->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Email</label>
                                <input type="email" name="contact_email" class="form-control" value="{{ $user->business->contact_email }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Phone</label>
                                <input type="text" name="contact_phone" class="form-control" value="{{ $user->business->contact_phone }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Website URL</label>
                                <input type="url" name="website" class="form-control" value="{{ $user->business->website }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Opening Time</label>
                                <input type="time" name="opening_time" class="form-control" value="{{ $user->business->opening_time }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Closing Time</label>
                                <input type="time" name="closing_time" class="form-control" value="{{ $user->business->closing_time }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address Line *</label>
                            <input type="text" name="address" class="form-control" value="{{ $user->business->address }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pincode *</label>
                                <input type="text" name="pincode" class="form-control" value="{{ $user->business->pincode }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country *</label>
                                <input type="text" name="country" value="{{ $user->business->country ?? 'India' }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">State *</label>
                                <input type="text" name="state" class="form-control" value="{{ $user->business->state }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City *</label>
                                <input type="text" name="city" class="form-control" value="{{ $user->business->city }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <input type="hidden" name="district" value="{{ $user->business->district }}">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Taluka</label>
                                <input type="text" name="taluka" class="form-control" value="{{ $user->business->taluka }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Village</label>
                                <input type="text" name="village" class="form-control" value="{{ $user->business->village }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description *</label>
                            <textarea name="description" class="form-control" rows="3" required>{{ $user->business->description }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Update Business Photos</label>
                            <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted">This will replace current images.</small>
                        </div>

                        @if($user->business->photo)
                            <div class="d-flex gap-2 mb-4">
                                @foreach(explode(',', $user->business->photo) as $img)
                                    @if(trim($img))
                                        <img src="{{ asset('storage/' . trim($img)) }}" class="rounded shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 cursor-pointer">Update Profile Info</button>
                    </form>
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
                    @if($user->is_business)
                        <input type="hidden" name="business_id" value="{{ $user->business->id }}">
                    @endif
                    <div class="mb-3 text-start">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="E.g. High Quality Seeds">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Cost (in INR) *</label>
                        <input type="number" name="cost" class="form-control" min="0" step="0.01" required placeholder="E.g. 299">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Product specifications and details..."></textarea>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
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
                    @if($user->is_business)
                        <input type="hidden" name="business_id" value="{{ $user->business->id }}">
                    @endif
                    <div class="mb-3 text-start">
                        <label class="form-label">Service Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="E.g. Drip Irrigation Installation">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Service Charge / Cost *</label>
                        <input type="number" name="cost" class="form-control" min="0" step="0.01" required placeholder="E.g. 1500">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Service timeline and information..."></textarea>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Service Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
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
<div class="modal fade" id="addJobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">Post a New Job Opening</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.business.jobs.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if($user->is_business)
                        <input type="hidden" name="business_id" value="{{ $user->business->id }}">
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Job Title *</label>
                            <input type="text" name="title" class="form-control" required placeholder="E.g. Senior Agronomist">
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Location *</label>
                            <input type="text" name="location" class="form-control" required placeholder="E.g. Pune, Maharashtra">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Employment Type *</label>
                            <select name="employment_type" class="form-select" required>
                                <option value="full-time">Full-Time</option>
                                <option value="part-time">Part-Time</option>
                                <option value="contract">Contract</option>
                                <option value="freelance">Freelance</option>
                                <option value="internship">Internship</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Experience Level *</label>
                            <select name="experience_level" class="form-select" required>
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
                            <input type="text" name="category" class="form-control" required placeholder="E.g. Agriculture, Retail">
                        </div>
                        <div class="col-md-6 mb-3 text-start">
                            <label class="form-label">Salary Range</label>
                            <input type="text" name="salary_range" class="form-control" placeholder="E.g. 40k - 50k / Month">
                        </div>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Job Description *</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Day-to-day duties and goals..."></textarea>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Requirements *</label>
                        <textarea name="requirements" class="form-control" rows="3" required placeholder="Skills, certifications, and qualification criteria..."></textarea>
                    </div>
                    <input type="hidden" name="job_type" value="Full-Time">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Publish Job Opening</button>
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
    // Toggle sub-sections of business management
    function toggleBusinessSection(section) {
        document.getElementById('business-list-view').style.display = 'none';
        document.getElementById('business-setup-section').style.display = 'none';
        
        const consoleSection = document.getElementById('business-console-section');
        const editSection = document.getElementById('business-edit-section');
        
        if (consoleSection) consoleSection.style.display = 'none';
        if (editSection) editSection.style.display = 'none';

        if (section === 'list') {
            document.getElementById('business-list-view').style.display = 'block';
        } else if (section === 'setup') {
            document.getElementById('business-setup-section').style.display = 'block';
        } else if (section === 'console' && consoleSection) {
            consoleSection.style.display = 'block';
        } else if (section === 'edit' && editSection) {
            editSection.style.display = 'block';
        }
    }

    // Modal job actions
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
        form.action = `/dashboard/business/jobs/update/${job.id}`;
        
        const modal = new bootstrap.Modal(document.getElementById('editJobModal'));
        modal.show();
    }

    function openModerateModal(appId, status) {
        document.getElementById('moderate_status').value = status;
        document.getElementById('moderate_status_text').innerText = status.toUpperCase();
        
        const form = document.getElementById('moderateForm');
        form.action = `/dashboard/business/applications/moderate/${appId}`;
        
        const modal = new bootstrap.Modal(document.getElementById('moderateModal'));
        modal.show();
    }

    // Indian Pincode Auto-lookup and Auto-population
    document.addEventListener('DOMContentLoaded', () => {
        // Toggle view on startup if parameters dictate
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('register') || @json(!$user->is_business)) {
            toggleBusinessSection('setup');
            
            // Set category if supplied in URL query
            if (urlParams.has('category_id')) {
                const categorySelect = document.querySelector('select[name="category_id"]');
                if (categorySelect) {
                    categorySelect.value = urlParams.get('category_id');
                }
            }
        } else if (@json($user->is_business)) {
            toggleBusinessSection('console');
        }

        // Pincode lookups inside Business forms (both setup & edit address fields)
        document.querySelectorAll('input[name="pincode"]').forEach(input => {
            input.addEventListener('input', function() {
                const pincode = this.value.trim();
                if (pincode.length === 6 && /^\d+$/.test(pincode)) {
                    const form = this.closest('form');
                    this.classList.add('is-valid');
                    
                    fetch(`https://api.postalpincode.in/pincode/${pincode}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data && data[0] && data[0].Status === 'Success') {
                                const postOffice = data[0].PostOffice[0];
                                const state = postOffice.State;
                                const city = postOffice.District; 
                                
                                const stateInput = form.querySelector('input[name="state"]');
                                const cityInput = form.querySelector('input[name="city"]');
                                const districtInput = form.querySelector('input[name="district"]');
                                
                                if (stateInput) {
                                    stateInput.value = state;
                                    stateInput.classList.add('is-valid');
                                }
                                if (cityInput) {
                                    cityInput.value = city;
                                    cityInput.classList.add('is-valid');
                                }
                                if (districtInput) {
                                    districtInput.value = city;
                                    districtInput.classList.add('is-valid');
                                }
                            }
                        })
                        .catch(err => console.error('Error auto-populating pincode info:', err));
                }
            });
        });
    });
</script>
@endsection
