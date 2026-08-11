<?php

if (isset($connection) && $connection) {
    mysqli_query($connection, "START TRANSACTION");
}

require_once 'header.php';

?>

<style>
    .pf-page-section {
        padding: 60px 0 90px 0;
        min-height: calc(100vh - 380px);
        background-color: var(--bg-color, #fdfbf7);
        background-image:
            radial-gradient(circle at 50% 10%, rgba(217, 48, 37, 0.07) 0%, transparent 60%),
            radial-gradient(circle at 8% 88%, rgba(249, 115, 22, 0.05) 0%, transparent 52%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-body, "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif);
    }

    .pf-layout-card {
        width: 100%;
        max-width: 860px;
        margin: 0 auto;
        background: var(--white, #ffffff);
        border: 1px solid var(--border-color, #e0e0e0);
        border-radius: 16px;
        box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.06), 0 4px 12px rgba(0, 0, 0, 0.02);
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
    }

    .pf-card-banner {
        height: 5px;
        background: linear-gradient(90deg, #dc2626 0%, #f97316 100%);
    }

    .pf-header {
        padding: 44px 40px 32px 40px;
        text-align: center;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .pf-error-icon-wrap {
        position: relative;
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #fff1f2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        border: 2px solid #fecdd3;
    }

    .pf-icon-pulse {
        position: absolute;
        top: -6px;
        left: -6px;
        right: -6px;
        bottom: -6px;
        border-radius: 50%;
        border: 2px solid rgba(220, 38, 38, 0.25);
        animation: pfPulseRing 2.5s infinite cubic-bezier(0.4, 0, 0.6, 1);
    }

    @keyframes pfPulseRing {
        0% {
            transform: scale(0.95);
            opacity: 0.8;
        }

        50% {
            transform: scale(1.1);
            opacity: 0.2;
        }

        100% {
            transform: scale(0.95);
            opacity: 0.8;
        }
    }

    .pf-kicker-tag {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: #b91c1c;
        background: rgba(220, 38, 38, 0.1);
        padding: 3px 12px;
        border-radius: 4px;
        margin-bottom: 12px;
    }

    .pf-title {
        font-family: var(--font-heading, "Playfair Display", Georgia, serif);
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--primary-color, #0a0a0a);
        margin-bottom: 10px;
        line-height: 1.2;
        letter-spacing: -0.5px;
    }

    .pf-subtitle {
        font-size: 1.05rem;
        color: var(--text-secondary, #555555);
        max-width: 620px;
        line-height: 1.55;
        margin: 0;
    }

    .pf-body-grid {
        display: grid;
        grid-template-columns: 1fr 1.1fr;
        gap: 0;
    }

    .pf-col {
        padding: 32px 36px;
        box-sizing: border-box;
    }

    .pf-receipt-col {
        background: #fffaf9;
        border-right: 1px solid #f1f5f9;
    }

    .pf-box-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        color: var(--primary-color, #0a0a0a);
    }

    .pf-box-header h3 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        font-family: var(--font-body, "Inter", sans-serif);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #0a0a0a;
    }

    .pf-box-header i {
        color: #64748b;
        font-size: 1rem;
    }

    .pf-detail-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pf-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 0.92rem;
    }

    .pf-detail-row:last-child {
        border-bottom: none;
    }

    .pf-detail-label {
        color: #64748b;
        font-weight: 500;
    }

    .pf-detail-val {
        font-weight: 600;
        color: var(--primary-color, #0a0a0a);
        text-align: right;
    }

    .pf-detail-val.pf-ref-code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        color: #b91c1c;
        background: #ffe4e6;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .pf-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 12px;
        background: #ffe4e6;
        color: #b91c1c;
    }

    .pf-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #dc2626;
    }

    .pf-actions-col {
        background: var(--white, #ffffff);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .pf-tips-list {
        list-style: none;
        padding: 0;
        margin: 0 0 24px 0;
    }

    .pf-tips-list li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 0.92rem;
        color: var(--text-secondary, #555555);
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .pf-tip-icon {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #fff7ed;
        color: #ea580c;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .pf-actions-wrapper {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .pf-btn-primary {
        display: block;
        width: 100%;
        text-align: center;
        padding: 13px 20px;
        background: #111827;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.9rem;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(17, 24, 39, 0.12);
        box-sizing: border-box;
    }

    .pf-btn-primary:hover {
        background: #dc2626;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.2);
    }

    .pf-btn-secondary {
        display: block;
        width: 100%;
        text-align: center;
        padding: 12px 20px;
        background: transparent;
        color: #111827 !important;
        border: 1px solid #e2e8f0;
        font-weight: 600;
        font-size: 0.9rem;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .pf-btn-secondary:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #111827 !important;
    }

    .pf-footer-bar {
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        padding: 16px 36px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        font-size: 0.82rem;
        color: #64748b;
    }

    .pf-footer-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pf-footer-item i {
        font-size: 1rem;
        color: #94a3b8;
    }

    .pf-footer-item a {
        color: #d93025;
        text-decoration: underline;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .pf-body-grid {
            grid-template-columns: 1fr;
        }

        .pf-receipt-col {
            border-right: none;
            border-bottom: 1px solid #f1f5f9;
        }

        .pf-header {
            padding: 32px 20px 24px 20px;
        }

        .pf-title {
            font-size: 1.75rem;
        }

        .pf-col {
            padding: 24px 20px;
        }

        .pf-footer-bar {
            padding: 16px 20px;
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<section class="pf-page-section">
    <div class="container">
        <div class="pf-layout-card">
            <div class="pf-card-banner"></div>

            <div class="pf-header">
                <div class="pf-error-icon-wrap">
                    <div class="pf-icon-pulse"></div>
                    <i class="ph ph-warning-circle" style="font-size: 32px;"></i>
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