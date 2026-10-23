@extends('admin.layouts.app')

@section('title', 'Admin Profile Settings')
@section('page-title', 'Admin Profile Settings')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Admin Profile Settings</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm shadow-sm rounded-pill px-3">
            <i class="fas fa-arrow-left fa-sm me-1 text-white-50"></i> Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Profile Card -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow border-0 h-100" style="border-radius: 16px;">
                <div class="card-body text-center p-4">
                    <div class="mb-4 d-inline-block position-relative">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Photo" id="sidebarAvatarPreview" class="rounded-circle shadow-sm border border-4 border-white" style="width: 140px; height: 140px; object-fit: cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-danger text-white rounded-circle shadow-sm border border-4 border-white mx-auto fw-bold" id="sidebarLetterPreview" style="width: 140px; height: 140px; font-size: 3rem;">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    
                    <h4 class="font-weight-bold mb-1 text-dark">{{ $user->name }}</h4>
                    <p class="text-muted small mb-3"><span class="badge bg-danger rounded-pill px-3">Administrator</span></p>
                    <hr class="my-4">
                    <div class="text-start">
                        <div class="mb-3 d-flex align-items-center">
                            <div class="bg-light p-2 rounded-3 text-muted me-3">
                                <i class="fas fa-envelope fa-fw"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Email Address</small>
                                <span class="text-dark fw-500" style="word-break: break-all;">{{ $user->email }}</span>
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <div class="bg-light p-2 rounded-3 text-muted me-3">
                                <i class="fas fa-phone fa-fw"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Phone Number</small>
                                <span class="text-dark fw-500">{{ $user->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-light p-2 rounded-3 text-muted me-3">
                                <i class="fas fa-calendar-alt fa-fw"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Member Since</small>
                                <span class="text-dark fw-500">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow border-0 mb-4" style="border-radius: 16px;">
                <div class="card-header py-3 bg-white border-bottom" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <h5 class="m-0 font-weight-bold text-primary">Update Profile Information</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Account Details -->
                        <h6 class="heading-small text-muted mb-4">Account Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="name" class="form-label font-weight-bold small">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-3 @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="email" class="form-label font-weight-bold small">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control rounded-3 @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="phone" class="form-label font-weight-bold small">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-3 @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="photo" class="form-label font-weight-bold small">Profile Photo</label>
                                    <input type="file" class="form-control rounded-3 @error('photo') is-invalid @enderror" 
                                           id="photo" name="photo" accept="image/*">
                                    <small class="text-muted small">Upload a JPEG, PNG, JPG, or WEBP image (max 2MB)</small>
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Password Section -->
                        <h6 class="heading-small text-muted mb-4">Security (Change Password)</h6>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label for="current_password" class="form-label font-weight-bold small">Current Password</label>
                                    <input type="password" class="form-control rounded-3 @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password" placeholder="Enter current password if changing password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="password" class="form-label font-weight-bold small">New Password</label>
                                    <input type="password" class="form-control rounded-3 @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Min 8 characters">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label font-weight-bold small">Confirm New Password</label>
                                    <input type="password" class="form-control rounded-3" 
                                           id="password_confirmation" name="password_confirmation" placeholder="Repeat new password">
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm font-weight-bold">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Live photo preview
    document.getElementById('photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const imgPreview = document.getElementById('sidebarAvatarPreview');
                const letterPreview = document.getElementById('sidebarLetterPreview');
                
                if (imgPreview) {
                    imgPreview.src = event.target.result;
                } else if (letterPreview) {
                    const parent = letterPreview.parentElement;
                    parent.innerHTML = `<img src="${event.target.result}" id="sidebarAvatarPreview" class="rounded-circle shadow-sm border border-4 border-white" style="width: 140px; height: 140px; object-fit: cover;">`;
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
