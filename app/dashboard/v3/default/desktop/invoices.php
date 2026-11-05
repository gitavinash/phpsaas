<?php

/**
 * Blumox Control Panel: Dedicated Invoices & Billing Page
 * Path: /app/dashboard/v3/default/desktop/invoices.php
 * Route: /dashboard/invoices/
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

<div class="cp-wrapper cp-page-dedicated" id="controlPanelApp" data-page="invoices" data-user-role="<?php echo htmlspecialchars($userRole); ?>">

    <!-- Top Executive Header -->
    <div class="cp-header-bar">
        <div class="cp-header-left">
            <div class="cp-header-icon">
                <i class="fi fi-rr-receipt"></i>
            </div>
            <div class="cp-header-titles">
                <h1>Invoices &amp; Client Subscription Billing</h1>
                <p>
                    <span>SaaS Subscriptions, Custom Enterprise Billings, Invoices &amp; Payment Verification</span>
                    <span class="cp-badge-role <?php echo htmlspecialchars($userRole === 'on_ground_sales' ? 'ground' : $userRole); ?>">
                        <i class="fi fi-rr-shield-check"></i> <?php echo htmlspecialchars($displayRole); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="cp-header-actions">
            <?php if ($userRole === 'administrator' || $userRole === 'sales' || $userRole === 'on_ground_sales') { ?>
                <button type="button" class="cp-btn-primary" id="btnOpenCreateInvoiceModal">
                    <i class="fi fi-rr-receipt"></i> <span>Create Invoice</span>
                </button>
            <?php } ?>
            <button type="button" class="cp-btn-secondary" id="btnCpRefreshData" title="Refresh Invoices">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Invoices Main Card -->
    <div class="cp-card">
        <div class="cp-card-header">
            <div class="cp-card-title">
                <h3>Billing &amp; Invoices Ledger</h3>
                <p>Track client invoice statuses, overdue payments, and receipts</p>
            </div>
        </div>

        <div class="cp-toolbar">
            <div class="cp-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" class="cp-search-input" id="cpSearchInvoices" placeholder="Search invoice #, domain, or client...">
            </div>
            <div class="cp-filters-wrap">
                <select class="cp-select cp-select2" id="cpFilterInvoiceStatus" data-placeholder="All Status" data-width="160px">
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
                    <tr>
                        <td colspan="7" class="text-center cp-loading-cell">
                            <i class="fi fi-rr-spinner-alt cp-spin"></i> Loading invoices...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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
                    <select class="cp-form-select cp-select2 cp-modal-select2" id="invSiteSelect" required data-placeholder="Select Client Site">
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
                    <select class="cp-form-select cp-select2 cp-modal-select2" id="invPartnerSelect" data-placeholder="Select Partner">
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
            <!-- Rendered asynchronously by JS -->
        </div>
        <div class="cp-modal-footer">
            <button type="button" class="cp-btn-secondary" data-close-modal>Close</button>
            <button type="button" class="cp-btn-primary" id="btnInvoicePrint">
                <i class="fi fi-rr-print"></i> <span>Print Invoice</span>
            </button>
        </div>
    </div>
</div>
