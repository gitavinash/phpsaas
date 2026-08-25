<?php

include_once 'header.php';

$subject = "Complete Your KYC for " . (defined('SITE_NAME') ? SITE_NAME : 'Our Platform') . " - Action Required";
if (!isset($verifyURL) || empty($verifyURL)) {
    $verifyURL = (defined('SITE_URL') ? rtrim(SITE_URL, '/') : '') . "/kyc/?setup_id=" . base64_encode($user_email ?? ($email ?? ''));
}

$displayName = $profile_display_name ?? 'Team Member';
$companyName = defined('SITE_NAME') ? SITE_NAME : 'Our Platform';

$email_body = "
	<table style='font-weight: normal; border-collapse: collapse; border: 0; margin: 0 auto; padding: 0; font-family: Arial, sans-serif; width: 100%; max-width: 600px;'>
		<tr>
			<td style='text-align: left; padding: 24px; background: #ffffff;'>
				<h1 style='color: #4f46e5; font-size: 22px; margin-top: 0; margin-bottom: 16px;'>Complete Your KYC Verification</h1>
				<p style='font-size: 14px; line-height: 24px; color: #334155; margin-bottom: 14px;'>Dear <strong>{$displayName}</strong>,</p>
				<p style='font-size: 14px; line-height: 24px; color: #334155; margin-bottom: 14px;'>
					To continue providing you with workspace access and verify your account on <strong>{$companyName}</strong>, please complete your Know Your Customer (KYC) compliance verification.
				</p>
				<p style='font-size: 14px; line-height: 24px; color: #334155; margin-bottom: 24px;'>
					Please click the button below to upload your identity documents (Aadhaar Card, PAN Card, and Business Certificate):
				</p>

				<div style='text-align: center; margin: 28px 0;'>
					<a href='{$verifyURL}' style='background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);'>Complete Your KYC Now &rarr;</a>
				</div>

				<div style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-top: 24px;'>
					<p style='font-size: 12px; color: #64748b; margin: 0 0 6px 0;'>If the button above does not work, copy and paste this verification URL into your browser:</p>
					<a href='{$verifyURL}' style='font-size: 12px; color: #4f46e5; word-break: break-all;'>{$verifyURL}</a>
				</div>

				<p style='font-size: 13px; color: #94a3b8; margin-top: 24px; margin-bottom: 0;'>
					This is an automated security and compliance notification from {$companyName}.
				</p>
			</td>
		</tr>
	</table>
";

include_once 'footer.php';