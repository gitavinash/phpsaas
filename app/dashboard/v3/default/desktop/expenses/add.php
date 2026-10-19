<h2 class="heading">Add Expense</h2>
<div class="main-box">
    <section>
        <form action="#" id="addExpenseForm" method="POST" enctype="multipart/form-data">
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
                        <label class="form-lable">Expense Amount <sup>*</sup></label>
                        <input id="amount" name="amount" type="number" step="0.01" min="0" required
                            placeholder="Enter amount" />
                    </div>
                    <div class="form-group">
                        <label class="form-lable">Expense Category <sup>*</sup></label>
                        <input id="category" name="category" type="text" required
                            placeholder="e.g. Stationery, Food, Maintenance" />
                    </div>
                    <div class="form-group">
                        <label class="form-lable">Description <sup>*</sup></label>
                        <textarea id="description" name="description" rows="2" required
                            placeholder="Describe the expense"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-lable">Date <sup>*</sup></label>
                        <input id="date" name="date" type="date" value="<?php echo date('Y-m-d'); ?>" required
                            max="<?php echo date('Y-m-d'); ?>" />
                    </div>
                    <div class="form-group" id="timeGroup">
                        <label class="form-lable">Time <sup>*</sup></label>
                        <input id="time" name="time" type="time" value="<?php echo date('H:i'); ?>" />
                    </div>
                    <div class="form-group">
                        <label class="form-lable">Payment Mode <sup>*</sup></label>
                        <select id="payment_mode" name="payment_mode" class="selectJS2 form-control" required>
                            <option value="">Select payment mode</option>
                            <option value="cash" selected>Cash</option>
                            <option value="upi">UPI</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-lable">Upload Photo (optional)</label>
                        <input type="file" id="photo" name="photo" accept="image/*" />
                        <div id="photoPreview" class="mt-10"></div>
                    </div>
                    <div class="form-group">
                        <input type="button" class="btn btn-green" id="addExpense" value="Add new expense" />
                    </div>
                    <div id="expenseMsg" class="mt-10 font-14"></div>
                </div>
            </section>
        </form>
    </section>
</div>