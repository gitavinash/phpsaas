<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

$httpStatus = 400;
$status = 'error';
$msg = 'Invalid request';
$galleryImages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $packageIdRaw = $_POST['packageId'] ?? 0;
    $packageId = is_numeric($packageIdRaw) ? (int)$packageIdRaw : (int)base64_decode($packageIdRaw);
    $galleryId = isset($_POST['galleryId']) ? (int)$_POST['galleryId'] : 0;
    $imagePath = isset($_POST['imagePath']) ? trim($_POST['imagePath']) : '';
    $assignedDay = isset($_POST['day']) ? (int)$_POST['day'] : 0;

    if ($packageId > 0 || $galleryId > 0) {
        $whereClause = "";
        if ($galleryId > 0) {
            $whereClause = "gallery_id = '{$galleryId}' AND gallery_site_id = {$siteId}";
        } else {
            $whereClause = "gallery_category = '{$packageId}' AND gallery_type = 'tour_package' AND gallery_site_id = {$siteId}";
        }

        $query = "SELECT gallery_id, gallery_image_list FROM {$tblPrefix}gallery WHERE {$whereClause} LIMIT 1";
        if ($res = mysqli_query($connection, $query)) {
            if (mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                $gId = (int)$row['gallery_id'];
                $list = json_decode($row['gallery_image_list'] ?? '[]', true) ?? [];

                if ($imagePath !== '') {
                    // Step 1: Enforce max 1 image per day rule
                    if ($assignedDay > 0) {
                        foreach ($list as &$item) {
                            if (isset($item['day']) && (int)$item['day'] === $assignedDay && ($item['path'] ?? '') !== $imagePath) {
                                $item['day'] = 0;
                            }
                        }
                        unset($item);
                    }

                    // Step 2: Update target image assignment
                    $targetFound = false;
                    foreach ($list as &$item) {
                        if (($item['path'] ?? '') === $imagePath) {
                            $item['day'] = $assignedDay > 0 ? $assignedDay : 0;
                            $targetFound = true;
                            break;
                        }
                    }
                    unset($item);

                    if (!$targetFound && $imagePath !== '') {
                        $list[] = [
                            'path' => $imagePath,
                            'name' => basename($imagePath),
                            'day' => $assignedDay > 0 ? $assignedDay : 0
                        ];
                    }

                    // Step 3: Sort images: assigned days (1, 2, 3...) first, then unassigned
                    usort($list, function ($a, $b) {
                        $dayA = isset($a['day']) && (int)$a['day'] > 0 ? (int)$a['day'] : 999999;
                        $dayB = isset($b['day']) && (int)$b['day'] > 0 ? (int)$b['day'] : 999999;
                        if ($dayA === $dayB) {
                            return 0;
                        }
                        return ($dayA < $dayB) ? -1 : 1;
                    });

                    $jsonEncoded = mysqli_real_escape_string($connection, json_encode($list, JSON_UNESCAPED_SLASHES));
                    $updateQuery = "UPDATE {$tblPrefix}gallery SET gallery_image_list = '{$jsonEncoded}' WHERE gallery_id = '{$gId}' AND gallery_site_id = {$siteId}";

                    if (mysqli_query($connection, $updateQuery)) {
                        $httpStatus = 200;
                        $status = 'success';
                        $msg = $assignedDay > 0 ? "Image assigned to Day {$assignedDay} successfully" : "Image unassigned successfully";
                        $galleryImages = $list;
                    } else {
                        $httpStatus = 500;
                        $status = 'error';
                        $msg = 'Database error while updating gallery assignment: ' . mysqli_error($connection);
                    }
                } else {
                    $msg = 'Missing image path';
                }
            } else {
                $httpStatus = 404;
                $msg = 'Gallery record not found';
            }
        } else {
            $httpStatus = 500;
            $msg = 'Query error: ' . mysqli_error($connection);
        }
    } else {
        $httpStatus = 400;
        $msg = 'Invalid Package or Gallery ID';
    }
}

http_response_code($httpStatus);
echo json_encode([
    'status' => $status,
    'msg' => $msg,
    'images' => $galleryImages
], JSON_UNESCAPED_SLASHES);
exit;
