<?php

$showForm = false;

if (isset($id)) {

    $membershipId = base64_decode($id);
    if ($checkMembership = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "membership INNER JOIN " . TABLE_PREFIX . "user ON user_id=membership_user_id WHERE membership_id='{$membershipId}' AND membership_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkMembership) != 0) {
            extract(mysqli_fetch_assoc($checkMembership));
            $showForm = true;
        }
    }
}

if ($showForm == true) { ?>
<h2 class="heading">Update membership</h2>
<div class="main-box">
    <section>
        <form action="#" id="updateMembershipForm" method="POST" enctype="multipart/form-data">
            <section class="form pt-25">
                <div class="step-open-box step_1_box">
                    <?php

                        if (isset($siteAccessMeta['warehouse'])) {
                            if ($siteAccessMeta['warehouse'] == 'all') {
                                $showWarehouse = 'all';
                            } else {
                                $showWarehouse = $siteAccessMeta['warehouse'];
                            }
                        } else {
                            $showWarehouse = 'all';
                        }

                        if ($showWarehouse == "all") {
                        ?>

                    <div class="form-group">
                        <select class="selectJS2 form-control" name="warehouseId" id="warehouseId">
                            <option value="select">Select branch</option>
                            <?php

                                    if ($warehouseList = mysqli_query($connection, "SELECT warehouse_id, warehouse_title, warehouse_code, warehouse_title, company_title FROM " . TABLE_PREFIX . "warehouse INNER JOIN " . TABLE_PREFIX . "company ON warehouse_company_id=company_id WHERE warehouse_status=1 AND company_address_id!=0 AND warehouse_site_id=" . SITE_ID)) {
                                        if (mysqli_num_rows($warehouseList) > 0) {
                                            while ($row = mysqli_fetch_array($warehouseList)) {
                                                echo "<option value='{$row['warehouse_id']}' " . (($row['warehouse_id'] == $membership_branch_id) ? 'selected' : '') . " >{$row['warehouse_title']}</option>";
                                            }
                                        }
                                    }

                                    ?>
                        </select>
                        <label class="form-lable" id="Label">Warehouse<sup>*</sup></label>
                    </div>

                    <?php
                        } else {
                            echo "<input type='hidden' name='warehouseId' value='{$showWarehouse}' id='warehouseId'>";
                        }

                        ?>
                    <div class="form-group">
                        <input id="name" placeholder="Ajay gupta" name="name" type="text"
                            value="<?php echo $membership_profile_name ?>" required />
                        <label class="form-lable" id="Label">Customer name<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="mobile" name="mobile" placeholder="9999999999" type="number"
                            value="<?php echo $user_mobile ?>" disabled />
                        <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <label class="form-lable">Room type<sup>*</sup></label>
                        <select class="selectJS2 form-control" name="package" id="package">
                            <option value="">Select room type</option>
                            <?php

                                if ($roomType = mysqli_query($connection, "SELECT category_id, category_title FROM " . TABLE_PREFIX . "category WHERE category_status=1 AND category_site_id='" . SITE_ID . "' AND category_type='spa_room'")) {
                                    if (mysqli_num_rows($roomType) != 0) {
                                        while ($room = mysqli_fetch_assoc($roomType)) {
                                            echo "<option value='{$room['category_id']}' " . (($room['category_id'] == $membership_type_id) ? 'selected' : '') . ">{$room['category_title']}</option>";
                                        }
                                    }
                                }

                                ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input id="price" placeholder="5000" name="price" type="number"
                            value="<?php echo $membership_price ?>" required />
                        <label class="form-lable" id="Label">Price<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="dueAmount" name="dueAmount" type="number"
                            value="<?php echo $membership_amount_due ?>" required />
                        <label class="form-lable" id="Label">Any Due Amount?<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="visit" name="visit" type="number" value="<?php echo $membership_total_visit ?>"
                            required />
                        <label class="form-lable" id="Label">Total visits<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="avlVisit" name="avl_visit" type="number" value="<?php echo $membership_avl_visit ?>"
                            required />
                        <label class="form-lable" id="Label">Available visits<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <textarea name="requirements" id="requirements" cols="30"
                            rows="10"><?php echo $membership_comment ?></textarea>
                        <label class="form-lable" id="Label">Any requirements</label>
                    </div>
                    <div class="box-1">
                        <div class="form-group">
                            <input type="hidden" name="membershipId" value="<?php echo $id ?>" id="membershipId">
                            <input type="button" class="btn btn-green" id="updateMembership" value="Update membership">
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </section>
</div>
<?php
}

?>