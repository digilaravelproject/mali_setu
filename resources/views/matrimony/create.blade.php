@extends('layouts.app')

@section('title', 'Register Matrimony')

@section('content')
<style>
    /* Brand Color Overrides & Step Wizard Styles */
    :root {
        --brand-primary: #84144f;
        --brand-light: #fef0f6;
        --text-muted: #8898aa;
    }
    
    .wizard-card {
        background: #ffffff;
        border: 1px solid rgba(132, 20, 79, 0.08);
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(132, 20, 79, 0.04);
        padding: 35px;
        transition: all 0.3s ease;
    }

    /* Mobile-like Header */
    .wizard-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    
    .wizard-title {
        color: #2d3748;
        font-weight: 800;
        font-size: 1.5rem;
        margin: 0;
        text-align: center;
    }

    /* Step Circles */
    .steps-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        max-width: 500px;
        margin: 0 auto 40px auto;
        padding: 0 10px;
    }

    .steps-progress-bar {
        position: absolute;
        top: 20px;
        left: 20px;
        right: 20px;
        height: 3px;
        background: #e2e8f0;
        z-index: 1;
    }

    .steps-progress-line {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 0%;
        background: var(--brand-primary);
        transition: width 0.4s ease;
    }

    .step-circle-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 2;
        position: relative;
    }

    .step-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #f8fafc;
        border: 3px solid #e2e8f0;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.4s ease;
    }

    .step-circle-wrapper.active .step-circle {
        border-color: var(--brand-primary);
        background: #ffffff;
        color: var(--brand-primary);
        box-shadow: 0 0 0 5px rgba(255, 71, 87, 0.1);
    }

    .step-circle-wrapper.completed .step-circle {
        border-color: var(--brand-primary);
        background: var(--brand-primary);
        color: #ffffff;
    }

    .step-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
        margin-top: 8px;
        transition: all 0.4s ease;
        text-align: center;
    }

    .step-circle-wrapper.active .step-label {
        color: var(--brand-primary);
    }

    .step-circle-wrapper.completed .step-label {
        color: #475569;
    }

    /* Form Fields & Custom Styling */
    .form-section {
        display: none;
    }

    .form-section.active {
        display: block;
        animation: slideIn 0.4s ease;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .section-subtitle {
        color: #718096;
        font-size: 0.9rem;
        margin-bottom: 25px;
    }

    .required-star {
        color: #dc3545;
        font-weight: bold;
        margin-left: 2px;
    }

    .form-control, .form-select {
        border-radius: 12px;
        padding: 12px 16px;
        border: 1.5px solid #cbd5e1;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 4px rgba(255, 71, 87, 0.08);
    }

    .invalid-feedback {
        display: none;
        color: #dc3545;
        font-size: 0.82rem;
        font-weight: 600;
        margin-top: 5px;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    .is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.08) !important;
    }

    /* Section Headers */
    .pink-section-header {
        color: var(--brand-primary);
        font-size: 0.85rem;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        border-bottom: 1.5px solid rgba(255, 71, 87, 0.1);
        padding-bottom: 6px;
        margin-top: 30px;
        margin-bottom: 20px;
    }

    /* Pincode Location Icon Wrapper */
    .pincode-input-wrapper {
        position: relative;
    }

    .pincode-input-wrapper .location-icon {
        position: absolute;
        right: 15px;
        top: 15px;
        color: var(--brand-primary);
        font-size: 1.1rem;
    }

    /* Pink Photo Upload Styles */
    .upload-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 15px;
    }

    .photo-upload-box {
        width: 100px;
        height: 100px;
        border: 2px dashed rgba(255, 71, 87, 0.3);
        background: #fff5f6;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .photo-upload-box:hover {
        background: #ffe5e7;
        border-color: var(--brand-primary);
    }

    .photo-upload-box i {
        color: var(--brand-primary);
        font-size: 1.5rem;
        margin-bottom: 5px;
    }

    .photo-upload-box span {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--brand-primary);
    }

    .preview-thumbnail {
        width: 100px;
        height: 100px;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }

    .preview-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .delete-thumb-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.95);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .delete-thumb-btn:hover {
        background: #dc3545;
        transform: scale(1.1);
    }

    /* Dynamic Dropdown Loader */
    .caste-loader {
        position: absolute;
        right: 35px;
        top: 45px;
        display: none;
    }

    /* Buttons */
    .btn-brand {
        background: var(--brand-primary);
        border: none;
        color: #ffffff;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(132, 20, 79, 0.15);
    }

    .btn-brand:hover {
        background: #630837;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(132, 20, 79, 0.25);
    }

    .btn-brand-outline {
        background: transparent;
        border: 1.5px solid var(--brand-primary);
        color: var(--brand-primary);
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .btn-brand-outline:hover {
        background: var(--brand-light);
        color: var(--brand-primary);
    }
</style>

<div class="py-4">
    <div class="wizard-card">
        
        {{-- Custom Mobile-Styled Header --}}
        <div class="wizard-header d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('matrimony.index') }}" class="btn btn-light btn-sm rounded-3 shadow-sm px-3 py-2"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
            <h4 class="wizard-title text-center flex-grow-1">Register Matrimony</h4>
            <div style="width: 78px;"></div> {{-- Spacer --}}
        </div>

        {{-- Step Progress Indicator --}}
        <div class="steps-container">
            <div class="steps-progress-bar">
                <div class="steps-progress-line" id="progressLine"></div>
            </div>
            
            <div class="step-circle-wrapper active" id="stepIndicator-1">
                <div class="step-circle">1</div>
                <span class="step-label">Personal</span>
            </div>
            
            <div class="step-circle-wrapper" id="stepIndicator-2">
                <div class="step-circle">2</div>
                <span class="step-label">Horoscope</span>
            </div>
            
            <div class="step-circle-wrapper" id="stepIndicator-3">
                <div class="step-circle">3</div>
                <span class="step-label">Education</span>
            </div>
            
            <div class="step-circle-wrapper" id="stepIndicator-4">
                <div class="step-circle">4</div>
                <span class="step-label">Lifestyle</span>
            </div>
        </div>

        {{-- Form Start --}}
        <form action="{{ route('matrimony.store') }}" method="POST" id="matrimonyForm" enctype="multipart/form-data">
            @csrf

            {{-- STEP 1: Personal Details --}}
            <div class="form-section active" id="section-1">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Personal Details</h5>
                        <p class="section-subtitle">Start with your basic info</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label">First Name<span class="required-star">*</span></label>
                        <input type="text" name="first_name" class="form-control" placeholder="Enter Your first name" value="{{ old('first_name') }}" required>
                        <span class="invalid-feedback">Please enter first name</span>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" placeholder="Enter Your Middle name" value="{{ old('middle_name') }}">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Last Name<span class="required-star">*</span></label>
                        <input type="text" name="last_name" class="form-control" placeholder="Enter Your Last name" value="{{ old('last_name') }}" required>
                        <span class="invalid-feedback">Please enter last name</span>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Profile Created By<span class="required-star">*</span></label>
                        <select name="profile_created_by" class="form-select" required>
                            <option value="">Select Profile Created by</option>
                            <option value="Self" {{ old('profile_created_by') == 'Self' ? 'selected' : '' }}>Self</option>
                            <option value="Parent" {{ old('profile_created_by') == 'Parent' ? 'selected' : '' }}>Parent</option>
                            <option value="Sibling" {{ old('profile_created_by') == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                            <option value="Relative" {{ old('profile_created_by') == 'Relative' ? 'selected' : '' }}>Relative</option>
                            <option value="Friend" {{ old('profile_created_by') == 'Friend' ? 'selected' : '' }}>Friend</option>
                        </select>
                        <span class="invalid-feedback">Please select profile creator</span>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Gender<span class="required-star">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        <span class="invalid-feedback">Please select gender</span>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Date of Birth<span class="required-star">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>
                        <span class="invalid-feedback">Please select date of birth</span>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Height (Feet)</label>
                        <select name="height" class="form-select">
                            <option value="">Select Height</option>
                            @for ($ft = 4.0; $ft <= 7.0; $ft += 0.1)
                                <option value="{{ sprintf('%.1f', $ft) }}" {{ old('height') == sprintf('%.1f', $ft) ? 'selected' : '' }}>{{ sprintf('%.1f', $ft) }} ft</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Weight (kg)</label>
                        <input type="number" name="weight" class="form-control" placeholder="Enter weight" min="30" max="150" value="{{ old('weight') }}">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Complexion</label>
                        <select name="complexion" class="form-select">
                            <option value="">Select Complexion</option>
                            <option value="Fair" {{ old('complexion') == 'Fair' ? 'selected' : '' }}>Fair</option>
                            <option value="Wheatish" {{ old('complexion') == 'Wheatish' ? 'selected' : '' }}>Wheatish</option>
                            <option value="Dark" {{ old('complexion') == 'Dark' ? 'selected' : '' }}>Dark</option>
                            <option value="Very Fair" {{ old('complexion') == 'Very Fair' ? 'selected' : '' }}>Very Fair</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Marital Status</label>
                        <select name="marital_status" class="form-select">
                            <option value="">Select Marital Status</option>
                            <option value="Never Married" {{ old('marital_status') == 'Never Married' ? 'selected' : '' }}>Never Married</option>
                            <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Awaiting Divorce" {{ old('marital_status') == 'Awaiting Divorce' ? 'selected' : '' }}>Awaiting Divorce</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Physical Status</label>
                        <select name="physical_status" class="form-select">
                            <option value="">Select Physical Status</option>
                            <option value="Normal" {{ old('physical_status', 'Normal') == 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Physically Challenged" {{ old('physical_status') == 'Physically Challenged' ? 'selected' : '' }}>Physically Challenged</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Mother Tongue</label>
                        <select name="mother_tongue" class="form-select">
                            <option value="">Select Mother Tongue</option>
                            <option value="Marathi" {{ old('mother_tongue', 'Marathi') == 'Marathi' ? 'selected' : '' }}>Marathi</option>
                            <option value="Hindi" {{ old('mother_tongue') == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                            <option value="English" {{ old('mother_tongue') == 'English' ? 'selected' : '' }}>English</option>
                            <option value="Gujarati" {{ old('mother_tongue') == 'Gujarati' ? 'selected' : '' }}>Gujarati</option>
                            <option value="Kannada" {{ old('mother_tongue') == 'Kannada' ? 'selected' : '' }}>Kannada</option>
                            <option value="Telugu" {{ old('mother_tongue') == 'Telugu' ? 'selected' : '' }}>Telugu</option>
                            <option value="Tamil" {{ old('mother_tongue') == 'Tamil' ? 'selected' : '' }}>Tamil</option>
                            <option value="Other" {{ old('mother_tongue') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Citizenship</label>
                        <select name="citizenship" class="form-select">
                            <option value="">Select Citizenship</option>
                            <option value="Indian" {{ old('citizenship', 'Indian') == 'Indian' ? 'selected' : '' }}>Indian</option>
                            <option value="Other" {{ old('citizenship') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-select">
                            <option value="">Select Blood Group</option>
                            <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                            <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                        </select>
                    </div>

                    <div class="col-12 mb-4">
                        <label class="form-label">Referral Name</label>
                        <input type="text" name="referral_name" class="form-control" placeholder="Enter Your Referral name" value="{{ old('referral_name') }}">
                    </div>

                    <div class="col-12 mb-4">
                        <label class="form-label">About Me</label>
                        <textarea name="about_me" class="form-control" rows="3" placeholder="Describe yourself, your interests, and personality...">{{ old('about_me') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-brand px-5 py-2.5" onclick="nextSection(1)">Next <i class="fa-solid fa-arrow-right ms-2"></i></button>
                </div>
            </div>

            {{-- STEP 2: Religious Horoscope --}}
            <div class="form-section" id="section-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Religious Horoscope</h5>
                    <p class="section-subtitle">Your Astrological Details</p>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4 position-relative">
                        <label class="form-label">Caste<span class="required-star">*</span></label>
                        <select name="caste" class="form-select" id="casteSelect" required>
                            <option value="">Select Caste</option>
                        </select>
                        <div class="spinner-border text-primary spinner-border-sm caste-loader" id="casteLoader"></div>
                        <span class="invalid-feedback" id="casteFeedback">Please select caste</span>
                    </div>

                    <div class="col-md-6 mb-4 position-relative">
                        <label class="form-label">Sub-Caste<span class="required-star">*</span></label>
                        <select name="sub_caste" class="form-select" id="subCasteSelect" required disabled>
                            <option value="">Select Sub-Caste</option>
                        </select>
                        <div class="spinner-border text-primary spinner-border-sm caste-loader" id="subCasteLoader"></div>
                        <span class="invalid-feedback" id="subCasteFeedback">Please select sub-caste</span>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Star</label>
                        <select name="star" class="form-select">
                            <option value="">Select Star</option>
                            <option value="Ashwini">Ashwini</option>
                            <option value="Bharani">Bharani</option>
                            <option value="Krittika">Krittika</option>
                            <option value="Rohini">Rohini</option>
                            <option value="Mrigashirsha">Mrigashirsha</option>
                            <option value="Ardra">Ardra</option>
                            <option value="Punarvasu">Punarvasu</option>
                            <option value="Pushya">Pushya</option>
                            <option value="Ashlesha">Ashlesha</option>
                            <option value="Magha">Magha</option>
                            <option value="Purva Phalguni">Purva Phalguni</option>
                            <option value="Uttara Phalguni">Uttara Phalguni</option>
                            <option value="Hasta">Hasta</option>
                            <option value="Chitra">Chitra</option>
                            <option value="Svati">Svati</option>
                            <option value="Vishakha">Vishakha</option>
                            <option value="Anuradha">Anuradha</option>
                            <option value="Jyeshtha">Jyeshtha</option>
                            <option value="Mula">Mula</option>
                            <option value="Purva Ashadha">Purva Ashadha</option>
                            <option value="Uttara Ashadha">Uttara Ashadha</option>
                            <option value="Shravana">Shravana</option>
                            <option value="Dhanishta">Dhanishta</option>
                            <option value="Shatabhisha">Shatabhisha</option>
                            <option value="Purva Bhadrapada">Purva Bhadrapada</option>
                            <option value="Uttara Bhadrapada">Uttara Bhadrapada</option>
                            <option value="Revati">Revati</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Raasi</label>
                        <select name="raasi" class="form-select">
                            <option value="">Select Raasi</option>
                            <option value="Mesha (Aries)">Mesha (Aries)</option>
                            <option value="Vrishabha (Taurus)">Vrishabha (Taurus)</option>
                            <option value="Mithuna (Gemini)">Mithuna (Gemini)</option>
                            <option value="Karka (Cancer)">Karka (Cancer)</option>
                            <option value="Simha (Leo)">Simha (Leo)</option>
                            <option value="Kanya (Virgo)">Kanya (Virgo)</option>
                            <option value="Tula (Libra)">Tula (Libra)</option>
                            <option value="Vrischika (Scorpio)">Vrischika (Scorpio)</option>
                            <option value="Dhanu (Sagittarius)">Dhanu (Sagittarius)</option>
                            <option value="Makara (Capricorn)">Makara (Capricorn)</option>
                            <option value="Kumbha (Aquarius)">Kumbha (Aquarius)</option>
                            <option value="Meena (Pisces)">Meena (Pisces)</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Manglik</label>
                        <select name="manglik" class="form-select">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                            <option value="Anshik (Partial)">Anshik (Partial)</option>
                            <option value="Don't Know">Don't Know</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Dosh</label>
                        <select name="dosh" class="form-select">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                            <option value="Don't Know">Don't Know</option>
                        </select>
                    </div>

                    <div class="col-12 mb-4">
                        <label class="form-label">PHOTOS (Min 5)</label>
                        <input type="file" id="fileInput" name="photos[]" multiple accept="image/*" class="d-none">
                        
                        <div class="upload-container" id="uploadContainer">
                            {{-- Interactive upload button --}}
                            <div class="photo-upload-box" onclick="document.getElementById('fileInput').click()">
                                <i class="fa-solid fa-camera"></i>
                                <span>Add Photo</span>
                            </div>
                            {{-- Preview thumbs render here --}}
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-brand-outline px-5 py-2.5" onclick="backSection(2)"><i class="fa-solid fa-arrow-left me-2"></i> Back</button>
                    <button type="button" class="btn btn-brand px-5 py-2.5" onclick="nextSection(2)">Next <i class="fa-solid fa-arrow-right ms-2"></i></button>
                </div>
            </div>

            {{-- STEP 3: Education & Career --}}
            <div class="form-section" id="section-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Education Career</h5>
                    <p class="section-subtitle">Qualification & Occupation</p>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Highest Qualification<span class="required-star">*</span></label>
                        <select name="highest_qualification" class="form-select" required>
                            <option value="">Select Highest Qualification</option>
                            <option value="PhD" {{ old('highest_qualification') == 'PhD' ? 'selected' : '' }}>PhD</option>
                            <option value="M.Com" {{ old('highest_qualification') == 'M.Com' ? 'selected' : '' }}>M.Com</option>
                            <option value="M.Sc" {{ old('highest_qualification') == 'M.Sc' ? 'selected' : '' }}>M.Sc</option>
                            <option value="M.Tech / M.E." {{ old('highest_qualification') == 'M.Tech / M.E.' ? 'selected' : '' }}>M.Tech / M.E.</option>
                            <option value="MBA / PGDM" {{ old('highest_qualification') == 'MBA / PGDM' ? 'selected' : '' }}>MBA / PGDM</option>
                            <option value="MCA" {{ old('highest_qualification') == 'MCA' ? 'selected' : '' }}>MCA</option>
                            <option value="M.Pharm" {{ old('highest_qualification') == 'M.Pharm' ? 'selected' : '' }}>M.Pharm</option>
                            <option value="Bachelor's Degree" {{ old('highest_qualification') == "Bachelor's Degree" ? 'selected' : '' }}>Bachelor's Degree</option>
                            <option value="Diploma" {{ old('highest_qualification') == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                            <option value="High School" {{ old('highest_qualification') == 'High School' ? 'selected' : '' }}>High School</option>
                            <option value="Other" {{ old('highest_qualification') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <span class="invalid-feedback">Please select highest qualification</span>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">College</label>
                        <input type="text" name="college_name" class="form-control" placeholder="Enter Your College" value="{{ old('college_name') }}">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Employment Type<span class="required-star">*</span></label>
                        <select name="employment_type" class="form-select" required>
                            <option value="">Select Employment Type</option>
                            <option value="Private Sector" {{ old('employment_type') == 'Private Sector' ? 'selected' : '' }}>Private Sector</option>
                            <option value="Government/Public Sector" {{ old('employment_type') == 'Government/Public Sector' ? 'selected' : '' }}>Government/Public Sector</option>
                            <option value="Civil Service" {{ old('employment_type') == 'Civil Service' ? 'selected' : '' }}>Civil Service</option>
                            <option value="Defense" {{ old('employment_type') == 'Defense' ? 'selected' : '' }}>Defense</option>
                            <option value="Owner" {{ old('employment_type') == 'Owner' ? 'selected' : '' }}>Owner</option>
                            <option value="Self Employed" {{ old('employment_type') == 'Self Employed' ? 'selected' : '' }}>Self Employed</option>
                            <option value="Not Working" {{ old('employment_type') == 'Not Working' ? 'selected' : '' }}>Not Working</option>
                        </select>
                        <span class="invalid-feedback">Please select employment type</span>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Job Title</label>
                        <input type="text" name="occupation" class="form-control" placeholder="Enter Your Job title" value="{{ old('occupation') }}">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control" placeholder="Enter Your Company name" value="{{ old('company_name') }}">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Annual Income (Lac)</label>
                        <input type="text" name="annual_income" class="form-control" placeholder="e.g. 4 Lakh" value="{{ old('annual_income') }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-brand-outline px-5 py-2.5" onclick="backSection(3)"><i class="fa-solid fa-arrow-left me-2"></i> Back</button>
                    <button type="button" class="btn btn-brand px-5 py-2.5" onclick="nextSection(3)">Next <i class="fa-solid fa-arrow-right ms-2"></i></button>
                </div>
            </div>

            {{-- STEP 4: Family, Lifestyle & Location --}}
            <div class="form-section" id="section-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Family & Lifestyle</h5>
                    <p class="section-subtitle">Family background & habits</p>
                </div>

                <div class="row">
                    {{-- Family Details --}}
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Family Type<span class="required-star">*</span></label>
                        <select name="family_type" class="form-select" required>
                            <option value="">Select Family Type</option>
                            <option value="Nuclear" {{ old('family_type') == 'Nuclear' ? 'selected' : '' }}>Nuclear</option>
                            <option value="Joint" {{ old('family_type') == 'Joint' ? 'selected' : '' }}>Joint</option>
                        </select>
                        <span class="invalid-feedback">Please select family type</span>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Family Class</label>
                        <select name="family_status" class="form-select">
                            <option value="">Select Family Class</option>
                            <option value="Rich" {{ old('family_status') == 'Rich' ? 'selected' : '' }}>Rich</option>
                            <option value="Upper Middle Class" {{ old('family_status') == 'Upper Middle Class' ? 'selected' : '' }}>Upper Middle Class</option>
                            <option value="Middle Class" {{ old('family_status') == 'Middle Class' ? 'selected' : '' }}>Middle Class</option>
                            <option value="Lower Middle Class" {{ old('family_status') == 'Lower Middle Class' ? 'selected' : '' }}>Lower Middle Class</option>
                            <option value="Lower Class" {{ old('family_status') == 'Lower Class' ? 'selected' : '' }}>Lower Class</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Family Value</label>
                        <select name="family_values" class="form-select">
                            <option value="">Select Family Value</option>
                            <option value="Orthodox" {{ old('family_values') == 'Orthodox' ? 'selected' : '' }}>Orthodox</option>
                            <option value="Traditional" {{ old('family_values') == 'Traditional' ? 'selected' : '' }}>Traditional</option>
                            <option value="Moderate" {{ old('family_values') == 'Moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="Liberal" {{ old('family_values') == 'Liberal' ? 'selected' : '' }}>Liberal</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Father's Occupation</label>
                        <input type="text" name="father_occupation" class="form-control" placeholder="Enter Your Father's occupation" value="{{ old('father_occupation') }}">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Mother Occupation</label>
                        <input type="text" name="mother_occupation" class="form-control" placeholder="Enter Your Mother occupation" value="{{ old('mother_occupation') }}">
                    </div>

                    {{-- Lifestyle Section --}}
                    <div class="col-12">
                        <div class="pink-section-header">Lifestyle</div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Diet</label>
                        <select name="diet" class="form-select">
                            <option value="">Select Diet</option>
                            <option value="Vegetarian" {{ old('diet') == 'Vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                            <option value="Non-Vegetarian" {{ old('diet') == 'Non-Vegetarian' ? 'selected' : '' }}>Non-Vegetarian</option>
                            <option value="Eggetarian" {{ old('diet') == 'Eggetarian' ? 'selected' : '' }}>Eggetarian</option>
                            <option value="Vegan" {{ old('diet') == 'Vegan' ? 'selected' : '' }}>Vegan</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Smoking</label>
                        <select name="smoking" class="form-select">
                            <option value="">Select Smoking</option>
                            <option value="No" {{ old('smoking') == 'No' ? 'selected' : '' }}>No</option>
                            <option value="Yes" {{ old('smoking') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="Occasionally" {{ old('smoking') == 'Occasionally' ? 'selected' : '' }}>Occasionally</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Drinking</label>
                        <select name="drinking" class="form-select">
                            <option value="">Select Drinking</option>
                            <option value="No" {{ old('drinking') == 'No' ? 'selected' : '' }}>No</option>
                            <option value="Yes" {{ old('drinking') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="Occasionally" {{ old('drinking') == 'Occasionally' ? 'selected' : '' }}>Occasionally</option>
                        </select>
                    </div>

                    {{-- Location Section --}}
                    <div class="col-12">
                        <div class="pink-section-header">Location</div>
                    </div>

                    <div class="col-12 mb-4">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" placeholder="Enter Your Address" value="{{ old('address') }}">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Pincode<span class="required-star">*</span></label>
                        <div class="pincode-input-wrapper">
                            <input type="text" name="pincode" class="form-control" id="pincodeInput" placeholder="Enter Your Pincode" maxlength="6" value="{{ old('pincode') }}" required>
                            <i class="fa-solid fa-location-dot location-icon"></i>
                            <span class="invalid-feedback">Please enter pincode</span>
                        </div>
                        <input type="hidden" name="latitude" id="matrimonyLatitudeInput" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" id="matrimonyLongitudeInput" value="{{ old('longitude') }}">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Country<span class="required-star">*</span></label>
                        <input type="text" name="country" class="form-control" id="countryInput" placeholder="Enter Your Country" value="{{ old('country', 'India') }}" required>
                        <span class="invalid-feedback">Please enter country</span>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">State<span class="required-star">*</span></label>
                        <input type="text" name="state" class="form-control" id="stateInput" placeholder="Enter Your State" value="{{ old('state') }}" required>
                        <span class="invalid-feedback">Please enter state</span>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">City<span class="required-star">*</span></label>
                        <input type="text" name="city" class="form-control" id="cityInput" placeholder="Enter Your City" value="{{ old('city') }}" required>
                        <span class="invalid-feedback">Please enter city</span>
                    </div>

                    <div class="col-md-2 mb-4">
                        <label class="form-label">Taluka</label>
                        <input type="text" name="taluka" class="form-control" id="talukaInput" placeholder="Enter Your Taluka" value="{{ old('taluka') }}">
                    </div>

                    <div class="col-md-2 mb-4">
                        <label class="form-label">Village</label>
                        <input type="text" name="village" class="form-control" id="villageInput" placeholder="Enter Your Village" value="{{ old('village') }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-brand-outline px-5 py-2.5" onclick="backSection(4)"><i class="fa-solid fa-arrow-left me-2"></i> Back</button>
                    <button type="submit" class="btn btn-brand px-5 py-2.5"><i class="fa-solid fa-check-circle me-2"></i> Register</button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    // File upload management & Premium thumbnails preview
    let selectedFiles = [];
    const fileInput = document.getElementById('fileInput');
    const uploadContainer = document.getElementById('uploadContainer');

    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        
        // Cap total photos to 5
        if (selectedFiles.length + files.length > 5) {
            alert('You can upload up to 5 photos only.');
            return;
        }

        // Check file size (max 5MB = 5 * 1024 * 1024 bytes)
        for (let file of files) {
            if (file.size > 5 * 1024 * 1024) {
                alert(`File "${file.name}" exceeds the 5MB size limit.`);
                return;
            }
        }

        files.forEach(file => {
            selectedFiles.push(file);
            
            // Create preview card
            const reader = new FileReader();
            reader.onload = function(event) {
                const div = document.createElement('div');
                div.className = 'preview-thumbnail';
                div.innerHTML = `
                    <img src="${event.target.result}" alt="Preview">
                    <button type="button" class="delete-thumb-btn" onclick="removeSelectedPhoto(this, '${file.name}')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                `;
                uploadContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        updateFileInput();
    });

    function removeSelectedPhoto(btnElement, fileName) {
        // Remove from local tracking array
        selectedFiles = selectedFiles.filter(file => file.name !== fileName);
        
        // Remove thumbnail element from UI
        btnElement.closest('.preview-thumbnail').remove();
        
        updateFileInput();
    }

    function updateFileInput() {
        // Re-construct input file list using DataTransfer
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
    }

    // Dynamic AJAX Caste & Sub-Caste loading from DB API
    const casteSelect = document.getElementById('casteSelect');
    const subCasteSelect = document.getElementById('subCasteSelect');
    const casteLoader = document.getElementById('casteLoader');
    const subCasteLoader = document.getElementById('subCasteLoader');

    document.addEventListener('DOMContentLoaded', function() {
        loadCastes();
    });

    function loadCastes() {
        casteLoader.style.display = 'block';
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
                        casteSelect.appendChild(opt);
                    });
                }
            })
            .catch(err => {
                casteLoader.style.display = 'none';
                casteSelect.disabled = false;
                console.error('Failed to load castes:', err);
            });
    }

    casteSelect.addEventListener('change', function() {
        const selectedOption = casteSelect.options[casteSelect.selectedIndex];
        const castId = selectedOption.dataset.id;

        // Reset subcaste list
        subCasteSelect.innerHTML = '<option value="">Select Sub-Caste</option>';
        subCasteSelect.disabled = true;

        if (!castId) {
            return;
        }

        subCasteLoader.style.display = 'block';

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
                        subCasteSelect.appendChild(opt);
                    });
                } else {
                    const opt = document.createElement('option');
                    opt.value = casteSelect.value; // Fallback to same as caste name
                    opt.textContent = casteSelect.value;
                    subCasteSelect.appendChild(opt);
                }
            })
            .catch(err => {
                subCasteLoader.style.display = 'none';
                subCasteSelect.disabled = false;
                console.error('Failed to load subcastes:', err);
                
                // Fallback to avoid blocking the user
                const opt = document.createElement('option');
                opt.value = casteSelect.value;
                opt.textContent = casteSelect.value;
                subCasteSelect.appendChild(opt);
            });
    });

    // Pincode dynamic lookup (Country, State, City, Taluka, Village, Address)
    function lookupPincode(isClick = false) {
        const pinInput = document.getElementById('pincodeInput');
        const pin = pinInput.value.trim();
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
                        
                        const countryInput = document.getElementById('countryInput');
                        if (countryInput) countryInput.value = "India";
                        
                        const stateInput = document.getElementById('stateInput');
                        if (stateInput) stateInput.value = po.State || '';
                        
                        const cityInput = document.getElementById('cityInput');
                        if (cityInput) cityInput.value = po.District || '';

                        const talukaInput = document.getElementById('talukaInput');
                        if (talukaInput) talukaInput.value = (po.Block && po.Block !== 'N.A.') ? po.Block : '';

                        const villageInput = document.getElementById('villageInput');
                        if (villageInput) villageInput.value = po.Name || '';

                        const addressInput = document.querySelector('input[name="address"]');
                        if (addressInput && po.Name && po.District) {
                            addressInput.value = po.Name + ", " + po.District;
                        }
                    } else {
                        if (isClick === true) {
                            alert("Invalid Pincode or no records found.");
                        }
                    }
                })
                .catch(err => {
                    icon.classList.remove('fa-spinner', 'fa-spin');
                    icon.classList.add('fa-location-dot');
                    console.error('Pincode lookup error:', err);
                    if (isClick === true) {
                        alert("Error searching pincode. Please try again.");
                    }
                });
        } else {
            if (isClick === true) {
                alert("Please enter a valid 6-digit Pincode first.");
            }
        }
    }

    document.getElementById('pincodeInput').addEventListener('input', function() {
        lookupPincode(false);
    });
    const locIcon = document.querySelector('.location-icon');
    if (locIcon) {
        locIcon.style.cursor = 'pointer';
        locIcon.addEventListener('click', function() {
            const oldClass = locIcon.className;
            locIcon.className = 'fa-solid fa-spinner fa-spin text-primary';
            
            navigator.geolocation.getCurrentPosition(function(position) {
                locIcon.className = 'fa-solid fa-circle-check text-success';
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                document.getElementById('matrimonyLatitudeInput').value = lat;
                document.getElementById('matrimonyLongitudeInput').value = lon;
                
                // Fetch reverse geocoding from OpenStreetMap Nominatim
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
                        if (country) {
                            document.getElementById('countryInput').value = country;
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
                        
                        const addressInput = document.querySelector('input[name="address"]');
                        if (addressInput && geoData.display_name) {
                            addressInput.value = geoData.display_name;
                        }
                    }
                })
                .catch(err => {
                    console.error('Reverse geocoding error:', err);
                });
                
                alert('GPS Coordinates fetched successfully: ' + lat.toFixed(4) + ', ' + lon.toFixed(4));
            }, function(error) {
                locIcon.className = oldClass;
                // Fallback to normal pincode lookup if pincode is present
                const pin = document.getElementById('pincodeInput').value.trim();
                if (pin.length === 6 && /^\d+$/.test(pin)) {
                    lookupPincode(true);
                } else {
                    alert('Geolocation Error: ' + error.message + '. Please enter Pincode manually.');
                }
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        });
    }

    // Wizard Step Navigation & Same-Page Validation
    function updateProgress(step) {
        // Line progress
        const percentage = ((step - 1) / 3) * 100;
        document.getElementById('progressLine').style.width = `${percentage}%`;

        // Circle highlights
        for (let i = 1; i <= 4; i++) {
            const wrapper = document.getElementById(`stepIndicator-${i}`);
            if (i < step) {
                wrapper.className = 'step-circle-wrapper completed';
            } else if (i === step) {
                wrapper.className = 'step-circle-wrapper active';
            } else {
                wrapper.className = 'step-circle-wrapper';
            }
        }
    }

    function validateSection(step) {
        let isValid = true;
        const section = document.getElementById(`section-${step}`);
        
        // Find all fields marked with required attribute in current step view
        const requiredFields = section.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            const errorSpan = field.parentNode.querySelector('.invalid-feedback');
            
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                if (errorSpan) errorSpan.style.display = 'block';
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
                if (errorSpan) errorSpan.style.display = 'none';
            }
        });

        return isValid;
    }

    function nextSection(currentStep) {
        // Run validation check
        if (!validateSection(currentStep)) {
            // Find first error and scroll to it
            const firstError = document.getElementById(`section-${currentStep}`).querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
            return;
        }

        // Enforce at least 2 photos on Step 2 (Horoscope/Photos step)
        if (currentStep === 2) {
            if (selectedFiles.length < 2) {
                alert('Please upload at least 2 photos.');
                return;
            }
        }

        // Advance to next section
        document.getElementById(`section-${currentStep}`).classList.remove('active');
        document.getElementById(`section-${currentStep + 1}`).classList.add('active');
        
        updateProgress(currentStep + 1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function backSection(currentStep) {
        document.getElementById(`section-${currentStep}`).classList.remove('active');
        document.getElementById(`section-${currentStep - 1}`).classList.add('active');
        
        updateProgress(currentStep - 1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Intercept form submit for a final validation run on step 4
    document.getElementById('matrimonyForm').addEventListener('submit', function(e) {
        if (!validateSection(4)) {
            e.preventDefault();
            const firstError = document.getElementById('section-4').querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        } else if (selectedFiles.length < 2) {
            e.preventDefault();
            alert('Please upload at least 2 photos.');
            // Go back to section 2 (photos section)
            document.getElementById('section-4').classList.remove('active');
            document.getElementById('section-2').classList.add('active');
            updateProgress(2);
        }
    });
</script>
@endsection
