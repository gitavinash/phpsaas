<?php
$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';
?>
<table class="showTableList">
    <thead>
        <tr>
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
                        $blockDimension = 'Matric Ton';
                        break;
                    default:
                        $blockDimension = $block_size_dimension;
                        break;
                }

                echo "<tr>
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
                                ";

                if (strpos($block_size_dimension, '_weight') !== false) {
                    echo "<td>
                        <a href='/dashboard/inventory/update/?id=" . base64_encode($block_id) . "' class='tableActionIconBtn' title='Edit block' data-title='Edit block'><i class='fi fi-rr-edit bgWhite'></i></a>
                        <a href='/dashboard/inventory/transfer/?id=" . base64_encode($block_id) . "' class='tableActionIconBtn transferBtn' title='Transfer block to new location' data-title='Transfer block to new location'><i class='fi fi-rr-calendar-shift-swap bgWhite'></i></a>
                        <a href='#' data-type='ims_marble/block' data-id='{$block_id}' title='Delete block' data-title='Delete block' class='tableActionIconBtn deleteButton' data-reload='true' data-redirect='false'><i class='fi fi-rr-trash bgRed'></i></a>
                    </td>";
                } else {
                    echo "<td>
                                <a href='/dashboard/inventory/update/?id=" . base64_encode($block_id) . "' class='tableActionIconBtn' title='Edit block' data-title='Edit block'><i class='fi fi-rr-edit bgWhite'></i></a>
                                <a href='{$block_qr}' class='tableActionIconBtn' target='_blank' title='Show QR Code' data-title='Show QR code'><i class='fi fi-rr-qr-scan bgWhite'></i></a>
                                <a href='/dashboard/inventory/update/?updateType=item&id=" . base64_encode($block_id) . "' class='tableActionIconBtn' title='Update block item' data-title='Update block slabs'><i class='fi fi-rr-menu-burger bgWhite'></i></a>
                                <a href='/view_report/?id=" . base64_encode($block_id) . "' title='Print block' data-title='Print block inventory report' class='tableActionIconBtn' target='_blank'><i class='fi fi-rr-inventory-alt bgWhite'></i></a>
                                <a href='/print_label/?id=" . base64_encode($block_id) . "&theme=2' title='Print block'  data-title='Print block label' class='tableActionIconBtn' target='_blank'><i class='fi fi-rr-print bgWhite'></i></a>
                                " . (($block_status == 1) ? "
                                <a href='#' data-type='ims_marble/block' data-id='{$block_id}' title='Delete block' data-title='Delete block' class='tableActionIconBtn deleteButton' data-reload='true' data-redirect='false'><i class='fi fi-rr-trash bgRed'></i></a>
                                <a href='#' title='Mark as sold out' data-title='Mark as Sold Out' class='tableActionIconBtn markAsSold' data-type='block' data-id='{$block_id}' ><i class='fi fi-rr-sold-signal bgRed'></i></a>" : "") . "
                                </td>";
                }

                echo "</tr>";
            }
        }
        echo "</tbody>";
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