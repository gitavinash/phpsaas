<div class="package-page-wrapper integration-plugin-wrapper" id="pluginSmtpApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="integration-brand-logo brand-smtp" style="width: 48px; height: 48px; border-radius: 12px;">
                <img src="/app/dashboard/v3/default/desktop/assets/images/integration/smtp.svg" alt="SMTP Email" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div class="package-header-titles">
                <h2>SMTP Email Configuration</h2>
                <p>Configure your custom outgoing SMTP email server, encryption, and sender identity for automated notifications.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/integration/" class="btn-v3-action" data-tooltip="Back to All Integrations">
                <i class="fi fi-rr-arrow-left"></i> <span>All Integrations</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Form & Details Layout -->
    <div class="v3-form-split-layout">
        <!-- Left: Configuration Form -->
        <div class="v3-form-main-col">
            <form id="pluginSmtpForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: SMTP Server Credentials -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-key"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>SMTP Server Connection</h3>
                            <p>Specify the outgoing SMTP mail host, port, and authentication credentials.</p>
                        </div>
                        <div style="margin-left: auto;">
                            <label class="v3-toggle-switch" title="Toggle SMTP Email Server">
                                <input type="checkbox" id="smtpActiveToggle" name="enable_smtp_email" value="1" checked>
                                <span class="v3-toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- SMTP Host -->
                            <div class="v3-form-group span-2">
                                <label for="smtpHost" class="v3-form-label">
                                    SMTP Host <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-globe input-icon"></i>
                                    <input type="text" id="smtpHost" name="email_smtp_host" class="v3-form-input" placeholder="e.g. smtp.zeptomail.in or smtp.gmail.com" required>
                                </div>
                            </div>

                            <!-- SMTP Port -->
                            <div class="v3-form-group">
                                <label for="smtpPort" class="v3-form-label">
                                    SMTP Port <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-route input-icon"></i>
                                    <input type="number" id="smtpPort" name="email_smtp_port" class="v3-form-input" placeholder="587" value="587" required>
                                </div>
                            </div>

                            <!-- Encryption Security -->
                            <div class="v3-form-group">
                                <label for="smtpSsl" class="v3-form-label">
                                    Encryption Protocol <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-shield-check input-icon"></i>
                                    <select id="smtpSsl" name="email_smtp_ssl" class="v3-form-input v3-form-select">
                                        <option value="tls" selected>TLS (Port 587)</option>
                                        <option value="ssl">SSL (Port 465)</option>
                                        <option value="false">None / Plain (Port 25)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Username -->
                            <div class="v3-form-group">
                                <label for="smtpUsername" class="v3-form-label">
                                    SMTP Username / API Key <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="smtpUsername" name="email_smtp_username" class="v3-form-input" placeholder="e.g. emailapikey or contact@domain.com" required>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="v3-form-group">
                                <label for="smtpPassword" class="v3-form-label">
                                    SMTP Password / Secret <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-lock input-icon"></i>
                                    <input type="password" id="smtpPassword" name="email_smtp_password" class="v3-form-input" placeholder="••••••••••••••••" autocomplete="new-password" required style="padding-right: 42px !important;">
                                    <button type="button" class="v3-input-action-btn" id="btnToggleSmtpPassword" title="Show password" aria-label="Toggle password visibility">
                                        <i class="fi fi-rr-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Sender & Reply-To Identities -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-envelope"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Sender &amp; Reply-To Identity</h3>
                            <p>Define the from-name and reply addresses displayed in clients' mailboxes.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- From Email Address -->
                            <div class="v3-form-group">
                                <label for="smtpEmail" class="v3-form-label">
                                    Sender From Email <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-at input-icon"></i>
                                    <input type="email" id="smtpEmail" name="email_smtp_email" class="v3-form-input" placeholder="e.g. donotreply@yourdomain.com" required>
                                </div>
                            </div>

                            <!-- Sender Display Name -->
                            <div class="v3-form-group">
                                <label for="smtpName" class="v3-form-label">
                                    Sender Display Name
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="smtpName" name="email_smtp_name" class="v3-form-input" placeholder="e.g. Holidays &amp; Travel Co.">
                                </div>
                            </div>

                            <!-- Reply-to Email Address -->
                            <div class="v3-form-group">
                                <label for="smtpReplyEmail" class="v3-form-label">
                                    Reply-To Email Address
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-envelope input-icon"></i>
                                    <input type="email" id="smtpReplyEmail" name="email_smtp_reply_email" class="v3-form-input" placeholder="e.g. support@yourdomain.com">
                                </div>
                            </div>

                            <!-- Reply-to Name -->
                            <div class="v3-form-group">
                                <label for="smtpReplyName" class="v3-form-label">
                                    Reply-To Display Name
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="smtpReplyName" name="email_smtp_reply_name" class="v3-form-input" placeholder="e.g. Support Team">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/integration/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" id="btnSaveSmtpPlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save SMTP Settings</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Test Dispatcher & Reference Sidebar -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Test Email Card -->
                <div class="v3-card-section" style="padding: 20px; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Connection Diagnostic</span>
                        <span class="v3-badge v3-badge-green" id="smtpTestBadge" style="font-size: 10px;">Test Dispatch</span>
                    </div>
                    <h4 style="font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">Verify Mail Server</h4>
                    <p style="font-size: 12.5px; color: #64748b; margin: 0 0 14px 0; line-height: 1.45;">
                        Send a test email to verify host connection, port connectivity, and SMTP authentication before saving.
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div class="v3-input-icon-wrap">
                            <i class="fi fi-rr-envelope input-icon"></i>
                            <input type="email" id="smtpTestRecipient" class="v3-form-input" placeholder="your-email@example.com" style="font-size: 12.5px;">
                        </div>
                        <button type="button" id="btnTestSmtpConnection" class="v3-btn-secondary" style="width: 100%; justify-content: center;">
                            <i class="fi fi-rr-paper-plane"></i> <span>Send Test Email</span>
                        </button>
                        <div id="smtpTestFeedback"></div>
                    </div>
                </div>

                <!-- Provider Quick Setup Reference Card -->
                <div class="v3-card-section" style="padding: 20px;">
                    <h4 style="font-size: 13.5px; font-weight: 700; color: #0f172a; margin: 0 0 10px 0;">Popular SMTP Settings</h4>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                            <strong style="font-size: 12px; color: #0f172a; display: block; margin-bottom: 2px;">ZeptoMail / Zoho</strong>
                            <span style="font-size: 11.5px; color: #64748b; display: block; font-family: monospace;">Host: smtp.zeptomail.in | Port: 587 (TLS)</span>
                        </div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                            <strong style="font-size: 12px; color: #0f172a; display: block; margin-bottom: 2px;">Google Workspace / Gmail</strong>
                            <span style="font-size: 11.5px; color: #64748b; display: block; font-family: monospace;">Host: smtp.gmail.com | Port: 587 (TLS)</span>
                        </div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                            <strong style="font-size: 12px; color: #0f172a; display: block; margin-bottom: 2px;">Amazon SES</strong>
                            <span style="font-size: 11.5px; color: #64748b; display: block; font-family: monospace;">Host: email-smtp.us-east-1.amazonaws.com | Port: 587</span>
                        </div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                            <strong style="font-size: 12px; color: #0f172a; display: block; margin-bottom: 2px;">SendGrid</strong>
                            <span style="font-size: 11.5px; color: #64748b; display: block; font-family: monospace;">Host: smtp.sendgrid.net | Port: 587 (TLS)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
