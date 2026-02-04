<?php
header('Content-Type: application/json');

// DB connection assumed: $connection

// 1. Get product type safely (example: from GET param)
$productType = $_GET['product_type'] ?? 'product'; // default

// 2. Whitelist allowed tables
$allowedTables = ['product', 'service', 'package'];

if (!in_array($productType, $allowedTables)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid product type"]);
    exit;
}

// 3. Build table name
$productTable = TABLE_PREFIX . $productType;

$sql = "
SELECT 
    pr.profile_display_name,
    p.product_title,
    CONCAT('" . SITE_URL . "', p.product_image) AS product_image,
    l.link_url,
    CONCAT(
        CASE
            WHEN TIMESTAMPDIFF(SECOND, o.order_update, NOW()) < 60 THEN CONCAT(TIMESTAMPDIFF(SECOND, o.order_update, NOW()), ' seconds ago')
            WHEN TIMESTAMPDIFF(MINUTE, o.order_update, NOW()) < 60 THEN CONCAT(TIMESTAMPDIFF(MINUTE, o.order_update, NOW()), ' minutes ago')
            WHEN TIMESTAMPDIFF(HOUR, o.order_update, NOW()) < 24 THEN CONCAT(TIMESTAMPDIFF(HOUR, o.order_update, NOW()), ' hours ago')
            WHEN TIMESTAMPDIFF(DAY, o.order_update, NOW()) < 30 THEN CONCAT(TIMESTAMPDIFF(DAY, o.order_update, NOW()), ' days ago')
            ELSE CONCAT(TIMESTAMPDIFF(MONTH, o.order_update, NOW()), ' months ago')
        END
    ) AS order_update
FROM " . TABLE_PREFIX . "order o
INNER JOIN " . TABLE_PREFIX . "profile pr
    ON o.order_user_id = pr.profile_user_id
INNER JOIN {$productTable} p
    ON FIND_IN_SET(
        p.product_id,
        REPLACE(REPLACE(o.order_product_id,'(',''),')','')
    )
INNER JOIN " . TABLE_PREFIX . "link l
    ON l.link_relation_id=p.{$productType}_id
    AND l.link_type='{$productType}'
    AND l.link_site_id=" . SITE_ID . "
WHERE o.order_site_id = ?
  AND p.product_site_id = ?

ORDER BY o.order_update DESC
LIMIT 10
";

$stmt = mysqli_prepare($connection, $sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Query preparation failed"]);
    exit;
}

$siteId = SITE_ID;
mysqli_stmt_bind_param($stmt, "ii", $siteId, $siteId);

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = [];

while ($row = mysqli_fetch_assoc($result)) {

    // mask name (privacy)
    if (!empty($row['profile_name'])) {
        $row['profile_name'] = substr($row['profile_name'], 0, 1) . "***";
    }

    $orders[] = $row;
}

echo json_encode([
    "status" => "success",
    "count" => count($orders),
    "data" => $orders
]);

mysqli_stmt_close($stmt);