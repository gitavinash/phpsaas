<?php

header('Content-Type: application/json');
$httpStatus = 405;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($blockId)) {

        $blockItemQuery = "
            SELECT 
                block_item_id AS slab_id,
                CONCAT(block_item_width, block_item_dimension_type) AS slab_width,
                CONCAT(block_item_height, block_item_dimension_type) AS slab_height,
                CONCAT(block_item_width, block_item_dimension_type, ' x ', block_item_height, block_item_dimension_type) AS slab_dimension,
                ROUND(block_item_damage_size, 2) AS slab_damage_area,
                CASE
                    WHEN block_size_dimension = 'sq. mm.' THEN CONCAT(ROUND((block_item_width * block_item_height) / 929.0304, 2))
                    WHEN block_size_dimension = 'sq. cm.' THEN CONCAT(ROUND((block_item_width * block_item_height) / 929.0304, 2))
                    WHEN block_size_dimension = 'sq. in.' THEN CONCAT(ROUND((block_item_width * block_item_height) / 929.0304, 2))
                    WHEN block_size_dimension = 'sq. ft.' THEN CONCAT(ROUND((block_item_width * block_item_height) / 929.0304, 2))
                    WHEN block_size_dimension = 'sq. mt.' THEN CONCAT(ROUND((block_item_width * block_item_height) / 10000, 2))
                END AS slab_total_area,
                block_size_dimension as slab_dimension_type,
                block_item_status
            FROM 
                {$tableNamePrefix}block_item
            INNER JOIN 
                {$tableNamePrefix}block
            ON 
                block_item_parent_id = block_id
            WHERE 
                block_item_parent_id = {$blockId}
                AND block_item_site_id = " . SITE_ID;

        // "SELECT * FROM {$tableNamePrefix}block_item INNER JOIN {$tableNamePrefix}block ON  block_item_parent_id=block_id WHERE block_item_parent_id={$blockId} AND block_item_status=1 AND block_item_site_id=" . SITE_ID

        if ($blocks = mysqli_query($connection, $blockItemQuery)) {
            if (mysqli_num_rows($blocks) != 0) {
                $blockList = array();
                $i = 1;
                while ($blocksData = mysqli_fetch_assoc($blocks)) {
                    if ($blocksData['block_item_status'] == 1) {
                        $blocksData['block_slab_id'] = $i;
                        $blockList[] = $blocksData;
                    }
                    $i++;
                }
                $httpStatus = 200;
                $status = 'success';
                $msg = "Data fetch successfully";
            } else {
                $httpStatus = 404;
                $msg = "No record found in this block id";
            }
        }
    } else {
        $httpStatus = 422;
        $msg = "Block id paramater is missing";
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid request method -> ' . $_SERVER['REQUEST_METHOD']),
    'items' => ((isset($blockList)) ? $blockList : ''),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
