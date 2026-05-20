@extends('layouts.app')

@section('content')
<style>
.profile-card { border-radius: 20px; background: rgba(255,255,255,0.8); border: 1px solid rgba(173,20,87,0.08); overflow: hidden; transition: all 0.3s ease; }
.profile-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px rgba(173,20,87,0.1); }
.profile-photo { width: 100%; height: 160px; object-fit: cover; background: linear-gradient(135deg, #f9d6e3, #fce4ec); }
.profile-photo-placeholder { width: 100%; height: 160px; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #fff; background: linear-gradient(135deg, rgba(173,20,87,0.18), rgba(173,20,87,0.10)); }
.conn-badge { font-size: 0.72rem; padding: 3px 10px; border-radius: 50px; font-weight: 700; }
.filter-pill { border-radius: 50px; font-size: 0.85rem; }
</style>

<div class="py-4">
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold mb-2">Browse Matrimony Profiles</h1>
                <p class="opacity-75 mb-0">Discover verified community members looking for a life partner.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('matrimony.index') }}" class="btn btn-light btn-sm rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> My Profile
                </a>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="glass-card mb-4">
        <form method="GET" action="{{ route('matrimony.browse') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold text-secondary">Gender</label>
                    <select name="gender" class="form-select form-select-sm rounded-3">
                        <option value="">Any</option>
                        <option value="male" {{ request('gender')==='male'?'selected':'' }}>Male</option>
                        <option value="female" {{ request('gender')==='female'?'selected':'' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold text-secondary">Age Min</label>
                    <input type="number" name="age_min" class="form-control form-control-sm rounded-3" min="18" max="100" value="{{ request('age_min') }}" placeholder="18">
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold text-secondary">Age Max</label>
                    <input type="number" name="age_max" class="form-control form-control-sm rounded-3" min="18" max="100" value="{{ request('age_max') }}" placeholder="60">
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold text-secondary">Religion</label>
                    <input type="text" name="religion" class="form-control form-control-sm rounded-3" value="{{ request('religion') }}" placeholder="Any">
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold text-secondary">State</label>
                    <input type="text" name="state" class="form-control form-control-sm rounded-3" value="{{ request('state') }}" placeholder="Any State">
                </div>
                <div class="col-md-2 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3 fw-bold flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('matrimony.browse') }}" class="btn btn-light btn-sm rounded-3 px-3">Clear</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Results --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-secondary mb-0">
            <i class="fa-solid fa-users me-1 text-primary"></i> {{ $profiles->total() }} Profiles Found
        </h6>
        <span class="text-muted small">Page {{ $profiles->currentPage() }} of {{ $profiles->lastPage() }}</span>
    </div>

    @if($profiles->count() === 0)
        <div class="glass-card text-center py-5">
            <div class="text-muted mb-3" style="font-size:3rem;"><i class="fa-solid fa-search"></i></div>
            <h5 class="fw-bold">No Profiles Found</h5>
            <p class="text-secondary">Try adjusting your filters to see more results.</p>
            <a href="{{ route('matrimony.browse') }}" class="btn btn-outline-primary rounded-3 px-4">Clear Filters</a>
        </div>
    @else
    <div class="row g-4 mb-4">
        @foreach($profiles as $profile)
        @php
            $pd = $profile->personal_details ?? [];
            $ld = $profile->location_details ?? [];
            $ed = $profile->education_details ?? [];
            $pro = $profile->professional_details ?? [];
        @endphp
        <div class="col-md-4 col-sm-6">
            <div class="profile-card">
                {{-- Photo --}}
                @if(!empty($pd['photos'][0]))
                    <img src="{{ asset('storage/' . $pd['photos'][0]) }}" class="profile-photo">
                @else
                    <div class="profile-photo-placeholder">
                        <i class="fa-solid fa-{{ ($pd['gender']??'') === 'female' ? 'female' : 'male' }}"></i>
                    </div>
                @endif

                <div class="p-3">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <h6 class="fw-bold mb-0">{{ $profile->user->name ?? 'Profile ' . $profile->id }}</h6>
                        @if($profile->my_connection_status === 'accepted')
                            <span class="conn-badge bg-success text-white">Connected</span>
                        @elseif($profile->my_connection_status === 'pending')
                            <span class="conn-badge bg-warning text-dark">Pending</span>
                        @endif
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-2" style="font-size:0.78rem;color:#6c757d;">
                        <span><i class="fa-solid fa-cake-candles text-primary me-1"></i>{{ $profile->age }}y</span>
                        <span><i class="fa-solid fa-ruler text-primary me-1"></i>{{ $profile->height ?? 'N/A' }}</span>
                        <span><i class="fa-solid fa-location-dot text-primary me-1"></i>{{ $ld['city'] ?? $ld['state'] ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-3" style="font-size:0.78rem;color:#6c757d;">
                        <span><i class="fa-solid fa-graduation-cap text-primary me-1"></i>{{ $ed['highest_qualification'] ?? 'N/A' }}</span>
                        <span><i class="fa-solid fa-briefcase text-primary me-1"></i>{{ $pro['occupation'] ?? 'N/A' }}</span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('matrimony.show', $profile->id) }}" class="btn btn-primary btn-sm rounded-3 flex-grow-1 fw-bold">
                            View Profile
                        </a>
                        @if($profile->my_connection_status === 'none')
                            <form action="{{ route('matrimony.request.send') }}" method="POST">
                                @csrf
                                <input type="hidden" name="receiver_id" value="{{ $profile->user_id }}">
                                <button class="btn btn-outline-primary btn-sm rounded-3 px-2" title="Send Request">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center">
        {{ $profiles->links() }}
    </div>
    @endif
</div>
@endsection
