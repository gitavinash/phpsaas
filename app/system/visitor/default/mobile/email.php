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
                <img src="/app/system/visitor/default/desktop/assets/images/icons/product_iteration.svg" alt="">
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
                <strong>Verify email</strong> <?php echo $message ?>
            </div>
            <form id="register" autocomplete="off">
                <div class="form-group">
                    <p><a href="/login/">Click here to access your account.</a></p>
                </div>
            </form>
        </div>
        <?php

    require_once 'footer.php';
    exit();

    ?>