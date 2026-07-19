<?php

include_once 'header.php';

$subject = 'Welcome to ' . SITE_NAME . ' - Company Profile Active';

$email_body = "
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%'>
			<tr>
				<td style='text-align: center'>
					<h1 style='color: {$themeColor}'>{$subject}</h1>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>Dear {$profile_display_name},</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>We are pleased to inform you that your company profile has been successfully registered on " . SITE_NAME . ". Your account is now active and ready for use.</p>
				</td>
			</tr>
			<tr>
				<td style='text-align: center'>
					<h3>Account Verification & Login</h3>
				</td>
			</tr>
			<tr>
				<td>
					<table style='width: 100%; max-width: 350px; margin: 0 auto; text-align: left;'>
						<tr>
							<td style='width: 120px; font-weight: bold;'>Registered Email:</td>
							<td>{$email}</td>
						</tr>
					</table>
					<p style='margin-top: 20px; font-size: 14px; line-height: 26px; text-align: center;'>To log in, please use your registered email address or mobile number. A secure One-Time Password (OTP) will be sent to complete your authentication.</p>
				</td>
			</tr>
		</table>
	";

include_once 'footer.php';
