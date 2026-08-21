<h2 class="heading">Add Expense</h2>
<div class="main-box">
    <section>
        <form action="#" id="addExpenseForm" method="POST" enctype="multipart/form-data">
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
                    <div class="form-group" id="timeGroup" style="display:none;">
                        <label class="form-lable">Time <sup>*</sup></label>
                        <input id="time" name="time" type="time" value="<?php echo date('H:i'); ?>" />
                    </div>
                    <div class="form-group">
                        <label class="form-lable">Payment Mode <sup>*</sup></label>
                        <select id="payment_mode" name="payment_mode" class="form-control" required>
                            <option value="">Select payment mode</option>
                            <option value="cash" selected>Cash</option>
                            <option value="upi">UPI</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-lable">Upload Photo (optional)</label>
                        <input type="file" id="photo" name="photo" accept="image/*" />
                        <div id="photoPreview" style="margin-top:10px;"></div>
                    </div>
                    <div class="form-group">
                        <input type="button" class="btn btn-green" id="addExpense" value="Add new expense" />
                    </div>
                    <div id="expenseMsg" style="margin-top:10px;font-size:14px;"></div>
                </div>
            </section>
        </form>
    </section>
</div>
<script>
// Photo preview
document.getElementById('photo').addEventListener('change', function(e) {
    const preview = document.getElementById('photoPreview');
    preview.innerHTML = '';
    const file = e.target.files[0];
    if (file) {
        const img = document.createElement('img');
        img.style.maxWidth = '180px';
        img.style.maxHeight = '180px';
        img.style.border = '1px solid #ccc';
        img.style.borderRadius = '8px';
        img.src = URL.createObjectURL(file);
        preview.appendChild(img);
    }
});

// Date logic: show time if not today, disable future dates
const dateInput = document.getElementById('date');
const timeGroup = document.getElementById('timeGroup');
const timeInput = document.getElementById('time');
const today = new Date().toISOString().slice(0, 10);
dateInput.setAttribute('max', today);
dateInput.addEventListener('change', function() {
    if (dateInput.value && dateInput.value !== today) {
        timeGroup.style.display = '';
        timeInput.setAttribute('required', 'required');
        // Set default time to current time when shown
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        timeInput.value = hh + ':' + mm;
    } else {
        timeGroup.style.display = 'none';
        timeInput.removeAttribute('required');
    }
});
</script>