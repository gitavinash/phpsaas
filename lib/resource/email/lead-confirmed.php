<?php

/**
 * V3 Executive Confirmed Sale Alert Email Notification Template
 * Path: /lib/resource/email/lead-confirmed.php
 */

$siteName = !empty($siteName) ? $siteName : (defined('SITE_NAME') && SITE_NAME !== 'CRM Platform' && !empty(SITE_NAME) ? SITE_NAME : 'Aleya Workspace');
if (!defined('SITE_NAME')) {
    define('SITE_NAME', $siteName);
}

$themeColor = '#10b981'; // Emerald Green for Confirmed Sale
$siteDomain = SITE_DOMAIN;

$customerNameText = ucwords(!empty($customerName) ? htmlspecialchars($customerName) : 'Valued Customer');
$customerMobileText = !empty($customerMobile) ? htmlspecialchars($customerMobile) : 'Not Provided';
$customerEmailText = !empty($customerEmail) ? htmlspecialchars($customerEmail) : 'Not Provided';
$leadSubjectText = !empty($leadSubject) ? htmlspecialchars($leadSubject) : 'Confirmed Tour Booking';
$assignedAgentText = ucwords(!empty($agentName) ? htmlspecialchars($agentName) : 'Unassigned');
$confirmedByText = ucwords(!empty($currentUserName) ? htmlspecialchars($currentUserName) : 'System');
$confirmedDateText = date('d M Y, h:i A');
$leadUrl = "https://{$siteDomain}/dashboard/leads/view/?id=" . base64_encode((string)$realLeadId);

$subject = "🎉 Lead Confirmed: {$customerNameText} (Lead #{$realLeadId}) - {$siteName}";

include __DIR__ . '/header.php';

$email_body = "
<table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='border-collapse: collapse; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif;'>
    <tr>
        <td style='padding: 10px 0 16px 0; text-align: left;'>
            <div style='display: inline-block; background-color: #ecfdf5; color: #059669; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 5px 14px; border-radius: 20px; margin-bottom: 12px; border: 1px solid #a7f3d0;'>
                ✓ Confirmed Sale Alert
            </div>
            <h1 style='margin: 0 0 10px 0; font-size: 22px; font-weight: 700; color: #0f172a; line-height: 1.3;'>
                New Lead Converted to Confirmed Sale
            </h1>
            <p style='margin: 0 0 16px 0; font-size: 14.5px; color: #475569; line-height: 1.6;'>
                Great news! A lead on <strong>{$siteName}</strong> has been marked as <strong>Confirmed Sale</strong>.
            </p>
        </td>
    </tr>

    <!-- Sale & Customer Info Table -->
    <tr>
        <td style='padding: 0 0 24px 0;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px;'>
                <tr>
                    <td style='padding: 8px 0; font-size: 13px; color: #64748b; width: 35%; font-weight: 600;'>Lead Reference</td>
                    <td style='padding: 8px 0; font-size: 14px; color: #0f172a; font-weight: 700;'>#{$realLeadId}</td>
                </tr>
                <tr>
                    <td style='padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Status</td>
                    <td style='padding: 8px 0;'>
                        <span style='background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 6px; border: 1px solid #bbf7d0;'>
                            ✓ Confirmed Sale
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Customer Name</td>
                    <td style='padding: 8px 0; font-size: 13px; color: #0f172a; font-weight: 700;'>{$customerNameText}</td>
                </tr>
                <tr>
                    <td style='padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Phone Number</td>
                    <td style='padding: 8px 0; font-size: 13px; color: #0f172a; font-weight: 600;'>{$customerMobileText}</td>
                </tr>
                <tr>
                    <td style='padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Email Address</td>
                    <td style='padding: 8px 0; font-size: 13px; color: #0f172a; font-weight: 600;'>{$customerEmailText}</td>
                </tr>
                <tr>
                    <td style='padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Requirement / Tour</td>
                    <td style='padding: 8px 0; font-size: 13px; color: #059669; font-weight: 700;'>{$leadSubjectText}</td>
                </tr>
                <tr>
                    <td style='padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Assigned Agent</td>
                    <td style='padding: 8px 0; font-size: 13px; color: #0f172a; font-weight: 600;'>{$assignedAgentText}</td>
                </tr>
                <tr>
                    <td style='padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Confirmed By</td>
                    <td style='padding: 8px 0; font-size: 13px; color: #0f172a; font-weight: 600;'>{$confirmedByText}</td>
                </tr>
                <tr>
                    <td style='padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600;'>Confirmed Date & Time</td>
                    <td style='padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 500;'>{$confirmedDateText}</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- CTA Button -->
    <tr>
        <td style='padding: 0 0 24px 0; text-align: center;'>
            <table role='presentation' align='center' cellpadding='0' cellspacing='0' style='margin: 0 auto;'>
                <tr>
                    <td style='border-radius: 8px; background: #10b981;'>
                        <a href='{$leadUrl}' target='_blank' style='display: inline-block; padding: 12px 28px; font-size: 14px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 8px;'>
                            View Confirmed Lead in Dashboard &rarr;
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Operational Note -->
    <tr>
        <td style='padding: 0 0 10px 0;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='background: #f0fdf4; border: 1px solid #dcfce7; border-radius: 8px; padding: 12px 16px;'>
                <tr>
                    <td style='font-size: 12.5px; color: #166534; line-height: 1.5;'>
                        💡 <strong>Next Steps:</strong> Verify booking advance payment and generate voucher/invoice if applicable.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
";

$suppressAutoSend = true;
include __DIR__ . '/footer.php';
