<?php

$redirect_url = ((isset($redirect_url)) ? $redirect_url : '/manage-subscription/');

if (isset($responseMsg)) {
    echo "<script>alert('{$responseMsg}')</script>";
}

if (isset($_COOKIE['isLogin'])) {
    header("Location: {$redirect_url}");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Login | <?php echo SITE_NAME ?></title>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- or -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta name="robots" content="noindex,nofollow">
    <?php require_once GOOGLE_RECAPTCHA_GENERATOR; ?>
</head>

<body>
    <div class="login_form">
        <div class="login__header">
            <a href="/"><img src="<?php echo SITE_LOGO ?>" class="img-fluid" alt=""></a>
        </div>
        <div class="login_content">
            <div class="form_box login">
                <form id="login" autocomplete="off">
                    <div id="enterEmail">
                        <div class="login_input">
                            <p style="float: left; margin-bottom: 1rem !important;" id="formTitle">
                                <strong>Enter your email to get started.</strong>
                            </p>
                        </div>
                        <div class="login_input">
                            <input type="text" id="email" placeholder="Email" name="email" required>
                        </div>
                        <div class="sign--in_btn">
                            <input type="hidden" class="recaptcha_token" name="recaptcha_token">
                            <button type="submit" id="emailLoginContinueBtn" class="button theme-button" formnovalidate>
                                Continue
                            </button>
                        </div>
                    </div>

                    <div id="verifyEmailOTP" style="display: none;">
                        <div class="login_input">
                            <p style="float: left; margin-bottom: 1rem !important;"><strong>Verify OTP</strong></p>
                        </div>

                        <!-- No nested form here -->
                        <div class="form-group">
                            <input type="text" inputmode="numeric" pattern="^\d{6}$" maxlength="6"
                                class="form-control input-with-label" name="otp" id="otpEmailValue" placeholder="123456"
                                disabled>
                        </div>

                        <input type="hidden" class="recaptcha_token" name="recaptcha_token">

                        <div class="login_input">
                            <p style="float: left; margin-bottom: 1rem !important; margin-top: 1rem !important;"><a
                                    href="#" id="resendEmailOTPBtn" class="disabled"
                                    style="pointer-events: none; text-decoration: none;">Resend
                                    OTP
                                    in
                                    30s</a></p>
                            </p>
                        </div>
                        <button type="button" id="verifyEmailOTPBtn" class="button theme-button">Verify OTP</button>
                    </div>

                    <div id="userRegistration" style="display: none;">
                        <div class="login_input">
                            <p style="float: left; margin-bottom: 1rem !important;"><strong>Enter your full
                                    name</strong></p>
                        </div>

                        <!-- No nested form here -->
                        <div class="form-group">
                            <input type="text" class="form-control input-with-label" name="name" id="userName"
                                placeholder="Avinash Kumar">
                        </div>

                        <input type="hidden" class="recaptcha_token" name="recaptcha_token">

                        <button type="button" id="userRegistrationBtn" class="button theme-button">Complete
                            Registration</button>
                    </div>
                </form>

                <div class="or">
                    <p>or</p>
                </div>

                <a href='/oauth/google/?redirect_url=<?php
                                                        echo ((isset($redirect_url)) ? $redirect_url : '/manage-subscription/')
                                                            . ((isset($method)) ? '&method=' . $method : '')
                                                            . ((isset($id)) ? '&id=' . $id : '')
                                                            . ((isset($baseUrl)) ? '&baseUrl=' . $baseUrl  : '');
                                                        ?>' class="authBtn">
                    <img src="<?php echo USER_IMAGES ?>google.png" class="img-fluid" alt="">Continue with Google
                </a>

                <br />
                <p>
                    Don't have an account?
                    <a href="/signup/<?php
                                        echo ((isset($redirect_url)) ? '?redirect_url=' . $redirect_url . '&auth=true' : '/manage-subscription/')
                                            . ((isset($method)) ? '&method=' . $method : '')
                                            . ((isset($id)) ? '&id=' . $id : '')
                                            . ((isset($baseUrl)) ? '&baseUrl=' . $baseUrl . '&auth=true' : '');
                                        ?>">Sign up</a>
                </p>

                <div class="form_bottom">
                    <p>By signing for The Head And Tale, you agree to the
                        <a href="/terms-and-conditions/" target="_blank">Terms & Conditions</a>
                        and <a href="/privacy-policy/" target="_blank">Privacy Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php require_once NOTIFICATION . 'index.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo USER_JS ?>script.js"></script>

    <?php if (isset($alertMsg)) {
        echo "<script>alert('{$alertMsg}')</script>";
    } ?>
</body>

</html>