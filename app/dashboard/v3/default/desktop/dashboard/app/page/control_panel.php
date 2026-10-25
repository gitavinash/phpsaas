<?php

/**
 * Blumox Control Panel V3 Main SPA Layout
 * Path: /app/dashboard/v3/default/desktop/dashboard/app/page/control_panel.php
 * Strict V3 Rule: External CSS and External JS only, no inline code
 */

$userRole = isset($site_access_type) ? strtolower(trim($site_access_type)) : 'administrator';
$roleTitles = [
    'administrator' => 'System Administrator',
    'partner' => 'Partner / Referral',
    'sales' => 'Sales Executive',
    'support' => 'Customer Support',
    'on_ground_sales' => 'On Ground Sales'
];
$displayRole = $roleTitles[$userRole] ?? 'Administrator';

?>

<div class="cp-wrapper" id="controlPanelApp" data-user-role="<?php echo htmlspecialchars($userRole); ?>">

    <!-- Top Executive Header -->
    <div class="cp-header-bar">
        <div class="cp-header-left">
            <div class="cp-header-icon">
                <i class="fi fi-rr-settings-sliders"></i>
            </div>
            <div class="cp-header-titles">
                <h1>Blumox Control Panel</h1>
                <p>
                    <span>Multi-tenant Cloud Infrastructure &amp; Business Engine</span>
                    <span class="cp-badge-role <?php echo htmlspecialchars($userRole === 'on_ground_sales' ? 'ground' : $userRole); ?>">
                        <i class="fi fi-rr-shield-check"></i> <?php echo htmlspecialchars($displayRole); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="cp-header-actions">
            <?php if ($userRole === 'administrator' || $userRole === 'sales' || $userRole === 'on_ground_sales') { ?>
                <button type="button" class="cp-btn-primary" id="btnHeaderProvisionSite">
                    <i class="fi fi-rr-add"></i> <span>Provision Site</span>
                </button>
            <?php } ?>

            <?php if ($userRole === 'administrator' || $userRole === 'sales') { ?>
                <button type="button" class="cp-btn-secondary" id="btnHeaderNewInvoice">
                    <i class="fi fi-rr-receipt"></i> <span>New Invoice</span>
                </button>
            <?php } ?>

            <button type="button" class="cp-btn-secondary" id="btnCpRefreshData" title="Refresh metrics">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="cp-kpi-grid">
        <div class="cp-kpi-card">
            <div class="cp-kpi-icon blue">
                <i class="fi fi-rr-apps"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Total Apps</p>
                <h3 id="cpStatApps">-</h3>
                <span id="cpStatAppsSub">Active in ecosystem</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon emerald">
                <i class="fi fi-rr-globe"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Client Sites</p>
                <h3 id="cpStatSites">-</h3>
                <span id="cpStatSitesSub">Provisioned instances</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon indigo">
                <i class="fi fi-rr-palette"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>App Themes</p>
                <h3 id="cpStatThemes">-</h3>
                <span id="cpStatThemesSub">UI design presets</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon purple">
                <i class="fi fi-rr-users"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Team &amp; Roles</p>
                <h3 id="cpStatUsers">-</h3>
                <span id="cpStatUsersSub">Across 5 user types</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon sky">
                <i class="fi fi-rr-receipt"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Billed Revenue</p>
                <h3 id="cpStatRevenue">&#8377;0.00</h3>
                <span id="cpStatRevenueSub">Paid client invoices</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon amber">
                <i class="fi fi-rr-hand-holding-usd"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Commissions</p>
                <h3 id="cpStatCommissions">&#8377;0.00</h3>
                <span id="cpStatCommissionsSub">Partner payouts balance</span>
            </div>
        </div>
    </div>

    <!-- Navigation Strip (Direct Links to Dedicated Pages) -->
    <div class="cp-nav-tabs" role="navigation">
        <a href="/dashboard/" class="cp-tab-btn active">
            <i class="fi fi-rr-dashboard"></i> <span>Overview</span>
        </a>

        <?php if ($userRole === 'administrator') { ?>
            <a href="/dashboard/apps/" class="cp-tab-btn">
                <i class="fi fi-rr-apps"></i> <span>Apps Hub</span>
                <span class="cp-tab-badge" id="cpTabBadgeApps">0</span>
            </a>

            <a href="/dashboard/themes/" class="cp-tab-btn">
                <i class="fi fi-rr-palette"></i> <span>Themes Matrix</span>
                <span class="cp-tab-badge" id="cpTabBadgeThemes">0</span>
            </a>
        <?php } ?>

        <?php if ($userRole !== 'partner') { ?>
            <a href="/dashboard/sites/" class="cp-tab-btn">
                <i class="fi fi-rr-globe"></i> <span>Client Sites</span>
                <span class="cp-tab-badge" id="cpTabBadgeSites">0</span>
            </a>
        <?php } ?>

        <?php if ($userRole === 'administrator' || $userRole === 'support') { ?>
            <a href="/dashboard/users/" class="cp-tab-btn">
                <i class="fi fi-rr-users-alt"></i> <span>Team &amp; Roles</span>
            </a>
        <?php } ?>

        <?php if ($userRole === 'administrator' || $userRole === 'sales' || $userRole === 'on_ground_sales') { ?>
            <a href="/dashboard/invoices/" class="cp-tab-btn">
                <i class="fi fi-rr-receipt"></i> <span>Invoices &amp; Billing</span>
            </a>
        <?php } ?>

        <?php if ($userRole === 'administrator' || $userRole === 'partner') { ?>
            <a href="/dashboard/commissions/" class="cp-tab-btn">
                <i class="fi fi-rr-hand-holding-usd"></i> <span>Partner Commissions</span>
            </a>
        <?php } ?>

        <?php if ($userRole === 'administrator' || $userRole === 'support') { ?>
            <a href="/dashboard/configurations/" class="cp-tab-btn" title="Open Dedicated Configurations Studio">
                <i class="fi fi-rr-settings-sliders"></i> <span>Configurations</span>
            </a>
        <?php } ?>
    </div>

    <!-- TAB 1: OVERVIEW -->
    <div class="cp-tab-content active" id="tabContentOverview">
        <div class="cp-card">
            <div class="cp-card-header">
                <div class="cp-card-title">
                    <h3>Recent Provisioned Client Sites</h3>
                    <p>Live instances across Blumox multi-tenant cluster</p>
                </div>
                <a href="/dashboard/sites/" class="cp-btn-secondary" id="btnOverviewViewAllSites">
                    <span>View All Sites</span> <i class="fi fi-rr-arrow-right"></i>
                </a>
            </div>
            <div class="cp-table-wrap">
                <table class="cp-table">
                    <thead>
                        <tr>
                            <th>Site / Brand</th>
                            <th>Domain / Endpoint</th>
                            <th>Attached Application</th>
                            <th>V3 Version</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cpOverviewSitesTbody">
                        <tr>
                            <td colspan="6" class="text-center">Loading recent deployments...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: APPS HUB -->
    <div class="cp-tab-content" id="tabContentApps">
        <div class="cp-card">
            <div class="cp-card-header">
                <div class="cp-card-title">
                    <h3>Blumox Application Catalog</h3>
                    <p>Core software modules and vertical products</p>
                </div>
                <button type="button" class="cp-btn-primary" id="btnOpenNewAppModal">
                    <i class="fi fi-rr-add"></i> <span>Register New App</span>
                </button>
            </div>

            <div class="cp-toolbar">
                <div class="cp-search-wrap">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" class="cp-search-input" id="cpSearchApps" placeholder="Search applications...">
                </div>
                <div class="cp-filters-wrap">
                    <select class="cp-select" id="cpFilterAppStatus">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="cp-grid-cards" id="cpAppsGrid">
                <!-- Rendered asynchronously by JS -->
            </div>
        </div>
    </div>

    <!-- TAB 3: THEMES MATRIX -->
    <div class="cp-tab-content" id="tabContentThemes">
        <div class="cp-card">
            <div class="cp-card-header">
                <div class="cp-card-title">
                    <h3>Theme Matrix</h3>
                    <p>Visual presentation designs linked to software applications</p>
                </div>
                <button type="button" class="cp-btn-primary" id="btnOpenNewThemeModal">
                    <i class="fi fi-rr-add"></i> <span>Register New Theme</span>
                </button>
            </div>

            <div class="cp-toolbar">
                <div class="cp-search-wrap">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" class="cp-search-input" id="cpSearchThemes" placeholder="Search themes...">
                </div>
                <div class="cp-filters-wrap">
                    <select class="cp-select" id="cpFilterThemeApp">
                        <option value="">All Applications</option>
                    </select>
                </div>
            </div>

            <div class="cp-grid-cards" id="cpThemesGrid">
                <!-- Rendered asynchronously by JS -->
            </div>
        </div>
    </div>

    <!-- TAB 4: CLIENT SITES -->
    <div class="cp-tab-content" id="tabContentSites">
        <div class="cp-card">
            <div class="cp-card-header">
                <div class="cp-card-title">
                    <h3>Client Websites &amp; Tenants</h3>
                    <p>Live instances, domains, and multi-tenant routing</p>
                </div>
                <button type="button" class="cp-btn-primary" id="btnOpenProvisionModal">
                    <i class="fi fi-rr-add"></i> <span>Provision Client Site</span>
                </button>
            </div>

            <div class="cp-toolbar">
                <div class="cp-search-wrap">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" class="cp-search-input" id="cpSearchSites" placeholder="Search domain, site name, or client email...">
                </div>
                <div class="cp-filters-wrap">
                    <select class="cp-select" id="cpFilterSiteApp">
                        <option value="">All Applications</option>
                    </select>
                    <select class="cp-select" id="cpFilterSiteStatus">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Suspended / Inactive</option>
                    </select>
                </div>
            </div>

            <div class="cp-table-wrap">
                <table class="cp-table" id="cpSitesTable">
                    <thead>
                        <tr>
                            <th>Site Name</th>
                            <th>Domain / URL</th>
                            <th>Application</th>
                            <th>Licenses &amp; Quotas</th>
                            <th>Owner / Client</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cpSitesTbody">
                        <!-- Rendered asynchronously by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 5: USERS & ROLES -->
    <div class="cp-tab-content" id="tabContentUsers">
        <div class="cp-card">
            <div class="cp-card-header">
                <div class="cp-card-title">
                    <h3>Users &amp; Role-Based Access Control</h3>
                    <p>Internal teams, sales force, support, and partner accounts</p>
                </div>
                <button type="button" class="cp-btn-primary" id="btnOpenNewUserModal">
                    <i class="fi fi-rr-user-add"></i> <span>Add New User</span>
                </button>
            </div>

            <div class="cp-toolbar">
                <div class="cp-search-wrap">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" class="cp-search-input" id="cpSearchUsers" placeholder="Search name, email, or mobile...">
                </div>
                <div class="cp-filters-wrap">
                    <select class="cp-select" id="cpFilterUserRole">
                        <option value="">All Roles</option>
                        <option value="administrator">System Administrator</option>
                        <option value="partner">Partner / Referral</option>
                        <option value="sales">Sales Executive</option>
                        <option value="support">Customer Support</option>
                        <option value="on_ground_sales">On Ground Sales</option>
                    </select>
                </div>
            </div>

            <div class="cp-table-wrap">
                <table class="cp-table" id="cpUsersTable">
                    <thead>
                        <tr>
                            <th>User / Name</th>
                            <th>Contact Email &amp; Mobile</th>
                            <th>System Role</th>
                            <th>Assigned Workspace</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cpUsersTbody">
                        <!-- Rendered asynchronously by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 6: INVOICES & BILLING -->
    <div class="cp-tab-content" id="tabContentInvoices">
        <div class="cp-card">
            <div class="cp-card-header">
                <div class="cp-card-title">
                    <h3>Invoices &amp; Subscription Billing</h3>
                    <p>Client SaaS subscriptions, setup fees, and payment tracking</p>
                </div>
                <button type="button" class="cp-btn-primary" id="btnOpenCreateInvoiceModal">
                    <i class="fi fi-rr-receipt"></i> <span>Create Invoice</span>
                </button>
            </div>

            <div class="cp-toolbar">
                <div class="cp-search-wrap">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" class="cp-search-input" id="cpSearchInvoices" placeholder="Search invoice #, domain, or client...">
                </div>
                <div class="cp-filters-wrap">
                    <select class="cp-select" id="cpFilterInvoiceStatus">
                        <option value="">All Status</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
            </div>

            <div class="cp-table-wrap">
                <table class="cp-table" id="cpInvoicesTable">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Client / Site</th>
                            <th>Plan / Service</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cpInvoicesTbody">
                        <!-- Rendered asynchronously by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 7: PARTNER COMMISSIONS -->
    <div class="cp-tab-content" id="tabContentCommissions">
        <div class="cp-card">
            <div class="cp-card-header">
                <div class="cp-card-title">
                    <h3>Partner Referral Commissions</h3>
                    <p>Earnings ledger, payout approvals, and reconciliation</p>
                </div>
                <?php if ($userRole === 'administrator') { ?>
                    <button type="button" class="cp-btn-primary" id="btnOpenManualCommissionModal">
                        <i class="fi fi-rr-add"></i> <span>Record Commission</span>
                    </button>
                <?php } ?>
            </div>

            <div class="cp-toolbar">
                <div class="cp-search-wrap">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" class="cp-search-input" id="cpSearchCommissions" placeholder="Search partner or referred site...">
                </div>
                <div class="cp-filters-wrap">
                    <select class="cp-select" id="cpFilterCommissionStatus">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </div>

            <div class="cp-table-wrap">
                <table class="cp-table" id="cpCommissionsTable">
                    <thead>
                        <tr>
                            <th>Partner / Referrer</th>
                            <th>Referred Site</th>
                            <th>Linked Invoice</th>
                            <th>Rate</th>
                            <th>Commission</th>
                            <th>Status</th>
                            <th>Payout Ref</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cpCommissionsTbody">
                        <!-- Rendered asynchronously by JS -->
                    </tbody>
                </table>
            </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODALS (SPA Popups) -->
<!-- ========================================================================= -->

<!-- Modal: Provision Client Site -->
<div class="cp-modal-backdrop" id="modalProvisionSite">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Provision New Client Site</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formProvisionSite">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="siteNameInput">Business / Site Name *</label>
                    <input type="text" class="cp-form-input" id="siteNameInput" required placeholder="e.g. Gracious World Tours">
                </div>
                <div class="cp-form-group">
                    <label for="siteDomainInput">Domain Name *</label>
                    <input type="text" class="cp-form-input" id="siteDomainInput" required placeholder="e.g. graciousworld.com or crm.domain.in">
                </div>
                <div class="cp-form-group">
                    <label for="siteAppSelect">Application Module *</label>
                    <select class="cp-form-select" id="siteAppSelect" required>
                        <option value="">Select Application</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="siteThemeSelect">Initial Theme Preset</label>
                    <select class="cp-form-select" id="siteThemeSelect">
                        <option value="0">Default System Theme</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="siteOwnerEmailInput">Client Owner Email *</label>
                    <input type="email" class="cp-form-input" id="siteOwnerEmailInput" required placeholder="client@company.com">
                </div>
                <div class="cp-form-group">
                    <label for="sitePartnerSelect">Referring Partner (Optional)</label>
                    <select class="cp-form-select" id="sitePartnerSelect">
                        <option value="0">No Partner / Direct</option>
                    </select>
                </div>
                <div class="cp-meter-group">
                    <div class="cp-form-group">
                        <label for="provUserLimitInput">Team Members Limit</label>
                        <input type="number" class="cp-form-input" id="provUserLimitInput" value="10" min="1">
                    </div>
                    <div class="cp-form-group">
                        <label for="provWaLimitInput">WhatsApp Devices Limit</label>
                        <input type="number" class="cp-form-input" id="provWaLimitInput" value="1" min="1">
                    </div>
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">
                    <i class="fi fi-rr-rocket"></i> <span>Provision Now</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Register App -->
<div class="cp-modal-backdrop" id="modalNewApp">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Register Application</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formNewApp">
            <input type="hidden" id="appIdInput" value="0">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="appTitleInput">Application Title *</label>
                    <input type="text" class="cp-form-input" id="appTitleInput" required placeholder="e.g. Flight Booking Portal">
                </div>
                <div class="cp-form-group">
                    <label for="appPathInput">App Path Slug (Unique) *</label>
                    <input type="text" class="cp-form-input" id="appPathInput" required placeholder="e.g. flight_booking">
                </div>
                <div class="cp-form-group">
                    <label for="appDescInput">Description</label>
                    <textarea class="cp-form-textarea" id="appDescInput" rows="2" placeholder="Brief intro to the application..."></textarea>
                </div>
                <div class="cp-form-group">
                    <label for="appColorInput">Primary Brand Color</label>
                    <input type="text" class="cp-form-input" id="appColorInput" value="#2563eb" placeholder="#2563eb">
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Save Application</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Register Theme -->
<div class="cp-modal-backdrop" id="modalNewTheme">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Register New Theme</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formNewTheme">
            <input type="hidden" id="themeIdInput" value="0">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="themeAppSelect">Parent Application *</label>
                    <select class="cp-form-select" id="themeAppSelect" required>
                        <option value="">Select App</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="themeTitleInput">Theme Title *</label>
                    <input type="text" class="cp-form-input" id="themeTitleInput" required placeholder="e.g. Modern Dark Travel">
                </div>
                <div class="cp-form-group">
                    <label for="themePathInput">Theme Directory Path *</label>
                    <input type="text" class="cp-form-input" id="themePathInput" required placeholder="e.g. modern_dark">
                </div>
                <div class="cp-form-group">
                    <label for="themeVersionInput">Version</label>
                    <input type="text" class="cp-form-input" id="themeVersionInput" value="1.0" placeholder="1.0">
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Save Theme</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add User -->
<div class="cp-modal-backdrop" id="modalNewUser">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Add New User &amp; Assign Role</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formNewUser">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="userEmailInput">Email Address *</label>
                    <input type="email" class="cp-form-input" id="userEmailInput" required placeholder="user@blumox.com">
                </div>
                <div class="cp-form-group">
                    <label for="userDisplayNameInput">Display / Full Name</label>
                    <input type="text" class="cp-form-input" id="userDisplayNameInput" placeholder="e.g. John Doe">
                </div>
                <div class="cp-form-group">
                    <label for="userMobileInput">Mobile Number</label>
                    <input type="text" class="cp-form-input" id="userMobileInput" placeholder="+91 9876543210">
                </div>
                <div class="cp-form-group">
                    <label for="userRoleSelect">Role &amp; Permissions *</label>
                    <select class="cp-form-select" id="userRoleSelect" required>
                        <option value="administrator">System Administrator</option>
                        <option value="partner">Partner / Referral</option>
                        <option value="sales">Sales Executive</option>
                        <option value="support">Customer Support</option>
                        <option value="on_ground_sales">On Ground Sales</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="userPasswordInput">Initial Password</label>
                    <input type="password" class="cp-form-input" id="userPasswordInput" value="Blumox@2026">
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Create User Account</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Create Invoice -->
<div class="cp-modal-backdrop" id="modalCreateInvoice">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Create Client Invoice</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formCreateInvoice">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="invSiteSelect">Client Site *</label>
                    <select class="cp-form-select" id="invSiteSelect" required>
                        <option value="">Select Client Site</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="invPlanInput">Plan / Service Title *</label>
                    <input type="text" class="cp-form-input" id="invPlanInput" required value="Blumox SaaS Annual Subscription">
                </div>
                <div class="cp-form-group">
                    <label for="invAmountInput">Subtotal Amount (INR) *</label>
                    <input type="number" step="0.01" class="cp-form-input" id="invAmountInput" required placeholder="e.g. 24999.00">
                </div>
                <div class="cp-form-group">
                    <label for="invTaxInput">Tax / GST (INR)</label>
                    <input type="number" step="0.01" class="cp-form-input" id="invTaxInput" value="0.00">
                </div>
                <div class="cp-form-group">
                    <label for="invDueDateInput">Payment Due Date</label>
                    <input type="date" class="cp-form-input" id="invDueDateInput">
                </div>
                <div class="cp-form-group">
                    <label for="invPartnerSelect">Credited Partner (For referral commission)</label>
                    <select class="cp-form-select" id="invPartnerSelect">
                        <option value="0">Direct / No Partner</option>
                    </select>
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Generate Invoice</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: View / Print Invoice -->
<div class="cp-modal-backdrop" id="modalViewInvoice">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3 id="invModalTitle">Invoice Details</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <div class="cp-invoice-sheet" id="invoiceSheetContent">
            <!-- Rendered by JS -->
        </div>
        <div class="cp-modal-footer">
            <button type="button" class="cp-btn-secondary" data-close-modal>Close</button>
            <button type="button" class="cp-btn-primary" id="btnInvoicePrint">
                <i class="fi fi-rr-print"></i> <span>Print Invoice</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal: Add Configuration -->
<div class="cp-modal-backdrop" id="modalNewConfig">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Add / Update Configuration</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formNewConfig">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="cfgKeyInput">Configuration Key *</label>
                    <input type="text" class="cp-form-input" id="cfgKeyInput" required placeholder="e.g. AWS_S3_BUCKET or SMTP_HOST">
                </div>
                <div class="cp-form-group">
                    <label for="cfgValueInput">Configuration Value</label>
                    <textarea class="cp-form-textarea" id="cfgValueInput" rows="3" placeholder="Enter configuration value..."></textarea>
                </div>
                <div class="cp-form-group">
                    <label for="cfgScopeSelect">Scope *</label>
                    <select class="cp-form-select" id="cfgScopeSelect">
                        <option value="1">Global (All Sites)</option>
                        <option value="0">Site-Specific (Current Control Panel)</option>
                    </select>
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Save Configuration</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Manage Site Licenses & Feature Toggles -->
<div class="cp-modal-backdrop" id="modalSiteLicenses">
    <div class="cp-modal-box large">
        <div class="cp-modal-header">
            <div>
                <h3 id="licModalSiteName">Manage Site Licenses &amp; Features</h3>
                <p class="cp-site-sub" id="licModalSiteDomain">domain.com</p>
            </div>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formSiteLicenses">
            <input type="hidden" id="licSiteIdInput" value="0">
            <div class="cp-modal-body">
                <!-- Live Usage Meter Cards -->
                <div class="cp-meter-group">
                    <div class="cp-meter-card">
                        <div class="cp-meter-head">
                            <span><i class="fi fi-rr-users"></i> Team Members</span>
                            <strong id="licMeterUsersText">0 / 0</strong>
                        </div>
                        <div class="cp-meter-bar">
                            <div class="cp-meter-fill" id="licMeterUsersFill"></div>
                        </div>
                    </div>
                    <div class="cp-meter-card">
                        <div class="cp-meter-head">
                            <span><i class="fi fi-brands-whatsapp"></i> WhatsApp Devices</span>
                            <strong id="licMeterWaText">0 / 0</strong>
                        </div>
                        <div class="cp-meter-bar">
                            <div class="cp-meter-fill" id="licMeterWaFill"></div>
                        </div>
                    </div>
                </div>

                <!-- Quota Inputs with Quick Preset Pills -->
                <div class="cp-meter-group">
                    <div class="cp-form-group cp-input-with-presets">
                        <label for="licUserLimitInput">Team Members License Limit *</label>
                        <input type="number" class="cp-form-input" id="licUserLimitInput" required min="1" value="10">
                        <div class="cp-preset-pills">
                            <button type="button" class="cp-preset-pill btn-set-user-preset" data-val="5">5</button>
                            <button type="button" class="cp-preset-pill btn-set-user-preset" data-val="10">10</button>
                            <button type="button" class="cp-preset-pill btn-set-user-preset" data-val="25">25</button>
                            <button type="button" class="cp-preset-pill btn-set-user-preset" data-val="50">50</button>
                            <button type="button" class="cp-preset-pill btn-set-user-preset" data-val="999">Unlimited</button>
                        </div>
                    </div>

                    <div class="cp-form-group cp-input-with-presets">
                        <label for="licWaLimitInput">WhatsApp Devices Limit *</label>
                        <input type="number" class="cp-form-input" id="licWaLimitInput" required min="1" value="1">
                        <div class="cp-preset-pills">
                            <button type="button" class="cp-preset-pill btn-set-wa-preset" data-val="1">1</button>
                            <button type="button" class="cp-preset-pill btn-set-wa-preset" data-val="3">3</button>
                            <button type="button" class="cp-preset-pill btn-set-wa-preset" data-val="5">5</button>
                            <button type="button" class="cp-preset-pill btn-set-wa-preset" data-val="10">10</button>
                        </div>
                    </div>
                </div>

                <!-- Feature Modules Toggles Grid -->
                <div class="cp-form-group">
                    <label>Active Modules &amp; Feature Capabilities</label>
                    <div class="cp-switches-grid">
                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>WhatsApp Web &amp; Automation</h5>
                                <p>Multi-device sync, chat inbox, automated workflows</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="swWhatsapp" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>CRM &amp; Leads Management</h5>
                                <p>Lead capture, assignments, activity timelines</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="swLeads" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>Billing &amp; Invoices Ledger</h5>
                                <p>Client accounts, payment links, invoicing</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="swAccounts" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>Transporters &amp; Fleet</h5>
                                <p>Vehicles catalog, driver tariffs, quotes</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="swTransporters" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>Tour Packages &amp; Itineraries</h5>
                                <p>Package builder, hotels, itinerary engine</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="swPackages" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>Custom Domain White-label</h5>
                                <p>Custom branding, custom domain routing, SSL</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="swCustomDomain" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>Developer API &amp; Webhooks</h5>
                                <p>REST API token generation, webhook web dispatch</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="swApiAccess">
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Plan, Expiry & Operational Status -->
                <div class="cp-meter-group">
                    <div class="cp-form-group">
                        <label for="licPlanSelect">Subscription Plan Tier</label>
                        <select class="cp-form-select" id="licPlanSelect">
                            <option value="Professional SaaS">Professional SaaS</option>
                            <option value="Enterprise Suite">Enterprise Suite</option>
                            <option value="Growth Plan">Growth Plan</option>
                            <option value="Starter Launch">Starter Launch</option>
                            <option value="Custom Dedicated">Custom Dedicated</option>
                        </select>
                    </div>

                    <div class="cp-form-group">
                        <label for="licExpiryInput">Subscription Expiry Date</label>
                        <input type="date" class="cp-form-input" id="licExpiryInput">
                    </div>
                </div>

                <div class="cp-form-group">
                    <label for="licSiteStatusSelect">Operational Status</label>
                    <select class="cp-form-select" id="licSiteStatusSelect">
                        <option value="1">Active / Live</option>
                        <option value="0">Suspended / Deactivated</option>
                    </select>
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">
                    <i class="fi fi-rr-disk"></i> <span>Save Licenses &amp; Features</span>
                </button>
            </div>
        </form>
    </div>
</div>
