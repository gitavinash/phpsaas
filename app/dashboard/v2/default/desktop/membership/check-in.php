<h2 class="heading">Check-in</h2>
<div class="main-box">
    <section>
        <form action="#" id="addNewInventoryForm" method="POST" enctype="multipart/form-data">
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
                                        while ($row = mysqli_fetch_array($warehouseList)) {
                                            echo "<option value='{$row['warehouse_id']}'>{$row['warehouse_title']}</option>";
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
                        <input id="mobile" name="mobile" placeholder="9999999999" type="number" autofocus required />
                        <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="visits" name="visits" value="1" type="number" required />
                        <label class="form-lable" id="Label">How many visits to redeem?<sup>*</sup></label>
                    </div>
                    <div class="box-1">
                        <div class="form-group">
                            <input type="button" class="btn btn-green" id="checkInMembership" value="Continue">
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </section>
</div>