<?php

require_once 'header.php';

?>
<div class="col d-none d-lg-block">
    <div class="slider">
        <div class="slide">
            <div class="img">
                <img src="/app/system/visitor/default/desktop/assets/images/icons/forgot_password.svg"
                    alt="Update Password">
            </div>
            <div class="text">
                <p><strong>Reset Your Password</strong>Secure your account with a new one!</p>
            </div>
        </div>
    </div>
</div>
<div class="col form-bg">
    <div class="form">
        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
        <div class="card-body">
            <div class="form-heading"><strong>Update Your Password</strong>Enter and confirm your new password below
            </div>
            <form id="updatePasswordForm" autocomplete="off">
                <div class="form-group">
                    <input type="password" class="form-control input-with-label" name="pass" id="password"
                        placeholder="New Password">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control input-with-label" name="cnf_pass" id="confirmPassword"
                        placeholder="Confirm New Password">
                </div>
                <input type="hidden" name="type" value="auth">
                <input type="hidden" name="action" value="updatePassword">
                <input type="hidden" name="transaction" value="<?php echo $_GET['transaction'] ?>">
                <input type="hidden" name="email" value="<?php echo $_GET['email'] ?>">
                <button type="submit" id="updatePassword" class="button theme-button">Update Password</button>
                <div class="form-group">
                    <p>Remembered everything? <a href="/login/">Back to Log in</a></p>
                </div>
            </form>
        </div>
        <?php

        require_once 'footer.php';

        ?>