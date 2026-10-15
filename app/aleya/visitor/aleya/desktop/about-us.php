<?php
/**
 * Aleya SaaS — About Us Page
 * Corporate mission, core values, platform history, and operational transparency
 */

require_once __DIR__ . '/includes/seo.php';

$seoConfig = [
    'title' => 'About Aleya — Enterprise SaaS Business Management & CRM Platform',
    'description' => 'Aleya is built by Blumox Technologies in New Delhi to eliminate operational chaos for travel agencies, transporters, distributors, and service enterprises.',
    'canonical' => 'https://aleya.in/about-us/',
    'keywords' => 'about aleya, blumox technologies, business CRM, travel ERP, operations platform, New Delhi SaaS company',
    'stylesheets' => [
        '/app/aleya/visitor/aleya_new/desktop/assets/css/homepage.css',
        '/app/aleya/visitor/aleya_new/desktop/assets/css/pages.css'
    ],
    'schema' => [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Aleya (Blumox Technologies)',
        'url' => 'https://aleya.in',
        'logo' => 'https://aleya.in/app/aleya/visitor/aleya_new/desktop/assets/images/aleya_logo.png',
        'description' => 'Business management, CRM, and ERP SaaS platform for travel, transport, distribution, and service enterprises.',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Kirti Nagar',
            'addressLocality' => 'New Delhi',
            'addressCountry' => 'IN'
        ],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => '+91-9990026008',
            'contactType' => 'customer service',
            'email' => 'support@aleya.in'
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php render_seo_meta($seoConfig); ?>
</head>
<body>
<?php require __DIR__ . '/includes/header.php'; ?>

<main id="mainContent">
    <!-- Hero Header -->
    <header class="page-hero">
        <div class="container">
            <span class="badge badge-brand">About Aleya</span>
            <h1>Engineered to Eliminate Operational Chaos</h1>
            <p>We built Aleya to help growing travel agencies, distributors, transporters, and service businesses run disciplined, profitable operations without complex enterprise software.</p>
        </div>
    </header>

    <!-- Key Metrics Grid -->
    <section class="section">
        <div class="container">
            <div class="about-stats-grid">
                <div class="about-stat-box">
                    <div class="about-stat-num">100+</div>
                    <div class="stat-label">Active Workspaces</div>
                    <p class="form-hint">Agencies, transporters, and distributors running daily operations on Aleya.</p>
                </div>
                <div class="about-stat-box">
                    <div class="about-stat-num">10,000+</div>
                    <div class="stat-label">Leads Processed</div>
                    <p class="form-hint">Qualified, tracked, and converted across integrated multi-channel funnels.</p>
                </div>
                <div class="about-stat-box">
                    <div class="about-stat-num">99.9%</div>
                    <div class="stat-label">Platform Uptime</div>
                    <p class="form-hint">Enterprise-grade cloud infrastructure with automated redundant backups.</p>
                </div>
                <div class="about-stat-box">
                    <div class="about-stat-num">24h</div>
                    <div class="stat-label">Typical Go-Live</div>
                    <p class="form-hint">From spreadsheet import to live team usage with direct technical assistance.</p>
                </div>
            </div>

            <!-- Story & Philosophy Layout -->
            <div class="contact-grid-layout mt-12">
                <div class="contact-form-card">
                    <span class="badge badge-brand">Our Story</span>
                    <h2>Why We Built Aleya</h2>
                    <p>Most growing service businesses do not fail from a lack of market demand. They stumble because their daily execution is scattered across WhatsApp messages, unorganized spreadsheets, sticky notes, and disconnected bookkeeping software.</p>
                    <p>Owners spend their evenings reconciling customer payments, following up on forgotten inquiries, or tracking down why an itinerary wasn't sent. Staff spend hours copying customer data from one spreadsheet to another rather than closing sales.</p>
                    <p>We developed <strong>Aleya</strong> under Blumox Technologies to solve this exact problem: a robust, high-contrast, no-nonsense software system that unifies customer inquiries, automated follow-ups, quotation creation, booking records, and financial visibility into one clean dashboard.</p>
                    <p>No unnecessary enterprise bloat. No complex multi-month consulting engagements. Just clear, disciplined software that works from day one.</p>
                </div>

                <div class="contact-info-card">
                    <span class="badge badge-brand">Our Principles</span>
                    <h2>How We Build</h2>
                    
                    <div class="contact-detail-row">
                        <div class="contact-detail-icon"><i class="fas fa-eye"></i></div>
                        <div class="contact-detail-info">
                            <h4>Clarity Over Gimmicks</h4>
                            <p>Every interface and report must answer a concrete operational question: What requires attention today? Which customer needs a quotation? Who hasn't paid?</p>
                        </div>
                    </div>

                    <div class="contact-detail-row">
                        <div class="contact-detail-icon"><i class="fas fa-layer-group"></i></div>
                        <div class="contact-detail-info">
                            <h4>Single Source of Truth</h4>
                            <p>From initial inquiry to final tax invoice, customer data remains consistent. Staff collaborate in real-time with role-based permissions and complete audit history.</p>
                        </div>
                    </div>

                    <div class="contact-detail-row">
                        <div class="contact-detail-icon"><i class="fas fa-user-shield"></i></div>
                        <div class="contact-detail-info">
                            <h4>Dedicated Human Support</h4>
                            <p>Software is only as good as its adoption. Our engineering and customer success team in New Delhi provides personal migration guidance and staff onboarding.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Company Profile & Infrastructure -->
    <section class="section section-gray">
        <div class="container">
            <div class="section-header text-center">
                <span class="badge badge-brand">Corporate Profile</span>
                <h2>Backed by Blumox Technologies</h2>
                <p>Aleya is developed and maintained by Blumox Technologies, an Indian software engineering firm dedicated to building high-performance B2B digital infrastructure.</p>
            </div>

            <div class="contact-grid-layout align-center">
                <div class="contact-form-card">
                    <h3>Infrastructure &amp; Security Standards</h3>
                    <ul class="comparison-list mt-4">
                        <li><strong>Database Isolation:</strong> Each customer workspace operates with dedicated schema partitioning, preventing data co-mingling.</li>
                        <li><strong>Encrypted in Transit &amp; at Rest:</strong> All traffic is safeguarded by strict TLS 1.3 encryption and automated snapshot backups.</li>
                        <li><strong>Indian Regulatory Compliance:</strong> Full GST invoice compatibility, HSN/SAC code tracking, and automated Indian tax compliance.</li>
                        <li><strong>High Availability:</strong> Hosted on Tier-3 data centers with round-the-clock uptime monitoring and rapid disaster recovery.</li>
                    </ul>
                </div>

                <div class="contact-info-card">
                    <h3>Corporate Headquarters</h3>
                    <div class="contact-detail-row">
                        <div class="contact-detail-icon"><i class="fas fa-building"></i></div>
                        <div class="contact-detail-info">
                            <h4>Parent Organization</h4>
                            <p>Blumox Technologies</p>
                        </div>
                    </div>
                    <div class="contact-detail-row">
                        <div class="contact-detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="contact-detail-info">
                            <h4>Registered Office</h4>
                            <p>Kirti Nagar, New Delhi, India</p>
                        </div>
                    </div>
                    <div class="contact-detail-row">
                        <div class="contact-detail-icon"><i class="fas fa-envelope"></i></div>
                        <div class="contact-detail-info">
                            <h4>Corporate Communications</h4>
                            <p><a href="mailto:support@aleya.in">support@aleya.in</a></p>
                        </div>
                    </div>
                    <div class="contact-detail-row">
                        <div class="contact-detail-icon"><i class="fas fa-phone"></i></div>
                        <div class="contact-detail-info">
                            <h4>Direct Contact Desk</h4>
                            <p><a href="tel:+919990026008">+91-9990026008</a></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conversion Strip -->
            <div class="cta-banner mt-16">
                <div class="cta-banner-content">
                    <span class="badge badge-brand-invert">Transform Your Business</span>
                    <h2>Ready to see how Aleya works for your industry?</h2>
                    <p>Schedule a live walk-through with our solution consultants or explore our transparent subscription options.</p>
                </div>
                <div class="cta-banner-actions">
                    <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-lg">
                        <span>Book a Demo</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="/pricing/" class="btn btn-secondary btn-lg">
                        <span>View Pricing</span>
                    </a>
                </div>
            </div>

        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
