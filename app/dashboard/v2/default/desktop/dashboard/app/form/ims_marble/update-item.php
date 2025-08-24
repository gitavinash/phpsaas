<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

if (!isset($id)) { ?>
<script>
window.location.href = '/dashboard/inventory/list/';
</script>
<?php
} else if ($id == '') { ?>
<script>
window.location.href = '/dashboard/inventory/list/';
</script>
<?php
}

$blockId = base64_decode($id);

$blockDetailsQuery = "SELECT 
    *,
    block_total_item - COALESCE(COUNT(block_item_id), 0) AS balanceSlabs
FROM 
    {$tableNamePrefix}block
LEFT JOIN 
    {$tableNamePrefix}block_item 
    ON block_item_parent_id = block_id 
    AND block_item_status IN (1, 3, 5) 
    AND block_site_id = " . SITE_ID . "
WHERE 
    block_id = '{$blockId}' OR block_item_parent_id = {$blockId}
GROUP BY 
    block_id";

if ($checkInventory = mysqli_query($connection, $blockDetailsQuery)) {
    if (mysqli_num_rows($checkInventory) > 0) {
        $validId = true;
        extract(mysqli_fetch_assoc($checkInventory));
        $measurementData = constant(strtoupper('measurement_' . $block_product_type));
        extract(json_decode($measurementData, TRUE));
        switch ($measurementType) {
            case 'mm':
                $measurementType = 'Millimeter';
                break;
            case 'cm':
                $measurementType = 'Centimeter';
                break;
            case 'in':
                $measurementType = 'Inches';
                break;
            case 'mt':
                $measurementType = 'Meter';
                break;
            case 'mt (t)':
                $measurementType = 'Metric Ton';
                break;
            default:
                $measurementType = 'Feet';
                break;
        }
    }
}

if (isset($validId)) {

?>


<div class="popUp manageDamageBox">
    <div class="fullScreenPopUpBox">
        <div class="widget box-7">
            <div class="contentArea">
                <form action="#">
                    <section class="form">
                        <div class="step-open-box step_1_box full-width">
                            <div class="form-header">
                                <h4 class="heading">Damage area in No. <id id="blockItemId"></id> Slab<br />
                                </h4>
                                <div class="full-width popUpUpdateDamageFormInputs" id="damageFormPrevious">
                                </div>
                                <div class="full-width popUpUpdateDamageForm" id="damageForm">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <input id="damageAreaWidth" class="width" name="damageAreaWidth" value=""
                                                type="number" onwheel="this.blur()" />
                                            <!-- <label class="form-lable" id="Label">Width ( in Centimeters
                                                )<sup>*</sup></label> -->
                                            <label class="form-lable" id="Label">Length</label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <select class="form-control" name="damageAreaWidthThicknessDimension"
                                                disabled>
                                                <option value="mm"
                                                    <?php echo ((strtolower($measurementType) == 'millimeter') ? 'selected' : '') ?>>
                                                    Millimeter</option>
                                                <option value="cm"
                                                    <?php echo ((strtolower($measurementType) == 'centimeter') ? 'selected' : '') ?>>
                                                    Centimeter</option>
                                                <option value="in"
                                                    <?php echo ((strtolower($measurementType) == 'inches') ? 'selected' : '') ?>>
                                                    Inches</option>
                                                <option value="ft"
                                                    <?php echo ((strtolower($measurementType) == 'feet') ? 'selected' : '') ?>>
                                                    Feet</option>
                                            </select>
                                            <label class="form-lable" id="Label">Size<sup>*</sup></label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <input id="damageAreaHeight" class="height" name="damageAreaHeight" value=""
                                                type="number" onwheel="this.blur()" />
                                            <!-- <label class="form-lable" id="Label">Height ( in Centimeters
                                                )<sup>*</sup></label> -->
                                            <label class="form-lable" id="Label">Height</label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <select class="form-control" name="damageAreaHeightThicknessDimension"
                                                disabled>
                                                <option value="mm"
                                                    <?php echo ((strtolower($measurementType) == 'millimeter') ? 'selected' : '') ?>>
                                                    Millimeter</option>
                                                <option value="cm"
                                                    <?php echo ((strtolower($measurementType) == 'centimeter') ? 'selected' : '') ?>>
                                                    Centimeter</option>
                                                <option value="in"
                                                    <?php echo ((strtolower($measurementType) == 'inches') ? 'selected' : '') ?>>
                                                    Inches</option>
                                                <option value="ft"
                                                    <?php echo ((strtolower($measurementType) == 'feet') ? 'selected' : '') ?>>
                                                    Feet</option>
                                            </select>
                                            <label class="form-lable" id="Label">Size<sup>*</sup></label>
                                        </div>
                                    </div>
                                    <!-- <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-lable" id="Label">Comment</label>
                                            <input id="damageAreaComment" class="comment" name="damageAreaComment"
                                                value="" type="number" onwheel="this.blur()" />
                                        </div>
                                    </div> -->
                                </div>
                                <div class="cloneDamageForm"></div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
        <div class="widget box-3">
            <div class="actionArea">
                <span href="#" class="btn popUpBtn" id="addNewDamage"><i class="fi-rr-add"></i> Add new
                    damage</span>
                <span class="btn popUpBtn" id="updateDamage"><i class="fi-rr-check"></i> Update
                    damage</span>
                <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                    Cancel</span>
            </div>
        </div>
    </div>
</div>

<div class="step-open-box step_1_box full-width">
    <div class="form-header">
        <h3 style="float:left; width:100%; margin-bottom: 2rem">
            <?php echo $block_code . ' - ' . $block_title ?>
            <span>
                <?php
                    if ($balanceSlabs != 0) {
                        $slabText = ($balanceSlabs == 1) ? "slab" : "slabs";
                        echo "Add up to {$balanceSlabs} {$slabText}";
                    } else {
                        $sold = $block_total_item - $block_available;
                        $block_gross_size = str_replace(',', '', number_format($block_gross_size, 2));
                        $block_damage_size = str_replace(',', '', number_format($block_damage_size, 2));
                        $block_net_size = str_replace(',', '', number_format($block_net_size, 2));

                        $blockDimension = $block_size_dimension;

                        echo "<strong>Availability:</strong> "
                            . "<span style='color:#555;display:inline-block;width:auto;float:none;margin:0 8px;'>Total: {$block_total_item} " . ($block_total_item == 1 ? "slab" : "slabs") . "</span> | "
                            . "<span style='color:green;display:inline-block;width:auto;float:none;margin:0 8px;'>Available: {$block_available} " . ($block_available == 1 ? "slab" : "slabs") . "</span> | "
                            . "<span style='color:red;display:inline-block;width:auto;float:none;margin:0 8px;'>Sold: {$sold} " . ($sold == 1 ? "slab" : "slabs") . "</span>"
                            . "<br><br><strong>Quantity:</strong> "
                            . "<span style='color:#555;display:inline-block;width:auto;float:none;margin:0 8px;'>Gross Qty.: {$block_gross_size} {$blockDimension}</span> | "
                            . "<span style='color:#555;display:inline-block;width:auto;float:none;margin:0 8px;'>Net Qty.: {$block_net_size} {$blockDimension}</span> | "
                            . "<span style='color:orange;display:inline-block;width:auto;float:none;margin:0 8px;'>Less/Damage Qty.: {$block_damage_size} {$blockDimension}</span>";
                    }
                    ?>
            </span>
        </h3>
    </div>
</div>

<?php

    if ($balanceSlabs == 0) { ?>

<!-- Show select all btn -->

<section class="actionStickySection">
    <div class="col-4">
        <button id="selectAllBtn">Select All</button>
        <button id="unSelectAllBtn">Unselect All</button>
        <a href='/download/?downloadUrl={$block_qr}' class='btn btn-green date-btn bgYellow btn-with-icon textGrey'
            target='_blank' title='Show QR Code' data-title='Download QR code' target='_blank'><i
                class='fi fi-rr-qr-scan bgWhite'></i></a>
        <a href='/view_report/?id=" . base64_encode($block_id) . "' title='Print block'
            data-title='Print block inventory report' class='btn btn-green date-btn bgYellow btn-with-icon textGrey'
            target='_blank' target='_blank'><i class='fi fi-rr-inventory-alt bgWhite'></i></a>
        <a href='/print_label/?id=" . base64_encode($block_id) . "&theme=2' title='Print block'
            data-title='Print block label' class='btn btn-green date-btn bgYellow btn-with-icon textGrey'
            target='_blank' target='_blank'><i class='fi fi-rr-print bgWhite'></i></a><a
            href='/dashboard/inventory/logs/?id=" . base64_encode($block_id) . "' title='Print block'
            data-title='Show Transfer history' class='btn btn-green date-btn bgYellow btn-with-icon textGrey'
            target='_blank' target='_blank'><i class='fi fi-rr-notebook bgWhite'></i></a>
        <a href='#' title='Mark as sold out' data-title='Mark as Sold Out'
            class='btn btn-green date-btn bgYellow btn-with-icon textGrey markAsSold' data-type='block'
            data-id='{$block_id}' target='_blank'><i class='fi fi-rr-sold-signal bgRed'></i></a>
        " . (($block_status == 1) ? "
        <a href='#' data-type='ims_marble/block' data-id='{$block_id}' title='Delete block' data-title='Delete block'
            class='btn btn-green date-btn bgYellow btn-with-icon textGrey deleteButton' data-reload='true'
            data-redirect='false' target='_blank'><i class='fi fi-rr-trash bgRed'></i></a>" : "") . "
    </div>
    <div class="col-3">
        <select class="selectJS warehouseTranfer form-control" style="width: 200px" name="newWarehouseId"
            id="newWarehouseId">
            <option value="select" selected>Select warehouse</option>
            <?php

                    if ($warehouseList = mysqli_query($connection, "SELECT warehouse_id, warehouse_title, warehouse_code, warehouse_title, company_title FROM " . TABLE_PREFIX . "warehouse INNER JOIN " . TABLE_PREFIX . "company ON warehouse_company_id=company_id WHERE warehouse_status=1 AND company_address_id!=0 AND warehouse_site_id=" . SITE_ID)) {
                        if (mysqli_num_rows($warehouseList) > 0) {
                            while ($row = mysqli_fetch_array($warehouseList)) {
                                echo "<option value='{$row['warehouse_id']}_{$row['warehouse_title']}' >{$row['warehouse_title']}</option>";
                            }
                        }
                    }

                    ?>
        </select>
    </div>
    <div class="col-3">
        <button id="transportSlabs" class="bgYellow right" style="margin-right: 0px;"><i
                class='fi fi-rr-calendar-shift-swap'></i> Transfer Block Warehouse</button>
    </div>
</section>

<!-- Show all slabs list -->
<div class="step-open-box step_1_box full-width">
    <table>
        <?php

                $blockItemIndex = '';

                if ($checkItems = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block_item WHERE block_item_parent_id='{$blockId}' AND block_item_status IN (1,3,5) AND block_item_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($checkItems) > 0) {
                        $i = 1;
                        while ($blockItem = mysqli_fetch_assoc($checkItems)) {
                            extract($blockItem);
                            if ($i == 1) {
                                echo "<script>var measurementType='" . strtolower($measurementType) . "'</script>";
                                $blockSizeCalculation = '';

                                switch ($block_item_dimension_type) {
                                    case 'mm':
                                        $blockSizeCalculation = 'Millimeter';
                                        break;
                                    case 'cm':
                                        $blockSizeCalculation = 'Centimeter';
                                        break;
                                    case 'in':
                                        $blockSizeCalculation = 'Inches';
                                        break;
                                    case 'mt':
                                        $blockSizeCalculation = 'Meter';
                                        break;
                                    case 'mt (t)':
                                        $blockSizeCalculation = 'Metric Ton';
                                        break;
                                    default:
                                        $blockSizeCalculation = 'Feet';
                                        break;
                                }
                            }
                            $blockItemIndex .= $block_item_id . ',';
                            echo "<script>
                        localStorage.setItem('blockItem_{$block_item_id}', '{$block_item_damage_area}');
                    </script>";

                            echo "
                        <tr style='" . (($block_item_status == 2) ? 'background: #f27474' : '') . "' >
                            <td style='width: 30px;line-height: 53px;text-align: center;'>
                                {$i}
                            </td>
                            ";

                            if (strpos($block_size_dimension, '_weight') !== false) {
                                // for weight related products

                                echo "
                            <td>
                            <td>
                                <div class='form-group'>
                                    <input name='width[{$block_item_id}]' class='widthValues' value='{$block_item_width}' type='number' " . (($block_item_status == 2) ? 'readonly' : '') . " />
                                    <label class='form-lable' id='Label'>Weight ( in {$blockSizeCalculation} )<sup>*</sup></label>
                                </div>
                            </td>
                                <div class='form-group' style='display: none' >
                                    <input name='height[{$block_item_id}]' class='heightValues' value='0' type='number' " . (($block_item_status == 2) ? 'readonly' : '') . " />
                                    <label class='form-lable' id='Label'>Height ( in {$blockSizeCalculation} )<sup>*</sup></label>
                                </div>
                        </tr>
                    ";
                            } else {

                                if ($block_size_dimension == 'mt (t)') {
                                    echo "
                                    <td>
                                        <div class='form-group'>
                                            <input name='width[{$block_item_id}]' class='widthValues' value='{$block_item_width}' type='number' " . (($block_item_status == 2) ? 'readonly' : '') . " />
                                            <label class='form-lable' id='Label'>Weight ( in {$blockSizeCalculation} )<sup>*</sup></label>
                                        </div>
                                    </td>";
                                } else {
                                    echo "
                                    <td>
                                        <div class='form-group'>
                                            <input name='width[{$block_item_id}]' class='widthValues' value='{$block_item_width}' type='number' " . (($block_item_status == 2) ? 'readonly' : '') . " />
                                            <label class='form-lable' id='Label'>Length ( in {$blockSizeCalculation} )<sup>*</sup></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class='form-group'>
                                            <input name='height[{$block_item_id}]' class='heightValues' value='{$block_item_height}' type='number' " . (($block_item_status == 2) ? 'readonly' : '') . " />
                                            <label class='form-lable' id='Label'>Height ( in {$blockSizeCalculation} )<sup>*</sup></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class='form-group'>
                                            " . (($block_item_damage_count != '') ?  $block_item_damage_count . ' Damages' : '') . "
                                        </div>
                                    </td>";
                                }

                                echo "<td>
                                <div class='actionBtn form-group' style='display:" . (($block_item_status == 2) ? 'none' : 'block') . "' >
                                    ";
                                if ($block_size_dimension != 'mt (t)') {
                                    echo "<a href='#' class='bgYellow " . (($block_item_status == 1) ? 'manageDamage' : 'viewDamage') . "' data-index='{$i}' data-id='{$block_item_id}'><i class='fi fi-rr-damage'></i></a>";
                                }
                                echo "
                                    " . (($block_item_status == 1) ? "
                                    <a href='#' class='bgRed deleteButton' data-type='ims_marble/item/' data-id='{$block_item_id}' data-reload='true' data-redirect='false'><i class='fi fi-rr-trash'></i></a><a href='#' title='Mark as sold out' class='bgRed markAsSold' data-type='item' data-id='{$block_item_id}' ><i class='fi fi-rr-sold-signal bgRed'></i></a>" : '') . "
                                </div>
                            </td>
                    ";
                            }

                            echo (($block_item_status == 1) ? "<td style='width:40px'>
                                <div class='form-check form-check-inline multipleSelect'>
                                    <input class='form-check-input slabCheckbox' type='checkbox' data-id='{$i}' name='slabs' value='{$block_item_id}'>
                                </div>
                            </td>
                        </tr>" : "");

                            $i++;
                        }
                    }
                }

                ?>
    </table>
    <div class="box-1">
        <div class="form-group">
            <input type="hidden" id="updateBlockId" name="blockId" value="<?php echo $block_id ?>">
            <input type="hidden" name="blockItemIndex" value="<?php echo rtrim($blockItemIndex, ',') ?>">
            <input type="button" class="btn btn-green hidden" id="updateBlockItem" value="Update block Slabs">
        </div>
    </div>
</div>
<?php
    } else { ?>
<!-- Show only clone option -->
<div class="step-open-box step_1_box full-width">
    <div class="box-1">
        <form>
            <div id="generateBlockRowBox">
                <div class="generateBlockRow">
                    <?php

                            if ($block_size_dimension == 'mt (t)') { ?>

                    <!-- For solid block or metric ton weight wise block -->

                    <div class="box-1">
                        <div class="form-group">
                            <input type="number" onwheel="this.blur()" class="form-control" name="slabLenght"
                                id="slabLenght" placeholder="10">
                            <label class="form-label">Weight ( <?php echo $measurementType ?> )<sup>*</sup></label>
                        </div>
                    </div>
                    <div class="box-1" style="display: none;">
                        <div class="form-group">
                            <input type="number" onwheel="this.blur()" class="form-control" name="slabHeight"
                                id="slabHeight" value="0" placeholder="200">
                            <label class="form-label">Height ( <?php echo $measurementType ?> )<sup>*</sup></label>
                        </div>
                    </div>
                    <div class="box-1" style="display: none;">
                        <div class="form-group">
                            <input type="number" value="1" onwheel="this.blur()" class="form-control" name="totalSlab"
                                id="totalSlab" placeholder="200">
                            <label class="form-label">Total slabs ( <?php echo $measurementType ?> )<sup>*</sup></label>
                        </div>
                    </div>
                    <?php
                            } else { ?>
                    <div class="box-3">
                        <div class="form-group">
                            <input type="number" onwheel="this.blur()" class="form-control" name="slabLenght"
                                id="slabLenght" placeholder="200">
                            <label class="form-label">Length ( <?php echo $measurementType ?> )<sup>*</sup></label>
                        </div>
                    </div>
                    <div class="box-3">
                        <div class="form-group">
                            <input type="number" onwheel="this.blur()" class="form-control" name="slabHeight"
                                id="slabHeight" placeholder="100">
                            <label class="form-label">Height ( <?php echo $measurementType ?> )<sup>*</sup></label>
                        </div>
                    </div>
                    <div class="box-4">
                        <div class="form-group">
                            <input type="number" onwheel="this.blur()" class="form-control" name="totalSlab"
                                id="totalSlab" placeholder="5">
                            <label class="form-label">Total slabs<sup>*</sup></label>
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-group">
                            <a href="#" class="btn cloneSlabForm" style="float:right"><i
                                    class="fi fi-rr-multiple"></i></a>
                        </div>
                    </div>
                    <?php
                            }

                            ?>
                </div>
            </div>
            <div class="form-group">
                <input type="hidden" name="blockId" id="blockId" value="<?php echo $block_id ?>">
                <input type="hidden" name="blockTotalCount" id="blockTotalCount"
                    value="<?php echo $block_total_item ?>">
                <input type="button" class="btn btn-green" id="createBlockItem" value="Generate block Slabs">
            </div>
        </form>
    </div>
</div>
<?php
    }
} else { ?>

<div class="step-open-box step_1_box">
    <div class="form-header">
        <h2>Invalid block request received</h2>
        <img src="<?php echo IMG_LIBRARY ?>storyset/missing.png">
    </div>

    <?php
}