@extends('layouts.app')

@section('title', 'Discover Matrimony Profiles')

@section('content')
<style>
    /* Premium Grid & Card Layouts */
    .profile-card { 
        border-radius: 20px; 
        background: #ffffff; 
        border: 1px solid rgba(173, 20, 87, 0.06); 
        overflow: hidden; 
        transition: all 0.3s ease; 
        box-shadow: 0 10px 25px rgba(173, 20, 87, 0.02);
    }
    .profile-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 16px 32px rgba(173, 20, 87, 0.08); 
    }
    .profile-photo { 
        width: 100%; 
        height: 180px; 
        object-fit: cover; 
        background: #f8fafc; 
    }
    .profile-photo-placeholder { 
        width: 100%; 
        height: 180px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 3.5rem; 
        color: #cbd5e1; 
        background: #f1f5f9; 
    }
    .conn-badge { 
        font-size: 0.72rem; 
        padding: 4px 12px; 
        border-radius: 50px; 
        font-weight: 700; 
    }

    /* Premium Collapsible Accordion Filter Panel */
    .discover-container {
        background: #ffffff;
        border: 1px solid rgba(173, 20, 87, 0.08);
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(173, 20, 87, 0.03);
        overflow: hidden;
    }
    .discover-header {
        border-bottom: 1.5px solid rgba(173, 20, 87, 0.08);
        padding: 20px 24px;
        background: #ffffff;
    }
    .discover-title {
        color: #ad1457;
        font-weight: 800;
        font-size: 1.15rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin: 0;
    }
    
    .discover-reset-btn {
        color: #ad1457;
        background: none;
        border: none;
        font-weight: 700;
        font-size: 0.85rem;
        text-decoration: none;
        transition: opacity 0.2s;
    }
    .discover-reset-btn:hover {
        opacity: 0.8;
    }

    /* Accordion Custom Styling */
    .accordion-item {
        border: none !important;
        border-bottom: 1px solid rgba(173, 20, 87, 0.08) !important;
    }
    .accordion-item:last-child {
        border-bottom: none !important;
    }
    .accordion-button {
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        color: #2d3436 !important;
        background-color: #ffffff !important;
        padding: 16px 20px !important;
        transition: all 0.2s ease !important;
    }
    .accordion-button:not(.collapsed) {
        color: var(--primary) !important;
        background-color: rgba(173, 20, 87, 0.02) !important;
        box-shadow: none !important;
    }
    .accordion-button::after {
        background-size: 0.85rem !important;
        transition: transform 0.2s ease !important;
    }
    .accordion-button:focus {
        box-shadow: none !important;
        border-color: rgba(173, 20, 87, 0.1) !important;
    }
    .accordion-body {
        padding: 20px !important;
        background-color: #ffffff !important;
    }

    /* Active filter tag selectors */
    .filter-tag-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }
    .filter-tag-btn {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        color: #475569;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-tag-btn:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }
    .filter-tag-btn.active {
        background: #fff5f8;
        border-color: #ad1457;
        color: #ad1457;
    }

    /* Premium fields layout */
    .form-label-bold {
        font-weight: 700;
        font-size: 0.82rem;
        color: #4a5568;
        margin-bottom: 6px;
    }
    .filter-field-wrapper {
        margin-bottom: 15px;
    }
    .filter-field-wrapper:last-child {
        margin-bottom: 0;
    }

    /* Input focus custom style */
    .discover-container .form-control, 
    .discover-container .form-select {
        color: #2d3436 !important;
        background-color: #ffffff !important;
        border: 1.5px solid #cbd5e1 !important;
        padding: 8px 12px !important;
        font-size: 0.88rem !important;
        border-radius: 10px !important;
    }
    .discover-container .form-control:focus, 
    .discover-container .form-select:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(173, 20, 87, 0.12) !important;
    }

    .apply-filter-btn {
        background: #ad1457;
        border: none;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 14px 20px;
        border-radius: 12px;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(173, 20, 87, 0.15);
    }
    .apply-filter-btn:hover {
        background: #8e0b44;
        box-shadow: 0 6px 15px rgba(173, 20, 87, 0.25);
    }

    /* Horizontal list elements */
    .premium-badge-tag {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        background: #fff1f2;
        color: #f43f5e;
        padding: 4px 10px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 12px;
    }
</style>

<div class="py-4">
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold mb-2">Browse Matrimony Profiles</h1>
                <p class="opacity-75 mb-0">Discover verified community members looking for a life partner.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('matrimony.index') }}" class="btn btn-light btn-sm rounded-3 px-3 py-2 fw-semibold shadow-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> My Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        {{-- LEFT DISCOVER FILTER CARD --}}
        <div class="col-lg-4 col-md-5">
            <div class="discover-container">
                
                <form method="GET" action="{{ route('matrimony.browse') }}" id="discoverForm">
                    
                    {{-- Header --}}
                    <div class="discover-header d-flex justify-content-between align-items-center">
                        <h5 class="discover-title"><i class="fa-solid fa-sliders me-2"></i> Discover</h5>
                        <button type="button" class="discover-reset-btn" onclick="resetFilters()"><i class="fa-solid fa-rotate-left me-1"></i> Reset</button>
                    </div>

                    {{-- Premium Vertical Accordion Filter Area --}}
                    <div class="accordion" id="filterAccordion">
                        
                        {{-- ITEM 1: Basic Details --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingBasic">
                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic" aria-expanded="true" aria-controls="collapseBasic">
                                    <i class="fa-solid fa-user text-primary me-2"></i> Basic Details
                                </button>
                            </h2>
                            <div id="collapseBasic" class="accordion-collapse collapse show" aria-labelledby="headingBasic" data-bs-parent="#filterAccordion">
                                <div class="accordion-body">
                                    
                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Gender</label>
                                        <select name="gender" class="form-select">
                                            <option value="">Any</option>
                                            <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold col-12">Age Range</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="number" name="age_min" class="form-control" min="18" max="100" value="{{ request('age_min', 18) }}" placeholder="Min">
                                            </div>
                                            <div class="col-6">
                                                <input type="number" name="age_max" class="form-control" min="18" max="100" value="{{ request('age_max', 60) }}" placeholder="Max">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold col-12">Height Range</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <select name="height_min" class="form-select">
                                                    <option value="">Min</option>
                                                    @for ($ft = 4.0; $ft <= 7.0; $ft += 0.1)
                                                        <option value="{{ sprintf('%.1f', $ft) }}" {{ request('height_min') == sprintf('%.1f', $ft) ? 'selected' : '' }}>{{ sprintf('%.1f', $ft) }} ft</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <select name="height_max" class="form-select">
                                                    <option value="">Max</option>
                                                    @for ($ft = 4.0; $ft <= 7.0; $ft += 0.1)
                                                        <option value="{{ sprintf('%.1f', $ft) }}" {{ request('height_max') == sprintf('%.1f', $ft) ? 'selected' : '' }}>{{ sprintf('%.1f', $ft) }} ft</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Profile Created By</label>
                                        <select name="profile_created_by" class="form-select">
                                            <option value="Any">Any</option>
                                            <option value="Self" {{ request('profile_created_by') === 'Self' ? 'selected' : '' }}>Self</option>
                                            <option value="Parent" {{ request('profile_created_by') === 'Parent' ? 'selected' : '' }}>Parent</option>
                                            <option value="Sibling" {{ request('profile_created_by') === 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                            <option value="Relative" {{ request('profile_created_by') === 'Relative' ? 'selected' : '' }}>Relative</option>
                                            <option value="Friend" {{ request('profile_created_by') === 'Friend' ? 'selected' : '' }}>Friend</option>
                                        </select>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Marital Status</label>
                                        <input type="hidden" name="marital_status" id="maritalInput" value="{{ request('marital_status', 'Any') }}">
                                        <div class="filter-tag-group">
                                            <button type="button" class="filter-tag-btn {{ request('marital_status', 'Any') === 'Any' ? 'active' : '' }}" onclick="selectTag('marital', 'Any', this)">Any</button>
                                            <button type="button" class="filter-tag-btn {{ request('marital_status') === 'Never Married' ? 'active' : '' }}" onclick="selectTag('marital', 'Never Married', this)">Single</button>
                                            <button type="button" class="filter-tag-btn {{ request('marital_status') === 'Divorced' ? 'active' : '' }}" onclick="selectTag('marital', 'Divorced', this)">Divorced</button>
                                            <button type="button" class="filter-tag-btn {{ request('marital_status') === 'Widowed' ? 'active' : '' }}" onclick="selectTag('marital', 'Widowed', this)">Widow</button>
                                            <button type="button" class="filter-tag-btn {{ request('marital_status') === 'Awaiting Divorce' ? 'active' : '' }}" onclick="selectTag('marital', 'Awaiting Divorce', this)">Awaiting Divorce</button>
                                        </div>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Mother Tongue</label>
                                        <select name="language" class="form-select">
                                            <option value="Any">Any</option>
                                            <option value="Marathi" {{ request('language') === 'Marathi' ? 'selected' : '' }}>Marathi</option>
                                            <option value="Hindi" {{ request('language') === 'Hindi' ? 'selected' : '' }}>Hindi</option>
                                            <option value="English" {{ request('language') === 'English' ? 'selected' : '' }}>English</option>
                                            <option value="Gujarati" {{ request('language') === 'Gujarati' ? 'selected' : '' }}>Gujarati</option>
                                            <option value="Kannada" {{ request('language') === 'Kannada' ? 'selected' : '' }}>Kannada</option>
                                            <option value="Telugu" {{ request('language') === 'Telugu' ? 'selected' : '' }}>Telugu</option>
                                            <option value="Tamil" {{ request('language') === 'Tamil' ? 'selected' : '' }}>Tamil</option>
                                            <option value="Other" {{ request('language') === 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Physical Status</label>
                                        <input type="hidden" name="physical_status" id="physicalInput" value="{{ request('physical_status', "Doesn't Matter") }}">
                                        <div class="filter-tag-group">
                                            <button type="button" class="filter-tag-btn {{ request('physical_status', "Doesn't Matter") === "Doesn't Matter" ? 'active' : '' }}" onclick="selectTag('physical', "Doesn't Matter", this)">Doesn't Matter</button>
                                            <button type="button" class="filter-tag-btn {{ request('physical_status') === 'Normal' ? 'active' : '' }}" onclick="selectTag('physical', 'Normal', this)">Normal</button>
                                            <button type="button" class="filter-tag-btn {{ request('physical_status') === 'Physically Challenged' ? 'active' : '' }}" onclick="selectTag('physical', 'Physically Challenged', this)">Physically Challenged</button>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        {{-- ITEM 2: Professional Details --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingProfessional">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProfessional" aria-expanded="false" aria-controls="collapseProfessional">
                                    <i class="fa-solid fa-briefcase text-primary me-2"></i> Professional Details
                                </button>
                            </h2>
                            <div id="collapseProfessional" class="accordion-collapse collapse" aria-labelledby="headingProfessional" data-bs-parent="#filterAccordion">
                                <div class="accordion-body">
                                    
                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Employment Type</label>
                                        <select name="employment_type" class="form-select">
                                            <option value="Any">Any</option>
                                            <option value="Private Sector" {{ request('employment_type') === 'Private Sector' ? 'selected' : '' }}>Private Sector</option>
                                            <option value="Government/Public Sector" {{ request('employment_type') === 'Government/Public Sector' ? 'selected' : '' }}>Government / Public Sector</option>
                                            <option value="Civil Service" {{ request('employment_type') === 'Civil Service' ? 'selected' : '' }}>Civil Service</option>
                                            <option value="Defense" {{ request('employment_type') === 'Defense' ? 'selected' : '' }}>Defense</option>
                                            <option value="Owner" {{ request('employment_type') === 'Owner' ? 'selected' : '' }}>Owner</option>
                                            <option value="Self Employed" {{ request('employment_type') === 'Self Employed' ? 'selected' : '' }}>Self Employed</option>
                                            <option value="Not Working" {{ request('employment_type') === 'Not Working' ? 'selected' : '' }}>Not Working</option>
                                        </select>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Occupation</label>
                                        <input type="hidden" name="occupation" id="occupationInput" value="{{ request('occupation', 'Any') }}">
                                        <div class="filter-tag-group">
                                            <button type="button" class="filter-tag-btn {{ request('occupation', 'Any') === 'Any' ? 'active' : '' }}" onclick="selectTag('occupation', 'Any', this)">Any</button>
                                            <button type="button" class="filter-tag-btn {{ request('occupation') === 'Engineering' ? 'active' : '' }}" onclick="selectTag('occupation', 'Engineering', this)">Engineering</button>
                                            <button type="button" class="filter-tag-btn {{ request('occupation') === 'Airline' ? 'active' : '' }}" onclick="selectTag('occupation', 'Airline', this)">Airline</button>
                                            <button type="button" class="filter-tag-btn {{ request('occupation') === 'IT & Software' ? 'active' : '' }}" onclick="selectTag('occupation', 'IT & Software', this)">IT & Software</button>
                                            <button type="button" class="filter-tag-btn {{ request('occupation') === 'Civil Services' ? 'active' : '' }}" onclick="selectTag('occupation', 'Civil Services', this)">Civil Services</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ITEM 3: Religion Details --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingReligion">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReligion" aria-expanded="false" aria-controls="collapseReligion">
                                    <i class="fa-solid fa-om text-primary me-2"></i> Horoscope & Religion
                                </button>
                            </h2>
                            <div id="collapseReligion" class="accordion-collapse collapse" aria-labelledby="headingReligion" data-bs-parent="#filterAccordion">
                                <div class="accordion-body">
                                    
                                    <div class="premium-badge-tag mb-3"><i class="fa-solid fa-lock me-1"></i> Horoscope Premium Filters</div>
                                    
                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Manglik</label>
                                        <input type="hidden" name="manglik" id="manglikInput" value="{{ request('manglik', "Doesn't Matter") }}">
                                        <div class="filter-tag-group">
                                            <button type="button" class="filter-tag-btn {{ request('manglik', "Doesn't Matter") === "Doesn't Matter" ? 'active' : '' }}" onclick="selectTag('manglik', "Doesn't Matter", this)">Doesn't Matter</button>
                                            <button type="button" class="filter-tag-btn {{ request('manglik') === 'Yes' ? 'active' : '' }}" onclick="selectTag('manglik', 'Yes', this)">Yes</button>
                                            <button type="button" class="filter-tag-btn {{ request('manglik') === 'No' ? 'active' : '' }}" onclick="selectTag('manglik', 'No', this)">No</button>
                                            <button type="button" class="filter-tag-btn {{ request('manglik') === "Don't Know" ? 'active' : '' }}" onclick="selectTag('manglik', "Don't Know", this)">Don't Know</button>
                                        </div>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Dosh</label>
                                        <input type="hidden" name="dosh" id="doshInput" value="{{ request('dosh', "Doesn't Matter") }}">
                                        <div class="filter-tag-group">
                                            <button type="button" class="filter-tag-btn {{ request('dosh', "Doesn't Matter") === "Doesn't Matter" ? 'active' : '' }}" onclick="selectTag('dosh', "Doesn't Matter", this)">Doesn't Matter</button>
                                            <button type="button" class="filter-tag-btn {{ request('dosh') === 'Yes' ? 'active' : '' }}" onclick="selectTag('dosh', 'Yes', this)">Yes</button>
                                            <button type="button" class="filter-tag-btn {{ request('dosh') === 'No' ? 'active' : '' }}" onclick="selectTag('dosh', 'No', this)">No</button>
                                            <button type="button" class="filter-tag-btn {{ request('dosh') === "Don't Know" ? 'active' : '' }}" onclick="selectTag('dosh', "Don't Know", this)">Don't Know</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ITEM 4: Family Details --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFamily">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFamily" aria-expanded="false" aria-controls="collapseFamily">
                                    <i class="fa-solid fa-people-roof text-primary me-2"></i> Family Details
                                </button>
                            </h2>
                            <div id="collapseFamily" class="accordion-collapse collapse" aria-labelledby="headingFamily" data-bs-parent="#filterAccordion">
                                <div class="accordion-body">
                                    
                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Family Type</label>
                                        <input type="hidden" name="family_type" id="familyTypeInput" value="{{ request('family_type', "Doesn't Matter") }}">
                                        <div class="filter-tag-group">
                                            <button type="button" class="filter-tag-btn {{ request('family_type', "Doesn't Matter") === "Doesn't Matter" ? 'active' : '' }}" onclick="selectTag('familyType', "Doesn't Matter", this)">Doesn't Matter</button>
                                            <button type="button" class="filter-tag-btn {{ request('family_type') === 'Nuclear' ? 'active' : '' }}" onclick="selectTag('familyType', 'Nuclear', this)"><i class="fa-solid fa-house-chimney me-1"></i> Nuclear</button>
                                            <button type="button" class="filter-tag-btn {{ request('family_type') === 'Joint' ? 'active' : '' }}" onclick="selectTag('familyType', 'Joint', this)"><i class="fa-solid fa-people-roof me-1"></i> Joint</button>
                                        </div>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Family Class</label>
                                        <select name="family_class" class="form-select">
                                            <option value="Doesn't Matter">Doesn't Matter</option>
                                            <option value="Rich" {{ request('family_class') === 'Rich' ? 'selected' : '' }}>Rich</option>
                                            <option value="Upper Middle Class" {{ request('family_class') === 'Upper Middle Class' ? 'selected' : '' }}>Upper Middle Class</option>
                                            <option value="Middle Class" {{ request('family_class') === 'Middle Class' ? 'selected' : '' }}>Middle Class</option>
                                            <option value="Lower Middle Class" {{ request('family_class') === 'Lower Middle Class' ? 'selected' : '' }}>Lower Middle Class</option>
                                            <option value="Lower Class" {{ request('family_class') === 'Lower Class' ? 'selected' : '' }}>Lower Class</option>
                                        </select>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Family Value</label>
                                        <input type="hidden" name="family_value" id="familyValueInput" value="{{ request('family_value', "Doesn't Matter") }}">
                                        <div class="filter-tag-group">
                                            <button type="button" class="filter-tag-btn {{ request('family_value', "Doesn't Matter") === "Doesn't Matter" ? 'active' : '' }}" onclick="selectTag('familyValue', "Doesn't Matter", this)">Doesn't Matter</button>
                                            <button type="button" class="filter-tag-btn {{ request('family_value') === 'Liberal' ? 'active' : '' }}" onclick="selectTag('familyValue', 'Liberal', this)">Liberal</button>
                                            <button type="button" class="filter-tag-btn {{ request('family_value') === 'Moderate' ? 'active' : '' }}" onclick="selectTag('familyValue', 'Moderate', this)">Moderate</button>
                                            <button type="button" class="filter-tag-btn {{ request('family_value') === 'Traditional' ? 'active' : '' }}" onclick="selectTag('familyValue', 'Traditional', this)">Traditional</button>
                                            <button type="button" class="filter-tag-btn {{ request('family_value') === 'Orthodox' ? 'active' : '' }}" onclick="selectTag('familyValue', 'Orthodox', this)">Orthodox</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ITEM 5: Location Details --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingLocation">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLocation" aria-expanded="false" aria-controls="collapseLocation">
                                    <i class="fa-solid fa-location-dot text-primary me-2"></i> Location Details
                                </button>
                            </h2>
                            <div id="collapseLocation" class="accordion-collapse collapse" aria-labelledby="headingLocation" data-bs-parent="#filterAccordion">
                                <div class="accordion-body">
                                    
                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Country</label>
                                        <select name="country" class="form-select">
                                            <option value="Any">Any</option>
                                            <option value="India" {{ request('country') === 'India' ? 'selected' : '' }}>India</option>
                                            <option value="USA" {{ request('country') === 'USA' ? 'selected' : '' }}>USA</option>
                                            <option value="UK" {{ request('country') === 'UK' ? 'selected' : '' }}>UK</option>
                                            <option value="Canada" {{ request('country') === 'Canada' ? 'selected' : '' }}>Canada</option>
                                            <option value="Australia" {{ request('country') === 'Australia' ? 'selected' : '' }}>Australia</option>
                                            <option value="Other" {{ request('country') === 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">State</label>
                                        <select name="state" class="form-select">
                                            <option value="Any">Any</option>
                                            <option value="Maharashtra" {{ request('state') === 'Maharashtra' ? 'selected' : '' }}>Maharashtra</option>
                                            <option value="Gujarat" {{ request('state') === 'Gujarat' ? 'selected' : '' }}>Gujarat</option>
                                            <option value="Karnataka" {{ request('state') === 'Karnataka' ? 'selected' : '' }}>Karnataka</option>
                                            <option value="Punjab" {{ request('state') === 'Punjab' ? 'selected' : '' }}>Punjab</option>
                                            <option value="Delhi" {{ request('state') === 'Delhi' ? 'selected' : '' }}>Delhi</option>
                                        </select>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">City</label>
                                        <input type="text" name="city" class="form-control" placeholder="Any City" value="{{ request('city') }}">
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ITEM 6: Lifestyle & Other Options --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingLifestyle">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLifestyle" aria-expanded="false" aria-controls="collapseLifestyle">
                                    <i class="fa-solid fa-mug-hot text-primary me-2"></i> Lifestyle & Options
                                </button>
                            </h2>
                            <div id="collapseLifestyle" class="accordion-collapse collapse" aria-labelledby="headingLifestyle" data-bs-parent="#filterAccordion">
                                <div class="accordion-body">
                                    
                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Diet</label>
                                        <input type="hidden" name="diet" id="dietInput" value="{{ request('diet', 'Any') }}">
                                        <div class="filter-tag-group">
                                            <button type="button" class="filter-tag-btn {{ request('diet', 'Any') === 'Any' ? 'active' : '' }}" onclick="selectTag('diet', 'Any', this)">Any</button>
                                            <button type="button" class="filter-tag-btn {{ request('diet') === 'Vegetarian' ? 'active' : '' }}" onclick="selectTag('diet', 'Vegetarian', this)">Vegetarian</button>
                                            <button type="button" class="filter-tag-btn {{ request('diet') === 'Non-Vegetarian' ? 'active' : '' }}" onclick="selectTag('diet', 'Non-Vegetarian', this)">Non-Vegetarian</button>
                                            <button type="button" class="filter-tag-btn {{ request('diet') === 'Eggetarian' ? 'active' : '' }}" onclick="selectTag('diet', 'Eggetarian', this)">Eggetarian</button>
                                            <button type="button" class="filter-tag-btn {{ request('diet') === 'Vegan' ? 'active' : '' }}" onclick="selectTag('diet', 'Vegan', this)">Vegan</button>
                                        </div>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Smoking Habits</label>
                                        <select name="smoking" class="form-select">
                                            <option value="Any">Any</option>
                                            <option value="No" {{ request('smoking') === 'No' ? 'selected' : '' }}>No</option>
                                            <option value="Yes" {{ request('smoking') === 'Yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="Occasionally" {{ request('smoking') === 'Occasionally' ? 'selected' : '' }}>Occasionally</option>
                                        </select>
                                    </div>

                                    <div class="filter-field-wrapper">
                                        <label class="form-label-bold">Drinking Habits</label>
                                        <select name="drinking" class="form-select">
                                            <option value="Any">Any</option>
                                            <option value="No" {{ request('drinking') === 'No' ? 'selected' : '' }}>No</option>
                                            <option value="Yes" {{ request('drinking') === 'Yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="Occasionally" {{ request('drinking') === 'Occasionally' ? 'selected' : '' }}>Occasionally</option>
                                        </select>
                                    </div>

                                    <div class="filter-field-wrapper border-top pt-3 mt-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="photo" value="yes" id="photoOnlyCheck" {{ request('photo') === 'yes' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark" for="photoOnlyCheck">
                                                Profiles with photo
                                            </label>
                                            <div class="text-secondary small mt-1">Matches who have added photos</div>
                                        </div>
                                    </div>

                                    <div class="filter-field-wrapper border-top pt-3 mt-3">
                                        <label class="form-label-bold mb-2">Profile Created</label>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="created_at" value="all" id="createdAll" {{ request('created_at', 'all') === 'all' ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" for="createdAll">All Time</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="created_at" value="today" id="createdToday" {{ request('created_at') === 'today' ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" for="createdToday">Today</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="created_at" value="last_7_days" id="createdSeven" {{ request('created_at') === 'last_7_days' ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" for="createdSeven">Last 7 Days</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="created_at" value="last_30_days" id="createdThirty" {{ request('created_at') === 'last_30_days' ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" for="createdThirty">Last 30 Days</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="created_at" value="one_week" id="createdOneWeek" {{ request('created_at') === 'one_week' ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" for="createdOneWeek">One Week</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="created_at" value="one_month" id="createdOneMonth" {{ request('created_at') === 'one_month' ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" for="createdOneMonth">One Month</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Apply button spanning bottom --}}
                    <div class="p-3 border-top bg-light">
                        <button type="submit" class="apply-filter-btn"><i class="fa-solid fa-filter me-2"></i> Apply Filters</button>
                    </div>

                </form>
            </div>
        </div>

        {{-- RIGHT RESULTS GRID COLUMN --}}
        <div class="col-lg-8 col-md-7">
            
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
                    <p class="text-secondary">Try adjusting your discover filters to see more results.</p>
                    <a href="{{ route('matrimony.browse') }}" class="btn btn-outline-primary rounded-3 px-4">Clear All Filters</a>
                </div>
            @else
                <div class="row g-3 mb-4">
                    @foreach($profiles as $profile)
                        @php
                            $pd = $profile->personal_details ?? [];
                            $ld = $profile->location_details ?? [];
                            $ed = $profile->education_details ?? [];
                            $pro = $profile->professional_details ?? [];
                        @endphp
                        <div class="col-xl-4 col-sm-6">
                            <div class="profile-card h-100 d-flex flex-column justify-content-between">
                                <div>
                                    {{-- Photo --}}
                                    @if(!empty($pd['photos'][0]))
                                        <img src="{{ asset('storage/' . $pd['photos'][0]) }}" class="profile-photo">
                                    @else
                                        <img src="{{ asset('default-avatar.png') }}" class="profile-photo">
                                    @endif

                                    <div class="p-3">
                                        <div class="d-flex align-items-start justify-content-between mb-2">
                                            <h6 class="fw-bold mb-0 text-truncate" style="max-width: 70%;" title="{{ $profile->user->name ?? 'Profile ' . $profile->id }}">{{ $profile->user->name ?? 'Profile ' . $profile->id }}</h6>
                                            @if($profile->my_connection_status === 'accepted')
                                                <span class="conn-badge bg-success text-white">Connected</span>
                                            @elseif($profile->my_connection_status === 'pending')
                                                <span class="conn-badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </div>

                                        <div class="d-flex flex-wrap gap-2 mb-2" style="font-size:0.78rem;color:#6c757d;">
                                            <span><i class="fa-solid fa-cake-candles text-primary me-1"></i>{{ $profile->age }}y</span>
                                            <span><i class="fa-solid fa-ruler text-primary me-1"></i>{{ $profile->height ?? 'N/A' }}</span>
                                            <span class="text-truncate" style="max-width: 100%;"><i class="fa-solid fa-location-dot text-primary me-1"></i>{{ $ld['city'] ?? $ld['state'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 mb-2 text-truncate" style="font-size:0.78rem;color:#6c757d;">
                                            <span class="text-truncate" style="max-width: 100%;"><i class="fa-solid fa-graduation-cap text-primary me-1"></i>{{ $ed['highest_qualification'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 mb-1 text-truncate" style="font-size:0.78rem;color:#6c757d;">
                                            <span class="text-truncate" style="max-width: 100%;"><i class="fa-solid fa-briefcase text-primary me-1"></i>{{ $pro['occupation'] ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 pt-0">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('matrimony.show', $profile->id) }}" class="btn btn-primary btn-sm rounded-3 flex-grow-1 fw-bold">
                                            View Profile
                                        </a>
                                        @if($profile->my_connection_status === 'none')
                                            <form action="{{ route('matrimony.request.send') }}" method="POST" class="mb-0">
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

    </div>
</div>

<script>
    function selectTag(inputGroup, value, btnElement) {
        // Update the value inside hidden inputs
        document.getElementById(inputGroup + 'Input').value = value;

        // Reset highlight borders/styles for current tag buttons list
        btnElement.parentNode.querySelectorAll('.filter-tag-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Add highlight styling to clicked option tag
        btnElement.classList.add('active');
    }

    function resetFilters() {
        // Redirect to clear filters completely
        window.location.href = "{{ route('matrimony.browse') }}";
    }
</script>
@endsection
