<?php

	include_once 'header.php';

	$subject='Welcome on Board';

	$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<h1 style='color: {$themeColor}'>{$subject}</h1>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque accumsan tempor laoreet. Quisque hendrerit nulla vitae purus commodo porta. Mauris eget eleifend nulla, sed pellentesque ante. Donec nec ipsum justo. Sed condimentum scelerisque mi eu rhoncus. Integer pellentesque ex ligula, at aliquam odio porttitor nec. Nam efficitur gravida magna, id sollicitudin lectus tristique a. Nunc sollicitudin purus commodo, pellentesque felis ac, ultricies massa. Pellentesque placerat condimentum venenatis. Pellentesque mollis turpis a porta vestibulum. Nam sed rhoncus est. Suspendisse eget tincidunt ipsum.</p>
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