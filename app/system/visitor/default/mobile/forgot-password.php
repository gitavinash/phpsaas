<?php

require_once 'header.php';

?>
<div class="col d-none d-lg-block">
    <div class="slider">
        <div class="slide">
            <div class="img">
                <img src="/app/system/visitor/default/desktop/assets/images/icons/forgot_password.svg" alt="">
            </div>
            <div class="text">
                <p><strong>Lost Your Password?</strong>Let's Get You Back In!</p>
            </div>
        </div>
    </div>
</div>
<div class="col form-bg">
    <div class="form">
        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
        <div class="card-body">
            <div class="form-heading"><strong>Forgot Your Password?</strong>We'll email you instructions
                on how to reset it
            </div>
            <form id="forgot" autocomplete="off">
                <div class="form-group">
                    <input type="text" class="form-control input-with-label" name="email" id="email"
                        placeholder="Email">
                </div>
                <input type="hidden" id="recaptcha_token" name="recaptcha_token">
                <input type="hidden" name="type" value="auth">
                <input type="hidden" name="action" value="forgotPassword">
                <button type="submit" id="forgotPassword" class="button theme-button">Recover password</button>
                <div class="form-group">
                    <p>Know your password? <a href="/login/">Back to Log in</a></p>
                </div>
            </form>
        </div>
        <?php

        require_once 'footer.php';

        ?>