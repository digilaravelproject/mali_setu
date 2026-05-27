@extends('layouts.app')

@section('content')
<style>
.profile-hero-photo { width: 120px; height: 120px; border-radius: 20px; object-fit: cover; border: 4px solid #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
.info-label { font-size: 0.75rem; font-weight: 700; color: #adb5bd; text-transform: uppercase; letter-spacing: 0.5px; }
.info-value { font-weight: 600; color: #2d3748; }
.section-title { font-size: 0.9rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid rgba(173,20,87,0.1); padding-bottom: 8px; margin-bottom: 16px; }
.photo-grid img { width: 80px; height: 80px; border-radius: 12px; object-fit: cover; cursor: pointer; }
</style>

<div class="py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('matrimony.browse') }}" class="btn btn-light btn-sm rounded-3"><i class="fa-solid fa-arrow-left"></i></a>
        <h4 class="fw-bold mb-0">Matrimony Profile</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 mb-4"><i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first() }}</div>
    @endif

    @php
        $pd = $profile->personal_details ?? [];
        $fd = $profile->family_details ?? [];
        $ed = $profile->education_details ?? [];
        $pro = $profile->professional_details ?? [];
        $ld = $profile->location_details ?? [];
        $lf = $profile->lifestyle_details ?? [];
        $pp = $profile->partner_preferences ?? [];
        // Normalize associative array values to strings to avoid passing arrays to blade echos
        // Preserve indexed arrays (like 'photos') so their usage in templates remains intact.
        $normalize = function ($arr) {
            if (!is_array($arr)) return $arr;
            // associative check
            $keys = array_keys($arr);
            $isAssoc = $keys !== range(0, count($arr) - 1);
            if (!$isAssoc) return $arr; // keep indexed arrays as-is

            foreach ($arr as $k => $v) {
                if (is_array($v)) {
                    // join simple arrays, json_encode nested structures
                    $arr[$k] = implode(', ', array_map(function ($item) {
                        return is_array($item) ? json_encode($item) : $item;
                    }, $v));
                }
            }
            return $arr;
        };

        $pd = $normalize($pd);
        $fd = $normalize($fd);
        $ed = $normalize($ed);
        $pro = $normalize($pro);
        $ld = $normalize($ld);
        $lf = $normalize($lf);
        $pp = $normalize($pp);
    @endphp

    <div class="row g-4">
        {{-- LEFT: Photo + Quick Info --}}
        <div class="col-lg-4">
            <div class="glass-card text-center mb-4">
                @if(!empty($pd['photos'][0]))
                    <img src="{{ asset('storage/' . $pd['photos'][0]) }}" class="profile-hero-photo mb-3">
                @else
                    <img src="{{ asset('default-avatar.png') }}" class="profile-hero-photo mb-3">
                @endif
                <h4 class="fw-bold mb-1">{{ $profile->user->name ?? 'User' }}</h4>
                <p class="text-muted small mb-2">{{ $profile->age }} yrs • {{ ucfirst($pd['gender'] ?? 'N/A') }}</p>
                <div class="mb-3">
                    @if($profile->approval_status === 'approved')
                        <span class="badge bg-success rounded-pill px-3">Verified Profile</span>
                    @else
                        <span class="badge bg-warning text-dark rounded-pill px-3">Pending Approval</span>
                    @endif
                </div>

                {{-- Connection Action --}}
                @if($profile->user_id !== $user->id)
                    @if($connectionStatus === 'none')
                        <button class="btn btn-primary w-100 rounded-3 fw-bold mb-2" data-bs-toggle="modal" data-bs-target="#sendRequestModal">
                            <i class="fa-solid fa-paper-plane me-1"></i> Send Interest
                        </button>
                    @elseif($connectionStatus === 'pending')
                        <button class="btn btn-warning w-100 rounded-3 fw-bold mb-2" disabled>
                            <i class="fa-solid fa-clock me-1"></i> Request Pending
                        </button>
                    @elseif($connectionStatus === 'accepted')
                        @if($conversation)
                            <a href="{{ route('matrimony.chat', $conversation->id) }}" class="btn btn-success w-100 rounded-3 fw-bold mb-2">
                                <i class="fa-solid fa-comments me-1"></i> Open Chat
                            </a>
                        @endif
                        <div class="alert alert-success p-2 rounded-3 small mb-2">
                            <i class="fa-solid fa-heart me-1"></i> You are connected!
                        </div>
                    @elseif($connectionStatus === 'rejected')
                        <div class="alert alert-danger p-2 rounded-3 small mb-2">Request was declined.</div>
                    @endif
                @endif

                {{-- Quick Stats --}}
                <div class="text-start mt-3 pt-3 border-top">
                    <div class="row g-2">
                        <div class="col-6 mb-2">
                            <div class="info-label">Height</div>
                            <div class="info-value small">{{ $profile->height ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="info-label">Weight</div>
                            <div class="info-value small">{{ $profile->weight ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="info-label">Complexion</div>
                            <div class="info-value small">{{ ucfirst($profile->complexion ?? 'N/A') }}</div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="info-label">Status</div>
                            <div class="info-value small">{{ ucwords(str_replace('_', ' ', $pd['marital_status'] ?? 'N/A')) }}</div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Location</div>
                            <div class="info-value small">{{ $ld['city'] ?? '' }}{{ !empty($ld['city']) && !empty($ld['state']) ? ', ' : '' }}{{ $ld['state'] ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Photo Gallery --}}
            @if(!empty($pd['photos']) && count($pd['photos']) > 1)
            <div class="glass-card mb-4">
                <div class="section-title mb-3">Photos</div>
                <div class="photo-grid d-flex flex-wrap gap-2">
                    @foreach($pd['photos'] as $photo)
                        <img src="{{ asset('storage/' . $photo) }}" alt="Photo">
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT: Detail Sections --}}
        <div class="col-lg-8">
            {{-- About Me --}}
            @if(!empty($pd['about_me']))
            <div class="glass-card mb-4">
                <div class="section-title">About Me</div>
                <p class="text-secondary mb-0">{{ $pd['about_me'] }}</p>
            </div>
            @endif

            {{-- Personal Info --}}
            <div class="glass-card mb-4">
                <div class="section-title">Personal Information</div>
                <div class="row g-3">
                    <div class="col-md-4 col-6"><div class="info-label">Mother Tongue</div><div class="info-value">{{ $pd['mother_tongue'] ?? 'N/A' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Religion</div><div class="info-value">{{ $pd['religion'] ?? 'N/A' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Caste</div><div class="info-value">{{ $pd['caste'] ?? 'N/A' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Sub Caste</div><div class="info-value">{{ $pd['sub_caste'] ?? 'N/A' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Physical Status</div><div class="info-value">{{ ucwords(str_replace('_',' ',$profile->physical_status ?? 'Normal')) }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Profile By</div><div class="info-value">{{ ucfirst($pd['profile_created_by'] ?? 'Self') }}</div></div>
                </div>
            </div>

            {{-- Family Info --}}
            <div class="glass-card mb-4">
                <div class="section-title">Family Details</div>
                <div class="row g-3">
                    <div class="col-md-6 col-6"><div class="info-label">Father</div><div class="info-value">{{ $fd['father_name'] ?? 'N/A' }} ({{ $fd['father_occupation'] ?? '' }})</div></div>
                    <div class="col-md-6 col-6"><div class="info-label">Mother</div><div class="info-value">{{ $fd['mother_name'] ?? 'N/A' }} ({{ $fd['mother_occupation'] ?? '' }})</div></div>
                    <div class="col-md-3 col-6"><div class="info-label">Brothers</div><div class="info-value">{{ $fd['no_of_brothers'] ?? 0 }}</div></div>
                    <div class="col-md-3 col-6"><div class="info-label">Sisters</div><div class="info-value">{{ $fd['no_of_sisters'] ?? 0 }}</div></div>
                    <div class="col-md-3 col-6"><div class="info-label">Family Type</div><div class="info-value">{{ ucfirst($fd['family_type'] ?? 'N/A') }}</div></div>
                    <div class="col-md-3 col-6"><div class="info-label">Family Values</div><div class="info-value">{{ ucfirst($fd['family_value'] ?? 'N/A') }}</div></div>
                    @if(!empty($fd['about_family']))
                    <div class="col-12"><div class="info-label">About Family</div><p class="text-secondary small mb-0 mt-1">{{ $fd['about_family'] }}</p></div>
                    @endif
                </div>
            </div>

            {{-- Education & Career --}}
            <div class="glass-card mb-4">
                <div class="section-title">Education & Career</div>
                <div class="row g-3">
                    <div class="col-md-4 col-6"><div class="info-label">Qualification</div><div class="info-value">{{ $ed['highest_qualification'] ?? 'N/A' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">College</div><div class="info-value">{{ $ed['college_name'] ?? 'N/A' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Passing Year</div><div class="info-value">{{ $ed['passing_year'] ?? 'N/A' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Occupation</div><div class="info-value">{{ $pro['occupation'] ?? 'N/A' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Company</div><div class="info-value">{{ $pro['company_name'] ?? 'N/A' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Annual Income</div><div class="info-value">{{ str_replace('_', ' ', strtoupper($pro['annual_income'] ?? 'N/A')) }}</div></div>
                </div>
            </div>

            {{-- Lifestyle --}}
            <div class="glass-card mb-4">
                <div class="section-title">Lifestyle</div>
                <div class="row g-3">
                    <div class="col-md-3 col-6"><div class="info-label">Diet</div><div class="info-value">{{ ucfirst($lf['diet'] ?? 'N/A') }}</div></div>
                    <div class="col-md-3 col-6"><div class="info-label">Smoking</div><div class="info-value">{{ ucfirst($lf['smoking'] ?? 'No') }}</div></div>
                    <div class="col-md-3 col-6"><div class="info-label">Drinking</div><div class="info-value">{{ ucfirst($lf['drinking'] ?? 'No') }}</div></div>
                    <div class="col-md-3 col-6"><div class="info-label">Hobbies</div><div class="info-value">{{ $lf['hobbies'] ?? 'N/A' }}</div></div>
                </div>
            </div>

            {{-- Partner Preferences --}}
            @if(!empty($pp))
            <div class="glass-card mb-4">
                <div class="section-title">Partner Preferences</div>
                <div class="row g-3">
                    <div class="col-md-4 col-6"><div class="info-label">Age Range</div><div class="info-value">{{ $pp['age_min'] ?? '18' }} – {{ $pp['age_max'] ?? '60' }} yrs</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Min Height</div><div class="info-value">{{ $pp['height_min'] ?? 'Any' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Religion</div><div class="info-value">{{ $pp['religion'] ?? 'Any' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Caste</div><div class="info-value">{{ $pp['caste'] ?? 'Any' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Education</div><div class="info-value">{{ $pp['education'] ?? 'Any' }}</div></div>
                    <div class="col-md-4 col-6"><div class="info-label">Income</div><div class="info-value">{{ str_replace('_', '-', strtoupper($pp['income'] ?? 'Any')) }}</div></div>
                    @if(!empty($pp['about_partner']))
                    <div class="col-12"><div class="info-label">About Ideal Partner</div><p class="text-secondary small mb-0 mt-1">{{ $pp['about_partner'] }}</p></div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Send Request Modal --}}
<div class="modal fade" id="sendRequestModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Send Connection Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('matrimony.request.send') }}" method="POST">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $profile->user_id }}">
                <div class="modal-body">
                    <p class="text-secondary small mb-3">Introduce yourself to <strong>{{ $profile->user->name ?? 'this member' }}</strong></p>
                    <textarea name="message" class="form-control rounded-3" rows="4" placeholder="Write a short message (optional)..." maxlength="500"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold">
                        <i class="fa-solid fa-paper-plane me-1"></i> Send Interest
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
