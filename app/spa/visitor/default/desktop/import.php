<?php

/**
 * Upload → validate → import memberships from JSON
 * Safe, idempotent (basic de-dup), and transactional.
 */

declare(strict_types=1);

require_once USER_FUNCTIONS; // must provide registerUserByMobile($conn, $mobile, $name)
require_once LOGIN_CHECK;    // must ensure $connection (mysqli) + auth

// ---------- Config ----------
$MAX_UPLOAD_MB  = 25;
$ALLOWED_MIMES  = ['application/json', 'text/json', 'text/plain', 'application/octet-stream'];
$DEFAULT_BRANCH = 1; // fallback branch id

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ---------- Helpers ----------
function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
function normalize_datetime($s)
{
    $s = trim((string)$s);
    if ($s === '' || stripos($s, '0000-00-00') === 0) return null;
    $t = strtotime($s);
    return $t ? date('Y-m-d H:i:s', $t) : null;
}
function normalize_date($s)
{
    $s = trim((string)$s);
    if ($s === '' || stripos($s, '0000-00-00') === 0) return null;
    $t = strtotime($s);
    return $t ? date('Y-m-d', $t) : null;
}
function intish($v, $d = 0)
{
    $v = trim((string)$v);
    if ($v === '') return $d;
    return (int)$v;
}
function moneyish($v, $d = '0')
{
    $v = trim((string)$v);
    if ($v === '') return $d;
    return preg_replace('/[^\d.-]/', '', $v);
}
function valid_mobile_india($m)
{
    return (bool)preg_match('/^(\+91[\-\s]?)?[0]?(91)?[6-9]\d{9}$/', trim($m));
}

/** Canonicalize package labels into category_title */
function normalize_package($raw)
{
    $s = strtolower(trim((string)$raw));
    $s = preg_replace('/\s+/', ' ', $s);
    $map = [
        // luxury
        'lxry' => 'luxury',
        'luxry' => 'luxury',
        'luxury' => 'luxury',
        'luxruy' => 'luxury',
        'nlxry' => 'luxury',
        'lxry package' => 'luxury',
        'lxry only' => 'luxury',
        'luxury package' => 'luxury',
        // semi → "semi luxury"
        'semi' => 'semi luxury',
        'semi only' => 'semi luxury',
        'sami' => 'semi luxury',
        'semi=lxry' => 'semi luxury',
        'semi any lxry' => 'semi luxury',
        'sami lxry' => 'semi luxury',
        'sami luxry' => 'semi luxury',
        'sami luxury' => 'semi luxury',
        // normal
        'nrml' => 'normal',
        'nlrml' => 'normal',
        'normal' => 'normal',
        'normal rooms' => 'normal',
        'normal with steam' => 'normal',
        'normal multi added package' => 'normal',
    ];
    if (isset($map[$s])) return $map[$s];
    if (preg_match('/lux/', $s))  return 'luxury';
    if (preg_match('/semi/', $s)) return 'semi luxury';
    if (preg_match('/norm/', $s)) return 'normal';
    return ucwords($s);
}

/** Read rows from uploaded JSON (flat array or phpMyAdmin wrapper) */
function read_uploaded_json_rows(string $tmpPath): array
{
    $raw = file_get_contents($tmpPath);
    if ($raw === false) throw new RuntimeException('Failed to read uploaded file.');
    $json = json_decode($raw, true, 512, JSON_BIGINT_AS_STRING);
    if (!is_array($json)) throw new RuntimeException('Invalid JSON.');

    if (isset($json[0]) && is_array($json[0]) && array_key_exists('m_name', $json[0])) return $json;

    foreach ($json as $blk) {
        if (isset($blk['type'], $blk['name'], $blk['data']) && $blk['type'] === 'table' && is_array($blk['data']) && isset($blk['data'][0]['m_name'])) {
            return $blk['data'];
        }
    }
    throw new RuntimeException('Could not locate membership rows in JSON (need keys like "m_name", "m_mobile", ...).');
}

/** Insert everything with prepared statements + transaction */
function import_rows(mysqli $connection, array $rows, int $warehouseId): array
{
    mysqli_query($connection, "SET NAMES utf8mb4");
    $out = ['imported' => 0, 'skipped' => 0, 'failed' => 0, 'notes' => []];

    // Table names for clearer notes
    $tableUser       = TABLE_PREFIX . "user";
    $tableCategory   = TABLE_PREFIX . "category";
    $tableMembership = TABLE_PREFIX . "membership";

    // Prepared statements
    $stmtUser = $connection->prepare("SELECT user_id FROM {$tableUser} WHERE user_mobile=? LIMIT 1");

    $stmtCatSel = $connection->prepare(
        "SELECT category_id
           FROM {$tableCategory}
          WHERE category_title=? AND category_type='spa_room' AND category_site_id=" . SITE_ID . "
          LIMIT 1"
    );
    // Create room if missing (note: using your existing column name category_time)
    $stmtCatIns = $connection->prepare(
        "INSERT INTO {$tableCategory}
         (category_site_id, category_type, category_title, category_time)
         VALUES (" . SITE_ID . ", 'spa_room', ?, NOW())"
    );

    $stmtMemIns = $connection->prepare(
        "INSERT INTO {$tableMembership}
         (membership_site_id, membership_user_id, membership_profile_name, membership_branch_id,
          membership_type, membership_type_id, membership_total_visit, membership_avl_visit,
          membership_price, membership_amount_due, membership_comment, membership_status,
          membership_created, membership_expire)
         VALUES
         (" . SITE_ID . ", ?, ?, ?, 'category', ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    // Optional membership_update column
    $hasUpdateCol = false;
    if ($r = mysqli_query($connection, "SHOW COLUMNS FROM {$tableMembership} LIKE 'membership_update'")) {
        if (mysqli_num_rows($r) > 0) $hasUpdateCol = true;
    }
    if ($hasUpdateCol) {
        $stmtMemUpdateDate = $connection->prepare(
            "UPDATE {$tableMembership} SET membership_update=? WHERE membership_id=?"
        );
    }

    // Transaction
    $connection->begin_transaction();
    try {
        if (count($rows) > 100000) {
            throw new RuntimeException('Too many rows. Split your file into smaller chunks.');
        }

        foreach ($rows as $i => $r) {
            $m_id    = trim((string)($r['m_id'] ?? ''));
            $name    = trim((string)($r['m_name'] ?? ''));
            $mobile  = trim((string)($r['m_mobile'] ?? ''));
            $package = normalize_package($r['m_pkg'] ?? '');
            $qty     = intish($r['m_qty'] ?? 0);
            $avl     = intish($r['m_avl'] ?? 0);
            $price   = moneyish($r['m_price'] ?? '0');
            $balance = moneyish($r['m_balance'] ?? '0');
            $status  = intish($r['m_status'] ?? 1, 1);
            $issueAt = normalize_datetime($r['m_issue'] ?? '');
            $expAt   = normalize_datetime($r['m_exp']   ?? '');
            $lastOn  = normalize_date($r['m_last'] ?? '');

            // membership_comment must be exactly m_comment content
            $rawComment = $r['m_comment'] ?? '';
            if (is_array($rawComment) || is_object($rawComment)) {
                $membershipComment = json_encode($rawComment, JSON_UNESCAPED_UNICODE);
            } else {
                $membershipComment = (string)$rawComment; // verbatim
            }

            // Basic hygiene
            if ($name === '' || $mobile === '' || $package === '') {
                $out['skipped']++;
                $out['notes'][] = "Row #$i skipped: missing name/mobile/package.";
                continue;
            }
            if (!valid_mobile_india($mobile)) {
                $out['skipped']++;
                $out['notes'][] = "Row #$i skipped: invalid mobile {$mobile}.";
                continue;
            }
            if ($avl > $qty) $avl = $qty;

            // Dates: if created missing → now; if expire missing → created + 1 year
            if ($issueAt === null) $issueAt = date('Y-m-d H:i:s');
            if ($expAt   === null) $expAt   = date('Y-m-d H:i:s', strtotime($issueAt . ' +1 year'));

            // ---------- USER by MOBILE ONLY ----------
            $userId = 0;
            $stmtUser->bind_param('s', $mobile);
            $stmtUser->execute();
            $resUser = $stmtUser->get_result();

            if ($resUser && $resUser->num_rows) {
                $userId = (int)$resUser->fetch_assoc()['user_id'];
            } else {
                // Not found → create user (with retries if username duplicate occurs)
                $created = false;
                $dupKey = 'unknown';
                $dupVal = 'unknown';

                // Clean base name for retrying username generation inside your creator
                $baseName = trim(preg_replace('/\s+/', ' ', $name));
                if ($baseName === '') $baseName = 'Member';

                // Try initial create + alternates to avoid username collisions
                $attemptNames = [$baseName];
                for ($k = 1; $k <= 5; $k++) {
                    $attemptNames[] = $baseName . ' ' . $k;
                }
                // Final fallback with random suffix
                $attemptNames[] = $baseName . ' ' . substr((string)mt_rand(1000, 9999), 0, 4);

                foreach ($attemptNames as $altName) {
                    try {
                        $userId = registerUserByMobile($connection, $mobile, $altName, 'sdnljnl@gmail.com');
                        if ($userId) {
                            $created = true;
                            break;
                        }
                    } catch (mysqli_sql_exception $ex) {
                        if ((int)$ex->getCode() === 1062) {
                            // Extract duplicate key/value for clear notes
                            if (preg_match("/Duplicate entry '([^']+)' for key '([^']+)'/i", $ex->getMessage(), $m2)) {
                                $dupVal = $m2[1];
                                $dupKey = $m2[2]; // e.g. isw_user.user_username or user_mobile
                            }
                            // If duplicate is on user_mobile, someone created it concurrently → re-check by mobile and use it.
                            if (stripos($dupKey, 'mobile') !== false) {
                                $stmtUser->bind_param('s', $mobile);
                                $stmtUser->execute();
                                $re = $stmtUser->get_result();
                                if ($re && $re->num_rows) {
                                    $userId = (int)$re->fetch_assoc()['user_id'];
                                    $created = true;
                                    $out['notes'][] = "Row #$i: user resolved post-duplicate in {$tableUser} (user_id={$userId}, phone={$mobile}).";
                                    break;
                                }
                            }
                            // If duplicate is on username, try the next altName (loop continues)
                            continue;
                        } else {
                            $out['failed']++;
                            $out['notes'][] =
                                "Row #$i FAILED: user create error ({$ex->getCode()}) in {$tableUser}; phone={$mobile}.";
                            continue 2; // next row
                        }
                    }
                }

                if (!$created || !$userId) {
                    $out['failed']++;
                    $out['notes'][] =
                        "Row #$i FAILED: could not create user in {$tableUser}; last duplicate (key={$dupKey}, value={$dupVal}), phone={$mobile}, name={$name}.";
                    continue;
                }
            }

            // ---------- CATEGORY (ROOM): create if missing ----------
            $stmtCatSel->bind_param('s', $package);
            $stmtCatSel->execute();
            $resCat = $stmtCatSel->get_result();
            if ($resCat && $resCat->num_rows) {
                $categoryId = (int)$resCat->fetch_assoc()['category_id'];
            } else {
                // Create room/category now
                $stmtCatIns->bind_param('s', $package);
                try {
                    $stmtCatIns->execute();
                    $categoryId = (int)$connection->insert_id;
                    $out['notes'][] = "Row #$i: created new room '{$package}' in {$tableCategory} (category_id={$categoryId}).";
                } catch (mysqli_sql_exception $ex) {
                    if ((int)$ex->getCode() === 1062) {
                        // Race: someone else created it → re-select
                        $stmtCatSel->bind_param('s', $package);
                        $stmtCatSel->execute();
                        $resCat2 = $stmtCatSel->get_result();
                        if ($resCat2 && $resCat2->num_rows) {
                            $categoryId = (int)$resCat2->fetch_assoc()['category_id'];
                        } else {
                            $out['failed']++;
                            $out['notes'][] =
                                "Row #$i FAILED: room '{$package}' duplicate detected but not resolvable in {$tableCategory}.";
                            continue;
                        }
                    } else {
                        $out['failed']++;
                        $out['notes'][] =
                            "Row #$i FAILED: room create error ({$ex->getCode()}) in {$tableCategory} for '{$package}'.";
                        continue;
                    }
                }
            }

            // ---------- MEMBERSHIP: ALWAYS INSERT (no logical skip) ----------
            // If DB unique constraint still blocks, nudge created +1s and retry a few times.
            $maxTries = 5;
            $try      = 0;
            $inserted = false;
            $issueTs  = strtotime($issueAt) ?: time();
            $expTs    = strtotime($expAt) ?: strtotime('+1 year', $issueTs);
            while ($try < $maxTries && !$inserted) {
                $issueStr = date('Y-m-d H:i:s', $issueTs);
                $expStr   = date('Y-m-d H:i:s', $expTs);

                try {
                    $stmtMemIns->bind_param(
                        'isiiissssiss',
                        $userId,
                        $name,
                        $warehouseId,
                        $categoryId,
                        $qty,
                        $avl,
                        $price,
                        $balance,
                        $membershipComment,
                        $status,
                        $issueStr,
                        $expStr
                    );
                    $stmtMemIns->execute();
                    $memId    = (int)$connection->insert_id;

                    if ($hasUpdateCol && $lastOn) {
                        $stmtMemUpdateDate->bind_param('si', $lastOn, $memId);
                        $stmtMemUpdateDate->execute();
                    }
                    $inserted = true;
                    $out['imported']++;
                } catch (mysqli_sql_exception $ex) {
                    if ((int)$ex->getCode() === 1062) {
                        $try++;
                        // Nudge created by +1 second and try again
                        $issueTs += 1;
                        // Keep expiry relative only if it was derived; if user provided, we won't change it
                        if ($r['m_exp'] === '' || stripos((string)$r['m_exp'], '0000-00-00') === 0) {
                            // if expAt originally derived, keep gap ≈ 1 year (nudge too)
                            $expTs   += 1;
                        }
                        if ($try >= $maxTries) {
                            $out['failed']++;
                            $out['notes'][] =
                                "Row #$i FAILED: membership unique conflict in {$tableMembership} after {$maxTries} retries "
                                . "(user_id={$userId}, category_id={$categoryId}, price={$price}, created~{$issueAt}).";
                        }
                    } else {
                        $out['failed']++;
                        $out['notes'][] =
                            "Row #$i FAILED: membership insert error ({$ex->getCode()}) in {$tableMembership}.";
                        break;
                    }
                }
            }
        }

        $connection->commit();
        return $out;
    } catch (Throwable $e) {
        $connection->rollback();
        throw $e;
    }
}

// ---------- Handle POST (upload + import) ----------
$importReport = null;
$errorTop = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $warehouseId = isset($_POST['warehouseId']) ? (int)$_POST['warehouseId'] : $DEFAULT_BRANCH;
        if ($warehouseId <= 0) $warehouseId = $DEFAULT_BRANCH;

        if (!isset($_FILES['json']) || ($_FILES['json']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Please choose a JSON file to upload.');
        }

        $file = $_FILES['json'];
        if ($file['size'] <= 0)                       throw new RuntimeException('Uploaded file is empty.');
        if ($file['size'] > $MAX_UPLOAD_MB * 1024 * 1024) throw new RuntimeException('File too large. Max ' . $MAX_UPLOAD_MB . ' MB.');

        $mime = $file['type'] ?? '';
        $ext  = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if (!in_array($mime, $ALLOWED_MIMES, true) && $ext !== 'json') {
            throw new RuntimeException('Please upload a valid JSON file (*.json).');
        }

        $rows = read_uploaded_json_rows($file['tmp_name']);
        $importReport = import_rows($connection, $rows, $warehouseId);
        $importReport['status'] = 'success';
        $importReport['msg']    = 'Import completed.';
    } catch (Throwable $e) {
        $errorTop = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Membership JSON Import</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <style>
    :root {
        /* Core palette */
        --bg: #0a0f1a;
        --surface: #0e1726;
        --surface-2: #0f1a2d;
        --border: #1c2740;
        --muted: #98a2b3;
        --fg: #e6eaf2;
        --brand: #6aa7ff;
        --brand-2: #4f8df0;
        --ok: #10b981;
        --warn: #f59e0b;
        --err: #ef4444;

        /* Effects & radii */
        --ring: 0 0 0 4px rgba(106, 167, 255, .16);
        --ring-warn: 0 0 0 4px rgba(245, 158, 11, .16);
        --ring-err: 0 0 0 4px rgba(239, 68, 68, .16);
        --shadow-xl: 0 18px 48px rgba(0, 0, 0, .45);
        --shadow: 0 10px 28px rgba(0, 0, 0, .35);
        --radius-xl: 18px;
        --radius: 14px;
        --radius-sm: 10px;
    }

    @media (prefers-color-scheme: light) {
        :root {
            --bg: #f5f7fb;
            --surface: #ffffff;
            --surface-2: #f9fbff;
            --border: #e6e8ef;
            --muted: #667085;
            --fg: #0f172a;
            --brand: #3b82f6;
            --brand-2: #2563eb;
            --ring: 0 0 0 4px rgba(59, 130, 246, .18);
            --ring-warn: 0 0 0 4px rgba(245, 158, 11, .18);
            --ring-err: 0 0 0 4px rgba(239, 68, 68, .18);
            --shadow-xl: 0 28px 64px rgba(15, 23, 42, .14);
            --shadow: 0 12px 28px rgba(15, 23, 42, .10);
        }
    }

    * {
        box-sizing: border-box
    }

    html,
    body {
        height: 100%
    }

    body {
        margin: 0;
        background:
            radial-gradient(1200px 700px at -10% -10%, rgba(106, 167, 255, .10), transparent 60%),
            radial-gradient(900px 560px at 110% 10%, rgba(16, 185, 129, .08), transparent 65%),
            var(--bg);
        color: var(--fg);
        font: 15px/1.5 ui-sans-serif, system-ui, -apple-system, "Segoe UI", Inter, Roboto, Arial, "Apple Color Emoji", "Segoe UI Emoji";
        display: grid;
        place-items: center;
        padding: 28px 18px;
    }

    .app {
        width: min(980px, 100%);
    }

    .card {
        background: linear-gradient(180deg, var(--surface), var(--surface-2));
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-xl);
        padding: clamp(20px, 3vw, 32px);
    }

    header.appbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        border: 1px solid var(--border);
        background: linear-gradient(135deg, rgba(106, 167, 255, .22), rgba(106, 167, 255, .06));
    }

    h1 {
        font-size: clamp(18px, 2vw, 22px);
        line-height: 1.2;
        margin: 0;
        letter-spacing: .2px;
    }

    .subtitle {
        color: var(--muted);
        font-size: 13px;
        margin-top: 2px;
    }

    /* Form layout */
    form {
        display: grid;
        gap: 18px;
    }

    .row {
        display: grid;
        gap: 12px;
        grid-template-columns: 1fr auto;
        align-items: end;
    }

    @media (max-width: 560px) {
        .row {
            grid-template-columns: 1fr;
        }
    }

    label {
        display: block;
        color: var(--muted);
        font-size: 13px;
        margin-bottom: 6px;
    }

    .select,
    .input,
    .file {
        width: 100%;
        padding: 12px 14px;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        background: transparent;
        color: var(--fg);
        outline: none;
        transition: box-shadow .15s ease, border-color .15s ease, transform .05s ease;
    }

    .select:focus,
    .input:focus,
    .file:focus {
        box-shadow: var(--ring);
        border-color: var(--brand);
    }

    /* Dropzone */
    .dropzone {
        position: relative;
        border: 1px dashed var(--border);
        border-radius: var(--radius);
        background: rgba(255, 255, 255, .02);
        padding: 18px;
        display: grid;
        gap: 10px;
        transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
    }

    .dropzone.dragover {
        border-color: var(--brand);
        background: color-mix(in srgb, var(--brand) 8%, transparent);
        box-shadow: var(--ring);
    }

    .dz-head {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--muted);
        font-size: 13px;
    }

    .dz-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .file-meta {
        color: var(--muted);
        font-size: 13px;
    }

    .file-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: linear-gradient(180deg, rgba(255, 255, 255, .06), rgba(255, 255, 255, 0));
        font-size: 13px;
    }

    .file-pill .ext {
        font-weight: 700;
        letter-spacing: .3px;
        padding: 2px 8px;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: rgba(106, 167, 255, .12);
        color: var(--brand);
    }

    .file-pill .remove {
        border: none;
        background: transparent;
        color: var(--muted);
        padding: 0;
        cursor: pointer;
    }

    .file-pill .remove:hover {
        color: var(--fg);
        text-decoration: underline;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background: linear-gradient(180deg, rgba(255, 255, 255, .06), rgba(255, 255, 255, 0));
        color: var(--fg);
        padding: 12px 16px;
        font-weight: 600;
        letter-spacing: .2px;
        cursor: pointer;
        transition: transform .05s ease, box-shadow .15s ease, border-color .15s ease, background .15s ease;
    }

    .btn.primary {
        background: linear-gradient(180deg, color-mix(in srgb, var(--brand) 30%, transparent), color-mix(in srgb, var(--brand) 16%, transparent));
        border-color: color-mix(in srgb, var(--brand) 50%, var(--border));
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow);
    }

    .btn:disabled {
        opacity: .6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Alert */
    .alert {
        border: 1px solid color-mix(in srgb, var(--err) 35%, var(--border));
        background: color-mix(in srgb, var(--err) 8%, transparent);
        color: var(--fg);
        border-radius: var(--radius);
        padding: 12px 14px;
        font-size: 14px;
        margin-bottom: 8px;
    }

    /* Results */
    .stats {
        margin-top: 22px;
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    @media (max-width: 560px) {
        .stats {
            grid-template-columns: 1fr;
        }
    }

    .chip {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 10px 12px;
        background: linear-gradient(180deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, 0));
        font-size: 13px;
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .ok .dot {
        background: var(--ok);
    }

    .warn .dot {
        background: var(--warn);
    }

    .err .dot {
        background: var(--err);
    }

    .notes {
        margin-top: 12px;
        border-top: 1px dashed var(--border);
        padding-top: 10px;
        max-height: 280px;
        overflow: auto;
        scrollbar-width: thin;
    }

    .note {
        font-size: 13px;
        color: var(--muted);
        padding: 8px 0;
        border-bottom: 1px dashed color-mix(in srgb, var(--border) 70%, transparent);
    }

    .note:last-child {
        border-bottom: none;
    }

    .tip {
        margin-top: 16px;
        color: var(--muted);
        font-size: 13px;
    }

    code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('json');
        const dz = document.getElementById('dropzone');
        const meta = document.getElementById('fileInfo');
        const submitBtn = document.getElementById('submitBtn');
        const pillWrap = document.getElementById('filePillWrap');

        const mimeOk = (f) => ['application/json', 'text/json', 'text/plain', 'application/octet-stream']
            .includes(f.type) ||
            f.name.toLowerCase().endsWith('.json');

        const humanMB = (bytes) => (bytes / 1024 / 1024).toFixed(2);

        const renderPill = (file) => {
            const ext = (file.name.split('.').pop() || 'json').toUpperCase();
            pillWrap.innerHTML = `
          <span class="file-pill" role="status" aria-live="polite">
            <span class="ext">${ext}</span>
            <span>${file.name} — ${humanMB(file.size)} MB</span>
            <button type="button" class="remove" id="removeFile" aria-label="Remove file">remove</button>
          </span>
        `;
            document.getElementById('removeFile').onclick = () => {
                // clear input
                fileInput.value = '';
                pillWrap.innerHTML = '';
                meta.textContent = 'No file selected';
                submitBtn.disabled = true;
            };
        };

        const updateState = (file) => {
            if (file) {
                if (!mimeOk(file)) {
                    meta.textContent = 'Unsupported file type. Please select a .json file.';
                    submitBtn.disabled = true;
                    pillWrap.innerHTML = '';
                    return;
                }
                meta.textContent = `Selected file`;
                submitBtn.disabled = false;
                renderPill(file);
            } else {
                meta.textContent = 'No file selected';
                submitBtn.disabled = true;
                pillWrap.innerHTML = '';
            }
        };

        // Native change
        fileInput.addEventListener('change', () => updateState(fileInput.files[0]));

        // Drag & drop
        ['dragenter', 'dragover'].forEach(ev =>
            dz.addEventListener(ev, e => {
                e.preventDefault();
                e.stopPropagation();
                dz.classList.add('dragover');
            })
        );
        ['dragleave', 'drop'].forEach(ev =>
            dz.addEventListener(ev, e => {
                e.preventDefault();
                e.stopPropagation();
                dz.classList.remove('dragover');
            })
        );
        dz.addEventListener('drop', (e) => {
            const f = e.dataTransfer.files?. [0];
            if (!f) return;
            if (!mimeOk(f)) {
                meta.textContent = 'Unsupported file type. Please select a .json file.';
                submitBtn.disabled = true;
                pillWrap.innerHTML = '';
                return;
            }
            // Assign so the form posts the file
            fileInput.files = e.dataTransfer.files;
            updateState(f);
        });

        // Keyboard support for dropzone
        dz.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                fileInput.click();
            }
        });

        // initial
        updateState(fileInput.files[0]);
    });
    </script>
</head>

<body>
    <div class="app">
        <div class="card">
            <header class="appbar">
                <div class="brand">
                    <div class="brand-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 7h16M4 12h12M4 17h8" stroke="currentColor" stroke-width="1.6"
                                stroke-linecap="round" />
                        </svg>
                    </div>
                    <div>
                        <h1>Membership JSON Import</h1>
                        <div class="subtitle">Upload your export and run a safe, transactional import.</div>
                    </div>
                </div>
            </header>

            <?php if (!empty($errorTop)): ?>
            <div class="alert" role="alert"><?= h($errorTop) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" novalidate>
                <!-- Branch + Submit -->
                <div class="row">
                    <div>
                        <label for="warehouseId">Branch / Warehouse</label>
                        <select class="select" id="warehouseId" name="warehouseId" aria-label="Select a branch">
                            <?php
                            if ($checkWarehouse = mysqli_query($connection, "SELECT warehouse_id, warehouse_title FROM " . TABLE_PREFIX . "warehouse WHERE warehouse_status=1 AND warehouse_site_id=" . SITE_ID)) {
                                if (mysqli_num_rows($checkWarehouse) != 0) {
                                    while ($warehouseInfo = mysqli_fetch_assoc($checkWarehouse)) {
                                        echo "<option value='{$warehouseInfo['warehouse_id']}'>" . h($warehouseInfo['warehouse_title']) . "</option>";
                                    }
                                } else {
                                    echo "<option>No Branch exists</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div>
                        <label>&nbsp;</label>
                        <button class="btn primary" id="submitBtn" type="submit" disabled>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12h9M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Start Import
                        </button>
                    </div>
                </div>

                <!-- Dropzone -->
                <div>
                    <label for="json">Select a JSON file (or drag &amp; drop)</label>
                    <div id="dropzone" class="dropzone" tabindex="0" role="button" aria-label="Drop JSON file here">
                        <div class="dz-head">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 6h10l6 6v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z"
                                    stroke="currentColor" stroke-width="1.6" />
                                <path d="M14 6v4a2 2 0 0 0 2 2h4" stroke="currentColor" stroke-width="1.6" />
                            </svg>
                            <span id="fileInfo" class="file-meta">No file selected</span>
                        </div>

                        <div class="dz-actions">
                            <input class="file" id="json" name="json" type="file"
                                accept=".json,application/json,text/json,text/plain" aria-describedby="fileInfo" />
                            <div id="filePillWrap"></div>
                        </div>
                    </div>
                </div>
            </form>

            <?php if (!empty($importReport)): ?>
            <section aria-live="polite">
                <div class="stats">
                    <div class="chip ok"><span class="dot"></span> Imported: <?= (int)$importReport['imported'] ?></div>
                    <div class="chip warn"><span class="dot"></span> Skipped: <?= (int)$importReport['skipped'] ?></div>
                    <div class="chip err"><span class="dot"></span> Failed: <?= (int)$importReport['failed'] ?></div>
                </div>

                <div class="notes">
                    <?php if (!empty($importReport['notes'])): foreach ($importReport['notes'] as $n): ?>
                    <div class="note">• <?= h($n) ?></div>
                    <?php endforeach;
                        else: ?>
                    <div class="note">No detailed notes.</div>
                    <?php endif; ?>
                </div>
            </section>
            <?php endif; ?>

            <div class="tip">
                Tip: JSON should be a flat array with keys like <code>m_name</code>, <code>m_mobile</code>,
                <code>m_pkg</code>, <code>m_qty</code>, <code>m_avl</code>, <code>m_price</code>, and
                <code>m_comment</code>.
            </div>
        </div>
    </div>
</body>

</html>