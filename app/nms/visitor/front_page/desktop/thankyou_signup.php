<?php

require_once 'header.php';

?>

<div class="status-page-wrapper">
    <div class="container">
        <div class="status-card">
            <div class="status-icon-wrapper success">
                <i class="ph ph-user-check"></i>
            </div>
            <h1 class="status-title">Thank You for Signing Up</h1>
            <p class="status-desc">We appreciate your support and trust in us. Welcome to The Head and Tale community!</p>
            <p class="status-note">If you have any questions, feedback, or suggestions, please contact us at <a href="mailto:info@theheadandtale.com">info@theheadandtale.com</a>.</p>
            
            <?php if (isset($redirect_url)) { ?>
                <div class="redirect-notice" data-redirect-url="<?php echo htmlspecialchars($redirect_url); ?>">
                    <p><i class="ph ph-arrows-clockwise"></i> Redirecting automatically in 5 seconds... <a href="<?php echo htmlspecialchars($redirect_url); ?>">Click here</a> if you are not redirected.</p>
                </div>
            <?php
                exit();
            } else { ?>
                <div class="status-actions">
                    <a href="/" class="auth-btn primary-btn">Start Reading</a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php

require_once 'footer.php';

?>