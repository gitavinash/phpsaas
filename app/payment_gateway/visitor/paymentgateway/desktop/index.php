<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once DEFAULT_META ?>
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
</head>

<body>

    <section>
        <form>
            <div class="formGroup">
                <div class="logo">
                    <img src="<?php echo USER_IMAGES ?>logo.png" alt="">
                </div>
            </div>
            <div id="searchByMobile">
                <div class="formGroup">
                    <input type="number" name="mobile" id="mobile" placeholder="Mobile *" pattern="[0-9]*"
                        inputmode="numeric" autofocus required>
                    <label class="error" id="mobileNumberError">Please enter valid mobile no.</label>
                </div>
                <div class="formGroup btnBox">
                    <label></label>
                    <button id="searchUser" class="submitBtn">Continue</button>
                </div>
            </div>
            <div id="profileBox">
                <div class="formGroup">
                    <input type="text" name="name" id="name" placeholder="Full name *" required>
                    <label class="error" id="nameError">Please enter a valid name.</label>
                </div>
                <div class="formGroup">
                    <input type="number" id="resMobile" placeholder="Mobile *" pattern="[0-9]*" inputmode="numeric"
                        required>
                    <label class="error" id="mobileNumberError">Please enter valid mobile no.</label>
                </div>
                <div class="formGroup">
                    <input type="email" name="email" id="email" placeholder="Email *" required>
                    <label class="error" id="emailError">Please enter a valid email address.</label>
                </div>
                <div class="formGroup">
                    <input type="number" name="amount" id="amount" placeholder="Amount *" pattern="[0-9]*"
                        inputmode="numeric" required>
                    <label class="error" id="amountError">Please enter a valid amount only in number.</label>
                </div>
                <div class="formGroup">
                    <textarea name="message" id="message" placeholder="Any message"></textarea>
                </div>
                <div class="formGroup btnBox">
                    <label></label>
                    <button id="generatePayment" class="submitBtn">Pay Now</button>
                </div>
            </div>
        </form>
        <p class="credit">Powered by <a href="https://blumox.com" title="Payment solution by blumox" target="_bl
        ">blumox</a></p>
    </section>

</body>

<!-- sections end -->
<?php include_once NOTIFICATION . 'index.php' ?>

<script type="text/javascript" src="<?php echo JS_LIBRARY ?>bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo JS_LIBRARY ?>popper.min.js"></script>
<script type="text/javascript" src="<?php echo JS_LIBRARY ?>flickity.min.js"></script>
<script type="text/javascript" src="<?php echo USER_JS ?>app.min.js"></script>

</html>