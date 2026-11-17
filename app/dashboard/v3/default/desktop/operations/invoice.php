<?php

/**
 * Operations Management - Generate Invoice & Proforma (Dedicated CTA)
 * Path: app/dashboard/v3/default/desktop/operations/invoice.php
 */

$userRole = '';
if (!empty($site_access_type)) {
    $userRole = strtolower(trim($site_access_type));
} elseif (defined('USER_TYPE') && !empty(USER_TYPE)) {
    $userRole = strtolower(trim(USER_TYPE));
} elseif (!empty($_SESSION['user_type'])) {
    $userRole = strtolower(trim($_SESSION['user_type']));
} elseif (!empty($_COOKIE['user_type'])) {
    $userRole = strtolower(trim(base64_decode($_COOKIE['user_type'])));
}

$canAccessOps = in_array($userRole, ['administrator', 'operations', 'super_administrator', 'admin', 'superadmin']);

if (!$canAccessOps) {
    echo '<div class="v3-unauthorized-box p-40 text-center">
            <i class="fi fi-rr-lock font-48 text-red"></i>
            <h3 class="mt-16">Access Restricted</h3>
            <p class="text-muted">You do not have operations permissions to generate invoices.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}

$prefilledLeadId = isset($_GET['lead_id']) ? htmlspecialchars(trim($_GET['lead_id']), ENT_QUOTES) : '';
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp" data-page="invoice" data-lead-id="<?php echo $prefilledLeadId; ?>">
    <!-- 1. Header Bar -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon amber">
                <i class="fi fi-rr-document"></i>
            </div>
            <div class="package-header-titles">
                <h2>Generate Invoice &amp; Proforma</h2>
                <p>Auto-calculated from itinerary quote with tax breakdown, verified advance payments, and bank remittance instructions.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <a href="/dashboard/operations/list/" class="btn-v3-action">
                <i class="fi fi-rr-arrow-left"></i> Confirmed Leads
            </a>
            <a href="/dashboard/operations/" class="btn-v3-action" title="Back to Operations Hub">
                <i class="fi fi-rr-apps"></i> Hub
            </a>
        </div>
    </div>

    <!-- 2. Main Invoice Form Panel -->
    <div class="package-card-panel p-24 max-w-700 mx-auto">
        <!-- Lead Lookup Box if not prefilled -->
        <div class="ops-lead-picker-box mb-20 <?php echo !empty($prefilledLeadId) ? 'd-none' : ''; ?>" id="opsInvoicePickerWrap">
            <div class="v3-form-group">
                <label class="v3-form-label">Enter Lead ID or Traveler Code *</label>
                <div class="d-flex gap-10">
                    <input type="text" id="invLeadIdManualInput" class="v3-form-input max-w-300" placeholder="e.g. 1024 or hash code" value="<?php echo $prefilledLeadId; ?>">
                    <button type="button" class="btn-v3-action btn-amber" id="btnLoadInvoiceLead">
                        <i class="fi fi-rr-search"></i> Calculate Invoice
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden fields used by operations.js -->
        <input type="hidden" id="invModalLeadId" value="<?php echo $prefilledLeadId; ?>">
        <input type="hidden" id="invGuestPhone" value="">
        <input type="hidden" id="invGuestName" value="">

        <div class="v3-form-group">
            <label class="v3-form-label">Document Type *</label>
            <div class="ops-radio-group">
                <label class="ops-radio-label">
                    <input type="radio" name="invDocType" value="proforma" checked>
                    <span>Proforma Invoice (Pre-trip)</span>
                </label>
                <label class="ops-radio-label">
                    <input type="radio" name="invDocType" value="tax_invoice">
                    <span>Tax Invoice (Settled)</span>
                </label>
            </div>
        </div>

        <div class="ops-form-row">
            <div class="v3-form-group">
                <label class="v3-form-label">Document Number</label>
                <input type="text" id="invNumber" class="v3-form-input" readonly>
            </div>
            <div class="v3-form-group">
                <label class="v3-form-label">Document Date</label>
                <input type="text" id="invDate" class="v3-form-input" readonly>
            </div>
        </div>

        <!-- Financials Summary Box -->
        <div class="ops-inv-financials-card mb-20">
            <div class="ops-inv-row">
                <span>Package Itinerary Total:</span>
                <strong id="invPkgTotal">₹ 0</strong>
            </div>
            <div class="ops-inv-row">
                <span>GST (5% Included):</span>
                <span id="invGstAmount">₹ 0</span>
            </div>
            <div class="ops-inv-row text-emerald">
                <span>Advance / Payments Verified:</span>
                <strong id="invAdvancePaid">- ₹ 0</strong>
            </div>
            <div class="ops-inv-row highlight">
                <span>Outstanding Balance Due:</span>
                <strong id="invBalanceDue" class="text-rose">₹ 0</strong>
            </div>
        </div>

        <!-- Agency Bank Details -->
        <div class="ops-inv-bank-box mb-24">
            <div class="bank-title"><i class="fi fi-rr-bank"></i> Remittance &amp; Bank Details:</div>
            <div class="bank-meta" id="invBankMeta">HDFC Bank | A/C: 50200088991122 | IFSC: HDFC0001234 | UPI: aleya@hdfcbank</div>
        </div>

        <div class="d-flex justify-content-between align-items-center pt-16 border-top">
            <a href="/dashboard/operations/list/" class="btn-v3-action btn-outline">Back to Confirmed List</a>
            <button type="button" class="btn-v3-action btn-amber font-14 py-10 px-20" id="btnConfirmSendInvoice">
                <i class="fi fi-brands-whatsapp"></i> Dispatch Invoice via WhatsApp
            </button>
        </div>
    </div>
</div>
