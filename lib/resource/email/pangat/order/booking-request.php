<?php

	include_once 'header.php';

	$subject='Booking Request';

	$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<h1 style='color: {$themeColor}'>{$subject}</h1>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>We have received request for booking Pangat for {$bookingDate} {$bookingTime}. Please complete the payment to confirm.</p>
				</td>
			</tr>
    
	";

	include_once 'footer.php';