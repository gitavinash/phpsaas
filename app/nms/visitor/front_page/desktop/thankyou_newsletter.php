<?php

require_once 'header.php';

$subscriberEmail = isset($_GET['email']) ? trim($_GET['email']) : '';
$cleanEmail = filter_var($subscriberEmail, FILTER_VALIDATE_EMAIL) ? htmlspecialchars($subscriberEmail) : '';

$query = "INSERT INTO " . TABLE_PREFIX . "newsletter
          (newsletter_email, newsletter_site_id, newsletter_status)
          VALUES ('$cleanEmail', " . SITE_ID . ", 1)
          ON DUPLICATE KEY UPDATE newsletter_status = 1";

if (!mysqli_query($connection, $query)) {
    $message = "There was an error processing your subscription. Please try again later.";
    echo $message;
    exit();
}

?>

<section class="ty-newsletter-section">
    <div class="container">
        <div class="ty-newsletter-container">
            <div class="ty-newsletter-card">

                <div class="ty-card-top-accent"></div>

                <!-- Header Block -->
                <div class="ty-newsletter-header">
                    <div class="ty-icon-wrapper">
                        <div class="ty-icon-pulse"></div>
                        <i class="ph ph-paper-plane-tilt"></i>
                    </div>

                    <div class="ty-kicker-badge">
                        <i class="ph ph-seal-check"></i> Free Subscription Active
                    </div>

                    <h1 class="ty-newsletter-title">Welcome to <?php echo SITE_NAME ?> Briefing</h1>
                    <p class="ty-newsletter-subtitle">
                        You're all set! Thank you for signing up to our newsletter. You will now receive curated briefings, market trends, and deep-dive analysis directly in your inbox.
                    </p>

                    <?php if (!empty($cleanEmail)) { ?>
                        <div class="ty-email-pill-box">
                            <span class="ty-email-pill-label"><i class="ph ph-envelope-simple-check"></i> Subscribed with</span>
                            <span class="ty-email-pill-address"><?php echo $cleanEmail; ?></span>
                        </div>
                    <?php } ?>
                </div>

                <!-- Benefits Highlights Section -->
                <div class="ty-benefits-section">
                    <div class="ty-benefits-heading-wrap">
                        <span class="ty-benefits-tag">What's Next</span>
                        <h2 class="ty-benefits-heading">
                            What to Expect in Your Inbox
                        </h2>
                    </div>

                    <div class="ty-benefits-grid">
                        <div class="ty-benefit-card">
                            <div class="ty-benefit-icon-box">
                                <i class="ph ph-newspaper-clipping"></i>
                            </div>
                            <h3 class="ty-benefit-title">Executive Briefings</h3>
                            <p class="ty-benefit-desc">Curated morning digests summarizing the day's top fintech, AI, and venture capital stories.</p>
                        </div>

                        <div class="ty-benefit-card">
                            <div class="ty-benefit-icon-box">
                                <i class="ph ph-trend-up"></i>
                            </div>
                            <h3 class="ty-benefit-title">Exclusive Insights</h3>
                            <p class="ty-benefit-desc">Deep-dive market trends, founder interviews, and ecosystem analysis before anyone else.</p>
                        </div>

                        <div class="ty-benefit-card">
                            <div class="ty-benefit-icon-box">
                                <i class="ph ph-shield-check"></i>
                            </div>
                            <h3 class="ty-benefit-title">Zero Spam Guarantee</h3>
                            <p class="ty-benefit-desc">100% editorial signal. We respect your attention and never share or sell subscriber emails.</p>
                        </div>
                    </div>
                </div>

                <!-- Inbox Delivery Tip Banner -->
                <div class="ty-tip-banner">
                    <div class="ty-tip-icon">
                        <i class="ph ph-lightbulb-filament"></i>
                    </div>
                    <div class="ty-tip-content">
                        <strong>Pro Tip:</strong> Check your inbox for a welcome confirmation. If it landed in your <em>Promotions</em> or <em>Spam</em> tab, drag it to <em>Primary</em> so you never miss an edition.
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="ty-newsletter-actions">
                    <a href="/" class="ty-btn-primary">
                        <span>Start Reading Stories</span>
                        <i class="ph ph-arrow-right"></i>
                    </a>
                    <a href="/category/deepdives/" class="ty-btn-secondary">
                        <i class="ph ph-sparkle"></i>
                        <span>Explore Deepdives</span>
                    </a>
                </div>

                <!-- Footer Bar -->
                <div class="ty-newsletter-footer">
                    <div class="ty-newsletter-footer-item">
                        <i class="ph ph-shield-check"></i>
                        <span>No spam ever &bull; Unsubscribe anytime</span>
                    </div>
                    <div class="ty-newsletter-footer-item">
                        <i class="ph ph-envelope-open"></i>
                        <span>Welcome email on its way</span>
                    </div>
                    <div class="ty-newsletter-footer-item">
                        <i class="ph ph-question"></i>
                        <span>Questions? Contact <a href="mailto:info@theheadandtale.com">info@theheadandtale.com</a></span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php

require_once 'footer.php';

?>