<!-- Full-screen "More" panel -->
<div id="morePanel" class="more-panel" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="more-header">
        <button id="moreClose" class="more-close" aria-label="Close">✕</button>
        <h3>More</h3>
    </div>

    <div class="more-scroller">
        <!-- LEADS -->
        <div class="more-section is-leads">
            <h4>Leads <small>Lead management</small></h4>
            <div class="more-grid">
                <a href="leads/" class="more-item more-leads">
                    <i class="fi fi-rr-chart-network"></i><span>Leads</span>
                </a>
                <a href="leads/list/" class="more-item more-leads">
                    <i class="fi-rr-apps-sort"></i><span>All leads</span>
                </a>
                <a href="leads/add/" class="more-item more-leads">
                    <i class="fi fi-rr-add"></i><span>Add lead</span>
                </a>
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

        <!-- SALES -->
        <div class="more-section is-sales">
            <h4>Sales <small>Sales operations</small></h4>
            <div class="more-grid">
                <a href="sales/" class="more-item more-sales">
                    <i class="fi fi-rr-calculator-bill"></i><span>Sales</span>
                </a>
                <a href="sales/list/" class="more-item more-sales">
                    <i class="fi-rr-apps-sort"></i><span>All sales</span>
                </a>
                <a href="sales/add/" class="more-item more-sales">
                    <i class="fi fi-rr-add"></i><span>Add sale</span>
                </a>
                <a href="sales_request/" class="more-item more-sales-req">
                    <i class="fi fi-rr-memo-circle-check"></i><span>Sales requests</span>
                </a>
                <a href="sales_request/list/" class="more-item more-sales-req">
                    <i class="fi fi-rr-apps-sort"></i><span>All requests</span>
                </a>
                <a href="return_request/" class="more-item more-return">
                    <i class="fi fi-rr-undo"></i><span>Return requests</span>
                </a>
                <a href="return_request/list/" class="more-item more-return">
                    <i class="fi-rr-apps-sort"></i><span>All returns</span>
                </a>
            </div>
        </div>

        <!-- INVENTORY -->
        <div class="more-section is-inventory">
            <h4>Inventory <small>Inventory management</small></h4>
            <div class="more-grid">
                <a href="inventory/" class="more-item more-inventory">
                    <i class="fi fi-rr-inventory-alt"></i><span>Inventory</span>
                </a>
                <a href="inventory/list/" class="more-item more-inventory">
                    <i class="fi-rr-apps-sort"></i><span>All blocks</span>
                </a>
                <a href="inventory/add/" class="more-item more-inventory">
                    <i class="fi fi-rr-add"></i><span>Add block</span>
                </a>
                <a href="restock/" class="more-item more-restock">
                    <i class="fi fi-rr-undo"></i><span>Restock</span>
                </a>
                <a href="restock/list/" class="more-item more-restock">
                    <i class="fi fi-rr-apps-sort"></i><span>All restocks</span>
                </a>
            </div>
        </div>

        <!-- BUSINESS HUB -->
        <div class="more-section is-business">
            <h4>Business Hub <small>Control your business</small></h4>
            <div class="more-grid">
                <a href="company/" class="more-item more-company">
                    <i class="fi fi-rr-building"></i><span>Company</span>
                </a>
                <a href="company/list/" class="more-item more-company">
                    <i class="fi-rr-apps-sort"></i><span>All companies</span>
                </a>
                <a href="company/add/" class="more-item more-company">
                    <i class="fi fi-rr-add"></i><span>Add company</span>
                </a>

                <a href="warehouse/" class="more-item more-warehouse">
                    <i class="fi fi-rr-warehouse-alt"></i><span>Warehouse</span>
                </a>
                <a href="warehouse/list/" class="more-item more-warehouse">
                    <i class="fi-rr-apps-sort"></i><span>All warehouses</span>
                </a>
                <a href="warehouse/add/" class="more-item more-warehouse">
                    <i class="fi fi-rr-add"></i><span>Add warehouse</span>
                </a>

                <a href="supplier/" class="more-item more-supplier">
                    <i class="fi fi-rr-shipping-fast"></i><span>Supplier</span>
                </a>
                <a href="supplier/list/" class="more-item more-supplier">
                    <i class="fi-rr-apps-sort"></i><span>All suppliers</span>
                </a>
                <a href="supplier/add/" class="more-item more-supplier">
                    <i class="fi fi-rr-add"></i><span>Add supplier</span>
                </a>

                <a href="measurement/" class="more-item more-measure">
                    <i class="fi fi-rr-measuring-tape"></i><span>Measurement</span>
                </a>
                <a href="measurement/list/" class="more-item more-measure">
                    <i class="fi-rr-apps-sort"></i><span>All measurements</span>
                </a>
                <a href="measurement/add/" class="more-item more-measure">
                    <i class="fi fi-rr-add"></i><span>Add measurement</span>
                </a>
            </div>
        </div>

        <!-- SETTINGS + USERS -->
        <div class="more-section is-account">
            <h4>Account <small>Profile & access</small></h4>
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