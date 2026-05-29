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
            <ul class="nav nav-tabs nav-fill mb-4 border-0 bg-light p-1 rounded-3" id="setupFormTabs" role="tablist" style="flex-wrap: nowrap !important; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none;">
                <li class="nav-item" role="presentation" style="flex-shrink: 0;">
                    <button class="nav-link active fw-bold small py-2.5 rounded-3" id="step1-tab" data-bs-toggle="tab" data-bs-target="#step1" type="button" role="tab" style="white-space: nowrap;"><span class="badge bg-secondary me-1">1</span> Basic Profile</button>
                </li>
                <li class="nav-item" role="presentation" style="flex-shrink: 0;">
                    <button class="nav-link fw-bold small py-2.5 rounded-3" id="step2-tab" data-bs-toggle="tab" data-bs-target="#step2" type="button" role="tab" style="white-space: nowrap;"><span class="badge bg-secondary me-1">2</span> Contact Info</button>
                </li>
                <li class="nav-item" role="presentation" style="flex-shrink: 0;">
                    <button class="nav-link fw-bold small py-2.5 rounded-3" id="step3-tab" data-bs-toggle="tab" data-bs-target="#step3" type="button" role="tab" style="white-space: nowrap;"><span class="badge bg-secondary me-1">3</span> Address & Location</button>
                </li>
                <li class="nav-item" role="presentation" style="flex-shrink: 0;">
                    <button class="nav-link fw-bold small py-2.5 rounded-3" id="step4-tab" data-bs-toggle="tab" data-bs-target="#step4" type="button" role="tab" style="white-space: nowrap;"><span class="badge bg-secondary me-1">4</span> Photos & Finish</button>
                </li>
            </ul>

            <form id="businessCreateForm" action="{{ route('dashboard.business.register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="tab-content" id="setupFormContent">
                    
                    <!-- Step 1: Basic Profile -->
                    <div class="tab-pane fade show active" id="step1" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Business Name <span class="text-danger">*</span></label>
                                <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" placeholder="E.g. Mali Agri Services" required value="{{ old('business_name') }}">
                                @error('business_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Business Type <span class="text-danger">*</span></label>
                                <select id="business_type_select" name="business_type" class="form-select @error('business_type') is-invalid @enderror" required>
                                    <option value="Proprietary /Partnership - LLP" {{ old('business_type') === 'Proprietary /Partnership - LLP' ? 'selected' : '' }}>Proprietary /Partnership - LLP</option>
                                    <option value="Private Ltd" {{ old('business_type') === 'Private Ltd' ? 'selected' : '' }}>Private Ltd</option>
                                    <option value="Public Ltd" {{ old('business_type') === 'Public Ltd' ? 'selected' : '' }}>Public Ltd</option>
                                </select>
                                @error('business_type')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Business Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Select business category...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary small">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" placeholder="Describe your business operations, products, and specialties..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary rounded-3 px-4 py-2.5" onclick="guardedNext('step2-tab')">Next Step <i class="fa-solid fa-arrow-right ms-1"></i></button>
                        </div>
                    </div>

                    <!-- Step 2: Contact Info -->
                    <div class="tab-pane fade" id="step2" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Contact Email</label>
                                <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror" placeholder="business@example.com" value="{{ old('contact_email', Auth::user()->email) }}">
                                @error('contact_email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Contact Phone</label>
                                <input type="text" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" placeholder="10-digit number" value="{{ old('contact_phone', Auth::user()->phone) }}">
                                @error('contact_phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Website URL</label>
                            <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" placeholder="https://example.com" value="{{ old('website') }}">
                            @error('website')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Opening Time</label>
                                <input type="time" name="opening_time" class="form-control @error('opening_time') is-invalid @enderror" value="{{ old('opening_time', '09:00') }}">
                                @error('opening_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Closing Time</label>
                                <input type="time" name="closing_time" class="form-control @error('closing_time') is-invalid @enderror" value="{{ old('closing_time', '21:00') }}">
                                @error('closing_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-light rounded-3 px-4 py-2.5" onclick="prevTab('step1-tab')"><i class="fa-solid fa-arrow-left me-1"></i> Back</button>
                            <button type="button" class="btn btn-primary rounded-3 px-4 py-2.5" onclick="guardedNext('step3-tab')">Next Step <i class="fa-solid fa-arrow-right ms-1"></i></button>
                        </div>
                    </div>

                    <!-- Step 3: Address & Location -->
                    <div class="tab-pane fade" id="step3" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Pincode <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-secondary"><i class="fa-solid fa-map-pin"></i></span>
                                    <input type="text" name="pincode" id="pincode_field" class="form-control @error('pincode') is-invalid @enderror" placeholder="6-digit pincode" maxlength="6" required value="{{ old('pincode') }}">
                                    <button class="btn btn-outline-secondary" type="button" id="lookup_pincode_btn">Verify</button>
                                    <button class="btn btn-outline-secondary" type="button" id="get_business_location_btn" title="Fetch My Current Coordinates"><i class="fa-solid fa-location-dot text-primary"></i></button>
                                </div>
                                <input type="hidden" name="latitude" id="businessLatitudeInput" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="businessLongitudeInput" value="{{ old('longitude') }}">
                                <div id="pincode_spinner" class="spinner-border spinner-border-sm text-primary mt-2" role="status" style="display: none;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                @error('pincode')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">Country <span class="text-danger">*</span></label>
                                <input type="text" name="country" value="India" class="form-control @error('country') is-invalid @enderror" required readonly>
                                @error('country')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Address Line <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Building, Street, Landmark" required value="{{ old('address') }}">
                            @error('address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">State <span class="text-danger">*</span></label>
                                <input type="text" name="state" id="state_field" class="form-control @error('state') is-invalid @enderror" placeholder="State" required value="{{ old('state') }}">
                                @error('state')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" id="city_field" class="form-control @error('city') is-invalid @enderror" placeholder="City" required value="{{ old('city') }}">
                                @error('city')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary small">District <span class="text-danger">*</span></label>
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
                            <button type="button" class="btn btn-primary rounded-3 px-4 py-2.5" onclick="guardedNext('step4-tab')">Next Step <i class="fa-solid fa-arrow-right ms-1"></i></button>
                        </div>
                    </div>

                    <!-- Step 4: Photos & Finish -->
                    <div class="tab-pane fade" id="step4" role="tabpanel">
                        <div class="p-4 rounded-4 bg-light text-center mb-4 border">
                            <div class="fs-1 text-primary mb-3"><i class="fa-solid fa-images"></i></div>
                            <h6 class="fw-bold mb-2">Upload Enterprise Gallery Photos <span class="text-danger">*</span></h6>
                            <p class="text-secondary small mb-4">Add visual catalog storefront photos. High quality JPG/PNG images work best.</p>
                            <input type="file" name="photos[]" class="form-control @error('photos') is-invalid @enderror" multiple accept="image/*" required>
                            @error('photos')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @if($errors->first('photos.*'))
                                <div class="text-danger small mt-1">{{ $errors->first('photos.*') }}</div>
                            @endif
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

    // Validate required fields in the current tab before moving
    function guardedNext(targetTabId) {
        const current = document.querySelector('.tab-pane.show.active') || document.querySelector('.tab-pane.active');
        if (!current) { nextTab(targetTabId); return; }

        // remove existing client-error elements in current tab
        current.querySelectorAll('.client-error').forEach(el => el.remove());

        const elements = Array.from(current.querySelectorAll('input, select, textarea'));
        const invalids = [];

        elements.forEach(el => {
            if (el.disabled) return;
            if (!el.hasAttribute('required')) return;

            let valid = true;
            if (el.type === 'file') {
                valid = el.files && el.files.length > 0;
            } else if (el.tagName.toLowerCase() === 'select') {
                valid = el.value !== '' && !el.querySelector('option:checked[disabled]');
            } else {
                valid = el.checkValidity ? el.checkValidity() : !!el.value;
            }

            if (!valid) {
                invalids.push(el);
                el.classList.add('is-invalid');
                // insert error message after element (or outside input-group if it exists)
                const err = document.createElement('div');
                err.className = 'text-danger small mt-1 client-error';
                err.innerText = el.validationMessage || 'Please complete this field';
                
                const inputGroup = el.closest('.input-group');
                if (inputGroup) {
                    inputGroup.insertAdjacentElement('afterend', err);
                } else {
                    el.insertAdjacentElement('afterend', err);
                }
                // add listener to clear error when user types
                const clear = () => {
                    el.classList.remove('is-invalid');
                    if (err) err.remove();
                    el.removeEventListener('input', clear);
                    el.removeEventListener('change', clear);
                };
                el.addEventListener('input', clear);
                el.addEventListener('change', clear);
            } else {
                el.classList.remove('is-invalid');
            }
        });

        if (invalids.length > 0) {
            invalids[0].focus();
            return; // don't move tabs
        }

        nextTab(targetTabId);
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

        const getBizLocBtn = document.getElementById('get_business_location_btn');
        if (getBizLocBtn) {
            getBizLocBtn.addEventListener('click', function() {
                const icon = getBizLocBtn.querySelector('i');
                const oldClass = icon.className;
                icon.className = 'fa-solid fa-spinner fa-spin text-primary';
                
                navigator.geolocation.getCurrentPosition(function(position) {
                    icon.className = 'fa-solid fa-circle-check text-success';
                    document.getElementById('businessLatitudeInput').value = position.coords.latitude;
                    document.getElementById('businessLongitudeInput').value = position.coords.longitude;
                    alert('GPS Coordinates fetched successfully: ' + position.coords.latitude.toFixed(4) + ', ' + position.coords.longitude.toFixed(4));
                }, function(error) {
                    icon.className = oldClass;
                    alert('Geolocation Error: ' + error.message);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            });
        }
    });
</script>
@endsection
