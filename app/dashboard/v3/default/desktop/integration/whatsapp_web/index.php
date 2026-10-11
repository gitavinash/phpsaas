<?php
$isWaWebActive = (defined('ENABLE_WHATSAPP_WEB_API') && ENABLE_WHATSAPP_WEB_API == '1');
?>
<div class="package-page-wrapper integration-plugin-wrapper" id="pluginWhatsappWebApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-whatsapp wa-web-icon-wrap">
                <img src="/app/dashboard/v3/default/desktop/assets/images/integration/whatsapp.svg" alt="WhatsApp">
            </div>
            <div class="package-header-titles">
                <h2>WhatsApp Web Multi-Device Manager</h2>
                <p>Link WhatsApp numbers, assign devices to CRM sales agents, and chat with customers directly from CRM.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/integration/" class="btn-v3-action" data-tooltip="Back to All Integrations">
                <i class="fi fi-rr-arrow-left"></i> <span>All Integrations</span>
            </a>
            <a href="/dashboard/whatsapp/" class="btn-v3-action btn-wa-chat-direct" data-tooltip="Open WhatsApp CRM Inbox">
                <i class="fi fi-rr-comment-alt-middle"></i> <span>Open Live Inbox</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Split Layout -->
    <div class="v3-form-split-layout">
        <!-- Main Column -->
        <div class="v3-form-main-col">
            <!-- Global Plugin Toggle Card -->
            <div class="v3-card-section" id="waWebToggleCard">
                <div class="v3-card-header">
                    <div class="v3-card-header-icon emerald">
                        <i class="fi fi-rr-power"></i>
                    </div>
                    <div class="v3-card-header-text">
                        <h3>WhatsApp Web Integration Status</h3>
                        <p>Turn integration on or off across your CRM instance.</p>
                    </div>
                    <div class="v3-header-toggle-wrap">
                        <label class="v3-toggle-switch" title="Toggle WhatsApp Web API">
                            <input type="checkbox" id="waWebActiveToggle" name="enable_whatsapp_web_api" value="1" <?php echo ($isWaWebActive ? 'checked' : ''); ?>>
                            <span class="v3-toggle-slider emerald"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Disabled State Notice Card (Shown when integration is disabled) -->
            <div class="v3-card-section wa-web-disabled-state <?php echo ($isWaWebActive ? 'd-none' : ''); ?>" id="waWebDisabledState">
                <div class="wa-web-disabled-content">
                    <div class="wa-web-disabled-icon">
                        <i class="fi fi-rr-info"></i>
                    </div>
                    <div class="wa-web-disabled-body">
                        <h4>WhatsApp Web Integration is Currently Disabled</h4>
                        <p>Turn on the integration status toggle switch above to manage linked WhatsApp numbers, assign devices to CRM sales agents, and synchronize customer messages.</p>
                    </div>
                </div>
            </div>

            <!-- Content Sections (Shown only when enabled) -->
            <div class="wa-web-sections-wrapper <?php echo ($isWaWebActive ? '' : 'd-none'); ?>" id="waWebSectionsWrapper">

                <!-- ======================================================== -->
                <!-- 1. ADMIN VIEW: Multi-Device Grid & Agent Assignment     -->
                <!-- ======================================================== -->
                <div class="v3-card-section d-none" id="waAdminSection">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-devices"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Linked WhatsApp Devices &amp; Agent Assignments</h3>
                            <p>Manage multi-device connections and assign each device to a CRM sales agent.</p>
                        </div>
                        <div class="v3-header-actions-wrap">
                            <button type="button" class="btn-wa-buy-license btn-contact-aleya btn-upgrade d-none" id="btnBuyDeviceLicense" title="Upgrade WhatsApp device licenses" data-modal-title="WhatsApp Device Limit Reached" data-modal-notice="You have reached your WhatsApp device license limit. Please contact Aleya to connect additional devices.">
                                <i class="fi fi-rr-sparkles"></i> <span>Buy More WhatsApp Licenses</span>
                            </button>
                            <button type="button" class="btn-integration-primary btn-sm" id="btnOpenAddDeviceModal">
                                <i class="fi fi-rr-plus"></i> <span>Add WhatsApp Device</span>
                            </button>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <!-- Top Summary Bar -->
                        <div class="wa-devices-top-bar">
                            <div class="wa-devices-summary-title">
                                <span>Connected Devices</span>
                                <span class="wa-count-badge" id="waAdminDeviceCount">0</span>
                            </div>
                            <div class="wa-devices-top-actions">
                                <button type="button" class="btn-wa-link" id="btnRepairSessions" title="Repair Signal Session Ratchets / Refresh Encryption">
                                    <i class="fi fi-rr-shield-check"></i> Fix Encryption Ratchets
                                </button>
                                <button type="button" class="btn-wa-link" id="btnAdminRefreshDevices">
                                    <i class="fi fi-rr-refresh"></i> Refresh Device List
                                </button>
                            </div>
                        </div>

                        <!-- Devices Grid (Populated dynamically) -->
                        <div class="wa-devices-grid" id="waAdminDevicesGrid">
                            <div class="wa-qr-placeholder">
                                <i class="fi fi-rr-spinner wa-spin"></i>
                                <span>Loading WhatsApp devices...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================== -->
                <!-- 2. SALES AGENT VIEW: If Device Already Assigned          -->
                <!-- ======================================================== -->
                <div class="v3-card-section d-none" id="waAgentAssignedSection">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-check-circle"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Your Assigned WhatsApp Device</h3>
                            <p>This WhatsApp number is assigned to your CRM sales agent account.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="wa-agent-assigned-banner">
                            <div class="wa-agent-assigned-info">
                                <div class="wa-agent-assigned-icon">
                                    <i class="fi fi-rr-smartphone"></i>
                                </div>
                                <div>
                                    <h4 class="wa-agent-assigned-title">
                                        <span id="waAgentDevicePhone">+91 ...</span>
                                        <span class="wa-agent-pill-max">1 of 1 Max</span>
                                    </h4>
                                    <p class="wa-agent-assigned-desc" id="waAgentDeviceDesc">Active &amp; connected to your account</p>
                                </div>
                            </div>
                            <div>
                                <span class="wa-device-badge connected">
                                    <span class="wa-device-badge-dot"></span> Connected
                                </span>
                            </div>
                        </div>

                        <div class="wa-device-meta-grid">
                            <div class="wa-meta-item">
                                <label>Assigned Phone</label>
                                <span class="wa-meta-value highlight" id="waAgentMetaPhone">+91 ...</span>
                            </div>
                            <div class="wa-meta-item">
                                <label>Profile / Push Name</label>
                                <span class="wa-meta-value" id="waAgentMetaName">-</span>
                            </div>
                            <div class="wa-meta-item">
                                <label>Status</label>
                                <span class="wa-meta-value" id="waAgentMetaStatus">Active</span>
                            </div>
                            <div class="wa-meta-item">
                                <label>Last Synced</label>
                                <span class="wa-meta-value" id="waAgentMetaSync">Just now</span>
                            </div>
                        </div>

                        <div class="wa-connected-actions">
                            <a href="/dashboard/whatsapp/" class="btn-integration-primary">
                                <i class="fi fi-rr-messages"></i> Open Live CRM Inbox
                            </a>
                            <button type="button" class="btn-wa-secondary" id="btnAgentSyncChats">
                                <i class="fi fi-rr-refresh"></i> Sync My Chats
                            </button>
                            <button type="button" class="btn-wa-secondary" id="btnAgentRepairSessions" title="Repair Signal Ratchets / Fix Bad MAC">
                                <i class="fi fi-rr-shield-check"></i> Repair Encryption
                            </button>
                            <button type="button" class="btn-wa-danger btn-device-disconnect" id="btnAgentDisconnect" data-session-id="" data-device-id="" title="Disconnect this WhatsApp device">
                                <i class="fi fi-rr-exit"></i> Disconnect
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ======================================================== -->
                <!-- 3. SALES AGENT VIEW: If No Device Assigned Yet           -->
                <!-- ======================================================== -->
                <div class="v3-card-section d-none" id="waAgentUnassignedSection">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-qr-scan"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Link Your WhatsApp Device</h3>
                            <p>Link your WhatsApp number to communicate with customers directly through CRM.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="wa-agent-unassigned-notice">
                            <i class="fi fi-rr-info"></i>
                            <span>You can add <strong>1 WhatsApp number</strong> to your CRM sales agent account. Once linked, it will be assigned to you.</span>
                        </div>

                        <!-- Pairing Container for Agent -->
                        <div class="wa-pairing-container">
                            <!-- Mode Switcher -->
                            <div class="wa-pairing-mode-nav">
                                <button type="button" class="wa-pairing-mode-btn active" data-pane="#waAgentQrPane">
                                    <i class="fi fi-rr-qr-scan"></i> <span>Scan QR Code</span>
                                </button>
                                <button type="button" class="wa-pairing-mode-btn" data-pane="#waAgentPhonePane">
                                    <i class="fi fi-rr-phone-call"></i> <span>Link with Phone Number (Pairing Code)</span>
                                </button>
                            </div>

                            <!-- Pane 1: Live QR Code -->
                            <div class="wa-pairing-pane" id="waAgentQrPane">
                                <div class="wa-qr-pairing-box">
                                    <div class="wa-qr-visual-col">
                                        <div class="wa-qr-canvas-wrap" id="waAgentQrCodeContainer">
                                            <div class="wa-qr-placeholder">
                                                <i class="fi fi-rr-spinner wa-spin"></i>
                                                <span>Generating QR Code...</span>
                                            </div>
                                        </div>
                                        <div class="wa-qr-countdown-wrap">
                                            <span>Auto-refreshes in <strong id="waAgentQrTimer">30</strong>s</span>
                                            <button type="button" class="btn-wa-link" id="btnAgentRefreshQr">
                                                <i class="fi fi-rr-refresh"></i> Refresh QR Code
                                            </button>
                                        </div>
                                    </div>

                                    <div class="wa-qr-instructions-col">
                                        <h4>How to link your WhatsApp:</h4>
                                        <ol class="wa-steps-list">
                                            <li>
                                                <span class="step-num">1</span>
                                                <div class="step-content">Open <strong>WhatsApp</strong> on your mobile phone</div>
                                            </li>
                                            <li>
                                                <span class="step-num">2</span>
                                                <div class="step-content">Tap <strong>Linked Devices</strong> &gt; <strong>Link a Device</strong></div>
                                            </li>
                                            <li>
                                                <span class="step-num">3</span>
                                                <div class="step-content">Point your camera at this QR code to connect</div>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <!-- Pane 2: Phone Number Pairing Code -->
                            <div class="wa-pairing-pane d-none" id="waAgentPhonePane">
                                <div class="wa-pairing-phone-box">
                                    <div class="wa-qr-instructions-col">
                                        <h4>Link with Phone Number (Alternative to Scanning):</h4>
                                        <ol class="wa-steps-list">
                                            <li>
                                                <span class="step-num">1</span>
                                                <div class="step-content">Enter your WhatsApp number below (with country code) and click <strong>Get Pairing Code</strong></div>
                                            </li>
                                            <li>
                                                <span class="step-num">2</span>
                                                <div class="step-content">Open <strong>WhatsApp</strong> &gt; <strong>Linked Devices</strong> &gt; <strong>Link with phone number instead</strong></div>
                                            </li>
                                            <li>
                                                <span class="step-num">3</span>
                                                <div class="step-content">Type the 8-digit code shown here into WhatsApp</div>
                                            </li>
                                        </ol>
                                    </div>

                                    <div class="wa-phone-form-wrap">
                                        <label class="v3-form-label" for="waAgentPairingPhoneInput">Your WhatsApp Phone Number</label>
                                        <div class="wa-phone-input-row">
                                            <input type="text" class="v3-input" id="waAgentPairingPhoneInput" placeholder="+91 98765 43210" autocomplete="tel">
                                            <button type="button" class="btn-integration-primary" id="btnAgentRequestPairingCode">
                                                <i class="fi fi-rr-key"></i> <span>Get Pairing Code</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="wa-pairing-code-banner d-none" id="waAgentPairingCodeResult">
                                        <span class="wa-code-hint">Enter this 8-digit code on your WhatsApp app:</span>
                                        <span class="wa-code-digits" id="waAgentPairingCodeValue">---- ----</span>
                                        <span class="wa-code-hint">Waiting for pairing confirmation on your mobile phone...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: CRM Automation & Sync Settings -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-settings-sliders"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Sync &amp; Lead Automation</h3>
                            <p>Configure automatic synchronization rules for incoming traveler chats.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <form id="waWebSettingsForm" class="v3-form-container">
                            <div class="wa-settings-toggle-row">
                                <div class="wa-toggle-info">
                                    <strong>Synchronize Incoming Messages</strong>
                                    <p>Automatically ingest customer WhatsApp messages and attach them to CRM threads.</p>
                                </div>
                                <label class="v3-toggle-switch">
                                    <input type="checkbox" id="waWebSyncIncoming" name="whatsapp_web_sync_incoming" value="1" checked>
                                    <span class="v3-toggle-slider"></span>
                                </label>
                            </div>

                            <div class="wa-settings-toggle-row">
                                <div class="wa-toggle-info">
                                    <strong>Auto-create CRM Leads from New Senders</strong>
                                    <p>When an unknown phone number messages your WhatsApp, automatically capture them as a new CRM enquiry.</p>
                                </div>
                                <label class="v3-toggle-switch">
                                    <input type="checkbox" id="waWebAutoCreateLead" name="whatsapp_web_auto_create_lead" value="1" checked>
                                    <span class="v3-toggle-slider"></span>
                                </label>
                            </div>

                            <div class="v3-form-actions-bar">
                                <button type="submit" class="btn-integration-primary" id="btnSaveWaWebSettings">
                                    <i class="fi fi-rr-disk"></i> Save Sync Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- /waWebSectionsWrapper -->
        </div>

        <!-- Sidebar Column -->
        <div class="v3-form-side-col <?php echo ($isWaWebActive ? '' : 'd-none'); ?>" id="waWebSideCol">
            <!-- Card: Inbound Webhook -->
            <div class="v3-card-section">
                <div class="v3-card-header">
                    <div class="v3-card-header-icon purple">
                        <i class="fi fi-rr-network"></i>
                    </div>
                    <div class="v3-card-header-text">
                        <h3>Inbound Sync Webhook</h3>
                        <p>Webhook URL for WhatsApp gateway notifications.</p>
                    </div>
                </div>
                <div class="v3-card-body">
                    <div class="v3-form-group">
                        <label class="v3-form-label">Webhook Receiver Endpoint</label>
                        <div class="wa-copy-input-wrap">
                            <input type="text" id="waWebhookUrlDisplay" class="v3-form-input readonly-input" readonly value="/api/v3/whatsapp/webhook.php">
                            <button type="button" class="btn-wa-copy" id="btnCopyWebhook" title="Copy to clipboard">
                                <i class="fi fi-rr-copy"></i>
                            </button>
                        </div>
                    </div>
                    <ul class="wa-feature-bullets">
                        <li><i class="fi fi-rr-check"></i> Multi-device agent assignment</li>
                        <li><i class="fi fi-rr-check"></i> 1 device limit per sales agent</li>
                        <li><i class="fi fi-rr-check"></i> Automatic lead capture &amp; chat sync</li>
                    </ul>
                </div>
            </div>

            <!-- Card: Quick Navigation -->
            <div class="v3-card-section">
                <div class="v3-card-header">
                    <div class="v3-card-header-icon amber">
                        <i class="fi fi-rr-apps"></i>
                    </div>
                    <div class="v3-card-header-text">
                        <h3>Quick Navigation</h3>
                        <p>CRM messaging tools</p>
                    </div>
                </div>
                <div class="v3-card-body">
                    <a href="/dashboard/whatsapp/" class="btn-wa-nav-link">
                        <i class="fi fi-rr-comment-alt-middle"></i>
                        <span>WhatsApp Live Inbox</span>
                        <i class="fi fi-rr-angle-small-right"></i>
                    </a>
                    <a href="/dashboard/integration/whatsapp/" class="btn-wa-nav-link">
                        <i class="fi fi-rr-cloud-upload"></i>
                        <span>WhatsApp Cloud API (Meta)</span>
                        <i class="fi fi-rr-angle-small-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: Admin Add New WhatsApp Device                     -->
    <!-- ======================================================== -->
    <div class="wa-modal-backdrop" id="waAddDeviceModal">
        <div class="wa-modal-window">
            <div class="wa-modal-header">
                <h3><i class="fi fi-rr-plus-circle"></i> <span>Add New WhatsApp Device</span></h3>
                <button type="button" class="btn-wa-modal-close" id="btnCloseAddDeviceModal" title="Close">
                    <i class="fi fi-rr-cross"></i>
                </button>
            </div>
            <div class="wa-modal-body">
                <!-- Mode Switcher -->
                <div class="wa-pairing-mode-nav">
                    <button type="button" class="wa-pairing-mode-btn active" data-pane="#waModalQrPane">
                        <i class="fi fi-rr-qr-scan"></i> <span>Scan QR Code</span>
                    </button>
                    <button type="button" class="wa-pairing-mode-btn" data-pane="#waModalPhonePane">
                        <i class="fi fi-rr-phone-call"></i> <span>Pair with Phone Number</span>
                    </button>
                </div>

                <!-- Pane 1: Modal Live QR Code -->
                <div class="wa-pairing-pane" id="waModalQrPane">
                    <div class="wa-qr-pairing-box">
                        <div class="wa-qr-visual-col">
                            <div class="wa-qr-canvas-wrap" id="waModalQrCodeContainer">
                                <div class="wa-qr-placeholder">
                                    <i class="fi fi-rr-spinner wa-spin"></i>
                                    <span>Generating QR Code for new device...</span>
                                </div>
                            </div>
                            <div class="wa-qr-countdown-wrap">
                                <span>Refreshes in <strong id="waModalQrTimer">30</strong>s</span>
                                <button type="button" class="btn-wa-link" id="btnModalRefreshQr">
                                    <i class="fi fi-rr-refresh"></i> Refresh QR
                                </button>
                            </div>
                        </div>

                        <div class="wa-qr-instructions-col">
                            <h4>Scan to Link:</h4>
                            <ol class="wa-steps-list">
                                <li>
                                    <span class="step-num">1</span>
                                    <div class="step-content">Open <strong>WhatsApp</strong> on the target phone</div>
                                </li>
                                <li>
                                    <span class="step-num">2</span>
                                    <div class="step-content">Go to <strong>Linked Devices</strong> &gt; <strong>Link a Device</strong></div>
                                </li>
                                <li>
                                    <span class="step-num">3</span>
                                    <div class="step-content">Point camera at this QR code</div>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Pane 2: Modal Phone Number Pairing Code -->
                <div class="wa-pairing-pane d-none" id="waModalPhonePane">
                    <div class="wa-pairing-phone-box">
                        <div class="wa-phone-form-wrap">
                            <label class="v3-form-label" for="waModalPairingPhoneInput">Phone Number (with Country Code)</label>
                            <div class="wa-phone-input-row">
                                <input type="text" class="v3-input" id="waModalPairingPhoneInput" placeholder="+91 98765 43210" autocomplete="tel">
                                <button type="button" class="btn-integration-primary" id="btnModalRequestPairingCode">
                                    <i class="fi fi-rr-key"></i> <span>Get Code</span>
                                </button>
                            </div>
                        </div>

                        <div class="wa-pairing-code-banner d-none" id="waModalPairingCodeResult">
                            <span class="wa-code-hint">Enter this 8-digit code on WhatsApp:</span>
                            <span class="wa-code-digits" id="waModalPairingCodeValue">---- ----</span>
                            <span class="wa-code-hint">Waiting for pairing confirmation on your mobile phone...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>