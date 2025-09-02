<!-- Full-screen "More" panel -->
<div id="morePanel" class="more-panel" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="more-header">
        <button id="moreClose" class="more-close" aria-label="Close">✕</button>
        <h3>More</h3>
    </div>

    <div class="more-scroller">
        <!-- INVENTORY -->
        <div class="more-section is-inventory">
            <h4>Inventory <small>Inventory management</small></h4>
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
                <a href="inventory/add/" class="more-item more-inventory">
                    <i class="fi fi-rr-add"></i><span>Product</span>
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