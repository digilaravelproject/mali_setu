@extends('layouts.app')

@section('title', 'Browse Volunteer Opportunities — Mali Setu')

@section('content')
<div class="row g-4 text-start">
    <main class="col-12 px-md-4 py-4">
        
        <!-- Welcome Banner -->
        <div class="welcome-banner mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge mb-3 bg-white text-primary shadow-sm px-3 py-2 rounded-pill fw-bold text-uppercase small">Community Support</span>
                    <h1 class="fw-bold mb-2">Volunteer Opportunity Directory</h1>
                    <p class="opacity-75 mb-0">Discover and apply for volunteer tasks. Share your skills to empower community events, educational programs, and development campaigns.</p>
                </div>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <div class="glass-card p-4 border shadow-sm mb-4 bg-white">
            <form action="{{ route('volunteers.browse') }}" method="GET" class="row g-3">
                <div class="col-lg-4 col-md-6 col-12">
                    <label class="form-label small fw-semibold text-secondary">Keyword Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="Search by position, group, details..." value="{{ request('q') }}">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <label class="form-label small fw-semibold text-secondary">Location Filter</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary"><i class="fa-solid fa-location-dot"></i></span>
                        <input type="text" name="location" class="form-control" placeholder="City, State, Region..." value="{{ request('location') }}">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <label class="form-label small fw-semibold text-secondary">Skill / Talent Needed</label>
                    <select name="skill" class="form-select">
                        <option value="">All Skills</option>
                        @php
                            $commonSkills = ['Event Planning', 'Teaching & Tutoring', 'Social Media Management', 'Fundraising', 'First Aid / Medical', 'IT & Web Support', 'Public Speaking', 'Community Organizing', 'Graphic Design', 'Logistics & Manual Labor'];
                        @endphp
                        @foreach($commonSkills as $sk)
                            <option value="{{ $sk }}" {{ request('skill') == $sk ? 'selected' : '' }}>{{ $sk }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 col-12 d-flex align-items-end">
                    <div class="w-100 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-sliders"></i> Filter</button>
                        @if(request()->anyFilled(['q', 'location', 'skill']))
                            <a href="{{ route('volunteers.browse') }}" class="btn btn-light border py-2.5 rounded-3 text-secondary" title="Clear Filters"><i class="fa-solid fa-times"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Opportunities Grid -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-hands-holding-child me-2 text-primary"></i> Active Opportunities
            </h5>
            <a href="{{ route('volunteers.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                <i class="fa-solid fa-user me-1"></i> My Profile
            </a>
        </div>

        <div class="row g-4">
            @forelse($opportunities as $opp)
                @php
                    $pct = $opp->volunteers_needed > 0 
                        ? round(($opp->volunteers_registered / $opp->volunteers_needed) * 100) 
                        : 0;
                    $pct = min($pct, 100);
                @endphp
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="card h-100 border-0 rounded-4 shadow-sm bg-white border" style="transition: transform 0.25s, box-shadow 0.25s; border: 1px solid rgba(0,0,0,0.08) !important;">
                        <!-- Top Header info -->
                        <div class="card-body p-4 text-start d-flex flex-column h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 px-2.5 py-1.5 rounded-2 font-semibold small mb-2 d-inline-block">
                                        <i class="fa-solid fa-building me-1"></i> {{ $opp->organization }}
                                    </span>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 py-1.5 px-2.5 rounded-pill small fw-semibold">
                                    <i class="fa-solid fa-clock me-1"></i> {{ $opp->time_commitment ?? 'Flexible' }}
                                </span>
                            </div>

                            <h5 class="fw-bold text-dark mb-2 text-truncate" title="{{ $opp->title }}">{{ $opp->title }}</h5>

                            <div class="d-flex align-items-center gap-2 mb-3 text-secondary small">
                                <span><i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ $opp->location }}</span>
                                <span class="text-muted">•</span>
                                <span><i class="fa-solid fa-calendar me-1 text-primary"></i> Starts {{ $opp->start_date ? $opp->start_date->format('M d, Y') : 'TBD' }}</span>
                            </div>

                            <p class="small text-secondary mb-4 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; min-height: 4.5em;">
                                {{ $opp->description }}
                            </p>

                            <!-- Progress Meter -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="text-secondary fw-semibold">Volunteers Recruited</span>
                                    <strong class="text-dark">{{ $opp->volunteers_registered }} / {{ $opp->volunteers_needed }}</strong>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 3px; background-color: rgba(0,0,0,0.06);">
                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <!-- Skills Badges -->
                            <div class="mb-4">
                                <div class="d-flex flex-wrap gap-1">
                                    @if(is_array($opp->required_skills))
                                        @foreach(array_slice($opp->required_skills, 0, 3) as $skill)
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-2 px-2 py-1 small">{{ trim($skill) }}</span>
                                        @endforeach
                                        @if(count($opp->required_skills) > 3)
                                            <span class="badge bg-light text-secondary border rounded-2 px-2 py-1 small">+{{ count($opp->required_skills) - 3 }} more</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div class="pt-3 border-top d-flex justify-content-end mt-auto">
                                <a href="{{ route('volunteers.opportunity.show', $opp->id) }}" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold w-100">
                                    View Details & Apply <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="text-secondary mb-3 fs-1"><i class="fa-solid fa-hands-bound"></i></div>
                    <h5 class="fw-bold">No Opportunities Found</h5>
                    <p class="text-secondary small mb-0">No active volunteer opportunities matched your filter. Try adjusting your query or filters.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-5 d-flex justify-content-center">
            {{ $opportunities->links() }}
        </div>

    </main>
</div>
@endsection
