<?php

	include_once 'header.php';

	$subject="Order Received | Vedic Vitals";

	$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>Hi {$profile_first_name}</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>Thanks for taking interest in ancient science of Ayurveda while ordering with Vedic Vitals.</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>Your order for {$productName} {$order_product_qty} Qty is successfully received.</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'><strong>Silent Features About Your Product<strong></p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>👉 Your Product is 100% Ayurvedic.</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>👉 Consume it with confidence. It does not have any side effects.</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>👉 Your Product is approved by Govt of India (Ayush Ministry).</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>👉 Your product is produced at GMP certified facilitation.</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>👉 Your product is made with Love in India.</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>In case of any concerns/queries feel free to reach us at care@vedicvitals.com</p>
				</td>
			</tr>
    
    ";

	include_once 'footer.php';

?>