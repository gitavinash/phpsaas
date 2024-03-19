<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- or -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div class="login_form">
        <div class="login__header">
            <a href="index.html"><img src="<?php echo USER_IMAGES ?>main_logo.png" class="img-fluid" alt=""></a>
        </div>
        <div class="login_content sign_up__content">
            <button><img src="<?php echo USER_IMAGES ?>google.png" class="img-fluid" alt=""> continue with
                google</button>
            <div class="or">
                <p>or</p>
            </div>
            <!-- <button>sign up using email</button> -->
            <div class="form_box">
                <form action="">
                    <div class="login_input">
                        <input type="text" placeholder="Enter Full Name" required>
                    </div>
                    <div class="login_input">
                        <input type="text" placeholder="Email" required>
                    </div>
                    <div class="login_input">
                        <input type="text" placeholder="Password" required>
                    </div>
                    <div class="sign--in_btn">
                        <button>Sign up</button>
                    </div>
                </form>

                <div class="form_bottom">
                    <p>By signing up for The Head And Tale, you agree to the <a href="/terms-and-conditions/"
                            target="_blank"> Terms &
                            Conditions</a>
                        and <a href="/privacy-policy/" target="_blank"> Privacy Policy</a></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>