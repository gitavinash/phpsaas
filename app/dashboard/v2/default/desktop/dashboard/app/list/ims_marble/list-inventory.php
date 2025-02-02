<?php
$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

if (isset($_GET['warehouse'])) {
    if ($_GET['warehouse'] != '') {
        $warehouseFilterValue = $_GET['warehouse'];
        $warehouseFilter = "AND block_warehouse_id='{$warehouseFilterValue}'";
    } else {
        $warehouseFilterValue = '#';
        $warehouseFilter = '';
    }
} else {
    $warehouseFilterValue = '#';
    $warehouseFilter = '';
}

if (isset($_GET['product_type'])) {
    if ($_GET['product_type'] != '') {
        $typeFilterValue = $_GET['product_type'];
        $typeFilter = "AND block_product_type='{$typeFilterValue}'";
    } else {
        $typeFilterValue = '#';
        $typeFilter = '';
    }
} else {
    $typeFilterValue = '#';
    $typeFilter = '';
}

if (isset($_GET['status'])) {
    if ($_GET['status'] != '') {
        $statusFilterValue = $_GET['status'];
        $statusFilter = "AND block_status='{$statusFilterValue}'";
    } else {
        $statusFilterValue = '#';
        $statusFilter = 'AND block_status!=2';
    }
} else {
    $statusFilterValue = '#';
    $statusFilter = 'AND block_status!=2';
}

?>
<div class="tableFilter">
    <div class="filterBox">
        <label>Filter by</label>
    </div>
    <div class="filterBox">
        <select id="warehouseFilter">
            <option value="">Warehouse</option>
            <?php

            if ($checkWarehouse = mysqli_query($connection, "SELECT warehouse_id, warehouse_title FROM " . TABLE_PREFIX . "warehouse WHERE warehouse_status=1 AND warehouse_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($checkWarehouse) != 0) {
                    while ($warehouseData = mysqli_fetch_assoc($checkWarehouse)) {
                        echo "<option value='{$warehouseData['warehouse_id']}' " . (($warehouseData['warehouse_id'] == $warehouseFilterValue) ? 'selected' : '') . " >{$warehouseData['warehouse_title']}</option>";
                    }
                }
            }

            ?>
        </select>
    </div>
    <div class="filterBox">
        <select id="typeFilter">
            <option value="">Product type</option>
            <?php

            if ($checkProductType = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key LIKE '%measurement_%' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($checkProductType) != 0) {
                    while ($productData = mysqli_fetch_assoc($checkProductType)) {
                        $productTypeValues = strtolower(str_replace('measurement_', '', $productData['site_configuration_key']));
                        echo "<option value='{$productTypeValues}'" . (($productTypeValues == $typeFilterValue) ? 'selected' : '') . ">" . ucwords(str_replace('measurement_', '', $productData['site_configuration_key'])) . "</option>";
                    }
                }
            }

            ?>
        </select>
    </div>
    <div class="filterBox">
        <select id="statusFilter">
            <option value="">Status</option>
            <option value="1" <?php echo (($statusFilterValue == '1') ? 'selected' : '') ?>>Active</option>
            <option value="3" <?php echo (($statusFilterValue == '3') ? 'selected' : '') ?>>Sold out</option>
            <option value="4" <?php echo (($statusFilterValue == '4') ? 'selected' : '') ?>>Return</option>
            <option value="2" <?php echo (($statusFilterValue == '2') ? 'selected' : '') ?>>Delete</option>
            <option value="5" <?php echo (($statusFilterValue == '5') ? 'selected' : '') ?>>Transfer request pending</option>
            <option value="0" <?php echo (($statusFilterValue == '0') ? 'selected' : '') ?>>Pending</option>
        </select>
    </div>
    <div class="filterBox">
        <a href="#" class="btn btn-green date-btn" id="applyInventoryFilter">Apply Fiter</a>
        <a href="/print_stock/?productType=<?php echo $typeFilterValue ?>&warehouse=<?php echo $warehouseFilterValue ?>&status=<?php echo $statusFilterValue ?>" class="btn btn-green date-btn bgYellow printBlockFilter" id="printInventoryBtn" target="_blank">🖨️ Print inventory</a>
        <!-- <select value="#" id="printInventory" class="filterBox bgYellow printBlockFilter">
            <option value="#" selected>🖨️ Print inventory</option>
            <option value="all">Print all inventory</option>
            <?php

            if ($checkProductType = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key LIKE '%measurement_%' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($checkProductType) != 0) {
                    while ($productData = mysqli_fetch_assoc($checkProductType)) {
                        $productTypeValues = strtolower(str_replace('measurement_', '', $productData['site_configuration_key']));
                        echo "<option value='{$productTypeValues}'" . (($productTypeValues == $typeFilterValue) ? 'selected' : '') . ">" . ucwords(str_replace('measurement_', '', $productData['site_configuration_key'])) . " inventory</option>";
                    }
                }
            }

            ?>
        </select> -->
    </div>
</div>
<table class="showInventoryTableList display nowrap" style="width: 100%;">
    <thead>
        <tr>
            <th>S. No.</th>
            <th>Code</th>
            <th>Name</th>
            <th>Warehouse</th>
            <th>Type</th>
            <th>Total slabs</th>
            <th>Avl. slabs</th>
            <th>Gross qty.</th>
            <th>Net Size</th>
            <th>Less qty.</th>
            <th>Status</th>
            <th style="width:160px"></th>
        </tr>
    </thead>
    <?php

    if ($site_access_type != 'administrator' && $site_access_type != 'both') {
        $userFilter = 'AND block_user_id="' . USER_ID . '" ';
    } else {
        $userFilter = '';
    }


    if ($inventoryList = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block INNER JOIN " . TABLE_PREFIX . "warehouse ON block_warehouse_id=warehouse_id LEFT JOIN " . TABLE_PREFIX . "supplier ON block_supplier_id=supplier_id INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id=address_id INNER JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id WHERE block_site_id='" . SITE_ID . "' {$userFilter} {$statusFilter} {$typeFilter} {$warehouseFilter} ORDER BY block_code ASC, block_title ASC")) {
        echo "<tbody>";
        $totalNoOfSlabs = array();
        $totalNoOfAvailableSlabs = array();
        $totalGrossQty = array();
        $totalNetQty = array();
        $totalLessQty = array();
        if (mysqli_num_rows($inventoryList) > 0) {
            $serialNo = 1;
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
                    case 2:
                        $blockStatus = 'Delete';
                        break;
                    case 5:
                        $blockStatus = 'Transfer request pending';
                        break;
                    default:
                        $blockStatus = 'Pending';
                        break;
                }

                switch ($block_size_dimension) {
                    case 'mt_weight':
                        $blockDimension = 'Metric Ton';
                        break;
                    default:
                        $blockDimension = $block_size_dimension;
                        break;
                }

                echo "<tr>
                                <td>{$serialNo}</td>
                                <td style='font-weight: 300'>" . (($block_code != '') ? '<strong style="color: #000; font-weight: 700" >' . $block_code . '</strong>' : '') . "</td>
                                <td style='font-weight: 300'>$block_title</td>
                                <td>{$warehouse_title}</td>
                                <td>" . ucwords($block_product_type) . "</td>
                                <td>{$block_total_item}</td>
                                <td>{$block_available}</td>
                                <td>" . (($block_gross_size == '') ? '0 ' : $block_gross_size) . ' ' . $blockDimension . "</td>
                                <td>" . (($block_net_size == '') ? '0 ' : $block_gross_size - $block_damage_size) . ' ' . $blockDimension . "</td>
                                <td>" . (($block_damage_size == '') ? '0 ' : $block_damage_size) . ' ' . $blockDimension . "</td>
                                <td>" . $blockStatus . "</td>
                                <td>
                                    <div class='tableActionButton'>
                                        <a href='/dashboard/inventory/update/?id=" . base64_encode($block_id) . "' class='tableActionIconBtn' title='Edit block' data-title='Edit block' target='_blank'><i class='fi fi-rr-edit bgWhite'></i></a>
                                            <a href='{$block_qr}' class='tableActionIconBtn' target='_blank' title='Show QR Code' data-title='Show QR code' target='_blank'><i class='fi fi-rr-qr-scan bgWhite'></i></a>
                                            <a href='/dashboard/inventory/update/?updateType=item&id=" . base64_encode($block_id) . "' class='tableActionIconBtn' title='Update block item' data-title='Update block slabs' target='_blank'><i class='fi fi-rr-menu-burger bgWhite'></i></a>
                                            <a href='/view_report/?id=" . base64_encode($block_id) . "' title='Print block' data-title='Print block inventory report' class='tableActionIconBtn' target='_blank' target='_blank'><i class='fi fi-rr-inventory-alt bgWhite'></i></a>
                                            <a href='/print_label/?id=" . base64_encode($block_id) . "&theme=2' title='Print block'  data-title='Print block label' class='tableActionIconBtn' target='_blank' target='_blank'><i class='fi fi-rr-print bgWhite'></i></a>
                                            <a href='/dashboard/inventory/logs/?id=" . base64_encode($block_id) . "' title='Print block'  data-title='Show Transfer history' class='tableActionIconBtn' target='_blank' target='_blank'><i class='fi fi-rr-notebook bgWhite'></i></a>
                                            " . (($block_status != 5) ? "<a href='/dashboard/inventory/transfer/?id=" . base64_encode($block_id) . "' class='tableActionIconBtn transferBtn' title='Transfer block to new location' data-title='Transfer block to new location' target='_blank'><i class='fi fi-rr-calendar-shift-swap bgYellow'></i></a>" : '') . "
                                            <a href='#' title='Mark as sold out' data-title='Mark as Sold Out' class='tableActionIconBtn markAsSold' data-type='block' data-id='{$block_id}'  target='_blank'><i class='fi fi-rr-sold-signal bgRed'></i></a>
                                            " . (($block_status == 1) ? "
                                            <a href='#' data-type='ims_marble/block' data-id='{$block_id}' title='Delete block' data-title='Delete block' class='tableActionIconBtn deleteButton' data-reload='true' data-redirect='false' target='_blank'><i class='fi fi-rr-trash bgRed'></i></a>" : "") . "
                                    </div>            
                                </td>";
                echo "</tr>";
                $serialNo++;

                $totalNoOfSlabs[] = $block_total_item;
                $totalNoOfAvailableSlabs[] = $block_available;
                $totalGrossQty[] = $block_gross_size;
                $totalNetQty[] = $block_net_size;
                $totalLessQty[] = $block_damage_size;
            }
        }
        echo "</tbody>";
        echo "
        <tfoot>
        <tr>
            <td colspan=5>Total</td>
            <td>" . array_sum($totalNoOfSlabs) . "</td>
            <td>" . array_sum($totalNoOfAvailableSlabs) . "</td>
            <td>" . array_sum($totalGrossQty) . "</td>
            <td>" . array_sum($totalNetQty) . "</td>
            <td>" . array_sum($totalLessQty) . "</td>
            <td colspan=2></td>
        </tr></tfoot>";
    } else {
        if (DEVELOPMENT_MODE == TRUE) {
            echo "Debugging: " . mysqli_error($connection);
            echo "Query: SELECT * FROM {$tableNamePrefix}block INNER JOIN " . TABLE_PREFIX . "warehouse ON block_warehouse_id=warehouse_id LEFT JOIN " . TABLE_PREFIX . "supplier ON block_supplier_id=supplier_id INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id=address_id INNER JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id WHERE block_site_id='" . SITE_ID . "' {$userFilter} {$statusFilter} {$typeFilter} {$warehouseFilter} ORDER BY block_code ASC, block_title ASC";
        }
    }

    ?>
</table>

<!-- Transfer pop up box -->

<div class="popUp transferBox">
    <div class="smallPopUpBox">
        <div class="widget box-7">
            <div class="contentArea">
                <form method="POST" action="#">
                    <section class="form">
                        <div class="step-open-box step_1_box">
                            <div class="form-header">
                                <h4 class="heading">Conclusion & Summary</h4>
                            </div>
                            <div class="form-group">
                                <select class="selectJS form-control" id="notesTitle" name="title">
                                    <option value=""></option>
                                    <option value="Not interested">Not Interested</option>
                                    <option value="Purchased Elsewhere">Purchased Elsewhere</option>
                                    <option value="Unqualified Lead">Unqualified Lead</option>
                                    <option value="Fake Enquiry">Fake Enquiry</option>
                                    <option value="Client is not Picking call">Client is not Picking call</option>
                                    <option value="other">Other</option>
                                </select>
                                <label for="">Title <sup>*</sup></label>
                            </div>
                            <div class="form-group">
                                <textarea type="text" name="noteMsg" id="noteMsg" placeholder="Message*"
                                    class="form-control" required></textarea>
                                <label class="form-lable" id="noteMsgLabel">Message</label>
                            </div>
                        </div>
                </form>
            </div>
        </div>
        <div class="widget box-3">
            <div class="actionArea bgRed">
                <a class="btn popUpBtn" id="updateNotes"><i class="fi-rr-check"></i> Mark as closed</a>
                <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                    Cancel</span>
            </div>
        </div>
    </div>
</div>