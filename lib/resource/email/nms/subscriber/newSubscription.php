<?php

include_once '../header.php';

$subject = "Welcome to " . SITE_NAME . " - Your Subscription is Active!";

$email_body = "
		
		<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
			<tr>
				<td style='text-align: center'>
					<p>We are thrilled to welcome you to the " . SITE_NAME . "! Your subscription has been successfully activated, and a world of insightful journalism and up-to-the-minute news updates awaits you.</p>
					<p><strong>Here are what you can look forward to:</strong></p>
					<ul>
						<li><strong>Exclusive Content:</strong> Dive into in-depth articles, expert analyses, and special reports that are reserved just for our subscribers.</li>
					</ul>
					<p><strong>Need Assistance?</strong></p>
					<p>Our support team is here for you. If you have any questions or need help navigating the site, please visit at  " . SITE_URL . "/contact-us/</p>
				</td>
			</tr>
    
	";

include_once '../footer.php';