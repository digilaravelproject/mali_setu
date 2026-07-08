@extends('admin.layouts.app')

@section('title', 'Add Business')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Add Business</h1>
        <div class="d-sm-flex">
            <a href="{{ route('admin.businesses.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to list
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Business Profile Information</h6>
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

                    <form action="{{ route('admin.businesses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- User Assignment & Basic details -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label font-weight-bold small">Owner User <span class="text-danger">*</span></label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="" disabled selected>Select user account</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }} - ID: {{ $user->id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="business_name" class="form-label font-weight-bold small">Business Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="business_name" name="business_name" value="{{ old('business_name') }}" required placeholder="Business Name">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="business_type" class="form-label font-weight-bold small">Business Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="business_type" name="business_type" required>
                                    <option value="" disabled selected>Select business type</option>
                                    @foreach($businessTypes as $type)
                                        <option value="{{ $type }}" {{ old('business_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label font-weight-bold small">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="" disabled selected>Select business category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="contact_phone" class="form-label font-weight-bold small">Contact Phone</label>
                                <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" placeholder="9876543210">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contact_email" class="form-label font-weight-bold small">Contact Email</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" placeholder="contact@business.com">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="website" class="form-label font-weight-bold small">Website</label>
                                <input type="text" class="form-control" id="website" name="website" value="{{ old('website') }}" placeholder="https://example.com">
                            </div>
                        </div>

                        <!-- Address & Location info -->
                        <h6 class="font-weight-bold text-primary mt-4 mb-3"><i class="fas fa-location-dot me-2"></i>Location Details</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label font-weight-bold small">Country <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="country" name="country" value="{{ old('country', 'India') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="state" class="form-label font-weight-bold small">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="state" name="state" value="{{ old('state') }}" required placeholder="e.g. Maharashtra">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="city" class="form-label font-weight-bold small">City / Town <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" required placeholder="e.g. Pune">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="pincode" class="form-label font-weight-bold small">Pincode <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="pincode" name="pincode" value="{{ old('pincode') }}" required placeholder="6 digits" pattern="\d{6}">
                                    <button class="btn btn-outline-secondary" type="button" id="get_gps_location_btn" title="Get GPS Location">
                                        <i class="fa-solid fa-location-crosshairs text-primary"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="village" class="form-label font-weight-bold small">Village</label>
                                <input type="text" class="form-control" id="village" name="village" value="{{ old('village') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="taluka" class="form-label font-weight-bold small">Taluka</label>
                                <input type="text" class="form-control" id="taluka" name="taluka" value="{{ old('taluka') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label font-weight-bold small">Full Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Full business address details">{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <!-- Timing & Coordinates -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="opening_time" class="form-label font-weight-bold small">Opening Time</label>
                                <input type="time" class="form-control" id="opening_time" name="opening_time" value="{{ old('opening_time') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="closing_time" class="form-label font-weight-bold small">Closing Time</label>
                                <input type="time" class="form-control" id="closing_time" name="closing_time" value="{{ old('closing_time') }}">
                            </div>
                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                        </div>

                        <!-- Description & Image upload -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label font-weight-bold small">Business Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Describe business offerings, products, or services...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="photo" class="form-label font-weight-bold small">Business Image / Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <small class="text-muted small">Upload business logo or primary image (max 2MB)</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="verification_status" class="form-label font-weight-bold small">Verification Status</label>
                                <select class="form-select" id="verification_status" name="verification_status">
                                    <option value="pending" {{ old('verification_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('verification_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('verification_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label font-weight-bold small">Business Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="banned" {{ old('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="subscription_expires_at" class="form-label font-weight-bold small">Subscription Expired At</label>
                                <input type="date" class="form-control" id="subscription_expires_at" name="subscription_expires_at" value="{{ old('subscription_expires_at') }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.businesses.index') }}" class="btn btn-secondary rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Save Business</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const getGpsBtn = document.getElementById('get_gps_location_btn');
        if (getGpsBtn) {
            getGpsBtn.addEventListener('click', function() {
                const icon = getGpsBtn.querySelector('i');
                const oldClass = icon.className;
                icon.className = 'fa-solid fa-spinner fa-spin text-primary';
                
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        icon.className = 'fa-solid fa-circle-check text-success';
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lon;
                        
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
                                const city = addr.city || addr.town || addr.municipality || addr.city_district || addr.state_district || addr.district || '';
                                const taluka = addr.subdistrict || addr.county || addr.taluk || addr.tehsil || addr.suburb || addr.neighbourhood || '';
                                const village = addr.village || addr.hamlet || '';
                                
                                if (pincode) {
                                    document.getElementById('pincode').value = pincode.replace(/\s+/g, '');
                                }
                                if (country) {
                                    document.getElementById('country').value = country;
                                }
                                if (state) {
                                    document.getElementById('state').value = state;
                                }
                                if (city) {
                                    document.getElementById('city').value = city;
                                }
                                if (taluka) {
                                    document.getElementById('taluka').value = taluka;
                                }
                                if (village) {
                                    document.getElementById('village').value = village;
                                }
                                const addressField = document.getElementById('address');
                                if (addressField && geoData.display_name) {
                                    addressField.value = geoData.display_name;
                                }
                            }
                        })
                        .catch(err => {
                            console.error('Reverse geocoding error:', err);
                        });
                        
                        alert('Location coordinates fetched successfully!');
                    }, function(error) {
                        icon.className = oldClass;
                        alert('Error fetching location: ' + error.message);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    });
                } else {
                    icon.className = oldClass;
                    alert('Geolocation is not supported by this browser.');
                }
            });
        }
    });
</script>
@endsection
