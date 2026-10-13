<?php
/**
 * Modal: WhatsApp Device Connection Required
 * Displayed when user clicks 'Chat on WhatsApp' but no WhatsApp device is connected/linked
 * Path: /app/dashboard/v3/default/desktop/templates/whatsapp/device_connect_modal.php
 */
?>
<div class="v3-modal-overlay wa-connect-prompt-modal-overlay" id="modalWaConnectDevicePrompt" aria-hidden="true" role="dialog" aria-labelledby="waConnectPromptModalTitle">
    <div class="v3-modal-dialog v3-modal-dialog-md wa-connect-prompt-dialog">
        <div class="v3-modal-header">
            <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box wa-connect-header-icon">
                    <i class="fi fi-brands-whatsapp"></i>
                </div>
                <div class="v3-modal-title-wrap">
                    <h4 class="v3-modal-title" id="waConnectPromptModalTitle">Connect Device to Start WhatsApp Msg</h4>
                    <span class="v3-modal-subtitle">WhatsApp CRM Live Messaging</span>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" aria-label="Close dialog" title="Close"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body wa-connect-prompt-body">
            <div class="wa-connect-illustration-wrap">
                <div class="wa-connect-pulse-ring">
                    <div class="wa-connect-phone-icon">
                        <i class="fi fi-rr-smartphone"></i>
                        <span class="wa-connect-status-indicator">
                            <i class="fi fi-rr-cross"></i>
                        </span>
                    </div>
                </div>
            </div>
            <h3 class="wa-connect-prompt-headline">No WhatsApp Device Connected</h3>
            <p class="wa-connect-prompt-desc">
                Please connect your WhatsApp device to start chatting with leads directly from your CRM inbox.
            </p>
            <div class="wa-connect-lead-preview d-none" id="waConnectLeadPreview">
                <div class="wa-connect-lead-info">
                    <span class="wa-connect-lead-label">Lead Contact:</span>
                    <strong class="wa-connect-lead-name" id="waConnectLeadName">-</strong>
                    <span class="wa-connect-lead-phone" id="waConnectLeadPhone">-</span>
                </div>
            </div>
            <div class="wa-connect-info-callout">
                <i class="fi fi-rr-info"></i>
                <span>Linking your device allows real-time incoming messages, live team replies, and full conversation history inside CRM.</span>
            </div>
        </div>
        <div class="v3-modal-footer wa-connect-prompt-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <a href="/dashboard/integration/whatsapp_web/" class="btn-v3-action btn-wa-connect-primary" id="btnGoToConnectWaDevice">
                <i class="fi fi-rr-qr-scan"></i> <span>Connect WhatsApp Device</span>
            </a>
        </div>
    </div>
</div>
