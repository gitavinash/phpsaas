<?php

if (isset($connection) && $connection) {
    mysqli_query($connection, "START TRANSACTION");
}

require_once 'header.php';

?>

<section class="pf-page-section">
    <div class="container">
        <div class="pf-layout-card">
            <div class="pf-card-banner"></div>

            <div class="pf-header">
                <div class="pf-error-icon-wrap">
                    <div class="pf-icon-pulse"></div>
                    <i class="ph ph-warning-circle pf-error-icon-lg"></i>
                </div>

                <span class="pf-kicker-tag">Payment Not Completed</span>
                <h1 class="pf-title">Your Payment Could Not Be Processed</h1>
                <p class="pf-subtitle">No worries. Your transaction was not completed successfully. Please review your payment details and try again. If money was debited, it is typically auto-reversed by your bank.</p>
            </div>

            <div class="pf-body-grid">
                <div class="pf-col pf-receipt-col">
                    <div class="pf-box-header">
                        <i class="ph ph-receipt"></i>
                        <h3>Transaction Snapshot</h3>
                    </div>

                    <ul class="pf-detail-list">
                        <?php if (isset($order_id) && !empty($order_id)) { ?>
                            <li class="pf-detail-row">
                                <span class="pf-detail-label">Transaction Ref</span>
                                <span class="pf-detail-val pf-ref-code">#<?php echo htmlspecialchars(base64_decode($order_id)); ?></span>
                            </li>
                        <?php } ?>

                        <li class="pf-detail-row">
                            <span class="pf-detail-label">Payment Status</span>
                            <span class="pf-status-pill">
                                <span class="pf-dot"></span> Declined
                            </span>
                        </li>

                        <li class="pf-detail-row">
                            <span class="pf-detail-label">Amount Charged</span>
                            <span class="pf-detail-val">Not Captured</span>
                        </li>

                        <li class="pf-detail-row">
                            <span class="pf-detail-label">Date</span>
                            <span class="pf-detail-val"><?php echo date('M d, Y'); ?></span>
                        </li>
                    </ul>
                </div>

                <div class="pf-col pf-actions-col">
                    <div>
                        <div class="pf-box-header">
                            <i class="ph ph-life-buoy"></i>
                            <h3>What You Can Do</h3>
                        </div>

                        <ul class="pf-tips-list">
                            <li>
                                <span class="pf-tip-icon"><i class="ph ph-check"></i></span>
                                <span>Verify card details, UPI PIN, bank OTP, or daily transaction limits.</span>
                            </li>
                            <li>
                                <span class="pf-tip-icon"><i class="ph ph-check"></i></span>
                                <span>Retry with another card, UPI app, or netbanking if this method fails again.</span>
                            </li>
                            <li>
                                <span class="pf-tip-icon"><i class="ph ph-check"></i></span>
                                <span>If money was debited, allow your bank some time for automatic reversal.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pf-actions-wrapper">
                        <a href="/subscribe/" class="pf-btn-primary">Try Payment Again</a>
                        <a href="/" class="pf-btn-secondary">Back to Home</a>
                    </div>
                </div>
            </div>

            <div class="pf-footer-bar">
                <div class="pf-footer-item">
                    <i class="ph ph-shield-check"></i>
                    <span>Secure Checkout Protected</span>
                </div>
                <div class="pf-footer-item">
                    <i class="ph ph-clock-countdown"></i>
                    <span>Retry Anytime</span>
                </div>
                <div class="pf-footer-item">
                    <i class="ph ph-question"></i>
                    <span>Need help? <a href="mailto:info@theheadandtale.com">info@theheadandtale.com</a></span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

require_once 'footer.php';

?>