<?php

/**
 * Cross-Site SSO Endpoint Router (/sso/ping/, /sso/check/, /sso/issue/, /sso/consume/)
 */

require_once MODULES . 'sso.php';

if (!function_exists('iswSsoFinish')) {
    function iswSsoFinish($connection): void
    {
        if ($connection instanceof mysqli) {
            @mysqli_close($connection);
        }
        exit();
    }
}

if (!function_exists('iswSsoRedirect')) {
    function iswSsoRedirect($connection, string $url, int $code = 302): void
    {
        if (!headers_sent()) {
            header("Location: {$url}", true, $code);
        }
        iswSsoFinish($connection);
    }
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$ssoAction = strtolower(trim((string) ($urlStructure[1] ?? '')));

switch ($ssoAction) {
    case 'ping':
        if (!headers_sent()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');
        }
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
            http_response_code(204);
            iswSsoFinish($connection);
        }
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        http_response_code(200);
        echo json_encode([
            'ok'      => true,
            'site_id' => defined('SITE_ID') ? (int) SITE_ID : 0,
        ]);
        iswSsoFinish($connection);
        break;

    case 'check':
        $rawReturn = $_GET['redirect_url'] ?? ($_GET['return'] ?? '/dashboard/');
        $returnPath = iswNormalizeRedirectPath($rawReturn, '/dashboard/');
        $loginBase = '/login/?sso_done=1&redirect_url=' . urlencode($returnPath);

        $currentSession = iswValidateCurrentSiteSession($connection, (int) SITE_ID);
        if (!empty($currentSession['ok'])) {
            iswSetSiteAuthCookies($currentSession, (string) $currentSession['device_id']);
            iswSsoRedirect($connection, $returnPath);
        }
        if (($currentSession['reason'] ?? '') === 'unverified_user') {
            $encEmail = urlencode(base64_encode((string) ($currentSession['user']['user_email'] ?? '')));
            iswSsoRedirect($connection, "/verify-account/?email={$encEmail}");
        }
        if (in_array($currentSession['reason'] ?? '', ['disabled', 'no_access'], true)) {
            iswSsoRedirect($connection, $loginBase . '&alertMsg=' . urlencode('You do not have access to this workspace.'));
        }

        // Clear broken leftover auth cookies if session is invalid
        if (isset($_COOKIE['isLogin']) || isset($_COOKIE['user_data']) || isset($_COOKIE['device_id'])) {
            iswClearAllAuthCookies(false);
        }

        $candidates = iswGetSiblingSitesWithActiveSessions($connection, (int) SITE_ID);
        if (empty($candidates)) {
            iswSsoRedirect($connection, $loginBase);
        }

        // Phase 2: Client JS already probed which candidate hosts are reachable
        if (isset($_GET['hosts'])) {
            $rawHosts = trim((string) $_GET['hosts']);
            $requestedIds = [];
            if ($rawHosts !== '' && strtolower($rawHosts) !== 'none') {
                foreach (explode(',', $rawHosts) as $part) {
                    $hid = (int) trim($part);
                    if ($hid > 0) {
                        $requestedIds[$hid] = true;
                    }
                }
            }

            $reachable = [];
            foreach ($candidates as $cand) {
                $cid = (int) ($cand['site_id'] ?? 0);
                if (isset($requestedIds[$cid]) && !empty($cand['origin'])) {
                    $reachable[] = $cand;
                }
            }

            if (empty($reachable)) {
                iswSsoRedirect($connection, $loginBase);
            }

            $first = array_shift($reachable);
            $remainingIds = array_map(static function ($row) {
                return (int) $row['site_id'];
            }, $reachable);
            $issueUrl = iswBuildSsoIssueUrl((string) $first['origin'], (int) SITE_ID, $returnPath, $remainingIds);
            iswSsoRedirect($connection, $issueUrl);
        }

        // Phase 1: Parallel client-side reachability check (skips dead/local-unmapped domains cleanly)
        $probeTargets = [];
        foreach ($candidates as $cand) {
            if (!empty($cand['origin'])) {
                $probeTargets[] = [
                    'site_id' => (int) $cand['site_id'],
                    'origin'  => rtrim((string) $cand['origin'], '/'),
                ];
            }
        }
        if (empty($probeTargets)) {
            iswSsoRedirect($connection, $loginBase);
        }

        $probeJson = json_encode($probeTargets, JSON_UNESCAPED_SLASHES);
        $returnJson = json_encode($returnPath, JSON_UNESCAPED_SLASHES);
        $loginJson = json_encode($loginBase, JSON_UNESCAPED_SLASHES);
        $loginEsc = htmlspecialchars($loginBase, ENT_QUOTES, 'UTF-8');
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Checking workspace session...</title>
    <noscript><meta http-equiv="refresh" content="0;url=<?php echo $loginEsc; ?>"></noscript>
</head>
<body style="margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f8fafc;color:#334155;">
    <div style="text-align:center;padding:24px;">
        <div style="font-size:15px;font-weight:500;">Signing you in across connected workspaces...</div>
    </div>
    <script>
    (function () {
        var targets = <?php echo $probeJson; ?>;
        var returnPath = <?php echo $returnJson; ?>;
        var loginUrl = <?php echo $loginJson; ?>;
        var timeoutMs = 800;

        function probeOne(item) {
            return new Promise(function (resolve) {
                var done = false;
                var ctrl = typeof AbortController !== 'undefined' ? new AbortController() : null;
                var timer = setTimeout(function () {
                    if (done) return;
                    done = true;
                    if (ctrl) { try { ctrl.abort(); } catch (e) {} }
                    resolve(null);
                }, timeoutMs);

                var opts = { method: 'GET', mode: 'cors', credentials: 'omit', cache: 'no-store' };
                if (ctrl) { opts.signal = ctrl.signal; }

                fetch(item.origin + '/sso/ping/', opts)
                    .then(function (resp) { return resp.ok ? resp.json() : null; })
                    .then(function (data) {
                        if (done) return;
                        done = true;
                        clearTimeout(timer);
                        if (data && data.ok === true) {
                            resolve(item.site_id);
                        } else {
                            resolve(null);
                        }
                    })
                    .catch(function () {
                        if (done) return;
                        done = true;
                        clearTimeout(timer);
                        resolve(null);
                    });
            });
        }

        Promise.all(targets.map(probeOne)).then(function (results) {
            var reachable = results.filter(function (id) { return id !== null; });
            if (reachable.length > 0) {
                window.location.replace('/sso/check/?hosts=' + encodeURIComponent(reachable.join(',')) + '&redirect_url=' + encodeURIComponent(returnPath));
            } else {
                window.location.replace(loginUrl);
            }
        });
    })();
    </script>
</body>
</html>
        <?php
        iswSsoFinish($connection);
        break;

    case 'issue':
        $rawParams = [
            'target'    => (string) ($_GET['target'] ?? ''),
            'return'    => (string) ($_GET['return'] ?? '/dashboard/'),
            'remaining' => (string) ($_GET['remaining'] ?? ''),
            'ts'        => (string) ($_GET['ts'] ?? ''),
            'sig'       => (string) ($_GET['sig'] ?? ''),
        ];

        $groupInfo = iswGetParentGroupInfo($connection, (int) SITE_ID);
        $targetSiteId = (int) $rawParams['target'];
        $returnPath = iswNormalizeRedirectPath($rawParams['return'], '/dashboard/');

        if (
            !iswVerifySsoSignature($rawParams, 120)
            || $targetSiteId <= 0
            || $targetSiteId === (int) SITE_ID
            || !isset($groupInfo['sites'][$targetSiteId])
        ) {
            iswSsoRedirect($connection, '/login/?sso_done=1&alertMsg=' . urlencode('Invalid or expired single sign-on request.'));
        }

        $targetSiteRow = $groupInfo['sites'][$targetSiteId];
        $targetOrigin = rtrim((string) ($targetSiteRow['origin'] ?? ''), '/');
        if ($targetOrigin === '') {
            iswSsoRedirect($connection, '/login/?sso_done=1');
        }

        $remainingIds = [];
        if (trim($rawParams['remaining']) !== '') {
            foreach (explode(',', $rawParams['remaining']) as $part) {
                $rid = (int) trim($part);
                if ($rid > 0 && $rid !== (int) SITE_ID && $rid !== $targetSiteId && isset($groupInfo['siblings'][$rid])) {
                    $remainingIds[] = $rid;
                }
            }
            $remainingIds = array_values(array_unique($remainingIds));
        }

        $localSession = iswValidateCurrentSiteSession($connection, (int) SITE_ID);
        if (!empty($localSession['ok'])) {
            $userId = (int) $localSession['user_id'];
            $deviceId = (string) $localSession['device_id'];

            $targetAccess = iswResolveSiteAccessForUser($connection, $userId, $targetSiteId);
            if ($targetAccess && !empty($targetAccess['has_access'])) {
                $userStatus = (int) ($targetAccess['user']['user_status'] ?? 0);
                $targetAppId = (int) ($targetAccess['site']['site_app_id'] ?? 0);
                if ($userStatus !== 1 && $targetAppId !== 3) {
                    $encEmail = urlencode(base64_encode((string) ($targetAccess['user']['user_email'] ?? '')));
                    iswSsoRedirect($connection, "{$targetOrigin}/verify-account/?email={$encEmail}");
                }

                $token = iswCreateSsoToken(
                    $connection,
                    $userId,
                    (int) SITE_ID,
                    $targetSiteId,
                    (int) $groupInfo['parent_site_id'],
                    $deviceId,
                    60
                );
                if ($token !== '') {
                    $consumeUrl = "{$targetOrigin}/sso/consume/?token=" . urlencode($token) . '&return=' . urlencode($returnPath);
                    iswSsoRedirect($connection, $consumeUrl);
                }
            }

            // Authenticated on sibling, but lacks active access on target site: stop hopping cleanly
            $denyUrl = "{$targetOrigin}/login/?sso_done=1&redirect_url=" . urlencode($returnPath) . '&alertMsg=' . urlencode('Your account does not have access to this workspace.');
            iswSsoRedirect($connection, $denyUrl);
        }

        // Not logged in on this sibling: clear any stale local auth cookies and try next sibling
        if (isset($_COOKIE['isLogin']) || isset($_COOKIE['user_data']) || isset($_COOKIE['device_id'])) {
            iswClearAllAuthCookies(false);
        }

        if (!empty($remainingIds)) {
            $nextId = array_shift($remainingIds);
            $nextOrigin = rtrim((string) ($groupInfo['siblings'][$nextId]['origin'] ?? ''), '/');
            if ($nextOrigin !== '') {
                $nextUrl = iswBuildSsoIssueUrl($nextOrigin, $targetSiteId, $returnPath, $remainingIds);
                iswSsoRedirect($connection, $nextUrl);
            }
        }

        iswSsoRedirect($connection, "{$targetOrigin}/login/?sso_done=1&redirect_url=" . urlencode($returnPath));
        break;

    case 'consume':
        $rawToken = isset($_GET['token']) ? trim((string) $_GET['token']) : '';
        $returnPath = iswNormalizeRedirectPath($_GET['return'] ?? '/dashboard/', '/dashboard/');
        $loginFailBase = '/login/?sso_done=1&redirect_url=' . urlencode($returnPath);

        $tokenRow = iswConsumeSsoToken($connection, $rawToken, (int) SITE_ID);
        if (!$tokenRow) {
            iswSsoRedirect($connection, $loginFailBase . '&alertMsg=' . urlencode('Single sign-on session expired or already used. Please sign in.'));
        }

        $groupInfo = iswGetParentGroupInfo($connection, (int) SITE_ID);
        $sourceSiteId = (int) $tokenRow['sso_source_site_id'];
        if (!in_array($sourceSiteId, $groupInfo['site_ids'], true)) {
            iswSsoRedirect($connection, $loginFailBase . '&alertMsg=' . urlencode('Workspace group mismatch during single sign-on.'));
        }

        $userId = (int) $tokenRow['sso_user_id'];
        $deviceId = (string) $tokenRow['sso_device_id'];

        $tblPfx = iswTablePrefix();
        $groupSiteIdsSql = implode(',', array_map('intval', !empty($groupInfo['site_ids']) ? $groupInfo['site_ids'] : [$sourceSiteId, (int) SITE_ID]));
        $escDevConsume = mysqli_real_escape_string($connection, $deviceId);
        $activeSessChk = @mysqli_query(
            $connection,
            "SELECT us_id FROM {$tblPfx}user_sessions
             WHERE us_user_id = {$userId}
               AND us_active_device = '{$escDevConsume}'
               AND us_status = 1
               AND us_site_id IN ({$groupSiteIdsSql})
             LIMIT 1"
        );
        if (!$activeSessChk || mysqli_num_rows($activeSessChk) === 0) {
            iswSsoRedirect($connection, $loginFailBase . '&alertMsg=' . urlencode('Your workspace session was signed out. Please sign in again.'));
        }

        $resolved = iswResolveSiteAccessForUser($connection, $userId, (int) SITE_ID);
        if (!$resolved || empty($resolved['has_access'])) {
            iswSsoRedirect($connection, $loginFailBase . '&alertMsg=' . urlencode('Access denied for this workspace.'));
        }

        $userStatus = (int) ($resolved['user']['user_status'] ?? 0);
        $siteAppId = (int) ($resolved['site']['site_app_id'] ?? 0);
        if ($userStatus !== 1 && $siteAppId !== 3) {
            $encEmail = urlencode(base64_encode((string) ($resolved['user']['user_email'] ?? '')));
            iswSsoRedirect($connection, "/verify-account/?email={$encEmail}");
        }

        iswSyncGroupUserSessions($connection, $userId, $deviceId, (int) SITE_ID, 0);
        iswSetSiteAuthCookies($resolved, $deviceId, 30);

        iswSsoRedirect($connection, $returnPath);
        break;

    default:
        http_response_code(404);
        iswSsoRedirect($connection, '/login/');
        break;
}
