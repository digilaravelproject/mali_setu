<!-- Top Navbar -->
<nav class="dashboard-navbar">
    <div class="d-flex align-items-center">
        <!-- Brand Branding (Logo + Name) -->
        <a href="{{ url('/') }}" class="navbar-brand-custom d-flex align-items-center gap-2 text-decoration-none text-dark">
            <img src="{{ asset('landing_page_logo.jpeg') }}" alt="MaliSetu Logo" class="navbar-logo-custom" style="height: 72px; border-radius: 8px; background: #fff; border: 1px solid rgba(0,0,0,0.05);">
            <span class="ms-2 fw-bold text-primary fs-4">
                Mali<span style="color: var(--accent);"> Setu</span>
            </span>
        </a>
    </div>

    <!-- Right-aligned Menu + User Dropdown Container -->
    <div class="d-flex align-items-center gap-4">
        <!-- Middle Menu Navigation Items (Visible only on Desktop) -->
        <div class="navbar-menu-items d-none d-lg-flex align-items-center gap-2">
            @if(auth()->user()->user_type === 'bloger')
                <!-- Blog Portal (Blogger Mode) -->
                <a href="{{ route('blogs.index') }}" class="nav-link-navbar {{ Request::routeIs('blogs.*') ? 'active' : '' }}">
                    Blog Portal
                </a>

                <!-- More Dropdown (Blogger Mode) -->
                <div class="nav-item-navbar dropdown-custom">
                    <button class="nav-link-navbar dropdown-toggle-navbar {{ Request::routeIs('dashboard.privacy-policy') || Request::routeIs('dashboard.terms-conditions') || Request::routeIs('dashboard.contact-support') ? 'active' : '' }}">
                        More <i class="fa-solid fa-chevron-down small ms-1"></i>
                    </button>
                    <div class="dropdown-menu-navbar">
                        <a href="{{ route('dashboard.privacy-policy') }}" class="dropdown-item-navbar {{ Request::routeIs('dashboard.privacy-policy') ? 'active' : '' }}">
                            <i class="fa-solid fa-shield-halved text-primary"></i> Privacy Policy
                        </a>
                        <a href="{{ route('dashboard.terms-conditions') }}" class="dropdown-item-navbar {{ Request::routeIs('dashboard.terms-conditions') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-contract text-primary"></i> Terms & Conditions
                        </a>
                        <a href="{{ route('dashboard.contact-support') }}" class="dropdown-item-navbar {{ Request::routeIs('dashboard.contact-support') ? 'active' : '' }}">
                            <i class="fa-solid fa-headset text-primary"></i> Contact Support
                        </a>
                        <div class="dropdown-divider my-1"></div>
                    </div>
                </div>
            @else
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="nav-link-navbar {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>

                <!-- Matrimony Dropdown -->
                <div class="nav-item-navbar dropdown-custom">
                    <button class="nav-link-navbar dropdown-toggle-navbar {{ Request::routeIs('matrimony.*') ? 'active' : '' }}">
                        Matrimony <i class="fa-solid fa-chevron-down small ms-1"></i>
                    </button>
                    <div class="dropdown-menu-navbar">
                        <a href="{{ route('matrimony.create') }}" class="dropdown-item-navbar {{ Request::routeIs('matrimony.create') ? 'active' : '' }}">
                            <i class="fa-solid fa-heart-circle-plus text-primary"></i> Create Profile
                        </a>
                        <a href="{{ route('matrimony.browse') }}" class="dropdown-item-navbar {{ Request::routeIs('matrimony.browse') ? 'active' : '' }}">
                            <i class="fa-solid fa-magnifying-glass text-primary"></i> Matrimony Profiles
                        </a>
                        <a href="{{ route('matrimony.requests') }}" class="dropdown-item-navbar {{ Request::routeIs('matrimony.requests') ? 'active' : '' }}">
                            <i class="fa-solid fa-paper-plane text-primary"></i> Requests
                        </a>
                        <a href="{{ route('matrimony.conversations') }}" class="dropdown-item-navbar {{ Request::routeIs('matrimony.conversations') || Request::routeIs('matrimony.chat') ? 'active' : '' }}">
                            <i class="fa-solid fa-comments text-primary"></i> Messages
                        </a>
                    </div>
                </div>

                <!-- Manage Business Dropdown -->
                <div class="nav-item-navbar dropdown-custom">
                    <button class="nav-link-navbar dropdown-toggle-navbar {{ Request::routeIs('dashboard.business.*') || Request::routeIs('dashboard.jobs.*') ? 'active' : '' }}">
                        Business <i class="fa-solid fa-chevron-down small ms-1"></i>
                    </button>
                    <div class="dropdown-menu-navbar">
                        <a href="{{ route('dashboard.business.create') }}" class="dropdown-item-navbar {{ Request::routeIs('dashboard.business.create') ? 'active' : '' }}">
                            <i class="fa-solid fa-plus-circle text-primary"></i> Create Business
                        </a>
                        <a href="{{ route('dashboard.business.browse') }}" class="dropdown-item-navbar {{ Request::routeIs('dashboard.business.browse') || Request::routeIs('dashboard.business.show') ? 'active' : '' }}">
                            <i class="fa-solid fa-magnifying-glass text-primary"></i> All Businesses
                        </a>
                        <a href="{{ route('dashboard.jobs.applied') }}" class="dropdown-item-navbar {{ Request::routeIs('dashboard.jobs.applied') ? 'active' : '' }}">
                            <i class="fa-solid fa-briefcase text-primary"></i> All Jobs
                        </a>
                    </div>
                </div>

                <!-- Blog Portal -->
                <a href="{{ route('blogs.index') }}" class="nav-link-navbar {{ Request::routeIs('blogs.*') ? 'active' : '' }}">
                    Blog Portal
                </a>

                <!-- More Dropdown -->
                <div class="nav-item-navbar dropdown-custom">
                    <button class="nav-link-navbar dropdown-toggle-navbar {{ Request::routeIs('subscriptions.*') || Request::routeIs('dashboard.privacy-policy') || Request::routeIs('dashboard.terms-conditions') || Request::routeIs('dashboard.contact-support') ? 'active' : '' }}">
                        More <i class="fa-solid fa-chevron-down small ms-1"></i>
                    </button>
                    <div class="dropdown-menu-navbar">
                        <a href="{{ route('subscriptions.index') }}" class="dropdown-item-navbar {{ Request::routeIs('subscriptions.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-credit-card text-primary"></i> My Subscription
                        </a>
                        <a href="{{ route('dashboard.privacy-policy') }}" class="dropdown-item-navbar {{ Request::routeIs('dashboard.privacy-policy') ? 'active' : '' }}">
                            <i class="fa-solid fa-shield-halved text-primary"></i> Privacy Policy
                        </a>
                        <a href="{{ route('dashboard.terms-conditions') }}" class="dropdown-item-navbar {{ Request::routeIs('dashboard.terms-conditions') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-contract text-primary"></i> Terms & Conditions
                        </a>
                        <a href="{{ route('dashboard.contact-support') }}" class="dropdown-item-navbar {{ Request::routeIs('dashboard.contact-support') ? 'active' : '' }}">
                            <i class="fa-solid fa-headset text-primary"></i> Contact Support
                        </a>
                        <div class="dropdown-divider my-1"></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Block (User Profile) -->
        <div class="navbar-user-dropdown">
            <button class="navbar-dropdown-btn" onclick="toggleNavbarDropdown(event)">
                @if(optional(auth()->user())->photo)
                    <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="Profile" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #fff;" />
                @else
                    <img src="{{ asset('default-avatar.png') }}" alt="Profile" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #fff;" />
                @endif
                <span class="d-none d-sm-inline">{{ optional(auth()->user())->name }}</span>
                <i class="fa-solid fa-chevron-down small"></i>
            </button>
            <div class="navbar-dropdown-menu" id="navbarDropdownMenu">
                @if(auth()->user()->user_type !== 'bloger')
                    <button class="navbar-dropdown-item" onclick="selectDropdownTab('edit-profile')">
                        <i class="fa-solid fa-user-pen text-primary"></i> Edit Profile
                    </button>
                @endif
                <button class="navbar-dropdown-item" onclick="selectDropdownTab('change-password')">
                    <i class="fa-solid fa-key text-primary"></i> Security
                </button>
                @if(auth()->user()->user_type !== 'bloger')
                    <button class="navbar-dropdown-item" onclick="selectDropdownTab('features')">
                        <i class="fa-solid fa-circle-nodes text-primary"></i> My Featured Nodes
                    </button>
                    <div class="dropdown-divider my-1"></div>
                    <button class="navbar-dropdown-item text-danger" onclick="selectDropdownTab('danger-zone')">
                        <i class="fa-solid fa-triangle-exclamation text-danger"></i> Danger Zone
                    </button>
                @endif

                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item-navbar text-danger w-100 border-0 text-start bg-transparent" style="margin-left: 8px !important;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
