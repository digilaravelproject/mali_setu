@extends('layouts.app')

@section('content')
<style>
    /* Mobile Responsive Overrides for Business cards in 2-column view */
    @media (max-width: 576px) {
        .welcome-banner {
            padding: 15px 12px !important;
            margin-bottom: 15px !important;
        }
        .welcome-banner h1 {
            font-size: 1.3rem !important;
        }
        .welcome-banner p {
            font-size: 0.75rem !important;
        }
        .welcome-banner .badge-type {
            font-size: 0.65rem !important;
            padding: 3px 8px !important;
            margin-bottom: 8px !important;
        }
        .glass-card.p-4 {
            padding: 12px !important;
            margin-bottom: 15px !important;
        }
        .glass-card.p-4 form.row.g-3 {
            --bs-gutter-y: 0.5rem !important;
            --bs-gutter-x: 0.5rem !important;
        }
        .glass-card.p-4 label {
            font-size: 0.7rem !important;
            margin-bottom: 2px !important;
        }
        .glass-card.p-4 .form-control, .glass-card.p-4 .form-select, .glass-card.p-4 .input-group-text {
            font-size: 0.78rem !important;
            padding: 6px 10px !important;
        }
        .glass-card.p-4 button, .glass-card.p-4 a {
            font-size: 0.78rem !important;
            padding: 8px 12px !important;
        }
        
        .card.h-100 {
            border-radius: 14px !important;
        }
        .card.h-100 .relative.bg-light {
            height: 110px !important;
        }
        .card.h-100 .card-body {
            padding: 10px !important;
        }
        .card.h-100 h5.fw-bold.text-dark {
            font-size: 0.82rem !important;
            margin-bottom: 2px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .card.h-100 .d-flex.align-items-center.gap-2.mb-3 {
            font-size: 0.65rem !important;
            margin-bottom: 8px !important;
            gap: 2px !important;
            flex-direction: column !important;
            align-items: start !important;
        }
        .card.h-100 .d-flex.align-items-center.gap-2.mb-3 span {
            max-width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .card.h-100 .d-flex.align-items-center.gap-2.mb-3 span.text-muted {
            display: none !important;
        }
        .card.h-100 p.small.text-secondary.mb-4 {
            font-size: 0.68rem !important;
            -webkit-line-clamp: 2 !important;
            height: 2.7em !important;
            margin-bottom: 8px !important;
            line-height: 1.35 !important;
        }
        .card.h-100 .d-flex.justify-content-between.align-items-center.pt-3 {
            padding-top: 8px !important;
            flex-direction: column !important;
            align-items: start !important;
            gap: 8px !important;
        }
        .card.h-100 .d-flex.justify-content-between.align-items-center.pt-3 div {
            width: 100% !important;
        }
        .card.h-100 .d-flex.justify-content-between.align-items-center.pt-3 strong {
            font-size: 0.72rem !important;
        }
        .card.h-100 .d-flex.justify-content-between.align-items-center.pt-3 span {
            font-size: 0.65rem !important;
        }
        .card.h-100 .d-flex.justify-content-between.align-items-center.pt-3 a.btn-primary {
            font-size: 0.72rem !important;
            padding: 6px 10px !important;
            border-radius: 8px !important;
            width: 100% !important;
            text-align: center !important;
        }
        .card.h-100 .position-absolute.m-3 {
            margin: 6px !important;
        }
        .card.h-100 .badge {
            font-size: 0.6rem !important;
            padding: 3px 6px !important;
        }
    }
</style>

<div class="row g-4 text-start">
    <main class="col-12 px-md-4 py-4">
        
        <!-- Directory Header -->
        <div class="welcome-banner mb-4 d-none">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge-type mb-3 bg-white text-black text-teal shadow-sm">Enterprise Finder</span>
                    <h1 class="fw-bold mb-2">Community Business Directory</h1>
                    <p class="opacity-75 mb-0">Browse caste-verified manufacturers, agricultural distributors, retailers, medical practitioners, and service providers.</p>
                </div>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <div class="glass-card p-4 border shadow-sm mb-4 bg-white">
            <form action="{{ route('dashboard.business.browse') }}" method="GET" class="row g-3">
                <div class="col-lg-4 col-md-6 col-12">
                    <label class="form-label small fw-semibold text-secondary">Keyword Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="Search by name, description, city..." value="{{ request('q') }}">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <label class="form-label small fw-semibold text-secondary">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <label class="form-label small fw-semibold text-secondary">Location Filter</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary"><i class="fa-solid fa-location-dot"></i></span>
                        <input type="text" name="location" class="form-control" placeholder="City or State name..." value="{{ request('location') }}">
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-12 d-flex align-items-end">
                    <div class="w-100 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-sliders"></i> Filter</button>
                        @if(request()->anyFilled(['q', 'category_id', 'location']))
                            <a href="{{ route('dashboard.business.browse') }}" class="btn btn-light border py-2.5 rounded-3 text-secondary" title="Clear Filters"><i class="fa-solid fa-times"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Directory Listings Grid -->
        <h5 class="fw-bold mb-3"><i class="fa-solid fa-store me-2 text-primary"></i> Active Business Listings</h5>
        <div class="row g-4">
            @forelse($businesses as $b)
                <div class="col-6 col-md-6 col-xl-4">
                    <div class="card h-100 border-0 rounded-4 shadow-sm relative overflow-hidden bg-white border" style="transition: transform 0.2s; border: 1px solid rgba(0,0,0,0.08) !important;">
                        
                        <!-- Thumbnail/Cover image -->
                        <div class="relative bg-light" style="height: 160px;">
                            @php
                                $photos = $b->photo ? explode(',', $b->photo) : [];
                                $cover = count($photos) > 0 ? trim($photos[0]) : null;
                            @endphp
                            
                            @if($cover)
                                <img src="{{ asset('storage/' . $cover) }}" class="w-100 h-100" style="object-fit: cover;">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, rgba(255,71,87,0.12), rgba(255,71,87,0.06));">
                                    <i class="fa-solid fa-store"></i>
                                </div>
                            @endif

                            <!-- Floating Badges -->
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-dark bg-opacity-75 py-1.5 px-2.5 rounded-pill small">{{ $b->category->name ?? 'Enterprise' }}</span>
                            </div>

                            @if($b->verification_status === 'approved')
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-success py-1.5 px-2.5 rounded-pill small shadow-sm"><i class="fa-solid fa-circle-check me-1"></i> Verified</span>
                                </div>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-4 text-start">
                            <h5 class="fw-bold text-dark mb-1">{{ $b->business_name }}</h5>
                            <div class="d-flex align-items-center gap-2 mb-3 text-secondary small flex-wrap" style="max-width: 100%;">
                                 @php
                                     $fullAddress = trim(($b->address ? $b->address . ', ' : '') . $b->city . ', ' . $b->state . ($b->pincode ? ' - ' . $b->pincode : ''));
                                 @endphp
                                 <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $fullAddress }}" style="cursor: help;">
                                     <i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ Str::limit($fullAddress, 40, '...') }}
                                 </span>
                                 @if(isset($b->distance))
                                     <span class="badge bg-light text-secondary border small text-nowrap"><i class="fa-solid fa-route text-danger me-1"></i> {{ $b->distance }} km away</span>
                                 @endif
                                 <span class="text-muted">•</span>
                                 <span><i class="fa-solid fa-briefcase me-1 text-primary"></i> {{ $b->business_type }}</span>
                             </div>

                            <p class="small text-secondary mb-4" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; height: 4.5em;">
                                {{ $b->description }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                <div>
                                    @php
                                        $avgRating = $b->reviews()->where('status', 'approved')->avg('rating') ?? 0;
                                        $reviewsCount = $b->reviews()->where('status', 'approved')->count();
                                    @endphp
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="text-warning small"><i class="fa-solid fa-star"></i></span>
                                        <strong class="text-dark small">{{ number_format($avgRating, 1) }}</strong>
                                        <span class="text-muted small">({{ $reviewsCount }})</span>
                                    </div>
                                </div>
                                <a href="{{ route('dashboard.business.show', $b->id) }}" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-semibold">
                                    View Profile <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="text-secondary mb-3 fs-1"><i class="fa-solid fa-store-slash"></i></div>
                    <h5 class="fw-bold">No Businesses Found</h5>
                    <p class="text-secondary small mb-0">No active verified business listings matched your query. Try clearing filters.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $businesses->links() }}
        </div>

    </main>
</div>
@endsection
