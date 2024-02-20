<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/otp.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <section class="login_wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 px-0">
                    <div class="login_wrapper__left">
                        <div class="logo">
                            <a href="#"><img src="assets/images/aleya_logo.png" class="img-fluid" alt=""></a>
                        </div>
                        <div class="login_title">
                            <h3>Access to our <br> Dashbord</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 px-0">
                    <div class="login_wrapper__right">
                        <h6>Verify OTP</h6>
                        <div class="login_img">
                            <img src="assets/images/verify.png" class="img-fluid" alt="">
                        </div>
                        <div class="otp_wrapper">
                            <form action="">
                                <h3>OTP Code</h3>
                                <div class="otp_code inputs" id="inputs">
                                    <input class="input" type="text" inputmode="numeric" maxlength="1" />
                                    <input class="input" type="text" inputmode="numeric" maxlength="1" />
                                    <input class="input" type="text" inputmode="numeric" maxlength="1" />
                                    <input class="input" type="text" inputmode="numeric" maxlength="1" />
                                    <input class="input" type="text" inputmode="numeric" maxlength="1" />
                                    <input class="input" type="text" inputmode="numeric" maxlength="1" />
                                </div>
                                <div class="otp_number">
                                    <p id="countdown">00:30 <span>second</span></p>
                                </div>
                            </form>
                        </div>
                        <div class="continue_btn">
                            <button class="global_btn">Verify OTP</button>
                        </div>
                        <div class="register">
                            <p>Didn’t Receive The OTP? <a href="/register.html">Resend</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

    <script src="assets/js/otp.js"></script>
    <script src="assets/js/counter.js"></script>
</body>

</html>