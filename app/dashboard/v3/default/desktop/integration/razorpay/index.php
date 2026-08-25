<div class="package-page-wrapper integration-plugin-wrapper" id="pluginRazorpayApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-razorpay" style="background: #e0f2fe; color: #0284c7; font-size: 24px; display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 12px;">
                <i class="fi fi-rr-credit-card"></i>
            </div>
            <div class="package-header-titles">
                <h2>Razorpay Payment Gateway</h2>
                <p>Accept online card, UPI, and Netbanking payments with automated booking milestone reconciliation.</p>
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
            <form id="pluginRazorpayForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: API Keys -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-key"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Razorpay API Keys</h3>
                            <p>Enter your Key ID and Key Secret from Razorpay Dashboard &gt; API Keys.</p>
                        </div>
                        <div style="margin-left: auto;">
                            <label class="v3-toggle-switch" title="Toggle Razorpay Integration">
                                <input type="checkbox" id="rzpActiveToggle" name="enable_razorpay" value="1" checked>
                                <span class="v3-toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Mode Selection -->
                            <div class="v3-form-group span-2">
                                <label class="v3-form-label">Environment Mode</label>
                                <div style="display: flex; gap: 14px; margin-top: 4px;">
                                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1.5px solid #e2e8f0; border-radius: 8px; background: #ffffff; cursor: pointer; font-size: 13px; font-weight: 500; color: #334155;">
                                        <input type="radio" name="razorpay_mode" id="rzpModeLive" value="live" checked>
                                        <span><strong style="color: #10b981;">Live Production</strong> (Real customer payments)</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1.5px solid #e2e8f0; border-radius: 8px; background: #ffffff; cursor: pointer; font-size: 13px; font-weight: 500; color: #334155;">
                                        <input type="radio" name="razorpay_mode" id="rzpModeTest" value="test">
                                        <span><strong style="color: #ea580c;">Test Sandbox</strong> (rzp_test_...)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Key ID -->
                            <div class="v3-form-group span-2">
                                <label for="rzpKeyId" class="v3-form-label">
                                    Key ID <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-key input-icon"></i>
                                    <input type="text" id="rzpKeyId" name="razorpay_key_id" class="v3-form-input" placeholder="rzp_live_..." required>
                                </div>
                            </div>

                            <!-- Key Secret -->
                            <div class="v3-form-group span-2">
                                <label for="rzpKeySecret" class="v3-form-label">
                                    Key Secret <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-lock input-icon"></i>
                                    <input type="password" id="rzpKeySecret" name="razorpay_key_secret" class="v3-form-input" placeholder="Enter Razorpay Key Secret" autocomplete="new-password" required>
                                </div>
                            </div>

                            <!-- Webhook Secret -->
                            <div class="v3-form-group span-2">
                                <label for="rzpWebhookSecret" class="v3-form-label">
                                    Webhook Secret (Optional)
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-shield-check input-icon"></i>
                                    <input type="password" id="rzpWebhookSecret" name="razorpay_webhook_secret" class="v3-form-input" placeholder="Webhook signature secret">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Automation Rules -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-receipt"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Payment Reconciliation &amp; Receipt</h3>
                            <p>Automate booking ledger updates upon successful payment captures.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <label style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; background: #ffffff; cursor: pointer;">
                            <input type="checkbox" id="rzpAutoReceipt" name="razorpay_auto_receipt" value="1" checked style="margin-top: 3px;">
                            <div>
                                <strong style="color: #0f172a; font-size: 13.5px; display: block;">Instant Booking Confirmation &amp; GST Receipt</strong>
                                <span style="color: #64748b; font-size: 12.5px;">Automatically mark booking payment status as 'Paid' and email the customer a stamped tax invoice upon webhook confirmation.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/integration/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" id="btnSaveRazorpayPlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save Razorpay Settings</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Webhook Documentation -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Webhook Details Box -->
                <div class="v3-card-section" style="padding: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Webhook Listener</span>
                        <span class="v3-status-badge status-active" style="font-size: 10px;">Automated</span>
                    </div>
                    <p style="font-size: 12.5px; color: #64748b; margin: 0 0 12px 0; line-height: 1.45;">
                        Configure this Webhook URL inside Razorpay Dashboard &gt; Webhooks &gt; Add New Webhook:
                    </p>
                    
                    <div class="v3-code-snippet-box" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-family: monospace; font-size: 11.5px; color: #334155; word-break: break-all; margin-bottom: 12px;">
                        https://app.aleya.in/webhook/razorpay/
                    </div>

                    <h5 style="font-size: 12px; font-weight: 700; color: #334155; margin: 0 0 6px 0;">Active Events to Subscribe:</h5>
                    <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: #64748b; line-height: 1.6;">
                        <li><code>payment.captured</code></li>
                        <li><code>payment.failed</code></li>
                        <li><code>payment_link.paid</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
