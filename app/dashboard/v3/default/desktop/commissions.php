<?php

/**
 * Blumox Control Panel: Dedicated Partner Commissions Page
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

<div class="cp-wrapper cp-page-dedicated" id="controlPanelApp" data-page="commissions" data-user-role="<?php echo htmlspecialchars($userRole); ?>">

    <!-- Top Executive Header -->
    <div class="cp-header-bar">
        <div class="cp-header-left">
            <div class="cp-header-icon">
                <i class="fi fi-rr-hand-holding-usd"></i>
            </div>
            <div class="cp-header-titles">
                <h1>Partner Referral Commissions</h1>
                <p>
                    <span>Partner Earnings Ledger, Commission Accruals &amp; Payout Reconciliation</span>
                    <span class="cp-badge-role <?php echo htmlspecialchars($userRole === 'on_ground_sales' ? 'ground' : $userRole); ?>">
                        <i class="fi fi-rr-shield-check"></i> <?php echo htmlspecialchars($displayRole); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="cp-header-actions">
            <?php if ($userRole === 'administrator') { ?>
                <button type="button" class="cp-btn-primary" id="btnOpenManualCommissionModal">
                    <i class="fi fi-rr-add"></i> <span>Record Commission</span>
                </button>
            <?php } ?>
            <button type="button" class="cp-btn-secondary" id="btnCpRefreshData" title="Refresh Commissions">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Commissions Main Card -->
    <div class="cp-card">
        <div class="cp-card-header">
            <div class="cp-card-title">
                <h3>Commission Ledger</h3>
                <p>Track referral earnings, approval statuses, and bank payout references</p>
            </div>
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
                    <tr>
                        <td colspan="8" class="text-center cp-loading-cell">
                            <i class="fi fi-rr-spinner-alt cp-spin"></i> Loading commissions ledger...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Record Manual Commission -->
<div class="cp-modal-backdrop" id="modalManualCommission">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Record Partner Commission</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formManualCommission">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="manualCommPartnerSelect">Partner Account *</label>
                    <select class="cp-form-select" id="manualCommPartnerSelect" required>
                        <option value="">Select Partner</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="manualCommSiteSelect">Referred Site *</label>
                    <select class="cp-form-select" id="manualCommSiteSelect" required>
                        <option value="">Select Site</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="manualCommAmountInput">Commission Amount (INR) *</label>
                    <input type="number" step="0.01" class="cp-form-input" id="manualCommAmountInput" required placeholder="e.g. 5000.00">
                </div>
                <div class="cp-form-group">
                    <label for="manualCommRateInput">Commission Rate (%)</label>
                    <input type="number" step="0.1" class="cp-form-input" id="manualCommRateInput" value="20.0">
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Record Accrual</button>
            </div>
        </form>
    </div>
</div>
