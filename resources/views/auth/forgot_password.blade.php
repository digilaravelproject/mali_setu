<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password — Mali Setu</title>
    <meta name="description" content="Reset your Mali Setu password securely using OTP email verification.">

    <!-- Bootstrap & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #84144f;
            --primary-dark: #630837;
            --accent: #aa1262;
            --light-bg: #f4f3f0;
            --glass: rgba(255, 255, 255, 0.95);
            --border-glow: rgba(132, 20, 79, 0.15);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f4f3f0 0%, #e9e8e4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2d3436;
            padding: 20px;
        }

        /* Bootstrap Overrides */
        .text-primary { color: var(--primary) !important; }
        .bg-primary { background-color: var(--primary) !important; }

        .auth-container {
            max-width: 550px;
            width: 100%;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(255, 71, 87, 0.1);
            overflow: hidden;
            padding: 40px;
            position: relative;
        }

        .form-control-lg {
            border-radius: 12px;
            font-size: 1rem;
            padding: 14px 20px;
            border: 1.5px solid #e2e8f0;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .form-control-lg:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--border-glow);
            background: #fff;
        }

        .btn-primary {
            background: var(--primary) !important;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.05rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 71, 87, 0.2);
        }

        .back-login {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .back-login:hover {
            color: var(--primary-dark);
            transform: translateX(-3px);
        }

        /* Step Indicators */
        .step-dots {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
        }

        .step-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #cbd5e1;
            transition: all 0.3s ease;
        }

        .step-dot.active {
            background: var(--primary);
            transform: scale(1.3);
            box-shadow: 0 0 0 4px var(--border-glow);
        }

        /* Slide Transition Animations */
        .wizard-step {
            display: none;
        }

        .wizard-step.active {
            display: block;
            animation: fadeInSlide 0.5s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
        }

        @keyframes fadeInSlide {
            0% { opacity: 0; transform: translateY(15px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .otp-input-box {
            letter-spacing: 12px;
            font-size: 1.8rem;
            font-weight: 700;
            text-align: center;
        }

        /* Floating Spinner overlay */
        .spinner-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            backdrop-filter: blur(2px);
            border-radius: 30px;
            visibility: hidden;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .spinner-overlay.show {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body>

<div class="auth-container">
    <!-- Spinner overlay -->
    <div class="spinner-overlay" id="loading-spinner">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Step dots -->
    <div class="step-dots">
        <div class="step-dot active" id="dot-1"></div>
        <div class="step-dot" id="dot-2"></div>
        <div class="step-dot" id="dot-3"></div>
    </div>

    <!-- Alert Panel -->
    <div class="alert alert-danger d-none rounded-4 border-0 shadow-sm" id="error-alert" role="alert"></div>
    <div class="alert alert-success d-none rounded-4 border-0 shadow-sm" id="success-alert" role="alert"></div>

    <!-- Step 1: Request OTP -->
    <div class="wizard-step active" id="step-1">
        <h4 class="fw-bold mb-2">Forgot Password?</h4>
        <p class="text-secondary small mb-4">No worries! Enter your registered email address and we'll send a 6-digit OTP code to verify your identity.</p>

        <form id="form-step-1" onsubmit="handleRequestOtp(event)">
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Email Address</label>
                <input type="email" id="email" class="form-control form-control-lg" placeholder="name@example.com" required>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                Send Verification OTP <i class="fa-solid fa-paper-plane ms-2"></i>
            </button>
        </form>
    </div>

    <!-- Step 2: Verify OTP -->
    <div class="wizard-step" id="step-2">
        <h4 class="fw-bold mb-2">Verify OTP</h4>
        <p class="text-secondary small mb-4">We've sent a 6-digit code to <strong id="sent-email-label">your email</strong>. Enter the OTP code below to verify.</p>

        <form id="form-step-2" onsubmit="handleVerifyOtp(event)">
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Verification Code</label>
                <input type="text" id="otp" maxlength="6" class="form-control form-control-lg otp-input-box" placeholder="******" required>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                Verify OTP <i class="fa-solid fa-shield-check ms-2"></i>
            </button>
            <div class="text-center">
                <button type="button" onclick="goBackToStep1()" class="btn btn-link text-secondary text-decoration-none small">
                    <i class="fa-solid fa-arrow-left me-1"></i> Request a new code
                </button>
            </div>
        </form>
    </div>

    <!-- Step 3: Reset Password -->
    <div class="wizard-step" id="step-3">
        <h4 class="fw-bold mb-2">Set New Password</h4>
        <p class="text-secondary small mb-4">Create a strong, new password to access your Mali Setu account.</p>

        <form id="form-step-3" onsubmit="handleResetPassword(event)">
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">New Password</label>
                <input type="password" id="password" class="form-control form-control-lg" placeholder="Min 8 characters" required>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Confirm New Password</label>
                <input type="password" id="password_confirmation" class="form-control form-control-lg" placeholder="Repeat new password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                Reset Password <i class="fa-solid fa-lock-open ms-2"></i>
            </button>
        </form>
    </div>

    <!-- Footer Links -->
    <div class="border-top border-muted border-opacity-10 pt-4 mt-3 text-center">
        <a href="{{ route('login') }}" class="back-login small">
            <i class="fa-solid fa-arrow-left"></i> Back to Sign In
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let userEmail = '';
    let verifiedOtp = '';

    const sendOtpUrl = "{{ route('password.otp.send') }}";
    const verifyOtpUrl = "{{ route('password.otp.verify') }}";
    const resetPasswordUrl = "{{ route('password.update') }}";

    // Common alerts display
    function showError(msg) {
        const alert = document.getElementById('error-alert');
        alert.innerText = msg;
        alert.classList.remove('d-none');
        document.getElementById('success-alert').classList.add('d-none');
    }

    function showSuccess(msg) {
        const alert = document.getElementById('success-alert');
        alert.innerText = msg;
        alert.classList.remove('d-none');
        document.getElementById('error-alert').classList.add('d-none');
    }

    function clearAlerts() {
        document.getElementById('error-alert').classList.add('d-none');
        document.getElementById('success-alert').classList.add('d-none');
    }

    function showLoading(show) {
        const spinner = document.getElementById('loading-spinner');
        if (show) {
            spinner.classList.add('show');
        } else {
            spinner.classList.remove('show');
        }
    }

    // Step 1: Send OTP Call
    async function handleRequestOtp(e) {
        e.preventDefault();
        clearAlerts();
        showLoading(true);

        userEmail = document.getElementById('email').value.trim();

        try {
            const response = await fetch(sendOtpUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: userEmail })
            });

            const data = await response.json();
            showLoading(false);

            if (response.ok && data.success) {
                document.getElementById('sent-email-label').innerText = userEmail;
                showSuccess(data.message);
                
                // Transition to Step 2
                setTimeout(() => {
                    clearAlerts();
                    transitionStep(2);
                }, 1500);
            } else {
                showError(data.message || 'Failed to send OTP.');
            }
        } catch (err) {
            showLoading(false);
            showError('Network connection failed. Please try again.');
        }
    }

    // Step 2: Verify OTP Call
    async function handleVerifyOtp(e) {
        e.preventDefault();
        clearAlerts();
        showLoading(true);

        const otpVal = document.getElementById('otp').value.trim();

        try {
            const response = await fetch(verifyOtpUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: userEmail, otp: otpVal })
            });

            const data = await response.json();
            showLoading(false);

            if (response.ok && data.success) {
                verifiedOtp = otpVal;
                showSuccess(data.message);

                // Transition to Step 3
                setTimeout(() => {
                    clearAlerts();
                    transitionStep(3);
                }, 1500);
            } else {
                showError(data.message || 'Verification failed.');
            }
        } catch (err) {
            showLoading(false);
            showError('Network connection failed. Please try again.');
        }
    }

    // Step 3: Reset Password Call
    async function handleResetPassword(e) {
        e.preventDefault();
        clearAlerts();
        
        const newPassword = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (newPassword.length < 8) {
            showError('Password must be at least 8 characters long.');
            return;
        }

        if (newPassword !== confirmPassword) {
            showError('Passwords do not match.');
            return;
        }

        showLoading(true);

        try {
            const response = await fetch(resetPasswordUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    email: userEmail, 
                    otp: verifiedOtp, 
                    password: newPassword,
                    password_confirmation: confirmPassword
                })
            });

            const data = await response.json();
            showLoading(false);

            if (response.ok && data.success) {
                showSuccess(data.message);
                
                // Redirect to Login Page
                setTimeout(() => {
                    window.location.href = "{{ route('login') }}";
                }, 2000);
            } else {
                showError(data.message || 'Failed to reset password.');
            }
        } catch (err) {
            showLoading(false);
            showError('Network connection failed. Please try again.');
        }
    }

    // Helpers
    function transitionStep(stepNum) {
        document.querySelectorAll('.wizard-step').forEach(step => {
            step.classList.remove('active');
        });
        document.getElementById(`step-${stepNum}`).classList.add('active');

        // Dots update
        document.querySelectorAll('.step-dot').forEach((dot, index) => {
            if (index < stepNum) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    function goBackToStep1() {
        clearAlerts();
        transitionStep(1);
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Global jQuery Form Validation Script
    $(document).ready(function() {
        // Disable native browser validation tooltips (bubbles)
        $('form').attr('novalidate', 'novalidate');

        // Monitor all form submits
        $(document).on('submit', 'form', function(e) {
            let form = $(this);

            // Bypass validation for delete confirmation forms or clear/reset filter forms
            const methodInput = form.find('input[name="_method"]');
            if (methodInput.length && methodInput.val().toUpperCase() === 'DELETE') {
                return;
            }
            if (form.attr('id') === 'logout-form') {
                return;
            }

            // Remove existing error states
            form.find('.invalid-feedback-custom').remove();
            form.find('.is-invalid-custom').removeClass('is-invalid-custom').css('border-color', '');

            let isValid = true;
            let firstInvalidEl = null;

            // Loop over all required fields
            form.find('input[required], textarea[required], select[required]').each(function() {
                let el = $(this);
                let val = el.val();

                // Check if empty or is unchecked checkbox/radio
                let isFieldInvalid = false;
                if (el.is(':checkbox') || el.is(':radio')) {
                    let name = el.attr('name');
                    if (name && form.find(`input[name="${name}"]:checked`).length === 0) {
                        isFieldInvalid = true;
                    }
                } else if (!val || val.toString().trim() === '') {
                    isFieldInvalid = true;
                }

                // If input type is email, check email format
                if (!isFieldInvalid && el.attr('type') === 'email') {
                    let emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailReg.test(val.toString().trim())) {
                        isValid = false;
                        el.addClass('is-invalid-custom').css('border-color', '#dc3545');
                        let container = el;
                        if (el.parent('.input-group').length) {
                            container = el.parent('.input-group');
                        }
                        if (!container.siblings('.invalid-feedback-custom').length) {
                            $('<div class="invalid-feedback-custom text-danger small mt-1 fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i> Please enter a valid email address.</div>').insertAfter(container);
                        }
                        if (!firstInvalidEl) {
                            firstInvalidEl = el;
                        }
                    }
                }

                if (isFieldInvalid) {
                    isValid = false;
                    el.addClass('is-invalid-custom').css('border-color', '#dc3545');

                    if (!firstInvalidEl) {
                        firstInvalidEl = el;
                    }

                    // Find a user-friendly label name
                    let fieldLabel = '';
                    let placeholder = el.attr('placeholder');
                    
                    // Try to find matching label using "for" or sibling
                    let id = el.attr('id');
                    let labelEl = id ? form.find(`label[for="${id}"]`) : [];
                    if (!labelEl.length) {
                        labelEl = el.closest('.mb-3, .mb-4, .form-group').find('label');
                    }
                    if (labelEl.length) {
                        fieldLabel = labelEl.first().text().replace('*', '').trim();
                    } else if (placeholder) {
                        fieldLabel = placeholder.trim();
                    } else {
                        fieldLabel = el.attr('name') ? el.attr('name').replace('_', ' ').trim() : 'This field';
                    }

                    // Clean label value
                    if (fieldLabel.toLowerCase().endsWith('optional')) {
                        fieldLabel = fieldLabel.replace(/optional/gi, '').replace(/[()]/g, '').trim();
                    }
                    if (fieldLabel.length > 30) {
                        fieldLabel = 'This field';
                    }

                    let container = el;
                    if (el.parent('.input-group').length) {
                        container = el.parent('.input-group');
                    }

                    // Append error message below the field if it doesn't already exist
                    if (!container.siblings('.invalid-feedback-custom').length) {
                        $('<div class="invalid-feedback-custom text-danger small mt-1 fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i> ' + fieldLabel + ' is required.</div>').insertAfter(container);
                    }
                }
            });

            // Prevent form submit if invalid
            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();

                // Focus/scroll to first invalid element
                if (firstInvalidEl) {
                    $('html, body').animate({
                        scrollTop: firstInvalidEl.offset().top - 120
                    }, 200);
                    firstInvalidEl.focus();
                }
            }
        });

        // Real-time clearance of error messages on value input
        $(document).on('input change keyup', 'form input, form textarea, form select', function() {
            let el = $(this);
            let val = el.val();
            let isCheckboxOrRadio = el.is(':checkbox') || el.is(':radio');
            
            let isValOk = true;
            if (isCheckboxOrRadio) {
                let name = el.attr('name');
                if (name && el.closest('form').find(`input[name="${name}"]:checked`).length > 0) {
                    isValOk = true;
                } else {
                    isValOk = false;
                }
            } else if (!val || val.toString().trim() === '') {
                isValOk = false;
            }

            if (isValOk) {
                el.removeClass('is-invalid-custom').css('border-color', '');
                let container = el;
                if (el.parent('.input-group').length) {
                    container = el.parent('.input-group');
                }
                container.siblings('.invalid-feedback-custom').remove();
            }
        });
    });
</script>
</body>
</html>
