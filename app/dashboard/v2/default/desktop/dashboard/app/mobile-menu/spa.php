<!-- Full-screen "More" panel (SPA Menu for Spa Management System) -->
<div id="morePanel" class="more-panel" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="more-header">
        <button id="moreClose" class="more-close" aria-label="Close">✕</button>
        <h3>More</h3>
    </div>

    <div class="more-scroller">

        <?php if ($site_access_type === 'administrator') { ?>
        <!-- =========================
         ADMIN SECTIONS
         ========================= -->

        <!-- MEMBERSHIP MANAGEMENT -->
        <div class="more-section is-membership">
            <h4>Membership <small>Plans & members</small></h4>
            <div class="more-row">
                <div class="more-grid">
                    <a href="membership/sell/" class="more-item spa-link" data-route="/membership/sell/">
                        <i class="fi fi-rr-receipt"></i><span>Sell Membership</span>
                    </a>
                    <a href="membership/plan/add/" class="more-item spa-link" data-route="/membership/plan/add/">
                        <i class="fi fi-rr-add"></i><span>New Plan</span>
                    </a>
                    <a href="membership/plan/" class="more-item spa-link" data-route="/membership/plan/">
                        <i class="fi fi-rr-category-alt"></i><span>Manage Plans</span>
                    </a>
                    <a href="membership/member/" class="more-item spa-link" data-route="/membership/member/">
                        <i class="fi fi-rr-users"></i><span>Members</span>
                    </a>
                    <a href="membership/renewals/" class="more-item spa-link" data-route="/membership/renewals/">
                        <i class="fi fi-rr-refresh"></i><span>Renewals</span>
                    </a>
                    <a href="membership/payments/" class="more-item spa-link" data-route="/membership/payments/">
                        <i class="fi fi-rr-credit-card"></i><span>Payments</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- THERAPISTS -->
        <div class="more-section is-therapist">
            <h4>Therapists <small>Manage spa staff</small></h4>
            <div class="more-row">
                <div class="more-grid">
                    <a href="therapist/add/" class="more-item spa-link" data-route="/therapist/add/">
                        <i class="fi fi-rr-user-add"></i><span>Add Therapist</span>
                    </a>
                    <a href="therapist/" class="more-item spa-link" data-route="/therapist/">
                        <i class="fi fi-rr-user"></i><span>All Therapists</span>
                    </a>
                    <a href="therapist/schedule/" class="more-item spa-link" data-route="/therapist/schedule/">
                        <i class="fi fi-rr-calendar"></i><span>Schedules</span>
                    </a>
                    <a href="therapist/availability/" class="more-item spa-link" data-route="/therapist/availability/">
                        <i class="fi fi-rr-clock"></i><span>Availability</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ROOMS & FACILITIES -->
        <div class="more-section is-rooms">
            <h4>Rooms & Facilities <small>Track spa rooms</small></h4>
            <div class="more-row">
                <div class="more-grid">
                    <a href="room/add/" class="more-item spa-link" data-route="/room/add/">
                        <i class="fi fi-rr-door-open"></i><span>Add Room</span>
                    </a>
                    <a href="room/" class="more-item spa-link" data-route="/room/">
                        <i class="fi fi-rr-bed"></i><span>Manage Rooms</span>
                    </a>
                    <a href="room/availability/" class="more-item spa-link" data-route="/room/availability/">
                        <i class="fi fi-rr-calendar"></i><span>Room Availability</span>
                    </a>
                    <a href="room/booking/" class="more-item spa-link" data-route="/room/booking/">
                        <i class="fi fi-rr-calendar-check"></i><span>Room Bookings</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- SERVICES -->
        <div class="more-section is-services">
            <h4>Services <small>Treatments & packages</small></h4>
            <div class="more-row">
                <div class="more-grid">
                    <a href="service/add/" class="more-item spa-link" data-route="/service/add/">
                        <i class="fi fi-rr-plus"></i><span>Add Service</span>
                    </a>
                    <a href="service/" class="more-item spa-link" data-route="/service/">
                        <i class="fi fi-rr-apps"></i><span>Manage Services</span>
                    </a>
                    <a href="service/packages/" class="more-item spa-link" data-route="/service/packages/">
                        <i class="fi fi-rr-gift"></i><span>Service Packages</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- REPORTING -->
        <div class="more-section is-reports">
            <h4>Reports <small>Analytics & performance</small></h4>
            <div class="more-row">
                <div class="more-grid">
                    <a href="reports/sales/" class="more-item spa-link" data-route="/reports/sales/">
                        <i class="fi fi-rr-chart-histogram"></i><span>Sales Report</span>
                    </a>
                    <a href="reports/memberships/" class="more-item spa-link" data-route="/reports/memberships/">
                        <i class="fi fi-rr-chart-pie-alt"></i><span>Membership Report</span>
                    </a>
                    <a href="reports/therapists/" class="more-item spa-link" data-route="/reports/therapists/">
                        <i class="fi fi-rr-chart-user"></i><span>Therapist Report</span>
                    </a>
                    <a href="reports/rooms/" class="more-item spa-link" data-route="/reports/rooms/">
                        <i class="fi fi-rr-chart-line-up"></i><span>Room Utilization</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- SETTINGS -->
        <div class="more-section is-settings">
            <h4>Settings <small>Configure your spa</small></h4>
            <div class="more-row">
                <div class="more-grid">
                    <a href="settings/general/" class="more-item spa-link" data-route="/settings/general/">
                        <i class="fi-rr-settings"></i><span>General Settings</span>
                    </a>
                    <a href="settings/users/" class="more-item spa-link" data-route="/settings/users/">
                        <i class="fi fi-rr-user"></i><span>Users</span>
                    </a>
                    <a href="settings/roles/" class="more-item spa-link" data-route="/settings/roles/">
                        <i class="fi fi-rr-lock"></i><span>Roles & Permissions</span>
                    </a>
                </div>
            </div>
        </div>

        <?php } else { ?>
        <!-- =========================
         RECEPTION SECTIONS
         ========================= -->

        <!-- MEMBERSHIP (Reception focus: Check-in) -->
        <div class="more-section is-membership">
            <h4>Membership <small>Quick actions</small></h4>
            <div class="more-row">
                <div class="more-grid">
                    <a href="membership/check-in/" class="more-item spa-link" data-route="/membership/check-in/">
                        <i class="fi fi-rr-entrance"></i><span>Check-in</span>
                    </a>
                    <a href="membership/member/" class="more-item spa-link" data-route="/membership/member/">
                        <i class="fi fi-rr-users"></i><span>Members</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ROOMS (Reception focus: Active rooms) -->
        <div class="more-section is-rooms">
            <h4>Rooms <small>Live status</small></h4>
            <div class="more-row">
                <div class="more-grid">
                    <a href="rooms/active/" class="more-item spa-link" data-route="/rooms/active/">
                        <i class="fi fi-rr-door-open"></i><span>Active Rooms</span>
                    </a>
                </div>
            </div>
        </div>

        <?php } ?>

    </div>

    <!-- Logout -->
    <div class="more-footer">
        <a href="logout/" class="logout-btn" aria-label="Log out">
            <i class="fi-rr-power"></i>
            <span>Log out</span>
        </a>
    </div>
</div>

<!-- Bottom Navigation -->
<nav class="bottom-nav" role="navigation" aria-label="Primary">
    <?php if ($site_access_type === 'administrator') { ?>
    <a href="dashboard/"
        class="bn-item spa-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard/') !== false) ? 'active' : ''; ?>"
        data-route="/dashboard/">
        <i class="fi-rr-home"></i><span>Home</span>
    </a>
    <a href="membership/"
        class="bn-item spa-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/membership/') !== false) ? 'active' : ''; ?>"
        data-route="/membership/">
        <i class="fi fi-rr-id-badge"></i><span>Membership</span>
    </a>
    <a href="room/"
        class="bn-item spa-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/room/') !== false) ? 'active' : ''; ?>"
        data-route="/room/">
        <i class="fi fi-rr-door-open"></i><span>Rooms</span>
    </a>
    <button type="button" class="bn-item bn-more" id="moreOpen" aria-haspopup="dialog" aria-controls="morePanel">
        <i class="fi-rr-apps"></i><span>More</span>
    </button>
    <?php } else { ?>
    <a href="dashboard/"
        class="bn-item spa-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard/') !== false) ? 'active' : ''; ?>"
        data-route="/dashboard/">
        <i class="fi-rr-home"></i><span>Home</span>
    </a>
    <a href="membership/check-in/"
        class="bn-item spa-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/membership/check-in/') !== false) ? 'active' : ''; ?>"
        data-route="/membership/check-in/">
        <i class="fi fi-rr-entrance"></i><span>Check-in</span>
    </a>
    <a href="rooms/active/"
        class="bn-item spa-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/rooms/active/') !== false) ? 'active' : ''; ?>"
        data-route="/rooms/active/">
        <i class="fi fi-rr-door-open"></i><span>Rooms</span>
    </a>
    <button type="button" class="bn-item bn-more" id="moreOpen" aria-haspopup="dialog" aria-controls="morePanel">
        <i class="fi-rr-apps"></i><span>More</span>
    </button>
    <?php } ?>
</nav>