<?php

/**
 * Control Panel: Apps Management API Endpoint
 * Path: /api/v3/control_panel/apps.php
 */

header("Content-Type: application/json; charset=utf-8");

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. GET: List Apps with Themes and Sites counts
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
    $status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;

    $where = [];
    if (!empty($search)) {
        $where[] = "(a.app_title LIKE '%{$search}%' OR a.app_path LIKE '%{$search}%' OR a.app_description LIKE '%{$search}%')";
    }
    if ($status !== null) {
        $where[] = "a.app_status = {$status}";
    }
    $whereSql = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

    $sql = "SELECT a.*, 
            (SELECT COUNT(*) FROM {$tblPrefix}theme t WHERE t.theme_app_id = a.app_id) AS themes_count,
            (SELECT COUNT(*) FROM {$tblPrefix}site s WHERE s.site_app_id = a.app_id) AS sites_count
            FROM {$tblPrefix}app a
            {$whereSql}
            ORDER BY a.app_id DESC";

    $res = mysqli_query($connection, $sql);
    $apps = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $apps[] = [
                'id' => (int)$row['app_id'],
                'title' => trim($row['app_title']),
                'path' => trim($row['app_path']),
                'description' => trim($row['app_description'] ?? ''),
                'image' => trim($row['app_image'] ?? ''),
                'primary_color' => trim($row['app_primary_color'] ?? '#2798F5'),
                'secondary_color' => trim($row['app_secondary_color'] ?? '#ACCAE3'),
                'status' => (int)$row['app_status'],
                'visibility' => (int)$row['app_visiblity'],
                'launch_time' => $row['app_launch_time'],
                'themes_count' => (int)$row['themes_count'],
                'sites_count' => (int)$row['sites_count']
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $apps,
        'total' => count($apps)
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Create or Update App
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $action = $payload['action'] ?? 'save';
    $appId = isset($payload['app_id']) ? (int)$payload['app_id'] : 0;

    // Toggle Status Action
    if ($action === 'toggle_status') {
        if ($appId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid App ID']);
            exit();
        }
        $newStatus = !empty($payload['status']) ? 1 : 0;
        $up = mysqli_query($connection, "UPDATE {$tblPrefix}app SET app_status = {$newStatus} WHERE app_id = {$appId}");
        if ($up) {
            echo json_encode(['status' => 'success', 'msg' => 'App status updated successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }

    // Create or Update
    $title = trim($payload['app_title'] ?? '');
    $path = trim($payload['app_path'] ?? '');
    $desc = trim($payload['app_description'] ?? '');
    $image = trim($payload['app_image'] ?? '');
    $primaryColor = trim($payload['app_primary_color'] ?? '#2798F5');
    $secondaryColor = trim($payload['app_secondary_color'] ?? '#ACCAE3');
    $status = isset($payload['app_status']) ? (int)$payload['app_status'] : 1;
    $visibility = isset($payload['app_visiblity']) ? (int)$payload['app_visiblity'] : 1;

    if (empty($title) || empty($path)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'App Title and App Path (slug) are required.']);
        exit();
    }

    // Sanitize path (alphanumeric and underscores)
    $path = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $path));

    $escTitle = mysqli_real_escape_string($connection, $title);
    $escPath = mysqli_real_escape_string($connection, $path);
    $escDesc = mysqli_real_escape_string($connection, $desc);
    $escImage = mysqli_real_escape_string($connection, $image);
    $escPrimary = mysqli_real_escape_string($connection, $primaryColor);
    $escSecondary = mysqli_real_escape_string($connection, $secondaryColor);

    if ($appId > 0) {
        // Check uniqueness of path for other apps
        $chk = mysqli_query($connection, "SELECT app_id FROM {$tblPrefix}app WHERE app_path = '{$escPath}' AND app_id != {$appId}");
        if (mysqli_num_rows($chk) > 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => "An application with path '{$path}' already exists."]);
            exit();
        }

        $query = "UPDATE {$tblPrefix}app SET 
                    app_title = '{$escTitle}',
                    app_path = '{$escPath}',
                    app_description = '{$escDesc}',
                    app_image = '{$escImage}',
                    app_primary_color = '{$escPrimary}',
                    app_secondary_color = '{$escSecondary}',
                    app_status = {$status},
                    app_visiblity = {$visibility}
                  WHERE app_id = {$appId}";
        if (mysqli_query($connection, $query)) {
            echo json_encode(['status' => 'success', 'msg' => 'Application updated successfully.', 'app_id' => $appId]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
    } else {
        // Check uniqueness of path
        $chk = mysqli_query($connection, "SELECT app_id FROM {$tblPrefix}app WHERE app_path = '{$escPath}'");
        if (mysqli_num_rows($chk) > 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => "An application with path '{$path}' already exists."]);
            exit();
        }

        $query = "INSERT INTO {$tblPrefix}app (app_title, app_description, app_image, app_path, app_primary_color, app_secondary_color, app_status, app_visiblity)
                  VALUES ('{$escTitle}', '{$escDesc}', '{$escImage}', '{$escPath}', '{$escPrimary}', '{$escSecondary}', {$status}, {$visibility})";
        if (mysqli_query($connection, $query)) {
            $newId = mysqli_insert_id($connection);
            echo json_encode(['status' => 'success', 'msg' => 'Application registered successfully.', 'app_id' => $newId]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
    }
    exit();
}
