<?php
header("Location: /profile/", true, 302);
exit();

require_once 'header.php';

?>
<!-- AUTH SCREEN CONTENT -->
<div id="view-auth" class="page-view active">
    <div class="auth-body">
        <!-- <div class="auth-logo">
            <img
                src="<?php echo SITE_LOGO ?>"
                alt="<?php echo SITE_NAME ?>"
                class="logo-img" />
        </div> -->
        <h2 class="auth-title">
            Welcome to <?php echo SITE_NAME ?>
        </h2>
        <p class="auth-subtitle">
            Enter your email to get started.
        </p>

        <div class="auth-form">
            <input
                type="email"
                class="auth-input"
                name="email"
                placeholder="youremail@example.com" />
            <a
                href="profile.html"
                class="auth-btn primary-btn">
                Continue
            </a>
        </div>

        <div class="auth-divider">
            <span>or</span>
        </div>

        <a
            href="/oauth/google/?redirect_url=/manage-subscription/"
            class="auth-btn google-btn">
            <i class="ph-fill ph-google-logo"></i> Continue with Google
        </a>

        <!-- <p class="auth-footer">
            Don't have an account?
            <a href="auth.html">Sign up</a><br /><br />
            By signing in, you agree to the Terms & Conditions and Privacy
            Policy
        </p> -->
    </div>
</div>
<?php

require_once 'footer.php';

?>