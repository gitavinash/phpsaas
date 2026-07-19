<?php

include_once 'header.php';

$subject = 'Welcome to ' . SITE_NAME . ' - Vendor Account Created';

$email_body = "
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%'>
			<tr>
				<td style='text-align: center'>
					<h1 style='color: {$themeColor}'>{$subject}</h1>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>Dear {$profile_display_name},</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>We are pleased to welcome you as a registered vendor on " . SITE_NAME . ". Your vendor account has been set up successfully.</p>
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
					<p style='margin-top: 20px; font-size: 14px; line-height: 26px; text-align: center;'>To access your vendor portal, please log in using your registered email address or mobile number. Authentication is completed securely via a One-Time Password (OTP) sent directly to you.</p>
				</td>
			</tr>
		</table>
	";

include_once 'footer.php';
