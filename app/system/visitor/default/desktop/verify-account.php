<?php

$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 5) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

include_once 'header.php';

$alreadyVerified = false;
$userNotFound = false;
$serverError = false;
$msg = '';

if (isset($_GET['email'])) {
    $rawDecodedEmail = trim((string) base64_decode((string) $_GET['email']));
    $email = mysqli_real_escape_string($connection, $rawDecodedEmail);
    $lookup = ($rawDecodedEmail !== '' && function_exists('iswFindUserAndSiteAccessByIdentifier'))
        ? iswFindUserAndSiteAccessByIdentifier($connection, $rawDecodedEmail, (int) SITE_ID, 'email')
        : ['reason' => 'not_found', 'resolved' => null];

    if (!empty($lookup['resolved']) && !empty($lookup['resolved']['has_access'])) {
        $resolvedUser = $lookup['resolved']['user'] ?? [];
        $user_email = (string) ($resolvedUser['user_email'] ?? $rawDecodedEmail);
        $user_status = (int) ($resolvedUser['user_status'] ?? 0);
        if ($user_status == 1) {
            $alreadyVerified = true;
        } else {
            $showError = true;
        }
    } elseif (($lookup['reason'] ?? '') === 'disabled') {
        $userNotFound = true;
        $msg = 'Your workspace access has been deactivated.';
    } elseif (($lookup['reason'] ?? '') === 'no_access') {
        $userNotFound = true;
        $msg = 'You do not have access to this workspace.';
    } else {
        $userNotFound = true;
        $msg = 'No user account found with this email.';
    }
}

?>
<div class="col form-bg">
    <div class="form">
        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
        <div class="card-body">
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
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($rawDecodedEmail, ENT_QUOTES, 'UTF-8'); ?>">
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