<?php

require_once LOGIN_CHECK;
require_once 'header.php';

// Cashfree credentials
$cashfreeClientId = PG_APP_ID;
$cashfreeClientSecret = PG_SECRET_KEY;
if (DEVELOPMENT_MODE == TRUE) {
    $cashfreeBaseUrl = 'https://sandbox.cashfree.com';
} else {
    $cashfreeBaseUrl = 'https://api.cashfree.com';
}
$cashfreeApiVersion = '2025-01-01';

// Get parameters from URL
$refId = $_GET['link_id'] ?? $_GET['ref_id'] ?? null;
$numUsers = (int)($_GET['num_users'] ?? 0);

$errors = [];
$paymentStatus = null;
$paymentData = null;

if (!$refId) {
    $errors[] = 'No payment reference found.';
} elseif (!$cashfreeClientId || !$cashfreeClientSecret) {
    $errors[] = 'Cashfree credentials are missing.';
} else {
    // Fetch payment link status from Cashfree
    $ch = curl_init($cashfreeBaseUrl . '/pg/links/' . urlencode($refId));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'x-api-version: ' . $cashfreeApiVersion,
            'x-client-id: ' . $cashfreeClientId,
            'x-client-secret: ' . $cashfreeClientSecret,
        ],
    ]);

    $responseBody = curl_exec($ch);
    $curlErr = curl_error($ch);
    $httpStatus = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlErr) {
        $errors[] = 'Failed to fetch payment status: ' . $curlErr;
    } else {
        $paymentData = json_decode($responseBody, true);
        if ($httpStatus === 200 && isset($paymentData['link_status'])) {
            $paymentStatus = $paymentData['link_status'];
            $numUsers = $numUsers ?: ($paymentData['link_notes']['num_users'] ?? 0);
        } else {
            $errors[] = 'Failed to fetch payment status. HTTP ' . $httpStatus . ': ' . ($paymentData['message'] ?? json_encode($paymentData));
        }
    }
}

function h($val)
{
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

?>

<style>
.payment-status-section {
    padding: 72px 0 88px;
    background: linear-gradient(180deg, #fdf7f5 0%, #f5f6f8 48%, #f5f6f8 100%);
    min-height: 60vh;
}

.payment-status-section .container {
    max-width: 720px;
    margin: 0 auto;
    padding: 0 20px;
}

.status-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e6e7ec;
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.status-hero {
    padding: 48px 32px 38px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.status-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 140px;
    opacity: 0.4;
    z-index: 0;
}

.status-hero.success::before {
    background: radial-gradient(circle at 50% 0%, #e8f5e9 0%, transparent 70%);
}

.status-hero.pending::before {
    background: radial-gradient(circle at 50% 0%, #fff3e0 0%, transparent 70%);
}

.status-hero.failed::before {
    background: radial-gradient(circle at 50% 0%, #ffebee 0%, transparent 70%);
}

.status-icon-wrapper {
    position: relative;
    z-index: 1;
    width: 96px;
    height: 96px;
    margin: 0 auto 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    animation: iconPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes iconPop {
    0% {
        transform: scale(0);
    }

    50% {
        transform: scale(1.1);
    }

    100% {
        transform: scale(1);
    }
}

.status-icon-wrapper.success {
    background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%);
    color: #fff;
    box-shadow: 0 12px 32px rgba(67, 160, 71, 0.3);
}

.status-icon-wrapper.pending {
    background: linear-gradient(135deg, #ffa726 0%, #f57c00 100%);
    color: #fff;
    box-shadow: 0 12px 32px rgba(245, 124, 0, 0.3);
}

.status-icon-wrapper.failed {
    background: linear-gradient(135deg, #ef5350 0%, #c62828 100%);
    color: #fff;
    box-shadow: 0 12px 32px rgba(198, 40, 40, 0.3);
}

.status-title {
    position: relative;
    z-index: 1;
    font-family: "Geologica", sans-serif;
    font-size: 32px;
    font-weight: 800;
    margin: 0 0 10px;
    color: #1d1d1f;
    letter-spacing: -0.4px;
}

.status-subtitle {
    position: relative;
    z-index: 1;
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    color: #6b6b70;
    margin: 0;
    line-height: 1.5;
}

.status-content {
    padding: 32px;
}

.details-grid {
    display: grid;
    gap: 1px;
    background: #e6e7ec;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 28px;
}

.detail-item {
    background: #fafbfc;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.detail-label {
    font-family: "Noto Sans", sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: #6b6b70;
    letter-spacing: 0.3px;
}

.detail-value {
    font-family: "Geologica", sans-serif;
    font-size: 18px;
    font-weight: 750;
    color: #1d1d1f;
    text-align: right;
}

.amount-highlight {
    color: #2e7d32;
    font-size: 22px;
}

.error-banner {
    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
    border: 1px solid #ef9a9a;
    border-radius: 12px;
    padding: 18px 20px;
    color: #c62828;
    margin-bottom: 24px;
    font-family: "Noto Sans", sans-serif;
    font-size: 14px;
    line-height: 1.6;
}

.success-message {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    border: 1px solid #a5d6a7;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 24px;
}

.success-message-text {
    font-family: "Noto Sans", sans-serif;
    font-size: 14px;
    color: #2e7d32;
    line-height: 1.6;
    margin: 0;
}

.action-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 16px 20px;
    background: linear-gradient(135deg, #ec1816 0%, #d81614 100%);
    color: #fff;
    text-align: center;
    text-decoration: none;
    border: none;
    border-radius: 12px;
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    font-weight: 800;
    letter-spacing: 0.6px;
    text-transform: uppercase;
    cursor: pointer;
    box-shadow: 0 12px 28px rgba(236, 24, 22, 0.25);
    transition: all 0.25s ease;
}

.action-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 32px rgba(236, 24, 22, 0.3);
}

.action-button.secondary {
    background: linear-gradient(135deg, #fff 0%, #f5f6f8 100%);
    color: #1d1d1f;
    border: 1px solid #e6e7ec;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
}

.action-button.secondary:hover {
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.button-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 24px;
}

.reference-code {
    display: inline-block;
    background: #f5f6f8;
    padding: 8px 12px;
    border-radius: 8px;
    font-family: "Courier New", monospace;
    font-size: 13px;
    color: #1d1d1f;
    font-weight: 600;
    letter-spacing: 0.5px;
}

@media (max-width: 768px) {
    .payment-status-section {
        padding: 48px 0 64px;
    }

    .status-hero {
        padding: 36px 24px 28px;
    }

    .status-icon-wrapper {
        width: 80px;
        height: 80px;
        font-size: 40px;
    }

    .status-title {
        font-size: 26px;
    }

    .status-subtitle {
        font-size: 15px;
    }

    .status-content {
        padding: 24px;
    }

    .button-row {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="payment-status-section">
    <div class="container">
        <div class="status-card">
            <div class="status-card">
                <?php if (!empty($errors)): ?>
                <div class="status-hero failed">
                    <div class="status-icon-wrapper failed">✕</div>
                    <h1 class="status-title">Unable to Load Status</h1>
                    <p class="status-subtitle">We encountered an issue fetching your payment details</p>
                </div>
                <div class="status-content">
                    <div class="error-banner">
                        <?php foreach ($errors as $err): ?>
                        <div><?php echo h($err); ?></div>
                        <?php endforeach; ?>
                    </div>
                    <a href="/corporate-subscription/" class="action-button">Back to Subscription Plans</a>
                </div>

                <?php elseif ($paymentStatus === 'PAID'): ?>
                <div class="status-hero success">
                    <div class="status-icon-wrapper success">✓</div>
                    <h1 class="status-title">Payment Successful!</h1>
                    <p class="status-subtitle">Your corporate subscription is now active</p>
                </div>
                <div class="status-content">
                    <div class="success-message">
                        <p class="success-message-text">
                            <strong>Great news!</strong> Your payment has been confirmed and your team can now access
                            premium features.
                            You'll receive a confirmation email shortly with subscription details.
                        </p>
                    </div>

                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Team seats</span>
                            <span class="detail-value"><?php echo h($numUsers); ?> users</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Amount paid</span>
                            <span class="detail-value amount-highlight">₹
                                <?php echo number_format($paymentData['link_amount_paid'] ?? 0); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Payment reference</span>
                            <span class="detail-value"><span
                                    class="reference-code"><?php echo h(substr($refId, 0, 24)); ?></span></span>
                        </div>
                    </div>

                    <a href="/manage-subscribers/" class="action-button">Manage Subscribers →</a>
                </div>

                <?php elseif ($paymentStatus === 'ACTIVE'): ?>
                <div class="status-hero pending">
                    <div class="status-icon-wrapper pending">⏳</div>
                    <h1 class="status-title">Payment Pending</h1>
                    <p class="status-subtitle">Complete your payment to activate subscription</p>
                </div>
                <div class="status-content">
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Team seats</span>
                            <span class="detail-value"><?php echo h($numUsers); ?> users</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Amount due</span>
                            <span class="detail-value">₹
                                <?php echo number_format($paymentData['link_amount'] ?? 0); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status</span>
                            <span class="detail-value">Awaiting payment</span>
                        </div>
                    </div>

                    <div class="button-row">
                        <a href="<?php echo h($paymentData['link_url'] ?? '#'); ?>" class="action-button">Complete
                            Payment →</a>
                        <a href="/corporate-subscription/" class="action-button secondary">Start Over</a>
                    </div>
                </div>

                <?php elseif ($paymentStatus === 'PARTIALLY_PAID'): ?>
                <div class="status-hero pending">
                    <div class="status-icon-wrapper pending">⏳</div>
                    <h1 class="status-title">Partial Payment Received</h1>
                    <p class="status-subtitle">Complete the remaining payment to activate your subscription</p>
                </div>
                <div class="status-content">
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Team seats</span>
                            <span class="detail-value"><?php echo h($numUsers); ?> users</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Amount paid</span>
                            <span class="detail-value">₹
                                <?php echo number_format($paymentData['link_amount_paid'] ?? 0); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Remaining balance</span>
                            <span class="detail-value">₹
                                <?php echo number_format(($paymentData['link_amount'] ?? 0) - ($paymentData['link_amount_paid'] ?? 0)); ?></span>
                        </div>
                    </div>

                    <a href="<?php echo h($paymentData['link_url'] ?? '#'); ?>" class="action-button">Complete Payment
                        →</a>
                </div>

                <?php elseif ($paymentStatus === 'EXPIRED' || $paymentStatus === 'CANCELLED'): ?>
                <div class="status-hero failed">
                    <div class="status-icon-wrapper failed">✕</div>
                    <h1 class="status-title">Payment
                        <?php echo $paymentStatus === 'EXPIRED' ? 'Expired' : 'Cancelled'; ?></h1>
                    <p class="status-subtitle">This payment link is no longer valid</p>
                </div>
                <div class="status-content">
                    <div class="error-banner">
                        The payment link has <?php echo $paymentStatus === 'EXPIRED' ? 'expired' : 'been cancelled'; ?>.
                        You'll need to create a new subscription order to proceed.
                    </div>

                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Team seats requested</span>
                            <span class="detail-value"><?php echo h($numUsers); ?> users</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status</span>
                            <span class="detail-value"><?php echo h($paymentStatus); ?></span>
                        </div>
                    </div>

                    <a href="/corporate-subscription/" class="action-button">Create New Subscription</a>
                </div>

                <?php else: ?>
                <div class="status-hero pending">
                    <div class="status-icon-wrapper pending">?</div>
                    <h1 class="status-title">Unknown Status</h1>
                    <p class="status-subtitle">Unable to determine payment status</p>
                </div>
                <div class="status-content">
                    <div class="error-banner">
                        Payment status: <?php echo h($paymentStatus ?? 'Unknown'); ?>
                    </div>
                    <a href="/corporate-subscription/" class="action-button">Back to Subscription Plans</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
</section>

<?php
require_once 'footer.php';