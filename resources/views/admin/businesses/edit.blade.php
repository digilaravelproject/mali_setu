@extends('admin.layouts.app')

@section('title', 'Edit Business')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Business</h1>
        <div class="d-sm-flex">
            <a href="{{ route('admin.businesses.show', $business->id) }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Business
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

                    <form action="{{ route('admin.businesses.update', $business->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Basic details -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="business_name" class="form-label font-weight-bold small">Business Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="business_name" name="business_name" value="{{ old('business_name', $business->business_name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="business_type" class="form-label font-weight-bold small">Business Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="business_type" name="business_type" required>
                                    <option value="" disabled>Select business type</option>
                                    @foreach($businessTypes as $type)
                                        <option value="{{ $type }}" {{ old('business_type', $business->business_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label font-weight-bold small">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="" disabled>Select business category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $business->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact_phone" class="form-label font-weight-bold small">Contact Phone</label>
                                <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $business->contact_phone) }}">
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_email" class="form-label font-weight-bold small">Contact Email</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ old('contact_email', $business->contact_email) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label font-weight-bold small">Website</label>
                                <input type="text" class="form-control" id="website" name="website" value="{{ old('website', $business->website) }}">
                            </div>
                        </div>

                        <!-- Address & Location info -->
                        <h6 class="font-weight-bold text-primary mt-4 mb-3"><i class="fas fa-location-dot me-2"></i>Location Details</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="country" class="form-label font-weight-bold small">Country <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $business->country ?? 'India') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label font-weight-bold small">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $business->state) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="district" class="form-label font-weight-bold small">District <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="district" name="district" value="{{ old('district', $business->district) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="city" class="form-label font-weight-bold small">City / Town <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $business->city) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="pincode" class="form-label font-weight-bold small">Pincode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pincode" name="pincode" value="{{ old('pincode', $business->pincode) }}" required pattern="\d{6}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="village" class="form-label font-weight-bold small">Village</label>
                                <input type="text" class="form-control" id="village" name="village" value="{{ old('village', $business->village) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="taluka" class="form-label font-weight-bold small">Taluka</label>
                                <input type="text" class="form-control" id="taluka" name="taluka" value="{{ old('taluka', $business->taluka) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label font-weight-bold small">Full Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Full business address details">{{ old('address', $business->address) }}</textarea>
                            </div>
                        </div>

                        <!-- Timing & Coordinates -->
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="opening_time" class="form-label font-weight-bold small">Opening Time</label>
                                <input type="time" class="form-control" id="opening_time" name="opening_time" value="{{ old('opening_time', $business->opening_time ? \Carbon\Carbon::parse($business->opening_time)->format('H:i') : '') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="closing_time" class="form-label font-weight-bold small">Closing Time</label>
                                <input type="time" class="form-control" id="closing_time" name="closing_time" value="{{ old('closing_time', $business->closing_time ? \Carbon\Carbon::parse($business->closing_time)->format('H:i') : '') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="latitude" class="form-label font-weight-bold small">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', $business->latitude) }}" placeholder="e.g. 18.52">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="longitude" class="form-label font-weight-bold small">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', $business->longitude) }}" placeholder="e.g. 73.85">
                            </div>
                        </div>

                        <!-- Description & Image upload -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label font-weight-bold small">Business Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $business->description) }}</textarea>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-3 mb-3">
                                <label for="photo" class="form-label font-weight-bold small">Business Image / Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <small class="text-muted small">Upload a new photo to replace the current one (max 2MB)</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="verification_status" class="form-label font-weight-bold small">Verification Status</label>
                                <select class="form-select" id="verification_status" name="verification_status">
                                    <option value="pending" {{ old('verification_status', $business->verification_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('verification_status', $business->verification_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('verification_status', $business->verification_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label font-weight-bold small">Business Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" {{ old('status', $business->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="suspended" {{ old('status', $business->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="banned" {{ old('status', $business->status) == 'banned' ? 'selected' : '' }}>Banned</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="subscription_expires_at" class="form-label font-weight-bold small">Subscription Expired At</label>
                                <input type="date" class="form-control" id="subscription_expires_at" name="subscription_expires_at" value="{{ old('subscription_expires_at', $business->subscription_expires_at ? $business->subscription_expires_at->format('Y-m-d') : '') }}">
                            </div>
                        </div>

                        @if($business->photo)
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold small d-block">Current Photo</label>
                                    <img src="{{ asset('storage/' . $business->photo) }}" alt="Business Photo" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.businesses.show', $business->id) }}" class="btn btn-secondary rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
