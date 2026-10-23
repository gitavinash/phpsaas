<?php

require_once 'header.php';

?>
<div class="col form-bg">
    <div class="form">
        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
        <div class="card-body">
            <div class="auth-icon-badge" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 2l-2 2m-1.5 1.5L16 7l-1.5-1.5L13 7l-1.5-1.5L10 7 8.5 5.5 7 7 3 11a5.66 5.66 0 1 0 8 8l4-4"></path>
                    <circle cx="16.5" cy="7.5" r=".5" fill="currentColor"></circle>
                </svg>
            </div>
            <div class="form-heading">
                <strong>Forgot Password?</strong>No worries, we'll send you instructions to reset it.
            </div>
            <form id="forgot" autocomplete="off">
                <div class="beforeClick">
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" id="email"
                            placeholder="Enter your registered email" required autofocus>
                    </div>
                    <input type="hidden" id="recaptcha_token" name="recaptcha_token">
                    <input type="hidden" name="type" value="auth">
                    <input type="hidden" name="action" value="forgotPassword">
                    <button type="submit" id="forgotPassword" class="button theme-button">Send Reset Link</button>
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
                        <div class="status-card-title">Check Your Email</div>
                        <p id="statusMsg" class="status-card-desc">We've sent password reset instructions to your email.</p>
                        <a href="/login/" class="button theme-button">Back to Log in</a>
                    </div>
                </div>
            </form>
        </div>
        <?php

        require_once 'footer.php';

        ?>