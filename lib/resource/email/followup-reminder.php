<?php

/**
 * V3 Executive Lead Follow-Up Reminder Email Notification Template
 * Path: /lib/resource/email/followup-reminder.php
 */

$themeColor = '#ea580c'; // Warm orange for follow-up alerts
$siteName = defined('SITE_NAME') && !empty(SITE_NAME) ? SITE_NAME : 'Aleya Workspace';
$siteDomain = defined('COMPANY_SITE_DOMAIN') ? COMPANY_SITE_DOMAIN : (defined('SITE_DOMAIN') ? SITE_DOMAIN : ($_SERVER['HTTP_HOST'] ?? 'aleya.in'));

$agentDisplayName = ucwords(!empty($agentName) ? htmlspecialchars($agentName) : 'Team Member');
$leadSubjectText = !empty($leadSubject) ? htmlspecialchars($leadSubject) : 'Customer Requirement';
$customerNameText = ucwords(!empty($customerName) ? htmlspecialchars($customerName) : 'Potential Customer');
$customerMobileText = !empty($customerMobile) ? htmlspecialchars($customerMobile) : 'Not Provided';
$followupTimeText = !empty($followupTimeFormatted) ? htmlspecialchars($followupTimeFormatted) : 'In 5 Minutes';
$followupReasonText = !empty($followupReason) ? htmlspecialchars($followupReason) : 'Scheduled Follow-Up';
$leadUrl = !empty($leadViewUrl) ? $leadViewUrl : "https://{$siteDomain}/dashboard/leads/view/?id=" . base64_encode((string)$realLeadId);

$subject = "🔔 Follow-Up Reminder: {$customerNameText} at {$followupTimeText} - {$siteName}";

include __DIR__ . '/header.php';

$email_body = "
<table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='border-collapse: collapse; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif;'>
    <tr>
        <td style='padding: 10px 0 16px 0; text-align: left;'>
            <div style='display: inline-block; background-color: #fff7ed; color: {$themeColor}; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 5px 14px; border-radius: 20px; margin-bottom: 12px; border: 1px solid #fed7aa;'>
                🔔 Follow-Up in 5 Minutes
            </div>
            <h1 style='margin: 0 0 10px 0; font-size: 22px; font-weight: 700; color: #0f172a; line-height: 1.3;'>
                Upcoming Follow-Up: {$customerNameText}
            </h1>
            <p style='margin: 0 0 16px 0; font-size: 14.5px; color: #475569; line-height: 1.6;'>
                Hello {$agentDisplayName},
            </p>
            <p style='margin: 0 0 20px 0; font-size: 14px; color: #475569; line-height: 1.6;'>
                This is a reminder that you have a follow-up scheduled in <strong>5 minutes</strong> for lead <strong>#{$realLeadId}</strong> on {$siteName}.
            </p>
        </td>
    </tr>

    <!-- Follow-Up Details Box -->
    <tr>
        <td style='padding: 0 0 24px 0;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px;'>
                <tr>
                    <td style='padding: 6px 0; font-size: 13px; color: #64748b; width: 35%; font-weight: 600;'>Scheduled Time</td>
                    <td style='padding: 6px 0; font-size: 13px; color: #ea580c; font-weight: 700;'>⏰ {$followupTimeText}</td>
                </tr>
                <tr>
                    <td style='padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Customer Name</td>
                    <td style='padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 700;'>{$customerNameText}</td>
                </tr>
                <tr>
                    <td style='padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Phone Number</td>
                    <td style='padding: 6px 0; font-size: 13px; color: #0284c7; font-weight: 700;'>{$customerMobileText}</td>
                </tr>
                <tr>
                    <td style='padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Follow-Up Purpose</td>
                    <td style='padding: 6px 0; font-size: 13px; color: #334155; font-weight: 600;'>{$followupReasonText}</td>
                </tr>
                <tr>
                    <td style='padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Lead Requirement</td>
                    <td style='padding: 6px 0; font-size: 13px; color: #475569; font-weight: 600;'>{$leadSubjectText}</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- CTA Button -->
    <tr>
        <td style='padding: 0 0 24px 0; text-align: center;'>
            <table role='presentation' align='center' cellpadding='0' cellspacing='0' style='margin: 0 auto;'>
                <tr>
                    <td style='border-radius: 8px; background: {$themeColor};'>
                        <a href='{$leadUrl}' target='_blank' style='display: inline-block; padding: 12px 28px; font-size: 14px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 8px;'>
                            Open Lead & Call Customer &rarr;
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Tip Box -->
    <tr>
        <td style='padding: 0 0 10px 0;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 12px 16px;'>
                <tr>
                    <td style='font-size: 12.5px; color: #92400e; line-height: 1.5;'>
                        💡 <strong>Action Required:</strong> Log call outcome or update follow-up notes in the Lead Activity tab after concluding your call.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
";

$suppressAutoSend = true;
include __DIR__ . '/footer.php';
