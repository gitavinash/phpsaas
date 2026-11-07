<?php

/**
 * Blumox Control Panel: Dedicated Partner Referred Clients Page
 * Path: /app/dashboard/v3/default/desktop/partners/clients.php
 * Route: /dashboard/partners/clients/ (and /dashboard/partner_clients/)
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

$partnerId = isset($_GET['partner_id']) && is_numeric($_GET['partner_id']) ? (int)$_GET['partner_id'] : 0;
?>

<div class="cp-wrapper cp-page-dedicated" id="controlPanelApp" data-page="partner_clients" data-partner-id="<?php echo (int)$partnerId; ?>" data-user-role="<?php echo htmlspecialchars($userRole); ?>">

    <!-- Top Navigation Breadcrumb & Header -->
    <div class="cp-header-bar">
        <div class="cp-header-left">
            <a href="/dashboard/commissions/" class="cp-back-btn" title="Back to Partners &amp; Commissions">
                <i class="fi fi-rr-arrow-left"></i>
            </a>
            <div class="cp-header-titles">
                <h1>Referred Client Instances</h1>
                <p>
                    <span>Client tenancies, license subscriptions &amp; earnings referred by <strong id="partnerHeadingName">Partner</strong></span>
                    <span class="cp-badge-role <?php echo htmlspecialchars($userRole === 'on_ground_sales' ? 'ground' : $userRole); ?>">
                        <i class="fi fi-rr-shield-check"></i> <?php echo htmlspecialchars($displayRole); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="cp-header-actions">
            <button type="button" class="cp-btn-secondary" id="btnPartnerClientsBack">
                <i class="fi fi-rr-users-alt"></i> <span>All Partners</span>
            </button>
            <?php if ($userRole === 'administrator') { ?>
                <button type="button" class="cp-btn-primary" id="btnOpenManualCommissionModal">
                    <i class="fi fi-rr-add"></i> <span>Record Commission</span>
                </button>
            <?php } ?>
            <button type="button" class="cp-btn-secondary" id="btnCpRefreshData" title="Refresh Clients">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Partner Profile Banner Card -->
    <div class="cp-partner-banner-card" id="partnerBannerWrap">
        <div class="cp-pbc-main">
            <div class="cp-pbc-avatar" id="pbcAvatar">P</div>
            <div class="cp-pbc-info">
                <div class="cp-pbc-name-row">
                    <h3 id="pbcName">Loading Partner...</h3>
                    <span class="cp-pbc-badge-rate" id="pbcRate">10% Commission</span>
                    <span class="cp-status-pill active" id="pbcStatus">Active</span>
                </div>
                <div class="cp-pbc-meta-row">
                    <span><i class="fi fi-rr-envelope"></i> <span id="pbcEmail">--</span></span>
                    <span><i class="fi fi-rr-phone-call"></i> <span id="pbcMobile">--</span></span>
                    <span><i class="fi fi-rr-calendar"></i> Registered: <span id="pbcRegDate">--</span></span>
                </div>
            </div>
        </div>
        <div class="cp-pbc-actions">
            <button type="button" class="cp-btn-secondary" id="btnBannerEditPartner">
                <i class="fi fi-rr-edit"></i> <span>Edit Partner Profile</span>
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
                <p>Total Referred Sites</p>
                <h3 id="statPartnerTotalClients">-</h3>
                <span class="cp-kpi-sub">Attributed instances</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon green">
                <i class="fi fi-rr-badge-check"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Commission Earned</p>
                <h3 id="statPartnerTotalCommission">-</h3>
                <span class="cp-kpi-sub">Total referral accrued</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon purple">
                <i class="fi fi-rr-wallet"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Disbursed Payouts</p>
                <h3 id="statPartnerPaidCommission">-</h3>
                <span class="cp-kpi-sub">Transferred to partner</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon orange">
                <i class="fi fi-rr-hourglass-end"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Pending Balance</p>
                <h3 id="statPartnerPendingCommission">-</h3>
                <span class="cp-kpi-sub">Ready for reconciliation</span>
            </div>
        </div>
    </div>

    <!-- Referred Clients Table Card -->
    <div class="cp-card">
        <div class="cp-card-header">
            <div class="cp-card-title">
                <h3>Attributed Client Instances Directory</h3>
                <p>Client applications, SaaS subscription tiers, active licenses, and generated commission history</p>
            </div>
        </div>

        <div class="cp-toolbar">
            <div class="cp-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" class="cp-search-input" id="cpSearchPartnerClients" placeholder="Search site, domain, or client owner...">
                <button type="button" class="cp-search-clear-btn cp-hidden" id="cpClearPartnerClientsSearch" title="Clear Search">&times;</button>
            </div>
            <div class="cp-filters-wrap">
                <select class="cp-select selectJS2 cp-select2" id="cpSwitchPartnerSelect" data-placeholder="Switch Partner" data-width="220px">
                    <option value="<?php echo (int)$partnerId; ?>">Current Partner</option>
                </select>
                <select class="cp-select selectJS2 cp-select2" id="cpFilterPartnerClientStatus" data-placeholder="All Status" data-width="150px">
                    <option value="">All Status</option>
                    <option value="1">Live Only</option>
                    <option value="0">Suspended Only</option>
                </select>
            </div>
        </div>

        <div class="cp-table-wrap">
            <table class="cp-table" id="cpPartnerClientsTable">
                <thead>
                    <tr>
                        <th>Client Site &amp; Domain</th>
                        <th>Application &amp; Theme</th>
                        <th>Client / Owner</th>
                        <th>Plan &amp; Expiry</th>
                        <th>Commission Earned</th>
                        <th>Status</th>
                        <th class="text-right cp-actions-header">Actions</th>
                    </tr>
                </thead>
                <tbody id="cpPartnerClientsTbody">
                    <tr>
                        <td colspan="7" class="text-center cp-loading-cell">
                            <i class="fi fi-rr-spinner-alt cp-spin"></i> Loading referred clients...
                        </td>
                    </tr>
                </tbody>
            </table>
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

<!-- Modal: Edit Partner Details (Shared) -->
<div class="cp-modal-backdrop" id="modalEditPartner">
    <div class="cp-modal-box cp-modal-wide">
        <div class="cp-modal-header">
            <h3>Edit Referral Partner Details</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formEditPartner">
            <input type="hidden" id="editPartnerIdInput" value="<?php echo (int)$partnerId; ?>">
            <div class="cp-modal-body">
                <div class="cp-partner-summary-card">
                    <div class="cp-partner-avatar" id="editPartnerAvatarDisplay">P</div>
                    <div class="cp-partner-meta-text">
                        <h4 id="editPartnerNameTitle">Partner Name</h4>
                        <p id="editPartnerEmailSubtitle">partner@example.com</p>
                    </div>
                </div>

                <div class="cp-form-row-2">
                    <div class="cp-form-group">
                        <label for="editPartnerNameInput">Full / Business Name *</label>
                        <input type="text" class="cp-form-input" id="editPartnerNameInput" required placeholder="e.g. Acme Ventures">
                    </div>
                    <div class="cp-form-group">
                        <label for="editPartnerEmailInput">Email Address *</label>
                        <input type="email" class="cp-form-input" id="editPartnerEmailInput" required placeholder="partner@acme.com">
                    </div>
                </div>

                <div class="cp-form-row-3">
                    <div class="cp-form-group">
                        <label for="editPartnerMobileInput">Mobile Number</label>
                        <input type="text" class="cp-form-input" id="editPartnerMobileInput" placeholder="+91 9876543210">
                    </div>
                    <div class="cp-form-group">
                        <label for="editPartnerRateInput">Commission Rate (%) *</label>
                        <input type="number" step="0.1" class="cp-form-input" id="editPartnerRateInput" required value="10.0" placeholder="10.0">
                    </div>
                    <div class="cp-form-group">
                        <label for="editPartnerStatusSelect">Partner Status</label>
                        <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="editPartnerStatusSelect" data-placeholder="Select Status" data-width="100%">
                            <option value="1">Active / Verified</option>
                            <option value="0">Disabled / Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="cp-modal-subheading">
                    <i class="fi fi-rr-bank"></i> <span>Banking &amp; Payout Details</span>
                </div>

                <div class="cp-form-row-2">
                    <div class="cp-form-group">
                        <label for="editPartnerBankNameInput">Bank Name</label>
                        <input type="text" class="cp-form-input" id="editPartnerBankNameInput" placeholder="e.g. HDFC Bank">
                    </div>
                    <div class="cp-form-group">
                        <label for="editPartnerAccountNoInput">Account Number</label>
                        <input type="text" class="cp-form-input" id="editPartnerAccountNoInput" placeholder="e.g. 50100234567890">
                    </div>
                </div>

                <div class="cp-form-row-3">
                    <div class="cp-form-group">
                        <label for="editPartnerIfscInput">IFSC Code</label>
                        <input type="text" class="cp-form-input" id="editPartnerIfscInput" placeholder="e.g. HDFC0000123">
                    </div>
                    <div class="cp-form-group">
                        <label for="editPartnerUpiInput">UPI ID / VPA</label>
                        <input type="text" class="cp-form-input" id="editPartnerUpiInput" placeholder="e.g. partner@okhdfcbank">
                    </div>
                    <div class="cp-form-group">
                        <label for="editPartnerPanInput">PAN / GSTIN</label>
                        <input type="text" class="cp-form-input" id="editPartnerPanInput" placeholder="e.g. ABCDE1234F">
                    </div>
                </div>

                <div class="cp-form-group">
                    <label for="editPartnerNotesInput">Referral Agreement &amp; Internal Notes</label>
                    <textarea class="cp-form-input" id="editPartnerNotesInput" rows="2" placeholder="Notes on referral terms, agreed payout schedules, or contracts"></textarea>
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Save Partner Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Record Manual Commission (Shared) -->
<div class="cp-modal-backdrop" id="modalManualCommission">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Record Partner Commission Accrual</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formManualCommission">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="manualCommPartnerSelect">Partner Account *</label>
                    <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="manualCommPartnerSelect" required data-placeholder="Select Partner" data-width="100%">
                        <option value="">Select Partner</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="manualCommSiteSelect">Referred Site *</label>
                    <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="manualCommSiteSelect" required data-placeholder="Select Site" data-width="100%">
                        <option value="">Select Site</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="manualCommAmountInput">Commission Amount (INR) *</label>
                    <input type="number" step="0.01" class="cp-form-input" id="manualCommAmountInput" required placeholder="e.g. 5000.00">
                </div>
                <div class="cp-form-group">
                    <label for="manualCommRateInput">Commission Rate (%)</label>
                    <input type="number" step="0.1" class="cp-form-input" id="manualCommRateInput" value="10.0">
                </div>
                <div class="cp-form-group">
                    <label for="manualCommNotesInput">Notes / Referral Reason</label>
                    <input type="text" class="cp-form-input" id="manualCommNotesInput" placeholder="e.g. Q4 Referral commission">
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Record Accrual</button>
            </div>
        </form>
    </div>
</div>

