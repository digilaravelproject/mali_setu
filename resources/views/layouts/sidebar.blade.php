<!-- Sidebar -->
<aside class="sidebar" id="sidebarMenu">
    <div>
        <a href="{{ url('/') }}" class="sidebar-brand">
            <img src="{{ asset('landing_page_logo.jpeg') }}" alt="MaliSetu Logo" class="sidebar-logo">
            <h5 class="fw-bold mb-0">Mali Setu</h5>
        </a>

        <ul class="nav-menu">
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
                    <i class="fa-solid fa-magnifying-glass text-primary"></i> Total Businesses
                </a>
            </li>
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
