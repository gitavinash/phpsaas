<h2 class="heading">Add new customer</h2>
<div class="main-box">
    <section>
        <form action="#" id="addNewSellForm" method="POST" enctype="multipart/form-data">
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
                            <option value="select">Select branch</option>
                            <?php

                                if ($warehouseList = mysqli_query($connection, "SELECT warehouse_id, warehouse_title, warehouse_code, warehouse_title, company_title FROM " . TABLE_PREFIX . "warehouse INNER JOIN " . TABLE_PREFIX . "company ON warehouse_company_id=company_id WHERE warehouse_status=1 AND company_address_id!=0 AND warehouse_site_id=" . SITE_ID)) {
                                    if (mysqli_num_rows($warehouseList) > 0) {
                                        $i = 0;
                                        while ($row = mysqli_fetch_array($warehouseList)) {
                                            echo "<option value='{$row['warehouse_id']}' " . (($i == 0) ? 'selected' : '') . ">{$row['warehouse_title']}</option>";
                                            $i++;
                                        }
                                    }
                                }

                                ?>
                        </select>
                        <label class="form-lable" id="Label">Branch<sup>*</sup></label>
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
                                        echo "<option value='{$room['category_id']}' " . (($room['category_title'] == 'deluxe') ? 'selected' : '') . ">" . ucwords($room['category_title']) . "</option>";
                                    }
                                }
                            }

                            ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:20px;">
                        <select class="selectJS form-control" id="paymentMode" name="paymentMode" required
                            style="width:100%;height:44px;border:1px solid #dfe3e8;border-radius:8px;padding:0 10px;font-size:16px;background:#fff;">
                            <option value="">Select mode</option>
                            <option value="cash" selected>Cash</option>
                            <option value="upi">UPI</option>
                            <option value="card">Card</option>
                            <option value="other">Other</option>
                        </select>
                        <label class="form-lable" id="paymentModeLabel">Payment Mode <sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="price" placeholder="5000" name="price" type="number" required />
                        <label class="form-lable" id="Label">Price ( In ₹ ) <sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <textarea name="requirements" id="requirements" cols="30" rows="10"></textarea>
                        <label class="form-lable" id="Label">Any requirements</label>
                    </div>
                    <div class="box-1">
                        <div class="form-group">
                            <input type="button" class="btn btn-green" id="addNewSell" value="Save record">
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </section>
</div>