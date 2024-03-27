<?php

    // header("Location: /login/", TRUE, 301);
    // exit();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/login.css">
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
                            <h3>welcome <br> back!</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 px-0">
                    <div class="login_wrapper__right">
                        <h6>Login</h6>
                        <div class="login_img">
                            <img src="assets/images/login_img.png" class="img-fluid" alt="">
                        </div>
                        <form action="">
                            <div class="login_input">
                                <label for="">Enter your mobile number</label>
                                <input type="text" autofocus>
                            </div>
                        </form>
                        <div class="continue_btn">
                            <button class="global_btn">Continue</button>
                        </div>
                        <div class="register">
                            <p>Don't have an account yet? <a href="/register.html">Register</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>