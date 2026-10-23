<?php

include_once 'header.php';

$alreadyVerified = false;
$userNotFound = false;
$serverError = false;
$msg = '';

if (isset($_GET['email'])) {
    $email = mysqli_real_escape_string($connection, base64_decode($_GET['email']));
    if ($checkEmail = mysqli_query($connection, "SELECT user_email, user_status FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}'")) {
        if (mysqli_num_rows($checkEmail) != 0) {
            extract(mysqli_fetch_assoc($checkEmail));
            if ($user_status == 1) {
                $alreadyVerified = true;
            } else {
                $showError = true;
            }
        } else {
            $userNotFound = true;
            $msg = 'No user account found with this email.';
        }
    } else {
        $serverError = true;
        $msg = 'Something went wrong while connecting to the server.';
    }
}

?>
<div class="col form-bg">
    <div class="form">
        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
        <div class="card-body">
            <div class="auth-icon-badge" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
            </div>
            
            <?php if (isset($showError)): ?>
                <div class="form-heading">
                    <strong>Verify Your Email</strong>We sent a verification link to your email address.
                </div>
                <form id="verifyEmail" autocomplete="off">
                    <div class="beforeClick">
                        <div class="form-group">
                            <p style="text-align: left; color: #475467; font-size: 14px; margin-bottom: 20px;">
                                Please check your inbox and click the verification link to activate your account. If you didn't receive it, click below to resend.
                            </p>
                        </div>
                        <input type="hidden" id="recaptcha_token" name="recaptcha_token">
                        <input type="hidden" name="type" value="auth">
                        <input type="hidden" name="action" value="emailVerification">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" id="resendVerificationMail" class="button theme-button">Resend Verification Email</button>
                        <div class="form-group">
                            <a href="/login/" class="back-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                                Back to Log in
                            </a>
                        </div>
                    </div>
                    <div class="afterClick">
                        <div class="status-card">
                            <div class="status-icon-badge" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <div class="status-card-title">Email Sent!</div>
                            <p id="statusMsg" class="status-card-desc">A new verification link has been sent to your email.</p>
                            <a href="/login/" class="button theme-button">Back to Log in</a>
                        </div>
                    </div>
                </form>
            <?php elseif ($alreadyVerified): ?>
                <div class="status-card">
                    <div class="status-icon-badge" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="status-card-title">Email Verified</div>
                    <p class="status-card-desc">Your account is already verified and ready to use.</p>
                    <a href="/login/" class="button theme-button">Proceed to Log in</a>
                </div>
            <?php else: ?>
                <div class="status-card">
                    <div class="status-card-title">Verification Notice</div>
                    <p class="status-card-desc"><?php echo !empty($msg) ? htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') : 'Invalid or expired verification link.'; ?></p>
                    <a href="/login/" class="button theme-button">Return to Log in</a>
                </div>
            <?php endif; ?>
        </div>
        <?php

        include_once 'footer.php';

        ?>