<h2 class="heading"><?php echo 'Add New supplier' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Supplier details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="addNewSupplierForm" method="POST">
                <section class="form">

                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter supplier details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <input id="name" name="name" type="text" />
                            <label class="form-lable" id="Label">Supplier name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">About supplier</label>
                        </div>
                        <div class="form-group">
                            <input id="address" name="address" type="text" />
                            <label class="form-lable" id="Label">Address<sup>*</sup></label>
                        </div>
                        <div class="form-group" id="stateDropDown">
                            <p>State<sup>*</sup></p>
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
                            <p>City<sup>*</sup></p>
                            <select class="selectJS form-control" name="city" id="city">
                            </select>
                        </div>
                        <div class="form-group">
                            <input id="pinCode" name="pinCode" type="number" />
                            <label class="form-lable" id="Label">Pin code<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="email" name="email" type="email" />
                            <label class="form-lable" id="Label">Email<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="mobile" name="mobile" type="number" />
                            <label class="form-lable" id="Label">Contact no.<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="supplierRegistrationNumber" name="cin" type="text" />
                            <label class="form-lable" id="Label">Registration no. ( CIN )</label>
                        </div>
                        <div class="form-group">
                            <input id="gstNumber" name="gst" type="text" />
                            <label class="form-lable" id="Label">GST Number</label>
                        </div>
                        <div class="form-group">
                            <input id="apnNumber" name="pan" type="text" />
                            <label class="form-lable" id="Label">PAN Number</label>
                        </div>
                        <div class="form-group">
                            <input id="supplierCode" name="supplierCode" type="text" />
                            <label class="form-lable" id="Label">Supplier Code or ID</label>
                        </div>
                        <div class="form-group">
                            <p>Company type</p>
                            <select class="selectJS form-control" name="supplierType" id="supplierType">
                                <option value="select">Select registration type</option>
                                <option value="private">Private Limited Supplier</option>
                                <option value="public">Public Limited Supplier</option>
                                <option value="partnerships">Partnerships</option>
                                <option value="llp">LLP Registration</option>
                                <option value="opc">One Person Supplier ( OPC )</option>
                                <option value="proprietorship">Sole Proprietorship</option>
                                <option value="section8">Section 8 Supplier</option>
                            </select>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="submit" class="btn btn-green" id="addSupplier" value="Add new supplier">
                            </div>
                        </div>
                    </div>

                </section>
            </form>
        </div>
    </section>
</div>