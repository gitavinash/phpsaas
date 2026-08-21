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
    <style>
        :root {
            --brand-color: <?php echo htmlspecialchars($siteThemeColor); ?>;
            --bg-page: color-mix(in srgb, var(--brand-color) 4%, #f8fafc);
            --card-bg: #ffffff;
            --card-border: color-mix(in srgb, var(--brand-color) 12%, #e2e8f0);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --badge-bg: #fffbeb;
            --badge-border: #fef08a;
            --badge-text: #b45309;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            width: 100%;
            max-width: 420px;
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.05), 0 5px 15px -3px rgba(0, 0, 0, 0.02);
        }

        .logo-container {
            margin-bottom: 1.75rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .site-logo {
            max-height: 52px;
            max-width: 200px;
            object-fit: contain;
        }

        .site-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.01em;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            background-color: var(--badge-bg);
            border: 1px solid var(--badge-border);
            color: var(--badge-text);
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1.25rem;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #d97706;
        }

        h1 {
            font-size: 1.35rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 0.75rem;
            color: var(--text-main);
            letter-spacing: -0.01em;
        }

        p.message {
            font-size: 0.925rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        <?php if ($displayName || $email): ?>.user-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: color-mix(in srgb, var(--brand-color) 6%, #f1f5f9);
            border: 1px solid color-mix(in srgb, var(--brand-color) 12%, #e2e8f0);
            border-radius: 100px;
            padding: 5px 14px 5px 6px;
            margin-bottom: 1.75rem;
            max-width: 100%;
        }

        .avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background-color: var(--brand-color);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-text {
            font-size: 0.825rem;
            font-weight: 500;
            color: var(--text-main);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        <?php endif; ?>.actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 0.8rem 1.25rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background-color: var(--brand-color);
            color: #ffffff;
            box-shadow: 0 4px 12px color-mix(in srgb, var(--brand-color) 25%, transparent);
        }

        .btn-primary:hover {
            opacity: 0.93;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #f8fafc;
            color: var(--text-main);
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
        }

        .btn-link {
            background: transparent;
            color: var(--text-muted);
            font-size: 0.825rem;
            margin-top: 0.25rem;
        }

        .btn-link:hover {
            color: var(--text-main);
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card">

            <div class="logo-container">
                <?php if ($siteLogo): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="site-logo">
                <?php else: ?>
                    <div class="site-title"><?php echo htmlspecialchars($siteName); ?></div>
                <?php endif; ?>
            </div>

            <div class="status-badge">
                <span class="badge-dot"></span>
                <span>Approval Required</span>
            </div>

            <h1>Access Approval Pending</h1>

            <p class="message">
                Please ask your system administrator to share or approve your login request to access this panel.
            </p>

            <?php if ($displayName || $email): ?>
                <div class="user-chip">
                    <div class="avatar">
                        <?php echo strtoupper(substr($displayName ? $displayName : ($email ? $email : 'U'), 0, 1)); ?>
                    </div>
                    <span class="user-text">
                        <?php echo htmlspecialchars($email ? $email : $displayName); ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="actions">
                <a href="<?php echo $mailtoUrl; ?>" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Send Email to Administrator
                </a>
                <button type="button" class="btn btn-secondary" onclick="window.location.reload()">
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

</body>

</html>