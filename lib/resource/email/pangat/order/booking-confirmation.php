<?php

	include_once 'header.php';

	$subject='Booking Confirmation';

	$guestNumbers=(($guest==1) ? "with {$noOfGuest} guests" : "" );

	$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<h1 style='color: {$themeColor}'>{$subject}</h1>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>You have successfully booked Pangat for {$bookingDate} {$bookingTime} {$guestNumbers}. Please call 011-23670506 in case of any issue.</p>
				</td>
			</tr>
    
	";

	include_once 'footer.php';

?>