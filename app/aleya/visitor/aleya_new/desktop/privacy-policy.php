<?php
/**
 * Aleya SaaS — Privacy Policy
 * Comprehensive data privacy disclosure for B2B subscribers and visitors
 */

require_once __DIR__ . '/includes/seo.php';

$seoConfig = [
    'title' => 'Privacy Policy — Aleya SaaS Business Platform',
    'description' => 'Read our comprehensive privacy policy governing data protection, subscriber workspace confidentiality, and security standards at Aleya.',
    'canonical' => 'https://aleya.in/privacy-policy/',
    'keywords' => 'aleya privacy policy, data security, cloud data privacy, subscriber confidentiality',
    'stylesheets' => [
        '/app/aleya/visitor/aleya_new/desktop/assets/css/pages.css'
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
    <header class="page-hero">
        <div class="container">
            <span class="badge badge-brand">Legal &amp; Compliance</span>
            <h1>Privacy Policy</h1>
            <p>Effective Date: January 1, 2026 &bull; Last Updated: October 15, 2026</p>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <article class="legal-card">
                <h2>1. Introduction &amp; Ownership</h2>
                <p>Aleya ("we", "our", or "the Platform") is a B2B Software-as-a-Service (SaaS) business management, CRM, and ERP system developed, owned, and operated by <strong>Blumox Technologies</strong>, headquartered in Kirti Nagar, New Delhi, India. We take data protection, customer confidentiality, and operational security seriously.</p>
                <p>This Privacy Policy explains how we collect, process, safeguard, and retain information when you visit our website (<strong>https://aleya.in/</strong>) or subscribe to an Aleya cloud workspace.</p>

                <h2>2. Information We Collect</h2>
                <p>We collect information in three primary categories:</p>
                <ul>
                    <li><strong>Subscriber Account Information:</strong> When you register or request a demo, we collect your name, business email address, direct telephone or WhatsApp number, company name, industry, and billing credentials (such as GSTIN and registered business address).</li>
                    <li><strong>Subscriber Workspace Data:</strong> As an operational software platform, your authorized team members may input business data into your workspace—including sales inquiries, traveler itineraries, booking confirmations, quotations, vendor records, driver assignments, and customer contact details. You retain 100% proprietary ownership over all Workspace Data.</li>
                    <li><strong>Technical &amp; Telemetry Data:</strong> We automatically log browser metadata, IP addresses, access timestamps, feature usage patterns, and operating system diagnostics to maintain system security, debug application performance, and prevent unauthorized account access.</li>
                </ul>

                <h2>3. How We Use Your Information</h2>
                <p>We process your data strictly for legitimate business and operational purposes:</p>
                <ul>
                    <li>To provision, authenticate, configure, and maintain your cloud workspace instance and database.</li>
                    <li>To process monthly and annual subscription payments and deliver GST-compliant tax invoices.</li>
                    <li>To provide technical support, product troubleshooting, and personalized onboarding training.</li>
                    <li>To transmit critical operational notices, security advisories, system status alerts, and transactional messages.</li>
                    <li>To analyze platform uptime, optimize performance, and prevent malicious attacks or spam.</li>
                </ul>

                <h2>4. Workspace Data Ownership &amp; Non-Disclosure</h2>
                <p><strong>We never sell, monetize, or commercialize your business or customer records.</strong></p>
                <p>The subscriber is the sole data controller of all customer lists, quotations, booking records, and financial figures stored inside their Aleya tenant. Aleya operates purely as a data processor. Our technical personnel will never access your private operational records unless explicitly requested by your authorized account administrator for troubleshooting purposes.</p>

                <h2>5. Payment Security &amp; Gateway Standards</h2>
                <p>All subscription transactions and billing settlements are handled through certified, PCI-DSS Level 1 compliant payment aggregators and authorized Indian banking networks (including Razorpay and NPCI UPI networks). Aleya never stores raw credit or debit card numbers, CVVs, or online banking passwords on our application servers.</p>

                <h2>6. Data Security Measures</h2>
                <p>We implement stringent technical and organizational safeguards:</p>
                <ul>
                    <li><strong>Encryption:</strong> All data transmitted between your browser and our cloud servers is encrypted using modern TLS 1.3 cryptographic protocols.</li>
                    <li><strong>Database Isolation:</strong> Subscriber tenants utilize segregated schema boundaries and strict authorization controls to prevent cross-tenant data exposure.</li>
                    <li><strong>Redundant Backups:</strong> Automated encrypted backups are generated daily and stored in secure offsite repositories to enable rapid disaster recovery.</li>
                </ul>

                <h2>7. Data Retention &amp; Right to Erasure</h2>
                <p>Your workspace data remains intact for the duration of your active subscription. Upon voluntary cancellation, subscribers are given a 30-day grace period to export all inquiries, bookings, quotations, and customer records in standard CSV or spreadsheet formats. Following this grace period, customer tenant data is securely purged from production clusters in accordance with industry best practices.</p>

                <h2>8. Contact Our Privacy Officer</h2>
                <p>If you have any questions regarding this Privacy Policy, your rights under applicable Indian IT laws, or data export requests, please contact our data desk:</p>
                <p>
                    <strong>Blumox Technologies — Aleya Division</strong><br>
                    Kirti Nagar, New Delhi, India<br>
                    Email: <a href="mailto:support@aleya.in">support@aleya.in</a><br>
                    Telephone: <a href="tel:+919990026008">+91-9990026008</a>
                </p>
            </article>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
