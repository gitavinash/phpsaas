<?php

require_once 'header.php';

$subscriberEmail = isset($_GET['email']) ? trim($_GET['email']) : '';
$cleanEmail = filter_var($subscriberEmail, FILTER_VALIDATE_EMAIL) ? htmlspecialchars($subscriberEmail) : '';

?>

<style>
    /* Dedicated Thank You Newsletter Page Styles */
    .ty-newsletter-section {
        padding: 50px 0 90px 0;
        background-color: var(--bg-color, #fdfbf7);
        min-height: calc(100vh - 350px);
        font-family: var(--font-body, "Inter", -apple-system, BlinkMacSystemFont, sans-serif);
    }

    .ty-newsletter-container {
        max-width: 820px;
        margin: 0 auto;
    }

    .ty-newsletter-card {
        background: var(--white, #ffffff);
        border: 1px solid var(--border-color, #e0e0e0);
        border-radius: 20px;
        box-shadow: 0 16px 45px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
    }

    .ty-card-top-accent {
        height: 5px;
        background: var(--accent-color);
    }

    .ty-newsletter-header {
        padding: 44px 40px 32px 40px;
        text-align: center;
        border-bottom: 1px solid #f1f5f9;
    }

    .ty-icon-wrapper {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #ecfdf5;
        color: #10b981;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        margin-bottom: 20px;
        position: relative;
        border: 2px solid #a7f3d0;
    }

    .ty-icon-pulse {
        position: absolute;
        top: -6px;
        left: -6px;
        right: -6px;
        bottom: -6px;
        border-radius: 50%;
        border: 1.5px solid #a7f3d0;
        animation: tyPulse 2s infinite ease-in-out;
    }

    @keyframes tyPulse {
        0% {
            transform: scale(1);
            opacity: 0.8;
        }

        50% {
            transform: scale(1.08);
            opacity: 0.3;
        }

        100% {
            transform: scale(1);
            opacity: 0.8;
        }
    }

    .ty-kicker-badge {
        display: inline-block;
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #059669;
        background: #ecfdf5;
        padding: 4px 14px;
        border-radius: 20px;
        margin-bottom: 14px;
        border: 1px solid #a7f3d0;
    }

    .ty-newsletter-title {
        font-family: var(--font-heading, "Playfair Display", Georgia, serif);
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--primary-color, #0a0a0a);
        margin: 0 0 12px 0;
        line-height: 1.2;
    }

    .ty-newsletter-subtitle {
        font-size: 1.02rem;
        color: var(--text-secondary, #555555);
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.65;
    }

    .ty-email-pill-box {
        margin-top: 20px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 8px 18px;
        border-radius: 30px;
        font-size: 0.9rem;
        color: var(--primary-color, #0a0a0a);
        font-weight: 600;
    }

    .ty-email-pill-box i {
        color: #059669;
    }

    /* Features Grid */
    .ty-benefits-section {
        padding: 36px 40px;
    }

    .ty-benefits-heading {
        font-family: var(--font-heading, "Playfair Display", Georgia, serif);
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary-color, #0a0a0a);
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ty-benefits-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .ty-benefit-card {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .ty-benefit-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: var(--primary-color, #0a0a0a);
        margin-bottom: 4px;
    }

    .ty-benefit-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--primary-color, #0a0a0a);
        margin: 0;
    }

    .ty-benefit-desc {
        font-size: 0.84rem;
        color: #64748b;
        line-height: 1.5;
        margin: 0;
    }

    /* Action Area */
    .ty-newsletter-actions {
        padding: 28px 40px 36px 40px;
        background: #ffffff;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .btn-ty-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--primary-color, #0a0a0a);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 14px 32px;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
    }

    .btn-ty-primary:hover {
        background: var(--accent-color, #d93025);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(217, 48, 37, 0.25);
    }

    .btn-ty-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        color: var(--primary-color, #0a0a0a) !important;
        border: 1px solid var(--border-color, #e0e0e0);
        font-weight: 600;
        font-size: 0.95rem;
        padding: 14px 26px;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-ty-secondary:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .ty-newsletter-footer {
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        padding: 18px 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.84rem;
        color: #64748b;
        flex-wrap: wrap;
        gap: 12px;
    }

    .ty-newsletter-footer a {
        color: var(--accent-color, #d93025);
        text-decoration: underline;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .ty-newsletter-header {
            padding: 32px 20px 24px 20px;
        }

        .ty-newsletter-title {
            font-size: 1.75rem;
        }

        .ty-benefits-section {
            padding: 24px 20px;
        }

        .ty-benefits-grid {
            grid-template-columns: 1fr;
        }

        .ty-newsletter-actions {
            padding: 24px 20px;
            flex-direction: column;
        }

        .btn-ty-primary,
        .btn-ty-secondary {
            width: 100%;
            justify-content: center;
        }

        .ty-newsletter-footer {
            padding: 16px 20px;
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

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
                    <span class="ty-kicker-badge">
                        <i class="ph ph-check-circle"></i> Subscription Confirmed
                    </span>

                    <h1 class="ty-newsletter-title">Welcome to The Head &amp; Tale Briefing</h1>
                    <p class="ty-newsletter-subtitle">
                        You're all set! Thank you for subscribing to our executive newsletter. You will now receive curated briefings, breaking market coverage, and deep-dive analysis directly in your inbox.
                    </p>

                    <?php if (!empty($cleanEmail)) { ?>
                        <div class="ty-email-pill-box">
                            <i class="ph ph-envelope-simple-check"></i> Subscribed: <strong><?php echo $cleanEmail; ?></strong>
                        </div>
                    <?php } ?>
                </div>

                <!-- Benefits Highlights Grid -->
                <div class="ty-benefits-section">
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
                </div>

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