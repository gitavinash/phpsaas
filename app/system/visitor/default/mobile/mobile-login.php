<?php

$redirect_url = ((isset($redirect_url)) ? $redirect_url : '/dashboard/');

if (isset($_COOKIE['isLogin'])) {

    header("Location: {$redirect_url}");
    exit();
}

$quoteOfTheDay['quoteText'] = '';
$quoteOfTheDay['quoteAuthor'] = '';

if (SITE_VERSION == 'desktop') {
    $context = stream_context_create([
        'http' => [
            'timeout' => 60
        ]
    ]);

    $quoteOfTheDay = file_get_contents("https://api.forismatic.com/api/1.0/?method=getQuote&format=json&key=1&lang=en", false, $context);
    $quoteOfTheDay = json_decode($quoteOfTheDay, TRUE);
}

require_once 'header.php';

?>
<div class="col d-none d-lg-block">
    <div class="slider">
        <div class="slide">
            <div class="blockquote-wrapper">
                <div class="blockquote">
                    <?php

                    if ($quoteOfTheDay['quoteText'] != '') {
                        echo '<p>' . $quoteOfTheDay['quoteText'] . '</p>';
                    }
                    if ($quoteOfTheDay['quoteAuthor'] != '') {
                        echo '<p><strong>&mdash; ' . $quoteOfTheDay['quoteAuthor'] . '</strong></p>';
                    }

                    ?>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="col form-bg">
    <div class="form">
        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
        <div class="card-body">
            <div id="enterMobile">
                <div class="form-heading">
                    <strong>Welcome</strong> Enter your phone number to get started.
                </div>
                <form id="login" autocomplete="off">
                    <div class="form-group">
                        <input type="number" class="form-control input-with-label" name="mobile" id="mobile" required
                            placeholder="Enter your mobile number"
                            value="<?php echo ((isset($mobile)) ? $mobile : '') ?>">
                    </div>
                    <button type="submit" id="mobileLoginContinueBtn" class="button theme-button">Continue</button>
                </form>
            </div>
            <div id="verifyOTP" style="display: none;">
                <div class="form-heading">
                    <strong>Verify OTP</strong> Please enter it below to verify.
                </div>
                <form id="login" autocomplete="off">
                    <div class="form-group">
                        <input type="number" class="form-control input-with-label" name="otp" id="otpValue" required
                            placeholder="123456" value="<?php echo ((isset($mobile)) ? $mobile : '') ?>">
                    </div>
                    <a href="#" id="resendOTPBtn" class="disabled"
                        style="pointer-events: none; float: left; margin-bottom: 10px; text-decoration: none;">Resend
                        OTP in 30s</a>
                    <button type="submit" id="verifyOTPBtn" class="button theme-button">Verify OTP</button>
                </form>
            </div>
            <div id="registrationForm" style="display: none;">
                <div class="form-heading">
                    <strong>Create Your Profile</strong> Complete your profile to get started.
                </div>
                <form id="registration" autocomplete="off">
                    <div class="form-group">
                        <input type="text" class="form-control input-with-label" name="full_name" id="fullName" required
                            placeholder="Enter your full name*">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control input-with-label" name="email" id="email" required
                            placeholder="Enter your email address">
                    </div>
                    <button type="submit" id="registrationeBtn" class="button theme-button">Complete
                        registration</button>
                </form>
            </div>
        </div>
        <?php

        require_once 'footer.php';

        ?>