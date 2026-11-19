<?php

/**
 * Operations Management - Booking Request Templates Manager
 * Path: app/dashboard/v3/default/desktop/operations/templates.php
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
            <p class="text-muted">You do not have operations permissions to access the Request Templates Manager.</p>
            <a href="/dashboard/" class="btn-v3-action btn-blue mt-12 d-inline-block">Return to Dashboard</a>
          </div>';
    return;
}
?>

<div class="package-page-wrapper ops-page-wrapper" id="operationsApp" data-page="templates">
    <!-- 1. Header Bar -->
    <div class="package-header-bar ops-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon purple">
                <i class="fi fi-rr-file-edit"></i>
            </div>
            <div class="package-header-titles">
                <div class="ops-header-title-wrap">
                    <h2>Booking Request Templates Manager</h2>
                    <span class="ops-live-pill"><span class="ops-live-dot"></span> Operations Desk</span>
                </div>
                <p>Standardize vendor reservation communications across WhatsApp and Email with dynamic merge variables.</p>
            </div>
        </div>

        <div class="package-header-actions">
            <!-- Create Template CTA -->
            <button type="button" class="btn-v3-action btn-purple ops-action-btn" id="btnOpenNewTemplateModal">
                <i class="fi fi-rr-plus"></i> <span>Create Template</span>
            </button>

            <!-- Hub Link -->
            <a href="/dashboard/operations/" class="btn-v3-action ops-action-btn" title="Back to Operations Hub">
                <i class="fi fi-rr-apps"></i> <span>Hub</span>
            </a>

            <!-- Refresh Button -->
            <button type="button" id="btnRefreshOps" class="btn-v3-action ops-action-btn" title="Refresh templates">
                <i class="fi fi-rr-refresh"></i> <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- Operations Sub-Navigation Tabs Strip -->
    <div class="ops-subnav-strip" id="opsSubNavTabs">
        <a href="/dashboard/operations/list/" class="ops-subnav-tab" title="Confirmed Leads Fulfillment Desk">
            <i class="fi fi-rr-badge-check"></i> <span>Confirmed Leads</span> <span class="badge-count-indigo" id="badgeConfirmedCount">0</span>
        </a>
        <a href="/dashboard/operations/trips/" class="ops-subnav-tab" title="Daily Trip Planner Board">
            <i class="fi fi-rr-plane-departure"></i> <span>Trip Planner</span> <span class="badge-count-amber" id="badgeTripsCount">0</span>
        </a>
        <a href="/dashboard/operations/requests/" class="ops-subnav-tab" title="Dispatched Booking Requests">
            <i class="fi fi-rr-paper-plane"></i> <span>Booking Requests</span>
        </a>
        <a href="/dashboard/operations/unassigned/" class="ops-subnav-tab" title="Unassigned Inbound Leads">
            <i class="fi fi-rr-user-add"></i> <span>Assign Leads</span> <span class="badge-count-red" id="badgeUnassignedCount">0</span>
        </a>
        <a href="/dashboard/operations/templates/" class="ops-subnav-tab" title="Booking Request Templates">
            <i class="fi fi-rr-file-edit"></i> <span>Templates</span>
        </a>
    </div>

    <!-- 2. Templates Section -->
    <div class="ops-tab-section active" id="tabSectionTemplates">
        <div class="ops-filter-toolbar">
            <div class="ops-channel-pills" id="tplChannelFilter">
                <button type="button" class="btn-ops-pill active" data-channel="all">All Channels</button>
                <button type="button" class="btn-ops-pill" data-channel="whatsapp"><i class="fi fi-brands-whatsapp"></i> WhatsApp</button>
                <button type="button" class="btn-ops-pill" data-channel="email"><i class="fi fi-rr-envelope"></i> Email</button>
            </div>

            <div class="ops-vendor-pills" id="tplVendorFilter">
                <button type="button" class="btn-ops-pill active" data-vendor="all">All Vendors</button>
                <button type="button" class="btn-ops-pill" data-vendor="hotel"><i class="fi fi-rr-bed"></i> Hotels</button>
                <button type="button" class="btn-ops-pill" data-vendor="transport"><i class="fi fi-rr-car"></i> Transporters</button>
                <button type="button" class="btn-ops-pill" data-vendor="activity"><i class="fi fi-rr-flag"></i> Activities</button>
            </div>

            <button type="button" class="btn-v3-action btn-blue" id="btnOpenNewTemplateModalAlt">
                <i class="fi fi-rr-plus"></i> New Template
            </button>
        </div>

        <div class="ops-templates-grid" id="opsTemplatesGrid">
            <!-- Populated dynamically via operations.js -->
        </div>
    </div>
</div>

<!-- Modal: Booking Request Template Editor Modal -->
<div class="v3-modal-overlay" id="modalOpsTemplateEditor">
    <div class="v3-modal-dialog v3-modal-dialog-xl ops-template-editor-dialog">
        <!-- Header -->
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box icon-purple" id="tplEditorHeaderIconBox">
                    <i class="fi fi-rr-file-edit" id="tplEditorHeaderIcon"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title" id="tplEditorModalTitle">Edit Template: Hotel Booking Request (Standard)</h4>
                    <div class="ops-modal-sub-details">
                        <span id="tplEditorModalSubtitle">Hotel / Property Reservation</span>
                        <span class="ops-modal-sub-dot"></span>
                        <span>Operations Dispatcher</span>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-12">
                <span class="ops-tpl-channel-badge wa-badge" id="tplEditorChannelBadge">
                    <i class="fi fi-brands-whatsapp"></i> <span>WhatsApp Mode</span>
                </span>
                <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
            </div>
        </div>

        <!-- 2-Column Workstation: Left = Preview, Right = Customization Options & Editor -->
        <div class="ops-tpl-workstation-body">
            <input type="hidden" id="tplEditorId" value="0">

            <!-- ========================================================
                 LEFT COLUMN: LIVE RECIPIENT PREVIEW
                 ======================================================== -->
            <div class="ops-tpl-left-preview-col">
                <!-- Preview Control Bar -->
                <div class="ops-preview-header-bar">
                    <span class="ops-preview-title">
                        <i class="fi fi-rr-eye text-indigo"></i> Live Recipient Preview
                    </span>
                    <div class="d-flex align-items-center gap-8">
                        <div class="ops-preview-mode-switch">
                            <button type="button" class="ops-prev-mode-btn active" id="btnTplModeSample" data-mode="sample" title="View with realistic sample booking data">
                                Sample Data
                            </button>
                            <button type="button" class="ops-prev-mode-btn" id="btnTplModeRaw" data-mode="raw" title="View with highlighted template variables">
                                Raw Tags
                            </button>
                        </div>
                        <button type="button" class="ops-btn-copy-preview" id="btnCopyTplPreview" title="Copy preview text">
                            <i class="fi fi-rr-copy"></i> <span>Copy</span>
                        </button>
                    </div>
                </div>

                <!-- WhatsApp Simulation Frame -->
                <div class="ops-wa-device-frame" id="tplWaPreviewFrame">
                    <div class="ops-wa-app-bar">
                        <div class="ops-wa-contact-info">
                            <i class="fi fi-rr-angle-left font-14"></i>
                            <div class="ops-wa-avatar" id="tplWaAvatar">🏨</div>
                            <div class="ops-wa-contact-meta">
                                <span class="ops-wa-contact-name" id="tplWaContactName">The Grand Palace Srinagar</span>
                                <span class="ops-wa-contact-status" id="tplWaContactStatus">online • Hotel Reservation Desk</span>
                            </div>
                        </div>
                        <div class="ops-wa-app-icons">
                            <i class="fi fi-rr-video-camera"></i>
                            <i class="fi fi-rr-phone-call"></i>
                            <i class="fi fi-rr-menu-dots-vertical"></i>
                        </div>
                    </div>

                    <div class="ops-wa-chat-canvas">
                        <div class="ops-wa-date-banner">Today</div>
                        <div class="ops-wa-bubble">
                            <div class="ops-wa-bubble-content" id="tplWaBubbleContent">
                                <!-- Dynamic Rendered Message -->
                            </div>
                            <div class="ops-wa-bubble-meta">
                                <span id="tplWaTime">10:45 AM</span>
                                <span class="ops-wa-ticks"><i class="fi fi-rr-check-double"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="ops-wa-mock-bottom">
                        <i class="fi fi-rr-smile font-16 text-slate-400"></i>
                        <div class="ops-wa-mock-input">Message...</div>
                        <i class="fi fi-rr-clip font-14 text-slate-400"></i>
                        <div class="ops-wa-mock-mic"><i class="fi fi-rr-microphone"></i></div>
                    </div>
                </div>

                <!-- Email Simulation Frame (Shown when Email channel selected) -->
                <div class="ops-email-device-frame d-none" id="tplEmailPreviewFrame">
                    <div class="ops-email-chrome-bar">
                        <div class="ops-email-dots">
                            <div class="ops-email-dot red"></div>
                            <div class="ops-email-dot yellow"></div>
                            <div class="ops-email-dot green"></div>
                        </div>
                        <span class="ops-email-chrome-title">Hotel Reservation Email Preview</span>
                        <div class="width-20"></div>
                    </div>

                    <div class="ops-email-header-meta">
                        <div class="ops-email-header-line">
                            <span class="ops-email-header-label">Subject:</span>
                            <span class="ops-email-header-val ops-email-header-subject" id="tplEmailSubjectPreview">Booking Request: Rajesh Sharma - 3N - 24 Dec 2026</span>
                        </div>
                        <div class="ops-email-header-line">
                            <span class="ops-email-header-label">From:</span>
                            <span class="ops-email-header-val">Aleya Operations Desk &lt;ops@aleya.in&gt;</span>
                        </div>
                        <div class="ops-email-header-line">
                            <span class="ops-email-header-label">To:</span>
                            <span class="ops-email-header-val" id="tplEmailRecipientPreview">The Grand Palace Reservations &lt;reservations@grandpalace.com&gt;</span>
                        </div>
                    </div>

                    <div class="ops-email-body-canvas" id="tplEmailBodyContent">
                        <!-- Rendered Email HTML Content -->
                    </div>
                </div>

                <!-- Variable Diagnostics & Health Checker -->
                <div class="ops-tpl-diagnostics-bar">
                    <div class="ops-diag-status valid" id="tplDiagStatus">
                        <i class="fi fi-rr-shield-check"></i>
                        <span id="tplDiagText">Ready for automated dispatch</span>
                    </div>
                    <span class="font-11 text-muted" id="tplDiagCount">All merge tags valid</span>
                </div>
            </div>

            <!-- ========================================================
                 RIGHT COLUMN: CUSTOMIZATION OPTIONS & COMPOSER
                 ======================================================== -->
            <div class="ops-tpl-right-editor-col">
                <!-- 1. Top Customization Options Card -->
                <div class="ops-tpl-customization-card">
                    <div class="ops-tpl-custom-grid">
                        <!-- Channel Switcher -->
                        <div class="ops-config-item">
                            <span class="ops-config-label">Communication Channel</span>
                            <div class="ops-segmented-selector" id="tplChannelSegmented">
                                <button type="button" class="ops-seg-btn seg-wa active" data-channel="whatsapp">
                                    <i class="fi fi-brands-whatsapp"></i> WhatsApp
                                </button>
                                <button type="button" class="ops-seg-btn seg-email" data-channel="email">
                                    <i class="fi fi-rr-envelope"></i> Email
                                </button>
                            </div>
                            <select id="tplEditorChannel" class="d-none">
                                <option value="whatsapp" selected>WhatsApp Message</option>
                                <option value="email">Email Notification</option>
                            </select>
                        </div>

                        <!-- Service / Vendor Switcher -->
                        <div class="ops-config-item">
                            <span class="ops-config-label">Vendor Service Type</span>
                            <div class="ops-segmented-selector" id="tplVendorSegmented">
                                <button type="button" class="ops-seg-btn active" data-vendor="hotel">
                                    <i class="fi fi-rr-bed"></i> Hotel
                                </button>
                                <button type="button" class="ops-seg-btn" data-vendor="transport">
                                    <i class="fi fi-rr-car"></i> Cab
                                </button>
                                <button type="button" class="ops-seg-btn" data-vendor="activity">
                                    <i class="fi fi-rr-flag"></i> Activity
                                </button>
                            </div>
                            <select id="tplEditorVendorType" class="d-none">
                                <option value="hotel" selected>Hotel / Property</option>
                                <option value="transport">Transporter / Cab</option>
                                <option value="activity">Activity / Tour Guide</option>
                            </select>
                        </div>

                        <!-- Default Template Switch -->
                        <div class="ops-config-item">
                            <span class="ops-config-label">Status</span>
                            <label class="ops-default-toggle-box" title="When checked, this template is automatically used when dispatching requests">
                                <input type="checkbox" id="tplEditorIsDefault" value="1">
                                <span class="ops-default-toggle-label"><i class="fi fi-rr-star"></i> Default Template</span>
                            </label>
                        </div>
                    </div>

                    <!-- Template Name Input -->
                    <div class="ops-tpl-name-row">
                        <span class="ops-config-label">Template Name *</span>
                        <input type="text" id="tplEditorTitle" class="ops-tpl-name-input" placeholder="e.g. Hotel Booking Request (Standard)">
                    </div>

                    <!-- Email Subject Line (only visible when Email channel is active) -->
                    <div class="ops-tpl-subject-row d-none" id="tplEditorSubjectWrap">
                        <span class="ops-config-label"><i class="fi fi-rr-envelope"></i> Email Subject Line</span>
                        <input type="text" id="tplEditorSubject" class="ops-tpl-subject-input" placeholder="e.g. Booking Request: {guest_name} - {nights}N - {check_in} - {agency_name}">
                    </div>
                </div>

                <!-- 2. Categorized Dynamic Variables Palette -->
                <div class="ops-tpl-vars-accordion">
                    <div class="ops-tpl-vars-head">
                        <span class="ops-tpl-vars-title">
                            <i class="fi fi-rr-magic-wand"></i> Dynamic Merge Variables
                            <span class="badge-count-indigo font-11">Click to Insert</span>
                        </span>
                        <div class="ops-tpl-var-search-wrap">
                            <i class="fi fi-rr-search"></i>
                            <input type="text" id="tplSearchMergeTags" class="ops-tpl-var-search" placeholder="Filter variables...">
                        </div>
                    </div>
                    <div class="ops-tpl-vars-content" id="tplMergeTagsWrap">
                        <!-- Group 1: Hotel & Stay -->
                        <div class="ops-var-cat-section" data-cat="hotel">
                            <div class="ops-var-cat-title"><i class="fi fi-rr-bed"></i> Hotel & Stay Details</div>
                            <div class="ops-var-cat-pills">
                                <span class="ops-tag-pill" data-tag="{vendor_name}" data-name="Property Name" data-sample="The Grand Palace Srinagar" title="Click to insert property name">
                                    <strong>{vendor_name}</strong><span class="tag-label">Property Name</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{check_in}" data-name="Check-in Date" data-sample="24 Dec 2026" title="Click to insert check-in date">
                                    <strong>{check_in}</strong><span class="tag-label">Check-in</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{check_out}" data-name="Check-out Date" data-sample="27 Dec 2026" title="Click to insert check-out date">
                                    <strong>{check_out}</strong><span class="tag-label">Check-out</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{nights}" data-name="Nights" data-sample="3" title="Click to insert total nights">
                                    <strong>{nights}</strong><span class="tag-label">Nights</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{room_category}" data-name="Room Category" data-sample="Deluxe Valley View Room" title="Click to insert room category">
                                    <strong>{room_category}</strong><span class="tag-label">Room Category</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{room_count}" data-name="No. of Rooms" data-sample="2" title="Click to insert room count">
                                    <strong>{room_count}</strong><span class="tag-label">Rooms Count</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{meal_plan}" data-name="Meal Plan" data-sample="MAP (Breakfast + Dinner)" title="Click to insert meal plan">
                                    <strong>{meal_plan}</strong><span class="tag-label">Meal Plan</span>
                                </span>
                            </div>
                        </div>

                        <!-- Group 2: Guest Details -->
                        <div class="ops-var-cat-section" data-cat="guest">
                            <div class="ops-var-cat-title"><i class="fi fi-rr-user"></i> Guest & Travelers</div>
                            <div class="ops-var-cat-pills">
                                <span class="ops-tag-pill" data-tag="{guest_name}" data-name="Lead Guest Name" data-sample="Rajesh & Priya Sharma" title="Click to insert guest name">
                                    <strong>{guest_name}</strong><span class="tag-label">Lead Guest</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{guest_phone}" data-name="Guest Phone" data-sample="+91 98765 43210" title="Click to insert guest contact">
                                    <strong>{guest_phone}</strong><span class="tag-label">Guest Phone</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{pax_adults}" data-name="Adults" data-sample="2" title="Click to insert adults count">
                                    <strong>{pax_adults}</strong><span class="tag-label">Adults</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{pax_children}" data-name="Children" data-sample="1" title="Click to insert children count">
                                    <strong>{pax_children}</strong><span class="tag-label">Children</span>
                                </span>
                            </div>
                        </div>

                        <!-- Group 3: Agency & Desk -->
                        <div class="ops-var-cat-section" data-cat="agency">
                            <div class="ops-var-cat-title"><i class="fi fi-rr-briefcase"></i> Operations Desk & Agency</div>
                            <div class="ops-var-cat-pills">
                                <span class="ops-tag-pill" data-tag="{agency_name}" data-name="Agency Name" data-sample="Aleya Travels" title="Click to insert agency name">
                                    <strong>{agency_name}</strong><span class="tag-label">Agency</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{ops_manager_name}" data-name="Ops Lead" data-sample="Amit Verma" title="Click to insert ops lead name">
                                    <strong>{ops_manager_name}</strong><span class="tag-label">Ops Lead</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{ops_manager_phone}" data-name="Ops Helpline" data-sample="+91 98111 22334" title="Click to insert ops helpline">
                                    <strong>{ops_manager_phone}</strong><span class="tag-label">Ops Helpline</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{special_instructions}" data-name="Special Remarks" data-sample="Quiet room on higher floor with valley view. Honeymoon couple." title="Click to insert remarks">
                                    <strong>{special_instructions}</strong><span class="tag-label">Special Notes</span>
                                </span>
                            </div>
                        </div>

                        <!-- Group 4: Transport & Extra (Optional) -->
                        <div class="ops-var-cat-section" data-cat="transport">
                            <div class="ops-var-cat-title"><i class="fi fi-rr-car"></i> Cab / Transfers (If applicable)</div>
                            <div class="ops-var-cat-pills">
                                <span class="ops-tag-pill" data-tag="{vehicle_type}" data-name="Vehicle Type" data-sample="Innova Crysta (7-Seater)">
                                    <strong>{vehicle_type}</strong><span class="tag-label">Vehicle</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{pickup_location}" data-name="Pickup Point" data-sample="Srinagar Airport (SXR)">
                                    <strong>{pickup_location}</strong><span class="tag-label">Pickup</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{drop_location}" data-name="Drop Point" data-sample="The Grand Palace Srinagar">
                                    <strong>{drop_location}</strong><span class="tag-label">Drop</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{pickup_date_time}" data-name="Pickup Time" data-sample="24 Dec 2026, 11:30 AM">
                                    <strong>{pickup_date_time}</strong><span class="tag-label">Time</span>
                                </span>
                                <span class="ops-tag-pill" data-tag="{vendor_contact}" data-name="Vendor Phone" data-sample="+91 194 250 1234">
                                    <strong>{vendor_contact}</strong><span class="tag-label">Vendor Phone</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Template Body Composer Area -->
                <div class="ops-tpl-composer-box">
                    <!-- Formatting Toolbar -->
                    <div class="ops-tpl-toolbar">
                        <div class="ops-tpl-tools-left">
                            <button type="button" class="ops-tool-btn" id="btnTplFormatBold" title="Bold (*text*)"><strong>B</strong></button>
                            <button type="button" class="ops-tool-btn" id="btnTplFormatItalic" title="Italic (_text_)"><em>I</em></button>
                            <button type="button" class="ops-tool-btn" id="btnTplFormatStrike" title="Strikethrough (~text~)"><s>S</s></button>
                            <button type="button" class="ops-tool-btn code-font" id="btnTplFormatCode" title="Monospace (```text```)">&lt;/&gt;</button>
                            <button type="button" class="ops-tool-btn" id="btnTplFormatBullet" title="Bullet Point (• )">•</button>

                            <div class="ops-tool-divider"></div>

                            <div class="ops-emoji-pills">
                                <button type="button" class="ops-emoji-btn" data-emoji="🏨" title="Hotel">🏨</button>
                                <button type="button" class="ops-emoji-btn" data-emoji="👤" title="Guest">👤</button>
                                <button type="button" class="ops-emoji-btn" data-emoji="📅" title="Calendar">📅</button>
                                <button type="button" class="ops-emoji-btn" data-emoji="🛏️" title="Bed">🛏️</button>
                                <button type="button" class="ops-emoji-btn" data-emoji="🍽️" title="Meal">🍽️</button>
                                <button type="button" class="ops-emoji-btn" data-emoji="🚗" title="Cab">🚗</button>
                                <button type="button" class="ops-emoji-btn" data-emoji="📱" title="Phone">📱</button>
                                <button type="button" class="ops-emoji-btn" data-emoji="📌" title="Note">📌</button>
                                <button type="button" class="ops-emoji-btn" data-emoji="✅" title="Check">✅</button>
                                <button type="button" class="ops-emoji-btn" data-emoji="⚠️" title="Alert">⚠️</button>
                            </div>
                        </div>

                        <div class="ops-tpl-tools-right">
                            <button type="button" class="ops-btn-tool-action" id="btnTplRestorePreset" title="Restore recommended standard preset for this category">
                                <i class="fi fi-rr-refresh"></i> <span>Reset to Standard Preset</span>
                            </button>
                        </div>
                    </div>

                    <!-- Main Textarea -->
                    <textarea id="tplEditorBody" class="ops-tpl-textarea" rows="12" placeholder="Write message body with dynamic variables..."></textarea>

                    <!-- Textarea Footer Stats -->
                    <div class="ops-tpl-composer-footer">
                        <div class="ops-tpl-stats">
                            <span class="ops-tpl-stat-item"><strong id="tplStatChars">0</strong> chars</span>
                            <span class="ops-tpl-stat-item"><strong id="tplStatWords">0</strong> words</span>
                            <span class="ops-tpl-stat-item"><strong id="tplStatVars">0</strong> variables</span>
                        </div>
                        <div class="ops-tpl-tip" id="tplFormattingTip">
                            <i class="fi fi-rr-info"></i> WhatsApp formatting: <strong>*bold*</strong>, <em>_italic_</em>, <s>~strike~</s>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="v3-modal-footer d-flex justify-content-between align-items-center">
            <div class="ops-tpl-footer-notice">
                <i class="fi fi-rr-check-circle"></i>
                <span>Changes will immediately apply to all automated &amp; manual booking request dispatches.</span>
            </div>
            <div class="d-flex align-items-center gap-10">
                <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
                <button type="button" class="btn-v3-action btn-purple" id="btnConfirmSaveTemplate">
                    <i class="fi fi-rr-disk"></i> Save Template
                </button>
            </div>
        </div>
    </div>
</div>