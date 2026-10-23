<?php

$themeColor = defined('SITE_THEME_COLOUR') && !empty(SITE_THEME_COLOUR) ? SITE_THEME_COLOUR : (defined('SITE_THEME_COLOR') ? SITE_THEME_COLOR : '#2563eb');
$siteName = defined('SITE_NAME') && !empty(SITE_NAME) ? SITE_NAME : 'ISW CRM';
$appName = defined('SITE_APP_NAME') && !empty(SITE_APP_NAME) ? ucwords(str_replace('_', ' ', SITE_APP_NAME)) : 'Aleya';
$creditName = defined('CREDIT') && !empty(CREDIT) ? CREDIT : 'Blumox Technologies';
$appUrl = 'https://aleya.in?utm_source=' . urlencode(strtolower(str_replace(' ', '_', $siteName))) . '&utm_medium=email';

if (defined('SITE_ADDRESS') && !empty(SITE_ADDRESS)) {
	$address = "<p style='margin: 4px 0; font-size: 12px; line-height: 18px; color: #64748b;'>" . SITE_ADDRESS . "</p>";
} else {
	$address = "";
}

$email_footer = "							</td>
					</tr>				
				</table>
			</td>
		</tr>
		" . ((SITE_ID != 49) ? "				
		<tr bgcolor='#fff'>
			<td valign='top' class='footer' style='border-collapse: collapse; border: 0; margin: 0; padding: 0; -webkit-text-size-adjust: none; color: #555559; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif; font-size: 16px; line-height: 26px; background: #ffffff; text-align: center; border-top: 4px solid {$themeColor};'>
				<table style='font-weight: normal; border-collapse: collapse; border: 0; margin: 0 auto; padding: 0; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif; width: 100%; max-width: 600px;'>
					<tr>
						<td class='inside-footer' align='center' valign='middle' style='border-collapse: collapse; border: 0; margin: 0; padding: 22px 20px; -webkit-text-size-adjust: none; color: #64748b; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif; font-size: 12px; line-height: 18px; vertical-align: middle; text-align: center; width: 100%; max-width: 580px;'>
							<div id='address' class='mktEditable' style='margin: 0; padding: 0;'>
								<b style='color: {$themeColor}; font-size: 14.5px; font-weight: 700;'>{$siteName}</b><br/>
								{$address}
								<div style='margin-top: 14px; padding-top: 12px; border-top: 1px solid #f1f5f9; font-size: 11.5px; color: #94a3b8; line-height: 1.6;'>
									Powered by <a href='{$appUrl}' target='_blank' style='color: {$themeColor}; font-weight: 700; text-decoration: none;'>{$appName}<sup>&trade;</sup></a> &bull; Developed with <span style='color: #ef4444; font-size: 12.5px;'>&hearts;</span> in India by <a href='https://blumox.com?utm_source=" . urlencode(strtolower(str_replace(' ', '_', $siteName))) . "&utm_medium=email_footer' target='_blank' style='color: #475569; font-weight: 600; text-decoration: none;'><strong>{$creditName}</strong></a>
								</div>
							</div>
						</td>
					</tr>	
				</table>
			</td>
		</tr>" : "") . "
	</table>
</td> 
</tr>
</table>
</body>
</html>
";

$email_body = $email_header . $email_body . $email_footer;

/* Send email */

if (!defined('SITE_APP_ID')) {
	define('SITE_APP_ID', 0);
}

if (!defined('SEND_EMAIL')) {
	$sendMailFile = dirname(__DIR__, 2) . '/configuration/mail/send-email.php';
	if (file_exists($sendMailFile)) {
		define('SEND_EMAIL', $sendMailFile);
	}
}

if (empty($toAddress) && !empty($email)) {
	$toAddress = $email; // Email receiver id
}

if (empty($suppressAutoSend) && defined('SEND_EMAIL') && file_exists(SEND_EMAIL)) {
	require SEND_EMAIL;
}
