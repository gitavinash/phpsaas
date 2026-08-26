<?php

$themeColor = defined('SITE_THEME_COLOUR') && !empty(SITE_THEME_COLOUR) ? SITE_THEME_COLOUR : (defined('SITE_THEME_COLOR') && !empty(SITE_THEME_COLOR) ? SITE_THEME_COLOR : '#2563eb');
$siteName = defined('SITE_NAME') && !empty(SITE_NAME) ? SITE_NAME : 'ISW CRM';
$siteUrl = defined('SITE_URL') ? SITE_URL : ('https://' . ($_SERVER['HTTP_HOST'] ?? ''));

$subject = 'Welcome to ' . $siteName . ' - Your Account Details';

$displayName = !empty($profile_display_name) ? htmlspecialchars($profile_display_name) : (!empty($firstName) ? htmlspecialchars($firstName) : 'Team Member');
$userEmail = !empty($email) ? htmlspecialchars($email) : '';
$userPass = !empty($password) ? htmlspecialchars($password) : (!empty($rawPassword) ? htmlspecialchars($rawPassword) : '(Default: Your Mobile Number)');
$userRoleText = !empty($user_role_title) ? htmlspecialchars($user_role_title) : (!empty($safeUserRole) ? htmlspecialchars(ucwords(str_replace('_', ' ', $safeUserRole))) : '');
$userMobileText = !empty($mobile) ? htmlspecialchars($mobile) : (!empty($safeMobile) ? htmlspecialchars($safeMobile) : '');

$actionURL = !empty($verifyURL) ? $verifyURL : (!empty($loginURL) ? $loginURL : ($siteUrl . '/login/'));
$actionButtonText = !empty($verifyURL) ? 'Verify & Access Account' : 'Log In to Dashboard';

include __DIR__ . '/header.php';

$email_body = "
<table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='border-collapse: collapse; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif;'>
    <tr>
        <td style='padding: 10px 0 20px 0; text-align: left;'>
            <div style='display: inline-block; background-color: {$themeColor}18; color: {$themeColor}; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 5px 14px; border-radius: 20px; margin-bottom: 12px; border: 1px solid {$themeColor}33;'>
                Welcome to the Team
            </div>
            <h1 style='margin: 0 0 10px 0; font-size: 22px; font-weight: 700; color: #0f172a; line-height: 1.3;'>
                Welcome aboard, {$displayName}!
            </h1>
            <p style='margin: 0 0 18px 0; font-size: 14.5px; color: #475569; line-height: 1.6;'>
                An account has been created for you on <strong>{$siteName}</strong>. You can now log in using the credentials below to access your dashboard and start collaborating.
            </p>
        </td>
    </tr>

    <!-- Credentials Card -->
    <tr>
        <td style='padding-bottom: 24px;'>
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 22px; border-collapse: separate;'>
                <tr>
                    <td colspan='2' style='padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;'>
                        <strong style='font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;'>
                            Account Access Credentials
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 10px 0 6px 0; width: 130px; font-size: 13.5px; color: #64748b;'>
                        <strong>Login Email:</strong>
                    </td>
                    <td style='padding: 10px 0 6px 0; font-size: 14px; color: #0f172a; font-weight: 600;'>
                        {$userEmail}
                    </td>
                </tr>";

if (!empty($userRoleText)) {
    $email_body .= "
                <tr>
                    <td style='padding: 6px 0; width: 130px; font-size: 13.5px; color: #64748b;'>
                        <strong>Assigned Role:</strong>
                    </td>
                    <td style='padding: 6px 0; font-size: 13.5px; color: #0f172a;'>
                        <span style='background-color: {$themeColor}18; color: {$themeColor}; border: 1px solid {$themeColor}33; padding: 3px 9px; border-radius: 4px; font-size: 12px; font-weight: 700;'>{$userRoleText}</span>
                    </td>
                </tr>";
}

if (!empty($userMobileText)) {
    $email_body .= "
                <tr>
                    <td style='padding: 6px 0 2px 0; width: 130px; font-size: 13.5px; color: #64748b;'>
                        <strong>Mobile:</strong>
                    </td>
                    <td style='padding: 6px 0 2px 0; font-size: 13.5px; color: #334155;'>
                        {$userMobileText}
                    </td>
                </tr>";
}

$email_body .= "
            </table>
        </td>
    </tr>

    <!-- CTA Button -->
    <tr>
        <td style='text-align: center; padding: 6px 0 24px 0;'>
            <table role='presentation' align='center' cellpadding='0' cellspacing='0' style='margin: 0 auto;'>
                <tr>
                    <td style='border-radius: 8px; background-color: {$themeColor}; text-align: center;'>
                        <a href='{$actionURL}' target='_blank' style='background-color: {$themeColor}; border: 12px solid {$themeColor}; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif; font-size: 14.5px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 8px; display: inline-block;'>
                            &nbsp;&nbsp;{$actionButtonText} &rarr;&nbsp;&nbsp;
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Security Tips -->
    <tr>
        <td style='padding: 14px 18px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; margin-bottom: 20px;'>
            <p style='margin: 0; font-size: 12px; color: #92400e; line-height: 1.5;'>
                <strong>&#128274; Security Tip:</strong> Please keep your login credentials confidential. We recommend changing your account password upon your first successful login.
            </p>
        </td>
    </tr>
</table>
";

include __DIR__ . '/footer.php';
