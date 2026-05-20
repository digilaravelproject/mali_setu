@extends('layouts.app')

@section('content')
<style>
.tab-nav-pill { border-radius: 50px; padding: 10px 24px; font-weight: 600; font-size: 0.9rem; border: none; background: transparent; color: #6c757d; cursor: pointer; transition: all 0.3s; }
.tab-nav-pill.active, .tab-nav-pill:hover { background: var(--primary); color: #fff; }
.tab-section { display: none; }
.tab-section.active { display: block; }
.step-indicator { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; }
.step-indicator.done { background: var(--primary); color: white; }
.step-indicator.current { border: 2px solid var(--primary); color: var(--primary); }
.step-indicator.todo { background: #e9ecef; color: #adb5bd; }
</style>

<div class="py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('matrimony.index') }}" class="btn btn-light btn-sm rounded-3"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h4 class="fw-bold mb-0">Create Matrimony Profile</h4>
            <p class="text-secondary small mb-0">Fill all sections to complete your matrimony profile.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first() }}</div>
    @endif

    {{-- Tab Navigation --}}
    <div class="glass-card p-3 mb-4">
        <div class="d-flex flex-wrap gap-2" id="tab-nav">
            <button class="tab-nav-pill active" onclick="showTab('personal', this)"><i class="fa-solid fa-user me-1"></i> 1. Personal</button>
            <button class="tab-nav-pill" onclick="showTab('family', this)"><i class="fa-solid fa-people-roof me-1"></i> 2. Family</button>
            <button class="tab-nav-pill" onclick="showTab('education', this)"><i class="fa-solid fa-graduation-cap me-1"></i> 3. Education</button>
            <button class="tab-nav-pill" onclick="showTab('location', this)"><i class="fa-solid fa-location-dot me-1"></i> 4. Location</button>
            <button class="tab-nav-pill" onclick="showTab('preferences', this)"><i class="fa-solid fa-heart me-1"></i> 5. Preferences</button>
        </div>
    </div>

    <form action="{{ route('matrimony.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- TAB 1: Personal --}}
        <div class="tab-section active glass-card mb-4" id="tab-personal">
            <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-user me-2"></i> Personal Details</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Gender *</label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender')=='male'?'selected':'' }}>Male</option>
                        <option value="female" {{ old('gender')=='female'?'selected':'' }}>Female</option>
                        <option value="other" {{ old('gender')=='other'?'selected':'' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Age *</label>
                    <input type="number" name="age" class="form-control" min="18" max="100" value="{{ old('age') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Marital Status *</label>
                    <select name="marital_status" class="form-select" required>
                        <option value="">Select Status</option>
                        <option value="never_married">Never Married</option>
                        <option value="divorced">Divorced</option>
                        <option value="widowed">Widowed</option>
                        <option value="awaiting_divorce">Awaiting Divorce</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mother Tongue *</label>
                    <input type="text" name="mother_tongue" class="form-control" value="{{ old('mother_tongue') }}" placeholder="E.g. Marathi" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Profile Created By *</label>
                    <select name="profile_created_by" class="form-select" required>
                        <option value="">Select</option>
                        <option value="self">Self</option>
                        <option value="parent">Parent</option>
                        <option value="sibling">Sibling</option>
                        <option value="relative">Relative</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Religion *</label>
                    <input type="text" name="religion" class="form-control" value="{{ old('religion') }}" placeholder="E.g. Hindu" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Caste *</label>
                    <select name="caste" class="form-select" required>
                        <option value="">Select Caste</option>
                        @foreach($casts as $cast)
                            <option value="{{ $cast->name }}">{{ $cast->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sub Caste</label>
                    <input type="text" name="sub_caste" class="form-control" value="{{ old('sub_caste') }}" placeholder="Sub caste (optional)">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Height</label>
                    <input type="text" name="height" class="form-control" value="{{ old('height') }}" placeholder="E.g. 5'7&quot;">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Weight</label>
                    <input type="text" name="weight" class="form-control" value="{{ old('weight') }}" placeholder="E.g. 65 kg">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Complexion</label>
                    <select name="complexion" class="form-select">
                        <option value="">Select</option>
                        <option value="fair">Fair</option>
                        <option value="wheatish">Wheatish</option>
                        <option value="dark">Dark</option>
                        <option value="very_fair">Very Fair</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Physical Status</label>
                    <select name="physical_status" class="form-select">
                        <option value="">Select</option>
                        <option value="normal">Normal</option>
                        <option value="physically_challenged">Physically Challenged</option>
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">About Me</label>
                    <textarea name="about_me" class="form-control" rows="3" placeholder="Write a short introduction about yourself...">{{ old('about_me') }}</textarea>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Profile Photos</label>
                    <input type="file" name="photos[]" class="form-control" multiple accept=".jpg,.jpeg,.png">
                    <small class="text-muted">Upload up to 5 photos (JPG/PNG, max 2MB each)</small>
                </div>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-primary rounded-3 px-4" onclick="showTab('family', document.querySelectorAll('.tab-nav-pill')[1])">Next: Family <i class="fa-solid fa-arrow-right ms-1"></i></button>
            </div>
        </div>

        {{-- TAB 2: Family --}}
        <div class="tab-section glass-card mb-4" id="tab-family">
            <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-people-roof me-2"></i> Family Details</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Father's Name</label>
                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}" placeholder="Father's full name">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Father's Occupation</label>
                    <input type="text" name="father_occupation" class="form-control" value="{{ old('father_occupation') }}" placeholder="E.g. Farmer, Business">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mother's Name</label>
                    <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name') }}" placeholder="Mother's full name">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mother's Occupation</label>
                    <input type="text" name="mother_occupation" class="form-control" value="{{ old('mother_occupation') }}" placeholder="E.g. Homemaker">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">No. of Brothers</label>
                    <input type="number" name="no_of_brothers" class="form-control" min="0" value="{{ old('no_of_brothers', 0) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">No. of Sisters</label>
                    <input type="number" name="no_of_sisters" class="form-control" min="0" value="{{ old('no_of_sisters', 0) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Family Type</label>
                    <select name="family_type" class="form-select">
                        <option value="">Select</option>
                        <option value="joint">Joint Family</option>
                        <option value="nuclear">Nuclear Family</option>
                        <option value="extended">Extended Family</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Family Status</label>
                    <select name="family_status" class="form-select">
                        <option value="">Select</option>
                        <option value="middle_class">Middle Class</option>
                        <option value="upper_middle_class">Upper Middle Class</option>
                        <option value="rich">Rich</option>
                        <option value="affluent">Affluent</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Family Values</label>
                    <select name="family_values" class="form-select">
                        <option value="">Select</option>
                        <option value="orthodox">Orthodox</option>
                        <option value="traditional">Traditional</option>
                        <option value="moderate">Moderate</option>
                        <option value="liberal">Liberal</option>
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">About Family</label>
                    <textarea name="about_family" class="form-control" rows="3" placeholder="Describe your family background...">{{ old('about_family') }}</textarea>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-light rounded-3 px-4" onclick="showTab('personal', document.querySelectorAll('.tab-nav-pill')[0])"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                <button type="button" class="btn btn-primary rounded-3 px-4" onclick="showTab('education', document.querySelectorAll('.tab-nav-pill')[2])">Next: Education <i class="fa-solid fa-arrow-right ms-1"></i></button>
            </div>
        </div>

        {{-- TAB 3: Education & Career --}}
        <div class="tab-section glass-card mb-4" id="tab-education">
            <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-graduation-cap me-2"></i> Education & Career</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Highest Qualification *</label>
                    <select name="highest_qualification" class="form-select" required>
                        <option value="">Select Qualification</option>
                        <option value="High School">High School</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Bachelor's Degree">Bachelor's Degree</option>
                        <option value="Master's Degree">Master's Degree</option>
                        <option value="Doctorate / PhD">Doctorate / PhD</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">College / University</label>
                    <input type="text" name="college_name" class="form-control" value="{{ old('college_name') }}" placeholder="College or university name">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Passing Year</label>
                    <input type="number" name="passing_year" class="form-control" min="1980" max="2030" value="{{ old('passing_year') }}" placeholder="E.g. 2018">
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Occupation *</label>
                    <input type="text" name="occupation" class="form-control" value="{{ old('occupation') }}" placeholder="E.g. Software Engineer" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Employment Type</label>
                    <select name="employment_type" class="form-select">
                        <option value="">Select</option>
                        <option value="private">Private Sector</option>
                        <option value="government">Government</option>
                        <option value="business">Business / Self-Employed</option>
                        <option value="ngo">NGO / Non-Profit</option>
                        <option value="not_working">Not Working</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="Current employer">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Annual Income</label>
                    <select name="annual_income" class="form-select">
                        <option value="">Select Income Range</option>
                        <option value="below_2lac">Below ₹2 Lakh</option>
                        <option value="2_5lac">₹2 – 5 Lakh</option>
                        <option value="5_10lac">₹5 – 10 Lakh</option>
                        <option value="10_20lac">₹10 – 20 Lakh</option>
                        <option value="above_20lac">Above ₹20 Lakh</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-light rounded-3 px-4" onclick="showTab('family', document.querySelectorAll('.tab-nav-pill')[1])"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                <button type="button" class="btn btn-primary rounded-3 px-4" onclick="showTab('location', document.querySelectorAll('.tab-nav-pill')[3])">Next: Location <i class="fa-solid fa-arrow-right ms-1"></i></button>
            </div>
        </div>

        {{-- TAB 4: Location & Lifestyle --}}
        <div class="tab-section glass-card mb-4" id="tab-location">
            <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-location-dot me-2"></i> Location & Lifestyle</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Pincode</label>
                    <input type="text" name="pincode" class="form-control" id="matrimony-pincode" maxlength="6" value="{{ old('pincode') }}" placeholder="6-digit pincode">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Country *</label>
                    <input type="text" name="country" class="form-control" id="m-country" value="{{ old('country', 'India') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">State *</label>
                    <input type="text" name="state" class="form-control" id="m-state" value="{{ old('state') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">City *</label>
                    <input type="text" name="city" class="form-control" id="m-city" value="{{ old('city') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Diet</label>
                    <select name="diet" class="form-select">
                        <option value="">Select</option>
                        <option value="vegetarian">Vegetarian</option>
                        <option value="non_vegetarian">Non-Vegetarian</option>
                        <option value="eggetarian">Eggetarian</option>
                        <option value="vegan">Vegan</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Smoking</label>
                    <select name="smoking" class="form-select">
                        <option value="">Select</option>
                        <option value="no">No</option>
                        <option value="occasionally">Occasionally</option>
                        <option value="yes">Yes</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Drinking</label>
                    <select name="drinking" class="form-select">
                        <option value="">Select</option>
                        <option value="no">No</option>
                        <option value="occasionally">Occasionally</option>
                        <option value="yes">Yes</option>
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Hobbies & Interests</label>
                    <input type="text" name="hobbies" class="form-control" value="{{ old('hobbies') }}" placeholder="E.g. Reading, Cooking, Sports...">
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-light rounded-3 px-4" onclick="showTab('education', document.querySelectorAll('.tab-nav-pill')[2])"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                <button type="button" class="btn btn-primary rounded-3 px-4" onclick="showTab('preferences', document.querySelectorAll('.tab-nav-pill')[4])">Next: Partner Preferences <i class="fa-solid fa-arrow-right ms-1"></i></button>
            </div>
        </div>

        {{-- TAB 5: Partner Preferences --}}
        <div class="tab-section glass-card mb-4" id="tab-preferences">
            <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-heart me-2"></i> Partner Preferences</h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Preferred Age Min</label>
                    <input type="number" name="pref_age_min" class="form-control" min="18" value="{{ old('pref_age_min') }}" placeholder="Min age">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Preferred Age Max</label>
                    <input type="number" name="pref_age_max" class="form-control" max="100" value="{{ old('pref_age_max') }}" placeholder="Max age">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Min Height</label>
                    <input type="text" name="pref_height_min" class="form-control" value="{{ old('pref_height_min') }}" placeholder="E.g. 5'2&quot;">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Preferred Religion</label>
                    <input type="text" name="pref_religion" class="form-control" value="{{ old('pref_religion') }}" placeholder="E.g. Hindu">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Preferred Caste</label>
                    <input type="text" name="pref_caste" class="form-control" value="{{ old('pref_caste') }}" placeholder="Any / Specific caste">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Preferred Education</label>
                    <select name="pref_education" class="form-select">
                        <option value="">Any</option>
                        <option value="Bachelor's Degree">Bachelor's Degree</option>
                        <option value="Master's Degree">Master's Degree</option>
                        <option value="Doctorate / PhD">Doctorate / PhD</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Preferred Income</label>
                    <select name="pref_income" class="form-select">
                        <option value="">Any</option>
                        <option value="2_5lac">₹2 – 5 Lakh</option>
                        <option value="5_10lac">₹5 – 10 Lakh</option>
                        <option value="10_20lac">₹10 – 20 Lakh</option>
                        <option value="above_20lac">Above ₹20 Lakh</option>
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Preferred Location</label>
                    <input type="text" name="pref_location" class="form-control" value="{{ old('pref_location') }}" placeholder="E.g. Pune, Maharashtra or Any">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">About My Ideal Partner</label>
                    <textarea name="about_partner" class="form-control" rows="4" placeholder="Describe what you are looking for in a life partner...">{{ old('about_partner') }}</textarea>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-light rounded-3 px-4" onclick="showTab('location', document.querySelectorAll('.tab-nav-pill')[3])"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                <button type="submit" class="btn btn-primary btn-lg rounded-3 px-5 fw-bold shadow-sm">
                    <i class="fa-solid fa-check-circle me-2"></i> Submit & Create Profile
                </button>
            </div>
        </div>

    </form>
</div>

<script>
function showTab(tabId, btn) {
    document.querySelectorAll('.tab-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.tab-nav-pill').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tabId).classList.add('active');
    if (btn) btn.classList.add('active');
}

// Pincode auto-fill
document.getElementById('matrimony-pincode').addEventListener('input', function() {
    const pin = this.value.trim();
    if (pin.length === 6 && /^\d+$/.test(pin)) {
        fetch(`https://api.postalpincode.in/pincode/${pin}`)
            .then(r => r.json()).then(data => {
                if (data && data[0] && data[0].Status === 'Success') {
                    const po = data[0].PostOffice[0];
                    document.getElementById('m-state').value = po.State;
                    document.getElementById('m-city').value = po.District;
                }
            });
    }
});
</script>
@endsection
