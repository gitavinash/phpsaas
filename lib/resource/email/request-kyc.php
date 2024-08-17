<?php

include_once 'header.php';

$subject = "Complete Your KYC for " . SITE_NAME . " - Action Required";

$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<h1 style='color: {$themeColor}'>Complete your KYC</h1>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>Dear {$profile_display_name},</p>
                    <p>We hope this email finds you well.</p>
                    <p>To continue providing you with our services, we need to verify your identity as part of our Know Your Customer (KYC) process. Please take a few moments to complete your KYC by clicking the link below:</p>
                    <p><a href='" . SITE_URL . "kyc/?setup_id=" . base64_encode($user_email) . "'>Complete Your KYC Now</a></p>
                    <p>It's a quick and secure process. Simply upload the necessary documents, and you'll be all set!</p>
				</td>
			</tr>
			<tr>
				<td style='float:left; width: 100%; text-align: center; margin-top: 20px'>
					<a style='color:#ffffff; background-color: {$themeColor};  border: 10px solid {$themeColor}; border-radius: 3px; text-decoration:none;' href='{$verifyURL}'>Verify your Account</a>
				</td>
			</tr>
    
	";

include_once 'footer.php';