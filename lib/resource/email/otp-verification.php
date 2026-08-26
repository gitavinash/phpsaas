<?php

/**
 * V3 Executive OTP Email Template
 * Path: /lib/resource/email/otp-verification.php
 */

$themeColor = defined('SITE_THEME_COLOUR') && !empty(SITE_THEME_COLOUR) ? SITE_THEME_COLOUR : (defined('SITE_THEME_COLOR') && !empty(SITE_THEME_COLOR) ? SITE_THEME_COLOR : '#4f46e5');
$siteName = defined('SITE_NAME') && !empty(SITE_NAME) ? SITE_NAME : 'ISW Workspace';
$siteUrl = defined('SITE_URL') ? SITE_URL : ('https://' . ($_SERVER['HTTP_HOST'] ?? ''));

$subject = "Your Login Verification Code: {$otpCode} - {$siteName}";
$recipientName = !empty($name) ? htmlspecialchars($name) : 'Team Member';
$displayOtp = !empty($otpCode) ? htmlspecialchars($otpCode) : '000000';

include __DIR__ . '/header.php';

$email_body = "
<table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='border-collapse: collapse; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif;'>
    <tr>
        <td style='padding: 10px 0 16px 0; text-align: left;'>
            <div style='display: inline-block; background-color: {$themeColor}15; color: {$themeColor}; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 5px 14px; border-radius: 20px; margin-bottom: 12px; border: 1px solid {$themeColor}30;'>
                Security Verification
            </div>
            <h1 style='margin: 0 0 10px 0; font-size: 22px; font-weight: 700; color: #0f172a; line-height: 1.3;'>
                One-Time Login Code
            </h1>
            <p style='margin: 0 0 16px 0; font-size: 14.5px; color: #475569; line-height: 1.6;'>
                Hello {$recipientName},
            </p>
            <p style='margin: 0 0 20px 0; font-size: 14px; color: #475569; line-height: 1.6;'>
                We received a request to access your <strong>{$siteName}</strong> account. Please use the 6-digit verification code below to complete your sign-in:
            </p>
        </td>
    </tr>

    <!-- OTP Display Box -->
    <tr>
        <td style='padding: 0 0 24px 0; text-align: center;'>
            <table role='presentation' align='center' cellpadding='0' cellspacing='0' style='margin: 0 auto; background: #f8fafc; border: 2px dashed {$themeColor}; border-radius: 12px; padding: 18px 36px;'>
                <tr>
                    <td style='text-align: center;'>
                        <span style='display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin-bottom: 6px;'>
                            Verification Code
                        </span>
                        <div style='font-size: 34px; font-weight: 800; letter-spacing: 12px; color: #0f172a; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; line-height: 1.2;'>
                            {$displayOtp}
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Expiry & Security Notice -->
    <tr>
        <td style='padding: 0 0 20px 0;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 12px 16px;'>
                <tr>
                    <td style='font-size: 13px; color: #92400e; line-height: 1.5;'>
                        ⏱️ <strong>Note:</strong> This verification code expires in <strong>10 minutes</strong>. Never share this code with anyone.
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style='padding: 0 0 10px 0;'>
            <p style='margin: 0; font-size: 12.5px; color: #94a3b8; line-height: 1.5;'>
                If you did not request this login code, you can safely ignore this email. Your account remains secure.
            </p>
        </td>
    </tr>
</table>
";

$suppressAutoSend = true;
include __DIR__ . '/footer.php';
