<?php
/**
 * Shared Operations Action Modals (V3)
 * Path: app/dashboard/v3/default/desktop/operations/modals-partial.php
 * 
 * Reusable modal dialogs for Operations workflows:
 *  1. modalOpsBookingRequest: Send Vendor / Property / Transport Booking Request
 *  2. modalOpsVoucher: Vendor Confirmation & Issue Travel Vouchers
 *  3. modalOpsInvoice: Generate Proforma / Tax Invoice
 *  4. modalOpsDriverDispatch: Chauffeur & Vehicle Dispatch
 */
?>
<!-- Modal 1: Send Vendor Booking Request -->
<div class="v3-modal-overlay" id="modalOpsBookingRequest">
    <div class="v3-modal-dialog v3-modal-dialog-xl">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-primary">
                    <i class="fi fi-rr-paper-plane"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Vendor Booking Request &amp; Live Preview</h4>
                    <span class="v3-modal-subtitle">Review, edit content in-line, and dispatch reservation request via WhatsApp or Email</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>

        <div class="v3-modal-body">
            <input type="hidden" id="bkgModalLeadId" value="">
            
            <!-- Lead Traveler Context Ribbon -->
            <div class="ops-bkg-traveler-ribbon">
                <div class="ops-bkg-traveler-left">
                    <i class="fi fi-rr-user text-blue"></i>
                    <span class="traveler-label">Traveler:</span>
                    <span id="bkgLeadGuestName" class="ops-bkg-traveler-name">Guest Traveler</span>
                    <span id="bkgLeadIdBadge" class="ops-bkg-tag id-tag">#--</span>
                </div>
                <div class="ops-bkg-traveler-tags">
                    <span id="bkgLeadDestBadge" class="ops-bkg-tag"><i class="fi fi-rr-map-marker"></i> Package</span>
                    <span id="bkgLeadPaxBadge" class="ops-bkg-tag"><i class="fi fi-rr-users"></i> Pax</span>
                </div>
            </div>

            <div class="ops-modal-grid">
                <!-- Left Column: Logistics Configuration -->
                <div class="ops-modal-col-left">
                    <h5 class="ops-modal-section-title"><i class="fi fi-rr-settings-sliders"></i> Reservation Details</h5>

                    <div class="v3-form-group">
                        <label class="v3-form-label">Vendor Service Type *</label>
                        <select id="bkgVendorType" class="v3-form-input selectJS2" data-placeholder="Choose Service Type">
                            <option value="hotel" selected>Hotel / Property</option>
                            <option value="transport">Transporter / Cab</option>
                            <option value="activity">Activity / Tour Guide</option>
                        </select>
                    </div>

                    <div class="v3-form-group" id="bkgSelectVendorWrap">
                        <label class="v3-form-label">Select Vendor / Property *</label>
                        <select id="bkgVendorSelect" class="v3-form-input selectJS2" data-placeholder="-- Choose Vendor / Property --">
                            <option value="">-- Choose Vendor --</option>
                        </select>
                    </div>

                    <div class="ops-form-row">
                        <div class="v3-form-group">
                            <label class="v3-form-label">Vendor Phone / WhatsApp *</label>
                            <div class="ops-input-icon-wrap">
                                <span class="ops-input-prefix"><i class="fi fi-brands-whatsapp text-emerald"></i></span>
                                <input type="text" id="bkgVendorPhone" class="v3-form-input" placeholder="e.g. 9876543210">
                            </div>
                        </div>
                        <div class="v3-form-group">
                            <label class="v3-form-label">Vendor Email</label>
                            <div class="ops-input-icon-wrap">
                                <span class="ops-input-prefix"><i class="fi fi-rr-envelope text-blue"></i></span>
                                <input type="email" id="bkgVendorEmail" class="v3-form-input" placeholder="e.g. booking@hotel.com">
                            </div>
                        </div>
                    </div>

                    <div class="ops-form-row">
                        <div class="v3-form-group">
                            <label class="v3-form-label">Check-In / Pickup Date *</label>
                            <div class="ops-input-icon-wrap">
                                <span class="ops-input-prefix"><i class="fi fi-rr-calendar"></i></span>
                                <input type="date" id="bkgCheckIn" class="v3-form-input">
                            </div>
                        </div>
                        <div class="v3-form-group">
                            <label class="v3-form-label">Check-Out / End Date *</label>
                            <div class="ops-input-icon-wrap">
                                <span class="ops-input-prefix"><i class="fi fi-rr-calendar"></i></span>
                                <input type="date" id="bkgCheckOut" class="v3-form-input">
                            </div>
                        </div>
                    </div>

                    <div class="ops-form-row">
                        <div class="v3-form-group">
                            <label class="v3-form-label">Room / Vehicle Type</label>
                            <div class="ops-input-icon-wrap">
                                <span class="ops-input-prefix"><i class="fi fi-rr-bed"></i></span>
                                <input type="text" id="bkgRoomCategory" class="v3-form-input" placeholder="e.g. Deluxe Room / Innova">
                            </div>
                        </div>
                        <div class="v3-form-group">
                            <label class="v3-form-label">Meal Plan</label>
                            <select id="bkgMealPlan" class="v3-form-input selectJS2" data-placeholder="Select Meal Plan">
                                <option value="CP" selected>CP (Breakfast only)</option>
                                <option value="MAP">MAP (Breakfast + Dinner)</option>
                                <option value="AP">AP (All Meals)</option>
                                <option value="EP">EP (Room only)</option>
                            </select>
                        </div>
                    </div>

                    <div class="v3-form-group">
                        <label class="v3-form-label">Special Remarks / Instructions</label>
                        <div class="ops-input-icon-wrap">
                            <span class="ops-input-prefix"><i class="fi fi-rr-comment-alt"></i></span>
                            <input type="text" id="bkgSpecialNotes" class="v3-form-input" placeholder="e.g. Early check-in requested, high floor">
                        </div>
                    </div>

                    <div class="v3-form-group">
                        <label class="v3-form-label">Dispatch Channel *</label>
                        <div class="ops-segmented-channel-toggle">
                            <label class="ops-channel-chip">
                                <input type="radio" name="bkgChannel" value="both" checked>
                                <div class="ops-chip-card">
                                    <span class="chip-icon"><i class="fi fi-rr-shuffle"></i></span>
                                    <div class="chip-text">
                                        <span class="chip-title">Both Channels</span>
                                        <span class="chip-subtitle">WhatsApp &amp; Email</span>
                                    </div>
                                    <span class="chip-badge">Best</span>
                                </div>
                            </label>
                            <label class="ops-channel-chip">
                                <input type="radio" name="bkgChannel" value="whatsapp">
                                <div class="ops-chip-card">
                                    <span class="chip-icon text-emerald"><i class="fi fi-brands-whatsapp"></i></span>
                                    <div class="chip-text">
                                        <span class="chip-title">WhatsApp</span>
                                        <span class="chip-subtitle">Direct Message</span>
                                    </div>
                                </div>
                            </label>
                            <label class="ops-channel-chip">
                                <input type="radio" name="bkgChannel" value="email">
                                <div class="ops-chip-card">
                                    <span class="chip-icon text-blue"><i class="fi fi-rr-envelope"></i></span>
                                    <div class="chip-text">
                                        <span class="chip-title">Email</span>
                                        <span class="chip-subtitle">Reservation Email</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Fulfillments & Delivery Toggles -->
                    <div class="ops-form-toggles-box">
                        <label class="ops-switch-item">
                            <div class="ops-switch-content">
                                <span class="ops-switch-title"><i class="fi fi-rr-document-signed"></i> Auto-Attach Itinerary Details</span>
                                <span class="ops-switch-desc">Includes stay duration, meal plan &amp; guest count</span>
                            </div>
                            <div class="ops-toggle-switch">
                                <input type="checkbox" id="bkgOptAttachGuest" checked>
                                <span class="ops-toggle-slider"></span>
                            </div>
                        </label>
                        <label class="ops-switch-item">
                            <div class="ops-switch-content">
                                <span class="ops-switch-title"><i class="fi fi-rr-bell-ring"></i> Operations Team Notification</span>
                                <span class="ops-switch-desc">Log dispatch alert in internal operations history</span>
                            </div>
                            <div class="ops-toggle-switch">
                                <input type="checkbox" id="bkgOptNotifyOps" checked>
                                <span class="ops-toggle-slider"></span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Right Column: Live Interactive Preview & In-line Editor -->
                <div class="ops-modal-col-right">
                    <div class="ops-preview-header">
                        <div class="ops-preview-tabs" id="bkgPreviewTabs">
                            <button type="button" class="btn-ops-preview-tab active" data-preview="whatsapp">
                                <i class="fi fi-brands-whatsapp text-emerald"></i> WhatsApp Preview &amp; Editor
                            </button>
                            <button type="button" class="btn-ops-preview-tab" data-preview="email">
                                <i class="fi fi-rr-envelope text-blue"></i> Email Preview &amp; Editor
                            </button>
                        </div>

                        <div class="ops-template-picker">
                            <label><i class="fi fi-rr-file-edit"></i> Template:</label>
                            <select id="bkgTemplatePicker" class="v3-form-input-sm selectJS2" data-placeholder="Choose Template">
                                <option value="">Default Template</option>
                            </select>
                        </div>
                    </div>

                    <!-- WhatsApp Preview Box -->
                    <div class="ops-preview-box active" id="previewBoxWhatsApp">
                        <div class="ops-wa-preview-card">
                            <div class="ops-wa-top-bar">
                                <div class="ops-wa-profile">
                                    <div class="ops-wa-avatar">
                                        <i class="fi fi-rr-building" id="bkgWaVendorAvatarIcon"></i>
                                    </div>
                                    <div class="ops-wa-meta">
                                        <span class="ops-wa-vendor-name" id="bkgWaLiveVendorName">Vendor Partner</span>
                                        <span class="ops-wa-vendor-status"><span class="ops-wa-status-dot"></span> WhatsApp Business &bull; Verified</span>
                                    </div>
                                </div>
                                <div class="ops-wa-badge-pill">
                                    <i class="fi fi-brands-whatsapp"></i> Live Chat Preview
                                </div>
                            </div>
                            
                            <div class="ops-wa-chat-canvas">
                                <div class="ops-wa-bubble">
                                    <div class="ops-wa-bubble-prompt">
                                        <span><i class="fi fi-rr-edit text-emerald"></i> Message Content (You can modify this text before sending):</span>
                                    </div>
                                    <textarea id="bkgWhatsAppContent" class="ops-wa-textarea" rows="11" placeholder="Type WhatsApp message here..."></textarea>
                                    <div class="ops-wa-bubble-footer">
                                        <span class="ops-wa-char-count" id="bkgWaCharCount">0 characters</span>
                                        <span class="ops-wa-time-meta">Live &bull; Ready <span class="ops-wa-ticks">&#10003;&#10003;</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ops-preview-actions-bar">
                            <button type="button" class="btn-ops-mini" id="btnResetWaPreview"><i class="fi fi-rr-refresh"></i> Reset Text</button>
                            <button type="button" class="btn-ops-mini" id="btnCopyWaPreview"><i class="fi fi-rr-copy"></i> Copy Text</button>
                        </div>
                    </div>

                    <!-- Email Preview Box -->
                    <div class="ops-preview-box" id="previewBoxEmail">
                        <div class="ops-email-preview-card">
                            <div class="ops-email-client-header">
                                <div class="ops-email-meta-row">
                                    <span class="ops-email-meta-label">To:</span>
                                    <span class="ops-email-recipient-chip" id="bkgEmailRecipientPreview">vendor@hotel.com</span>
                                </div>
                                <div class="ops-email-meta-row">
                                    <span class="ops-email-meta-label">Subject:</span>
                                    <input type="text" id="bkgEmailSubject" class="v3-form-input ops-email-subject-input" placeholder="Subject...">
                                </div>
                            </div>

                            <!-- Rich Text Toolbar -->
                            <div class="ops-email-editor-toolbar">
                                <div class="editor-tool-group">
                                    <button type="button" class="btn-editor-tool" data-cmd="bold" title="Bold"><i class="fi fi-rr-bold"></i></button>
                                    <button type="button" class="btn-editor-tool" data-cmd="italic" title="Italic"><i class="fi fi-rr-italic"></i></button>
                                    <button type="button" class="btn-editor-tool" data-cmd="underline" title="Underline"><i class="fi fi-rr-underline"></i></button>
                                </div>
                                <div class="editor-tool-separator"></div>
                                <div class="editor-tool-group">
                                    <button type="button" class="btn-editor-tool" data-cmd="insertUnorderedList" title="Bullet List"><i class="fi fi-rr-list"></i></button>
                                    <button type="button" class="btn-editor-tool" data-cmd="insertOrderedList" title="Numbered List"><i class="fi fi-rr-list-number"></i></button>
                                </div>
                                <div class="editor-mode-toggle">
                                    <button type="button" class="btn-mode-tab active" data-mode="visual" id="btnEmailModeVisual"><i class="fi fi-rr-eye"></i> Visual Preview</button>
                                    <button type="button" class="btn-mode-tab" data-mode="code" id="btnEmailModeCode"><i class="fi fi-rr-code-simple"></i> HTML Source</button>
                                </div>
                            </div>

                            <!-- Visual Rich HTML Viewport -->
                            <div class="ops-email-visual-viewport" id="bkgEmailVisualEditor" contenteditable="true">
                                <p>Loading preview...</p>
                            </div>

                            <!-- Raw HTML Code Editor (toggled via button) -->
                            <div class="ops-email-code-wrap" id="bkgEmailCodeWrap">
                                <textarea id="bkgEmailContent" class="ops-email-textarea" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="ops-preview-actions-bar">
                            <button type="button" class="btn-ops-mini" id="btnResetEmailPreview"><i class="fi fi-rr-refresh"></i> Reset Content</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="v3-modal-footer">
            <div class="ops-modal-footer-info">
                <i class="fi fi-rr-shield-check text-emerald"></i>
                <span>Direct Dispatch via Integrated WhatsApp &amp; Email Gateway</span>
            </div>
            <div class="ops-modal-footer-actions">
                <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
                <button type="button" class="btn-v3-action btn-blue" id="btnConfirmSendBookingRequest">
                    <i class="fi fi-rr-paper-plane"></i> Send Booking Request Now
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Confirmation Vouchers & Ref IDs Form -->
<div class="v3-modal-overlay" id="modalOpsVoucher">
    <div class="v3-modal-dialog v3-modal-dialog-lg">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-emerald">
                    <i class="fi fi-rr-ticket"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Vendor Confirmation &amp; Travel Vouchers</h4>
                    <span class="v3-modal-subtitle">Record hotel confirmation reference numbers, driver details, and issue vouchers</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>

        <div class="v3-modal-body">
            <input type="hidden" id="voucherLeadId" value="">

            <div class="ops-voucher-guest-banner" id="voucherGuestBanner">
                <!-- Traveler details populated dynamically -->
            </div>

            <div class="ops-voucher-payment-notice" id="voucherPaymentNotice">
                <!-- Payment verification warning or green badge -->
            </div>

            <h5 class="ops-modal-section-title"><i class="fi fi-rr-bed"></i> Booked Services &amp; Confirmation Numbers</h5>
            
            <div class="ops-voucher-items-list" id="voucherItemsList">
                <!-- Booking rows with confirmation ref inputs -->
            </div>

            <!-- Direct Supplementary / Manual Reference Numbers -->
            <div class="ops-voucher-direct-section" id="voucherDirectSection">
                <div class="ops-voucher-direct-header">
                    <span class="direct-title"><i class="fi fi-rr-edit text-emerald"></i> Quick / Direct Confirmation Details</span>
                    <span class="direct-subtitle">Enter property confirmation numbers or driver details directly for the voucher</span>
                </div>
                <div class="ops-form-row">
                    <div class="v3-form-group">
                        <label class="v3-form-label">Hotel / Resort Confirmation Ref</label>
                        <div class="ops-input-icon-wrap">
                            <span class="ops-input-prefix"><i class="fi fi-rr-building text-blue"></i></span>
                            <input type="text" id="voucherDirectHotelRef" class="v3-form-input" placeholder="e.g. HTL-2026-9812">
                        </div>
                    </div>
                    <div class="v3-form-group">
                        <label class="v3-form-label">Cab / Driver Contact &amp; Cab No.</label>
                        <div class="ops-input-icon-wrap">
                            <span class="ops-input-prefix"><i class="fi fi-rr-car text-emerald"></i></span>
                            <input type="text" id="voucherDirectDriverRef" class="v3-form-input" placeholder="e.g. Innova (Tariq - 9876543210)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dispatch Options Toggles -->
            <div class="ops-form-toggles-box">
                <label class="ops-switch-item">
                    <div class="ops-switch-content">
                        <span class="ops-switch-title"><i class="fi fi-brands-whatsapp text-emerald"></i> WhatsApp Voucher Alert to Traveler</span>
                        <span class="ops-switch-desc">Send official confirmation voucher link directly to traveler</span>
                    </div>
                    <div class="ops-toggle-switch">
                        <input type="checkbox" id="voucherOptSendWa" checked>
                        <span class="ops-toggle-slider"></span>
                    </div>
                </label>
                <label class="ops-switch-item">
                    <div class="ops-switch-content">
                        <span class="ops-switch-title"><i class="fi fi-rr-shield-check text-blue"></i> Allow Voucher Issuance Override</span>
                        <span class="ops-switch-desc">Manager authorization for post-trip or corporate payment terms</span>
                    </div>
                    <div class="ops-toggle-switch">
                        <input type="checkbox" id="voucherOptManagerOverride">
                        <span class="ops-toggle-slider"></span>
                    </div>
                </label>
            </div>
        </div>

        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Close</button>
            <button type="button" class="btn-v3-action btn-green" id="btnConfirmIssueVouchers">
                <i class="fi fi-rr-badge-check"></i> Save Reference IDs &amp; Issue Vouchers
            </button>
        </div>
    </div>
</div>

<!-- Modal 3: Invoice & Proforma Generator -->
<div class="v3-modal-overlay" id="modalOpsInvoice">
    <div class="v3-modal-dialog v3-modal-dialog-md">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-amber">
                    <i class="fi fi-rr-document"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Generate Invoice / Proforma</h4>
                    <span class="v3-modal-subtitle">Auto-calculated from itinerary quote with tax breakdown &amp; payments</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>

        <div class="v3-modal-body">
            <input type="hidden" id="invModalLeadId" value="">
            <input type="hidden" id="invGuestPhone" value="">
            <input type="hidden" id="invGuestName" value="">
            <input type="hidden" id="invRawPackageTotal" value="0">

            <div class="v3-form-group">
                <label class="v3-form-label">Document Type *</label>
                <div class="ops-segmented-invoice-toggle">
                    <label class="ops-doctype-chip">
                        <input type="radio" name="invDocType" value="proforma" checked>
                        <div class="ops-doctype-card">
                            <i class="fi fi-rr-document text-amber"></i>
                            <span>Proforma Invoice (Pre-trip)</span>
                        </div>
                    </label>
                    <label class="ops-doctype-chip">
                        <input type="radio" name="invDocType" value="tax_invoice">
                        <div class="ops-doctype-card">
                            <i class="fi fi-rr-receipt text-emerald"></i>
                            <span>Tax Invoice (Settled)</span>
                        </div>
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
            <div class="ops-inv-financials-card">
                <div class="ops-inv-row">
                    <span>Package Itinerary Total:</span>
                    <strong id="invPkgTotal">₹ 0</strong>
                </div>
                <div class="ops-inv-row text-muted">
                    <span>GST (5% Included):</span>
                    <span id="invGstAmount">₹ 0</span>
                </div>
                <!-- Editable Advance Payment Input -->
                <div class="ops-inv-row ops-inv-advance-input-row">
                    <label for="invAdvanceInput" class="ops-inv-advance-label">
                        <i class="fi fi-rr-wallet text-emerald"></i> Advance / Received Payment (₹):
                    </label>
                    <div class="ops-inv-advance-field-wrap">
                        <span class="currency-tag">₹</span>
                        <input type="number" id="invAdvanceInput" class="v3-form-input inv-editable-advance" placeholder="0" min="0" step="100">
                    </div>
                </div>
                <div class="ops-inv-row highlight">
                    <span>Outstanding Balance Due:</span>
                    <strong id="invBalanceDue" class="text-rose">₹ 0</strong>
                </div>
            </div>

            <!-- Agency Bank Details -->
            <div class="ops-inv-bank-box">
                <div class="bank-title"><i class="fi fi-rr-bank"></i> Remittance &amp; Bank Details:</div>
                <div class="bank-meta" id="invBankMeta">HDFC Bank | A/C: 50200088991122 | IFSC: HDFC0001234 | UPI: aleya@hdfcbank</div>
            </div>
        </div>

        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-amber" id="btnConfirmSendInvoice">
                <i class="fi fi-brands-whatsapp"></i> Dispatch Invoice via WhatsApp
            </button>
        </div>
    </div>
</div>

<!-- Modal 4: Driver & Vehicle Allocation Modal -->
<div class="v3-modal-overlay" id="modalOpsDriverDispatch">
    <div class="v3-modal-dialog v3-modal-dialog-md">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-primary">
                    <i class="fi fi-rr-car"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title">Chauffeur &amp; Vehicle Dispatch</h4>
                    <span class="v3-modal-subtitle">Assign driver and dispatch pre-trip WhatsApp details to traveler</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>

        <div class="v3-modal-body">
            <input type="hidden" id="dispatchLeadId" value="">

            <div class="ops-form-row">
                <div class="v3-form-group">
                    <label class="v3-form-label">Driver / Chauffeur Name *</label>
                    <input type="text" id="dispatchDriverName" class="v3-form-input" placeholder="e.g. Tariq Ahmad">
                </div>
                <div class="v3-form-group">
                    <label class="v3-form-label">Driver Mobile / WhatsApp *</label>
                    <input type="text" id="dispatchDriverPhone" class="v3-form-input" placeholder="e.g. 9876543210">
                </div>
            </div>

            <div class="ops-form-row">
                <div class="v3-form-group">
                    <label class="v3-form-label">Vehicle Model / Category</label>
                    <input type="text" id="dispatchVehicleType" class="v3-form-input" placeholder="e.g. Innova Crysta">
                </div>
                <div class="v3-form-group">
                    <label class="v3-form-label">Vehicle Registration Number *</label>
                    <input type="text" id="dispatchVehicleNo" class="v3-form-input" placeholder="e.g. JK-01-AB-1234">
                </div>
            </div>

            <div class="ops-form-row">
                <div class="v3-form-group">
                    <label class="v3-form-label">Scheduled Pickup Time</label>
                    <input type="text" id="dispatchPickupTime" class="v3-form-input" placeholder="e.g. 08:30 AM">
                </div>
                <div class="v3-form-group">
                    <label class="v3-form-label">Pickup Point / Airport / Hotel</label>
                    <input type="text" id="dispatchPickupLoc" class="v3-form-input" placeholder="e.g. Srinagar Airport Gate 2">
                </div>
            </div>
        </div>

        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-blue" id="btnConfirmDriverDispatch">
                <i class="fi fi-brands-whatsapp"></i> Dispatch Driver to Traveler WhatsApp
            </button>
        </div>
    </div>
</div>
