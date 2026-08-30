<?php
$currentUserType = strtolower(defined('USER_TYPE') ? USER_TYPE : '');
if (!in_array($currentUserType, ['administrator', 'operator'])) {
    echo "<script>window.location.href = '/dashboard/setting/profile/';</script>";
    exit();
}
?>
<div class="package-page-wrapper setting-page-wrapper" id="settingNotifyApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon" style="background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%);">
                <i class="fi fi-rr-bell-ring" style="color: #ffffff;"></i>
            </div>
            <div class="package-header-titles">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <h2 style="margin: 0;">Alert &amp; Notification Center</h2>
                </div>
                <p style="margin-top: 4px;">Choose the exact email inboxes, WhatsApp numbers, SMS, and browser push channels where you receive real-time business alerts.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/setting/" class="btn-v3-action" data-tooltip="Back to Settings">
                <i class="fi fi-rr-settings"></i> <span>Settings</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Form & Live Preview Layout -->
    <div class="v3-form-split-layout">
        <!-- Left: Main Form Cards -->
        <div class="v3-form-main-col">
            <form id="settingNotifyForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Email Alert Channel -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon indigo">
                            <i class="fi fi-rr-envelope"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Email Alert Inboxes</h3>
                            <p>Configure dedicated recipient email addresses for admin alerts, leads, and billing notices.</p>
                        </div>
                        <div style="margin-left: auto;">
                            <label class="v3-toggle-switch" title="Toggle Email Channel">
                                <input type="checkbox" id="notifyEmailEnabled" name="notification_email_notification" value="1" checked>
                                <span class="v3-toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Primary Admin Email -->
                            <div class="v3-form-group">
                                <label for="notifyAdminEmail" class="v3-form-label">
                                    Primary Admin Alert Email <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-envelope input-icon"></i>
                                    <input type="email" id="notifyAdminEmail" name="notification_admin_email" class="v3-form-input" placeholder="admin@yourdomain.com" required>
                                </div>
                                <span class="v3-field-error" id="errNotifyAdminEmail"></span>
                            </div>

                            <!-- Inquiries & Leads Email -->
                            <div class="v3-form-group">
                                <label for="notifyEnquiryEmail" class="v3-form-label">
                                    Inquiry &amp; Lead Alerts Email
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-inbox input-icon"></i>
                                    <input type="email" id="notifyEnquiryEmail" name="notification_enquiry_email" class="v3-form-input" placeholder="leads@yourdomain.com">
                                </div>
                            </div>

                            <!-- System & Security Alerts Email -->
                            <div class="v3-form-group span-2">
                                <label for="notifySiteEmailAlert" class="v3-form-label">
                                    System, Server &amp; Security Alerts Email
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-shield-check input-icon"></i>
                                    <input type="email" id="notifySiteEmailAlert" name="notification_site_email_alert" class="v3-form-input" placeholder="security@yourdomain.com">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: WhatsApp Instant Alert Channel -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <span class="v3-wa-icon-svg header-icon"><svg viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                </svg></span>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>WhatsApp Alert Channel</h3>
                            <p>Receive instant WhatsApp notifications with rich itinerary links and transaction summaries.</p>
                        </div>
                        <div style="margin-left: auto;">
                            <label class="v3-toggle-switch" title="Toggle WhatsApp Channel">
                                <input type="checkbox" id="notifyWhatsappEnabled" name="notification_enable_whatsapp_business_api" value="1" checked>
                                <span class="v3-toggle-slider emerald"></span>
                            </label>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Admin WhatsApp Number -->
                            <div class="v3-form-group">
                                <label for="notifyAdminWhatsapp" class="v3-form-label">
                                    Admin WhatsApp Alert Number <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <span class="v3-wa-icon-svg input-icon-svg"><svg viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                        </svg></span>
                                    <input type="tel" id="notifyAdminWhatsapp" name="notification_admin_whatsapp_number" class="v3-form-input" placeholder="e.g. 9990026008" required>
                                </div>
                                <span class="v3-field-error" id="errNotifyAdminWhatsapp"></span>
                            </div>

                            <!-- Company Official WhatsApp -->
                            <div class="v3-form-group">
                                <label for="notifyCompanyWhatsapp" class="v3-form-label">
                                    Company Sender WhatsApp Number
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <span class="v3-wa-icon-svg input-icon-svg"><svg viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                        </svg></span>
                                    <input type="tel" id="notifyCompanyWhatsapp" name="notification_company_whatsapp_number" class="v3-form-input" placeholder="e.g. 9990026008">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: SMS Text Alert Channel -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-mobile"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>SMS Text Notification Channel</h3>
                            <p>Direct cellular SMS alerts for urgent inquiries, booking verification codes, and fallback notifications.</p>
                        </div>
                        <div style="margin-left: auto;">
                            <label class="v3-toggle-switch" title="Toggle SMS Channel">
                                <input type="checkbox" id="notifySmsEnabled" name="notification_sms_enable" value="1">
                                <span class="v3-toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <div class="v3-form-group span-2">
                                <label for="notifySmsDefault" class="v3-form-label">
                                    Primary SMS Alert Mobile Number
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-mobile input-icon"></i>
                                    <input type="tel" id="notifySmsDefault" name="notification_sms_default" class="v3-form-input" placeholder="e.g. 9990026008">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Web Browser Push & In-App Alerts -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon rose">
                            <i class="fi fi-rr-bell"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Web Push &amp; In-App Notifications</h3>
                            <p>Real-time browser popups and floating top-bar dashboard notifications for active operators.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <!-- Push Notifications -->
                            <div class="v3-event-card">
                                <div class="v3-event-card-left">
                                    <div class="v3-event-icon-box sky">
                                        <i class="fi fi-rr-computer"></i>
                                    </div>
                                    <div class="v3-event-text">
                                        <h4>Browser Web Push Notifications</h4>
                                        <p>Desktop and mobile browser alerts even when the CRM dashboard tab is in the background.</p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <button type="button" id="btnTestPush" class="btn-v3-action" style="padding: 6px 12px; font-size: 11.5px;" title="Test Browser Notification">
                                        <i class="fi fi-rr-paper-plane"></i> <span>Test</span>
                                    </button>
                                    <label class="v3-toggle-switch" title="Toggle Web Push">
                                        <input type="checkbox" id="notifyPushEnabled" name="notification_enable_push_notification" value="1">
                                        <span class="v3-toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <!-- In-App Bell Notifications -->
                            <div class="v3-event-card">
                                <div class="v3-event-card-left">
                                    <div class="v3-event-icon-box emerald">
                                        <i class="fi fi-rr-bell-ring"></i>
                                    </div>
                                    <div class="v3-event-text">
                                        <h4>Dashboard In-App Bell &amp; Audio Chime</h4>
                                        <p>Play instant audio chime and display badge count in top navigation when new leads arrive.</p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <button type="button" id="btnTestAudio" class="btn-v3-action" style="padding: 6px 12px; font-size: 11.5px;" title="Play Chime Sound">
                                        <i class="fi fi-rr-volume"></i> <span>Play Sound</span>
                                    </button>
                                    <label class="v3-toggle-switch" title="Toggle In-App Audio">
                                        <input type="checkbox" id="notifyInAppEnabled" name="notification_enable_inapp_notification" value="1" checked>
                                        <span class="v3-toggle-slider emerald"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Real-Time Event Triggers -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon purple">
                            <i class="fi fi-rr-settings-sliders"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Real-Time Event Triggers</h3>
                            <p>Choose which business events automatically trigger alerts to your enabled channels.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <!-- Event 1 -->
                            <div class="v3-event-card">
                                <div class="v3-event-card-left">
                                    <div class="v3-event-icon-box sky">
                                        <i class="fi fi-rr-bullseye-arrow"></i>
                                    </div>
                                    <div class="v3-event-text">
                                        <h4>New Inquiries &amp; Website Lead Capture</h4>
                                        <p>Instant alert when a traveler submits a tour quote request or booking inquiry.</p>
                                    </div>
                                </div>
                                <label class="v3-toggle-switch" title="Toggle Inquiries Alert">
                                    <input type="checkbox" id="evtEnquiry" name="notification_company_whatsapp_enquiry_thank_you" value="1" checked>
                                    <span class="v3-toggle-slider"></span>
                                </label>
                            </div>

                            <!-- Event 2 -->
                            <div class="v3-event-card">
                                <div class="v3-event-card-left">
                                    <div class="v3-event-icon-box emerald">
                                        <i class="fi fi-rr-credit-card"></i>
                                    </div>
                                    <div class="v3-event-text">
                                        <h4>Payment Received &amp; Transaction Receipts</h4>
                                        <p>Notify immediately when a traveler completes an online or UPI milestone payment.</p>
                                    </div>
                                </div>
                                <label class="v3-toggle-switch" title="Toggle Payment Alert">
                                    <input type="checkbox" id="evtPayment" name="notification_company_whatsapp_payment_request" value="1" checked>
                                    <span class="v3-toggle-slider emerald"></span>
                                </label>
                            </div>

                            <!-- Event 3 -->
                            <div class="v3-event-card">
                                <div class="v3-event-card-left">
                                    <div class="v3-event-icon-box purple">
                                        <i class="fi fi-rr-document"></i>
                                    </div>
                                    <div class="v3-event-text">
                                        <h4>Itinerary Proposal &amp; Vouchers Dispatched</h4>
                                        <p>Confirmation alerts when staff generates and emails itinerary PDFs to clients.</p>
                                    </div>
                                </div>
                                <label class="v3-toggle-switch" title="Toggle Itinerary Alert">
                                    <input type="checkbox" id="evtItinerary" name="notification_company_whatsapp_itinerary_sent" value="1" checked>
                                    <span class="v3-toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/setting/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" id="btnSaveNotify" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span id="btnSaveNotifyText">Save Notification Settings</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Routing Simulator & Notify360 Badge -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Channel Status Overview Card -->
                <div class="v3-matrix-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Channel Delivery Matrix</span>
                        <span class="v3-status-badge status-active" style="font-size: 11px;"><span class="status-pulse-dot"></span> Multi-Channel</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px; font-size: 12.5px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f8fafc;">
                            <span style="display: flex; align-items: center; gap: 8px; color: #334155;"><i class="fi fi-rr-envelope" style="color: #4f46e5;"></i> Email Inboxes</span>
                            <span id="badgeStatusEmail" class="v3-status-badge status-active" style="font-size: 10px;">Active</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f8fafc;">
                            <span style="display: flex; align-items: center; gap: 8px; color: #334155;"><span class="v3-wa-icon-svg" style="width: 14px; height: 14px; color: #10b981;"><svg viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                    </svg></span> WhatsApp Alerts</span>
                            <span id="badgeStatusWhatsapp" class="v3-status-badge status-active" style="font-size: 10px;">Active</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f8fafc;">
                            <span style="display: flex; align-items: center; gap: 8px; color: #334155;"><i class="fi fi-rr-mobile" style="color: #0284c7;"></i> SMS Cellular</span>
                            <span id="badgeStatusSms" class="v3-status-badge status-inactive" style="font-size: 10px;">Disabled</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f8fafc;">
                            <span style="display: flex; align-items: center; gap: 8px; color: #334155;"><i class="fi fi-rr-computer" style="color: #ea580c;"></i> Web Push</span>
                            <span id="badgeStatusPush" class="v3-status-badge status-active" style="font-size: 10px;">Active</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 6px 0;">
                            <span style="display: flex; align-items: center; gap: 8px; color: #334155;"><i class="fi fi-rr-bell-ring" style="color: #7c3aed;"></i> Dashboard In-App</span>
                            <span id="badgeStatusInApp" class="v3-status-badge status-active" style="font-size: 10px;">Active</span>
                        </div>
                    </div>

                    <div class="v3-matrix-brand-watermark">
                        <a href="https://notify360.co" target="_blank"><img src="/app/dashboard/v3/default/desktop/assets/images/notify360.png" alt="Notify360"></a>
                    </div>
                </div>

                <!-- Live Alert Routing Card -->
                <div class="v3-live-preview-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Active Alert Routing</span>
                        <span class="v3-status-badge status-active" style="font-size: 11px;"><span class="status-pulse-dot"></span> Live</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <!-- Route 1 -->
                        <div class="v3-routing-box">
                            <div class="v3-routing-box-title sky">
                                <i class="fi fi-rr-bullseye-arrow"></i> <span>Inquiries &amp; Leads</span>
                            </div>
                            <div class="v3-routing-row">
                                <strong>Email:</strong>
                                <span id="previewEnquiryEmail">None</span>
                            </div>
                            <div class="v3-routing-row">
                                <strong>WhatsApp:</strong>
                                <span id="previewAdminWhatsapp">+91 &mdash;</span>
                            </div>
                        </div>

                        <!-- Route 2 -->
                        <div class="v3-routing-box">
                            <div class="v3-routing-box-title emerald">
                                <i class="fi fi-rr-credit-card"></i> <span>Payments &amp; Receipts</span>
                            </div>
                            <div class="v3-routing-row">
                                <strong>Email:</strong>
                                <span id="previewAdminEmail">None</span>
                            </div>
                            <div class="v3-routing-row">
                                <strong>WhatsApp:</strong>
                                <span id="previewAdminWhatsapp2">+91 &mdash;</span>
                            </div>
                        </div>

                        <!-- Route 3 -->
                        <div class="v3-routing-box">
                            <div class="v3-routing-box-title purple">
                                <i class="fi fi-rr-shield-check"></i> <span>System &amp; Security</span>
                            </div>
                            <div class="v3-routing-row">
                                <strong>Email:</strong>
                                <span id="previewSecurityEmail">Same as Admin</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>