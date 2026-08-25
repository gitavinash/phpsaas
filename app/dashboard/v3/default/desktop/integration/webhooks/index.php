<div class="package-page-wrapper integration-plugin-wrapper" id="pluginWebhooksApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-webhooks" style="background: #ede9fe; color: #7c3aed; font-size: 24px; display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 12px;">
                <i class="fi fi-rr-code-compare"></i>
            </div>
            <div class="package-header-titles">
                <h2>Custom CRM Outbound Webhooks</h2>
                <p>Stream real-time lead and booking payloads to external endpoints, Zapier, Make, and backend services.</p>
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
            <form id="pluginWebhooksForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Outbound URLs -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon violet">
                            <i class="fi fi-rr-link-alt"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Event Endpoint Destinations</h3>
                            <p>Enter the HTTPS destination URLs for each event trigger.</p>
                        </div>
                        <div style="margin-left: auto;">
                            <label class="v3-toggle-switch" title="Toggle Webhooks Integration">
                                <input type="checkbox" id="whActiveToggle" name="enable_webhooks" value="1" checked>
                                <span class="v3-toggle-slider violet"></span>
                            </label>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Lead Created Webhook -->
                            <div class="v3-form-group span-2">
                                <label for="whLeadUrl" class="v3-form-label">
                                    New Lead Ingested (<code>lead.created</code>)
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-bullseye-arrow input-icon"></i>
                                    <input type="url" id="whLeadUrl" name="webhook_lead_created_url" class="v3-form-input" placeholder="https://hooks.zapier.com/hooks/catch/...">
                                </div>
                            </div>

                            <!-- Payment Received Webhook -->
                            <div class="v3-form-group span-2">
                                <label for="whPaymentUrl" class="v3-form-label">
                                    Payment Received &amp; Reconciled (<code>payment.received</code>)
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-credit-card input-icon"></i>
                                    <input type="url" id="whPaymentUrl" name="webhook_payment_received_url" class="v3-form-input" placeholder="https://hook.eu1.make.com/...">
                                </div>
                            </div>

                            <!-- Tour Booked Webhook -->
                            <div class="v3-form-group span-2">
                                <label for="whTourUrl" class="v3-form-label">
                                    Booking Confirmed (<code>tour.booked</code>)
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-suitcase-alt input-icon"></i>
                                    <input type="url" id="whTourUrl" name="webhook_tour_booked_url" class="v3-form-input" placeholder="https://yourdomain.com/api/bookings/sync/">
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
                    <button type="submit" id="btnSaveWebhooksPlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save Webhook Settings</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Security Signing Secret -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Secret Box -->
                <div class="v3-card-section" style="padding: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Security Signature</span>
                        <span class="v3-status-badge status-active" style="font-size: 10px;">HMAC-SHA256</span>
                    </div>
                    <p style="font-size: 12.5px; color: #64748b; margin: 0 0 12px 0; line-height: 1.45;">
                        Every outgoing request includes an <code>X-Aleya-Signature</code> header signed with this secret key:
                    </p>
                    
                    <label class="v3-form-label" style="font-size: 12px; margin-bottom: 4px;">Signing Secret</label>
                    <input type="text" id="whSecretKey" name="webhook_secret_key" class="v3-form-input" readonly style="background: #f1f5f9; font-family: monospace; font-size: 12px; margin-bottom: 12px;">

                    <div style="font-size: 11.5px; color: #64748b; line-height: 1.5;">
                        Requests timeout after <strong>5 seconds</strong>. If your endpoint returns a non-2xx status, we retry 3 times with exponential backoff.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
