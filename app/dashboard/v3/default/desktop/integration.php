<?php
// Workspace Rule Compliant: No inline SQL queries, styles, or scripts.
// All styles are in admin.css and dynamic interactions in integration.js.
?>

<div class="package-page-wrapper integration-page-wrapper" id="integrationApp">
    <!-- Header Banner -->
    <div class="package-header-bar integration-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon integration-header-icon">
                <i class="fi fi-rr-apps-add"></i>
            </div>
            <div class="package-header-titles">
                <h2>Integrations & Connected Channels</h2>
                <p>Connect your advertising campaigns, instant messaging, payment gateways, and CRM webhooks.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <div class="integration-search-box">
                <i class="fi fi-rr-search"></i>
                <input type="text" id="integrationSearchInput" placeholder="Search integrations by name, feature..." class="integration-search-input">
            </div>
        </div>
    </div>

    <!-- Category Filter Tabs & Webhook Health Status -->
    <div class="integration-toolbar-row">
        <div class="integration-category-tabs">
            <button type="button" class="integration-cat-tab active" data-category="all">
                <i class="fi fi-rr-apps"></i> All Channels <span class="tab-count">8</span>
            </button>
            <button type="button" class="integration-cat-tab" data-category="leadgen">
                <i class="fi fi-rr-bullseye-arrow"></i> Lead Gen & Ads <span class="tab-count">2</span>
            </button>
            <button type="button" class="integration-cat-tab" data-category="messaging">
                <i class="fi fi-rr-comment-alt-middle"></i> Messaging & Chat <span class="tab-count">2</span>
            </button>
            <button type="button" class="integration-cat-tab" data-category="payments">
                <i class="fi fi-rr-credit-card"></i> Payments & Banking <span class="tab-count">3</span>
            </button>
            <button type="button" class="integration-cat-tab" data-category="developer">
                <i class="fi fi-rr-code-compare"></i> Developer & API <span class="tab-count">1</span>
            </button>
        </div>

        <div class="integration-live-indicator">
            <span class="live-dot pulse"></span>
            <span class="live-text">Webhooks Engine: <strong>Operational</strong></span>
        </div>
    </div>

    <!-- Integrations Grid -->
    <div class="integration-grid" id="integrationGrid">
        
        <!-- 1. Facebook Lead Ads -->
        <div class="integration-card" data-category="leadgen">
            <div>
                <div class="integration-card-top">
                    <div class="integration-card-identity">
                        <div class="integration-icon-wrap brand-facebook">
                            <svg viewBox="0 0 24 24" width="26" height="26" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </div>
                        <div class="integration-meta">
                            <h3>Facebook Lead Ads</h3>
                            <span class="integration-tag">Lead Generation</span>
                        </div>
                    </div>
                    <span class="integration-status-badge active"><i class="fi fi-rr-check"></i> Connected</span>
                </div>
                <div class="integration-card-body">
                    <p class="integration-desc">Capture real-time traveler inquiries from Facebook & Instagram ad campaigns with automated webhook synchronization.</p>
                    <ul class="integration-feature-list">
                        <li><i class="fi fi-rr-check-circle"></i> Instant real-time lead ingestion</li>
                        <li><i class="fi fi-rr-check-circle"></i> Automated page & form subscription</li>
                        <li><i class="fi fi-rr-check-circle"></i> Source and adset attribution</li>
                    </ul>
                </div>
            </div>
            <div class="integration-card-footer">
                <a href="integration/facebook/" class="btn-integration-primary">
                    <i class="fi fi-rr-settings-sliders"></i> Configure Integration
                </a>
            </div>
        </div>

        <!-- 2. WhatsApp Cloud API -->
        <div class="integration-card" data-category="messaging">
            <div>
                <div class="integration-card-top">
                    <div class="integration-card-identity">
                        <div class="integration-icon-wrap brand-whatsapp">
                            <svg viewBox="0 0 24 24" width="26" height="26" fill="currentColor">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                        </div>
                        <div class="integration-meta">
                            <h3>WhatsApp Cloud API</h3>
                            <span class="integration-tag">Messaging</span>
                        </div>
                    </div>
                    <span class="integration-status-badge active"><i class="fi fi-rr-check"></i> Active</span>
                </div>
                <div class="integration-card-body">
                    <p class="integration-desc">Deliver interactive travel itineraries, automatic booking confirmations, and quotation PDFs directly to travelers' WhatsApp.</p>
                    <ul class="integration-feature-list">
                        <li><i class="fi fi-rr-check-circle"></i> 1-Click Itinerary PDF dispatch</li>
                        <li><i class="fi fi-rr-check-circle"></i> Milestone notifications & payment alerts</li>
                        <li><i class="fi fi-rr-check-circle"></i> Verified WhatsApp Business Profile</li>
                    </ul>
                </div>
            </div>
            <div class="integration-card-footer">
                <a href="setting/whatsapp/" class="btn-integration-primary">
                    <i class="fi fi-rr-comment-alt-middle"></i> Manage WhatsApp
                </a>
            </div>
        </div>

        <!-- 3. Google Ads & Lead Extensions -->
        <div class="integration-card" data-category="leadgen">
            <div>
                <div class="integration-card-top">
                    <div class="integration-card-identity">
                        <div class="integration-icon-wrap brand-google">
                            <svg viewBox="0 0 24 24" width="26" height="26">
                                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.98 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                            </svg>
                        </div>
                        <div class="integration-meta">
                            <h3>Google Ads & Forms</h3>
                            <span class="integration-tag">Lead Generation</span>
                        </div>
                    </div>
                    <span class="integration-status-badge ready"><i class="fi fi-rr-link-alt"></i> Available</span>
                </div>
                <div class="integration-card-body">
                    <p class="integration-desc">Track offline conversion milestones, ingest Google Search lead extension forms, and calculate tour campaign ROI.</p>
                    <ul class="integration-feature-list">
                        <li><i class="fi fi-rr-check-circle"></i> GCLID conversion tracking & sync</li>
                        <li><i class="fi fi-rr-check-circle"></i> Performance Max form webhooks</li>
                        <li><i class="fi fi-rr-check-circle"></i> Multi-channel campaign attribution</li>
                    </ul>
                </div>
            </div>
            <div class="integration-card-footer">
                <button type="button" class="btn-integration-outline btn-connect-google">
                    <i class="fi fi-rr-link-alt"></i> Connect Google Ads
                </button>
            </div>
        </div>

        <!-- 4. Razorpay Payment Gateway -->
        <div class="integration-card" data-category="payments">
            <div>
                <div class="integration-card-top">
                    <div class="integration-card-identity">
                        <div class="integration-icon-wrap brand-razorpay">
                            <svg viewBox="0 0 24 24" width="26" height="26" fill="currentColor">
                                <path d="M14.07 0L4.54 14.12h7.32l-1.93 9.88L21.46 9.88h-7.39z"/>
                            </svg>
                        </div>
                        <div class="integration-meta">
                            <h3>Razorpay Gateway</h3>
                            <span class="integration-tag">Payments</span>
                        </div>
                    </div>
                    <span class="integration-status-badge active"><i class="fi fi-rr-check"></i> Connected</span>
                </div>
                <div class="integration-card-body">
                    <p class="integration-desc">Collect online credit/debit card, netbanking, and EMI payments with automatic invoice generation and booking status updates.</p>
                    <ul class="integration-feature-list">
                        <li><i class="fi fi-rr-check-circle"></i> Dynamic booking payment links</li>
                        <li><i class="fi fi-rr-check-circle"></i> Auto-reconcile booking milestone status</li>
                        <li><i class="fi fi-rr-check-circle"></i> Instant automated receipt generation</li>
                    </ul>
                </div>
            </div>
            <div class="integration-card-footer">
                <a href="integration/razorpay/" class="btn-integration-primary">
                    <i class="fi fi-rr-credit-card"></i> Manage Razorpay
                </a>
            </div>
        </div>

        <!-- 5. UPI & Instant QR Code -->
        <div class="integration-card" data-category="payments">
            <div>
                <div class="integration-card-top">
                    <div class="integration-card-identity">
                        <div class="integration-icon-wrap brand-upi">
                            <i class="fi fi-rr-qr-scan"></i>
                        </div>
                        <div class="integration-meta">
                            <h3>UPI & Dynamic QR</h3>
                            <span class="integration-tag">Payments</span>
                        </div>
                    </div>
                    <span class="integration-status-badge active"><i class="fi fi-rr-check"></i> Enabled</span>
                </div>
                <div class="integration-card-body">
                    <p class="integration-desc">Embed custom dynamic UPI QR codes directly inside itinerary PDFs and payment links for zero-fee instant mobile transactions.</p>
                    <ul class="integration-feature-list">
                        <li><i class="fi fi-rr-check-circle"></i> 0% transaction fee direct collection</li>
                        <li><i class="fi fi-rr-check-circle"></i> Custom VPA & Business UPI ID</li>
                        <li><i class="fi fi-rr-check-circle"></i> Auto-embedded on PDF proposals</li>
                    </ul>
                </div>
            </div>
            <div class="integration-card-footer">
                <a href="setting/company/" class="btn-integration-outline">
                    <i class="fi fi-rr-settings"></i> Configure UPI ID
                </a>
            </div>
        </div>

        <!-- 6. Bank Transfer / NEFT & IMPS -->
        <div class="integration-card" data-category="payments">
            <div>
                <div class="integration-card-top">
                    <div class="integration-card-identity">
                        <div class="integration-icon-wrap brand-bank">
                            <i class="fi fi-rr-bank"></i>
                        </div>
                        <div class="integration-meta">
                            <h3>Bank Wire Transfer</h3>
                            <span class="integration-tag">Banking</span>
                        </div>
                    </div>
                    <span class="integration-status-badge active"><i class="fi fi-rr-check"></i> Enabled</span>
                </div>
                <div class="integration-card-body">
                    <p class="integration-desc">Display official verified company bank account details on vouchers with manual payment ledger recording and UTR tracking.</p>
                    <ul class="integration-feature-list">
                        <li><i class="fi fi-rr-check-circle"></i> Multi-bank account configuration</li>
                        <li><i class="fi fi-rr-check-circle"></i> UTR & transaction verification</li>
                        <li><i class="fi fi-rr-check-circle"></i> Multi-currency accounts ready</li>
                    </ul>
                </div>
            </div>
            <div class="integration-card-footer">
                <a href="setting/company/" class="btn-integration-outline">
                    <i class="fi fi-rr-bank"></i> Manage Accounts
                </a>
            </div>
        </div>

        <!-- 7. Email & Transactional SMTP -->
        <div class="integration-card" data-category="messaging">
            <div>
                <div class="integration-card-top">
                    <div class="integration-card-identity">
                        <div class="integration-icon-wrap brand-email">
                            <i class="fi fi-rr-envelope"></i>
                        </div>
                        <div class="integration-meta">
                            <h3>Email & SMTP Engine</h3>
                            <span class="integration-tag">Communication</span>
                        </div>
                    </div>
                    <span class="integration-status-badge active"><i class="fi fi-rr-check"></i> Active</span>
                </div>
                <div class="integration-card-body">
                    <p class="integration-desc">High-deliverability email infrastructure to dispatch branded travel itineraries, tax invoices, and confirmation vouchers.</p>
                    <ul class="integration-feature-list">
                        <li><i class="fi fi-rr-check-circle"></i> DKIM & SPF authenticated relay</li>
                        <li><i class="fi fi-rr-check-circle"></i> Open and click rate analytics</li>
                        <li><i class="fi fi-rr-check-circle"></i> Custom domain SMTP relay (SES/SendGrid)</li>
                    </ul>
                </div>
            </div>
            <div class="integration-card-footer">
                <a href="setting/email/" class="btn-integration-outline">
                    <i class="fi fi-rr-settings"></i> SMTP Settings
                </a>
            </div>
        </div>

        <!-- 8. Webhooks & Developer REST API -->
        <div class="integration-card" data-category="developer">
            <div>
                <div class="integration-card-top">
                    <div class="integration-card-identity">
                        <div class="integration-icon-wrap brand-webhook">
                            <i class="fi fi-rr-code-compare"></i>
                        </div>
                        <div class="integration-meta">
                            <h3>Webhooks & REST API</h3>
                            <span class="integration-tag">Developer Tools</span>
                        </div>
                    </div>
                    <span class="integration-status-badge ready"><i class="fi fi-rr-code-branch"></i> Ready</span>
                </div>
                <div class="integration-card-body">
                    <p class="integration-desc">Real-time incoming and outgoing webhooks to connect custom website forms, third-party CRMs, and Zapier automations.</p>
                    <ul class="integration-feature-list">
                        <li><i class="fi fi-rr-check-circle"></i> Real-time JSON webhook endpoint</li>
                        <li><i class="fi fi-rr-check-circle"></i> Secure token authorization</li>
                        <li><i class="fi fi-rr-check-circle"></i> Automated lead parsing & mapping</li>
                    </ul>
                </div>
            </div>
            <div class="integration-card-footer">
                <button type="button" class="btn-integration-outline btn-show-webhook-modal">
                    <i class="fi fi-rr-code-branch"></i> View Webhook URL
                </button>
            </div>
        </div>

        <!-- Empty State for Search / Filter -->
        <div class="integration-empty-state is-hidden" id="integrationEmptyState">
            <i class="fi fi-rr-search-alt"></i>
            <h4>No integrations match your search</h4>
            <p>Try adjusting your search keywords or select a different category filter above.</p>
        </div>

    </div>
</div>

<!-- Modal: Webhook & Developer API Details -->
<div class="v3-modal-overlay" id="modalWebhookDetails">
    <div class="v3-modal-dialog v3-modal-dialog-md">
        <div class="v3-modal-header">
            <h4 class="v3-modal-title"><i class="fi fi-rr-code-compare"></i> Incoming Webhook Integration</h4>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <h5 class="v3-modal-subheading">Your Ingestion Webhook URL</h5>
            <p class="v3-modal-label">Send HTTP POST requests with JSON payloads to this secure endpoint to automatically ingest leads into your pipeline:</p>
            
            <div class="v3-code-block">
                <span id="webhookEndpointText">https://app.aleya.in/webhook/leads/</span>
                <button type="button" class="v3-btn-copy-code" id="btnCopyWebhookUrl"><i class="fi fi-rr-copy"></i> Copy URL</button>
            </div>

            <h5 class="v3-modal-subheading">Sample JSON Payload (POST)</h5>
            <pre class="v3-code-json">{
  "first_name": "Ranjit",
  "last_name": "Sarvaiya",
  "email": "ranjitsinh2007@gmail.com",
  "mobile": "9173251340",
  "subject": "Kerala Tour Package",
  "source": "Website Form",
  "adults": 2,
  "children": 0,
  "duration_days": 5
}</pre>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Close</button>
        </div>
    </div>
</div>

<script src="<?php echo ADMIN_JS ?>integration.js?v=<?php echo time(); ?>"></script>