<?php

/**
 * Transaction & Customer Financial Details View
 * Path: app/dashboard/v3/default/desktop/accounts/view.php
 */

$userRole = strtolower(trim(isset($site_access_type) ? $site_access_type : (defined('USER_TYPE') ? USER_TYPE : 'agent')));
$canManageAccounts = in_array($userRole, ['administrator', 'accountant', 'operations', 'manager']);

if (!$canManageAccounts) {
    echo '<div class="v3-unauthorized-box" style="padding: 40px; text-align: center;">
            <i class="fi fi-rr-lock" style="font-size: 48px; color: #ef4444;"></i>
            <h3 style="margin-top: 16px;">Access Restricted</h3>
            <p style="color: #64748b;">You do not have administrative or accounting permissions to view this transaction.</p>
            <a href="/dashboard/accounts/" class="btn-v3-action btn-blue" style="margin-top: 12px; display: inline-block;">Return to Accounts</a>
          </div>';
    return;
}

$rawTxId = isset($id) ? $id : (isset($_GET['id']) ? $_GET['id'] : '');
if (empty($rawTxId) && isset($urlStructure) && is_array($urlStructure)) {
    $lastSeg = end($urlStructure);
    if (!empty($lastSeg) && $lastSeg !== 'view') {
        $rawTxId = $lastSeg;
    }
}
?>

<div class="package-page-wrapper accounts-detail-wrapper" id="accountDetailApp" data-tx-id="<?php echo htmlspecialchars($rawTxId); ?>">
    <!-- 1. Header Navigation Bar -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <a href="/dashboard/accounts/" class="btn-back-link" title="Back to Accounts Ledger">
                <i class="fi fi-rr-arrow-left"></i>
            </a>
            <div class="package-header-titles">
                <h2>Transaction Details <span class="tx-id-badge" id="dtlTxIdBadge">#TX-...</span></h2>
                <p id="dtlTxSubheader">Customer payment record &amp; financial audit ledger</p>
            </div>
        </div>

        <div class="package-header-actions" id="dtlHeaderActions">
            <a href="/dashboard/accounts/" class="btn-v3-action btn-outline">
                <i class="fi fi-rr-arrow-left"></i> All Transactions
            </a>
            <a href="#" target="_blank" class="btn-v3-action btn-lead-crm-highlight" id="dtlOpenLeadBtn" style="display:none;">
                <i class="fi fi-rr-arrow-up-right-from-square"></i> Open Lead CRM
            </a>
            <span id="dtlApprovalActionWrap" style="display:inline-flex; gap: 8px;"></span>
        </div>
    </div>

    <!-- Loading Skeleton Placeholder -->
    <div id="accDetailLoadingWrap" style="margin-top: 18px;">
        <div class="acc-detail-grid">
            <div class="acc-detail-col-left">
                <div class="acc-card-box">
                    <div class="v3-skeleton-line h-22 w-40" style="margin-bottom: 12px;"></div>
                    <div class="v3-skeleton-line h-16 w-80" style="margin-bottom: 8px;"></div>
                    <div class="v3-skeleton-line h-16 w-60"></div>
                </div>
            </div>
            <div class="acc-detail-col-right">
                <div class="acc-card-box">
                    <div class="v3-skeleton-line h-22 w-50" style="margin-bottom: 12px;"></div>
                    <div class="v3-skeleton-line h-16 w-70"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic Content Container -->
    <div id="accDetailContentWrap" style="display:none; margin-top: 18px;">
        <div class="acc-detail-grid">
            
            <!-- Left Column: Transaction Breakdown & Receipt Proof -->
            <div class="acc-detail-col-left">
                
                <!-- Card 1: Amount & Status Highlight -->
                <div class="acc-card-box tx-highlight-card">
                    <div class="tx-highlight-header">
                        <div>
                            <span class="tx-highlight-label">Payment Amount</span>
                            <h1 class="tx-highlight-val" id="dtlAmount">₹ 0.00</h1>
                        </div>
                        <div id="dtlStatusWrap">
                            <span class="v3-status-pill status-verified" id="dtlStatusBadge"><i class="fi fi-rr-check"></i> Verified</span>
                        </div>
                    </div>

                    <div class="acc-details-meta-grid">
                        <div class="meta-item">
                            <span class="meta-label"><i class="fi fi-rr-calendar"></i> Payment Date</span>
                            <strong class="meta-val" id="dtlDate">-</strong>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label"><i class="fi fi-rr-credit-card"></i> Method</span>
                            <div class="meta-val" id="dtlMethod">-</div>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label"><i class="fi fi-rr-hashtag"></i> UTR / Reference</span>
                            <div class="ref-copy-row">
                                <span class="meta-val monospace-ref" id="dtlRef">-</span>
                                <button type="button" class="btn-copy-ref" id="btnCopyDtlRef" title="Copy Reference"><i class="fi fi-rr-copy"></i></button>
                            </div>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label"><i class="fi fi-rr-user"></i> Recorded By</span>
                            <span class="meta-val" id="dtlRecordedBy">-</span>
                        </div>
                        <div class="meta-item full-width" id="dtlVerifiedByRow" style="display:none;">
                            <span class="meta-label" style="color: #047857;"><i class="fi fi-rr-badge-check"></i> Verified Audit</span>
                            <span class="meta-val" id="dtlVerifiedBy" style="color: #065f46;">-</span>
                        </div>
                        <div class="meta-item full-width" id="dtlRejectionRow" style="display:none;">
                            <span class="meta-label" style="color: #dc2626;"><i class="fi fi-rr-cross-circle"></i> Rejection Details</span>
                            <p class="meta-val" id="dtlRejectionReason" style="color: #991b1b; margin: 0;">-</p>
                        </div>
                        <div class="meta-item full-width">
                            <span class="meta-label"><i class="fi fi-rr-comment-alt"></i> Remarks / Description</span>
                            <p class="meta-val notes-box" id="dtlNotes">No remarks provided.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Receipt Proof / Screenshot -->
                <div class="acc-card-box receipt-proof-card">
                    <div class="acc-card-header">
                        <div class="acc-card-title">
                            <i class="fi fi-rr-picture"></i> Receipt &amp; Proof of Payment
                        </div>
                        <a href="#" target="_blank" class="btn-v3-action btn-outline btn-xs" id="dtlDownloadReceiptBtn" download style="display:none;">
                            <i class="fi fi-rr-download"></i> Download
                        </a>
                    </div>
                    <div class="acc-receipt-view-area" id="dtlReceiptArea">
                        <div class="v3-receipt-empty-preview">
                            <i class="fi fi-rr-picture"></i>
                            <p>No receipt image attached for this transaction.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Customer Profile & Financial History -->
            <div class="acc-detail-col-right">
                
                <!-- Card 3: Customer Context -->
                <div class="acc-card-box customer-context-card">
                    <div class="acc-card-header">
                        <div class="acc-card-title">
                            <i class="fi fi-rr-user"></i> Customer Profile
                        </div>
                        <span class="v3-lead-num-chip" id="dtlLeadStatusBadge">Lead #...</span>
                    </div>
                    <div class="customer-info-box">
                        <div class="customer-avatar-box" id="dtlCustomerAvatar">C</div>
                        <div class="customer-details">
                            <h3 class="customer-name" id="dtlCustomerName">-</h3>
                            <div class="customer-contacts">
                                <span class="contact-item">
                                    <i class="fi fi-rr-phone-call"></i> <span id="dtlCustomerPhone">-</span>
                                    <a href="#" id="dtlCustWaLink" target="_blank" class="btn-quick-wa" title="WhatsApp Customer" style="display:none;"><svg viewBox="0 0 24 24" width="13" height="13" fill="#ffffff"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg></a>
                                    <a href="#" id="dtlCustCallLink" class="btn-quick-call" title="Call Customer" style="display:none;"><i class="fi fi-rr-phone-call"></i></a>
                                </span>
                                <span class="contact-item"><i class="fi fi-rr-envelope"></i> <span id="dtlCustomerEmail">-</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="customer-package-snippet">
                        <span class="pkg-snippet-label"><i class="fi fi-rr-suitcase"></i> Itinerary / Package</span>
                        <h4 class="pkg-snippet-title" id="dtlPackageTitle">Custom Tour Package</h4>
                    </div>
                </div>

                <!-- Card 4: Financial Summary & Outstanding Balance -->
                <div class="acc-card-box financial-summary-card">
                    <div class="acc-card-header">
                        <div class="acc-card-title">
                            <i class="fi fi-rr-calculator"></i> Booking Financial Ledger
                        </div>
                    </div>
                    <div class="acc-fin-grid">
                        <div class="acc-fin-card card-quoted">
                            <div class="acc-fin-card-top">
                                <div class="acc-fin-label-group">
                                    <span class="acc-fin-icon"><i class="fi fi-rr-box-alt"></i></span>
                                    <span class="acc-fin-label">Total Quoted</span>
                                </div>
                            </div>
                            <div class="acc-fin-card-body">
                                <h3 class="acc-fin-amount" id="dtlQuotePrice">₹ 0.00</h3>
                            </div>
                        </div>

                        <div class="acc-fin-card card-paid">
                            <div class="acc-fin-card-top">
                                <div class="acc-fin-label-group">
                                    <span class="acc-fin-icon"><i class="fi fi-rr-check-circle"></i></span>
                                    <span class="acc-fin-label">Total Paid</span>
                                </div>
                            </div>
                            <div class="acc-fin-card-body">
                                <h3 class="acc-fin-amount" id="dtlTotalPaid">₹ 0.00</h3>
                            </div>
                        </div>

                        <div class="acc-fin-card card-balance" id="dtlPendingCard">
                            <div class="acc-fin-card-top">
                                <div class="acc-fin-label-group">
                                    <span class="acc-fin-icon" id="dtlBalanceIcon"><i class="fi fi-rr-clock"></i></span>
                                    <span class="acc-fin-label" id="dtlBalanceLabel">Balance Due</span>
                                </div>
                                <span class="acc-fin-status-tag" id="dtlBalanceTag" style="display:none;"></span>
                            </div>
                            <div class="acc-fin-card-body">
                                <h3 class="acc-fin-amount" id="dtlPendingBalance">₹ 0.00</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5: All Transactions for this Customer -->
                <div class="acc-card-box history-list-card">
                    <div class="acc-card-header">
                        <div class="acc-card-title">
                            <i class="fi fi-rr-time-past"></i> Payment History
                        </div>
                        <span class="history-count" id="dtlHistoryCount">0 Records</span>
                    </div>
                    <div class="acc-history-table-wrap">
                        <table class="acc-history-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="dtlHistoryTableBody">
                                <!-- History rows -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="v3-modal-overlay" id="modalDtlRejectPayment">
    <div class="v3-modal-dialog" style="max-width: 480px;">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box" style="background: #fee2e2; color: #dc2626;">
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
            <p style="color: #475569; font-size: 13.5px; line-height: 1.5; margin: 0 0 12px 0;">
                Are you sure you want to reject this transaction? It will not be counted towards the customer's paid total.
            </p>
            <div class="v3-form-group">
                <label class="v3-form-label">Rejection Reason</label>
                <textarea id="dtlRejectReason" class="v3-form-input" rows="2" placeholder="e.g. UTR does not match bank statement"></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-red" id="btnConfirmDtlReject">
                <i class="fi fi-rr-cross"></i> Confirm Rejection
            </button>
        </div>
    </div>
</div>
