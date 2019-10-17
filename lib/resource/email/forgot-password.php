<?php

	include_once 'header.php';

	$subject='Forgot Password';

	$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<h1 style='color: {$themeColor}'>{$subject}</h1>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'></p>
				</td>
			</tr>
			<tr>
				<td stye='text-align: center' >
					<h3>Looks like you lost your password?</h3>
					<p>We’re here to help. Click on the button below to change your password.</p>
				</td>
			</tr>
			<tr>
				<td style='float:left; width: 100%; text-align: center; margin-top: 20px; margin-bottom: 20px'>
					<a style='color:#ffffff; background-color: {$themeColor};  border: 10px solid {$themeColor}; border-radius: 3px; text-decoration:none;' href='{$passwordResetURL}'>Reset Your Password</a>
				</td>
			</tr>
			<tr>
				<td stye='text-align: center' >
					<p>If you did not ask to recover your password, please ignore this email.</p>
				</td>
			</tr>
    
	";

	include_once 'footer.php';

?>