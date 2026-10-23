@extends('layouts.app')

@section('title', 'Volunteer Portal — Mali Setu')

@section('content')
<div class="container-fluid py-2">
    <!-- Welcome Header -->
    <div class="welcome-banner mb-4 d-none">
        <h1 class="fw-bold mb-2">Volunteer Portal</h1>
        <p class="lead mb-0 text-white-50">Join community efforts, share your expertise, and build a stronger community together.</p>
    </div>

    @if(!$volunteer)
        <!-- Registration Form (Unregistered Volunteer) -->
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="glass-card shadow-sm border">
                    <div class="text-center mb-4 border-bottom pb-3">
                        <div class="metric-icon mx-auto mb-3">
                            <i class="fa-solid fa-hands-holding-child fs-3"></i>
                        </div>
                        <h3 class="fw-bold text-dark">Become a Volunteer</h3>
                        <p class="text-muted">Register your volunteer profile to search and apply for service events matching your skills.</p>
                    </div>

                    <form method="POST" action="{{ route('volunteers.profile.update') }}" class="row g-4">
                        @csrf
                        
                        <!-- Bio -->
                        <div class="col-12">
                            <label for="bio" class="form-label">Brief Bio / Introduction</label>
                            <textarea name="bio" id="bio" rows="3" class="form-control" placeholder="Introduce yourself, your passions, and why you want to volunteer...">{{ old('bio') }}</textarea>
                            <span class="small text-muted">Tell us a bit about yourself (Max 1000 characters).</span>
                        </div>

                        <!-- Experience -->
                        <div class="col-12">
                            <label for="experience" class="form-label">Past Volunteer/Work Experience</label>
                            <textarea name="experience" id="experience" rows="3" class="form-control" placeholder="Briefly describe any prior volunteering experience or professional skills you bring...">{{ old('experience') }}</textarea>
                        </div>

                        <!-- Skills Checklist -->
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label d-block">Skills & Talents</label>
                            <div class="p-3 border rounded-3 bg-light bg-opacity-25" style="max-height: 200px; overflow-y: auto;">
                                @php
                                    $commonSkills = ['Event Planning', 'Teaching & Tutoring', 'Social Media Management', 'Fundraising', 'First Aid / Medical', 'IT & Web Support', 'Public Speaking', 'Community Organizing', 'Graphic Design', 'Logistics & Manual Labor'];
                                @endphp
                                @foreach($commonSkills as $skill)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="skills[]" value="{{ $skill }}" id="skill_{{ $loop->index }}" {{ is_array(old('skills')) && in_array($skill, old('skills')) ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="skill_{{ $loop->index }}">
                                            {{ $skill }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <span class="small text-muted mt-1 d-block">Select skills you are willing to offer.</span>
                        </div>

                        <!-- Interests Checklist -->
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label d-block">Fields of Interest</label>
                            <div class="p-3 border rounded-3 bg-light bg-opacity-25" style="max-height: 200px; overflow-y: auto;">
                                @php
                                    $interests = ['Education & Literacy', 'Health & Wellness', 'Environment & Conservation', 'Disaster Relief / Community Support', 'Youth Mentorship', 'Senior Care & Outreach', 'Arts & Culture', 'Animal Welfare'];
                                @endphp
                                @foreach($interests as $interest)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="interests[]" value="{{ $interest }}" id="interest_{{ $loop->index }}" {{ is_array(old('interests')) && in_array($interest, old('interests')) ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="interest_{{ $loop->index }}">
                                            {{ $interest }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <span class="small text-muted mt-1 d-block">Select areas you care about most.</span>
                        </div>

                        <!-- Availability & Location -->
                        <div class="col-md-6 col-sm-12">
                            <label for="availability" class="form-label">Availability / Commitment</label>
                            <select name="availability" id="availability" class="form-select">
                                <option value="" disabled selected>Select availability</option>
                                <option value="Weekends Only" {{ old('availability') == 'Weekends Only' ? 'selected' : '' }}>Weekends Only</option>
                                <option value="Weekdays Only" {{ old('availability') == 'Weekdays Only' ? 'selected' : '' }}>Weekdays Only</option>
                                <option value="Flexible (Few hours/week)" {{ old('availability') == 'Flexible (Few hours/week)' ? 'selected' : '' }}>Flexible (Few hours/week)</option>
                                <option value="Evenings Only" {{ old('availability') == 'Evenings Only' ? 'selected' : '' }}>Evenings Only</option>
                                <option value="On Call / Emergency Relief" {{ old('availability') == 'On Call / Emergency Relief' ? 'selected' : '' }}>On Call / Emergency Relief</option>
                            </select>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <label for="location" class="form-label">Service Region / Location</label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="e.g., Mumbai, Maharashtra" value="{{ old('location', Auth::user()->city . (Auth::user()->state ? ', ' . Auth::user()->state : '')) }}">
                        </div>

                        <div class="col-12 mt-4 text-center">
                            <button type="submit" class="btn btn-primary py-2.5 px-5 rounded-3 fw-bold shadow-sm">
                                <i class="fa-solid fa-square-plus me-2"></i> Register Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <!-- Dashboard & Applications (Registered Volunteer) -->
        <div class="row">
            <!-- Left Side: Profile Card & Recommendations -->
            <div class="col-lg-4">
                <div class="glass-card shadow-sm border mb-4">
                    <div class="text-center border-bottom pb-3 mb-3">
                        <div class="position-relative d-inline-block">
                            @if(Auth::user()->photo)
                                <img src="{{ asset('storage/' . Auth::user()->photo) }}" class="profile-photo-circle mb-3 mx-auto d-block" alt="Avatar">
                            @else
                                <img src="{{ asset('default-avatar.png') }}" class="profile-photo-circle mb-3 mx-auto d-block" alt="Avatar" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
                            @endif
                            <span class="position-absolute bottom-0 end-0 badge rounded-circle p-2 bg-success border border-white border-3" title="Registered Volunteer"></span>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">{{ Auth::user()->name }}</h4>
                        <p class="text-muted small mb-2"><i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ $volunteer->location }}</p>
                        
                        @if($volunteer->status === 'active')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 py-2 px-3 rounded-pill fw-bold small">
                                <i class="fa-solid fa-shield-check me-1"></i> Active Status
                            </span>
                        @elseif($volunteer->status === 'pending')
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 py-2 px-3 rounded-pill fw-bold small">
                                <i class="fa-solid fa-clock me-1"></i> Verification Pending
                            </span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 py-2 px-3 rounded-pill fw-bold small">
                                <i class="fa-solid fa-eye-slash me-1"></i> Inactive
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold text-dark small text-uppercase text-secondary mb-2">Capabilities / Skills</h6>
                        <div class="d-flex flex-wrap gap-1.5">
                            @foreach(explode(',', $volunteer->skills) as $skill)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-2 px-2.5 py-1.5 fw-semibold small">{{ trim($skill) }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3 border-top pt-3">
                        <h6 class="fw-bold text-dark small text-uppercase text-secondary mb-2">Interests</h6>
                        <div class="d-flex flex-wrap gap-1.5">
                            @foreach($volunteer->interests ?? [] as $interest)
                                <span class="badge bg-accent bg-opacity-10 text-accent border border-accent border-opacity-20 rounded-2 px-2.5 py-1.5 fw-semibold small">{{ trim($interest) }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary w-100 rounded-3 py-2 fw-semibold" data-bs-toggle="collapse" data-bs-target="#editProfileCollapse">
                            <i class="fa-solid fa-user-gear me-1"></i> Edit Volunteer Credentials
                        </button>
                    </div>
                </div>

                <!-- Recommended Opportunities -->
                <div class="glass-card shadow-sm border mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-fire text-accent me-1"></i> Open Opportunities</h5>
                    
                    @if($matchedOpportunities->isEmpty())
                        <p class="small text-muted mb-0">No active matched openings found in your location. Try exploring all listings.</p>
                    @else
                        <div class="d-grid gap-3">
                            @foreach($matchedOpportunities as $match)
                                <div class="p-3 border rounded-3 bg-light bg-opacity-25">
                                    <span class="small text-secondary fw-semibold d-block mb-1">{{ $match->organization }}</span>
                                    <h6 class="fw-bold text-dark mb-2 text-truncate">{{ $match->title }}</h6>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="small text-muted"><i class="fa-solid fa-location-dot me-1"></i> {{ $match->location }}</span>
                                        <a href="{{ route('volunteers.opportunity.show', $match->id) }}" class="btn btn-sm btn-primary rounded-2 px-3 fw-bold small">
                                            Apply
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="border-top pt-3 mt-3 text-center">
                        <a href="{{ route('volunteers.browse') }}" class="btn btn-sm btn-outline-secondary w-100 rounded-3 py-2 fw-semibold">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Browse All Opportunities
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Side: Edit Form (Collapse) & Applications Tracking -->
            <div class="col-lg-8">
                <!-- Collapsible Edit Form -->
                <div class="collapse mb-4" id="editProfileCollapse">
                    <div class="glass-card shadow-sm border">
                        <h4 class="fw-bold text-dark mb-3">Modify Volunteer Profile</h4>
                        <form method="POST" action="{{ route('volunteers.profile.update') }}" class="row g-3">
                            @csrf
                            
                            <div class="col-12">
                                <label for="bio" class="form-label">Brief Bio / Introduction</label>
                                <textarea name="bio" id="bio" rows="3" class="form-control">{{ old('bio', $volunteer->bio) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="experience" class="form-label">Past Volunteer/Work Experience</label>
                                <textarea name="experience" id="experience" rows="3" class="form-control">{{ old('experience', $volunteer->experience) }}</textarea>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <label class="form-label d-block">Skills & Talents</label>
                                <div class="p-3 border rounded-3 bg-light bg-opacity-25" style="max-height: 180px; overflow-y: auto;">
                                    @php
                                        $commonSkills = ['Event Planning', 'Teaching & Tutoring', 'Social Media Management', 'Fundraising', 'First Aid / Medical', 'IT & Web Support', 'Public Speaking', 'Community Organizing', 'Graphic Design', 'Logistics & Manual Labor'];
                                        $mySkills = array_map('trim', explode(',', $volunteer->skills));
                                    @endphp
                                    @foreach($commonSkills as $skill)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="skills[]" value="{{ $skill }}" id="edit_skill_{{ $loop->index }}" {{ in_array($skill, $mySkills) ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" for="edit_skill_{{ $loop->index }}">
                                                {{ $skill }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <label class="form-label d-block">Fields of Interest</label>
                                <div class="p-3 border rounded-3 bg-light bg-opacity-25" style="max-height: 180px; overflow-y: auto;">
                                    @php
                                        $interests = ['Education & Literacy', 'Health & Wellness', 'Environment & Conservation', 'Disaster Relief / Community Support', 'Youth Mentorship', 'Senior Care & Outreach', 'Arts & Culture', 'Animal Welfare'];
                                        $myInterests = $volunteer->interests ?? [];
                                    @endphp
                                    @foreach($interests as $interest)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="interests[]" value="{{ $interest }}" id="edit_interest_{{ $loop->index }}" {{ in_array($interest, $myInterests) ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" for="edit_interest_{{ $loop->index }}">
                                                {{ $interest }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <label for="availability" class="form-label">Availability / Commitment</label>
                                <select name="availability" id="availability" class="form-select">
                                    <option value="Weekends Only" {{ old('availability', $volunteer->availability) == 'Weekends Only' ? 'selected' : '' }}>Weekends Only</option>
                                    <option value="Weekdays Only" {{ old('availability', $volunteer->availability) == 'Weekdays Only' ? 'selected' : '' }}>Weekdays Only</option>
                                    <option value="Flexible (Few hours/week)" {{ old('availability', $volunteer->availability) == 'Flexible (Few hours/week)' ? 'selected' : '' }}>Flexible (Few hours/week)</option>
                                    <option value="Evenings Only" {{ old('availability', $volunteer->availability) == 'Evenings Only' ? 'selected' : '' }}>Evenings Only</option>
                                    <option value="On Call / Emergency Relief" {{ old('availability', $volunteer->availability) == 'On Call / Emergency Relief' ? 'selected' : '' }}>On Call / Emergency Relief</option>
                                </select>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <label for="location" class="form-label">Service Region / Location</label>
                                <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $volunteer->location) }}">
                            </div>

                            <div class="col-12 mt-3 text-end gap-2 d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#editProfileCollapse">Cancel</button>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Applications Tracking -->
                <div class="glass-card shadow-sm border p-0 overflow-hidden">
                    <div class="p-4 border-bottom bg-white bg-opacity-50 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0 text-dark">My Active Support Tickets</h5>
                        <span class="badge bg-primary py-2 px-3 rounded-pill fw-semibold">{{ count($applications) }} Applications</span>
                    </div>

                    @if(empty($applications) || count($applications) === 0)
                        <div class="text-center py-5 my-3">
                            <div class="metric-icon mx-auto mb-3">
                                <i class="fa-solid fa-list-check fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark">No Submitted Applications</h5>
                            <p class="text-muted mb-0">Browse open opportunities and apply to help local groups.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-uppercase fs-7 fw-bold text-secondary">
                                    <tr>
                                        <th class="ps-4 py-3">Applied</th>
                                        <th class="py-3">Opportunity</th>
                                        <th class="py-3">Organization</th>
                                        <th class="py-3 text-center">Status</th>
                                        <th class="pe-4 py-3 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $app)
                                        <tr>
                                            <td class="ps-4 fw-semibold text-dark">{{ $app->applied_at ? $app->applied_at->format('M d, Y') : $app->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('volunteers.opportunity.show', $app->volunteerOpportunity->id) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                                    {{ $app->volunteerOpportunity->title }}
                                                </a>
                                            </td>
                                            <td class="text-secondary font-semibold">{{ $app->volunteerOpportunity->organization }}</td>
                                            <td class="text-center">
                                                @if($app->status === 'approved')
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 py-2 px-3 rounded-pill fw-bold">
                                                        Approved
                                                    </span>
                                                @elseif($app->status === 'pending')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 py-2 px-3 rounded-pill fw-bold">
                                                        Pending
                                                    </span>
                                                @elseif($app->status === 'rejected')
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 py-2 px-3 rounded-pill fw-bold">
                                                        Rejected
                                                    </span>
                                                @elseif($app->status === 'withdrawn')
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 py-2 px-3 rounded-pill fw-bold">
                                                        Withdrawn
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="pe-4 text-end">
                                                @if($app->status === 'pending' || $app->status === 'approved')
                                                    <form method="POST" action="{{ route('volunteers.application.withdraw', $app->id) }}" onsubmit="return confirm('Are you sure you want to withdraw this application?');" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-3.5 rounded-2 fw-semibold">
                                                            <i class="fa-solid fa-ban me-1"></i> Withdraw
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="small text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
