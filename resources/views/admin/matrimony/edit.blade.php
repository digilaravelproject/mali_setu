@extends('admin.layouts.app')

@section('title', 'Edit Matrimony Profile')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Matrimony Profile - {{ $profile->user->name ?? 'Seeker' }}</h1>
        <div class="d-sm-flex">
            <a href="{{ route('admin.matrimony.show', $profile->id) }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Profile
            </a>
        </div>
    </div>

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
            foreach ($arr as $k => $v) {
                if ($k === 'photos') continue;
                if (is_array($v)) {
                    $arr[$k] = implode(', ', array_filter(array_map(function ($item) {
                        return is_array($item) ? json_encode($item) : $item;
                    }, $v)));
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

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Matrimony Seeker Account Information</h6>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.matrimony.update', $profile->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Seeker Profile Ownership & Basic Seeker Info -->
                        <div class="card mb-4 border-light shadow-sm">
                            <div class="card-header bg-light py-3">
                                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-user-circle me-2 text-primary"></i>1. Account Info & Basic Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold small">Owner User</label>
                                        <input type="text" class="form-control" value="{{ $profile->user->name ?? 'N/A' }} ({{ $profile->user->email ?? 'N/A' }} - ID: {{ $profile->user_id }})" disabled>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="age" class="form-label font-weight-bold small">Age <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="age" name="age" value="{{ old('age', $profile->age) }}" min="18" max="100" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="gender" class="form-label font-weight-bold small">Gender <span class="text-danger">*</span></label>
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="male" {{ old('gender', $profile->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $profile->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender', $profile->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="date_of_birth" class="form-label font-weight-bold small">Date of Birth</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $profile->date_of_birth ? \Carbon\Carbon::parse($profile->date_of_birth)->format('Y-m-d') : '') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="time_of_birth" class="form-label font-weight-bold small">Time of Birth</label>
                                        <input type="text" class="form-control" id="time_of_birth" name="time_of_birth" value="{{ old('time_of_birth', $profile->time_of_birth) }}" placeholder="e.g. 10:30 AM">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="height" class="form-label font-weight-bold small">Height</label>
                                        <input type="text" class="form-control" id="height" name="height" value="{{ old('height', $profile->height) }}" placeholder="e.g. 5ft 8in">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="weight" class="form-label font-weight-bold small">Weight</label>
                                        <input type="text" class="form-control" id="weight" name="weight" value="{{ old('weight', $profile->weight) }}" placeholder="e.g. 70 kg">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="complexion" class="form-label font-weight-bold small">Complexion</label>
                                        <input type="text" class="form-control" id="complexion" name="complexion" value="{{ old('complexion', $profile->complexion) }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="physical_status" class="form-label font-weight-bold small">Physical Status</label>
                                        <input type="text" class="form-control" id="physical_status" name="physical_status" value="{{ old('physical_status', $profile->physical_status) }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="approval_status" class="form-label font-weight-bold small">Approval Status <span class="text-danger">*</span></label>
                                        <select class="form-select" id="approval_status" name="approval_status" required>
                                            <option value="pending" {{ old('approval_status', $profile->approval_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ old('approval_status', $profile->approval_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ old('approval_status', $profile->approval_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="profile_expires_at" class="form-label font-weight-bold small">Profile Expires At</label>
                                        <input type="date" class="form-control" id="profile_expires_at" name="profile_expires_at" value="{{ old('profile_expires_at', $profile->profile_expires_at ? $profile->profile_expires_at->format('Y-m-d') : '') }}">
                                    </div>
                            </div>
                        </div>

                        <!-- Personal details -->
                        <div class="card mb-4 border-light shadow-sm">
                            <div class="card-header bg-light py-3">
                                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-id-card me-2 text-primary"></i>2. Personal Details & Caste Info</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="marital_status" class="form-label font-weight-bold small">Marital Status <span class="text-danger">*</span></label>
                                        <select class="form-select" id="marital_status" name="personal_details[marital_status]" required>
                                            <option value="never_married" {{ ($pd['marital_status'] ?? '') == 'never_married' ? 'selected' : '' }}>Never Married</option>
                                            <option value="widowed" {{ ($pd['marital_status'] ?? '') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                            <option value="divorced" {{ ($pd['marital_status'] ?? '') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                            <option value="awaiting_divorce" {{ ($pd['marital_status'] ?? '') == 'awaiting_divorce' ? 'selected' : '' }}>Awaiting Divorce</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="mother_tongue" class="form-label font-weight-bold small">Mother Tongue <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="mother_tongue" name="personal_details[mother_tongue]" value="{{ old('personal_details.mother_tongue', $pd['mother_tongue'] ?? '') }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="profile_created_by" class="form-label font-weight-bold small">Profile Created By</label>
                                        <select class="form-select" id="profile_created_by" name="personal_details[profile_created_by]">
                                            <option value="self" {{ ($pd['profile_created_by'] ?? '') == 'self' ? 'selected' : '' }}>Self</option>
                                            <option value="parents" {{ ($pd['profile_created_by'] ?? '') == 'parents' ? 'selected' : '' }}>Parents</option>
                                            <option value="sibling" {{ ($pd['profile_created_by'] ?? '') == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                            <option value="relative" {{ ($pd['profile_created_by'] ?? '') == 'relative' ? 'selected' : '' }}>Relative</option>
                                            <option value="friend" {{ ($pd['profile_created_by'] ?? '') == 'friend' ? 'selected' : '' }}>Friend</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="religion" class="form-label font-weight-bold small">Religion <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="religion" name="personal_details[religion]" value="{{ old('personal_details.religion', $pd['religion'] ?? 'Hindu') }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="caste" class="form-label font-weight-bold small">Caste <span class="text-danger">*</span></label>
                                        <select class="form-select" id="caste" name="personal_details[caste]" required>
                                            @foreach($castes as $caste)
                                                <option value="{{ $caste->name }}" {{ ($pd['caste'] ?? '') == $caste->name ? 'selected' : '' }}>{{ $caste->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="sub_caste" class="form-label font-weight-bold small">Sub Caste</label>
                                        <select class="form-select" id="sub_caste" name="personal_details[sub_caste]">
                                            <option value="" {{ empty($pd['sub_caste']) ? 'selected' : '' }}>None</option>
                                            @foreach($subcastes as $sub)
                                                <option value="{{ $sub->name }}" {{ ($pd['sub_caste'] ?? '') == $sub->name ? 'selected' : '' }}>{{ $sub->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="about_me" class="form-label font-weight-bold small">About Seeker <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="about_me" name="personal_details[about_me]" rows="3" required>{{ old('personal_details.about_me', $pd['about_me'] ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="card mb-4 border-light shadow-sm">
                            <div class="card-header bg-light py-3">
                                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-location-arrow me-2 text-primary"></i>3. Seeker Location Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="location_city" class="form-label font-weight-bold small">Current City / Town <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="location_city" name="location_details[city]" value="{{ old('location_details.city', $ld['city'] ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="location_state" class="form-label font-weight-bold small">Current State <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="location_state" name="location_details[state]" value="{{ old('location_details.state', $ld['state'] ?? '') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Education & Career -->
                        <div class="card mb-4 border-light shadow-sm">
                            <div class="card-header bg-light py-3">
                                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-graduation-cap me-2 text-primary"></i>4. Education & Career Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="highest_qualification" class="form-label font-weight-bold small">Highest Qualification <span class="text-danger">*</span></label>
                                        <select class="form-select" id="highest_qualification" name="education_details[highest_qualification]" required>
                                            <option value="" disabled {{ !old('education_details.highest_qualification', $ed['highest_qualification'] ?? '') ? 'selected' : '' }}>Select Qualification</option>
                                            @foreach($educations as $edu)
                                                <option value="{{ $edu->highest_qualification }}" {{ old('education_details.highest_qualification', $ed['highest_qualification'] ?? '') == $edu->highest_qualification ? 'selected' : '' }}>{{ $edu->highest_qualification }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="college_name" class="form-label font-weight-bold small">College / University</label>
                                        <input type="text" class="form-control" id="college_name" name="education_details[college_name]" value="{{ old('education_details.college_name', $ed['college_name'] ?? '') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="passing_year" class="form-label font-weight-bold small">Passing Year</label>
                                        <input type="text" class="form-control" id="passing_year" name="education_details[passing_year]" value="{{ old('education_details.passing_year', $ed['passing_year'] ?? '') }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="matri_occupation" class="form-label font-weight-bold small">Occupation <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="matri_occupation" name="professional_details[occupation]" value="{{ old('professional_details.occupation', $pro['occupation'] ?? '') }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="matri_company" class="form-label font-weight-bold small">Company Name</label>
                                        <input type="text" class="form-control" id="matri_company" name="professional_details[company_name]" value="{{ old('professional_details.company_name', $pro['company_name'] ?? '') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="annual_income" class="form-label font-weight-bold small">Annual Income <span class="text-danger">*</span></label>
                                        <select class="form-select" id="annual_income" name="professional_details[annual_income]" required>
                                            <option value="under_3_lakh" {{ ($pro['annual_income'] ?? '') == 'under_3_lakh' ? 'selected' : '' }}>Under ₹3 Lakh</option>
                                            <option value="3_to_5_lakh" {{ ($pro['annual_income'] ?? '') == '3_to_5_lakh' ? 'selected' : '' }}>₹3 Lakh to ₹5 Lakh</option>
                                            <option value="5_to_10_lakh" {{ ($pro['annual_income'] ?? '') == '5_to_10_lakh' ? 'selected' : '' }}>₹5 Lakh to ₹10 Lakh</option>
                                            <option value="10_to_15_lakh" {{ ($pro['annual_income'] ?? '') == '10_to_15_lakh' ? 'selected' : '' }}>₹10 Lakh to ₹15 Lakh</option>
                                            <option value="above_15_lakh" {{ ($pro['annual_income'] ?? '') == 'above_15_lakh' ? 'selected' : '' }}>Above ₹15 Lakh</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Family details -->
                        <div class="card mb-4 border-light shadow-sm">
                            <div class="card-header bg-light py-3">
                                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-users me-2 text-primary"></i>5. Family Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="father_name" class="form-label font-weight-bold small">Father's Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="father_name" name="family_details[father_name]" value="{{ old('family_details.father_name', $fd['father_name'] ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="father_occupation" class="form-label font-weight-bold small">Father's Occupation</label>
                                        <input type="text" class="form-control" id="father_occupation" name="family_details[father_occupation]" value="{{ old('family_details.father_occupation', $fd['father_occupation'] ?? '') }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="mother_name" class="form-label font-weight-bold small">Mother's Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="mother_name" name="family_details[mother_name]" value="{{ old('family_details.mother_name', $fd['mother_name'] ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="mother_occupation" class="form-label font-weight-bold small">Mother's Occupation</label>
                                        <input type="text" class="form-control" id="mother_occupation" name="family_details[mother_occupation]" value="{{ old('family_details.mother_occupation', $fd['mother_occupation'] ?? '') }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="no_of_brothers" class="form-label font-weight-bold small">Number of Brothers</label>
                                        <input type="number" class="form-control" id="no_of_brothers" name="family_details[no_of_brothers]" value="{{ old('family_details.no_of_brothers', $fd['no_of_brothers'] ?? 0) }}" min="0">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="no_of_sisters" class="form-label font-weight-bold small">Number of Sisters</label>
                                        <input type="number" class="form-control" id="no_of_sisters" name="family_details[no_of_sisters]" value="{{ old('family_details.no_of_sisters', $fd['no_of_sisters'] ?? 0) }}" min="0">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="family_type" class="form-label font-weight-bold small">Family Type <span class="text-danger">*</span></label>
                                        <select class="form-select" id="family_type" name="family_details[family_type]" required>
                                            <option value="nuclear" {{ ($fd['family_type'] ?? '') == 'nuclear' ? 'selected' : '' }}>Nuclear Family</option>
                                            <option value="joint" {{ ($fd['family_type'] ?? '') == 'joint' ? 'selected' : '' }}>Joint Family</option>
                                            <option value="extended" {{ ($fd['family_type'] ?? '') == 'extended' ? 'selected' : '' }}>Extended Family</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="family_value" class="form-label font-weight-bold small">Family Values <span class="text-danger">*</span></label>
                                        <select class="form-select" id="family_value" name="family_details[family_value]" required>
                                            <option value="traditional" {{ ($fd['family_value'] ?? '') == 'traditional' ? 'selected' : '' }}>Traditional</option>
                                            <option value="moderate" {{ ($fd['family_value'] ?? '') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                            <option value="liberal" {{ ($fd['family_value'] ?? '') == 'liberal' ? 'selected' : '' }}>Liberal</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="about_family" class="form-label font-weight-bold small">About Family Details</label>
                                        <textarea class="form-control" id="about_family" name="family_details[about_family]" rows="2">{{ old('family_details.about_family', $fd['about_family'] ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lifestyle habits -->
                        <div class="card mb-4 border-light shadow-sm">
                            <div class="card-header bg-light py-3">
                                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-heart-pulse me-2 text-primary"></i>6. Lifestyle & Habits</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="diet" class="form-label font-weight-bold small">Diet Preference</label>
                                        <select class="form-select" id="diet" name="lifestyle_details[diet]">
                                            <option value="vegetarian" {{ ($lf['diet'] ?? '') == 'vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                                            <option value="non_vegetarian" {{ ($lf['diet'] ?? '') == 'non_vegetarian' ? 'selected' : '' }}>Non-Vegetarian</option>
                                            <option value="eggetarian" {{ ($lf['diet'] ?? '') == 'eggetarian' ? 'selected' : '' }}>Eggetarian</option>
                                            <option value="vegan" {{ ($lf['diet'] ?? '') == 'vegan' ? 'selected' : '' }}>Vegan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="smoking" class="form-label font-weight-bold small">Smoking habits</label>
                                        <select class="form-select" id="smoking" name="lifestyle_details[smoking]">
                                            <option value="no" {{ ($lf['smoking'] ?? '') == 'no' ? 'selected' : '' }}>No</option>
                                            <option value="yes" {{ ($lf['smoking'] ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="occasionally" {{ ($lf['smoking'] ?? '') == 'occasionally' ? 'selected' : '' }}>Occasionally</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="drinking" class="form-label font-weight-bold small">Drinking habits</label>
                                        <select class="form-select" id="drinking" name="lifestyle_details[drinking]">
                                            <option value="no" {{ ($lf['drinking'] ?? '') == 'no' ? 'selected' : '' }}>No</option>
                                            <option value="yes" {{ ($lf['drinking'] ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="occasionally" {{ ($lf['drinking'] ?? '') == 'occasionally' ? 'selected' : '' }}>Occasionally</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="hobbies" class="form-label font-weight-bold small">Hobbies & Interests</label>
                                        <input type="text" class="form-control" id="hobbies" name="lifestyle_details[hobbies]" value="{{ old('lifestyle_details.hobbies', $lf['hobbies'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Partner preferences -->
                        <div class="card mb-4 border-light shadow-sm">
                            <div class="card-header bg-light py-3">
                                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-sliders me-2 text-primary"></i>7. Partner Expectations</h6>
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
                                        <input type="text" class="form-control" id="pref_height" name="partner_preferences[height_min]" value="{{ old('partner_preferences.height_min', $pp['height_min'] ?? '') }}">
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
                                        <textarea class="form-control" id="pref_about" name="partner_preferences[about_partner]" rows="2">{{ old('partner_preferences.about_partner', $pp['about_partner'] ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image management -->
                        <div class="card mb-4 border-light shadow-sm">
                            <div class="card-header bg-light py-3">
                                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-images me-2 text-primary"></i>8. Gallery Photos</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label font-weight-bold small d-block">Current Photos (Check to Delete)</label>
                                        @if(!empty($pd['photos']))
                                            <div class="row g-3">
                                                @foreach($pd['photos'] as $idx => $photo)
                                                    <div class="col-md-2 col-4 text-center">
                                                        <div class="position-relative border rounded p-1 mb-2">
                                                            <img src="{{ asset('storage/' . $photo) }}" alt="Photo" class="img-fluid rounded" style="max-height: 100px; object-fit: cover;">
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
                                        <label for="photos" class="form-label font-weight-bold small">Upload New Photos</label>
                                        <input type="file" class="form-control" id="photos" name="photos[]" accept="image/*" multiple>
                                        <small class="text-muted small">Select new photos to add (max 5MB per photo). Keep at least 2 photos total.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.matrimony.show', $profile->id) }}" class="btn btn-secondary rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
