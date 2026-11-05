<?php

/**
 * Control Panel: Themes Management API Endpoint
 * Path: /api/v3/control_panel/themes.php
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
// 1. GET: List Themes (with App info & site counts)
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $appId = isset($_GET['app_id']) && is_numeric($_GET['app_id']) ? (int)$_GET['app_id'] : 0;
    $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
    $status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;

    $where = [];
    if ($appId > 0) {
        $where[] = "t.theme_app_id = {$appId}";
    }
    if (!empty($search)) {
        $where[] = "(t.theme_title LIKE '%{$search}%' OR t.theme_path LIKE '%{$search}%' OR a.app_title LIKE '%{$search}%')";
    }
    if ($status !== null) {
        $where[] = "t.theme_status = {$status}";
    }
    $whereSql = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

    $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';
    $orderBy = "t.theme_id DESC";
    if ($sort === 'oldest') {
        $orderBy = "t.theme_id ASC";
    } elseif ($sort === 'title_asc') {
        $orderBy = "t.theme_title ASC";
    } elseif ($sort === 'most_sites') {
        $orderBy = "sites_using_theme DESC, t.theme_id DESC";
    }

    $sql = "SELECT t.*, a.app_title, a.app_path,
            (SELECT COUNT(*) FROM {$tblPrefix}site s WHERE s.site_theme_id = t.theme_id) as sites_using_theme
            FROM {$tblPrefix}theme t
            LEFT JOIN {$tblPrefix}app a ON t.theme_app_id = a.app_id
            {$whereSql}
            ORDER BY {$orderBy}";

    $res = mysqli_query($connection, $sql);
    $themes = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $themes[] = [
                'id' => (int)$row['theme_id'],
                'app_id' => (int)$row['theme_app_id'],
                'app_title' => htmlspecialchars($row['app_title'] ?? 'Generic'),
                'app_path' => htmlspecialchars($row['app_path'] ?? ''),
                'title' => trim($row['theme_title']),
                'path' => trim($row['theme_path']),
                'description' => trim($row['theme_description'] ?? ''),
                'image' => trim($row['theme_image'] ?? ''),
                'version' => trim($row['theme_version'] ?? '1.0'),
                'status' => (int)$row['theme_status'],
                'sites_count' => (int)$row['sites_using_theme']
            ];
        }
    }

    // Global KPI Metrics for Themes
    $metrics = [
        'total' => 0,
        'active' => 0,
        'inactive' => 0,
        'apps' => 0,
        'sites' => 0
    ];
    $mQ = mysqli_query($connection, "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN theme_status = 1 THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN theme_status = 0 THEN 1 ELSE 0 END) as inactive,
        COUNT(DISTINCT theme_app_id) as apps
        FROM {$tblPrefix}theme");
    if ($mQ && $mR = mysqli_fetch_assoc($mQ)) {
        $metrics['total'] = (int)$mR['total'];
        $metrics['active'] = (int)$mR['active'];
        $metrics['inactive'] = (int)$mR['inactive'];
        $metrics['apps'] = (int)$mR['apps'];
    }
    $sQ = mysqli_query($connection, "SELECT COUNT(*) as total FROM {$tblPrefix}site WHERE site_theme_id > 0");
    if ($sQ && $sR = mysqli_fetch_assoc($sQ)) {
        $metrics['sites'] = (int)$sR['total'];
    }

    echo json_encode([
        'status' => 'success',
        'data' => $themes,
        'metrics' => $metrics,
        'total' => count($themes)
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Create or Update Theme
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $action = $payload['action'] ?? 'save';
    $themeId = isset($payload['theme_id']) ? (int)$payload['theme_id'] : 0;

    // Toggle Status Action
    if ($action === 'toggle_status') {
        if ($themeId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid Theme ID']);
            exit();
        }
        $newStatus = !empty($payload['status']) ? 1 : 0;
        $up = mysqli_query($connection, "UPDATE {$tblPrefix}theme SET theme_status = {$newStatus} WHERE theme_id = {$themeId}");
        if ($up) {
            echo json_encode(['status' => 'success', 'msg' => 'Theme status updated successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }

    $appId = isset($payload['theme_app_id']) ? (int)$payload['theme_app_id'] : 0;
    $title = trim($payload['theme_title'] ?? '');
    $path = trim($payload['theme_path'] ?? '');
    $desc = trim($payload['theme_description'] ?? '');
    $image = trim($payload['theme_image'] ?? '');
    $version = trim($payload['theme_version'] ?? '1.0');
    $status = isset($payload['theme_status']) ? (int)$payload['theme_status'] : 1;

    if ($appId <= 0 || empty($title) || empty($path)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'App ID, Theme Title, and Theme Path are required.']);
        exit();
    }

    $path = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $path));

    $escTitle = mysqli_real_escape_string($connection, $title);
    $escPath = mysqli_real_escape_string($connection, $path);
    $escDesc = mysqli_real_escape_string($connection, $desc);
    $escImage = mysqli_real_escape_string($connection, $image);
    $escVersion = mysqli_real_escape_string($connection, $version);

    if ($themeId > 0) {
        $query = "UPDATE {$tblPrefix}theme SET 
                    theme_app_id = {$appId},
                    theme_title = '{$escTitle}',
                    theme_path = '{$escPath}',
                    theme_description = '{$escDesc}',
                    theme_image = '{$escImage}',
                    theme_version = '{$escVersion}',
                    theme_status = {$status}
                  WHERE theme_id = {$themeId}";
        if (mysqli_query($connection, $query)) {
            echo json_encode(['status' => 'success', 'msg' => 'Theme updated successfully.', 'theme_id' => $themeId]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
    } else {
        $query = "INSERT INTO {$tblPrefix}theme (theme_app_id, theme_title, theme_path, theme_description, theme_image, theme_version, theme_status)
                  VALUES ({$appId}, '{$escTitle}', '{$escPath}', '{$escDesc}', '{$escImage}', '{$escVersion}', {$status})";
        if (mysqli_query($connection, $query)) {
            $newId = mysqli_insert_id($connection);
            echo json_encode(['status' => 'success', 'msg' => 'Theme registered successfully.', 'theme_id' => $newId]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
    }
    exit();
}
