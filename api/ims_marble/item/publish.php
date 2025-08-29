<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if (isset($blockId)) {
    if ($blockId != '') {
        $validRequest = true;
        $blockItemId = base64_decode($blockId); // keep your pattern
    } else {
        $msg = 'Block item id cannot be blank';
    }
} else {
    $msg = 'Block item id is missing';
}

if (isset($validRequest)) {

    mysqli_query($connection, "START TRANSACTION");

    // Lock block row
    if ($checkBlock = mysqli_query(
        $connection,
        "SELECT block_product_type,
                block_size_dimension,
                block_total_size,
                block_gross_size,
                block_net_size,
                block_damage_size
         FROM {$tableNamePrefix}block
         WHERE block_id='{$blockId}'
           AND block_status=1
           AND block_site_id=" . SITE_ID . " FOR UPDATE"
    )) {
        if (mysqli_num_rows($checkBlock) != 0) {

            extract(mysqli_fetch_assoc($checkBlock));

            $receivedWidth  = (isset($_POST['width'])  && is_array($_POST['width']))  ? $_POST['width']  : [];
            $receivedHeight = (isset($_POST['height']) && is_array($_POST['height'])) ? $_POST['height'] : [];

            $changedIds = array_unique(array_merge(array_keys($receivedWidth), array_keys($receivedHeight)));
            $changedIds = array_values(array_filter($changedIds, function ($v) {
                return ctype_digit((string)$v);
            }));

            if (empty($changedIds)) {
                echo json_encode(['status' => 'warning', 'msg' => 'No changes to save.'], TRUE);
                exit;
            }

            // Fetch current item values
            $idList = implode(',', array_map('intval', $changedIds));
            $itemsSql = "
                SELECT block_item_id, block_item_width, block_item_height, block_item_damage_size
                FROM {$tableNamePrefix}block_item
                WHERE block_item_parent_id={$blockId}
                  AND block_item_status != 3
                  AND block_item_site_id=" . SITE_ID . "
                  AND block_item_id IN ({$idList})
                FOR UPDATE";
            $itemsRes = mysqli_query($connection, $itemsSql);

            if (!$itemsRes) {
                mysqli_query($connection, "ROLLBACK");
                echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)], TRUE);
                exit;
            }

            $current = [];
            while ($r = mysqli_fetch_assoc($itemsRes)) {
                $current[(int)$r['block_item_id']] = $r;
            }

            $changedIds = array_values(array_filter($changedIds, fn($id) => isset($current[(int)$id])));
            if (empty($changedIds)) {
                mysqli_query($connection, "ROLLBACK");
                echo json_encode(['status' => 'warning', 'msg' => 'No valid items to update.'], TRUE);
                exit;
            }

            // measurement config
            $measurementData = constant(strtoupper('measurement_' . $block_product_type));
            extract(json_decode($measurementData, TRUE));

            // converter
            $convert = function (float $raw) use ($measurementType, $calculationType): float {
                switch ([$measurementType, $calculationType]) {
                    case ['cm', 'sq. mm.']:
                        return $raw * 100;
                    case ['cm', 'sq. in.']:
                        return $raw * 0.15500031;
                    case ['cm', 'sq. ft.']:
                        return $raw * 0.001076391;
                    case ['cm', 'sq. mt.']:
                        return $raw * 0.0001;
                    case ['mm', 'sq. cm.']:
                        return $raw * 0.01;
                    case ['mm', 'sq. in.']:
                        return $raw * 0.0015500031;
                    case ['mm', 'sq. ft.']:
                        return $raw * 0.00001076391;
                    case ['mm', 'sq. mt.']:
                        return $raw * 0.000001;
                    case ['in', 'sq. cm.']:
                        return $raw * 6.4516;
                    case ['in', 'sq. mm.']:
                        return $raw * 645.16;
                    case ['in', 'sq. ft.']:
                        return $raw * 0.00694444;
                    case ['in', 'sq. mt.']:
                        return $raw * 0.00064516;
                    case ['ft', 'sq. cm.']:
                        return $raw * 929.0304;
                    case ['ft', 'sq. mm.']:
                        return $raw * 92903.04;
                    case ['ft', 'sq. in.']:
                        return $raw * 144;
                    case ['ft', 'sq. mt.']:
                        return $raw * 0.09290304;
                    case ['mt', 'sq. cm.']:
                        return $raw * 10000;
                    case ['mt', 'sq. mm.']:
                        return $raw * 1000000;
                    case ['mt', 'sq. in.']:
                        return $raw * 1550.0031;
                    case ['mt', 'sq. ft.']:
                        return $raw * 10.7639;
                    default:
                        return $raw;
                }
            };

            $widthCase = $heightCase = $grossCase = $netCase = '';
            $deltaSum = 0.0;

            foreach ($changedIds as $index) {
                $idx = (int)$index;
                $currW = (float)$current[$idx]['block_item_width'];
                $currH = (float)$current[$idx]['block_item_height'];

                $newW = array_key_exists($index, $receivedWidth) ? (float)$receivedWidth[$index] : $currW;
                $newH = array_key_exists($index, $receivedHeight) ? (float)$receivedHeight[$index] : $currH;

                if ($block_size_dimension == 'mt (t)') {
                    $oldRaw = $currW;
                    $newRaw = $newW;
                    $itemDelta = $newRaw - $oldRaw;
                } else {
                    $oldRaw = $currW * ($currH ?: 1);
                    $newRaw = $newW * ($newH ?: 1);
                    $itemDelta = $convert($newRaw) - $convert($oldRaw);
                }

                $deltaSum += $itemDelta;

                // save as INTs
                $widthCase  .= "WHEN block_item_id='{$idx}' THEN " . intval(round($newW)) . " ";
                $heightCase .= "WHEN block_item_id='{$idx}' THEN " . intval(round($newH)) . " ";

                $itemGrossRaw = ($block_size_dimension == 'mt (t)') ? $newW : $newRaw;
                $grossCase .= "WHEN block_item_id='{$idx}' THEN " . intval(round($itemGrossRaw)) . " ";
                $netCase  .= "WHEN block_item_id='{$idx}' THEN " . intval(round($itemGrossRaw)) . " ";
            }

            $new_total = (float)$block_total_size + $deltaSum;
            $new_gross = (float)$block_gross_size + $deltaSum;
            $new_net = $new_gross - (float)$block_damage_size;

            // round and cast to int before saving
            $new_total = intval(round($new_total));
            $new_gross = intval(round($new_gross));
            $new_net = intval(round($new_net));

            if ($new_total < 0 || $new_gross < 0 || $new_net < 0 || $new_net > $new_gross) {
                mysqli_query($connection, "ROLLBACK");
                echo json_encode(['status' => 'error', 'msg' => 'Validation failed'], TRUE);
                exit;
            }

            $updateItemQuery = "
                UPDATE {$tableNamePrefix}block_item
                SET
                    block_item_status=1,
                    block_item_width=CASE {$widthCase} ELSE block_item_width END,
                    block_item_height=CASE {$heightCase} ELSE block_item_height END,
                    block_item_gross_size=CASE {$grossCase} ELSE block_item_gross_size END,
                    block_item_net_size=(CASE {$netCase} ELSE block_item_net_size END - block_item_damage_size)
                WHERE block_item_parent_id={$blockId}
                  AND block_item_status!=3
                  AND block_item_site_id=" . SITE_ID . "
                  AND block_item_id IN ({$idList})";

            $updateBlockTotalsQuery = "
                UPDATE {$tableNamePrefix}block
                SET block_total_size={$new_total},
                    block_gross_size={$new_gross},
                    block_net_size={$new_net}
                WHERE block_id='{$blockId}' AND block_site_id=" . SITE_ID;

            $okItems = mysqli_query($connection, $updateItemQuery);
            $okBlock = mysqli_query($connection, $updateBlockTotalsQuery);

            if ($okItems && $okBlock) {
                mysqli_query($connection, "COMMIT");
                $status = 'success';
                $msg = 'Block updated successfully';
            } else {
                mysqli_query($connection, "ROLLBACK");
                $status = 'error';
                $msg = DEVELOPMENT_MODE ? mysqli_error($connection) : 'Something went wrong';
            }
        }
    }
}

echo json_encode(['status' => $status, 'msg' => $msg], TRUE);