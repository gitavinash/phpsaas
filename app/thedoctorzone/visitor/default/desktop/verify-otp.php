<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME ?></title>
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4 bg-sky position-relative main">
                <div class="d-flex justify-content-center w-100">
                    <img src="<?php echo SITE_LOGO ?>" class="rounded-circle profile mt-5" alt="">
                </div>
    
                <div class="signup w-100">
                    <form id="otpForm" >
                        <input type="number" id="otp" name="otp" placeholder="Enter OTP" class="form-control mb-4 w-100" autofocus >
                        <input type="hidden" name="type" value="authentication" >
                        <input type="hidden" name="action" value="verifyOTP" >
                        <input type="hidden" name="mobile" value="<?php echo $mobile ?>" >
                        <input type="hidden" name="otp_id" value="<?php echo $id ?>" >
                        <?php
                        
                        if(isset($redirect)) {

                            echo "<input type='hidden' name='redirectUrl' value='{$redirect}' >";

                        }

                        ?>
                        
                        <button id="verifyOTP" class="btn dark-blue w-100 text-white" >Verify OTP</button>
                    </form>
                </div>
        
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
    <?php include_once NOTIFICATION.'index.php' ?>
    <script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" ></script>
    <script src="<?php echo APP_JS_LIBRARY ?>mobile-app.min.js" ></script>
</body>
</html>