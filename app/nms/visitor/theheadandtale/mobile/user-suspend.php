<?php

    require_once 'header-2.php';

?>
<section class="error_wrapper">
    <div class="container">
        <div class="error_wrapper__content" style="margin-bottom: 200px; float:left; text-align:left">
            <h1>Account Suspended Due to Multiple Active Sessions</h1>
            <p>Dear <strong><?php echo PROFILE_DISPLAY_NAME; ?></strong>,</p>
            <p>We have detected that your account has exceeded the maximum limit of <span class="highlight">2 active
                    screens/sessions</span>.
                To maintain security and fair usage, your account has been <span class="highlight">temporarily
                    suspended</span>.</p>

            <p><strong>Why Did This Happen?</strong></p>
            <p>Our system allows a maximum of <strong>2 active screens per user</strong> at a time. Your account was
                found to be active on more than the allowed limit.</p>

            <p><strong>Suspension Duration:</strong> Your account will be automatically restored in <span
                    class="highlight">48 hours</span>.</p>

            <p>If you believe this is an error, please contact our support team:</p>

            <a href="mailto:info@theheadandtale.com" class="support-btn">Contact Support</a>

        </div>
    </div>
</section>
<?php

require_once 'footer.php';