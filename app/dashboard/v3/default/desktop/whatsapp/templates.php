<?php
/**
 * V3 WhatsApp Templates & Action Engine
 * Path: /app/dashboard/v3/default/desktop/whatsapp/templates.php
 * Workspace boundary: app/dashboard/v3/
 * Strict compliance: External CSS & external JS only, no inline CSS, no inline JS.
 */

$userRole = strtolower(isset($site_access_type) ? (string)$site_access_type : (defined('USER_TYPE') ? (string)USER_TYPE : ''));
$isAdmin = (isset($site_access_type) && ($site_access_type === 'administrator' || $site_access_type === 'admin')) || in_array($userRole, ['administrator', 'admin', 'superadmin', 'operator', 'owner'], true);
?>
<div class="package-page-wrapper wa-templates-page-wrapper" id="whatsappTemplatesApp" data-is-admin="<?php echo $isAdmin ? '1' : '0'; ?>">

    <!-- Top Header Banner -->
    <div class="package-header-bar wa-templates-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-whatsapp wa-templates-header-icon">
                <i class="fi fi-rr-document-signed"></i>
            </div>
            <div class="package-header-titles">
                <h2>WhatsApp Templates &amp; Action Triggers</h2>
                <p>Create personalized WhatsApp templates, automate customer touchpoints, and select active default templates for each CRM workflow event.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/whatsapp/" class="btn-v3-action wa-action-btn-secondary" title="Go to WhatsApp CRM Live Chat">
                <i class="fi fi-rr-comment-alt-middle"></i> <span>Live Inbox</span>
            </a>
            <button type="button" class="btn-v3-action wa-action-btn-secondary" id="btnOpenActionDefaultsModal" title="View &amp; Switch Active CRM Action Templates">
                <i class="fi fi-rr-settings-sliders"></i> <span>Action Defaults</span>
            </button>
            <button type="button" class="btn-v3-action wa-action-btn-secondary" id="btnOpenQuickSendModal" title="Test &amp; Send Template to a Mobile Number or Lead">
                <i class="fi fi-rr-paper-plane"></i> <span>Test / Send</span>
            </button>
            <button type="button" class="btn-v3-action wa-action-btn-primary" id="btnOpenCreateTemplateModal" title="Create New Custom WhatsApp Template">
                <i class="fi fi-rr-plus"></i> <span>Create Template</span>
            </button>
        </div>
    </div>

    <!-- KPI Statistics Grid -->
    <div class="wa-tpl-kpi-grid">
        <div class="wa-tpl-kpi-card">
            <div class="wa-tpl-kpi-icon emerald">
                <i class="fi fi-rr-document-signed"></i>
            </div>
            <div class="wa-tpl-kpi-info">
                <span class="wa-tpl-kpi-value" id="kpiTotalTemplates">0</span>
                <span class="wa-tpl-kpi-label">Total Templates</span>
            </div>
        </div>
        <div class="wa-tpl-kpi-card">
            <div class="wa-tpl-kpi-icon blue">
                <i class="fi fi-rr-bolt"></i>
            </div>
            <div class="wa-tpl-kpi-info">
                <span class="wa-tpl-kpi-value" id="kpiActiveActions">8 / 8</span>
                <span class="wa-tpl-kpi-label">Active Action Triggers</span>
            </div>
        </div>
        <div class="wa-tpl-kpi-card">
            <div class="wa-tpl-kpi-icon purple">
                <i class="fi fi-rr-user-gear"></i>
            </div>
            <div class="wa-tpl-kpi-info">
                <span class="wa-tpl-kpi-value" id="kpiCustomTemplates">0</span>
                <span class="wa-tpl-kpi-label">Custom Created</span>
            </div>
        </div>
        <div class="wa-tpl-kpi-card">
            <div class="wa-tpl-kpi-icon amber">
                <i class="fi fi-rr-paper-plane-top"></i>
            </div>
            <div class="wa-tpl-kpi-info">
                <span class="wa-tpl-kpi-value" id="kpiTotalDispatches">0</span>
                <span class="wa-tpl-kpi-label">Total Sent via Actions</span>
            </div>
        </div>
    </div>

    <!-- Active Action Triggers Matrix Strip -->
    <div class="wa-action-matrix-strip">
        <div class="wa-matrix-strip-header">
            <div class="wa-matrix-strip-title">
                <i class="fi fi-rr-check-circle"></i>
                <span>Active CRM Action Templates</span>
            </div>
            <span class="wa-matrix-strip-hint">Click any action below to filter templates or switch its active default</span>
        </div>
        <div class="wa-matrix-pills-scroll" id="waActionMatrixPills">
            <!-- Populated dynamically via JS -->
            <div class="wa-matrix-pill-skeleton"></div>
            <div class="wa-matrix-pill-skeleton"></div>
            <div class="wa-matrix-pill-skeleton"></div>
            <div class="wa-matrix-pill-skeleton"></div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="wa-tpl-toolbar-row">
        <!-- Search bar -->
        <div class="wa-tpl-search-wrap">
            <i class="fi fi-rr-search"></i>
            <input type="text" id="tplSearchInput" placeholder="Search templates by title, keywords, or content..." class="wa-tpl-search-input" autocomplete="off">
            <button type="button" class="btn-wa-clear-search is-hidden" id="btnTplClearSearch" aria-label="Clear Search">
                <i class="fi fi-rr-cross-small"></i>
            </button>
        </div>

        <!-- Category & Action Filter Tabs -->
        <div class="wa-tpl-tabs-wrap">
            <div class="wa-tpl-tabs-scroll" id="waCategoryFilterTabs">
                <button type="button" class="wa-tpl-tab-btn active" data-action-trigger="all">All Templates</button>
                <button type="button" class="wa-tpl-tab-btn" data-action-trigger="lead_welcome">Lead Welcome</button>
                <button type="button" class="wa-tpl-tab-btn" data-action-trigger="itinerary_share">Itinerary Proposal</button>
                <button type="button" class="wa-tpl-tab-btn" data-action-trigger="followup_reminder">Follow-up</button>
                <button type="button" class="wa-tpl-tab-btn" data-action-trigger="booking_confirmed">Booking Confirmed</button>
                <button type="button" class="wa-tpl-tab-btn" data-action-trigger="payment_request">Payment Link</button>
                <button type="button" class="wa-tpl-tab-btn" data-action-trigger="trip_voucher">Tour Voucher</button>
                <button type="button" class="wa-tpl-tab-btn" data-action-trigger="feedback_review">Review &amp; Feedback</button>
                <button type="button" class="wa-tpl-tab-btn" data-action-trigger="manual_quick_reply">Quick Chat</button>
            </div>
        </div>

        <!-- Filter dropdown / switches -->
        <div class="wa-tpl-filter-controls">
            <div class="wa-tpl-segmented-control">
                <button type="button" class="wa-seg-btn active" data-scope="all">All</button>
                <button type="button" class="wa-seg-btn" data-scope="defaults_only">Active Defaults</button>
                <button type="button" class="wa-seg-btn" data-scope="custom_only">Custom</button>
            </div>
            <button type="button" class="btn-v3-action wa-action-btn-secondary" id="btnRefreshTemplates" title="Refresh templates">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Templates Cards Container -->
    <div class="wa-templates-cards-grid" id="waTemplatesGrid">
        <!-- Rendered dynamically via whatsapp_templates.js -->
        <div class="wa-tpl-loading-state">
            <i class="fi fi-rr-spinner wa-spin"></i>
            <span>Loading WhatsApp templates...</span>
        </div>
    </div>

    <!-- Modal 1: Create / Edit Template -->
    <div class="wa-modal-overlay is-hidden" id="waTemplateModal" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="wa-modal-dialog wa-modal-dialog-large">
            <div class="wa-modal-header">
                <div class="wa-modal-header-title">
                    <span class="wa-modal-icon-badge emerald"><i class="fi fi-brands-whatsapp"></i></span>
                    <div>
                        <h3 id="waTemplateModalHeading">Create WhatsApp Template</h3>
                        <p class="wa-modal-subtitle">Design high-converting messages with dynamic tags and live WhatsApp phone rendering.</p>
                    </div>
                </div>
                <button type="button" class="btn-wa-modal-close" id="btnCloseTemplateModal" aria-label="Close modal">
                    <i class="fi fi-rr-cross"></i>
                </button>
            </div>
            <form id="waTemplateForm" class="wa-modal-body wa-tpl-modal-grid">
                <input type="hidden" id="formTemplateId" value="0">

                <!-- Left Column: Form Fields -->
                <div class="wa-tpl-form-col">
                    <div class="v3-form-group">
                        <label for="formTemplateName" class="v3-form-label">Template Name <span class="required">*</span></label>
                        <div class="v3-input-icon-wrap wa-full-input-wrap">
                            <i class="fi fi-rr-pencil input-icon"></i>
                            <input type="text" id="formTemplateName" class="v3-form-input wa-clean-input" placeholder="e.g. VIP Itinerary Proposal or Friendly 48h Follow-up" required>
                        </div>
                    </div>

                    <div class="v3-form-group">
                        <label for="formActionTrigger" class="v3-form-label">CRM Action Trigger <span class="required">*</span></label>
                        <div class="wa-action-select-wrapper">
                            <i class="fi fi-rr-bolt wa-action-select-icon"></i>
                            <select id="formActionTrigger" class="wa-custom-select" required>
                                <option value="lead_welcome">🎯 New Lead Welcome (Inquiry Acknowledgment)</option>
                                <option value="itinerary_share">📋 Itinerary Proposal (Quotation &amp; Day-wise Plan)</option>
                                <option value="followup_reminder">🔔 Follow-up Reminder (Check-in &amp; Booking Push)</option>
                                <option value="booking_confirmed">🎉 Booking Confirmed (Celebration &amp; Receipt)</option>
                                <option value="payment_request">💳 Payment Link Request (Deposit / Balance)</option>
                                <option value="trip_voucher">📄 Tour Voucher Issued (Pre-departure Documents)</option>
                                <option value="feedback_review">⭐ Post-Trip Review (Feedback &amp; Rating)</option>
                                <option value="manual_quick_reply">💬 Live Chat Quick Reply (Manual Chat Concierge)</option>
                            </select>
                            <i class="fi fi-rr-angle-small-down wa-action-select-chevron"></i>
                        </div>
                        <div class="wa-action-info-card" id="waActionInfoCard">
                            <span class="wa-action-info-icon" id="waActionInfoIcon"><i class="fi fi-rr-bullseye-arrow"></i></span>
                            <div class="wa-action-info-text">
                                <strong id="waActionInfoTitle">New Lead Welcome</strong>
                                <p id="waActionInfoDesc">Triggered automatically when a new inquiry arrives via Web Form, Facebook, or Manual Lead ingestion.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Variable Inserter Tag Toolbar (Categorized) -->
                    <div class="wa-tpl-vars-box">
                        <div class="wa-tpl-vars-header">
                            <span class="wa-vars-title"><i class="fi fi-rr-magic-wand"></i> Click Placeholder Tag to Insert:</span>
                        </div>
                        <div class="wa-tpl-vars-grouped" id="waTemplateVarsTags">
                            <div class="wa-var-group">
                                <span class="wa-var-group-label">👤 Traveler:</span>
                                <button type="button" class="wa-var-pill" data-var="{customer_name}">+ {customer_name}</button>
                                <button type="button" class="wa-var-pill" data-var="{lead_id}">+ {lead_id}</button>
                            </div>
                            <div class="wa-var-group">
                                <span class="wa-var-group-label">🌴 Tour Details:</span>
                                <button type="button" class="wa-var-pill" data-var="{destination}">+ {destination}</button>
                                <button type="button" class="wa-var-pill" data-var="{dates}">+ {dates}</button>
                                <button type="button" class="wa-var-pill" data-var="{itinerary_link}">+ {itinerary_link}</button>
                            </div>
                            <div class="wa-var-group">
                                <span class="wa-var-group-label">💳 Payment &amp; Review:</span>
                                <button type="button" class="wa-var-pill" data-var="{payment_link}">+ {payment_link}</button>
                                <button type="button" class="wa-var-pill" data-var="{review_link}">+ {review_link}</button>
                            </div>
                            <div class="wa-var-group">
                                <span class="wa-var-group-label">🏢 Brand &amp; Agent:</span>
                                <button type="button" class="wa-var-pill" data-var="{company_name}">+ {company_name}</button>
                                <button type="button" class="wa-var-pill" data-var="{agent_name}">+ {agent_name}</button>
                                <button type="button" class="wa-var-pill" data-var="{agent_mobile}">+ {agent_mobile}</button>
                            </div>
                        </div>
                    </div>

                    <!-- Content Textarea with Formatting & Emoji Toolbar -->
                    <div class="v3-form-group wa-textarea-group">
                        <div class="wa-textarea-label-row">
                            <label for="formTemplateContent" class="v3-form-label">WhatsApp Message Copy <span class="required">*</span></label>
                            <span class="wa-char-counter" id="formCharCounter">0 characters</span>
                        </div>
                        <div class="wa-composer-tools">
                            <div class="wa-format-buttons">
                                <button type="button" class="wa-fmt-btn" data-fmt="bold" title="Bold (*text*)"><b>B</b></button>
                                <button type="button" class="wa-fmt-btn" data-fmt="italic" title="Italic (_text_)"><i>I</i></button>
                                <button type="button" class="wa-fmt-btn" data-fmt="strike" title="Strikethrough (~text~)"><s>S</s></button>
                                <button type="button" class="wa-fmt-btn" data-fmt="code" title="Monospace (```text```)"><code>&lt;&gt;</code></button>
                            </div>
                            <div class="wa-emoji-quick-bar">
                                <button type="button" class="wa-emoji-btn" data-emoji="🙏">🙏</button>
                                <button type="button" class="wa-emoji-btn" data-emoji="✨">✨</button>
                                <button type="button" class="wa-emoji-btn" data-emoji="🌴">🌴</button>
                                <button type="button" class="wa-emoji-btn" data-emoji="✈️">✈️</button>
                                <button type="button" class="wa-emoji-btn" data-emoji="🎒">🎒</button>
                                <button type="button" class="wa-emoji-btn" data-emoji="🚗">🚗</button>
                                <button type="button" class="wa-emoji-btn" data-emoji="⭐">⭐</button>
                                <button type="button" class="wa-emoji-btn" data-emoji="💳">💳</button>
                                <button type="button" class="wa-emoji-btn" data-emoji="📋">📋</button>
                                <button type="button" class="wa-emoji-btn" data-emoji="📞">📞</button>
                                <button type="button" class="wa-emoji-btn" data-emoji="✅">✅</button>
                            </div>
                        </div>
                        <textarea id="formTemplateContent" class="wa-tpl-textarea" rows="8" placeholder="Type your WhatsApp message copy here. Use dynamic tags like {customer_name} and {destination}..." required></textarea>
                        <div class="wa-tpl-formatting-help">
                            <span><i class="fi fi-rr-info"></i> WhatsApp syntax: <b>*bold*</b>, <i>_italic_</i>, <s>~strike~</s>, <code>```monospace```</code></span>
                        </div>
                    </div>

                    <!-- Set as Active Default Toggle -->
                    <div class="wa-tpl-active-switch-box">
                        <div class="wa-active-switch-text">
                            <strong>Set as Active Default for this Action</strong>
                            <p>When enabled, this template will be automatically used whenever this CRM action triggers across the platform.</p>
                        </div>
                        <label class="v3-toggle-switch">
                            <input type="checkbox" id="formIsActionActive" value="1">
                            <span class="v3-toggle-slider emerald"></span>
                        </label>
                    </div>
                </div>

                <!-- Right Column: Real-time Live Phone Mockup Preview -->
                <div class="wa-tpl-preview-col">
                    <div class="wa-preview-panel-title">
                        <i class="fi fi-rr-mobile-notch"></i>
                        <span>Live WhatsApp Preview</span>
                        <span class="wa-preview-live-pill"><span class="wa-live-dot"></span> Live</span>
                    </div>
                    <div class="wa-mock-phone">
                        <!-- iPhone Status Bar -->
                        <div class="wa-iphone-status-bar">
                            <span class="wa-iphone-time">9:41</span>
                            <div class="wa-dynamic-island">
                                <span class="wa-island-camera"></span>
                            </div>
                            <div class="wa-iphone-icons">
                                <i class="fi fi-rr-signal-alt-2"></i>
                                <i class="fi fi-rr-wifi-alt"></i>
                                <i class="fi fi-rr-battery"></i>
                            </div>
                        </div>

                        <!-- WhatsApp App Header -->
                        <div class="wa-mock-phone-header">
                            <div class="wa-mock-header-left">
                                <i class="fi fi-rr-angle-left wa-mock-back-btn"></i>
                                <div class="wa-mock-phone-avatar">
                                    <i class="fi fi-rr-user"></i>
                                    <span class="wa-avatar-online-dot"></span>
                                </div>
                                <div class="wa-mock-phone-meta">
                                    <span class="wa-mock-phone-name">Rahul Sharma</span>
                                    <span class="wa-mock-phone-sub">online</span>
                                </div>
                            </div>
                            <div class="wa-mock-header-actions">
                                <i class="fi fi-rr-video-camera"></i>
                                <i class="fi fi-rr-phone-call"></i>
                                <i class="fi fi-rr-menu-dots-vertical"></i>
                            </div>
                        </div>

                        <!-- Chat Messages Stream -->
                        <div class="wa-mock-phone-chat-bg">
                            <div class="wa-chat-date-pill">TODAY</div>
                            <div class="wa-chat-encryption-notice">
                                <i class="fi fi-rr-lock"></i> Messages are end-to-end encrypted. No one outside of this chat can read them.
                            </div>
                            <div class="wa-mock-bubble-outgoing">
                                <div class="wa-mock-bubble-tail"></div>
                                <div class="wa-mock-bubble-text" id="waLivePreviewBubbleText">
                                    Type message to preview...
                                </div>
                                <div class="wa-mock-bubble-footer">
                                    <span class="wa-mock-time">12:30 PM</span>
                                    <span class="wa-mock-ticks"><i class="fi fi-rr-check-double"></i></span>
                                </div>
                            </div>
                        </div>

                        <!-- WhatsApp Mock Composer Bar -->
                        <div class="wa-mock-composer-bar">
                            <div class="wa-mock-input-pill">
                                <i class="fi fi-rr-smile wa-mock-composer-icon"></i>
                                <span class="wa-mock-input-placeholder">Message</span>
                                <i class="fi fi-rr-clip wa-mock-composer-icon"></i>
                                <i class="fi fi-rr-camera wa-mock-composer-icon"></i>
                            </div>
                            <div class="wa-mock-mic-btn">
                                <i class="fi fi-rr-microphone"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="wa-modal-footer">
                <button type="button" class="btn-wa-secondary" id="btnCancelTemplateModal">Cancel</button>
                <button type="submit" form="waTemplateForm" class="btn-wa-primary" id="btnSaveTemplateSubmit">
                    <i class="fi fi-rr-disk"></i> <span>Save Template</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 2: Quick Test & Send Template -->
    <div class="wa-modal-overlay is-hidden" id="waSendModal" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="wa-modal-dialog">
            <div class="wa-modal-header">
                <div class="wa-modal-header-title">
                    <span class="wa-modal-icon-badge emerald"><i class="fi fi-rr-paper-plane"></i></span>
                    <h3>Test &amp; Send WhatsApp Template</h3>
                </div>
                <button type="button" class="btn-wa-modal-close" id="btnCloseSendModal" aria-label="Close modal">
                    <i class="fi fi-rr-cross"></i>
                </button>
            </div>
            <form id="waSendForm" class="wa-modal-body">
                <input type="hidden" id="sendTemplateId" value="0">

                <div class="v3-form-group">
                    <label for="sendTemplateSelector" class="v3-form-label">Selected Template</label>
                    <div class="wa-action-select-wrapper">
                        <i class="fi fi-rr-document-signed wa-action-select-icon"></i>
                        <select id="sendTemplateSelector" class="wa-custom-select"></select>
                        <i class="fi fi-rr-angle-small-down wa-action-select-chevron"></i>
                    </div>
                </div>

                <div class="v3-form-group wa-lead-search-group">
                    <label for="sendLeadSearchInput" class="v3-form-label">Pick CRM Lead (Auto-Fill &amp; Preview with Lead Data)</label>
                    <div class="v3-input-icon-wrap wa-full-input-wrap">
                        <i class="fi fi-rr-search input-icon"></i>
                        <input type="text" id="sendLeadSearchInput" class="v3-form-input wa-clean-input" placeholder="Search lead by name, phone, lead ID, or destination..." autocomplete="off">
                    </div>
                    <div class="wa-lead-search-dropdown is-hidden" id="waLeadSearchResults"></div>
                </div>

                <div class="v3-form-group">
                    <label for="sendRecipientPhone" class="v3-form-label">Recipient Mobile Number <span class="required">*</span></label>
                    <div class="v3-input-icon-wrap wa-full-input-wrap">
                        <i class="fi fi-rr-phone-call input-icon"></i>
                        <input type="tel" id="sendRecipientPhone" class="v3-form-input wa-clean-input" placeholder="e.g. +91 9876543210" required>
                    </div>
                    <span class="v3-field-hint">Include country code (e.g. 91 for India).</span>
                </div>

                <!-- Live Message Preview Box -->
                <div class="v3-form-group">
                    <label class="v3-form-label">Rendered Message with Contextual Data</label>
                    <div class="wa-rendered-preview-box" id="sendRenderedPreview">
                        Loading preview...
                    </div>
                </div>
            </form>
            <div class="wa-modal-footer">
                <button type="button" class="btn-wa-secondary" id="btnCancelSendModal">Close</button>
                <button type="submit" form="waSendForm" class="btn-wa-primary emerald" id="btnSendTemplateSubmit">
                    <i class="fi fi-rr-paper-plane"></i> <span>Send via WhatsApp</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 3: Action Defaults Matrix (Overview of all CRM Action triggers) -->
    <div class="wa-modal-overlay is-hidden" id="waActionDefaultsModal" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="wa-modal-dialog wa-modal-dialog-large">
            <div class="wa-modal-header">
                <div class="wa-modal-header-title">
                    <span class="wa-modal-icon-badge purple"><i class="fi fi-rr-settings-sliders"></i></span>
                    <h3>CRM Action Templates Matrix</h3>
                </div>
                <button type="button" class="btn-wa-modal-close" id="btnCloseActionDefaultsModal" aria-label="Close modal">
                    <i class="fi fi-rr-cross"></i>
                </button>
            </div>
            <div class="wa-modal-body">
                <p class="wa-matrix-intro-text">
                    Below are the standard CRM workflow actions. By default, our built-in curated templates are active for all actions. If you create custom templates, you can change the default action template here with one click.
                </p>
                <div class="wa-matrix-table-wrap">
                    <table class="wa-matrix-table">
                        <thead>
                            <tr>
                                <th>Action Event</th>
                                <th>Trigger Description</th>
                                <th>Active Default Template</th>
                                <th>Type</th>
                                <th class="text-right">Switch Template</th>
                            </tr>
                        </thead>
                        <tbody id="waMatrixTableBody">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="wa-modal-footer">
                <button type="button" class="btn-wa-secondary" id="btnCloseActionDefaultsBtn">Done</button>
            </div>
        </div>
    </div>

</div>
