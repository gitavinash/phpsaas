<?php

	include_once 'header.php';

	$subject='Verify your Email';

	$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<h1 style='color: {$themeColor}'>{$subject}</h1>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>Kindly verify your email id to continue the installation process.<p>
				</td>
			</tr>
			<tr>
				<td stye='text-align: center' >
					<h3>Account Credentials</h3>
				</td>
			</tr>
			<tr >
				<table>
					<tr>
						<td style='width: 100px'>Email</td>
						<td>{$email}</td>
					</tr>
					<tr>
						<td>Password</td>
						<td>{$password}</td>
					</tr>
				</table>
			</tr>
			<tr>
				<td style='float:left; width: 100%; text-align: center; margin-top: 20px'>
					<a style='color:#ffffff; background-color: {$themeColor};  border: 10px solid {$themeColor}; border-radius: 3px; text-decoration:none;' href='{$verifyURL}'>Verify your Account</a>
				</td>
			</tr>
    
	";

	include_once 'footer.php';

?>