<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <section class="login_wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 px-0">
                    <div class="logo_left">
                        <a href="/index.html"><img src="assets/images/aleya_logo.png" class="img-fluid" alt=""></a>
                    </div>
                </div>
                <div class="col-lg-6 px-0">
                    <div class="sign_up">
                        <h6>Register</h6>
                        <div class="register_form">
                            <form action="">
                                <div class="register_form__content form_flex">
                                    <div class="register_input">
                                        <label for="">First name</label>
                                        <input type="text" autofocus>
                                    </div>
                                    <div class="register_input">
                                        <label for="">Last name</label>
                                        <input type="text">
                                    </div>
                                </div>
                                <div class="register_form__content register_input">
                                    <label for="">Email</label>
                                    <input type="email">
                                </div>
                                <div class="register_form__content register_input">
                                    <label for="">Company Name</label>
                                    <input type="text">
                                </div>
                                <div class="register_form__content form_flex">
                                    <div class="register_input">
                                        <label for="">region</label>
                                        <input type="text">
                                    </div>
                                    <div class="register_input">
                                        <label for="">industry</label>
                                        <input type="text">
                                    </div>
                                </div>
                                <div class="continue_btn">
                                    <button class="global_btn">Register</button>
                                </div>

                            </form>
                            <div class="register">
                                <p>Already have an account? <a href="/index.html">Login</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>