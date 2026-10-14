<?php
/**
 * Link Shortener Landing Page
 * Reference UI: https://aleya.in
 */

$siteTitle = defined('SITE_NAME') && !empty(SITE_NAME) ? SITE_NAME : 'Aleya QuickLink';
$siteDesc  = defined('SITE_DESC') && !empty(SITE_DESC) ? SITE_DESC : 'Lightning-fast, branded URL shortening for travel agencies, itineraries, customer quotes, and payment links.';
$siteDomain = defined('SITE_DOMAIN') && !empty(SITE_DOMAIN) ? SITE_DOMAIN : 'dc.local';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?> — Premium Link Shortening Engine</title>
    <meta name="description" content="<?php echo htmlspecialchars($siteDesc, ENT_QUOTES, 'UTF-8'); ?>">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?> — Shorten, Share & Track">
    <meta property="og:description" content="<?php echo htmlspecialchars($siteDesc, ENT_QUOTES, 'UTF-8'); ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- External Stylesheet -->
    <link rel="stylesheet" href="/app/link_shortener/visitor/default/desktop/assets/css/style.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>

    <!-- Header Navigation -->
    <nav class="nav" id="mainNav">
        <div class="nav-container">
            <a href="/" class="nav-logo">
                <div class="nav-logo-icon">
                    <i class="fa-solid fa-link"></i>
                </div>
                <span><?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?></span>
            </a>

            <div class="nav-menu" id="navMenu">
                <a href="#features" class="nav-link">Features</a>
                <a href="#how-it-works" class="nav-link">How It Works</a>
                <a href="#use-cases" class="nav-link">Use Cases</a>
                <a href="#faq" class="nav-link">FAQ</a>
            </div>

            <div class="nav-actions">
                <a href="#shortenerCard" class="nav-btn primary">
                    <span>Shorten Link</span>
                    <i class="fa-solid fa-arrow-down"></i>
                </a>
            </div>

            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation menu">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-bg">
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>
            <div class="grid-lines"></div>
        </div>

        <div class="container hero-container">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="pulse"></span>
                    <span>Sub-15ms 301 Redirection • Dynamic QR Generator</span>
                </div>

                <h1 class="hero-title">
                    <span class="line">Shorten, Share &amp; Track</span>
                    <span class="line highlight">Links on Autopilot.</span>
                </h1>

                <p class="hero-subtitle">
                    Turn cumbersome URLs into clean, high-converting branded short links. Effortlessly share travel itineraries, customer quotations, invoices, and instant payment links across WhatsApp, SMS, and Email.
                </p>

                <!-- URL Shortener Interactive Box -->
                <div class="shortener-card" id="shortenerCard">
                    <form class="shortener-form" id="shortenForm">
                        <div class="input-group-row">
                            <div class="input-field-wrap">
                                <i class="fa-solid fa-link input-icon"></i>
                                <input type="url" id="longUrlInput" class="url-input" placeholder="Paste your long link here (e.g., https://...)" required autocomplete="off">
                            </div>
                            <button type="button" class="paste-btn" id="pasteBtn" title="Paste link from clipboard">
                                <i class="fa-regular fa-clipboard"></i>
                                <span>Paste</span>
                            </button>
                            <button type="submit" class="submit-btn" id="submitBtn">
                                <i class="fa-solid fa-spinner fa-spin spinner-icon-hidden" id="submitSpinner"></i>
                                <span id="submitBtnText">Shorten URL</span>
                                <i class="fa-solid fa-bolt"></i>
                            </button>
                        </div>

                        <div class="alias-toggle-row">
                            <span class="alias-toggle-btn" id="aliasToggleBtn">
                                <i class="fa-solid fa-sliders"></i>
                                <span>Customize link slug (optional)</span>
                            </span>
                            <span class="alias-note">Branded on <strong><?php echo htmlspecialchars($siteDomain, ENT_QUOTES, 'UTF-8'); ?></strong></span>
                        </div>

                        <div class="alias-box" id="aliasBox">
                            <span class="alias-prefix"><?php echo htmlspecialchars($siteDomain, ENT_QUOTES, 'UTF-8'); ?>/</span>
                            <input type="text" id="customAliasInput" class="alias-input" placeholder="my-custom-slug" pattern="[a-zA-Z0-9_-]+" title="Only alphanumeric letters, hyphens, and underscores allowed">
                        </div>
                    </form>

                    <!-- Instant Result Container -->
                    <div class="shortener-result" id="shortenerResult">
                        <div class="result-header">
                            <div class="result-badge">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Short link created successfully!</span>
                            </div>
                            <div class="result-actions">
                                <button type="button" class="action-btn copy" id="copyBtn" title="Copy to clipboard">
                                    <i class="fa-regular fa-copy"></i>
                                    <span id="copyBtnText">Copy</span>
                                </button>
                                <button type="button" class="action-btn qr" id="qrToggleBtn" title="View QR Code">
                                    <i class="fa-solid fa-qrcode"></i>
                                    <span>QR Code</span>
                                </button>
                                <a href="#" target="_blank" rel="noopener noreferrer" class="action-btn visit" id="visitLinkBtn" title="Open in new tab">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                        </div>

                        <div class="result-url-box">
                            <span class="result-url-text" id="resultUrlText"></span>
                        </div>

                        <div class="qr-modal" id="qrModal">
                            <img src="" alt="Short Link QR Code" id="qrImg" class="qr-image">
                            <a href="#" target="_blank" download="shortlink-qr.png" class="action-btn qr" id="downloadQrBtn">
                                <i class="fa-solid fa-download"></i>
                                <span>Download QR</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="hero-proof">
                    <div class="proof-avatars">
                        <div class="avatar">PK</div>
                        <div class="avatar">RS</div>
                        <div class="avatar">AS</div>
                        <div class="avatar">+</div>
                    </div>
                    <span>Trusted by <strong>100+ travel agencies</strong> across India</span>
                </div>
            </div>

            <!-- Floating Dashboard Preview Window -->
            <div class="hero-dashboard">
                <div class="dashboard-window">
                    <div class="window-bar">
                        <div class="dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <div class="window-url"><?php echo htmlspecialchars($siteDomain, ENT_QUOTES, 'UTF-8'); ?>/analytics</div>
                    </div>
                    <div class="window-content">
                        <div class="dash-stats-grid">
                            <div class="dash-stat-card highlight">
                                <span class="dash-stat-label">Total Links</span>
                                <span class="dash-stat-val">124,850</span>
                                <span class="dash-stat-badge">↑ 18% this month</span>
                            </div>
                            <div class="dash-stat-card">
                                <span class="dash-stat-label">Avg Latency</span>
                                <span class="dash-stat-val">11ms</span>
                                <span class="dash-stat-badge">Edge 301</span>
                            </div>
                            <div class="dash-stat-card">
                                <span class="dash-stat-label">Success Rate</span>
                                <span class="dash-stat-val">99.98%</span>
                                <span class="dash-stat-badge">Zero downtime</span>
                            </div>
                        </div>

                        <div class="dash-links-list">
                            <div class="dash-links-title">Recently Shortened &amp; Shared Links</div>
                            <div class="dash-link-row">
                                <div class="dash-link-info">
                                    <span class="dash-link-tag">/itin-kashmir</span>
                                    <span class="dash-link-dest">crm.aleya.in/view-itinerary/...</span>
                                </div>
                                <div class="dash-link-meta">
                                    <i class="fa-brands fa-whatsapp"></i>
                                    <span>24 clicks</span>
                                </div>
                            </div>
                            <div class="dash-link-row">
                                <div class="dash-link-info">
                                    <span class="dash-link-tag">/pay-4821</span>
                                    <span class="dash-link-dest">crm.aleya.in/payment/gen/...</span>
                                </div>
                                <div class="dash-link-meta">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span>₹85,000</span>
                                </div>
                            </div>
                            <div class="dash-link-row">
                                <div class="dash-link-info">
                                    <span class="dash-link-tag">/quote-dubai</span>
                                    <span class="dash-link-dest">crm.aleya.in/view-proposal/...</span>
                                </div>
                                <div class="dash-link-meta">
                                    <i class="fa-solid fa-file-pdf"></i>
                                    <span>PDF Sent</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating Notification Badges -->
                <div class="float-card top">
                    <div class="float-card-icon green">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div class="float-card-text">
                        <strong>Instant 301 Redirect</strong>
                        <span>12ms server response time</span>
                    </div>
                </div>

                <div class="float-card bottom">
                    <div class="float-card-icon blue">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div class="float-card-text">
                        <strong>WhatsApp Ready</strong>
                        <span>Perfect for quotes &amp; itineraries</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Marquee Highlights Banner -->
    <section class="marquee-section">
        <div class="marquee-track">
            <span class="marquee-item"><span>✦</span> Lightning-Fast 301 Edge Redirection</span>
            <span class="marquee-item"><span>✦</span> Travel Itinerary WhatsApp Sharing</span>
            <span class="marquee-item"><span>✦</span> Customer Quotation &amp; Proposal Links</span>
            <span class="marquee-item"><span>✦</span> Instant QR Code Generator</span>
            <span class="marquee-item"><span>✦</span> 99.99% Server Uptime</span>
            <span class="marquee-item"><span>✦</span> Payment Gateway Integration</span>
            <span class="marquee-item"><span>✦</span> Developer-Friendly REST API</span>
            <!-- Duplicated for continuous marquee loop -->
            <span class="marquee-item"><span>✦</span> Lightning-Fast 301 Edge Redirection</span>
            <span class="marquee-item"><span>✦</span> Travel Itinerary WhatsApp Sharing</span>
            <span class="marquee-item"><span>✦</span> Customer Quotation &amp; Proposal Links</span>
            <span class="marquee-item"><span>✦</span> Instant QR Code Generator</span>
            <span class="marquee-item"><span>✦</span> 99.99% Server Uptime</span>
            <span class="marquee-item"><span>✦</span> Payment Gateway Integration</span>
            <span class="marquee-item"><span>✦</span> Developer-Friendly REST API</span>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section" id="features">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Built For Speed &amp; Scale</div>
                <h2 class="section-title">Everything you need in a modern link engine.</h2>
                <p class="section-desc">Designed specifically for fast-growing agencies and modern web applications that demand reliability, speed, and clean presentation.</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <h3 class="feature-title">Itinerary &amp; Proposal Sharing</h3>
                    <p class="feature-text">Stop sending messy, breakable 300-character URLs to your clients. Send crisp, compact links that look clean and professional on WhatsApp and SMS.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <h3 class="feature-title">Dynamic QR Codes</h3>
                    <p class="feature-text">Instantly generate high-resolution QR codes for physical flyers, boarding envelopes, and presentation packets with zero extra steps.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="fa-solid fa-gauge-high"></i>
                    </div>
                    <h3 class="feature-title">Sub-15ms Redirection</h3>
                    <p class="feature-text">Engineered with lightweight native database lookups and optimized HTTP 301 headers so your visitors experience instantaneous page transitions.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>
                    <h3 class="feature-title">Payment Link Safe</h3>
                    <p class="feature-text">Full compatibility with payment links from Razorpay, Cashfree, and PayU. All query parameters, lead IDs, and security hashes are preserved intact.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="feature-title">Branded &amp; Trusted</h3>
                    <p class="feature-text">Every short link is hosted under verified domains with HTTPS encryption, eliminating spam triggers and ensuring maximum deliverability.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="fa-solid fa-code"></i>
                    </div>
                    <h3 class="feature-title">REST API Ready</h3>
                    <p class="feature-text">Directly connect your CRM, automation webhooks, and dispatchers using our fast JSON endpoint for programmatic link generation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Use Cases Section -->
    <section class="section alt-bg" id="use-cases">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Agency Workflows</div>
                <h2 class="section-title">Where short links make the biggest difference.</h2>
                <p class="section-desc">Short links streamline communication at every touchpoint of your customer lifecycle.</p>
            </div>

            <div class="use-cases-grid">
                <div class="use-case-card">
                    <div class="use-case-icon">
                        <i class="fa-solid fa-plane-departure"></i>
                    </div>
                    <h3 class="use-case-title">Travel Itineraries</h3>
                    <p class="use-case-desc">Send interactive web itineraries and PDF download links that clients can open directly on mobile.</p>
                </div>

                <div class="use-case-card">
                    <div class="use-case-icon">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <h3 class="use-case-title">Client Quotations</h3>
                    <p class="use-case-desc">Share detailed holiday packages and quotes with custom aliases like <code>/maldives-quote</code>.</p>
                </div>

                <div class="use-case-card">
                    <div class="use-case-icon">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <h3 class="use-case-title">Payment Collection</h3>
                    <p class="use-case-desc">Shorten order payment requests to reduce WhatsApp link wrapping and eliminate broken payment links.</p>
                </div>

                <div class="use-case-card">
                    <div class="use-case-icon">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <h3 class="use-case-title">WhatsApp Campaigns</h3>
                    <p class="use-case-desc">Boost click-through rates in bulk WhatsApp broadcasts with concise, trustworthy short links.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="section" id="how-it-works">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Simple 3-Step Process</div>
                <h2 class="section-title">How It Works</h2>
                <p class="section-desc">Generating high-speed short links takes less than two seconds.</p>
            </div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Paste Destination URL</h3>
                    <p class="step-desc">Enter any webpage link, PDF proposal, payment gateway URL, or Google Drive resource into the input box above.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Customize (Optional)</h3>
                    <p class="step-desc">Choose an optional memorable alias or let our high-concurrency engine assign an ultra-short alphanumeric slug.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Share Everywhere</h3>
                    <p class="step-desc">Copy your short link or download the generated QR code to share instantly across WhatsApp, SMS, and Email.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion Section -->
    <section class="section alt-bg" id="faq">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Frequently Asked Questions</div>
                <h2 class="section-title">Got questions? We've got answers.</h2>
                <p class="section-desc">Everything you need to know about our short link infrastructure.</p>
            </div>

            <div class="faq-wrap">
                <div class="faq-item active">
                    <div class="faq-header">
                        <span>Are short links permanent?</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-body">
                        <div class="faq-content">
                            Yes. All links shortened via this platform are stored as permanent 301 redirects and do not expire unless explicitly removed or updated by an administrator.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-header">
                        <span>Will long parameters and UTM tags work?</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-body">
                        <div class="faq-content">
                            Absolutely. When a visitor hits your short link, the server issues a standard HTTP 301 redirection carrying all destination query strings, order parameters, and payment tokens without truncating them.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-header">
                        <span>Can I use this for WhatsApp and SMS broadcasts?</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-body">
                        <div class="faq-content">
                            Yes, that is our primary use case! Clean short URLs fit easily within SMS character limitations and avoid messy auto-wrapping on WhatsApp and Instagram Direct.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-header">
                        <span>How can I automate link shortening in my code?</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-body">
                        <div class="faq-content">
                            You can send a <code>POST</code> request to <code>/api/v3/shortener/shorten.php</code> with <code>{"url": "https://..."}</code> in JSON to receive an immediate response containing the short URL.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container cta-container">
            <h2 class="cta-title">Upgrade Your Link Sharing Today.</h2>
            <p class="cta-desc">Start sending clean, branded links that your customers trust and love clicking.</p>
            <a href="#shortenerCard" class="cta-btn">
                <span>Shorten Your First Link</span>
                <i class="fa-solid fa-arrow-up"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-top">
                <div class="footer-brand">
                    <div class="nav-logo">
                        <div class="nav-logo-icon">
                            <i class="fa-solid fa-link"></i>
                        </div>
                        <span><?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <p class="section-desc"><?php echo htmlspecialchars($siteDesc, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>

                <div class="footer-col">
                    <div class="footer-col-title">Navigation</div>
                    <div class="footer-links">
                        <a href="#features">Features</a>
                        <a href="#how-it-works">How It Works</a>
                        <a href="#use-cases">Use Cases</a>
                        <a href="#faq">FAQ</a>
                    </div>
                </div>

                <div class="footer-col">
                    <div class="footer-col-title">Integrated Platforms</div>
                    <div class="footer-links">
                        <a href="/dashboard/">Aleya Travel CRM</a>
                        <a href="/dashboard/">Quotation Engine</a>
                        <a href="/dashboard/">Payment Generator</a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?>. Powered by Blumox Technologies.</span>
                <span>Active Engine: <?php echo htmlspecialchars($siteDomain, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </footer>

    <!-- SweetAlert2 Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Client Script -->
    <script src="/app/link_shortener/visitor/default/desktop/assets/js/app.js"></script>
</body>
</html>
