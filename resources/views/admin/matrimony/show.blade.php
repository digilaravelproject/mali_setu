@extends('admin.layouts.app')

@section('title', 'Matrimony Profile Details')

@section('content')
<!-- Google Fonts for modern premium typography -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
    .matrimony-show-container {
        font-family: 'Plus Jakarta Sans', 'Outfit', 'Inter', sans-serif;
        color: #2d3748;
    }
    
    .matrimony-brand-glow {
        box-shadow: 0 10px 30px rgba(255, 71, 87, 0.08);
    }
    
    .premium-profile-card {
        border: none;
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(255, 71, 87, 0.05);
    }
    
    .avatar-wrapper {
        position: relative;
        display: inline-block;
        border-radius: 50%;
        padding: 6px;
        background: linear-gradient(135deg, hsl(354, 100%, 63%) 0%, hsl(25, 100%, 58%) 100%);
        box-shadow: 0 8px 25px rgba(255, 71, 87, 0.15);
    }
    .avatar-image {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #ffffff;
    }
    
    .section-title-premium {
        font-size: 0.95rem;
        font-weight: 800;
        color: hsl(354, 100%, 63%);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        border-bottom: 2px solid rgba(255, 71, 87, 0.08);
        padding-bottom: 8px;
        margin-bottom: 20px;
        margin-top: 10px;
    }
    
    .info-item-box {
        background: #f8fafc;
        border: 1px solid rgba(0, 0, 0, 0.02);
        border-radius: 16px;
        padding: 12px 18px;
        height: 100%;
        transition: all 0.3s ease;
    }
    .info-item-box:hover {
        background: hsl(354, 100%, 99.4%);
        border-color: rgba(255, 71, 87, 0.12);
        transform: translateY(-2px);
    }
    .info-label-premium {
        font-size: 0.7rem;
        font-weight: 750;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 4px;
    }
    .info-value-premium {
        font-size: 0.92rem;
        font-weight: 650;
        color: #1e293b;
    }
    
    .photo-gallery-grid img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        border-radius: 18px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .photo-gallery-grid img:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(255, 71, 87, 0.12);
        border-color: hsl(354, 100%, 63%);
    }
    
    .premium-badge-status {
        font-size: 0.82rem;
        font-weight: 750;
        padding: 6px 16px;
        border-radius: 50px;
        letter-spacing: 0.5px;
    }
    
    .btn-action-premium {
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    .btn-action-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>

@php
    $pd = $profile->personal_details ?? [];
    $fd = $profile->family_details ?? [];
    $ed = $profile->education_details ?? [];
    $pro = $profile->professional_details ?? [];
    $ld = $profile->location_details ?? [];
    $lf = $profile->lifestyle_details ?? [];
    $pp = $profile->partner_preferences ?? [];

    $normalize = function ($arr) {
        if (!is_array($arr)) return $arr;
        $keys = array_keys($arr);
        $isAssoc = $keys !== range(0, count($arr) - 1);
        if (!$isAssoc) return $arr; // keep indexed arrays as-is

        foreach ($arr as $k => $v) {
            if ($k === 'photos') continue; // keep photos as an array
            if (is_array($v)) {
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

<div class="container-fluid matrimony-show-container py-3">
    <div class="row">
        <div class="col-12">
            <div class="premium-profile-card card mb-4">
                <!-- Header Card -->
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width:48px; height:48px; border-radius:14px;">
                            <i class="fas fa-heart fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Matrimony Profile Moderation</h4>
                            <p class="text-muted mb-0 small">Verify, approve, or reject user matrimony credentials</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.matrimony.index') }}" class="btn btn-secondary btn-action-premium d-flex align-items-center gap-2">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        
                        @if($profile->approval_status === 'pending')
                            <!-- Approve Form -->
                            <form method="POST" action="{{ route('admin.matrimony.approve', $profile->id) }}" class="d-inline m-0">
                                @csrf
                                <button type="submit" class="btn btn-success btn-action-premium d-flex align-items-center gap-2" 
                                        onclick="return confirm('Are you sure you want to approve this matrimony profile?')">
                                    <i class="fas fa-check"></i> Approve Profile
                                </button>
                            </form>
                            
                            <!-- Reject Button triggers Modal -->
                            <button type="button" class="btn btn-danger btn-action-premium d-flex align-items-center gap-2" data-toggle="modal" data-target="#rejectModal">
                                <i class="fas fa-times"></i> Reject Profile
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4 pt-0">
                    <div class="row g-4">
                        <!-- Profile Card Visuals (Left Column) -->
                        <div class="col-lg-4 text-center">
                            <div class="matrimony-brand-glow p-4 rounded-4 bg-light border border-light">
                                <div class="avatar-wrapper mb-3">
                                    @if(!empty($pd['photos'][0]))
                                        <img src="{{ asset('storage/' . $pd['photos'][0]) }}" alt="Profile Photo" class="avatar-image">
                                    @else
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center avatar-image" style="background:#edf2f7 !important;">
                                            <i class="fas fa-user fa-5x text-secondary"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <h4 class="fw-bold text-dark mb-1">{{ $profile->user->name ?? 'N/A' }}</h4>
                                <p class="text-muted small mb-3">ID: #{{ $profile->id }} • Seeker Account</p>
                                
                                <div class="mb-4">
                                    @if($profile->approval_status === 'approved')
                                        <span class="badge bg-success bg-opacity-10 text-success premium-badge-status border border-success border-opacity-10"><i class="fas fa-circle-check me-1"></i>Approved & Live</span>
                                    @elseif($profile->approval_status === 'pending')
                                        <span class="badge bg-warning bg-opacity-10 text-warning premium-badge-status border border-warning border-opacity-10"><i class="fas fa-clock me-1"></i>Pending Review</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger premium-badge-status border border-danger border-opacity-10"><i class="fas fa-circle-xmark me-1"></i>Rejected</span>
                                    @endif
                                </div>
                                
                                <div class="row g-2 pt-3 border-top border-light text-start">
                                    <div class="col-6 mb-2">
                                        <div class="info-label-premium">Age</div>
                                        <div class="info-value-premium">{{ $profile->age ?? 'N/A' }} yrs</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <div class="info-label-premium">Gender</div>
                                        <div class="info-value-premium">{{ ucfirst($pd['gender'] ?? $profile->gender ?? 'N/A') }}</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <div class="info-label-premium">Height</div>
                                        <div class="info-value-premium">{{ $profile->height ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <div class="info-label-premium">Weight</div>
                                        <div class="info-value-premium">{{ $profile->weight ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-label-premium">Complexion</div>
                                        <div class="info-value-premium">{{ ucfirst($profile->complexion ?? 'N/A') }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-label-premium">Marital Status</div>
                                        <div class="info-value-premium">{{ ucwords(str_replace('_', ' ', $pd['marital_status'] ?? 'N/A')) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column Sections -->
                        <div class="col-lg-8">
                            <!-- About Seeker -->
                            @if(!empty($pd['about_me']))
                            <div class="mb-4 bg-danger bg-opacity-5 p-3 rounded-4 border border-danger border-opacity-10 matrimony-brand-glow">
                                <div class="section-title-premium mt-0 mb-2"><i class="fas fa-quote-left me-1"></i>About Seeker</div>
                                <p class="text-secondary small mb-0 font-italic">{{ $pd['about_me'] }}</p>
                            </div>
                            @endif

                            <!-- Personal Details -->
                            <div class="mb-4">
                                <div class="section-title-premium"><i class="fas fa-user-tag me-1"></i>Personal Information</div>
                                <div class="row g-3">
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Mother Tongue</div>
                                            <div class="info-value-premium">{{ $pd['mother_tongue'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Religion</div>
                                            <div class="info-value-premium">{{ $pd['religion'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Caste</div>
                                            <div class="info-value-premium">{{ $pd['caste'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Sub Caste</div>
                                            <div class="info-value-premium">{{ $pd['sub_caste'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Physical Status</div>
                                            <div class="info-value-premium">{{ ucwords(str_replace('_', ' ', $profile->physical_status ?? 'Normal')) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Profile Created By</div>
                                            <div class="info-value-premium">{{ ucfirst($pd['profile_created_by'] ?? 'Self') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Details -->
                            <div class="mb-4">
                                <div class="section-title-premium"><i class="fas fa-address-book me-1"></i>Contact Information</div>
                                <div class="row g-3">
                                    <div class="col-md-4 col-12">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Email Address</div>
                                            <div class="info-value-premium">{{ $profile->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Phone Number</div>
                                            <div class="info-value-premium">{{ $profile->user->phone ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Location / Native Place</div>
                                            <div class="info-value-premium">
                                                {{ $ld['city'] ?? '' }}{{ !empty($ld['city']) && !empty($ld['state']) ? ', ' : '' }}{{ $ld['state'] ?? 'Not specified' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Education & Career -->
                            <div class="mb-4">
                                <div class="section-title-premium"><i class="fas fa-graduation-cap me-1"></i>Education & Career</div>
                                <div class="row g-3">
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Highest Qualification</div>
                                            <div class="info-value-premium">{{ $ed['highest_qualification'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">College / University</div>
                                            <div class="info-value-premium">{{ $ed['college_name'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Passing Year</div>
                                            <div class="info-value-premium">{{ $ed['passing_year'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Occupation</div>
                                            <div class="info-value-premium">{{ $pro['occupation'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Company Name</div>
                                            <div class="info-value-premium">{{ $pro['company_name'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="info-item-box">
                                            <div class="info-label-premium">Annual Income</div>
                                            <div class="info-value-premium">{{ str_replace('_', ' ', strtoupper($pro['annual_income'] ?? 'N/A')) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Details Card -->
                    <div class="premium-profile-card p-4 bg-light border-0 rounded-4 mt-4">
                        <div class="section-title-premium mt-0"><i class="fas fa-users me-1"></i>Family Details</div>
                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Father's Details</div>
                                    <div class="info-value-premium">
                                        {{ $fd['father_name'] ?? 'Not specified' }}
                                        @if(!empty($fd['father_occupation']))
                                            <span class="text-muted small">({{ $fd['father_occupation'] }})</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Mother's Details</div>
                                    <div class="info-value-premium">
                                        {{ $fd['mother_name'] ?? 'Not specified' }}
                                        @if(!empty($fd['mother_occupation']))
                                            <span class="text-muted small">({{ $fd['mother_occupation'] }})</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">No. of Brothers</div>
                                    <div class="info-value-premium">{{ $fd['no_of_brothers'] ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">No. of Sisters</div>
                                    <div class="info-value-premium">{{ $fd['no_of_sisters'] ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Family Type</div>
                                    <div class="info-value-premium">{{ ucfirst($fd['family_type'] ?? 'N/A') }}</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Family Values</div>
                                    <div class="info-value-premium">{{ ucfirst($fd['family_value'] ?? 'N/A') }}</div>
                                </div>
                            </div>
                            @if(!empty($fd['about_family']))
                            <div class="col-12">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">About Family</div>
                                    <div class="info-value-premium small text-secondary mt-1">{{ $fd['about_family'] }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Lifestyle & Habits -->
                    <div class="premium-profile-card p-4 bg-light border-0 rounded-4 mt-4">
                        <div class="section-title-premium mt-0"><i class="fas fa-heart-pulse me-1"></i>Lifestyle & Habits</div>
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Diet Preference</div>
                                    <div class="info-value-premium">{{ ucfirst($lf['diet'] ?? 'N/A') }}</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Smoking habits</div>
                                    <div class="info-value-premium">{{ ucfirst($lf['smoking'] ?? 'No') }}</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Drinking habits</div>
                                    <div class="info-value-premium">{{ ucfirst($lf['drinking'] ?? 'No') }}</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Hobbies</div>
                                    <div class="info-value-premium">{{ $lf['hobbies'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Partner Preferences -->
                    @if(!empty($pp))
                    <div class="premium-profile-card p-4 bg-light border-0 rounded-4 mt-4">
                        <div class="section-title-premium mt-0"><i class="fas fa-sliders me-1"></i>Partner Preferences</div>
                        <div class="row g-3">
                            <div class="col-md-4 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Preferred Age Range</div>
                                    <div class="info-value-premium">{{ $pp['age_min'] ?? '18' }} – {{ $pp['age_max'] ?? '60' }} yrs</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Preferred Min Height</div>
                                    <div class="info-value-premium">{{ $pp['height_min'] ?? 'Any' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Preferred Religion</div>
                                    <div class="info-value-premium">{{ $pp['religion'] ?? 'Any' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Preferred Caste</div>
                                    <div class="info-value-premium">{{ $pp['caste'] ?? 'Any' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Preferred Education</div>
                                    <div class="info-value-premium">{{ $pp['education'] ?? 'Any' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Preferred Income</div>
                                    <div class="info-value-premium">{{ str_replace('_', '-', strtoupper($pp['income'] ?? 'Any')) }}</div>
                                </div>
                            </div>
                            @if(!empty($pp['about_partner']))
                            <div class="col-12">
                                <div class="info-item-box bg-white">
                                    <div class="info-label-premium">Ideal Partner Description</div>
                                    <div class="info-value-premium small text-secondary mt-1">{{ $pp['about_partner'] }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- User Photo Gallery Section -->
                    <div class="mt-4 p-4 rounded-4 bg-white border border-light shadow-sm">
                        <div class="section-title-premium mt-0"><i class="fas fa-images me-1"></i> Seekers Photo Gallery</div>
                        @if(!empty($pd['photos']))
                            <div class="row photo-gallery-grid g-3">
                                @foreach($pd['photos'] as $idx => $photo)
                                    <div class="col-md-3 col-6">
                                        <div class="position-relative overflow-hidden rounded-3 border border-light" style="border-radius:18px;">
                                            <img src="{{ asset('storage/' . $photo) }}" alt="Photo {{ $idx+1 }}" class="img-fluid rounded">
                                            @if($idx === 0)
                                                <span class="position-absolute top-0 start-0 m-2 badge bg-danger text-white rounded-pill px-2 py-1 small fw-bold">Primary</span>
                                            @else
                                                <span class="position-absolute top-0 start-0 m-2 badge bg-secondary text-white rounded-pill px-2 py-1 small fw-bold">Photo {{ $idx+1 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-light border-0 text-center py-4 rounded-3 mb-0">
                                <i class="fas fa-image fa-2x text-muted opacity-40 mb-2"></i>
                                <p class="text-secondary small mb-0">No photos have been uploaded by this seeker yet.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Admin audit logs information card -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 bg-light p-3" style="border-radius: 16px;">
                                <div class="card-body py-2 px-3 small d-flex flex-wrap justify-content-between align-items-center text-muted">
                                    <div>
                                        <i class="fas fa-calendar-alt me-1"></i> <strong>Created At:</strong> {{ $profile->created_at->format('M d, Y \a\t H:i') }}
                                    </div>
                                    <div>
                                        <i class="fas fa-pen-fancy me-1"></i> <strong>Last Modified:</strong> {{ $profile->updated_at->format('M d, Y \a\t H:i') }}
                                    </div>
                                    @if($profile->verified_at)
                                        <div>
                                            <i class="fas fa-certificate text-success me-1"></i> <strong>Verified At:</strong> {{ $profile->verified_at->format('M d, Y') }}
                                        </div>
                                    @endif
                                    @if($profile->rejection_reason)
                                        <div class="w-100 mt-2 border-top border-light pt-2 text-danger">
                                            <i class="fas fa-circle-exclamation me-1"></i> <strong>Previous Rejection Reason:</strong> "{{ $profile->rejection_reason }}"
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius:20px;">
            <div class="modal-header border-0 pb-0 mt-2 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-circle-xmark text-danger me-2"></i>Reject Matrimony Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; outline: none; border: none; background: transparent;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="POST" action="{{ route('admin.matrimony.reject', $profile->id) }}">
                @csrf
                <div class="modal-body px-4 py-3">
                    <p class="text-secondary small mb-3">Provide a clear description for why this matrimony seeker account is rejected. The user will be notified of this reason.</p>
                    <div class="form-group">
                        <label for="rejection_reason" class="fw-bold small text-dark mb-2">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                                  rows="4" required placeholder="Describe the reason (e.g. Invalid Caste Certificate, missing basic contact details, inappropriate primary photo etc.)" style="border-radius:12px; border-color:#e2e8f0;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection