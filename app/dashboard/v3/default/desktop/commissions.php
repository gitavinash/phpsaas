<?php

/**
 * Blumox Control Panel: Dedicated Partner Commissions & Referral Management Page
 * Path: /app/dashboard/v3/default/desktop/commissions.php
 * Route: /dashboard/commissions/
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

<div class="cp-wrapper cp-page-dedic3ated" id="controlPanelApp" data-page="commissions" data-user-role="<?php echo htmlspecialchars($userRole); ?>">

    <!-- Top Executive Header -->
    <div class="cp-header-bar">
        <div class="cp-header-left">
            <div class="cp-header-icon">
                <i class="fi fi-rr-handshake"></i>
            </div>
            <div class="cp-header-titles">
                <h1>Partner Referrals &amp; Commissions</h1>
                <p>
                    <span>Affiliate Partners Directory, Referred Client Instances &amp; Commission Accruals</span>
                    <span class="cp-badge-role <?php echo htmlspecialchars($userRole === 'on_ground_sales' ? 'ground' : $userRole); ?>">
                        <i class="fi fi-rr-shield-check"></i> <?php echo htmlspecialchars($displayRole); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="cp-header-actions">
            <?php if ($userRole === 'administrator') { ?>
                <button type="button" class="cp-btn-secondary" id="btnOpenNewPartnerModal">
                    <i class="fi fi-rr-user-add"></i> <span>Add Partner</span>
                </button>
                <button type="button" class="cp-btn-primary" id="btnOpenManualCommissionModal">
                    <i class="fi fi-rr-add"></i> <span>Record Commission</span>
                </button>
            <?php } ?>
            <button type="button" class="cp-btn-secondary" id="btnCpRefreshData" title="Refresh Data">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Executive KPI Metric Counters -->
    <div class="cp-kpi-grid">
        <div class="cp-kpi-card">
            <div class="cp-kpi-icon blue">
                <i class="fi fi-rr-handshake"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Total Partners</p>
                <h3 id="statTotalPartners">-</h3>
                <span class="cp-kpi-sub" id="statActivePartners">0 active affiliates</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon purple">
                <i class="fi fi-rr-globe"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Referred Clients</p>
                <h3 id="statReferredClients">-</h3>
                <span class="cp-kpi-sub">Provisioned client tenancies</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon green">
                <i class="fi fi-rr-badge-check"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Paid Commissions</p>
                <h3 id="statPaidCommission">-</h3>
                <span class="cp-kpi-sub">Disbursed to partners</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon orange">
                <i class="fi fi-rr-time-quarter-to"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Pending Accruals</p>
                <h3 id="statPendingCommission">-</h3>
                <span class="cp-kpi-sub">Awaiting payout or approval</span>
            </div>
        </div>
    </div>

    <!-- Segmented Navigation Tabs -->
    <div class="cp-card">
        <div class="cp-card-header cp-tabs-header">
            <div class="cp-tab-pills" id="commissionsTabNav">
                <button type="button" class="cp-tab-pill active" data-tab-target="partners">
                    <i class="fi fi-rr-users-alt"></i> <span>Partners Directory</span>
                </button>
                <button type="button" class="cp-tab-pill" data-tab-target="ledger">
                    <i class="fi fi-rr-receipt"></i> <span>Commission Ledger</span>
                </button>
            </div>
        </div>

        <!-- Tab 1: Partners Directory Panel -->
        <div class="cp-tab-pane active" id="tabPanePartners">
            <div class="cp-toolbar">
                <div class="cp-search-wrap">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" class="cp-search-input" id="cpSearchPartners" placeholder="Search partner by name, email, or mobile...">
                    <button type="button" class="cp-search-clear-btn cp-hidden" id="cpClearPartnersSearch" title="Clear Search">&times;</button>
                </div>
                <div class="cp-filters-wrap">
                    <select class="cp-select selectJS2 cp-select2" id="cpFilterPartnerStatus" data-placeholder="All Status" data-width="150px">
                        <option value="">All Status</option>
                        <option value="1">Active Only</option>
                        <option value="0">Disabled Only</option>
                    </select>
                </div>
            </div>

            <div class="cp-table-wrap">
                <table class="cp-table" id="cpPartnersTable">
                    <thead>
                        <tr>
                            <th>Partner &amp; Identity</th>
                            <th>Contact &amp; Mobile</th>
                            <th>Referred Clients</th>
                            <th>Commission Rate</th>
                            <th>Total Earned</th>
                            <th>Payout Breakdown</th>
                            <th>Status</th>
                            <th class="text-right cp-actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cpPartnersTbody">
                        <tr>
                            <td colspan="8" class="text-center cp-loading-cell">
                                <i class="fi fi-rr-spinner-alt cp-spin"></i> Loading partners directory...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Partners Pagination -->
            <div class="cp-pagination-bar cp-hidden" id="cpPartnersPaginationWrap">
                <div class="cp-pagination-info" id="cpPartnersPaginationInfo"></div>
                <div class="cp-pagination-controls" id="cpPartnersPaginationControls"></div>
            </div>
        </div>

        <!-- Tab 2: Commission Transactions Ledger Panel -->
        <div class="cp-tab-pane cp-hidden" id="tabPaneLedger">
            <div class="cp-toolbar">
                <div class="cp-search-wrap">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" class="cp-search-input" id="cpSearchCommissions" placeholder="Search partner, referred site, or invoice...">
                    <button type="button" class="cp-search-clear-btn cp-hidden" id="cpClearCommissionsSearch" title="Clear Search">&times;</button>
                </div>
                <div class="cp-filters-wrap">
                    <select class="cp-select selectJS2 cp-select2" id="cpFilterCommissionStatus" data-placeholder="All Status" data-width="160px">
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
                            <th class="text-right cp-actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cpCommissionsTbody">
                        <tr>
                            <td colspan="8" class="text-center cp-loading-cell">
                                <i class="fi fi-rr-spinner-alt cp-spin"></i> Loading commission ledger...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal: Edit Partner Details -->
<div class="cp-modal-backdrop" id="modalEditPartner">
    <div class="cp-modal-box cp-modal-wide">
        <div class="cp-modal-header">
            <h3>Edit Referral Partner Details</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formEditPartner">
            <input type="hidden" id="editPartnerIdInput" value="0">
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

<!-- Modal: Add New Partner -->
<div class="cp-modal-backdrop" id="modalNewPartner">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Add New Referral Partner</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formNewPartner">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="newPartnerEmailInput">Email Address *</label>
                    <input type="email" class="cp-form-input" id="newPartnerEmailInput" required placeholder="partner@company.com">
                </div>
                <div class="cp-form-group">
                    <label for="newPartnerNameInput">Full / Business Name</label>
                    <input type="text" class="cp-form-input" id="newPartnerNameInput" placeholder="e.g. John Doe">
                </div>
                <div class="cp-form-group">
                    <label for="newPartnerMobileInput">Mobile Number</label>
                    <input type="text" class="cp-form-input" id="newPartnerMobileInput" placeholder="+91 9876543210">
                </div>
                <div class="cp-form-group">
                    <label for="newPartnerRateInput">Commission Rate (%) *</label>
                    <input type="number" step="0.1" class="cp-form-input" id="newPartnerRateInput" required value="10.0" placeholder="10.0">
                </div>
                <div class="cp-form-group">
                    <label for="newPartnerUpiInput">UPI ID or Bank Account (Optional)</label>
                    <input type="text" class="cp-form-input" id="newPartnerUpiInput" placeholder="e.g. partner@upi or 1234567890">
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Register Partner</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Record Manual Commission -->
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
                    <input type="text" class="cp-form-input" id="manualCommNotesInput" placeholder="e.g. Q4 Growth Campaign Commission">
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Record Accrual</button>
            </div>
        </form>
    </div>
</div>