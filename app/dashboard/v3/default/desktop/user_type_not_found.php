<?php
if (file_exists(__DIR__ . '/../../../../lib/configuration/directory.php')) {
    @include_once __DIR__ . '/../../../../lib/configuration/directory.php';
}

$siteName = defined('SITE_NAME') ? SITE_NAME : 'ISW Portal';
$siteLogo = (defined('SITE_LOGO') && SITE_LOGO != '') ? SITE_LOGO : '';
$siteThemeColor = (defined('SITE_THEME_COLOUR') && SITE_THEME_COLOUR != '') ? SITE_THEME_COLOUR : '#4f46e5';
$siteIcon = defined('SITE_ICON') ? SITE_ICON : '';
$displayName = defined('PROFILE_DISPLAY_NAME') ? PROFILE_DISPLAY_NAME : '';
$email = defined('PROFILE_EMAIL') ? PROFILE_EMAIL : '';

// Resolve administrator email
$siteEmail = '';
if (defined('SITE_EMAIL') && SITE_EMAIL != '') {
    $siteEmail = SITE_EMAIL;
} elseif (isset($site_email) && $site_email != '') {
    $siteEmail = $site_email;
} elseif (defined('CREDIT_MAIL') && CREDIT_MAIL != '') {
    $siteEmail = CREDIT_MAIL;
}

$mailSubject = "Access Approval Request - " . $siteName;
$mailBody = "Hi Administrator,\n\nI am requesting approval for my login account on " . $siteName . ".\n\nAccount Email: " . ($email ? $email : 'Not provided') . "\nAccount Name: " . ($displayName ? $displayName : 'Not provided') . "\n\nPlease grant approval or assign my required role.\n\nThank you!";
$mailtoUrl = "mailto:" . htmlspecialchars($siteEmail) . "?subject=" . rawurlencode($mailSubject) . "&body=" . rawurlencode($mailBody);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Approval Required | <?php echo htmlspecialchars($siteName); ?></title>
    <?php if ($siteIcon): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($siteIcon); ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/app/dashboard/v3/default/desktop/assets/css/admin.css">
</head>

<body class="access-pending-page">

    <div class="access-pending-container">
        <div class="access-pending-card">

            <div class="access-pending-logo">
                <?php if ($siteLogo): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="site-logo">
                <?php else: ?>
                    <div class="site-title"><?php echo htmlspecialchars($siteName); ?></div>
                <?php endif; ?>
            </div>

            <div class="access-pending-badge">
                <span class="access-pending-badge-dot"></span>
                <span>Approval Required</span>
            </div>

            <h1>Access Approval Pending</h1>

            <p class="message">
                Please ask your system administrator to share or approve your login request to access this panel.
            </p>

            <?php if ($displayName || $email): ?>
                <div class="access-pending-user-chip">
                    <div class="avatar">
                        <?php echo strtoupper(substr($displayName ? $displayName : ($email ? $email : 'U'), 0, 1)); ?>
                    </div>
                    <span class="user-text">
                        <?php echo htmlspecialchars($email ? $email : $displayName); ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="access-pending-actions">
                <a href="<?php echo $mailtoUrl; ?>" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Send Email to Administrator
                </a>
                <button type="button" class="btn btn-secondary" data-action="reload-page">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Check Status & Refresh
                </button>
                <a href="/logout/" class="btn btn-link">
                    Sign Out / Switch Account
                </a>
            </div>

        </div>
    </div>

    <script src="<?php echo PLUGIN_LIBRARY ?>jquery/jquery.min.js"></script>
    <script src="/app/dashboard/v3/default/desktop/assets/js/app.js"></script>
</body>

</html>