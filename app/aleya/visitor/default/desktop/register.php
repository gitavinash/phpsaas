<?php

require_once 'header.php';

?>
<div class="col d-none d-lg-block">
    <div class="slider">
        <!-- <div class="slide">
            <div class="img">
                <img src="<?php echo USER_IMAGES ?>icons/forgot_password.svg" alt="">
            </div>
            <div class="text">
                <p><strong>Track and Convert Leads</strong> Manage potential customers.</p>
            </div>
        </div> -->
        <div class="slide">
            <div class="img">
                <img src="<?php echo USER_IMAGES ?>icons/product_iteration.svg" alt="">
            </div>
            <div class="text">
                <p><strong>Boost Your Productivity</strong> Get more done in less time.</p>
            </div>
        </div>
    </div>
</div>
<div class="col form-bg">
    <div class="form">
        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
        <div class="card-body">
            <div class="form-heading">
                <strong>Get Started:</strong> Register Your Account
            </div>
            <form id="register" autocomplete="off">
                <div class="form-group">
                    <input type="text" name="first_name" id="first_name" placeholder="First name*" class="form-control" autofocus required />
                </div>
                <div class="form-group">
                    <input type="text" name="last_name" id="last_name" placeholder="Last name*" class="form-control" required />
                </div>
                <div class="form-group">
                    <input type="number" onwheel="this.blur()" name="mobile" id="mobile" placeholder="Mobile no*" class="form-control" required />
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" id="email" placeholder="Email address*" name="email" required />
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" placeholder="Password*" name="password" required />
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm Password*" name="confirm_password" required />
                </div>
                <input type="hidden" name="type" value="auth">
                <input type="hidden" name="action" value="register">
                <button type="submit" id="registerBtn" class="button theme-button">Register</button>
                <div class="form-group">
                    <p>Already have an Account? <a href="/login/">Log in</a></p>
                </div>
            </form>
        </div>
        <?php

        require_once 'footer.php';

        ?>