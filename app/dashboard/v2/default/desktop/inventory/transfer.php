<h2 class="heading"><?php echo 'Update inventory details' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Select warehouse</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="blockTransferForm" method="POST" enctype="multipart/form-data">
                <section class="form">

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

                        <div class="step-open-box step_1_box">
                            <div class="form-header">
                                <h1>Transfer block</h1>
                                <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                            </div>
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
                                    <select class="form-control" name="warehouseId" id="warehouseId" disabled>
                                        <option value="select">Select location</option>
                                        <?php

                                        if ($warehouseList = mysqli_query($connection, "SELECT warehouse_id, warehouse_title, warehouse_code, warehouse_title, company_title FROM " . TABLE_PREFIX . "warehouse INNER JOIN " . TABLE_PREFIX . "company ON warehouse_company_id=company_id WHERE warehouse_status=1 AND company_address_id!=0 AND warehouse_site_id=" . SITE_ID)) {
                                            if (mysqli_num_rows($warehouseList) > 0) {
                                                while ($row = mysqli_fetch_array($warehouseList)) {
                                                    echo "<option value='{$row['warehouse_id']}'" . (($row['warehouse_id'] == $block_warehouse_id) ? 'selected' : '') . ">{$row['warehouse_title']}</option>";
                                                }
                                            }
                                        }

                                        ?>
                                    </select>
                                    <label class="form-lable" id="Label">Current location<sup>*</sup></label>
                                </div>

                                <div class="form-group">
                                    <select class="selectJS form-control" name="newWareHouseId" id="newWareHouseId">
                                        <option value="select">Select warehouse</option>
                                        <?php

                                        if ($warehouseList = mysqli_query($connection, "SELECT warehouse_id, warehouse_title, warehouse_code, warehouse_title, company_title FROM " . TABLE_PREFIX . "warehouse INNER JOIN " . TABLE_PREFIX . "company ON warehouse_company_id=company_id WHERE warehouse_status=1 AND company_address_id!=0 AND warehouse_id!={$block_warehouse_id} AND warehouse_site_id=" . SITE_ID)) {
                                            if (mysqli_num_rows($warehouseList) > 0) {
                                                while ($row = mysqli_fetch_array($warehouseList)) {
                                                    echo "<option value='{$row['warehouse_id']}'>{$row['warehouse_title']}</option>";
                                                }
                                            }
                                        }

                                        ?>
                                    </select>
                                    <label class="form-lable" id="Label">New Location<sup>*</sup></label>
                                </div>

                            <?php
                            } else {
                                echo "<input type='hidden' name='warehouseId' value='{$showWarehouse}' id='warehouseId'>";
                            }

                            ?>
                            <div class="form-group">
                                <input name="reason" id="reason" type="text" />
                                <label class="form-lable" id="Label">Reason<sup>*</sup></label>
                            </div>
                            <div class="form-group">
                                <textarea name="description" id="description" cols="30" rows="10"></textarea>
                                <label class="form-lable" id="Label">Comment</label>
                            </div>
                            <div class="box-1">
                                <div class="form-group">
                                    <input type="hidden" name="blockId" value="<?php echo $block_id ?>">
                                    <input type="hidden" name="currentWarehouse" value="<?php echo $block_warehouse_id ?>">
                                    <input type="button" class="btn btn-green" id="transferBlock" value="Send transfer request">
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
                        ?>

                </section>
            </form>
        </div>
    </section>
</div>