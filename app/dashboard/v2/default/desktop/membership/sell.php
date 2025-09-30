<h2 class="heading">Add new membership</h2>
<div class="main-box">
    <section>
        <form action="#" id="addNewMembershipForm" method="POST" enctype="multipart/form-data">
            <section class="form" style="padding-top:25px">
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
                        <select class="selectJS form-control" name="warehouseId" id="warehouseId">
                            <option value="select">Select warehouse</option>
                            <?php

                                if ($warehouseList = mysqli_query($connection, "SELECT warehouse_id, warehouse_title, warehouse_code, warehouse_title, company_title FROM " . TABLE_PREFIX . "warehouse INNER JOIN " . TABLE_PREFIX . "company ON warehouse_company_id=company_id WHERE warehouse_status=1 AND company_address_id!=0 AND warehouse_site_id=" . SITE_ID)) {
                                    if (mysqli_num_rows($warehouseList) > 0) {
                                        while ($row = mysqli_fetch_array($warehouseList)) {
                                            echo "<option value='{$row['warehouse_id']}'>{$row['warehouse_title']}</option>";
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
                        <input id="name" placeholder="Ajay gupta" name="name" type="text" required />
                        <label class="form-lable" id="Label">Customer name<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="mobile" name="mobile" placeholder="9999999999" type="number" required />
                        <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <label class="form-lable">Room type<sup>*</sup></label>
                        <select class="selectJS" style="float: left; width:100%" name="package" id="package">
                            <option value="">Select room type</option>
                            <?php

                            if ($roomType = mysqli_query($connection, "SELECT category_id, category_title FROM " . TABLE_PREFIX . "category WHERE category_status=1 AND category_site_id='" . SITE_ID . "' AND category_type='spa_room'")) {
                                if (mysqli_num_rows($roomType) != 0) {
                                    while ($room = mysqli_fetch_assoc($roomType)) {
                                        echo "<option value='{$room['category_id']}' " . (($room['category_title'] == 'deluxe') ? 'selected' : '') . ">{$room['category_title']}</option>";
                                    }
                                }
                            }

                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input id="price" placeholder="5000" name="price" type="number" required />
                        <label class="form-lable" id="Label">Price<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="dueAmount" value="0" name="dueAmount" type="number" required />
                        <label class="form-lable" id="Label">Any Due Amount?<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="visit" value="5" name="visit" type="number" required />
                        <label class="form-lable" id="Label">Total visit<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <textarea name="requirements" id="requirements" cols="30" rows="10"></textarea>
                        <label class="form-lable" id="Label">Any requirements</label>
                    </div>
                    <div class="box-1">
                        <div class="form-group">
                            <input type="button" class="btn btn-green" id="addMembership" value="Add new membership">
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </section>
</div>