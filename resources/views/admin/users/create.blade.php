@extends('admin.layouts.app')

@section('title', 'Add User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h5 class="m-0 font-weight-bold text-primary">Add New User</h5>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="row g-4">
                            <!-- Basic Credentials Section -->
                            <div class="col-md-6">
                                <div class="card h-100 border-light shadow-sm">
                                    <div class="card-header bg-light py-3">
                                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-key me-2 text-primary"></i>Credentials & Role</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label font-weight-bold small">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required placeholder="John Doe">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label font-weight-bold small">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="phone" class="form-label font-weight-bold small">Phone Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone') }}" required placeholder="9876543210">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="user_type" class="form-label font-weight-bold small">User Type <span class="text-danger">*</span></label>
                                            <select class="form-select @error('user_type') is-invalid @enderror" 
                                                    id="user_type" name="user_type" required>
                                                <option value="" disabled selected>Select User Type</option>
                                                <option value="general" {{ old('user_type') == 'general' ? 'selected' : '' }}>General</option>
                                                <option value="business" {{ old('user_type') == 'business' ? 'selected' : '' }}>Business</option>
                                                <option value="matrimony" {{ old('user_type') == 'matrimony' ? 'selected' : '' }}>Matrimony</option>
                                                <option value="volunteer" {{ old('user_type') == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                                                <option value="bloger" {{ old('user_type') == 'bloger' ? 'selected' : '' }}>Blogger</option>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="password" class="form-label font-weight-bold small">Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" name="password" required autocomplete="new-password">
                                                <small class="text-muted small">Minimum 8 characters</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="password_confirmation" class="form-label font-weight-bold small">Confirm Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" 
                                                       id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Administrative settings Section -->
                            <div class="col-md-6">
                                <div class="card h-100 border-light shadow-sm">
                                    <div class="card-header bg-light py-3">
                                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-shield-halved me-2 text-primary"></i>Administration</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="caste_verification_status" class="form-label font-weight-bold small">Caste Verification Status</label>
                                            <select class="form-select @error('caste_verification_status') is-invalid @enderror" 
                                                    id="caste_verification_status" name="caste_verification_status">
                                                <option value="pending" {{ old('caste_verification_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ old('caste_verification_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ old('caste_verification_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check mt-4 pt-2">
                                                <input class="form-check-input" type="checkbox" id="email_verified" 
                                                       name="email_verified" value="1" {{ old('email_verified') ? 'checked' : '' }}>
                                                <label class="form-check-label font-weight-bold small" for="email_verified">
                                                    Email Verified Automatically
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="status" class="form-label font-weight-bold small">Account Status</label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status">
                                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                                <option value="banned" {{ old('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="admin_notes" class="form-label font-weight-bold small">Admin Notes</label>
                                            <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                                      id="admin_notes" name="admin_notes" rows="3" 
                                                      placeholder="Internal admin notes...">{{ old('admin_notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personal & Professional details Section -->
                        <div class="row g-4 mt-2">
                            <div class="col-12">
                                <div class="card border-light shadow-sm">
                                    <div class="card-header bg-light py-3">
                                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-briefcase me-2 text-primary"></i>Profile Details (Personal & Professional)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label for="age" class="form-label font-weight-bold small">Age</label>
                                                <input type="number" class="form-control" id="age" name="age" value="{{ old('age') }}" min="18" max="100">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="dob" class="form-label font-weight-bold small">Date of Birth</label>
                                                <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob') }}">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="occupation" class="form-label font-weight-bold small">Occupation</label>
                                                <select class="form-select @error('occupation') is-invalid @enderror" id="occupation" name="occupation">
                                                    <option value="" disabled {{ old('occupation') ? '' : 'selected' }}>Select Occupation</option>
                                                    <option value="Service" {{ old('occupation') == 'Service' ? 'selected' : '' }}>Service</option>
                                                    <option value="Business" {{ old('occupation') == 'Business' ? 'selected' : '' }}>Business</option>
                                                    <option value="Student" {{ old('occupation') == 'Student' ? 'selected' : '' }}>Student</option>
                                                    <option value="Not Working" {{ old('occupation') == 'Not Working' ? 'selected' : '' }}>Not Working</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="designation" class="form-label font-weight-bold small">Designation</label>
                                                <input type="text" class="form-control" id="designation" name="designation" value="{{ old('designation') }}" placeholder="Senior Analyst">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="company_name" class="form-label font-weight-bold small">Company Name</label>
                                                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Google Ltd">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="dept_name" class="form-label font-weight-bold small">Department Name</label>
                                                <input type="text" class="form-control" id="dept_name" name="dept_name" value="{{ old('dept_name') }}" placeholder="IT / Engineering">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="reffral_code" class="form-label font-weight-bold small">Referral Code</label>
                                                <input type="text" class="form-control" id="reffral_code" name="reffral_code" value="{{ old('reffral_code') }}" placeholder="REF123">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address & Location Section -->
                        <div class="row g-4 mt-2">
                            <div class="col-12">
                                <div class="card border-light shadow-sm">
                                    <div class="card-header bg-light py-3">
                                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-location-dot me-2 text-primary"></i>Location & Address Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label for="road_number" class="form-label font-weight-bold small">Road / Street Number</label>
                                                <input type="text" class="form-control" id="road_number" name="road_number" value="{{ old('road_number') }}" placeholder="12B, Park Ave">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="nearby_location" class="form-label font-weight-bold small">Nearby Landmark</label>
                                                <input type="text" class="form-control" id="nearby_location" name="nearby_location" value="{{ old('nearby_location') }}" placeholder="Opp. Central Library">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="sector" class="form-label font-weight-bold small">Sector / Area</label>
                                                <input type="text" class="form-control" id="sector" name="sector" value="{{ old('sector') }}" placeholder="Sector 4">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="pincode" class="form-label font-weight-bold small">Pincode (6 digits)</label>
                                                <input type="text" class="form-control" id="pincode" name="pincode" value="{{ old('pincode') }}" placeholder="411001">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label for="village" class="form-label font-weight-bold small">Village</label>
                                                <input type="text" class="form-control" id="village" name="village" value="{{ old('village') }}" placeholder="Village Name">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="city" class="form-label font-weight-bold small">City</label>
                                                <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" placeholder="Pune">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="district" class="form-label font-weight-bold small">District</label>
                                                <input type="text" class="form-control" id="district" name="district" value="{{ old('district') }}" placeholder="Pune District">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="state" class="form-label font-weight-bold small">State</label>
                                                <input type="text" class="form-control" id="state" name="state" value="{{ old('state') }}" placeholder="Maharashtra">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="address" class="form-label font-weight-bold small">Full Address</label>
                                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Complete address detail...">{{ old('address') }}</textarea>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="destination" class="form-label font-weight-bold small">Destination Landmark</label>
                                                <input type="text" class="form-control" id="destination" name="destination" value="{{ old('destination') }}" placeholder="Destination point description">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="latitude" class="form-label font-weight-bold small">Latitude</label>
                                                <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="18.5204">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="longitude" class="form-label font-weight-bold small">Longitude</label>
                                                <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="73.8567">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light py-3">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary rounded-pill px-4">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-save"></i> Save User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('password_confirmation');
    
    function validatePasswords() {
        if (passwordField.value && confirmPasswordField.value) {
            if (passwordField.value !== confirmPasswordField.value) {
                confirmPasswordField.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordField.setCustomValidity('');
            }
        }
    }
    
    passwordField.addEventListener('input', validatePasswords);
    confirmPasswordField.addEventListener('input', validatePasswords);
});
</script>
@endsection
