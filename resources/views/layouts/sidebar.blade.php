<!-- Sidebar -->
<aside class="sidebar" id="sidebarMenu">
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
            @else
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link-custom {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i> Dashboard
                    </a>
                </li>

                <!-- Matrimony Module -->
                <li class="nav-item">
                    <a href="{{ route('matrimony.index') }}" class="nav-link-custom {{ Request::routeIs('matrimony.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-heart"></i> Matrimony
                    </a>
                </li>
                <li class="nav-item ps-3">
                    <a href="{{ route('matrimony.browse') }}" class="nav-link-custom small {{ Request::routeIs('matrimony.browse') ? 'active' : '' }}" style="font-size:0.82rem; padding: 8px 16px;">
                        <i class="fa-solid fa-magnifying-glass text-primary"></i> Users Profiles
                    </a>
                </li>
                <li class="nav-item ps-3">
                    <a href="{{ route('matrimony.requests') }}" class="nav-link-custom small {{ Request::routeIs('matrimony.requests') ? 'active' : '' }}" style="font-size:0.82rem; padding: 8px 16px;">
                        <i class="fa-solid fa-paper-plane text-primary"></i> Requests
                    </a>
                </li>
                <li class="nav-item ps-3">
                    <a href="{{ route('matrimony.conversations') }}" class="nav-link-custom small {{ Request::routeIs('matrimony.conversations') || Request::routeIs('matrimony.chat') ? 'active' : '' }}" style="font-size:0.82rem; padding: 8px 16px;">
                        <i class="fa-solid fa-comments text-primary"></i> Messages
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('dashboard.business.index') }}" class="nav-link-custom {{ Request::routeIs('dashboard.business.index') || Request::routeIs('dashboard.business.create') || Request::routeIs('dashboard.business.edit') ? 'active' : '' }}">
                        <i class="fa-solid fa-briefcase"></i> Manage Business
                    </a>
                </li>
                <li class="nav-item ps-3">
                    <a href="{{ route('dashboard.business.browse') }}" class="nav-link-custom small {{ Request::routeIs('dashboard.business.browse') || Request::routeIs('dashboard.business.show') ? 'active' : '' }}" style="font-size:0.82rem; padding: 8px 16px;">
                        <i class="fa-solid fa-magnifying-glass text-primary"></i> All Businesses
                    </a>
                </li>
                <li class="nav-item ps-3">
                    <a href="{{ route('dashboard.jobs.applied') }}" class="nav-link-custom small {{ Request::routeIs('dashboard.jobs.applied') ? 'active' : '' }}" style="font-size:0.82rem; padding: 8px 16px;">
                        <i class="fa-solid fa-briefcase text-primary"></i> All Jobs
                    </a>
                </li>

                <!-- Blog Module -->
                <li class="nav-item">
                    <a href="{{ route('blogs.index') }}" class="nav-link-custom {{ Request::routeIs('blogs.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-blog"></i> Blog Portal
                    </a>
                </li>

                <!-- Subscription Module -->
                <li class="nav-item">
                    <a href="{{ route('subscriptions.index') }}" class="nav-link-custom {{ Request::routeIs('subscriptions.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-credit-card"></i> My Subscriptions
                    </a>
                </li>

                <!-- Privacy Policy -->
                <li class="nav-item">
                    <a href="{{ route('dashboard.privacy-policy') }}" class="nav-link-custom {{ Request::routeIs('dashboard.privacy-policy') ? 'active' : '' }}">
                        <i class="fa-solid fa-shield-halved"></i> Privacy Policy
                    </a>
                </li>

                <!-- Terms & Conditions -->
                <li class="nav-item">
                    <a href="{{ route('dashboard.terms-conditions') }}" class="nav-link-custom {{ Request::routeIs('dashboard.terms-conditions') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-contract"></i> Terms & Conditions
                    </a>
                </li>

                <!-- Contact Support -->
                <li class="nav-item">
                    <a href="{{ route('dashboard.contact-support') }}" class="nav-link-custom {{ Request::routeIs('dashboard.contact-support') ? 'active' : '' }}">
                        <i class="fa-solid fa-headset"></i> Contact Support
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <div>
        <form action="{{ route('logout') }}" method="POST" id="logout-form">
            @csrf
            <button type="submit" class="nav-link-custom logout-btn w-100 border-0 text-start">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
            </button>
        </form>
    </div>
</aside>
