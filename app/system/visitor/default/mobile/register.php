<?php

require_once 'header.php';

?>
<div class="col form-bg">
    <div class="form">
        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
        <div class="card-body">
            <div class="form-heading">
                <strong>Create Account</strong>Fill in your details to get started.
            </div>
            <form id="register" autocomplete="off">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" name="first_name" id="first_name" placeholder="First name*" class="form-control"
                            autofocus required />
                    </div>
                    <div class="form-group">
                        <input type="text" name="last_name" id="last_name" placeholder="Last name*" class="form-control"
                            required />
                    </div>
                </div>
                <div class="form-group">
                    <input type="number" name="mobile" id="mobile" placeholder="Mobile number*"
                        class="form-control" required />
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" id="email" placeholder="Email address*" name="email"
                        required />
                </div>
                <div class="form-group">
                    <div class="input-password-wrapper">
                        <input type="password" class="form-control" id="password" placeholder="Password*" name="password"
                            required />
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
                        <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm Password*"
                            name="confirm_password" required />
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
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="userType"
                    value="<?php echo ((isset($registration_type)) ? $registration_type : 'administrator') ?>">
                <button type="submit" id="registerBtn" class="button theme-button">Create Account</button>
                <div class="form-group">
                    <p>Already have an account? <a href="/login/">Log in</a></p>
                </div>
            </form>
        </div>
        <?php

        require_once 'footer.php';

        ?>