<!-- Full-screen "More" panel -->
<div id="morePanel" class="more-panel" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="more-header">
        <button id="moreClose" class="more-close" aria-label="Close">✕</button>
        <h3>More</h3>
    </div>

    <div class="more-scroller">

        <!-- =========================
         INVENTORY (admin/superwiser/both)
         ========================= -->
        <?php if ($site_access_type == 'administrator' || $site_access_type == 'superwiser' || $site_access_type == 'both') { ?>
        <div class="more-section is-inventory">
            <h4>Inventory <small>Inventory management</small></h4>

            <!-- Row: Products -->
            <div class="more-row">
                <div class="more-grid">
                    <a href="inventory/" class="more-item more-inventory">
                        <i class="fi fi-rr-inventory-alt"></i><span>Inventory</span>
                    </a>
                    <a href="inventory/list/" class="more-item more-inventory">
                        <i class="fi-rr-apps-sort"></i><span>All Inventory</span>
                    </a>
                    <a href="inventory/add/" class="more-item more-inventory">
                        <i class="fi fi-rr-add"></i><span>Add Product</span>
                    </a>
                    <a href="inventory/category/" class="more-item more-inventory">
                        <i class="fi fi-rr-category-alt"></i><span>Product Category</span>
                    </a>
                </div>
            </div>

            <!-- Row: Restock -->
            <div class="more-row">
                <div class="more-grid">
                    <a href="restock/" class="more-item more-restock">
                        <i class="fi fi-rr-undo"></i><span>Restock</span>
                    </a>
                    <a href="restock/list/" class="more-item more-restock">
                        <i class="fi fi-rr-apps-sort"></i><span>All requests</span>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- =========================
         VENDORS (admin/operations)
         ========================= -->
        <?php if ($site_access_type == 'administrator' || $site_access_type == 'operations') { ?>
        <div class="more-section is-vendors">
            <h4>Vendors <small>Manage vendor</small></h4>

            <!-- Row: Transporter vendor -->
            <div class="more-row">
                <div class="more-grid">
                    <a href="vendors/transporter/" class="more-item more-vendors">
                        <i class="fi fi-rr-bus"></i><span>Transporter vendor</span>
                    </a>
                    <a href="vendors/transporter/list/" class="more-item more-vendors">
                        <i class="fi-rr-apps-sort"></i><span>All transporters</span>
                    </a>
                    <a href="vendors/transporter/add/" class="more-item more-vendors">
                        <i class="fi fi-rr-add"></i><span>Add new transporter</span>
                    </a>
                    <a href="vendors/transporter/vehicle/" class="more-item more-vendors">
                        <i class="fi fi-rr-car-bus"></i><span>Manage vehicles</span>
                    </a>
                </div>
            </div>

            <!-- Row: Property vendors -->
            <div class="more-row">
                <div class="more-grid">
                    <a href="vendors/property/" class="more-item more-vendors">
                        <i class="fi fi-rr-city"></i><span>Property Vendors</span>
                    </a>
                    <a href="vendors/property/list/" class="more-item more-vendors">
                        <i class="fi-rr-apps-sort"></i><span>All properties</span>
                    </a>
                    <a href="vendors/property/add/" class="more-item more-vendors">
                        <i class="fi fi-rr-add"></i><span>Add new property</span>
                    </a>
                    <a href="vendors/property/amenities/" class="more-item more-vendors">
                        <i class="fi fi-rr-star"></i><span>Manage amenities</span>
                    </a>
                    <a href="vendors/property/room/" class="more-item more-vendors">
                        <i class="fi fi-rr-door-open"></i><span>Manage room types</span>
                    </a>
                    <a href="vendors/property/upload/" class="more-item more-vendors">
                        <i class="fi fi-rr-file-upload"></i><span>Upload properties</span>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- =========================
         TOUR PACKAGES (admin/operations)
         ========================= -->
        <?php if ($site_access_type == 'administrator' || $site_access_type == 'operations') { ?>
        <div class="more-section is-tour">
            <h4>Tour Packages <small>Manage all tour packages</small></h4>

            <!-- Row: Domestic -->
            <div class="more-row">
                <div class="more-grid">
                    <a href="packages/domestic/" class="more-item more-tour">
                        <i class="fi fi-rr-suitcase-alt"></i><span>Domestic packages</span>
                    </a>
                    <a href="packages/domestic/list/" class="more-item more-tour">
                        <i class="fi-rr-apps-sort"></i><span>All packages</span>
                    </a>
                    <a href="packages/domestic/add/" class="more-item more-tour">
                        <i class="fi fi-rr-add"></i><span>Add new package</span>
                    </a>
                    <a href="packages/domestic/location/" class="more-item more-tour">
                        <i class="fi fi-rr-marker"></i><span>Destinations</span>
                    </a>
                </div>
            </div>

            <!-- Row: International -->
            <div class="more-row">
                <div class="more-grid">
                    <a href="packages/international/" class="more-item more-tour">
                        <i class="fi fi-rr-earth-asia"></i><span>International packages</span>
                    </a>
                    <a href="packages/international/list/" class="more-item more-tour">
                        <i class="fi-rr-apps-sort"></i><span>All packages</span>
                    </a>
                    <a href="packages/international/add/" class="more-item more-tour">
                        <i class="fi fi-rr-add"></i><span>Add new package</span>
                    </a>
                    <a href="packages/international/location/" class="more-item more-tour">
                        <i class="fi fi-rr-marker"></i><span>Destinations</span>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- =========================
         LEADS (all roles)
         ========================= -->
        <div class="more-section is-leads">
            <h4>Leads <small>Manage leads</small></h4>

            <!-- Row: Core actions -->
            <div class="more-row">
                <div class="more-grid">
                    <a href="leads/" class="more-item more-leads">
                        <i class="fi fi-rr-chart-network"></i><span>Leads</span>
                    </a>
                    <a href="leads/list/" class="more-item more-leads">
                        <i class="fi-rr-apps-sort"></i><span>All leads</span>
                    </a>
                    <a href="leads/add/" class="more-item more-leads">
                        <i class="fi fi-rr-add"></i><span>Add new lead</span>
                    </a>
                    <a href="/dashboard/whatsapp/" class="more-item more-whatsapp" id="v3MobileNavWhatsapp">
                        <i class="ri-whatsapp-fill"></i><span>WhatsApp Inbox</span>
                    </a>
                </div>
            </div>

            <!-- Row: Status filters -->
            <div class="more-row">
                <div class="more-grid">
                    <a href="leads/list/?leadStatus=1" class="more-item more-leads">
                        <i class="fi-rr-check"></i><span>Confirmed</span>
                    </a>
                    <a href="leads/list/?leadStatus=2" class="more-item more-leads">
                        <i class="fi-rr-hourglass-end"></i><span>Pending</span>
                    </a>
                    <a href="leads/list/?leadStatus=3" class="more-item more-leads">
                        <i class="fi-rr-comment"></i><span>Follow ups</span>
                    </a>
                    <a href="leads/list/?leadStatus=4" class="more-item more-leads">
                        <i class="fi-rr-cross-circle"></i><span>Cancelled</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- =========================
         REPORTS (admin)
         ========================= -->
        <?php if ($site_access_type == 'administrator') { ?>
        <div class="more-section is-reports">
            <h4>Reports <small>Analytics & performance</small></h4>

            <div class="more-row">
                <div class="more-grid">
                    <a href="report/" class="more-item more-reports">
                        <i class="fi fi-rr-chart-histogram"></i><span>Reports</span>
                    </a>
                    <a href="report/employees/" class="more-item more-reports">
                        <i class="fi-rr-employee-man-alt"></i><span>Employees performance</span>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- =========================
         INTEGRATIONS (admin)
         ========================= -->
        <?php if ($site_access_type == 'administrator') { ?>
        <div class="more-section is-integration">
            <h4>Integration <small>Manage third party resources</small></h4>
            <div class="more-row">
                <div class="more-grid">
                    <a href="integration/" class="more-item more-integration">
                        <i class="fi fi-rr-plug-connection"></i><span>Plugins</span>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- =========================
         BUSINESS HUB (admin)
         ========================= -->
        <?php if ($site_access_type == 'administrator') { ?>
        <div class="more-section is-business">
            <h4>Business Hub <small>Control Your Business</small></h4>

            <!-- Row: Policies -->
            <div class="more-row">
                <div class="more-grid">
                    <a href="policy/" class="more-item more-policy">
                        <i class="fi fi-rr-memo"></i><span>Policies</span>
                    </a>
                    <a href="policy/list/" class="more-item more-policy">
                        <i class="fi-rr-apps-sort"></i><span>All Policies</span>
                    </a>
                    <a href="policy/add/" class="more-item more-policy">
                        <i class="fi fi-rr-add"></i><span>Add new policy</span>
                    </a>
                </div>
            </div>

            <!-- Row: Payments -->
            <div class="more-row">
                <div class="more-grid">
                    <a href="payment/" class="more-item more-payment">
                        <i class="fi fi-rr-indian-rupee-sign"></i><span>Payments</span>
                    </a>
                    <a href="payment/list/" class="more-item more-payment">
                        <i class="fi-rr-apps-sort"></i><span>All methods</span>
                    </a>
                    <a href="payment/add/" class="more-item more-payment">
                        <i class="fi fi-rr-add"></i><span>Add new method</span>
                    </a>
                </div>
            </div>

            <!-- Row: Company -->
            <div class="more-row">
                <div class="more-grid">
                    <a href="company/" class="more-item more-company">
                        <i class="fi fi-rr-building"></i><span>Company</span>
                    </a>
                    <a href="company/list/" class="more-item more-company">
                        <i class="fi-rr-apps-sort"></i><span>All companies</span>
                    </a>
                    <a href="company/add/" class="more-item more-company">
                        <i class="fi fi-rr-add"></i><span>Add new company</span>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- =========================
         ACCOUNT (all roles)
         ========================= -->
        <div class="more-section is-account">
            <h4>Account <small>Profile & access</small></h4>

            <!-- Row: Settings + Users combined (compact) -->
            <div class="more-row">
                <div class="more-grid">
                    <a href="setting/" class="more-item more-setting">
                        <i class="fi-rr-settings"></i><span>Setting</span>
                    </a>
                    <a href="setting/configuration/" class="more-item more-setting">
                        <i class="fi-rr-settings"></i><span>Configuration</span>
                    </a>
                    <a href="setting/password/" class="more-item more-setting">
                        <i class="fi-rr-key"></i><span>Change password</span>
                    </a>
                    <a href="setting/profile/" class="more-item more-setting">
                        <i class="fi-rr-portrait"></i><span>Profile</span>
                    </a>

                    <a href="user/" class="more-item more-users">
                        <i class="fi fi-rr-user"></i><span>Users</span>
                    </a>
                    <a href="user/list/" class="more-item more-users">
                        <i class="fi-rr-apps-sort"></i><span>All users</span>
                    </a>
                    <a href="user/add/" class="more-item more-users">
                        <i class="fi fi-rr-user-add"></i><span>Add user</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout at the very end (NOT sticky) -->
    <div class="more-footer">
        <a href="/logout/?redirect_url=<?php echo ACTIVE_URL . (($site_access_type == 'customer') ? '&authby=mobile' : '') ?>"
            class="logout-btn" aria-label="Log out">
            <i class="fi-rr-power"></i>
            <span>Log out</span>
        </a>
    </div>
</div>

<!-- Bottom Navigation (mobile only) -->
<nav class="bottom-nav" role="navigation" aria-label="Primary">
    <a href="/dashboard/" class="bn-item <?php echo (ADMIN_BASE_URL == '/') ? 'active' : ''; ?>">
        <i class="fi-rr-home"></i><span>Home</span>
    </a>
    <a href="inventory/"
        class="bn-item <?php echo (strpos(ADMIN_BASE_URL, '/inventory/') !== false) ? 'active' : ''; ?>">
        <i class="fi fi-rr-inventory-alt"></i><span>Inventory</span>
    </a>
    <a href="sales/" class="bn-item <?php echo (strpos(ADMIN_BASE_URL, '/sales/') !== false) ? 'active' : ''; ?>">
        <i class="fi fi-rr-calculator-bill"></i><span>Sales</span>
    </a>
    <button type="button" class="bn-item bn-more" id="moreOpen" aria-haspopup="dialog" aria-controls="morePanel">
        <i class="fi-rr-apps"></i><span>More</span>
    </button>
</nav>