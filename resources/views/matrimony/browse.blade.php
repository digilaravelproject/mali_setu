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
        background: linear-gradient(135deg, #f9d6e3, #fce4ec); 
    }
    .profile-photo-placeholder { 
        width: 100%; 
        height: 180px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 3.5rem; 
        color: #fff; 
        background: linear-gradient(135deg, rgba(173, 20, 87, 0.18), rgba(173, 20, 87, 0.10)); 
    }
    .conn-badge { 
        font-size: 0.72rem; 
        padding: 4px 12px; 
        border-radius: 50px; 
        font-weight: 700; 
    }

    /* Premium Tabbed Mobile-Style Filter Panel */
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

    /* Sidebar category list */
    .discover-sidebar {
        background: #f8fafc;
        border-right: 1.5px solid rgba(173, 20, 87, 0.08);
        padding: 15px 0;
    }
    .discover-tab-link {
        display: block;
        padding: 14px 20px;
        color: #64748b;
        font-weight: 700;
        font-size: 0.82rem;
        text-decoration: none;
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
        cursor: pointer;
    }
    .discover-tab-link:hover {
        background: rgba(173, 20, 87, 0.02);
        color: #ad1457;
    }
    .discover-tab-link.active {
        background: #ffffff;
        color: #ad1457;
        border-left-color: #ad1457;
    }

    /* Filter contents area */
    .discover-content {
        padding: 25px 30px;
        background: #ffffff;
    }
    
    .filter-tab-pane {
        display: none;
        animation: fadeInTab 0.3s ease;
    }
    .filter-tab-pane.active {
        display: block;
    }

    @keyframes fadeInTab {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Active filter tag selectors */
    .filter-tag-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    .filter-tag-btn {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #475569;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 8px 18px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-tag-btn:hover {
        background: #e2e8f0;
    }
    .filter-tag-btn.active {
        background: #fff5f8;
        border-color: #ad1457;
        color: #ad1457;
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

                    {{-- Dynamic Tabs Sidebar / Content Area --}}
                    <div class="row g-0">
                        
                        {{-- Sidebar Categories --}}
                        <div class="col-5 discover-sidebar">
                            <a class="discover-tab-link active" onclick="showFilterPane('basic', this)">Basic Details</a>
                            <a class="discover-tab-link" onclick="showFilterPane('professional', this)">Professional Details</a>
                            <a class="discover-tab-link" onclick="showFilterPane('religion', this)">Religion Details</a>
                            <a class="discover-tab-link" onclick="showFilterPane('family', this)">Family Details</a>
                            <a class="discover-tab-link" onclick="showFilterPane('location', this)">Location Details</a>
                            <a class="discover-tab-link" onclick="showFilterPane('lifestyle', this)">Lifestyle</a>
                            <a class="discover-tab-link" onclick="showFilterPane('profile', this)">Profile Type</a>
                            <a class="discover-tab-link" onclick="showFilterPane('recent', this)">Recently Created</a>
                        </div>

                        {{-- Content Tabs --}}
                        <div class="col-7 discover-content">
                            
                            {{-- TAB 1: Basic Details --}}
                            <div class="filter-tab-pane active" id="filter-pane-basic">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Gender</label>
                                    <select name="gender" class="form-select form-select-sm">
                                        <option value="">Any</option>
                                        <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>

                                <div class="row mb-3">
                                    <label class="form-label fw-bold col-12">Age Range</label>
                                    <div class="col-6">
                                        <input type="number" name="age_min" class="form-control form-control-sm" min="18" max="100" value="{{ request('age_min', 18) }}" placeholder="Min">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="age_max" class="form-control form-control-sm" min="18" max="100" value="{{ request('age_max', 60) }}" placeholder="Max">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="form-label fw-bold col-12">Height Range</label>
                                    <div class="col-6">
                                        <select name="height_min" class="form-select form-select-sm">
                                            <option value="">Min</option>
                                            @for ($ft = 4.0; $ft <= 7.0; $ft += 0.1)
                                                <option value="{{ sprintf('%.1f', $ft) }}" {{ request('height_min') == sprintf('%.1f', $ft) ? 'selected' : '' }}>{{ sprintf('%.1f', $ft) }} ft</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <select name="height_max" class="form-select form-select-sm">
                                            <option value="">Max</option>
                                            @for ($ft = 4.0; $ft <= 7.0; $ft += 0.1)
                                                <option value="{{ sprintf('%.1f', $ft) }}" {{ request('height_max') == sprintf('%.1f', $ft) ? 'selected' : '' }}>{{ sprintf('%.1f', $ft) }} ft</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Profile Created By</label>
                                    <select name="profile_created_by" class="form-select form-select-sm">
                                        <option value="Any">Any</option>
                                        <option value="Self" {{ request('profile_created_by') === 'Self' ? 'selected' : '' }}>Self</option>
                                        <option value="Parent" {{ request('profile_created_by') === 'Parent' ? 'selected' : '' }}>Parent</option>
                                        <option value="Sibling" {{ request('profile_created_by') === 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                        <option value="Relative" {{ request('profile_created_by') === 'Relative' ? 'selected' : '' }}>Relative</option>
                                        <option value="Friend" {{ request('profile_created_by') === 'Friend' ? 'selected' : '' }}>Friend</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Marital Status</label>
                                    <input type="hidden" name="marital_status" id="maritalInput" value="{{ request('marital_status', 'Any') }}">
                                    <div class="filter-tag-group">
                                        <button type="button" class="filter-tag-btn {{ request('marital_status', 'Any') === 'Any' ? 'active' : '' }}" onclick="selectTag('marital', 'Any', this)">Any</button>
                                        <button type="button" class="filter-tag-btn {{ request('marital_status') === 'Never Married' ? 'active' : '' }}" onclick="selectTag('marital', 'Never Married', this)">Single</button>
                                        <button type="button" class="filter-tag-btn {{ request('marital_status') === 'Divorced' ? 'active' : '' }}" onclick="selectTag('marital', 'Divorced', this)">Divorced</button>
                                        <button type="button" class="filter-tag-btn {{ request('marital_status') === 'Widowed' ? 'active' : '' }}" onclick="selectTag('marital', 'Widowed', this)">Widow</button>
                                        <button type="button" class="filter-tag-btn {{ request('marital_status') === 'Awaiting Divorce' ? 'active' : '' }}" onclick="selectTag('marital', 'Awaiting Divorce', this)">Awaiting Divorce</button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mother Tongue</label>
                                    <select name="language" class="form-select form-select-sm">
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

                                <div class="mb-2">
                                    <label class="form-label fw-bold">Physical Status</label>
                                    <input type="hidden" name="physical_status" id="physicalInput" value="{{ request('physical_status', "Doesn't Matter") }}">
                                    <div class="filter-tag-group">
                                        <button type="button" class="filter-tag-btn {{ request('physical_status', "Doesn't Matter") === "Doesn't Matter" ? 'active' : '' }}" onclick="selectTag('physical', "Doesn't Matter", this)">Doesn't Matter</button>
                                        <button type="button" class="filter-tag-btn {{ request('physical_status') === 'Normal' ? 'active' : '' }}" onclick="selectTag('physical', 'Normal', this)">Normal</button>
                                        <button type="button" class="filter-tag-btn {{ request('physical_status') === 'Physically Challenged' ? 'active' : '' }}" onclick="selectTag('physical', 'Physically Challenged', this)">Physically Challenged</button>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 2: Professional --}}
                            <div class="filter-tab-pane" id="filter-pane-professional">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Employment Type</label>
                                    <select name="employment_type" class="form-select form-select-sm">
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

                                <div class="mb-2">
                                    <label class="form-label fw-bold">Occupation</label>
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

                            {{-- TAB 3: Religion Details --}}
                            <div class="filter-tab-pane" id="filter-pane-religion">
                                <div class="premium-badge-tag"><i class="fa-solid fa-lock me-1"></i> Horoscope Premium Filters</div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Manglik</label>
                                    <input type="hidden" name="manglik" id="manglikInput" value="{{ request('manglik', "Doesn't Matter") }}">
                                    <div class="filter-tag-group">
                                        <button type="button" class="filter-tag-btn {{ request('manglik', "Doesn't Matter") === "Doesn't Matter" ? 'active' : '' }}" onclick="selectTag('manglik', "Doesn't Matter", this)">Doesn't Matter</button>
                                        <button type="button" class="filter-tag-btn {{ request('manglik') === 'Yes' ? 'active' : '' }}" onclick="selectTag('manglik', 'Yes', this)">Yes</button>
                                        <button type="button" class="filter-tag-btn {{ request('manglik') === 'No' ? 'active' : '' }}" onclick="selectTag('manglik', 'No', this)">No</button>
                                        <button type="button" class="filter-tag-btn {{ request('manglik') === "Don't Know" ? 'active' : '' }}" onclick="selectTag('manglik', "Don't Know", this)">Don't Know</button>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold">Dosh</label>
                                    <input type="hidden" name="dosh" id="doshInput" value="{{ request('dosh', "Doesn't Matter") }}">
                                    <div class="filter-tag-group">
                                        <button type="button" class="filter-tag-btn {{ request('dosh', "Doesn't Matter") === "Doesn't Matter" ? 'active' : '' }}" onclick="selectTag('dosh', "Doesn't Matter", this)">Doesn't Matter</button>
                                        <button type="button" class="filter-tag-btn {{ request('dosh') === 'Yes' ? 'active' : '' }}" onclick="selectTag('dosh', 'Yes', this)">Yes</button>
                                        <button type="button" class="filter-tag-btn {{ request('dosh') === 'No' ? 'active' : '' }}" onclick="selectTag('dosh', 'No', this)">No</button>
                                        <button type="button" class="filter-tag-btn {{ request('dosh') === "Don't Know" ? 'active' : '' }}" onclick="selectTag('dosh', "Don't Know", this)">Don't Know</button>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 4: Family Details --}}
                            <div class="filter-tab-pane" id="filter-pane-family">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Family Type</label>
                                    <input type="hidden" name="family_type" id="familyTypeInput" value="{{ request('family_type', "Doesn't Matter") }}">
                                    <div class="filter-tag-group">
                                        <button type="button" class="filter-tag-btn {{ request('family_type', "Doesn't Matter") === "Doesn't Matter" ? 'active' : '' }}" onclick="selectTag('familyType', "Doesn't Matter", this)">Doesn't Matter</button>
                                        <button type="button" class="filter-tag-btn {{ request('family_type') === 'Nuclear' ? 'active' : '' }}" onclick="selectTag('familyType', 'Nuclear', this)"><i class="fa-solid fa-house-chimney me-1"></i> Nuclear</button>
                                        <button type="button" class="filter-tag-btn {{ request('family_type') === 'Joint' ? 'active' : '' }}" onclick="selectTag('familyType', 'Joint', this)"><i class="fa-solid fa-people-roof me-1"></i> Joint</button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Family Class</label>
                                    <select name="family_class" class="form-select form-select-sm">
                                        <option value="Doesn't Matter">Doesn't Matter</option>
                                        <option value="Rich" {{ request('family_class') === 'Rich' ? 'selected' : '' }}>Rich</option>
                                        <option value="Upper Middle Class" {{ request('family_class') === 'Upper Middle Class' ? 'selected' : '' }}>Upper Middle Class</option>
                                        <option value="Middle Class" {{ request('family_class') === 'Middle Class' ? 'selected' : '' }}>Middle Class</option>
                                        <option value="Lower Middle Class" {{ request('family_class') === 'Lower Middle Class' ? 'selected' : '' }}>Lower Middle Class</option>
                                        <option value="Lower Class" {{ request('family_class') === 'Lower Class' ? 'selected' : '' }}>Lower Class</option>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold">Family Value</label>
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

                            {{-- TAB 5: Location Details --}}
                            <div class="filter-tab-pane" id="filter-pane-location">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Country</label>
                                    <select name="country" class="form-select form-select-sm">
                                        <option value="Any">Any</option>
                                        <option value="India" {{ request('country') === 'India' ? 'selected' : '' }}>India</option>
                                        <option value="USA" {{ request('country') === 'USA' ? 'selected' : '' }}>USA</option>
                                        <option value="UK" {{ request('country') === 'UK' ? 'selected' : '' }}>UK</option>
                                        <option value="Canada" {{ request('country') === 'Canada' ? 'selected' : '' }}>Canada</option>
                                        <option value="Australia" {{ request('country') === 'Australia' ? 'selected' : '' }}>Australia</option>
                                        <option value="Other" {{ request('country') === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">State</label>
                                    <select name="state" class="form-select form-select-sm">
                                        <option value="Any">Any</option>
                                        <option value="Maharashtra" {{ request('state') === 'Maharashtra' ? 'selected' : '' }}>Maharashtra</option>
                                        <option value="Gujarat" {{ request('state') === 'Gujarat' ? 'selected' : '' }}>Gujarat</option>
                                        <option value="Karnataka" {{ request('state') === 'Karnataka' ? 'selected' : '' }}>Karnataka</option>
                                        <option value="Punjab" {{ request('state') === 'Punjab' ? 'selected' : '' }}>Punjab</option>
                                        <option value="Delhi" {{ request('state') === 'Delhi' ? 'selected' : '' }}>Delhi</option>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold">City</label>
                                    <input type="text" name="city" class="form-control form-control-sm" placeholder="Any City" value="{{ request('city') }}">
                                </div>
                            </div>

                            {{-- TAB 6: Lifestyle --}}
                            <div class="filter-tab-pane" id="filter-pane-lifestyle">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Diet</label>
                                    <input type="hidden" name="diet" id="dietInput" value="{{ request('diet', 'Any') }}">
                                    <div class="filter-tag-group">
                                        <button type="button" class="filter-tag-btn {{ request('diet', 'Any') === 'Any' ? 'active' : '' }}" onclick="selectTag('diet', 'Any', this)">Any</button>
                                        <button type="button" class="filter-tag-btn {{ request('diet') === 'Vegetarian' ? 'active' : '' }}" onclick="selectTag('diet', 'Vegetarian', this)">Vegetarian</button>
                                        <button type="button" class="filter-tag-btn {{ request('diet') === 'Non-Vegetarian' ? 'active' : '' }}" onclick="selectTag('diet', 'Non-Vegetarian', this)">Non-Vegetarian</button>
                                        <button type="button" class="filter-tag-btn {{ request('diet') === 'Eggetarian' ? 'active' : '' }}" onclick="selectTag('diet', 'Eggetarian', this)">Eggetarian</button>
                                        <button type="button" class="filter-tag-btn {{ request('diet') === 'Vegan' ? 'active' : '' }}" onclick="selectTag('diet', 'Vegan', this)">Vegan</button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Smoking Habits</label>
                                    <select name="smoking" class="form-select form-select-sm">
                                        <option value="Any">Any</option>
                                        <option value="No" {{ request('smoking') === 'No' ? 'selected' : '' }}>No</option>
                                        <option value="Yes" {{ request('smoking') === 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="Occasionally" {{ request('smoking') === 'Occasionally' ? 'selected' : '' }}>Occasionally</option>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold">Drinking Habits</label>
                                    <select name="drinking" class="form-select form-select-sm">
                                        <option value="Any">Any</option>
                                        <option value="No" {{ request('drinking') === 'No' ? 'selected' : '' }}>No</option>
                                        <option value="Yes" {{ request('drinking') === 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="Occasionally" {{ request('drinking') === 'Occasionally' ? 'selected' : '' }}>Occasionally</option>
                                    </select>
                                </div>
                            </div>

                            {{-- TAB 7: Profile Type --}}
                            <div class="filter-tab-pane" id="filter-pane-profile">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="photo" value="yes" id="photoOnlyCheck" {{ request('photo') === 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="photoOnlyCheck">
                                        Profiles with photo
                                    </label>
                                    <div class="text-secondary small mt-1">Matches who have added photos</div>
                                </div>
                            </div>

                            {{-- TAB 8: Recently Created --}}
                            <div class="filter-tab-pane" id="filter-pane-recent">
                                <label class="form-label fw-bold mb-2">Profile Created</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="created_at" value="all" id="createdAll" {{ request('created_at', 'all') === 'all' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="createdAll">All Time</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="created_at" value="today" id="createdToday" {{ request('created_at') === 'today' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="createdToday">Today</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="created_at" value="last_7_days" id="createdSeven" {{ request('created_at') === 'last_7_days' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="createdSeven">Last 7 Days</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="created_at" value="last_30_days" id="createdThirty" {{ request('created_at') === 'last_30_days' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="createdThirty">Last 30 Days</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="created_at" value="one_week" id="createdOneWeek" {{ request('created_at') === 'one_week' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="createdOneWeek">One Week</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="created_at" value="one_month" id="createdOneMonth" {{ request('created_at') === 'one_month' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="createdOneMonth">One Month</label>
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
                                        <div class="profile-photo-placeholder">
                                            <i class="fa-solid fa-{{ ($pd['gender']??'') === 'female' ? 'female' : 'male' }}"></i>
                                        </div>
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
    function showFilterPane(paneName, tabBtn) {
        // Hide all filter tab content sheets
        document.querySelectorAll('.filter-tab-pane').forEach(el => {
            el.classList.remove('active');
        });
        
        // Show target content sheet
        document.getElementById('filter-pane-' + paneName).classList.add('active');

        // Remove active highlights from category tab sidebar
        document.querySelectorAll('.discover-tab-link').forEach(el => {
            el.classList.remove('active');
        });
        
        // Highlight active clicked category
        tabBtn.classList.add('active');
    }

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
