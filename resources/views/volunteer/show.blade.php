@extends('layouts.app')

@section('title', $opportunity->title . ' — Mali Setu')

@section('content')
<div class="row g-4 text-start">
    <main class="col-12 px-md-4 py-4">
        
        <!-- Breadcrumb / Back Navigation -->
        <div class="mb-4">
            <a href="{{ route('volunteers.browse') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Opportunities
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Left Panel: Detailed Information -->
            <div class="col-lg-8 col-md-12">
                <div class="glass-card shadow-sm border p-4 bg-white mb-4">
                    <!-- Title & Organization -->
                    <div class="border-bottom pb-4 mb-4">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-2 fw-bold mb-3">
                            <i class="fa-solid fa-building me-1"></i> {{ $opportunity->organization }}
                        </span>
                        <h2 class="fw-bold text-dark mb-3">{{ $opportunity->title }}</h2>
                        
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-2.5 text-secondary">
                                    <div class="rounded-circle p-2 bg-light text-primary">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div>
                                        <span class="small d-block text-muted">Location</span>
                                        <strong class="text-dark small">{{ $opportunity->location }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-2.5 text-secondary">
                                    <div class="rounded-circle p-2 bg-light text-primary">
                                        <i class="fa-solid fa-clock"></i>
                                    </div>
                                    <div>
                                        <span class="small d-block text-muted">Time Commitment</span>
                                        <strong class="text-dark small">{{ $opportunity->time_commitment ?? 'Flexible' }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-3">Job Description & Scope</h5>
                        <p class="text-secondary leading-relaxed" style="white-space: pre-line;">{{ $opportunity->description }}</p>
                    </div>

                    <!-- Requirements -->
                    @if($opportunity->requirements)
                        <div class="mb-4 pt-4 border-top">
                            <h5 class="fw-bold text-dark mb-3">Special Requirements</h5>
                            <p class="text-secondary leading-relaxed" style="white-space: pre-line;">{{ $opportunity->requirements }}</p>
                        </div>
                    @endif

                    <!-- Skills Required -->
                    <div class="mb-2 pt-4 border-top">
                        <h5 class="fw-bold text-dark mb-3">Target Skills & Expertise</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @if(is_array($opportunity->required_skills))
                                @foreach($opportunity->required_skills as $skill)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-2 px-3 py-2 fw-semibold">{{ trim($skill) }}</span>
                                @endforeach
                            @else
                                <span class="text-muted small">No specific skills listed.</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Coordinator Contact Details -->
                <div class="glass-card shadow-sm border p-4 bg-white">
                    <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-address-book text-primary me-2"></i> Coordinator Contact</h5>
                    <div class="row g-3">
                        <div class="col-md-4 col-sm-12">
                            <div class="p-3 border rounded-3 bg-light bg-opacity-25 h-100">
                                <span class="small text-muted d-block mb-1">Contact Person</span>
                                <strong class="text-dark">{{ $opportunity->contact_person ?? 'Community Coordinator' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="p-3 border rounded-3 bg-light bg-opacity-25 h-100">
                                <span class="small text-muted d-block mb-1">Email Address</span>
                                <strong class="text-dark">{{ $opportunity->contact_email ?? 'support@malisetu.org' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="p-3 border rounded-3 bg-light bg-opacity-25 h-100">
                                <span class="small text-muted d-block mb-1">Phone Number</span>
                                <strong class="text-dark">{{ $opportunity->contact_phone ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Application Portal & Progress -->
            <div class="col-lg-4 col-md-12">
                <!-- Opportunity Overview Status -->
                <div class="glass-card shadow-sm border p-4 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-3">Status Overview</h5>
                    
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted small">Start Date</span>
                        <strong class="text-dark small">{{ $opportunity->start_date ? $opportunity->start_date->format('M d, Y') : 'N/A' }}</strong>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted small">End Date</span>
                        <strong class="text-dark small">{{ $opportunity->end_date ? $opportunity->end_date->format('M d, Y') : 'N/A' }}</strong>
                    </div>

                    @php
                        $pct = $opportunity->volunteers_needed > 0 
                            ? round(($opportunity->volunteers_registered / $opportunity->volunteers_needed) * 100) 
                            : 0;
                        $pct = min($pct, 100);
                    @endphp

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1.5 small">
                            <span class="text-muted">Registered Volunteers</span>
                            <strong class="text-dark">{{ $opportunity->volunteers_registered }} / {{ $opportunity->volunteers_needed }}</strong>
                        </div>
                        <div class="progress mb-2" style="height: 8px; border-radius: 4px; background-color: rgba(0,0,0,0.06);">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="small text-muted d-block text-center mt-1">{{ 100 - $pct }}% slots remaining</span>
                    </div>
                </div>

                <!-- Registration / Action Area -->
                <div class="glass-card shadow-sm border p-4 bg-white">
                    <h5 class="fw-bold text-dark mb-3">Application Hub</h5>

                    @if($hasApplied)
                        <!-- Already Applied State -->
                        <div class="text-center p-3 rounded-3 bg-light bg-opacity-50 mb-3 border">
                            <div class="metric-icon mx-auto mb-2 text-success bg-success bg-opacity-10" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="fa-solid fa-circle-check fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Application Submitted</h6>
                            <p class="text-secondary small mb-2">Applied on {{ $application->applied_at ? $application->applied_at->format('M d, Y') : $application->created_at->format('M d, Y') }}</p>
                            
                            <div class="mb-3">
                                @if($application->status === 'approved')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 py-2 px-3 rounded-pill fw-bold">
                                        Approved / Active
                                    </span>
                                @elseif($application->status === 'pending')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 py-2 px-3 rounded-pill fw-bold">
                                        Pending Review
                                    </span>
                                @elseif($application->status === 'rejected')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 py-2 px-3 rounded-pill fw-bold">
                                        Rejected
                                    </span>
                                @elseif($application->status === 'withdrawn')
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 py-2 px-3 rounded-pill fw-bold">
                                        Withdrawn
                                    </span>
                                @endif
                            </div>

                            @if($application->message)
                                <div class="text-start border-top pt-2.5 mt-2.5">
                                    <span class="small text-muted fw-bold d-block mb-1">Your message:</span>
                                    <p class="text-secondary small mb-0 font-italic" style="word-break: break-word;">"{{ $application->message }}"</p>
                                </div>
                            @endif
                        </div>

                        @if($application->status === 'pending' || $application->status === 'approved')
                            <form method="POST" action="{{ route('volunteers.application.withdraw', $application->id) }}" onsubmit="return confirm('Are you sure you want to withdraw your application?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100 rounded-3 py-2.5 fw-bold shadow-sm">
                                    <i class="fa-solid fa-ban me-1"></i> Withdraw Application
                                </button>
                            </form>
                        @endif

                    @elseif(!$volunteer)
                        <!-- Unregistered User State -->
                        <div class="text-center p-3 rounded-3 bg-light bg-opacity-50 border">
                            <div class="metric-icon mx-auto mb-2 text-warning bg-warning bg-opacity-10" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="fa-solid fa-triangle-exclamation fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-2">Volunteer Profile Required</h6>
                            <p class="text-secondary small mb-3">You must register a volunteer profile containing your skills and service region before applying.</p>
                            <a href="{{ route('volunteers.index') }}" class="btn btn-primary w-100 rounded-3 py-2 fw-bold">
                                <i class="fa-solid fa-user-plus me-1"></i> Set Up Profile Now
                            </a>
                        </div>

                    @elseif(!$opportunity->isAcceptingApplications())
                        <!-- Opportunity Closed State -->
                        <div class="text-center p-3 rounded-3 bg-light bg-opacity-50 border">
                            <div class="metric-icon mx-auto mb-2 text-secondary bg-secondary bg-opacity-10" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="fa-solid fa-ban fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Applications Closed</h6>
                            <p class="text-secondary small mb-0">This volunteer opportunity is either full, inactive, or has already commenced.</p>
                        </div>

                    @else
                        <!-- Eligible & Not Applied State (Show Form) -->
                        <form method="POST" action="{{ route('volunteers.opportunity.apply') }}">
                            @csrf
                            <input type="hidden" name="volunteer_opportunity_id" value="{{ $opportunity->id }}">

                            <div class="mb-3">
                                <label for="message" class="form-label small fw-semibold text-secondary">Introductory Note (Optional)</label>
                                <textarea name="message" id="message" rows="4" class="form-control small" placeholder="Why are you interested in helping out with this event? Highlight matching skills..." style="font-size:0.85rem;"></textarea>
                                <span class="small text-muted d-block mt-1">Briefly tell the coordinator how you can assist.</span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2.5 fw-bold shadow-sm">
                                <i class="fa-solid fa-paper-plane me-1"></i> Submit Application
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    </main>
</div>
@endsection
