<?php

	$subject='Booking Confirmation';
	
	include_once 'header.php';

	$noOfGuest=((isset($noOfGuest)) ? $noOfGuest : '' );
	$guestNumbers=(($guest==1) ? "with {$noOfGuest} guests" : "" );
	$transaction_time=((isset($transaction_time)) ? $transaction_time : '' );
	$transaction_currency=((isset($transaction_currency)) ? $transaction_currency : '' );
	$transaction_amount=((isset($transaction_amount)) ? $transaction_amount : '' );
	$transaction_payment_mode=((isset($transaction_payment_mode)) ? $transaction_payment_mode : '' );

	$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<h1 style='color: {$themeColor}'>{$subject}</h1>
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
							<td>Pangat booking type</td>
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