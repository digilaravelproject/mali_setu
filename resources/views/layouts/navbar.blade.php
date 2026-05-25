<!-- Top Navbar -->
<nav class="dashboard-navbar">
    <div class="d-flex align-items-center">
        <button class="btn btn-outline-primary d-lg-none me-3 py-1.5 px-2.5 rounded-3" id="sidebarToggle" onclick="toggleSidebarMenu(event)" style="border-width: 1.5px;">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="navbar-brand-title">
            <i class="fa-solid fa-briefcase text-primary me-2"></i> Mali Setu Workspace
        </div>
    </div>
    
    <div class="navbar-user-dropdown">
        <button class="navbar-dropdown-btn" onclick="toggleNavbarDropdown(event)">
            @if(optional(auth()->user())->photo)
                <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="Profile" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #fff;" />
            @else
                <span style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg, rgba(173,20,87,0.18), rgba(173,20,87,0.10));color:#fff;font-size:16px;"><i class="fa-solid fa-user"></i></span>
            @endif
            <span>{{ optional(auth()->user())->name }}</span>
            <i class="fa-solid fa-chevron-down small"></i>
        </button>
        <div class="navbar-dropdown-menu" id="navbarDropdownMenu">
            <button class="navbar-dropdown-item" onclick="selectDropdownTab('edit-profile')">
                <i class="fa-solid fa-user-pen text-primary"></i> Edit Profile
            </button>
            <button class="navbar-dropdown-item" onclick="selectDropdownTab('change-password')">
                <i class="fa-solid fa-key text-primary"></i> Security
            </button>
            <button class="navbar-dropdown-item" onclick="selectDropdownTab('features')">
                <i class="fa-solid fa-circle-nodes text-primary"></i> My Featured Nodes
            </button>
            <div class="dropdown-divider my-1"></div>
            <button class="navbar-dropdown-item text-danger" onclick="selectDropdownTab('danger-zone')">
                <i class="fa-solid fa-triangle-exclamation text-danger"></i> Danger Zone
            </button>
        </div>
    </div>
</nav>
