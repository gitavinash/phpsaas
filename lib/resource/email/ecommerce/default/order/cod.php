<?php

	$subject="Order Received | ".SITE_NAME;
	
	include_once 'header.php';
	
	$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>Hi {$profile_first_name}</p>
				</td>
			</tr>
    
    ";

	include_once 'footer.php';

?>