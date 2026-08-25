<div class="package-page-wrapper integration-plugin-wrapper" id="pluginWhatsappApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-whatsapp" style="background: #e6f9ed; color: #10b981; font-size: 24px; display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 12px;">
                <span class="v3-wa-icon-svg" style="width: 24px; height: 24px; color: #10b981;"><svg viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg></span>
            </div>
            <div class="package-header-titles">
                <h2>WhatsApp Cloud API &amp; Notify360</h2>
                <p>Configure Meta Cloud API credentials, webhook verification tokens, and automated message triggers.</p>
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
            <form id="pluginWhatsappForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: API Credentials -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-key"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Meta Cloud API Credentials</h3>
                            <p>Enter your Meta Developer App &amp; WhatsApp Business Account tokens.</p>
                        </div>
                        <div style="margin-left: auto;">
                            <label class="v3-toggle-switch" title="Toggle WhatsApp Integration">
                                <input type="checkbox" id="waActiveToggle" name="enable_whatsapp_business_api" value="1" checked>
                                <span class="v3-toggle-slider emerald"></span>
                            </label>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Phone Number ID -->
                            <div class="v3-form-group">
                                <label for="waPhoneNumberId" class="v3-form-label">
                                    Phone Number ID <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-phone-call input-icon"></i>
                                    <input type="text" id="waPhoneNumberId" name="whatsapp_phone_number_id" class="v3-form-input" placeholder="e.g. 109876543210987">
                                </div>
                            </div>

                            <!-- WABA Account ID -->
                            <div class="v3-form-group">
                                <label for="waWabaId" class="v3-form-label">
                                    WhatsApp Business Account ID (WABA)
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-briefcase input-icon"></i>
                                    <input type="text" id="waWabaId" name="whatsapp_waba_id" class="v3-form-input" placeholder="e.g. 209876543210987">
                                </div>
                            </div>

                            <!-- Permanent Access Token -->
                            <div class="v3-form-group span-2">
                                <label for="waAccessToken" class="v3-form-label">
                                    System User Permanent Access Token <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-lock input-icon"></i>
                                    <input type="password" id="waAccessToken" name="whatsapp_access_token" class="v3-form-input" placeholder="EAAB..." autocomplete="new-password">
                                </div>
                                <span class="v3-field-hint">Generate a permanent System User Token in Meta Business Manager with <code>whatsapp_business_messaging</code>.</span>
                            </div>

                            <!-- Sender WhatsApp Display Number -->
                            <div class="v3-form-group span-2">
                                <label for="waCompanyNumber" class="v3-form-label">
                                    Official Sender WhatsApp Number
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <span class="v3-wa-icon-svg input-icon-svg"><svg viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg></span>
                                    <input type="tel" id="waCompanyNumber" name="company_whatsapp_number" class="v3-form-input" placeholder="e.g. 9958719998">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Automated Triggers -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-bolt"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Milestone Event Autoresponders</h3>
                            <p>Toggle automated WhatsApp notifications dispatched to travelers.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div style="display: flex; flex-direction: column; gap: 14px;">
                            <label style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; background: #ffffff; cursor: pointer;">
                                <input type="checkbox" id="waEvtEnquiry" name="company_whatsapp_enquiry_thank_you" value="1" checked style="margin-top: 3px;">
                                <div>
                                    <strong style="color: #0f172a; font-size: 13.5px; display: block;">Instant Inquiry Welcome Brochure</strong>
                                    <span style="color: #64748b; font-size: 12.5px;">Auto-reply immediately when a new website inquiry or lead form arrives.</span>
                                </div>
                            </label>

                            <label style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; background: #ffffff; cursor: pointer;">
                                <input type="checkbox" id="waEvtPayment" name="company_whatsapp_payment_request" value="1" checked style="margin-top: 3px;">
                                <div>
                                    <strong style="color: #0f172a; font-size: 13.5px; display: block;">Payment Request &amp; Dynamic QR Link</strong>
                                    <span style="color: #64748b; font-size: 12.5px;">Send interactive payment reminders and receipt confirmations.</span>
                                </div>
                            </label>

                            <label style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; background: #ffffff; cursor: pointer;">
                                <input type="checkbox" id="waEvtItinerary" name="company_whatsapp_itinerary_sent" value="1" checked style="margin-top: 3px;">
                                <div>
                                    <strong style="color: #0f172a; font-size: 13.5px; display: block;">Quotation &amp; PDF Itinerary Dispatch</strong>
                                    <span style="color: #64748b; font-size: 12.5px;">Deliver high-res day-wise tour itinerary PDFs directly to WhatsApp.</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/integration/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" id="btnSaveWhatsappPlugin" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span>Save WhatsApp Settings</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Webhook Setup & Documentation -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Webhook Setup Box -->
                <div class="v3-card-section" style="padding: 20px; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Webhook Configuration</span>
                        <span class="v3-status-badge status-active" style="font-size: 10px;">Cloud API</span>
                    </div>
                    <p style="font-size: 12.5px; color: #64748b; margin: 0 0 12px 0; line-height: 1.45;">
                        Configure this Callback URL in Meta App Dashboard under WhatsApp &gt; Configuration:
                    </p>
                    
                    <div class="v3-code-snippet-box" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-family: monospace; font-size: 11.5px; color: #334155; word-break: break-all; margin-bottom: 10px;">
                        https://app.aleya.in/webhook/whatsapp/
                    </div>

                    <label class="v3-form-label" style="font-size: 12px; margin-bottom: 4px;">Webhook Verify Token</label>
                    <input type="text" id="waVerifyToken" class="v3-form-input" readonly value="isw_wa_secret_verify" style="background: #f1f5f9; font-family: monospace; font-size: 12px;">
                </div>

                <!-- Notify360 Badge -->
                <div class="v3-matrix-card" style="padding: 16px; text-align: center;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">Delivery Engine</div>
                    <img src="/app/dashboard/v3/default/desktop/assets/images/notify360.png" alt="Notify360" style="height: 22px; width: auto; object-fit: contain; margin-bottom: 6px;">
                    <p style="font-size: 11.5px; color: #64748b; margin: 0;">Enterprise WhatsApp messaging layer with 99.9% uptime SLA.</p>
                </div>
            </div>
        </div>
    </div>
</div>
