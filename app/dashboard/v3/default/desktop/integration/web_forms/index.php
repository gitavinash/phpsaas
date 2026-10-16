<?php

/**
 * V3 Landing Page Web Forms Plugin Dashboard Interface
 * Path: /app/dashboard/v3/default/desktop/integration/web_forms/index.php
 */
?>
<div class="package-page-wrapper integration-plugin-wrapper" id="pluginWebFormsApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-web-forms wf-kpi-icon indigo">
                <img src="/app/dashboard/v3/default/desktop/assets/images/integration/web_forms.svg" alt="Web Forms">
            </div>
            <div class="package-header-titles">
                <h2>Web Forms</h2>
                <p>Create and customize embeddable lead generation forms with unique URLs and seamless iframe integration for marketing funnels.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/integration/" class="btn-v3-action" data-tooltip="Back to All Integrations">
                <i class="fi fi-rr-arrow-left"></i> <span>All Integrations</span>
            </a>
            <button type="button" class="btn-v3-action btn-v3-primary" id="btnCreateNewForm">
                <i class="fi fi-rr-plus"></i> <span>Create Web Form</span>
            </button>
        </div>
    </div>

    <!-- KPI Statistics Row -->
    <div class="wf-kpi-grid">
        <div class="wf-kpi-card">
            <div class="wf-kpi-icon indigo">
                <i class="fi fi-rr-form"></i>
            </div>
            <div class="wf-kpi-info">
                <span class="wf-kpi-value" id="statTotalForms">0</span>
                <span class="wf-kpi-label">Total Web Forms</span>
            </div>
        </div>

        <div class="wf-kpi-card">
            <div class="wf-kpi-icon emerald">
                <i class="fi fi-rr-check-circle"></i>
            </div>
            <div class="wf-kpi-info">
                <span class="wf-kpi-value" id="statActiveForms">0</span>
                <span class="wf-kpi-label">Active Published Forms</span>
            </div>
        </div>

        <div class="wf-kpi-card">
            <div class="wf-kpi-icon amber">
                <i class="fi fi-rr-paper-plane"></i>
            </div>
            <div class="wf-kpi-info">
                <span class="wf-kpi-value" id="statTotalSubmissions">0</span>
                <span class="wf-kpi-label">Leads Ingested</span>
            </div>
        </div>

        <div class="wf-kpi-card">
            <div class="wf-kpi-icon purple">
                <i class="fi fi-rr-shield-check"></i>
            </div>
            <div class="wf-kpi-info">
                <span class="wf-kpi-value">reCAPTCHA v3</span>
                <span class="wf-kpi-label">Powered by Aleya.in</span>
            </div>
        </div>
    </div>

    <!-- Main Content Card & Forms List -->
    <div class="wf-content-card">
        <!-- Toolbar -->
        <div class="wf-toolbar">
            <div class="wf-toolbar-left">
                <div class="wf-search-box">
                    <i class="fi fi-rr-search"></i>
                    <input type="text" id="wfSearchInput" class="wf-search-input" placeholder="Search forms by title, slug...">
                </div>
                <select id="wfStatusFilter" class="wf-filter-select selectjs2 selectJS">
                    <option value="all">All Status</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
            </div>
            <div class="wf-toolbar-right">
                <button type="button" class="btn-v3-action" id="btnRefreshForms" title="Refresh List">
                    <i class="fi fi-rr-refresh"></i> <span>Refresh</span>
                </button>
            </div>
        </div>

        <!-- Forms Table -->
        <div class="wf-table-responsive" id="wfTableWrapper">
            <table class="wf-table" id="wfFormsTable">
                <thead>
                    <tr>
                        <th>Form Details</th>
                        <th>Unique URL / Embed</th>
                        <th>Active Fields</th>
                        <th>Submissions</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="wfTableBody">
                    <!-- Populated via AJAX -->
                </tbody>
            </table>

            <!-- Empty State -->
            <div class="wf-empty-state" id="wfEmptyState">
                <div class="wf-empty-icon">
                    <i class="fi fi-rr-browser"></i>
                </div>
                <h3>No Web Forms Created Yet</h3>
                <p>Create your first custom lead generation form to embed into your landing pages, ads, or partner websites.</p>
                <button type="button" class="btn-v3-action btn-v3-primary" id="btnEmptyCreateForm">
                    <i class="fi fi-rr-plus"></i> <span>Create Your First Form</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 1: Visual Form Builder Modal -->
    <div class="wf-modal-backdrop" id="wfBuilderModal">
        <div class="wf-modal-dialog">
            <div class="wf-modal-header">
                <div class="wf-modal-header-titles">
                    <div class="wf-modal-header-icon">
                        <i class="fi fi-rr-edit"></i>
                    </div>
                    <div>
                        <h3 id="builderModalTitle">Create Web Form</h3>
                        <p>Configure fields, styling, and tracking for your landing page embed form.</p>
                    </div>
                </div>
                <button type="button" class="wf-modal-close" id="btnCloseBuilderModal" aria-label="Close">
                    <i class="fi fi-rr-cross"></i>
                </button>
            </div>

            <div class="wf-modal-body">
                <form id="wfBuilderForm" autocomplete="off" novalidate>
                    <input type="hidden" id="builderFormId" value="0">
                    <input type="hidden" id="builderFormUid" value="">

                    <div class="wf-builder-split">
                        <!-- Left Pane: Configuration Controls -->
                        <div class="wf-builder-left">
                            <!-- Tabs -->
                            <div class="wf-builder-tabs">
                                <button type="button" class="wf-builder-tab active" data-tab="tab-general">
                                    <i class="fi fi-rr-settings"></i> <span>General</span>
                                </button>
                                <button type="button" class="wf-builder-tab" data-tab="tab-fields">
                                    <i class="fi fi-rr-list-check"></i> <span>Form Fields</span>
                                </button>
                                <button type="button" class="wf-builder-tab" data-tab="tab-style">
                                    <i class="fi fi-rr-palette"></i> <span>Style &amp; Theme</span>
                                </button>
                                <button type="button" class="wf-builder-tab" data-tab="tab-notifications">
                                    <i class="fi fi-rr-envelope"></i> <span>Email Notifications</span>
                                </button>
                                <button type="button" class="wf-builder-tab" data-tab="tab-tracking">
                                    <i class="fi fi-rr-bullseye-arrow"></i> <span>Tracking &amp; Post-Submit</span>
                                </button>
                            </div>

                            <!-- Pane 1: General Settings -->
                            <div class="wf-tab-pane active" id="tab-general">
                                <div class="wf-builder-section">
                                    <div class="wf-builder-section-title">
                                        <i class="fi fi-rr-info"></i> <span>Basic Form Information</span>
                                    </div>

                                    <div class="wf-form-row">
                                        <label for="formTitleInput" class="wf-builder-label">Form Title (Internal Reference) *</label>
                                        <input type="text" id="formTitleInput" class="wf-builder-input" placeholder="e.g. Kashmir Summer Special Landing Page" required>
                                    </div>

                                    <div class="wf-form-row">
                                        <div class="wf-label-with-hint">
                                            <label for="formSlugInput" class="wf-builder-label">Custom URL Slug (Optional)</label>
                                            <span class="wf-sub-label">Public link: <code>/form/{slug}/</code></span>
                                        </div>
                                        <div class="wf-slug-input-wrap">
                                            <span class="wf-slug-prefix">/form/</span>
                                            <input type="text" id="formSlugInput" class="wf-builder-input wf-slug-field" placeholder="e.g. kashmir-summer-2026">
                                            <span class="wf-slug-status-icon" id="slugStatusIcon"></span>
                                        </div>
                                        <div class="wf-slug-feedback" id="slugFeedback"></div>
                                    </div>

                                    <div class="wf-form-row">
                                        <div class="wf-row-header-flex">
                                            <label for="formHeadingInput" class="wf-builder-label">Display Heading</label>
                                            <label class="v3-toggle-switch wf-toggle-compact" title="Hide heading from public form">
                                                <input type="checkbox" id="formHideHeadingCheck">
                                                <span class="v3-toggle-slider"></span>
                                                <span class="v3-toggle-label wf-toggle-hide-text">Hide</span>
                                            </label>
                                        </div>
                                        <input type="text" id="formHeadingInput" class="wf-builder-input" placeholder="e.g. Get a Free Customized Quote" value="Get a Free Quote">
                                    </div>

                                    <div class="wf-form-row">
                                        <div class="wf-row-header-flex">
                                            <label for="formSubheadingInput" class="wf-builder-label">Display Subheading</label>
                                            <label class="v3-toggle-switch wf-toggle-compact" title="Hide subheading from public form">
                                                <input type="checkbox" id="formHideSubheadingCheck">
                                                <span class="v3-toggle-slider"></span>
                                                <span class="v3-toggle-label wf-toggle-hide-text">Hide</span>
                                            </label>
                                        </div>
                                        <input type="text" id="formSubheadingInput" class="wf-builder-input" placeholder="e.g. Plan your dream vacation with our destination experts" value="Plan your dream vacation with our travel specialists">
                                    </div>

                                    <div class="wf-form-row">
                                        <label for="formSubmitTextInput" class="wf-builder-label">Submit Button Text</label>
                                        <input type="text" id="formSubmitTextInput" class="wf-builder-input" placeholder="e.g. Get Free Quote" value="Get Free Quote">
                                    </div>
                                </div>
                            </div>

                            <!-- Pane 2: Form Fields -->
                            <div class="wf-tab-pane" id="tab-fields">
                                <div class="wf-builder-section">
                                    <div class="wf-fields-toolbar">
                                        <div class="wf-fields-toolbar-info">
                                            <div class="wf-builder-section-title">
                                                <i class="fi fi-rr-list-check"></i> <span>Form Fields Configuration</span>
                                            </div>
                                            <p class="wf-fields-help-text">
                                                Drag any field up or down using the grip handle to rearrange its order. <strong>Full Name</strong> and <strong>Mobile Number</strong> are mandatory default fields.
                                            </p>
                                        </div>
                                        <div class="wf-fields-toolbar-actions">
                                            <button type="button" class="btn-v3-action btn-v3-primary" id="btnOpenAddCustomField">
                                                <i class="fi fi-rr-plus"></i> <span>Add Custom Field</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Collapsible Custom Field Creator / Editor Card -->
                                    <div class="wf-custom-field-creator-card" id="wfCustomFieldCard">
                                        <div class="wf-cf-card-header">
                                            <div class="wf-cf-card-title">
                                                <i class="fi fi-rr-add"></i> <span id="cfFormTitle">Add Custom Field</span>
                                            </div>
                                            <button type="button" class="wf-cf-close-btn" id="btnCancelCustomField" title="Close">
                                                <i class="fi fi-rr-cross"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" id="cfEditIndex" value="-1">
                                        <div class="wf-cf-form-grid">
                                            <div class="wf-cf-col-6">
                                                <label for="cfLabelInput" class="wf-builder-label">Field Label <span class="wf-req">*</span></label>
                                                <input type="text" id="cfLabelInput" class="wf-builder-input" placeholder="e.g. Passport Number, Meal Preference">
                                            </div>
                                            <div class="wf-cf-col-6">
                                                <label for="cfKeyInput" class="wf-builder-label">Field Key / Parameter <span class="wf-req">*</span></label>
                                                <input type="text" id="cfKeyInput" class="wf-builder-input" placeholder="e.g. passport_number">
                                            </div>
                                            <div class="wf-cf-col-6">
                                                <label for="cfTypeSelect" class="wf-builder-label">Input Type</label>
                                                <select id="cfTypeSelect" class="wf-builder-select selectjs2 selectJS">
                                                    <option value="text">Single Line Text</option>
                                                    <option value="number">Number</option>
                                                    <option value="date">Date Picker</option>
                                                    <option value="select">Dropdown Select</option>
                                                    <option value="textarea">Multi-line Text Area</option>
                                                    <option value="tel">Phone / Mobile</option>
                                                    <option value="email">Email Address</option>
                                                </select>
                                            </div>
                                            <div class="wf-cf-col-6">
                                                <label for="cfWidthSelect" class="wf-builder-label">Form Column Width</label>
                                                <select id="cfWidthSelect" class="wf-builder-select selectjs2 selectJS">
                                                    <option value="12">1 in a row (100% Full Width)</option>
                                                    <option value="6">2 in a row (50% Half Width)</option>
                                                </select>
                                            </div>
                                            <div class="wf-cf-col-12 wf-cf-options-row" id="cfOptionsRow">
                                                <label for="cfOptionsInput" class="wf-builder-label">Dropdown Options <span class="wf-sub-label">(comma-separated)</span></label>
                                                <input type="text" id="cfOptionsInput" class="wf-builder-input" placeholder="e.g. Standard, Deluxe, Super Deluxe, Luxury">
                                            </div>
                                            <div class="wf-cf-col-12">
                                                <label for="cfPlaceholderInput" class="wf-builder-label">Input Placeholder Value</label>
                                                <input type="text" id="cfPlaceholderInput" class="wf-builder-input" placeholder="e.g. Enter placeholder text...">
                                                
                                                <!-- Smart Placeholder Improvement Suggestions -->
                                                <div class="wf-ph-suggestions-box" id="cfPhSuggestionsWrap">
                                                    <span class="wf-ph-sug-label">
                                                        <i class="fi fi-rr-magic-wand"></i> <span>Suggested &amp; Improved Placeholders (Click to apply):</span>
                                                    </span>
                                                    <div class="wf-ph-chips-list" id="cfPhChipsList">
                                                        <!-- Injected dynamically based on field type and key -->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="wf-cf-col-12 wf-cf-checkbox-row" id="cfReqWrap">
                                                <label class="wf-cf-checkbox-label">
                                                    <input type="checkbox" id="cfRequiredCheckbox">
                                                    <span>Make this field mandatory / required</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="wf-cf-actions">
                                            <button type="button" class="btn-v3-action" id="btnDismissCustomField">Cancel</button>
                                            <button type="button" class="btn-v3-action btn-v3-primary" id="btnSaveCustomField">
                                                <i class="fi fi-rr-check"></i> <span id="cfSaveBtnLabel">Add to Form</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Dynamic Fields Rows -->
                                    <div id="builderFieldsContainer">
                                        <!-- Injected by web_forms.js -->
                                    </div>

                                    <!-- Bottom Add Custom Field Action -->
                                    <div class="wf-add-field-bottom-wrap">
                                        <button type="button" class="wf-btn-add-field-dashed" id="btnOpenAddCustomFieldBottom">
                                            <i class="fi fi-rr-plus"></i> <span>Add Custom Field</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Pane 3: Style & Theme -->
                            <div class="wf-tab-pane" id="tab-style">
                                <div class="wf-builder-section">
                                    <div class="wf-builder-section-title">
                                        <i class="fi fi-rr-palette"></i> <span>Brand Color &amp; Appearance</span>
                                    </div>

                                    <div class="wf-form-row">
                                        <label for="formThemeColorInput" class="wf-builder-label">Primary Brand Accent Color</label>
                                        <div class="wf-color-picker-row">
                                            <input type="color" id="formThemeColorInput" value="#4f46e5" class="wf-color-input-thumb">
                                            <input type="text" id="formThemeColorHex" class="wf-builder-input wf-color-hex-input" value="#4f46e5">
                                        </div>

                                        <div class="wf-color-presets">
                                            <span class="wf-color-preset-label">Presets:</span>
                                            <button type="button" class="wf-color-preset-btn wf-color-indigo" data-color="#4f46e5" title="Indigo"></button>
                                            <button type="button" class="wf-color-preset-btn wf-color-emerald" data-color="#059669" title="Emerald"></button>
                                            <button type="button" class="wf-color-preset-btn wf-color-sky" data-color="#0284c7" title="Sky Blue"></button>
                                            <button type="button" class="wf-color-preset-btn wf-color-amber" data-color="#d97706" title="Amber"></button>
                                            <button type="button" class="wf-color-preset-btn wf-color-rose" data-color="#e11d48" title="Rose"></button>
                                            <button type="button" class="wf-color-preset-btn wf-color-slate" data-color="#0f172a" title="Slate Dark"></button>
                                        </div>
                                    </div>

                                    <div class="wf-form-row">
                                        <label for="formBgModeSelect" class="wf-builder-label">Background Container Mode</label>
                                        <select id="formBgModeSelect" class="wf-builder-select selectjs2 selectJS">
                                            <option value="card" selected>White Card (Clean border &amp; soft shadow)</option>
                                            <option value="transparent">Transparent (Seamless blending into page hero sections)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Pane: Email Notifications -->
                            <div class="wf-tab-pane" id="tab-notifications">
                                <div class="wf-builder-section">
                                    <div class="wf-builder-section-title">
                                        <i class="fi fi-rr-envelope"></i> <span>Enquiry Email Routing</span>
                                    </div>
                                    <p class="wf-builder-section-desc">
                                        Configure destination email addresses where new leads and enquiries submitted through this form will be delivered.
                                    </p>

                                    <div class="wf-form-row">
                                        <label for="formEmailToInput" class="wf-builder-label">Send Enquiry Email To (To)</label>
                                        <input type="text" id="formEmailToInput" class="wf-builder-input" placeholder="e.g. sales@yourdomain.com, leads@yourdomain.com">
                                        <span class="wf-input-help-text">Primary recipient(s). Multiple email addresses can be separated with a comma (,). Defaults to Primary Admin Alert Email.</span>
                                    </div>

                                    <div class="wf-form-row">
                                        <label for="formEmailCcInput" class="wf-builder-label">Carbon Copy (CC)</label>
                                        <input type="text" id="formEmailCcInput" class="wf-builder-input" placeholder="e.g. manager@yourdomain.com, team@yourdomain.com">
                                        <span class="wf-input-help-text">CC recipient(s) who will receive a copy of each new enquiry. Separate multiple emails with a comma (,).</span>
                                    </div>

                                    <div class="wf-form-row">
                                        <label for="formEmailBccInput" class="wf-builder-label">Blind Carbon Copy (BCC)</label>
                                        <input type="text" id="formEmailBccInput" class="wf-builder-input" placeholder="e.g. archive@yourdomain.com, audit@yourdomain.com">
                                        <span class="wf-input-help-text">Confidential BCC recipient(s). Separate multiple emails with a comma (,).</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pane 4: Tracking & Post-Submit -->
                            <div class="wf-tab-pane" id="tab-tracking">
                                <div class="wf-builder-section">
                                    <div class="wf-builder-section-title">
                                        <i class="fi fi-rr-bullseye-arrow"></i> <span>Campaign Attribution Tags</span>
                                    </div>

                                    <div class="wf-form-row">
                                        <label for="formTrackingSource" class="wf-builder-label">Default Source</label>
                                        <input type="text" id="formTrackingSource" class="wf-builder-input" value="landing_page" placeholder="e.g. landing_page">
                                    </div>

                                    <div class="wf-form-row">
                                        <label for="formTrackingMedium" class="wf-builder-label">Default Medium</label>
                                        <input type="text" id="formTrackingMedium" class="wf-builder-input" value="web_form" placeholder="e.g. web_form">
                                    </div>

                                    <div class="wf-form-row">
                                        <label for="formTrackingCampaign" class="wf-builder-label">Campaign Name</label>
                                        <input type="text" id="formTrackingCampaign" class="wf-builder-input" placeholder="e.g. summer_discount_2026">
                                    </div>

                                    <div class="wf-form-row">
                                        <label for="formTrackingSubject" class="wf-builder-label">Default Inquiry Subject</label>
                                        <input type="text" id="formTrackingSubject" class="wf-builder-input" placeholder="e.g. Website Landing Page Lead">
                                    </div>
                                </div>

                                <div class="wf-builder-section">
                                    <div class="wf-builder-section-title">
                                        <i class="fi fi-rr-paper-plane"></i> <span>Submission Actions</span>
                                    </div>

                                    <div class="wf-form-row">
                                        <label for="formRedirectUrlInput" class="wf-builder-label">Custom Thank-You Redirect URL (Optional)</label>
                                        <input type="url" id="formRedirectUrlInput" class="wf-builder-input" placeholder="https://yourwebsite.com/thank-you/">
                                        <span class="wf-input-help-text">Leave empty to show an in-place animated success message instead of redirecting.</span>
                                    </div>

                                    <div class="wf-form-row">
                                        <label for="formSuccessMsgInput" class="wf-builder-label">Success Message</label>
                                        <textarea id="formSuccessMsgInput" class="wf-builder-textarea" rows="2" placeholder="Thank you! We have received your inquiry and will contact you shortly.">Thank you! We have received your inquiry and will contact you shortly.</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Pane: Real-time Live Preview -->
                        <div class="wf-builder-right">
                            <div class="wf-preview-header">
                                <span class="wf-preview-title">Real-Time Interactive Preview</span>
                                <div class="wf-preview-viewport-btns">
                                    <button type="button" class="wf-btn-viewport active" id="btnPreviewDesktop" title="Desktop View">
                                        <i class="fi fi-rr-computer"></i>
                                    </button>
                                    <button type="button" class="wf-btn-viewport" id="btnPreviewMobile" title="Mobile View">
                                        <i class="fi fi-rr-mobile"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="wf-live-preview-box" id="livePreviewContainer">
                                <div class="wf-preview-rendered" id="livePreviewContent">
                                    <!-- Rendered dynamically by web_forms.js -->
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="wf-modal-footer">
                <button type="button" class="btn-v3-action" id="btnCancelBuilder">Cancel</button>
                <button type="button" class="btn-v3-action btn-v3-primary" id="btnSaveWebForm">
                    <i class="fi fi-rr-disk"></i> <span>Save Web Form</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 2: Embed Code & Iframe Generator Modal -->
    <div class="wf-modal-backdrop" id="wfEmbedModal">
        <div class="wf-modal-dialog wf-modal-dialog-sm">
            <div class="wf-modal-header">
                <div class="wf-modal-header-titles">
                    <div class="wf-modal-header-icon">
                        <i class="fi fi-rr-code-branch"></i>
                    </div>
                    <div>
                        <h3>Embed Landing Page Form</h3>
                        <p>Copy the iframe code or unique URL to integrate into any website or funnel.</p>
                    </div>
                </div>
                <button type="button" class="wf-modal-close" id="btnCloseEmbedModal" aria-label="Close">
                    <i class="fi fi-rr-cross"></i>
                </button>
            </div>

            <div class="wf-modal-body wf-embed-modal-body">
                <!-- Unique Public URL -->
                <label class="wf-builder-label">Direct Public URL</label>
                <div class="wf-embed-url-row">
                    <input type="text" id="embedPublicUrlInput" class="wf-builder-input wf-embed-url-input" readonly>
                    <button type="button" class="btn-v3-action" id="btnCopyPublicUrl" title="Copy URL">
                        <i class="fi fi-rr-copy"></i>
                    </button>
                    <a href="#" target="_blank" class="btn-v3-action" id="btnOpenPublicUrl" title="Open Form in New Tab">
                        <i class="fi fi-rr-arrow-up-right-from-square"></i>
                    </a>
                </div>

                <!-- Iframe Snippet -->
                <label class="wf-builder-label">Responsive &lt;iframe&gt; Integration Snippet</label>
                <div class="wf-snippet-box">
                    <button type="button" class="wf-btn-copy-snippet" id="btnCopyIframeSnippet">
                        <i class="fi fi-rr-copy"></i> <span>Copy Code</span>
                    </button>
                    <code class="wf-snippet-text" id="embedIframeSnippetText"></code>
                </div>

                <div class="wf-info-callout">
                    <i class="fi fi-rr-info"></i>
                    <div>
                        Paste this code anywhere inside your HTML, WordPress (Custom HTML block), Webflow, Wix, or landing page builder. Submissions will automatically post to <code>/enquiry/</code> with Google reCAPTCHA v3 verification.
                    </div>
                </div>
            </div>

            <div class="wf-modal-footer">
                <button type="button" class="btn-v3-action" id="btnDoneEmbedModal">Done</button>
            </div>
        </div>
    </div>
</div>