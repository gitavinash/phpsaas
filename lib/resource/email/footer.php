<?php

if (defined('SITE_ADDRESS')) {
	$address = "<p style='margin: 5px 0; font-size: 12px; line-height: 18px; color: #555559;'>" . SITE_ADDRESS . "</p>";
} else {
	$address = "";
}

$email_footer = "							</td>
					</tr>				
				</table>
			</td>
		</tr>				
		<tr bgcolor='#fff'>
			<td valign='top' class='footer' style='border-collapse: collapse; border: 0; margin: 0; padding: 0; -webkit-text-size-adjust: none; color: #555559; font-family: Arial, sans-serif; font-size: 16px; line-height: 26px; background: #fff; text-align: center; border-top: 4px solid {$themeColor};'>
				<table style='font-weight: normal; border-collapse: collapse; border: 0; margin: 0 auto; padding: 0; font-family: Arial, sans-serif; width: 100%; max-width: 600px;'>
					<tr>
						<td class='inside-footer' align='center' valign='middle' style='border-collapse: collapse; border: 0; margin: 0; padding: 20px; -webkit-text-size-adjust: none; color: #555559; font-family: Arial, sans-serif; font-size: 12px; line-height: 16px; vertical-align: middle; text-align: center; width: 100%; max-width: 580px;'>
							<div id='address' class='mktEditable' style='margin: 0; padding: 0;'>
								<b style='color: {$themeColor}; font-size: 14px;'>" . SITE_NAME . "</b><br/>
								{$address}
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</td> 
</tr>
</table>
</body>
</html>
";

$email_body = $email_header . $email_body . $email_footer;

/* Send email */

$toAddress = $email; // Email receiver id

require_once SEND_EMAIL;
