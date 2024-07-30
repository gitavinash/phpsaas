<?php
$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';
?>
<table class="showTableList">
    <thead>
        <tr>
            <th>Name</th>
            <th>Warehouse</th>
            <th>Type</th>
            <th>Total slabs</th>
            <th>Gross qty.</th>
            <th>Less qty.</th>
            <th>Net Size</th>
            <th></th>
        </tr>
    </thead>
    <?php

    if ($site_access_type != 'administrator') {
        $userFilter = 'AND block_user_id="' . USER_ID . '"';
    } else {
        $userFilter = '';
    }

    if ($inventoryList = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block INNER JOIN " . TABLE_PREFIX . "warehouse ON block_warehouse_id=warehouse_id LEFT JOIN " . TABLE_PREFIX . "supplier ON block_supplier_id=supplier_id INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id=address_id INNER JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id WHERE block_status=1 {$userFilter} AND block_site_id='" . SITE_ID . "' ORDER BY block_update DESC")) {
        echo "<tbody>";
        if (mysqli_num_rows($inventoryList) > 0) {
            while ($inventoryData = mysqli_fetch_assoc($inventoryList)) {
                extract($inventoryData);

                echo "<tr>
                                <td>{$block_title}</td>
                                <td>{$warehouse_title}, {$city_name}, {$state_name} - {$address_pin_code}</td>
                                <td>" . ucwords($block_product_type) . "</td>
                                <td>{$block_total_item}</td>
                                <td>" . (($block_gross_size == '') ? '0 '  : $block_gross_size) . ' ' . $block_size_dimension . "</td>
                                <td>" . (($block_damage_size == '') ? '0 '  : $block_damage_size) . ' ' . $block_size_dimension . "</td>
                                <td>" . (($block_net_size == '') ? '0 '  : $block_net_size) . ' ' . $block_size_dimension . "</td>
                                <td>
                                <a href='/dashboard/inventory/update/?id=" . base64_encode($block_id) . "' class='tableActionIconBtn' title='Edit block'><i class='fi fi-rr-edit bgLightGreen'></i></a>
                                <a href='{$block_qr}' class='tableActionIconBtn' target='_blank' title='Show QR Code'><i class='fi fi-rr-qr-scan bgLightGreen'></i></a>
                                <a href='/dashboard/inventory/update/?updateType=item&id=" . base64_encode($block_id) . "' class='tableActionIconBtn' title='Update block item'><i class='fi fi-rr-menu-burger bgLightGreen'></i></a>
                                <a href='/view_report/?id=" . base64_encode($block_id) . "' title='Print block' class='tableActionIconBtn' target='_blank'><i class='fi fi-rr-inventory-alt bgLightGreen'></i></a>
                                <a href='/view_label/?id=" . base64_encode($block_id) . "' title='Print block' class='tableActionIconBtn' target='_blank'><i class='fi fi-rr-print bgLightGreen'></i></a>
                                <a href='#' data-type='ims_marble/block' data-id='{$block_id}' title='Delete block' class='tableActionIconBtn deleteButton' data-reload='true' data-redirect='false'><i class='fi fi-rr-trash bgRed'></i></a>
                                </td>
                            </tr>";
            }
        }
        echo "</tbody>";
    }

    ?>
</table>