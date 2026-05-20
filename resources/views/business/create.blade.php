@extends('layouts.app')

@section('content')
<div class="row g-4 justify-content-center text-start">
    <div class="col-lg-10 col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dashboard.business.index') }}" class="btn btn-light btn-sm rounded-3">
                    <i class="fa-solid fa-arrow-left"></i> Back to Hub
                </a>
                <h4 class="fw-bold mb-0 text-dark">Register Your Business</h4>
            </div>
        </div>

        <div class="glass-card p-4 border shadow-sm">
            <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-store me-2"></i> Initial Business Setup</h5>
            <p class="text-secondary small mb-4">Join our premium local business directory to increase visibility, showcase products, and post recruitment offers.</p>

            @if($errors->any())
                <div class="alert alert-danger rounded-3">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Multi-step Navigation tabs -->
            <ul class="nav nav-tabs nav-fill mb-4 border-0 bg-light p-1 rounded-3" id="setupFormTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold small py-2.5 rounded-3" id="step1-tab" data-bs-toggle="tab" data-bs-target="#step1" type="button" role="tab"><span class="badge bg-secondary me-1">1</span> Basic Profile</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold small py-2.5 rounded-3" id="step2-tab" data-bs-toggle="tab" data-bs-target="#step2" type="button" role="tab"><span class="badge bg-secondary me-1">2</span> Contact Info</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold small py-2.5 rounded-3" id="step3-tab" data-bs-toggle="tab" data-bs-target="#step3" type="button" role="tab"><span class="badge bg-secondary me-1">3</span> Address & Location</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold small py-2.5 rounded-3" id="step4-tab" data-bs-toggle="tab" data-bs-target="#step4" type="button" role="tab"><span class="badge bg-secondary me-1">4</span> Photos & Finish</button>
                </li>
            </ul>

            <form action="{{ route('dashboard.business.register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="tab-content" id="setupFormContent">
                    
                    <!-- Step 1: Basic Profile -->
                    <div class="tab-pane fade show active" id="step1" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Business Name *</label>
                                <input type="text" name="business_name" class="form-control" placeholder="E.g. Mali Agri Services" required value="{{ old('business_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Business Type *</label>
                                <select name="business_type" class="form-select" required>
                                    <option value="Retailer" {{ old('business_type') === 'Retailer' ? 'selected' : '' }}>Retailer</option>
                                    <option value="Wholesaler" {{ old('business_type') === 'Wholesaler' ? 'selected' : '' }}>Wholesaler</option>
                                    <option value="Manufacturer" {{ old('business_type') === 'Manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                    <option value="Service Provider" {{ old('business_type') === 'Service Provider' ? 'selected' : '' }}>Service Provider</option>
                                    <option value="Distributor" {{ old('business_type') === 'Distributor' ? 'selected' : '' }}>Distributor</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Business Category *</label>
                            <select name="category_id" class="form-select" required>
                                <option value="" disabled selected>Select business category...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary small">Description *</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Describe your business operations, products, and specialties..." required>{{ old('description') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary rounded-3 px-4 py-2.5" onclick="nextTab('step2-tab')">Next Step <i class="fa-solid fa-arrow-right ms-1"></i></button>
                        </div>
                    </div>

                    <!-- Step 2: Contact Info -->
                    <div class="tab-pane fade" id="step2" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Contact Email</label>
                                <input type="email" name="contact_email" class="form-control" placeholder="business@example.com" value="{{ old('contact_email', Auth::user()->email) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Contact Phone</label>
                                <input type="text" name="contact_phone" class="form-control" placeholder="10-digit number" value="{{ old('contact_phone', Auth::user()->phone) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Website URL</label>
                            <input type="url" name="website" class="form-control" placeholder="https://example.com" value="{{ old('website') }}">
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Opening Time</label>
                                <input type="time" name="opening_time" class="form-control" value="{{ old('opening_time', '09:00') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Closing Time</label>
                                <input type="time" name="closing_time" class="form-control" value="{{ old('closing_time', '21:00') }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-light rounded-3 px-4 py-2.5" onclick="prevTab('step1-tab')"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                            <button type="button" class="btn btn-primary rounded-3 px-4 py-2.5" onclick="nextTab('step3-tab')">Next Step <i class="fa-solid fa-arrow-right ms-1"></i></button>
                        </div>
                    </div>

                    <!-- Step 3: Address & Location -->
                    <div class="tab-pane fade" id="step3" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Pincode *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-secondary"><i class="fa-solid fa-map-pin"></i></span>
                                    <input type="text" name="pincode" id="pincode_field" class="form-control" placeholder="6-digit pincode" maxlength="6" required value="{{ old('pincode') }}">
                                    <button class="btn btn-outline-secondary" type="button" id="lookup_pincode_btn">Verify</button>
                                </div>
                                <div id="pincode_spinner" class="spinner-border spinner-border-sm text-primary mt-2" role="status" style="display: none;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Country *</label>
                                <input type="text" name="country" value="India" class="form-control" required readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Address Line *</label>
                            <input type="text" name="address" class="form-control" placeholder="Building, Street, Landmark" required value="{{ old('address') }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">State *</label>
                                <input type="text" name="state" id="state_field" class="form-control" placeholder="State" required value="{{ old('state') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">City *</label>
                                <input type="text" name="city" id="city_field" class="form-control" placeholder="City" required value="{{ old('city') }}">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">District *</label>
                                <input type="text" name="district" id="district_field" class="form-control" placeholder="District" required value="{{ old('district') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Taluka</label>
                                <input type="text" name="taluka" id="taluka_field" class="form-control" placeholder="Taluka" value="{{ old('taluka') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Village</label>
                                <input type="text" name="village" id="village_field" class="form-control" placeholder="Village" value="{{ old('village') }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-light rounded-3 px-4 py-2.5" onclick="prevTab('step2-tab')"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                            <button type="button" class="btn btn-primary rounded-3 px-4 py-2.5" onclick="nextTab('step4-tab')">Next Step <i class="fa-solid fa-arrow-right ms-1"></i></button>
                        </div>
                    </div>

                    <!-- Step 4: Photos & Finish -->
                    <div class="tab-pane fade" id="step4" role="tabpanel">
                        <div class="p-4 rounded-4 bg-light text-center mb-4 border">
                            <div class="fs-1 text-primary mb-3"><i class="fa-solid fa-images"></i></div>
                            <h6 class="fw-bold mb-2">Upload Enterprise Gallery Photos</h6>
                            <p class="text-secondary small mb-4">Add visual catalog storefront photos. High quality JPG/PNG images work best.</p>
                            <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted d-block mt-2">You can select multiple photos at once.</small>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-light rounded-3 px-4 py-2.5" onclick="prevTab('step3-tab')"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                            <button type="submit" class="btn btn-primary btn-lg rounded-3 px-5 fw-bold shadow-sm">Complete Registration <i class="fa-solid fa-circle-check ms-1"></i></button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function nextTab(tabId) {
        const tabEl = document.getElementById(tabId);
        if (tabEl) {
            const tab = new bootstrap.Tab(tabEl);
            tab.show();
        }
    }

    function prevTab(tabId) {
        const tabEl = document.getElementById(tabId);
        if (tabEl) {
            const tab = new bootstrap.Tab(tabEl);
            tab.show();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const pincodeField = document.getElementById('pincode_field');
        const lookupBtn = document.getElementById('lookup_pincode_btn');
        const spinner = document.getElementById('pincode_spinner');

        function lookupPincode() {
            const pincode = pincodeField.value.trim();
            if (pincode.length === 6 && /^\d+$/.test(pincode)) {
                spinner.style.display = 'inline-block';
                pincodeField.classList.remove('is-invalid');

                fetch(`https://api.postalpincode.in/pincode/${pincode}`)
                    .then(res => res.json())
                    .then(data => {
                        spinner.style.display = 'none';
                        if (data && data[0] && data[0].Status === 'Success') {
                            const postOffice = data[0].PostOffice[0];
                            const state = postOffice.State;
                            const city = postOffice.District; 
                            
                            const stateInput = document.getElementById('state_field');
                            const cityInput = document.getElementById('city_field');
                            const districtInput = document.getElementById('district_field');
                            
                            if (stateInput) stateInput.value = state;
                            if (cityInput) cityInput.value = city;
                            if (districtInput) districtInput.value = city;
                            
                            pincodeField.classList.add('is-valid');
                        } else {
                            pincodeField.classList.add('is-invalid');
                            alert("Invalid pincode. Please try again.");
                        }
                    })
                    .catch(err => {
                        spinner.style.display = 'none';
                        console.error('Error auto-populating pincode info:', err);
                    });
            } else {
                pincodeField.classList.add('is-invalid');
                alert("Please enter a valid 6-digit pincode.");
            }
        }

        lookupBtn.addEventListener('click', lookupPincode);
        pincodeField.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                lookupPincode();
            }
        });
    });
</script>
@endsection
