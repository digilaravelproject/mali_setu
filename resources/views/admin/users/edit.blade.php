@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Edit User - {{ $user->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> View User
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
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
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="user_type" class="form-label">User Type <span class="text-danger">*</span></label>
                                            <select class="form-select @error('user_type') is-invalid @enderror" 
                                                    id="user_type" name="user_type" required>
                                                <option value="">Select User Type</option>
                                                <option value="individual" {{ old('user_type', $user->user_type) == 'individual' ? 'selected' : '' }}>Individual</option>
                                                <option value="business" {{ old('user_type', $user->user_type) == 'business' ? 'selected' : '' }}>Business</option>
                                                <option value="matrimony" {{ old('user_type', $user->user_type) == 'matrimony' ? 'selected' : '' }}>Matrimony</option>
                                                <option value="volunteer" {{ old('user_type', $user->user_type) == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                                            </select>
                                            @error('user_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Verification & Status -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Verification & Status</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="caste_verification_status" class="form-label">Caste Verification Status</label>
                                            <select class="form-select @error('caste_verification_status') is-invalid @enderror" 
                                                    id="caste_verification_status" name="caste_verification_status">
                                                <option value="pending" {{ old('caste_verification_status', $user->caste_verification_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ old('caste_verification_status', $user->caste_verification_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ old('caste_verification_status', $user->caste_verification_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                            @error('caste_verification_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="email_verified" 
                                                       name="email_verified" value="1" 
                                                       {{ $user->email_verified_at ? 'checked' : '' }}>
                                                <label class="form-check-label" for="email_verified">
                                                    Email Verified
                                                </label>
                                            </div>
                                            <small class="text-muted">
                                                @if($user->email_verified_at)
                                                    Verified on {{ $user->email_verified_at->format('M d, Y H:i') }}
                                                @else
                                                    Email not verified
                                                @endif
                                            </small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Account Status</label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status">
                                                <option value="active" {{ old('status', $user->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="suspended" {{ old('status', $user->status ?? 'active') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                                <option value="banned" {{ old('status', $user->status ?? 'active') == 'banned' ? 'selected' : '' }}>Banned</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="admin_notes" class="form-label">Admin Notes</label>
                                            <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                                      id="admin_notes" name="admin_notes" rows="4" 
                                                      placeholder="Add any admin notes about this user...">{{ old('admin_notes', $user->admin_notes ?? '') }}</textarea>
                                            @error('admin_notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Password Reset Section -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Password Management</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Leave password fields empty to keep the current password unchanged.
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">New Password</label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                           id="password" name="password" autocomplete="new-password">
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Minimum 8 characters</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                                    <input type="password" class="form-control" 
                                                           id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="force_password_reset" 
                                                   name="force_password_reset" value="1">
                                            <label class="form-check-label" for="force_password_reset">
                                                Force user to change password on next login
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Information -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Account Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Account Created:</strong><br>
                                                <span class="text-muted">{{ $user->created_at->format('M d, Y H:i') }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Last Updated:</strong><br>
                                                <span class="text-muted">{{ $user->updated_at->format('M d, Y H:i') }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>User ID:</strong><br>
                                                <span class="text-muted">#{{ $user->id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update User
                                </button>
                            </div>
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
// Form validation
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