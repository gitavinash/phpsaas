<section class="form">
    <div class="step-open-box step_1_box">
        <div class="form-header">
            <h1>Enter lead details</h1>
            <img src="<?php echo IMG_LIBRARY ?>storyset/hosting.png" alt="Add new Hosting tld">
        </div>
        <div class="form-group">
            <?php

            if (!isset($loggedInUserMeta['warehouse'])) {
                $loggedInUserMeta = array();
                $loggedInUserMeta['warehouse'] = 'all';
            }

            if ($loggedInUserMeta['warehouse'] == 'all') { ?>
            <select class="selectJS form-control" name="warehouse" id="warehouse" required>
                <option value="">Select warehouse</option>
                <?php
                    $warehouseFilter = '';
                    // if ($loggedInUserMeta['warehouse'] != 'all') {
                    //     $warehouseFilter = "warehouse_id IN ({$loggedInUserMeta['warehouse']}) AND ";
                    // }

                    if ($warehouseList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "warehouse WHERE warehouse_status=1 AND {$warehouseFilter} warehouse_site_id=" . SITE_ID)) {
                        if (mysqli_num_rows($warehouseList) != 0) {
                            while ($warehouseData = mysqli_fetch_assoc($warehouseList)) {
                                extract($warehouseData);
                                echo "<option value='{$warehouse_id}'>{$warehouse_title}</option>";
                            }
                        }
                    }
                    ?>
            </select>
            <label for="">Select warehouse</label>
            <?php
            } else {
                echo "<input type='hidden' name='warehouse' value='{$loggedInUserMeta['warehouse']}'>";
            }

            ?>
        </div>
        <div class="form-group">
            <input id="firstName" name="name" type="text" required />
            <label class="form-lable" id="Label">Full name<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="email" name="email" type="email" />
            <label class="form-lable" id="Label">Email</label>
        </div>
        <div class="form-group">
            <input id="mobile" name="mobile" type="text" required />
            <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
        </div>
        <div class="form-group">
            <textarea name="address" id="requirements" cols="30" rows="10"></textarea>
            <label class="form-lable" id="Label">Address<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="mobile" name="architecture" type="text" required />
            <label class="form-lable" id="Label">Architecture name<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="mobile" name="constrictctor" type="text" required />
            <label class="form-lable" id="Label">Constrictctor name<sup>*</sup></label>
        </div>
        <div class="form-group">
            <textarea name="requirements" id="requirements" cols="30" rows="10"></textarea>
            <label class="form-lable" id="Label">Requirements and details</label>
        </div>
        <div class="box-1">
            <div class="form-group">
                <!-- <input type="submit" class="btn btn-green" id="addEnquiry" value="Add new lead"> -->
                <input type="hidden" name="type" value="enquiry">
                <input type="hidden" name="action" value="new">
                <input type="hidden" name="subject" value="Marble request">
                <input type="hidden" name="sender" value="<?php echo base64_encode("aviansh@blumox.com") ?>">
                <input type="hidden" name="redirect_url" value="/dashboard/leads/list/">
                <input type="submit" class="btn btn-green" value="Add lead">
            </div>
        </div>
    </div>
</section>