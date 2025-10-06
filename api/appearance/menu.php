<?php
require_once USER_FUNCTIONS;
require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw, true);

    if (!is_array($decoded)) {
        $httpStatus = 400;
        $status = 'error';
        $msg = 'Invalid JSON: ' . json_last_error_msg();
    } else {
        // Build key and JSON payload
        $positionName = isset($decoded['position']) ? (string)$decoded['position'] : '';
        $items        = isset($decoded['items']) ? $decoded['items'] : [];

        // slug: lower + spaces→underscore
        $menuName = 'site_nav_' . preg_replace('/\s+/', '_', strtolower(trim($positionName)));
        // encode menu items (do not interpolate directly in SQL!)
        $menuJson = json_encode($items, JSON_UNESCAPED_SLASHES);

        if ($menuJson === false) {
            $httpStatus = 400;
            $status = 'error';
            $msg = 'Failed to encode menu JSON.';
        } else {
            // 1) Check if row exists
            $sqlCheck = "SELECT site_configuration_id 
                         FROM " . TABLE_PREFIX . "site_configuration
                         WHERE site_configuration_key = ? 
                           AND site_configuration_site_id = ?
                           AND site_configuration_status = 1
                         LIMIT 1";
            $stmt = $connection->prepare($sqlCheck);
            if ($stmt === false) {
                $httpStatus = 500;
                $status = 'error';
                $msg = 'DB prepare failed (check): ' . $connection->error;
            } else {
                $siteId = (int)SITE_ID;
                $stmt->bind_param('si', $menuName, $siteId);
                $stmt->execute();
                $stmt->bind_result($existingId);
                $hasRow = $stmt->fetch();
                $stmt->close();

                if ($hasRow) {
                    // 2a) UPDATE existing row (use prepared statement!)
                    $sqlUpd = "UPDATE " . TABLE_PREFIX . "site_configuration
                               SET site_configuration_value = ?, 
                                   site_configuration_status = 1,
                                   site_configuration_update = NOW()
                               WHERE site_configuration_id = ?
                                 AND site_configuration_site_id = ?";
                    $stmt = $connection->prepare($sqlUpd);
                    if ($stmt === false) {
                        $httpStatus = 500;
                        $status = 'error';
                        $msg = 'DB prepare failed (update): ' . $connection->error;
                    } else {
                        $stmt->bind_param('sii', $menuJson, $existingId, $siteId);
                        if ($stmt->execute()) {
                            $httpStatus = 201;
                            $status = 'success';
                            $msg = (ucwords($positionName) ?: 'Menu') . ' updated successfully';
                        } else {
                            $httpStatus = 500;
                            $status = 'error';
                            $msg = 'DB execute failed (update): ' . $stmt->error;
                        }
                        $stmt->close();
                    }
                } else {
                    // 2b) INSERT new row (use prepared statement!)
                    $sqlIns = "INSERT INTO " . TABLE_PREFIX . "site_configuration
                               (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status, site_configuration_global)
                               VALUES (?, ?, ?, 1, 0)";
                    $stmt = $connection->prepare($sqlIns);
                    if ($stmt === false) {
                        $httpStatus = 500;
                        $status = 'error';
                        $msg = 'DB prepare failed (insert): ' . $connection->error;
                    } else {
                        $stmt->bind_param('iss', $siteId, $menuName, $menuJson);
                        if ($stmt->execute()) {
                            $httpStatus = 201;
                            $status = 'success';
                            $msg = (ucwords($positionName) ?: 'Menu') . ' created successfully';
                        } else {
                            $httpStatus = 500;
                            $status = 'error';
                            $msg = 'DB execute failed (insert): ' . $stmt->error;
                        }
                        $stmt->close();
                    }
                }
            }
        }
    }
} else {
    $httpStatus = 400;
    $status = 'error';
    $msg = 'Bad request';
}

http_response_code($httpStatus);
echo json_encode([
    'status' => $status,
    'msg'    => $msg
], JSON_UNESCAPED_SLASHES);