<?php

/**
 * Site Subscription Expiration Reminder Email Notification Template
 * Path: /lib/resource/email/site-subscription-reminder.php
 *
 * Variables expected:
 *  - $stage: '7_days_before' | '3_days_before' | 'same_day' | '1_day_after' | '7_days_after'
 *  - $siteNameText: Site display name (or domain)
 *  - $siteDomainText: Primary website domain
 *  - $expiryDateFormatted: e.g. "09 Nov 2026"
 *  - $daysRemaining: integer days until expiry (or negative if expired)
 *  - $ownerDisplayName: Name of site owner
 *  - $dashboardUrl: URL to renew in dashboard
 *  - $whatsappRenewUrl: One-click WhatsApp renewal support URL
 *  - $brandName: 'Aleya' or 'Blumox Technologies'
 *  - $brandLogo: URL of brand logo
 *  - $brandWebsite: 'https://aleya.in' or 'https://blumox.com'
 */

if (!defined('SITE_ID')) {
    define('SITE_ID', 0);
}

// Brand configuration
$brandNameSafe      = !empty($brandName) ? htmlspecialchars($brandName) : 'Blumox Technologies';
$isAleyaBrand       = (stripos($brandNameSafe, 'Aleya') !== false);
$brandLogoSafe      = !empty($brandLogo) ? $brandLogo : ($isAleyaBrand ? 'https://crm.blumox.com/upload/others/aleya-logo.png' : 'https://blumox.com/assets/images/logo.png');
$brandWebsiteSafe   = !empty($brandWebsite) ? $brandWebsite : ($isAleyaBrand ? 'https://aleya.in' : 'https://blumox.com');
$brandSupportEmail  = 'accounts@blumox.com';
$brandAccentColor   = $isAleyaBrand ? '#0284c7' : '#2563eb';

$siteDomainSafe     = htmlspecialchars($siteDomainText ?? '');
$siteNameSafe       = htmlspecialchars(!empty($siteNameText) ? $siteNameText : $siteDomainSafe);
$ownerNameSafe      = htmlspecialchars(!empty($ownerDisplayName) ? $ownerDisplayName : 'Site Owner');
$expiryFormatted    = htmlspecialchars($expiryDateFormatted ?? '');
$dashboardLink      = !empty($dashboardUrl) ? $dashboardUrl : "https://{$siteDomainSafe}/dashboard/";
$whatsappLink       = !empty($whatsappRenewUrl) ? $whatsappRenewUrl : ("https://api.whatsapp.com/send?phone=919990026008&text=" . urlencode("Hi, I want to renew my website subscription for {$siteDomainSafe}"));

// Milestone configurations (NO EMOJIS, clean corporate typography)
switch ($stage ?? '') {
    case '7_days_before':
        $themeColor      = '#2563eb'; // Royal Blue
        $badgeBg         = '#eff6ff';
        $badgeBorder     = '#bfdbfe';
        $badgeTextColor  = '#1e40af';
        $badgeText       = '7 Days Remaining';
        $emailHeading    = "Subscription Expiring in 7 Days";
        $subject         = "Reminder: Subscription for {$siteNameSafe} expires in 7 days";
        $messageIntro    = "Your website subscription for <strong>{$siteNameSafe}</strong> will expire on <strong>{$expiryFormatted}</strong> (in 7 days).";
        $actionAdvice    = "Please renew before the expiry date to ensure continuous website uptime and uninterrupted dashboard access.";
        $buttonText      = "Renew Subscription &rarr;";
        break;

    case '3_days_before':
        $themeColor      = '#d97706'; // Amber
        $badgeBg         = '#fffbeb';
        $badgeBorder     = '#fde68a';
        $badgeTextColor  = '#92400e';
        $badgeText       = 'Urgent: 3 Days Remaining';
        $emailHeading    = "Only 3 Days Left to Renew";
        $subject         = "Urgent: 3 days left to renew subscription for {$siteNameSafe}";
        $messageIntro    = "This is an important reminder that only <strong>3 days remain</strong> on your subscription for <strong>{$siteNameSafe}</strong> (expires on <strong>{$expiryFormatted}</strong>).";
        $actionAdvice    = "Complete your renewal today to avoid service disruptions or automatic account deactivation.";
        $buttonText      = "Renew Immediately &rarr;";
        break;

    case 'same_day':
        $themeColor      = '#dc2626'; // Red
        $badgeBg         = '#fef2f2';
        $badgeBorder     = '#fecaca';
        $badgeTextColor  = '#991b1b';
        $badgeText       = 'Action Required: Expires Today';
        $emailHeading    = "Your Subscription Expires Today";
        $subject         = "Action Required: Subscription for {$siteNameSafe} expires today";
        $messageIntro    = "Today (<strong>{$expiryFormatted}</strong>) is the final day of your active subscription cycle for <strong>{$siteNameSafe}</strong>.";
        $actionAdvice    = "Please complete your renewal today to prevent website suspension and preserve all active services.";
        $buttonText      = "Renew Subscription Today &rarr;";
        break;

    case '1_day_after':
        $themeColor      = '#e11d48'; // Rose
        $badgeBg         = '#fff1f2';
        $badgeBorder     = '#fecdd3';
        $badgeTextColor  = '#9f1239';
        $badgeText       = 'Notice: Subscription Expired';
        $emailHeading    = "Your Website Subscription Has Expired";
        $subject         = "Notice: Subscription for {$siteNameSafe} has expired";
        $messageIntro    = "Your subscription for <strong>{$siteNameSafe}</strong> expired yesterday on <strong>{$expiryFormatted}</strong>.";
        $actionAdvice    = "Your account has entered the grace period. Reactivate your subscription now to restore full access and prevent deactivation.";
        $buttonText      = "Renew & Reactivate &rarr;";
        break;

    case '7_days_after':
    default:
        $themeColor      = '#991b1b'; // Deep Dark Red
        $badgeBg         = '#fef2f2';
        $badgeBorder     = '#fca5a5';
        $badgeTextColor  = '#7f1d1d';
        $badgeText       = 'Final Suspension Warning';
        $emailHeading    = "Account Scheduled for Permanent Suspension";
        $subject         = "Final Notice: Subscription for {$siteNameSafe} scheduled for suspension";
        $messageIntro    = "Your subscription for <strong>{$siteNameSafe}</strong> expired 7 days ago on <strong>{$expiryFormatted}</strong>.";
        $actionAdvice    = "Your grace period has ended and the website is scheduled for suspension. Immediate renewal is required to protect your website data.";
        $buttonText      = "Reactivate Subscription &rarr;";
        break;
}

if (!defined('SITE_THEME_COLOUR')) {
    define('SITE_THEME_COLOUR', $themeColor);
}

include __DIR__ . '/header.php';

// Dynamically inject the exact Brand Logo & Brand styling into the email header bar
$brandLogoMarkup = "<a href='{$brandWebsiteSafe}' target='_blank' style='display: inline-block; padding: 14px 20px; text-decoration: none;'>
    <img src='{$brandLogoSafe}' alt='{$brandNameSafe}' style='max-height: 36px; height: 36px; width: auto; max-width: 200px; display: block; border: 0; outline: none;' />
</a>";

if (isset($email_header)) {
    $email_header = preg_replace(
        '/<td colspan=\'4\' valign=\'top\' class=\'image-section\'[^>]*>.*?<\/td>/s',
        "<td colspan='4' valign='top' class='image-section' style='padding: 10px 18px; background: #ffffff; border-bottom: 3px solid {$themeColor};'>{$brandLogoMarkup}</td>",
        $email_header
    );

    // Inject mobile responsive CSS for buttons
    $responsiveStyles = "
    <style type='text/css'>
        @media only screen and (max-width: 520px) {
            .btn-table-wrapper { width: 100% !important; }
            .btn-cell-responsive { display: block !important; width: 100% !important; padding: 0 0 12px 0 !important; }
            .btn-action-responsive { display: block !important; width: 100% !important; box-sizing: border-box !important; text-align: center !important; }
        }
    </style>
    ";
    $email_header = str_replace('</head>', $responsiveStyles . '</head>', $email_header);
}

$email_body = "
<table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='border-collapse: collapse; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; color: #1e293b;'>
    
    <!-- Status Badge & Heading -->
    <tr>
        <td style='padding: 8px 0 14px 0; text-align: left;'>
            <div style='display: inline-block; background-color: {$badgeBg}; color: {$badgeTextColor}; font-size: 11.5px; font-weight: 700; letter-spacing: 0.04em; padding: 5px 14px; border-radius: 20px; margin-bottom: 12px; border: 1px solid {$badgeBorder}; text-transform: uppercase;'>
                <span style='display: inline-block; width: 6px; height: 6px; border-radius: 50%; background-color: {$themeColor}; margin-right: 6px; vertical-align: 1px;'></span>{$badgeText}
            </div>
            <h1 style='margin: 0 0 10px 0; font-size: 22px; font-weight: 800; color: #0f172a; line-height: 1.3; letter-spacing: -0.02em;'>
                {$emailHeading}
            </h1>
            <p style='margin: 0 0 12px 0; font-size: 14.5px; color: #334155; line-height: 1.6;'>
                Hello <strong>{$ownerNameSafe}</strong>,
            </p>
            <p style='margin: 0 0 16px 0; font-size: 14px; color: #475569; line-height: 1.6;'>
                {$messageIntro}
            </p>
        </td>
    </tr>

    <!-- Details Card: ONLY Site Name, Domain, Expiry -->
    <tr>
        <td style='padding: 0 0 18px 0;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px;'>
                <tr>
                    <td style='padding: 8px 0; font-size: 13px; color: #64748b; width: 32%; font-weight: 600; border-bottom: 1px solid #edf2f7;'>Site Name</td>
                    <td style='padding: 8px 0; font-size: 14px; color: #0f172a; font-weight: 700; border-bottom: 1px solid #edf2f7;'>{$siteNameSafe}</td>
                </tr>
                <tr>
                    <td style='padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600; border-bottom: 1px solid #edf2f7;'>Domain</td>
                    <td style='padding: 8px 0; font-size: 14px; color: #0284c7; font-weight: 600; border-bottom: 1px solid #edf2f7;'>
                        <a href='https://{$siteDomainSafe}' target='_blank' style='color: #0284c7; text-decoration: none;'>{$siteDomainSafe}</a>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 8px 0 2px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Expiry</td>
                    <td style='padding: 8px 0 2px 0; font-size: 14px; color: {$themeColor}; font-weight: 800;'>{$expiryFormatted}</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Important Notice Box -->
    <tr>
        <td style='padding: 0 0 22px 0;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='background: {$badgeBg}; border: 1px solid {$badgeBorder}; border-radius: 10px; padding: 13px 16px;'>
                <tr>
                    <td style='font-size: 13px; color: {$badgeTextColor}; line-height: 1.55;'>
                        <strong>Important:</strong> {$actionAdvice}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Responsive Action Buttons with Gap (Desktop Side-by-Side | Mobile Full-Width Stacked) -->
    <tr>
        <td style='padding: 0 0 24px 0;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' class='btn-table-wrapper'>
                <tr>
                    <td align='center'>
                        <table role='presentation' cellpadding='0' cellspacing='0' class='btn-table-wrapper'>
                            <tr>
                                <td class='btn-cell-responsive' style='padding: 0 8px 0 0; text-align: center;'>
                                    <a href='{$dashboardLink}' target='_blank' class='btn-action-responsive' style='display: inline-block; background-color: {$themeColor}; color: #ffffff; padding: 13px 26px; font-size: 14px; font-weight: 700; text-decoration: none; border-radius: 8px; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif; white-space: nowrap;'>
                                        {$buttonText}
                                    </a>
                                </td>
                                <td class='btn-cell-responsive' style='padding: 0 0 0 8px; text-align: center;'>
                                    <a href='{$whatsappLink}' target='_blank' class='btn-action-responsive' style='display: inline-block; background-color: #25d366; color: #ffffff; padding: 13px 22px; font-size: 14px; font-weight: 700; text-decoration: none; border-radius: 8px; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif; white-space: nowrap;'>
                                        <img src='https://crm.blumox.com/upload/others/whatsapp-icon.png' alt='WhatsApp' width='16' height='16' style='vertical-align: -2px; margin-right: 6px; display: inline-block; border: 0;' />Renew via WhatsApp
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Billing Support Notice -->
    <tr>
        <td style='padding: 0 0 10px 0; border-top: 1px solid #f1f5f9; padding-top: 16px;'>
            <p style='margin: 0; font-size: 12.5px; color: #64748b; line-height: 1.6; text-align: center;'>
                Need assistance with payment, formal invoice, or bank transfer (NEFT/RTGS)?<br/>
                Reply directly to this email or write to <a href='mailto:{$brandSupportEmail}' style='color: {$themeColor}; font-weight: 700; text-decoration: none;'>{$brandSupportEmail}</a>.
            </p>
        </td>
    </tr>
</table>
";

$suppressAutoSend = true;
include __DIR__ . '/footer.php';
