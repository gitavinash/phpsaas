<?php

require_once MODULES . 'sso.php';

$defaultLoginRedirect = (defined('SITE_APP_ID') && (int) SITE_APP_ID === 3) ? '/manage-subscription/' : '/dashboard/';
$rawRedirect = $_GET['redirect_url'] ?? ($_GET['redirect'] ?? ($redirect_url ?? $defaultLoginRedirect));
$redirect_url = iswNormalizeRedirectPath($rawRedirect, $defaultLoginRedirect);

$currentSession = iswValidateCurrentSiteSession($connection, (int) SITE_ID);
if (!empty($currentSession['ok'])) {
    iswSetSiteAuthCookies($currentSession, (string) $currentSession['device_id'], 30);
    header("Location: {$redirect_url}");
    exit();
}

$sessionReason = (string) ($currentSession['reason'] ?? '');
if (in_array($sessionReason, ['disabled', 'no_access'], true)) {
    // Do not wipe shared-domain sibling cookies; only clear PHP in-memory flags so theme login views do not loop
    unset($_COOKIE['isLogin'], $_COOKIE['user_data'], $_COOKIE['device_id']);
    if (!isset($alertMsg) && !isset($_GET['alertMsg'])) {
        $alertMsg = ($sessionReason === 'disabled')
            ? 'Your workspace access has been deactivated.'
            : 'You do not have access to this workspace.';
    }
} else {
    if (isset($_COOKIE['isLogin']) || isset($_COOKIE['user_data']) || isset($_COOKIE['device_id'])) {
        iswClearAllAuthCookies(false);
    }

    if (
        ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET'
        && $sessionReason !== 'unverified_user'
        && !isset($_GET['sso_done'])
        && !isset($_GET['logout'])
        && !isset($_GET['alertMsg'])
        && !isset($_GET['msg'])
        && !isset($_GET['auth_method'])
    ) {
        $siblingCandidates = iswGetSiblingSitesWithActiveSessions($connection, (int) SITE_ID);
        if (!empty($siblingCandidates)) {
            header('Location: /sso/check/?redirect_url=' . urlencode($redirect_url), true, 302);
            exit();
        }
    }
}

// User selected theme login template

$themeLoginFile = USER_PATH . $urlStructure[0] . '.php';

if (!file_exists($themeLoginFile)) {

    if (SITE_APP == 'spa') {
        $authby = 'mobile';
    }

    if (DEVELOPMENT_MODE == true) {
        $fileName = 'login';
    } else {
        $fileName = ((isset($authby)) ? strtolower($authby) : 'email') . '-login';
    }

    $fileName = ((isset($authby)) ? strtolower($authby) : 'email') . '-login';

    $themeLoginFile = str_replace('/default/', '/' . USER_TYPE . '/', ADMIN_PATH) . $fileName . '.php';

    if (!file_exists($themeLoginFile)) {

        $themeLoginFile = ADMIN_PATH . $fileName . '.php';
    }

    if (!file_exists($themeLoginFile)) {

        // Default dashboard theme login template

        $themeLoginFile = DEFAULT_VISITOR_PATH . $fileName . '.php';
    }
}

require_once $themeLoginFile;

exit();
