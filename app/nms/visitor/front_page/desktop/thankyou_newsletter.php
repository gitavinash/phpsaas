<?php

require_once 'header.php';

$subscriberEmail = isset($_GET['email']) ? trim($_GET['email']) : '';
$cleanEmail = filter_var($subscriberEmail, FILTER_VALIDATE_EMAIL) ? htmlspecialchars($subscriberEmail) : '';

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

                    <br>

                    <h1 class="ty-newsletter-title">Welcome to <?php echo SITE_NAME ?> Briefing</h1>
                    <p class="ty-newsletter-subtitle">
                        You are all set! Thank you for signing up to our newsletter. You will now receive curated briefings and deep-dive analysis directly in your inbox.
                    </p>

                    <?php if (!empty($cleanEmail)) { ?>
                        <div class="ty-email-pill-box">
                            <i class="ph ph-envelope-simple-check"></i> Subscribed: <strong><?php echo $cleanEmail; ?></strong>
                        </div>
                    <?php } ?>
                </div>

                <!-- Benefits Highlights Grid -->
                <!-- <div class="ty-benefits-section">
                    <h2 class="ty-benefits-heading">
                        <i class="ph ph-sparkle"></i> What to Expect in Your Inbox
                    </h2>

                    <div class="ty-benefits-grid">
                        <div class="ty-benefit-card">
                            <div class="ty-benefit-icon">
                                <i class="ph ph-newspaper"></i>
                            </div>
                            <h3 class="ty-benefit-title">Executive Briefings</h3>
                            <p class="ty-benefit-desc">Curated morning digests summarizing the day's top fintech, AI, and venture capital stories.</p>
                        </div>

                        <div class="ty-benefit-card">
                            <div class="ty-benefit-icon">
                                <i class="ph ph-trend-up"></i>
                            </div>
                            <h3 class="ty-benefit-title">Exclusive Insights</h3>
                            <p class="ty-benefit-desc">Deep-dive market trends, founder interviews, and ecosystem analysis before anyone else.</p>
                        </div>

                        <div class="ty-benefit-card">
                            <div class="ty-benefit-icon">
                                <i class="ph ph-shield-check"></i>
                            </div>
                            <h3 class="ty-benefit-title">Zero Spam Guarantee</h3>
                            <p class="ty-benefit-desc">100% editorial signal. We respect your inbox and never share or sell subscriber emails.</p>
                        </div>
                    </div>
                </div> -->

                <!-- CTA Buttons -->
                <div class="ty-newsletter-actions">
                    <a href="/" class="btn-ty-primary">
                        Start Reading Stories <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- Footer Bar -->
                <div class="ty-newsletter-footer">
                    <span><i class="ph ph-info"></i> Please check your inbox for a confirmation message.</span>
                    <span>Questions? Reach out at <a href="mailto:info@theheadandtale.com">info@theheadandtale.com</a></span>
                </div>

            </div>
        </div>
    </div>
</section>

<?php

require_once 'footer.php';

?>