<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account — Mali Setu</title>
    <meta name="description" content="Register an account on Mali Setu today. Expand your business, find matches, and serve the community.">

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
            padding: 40px 20px;
        }

        /* Bootstrap Overrides */
        .text-primary { color: var(--primary) !important; }
        .bg-primary { background-color: var(--primary) !important; }

        .auth-container {
            max-width: 1100px;
            width: 100%;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(132, 20, 79, 0.1);
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .auth-banner {
            background: linear-gradient(135deg, #84144f, #aa1262);
            color: #fff;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .auth-banner::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }

        .auth-form-side {
            padding: 50px;
        }

        .form-control-lg, .form-select-lg {
            border-radius: 12px;
            font-size: 1rem;
            padding: 14px 20px;
            border: 1.5px solid #e2e8f0;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .form-control-lg:focus, .form-select-lg:focus {
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

        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            z-index: 10;
        }

        .back-home:hover {
            color: var(--primary-dark);
            transform: translateX(-3px);
        }

        /* .brand-logo {
            height: 50px;
            width: auto;
            margin-bottom: 30px;
        } */

        .brand-logo {
            margin-left: 35%;
            height: 88px;
            width: auto;
        }

        /* Interactive User Type Select Cards */
        .type-option-card {
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            background: #fff;
        }

        .type-option-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.03);
            border-color: var(--primary);
        }

        .type-option-card.selected {
            border-color: var(--primary);
            background: rgba(255, 71, 87, 0.05);
            box-shadow: 0 10px 20px rgba(255, 71, 87, 0.08);
        }

        .type-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(255, 71, 87, 0.05);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 12px;
            transition: 0.3s;
        }

        .type-option-card.selected .type-icon {
            background: var(--primary);
            color: #fff;
        }

        .help-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 24px;
            margin-top: 30px;
        }

        @media (max-width: 991px) {
            .auth-banner {
                display: none !important;
            }
            .auth-form-side {
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>

<a href="{{ url('/') }}" class="back-home">
    <i class="fa-solid fa-arrow-left"></i> Home
</a>

<div class="auth-container">
    <div class="row g-0">
        <!-- Banner Side -->
        <div class="col-lg-5 auth-banner">
            <div>
                <img src="{{ asset('landing_page_logo.jpeg') }}" alt="MaliSetu Logo" class="brand-logo rounded-3 shadow-sm bg-white p-2">
                <h2 class="fw-bold mt-4">Empowering Our Community Digitally</h2>
                <p class="opacity-75 mt-3">Register your profile today to access secure Matrimony profiles, connect your business, list volunteer options, or interact with professional services.</p>
            </div>

            <!-- Dynamic Info Cards on selected type -->
            <div class="help-panel">
                <h6 class="fw-bold text-warning mb-2" id="info-title">General Membership</h6>
                <p class="small opacity-75 mb-0" id="info-description">Standard membership allows you to browse the active business directory, verify details, and read official community announcements.</p>
            </div>
            
            <div class="border-top border-white border-opacity-10 pt-4 mt-4">
                <small>Already have an account? <a href="{{ route('login') }}" class="text-warning fw-bold text-decoration-none">Sign In instead</a></small>
            </div>
        </div>

        <!-- Form Side -->
        <div class="col-lg-7 auth-form-side">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">Register Profile</h3>
                <span class="text-muted small">Have an account? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign In</a></span>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf
                
                <!-- Section 1: Personal Details -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(10px);">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-user me-2"></i> 1. Personal Information</h5>
                        
                        <div class="row">
                            <!-- Title select -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label small fw-bold text-secondary">Title <span class="text-danger">*</span></label>
                                <select name="title" class="form-select form-select-lg @error('title') is-invalid @enderror" required>
                                    <option value="Mr." {{ old('title') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                    <option value="Mrs." {{ old('title') == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                    <option value="Ms." {{ old('title') == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                                    <option value="Dr." {{ old('title') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                </select>
                                @error('title')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- First Name -->
                            <div class="col-md-9 mb-3">
                                <label class="form-label small fw-bold text-secondary">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control form-control-lg @error('first_name') is-invalid @enderror" placeholder="Enter first name" required>
                                @error('first_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Middle Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="form-control form-control-lg @error('middle_name') is-invalid @enderror" placeholder="Enter middle name (optional)">
                                @error('middle_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Last Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control form-control-lg @error('last_name') is-invalid @enderror" placeholder="Enter last name" required>
                                @error('last_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="name@example.com" required>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Date of Birth -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" value="{{ old('dob') }}" class="form-control form-control-lg @error('dob') is-invalid @enderror" required>
                                @error('dob')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone Number with +91 code prefix -->
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary">Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-secondary border-end-0 rounded-start-3" style="padding: 14px 20px;">+91</span>
                                <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control form-control-lg border-start-0 rounded-end-3 @error('phone') is-invalid @enderror" placeholder="10-digit mobile number" pattern="[0-9]{10}" required>
                            </div>
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Verification Details -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(10px);">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-file-shield me-2"></i> 2. Caste Verification</h5>
                        <p class="small text-secondary mb-4">Please upload a valid Caste Certificate or other proof (Father's Caste Document) to verify community authenticity. Supported: JPG, JPEG, PNG, PDF (Max: 5MB).</p>
                        
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary">Caste Certificate File <span class="text-danger">*</span></label>
                            <input type="file" name="cast_certificate" class="form-control form-control-lg @error('cast_certificate') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf" required>
                            @error('cast_certificate')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Address Details -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(10px);">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-location-dot me-2"></i> 3. Address Details</h5>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Address Line <span class="text-danger">*</span></label>
                            <input type="text" name="address" value="{{ old('address') }}" class="form-control form-control-lg @error('address') is-invalid @enderror" placeholder="House/Flat No, Apartment, Street" required>
                            @error('address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Pincode <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="pincode" value="{{ old('pincode') }}" class="form-control form-control-lg @error('pincode') is-invalid @enderror" placeholder="6-digit pincode" id="pincodeInput" maxlength="6" required>
                                    <button class="btn btn-outline-secondary bg-white border-start-0" type="button" id="get_location_btn" style="border-top-right-radius: 12px; border-bottom-right-radius: 12px;" title="Fetch My Current Coordinates">
                                        <i class="fa-solid fa-location-dot text-primary"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="latitude" id="latitudeInput" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitudeInput" value="{{ old('longitude') }}">
                                @error('pincode')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Country <span class="text-danger">*</span></label>
                                <input type="text" name="country" value="{{ old('country', 'India') }}" class="form-control form-control-lg @error('country') is-invalid @enderror" required>
                                @error('country')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">State <span class="text-danger">*</span></label>
                                <input type="text" name="state" value="{{ old('state') }}" class="form-control form-control-lg @error('state') is-invalid @enderror" placeholder="Enter state" required>
                                @error('state')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" value="{{ old('city') }}" class="form-control form-control-lg @error('city') is-invalid @enderror" placeholder="Enter city" required>
                                @error('city')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Taluka</label>
                                <input type="text" name="taluka" value="{{ old('taluka') }}" class="form-control form-control-lg @error('taluka') is-invalid @enderror" placeholder="Enter taluka (optional)">
                                @error('taluka')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold text-secondary">Village <span class="text-danger">*</span></label>
                                <input type="text" name="village" value="{{ old('village') }}" class="form-control form-control-lg @error('village') is-invalid @enderror" placeholder="Enter village" required>
                                @error('village')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Professional & Security Details -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(10px);">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-briefcase me-2"></i> 4. Professional & Security Details</h5>
                        
                        <!-- Select Profile Type (Interactive Grid) -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary mb-3">I am signing up as a... *</label>
                            <input type="hidden" name="user_type" id="user_type" value="{{ old('user_type', 'general') }}">
                            
                            <div class="row g-3">
                                <div class="col-6 col-sm-3">
                                    <div class="type-option-card selected" data-type="general" onclick="selectUserType('general')">
                                        <div class="type-icon"><i class="fa-solid fa-users"></i></div>
                                        <h6 class="small fw-bold mb-0">General</h6>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="type-option-card" data-type="business" onclick="selectUserType('business')">
                                        <div class="type-icon"><i class="fa-solid fa-briefcase"></i></div>
                                        <h6 class="small fw-bold mb-0">Business</h6>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="type-option-card" data-type="matrimony" onclick="selectUserType('matrimony')">
                                        <div class="type-icon"><i class="fa-solid fa-heart"></i></div>
                                        <h6 class="small fw-bold mb-0">Matrimony</h6>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="type-option-card" data-type="volunteer" onclick="selectUserType('volunteer')">
                                        <div class="type-icon"><i class="fa-solid fa-handshake-angle"></i></div>
                                        <h6 class="small fw-bold mb-0">Volunteer</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Occupation & Company -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Occupation <span class="text-danger">*</span></label>
                                <select name="occupation" class="form-select form-select-lg @error('occupation') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('occupation') ? '' : 'selected' }}>Select Occupation</option>
                                    <option value="Service" {{ old('occupation') == 'Service' ? 'selected' : '' }}>Service</option>
                                    <option value="Business" {{ old('occupation') == 'Business' ? 'selected' : '' }}>Business</option>
                                    <option value="Student" {{ old('occupation') == 'Student' ? 'selected' : '' }}>Student</option>
                                    <option value="Not Working" {{ old('occupation') == 'Not Working' ? 'selected' : '' }}>Not Working</option>
                                </select>
                                @error('occupation')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Company Name</label>
                                <input type="text" name="company_name" value="{{ old('company_name') }}" class="form-control form-control-lg @error('company_name') is-invalid @enderror" placeholder="Enter company (optional)">
                                @error('company_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Dept & Designation -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Department Name</label>
                                <input type="text" name="dept_name" value="{{ old('dept_name') }}" class="form-control form-control-lg @error('dept_name') is-invalid @enderror" placeholder="Enter department (optional)">
                                @error('dept_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Designation</label>
                                <input type="text" name="designation" value="{{ old('designation') }}" class="form-control form-control-lg @error('designation') is-invalid @enderror" placeholder="Enter designation (optional)">
                                @error('designation')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Password Input -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-secondary">Password *</label>
                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="reg-password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                        placeholder="Min 8 characters" 
                                        required
                                    >
                                    <button type="button" class="btn btn-outline-secondary border-start-0 bg-white" onclick="togglePasswordVisibility('reg-password', this)" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                                        <i class="fa-solid fa-eye text-secondary"></i>
                                    </button>
                                    @error('password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password Confirmation Input -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-secondary">Confirm Password *</label>
                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        name="password_confirmation" 
                                        id="reg-confirm-password"
                                        class="form-control form-control-lg" 
                                        placeholder="Repeat password" 
                                        required
                                    >
                                    <button type="button" class="btn btn-outline-secondary border-start-0 bg-white" onclick="togglePasswordVisibility('reg-confirm-password', this)" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                                        <i class="fa-solid fa-eye text-secondary"></i>
                                    </button>
                                    @error('password_confirmation')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms & Conditions Checkbox -->
                <div class="mb-4 form-check d-flex align-items-center gap-2 px-4">
                    <input type="checkbox" name="term_condition" class="form-check-input shadow-none cursor-pointer" id="term_condition" value="1" required>
                    <label class="form-check-label small text-secondary cursor-pointer" for="term_condition">
                        I agree to the <a href="{{ url('terms-condition') }}" class="text-primary text-decoration-none fw-bold" target="_blank">Terms of Service</a> & <a href="{{ url('privacy-policy') }}" class="text-primary text-decoration-none fw-bold" target="_blank">Privacy Policy</a>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-lg w-100 mt-2 py-3 rounded-4 shadow-sm fw-bold">
                    Create Account <i class="fa-solid fa-user-plus ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Interactive selection logic
    const profilesInfo = {
        general: {
            title: "General Membership",
            desc: "Standard membership allows you to browse the active business directory, verify details, and read official community announcements."
        },
        business: {
            title: "Business Account",
            desc: "List your shop, service profile, or enterprise. Highlight products, advertise services, receive payments, and post jobs to hire community talent."
        },
        matrimony: {
            title: "Matrimony SEEKER Profile",
            desc: "Create a detailed personal matchmaking profile. Send connection requests, chat with potential life partners securely under high-privacy filters."
        },
        volunteer: {
            title: "Volunteer Program",
            desc: "Engage in social work. View opportunities listed by charities or community nodes, apply for tasks, and coordinate events."
        }
    };

    function selectUserType(type) {
        // Set hidden input
        document.getElementById('user_type').value = type;

        // Change select class
        document.querySelectorAll('.type-option-card').forEach(card => {
            card.classList.remove('selected');
        });
        document.querySelector(`.type-option-card[data-type="${type}"]`).classList.add('selected');

        // Update banner text
        document.getElementById('info-title').innerText = profilesInfo[type].title;
        document.getElementById('info-description').innerText = profilesInfo[type].desc;
    }

    // Toggle Password Visibility
    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Indian Pincode Auto-lookup and Auto-population
    document.addEventListener('DOMContentLoaded', () => {
        const type = document.getElementById('user_type').value;
        selectUserType(type);

        const pincodeInput = document.querySelector('input[name="pincode"]');
        if (pincodeInput) {
            pincodeInput.addEventListener('input', function() {
                const pincode = this.value.trim();
                if (pincode.length === 6 && /^\d+$/.test(pincode)) {
                    pincodeInput.classList.add('is-valid');
                    
                    fetch(`https://api.postalpincode.in/pincode/${pincode}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data && data[0] && data[0].Status === 'Success') {
                                const postOffice = data[0].PostOffice[0];
                                const state = postOffice.State;
                                const city = postOffice.District; 
                                
                                const stateInput = document.querySelector('input[name="state"]');
                                const cityInput = document.querySelector('input[name="city"]');
                                
                                if (stateInput) {
                                    stateInput.value = state;
                                    stateInput.classList.add('is-valid');
                                }
                                if (cityInput) {
                                    cityInput.value = city;
                                    cityInput.classList.add('is-valid');
                                }
                            }
                        })
                        .catch(err => console.error('Error fetching pincode details:', err));
                }
            });
        }

        const getLocBtn = document.getElementById('get_location_btn');
        if (getLocBtn) {
            getLocBtn.addEventListener('click', function() {
                const icon = getLocBtn.querySelector('i');
                const oldClass = icon.className;
                icon.className = 'fa-solid fa-spinner fa-spin text-primary';
                
                navigator.geolocation.getCurrentPosition(function(position) {
                    icon.className = 'fa-solid fa-circle-check text-success';
                    document.getElementById('latitudeInput').value = position.coords.latitude;
                    document.getElementById('longitudeInput').value = position.coords.longitude;
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
