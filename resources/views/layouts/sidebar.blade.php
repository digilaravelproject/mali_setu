<!-- Sidebar -->
<aside class="sidebar" id="sidebarMenu">
    <script>
        if (window.innerWidth > 991 && localStorage.getItem('sidebar_collapsed') === 'true') {
            document.getElementById('sidebarMenu').classList.add('collapsed');
        }
    </script>
    <!-- Desktop/Mobile Edge Toggle Button -->
    <button class="sidebar-toggle-arrow" id="desktopSidebarToggle" onclick="toggleDesktopSidebar(event)" title="Toggle Sidebar">
        <i class="fa-solid fa-chevron-left" id="toggleArrowIcon"></i>
    </button>
    <a href="{{ url('/') }}" class="sidebar-brand">
        <img src="{{ asset('landing_page_logo.jpeg') }}" alt="MaliSetu Logo" class="sidebar-logo">
        <h5 class="fw-bold mb-0">Mali Setu</h5>
    </a>

    <div class="sidebar-menu-wrapper" style="flex-grow: 1; overflow-y: auto; margin-bottom: 20px; padding-right: 5px;">
        <ul class="nav-menu">
            @if(auth()->user()->user_type === 'bloger')
                <!-- Blog Module -->
                <li class="nav-item">
                    <a href="{{ route('blogs.index') }}" class="nav-link-custom {{ Request::routeIs('blogs.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-blog"></i> Blog Portal
                    </a>
                </li>

                <!-- More Module Dropdown (for bloggers) -->
                <li class="nav-item has-dropdown {{ Request::routeIs('dashboard.privacy-policy') || Request::routeIs('dashboard.terms-conditions') || Request::routeIs('dashboard.contact-support') ? 'open' : '' }}">
                    <a class="nav-link-custom dropdown-toggle-custom" onclick="toggleSidebarDropdown(event)">
                        <i class="fa-solid fa-ellipsis"></i> More
                        <i class="fa-solid fa-chevron-down ms-auto dropdown-arrow"></i>
                    </a>
                    <ul class="submenu-menu">
                        <li>
                            <a href="{{ route('dashboard.privacy-policy') }}" class="nav-link-custom submenu-link {{ Request::routeIs('dashboard.privacy-policy') ? 'active' : '' }}">
                                <i class="fa-solid fa-shield-halved"></i> Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.terms-conditions') }}" class="nav-link-custom submenu-link {{ Request::routeIs('dashboard.terms-conditions') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-contract"></i> Terms & Condition
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.contact-support') }}" class="nav-link-custom submenu-link {{ Request::routeIs('dashboard.contact-support') ? 'active' : '' }}">
                                <i class="fa-solid fa-headset"></i> Contact Support
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                @csrf
                                <button type="submit" class="nav-link-custom submenu-link w-100 border-0 text-start logout-btn-submenu" style="padding: 10px 24px 10px 48px; background: transparent; color: #ff7878;">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            @else
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link-custom {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i> Dashboard
                    </a>
                </li>

                <!-- Matrimony Module Dropdown -->
                <li class="nav-item has-dropdown {{ Request::routeIs('matrimony.*') ? 'open' : '' }}">
                    <a class="nav-link-custom dropdown-toggle-custom" onclick="toggleSidebarDropdown(event)">
                        <i class="fa-solid fa-heart"></i> Matrimony
                        <i class="fa-solid fa-chevron-down ms-auto dropdown-arrow"></i>
                    </a>
                    <ul class="submenu-menu">
                        <li>
                            <a href="{{ route('matrimony.create') }}" class="nav-link-custom submenu-link {{ Request::routeIs('matrimony.create') ? 'active' : '' }}">
                                <i class="fa-solid fa-heart-circle-plus"></i> Create Profile
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('matrimony.browse') }}" class="nav-link-custom submenu-link {{ Request::routeIs('matrimony.browse') ? 'active' : '' }}">
                                <i class="fa-solid fa-magnifying-glass"></i> Matrimony Profile
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('matrimony.requests') }}" class="nav-link-custom submenu-link {{ Request::routeIs('matrimony.requests') ? 'active' : '' }}">
                                <i class="fa-solid fa-paper-plane"></i> Request
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('matrimony.conversations') }}" class="nav-link-custom submenu-link {{ Request::routeIs('matrimony.conversations') || Request::routeIs('matrimony.chat') ? 'active' : '' }}">
                                <i class="fa-solid fa-comments"></i> Messages
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Manage Business Dropdown -->
                <li class="nav-item has-dropdown {{ Request::routeIs('dashboard.business.*') || Request::routeIs('dashboard.jobs.*') ? 'open' : '' }}">
                    <a class="nav-link-custom dropdown-toggle-custom" onclick="toggleSidebarDropdown(event)">
                        <i class="fa-solid fa-briefcase"></i> Business
                        <i class="fa-solid fa-chevron-down ms-auto dropdown-arrow"></i>
                    </a>
                    <ul class="submenu-menu">
                        <li>
                            <a href="{{ route('dashboard.business.create') }}" class="nav-link-custom submenu-link {{ Request::routeIs('dashboard.business.create') ? 'active' : '' }}">
                                <i class="fa-solid fa-plus-circle"></i> Create Business
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.business.browse') }}" class="nav-link-custom submenu-link {{ Request::routeIs('dashboard.business.browse') || Request::routeIs('dashboard.business.show') ? 'active' : '' }}">
                                <i class="fa-solid fa-magnifying-glass"></i> All Business
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.jobs.applied') }}" class="nav-link-custom submenu-link {{ Request::routeIs('dashboard.jobs.applied') ? 'active' : '' }}">
                                <i class="fa-solid fa-briefcase"></i> All Jobs
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Blog Portal -->
                <li class="nav-item">
                    <a href="{{ route('blogs.index') }}" class="nav-link-custom {{ Request::routeIs('blogs.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-blog"></i> Blog Portal
                    </a>
                </li>

                <!-- More Dropdown -->
                <li class="nav-item has-dropdown {{ Request::routeIs('subscriptions.*') || Request::routeIs('dashboard.privacy-policy') || Request::routeIs('dashboard.terms-conditions') || Request::routeIs('dashboard.contact-support') ? 'open' : '' }}">
                    <a class="nav-link-custom dropdown-toggle-custom" onclick="toggleSidebarDropdown(event)">
                        <i class="fa-solid fa-ellipsis"></i> More
                        <i class="fa-solid fa-chevron-down ms-auto dropdown-arrow"></i>
                    </a>
                    <ul class="submenu-menu">
                        <li>
                            <a href="{{ route('subscriptions.index') }}" class="nav-link-custom submenu-link {{ Request::routeIs('subscriptions.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-credit-card"></i> My Subscription
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.privacy-policy') }}" class="nav-link-custom submenu-link {{ Request::routeIs('dashboard.privacy-policy') ? 'active' : '' }}">
                                <i class="fa-solid fa-shield-halved"></i> Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.terms-conditions') }}" class="nav-link-custom submenu-link {{ Request::routeIs('dashboard.terms-conditions') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-contract"></i> Terms & Condition
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.contact-support') }}" class="nav-link-custom submenu-link {{ Request::routeIs('dashboard.contact-support') ? 'active' : '' }}">
                                <i class="fa-solid fa-headset"></i> Contact Support
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                @csrf
                                <button type="submit" class="nav-link-custom submenu-link w-100 border-0 text-start logout-btn-submenu" style="padding: 10px 24px 10px 48px; background: transparent; color: #ff7878;">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</aside>
