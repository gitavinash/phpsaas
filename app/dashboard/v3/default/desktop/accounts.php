<?php

/**
 * Accounts & Transactions Ledger - Single Page Application View
 * Path: app/dashboard/v3/default/desktop/accounts.php
 */

$userRole = strtolower(trim(isset($site_access_type) ? $site_access_type : (defined('USER_TYPE') ? USER_TYPE : 'agent')));
$canManageAccounts = in_array($userRole, ['administrator', 'accountant', 'operations', 'manager']);

if (!$canManageAccounts) {
    echo '<div class="v3-unauthorized-box p-40 text-center">
            <i class="fi fi-rr-lock font-48 text-red"></i>
            <h3 class="mt-16">Access Restricted</h3>
            <p class="text-muted">You do not have administrative or accounting permissions to access the Accounts Ledger.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}
?>

<div class="package-page-wrapper accounts-page-wrapper" id="accountsApp">
    <!-- 1. Header Bar -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon emerald">
                <i class="fi fi-rr-wallet"></i>
            </div>
            <div class="package-header-titles">
                <h2>Accounts &amp; Transactions Ledger</h2>
                <p>Track collections, verify customer payments, and manage financial records across team members.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <!-- Quick Status Filter Pills in Header -->
            <div class="report-preset-btn-group" id="headerStatusGroup">
                <button type="button" class="btn-date-preset active" data-status="all">All</button>
                <button type="button" class="btn-date-preset" data-status="pending">Pending <span class="badge-count-amber" id="hdrPendingBadge">0</span></button>
                <button type="button" class="btn-date-preset" data-status="verified">Verified</button>
            </div>

            <!-- Refresh Button -->
            <button type="button" id="btnRefreshAccounts" class="btn-v3-action" title="Refresh transactions">
                <i class="fi fi-rr-refresh"></i> Refresh
            </button>

            <!-- Record Payment Button -->
            <!-- <button type="button" id="btnOpenAccountsPaymentModal" class="btn-v3-action btn-green">
                <i class="fi fi-rr-plus"></i> Record Payment
            </button> -->
        </div>
    </div>

    <!-- 2. KPI Metric Cards -->
    <div class="package-kpi-row">
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi fi-rr-check-circle"></i></div>
            <div class="kpi-meta">
                <p>Total Verified Collections</p>
                <h3 id="statTotalVerifiedAmount">₹ 0</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi amber">
            <div class="kpi-icon"><i class="fi fi-rr-clock"></i></div>
            <div class="kpi-meta">
                <p>Pending Verification</p>
                <h3 id="statTotalPendingAmount">₹ 0</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi fi-rr-calendar"></i></div>
            <div class="kpi-meta">
                <p>Today's Inflow</p>
                <h3 id="statTodayVerifiedAmount">₹ 0</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi purple">
            <div class="kpi-icon"><i class="fi fi-rr-receipt"></i></div>
            <div class="kpi-meta">
                <p>Total Transactions</p>
                <h3 id="statTotalCount">0</h3>
            </div>
        </div>
    </div>

    <!-- 3. Filter & Search Toolbar -->
    <div class="v3-accounts-filter-card">
        <div class="v3-accounts-filter-grid">
            <!-- Search -->
            <div class="filter-field-wrap search-wrap">
                <i class="fi fi-rr-search filter-icon"></i>
                <input type="text" id="accFilterSearch" class="v3-form-input" placeholder="Search customer, mobile, ref UTR, amount...">
                <button type="button" class="btn-clear-search" id="btnClearAccSearch"><i class="fi fi-rr-cross-small"></i></button>
            </div>

            <!-- Date Range Filter -->
            <div class="filter-field-wrap daterange-wrap">
                <div class="v3-daterange-picker-box" id="accDateRangePicker">
                    <i class="fi fi-rr-calendar filter-icon-static"></i>
                    <span id="accDateRangeLabel">All Dates</span>
                    <i class="fi fi-rr-angle-small-down filter-icon-caret"></i>
                </div>
                <input type="hidden" id="accFilterDateRange" value="">
            </div>

            <!-- Agent Filter -->
            <div class="filter-field-wrap">
                <select id="accFilterAgent" class="v3-form-input selectJS2">
                    <option value="">All Team Members</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="filter-field-wrap">
                <select id="accFilterStatus" class="v3-form-input selectJS2">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending Verification</option>
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <!-- Payment Method Filter -->
            <div class="filter-field-wrap">
                <select id="accFilterMethod" class="v3-form-input selectJS2">
                    <option value="">All Methods</option>
                    <option value="upi">UPI / QR</option>
                    <option value="bank">Bank Transfer / NEFT</option>
                    <option value="card">Credit / Debit Card</option>
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque / DD</option>
                </select>
            </div>

            <!-- Reset Filters -->
            <div class="filter-field-wrap action-wrap">
                <button type="button" id="btnResetAccFilters" class="btn-v3-action btn-outline" title="Reset All Filters">
                    <i class="fi fi-rr-rotate-left"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- 4. Transactions Data Table Container -->
    <div class="v3-payments-container-card mt-16">
        <div class="v3-table-responsive-wrapper">
            <table class="v3-payments-table" id="accountsTransactionsTable">
                <thead>
                    <tr>
                        <th>Date &amp; ID</th>
                        <th>Customer / Lead</th>
                        <th>Amount &amp; Method</th>
                        <th>Recorded By</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody id="accountsTableBody">
                    <?php for ($sk = 0; $sk < 5; $sk++) { ?>
                        <tr class="v3-skeleton-row">
                            <td>
                                <div class="v3-skeleton-line w-70 mb-5"></div>
                                <div class="v3-skeleton-line h-10 w-40"></div>
                            </td>
                            <td>
                                <div class="v3-skeleton-line w-80 mb-5"></div>
                                <div class="v3-skeleton-line h-10 w-50"></div>
                            </td>
                            <td>
                                <div class="v3-skeleton-line h-16 w-60 mb-1"></div>
                                <div class="v3-skeleton-pill w-65px"></div>
                            </td>
                            <td>
                                <div class="v3-skeleton-line w-70 mb-1"></div>
                                <div class="v3-skeleton-line h-10 w-40"></div>
                            </td>
                            <td>
                                <div class="v3-skeleton-pill w-80px"></div>
                            </td>
                            <td class="text-end">
                                <div class="v3-skeleton-btn w-90px ml-auto"></div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="v3-accounts-pagination-bar d-none" id="accountsPaginationBar">
            <div class="pagination-info" id="accountsPaginationInfo">Showing 0 of 0 transactions</div>
            <div class="pagination-actions">
                <button type="button" class="btn-v3-action btn-outline btn-sm" id="btnAccPrevPage"><i class="fi fi-rr-angle-left"></i> Previous</button>
                <span class="pagination-current-badge" id="accPageIndicator">Page 1 of 1</span>
                <button type="button" class="btn-v3-action btn-outline btn-sm" id="btnAccNextPage">Next <i class="fi fi-rr-angle-right"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1: Record Payment (Accounts Hub) -->
<div class="v3-modal-overlay" id="modalAccountsRecordPayment">
    <div class="v3-modal-dialog">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-payment">
                    <i class="fi fi-rr-wallet"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Record &amp; Verify Payment</h4>
                    <span class="v3-modal-subtitle">Log transaction for customer lead</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <div class="v3-form-group">
                <label class="v3-form-label">Lead / Customer ID <span class="req">*</span></label>
                <input type="number" id="accPayLeadId" class="v3-form-input" placeholder="e.g. 1042" required>
                <small class="v3-form-help-text">Enter the Lead ID to attach this payment to.</small>
            </div>
            <div class="v3-form-group">
                <label class="v3-form-label">Amount Received (₹) <span class="req">*</span></label>
                <input type="number" id="accPayAmount" class="v3-form-input" placeholder="0.00" step="0.01" required>
            </div>
            <div class="v3-form-row">
                <div class="v3-form-group flex-1">
                    <label class="v3-form-label">Payment Method</label>
                    <select id="accPayMethod" class="v3-form-input">
                        <option value="UPI / QR">UPI / QR Code</option>
                        <option value="Bank Transfer">Bank Transfer / NEFT / IMPS</option>
                        <option value="Credit/Debit Card">Credit / Debit Card</option>
                        <option value="Cash">Cash</option>
                        <option value="Cheque / DD">Cheque / Demand Draft</option>
                        <option value="Payment Gateway">Online Gateway</option>
                    </select>
                </div>
                <div class="v3-form-group flex-1">
                    <label class="v3-form-label">Payment Date</label>
                    <input type="date" id="accPayDate" class="v3-form-input" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="v3-form-group">
                <label class="v3-form-label">Transaction / UTR Reference Number</label>
                <input type="text" id="accPayRef" class="v3-form-input" placeholder="e.g. UTR84729103984">
            </div>
            <div class="v3-form-group">
                <label class="v3-form-label">Payment Receipt / Screenshot (Optional)</label>
                <div class="v3-upload-zone" id="accPayUploadZone">
                    <input type="file" id="accPayFile" accept="image/*,.pdf" class="d-none">
                    <div class="v3-upload-placeholder" id="accPayFilePlaceholder">
                        <i class="fi fi-rr-cloud-upload"></i>
                        <h4>Upload Receipt Proof</h4>
                        <p id="accPayFileText">Click to upload payment receipt or screenshot</p>
                    </div>
                    <div class="v3-upload-preview v3-hidden" id="accPayFilePreview"></div>
                </div>
                <button type="button" class="btn-clear-file v3-hidden" id="btnClearAccPayFile"><i class="fi fi-rr-trash"></i> Remove File</button>
            </div>
            <div class="v3-form-group">
                <label class="v3-form-label">Account Remarks / Notes</label>
                <textarea id="accPayNotes" class="v3-form-input" rows="2" placeholder="e.g. Verified against HDFC bank statement"></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-green" id="btnSubmitAccountsPayment">
                <i class="fi fi-rr-check"></i> Record &amp; Verify Payment
            </button>
        </div>
    </div>
</div>

<!-- Modal 2: View Payment Receipt (2-Column Lightbox) -->
<div class="v3-modal-overlay" id="modalAccountsViewReceipt">
    <div class="v3-modal-dialog v3-modal-dialog-receipt v3-modal-dialog-lg">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-payment">
                    <i class="fi fi-rr-receipt"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Payment Confirmation &amp; Receipt</h4>
                    <span class="v3-modal-subtitle" id="accReceiptModalSubtitle">Transaction Details</span>
                </div>
            </div>
            <div class="v3-modal-header-right-group">
                <div class="v3-receipt-nav-bar" id="accReceiptNavBar">
                    <button type="button" class="btn-receipt-nav" id="btnAccReceiptNavPrev" title="Previous Payment"><i class="fi fi-rr-angle-left"></i></button>
                    <span class="v3-receipt-nav-counter" id="accReceiptNavCounter">1 of 1</span>
                    <button type="button" class="btn-receipt-nav" id="btnAccReceiptNavNext" title="Next Payment"><i class="fi fi-rr-angle-right"></i></button>
                </div>
                <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
            </div>
        </div>
        <div class="v3-receipt-modal-content-wrap">
            <!-- Left Side: Image / Receipt Preview -->
            <div class="v3-receipt-col-left" id="accReceiptPreviewPane">
                <div class="v3-receipt-img-container" id="accReceiptImgContainer"></div>
            </div>
            <!-- Right Side: Structured Payment Information -->
            <div class="v3-receipt-col-right" id="accReceiptInfoPane">
                <div class="v3-receipt-amount-card">
                    <span class="v3-receipt-amount-label">Amount Received</span>
                    <h2 class="v3-receipt-amount-val" id="accReceiptModalAmount">₹ 0</h2>
                    <span class="v3-lead-badge badge-confirmed" id="accReceiptModalStatusBadge"><i class="fi fi-rr-check"></i> Verified</span>
                </div>

                <div class="v3-receipt-details-list">
                    <div class="v3-receipt-detail-item">
                        <span class="v3-receipt-detail-label"><i class="fi fi-rr-user"></i> Customer / Lead</span>
                        <span class="v3-receipt-detail-val" id="accReceiptModalCustomer">-</span>
                    </div>
                    <div class="v3-receipt-detail-item">
                        <span class="v3-receipt-detail-label"><i class="fi fi-rr-calendar"></i> Payment Date</span>
                        <span class="v3-receipt-detail-val" id="accReceiptModalDate">-</span>
                    </div>
                    <div class="v3-receipt-detail-item">
                        <span class="v3-receipt-detail-label"><i class="fi fi-rr-credit-card"></i> Payment Method</span>
                        <span class="v3-receipt-detail-val" id="accReceiptModalMethod">-</span>
                    </div>
                    <div class="v3-receipt-detail-item">
                        <span class="v3-receipt-detail-label"><i class="fi fi-rr-hashtag"></i> Transaction / UTR Ref</span>
                        <div class="v3-receipt-ref-row">
                            <span class="v3-receipt-detail-val monospace" id="accReceiptModalRef">-</span>
                            <button type="button" class="btn-copy-receipt-ref" id="btnCopyAccReceiptRef" title="Copy Reference"><i class="fi fi-rr-copy"></i></button>
                        </div>
                    </div>
                    <div class="v3-receipt-detail-item">
                        <span class="v3-receipt-detail-label"><i class="fi fi-rr-id-badge"></i> Recorded By</span>
                        <span class="v3-receipt-detail-val" id="accReceiptModalRecordedBy">-</span>
                    </div>
                    <div class="v3-receipt-detail-item full-width">
                        <span class="v3-receipt-detail-label"><i class="fi fi-rr-comment-alt"></i> Remarks / Description</span>
                        <p class="v3-receipt-notes-text" id="accReceiptModalNotes">-</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="v3-modal-footer">
            <a href="#" target="_blank" class="btn-v3-action btn-outline" id="accReceiptModalDownloadBtn" download>
                <i class="fi fi-rr-download"></i> Download Image
            </a>
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Close</button>
        </div>
    </div>
</div>

<!-- Modal 3: Reject Payment Confirmation -->
<div class="v3-modal-overlay" id="modalAccountsRejectPayment">
    <div class="v3-modal-dialog v3-modal-dialog-sm">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-danger">
                    <i class="fi fi-rr-cross-circle"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Reject Payment Record</h4>
                    <span class="v3-modal-subtitle">Mark transaction as rejected</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <input type="hidden" id="accRejectNoteId" value="0">
            <p class="v3-modal-prompt-text">
                Are you sure you want to reject this payment record? This transaction will not be credited towards the lead.
            </p>
            <div class="v3-form-group">
                <label class="v3-form-label">Rejection Reason</label>
                <textarea id="accRejectReason" class="v3-form-input" rows="2" placeholder="e.g. UTR does not match bank credit"></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-red" id="btnConfirmRejectPayment">
                <i class="fi fi-rr-cross"></i> Confirm Rejection
            </button>
        </div>
    </div>
</div>