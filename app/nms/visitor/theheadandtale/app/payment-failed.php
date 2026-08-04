<?php

mysqli_query($connection, "START TRANSACTION");

require_once 'header.php';

?>

<div class="status-page-wrapper">
    <div class="container">
        <div class="status-card">
            <div class="status-icon-wrapper error">
                <i class="ph ph-x-circle"></i>
            </div>
            <h1 class="status-title">Payment Declined</h1>
            <p class="status-desc">We were unable to process your payment. Please check your payment details or try again with a different payment method.</p>
            
            <?php if (isset($order_id) && !empty($order_id)) { ?>
                <div class="transaction-badge-box">
                    <span class="transaction-label">Transaction ID:</span>
                    <span class="transaction-badge"><?php echo htmlspecialchars(base64_decode($order_id)); ?></span>
                </div>
            <?php } ?>

            <div class="status-actions">
                <a href="/subscribe/" class="auth-btn primary-btn">Try Again</a>
                <a href="/" class="auth-btn primary-btn outline">Back to Home</a>
            </div>
        </div>
    </div>
</div>

<?php

require_once 'footer.php';

?>
