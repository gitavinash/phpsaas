<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin_new.css">
<script src="<?php echo JS_LIBRARY ?>admin_new.js"></script>

<div class="modern-user-form">
    <div class="box-2">
        <h2>Add New Vendor</h2>
        <div class="row">
            <div class="category">
                <div class="form-box">
                    <form method="POST" autocomplete="off" id="addNewVendorForm">
                        <h3 class="form-title"><?php echo ((isset($_GET['id'])) ? 'Update' : 'Add New'); ?> Vendor</h3>
                        <div class="form-group">
                            <label>Vendor name<sup>*</sup></label>
                            <input type="text" name="name" id="name" placeholder="Vendor name"
                                class="form-control inside-shadow" autofocus required />
                        </div>
                        <div class="form-group">
                            <label>Email<sup>*</sup></label>
                            <input type="email" name="email" id="email" placeholder="Email"
                                class="form-control inside-shadow" required />
                        </div>
                        <div class="form-group">
                            <label>Contact No.<sup>*</sup></label>
                            <input type="number" name="mobile" id="mobile" placeholder="Contact No."
                                class="form-control inside-shadow" required />
                        </div>
                        <div class="form-group">
                            <label>About vendor</label>
                            <textarea name="description" class="form-control inside-shadow" id="description" placeholder="About vendor"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control inside-shadow" id="address" placeholder="Vendor address"></textarea>
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <select class="selectJS form-control" name="state" id="state">
                                <option value="select">Select state</option>
                                <?php

                                if ($stateList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "state WHERE state_country_id=101 AND state_status=1")) {
                                    if (mysqli_num_rows($stateList) > 0) {
                                        while ($state = mysqli_fetch_assoc($stateList)) {
                                            echo "<option value='{$state['state_id']}' >{$state['state_name']}</option>";
                                        }
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="form-group" id="cityBox" style="display: none">
                            <label>City</label>
                            <select class="selectJS form-control" name="city" id="city">
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pin code</label>
                            <input type="number" name="pinCode" id="pinCode" placeholder="Pin code"
                                class="form-control inside-shadow" />
                        </div>
                        <div class="form-group">
                            <label>Vendor registration number ( CIN )</label>
                            <input type="text" name="cin" id="cin" placeholder="Vendor registration number ( CIN )"
                                class="form-control inside-shadow" />
                        </div>
                        <div class="form-group">
                            <label>Goods and Services Tax ( GST ) </label>
                            <input type="text" name="gst" id="gst" placeholder="Goods and Services Tax ( GST )"
                                class="form-control inside-shadow" />
                        </div>
                        <div class="form-group">
                            <label>Vendor PAN</label>
                            <input type="text" name="pan" id="pan" placeholder="Vendor PAN"
                                class="form-control inside-shadow" />
                        </div>
                        <div class="form-group">
                            <label>Vendor Registration Type</label>
                            <select class="selectJS form-control" name="vendorType" id="vendorType">
                                <option value="select">Select registration type</option>
                                <option value="private">Private Limited Vendor</option>
                                <option value="public">Public Limited Vendor</option>
                                <option value="partnerships">Partnerships</option>
                                <option value="llp">LLP Registration</option>
                                <option value="opc">One Person Vendor ( OPC )</option>
                                <option value="proprietorship">Sole Proprietorship</option>
                                <option value="section8">Section 8 Vendor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="type" value="vendor">
                            <input type="hidden" name="action" value="publish">
                            <input type="submit" class="btn btn-green" value="Add new vendor">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="box-2">
    </div>
</div>