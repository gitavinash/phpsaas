<h2 class="heading"><?php echo 'Add New company' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Company details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="addNewCompanyForm" method="POST">
                <section class="form">

                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter company details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <input id="name" name="name" type="text" />
                            <label class="form-lable" id="Label">Company name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">About company</label>
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
                            <input id="pinCode" name="pinCode" type="number" onwheel="this.blur()" />
                            <label class="form-lable" id="Label">Pin code<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="email" name="email" type="email" />
                            <label class="form-lable" id="Label">Email<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="mobile" name="mobile" type="number" onwheel="this.blur()" />
                            <label class="form-lable" id="Label">Contact no.<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="companyRegistrationNumber" name="cin" type="text" />
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
                            <input id="companyCode" name="companyCode" type="text" />
                            <label class="form-lable" id="Label">Company Code or ID</label>
                        </div>
                        <div class="form-group">
                            <p>Company type<sup>*</sup></p>
                            <select class="selectJS form-control" name="companyType" id="companyType">
                                <option value="select">Select registration type</option>
                                <option value="private">Private Limited Company</option>
                                <option value="public">Public Limited Company</option>
                                <option value="partnerships">Partnerships</option>
                                <option value="llp">LLP Registration</option>
                                <option value="opc">One Person Company ( OPC )</option>
                                <option value="proprietorship">Sole Proprietorship</option>
                                <option value="section8">Section 8 Company</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <p><strong>Company feature image</strong></p>
                            <div id="dropZone">
                                <span class="defaultText">Drag & Drop files here or click to upload</span>
                            </div>
                            <input type="file" id="fileInput" name="featureImage[]" data-limit="1"
                                accept=".jpg, .jpeg, .png" style="display: none;" />
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="button" class="btn btn-green" id="addCompany" value="Add new company">
                            </div>
                        </div>
                    </div>

                </section>
            </form>
        </div>
    </section>
</div>