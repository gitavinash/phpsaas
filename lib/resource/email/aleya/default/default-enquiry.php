<?php

$enquiryUrl = SITE_URL . "/dashboard/leads/view/?id=" . base64_encode($leadId);


require 'header.php';

$email_body = "
	<table style='font-weight: normal; border-collapse: collapse; border: 0; margin: 0; padding: 0; font-family: Arial, sans-serif; width: 100%'>
		<tr>
			<td style='text-align: center'>
				<p style='float: left; width: 100%; text-align: left; font-size: 14px; line-height: 26px'>
					Hello <strong>" . SITE_NAME . " </strong>Team,
				</p>
			</td>
		</tr>
		<tr>
			<td style='text-align: center'>
				<p style='float: left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>
					You have received a new enquiry for the subject: <strong>{$subject}</strong>. Below are the details of the enquiry:
				</p>
			</td>
		</tr>
		<tr>
			<td style='text-align: center'>
				<table style='width: 100%; margin-top: 10px; border: 1px solid #ddd; font-size: 14px; line-height: 26px;'>
					<tr>
						<td style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Name:</td>
						<td style='padding: 8px; text-align: left; border: 1px solid #ddd;'>{$first_name} {$last_name}</td>
					</tr>
					<tr>
						<td style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Email:</td>
						<td style='padding: 8px; text-align: left; border: 1px solid #ddd;'>{$email}</td>
					</tr>
					<tr>
						<td style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Mobile:</td>
						<td style='padding: 8px; text-align: left; border: 1px solid #ddd;'>{$mobile}</td>
					</tr>
					<tr>
						<td style='padding: 8px; text-align: left; border: 1px solid #ddd;'>View enquiry:</td>
						<td style='padding: 8px; text-align: left; border: 1px solid #ddd;'><a href='{$enquiryUrl}'>Click here to view enquiry details</a></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style='text-align: center'>
				<p style='float: left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'>
					Please review the enquiry and respond as soon as possible. If you have any questions, feel free to reach out to us.
				</p>
			</td>
		</tr>
	</table>
";
require 'footer.php';
