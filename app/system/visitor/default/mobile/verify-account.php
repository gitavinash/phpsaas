<?php

include_once 'header.php';

if (isset($_GET['email'])) {

	$email = mysqli_real_escape_string($connection, base64_decode($_GET['email']));
	if ($checkEmail = mysqli_query($connection, "SELECT user_email, user_status FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}'")) {

		if (mysqli_num_rows($checkEmail) != 0) {

			extract(mysqli_fetch_assoc($checkEmail));

			if ($user_status == 1) {
				$msg = "This email is already verified <a href='/login/'>Click here to login</a>";
			} else {
				$showError = TRUE;
			}
		} else {
			$msg = 'No email or user found';
		}
	} else {
		$msg = 'Something is wrong while trying to connect server';
	}
}

?>
<div class="form-box">
    <form id="verifyEmail">
        <?php

		if (isset($showError)) { ?>

        <div class="beforeClick">
            <h1>Verification <span>Please verify your email to access into your account</span></h1>
            <p>Verification email has been sent to your register email at the time of registration. </p>
            <input type="hidden" id="recaptcha_token" name="recaptcha_token">
            <input type="hidden" name="type" value="auth">
            <input type="hidden" name="action" value="emailVerification">
            <input type="hidden" name="email" value="<?php echo $email ?>">
            <input type="submit" id="resendVerificationMail" class="btn btn-block btn-success"
                value="Resend verification email">
        </div>
        <div class="afterClick">
            <p id='statusMsg'></p>
        </div>

        <?php
		} else {
			echo "<p>{$msg}</p>";
		}

		?>

    </form>
</div>
<?php

include_once 'footer.php';

?>