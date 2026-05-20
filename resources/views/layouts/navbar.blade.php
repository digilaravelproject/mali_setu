<!-- Top Navbar -->
<nav class="dashboard-navbar">
    <div class="navbar-brand-title">
        <i class="fa-solid fa-briefcase text-primary me-2"></i> Mali Setu Workspace
    </div>
    
    <div class="navbar-user-dropdown">
        <button class="navbar-dropdown-btn" onclick="toggleNavbarDropdown(event)">
            <i class="fa-solid fa-circle-user fs-5"></i>
            <span>{{ $user->name }}</span>
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
