<?php

/**
 * Control Panel: System & Site Configurations API Endpoint
 * Path: /api/v3/control_panel/configurations.php
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
$controlPanelSiteId = defined('SITE_ID') ? (int)SITE_ID : 1;
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. GET: List Configurations
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $scope = isset($_GET['scope']) ? strtolower(trim($_GET['scope'])) : 'all';
    $siteIdParam = isset($_GET['site_id']) ? trim($_GET['site_id']) : '';
    $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';

    $where = [];
    if ($scope === 'global') {
        $where[] = "c.site_configuration_global = 1";
    } elseif ($scope === 'site') {
        $where[] = "c.site_configuration_global = 0";
        if ($siteIdParam !== '' && $siteIdParam !== 'all' && is_numeric($siteIdParam)) {
            $siteId = (int)$siteIdParam;
            $where[] = "c.site_configuration_site_id = {$siteId}";
        }
    } else {
        // 'all'
        if ($siteIdParam !== '' && $siteIdParam !== 'all' && is_numeric($siteIdParam)) {
            $siteId = (int)$siteIdParam;
            $where[] = "(c.site_configuration_site_id = {$siteId} OR c.site_configuration_global = 1)";
        }
    }

    if (!empty($search)) {
        $where[] = "(c.site_configuration_key LIKE '%{$search}%' OR c.site_configuration_value LIKE '%{$search}%')";
    }
    $whereSql = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

    $sql = "SELECT c.*, s.site_name, s.site_domain 
            FROM {$tblPrefix}site_configuration c
            LEFT JOIN {$tblPrefix}site s ON s.site_id = c.site_configuration_site_id
            {$whereSql}
            ORDER BY c.site_configuration_global DESC, c.site_configuration_key ASC";

    $res = mysqli_query($connection, $sql);
    $configs = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $configs[] = [
                'id' => (int)$row['site_configuration_id'],
                'site_id' => (int)$row['site_configuration_site_id'],
                'site_name' => htmlspecialchars($row['site_name'] ?? 'Global Infra'),
                'site_domain' => htmlspecialchars($row['site_domain'] ?? ''),
                'key' => htmlspecialchars($row['site_configuration_key']),
                'raw_key' => $row['site_configuration_key'],
                'value' => htmlspecialchars($row['site_configuration_value'] ?? ''),
                'raw_value' => $row['site_configuration_value'] ?? '',
                'status' => (int)$row['site_configuration_status'],
                'global' => (int)$row['site_configuration_global'],
                'updated_at' => $row['site_configuration_update']
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $configs,
        'total' => count($configs)
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Save, Delete, Raw Code Bulk Save, or Toggle Configuration
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $action = $payload['action'] ?? 'save';
    $configId = isset($payload['config_id']) ? (int)$payload['config_id'] : 0;

    // Delete
    if ($action === 'delete') {
        if ($configId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid Configuration ID']);
            exit();
        }
        $del = mysqli_query($connection, "DELETE FROM {$tblPrefix}site_configuration WHERE site_configuration_id = {$configId}");
        if ($del) {
            echo json_encode(['status' => 'success', 'msg' => 'Configuration deleted successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }

    // Toggle Status
    if ($action === 'toggle_status') {
        if ($configId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid Configuration ID']);
            exit();
        }
        $newStatus = !empty($payload['status']) ? 1 : 0;
        $up = mysqli_query($connection, "UPDATE {$tblPrefix}site_configuration SET site_configuration_status = {$newStatus} WHERE site_configuration_id = {$configId}");
        if ($up) {
            echo json_encode(['status' => 'success', 'msg' => 'Status updated.', 'new_status' => $newStatus]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }

    // Direct Value Update (Inline)
    if ($action === 'update_value') {
        if ($configId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid Configuration ID']);
            exit();
        }
        $newVal = mysqli_real_escape_string($connection, $payload['value'] ?? '');
        $up = mysqli_query($connection, "UPDATE {$tblPrefix}site_configuration SET site_configuration_value = '{$newVal}' WHERE site_configuration_id = {$configId}");
        if ($up) {
            echo json_encode(['status' => 'success', 'msg' => 'Value updated successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }

    // Save Bulk Raw .ENV Code
    if ($action === 'save_raw_env') {
        $rawEnv = $payload['raw_env'] ?? '';
        $siteId = isset($payload['site_id']) ? (int)$payload['site_id'] : $controlPanelSiteId;
        $isGlobal = !empty($payload['global']) ? 1 : 0;

        $lines = preg_split("/\r\n|\n|\r/", $rawEnv);
        $savedCount = 0;
        $errors = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue; // Skip comments and empty lines
            }

            $pos = strpos($trimmed, '=');
            if ($pos === false) {
                continue;
            }

            $k = trim(substr($trimmed, 0, $pos));
            $v = trim(substr($trimmed, $pos + 1));

            // Strip surrounding double or single quotes if present
            if ((str_starts_with($v, '"') && str_ends_with($v, '"')) || (str_starts_with($v, "'") && str_ends_with($v, "'"))) {
                $v = substr($v, 1, -1);
            }

            if (empty($k)) {
                continue;
            }

            $escK = mysqli_real_escape_string($connection, $k);
            $escV = mysqli_real_escape_string($connection, $v);

            // Check if key already exists in this scope
            $scopeCond = $isGlobal ? "site_configuration_global = 1" : "(site_configuration_site_id = {$siteId} AND site_configuration_global = 0)";
            $chk = mysqli_query($connection, "SELECT site_configuration_id FROM {$tblPrefix}site_configuration WHERE site_configuration_key = '{$escK}' AND {$scopeCond} LIMIT 1");
            if ($chk && mysqli_num_rows($chk) > 0) {
                $row = mysqli_fetch_assoc($chk);
                $cid = (int)$row['site_configuration_id'];
                mysqli_query($connection, "UPDATE {$tblPrefix}site_configuration SET site_configuration_value = '{$escV}', site_configuration_status = 1 WHERE site_configuration_id = {$cid}");
            } else {
                mysqli_query($connection, "INSERT INTO {$tblPrefix}site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_global, site_configuration_status) VALUES ({$siteId}, '{$escK}', '{$escV}', {$isGlobal}, 1)");
            }
            $savedCount++;
        }

        echo json_encode([
            'status' => 'success',
            'msg' => "Successfully parsed and saved {$savedCount} environment variables.",
            'count' => $savedCount
        ]);
        exit();
    }

    // Save Bulk Raw JSON Code
    if ($action === 'save_raw_json') {
        $rawJson = $payload['raw_json'] ?? '';
        $siteId = isset($payload['site_id']) ? (int)$payload['site_id'] : $controlPanelSiteId;
        $isGlobal = !empty($payload['global']) ? 1 : 0;

        $parsed = json_decode($rawJson, true);
        if (!is_array($parsed)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid JSON syntax. Please check formatting.']);
            exit();
        }

        $savedCount = 0;
        foreach ($parsed as $k => $v) {
            $k = trim($k);
            if (empty($k)) continue;
            if (is_array($v) || is_object($v)) {
                $v = json_encode($v, JSON_UNESCAPED_SLASHES);
            } elseif (is_bool($v)) {
                $v = $v ? '1' : '0';
            } else {
                $v = (string)$v;
            }

            $escK = mysqli_real_escape_string($connection, $k);
            $escV = mysqli_real_escape_string($connection, $v);

            $scopeCond = $isGlobal ? "site_configuration_global = 1" : "(site_configuration_site_id = {$siteId} AND site_configuration_global = 0)";
            $chk = mysqli_query($connection, "SELECT site_configuration_id FROM {$tblPrefix}site_configuration WHERE site_configuration_key = '{$escK}' AND {$scopeCond} LIMIT 1");
            if ($chk && mysqli_num_rows($chk) > 0) {
                $row = mysqli_fetch_assoc($chk);
                $cid = (int)$row['site_configuration_id'];
                mysqli_query($connection, "UPDATE {$tblPrefix}site_configuration SET site_configuration_value = '{$escV}', site_configuration_status = 1 WHERE site_configuration_id = {$cid}");
            } else {
                mysqli_query($connection, "INSERT INTO {$tblPrefix}site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_global, site_configuration_status) VALUES ({$siteId}, '{$escK}', '{$escV}', {$isGlobal}, 1)");
            }
            $savedCount++;
        }

        echo json_encode([
            'status' => 'success',
            'msg' => "Successfully parsed and saved {$savedCount} JSON configuration keys.",
            'count' => $savedCount
        ]);
        exit();
    }

    // Save (Create or Update single parameter)
    $key = trim($payload['key'] ?? '');
    $value = trim($payload['value'] ?? '');
    $siteId = isset($payload['site_id']) ? (int)$payload['site_id'] : $controlPanelSiteId;
    $isGlobal = !empty($payload['global']) ? 1 : 0;
    $status = isset($payload['status']) ? (int)$payload['status'] : 1;

    if (empty($key)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Configuration key is required.']);
        exit();
    }

    $escKey = mysqli_real_escape_string($connection, $key);
    $escVal = mysqli_real_escape_string($connection, $value);

    if ($configId > 0) {
        $up = "UPDATE {$tblPrefix}site_configuration SET 
                site_configuration_key = '{$escKey}',
                site_configuration_value = '{$escVal}',
                site_configuration_global = {$isGlobal},
                site_configuration_status = {$status}
               WHERE site_configuration_id = {$configId}";
        if (mysqli_query($connection, $up)) {
            echo json_encode(['status' => 'success', 'msg' => 'Configuration updated successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
    } else {
        $ins = "INSERT INTO {$tblPrefix}site_configuration 
                (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_global, site_configuration_status)
                VALUES ({$siteId}, '{$escKey}', '{$escVal}', {$isGlobal}, {$status})";
        if (mysqli_query($connection, $ins)) {
            echo json_encode(['status' => 'success', 'msg' => 'Configuration created successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
    }
    exit();
}
