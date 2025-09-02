<!-- Full-screen "More" panel -->
<div id="morePanel" class="more-panel" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="more-header">
        <button id="moreClose" class="more-close" aria-label="Close">✕</button>
        <h3>More</h3>
    </div>

    <div class="more-scroller">
        <?php
        /* =========================================
       LEADS + SALES (admin or sales_executive)
       ========================================= */
        if ($site_access_type == 'administrator' || $site_access_type == 'sales_executive') { ?>
        <!-- Leads -->
        <div class="more-section is-leads">
            <h4>Leads <small>Lead management</small></h4>

            <div class="more-sub">
                <h5 class="more-subtitle">
                    <i class="fi fi-rr-chart-network" aria-hidden="true"></i>
                    Actions
                </h5>
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
                </div>
            </div>

            <div class="more-sub">
                <h5 class="more-subtitle">
                    <i class="fi fi-rr-list-check" aria-hidden="true"></i>
                    Status
                </h5>
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

        <!-- Sales -->
        <div class="more-section is-sales">
            <h4>Sales <small>Sales operations</small></h4>
            <div class="more-sub">
                <h5 class="more-subtitle">
                    <i class="fi fi-rr-calculator-bill" aria-hidden="true"></i>
                    Core
                </h5>
                <div class="more-grid">
                    <a href="sales/" class="more-item more-sales">
                        <i class="fi fi-rr-calculator-bill"></i><span>Sales</span>
                    </a>
                    <a href="sales/list/" class="more-item more-sales">
                        <i class="fi-rr-apps-sort"></i><span>All sales</span>
                    </a>
                    <a href="sales/add/" class="more-item more-sales">
                        <i class="fi fi-rr-add"></i><span>Add new sale</span>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php
        /* =========================================
       SALES MANAGEMENT (admin / accounts / both)
       ========================================= */
        if ($site_access_type == 'administrator' || $site_access_type == 'accounts' || $site_access_type == 'both') { ?>
        <div class="more-section is-sales-ops">
            <h4>Sales Management <small>Requests & returns</small></h4>

            <div class="more-sub">
                <h5 class="more-subtitle">
                    <i class="fi fi-rr-memo-circle-check" aria-hidden="true"></i>
                    Sales requests
                </h5>
                <div class="more-grid">
                    <a href="sales_request/" class="more-item more-sales-req">
                        <i class="fi fi-rr-memo-circle-check"></i><span>Sales requests</span>
                    </a>
                    <a href="sales_request/list/" class="more-item more-sales-req">
                        <i class="fi fi-rr-apps-sort"></i><span>All requests</span>
                    </a>
                </div>
            </div>

            <div class="more-sub">
                <h5 class="more-subtitle">
                    <i class="fi fi-rr-undo" aria-hidden="true"></i>
                    Returns
                </h5>
                <div class="more-grid">
                    <a href="return_request/" class="more-item more-return">
                        <i class="fi fi-rr-undo"></i><span>Return requests</span>
                    </a>
                    <a href="return_request/list/" class="more-item more-return">
                        <i class="fi-rr-apps-sort"></i><span>All requests</span>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php
        /* =========================================
       INVENTORY (admin / superwiser / both)
       ========================================= */
        if ($site_access_type == 'administrator' || $site_access_type == 'superwiser' || $site_access_type == 'both') { ?>
        <div class="more-section is-inventory">
            <h4>Inventory <small>Inventory management</small></h4>

            <div class="more-sub">
                <h5 class="more-subtitle">
                    <i class="fi fi-rr-inventory-alt" aria-hidden="true"></i>
                    Products
                </h5>
                <div class="more-grid">
                    <a href="inventory/" class="more-item more-inventory">
                        <i class="fi fi-rr-inventory-alt"></i><span>Inventory</span>
                    </a>
                    <a href="inventory/list/" class="more-item more-inventory">
                        <i class="fi-rr-apps-sort"></i><span>All blocks</span>
                    </a>
                    <a href="inventory/add/" class="more-item more-inventory">
                        <i class="fi fi-rr-add"></i><span>Add new block</span>
                    </a>
                </div>
            </div>

            <div class="more-sub">
                <h5 class="more-subtitle">
                    <i class="fi fi-rr-undo" aria-hidden="true"></i>
                    Restock
                </h5>
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

        <?php
        /* =========================================
       BUSINESS HUB (admin)
       ========================================= */
        if ($site_access_type == 'administrator') { ?>
        <div class="more-section is-business">
            <h4>Business Hub <small>Control Your Business</small></h4>

            <div class="more-sub">
                <h5 class="more-subtitle">
                    <i class="fi fi-rr-building" aria-hidden="true"></i>
                    Company
                </h5>
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

            <div class="more-sub">
                <h5 class="more-subtitle">
                    <i class="fi fi-rr-warehouse-alt" aria-hidden="true"></i>
                    Warehouse
                </h5>
                <div class="more-grid">
                    <a href="warehouse/" class="more-item more-warehouse">
                        <i class="fi fi-rr-warehouse-alt"></i><span>Warehouse</span>
                    </a>
                    <a href="warehouse/list/" class="more-item more-warehouse">
                        <i class="fi-rr-apps-sort"></i><span>All warehouses</span>
                    </a>
                    <a href="warehouse/add/" class="more-item more-warehouse">
                        <i class="fi fi-rr-add"></i><span>Add new warehouse</span>
                    </a>
                </div>
            </div>

            <div class="more-sub">
                <h5 class="more-subtitle">
                    <i class="fi fi-rr-shipping-fast" aria-hidden="true"></i>
                    Supplier
                </h5>
                <div class="more-grid">
                    <a href="supplier/" class="more-item more-supplier">
                        <i class="fi fi-rr-shipping-fast"></i><span>Supplier</span>
                    </a>
                    <a href="supplier/list/" class="more-item more-supplier">
                        <i class="fi-rr-apps-sort"></i><span>All suppliers</span>
                    </a>
                    <a href="supplier/add/" class="more-item more-supplier">
                        <i class="fi fi-rr-add"></i><span>Add new supplier</span>
                    </a>
                </div>
            </div>

            <div class="more-sub">
                <h5 class="more-subtitle">
                    <i class="fi fi-rr-measuring-tape" aria-hidden="true"></i>
                    Measurement
                </h5>
                <div class="more-grid">
                    <a href="measurement/" class="more-item more-measure">
                        <i class="fi fi-rr-measuring-tape"></i><span>Measurement</span>
                    </a>
                    <a href="measurement/list/" class="more-item more-measure">
                        <i class="fi-rr-apps-sort"></i><span>All measurements</span>
                    </a>
                    <a href="measurement/add/" class="more-item more-measure">
                        <i class="fi fi-rr-add"></i><span>Add new measurement</span>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>
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