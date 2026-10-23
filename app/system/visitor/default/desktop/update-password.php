<?php

require_once 'header.php';

?>
<div class="col form-bg">
    <div class="form">
        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
        <div class="card-body">
            <div class="auth-icon-badge" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <div class="form-heading">
                <strong>Reset Your Password</strong>Please enter and confirm your new secure password.
            </div>
            <form id="updatePasswordForm" autocomplete="off">
                <div class="beforeClick">
                    <div class="form-group">
                        <div class="input-password-wrapper">
                            <input type="password" class="form-control" name="pass" id="password"
                                placeholder="New Password" required autofocus>
                            <button type="button" class="password-toggle-btn" aria-label="Show password">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-password-wrapper">
                            <input type="password" class="form-control" name="cnf_pass" id="confirmPassword"
                                placeholder="Confirm New Password" required>
                            <button type="button" class="password-toggle-btn" aria-label="Show password">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" id="recaptcha_token" name="recaptcha_token">
                    <input type="hidden" name="type" value="auth">
                    <input type="hidden" name="action" value="updatePassword">
                    <input type="hidden" name="transaction" value="<?php echo htmlspecialchars((isset($_GET['transaction']) ? $_GET['transaction'] : ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars((isset($_GET['email']) ? $_GET['email'] : ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" id="updatePassword" class="button theme-button">Update Password</button>
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
                        <div class="status-card-title">Password Updated!</div>
                        <p id="statusMsg" class="status-card-desc">Your password has been reset successfully. Redirecting to login...</p>
                        <a href="/login/" class="button theme-button">Proceed to Log in</a>
                    </div>
                </div>
            </form>
        </div>
        <?php

        require_once 'footer.php';

        ?>