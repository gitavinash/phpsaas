<?php
/**
 * Aleya SaaS — Refund and Cancellation Policy
 * Transparent cancellation terms and refund guidelines for B2B subscribers
 */

require_once __DIR__ . '/includes/seo.php';

$seoConfig = [
    'title' => 'Refund & Cancellation Policy — Aleya SaaS Business Platform',
    'description' => 'Understand our cancellation terms, subscription renewals, and refund policies for Aleya cloud workspaces.',
    'canonical' => 'https://aleya.in/refund-policy/',
    'keywords' => 'refund policy, cancellation policy, subscription refund, SaaS billing terms',
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
            <h1>Refund &amp; Cancellation Policy</h1>
            <p>Effective Date: January 1, 2026 &bull; Last Updated: October 15, 2026</p>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <article class="legal-card">
                <h2>1. Overview</h2>
                <p>At <strong>Aleya</strong> (a business automation product of <strong>Blumox Technologies</strong>), we are committed to transparent, fair, and reliable software operations. Because Aleya is a cloud-based Software-as-a-Service (SaaS) application with instant infrastructure provisioning, dedicated tenant database creation, and immediate software access, our cancellation and refund practices are governed by the terms set forth below.</p>

                <h2>2. Subscription Cancellation</h2>
                <p>Subscribers may cancel their subscription at any time directly through their workspace administration portal or by submitting a written request to <a href="mailto:support@aleya.in">support@aleya.in</a> from their registered billing email address.</p>
                <ul>
                    <li><strong>Cancellation Timing:</strong> Cancellation requests must be submitted at least 48 hours prior to your scheduled renewal date to ensure recurring billing is halted.</li>
                    <li><strong>Access Through Billing Term:</strong> When you cancel a subscription, your workspace remains fully accessible with all features active until the final day of your current paid billing period (monthly or annual).</li>
                    <li><strong>Data Export:</strong> You retain full access to export all contacts, leads, booking records, and financial ledgers via CSV or Excel prior to workspace decommission.</li>
                </ul>

                <h2>3. Refund Terms</h2>
                <p>Please review our refund criteria for each subscription category:</p>
                <ul>
                    <li><strong>Monthly Subscriptions:</strong> Due to immediate operational server provisioning and active computing consumption, payments for monthly subscriptions are non-refundable once the billing month has commenced.</li>
                    <li><strong>Annual Subscriptions:</strong> Annual plans receive significant discounts (up to 20%). If within 14 days of your initial annual activation you experience an irremediable technical defect or system unavailability verified by our engineering team, you may request a pro-rated refund for the unconsumed months of the annual term.</li>
                    <li><strong>Duplicate or Erroneous Charges:</strong> In the rare event of a duplicate billing transaction caused by network latency or gateway errors, we will issue a 100% immediate reversal upon notification. Refunds are credited back to the original source instrument within 5 to 7 business days depending on banking clearing cycles.</li>
                </ul>

                <h2>4. Onboarding &amp; Migration Services</h2>
                <p>Standard onboarding, account configuration, and migration guidance provided during onboarding are included with active software subscriptions. Any separately contracted custom development or dedicated integration engineering services are non-refundable once the customized deliverables have been completed and approved by the subscriber.</p>

                <h2>5. How to Initiate a Request</h2>
                <p>To request a billing review or dispute a charge, please email our billing department:</p>
                <p>
                    <strong>Billing Desk — Blumox Technologies</strong><br>
                    Email: <a href="mailto:support@aleya.in">support@aleya.in</a><br>
                    Subject Line: Billing Review — [Your Workspace Subdomain]<br>
                    Direct Desk: <a href="tel:+919990026008">+91-9990026008</a> (Mon–Sat, 9:30 AM – 7:30 PM IST)
                </p>
            </article>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
