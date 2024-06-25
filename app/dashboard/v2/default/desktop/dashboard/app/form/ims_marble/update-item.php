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

if (isset($validId)) { ?>


<div class="popUp manageDamageBox">
    <div class="fullScreenPopUpBox">
        <div class="widget box-7">
            <div class="contentArea">
                <form method="POST" action="#">
                    <section class="form">
                        <div class="step-open-box step_1_box full-width">
                            <div class="form-header">
                                <h4 class="heading">Conclusion & Summary</h4>
                                <div class="full-width" id="damageForm">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <input id="damageAreaWidth" class="width" name="damageAreaWidth" value=""
                                                type="number" />
                                            <label class="form-lable" id="Label">Width ( in Centimeters
                                                )<sup>*</sup></label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <input id="damageAreaHeight" class="height" name="damageAreaHeight" value=""
                                                type="number" />
                                            <label class="form-lable" id="Label">Height ( in Centimeters
                                                )<sup>*</sup></label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <input id="damageAreaComment" class="comment" name="damageAreaComment"
                                                value="" type="text" />
                                            <label class="form-lable" id="Label">Comment</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="cloneDamageForm"></div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
        <div class="widget box-3">
            <div class="actionArea">
                <button href="#" class="btn popUpBtn" id="addNewDamage"><i class="fi-rr-add"></i> Add new
                    damage</button>
                <button href="#" class="btn popUpBtn" id="updateDamage"><i class="fi-rr-check"></i> Update
                    damage</button>
                <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                    Cancel</span>
            </div>
        </div>
    </div>
</div>

<div class="step-open-box step_1_box">
    <div class="form-header">
        <h1>Manage block items</h1>
        <img src="<?php echo IMG_LIBRARY ?>storyset/manage.png" alt="Add new Hosting tld">
    </div>
</div>

<div class="step-open-box step_1_box full-width">

    <?php

        if ($checkItems = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block_item WHERE block_item_parent_id='{$blockId}' AND block_item_status!=2 AND block_item_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkItems) > 0) {
                while ($blockItem = mysqli_fetch_assoc($checkItems)) {
                    extract($blockItem);
                    echo "<script>
                        localStorage.setItem('blockItem_{$block_item_id}', '{$block_item_damage_area}');
                    </script>";
                    echo "<div class='full-width'>
            <div class='col-3'>
                <div class='form-group'>
                    <input id='thickness' name='thickness' value='{$block_item_width}' type='number' />
    <label class='form-lable' id='Label'>Width ( in Feets )<sup>*</sup></label>
</div>
</div>
<div class='col-3'>
    <div class='form-group'>
        <input id='thickness' name='thickness' value='{$block_item_height}' type='number' />
    <label class='form-lable' id='Label'>Height ( in Feets )<sup>*</sup></label>
</div>
</div>
<div class='col-4'>
    <div class='actionBtn'>
        <a href='#' class='bgYellow manageDamage' data-id='{$block_item_id}'><i class='fi fi-rr-damage'></i></a>
        <a href='#' class='bgRed deleteBtn' data-type='ims_marble/item/' data-id='{$block_item_id}' data-redirect='false'><i
                class='fi fi-rr-trash'></i></a>
    </div>
</div>
</div>";
                }
            }
        }

        ?>

    <div class="box-1">
        <div class="form-group">
            <input type="hidden" name="blockId" value="<?php echo $block_id ?>">
            <input type="submit" class="btn btn-green" id="updateBlock" value="Manage block items">
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