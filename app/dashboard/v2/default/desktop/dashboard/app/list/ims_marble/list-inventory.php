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
            <th>Avl. slabs</th>
            <th>Gross qty.</th>
            <th>Less qty.</th>
            <th>Net Size</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <?php

    if ($site_access_type != 'administrator' && $site_access_type != 'both') {
        $userFilter = 'block_user_id="' . USER_ID . '" AND ';
    } else {
        $userFilter = '';
    }

    if ($inventoryList = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block INNER JOIN " . TABLE_PREFIX . "warehouse ON block_warehouse_id=warehouse_id LEFT JOIN " . TABLE_PREFIX . "supplier ON block_supplier_id=supplier_id INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id=address_id INNER JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id WHERE {$userFilter} block_status!=2 AND block_site_id='" . SITE_ID . "' ORDER BY block_code ASC, block_title ASC")) {
        echo "<tbody>";
        if (mysqli_num_rows($inventoryList) > 0) {
            while ($inventoryData = mysqli_fetch_assoc($inventoryList)) {
                extract($inventoryData);

                $block_gross_size = str_replace(',', '', number_format($block_gross_size, 2));
                $block_damage_size = str_replace(',', '', number_format($block_damage_size, 2));
                $block_net_size = str_replace(',', '', number_format($block_net_size, 2));

                switch ($block_status) {
                    case 1:
                        $blockStatus = 'Active';
                        break;
                    case 3:
                        $blockStatus = 'Sold out';
                        break;
                    case 4:
                        $blockStatus = 'Return';
                        break;
                    case 5:
                        $blockStatus = 'Delete';
                        break;
                    default:
                        $blockStatus = 'Pending';
                        break;
                }

                echo "<tr>
                                <td style='font-weight: 300'>" . (($block_code != '') ? '<strong style="color: #000; font-weight: 700" >' . $block_code . '</strong> - ' : '') . "{$block_title}</td>
                                <td>{$warehouse_title}, {$city_name}, {$state_name} - {$address_pin_code}</td>
                                <td>" . ucwords($block_product_type) . "</td>
                                <td>{$block_total_item}</td>
                                <td>{$block_available}</td>
                                <td>" . (($block_gross_size == '') ? '0 ' : $block_gross_size) . ' ' . $block_size_dimension . "</td>
                                <td>" . (($block_damage_size == '') ? '0 ' : $block_damage_size) . ' ' . $block_size_dimension . "</td>
                                <td>" . (($block_net_size == '') ? '0 ' : $block_net_size) . ' ' . $block_size_dimension . "</td>
                                <td>" . $blockStatus . "</td>
                                <td>
                                <a href='/dashboard/inventory/update/?id=" . base64_encode($block_id) . "' class='tableActionIconBtn' title='Edit block' data-title='Edit block'><i class='fi fi-rr-edit bgWhite'></i></a>
                                <a href='{$block_qr}' class='tableActionIconBtn' target='_blank' title='Show QR Code' data-title='Show QR code'><i class='fi fi-rr-qr-scan bgWhite'></i></a>
                                <a href='/dashboard/inventory/update/?updateType=item&id=" . base64_encode($block_id) . "' class='tableActionIconBtn' title='Update block item' data-title='Update block slabs'><i class='fi fi-rr-menu-burger bgWhite'></i></a>
                                <a href='/view_report/?id=" . base64_encode($block_id) . "' title='Print block' data-title='Print block inventory report' class='tableActionIconBtn' target='_blank'><i class='fi fi-rr-inventory-alt bgWhite'></i></a>
                                <a href='/print_label/?id=" . base64_encode($block_id) . "&theme=2' title='Print block'  data-title='Print block label' class='tableActionIconBtn' target='_blank'><i class='fi fi-rr-print bgWhite'></i></a>
                                " . (($block_status == 1) ? "
                                <a href='#' data-type='ims_marble/block' data-id='{$block_id}' title='Delete block' data-title='Delete block' class='tableActionIconBtn deleteButton' data-reload='true' data-redirect='false'><i class='fi fi-rr-trash bgRed'></i></a>
                                <a href='#' title='Mark as sold out' data-title='Mark as Sold Out' class='tableActionIconBtn markAsSold' data-type='block' data-id='{$block_id}' ><i class='fi fi-rr-sold-signal bgRed'></i></a>" : "") . "
                                </td>
                            </tr>";
            }
        }
        echo "</tbody>";
    }

    ?>
</table>