<?php

/**
 * V3 Executive Lead Assignment Email Notification Template
 * Path: /lib/resource/email/lead-assignment.php
 */

$themeColor = defined('SITE_THEME_COLOUR') && !empty(SITE_THEME_COLOUR) ? SITE_THEME_COLOUR : (defined('SITE_THEME_COLOR') && !empty(SITE_THEME_COLOR) ? SITE_THEME_COLOR : '#0284c7');
$siteName = defined('SITE_NAME') && !empty(SITE_NAME) ? SITE_NAME : 'Aleya Workspace';
$siteDomain = defined('COMPANY_SITE_DOMAIN') ? COMPANY_SITE_DOMAIN : (defined('SITE_DOMAIN') ? SITE_DOMAIN : ($_SERVER['HTTP_HOST'] ?? 'aleya.in'));

$agentDisplayName = ucwords(!empty($agentName) ? htmlspecialchars($agentName) : 'Team Member');
$leadSubjectText = !empty($leadSubject) ? htmlspecialchars($leadSubject) : 'Customer Enquiry';
$customerNameText = ucwords(!empty($customerName) ? htmlspecialchars($customerName) : 'Potential Customer');
$customerMobileText = !empty($customerMobile) ? htmlspecialchars($customerMobile) : 'Not Provided';
$customerEmailText = !empty($customerEmail) ? htmlspecialchars($customerEmail) : 'Not Provided';
$leadUrl = !empty($leadViewUrl) ? $leadViewUrl : "https://{$siteDomain}/dashboard/leads/view/?id=" . base64_encode((string)$realLeadId);

// $subject = "New Lead Assigned: {$customerNameText} ({$leadSubjectText}) - {$siteName}";
$subject = "New Lead Assigned - {$siteName}";

include __DIR__ . '/header.php';

$email_body = "
<table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='border-collapse: collapse; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif;'>
    <tr>
        <td style='padding: 10px 0 16px 0; text-align: left;'>
            <div style='display: inline-block; background-color: {$themeColor}15; color: {$themeColor}; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 5px 14px; border-radius: 20px; margin-bottom: 12px; border: 1px solid {$themeColor}30;'>
                Lead Assignment
            </div>
            <h1 style='margin: 0 0 10px 0; font-size: 22px; font-weight: 700; color: #0f172a; line-height: 1.3;'>
                New Lead Assigned to You
            </h1>
            <p style='margin: 0 0 16px 0; font-size: 14.5px; color: #475569; line-height: 1.6;'>
                Hello {$agentDisplayName},
            </p>
            <p style='margin: 0 0 20px 0; font-size: 14px; color: #475569; line-height: 1.6;'>
                A new customer enquiry on <strong>{$siteName}</strong> has been assigned to your workspace. Here are the enquiry details:
            </p>
        </td>
    </tr>

    <!-- Lead Info Table -->
    <tr>
        <td style='padding: 0 0 24px 0;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px;'>
                <tr>
                    <td style='padding: 6px 0; font-size: 13px; color: #64748b; width: 35%; font-weight: 600;'>Lead ID</td>
                    <td style='padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 700;'>#{$realLeadId}</td>
                </tr>
                <tr>
                    <td style='padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Customer Name</td>
                    <td style='padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 700;'>{$customerNameText}</td>
                </tr>
                <tr>
                    <td style='padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Phone Number</td>
                    <td style='padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600;'>{$customerMobileText}</td>
                </tr>
                <tr>
                    <td style='padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Email</td>
                    <td style='padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600;'>{$customerEmailText}</td>
                </tr>
                <tr>
                    <td style='padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Requirement / Subject</td>
                    <td style='padding: 6px 0; font-size: 13px; color: {$themeColor}; font-weight: 700;'>{$leadSubjectText}</td>
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
                            Open Lead in Workspace &rarr;
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Pro Tip Notice -->
    <tr>
        <td style='padding: 0 0 10px 0;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='background: #f0fdf4; border: 1px solid #dcfce7; border-radius: 8px; padding: 12px 16px;'>
                <tr>
                    <td style='font-size: 12.5px; color: #166534; line-height: 1.5;'>
                        ⚡ <strong>Tip:</strong> Contacting leads within 5 minutes increases conversion rates significantly. Please review and update lead status accordingly.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
";

$suppressAutoSend = true;
include __DIR__ . '/footer.php';
