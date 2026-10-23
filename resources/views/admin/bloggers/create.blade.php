@extends('admin.layouts.app')

@section('title', 'Add Blogger')

@section('content')
<div class="content-area">
    <!-- Back link -->
    <div class="mb-4">
        <a href="{{ route('admin.bloggers.index') }}" class="text-primary text-decoration-none fw-bold small">
            <i class="fas fa-arrow-left me-1"></i> Back to Listing
        </a>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm col-lg-12 mx-auto" style="border-radius:16px;">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-user-plus me-2 text-primary"></i>Register New Blogger</h5>
        </div>
        <div class="card-body p-4">
            
            @if($errors->any())
                <div class="alert alert-danger border-0 rounded-3">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.bloggers.store') }}" method="POST" id="create-blogger-form">
                @csrf

                <!-- Name Field -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold text-secondary">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter blogger's full name" value="{{ old('name') }}" required>
                </div>

                <!-- Email Field with AJAX Uniqueness Checking -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold text-secondary">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter unique email address" value="{{ old('email') }}" required autocomplete="off">
                    <!-- Error Container for AJAX Uniqueness Feedback -->
                    <div id="email-ajax-error" class="text-danger small mt-1 fw-semibold" style="display: none;"></div>
                    <div class="form-text small text-muted">Blogger accounts must have a unique email address. We will verify this instantly.</div>
                </div>

                <!-- Phone Field -->
                <div class="mb-3">
                    <label for="phone" class="form-label fw-bold text-secondary">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone number (e.g. +91 9876543210)" value="{{ old('phone') }}" required>
                </div>

                <!-- Category Select Field -->
                <div class="mb-3">
                    <label for="blog_category_id" class="form-label fw-bold text-secondary">Blog Category</label>
                    <select name="blog_category_id" id="blog_category_id" class="form-select @error('blog_category_id') is-invalid @enderror" required>
                        <option value="" disabled {{ old('blog_category_id') === null ? 'selected' : '' }}>Select a blog category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('blog_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('blog_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Info Callout -->
                <div class="alert alert-light border rounded-3 p-3 mb-4">
                    <div class="d-flex">
                        <div class="text-primary me-2"><i class="fas fa-info-circle fa-lg"></i></div>
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">Password Autogeneration</h6>
                            <p class="text-secondary small mb-0">A random password will be automatically generated, hashed, and stored in the database. The system will dispatch an onboarding email containing their credentials directly to the registered address.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row g-2">
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-bold w-100" id="submit-btn">
                            <i class="fas fa-paper-plane me-1"></i> Create Blogger
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.bloggers.index') }}" class="btn btn-outline-secondary rounded-pill py-2.5 w-100">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const errorDiv = document.getElementById('email-ajax-error');
    const submitBtn = document.getElementById('submit-btn');
    const form = document.getElementById('create-blogger-form');
    
    let debounceTimer;

    emailInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        
        const email = emailInput.value.trim();
        if (email === '') {
            resetValidation();
            return;
        }

        // Validate basic email format before calling AJAX
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showInvalidState("Please enter a valid email address.");
            return;
        }

        // Debounce to prevent rapid database queries
        debounceTimer = setTimeout(() => {
            checkEmailUniqueness(email);
        }, 300);
    });

    function checkEmailUniqueness(email) {
        fetch("{{ route('admin.bloggers.check-email') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => {
            if (!response.ok) throw new Error("HTTP error " + response.status);
            return response.json();
        })
        .then(data => {
            if (data.exists) {
                showInvalidState("This email is already in use. Please enter a different email address.");
            } else {
                showValidState();
            }
        })
        .catch(error => {
            console.error('Email uniqueness check failed:', error);
        });
    }

    function showInvalidState(message) {
        emailInput.classList.remove('is-valid');
        emailInput.classList.add('is-invalid');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        submitBtn.disabled = true;
    }

    function showValidState() {
        emailInput.classList.remove('is-invalid');
        emailInput.classList.add('is-valid');
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
        submitBtn.disabled = false;
    }

    function resetValidation() {
        emailInput.classList.remove('is-invalid', 'is-valid');
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
        submitBtn.disabled = false;
    }

    // Double check on form submission
    form.addEventListener('submit', function(e) {
        if (emailInput.classList.contains('is-invalid')) {
            e.preventDefault();
            alert("Please fix validation errors before submitting.");
        }
    });
});
</script>
@endpush
