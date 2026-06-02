<!-- Mobile Bottom Bar (Sticky/Fixed at bottom, visible only on screens <= 991px) -->
<div class="mobile-bottom-bar d-lg-none">
    @if(auth()->user()->user_type === 'bloger')
        <a href="{{ route('blogs.index') }}" class="mobile-tab-item {{ Request::routeIs('blogs.*') ? 'active' : '' }}">
            <i class="fa-solid fa-blog"></i>
            <span>Blogs</span>
        </a>
        <a href="#" class="mobile-tab-item" onclick="toggleMobileMoreDrawer(event)">
            <i class="fa-solid fa-ellipsis"></i>
            <span>More</span>
        </a>
    @else
        <a href="{{ route('dashboard') }}" class="mobile-tab-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <a href="#" class="mobile-tab-item {{ Request::routeIs('dashboard.business.*') || Request::routeIs('dashboard.jobs.*') ? 'active' : '' }}" onclick="toggleMobileBusinessDrawer(event)">
            <i class="fa-solid fa-briefcase"></i>
            <span>Business</span>
        </a>
        <a href="#" class="mobile-tab-item {{ Request::routeIs('matrimony.*') ? 'active' : '' }}" onclick="toggleMobileMatrimonyDrawer(event)">
            <i class="fa-solid fa-heart"></i>
            <span>Matrimony</span>
        </a>
        <a href="{{ route('blogs.index') }}" class="mobile-tab-item {{ Request::routeIs('blogs.*') ? 'active' : '' }}">
            <i class="fa-solid fa-blog"></i>
            <span>Blogs</span>
        </a>
        <a href="#" class="mobile-tab-item" onclick="toggleMobileMoreDrawer(event)">
            <i class="fa-solid fa-ellipsis"></i>
            <span>More</span>
        </a>
    @endif
</div>

<!-- Mobile Bottom Drawer Backdrop -->
<div class="mobile-drawer-backdrop" id="mobileDrawerBackdrop" onclick="closeAllMobileDrawers()"></div>

<!-- Mobile Business Drawer Menu -->
<div class="mobile-bottom-drawer" id="mobileBusinessDrawer">
    <div class="drawer-handle"></div>
    <div class="drawer-header">
        <h5 class="fw-bold mb-0 text-dark">Business Menu</h5>
        <button type="button" class="btn-close" onclick="closeAllMobileDrawers()"></button>
    </div>
    <div class="drawer-content">
        <ul class="drawer-menu-list">
            <li>
                <a href="{{ route('dashboard.business.create') }}" class="drawer-menu-item {{ Request::routeIs('dashboard.business.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-plus-circle text-primary"></i> Create Business
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard.business.browse') }}" class="drawer-menu-item {{ Request::routeIs('dashboard.business.browse') || Request::routeIs('dashboard.business.show') ? 'active' : '' }}">
                    <i class="fa-solid fa-magnifying-glass text-primary"></i> All Business
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard.jobs.applied') }}" class="drawer-menu-item {{ Request::routeIs('dashboard.jobs.applied') ? 'active' : '' }}">
                    <i class="fa-solid fa-briefcase text-primary"></i> All Jobs
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Mobile Matrimony Drawer Menu -->
<div class="mobile-bottom-drawer" id="mobileMatrimonyDrawer">
    <div class="drawer-handle"></div>
    <div class="drawer-header">
        <h5 class="fw-bold mb-0 text-dark">Matrimony Menu</h5>
        <button type="button" class="btn-close" onclick="closeAllMobileDrawers()"></button>
    </div>
    <div class="drawer-content">
        <ul class="drawer-menu-list">
            <li>
                <a href="{{ route('matrimony.create') }}" class="drawer-menu-item {{ Request::routeIs('matrimony.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-heart-circle-plus text-primary"></i> Create Profile
                </a>
            </li>
            <li>
                <a href="{{ route('matrimony.browse') }}" class="drawer-menu-item {{ Request::routeIs('matrimony.browse') ? 'active' : '' }}">
                    <i class="fa-solid fa-magnifying-glass text-primary"></i> Users Profile
                </a>
            </li>
            <li>
                <a href="{{ route('matrimony.requests') }}" class="drawer-menu-item {{ Request::routeIs('matrimony.requests') ? 'active' : '' }}">
                    <i class="fa-solid fa-paper-plane text-primary"></i> Request
                </a>
            </li>
            <li>
                <a href="{{ route('matrimony.conversations') }}" class="drawer-menu-item {{ Request::routeIs('matrimony.conversations') || Request::routeIs('matrimony.chat') ? 'active' : '' }}">
                    <i class="fa-solid fa-comments text-primary"></i> Messages
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Mobile Bottom Drawer Menu (More) -->
<div class="mobile-bottom-drawer" id="mobileMoreDrawer">
    <div class="drawer-handle"></div>
    <div class="drawer-header">
        <h5 class="fw-bold mb-0 text-dark">More Options</h5>
        <button type="button" class="btn-close" onclick="closeAllMobileDrawers()"></button>
    </div>
    <div class="drawer-content">
        <ul class="drawer-menu-list">
            @if(auth()->user()->user_type !== 'bloger')
                <li>
                    <a href="{{ route('subscriptions.index') }}" class="drawer-menu-item {{ Request::routeIs('subscriptions.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-credit-card text-primary"></i> My Subscription
                    </a>
                </li>
            @endif
            <li>
                <a href="{{ route('dashboard.privacy-policy') }}" class="drawer-menu-item {{ Request::routeIs('dashboard.privacy-policy') ? 'active' : '' }}">
                    <i class="fa-solid fa-shield-halved text-primary"></i> Privacy Policy
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard.terms-conditions') }}" class="drawer-menu-item {{ Request::routeIs('dashboard.terms-conditions') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-contract text-primary"></i> Terms & Conditions
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard.contact-support') }}" class="drawer-menu-item {{ Request::routeIs('dashboard.contact-support') ? 'active' : '' }}">
                    <i class="fa-solid fa-headset text-primary"></i> Contact Support
                </a>
            </li>
            <li class="border-top my-2 pt-2">
                <!-- Programmatically click on the desktop logout form's submit button to ensure full intercept and contribution features are fired on mobile too -->
                <a href="#" onclick="event.preventDefault(); document.querySelector('#logout-form button[type=submit]').click();" class="drawer-menu-item text-danger">
                    <i class="fa-solid fa-arrow-right-from-bracket text-danger"></i> Sign Out
                </a>
            </li>
        </ul>
    </div>
</div>
