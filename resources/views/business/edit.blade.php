@extends('layouts.app')

@section('content')
<div class="row g-4 justify-content-center text-start">
    <div class="col-lg-10 col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dashboard.business.index') }}" class="btn btn-light btn-sm rounded-3">
                    <i class="fa-solid fa-arrow-left"></i> Back to Hub
                </a>
                <h4 class="fw-bold mb-0 text-dark">Edit Business Profile Details</h4>
            </div>
        </div>

        <div class="glass-card p-4 border shadow-sm">
            <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-pen-to-square me-2"></i> Update Enterprise Registry Info</h5>

            @if($errors->any())
                <div class="alert alert-danger rounded-3">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dashboard.business.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary small">Business Name *</label>
                        <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $business->business_name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary small">Business Type *</label>
                        <select name="business_type" class="form-select" required>
                            <option value="Retailer" {{ old('business_type', $business->business_type) === 'Retailer' ? 'selected' : '' }}>Retailer</option>
                            <option value="Wholesaler" {{ old('business_type', $business->business_type) === 'Wholesaler' ? 'selected' : '' }}>Wholesaler</option>
                            <option value="Manufacturer" {{ old('business_type', $business->business_type) === 'Manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                            <option value="Service Provider" {{ old('business_type', $business->business_type) === 'Service Provider' ? 'selected' : '' }}>Service Provider</option>
                            <option value="Distributor" {{ old('business_type', $business->business_type) === 'Distributor' ? 'selected' : '' }}>Distributor</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary small">Business Category *</label>
                        <select name="category_id" class="form-select" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $business->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary small">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $business->contact_email) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary small">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $business->contact_phone) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary small">Website URL</label>
                        <input type="url" name="website" class="form-control" value="{{ old('website', $business->website) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary small">Opening Time</label>
                        <input type="time" name="opening_time" class="form-control" value="{{ old('opening_time', $business->opening_time) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary small">Closing Time</label>
                        <input type="time" name="closing_time" class="form-control" value="{{ old('closing_time', $business->closing_time) }}">
                    </div>
                </div>

                <h6 class="fw-bold text-primary mt-4 mb-3 border-bottom pb-2">Business Address Details</h6>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small">Address Line *</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $business->address) }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary small">Pincode *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary"><i class="fa-solid fa-map-pin"></i></span>
                            <input type="text" name="pincode" id="pincode_field" class="form-control" value="{{ old('pincode', $business->pincode) }}" required maxlength="6">
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

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary small">State *</label>
                        <input type="text" name="state" id="state_field" class="form-control" value="{{ old('state', $business->state) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary small">City *</label>
                        <input type="text" name="city" id="city_field" class="form-control" value="{{ old('city', $business->city) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary small">District *</label>
                        <input type="text" name="district" id="district_field" class="form-control" value="{{ old('district', $business->district) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold text-secondary small">Taluka</label>
                        <input type="text" name="taluka" id="taluka_field" class="form-control" value="{{ old('taluka', $business->taluka) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold text-secondary small">Village</label>
                        <input type="text" name="village" id="village_field" class="form-control" value="{{ old('village', $business->village) }}">
                    </div>
                </div>

                <div class="mb-4 mt-3">
                    <label class="form-label fw-semibold text-secondary small">Description *</label>
                    <textarea name="description" class="form-control" rows="4" required>{{ old('description', $business->description) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small">Upload New Business Photos</label>
                    <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">Note: Uploading new photos will overwrite existing photos.</small>
                </div>

                @if($business->photo)
                    <div class="d-flex gap-2 mb-4 flex-wrap">
                        @foreach(explode(',', $business->photo) as $img)
                            @if(trim($img))
                                <img src="{{ asset('storage/' . trim($img)) }}" class="rounded shadow-sm border" style="width: 80px; height: 80px; object-fit: cover;">
                            @endif
                        @endforeach
                    </div>
                @endif

                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-3 shadow-sm fw-bold">
                    Update Business Profile Info <i class="fa-solid fa-circle-check ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
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
