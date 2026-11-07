<?php

/**
 * Blumox Control Panel: Dedicated Client Sites Page
 * Path: /app/dashboard/v3/default/desktop/sites.php
 * Route: /dashboard/sites/
 * Strict V3 Rule: External CSS and External JS only, zero inline code
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

<div class="cp-wrapper cp-page-dedicated" id="controlPanelApp" data-page="sites" data-user-role="<?php echo htmlspecialchars($userRole); ?>">

    <!-- Top Executive Header -->
    <div class="cp-header-bar">
        <div class="cp-header-left">
            <div class="cp-header-icon">
                <i class="fi fi-rr-globe"></i>
            </div>
            <div class="cp-header-titles">
                <h1>Client Websites &amp; Multi-Tenant Instances</h1>
                <p>
                    <span>Multi-Tenant Provisioning, Domain Routing, License Quotas &amp; Renewal Lifecycle</span>
                    <span class="cp-badge-role <?php echo htmlspecialchars($userRole === 'on_ground_sales' ? 'ground' : $userRole); ?>">
                        <i class="fi fi-rr-shield-check"></i> <?php echo htmlspecialchars($displayRole); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="cp-header-actions">
            <?php if ($userRole === 'administrator' || $userRole === 'sales' || $userRole === 'on_ground_sales') { ?>
                <button type="button" class="cp-btn-primary" id="btnOpenProvisionModal">
                    <i class="fi fi-rr-add"></i> <span>Provision Client Site</span>
                </button>
            <?php } ?>
            <button type="button" class="cp-btn-secondary" id="btnCpRefreshData" title="Refresh Sites Catalog">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Executive KPI Metric Counters -->
    <div class="cp-kpi-grid">
        <div class="cp-kpi-card">
            <div class="cp-kpi-icon blue">
                <i class="fi fi-rr-globe"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Total Sites</p>
                <h3 id="cpSitesKpiTotal">--</h3>
                <span>Provisioned tenants</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon emerald">
                <i class="fi fi-rr-badge-check"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Active Live</p>
                <h3 id="cpSitesKpiActive">--</h3>
                <span class="cp-kpi-live"><i class="fi fi-rr-bullet"></i> Operational</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon amber">
                <i class="fi fi-rr-clock"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Expiring Soon</p>
                <h3 id="cpSitesKpiExpiring">--</h3>
                <span>Next 30 days renewal</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon rose">
                <i class="fi fi-rr-ban"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Suspended</p>
                <h3 id="cpSitesKpiSuspended">--</h3>
                <span>Locked or inactive</span>
            </div>
        </div>
    </div>

    <!-- Client Sites Main Card -->
    <div class="cp-card">
        <div class="cp-card-header">
            <div class="cp-card-title">
                <h3>Live Client Instances</h3>
                <p>Provisioned tenants across Blumox infrastructure cluster</p>
            </div>

            <!-- Quick Segmented Status Tabs -->
            <div class="cp-filter-tabs" id="cpSiteStatusTabs">
                <button type="button" class="cp-filter-pill active" data-filter-status="">
                    <span>All Sites</span>
                    <span class="cp-pill-count" id="cpBadgeAllSites">--</span>
                </button>
                <button type="button" class="cp-filter-pill" data-filter-status="1">
                    <i class="fi fi-rr-check"></i>
                    <span>Active Only</span>
                    <span class="cp-pill-count" id="cpBadgeActiveSites">--</span>
                </button>
                <button type="button" class="cp-filter-pill" data-filter-status="expiring">
                    <i class="fi fi-rr-clock"></i>
                    <span>Expiring Soon</span>
                    <span class="cp-pill-count" id="cpBadgeExpiringSites">--</span>
                </button>
                <button type="button" class="cp-filter-pill" data-filter-status="0">
                    <i class="fi fi-rr-ban"></i>
                    <span>Suspended</span>
                    <span class="cp-pill-count" id="cpBadgeSuspendedSites">--</span>
                </button>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="cp-toolbar">
            <div class="cp-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" class="cp-search-input" id="cpSearchSites" placeholder="Search domain, site name, or client email...">
                <button type="button" class="cp-search-clear-btn" id="cpClearSitesSearch" title="Clear search">
                    <i class="fi fi-rr-cross-small"></i>
                </button>
            </div>
            <div class="cp-filters-wrap">
                <select class="cp-select v3-form-select selectJS2 cp-select2" id="cpFilterSiteApp" data-placeholder="All Applications" data-width="170px">
                    <option value="">All Applications</option>
                </select>
                <select class="cp-select v3-form-select selectJS2 cp-select2" id="cpFilterSitePlan" data-placeholder="All Plans" data-width="160px">
                    <option value="">All Subscription Plans</option>
                    <option value="Professional SaaS">Professional SaaS</option>
                    <option value="Enterprise Suite">Enterprise Suite</option>
                    <option value="Growth Plan">Growth Plan</option>
                    <option value="Starter Launch">Starter Launch</option>
                    <option value="Custom Dedicated">Custom Dedicated</option>
                </select>
                <select class="cp-select v3-form-select selectJS2 cp-select2" id="cpFilterSiteStatus" data-placeholder="All Status" data-width="145px">
                    <option value="">All Statuses</option>
                    <option value="1">Active / Live</option>
                    <option value="0">Suspended</option>
                    <option value="expiring">Expiring Soon</option>
                </select>
                <select class="cp-select v3-form-select selectJS2 cp-select2" id="cpSortSites" data-placeholder="Sort Sites" data-width="165px">
                    <option value="newest">Newest Provisioned</option>
                    <option value="oldest">Oldest First</option>
                    <option value="expiry_asc">Expiry Date (Soonest)</option>
                    <option value="name_asc">Site Name (A-Z)</option>
                    <option value="domain_asc">Domain (A-Z)</option>
                </select>
            </div>
        </div>

        <!-- Sites Table -->
        <div class="cp-table-wrap">
            <table class="cp-table package-v3-datatable" id="cpSitesTable">
                <thead>
                    <tr>
                        <th>Site &amp; Domain</th>
                        <th>Owner Details</th>
                        <th>App</th>
                        <th>Active Date</th>
                        <th>Expiry Date</th>
                        <th>Licence Details</th>
                        <th class="text-center">Status</th>
                        <th class="no-sort text-right cp-actions-header">Actions</th>
                    </tr>
                </thead>
                <tbody id="cpSitesTbody">
                    <tr>
                        <td colspan="8" class="text-center cp-loading-cell">
                            <i class="fi fi-rr-spinner-alt cp-spin"></i> Loading client sites catalog...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer / Pagination -->
        <div class="cp-table-footer" id="cpSitesPaginationBar">
            <div class="cp-pagination-info">
                Showing <span id="cpSitesPageStart">0</span> to <span id="cpSitesPageEnd">0</span> of <span id="cpSitesTotalCount">0</span> client sites
            </div>
            <div class="cp-pagination-controls" id="cpSitesPaginationControls">
                <!-- Dynamically populated -->
            </div>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- Modal 1: Send Subscription Renewal Reminder                              -->
<!-- ========================================================================= -->
<div class="cp-modal-backdrop" id="modalRenewalReminder">
    <div class="cp-modal-box large">
        <div class="cp-modal-header">
            <div class="cp-modal-header-titles">
                <h3>Send Subscription Renewal Reminder</h3>
                <p id="remModalSubTitle">Notify client via WhatsApp and Email</p>
            </div>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formRenewalReminder">
            <input type="hidden" id="remSiteIdInput" value="0">
            <div class="cp-modal-body">

                <!-- Site Summary Banner -->
                <div class="cp-site-rem-summary">
                    <div class="cp-rem-sum-grid">
                        <div class="cp-rem-sum-item">
                            <span class="cp-rem-label">Site Name &amp; Domain</span>
                            <strong id="remSumSiteName">--</strong>
                            <span class="cp-rem-sub" id="remSumDomain">--</span>
                        </div>
                        <div class="cp-rem-sum-item">
                            <span class="cp-rem-label">Client Contact</span>
                            <strong id="remSumClientName">--</strong>
                            <span class="cp-rem-sub" id="remSumContacts">--</span>
                        </div>
                        <div class="cp-rem-sum-item">
                            <span class="cp-rem-label">Current Plan</span>
                            <span class="cp-badge-plan" id="remSumPlan">--</span>
                        </div>
                        <div class="cp-rem-sum-item">
                            <span class="cp-rem-label">Subscription Expiry</span>
                            <strong id="remSumExpiryDate">--</strong>
                            <span class="cp-expiry-pill" id="remSumExpiryBadge">--</span>
                        </div>
                        <div class="cp-rem-sum-item">
                            <span class="cp-rem-label">Renewal Rate</span>
                            <strong id="remSumPrice">--</strong>
                            <span class="cp-rem-sub" id="remSumInterval">--</span>
                        </div>
                    </div>
                    <div class="cp-rem-history-bar" id="remSumHistory">
                        <i class="fi fi-rr-info"></i>
                        <span id="remHistoryText">No reminders sent yet for this billing cycle.</span>
                    </div>
                </div>

                <!-- Live Client Renewal Price Override -->
                <div class="cp-pricing-override-compact">
                    <div class="cp-pricing-compact-head">
                        <label class="cp-form-label">
                            <i class="fi fi-rr-hand-holding-usd"></i> <span>Override Client Renewal Price (Optional)</span>
                        </label>
                        <span class="cp-override-hint">Customize renewal amount quoted in this reminder</span>
                    </div>
                    <div class="cp-form-row">
                        <div class="cp-form-group flex-1">
                            <div class="cp-input-affix-wrap">
                                <span class="cp-input-prefix" id="remCurrencyPrefix">₹</span>
                                <input type="number" step="0.01" min="0" class="cp-form-input cp-affix-input" id="remCustomPriceInput" placeholder="Renewal price">
                                <span class="cp-input-suffix" id="remPriceSuffix">/ year</span>
                            </div>
                        </div>
                        <div class="cp-form-group flex-1">
                            <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="remBillingIntervalSelect" data-placeholder="Billing Cycle" data-width="100%">
                                <option value="yearly">Yearly Renewal</option>
                                <option value="monthly">Monthly Renewal</option>
                                <option value="quarterly">Quarterly Renewal</option>
                                <option value="lifetime">Lifetime Upgrade</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Quick Expiry Extension Options -->
                <div class="cp-form-group">
                    <label class="cp-form-label">
                        <span>Quick Extend Expiry Before Sending (Optional)</span>
                    </label>
                    <div class="cp-preset-pills">
                        <button type="button" class="cp-preset-pill btn-quick-extend-expiry" data-months="1">+1 Month</button>
                        <button type="button" class="cp-preset-pill btn-quick-extend-expiry" data-months="3">+3 Months</button>
                        <button type="button" class="cp-preset-pill btn-quick-extend-expiry" data-months="6">+6 Months</button>
                        <button type="button" class="cp-preset-pill btn-quick-extend-expiry" data-months="12">+1 Year</button>
                        <button type="button" class="cp-preset-pill btn-quick-extend-expiry" data-months="lifetime">Lifetime</button>
                    </div>
                </div>

                <!-- Delivery Channels Selection -->
                <div class="cp-form-group">
                    <label class="cp-form-label">Delivery Communication Channels *</label>
                    <div class="cp-channel-cards-row">
                        <label class="cp-channel-card" id="lblChannelWhatsApp">
                            <input type="checkbox" id="chkChannelWhatsApp" checked>
                            <div class="cp-channel-icon emerald">
                                <i class="fi fi-brands-whatsapp"></i>
                            </div>
                            <div class="cp-channel-info">
                                <strong>WhatsApp Notification</strong>
                                <span id="remWaRecipientText">To: Client Mobile</span>
                            </div>
                        </label>

                        <label class="cp-channel-card" id="lblChannelEmail">
                            <input type="checkbox" id="chkChannelEmail" checked>
                            <div class="cp-channel-icon blue">
                                <i class="fi fi-rr-envelope"></i>
                            </div>
                            <div class="cp-channel-info">
                                <strong>Email Notification</strong>
                                <span id="remEmailRecipientText">To: Client Email</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Editable Message Composer -->
                <div class="cp-form-group">
                    <div class="cp-form-label-row">
                        <label for="remCustomMessageInput">Reminder Message Body *</label>
                        <button type="button" class="cp-btn-text" id="btnResetReminderTemplate">
                            <i class="fi fi-rr-refresh"></i> Reset Template
                        </button>
                    </div>
                    <textarea class="cp-form-textarea" id="remCustomMessageInput" rows="7" required placeholder="Type the renewal reminder message..."></textarea>
                    <span class="cp-form-hint">Variables like client name, domain, plan, and expiry date are automatically filled.</span>
                </div>

            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary" id="btnSubmitRenewalReminder">
                    <i class="fi fi-rr-paper-plane"></i> <span>Send Renewal Reminder</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal 2: Manage Site Settings, Licenses & Configurations                 -->
<!-- ========================================================================= -->
<div class="cp-modal-backdrop" id="modalSiteSettingsConfig">
    <div class="cp-modal-box xl-box">
        <div class="cp-modal-header">
            <div class="cp-modal-header-titles">
                <h3 id="mscSiteTitle">Manage Site Settings &amp; Configuration</h3>
                <p id="mscSiteDomainSub">Configure identity, licenses, feature flags and tenant overrides</p>
            </div>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>

        <!-- Modal Nav Tabs -->
        <div class="cp-modal-nav-tabs" id="siteModalNavTabs">
            <button type="button" class="cp-modal-nav-tab active" data-pane="tabPaneGeneral">
                <i class="fi fi-rr-browser"></i> <span>General &amp; Identity</span>
            </button>
            <button type="button" class="cp-modal-nav-tab" data-pane="tabPaneLicenses">
                <i class="fi fi-rr-key"></i> <span>Licenses &amp; Subscriptions</span>
            </button>
            <button type="button" class="cp-modal-nav-tab" data-pane="tabPaneFeatures">
                <i class="fi fi-rr-toggle-on"></i> <span>Feature Modules</span>
            </button>
            <button type="button" class="cp-modal-nav-tab" data-pane="tabPaneParameters">
                <i class="fi fi-rr-settings-sliders"></i> <span>Site Parameters</span>
            </button>
        </div>

        <form id="formSiteSettingsConfig">
            <input type="hidden" id="mscSiteIdInput" value="0">

            <div class="cp-modal-body">

                <!-- ----------------------------------------------------------- -->
                <!-- PANE 1: General & Identity                                  -->
                <!-- ----------------------------------------------------------- -->
                <div class="cp-modal-pane active" id="tabPaneGeneral">
                    <div class="cp-form-row">
                        <div class="cp-form-group flex-1">
                            <label for="mscSiteNameInput">Business / Site Name *</label>
                            <input type="text" class="cp-form-input" id="mscSiteNameInput" required placeholder="e.g. Gracious World Tours">
                        </div>
                        <div class="cp-form-group flex-1">
                            <label for="mscSiteDomainInput">Domain Name</label>
                            <input type="text" class="cp-form-input readonly-input" id="mscSiteDomainInput" readonly title="Domain routing is fixed">
                        </div>
                    </div>

                    <div class="cp-form-row">
                        <div class="cp-form-group flex-1">
                            <label for="mscSiteUrlInput">Full Site URL</label>
                            <input type="text" class="cp-form-input" id="mscSiteUrlInput" placeholder="http://domain.com">
                        </div>
                        <div class="cp-form-group flex-1">
                            <label for="mscSiteDashVersionSelect">Dashboard Version</label>
                            <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="mscSiteDashVersionSelect" data-placeholder="Dashboard Version" data-width="100%">
                                <option value="3">V3 (Modern SPA Framework)</option>
                                <option value="2">V2 (Classic Dashboard)</option>
                            </select>
                        </div>
                    </div>

                    <div class="cp-form-row">
                        <div class="cp-form-group flex-1">
                            <label for="mscSiteAppSelect">Application Module</label>
                            <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="mscSiteAppSelect" data-placeholder="Select Application" data-width="100%">
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                        <div class="cp-form-group flex-1">
                            <label for="mscSiteThemeSelect">Active Theme Preset</label>
                            <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="mscSiteThemeSelect" data-placeholder="Select Theme" data-width="100%">
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                    </div>

                    <div class="cp-form-group">
                        <label for="mscSiteThemeColorInput">Primary Brand Theme Color</label>
                        <div class="cp-color-picker-row">
                            <input type="color" class="cp-color-input" id="mscSiteThemeColorInput" value="#2563eb">
                            <input type="text" class="cp-form-input cp-hex-input" id="mscSiteThemeColorHex" value="#2563eb" placeholder="#2563eb">
                            <div class="cp-color-presets">
                                <button type="button" class="cp-color-swatch btn-pick-color" data-color="#2563eb" title="Royal Blue"></button>
                                <button type="button" class="cp-color-swatch btn-pick-color" data-color="#059669" title="Emerald Green"></button>
                                <button type="button" class="cp-color-swatch btn-pick-color" data-color="#7c3aed" title="Purple"></button>
                                <button type="button" class="cp-color-swatch btn-pick-color" data-color="#ea580c" title="Orange"></button>
                                <button type="button" class="cp-color-swatch btn-pick-color" data-color="#0891b2" title="Teal"></button>
                                <button type="button" class="cp-color-swatch btn-pick-color" data-color="#dc2626" title="Ruby Red"></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ----------------------------------------------------------- -->
                <!-- PANE 2: Licenses & Subscriptions                            -->
                <!-- ----------------------------------------------------------- -->
                <div class="cp-modal-pane" id="tabPaneLicenses">
                    <!-- Live Usage Meter Cards -->
                    <div class="cp-meter-group">
                        <div class="cp-meter-card">
                            <div class="cp-meter-head">
                                <span><i class="fi fi-rr-users"></i> Team Members</span>
                                <strong id="mscMeterUsersText">0 / 0</strong>
                            </div>
                            <div class="cp-meter-bar">
                                <div class="cp-meter-fill" id="mscMeterUsersFill"></div>
                            </div>
                        </div>
                        <div class="cp-meter-card">
                            <div class="cp-meter-head">
                                <span><i class="fi fi-brands-whatsapp"></i> WhatsApp Devices</span>
                                <strong id="mscMeterWaText">0 / 0</strong>
                            </div>
                            <div class="cp-meter-bar">
                                <div class="cp-meter-fill" id="mscMeterWaFill"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 1: License Quota Inputs with Quick Preset Pills -->
                    <div class="cp-form-row">
                        <div class="cp-form-group cp-input-with-presets">
                            <label for="mscUserLimitInput">Team Members License Limit *</label>
                            <input type="number" class="cp-form-input" id="mscUserLimitInput" required min="1" value="10">
                            <div class="cp-preset-pills">
                                <button type="button" class="cp-preset-pill btn-set-msc-user-preset" data-val="5">5</button>
                                <button type="button" class="cp-preset-pill btn-set-msc-user-preset" data-val="10">10</button>
                                <button type="button" class="cp-preset-pill btn-set-msc-user-preset" data-val="25">25</button>
                                <button type="button" class="cp-preset-pill btn-set-msc-user-preset" data-val="50">50</button>
                                <button type="button" class="cp-preset-pill btn-set-msc-user-preset" data-val="999">Unlimited</button>
                            </div>
                        </div>

                        <div class="cp-form-group cp-input-with-presets">
                            <label for="mscWaLimitInput">WhatsApp Devices Limit *</label>
                            <input type="number" class="cp-form-input" id="mscWaLimitInput" required min="1" value="1">
                            <div class="cp-preset-pills">
                                <button type="button" class="cp-preset-pill btn-set-msc-wa-preset" data-val="1">1</button>
                                <button type="button" class="cp-preset-pill btn-set-msc-wa-preset" data-val="3">3</button>
                                <button type="button" class="cp-preset-pill btn-set-msc-wa-preset" data-val="5">5</button>
                                <button type="button" class="cp-preset-pill btn-set-msc-wa-preset" data-val="10">10</button>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Storage Quota & Expiry Date (Both with matched preset pills) -->
                    <div class="cp-form-row">
                        <div class="cp-form-group cp-input-with-presets">
                            <label for="mscStorageLimitInput">Storage Quota (MB)</label>
                            <input type="number" class="cp-form-input" id="mscStorageLimitInput" value="5120" min="100">
                            <div class="cp-preset-pills">
                                <button type="button" class="cp-preset-pill btn-set-msc-storage-preset" data-val="1024">1 GB</button>
                                <button type="button" class="cp-preset-pill btn-set-msc-storage-preset" data-val="2048">2 GB</button>
                                <button type="button" class="cp-preset-pill btn-set-msc-storage-preset" data-val="5120">5 GB</button>
                                <button type="button" class="cp-preset-pill btn-set-msc-storage-preset" data-val="10240">10 GB</button>
                                <button type="button" class="cp-preset-pill btn-set-msc-storage-preset" data-val="25600">25 GB</button>
                            </div>
                        </div>

                        <div class="cp-form-group cp-input-with-presets">
                            <label for="mscExpiryInput">Subscription Expiry Date</label>
                            <input type="date" class="cp-form-input" id="mscExpiryInput">
                            <div class="cp-preset-pills">
                                <button type="button" class="cp-preset-pill btn-msc-extend-date" data-months="1">+1M</button>
                                <button type="button" class="cp-preset-pill btn-msc-extend-date" data-months="3">+3M</button>
                                <button type="button" class="cp-preset-pill btn-msc-extend-date" data-months="6">+6M</button>
                                <button type="button" class="cp-preset-pill btn-msc-extend-date" data-months="12">+1Y</button>
                                <button type="button" class="cp-preset-pill btn-msc-extend-date" data-months="lifetime">Lifetime</button>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Plan Tier & Operational Status (Both selectJS2 dropdowns) -->
                    <div class="cp-form-row">
                        <div class="cp-form-group flex-1">
                            <label for="mscPlanSelect">Subscription Plan Tier</label>
                            <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="mscPlanSelect" data-placeholder="Select Plan Tier" data-width="100%">
                                <option value="Professional SaaS">Professional SaaS</option>
                                <option value="Enterprise Suite">Enterprise Suite</option>
                                <option value="Growth Plan">Growth Plan</option>
                                <option value="Starter Launch">Starter Launch</option>
                                <option value="Custom Dedicated">Custom Dedicated</option>
                            </select>
                        </div>

                        <div class="cp-form-group flex-1">
                            <label for="mscSiteStatusSelect">Operational Status</label>
                            <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="mscSiteStatusSelect" data-placeholder="Select Status" data-width="100%">
                                <option value="1">Active / Live (Accessible)</option>
                                <option value="0">Suspended / Deactivated (Blocked)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Subscription Pricing & Client Renewal Override Card -->
                    <div class="cp-pricing-override-card">
                        <div class="cp-pricing-override-header">
                            <div>
                                <h4 class="cp-pricing-card-title">
                                    <i class="fi fi-rr-tags"></i> <span>Subscription Pricing &amp; Renewal Overrides</span>
                                </h4>
                                <p class="cp-pricing-card-sub">Configure custom monthly and yearly renewal pricing for this client site</p>
                            </div>
                            <div class="cp-switch-wrap">
                                <label class="cp-toggle" title="Enable price override for this tenant">
                                    <input type="checkbox" id="mscPriceOverrideSwitch" checked>
                                    <span class="cp-toggle-slider"></span>
                                </label>
                                <span class="cp-switch-text">Enable Price Override</span>
                            </div>
                        </div>

                        <div class="cp-form-row">
                            <div class="cp-form-group flex-1">
                                <label for="mscPriceMonthlyInput">Monthly Subscription Price</label>
                                <div class="cp-input-affix-wrap">
                                    <span class="cp-input-prefix" id="mscCurrencyPrefixMonthly">₹</span>
                                    <input type="number" step="0.01" min="0" class="cp-form-input cp-affix-input" id="mscPriceMonthlyInput" placeholder="0.00">
                                    <span class="cp-input-suffix">/ month</span>
                                </div>
                            </div>
                            <div class="cp-form-group flex-1">
                                <label for="mscPriceYearlyInput">Yearly Subscription Price</label>
                                <div class="cp-input-affix-wrap">
                                    <span class="cp-input-prefix" id="mscCurrencyPrefixYearly">₹</span>
                                    <input type="number" step="0.01" min="0" class="cp-form-input cp-affix-input" id="mscPriceYearlyInput" placeholder="0.00">
                                    <span class="cp-input-suffix">/ year</span>
                                </div>
                            </div>
                        </div>

                        <div class="cp-form-row">
                            <div class="cp-form-group flex-1">
                                <label for="mscBillingIntervalSelect">Active Billing Cycle</label>
                                <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="mscBillingIntervalSelect" data-placeholder="Billing Cycle" data-width="100%">
                                    <option value="yearly">Yearly (Annual Renewal)</option>
                                    <option value="monthly">Monthly (Recurring Monthly)</option>
                                    <option value="quarterly">Quarterly (Every 3 Months)</option>
                                    <option value="lifetime">One-time / Lifetime</option>
                                </select>
                            </div>
                            <div class="cp-form-group flex-1">
                                <label for="mscCurrencySelect">Billing Currency</label>
                                <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="mscCurrencySelect" data-placeholder="Currency" data-width="100%">
                                    <option value="INR">INR (₹) - Indian Rupee</option>
                                    <option value="USD">USD ($) - US Dollar</option>
                                    <option value="EUR">EUR (€) - Euro</option>
                                    <option value="GBP">GBP (£) - British Pound</option>
                                    <option value="AED">AED (د.إ) - UAE Dirham</option>
                                </select>
                            </div>
                        </div>

                        <div class="cp-form-group">
                            <label for="mscRenewalNotesInput">Special Renewal Terms / Price Agreement Notes</label>
                            <input type="text" class="cp-form-input" id="mscRenewalNotesInput" placeholder="e.g. Special partnership renewal discount locked until Dec 2028">
                        </div>
                    </div>
                </div>

                <!-- ----------------------------------------------------------- -->
                <!-- PANE 3: Feature Modules                                     -->
                <!-- ----------------------------------------------------------- -->
                <div class="cp-modal-pane" id="tabPaneFeatures">
                    <p class="cp-pane-intro">Enable or disable core application modules and functional features for this site.</p>
                    <div class="cp-switches-grid">
                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>WhatsApp Web &amp; Automation</h5>
                                <p>Multi-device sync, chat inbox, automated workflows</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="mscSwWhatsapp" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>CRM &amp; Leads Management</h5>
                                <p>Lead capture, assignments, activity timelines</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="mscSwLeads" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>Billing &amp; Invoices Ledger</h5>
                                <p>Client accounts, payment links, invoicing</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="mscSwAccounts" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>Transporters &amp; Fleet</h5>
                                <p>Vehicles catalog, driver tariffs, quotes</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="mscSwTransporters" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>Tour Packages &amp; Itineraries</h5>
                                <p>Package builder, hotels, itinerary engine</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="mscSwPackages" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>Custom Domain White-label</h5>
                                <p>Custom branding, custom domain routing, SSL</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="mscSwCustomDomain" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cp-switch-card">
                            <div class="cp-switch-info">
                                <h5>Developer API &amp; Webhooks</h5>
                                <p>REST API token generation, webhook dispatch</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="mscSwApiAccess">
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ----------------------------------------------------------- -->
                <!-- PANE 4: Site Parameters (isw_site_configuration)            -->
                <!-- ----------------------------------------------------------- -->
                <div class="cp-modal-pane" id="tabPaneParameters">
                    <div class="cp-params-toolbar">
                        <div class="cp-search-wrap small-search">
                            <i class="fi fi-rr-search"></i>
                            <input type="text" class="cp-search-input" id="mscSearchSiteParams" placeholder="Filter configuration keys...">
                        </div>
                        <button type="button" class="cp-btn-secondary" id="btnOpenAddParamRow">
                            <i class="fi fi-rr-plus"></i> Add Override Parameter
                        </button>
                    </div>

                    <!-- Inline New Parameter Form -->
                    <div class="cp-new-param-row" id="mscNewParamRow">
                        <div class="cp-form-row">
                            <div class="cp-form-group flex-1">
                                <input type="text" class="cp-form-input" id="newParamKeyInput" placeholder="Parameter Key (e.g. app_tax_rate)">
                            </div>
                            <div class="cp-form-group flex-1">
                                <input type="text" class="cp-form-input" id="newParamValueInput" placeholder="Parameter Value (e.g. 18.0)">
                            </div>
                            <div class="cp-param-actions">
                                <button type="button" class="cp-btn-primary" id="btnSaveNewParam">
                                    <i class="fi fi-rr-disk"></i> Save
                                </button>
                                <button type="button" class="cp-btn-secondary" id="btnCancelNewParam">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Live Parameter Overrides Table -->
                    <div class="cp-table-wrap cp-modal-table-wrap">
                        <table class="cp-table cp-table-compact" id="mscParamsTable">
                            <thead>
                                <tr>
                                    <th>Parameter Key</th>
                                    <th>Configuration Value</th>
                                    <th>Scope</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="mscParamsTbody">
                                <tr>
                                    <td colspan="4" class="text-center cp-loading-cell">
                                        <i class="fi fi-rr-spinner-alt cp-spin"></i> Loading site configuration parameters...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary" id="btnSubmitSiteSettings">
                    <i class="fi fi-rr-disk"></i> <span>Save Site Settings</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal 3: Provision Client Site                                            -->
<!-- ========================================================================= -->
<div class="cp-modal-backdrop" id="modalProvisionSite">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <div class="cp-modal-header-titles">
                <h3>Provision New Client Site</h3>
                <p>Deploy a new multi-tenant instance on Blumox infrastructure</p>
            </div>
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
                    <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="siteAppSelect" required data-placeholder="Select Application" data-width="100%">
                        <option value="">Select Application</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="siteThemeSelect">Initial Theme Preset</label>
                    <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="siteThemeSelect" data-placeholder="Select Theme Preset" data-width="100%">
                        <option value="0">Default System Theme</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="siteOwnerEmailInput">Client Owner Email *</label>
                    <input type="email" class="cp-form-input" id="siteOwnerEmailInput" required placeholder="client@company.com">
                </div>
                <div class="cp-form-group">
                    <label for="sitePartnerSelect">Referring Partner (Optional)</label>
                    <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="sitePartnerSelect" data-placeholder="Select Referring Partner" data-width="100%">
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

<!-- ========================================================================= -->
<!-- Modal 4: Quick Site Details Overview                                      -->
<!-- ========================================================================= -->
<div class="cp-modal-backdrop" id="modalSiteQuickDetails">
    <div class="cp-modal-box xl-box">
        <div class="cp-modal-header">
            <div class="cp-modal-header-titles">
                <div class="cp-modal-title-row">
                    <h3 id="sqdSiteName">Site Details</h3>
                    <span id="sqdSiteStatusPill" class="cp-status-pill active">Live</span>
                </div>
                <p id="sqdSiteDomainSub">Instant overview of tenant identity, license limits &amp; features</p>
            </div>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>

        <div class="cp-modal-body cp-quick-details-body">
            <!-- Stat Tiles -->
            <div class="cp-sqd-stats-grid">
                <div class="cp-sqd-stat-card">
                    <div class="cp-sqd-stat-label">Subscription Plan</div>
                    <div class="cp-sqd-stat-value" id="sqdPlanBadge">Professional SaaS</div>
                    <div class="cp-sqd-stat-sub" id="sqdExpiryText">No Expiry</div>
                </div>
                <div class="cp-sqd-stat-card">
                    <div class="cp-sqd-stat-label">Users Quota</div>
                    <div class="cp-sqd-stat-value" id="sqdUsersQuota">0 / 10</div>
                    <div class="cp-sqd-stat-sub" id="sqdUsersSub">Active Team Members</div>
                </div>
                <div class="cp-sqd-stat-card">
                    <div class="cp-sqd-stat-label">WhatsApp Devices</div>
                    <div class="cp-sqd-stat-value" id="sqdWaQuota">0 / 1</div>
                    <div class="cp-sqd-stat-sub" id="sqdWaSub">Connected Instances</div>
                </div>
                <div class="cp-sqd-stat-card">
                    <div class="cp-sqd-stat-label">Storage Allocated</div>
                    <div class="cp-sqd-stat-value" id="sqdStorageQuota">5 GB</div>
                    <div class="cp-sqd-stat-sub">Media &amp; Documents</div>
                </div>
            </div>

            <!-- Details 2-Column Info Grid -->
            <div class="cp-sqd-info-grid">
                <div class="cp-sqd-info-section">
                    <h5 class="cp-sqd-section-title"><i class="fi fi-rr-browser"></i> Identity &amp; Platform</h5>
                    <div class="cp-sqd-kv-row">
                        <span class="cp-sqd-key">Site ID &amp; Version</span>
                        <span class="cp-sqd-val" id="sqdSiteIdVersion">#0 &bull; V3</span>
                    </div>
                    <div class="cp-sqd-kv-row">
                        <span class="cp-sqd-key">Primary Domain</span>
                        <span class="cp-sqd-val"><a href="#" target="_blank" id="sqdDomainLink" class="cp-sqd-link">domain.com <i class="fi fi-rr-arrow-up-right-from-square"></i></a></span>
                    </div>
                    <div class="cp-sqd-kv-row">
                        <span class="cp-sqd-key">Site URL</span>
                        <span class="cp-sqd-val" id="sqdFullUrl">http://domain.com</span>
                    </div>
                    <div class="cp-sqd-kv-row">
                        <span class="cp-sqd-key">Application</span>
                        <span class="cp-sqd-val" id="sqdAppTitle">Core App</span>
                    </div>
                    <div class="cp-sqd-kv-row">
                        <span class="cp-sqd-key">Theme</span>
                        <span class="cp-sqd-val" id="sqdThemeTitle">Default Theme</span>
                    </div>
                </div>

                <div class="cp-sqd-info-section">
                    <h5 class="cp-sqd-section-title"><i class="fi fi-rr-user"></i> Client &amp; Renewal</h5>
                    <div class="cp-sqd-kv-row">
                        <span class="cp-sqd-key">Owner / Client</span>
                        <span class="cp-sqd-val font-semibold" id="sqdOwnerName">Client Name</span>
                    </div>
                    <div class="cp-sqd-kv-row">
                        <span class="cp-sqd-key">Email Address</span>
                        <span class="cp-sqd-val" id="sqdOwnerEmail">email@example.com</span>
                    </div>
                    <div class="cp-sqd-kv-row">
                        <span class="cp-sqd-key">Contact Mobile</span>
                        <span class="cp-sqd-val" id="sqdOwnerMobile">N/A</span>
                    </div>
                    <div class="cp-sqd-kv-row">
                        <span class="cp-sqd-key">Renewal Pricing</span>
                        <span class="cp-sqd-val" id="sqdPricingDetail">--</span>
                    </div>
                    <div class="cp-sqd-kv-row">
                        <span class="cp-sqd-key">Last Renewal Reminder</span>
                        <span class="cp-sqd-val" id="sqdLastReminder">Never Sent</span>
                    </div>
                    <div class="cp-sqd-kv-row">
                        <span class="cp-sqd-key">Reminder Count</span>
                        <span class="cp-sqd-val" id="sqdReminderCount">0</span>
                    </div>
                </div>
            </div>

            <!-- Features Summary -->
            <div class="cp-sqd-features-wrap">
                <h5 class="cp-sqd-section-title"><i class="fi fi-rr-toggle-on"></i> Active Feature Modules</h5>
                <div class="cp-sqd-feature-chips" id="sqdFeatureChips">
                    <!-- Populated dynamically -->
                </div>
            </div>
        </div>

        <div class="cp-modal-footer cp-sqd-footer">
            <button type="button" class="cp-btn-secondary" data-close-modal>Close</button>
            <button type="button" class="cp-btn-secondary" id="btnSqdSendReminder">
                <i class="fi fi-rr-bell"></i> Send Reminder
            </button>
            <button type="button" class="cp-btn-primary" id="btnSqdOpenSettings">
                <i class="fi fi-rr-settings-sliders"></i> Edit Configuration
            </button>
            <a href="#" target="_blank" class="cp-btn-secondary" id="btnSqdOpenLive" title="Open Tenant Dashboard">
                <i class="fi fi-rr-arrow-up-right-from-square"></i> Open Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Modal: Assign / Change Referral Partner for Site -->
<div class="cp-modal-backdrop" id="modalAssignSitePartner">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Assign Referral Partner</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formAssignSitePartner">
            <input type="hidden" id="aspSiteIdInput" value="0">
            <div class="cp-modal-body">
                <div class="cp-site-partner-summary-box">
                    <div class="cp-site-partner-icon">
                        <i class="fi fi-rr-globe"></i>
                    </div>
                    <div class="cp-site-partner-meta">
                        <h4 id="aspSiteNameDisplay">Site Name</h4>
                        <p id="aspSiteDomainDisplay">domain.com</p>
                        <span class="cp-badge-site-partner" id="aspCurrentPartnerDisplay">No Partner Assigned</span>
                    </div>
                </div>

                <div class="cp-form-group">
                    <label for="aspPartnerSelect">Referral / Affiliate Partner *</label>
                    <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="aspPartnerSelect" required data-placeholder="Select Partner" data-width="100%">
                        <option value="0">Direct / No Partner</option>
                    </select>
                    <small class="cp-form-hint">Select the referral partner to attribute commissions for this client instance.</small>
                </div>

                <div class="cp-form-group">
                    <label for="aspCommissionRateInput">Commission Rate (%)</label>
                    <input type="number" step="0.1" class="cp-form-input" id="aspCommissionRateInput" value="10.0" placeholder="e.g. 10.0">
                </div>

                <div class="cp-form-group">
                    <label for="aspCommissionNotesInput">Referral / Attribution Notes</label>
                    <textarea class="cp-form-input" id="aspCommissionNotesInput" rows="2" placeholder="e.g. Referral partner assigned from Control Panel"></textarea>
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Save Partner Attribution</button>
            </div>
        </form>
    </div>
</div>
