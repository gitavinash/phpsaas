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

if ($checkInventory = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block WHERE block_id='{$blockId}' AND block_status=1 AND block_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkInventory) > 0) {
        $validId = true;
        extract(mysqli_fetch_assoc($checkInventory));
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
                                                <input id="damageAreaWidth" class="width" name="damageAreaWidth" value="" type="number" />
                                                <!-- <label class="form-lable" id="Label">Width ( in Centimeters
                                                )<sup>*</sup></label> -->
                                                <label class="form-lable" id="Label">Length</label>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <select class="form-control" name="damageAreaWidthThicknessDimension">
                                                    <option value="mm">Millimeter</option>
                                                    <option value="cm" selected>Centimeter</option>
                                                    <option value="in">Inches</option>
                                                    <option value="ft">Feet</option>
                                                </select>
                                                <label class="form-lable" id="Label">Size<sup>*</sup></label>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <input id="damageAreaHeight" class="height" name="damageAreaHeight" value="" type="number" />
                                                <!-- <label class="form-lable" id="Label">Height ( in Centimeters
                                                )<sup>*</sup></label> -->
                                                <label class="form-lable" id="Label">Height</label>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <select class="form-control" name="damageAreaHeightThicknessDimension">
                                                    <option value="mm">Millimeter</option>
                                                    <option value="cm" selected>Centimeter</option>
                                                    <option value="in">Inches</option>
                                                    <option value="ft">Feet</option>
                                                </select>
                                                <label class="form-lable" id="Label">Size<sup>*</sup></label>
                                            </div>
                                        </div>
                                        <!-- <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-lable" id="Label">Comment</label>
                                            <input id="damageAreaComment" class="comment" name="damageAreaComment"
                                                value="" type="number" />
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

    <div class="step-open-box step_1_box">
        <div class="form-header">
            <h1>Manage block Slabs</h1>
            <img src="<?php echo IMG_LIBRARY ?>storyset/manage.png" alt="Add new Hosting tld">
        </div>
    </div>

    <div class="step-open-box step_1_box full-width">
        <table>
            <?php

            $blockItemIndex = '';

            if ($checkItems = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block_item WHERE block_item_parent_id='{$blockId}' AND block_item_status!=3 AND block_item_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($checkItems) > 0) {
                    $i = 1;
                    while ($blockItem = mysqli_fetch_assoc($checkItems)) {
                        extract($blockItem);
                        if ($i == 1) {
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
                        <tr>
                            <td>
                                <div class='form-group' >
                                    {$i}
                                </div>
                            </td>
                            <td>
                                <div class='form-group'>
                                    <input name='width[{$block_item_id}]' class='widthValues' value='{$block_item_width}' type='number' />
                                    <label class='form-lable' id='Label'>Length ( in {$blockSizeCalculation} )<sup>*</sup></label>
                                </div>
                            </td>
                            <td>
                                <div class='form-group'>
                                    <input name='height[{$block_item_id}]' class='heightValues' value='{$block_item_height}' type='number' />
                                    <label class='form-lable' id='Label'>Height ( in {$blockSizeCalculation} )<sup>*</sup></label>
                                </div>
                            </td>
                            <td>
                                <div class='form-group'>
                                    " . (($block_item_damage_count != '') ?  $block_item_damage_count . ' Damages' : '') . "
                                </div>
                            </td>
                            <td>
                                <div class='actionBtn form-group'>
                                    <a href='#' class='bgYellow manageDamage' data-id='{$block_item_id}'><i class='fi fi-rr-damage'></i></a>
                                    <a href='#' class='bgRed deleteButton' data-type='ims_marble/item/' data-id='{$block_item_id}' data-reload='true' data-redirect='false'><i class='fi fi-rr-trash'></i></a>
                                </div>
                            </td>
                        </tr>
                    ";
                        $i++;
                    }
                }
            }

            ?>
        </table>
        <div class="box-1">
            <div class="form-group">
                <input type="hidden" name="blockId" value="<?php echo $block_id ?>">
                <input type="hidden" name="blockItemIndex" value="<?php echo rtrim($blockItemIndex, ',') ?>">
                <input type="submit" class="btn btn-green" id="updateBlockItem" value="Update block Slabs">
            </div>
        </div>
    </div>
<?php
} else { ?>

    <div class="step-open-box step_1_box">
        <div class="form-header">
            <h2>Invalid block request received</h2>
            <img src="<?php echo IMG_LIBRARY ?>storyset/missing.png">
        </div>

    <?php
}
