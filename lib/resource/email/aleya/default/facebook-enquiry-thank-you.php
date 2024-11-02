<?php

$tourPackage = $subject;
$subject = "Confirmation of Your Enquiry for {$subject} | " . SITE_NAME;

include_once 'header.php';

$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<p style='float:left; width: 100%; text-align: left; font-size: 14px; line-height: 26px'>Dear <strong>{$first_name}</strong></p>
				</td>
			</tr>
            <tr>
				<td style='text-align: center'>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>
                        Thank you for showing your interest in the <strong>{$tourPackage}</strong> with <strong>{$companyName}</strong>. We have received your enquiry and are excited to assist you in planning your trip.
                    </p>
				</td>
            </tr>
            <tr>
				<td style='text-align: center'>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>
                        Our team is currently reviewing your request and will provide you with a personalized response shortly. If you need to update any details in your enquiry, please visit the following link: <a href='" . SITE_URL . "/update-enquiry/?leadId=" . base64_encode($leadId) . "'>Click here to update enquiry</a>.
                    </p>
				</td>
            </tr>
            <tr>
				<td style='text-align: center'>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>
                        In the meantime, if you have any questions or need further assistance, please do not hesitate to contact us. We are here to ensure that your trip is enjoyable and hassle-free.
                    </p>
				</td>
            </tr>
            <tr>
				<td style='text-align: center'>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>
                        Thank you once again for choosing {$companyName} We look forward to helping you create wonderful memories in your trip.
                    </p>
				</td>
            </tr>
            <tr>
				<td style='text-align: center'>
					<p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>
                    </p>
				</td>
            </tr>
    
    ";

$email_body;

include_once 'footer.php';
