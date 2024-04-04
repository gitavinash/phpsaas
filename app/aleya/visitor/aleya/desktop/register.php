<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title><?php echo ucwords(str_replace('/', '', ACTIVE_URL)).' | '.SITE_NAME ?></title>
    <link href="<?php echo CSS_LIBRARY ?>bootstrap_5/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>process.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="centered-box">
            <div class="row">
                <div class="col d-none d-lg-block">
                    <div class="slider">
                        box 1
                    </div>
                </div>
                <div class="col form-bg">
                    <div class="form">
                        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
                        <div class="card-body">
                            <div class="form-heading">
                                <strong>Get Started:</strong> Register Your Account
                            </div>
                            <form autocomplete="off">
                                <div class="form-group">
                                    <!-- <label for="inputField2" class="form-label">Input 2 Label</label> -->
                                    <input type="text" class="form-control input-with-label" id="inputField2"
                                        placeholder="Full name">
                                </div>
                                <div class="form-group">
                                    <!-- <label for="inputField2" class="form-label">Input 2 Label</label> -->
                                    <input type="text" class="form-control input-with-label" id="inputField2"
                                        placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <!-- <label for="inputField2" class="form-label">Input 2 Label</label> -->
                                    <input type="text" class="form-control input-with-label" id="inputField2"
                                        placeholder="Contact No.">
                                </div>
                                <div class="form-group">
                                    <!-- <label for="inputField2" class="form-label">Input 2 Label</label> -->
                                    <input type="password" class="form-control input-with-label" id="inputField2"
                                        placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <!-- <label for="inputField2" class="form-label">Input 2 Label</label> -->
                                    <input type="password" class="form-control input-with-label" id="inputField2"
                                        placeholder="Confirm password">
                                </div>
                                <button type="submit" class="button theme-button">Register</button>
                                <div class="form-group">
                                    <p>Already have an Account? <a href="/login/">Log in</a></p>
                                </div>
                            </form>
                        </div>
                        <div class="legal">
                            <p>By continuing you agree to our <a href="https://blumox.com/terms-and-conditions/">Terms
                                    of Service</a> and <a href="https://blumox.com/privacy-policy/">Privacy
                                    Policy</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p class="version">
            v <?php echo ALEYA_APP_VERSION ?>
        </p>
    </div>
</body>

</html>