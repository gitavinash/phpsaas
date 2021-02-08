<?php

	include_once 'header.php';

	$subject="Your Order OD{$order_id} is confirmed | Vedic Vitals";

	$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>Hi {$profile_first_name}</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>Thanks for shopping with Vedic Vitals. Your order number OD{$order_id} has been successful received. Your order will reach you within next 5-7 days.</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>Know some facts about our delivery system.</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'><li> Your order is packed in 3 layer packaging, which makes it spill proof & secure.</li></p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'><li> Your order is dispatched within 24 hrs from the confirmation.</li></p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'><li> We try our best to deliver your product within 5-7 days.</li></p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'><li> In case of any miscommunication we make 2-3 attempts for the delivery.</li></p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'><li> You can always track your order at <a href='https://vedicvitals.ordr.live'>Click here to track your order</a></li></p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'><li> In case of any difficulty you can reach our customer support number - 9310142836 our customer support will help you track your order. </li><br/></p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>We request you to receive your order from the courier boy with a smile and offer them water as they are the real heroes who are serving us in this tough time.</p>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'></p>
				</td>
			</tr>
    
	";

	include_once 'footer.php';

?>