<?php

header("Content-Type: application/json; charset=utf-8");

$action = $_REQUEST['action'] ?? 'toggle';
$ref_id = (int)($_REQUEST['ref_id'] ?? ($_REQUEST['news_id'] ?? 0));
$ref_type = trim((string)($_REQUEST['ref_type'] ?? ($_REQUEST['type'] ?? 'post')));
if (empty($ref_type)) {
    $ref_type = 'post';
}

$status = 200;
$site_id = defined('SITE_ID') ? (int)SITE_ID : 1;
$table_prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';

// Ensure connection & table exist
if (isset($connection) && $connection) {
    $ref_type = mysqli_real_escape_string($connection, $ref_type);

    // Auto-create & migrate bookmark table
    mysqli_query($connection, "CREATE TABLE IF NOT EXISTS {$table_prefix}bookmark (
        bookmark_id INT AUTO_INCREMENT PRIMARY KEY,
        bookmark_user_id INT NOT NULL,
        bookmark_ref_id INT NOT NULL,
        bookmark_ref_type VARCHAR(50) NOT NULL DEFAULT 'post',
        bookmark_site_id INT NOT NULL,
        bookmark_created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY user_ref_site (bookmark_user_id, bookmark_ref_id, bookmark_ref_type, bookmark_site_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $checkOldCol = mysqli_query($connection, "SHOW COLUMNS FROM {$table_prefix}bookmark LIKE 'bookmark_news_id'");
    if ($checkOldCol && mysqli_num_rows($checkOldCol) > 0) {
        mysqli_query($connection, "ALTER TABLE {$table_prefix}bookmark CHANGE bookmark_news_id bookmark_ref_id INT NOT NULL");
        mysqli_query($connection, "ALTER TABLE {$table_prefix}bookmark ADD COLUMN bookmark_ref_type VARCHAR(50) NOT NULL DEFAULT 'post' AFTER bookmark_ref_id");
    }
}

// Determine logged in user
$loggedInUserId = 0;
if (defined('USER_ID') && USER_ID > 0) {
    $loggedInUserId = (int)USER_ID;
} else if (!empty($_COOKIE['user_data']) && isset($connection) && $connection) {
    $user_salt_key = mysqli_real_escape_string($connection, base64_decode($_COOKIE['user_data']));
    $userRes = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$user_salt_key}' LIMIT 1");
    if ($userRes && mysqli_num_rows($userRes) > 0) {
        $userData = mysqli_fetch_assoc($userRes);
        $loggedInUserId = (int)$userData['user_id'];
    }
}

if ($action === 'check') {
    $isBookmarked = false;
    if ($loggedInUserId > 0 && $ref_id > 0 && isset($connection) && $connection) {
        $checkRes = mysqli_query($connection, "SELECT bookmark_id FROM {$table_prefix}bookmark WHERE bookmark_user_id = {$loggedInUserId} AND bookmark_ref_id = {$ref_id} AND bookmark_ref_type = '{$ref_type}' AND bookmark_site_id = {$site_id} LIMIT 1");
        if ($checkRes && mysqli_num_rows($checkRes) > 0) {
            $isBookmarked = true;
        }
    }
    echo json_encode([
        'status' => 200,
        'loggedIn' => ($loggedInUserId > 0),
        'bookmarked' => $isBookmarked
    ]);
    exit;
}

if ($loggedInUserId === 0) {
    echo json_encode([
        'status' => 401,
        'msg' => 'Please log in to save articles to your account.',
        'redirect' => '/login/?redirect_url=' . urlencode($_SERVER['HTTP_REFERER'] ?? '/saved/')
    ]);
    exit;
}

if ($action === 'toggle') {
    if ($ref_id <= 0) {
        echo json_encode(['status' => 400, 'msg' => 'Invalid reference ID']);
        exit;
    }

    $checkRes = mysqli_query($connection, "SELECT bookmark_id FROM {$table_prefix}bookmark WHERE bookmark_user_id = {$loggedInUserId} AND bookmark_ref_id = {$ref_id} AND bookmark_ref_type = '{$ref_type}' AND bookmark_site_id = {$site_id} LIMIT 1");

    if ($checkRes && mysqli_num_rows($checkRes) > 0) {
        // Unbookmark
        mysqli_query($connection, "DELETE FROM {$table_prefix}bookmark WHERE bookmark_user_id = {$loggedInUserId} AND bookmark_ref_id = {$ref_id} AND bookmark_ref_type = '{$ref_type}' AND bookmark_site_id = {$site_id}");
        echo json_encode([
            'status' => 200,
            'bookmarked' => false,
            'msg' => 'Removed from saved items'
        ]);
    } else {
        // Bookmark
        mysqli_query($connection, "INSERT INTO {$table_prefix}bookmark (bookmark_user_id, bookmark_ref_id, bookmark_ref_type, bookmark_site_id) VALUES ({$loggedInUserId}, {$ref_id}, '{$ref_type}', {$site_id})");
        echo json_encode([
            'status' => 200,
            'bookmarked' => true,
            'msg' => 'Item saved successfully'
        ]);
    }
    exit;
}

if ($action === 'remove') {
    if ($ref_id > 0) {
        mysqli_query($connection, "DELETE FROM {$table_prefix}bookmark WHERE bookmark_user_id = {$loggedInUserId} AND bookmark_ref_id = {$ref_id} AND bookmark_ref_type = '{$ref_type}' AND bookmark_site_id = {$site_id}");
    }
    echo json_encode([
        'status' => 200,
        'msg' => 'Item removed from bookmarks'
    ]);
    exit;
}

echo json_encode(['status' => 400, 'msg' => 'Invalid action']);
exit;

?>
