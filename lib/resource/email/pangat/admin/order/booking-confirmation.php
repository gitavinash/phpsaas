<?php

	$subject='New Booking Confirmation';
	
	include_once 'header.php';

	$guestNumbers=(($guest==1) ? "with {$noOfGuest} guests" : "" );

	$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<h1 style='color: {$themeColor}'>{$subject}</h1>
					<p style='float:left; width: 100%; text-align: left; font-size: 14px; line-height: 26px'></p>
					<table style='padding: 20px 0px; text-align: left' >
						<tr>
							<td>Name</td>
							<td style='float: left; margin-left: 20px;' >{$enquiry_display_name}</td>
						</tr>
						<tr>
							<td>Mobile</td>
							<td style='float: left; margin-left: 20px;' >{$mobile}</td>
						</tr>
						<tr>
							<td>Email</td>
							<td style='float: left; margin-left: 20px;' >{$email}</td>
						</tr>
						<tr>
							<td>Pangat booking date</td>
							<td style='float: left; margin-left: 20px;' >".date('d M Y', strtotime($booking_date))."</td>
						</tr>
						<tr>
							<td>Pangat booking time</td>
							<td style='float: left; margin-left: 20px;' >{$booking_time}</td>
						</tr>
						<tr>
							<td>Guests</td>
							<td style='float: left; margin-left: 20px;' >{$guest}</td>
						</tr>
						<tr>
							<td>Booking date</td>
							<td style='float: left; margin-left: 20px;' >".date('d M Y', strtotime($transaction_time))."</td>
						</tr>
						<tr>
							<td>Payment</td>
							<td style='float: left; margin-left: 20px;' >{$transaction_currency} {$transaction_amount}</td>
						</tr>
						<tr>
							<td>Payment mode</td>
							<td style='float: left; margin-left: 20px;' >{$transaction_payment_mode}</td>
						</tr>
					</table>
				</td>
			</tr>
    
	";

	include_once 'footer.php';

?>