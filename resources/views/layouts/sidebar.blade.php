<!-- Sidebar -->
<aside class="sidebar" id="sidebarMenu">
    <div>
        <a href="{{ url('/') }}" class="sidebar-brand">
            <img src="{{ asset('landing_page_logo.jpeg') }}" alt="MaliSetu Logo" class="sidebar-logo">
            <h5 class="fw-bold mb-0">Mali Setu</h5>
        </a>

        <ul class="nav-menu">
            <li class="nav-item">
                <a class="nav-link-custom active" onclick="switchTab('overview', this)">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom" onclick="switchTab('business', this)">
                    <i class="fa-solid fa-briefcase"></i> Manage Business
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
