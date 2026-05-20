@extends('layouts.app')

@section('content')
<style>
.tab-nav-pill { border-radius: 50px; padding: 10px 24px; font-weight: 600; font-size: 0.9rem; border: none; background: transparent; color: #6c757d; cursor: pointer; transition: all 0.3s; }
.tab-nav-pill.active, .tab-nav-pill:hover { background: var(--primary); color: #fff; }
.tab-section { display: none; }
.tab-section.active { display: block; }
</style>

<div class="py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('matrimony.index') }}" class="btn btn-light btn-sm rounded-3"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h4 class="fw-bold mb-0">Edit Matrimony Profile</h4>
            <p class="text-secondary small mb-0">Update your information and save changes.</p>
        </div>
    </div>

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
    @endphp

    <div class="glass-card p-3 mb-4">
        <div class="d-flex flex-wrap gap-2">
            <button class="tab-nav-pill active" onclick="showTab('personal', this)"><i class="fa-solid fa-user me-1"></i> 1. Personal</button>
            <button class="tab-nav-pill" onclick="showTab('family', this)"><i class="fa-solid fa-people-roof me-1"></i> 2. Family</button>
            <button class="tab-nav-pill" onclick="showTab('education', this)"><i class="fa-solid fa-graduation-cap me-1"></i> 3. Education</button>
            <button class="tab-nav-pill" onclick="showTab('location', this)"><i class="fa-solid fa-location-dot me-1"></i> 4. Location</button>
            <button class="tab-nav-pill" onclick="showTab('preferences', this)"><i class="fa-solid fa-heart me-1"></i> 5. Preferences</button>
        </div>
    </div>

    <form action="{{ route('matrimony.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- TAB 1: Personal --}}
        <div class="tab-section active glass-card mb-4" id="tab-personal">
            <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-user me-2"></i> Personal Details</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="male" {{ ($pd['gender']??'')==='male'?'selected':'' }}>Male</option>
                        <option value="female" {{ ($pd['gender']??'')==='female'?'selected':'' }}>Female</option>
                        <option value="other" {{ ($pd['gender']??'')==='other'?'selected':'' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ $pd['date_of_birth'] ?? '' }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Age *</label>
                    <input type="number" name="age" class="form-control" min="18" max="100" value="{{ $profile->age }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Marital Status</label>
                    <select name="marital_status" class="form-select">
                        <option value="never_married" {{ ($pd['marital_status']??'')==='never_married'?'selected':'' }}>Never Married</option>
                        <option value="divorced" {{ ($pd['marital_status']??'')==='divorced'?'selected':'' }}>Divorced</option>
                        <option value="widowed" {{ ($pd['marital_status']??'')==='widowed'?'selected':'' }}>Widowed</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mother Tongue</label>
                    <input type="text" name="mother_tongue" class="form-control" value="{{ $pd['mother_tongue'] ?? '' }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Religion</label>
                    <input type="text" name="religion" class="form-control" value="{{ $pd['religion'] ?? '' }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Caste</label>
                    <select name="caste" class="form-select">
                        @foreach($casts as $cast)
                            <option value="{{ $cast->name }}" {{ ($pd['caste']??'')===$cast->name?'selected':'' }}>{{ $cast->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sub Caste</label>
                    <input type="text" name="sub_caste" class="form-control" value="{{ $pd['sub_caste'] ?? '' }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Height</label>
                    <input type="text" name="height" class="form-control" value="{{ $profile->height ?? '' }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Weight</label>
                    <input type="text" name="weight" class="form-control" value="{{ $profile->weight ?? '' }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Complexion</label>
                    <select name="complexion" class="form-select">
                        <option value="fair" {{ ($profile->complexion??'')==='fair'?'selected':'' }}>Fair</option>
                        <option value="wheatish" {{ ($profile->complexion??'')==='wheatish'?'selected':'' }}>Wheatish</option>
                        <option value="dark" {{ ($profile->complexion??'')==='dark'?'selected':'' }}>Dark</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Physical Status</label>
                    <select name="physical_status" class="form-select">
                        <option value="normal" {{ ($profile->physical_status??'')==='normal'?'selected':'' }}>Normal</option>
                        <option value="physically_challenged" {{ ($profile->physical_status??'')==='physically_challenged'?'selected':'' }}>Physically Challenged</option>
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">About Me</label>
                    <textarea name="about_me" class="form-control" rows="3">{{ $pd['about_me'] ?? '' }}</textarea>
                </div>
                @if(!empty($pd['photos']))
                <div class="col-12 mb-3">
                    <label class="form-label">Existing Photos</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($pd['photos'] as $photo)
                            <img src="{{ asset('storage/' . $photo) }}" style="width:80px;height:80px;border-radius:10px;object-fit:cover;">
                        @endforeach
                    </div>
                </div>
                @endif
                <div class="col-12 mb-3">
                    <label class="form-label">Add More Photos</label>
                    <input type="file" name="photos[]" class="form-control" multiple accept=".jpg,.jpeg,.png">
                </div>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-primary rounded-3 px-4" onclick="showTab('family', document.querySelectorAll('.tab-nav-pill')[1])">Next <i class="fa-solid fa-arrow-right ms-1"></i></button>
            </div>
        </div>

        {{-- TAB 2: Family --}}
        <div class="tab-section glass-card mb-4" id="tab-family">
            <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-people-roof me-2"></i> Family Details</h5>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Father's Name</label><input type="text" name="father_name" class="form-control" value="{{ $fd['father_name'] ?? '' }}"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Father's Occupation</label><input type="text" name="father_occupation" class="form-control" value="{{ $fd['father_occupation'] ?? '' }}"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Mother's Name</label><input type="text" name="mother_name" class="form-control" value="{{ $fd['mother_name'] ?? '' }}"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Mother's Occupation</label><input type="text" name="mother_occupation" class="form-control" value="{{ $fd['mother_occupation'] ?? '' }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Brothers</label><input type="number" name="no_of_brothers" class="form-control" min="0" value="{{ $fd['no_of_brothers'] ?? 0 }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Sisters</label><input type="number" name="no_of_sisters" class="form-control" min="0" value="{{ $fd['no_of_sisters'] ?? 0 }}"></div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Family Type</label>
                    <select name="family_type" class="form-select">
                        <option value="joint" {{ ($fd['family_type']??'')==='joint'?'selected':'' }}>Joint</option>
                        <option value="nuclear" {{ ($fd['family_type']??'')==='nuclear'?'selected':'' }}>Nuclear</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Family Status</label>
                    <select name="family_status" class="form-select">
                        <option value="middle_class" {{ ($fd['family_class']??'')==='middle_class'?'selected':'' }}>Middle Class</option>
                        <option value="upper_middle_class" {{ ($fd['family_class']??'')==='upper_middle_class'?'selected':'' }}>Upper Middle Class</option>
                        <option value="rich" {{ ($fd['family_class']??'')==='rich'?'selected':'' }}>Rich</option>
                    </select>
                </div>
                <div class="col-12 mb-3"><label class="form-label">About Family</label><textarea name="about_family" class="form-control" rows="3">{{ $fd['about_family'] ?? '' }}</textarea></div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-light rounded-3 px-4" onclick="showTab('personal', document.querySelectorAll('.tab-nav-pill')[0])"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                <button type="button" class="btn btn-primary rounded-3 px-4" onclick="showTab('education', document.querySelectorAll('.tab-nav-pill')[2])">Next <i class="fa-solid fa-arrow-right ms-1"></i></button>
            </div>
        </div>

        {{-- TAB 3: Education --}}
        <div class="tab-section glass-card mb-4" id="tab-education">
            <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-graduation-cap me-2"></i> Education & Career</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Highest Qualification *</label>
                    <select name="highest_qualification" class="form-select" required>
                        <option value="Bachelor's Degree" {{ ($ed['highest_qualification']??'')==="Bachelor's Degree"?'selected':'' }}>Bachelor's Degree</option>
                        <option value="Master's Degree" {{ ($ed['highest_qualification']??'')==="Master's Degree"?'selected':'' }}>Master's Degree</option>
                        <option value="Doctorate / PhD" {{ ($ed['highest_qualification']??'')==="Doctorate / PhD"?'selected':'' }}>Doctorate / PhD</option>
                        <option value="Diploma" {{ ($ed['highest_qualification']??'')==="Diploma"?'selected':'' }}>Diploma</option>
                        <option value="High School" {{ ($ed['highest_qualification']??'')==="High School"?'selected':'' }}>High School</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label">College</label><input type="text" name="college_name" class="form-control" value="{{ $ed['college_name'] ?? '' }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Passing Year</label><input type="number" name="passing_year" class="form-control" value="{{ $ed['passing_year'] ?? '' }}"></div>
                <div class="col-md-5 mb-3"><label class="form-label">Occupation *</label><input type="text" name="occupation" class="form-control" value="{{ $pro['occupation'] ?? '' }}" required></div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Employment Type</label>
                    <select name="employment_type" class="form-select">
                        <option value="private" {{ ($pro['employment_type']??'')==='private'?'selected':'' }}>Private</option>
                        <option value="government" {{ ($pro['employment_type']??'')==='government'?'selected':'' }}>Government</option>
                        <option value="business" {{ ($pro['employment_type']??'')==='business'?'selected':'' }}>Business</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label">Company</label><input type="text" name="company_name" class="form-control" value="{{ $pro['company_name'] ?? '' }}"></div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Annual Income</label>
                    <select name="annual_income" class="form-select">
                        <option value="below_2lac" {{ ($pro['annual_income']??'')==='below_2lac'?'selected':'' }}>Below ₹2L</option>
                        <option value="2_5lac" {{ ($pro['annual_income']??'')==='2_5lac'?'selected':'' }}>₹2-5L</option>
                        <option value="5_10lac" {{ ($pro['annual_income']??'')==='5_10lac'?'selected':'' }}>₹5-10L</option>
                        <option value="10_20lac" {{ ($pro['annual_income']??'')==='10_20lac'?'selected':'' }}>₹10-20L</option>
                        <option value="above_20lac" {{ ($pro['annual_income']??'')==='above_20lac'?'selected':'' }}>Above ₹20L</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-light rounded-3 px-4" onclick="showTab('family', document.querySelectorAll('.tab-nav-pill')[1])"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                <button type="button" class="btn btn-primary rounded-3 px-4" onclick="showTab('location', document.querySelectorAll('.tab-nav-pill')[3])">Next <i class="fa-solid fa-arrow-right ms-1"></i></button>
            </div>
        </div>

        {{-- TAB 4: Location --}}
        <div class="tab-section glass-card mb-4" id="tab-location">
            <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-location-dot me-2"></i> Location & Lifestyle</h5>
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Pincode</label><input type="text" name="pincode" class="form-control" id="edit-pincode" maxlength="6" value="{{ $ld['pincode'] ?? '' }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Country *</label><input type="text" name="country" class="form-control" id="e-country" value="{{ $ld['country'] ?? 'India' }}" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">State *</label><input type="text" name="state" class="form-control" id="e-state" value="{{ $ld['state'] ?? '' }}" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">City *</label><input type="text" name="city" class="form-control" id="e-city" value="{{ $ld['city'] ?? '' }}" required></div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Diet</label>
                    <select name="diet" class="form-select">
                        <option value="vegetarian" {{ ($lf['diet']??'')==='vegetarian'?'selected':'' }}>Vegetarian</option>
                        <option value="non_vegetarian" {{ ($lf['diet']??'')==='non_vegetarian'?'selected':'' }}>Non-Vegetarian</option>
                        <option value="eggetarian" {{ ($lf['diet']??'')==='eggetarian'?'selected':'' }}>Eggetarian</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Smoking</label>
                    <select name="smoking" class="form-select">
                        <option value="no" {{ ($lf['smoking']??'')==='no'?'selected':'' }}>No</option>
                        <option value="occasionally" {{ ($lf['smoking']??'')==='occasionally'?'selected':'' }}>Occasionally</option>
                        <option value="yes" {{ ($lf['smoking']??'')==='yes'?'selected':'' }}>Yes</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Drinking</label>
                    <select name="drinking" class="form-select">
                        <option value="no" {{ ($lf['drinking']??'')==='no'?'selected':'' }}>No</option>
                        <option value="occasionally" {{ ($lf['drinking']??'')==='occasionally'?'selected':'' }}>Occasionally</option>
                        <option value="yes" {{ ($lf['drinking']??'')==='yes'?'selected':'' }}>Yes</option>
                    </select>
                </div>
                <div class="col-md-8 mb-3"><label class="form-label">Hobbies</label><input type="text" name="hobbies" class="form-control" value="{{ $lf['hobbies'] ?? '' }}"></div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-light rounded-3 px-4" onclick="showTab('education', document.querySelectorAll('.tab-nav-pill')[2])"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                <button type="button" class="btn btn-primary rounded-3 px-4" onclick="showTab('preferences', document.querySelectorAll('.tab-nav-pill')[4])">Next <i class="fa-solid fa-arrow-right ms-1"></i></button>
            </div>
        </div>

        {{-- TAB 5: Preferences --}}
        <div class="tab-section glass-card mb-4" id="tab-preferences">
            <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-heart me-2"></i> Partner Preferences</h5>
            <div class="row">
                <div class="col-md-3 mb-3"><label class="form-label">Age Min</label><input type="number" name="pref_age_min" class="form-control" value="{{ $pp['age_min'] ?? '' }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Age Max</label><input type="number" name="pref_age_max" class="form-control" value="{{ $pp['age_max'] ?? '' }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Min Height</label><input type="text" name="pref_height_min" class="form-control" value="{{ $pp['height_min'] ?? '' }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Religion</label><input type="text" name="pref_religion" class="form-control" value="{{ $pp['religion'] ?? '' }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Caste</label><input type="text" name="pref_caste" class="form-control" value="{{ $pp['caste'] ?? '' }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Education</label><input type="text" name="pref_education" class="form-control" value="{{ $pp['education'] ?? '' }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Income</label><input type="text" name="pref_income" class="form-control" value="{{ $pp['income'] ?? '' }}"></div>
                <div class="col-12 mb-3"><label class="form-label">Preferred Location</label><input type="text" name="pref_location" class="form-control" value="{{ $pp['location'] ?? '' }}"></div>
                <div class="col-12 mb-3"><label class="form-label">About Ideal Partner</label><textarea name="about_partner" class="form-control" rows="4">{{ $pp['about_partner'] ?? '' }}</textarea></div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-light rounded-3 px-4" onclick="showTab('location', document.querySelectorAll('.tab-nav-pill')[3])"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                <button type="submit" class="btn btn-primary btn-lg rounded-3 px-5 fw-bold shadow-sm"><i class="fa-solid fa-floppy-disk me-2"></i> Save Changes</button>
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
document.getElementById('edit-pincode').addEventListener('input', function() {
    const pin = this.value.trim();
    if (pin.length === 6 && /^\d+$/.test(pin)) {
        fetch(`https://api.postalpincode.in/pincode/${pin}`).then(r => r.json()).then(data => {
            if (data?.[0]?.Status === 'Success') {
                const po = data[0].PostOffice[0];
                document.getElementById('e-state').value = po.State;
                document.getElementById('e-city').value = po.District;
            }
        });
    }
});
</script>
@endsection
