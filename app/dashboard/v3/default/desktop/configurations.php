<?php

/**
 * Blumox Control Panel: Dedicated Configurations & Code Studio
 * Path: /app/dashboard/v3/default/desktop/configurations.php
 * Route: /dashboard/configurations/
 * Strict V3 Rule: External CSS and External JS only, zero inline styles/scripts
 */

$userRole = isset($site_access_type) ? strtolower(trim($site_access_type)) : 'administrator';
$isAdmin = ($userRole === 'administrator');

?>
<div class="cfg-page-wrapper" id="configurationsApp" data-user-role="<?php echo htmlspecialchars($userRole); ?>">

    <!-- 1. Header & Quick Actions Bar -->
    <div class="cfg-header-banner">
        <div class="cfg-header-left">
            <a href="/dashboard/" class="cfg-back-link" title="Return to Control Panel Dashboard">
                <i class="fi fi-rr-arrow-left"></i> <span>Control Panel</span>
            </a>
            <div class="cfg-title-row">
                <div class="cfg-title-icon">
                    <i class="fi fi-rr-settings-sliders"></i>
                </div>
                <div class="cfg-title-meta">
                    <h2>Configuration Studio &amp; Code Engine</h2>
                    <p>Global environment variables, master API keys, and site-wise subscription billing &amp; status control.</p>
                </div>
            </div>
        </div>

        <div class="cfg-header-actions">
            <button type="button" class="cfg-btn-secondary" id="btnToggleCodeView">
                <i class="fi fi-rr-terminal"></i> <span id="btnToggleCodeViewText">Code View (.env)</span>
            </button>
            <button type="button" class="cfg-btn-secondary" id="btnExportEnv" title="Export as .env file">
                <i class="fi fi-rr-download"></i> <span>Export .env</span>
            </button>
            <button type="button" class="cfg-btn-icon" id="btnRefreshConfig" title="Reload Configurations">
                <i class="fi fi-rr-refresh"></i>
            </button>
            <?php if ($isAdmin) { ?>
                <button type="button" class="cfg-btn-primary" id="btnOpenAddConfigModal">
                    <i class="fi fi-rr-add"></i> <span>Add Parameter</span>
                </button>
            <?php } ?>
        </div>
    </div>

    <!-- 2. Primary Mode Selector: Global (Unique) vs Site-Wise Configurations -->
    <div class="cfg-mode-switcher-wrap">
        <div class="cfg-mode-tabs" role="tablist">
            <button type="button" class="cfg-mode-tab active" id="btnModeGlobal" data-mode="global">
                <div class="cfg-mode-icon"><i class="fi fi-rr-globe"></i></div>
                <div class="cfg-mode-info">
                    <h4>Global (Unique) Configurations</h4>
                    <p>System-wide environment variables, master API tokens &amp; cluster defaults</p>
                </div>
            </button>
            <button type="button" class="cfg-mode-tab" id="btnModeSiteWise" data-mode="site-wise">
                <div class="cfg-mode-icon"><i class="fi fi-rr-building"></i></div>
                <div class="cfg-mode-info">
                    <h4>Site-Wise Configurations &amp; Operations</h4>
                    <p>Handle billing plans, subscription expiry dates, live site status &amp; quotas per site</p>
                </div>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODE 1: GLOBAL (UNIQUE) CONFIGURATIONS                                    -->
    <!-- ========================================================================= -->
    <div class="cfg-mode-container active" id="cfgGlobalContainer">

        <!-- Insights Summary Counters -->
        <div class="cfg-kpi-grid">
            <div class="cfg-kpi-card">
                <div class="cfg-kpi-icon indigo"><i class="fi fi-rr-settings"></i></div>
                <div class="cfg-kpi-info">
                    <span class="cfg-kpi-label">Total Parameters</span>
                    <h3 class="cfg-kpi-value" id="cfgKpiTotal">-</h3>
                    <span class="cfg-kpi-sub">Across all environments</span>
                </div>
            </div>
            <div class="cfg-kpi-card">
                <div class="cfg-kpi-icon emerald"><i class="fi fi-rr-globe"></i></div>
                <div class="cfg-kpi-info">
                    <span class="cfg-kpi-label">Global Parameters</span>
                    <h3 class="cfg-kpi-value" id="cfgKpiGlobal">-</h3>
                    <span class="cfg-kpi-sub">Inherited system defaults</span>
                </div>
            </div>
            <div class="cfg-kpi-card">
                <div class="cfg-kpi-icon amber"><i class="fi fi-rr-building"></i></div>
                <div class="cfg-kpi-info">
                    <span class="cfg-kpi-label">Tenant Overrides</span>
                    <h3 class="cfg-kpi-value" id="cfgKpiSite">-</h3>
                    <span class="cfg-kpi-sub">Client site specific</span>
                </div>
            </div>
            <div class="cfg-kpi-card">
                <div class="cfg-kpi-icon violet"><i class="fi fi-rr-lock"></i></div>
                <div class="cfg-kpi-info">
                    <span class="cfg-kpi-label">Secrets &amp; Tokens</span>
                    <h3 class="cfg-kpi-value" id="cfgKpiSecrets">-</h3>
                    <span class="cfg-kpi-sub">Masked &amp; encrypted</span>
                </div>
            </div>
        </div>

        <!-- Toolbar: Scope Filters, Search, Category Pills -->
        <div class="cfg-toolbar-card">
            <div class="cfg-toolbar-top">
                <div class="cfg-scope-tabs">
                    <button type="button" class="cfg-scope-btn active" data-scope="all">
                        <i class="fi fi-rr-apps"></i> All Scopes
                    </button>
                    <button type="button" class="cfg-scope-btn" data-scope="global">
                        <i class="fi fi-rr-globe"></i> Global Infra
                    </button>
                    <button type="button" class="cfg-scope-btn" data-scope="site">
                        <i class="fi fi-rr-building"></i> Tenant Overrides
                    </button>
                </div>

                <div class="cfg-site-selector-wrap" id="cfgSiteSelectorWrap">
                    <label for="cfgSiteFilterSelect">Tenant Site:</label>
                    <select id="cfgSiteFilterSelect" class="cfg-select">
                        <option value="all">All Sites &amp; Global</option>
                    </select>
                </div>

                <div class="cfg-search-wrap">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" id="cfgSearchInput" class="cfg-search-input" placeholder="Search by key, value, or domain...">
                    <button type="button" id="cfgSearchClear" class="cfg-search-clear" title="Clear Search">&times;</button>
                </div>
            </div>

            <div class="cfg-category-pills">
                <button type="button" class="cfg-cat-pill active" data-cat="all">All Categories</button>
                <button type="button" class="cfg-cat-pill" data-cat="general">General &amp; Branding</button>
                <button type="button" class="cfg-cat-pill" data-cat="security">Security &amp; Keys</button>
                <button type="button" class="cfg-cat-pill" data-cat="features">Features &amp; Flags</button>
                <button type="button" class="cfg-cat-pill" data-cat="quotas">Limits &amp; Quotas</button>
                <button type="button" class="cfg-cat-pill" data-cat="comms">Communications</button>
            </div>
        </div>

        <!-- Global VIEW A: Visual Key-Value Table -->
        <div class="cfg-view-container active" id="cfgVisualView">
            <div class="cfg-card">
                <div class="cfg-table-wrap">
                    <table class="cfg-table" id="cfgTable">
                        <thead>
                            <tr>
                                <th class="col-cfg-key">Configuration Key</th>
                                <th class="col-cfg-scope">Scope</th>
                                <th class="col-cfg-type">Type</th>
                                <th>Value &amp; Preview</th>
                                <th class="col-cfg-status">Status</th>
                                <th class="col-cfg-updated">Updated</th>
                                <th class="col-cfg-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="cfgTbody">
                            <tr>
                                <td colspan="7" class="text-center cfg-loading-state">
                                    <i class="fi fi-rr-spinner-alt cfg-spin"></i> Loading configuration parameters...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Global VIEW B: Code Studio Editor (.ENV & JSON) -->
        <div class="cfg-view-container" id="cfgCodeView">
            <div class="cfg-card cfg-code-card">
                <div class="cfg-code-topbar">
                    <div class="cfg-code-modes">
                        <button type="button" class="cfg-code-format-btn active" data-format="env">
                            <i class="fi fi-rr-file-code"></i> .ENV Format
                        </button>
                        <button type="button" class="cfg-code-format-btn" data-format="json">
                            <i class="fi fi-rr-brackets-curly"></i> JSON Format
                        </button>
                    </div>

                    <div class="cfg-code-status-wrap">
                        <span class="cfg-syntax-badge valid" id="cfgSyntaxBadge">
                            <i class="fi fi-rr-check"></i> Syntax Valid
                        </span>
                        <span class="cfg-code-count" id="cfgCodeLineCount">0 lines</span>
                    </div>

                    <div class="cfg-code-actions">
                        <button type="button" class="cfg-btn-subtle" id="btnCopyRawCode" title="Copy code to clipboard">
                            <i class="fi fi-rr-copy"></i> Copy
                        </button>
                        <button type="button" class="cfg-btn-subtle" id="btnFormatRawCode" title="Format code">
                            <i class="fi fi-rr-magic-wand"></i> Format
                        </button>
                        <?php if ($isAdmin) { ?>
                            <button type="button" class="cfg-btn-primary" id="btnSaveRawCode">
                                <i class="fi fi-rr-disk"></i> <span>Save Changes</span>
                            </button>
                        <?php } ?>
                    </div>
                </div>

                <div class="cfg-editor-canvas">
                    <div class="cfg-gutter" id="cfgEditorGutter">1</div>
                    <textarea id="cfgRawEditor" class="cfg-code-textarea font-mono" spellcheck="false" placeholder="# Environment configurations..."></textarea>
                </div>

                <div class="cfg-code-footer">
                    <div class="cfg-code-tip">
                        <i class="fi fi-rr-info"></i>
                        <span><strong>Pro Tip:</strong> In .ENV mode, lines starting with <code>#</code> are preserved as comments. Blank lines are skipped. Surround strings with double quotes if they contain spaces.</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- MODE 2: SITE-WISE CONFIGURATIONS & OPERATIONS                             -->
    <!-- ========================================================================= -->
    <div class="cfg-mode-container" id="cfgSiteWiseContainer">

        <!-- 1. Site Selector & Tenant Overview Bar -->
        <div class="cfg-sitewise-picker-card">
            <div class="cfg-picker-left">
                <span class="cfg-picker-label"><i class="fi fi-rr-search-alt"></i> Select Target Site:</span>
                <select id="cfgSiteWisePicker" class="cfg-select-large">
                    <option value="">Select a Client Site...</option>
                    <!-- Populated dynamically via API -->
                </select>
            </div>
            <div class="cfg-site-badges-strip" id="cfgSiteWiseBadges">
                <span class="cfg-site-badge" id="cfgBadgeApp"><i class="fi fi-rr-apps"></i> <span id="cfgSiteWiseAppTitle">Application</span></span>
                <span class="cfg-site-badge" id="cfgBadgeDomain"><i class="fi fi-rr-globe"></i> <a href="#" id="cfgSiteWiseDomainLink" target="_blank">domain.com</a></span>
                <span class="cfg-site-badge" id="cfgBadgeOwner"><i class="fi fi-rr-user"></i> <span id="cfgSiteWiseOwnerName">Owner</span></span>
                <span class="cfg-site-badge status-live" id="cfgSiteWiseStatusBadge">Active</span>
            </div>
        </div>

        <!-- 2. Operational, Billing, Expiry & Quotas Command Center -->
        <div class="cfg-card">
            <div class="cfg-card-header">
                <div class="cfg-card-title">
                    <h3>Site Operational Lifecycle, Billing &amp; Subscription Controls</h3>
                    <p>Handle commercial tiers, subscription renewals, operational site status, and resource limits for the selected site</p>
                </div>
            </div>

            <form id="formSiteWiseOperations">
                <div class="cfg-ops-grid">

                    <!-- Section A: Billing & Plan -->
                    <div class="cfg-ops-section">
                        <h4 class="cfg-ops-section-title"><i class="fi fi-rr-receipt"></i> Subscription Plan &amp; Billing</h4>
                        
                        <div class="cp-form-group">
                            <label for="cfgSiteWisePlanSelect">Subscription Plan Tier *</label>
                            <select class="cp-select" id="cfgSiteWisePlanSelect">
                                <option value="Professional SaaS">Professional SaaS</option>
                                <option value="Enterprise Suite">Enterprise Suite</option>
                                <option value="Growth Plan">Growth Plan</option>
                                <option value="Starter Launch">Starter Launch</option>
                                <option value="Custom Dedicated">Custom Dedicated</option>
                            </select>
                        </div>

                        <div class="cp-form-group">
                            <label for="cfgSiteWiseExpiryInput">Subscription Expiry Date *</label>
                            <input type="date" class="cp-input" id="cfgSiteWiseExpiryInput">
                            
                            <div class="cfg-quick-extend-group">
                                <span class="cfg-picker-label">Quick Extend:</span>
                                <button type="button" class="cfg-quick-extend-btn btn-extend-expiry" data-days="30">+1 Month</button>
                                <button type="button" class="cfg-quick-extend-btn btn-extend-expiry" data-days="90">+3 Months</button>
                                <button type="button" class="cfg-quick-extend-btn btn-extend-expiry" data-days="180">+6 Months</button>
                                <button type="button" class="cfg-quick-extend-btn btn-extend-expiry" data-days="365">+1 Year</button>
                                <button type="button" class="cfg-quick-extend-btn btn-extend-expiry" data-days="lifetime">Lifetime</button>
                            </div>
                        </div>
                    </div>

                    <!-- Section B: Site Operational Status -->
                    <div class="cfg-ops-section">
                        <h4 class="cfg-ops-section-title"><i class="fi fi-rr-shield-check"></i> Operational Status</h4>

                        <div class="cp-form-group">
                            <label for="cfgSiteWiseStatusSelect">Site Operational Status *</label>
                            <select class="cp-select" id="cfgSiteWiseStatusSelect">
                                <option value="1">Active / Live (Site Accessible)</option>
                                <option value="0">Suspended / Deactivated (Access Blocked)</option>
                            </select>
                            <small class="cp-hint">When set to Suspended, users visiting this site domain will see a subscription inactive notice.</small>
                        </div>

                        <div class="cp-form-group">
                            <label for="cfgSiteWiseOwnerEmail">Registered Owner Email</label>
                            <input type="email" class="cp-input" id="cfgSiteWiseOwnerEmail" readonly>
                        </div>
                    </div>

                    <!-- Section C: Resource Quotas -->
                    <div class="cfg-ops-section">
                        <h4 class="cfg-ops-section-title"><i class="fi fi-rr-dashboard"></i> Resource Quotas &amp; Limits</h4>

                        <div class="cp-form-group">
                            <label for="cfgSiteWiseUserLimit">Team Members Limit</label>
                            <input type="number" class="cp-input" id="cfgSiteWiseUserLimit" min="1" value="10">
                            <div class="cfg-preset-pills">
                                <button type="button" class="cfg-preset-pill btn-set-cfg-user" data-val="5">5</button>
                                <button type="button" class="cfg-preset-pill btn-set-cfg-user" data-val="10">10</button>
                                <button type="button" class="cfg-preset-pill btn-set-cfg-user" data-val="25">25</button>
                                <button type="button" class="cfg-preset-pill btn-set-cfg-user" data-val="50">50</button>
                                <button type="button" class="cfg-preset-pill btn-set-cfg-user" data-val="999">Unlimited</button>
                            </div>
                        </div>

                        <div class="cp-form-group">
                            <label for="cfgSiteWiseWaLimit">WhatsApp Devices Limit</label>
                            <input type="number" class="cp-input" id="cfgSiteWiseWaLimit" min="1" value="1">
                            <div class="cfg-preset-pills">
                                <button type="button" class="cfg-preset-pill btn-set-cfg-wa" data-val="1">1</button>
                                <button type="button" class="cfg-preset-pill btn-set-cfg-wa" data-val="3">3</button>
                                <button type="button" class="cfg-preset-pill btn-set-cfg-wa" data-val="5">5</button>
                                <button type="button" class="cfg-preset-pill btn-set-cfg-wa" data-val="10">10</button>
                            </div>
                        </div>

                        <div class="cp-form-group">
                            <label for="cfgSiteWiseStorageLimit">Storage Limit (MB)</label>
                            <input type="number" class="cp-input" id="cfgSiteWiseStorageLimit" min="100" value="5120">
                            <div class="cfg-preset-pills">
                                <button type="button" class="cfg-preset-pill btn-set-cfg-storage" data-val="1024">1 GB</button>
                                <button type="button" class="cfg-preset-pill btn-set-cfg-storage" data-val="5120">5 GB</button>
                                <button type="button" class="cfg-preset-pill btn-set-cfg-storage" data-val="10240">10 GB</button>
                                <button type="button" class="cfg-preset-pill btn-set-cfg-storage" data-val="51200">50 GB</button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Section D: Feature Modules & Capabilities Toggles -->
                <div class="cp-form-group mt-4">
                    <label>Active Modules &amp; Feature Capabilities for this Site</label>
                    <div class="cfg-switches-grid">
                        <div class="cfg-switch-card">
                            <div class="cfg-switch-info">
                                <h5>WhatsApp Web &amp; Automation</h5>
                                <p>Multi-device sync, chat inbox, automated workflows</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="cfgSwWhatsapp" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cfg-switch-card">
                            <div class="cfg-switch-info">
                                <h5>CRM &amp; Leads Management</h5>
                                <p>Lead capture, assignments, activity timelines</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="cfgSwLeads" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cfg-switch-card">
                            <div class="cfg-switch-info">
                                <h5>Billing &amp; Invoices Ledger</h5>
                                <p>Client accounts, payment links, invoicing</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="cfgSwAccounts" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cfg-switch-card">
                            <div class="cfg-switch-info">
                                <h5>Transporters &amp; Fleet</h5>
                                <p>Vehicles catalog, driver tariffs, quotes</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="cfgSwTransporters" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cfg-switch-card">
                            <div class="cfg-switch-info">
                                <h5>Tour Packages &amp; Itineraries</h5>
                                <p>Package builder, hotels, itinerary engine</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="cfgSwPackages" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cfg-switch-card">
                            <div class="cfg-switch-info">
                                <h5>Custom Domain White-label</h5>
                                <p>Custom branding, custom domain routing, SSL</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="cfgSwCustomDomain" checked>
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="cfg-switch-card">
                            <div class="cfg-switch-info">
                                <h5>Developer API &amp; Webhooks</h5>
                                <p>REST API token generation, webhook web dispatch</p>
                            </div>
                            <label class="cp-toggle">
                                <input type="checkbox" id="cfgSwApiAccess">
                                <span class="cp-toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Footer Save Action -->
                <div class="cfg-sitewise-footer">
                    <?php if ($isAdmin) { ?>
                        <button type="submit" class="cfg-btn-primary" id="btnSaveSiteWiseOperations">
                            <i class="fi fi-rr-disk"></i> <span>Save Site Billing, Expiry &amp; Status</span>
                        </button>
                    <?php } ?>
                    <span class="cfg-save-feedback" id="cfgSiteWiseSaveFeedback"></span>
                </div>
            </form>
        </div>

        <!-- 3. Site-Specific Configuration Variables -->
        <div class="cfg-card">
            <div class="cfg-card-header">
                <div class="cfg-card-title">
                    <h3>Tenant-Specific Configuration Variables</h3>
                    <p>Environment overrides and custom variables unique to this site instance</p>
                </div>
                <?php if ($isAdmin) { ?>
                    <button type="button" class="cfg-btn-secondary" id="btnOpenAddSiteParamModal">
                        <i class="fi fi-rr-add"></i> <span>Add Site Parameter</span>
                    </button>
                <?php } ?>
            </div>

            <div class="cfg-table-wrap">
                <table class="cfg-table" id="cfgSiteWiseParamsTable">
                    <thead>
                        <tr>
                            <th class="col-cfg-key">Parameter Key</th>
                            <th class="col-cfg-type">Type</th>
                            <th>Value &amp; Preview</th>
                            <th class="col-cfg-status">Status</th>
                            <th class="col-cfg-updated">Updated</th>
                            <th class="col-cfg-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cfgSiteWiseParamsTbody">
                        <tr>
                            <td colspan="6" class="text-center cfg-loading-state">
                                Select a client site to inspect tenant-specific parameters.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- MODALS: Add / Edit Parameter & JSON Inspector                             -->
    <!-- ========================================================================= -->

    <!-- Modal: Add / Edit Parameter -->
    <div class="cp-modal-backdrop" id="modalConfigParam">
        <div class="cp-modal-dialog">
            <div class="cp-modal-header">
                <h3 id="modalConfigTitle">Add Configuration Parameter</h3>
                <button type="button" class="cp-modal-close" data-close-modal>&times;</button>
            </div>
            <form id="formConfigParam">
                <input type="hidden" id="cfgParamId" value="0">
                <div class="cp-modal-body">
                    <div class="cp-form-group">
                        <label for="cfgParamKey">Parameter Key <span class="text-danger">*</span></label>
                        <input type="text" class="cp-input font-mono" id="cfgParamKey" placeholder="e.g. WHATSAPP_API_TOKEN or site_theme" required>
                        <small class="cp-hint">Use uppercase snake_case for environment variables, or lowercase for app parameters.</small>
                    </div>

                    <div class="cp-form-row">
                        <div class="cp-form-group">
                            <label for="cfgParamScope">Configuration Scope</label>
                            <select class="cp-select" id="cfgParamScope">
                                <option value="1">Global System (All Tenants)</option>
                                <option value="0">Tenant Site Override</option>
                            </select>
                        </div>
                        <div class="cp-form-group" id="cfgModalSiteRow">
                            <label for="cfgParamSiteId">Target Client Site</label>
                            <select class="cp-select" id="cfgParamSiteId">
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                    </div>

                    <div class="cp-form-row">
                        <div class="cp-form-group">
                            <label for="cfgParamType">Data Type</label>
                            <select class="cp-select" id="cfgParamType">
                                <option value="string">String / Text</option>
                                <option value="boolean">Boolean (0 or 1)</option>
                                <option value="number">Number / Limit</option>
                                <option value="secret">Secret / API Token</option>
                                <option value="json">JSON / Array</option>
                            </select>
                        </div>
                        <div class="cp-form-group">
                            <label for="cfgParamStatus">Status</label>
                            <select class="cp-select" id="cfgParamStatus">
                                <option value="1">Active</option>
                                <option value="0">Disabled</option>
                            </select>
                        </div>
                    </div>

                    <div class="cp-form-group">
                        <label for="cfgParamValue">Value</label>
                        <textarea class="cp-input font-mono" id="cfgParamValue" rows="4" placeholder="Enter configuration value..."></textarea>
                    </div>
                </div>
                <div class="cp-modal-footer">
                    <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                    <button type="submit" class="cp-btn-primary" id="btnSaveConfigParam">
                        <i class="fi fi-rr-disk"></i> <span>Save Parameter</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: JSON Inspector / Pretty Viewer -->
    <div class="cp-modal-backdrop" id="modalJsonInspector">
        <div class="cp-modal-dialog">
            <div class="cp-modal-header">
                <h3 id="modalJsonTitle">JSON Inspector</h3>
                <button type="button" class="cp-modal-close" data-close-modal>&times;</button>
            </div>
            <div class="cp-modal-body">
                <pre class="cfg-json-pre" id="cfgJsonPrettyContent"><code>{}</code></pre>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" id="btnCopyJsonContent">
                    <i class="fi fi-rr-copy"></i> Copy JSON
                </button>
                <button type="button" class="cp-btn-primary" data-close-modal>Close</button>
            </div>
        </div>
    </div>

</div>
