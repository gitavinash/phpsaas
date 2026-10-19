<div class="package-page-wrapper integration-plugin-wrapper" id="pluginSmtpApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-smtp">
                <i class="fi fi-rr-envelope"></i>
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
                        <div class="ms-auto">
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
                                    <select id="smtpSsl" name="email_smtp_ssl" class="v3-form-input v3-form-select selectJS2">
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
                                    <input type="password" id="smtpPassword" name="email_smtp_password" class="v3-form-input pr-42" placeholder="••••••••••••••••" autocomplete="new-password" required>
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
                <div class="v3-card-section v3-card-p20 mb-16">
                    <div class="v3-sidebar-header-row">
                        <span class="v3-sidebar-header-badge">Connection Diagnostic</span>
                        <span class="v3-badge v3-badge-green status-sm" id="smtpTestBadge">Test Dispatch</span>
                    </div>
                    <h4 class="v3-sidebar-box-title">Verify Mail Server</h4>
                    <p class="v3-sidebar-desc mb-14">
                        Send a test email to verify host connection, port connectivity, and SMTP authentication before saving.
                    </p>

                    <div class="v3-test-dispatch-wrap">
                        <div class="v3-input-icon-wrap">
                            <i class="fi fi-rr-envelope input-icon"></i>
                            <input type="email" id="smtpTestRecipient" class="v3-form-input v3-input-sm" placeholder="your-email@example.com">
                        </div>
                        <button type="button" id="btnTestSmtpConnection" class="v3-btn-secondary w-full justify-center">
                            <i class="fi fi-rr-paper-plane"></i> <span>Send Test Email</span>
                        </button>
                        <div id="smtpTestFeedback"></div>
                    </div>
                </div>

                <!-- Provider Quick Setup Reference Card -->
                <div class="v3-card-section v3-card-p20">
                    <h4 class="v3-sidebar-box-title mb-10">Popular SMTP Settings</h4>
                    <div class="v3-provider-list">
                        <div class="v3-provider-card">
                            <strong class="v3-provider-name">ZeptoMail / Zoho</strong>
                            <span class="v3-provider-meta">Host: smtp.zeptomail.in | Port: 587 (TLS)</span>
                        </div>
                        <div class="v3-provider-card">
                            <strong class="v3-provider-name">Google Workspace / Gmail</strong>
                            <span class="v3-provider-meta">Host: smtp.gmail.com | Port: 587 (TLS)</span>
                        </div>
                        <div class="v3-provider-card">
                            <strong class="v3-provider-name">Amazon SES</strong>
                            <span class="v3-provider-meta">Host: email-smtp.us-east-1.amazonaws.com | Port: 587</span>
                        </div>
                        <div class="v3-provider-card">
                            <strong class="v3-provider-name">SendGrid</strong>
                            <span class="v3-provider-meta">Host: smtp.sendgrid.net | Port: 587 (TLS)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
