<?php

/**
 * Blumox Control Panel: Dedicated Client Sites Page
 * Path: /app/dashboard/v3/default/desktop/sites.php
 * Route: /dashboard/sites/
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
                    <span>Multi-Tenant Provisioning, Domain Routing, License Quotas &amp; Status Control</span>
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
            <button type="button" class="cp-btn-secondary" id="btnCpRefreshData" title="Refresh Sites">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Client Sites Main Card -->
    <div class="cp-card">
        <div class="cp-card-header">
            <div class="cp-card-title">
                <h3>Live Client Instances</h3>
                <p>Provisioned tenants across Blumox infrastructure cluster</p>
            </div>
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
                    <tr>
                        <td colspan="7" class="text-center cp-loading-cell">
                            <i class="fi fi-rr-spinner-alt cp-spin"></i> Loading client sites...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

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
                                <p>REST API token generation, webhook dispatch</p>
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
