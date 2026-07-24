@extends('admin.layouts.app')

@section('title', 'Edit Matrimony Profile')

@section('content')
<style>
    .required-star {
        color: #dc3545;
        font-weight: bold;
        margin-left: 2px;
    }
    .pincode-input-wrapper {
        position: relative;
    }
    .pincode-input-wrapper .location-icon {
        position: absolute;
        right: 15px;
        top: 12px;
        color: #84144f;
        font-size: 1.1rem;
        cursor: pointer;
        z-index: 10;
    }
    .caste-loader {
        margin-left: 10px;
        display: none;
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

    // Names fallback parsing
    $firstName = $pd['first_name'] ?? '';
    $middleName = $pd['middle_name'] ?? '';
    $lastName = $pd['last_name'] ?? '';
    
    if (empty($firstName) && !empty($profile->user->name)) {
        $parts = explode(' ', trim($profile->user->name));
        $firstName = $parts[0] ?? '';
        if (count($parts) > 2) {
            $middleName = $parts[1] ?? '';
            $lastName = end($parts);
        } elseif (count($parts) == 2) {
            $lastName = $parts[1] ?? '';
        }
    }

    // Caste and Sub-Caste from DB
    $savedCaste = $pd['caste'] ?? ($pd['religion'][1] ?? '');
    $savedSubCaste = $pd['sub_caste'] ?? '';
    
    // Star details
    $savedStar = $pd['star'] ?? ($pd['star_details'][0] ?? '');
    $savedRaasi = $pd['raasi'] ?? ($pd['star_details'][1] ?? '');
    
    $rawManglik = $pd['manglik'] ?? (isset($pd['star_details'][2]) ? str_replace('manglik-', '', $pd['star_details'][2]) : 'No');
    $savedManglik = ucfirst(strtolower(trim($rawManglik)));
    if ($savedManglik == 'Don\'t know' || $savedManglik == 'Don\'t Know' || $savedManglik == 'Dont know') {
        $savedManglik = "Don't Know";
    }

    $savedDosh = ucfirst(strtolower(trim($pd['dosh'] ?? 'No')));
    if ($savedDosh == 'Don\'t know' || $savedDosh == 'Don\'t Know' || $savedDosh == 'Dont know') {
        $savedDosh = "Don't Know";
    }
@endphp

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Matrimony Profile - {{ $profile->user->name ?? 'Seeker' }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.matrimony.index') }}">Matrimony Management</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.matrimony.show', $profile->id) }}">Profile Details</a></li>
        <li class="breadcrumb-item active">Edit Profile</li>
    </ol>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-left-danger">
            <h6 class="font-weight-bold">Please correct the following errors:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.matrimony.update', $profile->id) }}" method="POST" enctype="multipart/form-data" id="adminMatrimonyForm">
        @csrf
        @method('PUT')

        <!-- SECTION 1: Admin & Account Configuration -->
        <div class="card mb-4 shadow-sm border-left-primary">
            <div class="card-header bg-light py-3">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-cogs me-2 text-primary"></i>1. Account & Moderation Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold small">Owner User</label>
                        <input type="text" class="form-control" value="{{ $profile->user->name ?? 'N/A' }} ({{ $profile->user->email ?? 'N/A' }} - ID: {{ $profile->user_id }})" disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="approval_status" class="form-label font-weight-bold small">Approval Status <span class="required-star">*</span></label>
                        <select class="form-select" id="approval_status" name="approval_status" required>
                            <option value="pending" {{ old('approval_status', $profile->approval_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('approval_status', $profile->approval_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('approval_status', $profile->approval_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="profile_expires_at" class="form-label font-weight-bold small">Profile Expires At</label>
                        <input type="date" class="form-control" id="profile_expires_at" name="profile_expires_at" value="{{ old('profile_expires_at', $profile->profile_expires_at ? \Carbon\Carbon::parse($profile->profile_expires_at)->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Personal Details -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light py-3">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-user me-2 text-primary"></i>2. Personal Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold small">First Name <span class="required-star">*</span></label>
                        <input type="text" name="personal_details[first_name]" class="form-control" placeholder="Enter first name" value="{{ old('personal_details.first_name', $firstName) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold small">Middle Name</label>
                        <input type="text" name="personal_details[middle_name]" class="form-control" placeholder="Enter middle name" value="{{ old('personal_details.middle_name', $middleName) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold small">Last Name <span class="required-star">*</span></label>
                        <input type="text" name="personal_details[last_name]" class="form-control" placeholder="Enter last name" value="{{ old('personal_details.last_name', $lastName) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold small">Profile Created By <span class="required-star">*</span></label>
                        <select name="personal_details[profile_created_by]" class="form-select" required>
                            <option value="">Select Creator</option>
                            @php
                                $creatorList = ['Self', 'Parent', 'Sibling', 'Relative', 'Friend'];
                                $savedCreator = ucfirst(strtolower($pd['profile_created_by'] ?? ''));
                            @endphp
                            @foreach($creatorList as $creator)
                                <option value="{{ $creator }}" {{ old('personal_details.profile_created_by', $savedCreator) == $creator ? 'selected' : '' }}>{{ $creator }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="gender" class="form-label font-weight-bold small">Gender <span class="required-star">*</span></label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="" disabled>Select Gender</option>
                            @php
                                $genderVal = ucfirst(strtolower($profile->gender ?? ''));
                            @endphp
                            <option value="Male" {{ old('gender', $genderVal) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $genderVal) == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="date_of_birth" class="form-label font-weight-bold small">Date of Birth <span class="required-star">*</span></label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $profile->date_of_birth ? \Carbon\Carbon::parse($profile->date_of_birth)->format('Y-m-d') : '') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="age" class="form-label font-weight-bold small">Age <span class="required-star">*</span></label>
                        <input type="number" class="form-control" id="age" name="age" value="{{ old('age', $profile->age) }}" min="18" max="100" placeholder="e.g. 24" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="height" class="form-label font-weight-bold small">Height (Feet)</label>
                        <select name="height" id="height" class="form-select">
                            <option value="">Select Height</option>
                            @for ($ft = 4.0; $ft <= 7.0; $ft += 0.1)
                                <option value="{{ sprintf('%.1f', $ft) }}" {{ old('height', $profile->height) == sprintf('%.1f', $ft) ? 'selected' : '' }}>{{ sprintf('%.1f', $ft) }} ft</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="weight" class="form-label font-weight-bold small">Weight (kg)</label>
                        <input type="number" class="form-control" id="weight" name="weight" value="{{ old('weight', $profile->weight) }}" placeholder="e.g. 65" min="30" max="150">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="complexion" class="form-label font-weight-bold small">Complexion</label>
                        <select name="complexion" id="complexion" class="form-select">
                            <option value="">Select Complexion</option>
                            <option value="Fair" {{ old('complexion', $profile->complexion) == 'Fair' ? 'selected' : '' }}>Fair</option>
                            <option value="Wheatish" {{ old('complexion', $profile->complexion) == 'Wheatish' ? 'selected' : '' }}>Wheatish</option>
                            <option value="Dark" {{ old('complexion', $profile->complexion) == 'Dark' ? 'selected' : '' }}>Dark</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="marital_status" class="form-label font-weight-bold small">Marital Status</label>
                        <select class="form-select" id="marital_status" name="personal_details[marital_status]">
                            <option value="">Select Status</option>
                            <option value="Never Married" {{ old('personal_details.marital_status', $pd['marital_status'] ?? '') == 'Never Married' ? 'selected' : '' }}>Never Married</option>
                            <option value="Divorced" {{ old('personal_details.marital_status', $pd['marital_status'] ?? '') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Widowed" {{ old('personal_details.marital_status', $pd['marital_status'] ?? '') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Awaiting Divorce" {{ old('personal_details.marital_status', $pd['marital_status'] ?? '') == 'Awaiting Divorce' ? 'selected' : '' }}>Awaiting Divorce</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="physical_status" class="form-label font-weight-bold small">Physical Status</label>
                        <select class="form-select" id="physical_status" name="physical_status">
                            <option value="Normal" {{ old('physical_status', $profile->physical_status ?? 'Normal') == 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Physically Challenged" {{ old('physical_status', $profile->physical_status) == 'Physically Challenged' ? 'selected' : '' }}>Physically Challenged</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="mother_tongue" class="form-label font-weight-bold small">Language</label>
                        <select class="form-select" id="mother_tongue" name="personal_details[mother_tongue]">
                            <option value="">Select Language</option>
                            @php
                                $langList = ['Hindi', 'English', 'Marathi', 'Gujarati', 'Punjabi', 'Bengali', 'Tamil', 'Telugu', 'Kannada', 'Malayalam', 'Urdu', 'Other'];
                                $savedLang = $pd['mother_tongue'] ?? $pd['language'] ?? '';
                            @endphp
                            @foreach($langList as $lang)
                                <option value="{{ $lang }}" {{ old('personal_details.mother_tongue', $savedLang) == $lang ? 'selected' : '' }}>{{ $lang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="citizenship" class="form-label font-weight-bold small">Citizenship</label>
                        <select class="form-select" id="citizenship" name="personal_details[citizenship]">
                            <option value="Indian" {{ old('personal_details.citizenship', $pd['citizenship'] ?? 'Indian') == 'Indian' ? 'selected' : '' }}>Indian</option>
                            <option value="NRI" {{ old('personal_details.citizenship', $pd['citizenship'] ?? '') == 'NRI' ? 'selected' : '' }}>NRI</option>
                            <option value="Other" {{ old('personal_details.citizenship', $pd['citizenship'] ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="blood_group" class="form-label font-weight-bold small">Blood Group</label>
                        <select class="form-select" id="blood_group" name="personal_details[blood_group]">
                            <option value="">Select Blood Group</option>
                            @php
                                $bgList = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                            @endphp
                            @foreach($bgList as $bg)
                                <option value="{{ $bg }}" {{ old('personal_details.blood_group', $pd['blood_group'] ?? '') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="referral_name" class="form-label font-weight-bold small">Referral Name</label>
                        <input type="text" class="form-control" id="referral_name" name="personal_details[referral_name]" value="{{ old('personal_details.referral_name', $pd['referral_name'] ?? $pd['refferal_name'] ?? '') }}" placeholder="Enter referral name">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Religious & Horoscope Details -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light py-3">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-dharmachakra me-2 text-primary"></i>3. Caste & Astrological Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="casteSelect" class="form-label font-weight-bold small">Caste <span class="required-star">*</span><span class="spinner-border text-primary spinner-border-sm caste-loader" id="casteLoader"></span></label>
                        <select name="personal_details[caste]" class="form-select" id="casteSelect" required>
                            <option value="">Select Caste</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="subCasteSelect" class="form-label font-weight-bold small">Sub-Caste <span class="required-star">*</span><span class="spinner-border text-primary spinner-border-sm caste-loader" id="subCasteLoader"></span></label>
                        <select name="personal_details[sub_caste]" class="form-select" id="subCasteSelect" required disabled>
                            <option value="">Select Sub-Caste</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="star" class="form-label font-weight-bold small">Star</label>
                        <select name="personal_details[star]" id="star" class="form-select">
                            <option value="">Select Star</option>
                            @php
                                $starsList = ['Ashwini', 'Bharani', 'Krittika', 'Rohini', 'Mrigashirsha', 'Ardra', 'Punarvasu', 'Pushya', 'Ashlesha', 'Magha', 'Purva Phalguni', 'Uttara Phalguni', 'Hasta', 'Chitra', 'Swati', 'Vishakha', 'Anuradha', 'Jyeshtha', 'Mula', 'Purva Ashadha', 'Uttara Ashadha', 'Shravana', 'Dhanishta', 'Shatabhisha', 'Purva Bhadrapada', 'Uttara Bhadrapada', 'Revati'];
                            @endphp
                            @foreach($starsList as $st)
                                <option value="{{ $st }}" {{ old('personal_details.star', $savedStar) == $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="raasi" class="form-label font-weight-bold small">Raasi</label>
                        <select name="personal_details[raasi]" id="raasi" class="form-select">
                            <option value="">Select Raasi</option>
                            @php
                                $raasiList = ['Mesha (Aries)', 'Vrishabha (Taurus)', 'Mithuna (Gemini)', 'Karka (Cancer)', 'Simha (Leo)', 'Kanya (Virgo)', 'Tula (Libra)', 'Vrishchika (Scorpio)', 'Dhanu (Sagittarius)', 'Makara (Capricorn)', 'Kumbha (Aquarius)', 'Meena (Pisces)'];
                            @endphp
                            @foreach($raasiList as $rs)
                                <option value="{{ $rs }}" {{ old('personal_details.raasi', $savedRaasi) == $rs ? 'selected' : '' }}>{{ $rs }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="manglik" class="form-label font-weight-bold small">Manglik</label>
                        <select name="personal_details[manglik]" id="manglik" class="form-select">
                            <option value="No" {{ old('personal_details.manglik', $savedManglik) == 'No' ? 'selected' : '' }}>No</option>
                            <option value="Yes" {{ old('personal_details.manglik', $savedManglik) == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="Anshik (Partial)" {{ old('personal_details.manglik', $savedManglik) == 'Anshik (Partial)' ? 'selected' : '' }}>Anshik (Partial)</option>
                            <option value="Don't Know" {{ old('personal_details.manglik', $savedManglik) == "Don't Know" ? 'selected' : '' }}>Don't Know</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="dosh" class="form-label font-weight-bold small">Dosh</label>
                        <select name="personal_details[dosh]" id="dosh" class="form-select">
                            <option value="No" {{ old('personal_details.dosh', $savedDosh) == 'No' ? 'selected' : '' }}>No</option>
                            <option value="Yes" {{ old('personal_details.dosh', $savedDosh) == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="Don't Know" {{ old('personal_details.dosh', $savedDosh) == "Don't Know" ? 'selected' : '' }}>Don't Know</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: Education & Career Details -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light py-3">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-graduation-cap me-2 text-primary"></i>4. Education & Career Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="highest_qualification" class="form-label font-weight-bold small">Highest Qualification <span class="required-star">*</span></label>
                        <select class="form-select" id="highest_qualification" name="education_details[highest_qualification]" required>
                            <option value="" disabled>Select Qualification</option>
                            @foreach($educations as $edu)
                                <option value="{{ $edu->highest_qualification }}" {{ old('education_details.highest_qualification', $ed['highest_qualification'] ?? '') == $edu->highest_qualification ? 'selected' : '' }}>{{ $edu->highest_qualification }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="college_name" class="form-label font-weight-bold small">College / University</label>
                        <input type="text" class="form-control" id="college_name" name="education_details[college_name]" value="{{ old('education_details.college_name', $ed['college_name'] ?? '') }}" placeholder="Enter college name">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="employment_type" class="form-label font-weight-bold small">Employment Type <span class="required-star">*</span></label>
                        <select name="personal_details[employment_type]" id="employment_type" class="form-select" required>
                            <option value="">Select Employment Type</option>
                            @php
                                $empList = ['Private Sector', 'Government/Public Sector', 'Civil Service', 'Defense', 'Owner', 'Self Employed', 'Not Working'];
                            @endphp
                            @foreach($empList as $emp)
                                <option value="{{ $emp }}" {{ old('personal_details.employment_type', $pd['employment_type'] ?? '') == $emp ? 'selected' : '' }}>{{ $emp }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="occupation" class="form-label font-weight-bold small">Job Title / Occupation</label>
                        <input type="text" class="form-control" id="occupation" name="professional_details[occupation]" value="{{ old('professional_details.occupation', $pro['occupation'] ?? '') }}" placeholder="e.g. Software Engineer">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="company_name" class="form-label font-weight-bold small">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="professional_details[company_name]" value="{{ old('professional_details.company_name', $pro['company_name'] ?? '') }}" placeholder="e.g. TCS Ltd">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="annual_income" class="form-label font-weight-bold small">Annual Income (Lac)</label>
                        <input type="text" class="form-control" id="annual_income" name="personal_details[annual_income]" value="{{ old('personal_details.annual_income', $pd['annual_income'] ?? '') }}" placeholder="e.g. 5 Lakh">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 5: Family Information -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light py-3">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-users me-2 text-primary"></i>5. Family Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="father_occupation" class="form-label font-weight-bold small">Father's Occupation</label>
                        <input type="text" class="form-control" id="father_occupation" name="family_details[father_occupation]" value="{{ old('family_details.father_occupation', $fd['father_occupation'] ?? '') }}" placeholder="Enter father's occupation">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="mother_occupation" class="form-label font-weight-bold small">Mother's Occupation</label>
                        <input type="text" class="form-control" id="mother_occupation" name="family_details[mother_occupation]" value="{{ old('family_details.mother_occupation', $fd['mother_occupation'] ?? '') }}" placeholder="Enter mother's occupation">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="family_type" class="form-label font-weight-bold small">Family Type <span class="required-star">*</span></label>
                        <select class="form-select" id="family_type" name="family_details[family_type]" required>
                            <option value="">Select Family Type</option>
                            <option value="Nuclear" {{ old('family_details.family_type', $fd['family_type'] ?? '') == 'Nuclear' ? 'selected' : '' }}>Nuclear</option>
                            <option value="Joint" {{ old('family_details.family_type', $fd['family_type'] ?? '') == 'Joint' ? 'selected' : '' }}>Joint</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="family_class" class="form-label font-weight-bold small">Family Class</label>
                        <select class="form-select" id="family_class" name="family_details[family_class]">
                            <option value="">Select Family Class</option>
                            <option value="Rich" {{ old('family_details.family_class', $fd['family_class'] ?? '') == 'Rich' ? 'selected' : '' }}>Rich</option>
                            <option value="Upper Middle Class" {{ old('family_details.family_class', $fd['family_class'] ?? '') == 'Upper Middle Class' ? 'selected' : '' }}>Upper Middle Class</option>
                            <option value="Middle Class" {{ old('family_details.family_class', $fd['family_class'] ?? '') == 'Middle Class' ? 'selected' : '' }}>Middle Class</option>
                            <option value="Lower Middle Class" {{ old('family_details.family_class', $fd['family_class'] ?? '') == 'Lower Middle Class' ? 'selected' : '' }}>Lower Middle Class</option>
                            <option value="Lower Class" {{ old('family_details.family_class', $fd['family_class'] ?? '') == 'Lower Class' ? 'selected' : '' }}>Lower Class</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="family_value" class="form-label font-weight-bold small">Family Value</label>
                        <select class="form-select" id="family_value" name="family_details[family_value]">
                            <option value="">Select Family Value</option>
                            <option value="Orthodox" {{ old('family_details.family_value', $fd['family_value'] ?? '') == 'Orthodox' ? 'selected' : '' }}>Orthodox</option>
                            <option value="Traditional" {{ old('family_details.family_value', $fd['family_value'] ?? '') == 'Traditional' ? 'selected' : '' }}>Traditional</option>
                            <option value="Moderate" {{ old('family_details.family_value', $fd['family_value'] ?? '') == 'Moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="Liberal" {{ old('family_details.family_value', $fd['family_value'] ?? '') == 'Liberal' ? 'selected' : '' }}>Liberal</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 6: Lifestyle Habits -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light py-3">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-heart-pulse me-2 text-primary"></i>6. Lifestyle Habits</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="diet" class="form-label font-weight-bold small">Diet</label>
                        <select class="form-select" id="diet" name="lifestyle_details[diet]">
                            <option value="">Select Diet</option>
                            <option value="Vegetarian" {{ old('lifestyle_details.diet', $lf['diet'] ?? '') == 'Vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                            <option value="Non-Vegetarian" {{ old('lifestyle_details.diet', $lf['diet'] ?? '') == 'Non-Vegetarian' ? 'selected' : '' }}>Non-Vegetarian</option>
                            <option value="Eggetarian" {{ old('lifestyle_details.diet', $lf['diet'] ?? '') == 'Eggetarian' ? 'selected' : '' }}>Eggetarian</option>
                            <option value="Vegan" {{ old('lifestyle_details.diet', $lf['diet'] ?? '') == 'Vegan' ? 'selected' : '' }}>Vegan</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="smoking" class="form-label font-weight-bold small">Smoking</label>
                        <select class="form-select" id="smoking" name="lifestyle_details[smoking]">
                            <option value="">Select Smoking</option>
                            <option value="No" {{ old('lifestyle_details.smoking', $lf['smoking'] ?? '') == 'No' ? 'selected' : '' }}>No</option>
                            <option value="Yes" {{ old('lifestyle_details.smoking', $lf['smoking'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="Occasionally" {{ old('lifestyle_details.smoking', $lf['smoking'] ?? '') == 'Occasionally' ? 'selected' : '' }}>Occasionally</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="drinking" class="form-label font-weight-bold small">Drinking</label>
                        <select class="form-select" id="drinking" name="lifestyle_details[drinking]">
                            <option value="">Select Drinking</option>
                            <option value="No" {{ old('lifestyle_details.drinking', $lf['drinking'] ?? '') == 'No' ? 'selected' : '' }}>No</option>
                            <option value="Yes" {{ old('lifestyle_details.drinking', $lf['drinking'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="Occasionally" {{ old('lifestyle_details.drinking', $lf['drinking'] ?? '') == 'Occasionally' ? 'selected' : '' }}>Occasionally</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 7: Location details -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light py-3">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-location-dot me-2 text-primary"></i>7. Location Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="address" class="form-label font-weight-bold small">Address</label>
                        <input type="text" class="form-control" id="address" name="location_details[address]" value="{{ old('location_details.address', $ld['address'] ?? '') }}" placeholder="Enter address details">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="pincodeInput" class="form-label font-weight-bold small">Pincode <span class="required-star">*</span></label>
                        <div class="pincode-input-wrapper">
                            <input type="text" class="form-control" id="pincodeInput" name="location_details[pincode]" value="{{ old('location_details.pincode', $ld['pincode'] ?? '') }}" placeholder="Enter 6-digit Pincode" maxlength="6" required>
                            <i class="fa-solid fa-location-dot location-icon" title="Autofetch location coords & address"></i>
                        </div>
                        <input type="hidden" name="latitude" id="matrimonyLatitudeInput" value="{{ old('latitude', $ld['latitude'] ?? '') }}">
                        <input type="hidden" name="longitude" id="matrimonyLongitudeInput" value="{{ old('longitude', $ld['longitude'] ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="countryInput" class="form-label font-weight-bold small">Country <span class="required-star">*</span></label>
                        @php
                            $savedCountry = $ld['country'] ?? 'India';
                        @endphp
                        <select class="form-select" id="countryInput" name="location_details[country]" required>
                            <option value="India" {{ old('location_details.country', $savedCountry) == 'India' ? 'selected' : '' }}>India</option>
                            <option value="USA" {{ old('location_details.country', $savedCountry) == 'USA' ? 'selected' : '' }}>USA</option>
                            <option value="UK" {{ old('location_details.country', $savedCountry) == 'UK' ? 'selected' : '' }}>UK</option>
                            <option value="Canada" {{ old('location_details.country', $savedCountry) == 'Canada' ? 'selected' : '' }}>Canada</option>
                            <option value="Australia" {{ old('location_details.country', $savedCountry) == 'Australia' ? 'selected' : '' }}>Australia</option>
                            <option value="Other" {{ old('location_details.country', $savedCountry) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="stateInput" class="form-label font-weight-bold small">State <span class="required-star">*</span></label>
                        <input type="text" class="form-control" id="stateInput" name="location_details[state]" value="{{ old('location_details.state', $ld['state'] ?? '') }}" placeholder="Enter State" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="cityInput" class="form-label font-weight-bold small">City <span class="required-star">*</span></label>
                        <input type="text" class="form-control" id="cityInput" name="location_details[city]" value="{{ old('location_details.city', $ld['city'] ?? '') }}" placeholder="Enter City" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="talukaInput" class="form-label font-weight-bold small">Taluka</label>
                        <input type="text" class="form-control" id="talukaInput" name="location_details[taluka]" value="{{ old('location_details.taluka', $ld['taluka'] ?? '') }}" placeholder="Enter Taluka">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="villageInput" class="form-label font-weight-bold small">Village</label>
                        <input type="text" class="form-control" id="villageInput" name="location_details[village]" value="{{ old('location_details.village', $ld['village'] ?? '') }}" placeholder="Enter Village">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 8: Partner Expectations -->
        <?php /*<div class="card mb-4 shadow-sm">
            <div class="card-header bg-light py-3">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-sliders me-2 text-primary"></i>8. Partner Expectations</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="pref_age_min" class="form-label font-weight-bold small">Preferred Min Age</label>
                        <input type="number" class="form-control" id="pref_age_min" name="partner_preferences[age_min]" value="{{ old('partner_preferences.age_min', $pp['age_min'] ?? 18) }}" min="18" max="100">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="pref_age_max" class="form-label font-weight-bold small">Preferred Max Age</label>
                        <input type="number" class="form-control" id="pref_age_max" name="partner_preferences[age_max]" value="{{ old('partner_preferences.age_max', $pp['age_max'] ?? 35) }}" min="18" max="100">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="pref_height" class="form-label font-weight-bold small">Preferred Height (min)</label>
                        <input type="text" class="form-control" id="pref_height" name="partner_preferences[height_min]" value="{{ old('partner_preferences.height_min', $pp['height_min'] ?? '') }}" placeholder="e.g. 5.2 ft">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="pref_religion" class="form-label font-weight-bold small">Preferred Religion</label>
                        <input type="text" class="form-control" id="pref_religion" name="partner_preferences[religion]" value="{{ old('partner_preferences.religion', $pp['religion'] ?? 'Hindu') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="pref_caste" class="form-label font-weight-bold small">Preferred Caste</label>
                        <input type="text" class="form-control" id="pref_caste" name="partner_preferences[caste]" value="{{ old('partner_preferences.caste', $pp['caste'] ?? 'Any') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="pref_education" class="form-label font-weight-bold small">Preferred Education</label>
                        <input type="text" class="form-control" id="pref_education" name="partner_preferences[education]" value="{{ old('partner_preferences.education', $pp['education'] ?? 'Any') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="pref_income" class="form-label font-weight-bold small">Preferred Income</label>
                        <input type="text" class="form-control" id="pref_income" name="partner_preferences[income]" value="{{ old('partner_preferences.income', $pp['income'] ?? 'Any') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="pref_about" class="form-label font-weight-bold small">Ideal Partner Description</label>
                        <textarea class="form-control" id="pref_about" name="partner_preferences[about_partner]" rows="2" placeholder="Write description about partner expectations...">{{ old('partner_preferences.about_partner', $pp['about_partner'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div> */?>

        <!-- SECTION 9: Gallery Photos -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light py-3">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-images me-2 text-primary"></i>8. Gallery Photos</h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label font-weight-bold small d-block">Current Photos (Check to Delete)</label>
                        @if(!empty($pd['photos']))
                            <div class="row g-3">
                                @foreach($pd['photos'] as $idx => $photo)
                                    <div class="col-md-2 col-4 text-center">
                                        <div class="position-relative border rounded p-1 mb-2">
                                            <img src="{{ asset('storage/' . $photo) }}" alt="Photo" class="img-fluid rounded" style="height: 90px; width: 100%; object-fit: cover;">
                                        </div>
                                        <div class="form-check justify-content-center d-flex">
                                            <input class="form-check-input" type="checkbox" name="deleted_photos[]" value="{{ $photo }}" id="del_photo_{{ $idx }}">
                                            <label class="form-check-label small ms-1 text-danger font-weight-bold" for="del_photo_{{ $idx }}">
                                                Delete
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small">No photos uploaded yet.</p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="photosInput" class="form-label font-weight-bold small">Upload New Photos</label>
                        <input type="file" class="form-control" id="photosInput" name="photos[]" accept="image/*" multiple>
                        <small class="text-muted small">Select new photos to add (max 5MB per photo). Keep at least 2 photos total.</small>
                        <div class="photo-preview-container" id="photoPreviewContainer" style="display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4 mb-5">
            <a href="{{ route('admin.matrimony.show', $profile->id) }}" class="btn btn-secondary rounded-pill px-4">Cancel</a>
            <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // AJAX Caste & Sub-Caste dynamic loader with saved value pre-selection
    const casteSelect = document.getElementById('casteSelect');
    const subCasteSelect = document.getElementById('subCasteSelect');
    const casteLoader = document.getElementById('casteLoader');
    const subCasteLoader = document.getElementById('subCasteLoader');

    const savedCasteName = @json($savedCaste);
    const savedSubCasteName = @json($savedSubCaste);

    document.addEventListener('DOMContentLoaded', function() {
        loadCastes();
    });

    function loadCastes() {
        casteLoader.style.display = 'inline-block';
        casteSelect.disabled = true;

        fetch('/api/matrimony/casts')
            .then(res => res.json())
            .then(res => {
                casteLoader.style.display = 'none';
                casteSelect.disabled = false;
                
                if (res.success && res.data && res.data.casts) {
                    res.data.casts.forEach(cast => {
                        const opt = document.createElement('option');
                        opt.value = cast.name;
                        opt.dataset.id = cast.id;
                        opt.textContent = cast.name;
                        
                        if (savedCasteName && cast.name.toLowerCase() === savedCasteName.toLowerCase()) {
                            opt.selected = true;
                        }
                        casteSelect.appendChild(opt);
                    });

                    // Trigger subcaste load if caste is preselected
                    if (casteSelect.value) {
                        triggerSubCasteLoad();
                    }
                }
            })
            .catch(err => {
                casteLoader.style.display = 'none';
                casteSelect.disabled = false;
                console.error('Failed to load castes:', err);
            });
    }

    function triggerSubCasteLoad() {
        const selectedOption = casteSelect.options[casteSelect.selectedIndex];
        const castId = selectedOption.dataset.id;

        subCasteSelect.innerHTML = '<option value="">Select Sub-Caste</option>';
        subCasteSelect.disabled = true;

        if (!castId) return;

        subCasteLoader.style.display = 'inline-block';

        fetch(`/api/matrimony/casts/${castId}/subcasts`)
            .then(res => res.json())
            .then(res => {
                subCasteLoader.style.display = 'none';
                subCasteSelect.disabled = false;

                if (res.success && res.data && res.data.sub_casts) {
                    res.data.sub_casts.forEach(sc => {
                        const opt = document.createElement('option');
                        opt.value = sc.name;
                        opt.textContent = sc.name;
                        if (savedSubCasteName && sc.name.toLowerCase() === savedSubCasteName.toLowerCase()) {
                            opt.selected = true;
                        }
                        subCasteSelect.appendChild(opt);
                    });
                } else {
                    const opt = document.createElement('option');
                    opt.value = casteSelect.value;
                    opt.textContent = casteSelect.value;
                    if (savedSubCasteName && casteSelect.value.toLowerCase() === savedSubCasteName.toLowerCase()) {
                        opt.selected = true;
                    }
                    subCasteSelect.appendChild(opt);
                }
            })
            .catch(err => {
                subCasteLoader.style.display = 'none';
                subCasteSelect.disabled = false;
                console.error('Failed to load subcastes:', err);
                
                const opt = document.createElement('option');
                opt.value = casteSelect.value;
                opt.textContent = casteSelect.value;
                subCasteSelect.appendChild(opt);
            });
    }

    casteSelect.addEventListener('change', triggerSubCasteLoad);

    // Pincode dynamic postal API & Geolocation auto-fetch lookup
    function lookupPincode(isClick = false) {
        const pinInput = document.getElementById('pincodeInput');
        const pin = pinInput.value.trim();
        const countrySelect = document.getElementById('countryInput');
        const stateInput = document.getElementById('stateInput');
        const cityInput = document.getElementById('cityInput');
        const talukaInput = document.getElementById('talukaInput');
        const villageInput = document.getElementById('villageInput');
        const addressInput = document.getElementById('address');

        if (pin.length === 6 && /^\d+$/.test(pin)) {
            const icon = document.querySelector('.location-icon');
            icon.classList.remove('fa-location-dot');
            icon.classList.add('fa-spinner', 'fa-spin');
            
            fetch(`https://api.postalpincode.in/pincode/${pin}`)
                .then(res => res.json())
                .then(data => {
                    icon.classList.remove('fa-spinner', 'fa-spin');
                    icon.classList.add('fa-location-dot');
                    
                    if (data && data[0] && data[0].Status === 'Success') {
                        const po = data[0].PostOffice[0];
                        
                        if (countrySelect) countrySelect.value = "India";
                        if (stateInput) stateInput.value = po.State || '';
                        if (cityInput) cityInput.value = po.District || '';
                        if (talukaInput) talukaInput.value = (po.Block && po.Block !== 'N.A.') ? po.Block : '';
                        if (villageInput) villageInput.value = po.Name || '';
                        if (addressInput && po.Name && po.District) {
                            addressInput.value = po.Name + ", " + po.District;
                        }
                        
                        pinInput.classList.add('is-valid');
                        pinInput.classList.remove('is-invalid');
                    } else {
                        pinInput.value = '';
                        pinInput.classList.remove('is-valid');
                        pinInput.classList.add('is-invalid');
                        if (stateInput) stateInput.value = '';
                        if (cityInput) cityInput.value = '';
                        if (talukaInput) talukaInput.value = '';
                        if (villageInput) villageInput.value = '';
                        alert("Invalid Pincode or no records found.");
                    }
                })
                .catch(err => {
                    icon.classList.remove('fa-spinner', 'fa-spin');
                    icon.classList.add('fa-location-dot');
                    console.error('Pincode lookup error:', err);
                    pinInput.value = '';
                    pinInput.classList.remove('is-valid');
                    pinInput.classList.add('is-invalid');
                    if (stateInput) stateInput.value = '';
                    if (cityInput) cityInput.value = '';
                    if (talukaInput) talukaInput.value = '';
                    if (villageInput) villageInput.value = '';
                    alert("Error searching pincode. Please try again.");
                });
        } else {
            if (isClick === true) {
                pinInput.value = '';
                pinInput.classList.remove('is-valid');
                pinInput.classList.add('is-invalid');
                alert("Please enter a valid 6-digit Pincode first.");
            }
        }
    }

    document.getElementById('pincodeInput').addEventListener('input', function() {
        lookupPincode(false);
    });

    const locIcon = document.querySelector('.location-icon');
    if (locIcon) {
        locIcon.addEventListener('click', function() {
            locIcon.className = 'fa-solid fa-spinner fa-spin text-primary';
            
            navigator.geolocation.getCurrentPosition(function(position) {
                locIcon.className = 'fa-solid fa-circle-check text-success';
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                document.getElementById('matrimonyLatitudeInput').value = lat;
                document.getElementById('matrimonyLongitudeInput').value = lon;
                
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`, {
                    headers: {
                        'User-Agent': 'MaliSetuApp/1.0'
                    }
                })
                .then(res => res.json())
                .then(geoData => {
                    if (geoData && geoData.address) {
                        const addr = geoData.address;
                        const pincode = addr.postcode || '';
                        const country = addr.country || 'India';
                        const state = addr.state || addr.region || '';
                        const city = addr.city || addr.town || addr.village || addr.municipality || addr.county || addr.state_district || addr.district || '';
                        
                        if (pincode) {
                            document.getElementById('pincodeInput').value = pincode.replace(/\s+/g, '');
                        }
                        const countrySelect = document.getElementById('countryInput');
                        if (countrySelect) {
                            if (country.toLowerCase().includes('india')) {
                                countrySelect.value = "India";
                            } else {
                                countrySelect.value = "Other";
                            }
                        }
                        if (state) {
                            document.getElementById('stateInput').value = state;
                        }
                        if (city) {
                            document.getElementById('cityInput').value = city;
                        }
                        
                        const talukaInput = document.getElementById('talukaInput');
                        if (talukaInput && (addr.suburb || addr.neighbourhood)) {
                            talukaInput.value = addr.suburb || addr.neighbourhood || '';
                        }
                        
                        const villageInput = document.getElementById('villageInput');
                        if (villageInput && addr.village) {
                            villageInput.value = addr.village;
                        }
                    }
                })
                .catch(err => {
                    console.error('OSM Nominatim Geolocation error:', err);
                });
            }, function(error) {
                locIcon.className = 'fa-solid fa-location-dot';
                const pinInput = document.getElementById('pincodeInput');
                if (pinInput) {
                    pinInput.value = '';
                    pinInput.classList.remove('is-valid', 'is-invalid');
                }
                const stateInput = document.getElementById('stateInput');
                const cityInput = document.getElementById('cityInput');
                const talukaInput = document.getElementById('talukaInput');
                const villageInput = document.getElementById('villageInput');
                if (stateInput) stateInput.value = '';
                if (cityInput) cityInput.value = '';
                if (talukaInput) talukaInput.value = '';
                if (villageInput) villageInput.value = '';
                alert("Geolocation error: " + error.message + ". Please enter details manually.");
            });
        });
    }

    // Photo input gallery preview handler
    const photosInput = document.getElementById('photosInput');
    const photoPreviewContainer = document.getElementById('photoPreviewContainer');

    photosInput.addEventListener('change', function() {
        photoPreviewContainer.innerHTML = '';
        const files = Array.from(photosInput.files);
        
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const thumb = document.createElement('div');
                thumb.className = 'preview-thumbnail';
                thumb.style.width = '100px';
                thumb.style.height = '100px';
                thumb.style.borderRadius = '8px';
                thumb.style.position = 'relative';
                thumb.style.overflow = 'hidden';
                thumb.style.border = '2px solid #e2e8f0';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                
                const delBtn = document.createElement('button');
                delBtn.type = 'button';
                delBtn.className = 'delete-thumb-btn';
                delBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                delBtn.onclick = function() {
                    thumb.remove();
                    const dt = new DataTransfer();
                    const newFiles = Array.from(photosInput.files).filter((_, fIdx) => fIdx !== index);
                    newFiles.forEach(f => dt.items.add(f));
                    photosInput.files = dt.files;
                };

                thumb.appendChild(img);
                thumb.appendChild(delBtn);
                photoPreviewContainer.appendChild(thumb);
            }
            reader.readAsDataURL(file);
        });
    });
</script>
@endpush
