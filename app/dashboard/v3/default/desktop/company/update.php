<?php

if (!isset($id)) { ?>
    <script>
        window.location.href = '/dashboard/company/list/';
    </script>
<?php
} else if ($id == '') { ?>
    <script>
        window.location.href = '/dashboard/company/list/';
    </script>
<?php
}

$companyId = base64_decode($id);

if ($checkCompany = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "company INNER JOIN " . TABLE_PREFIX . "address ON company_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "state ON city_state_id=state_id WHERE company_id='{$companyId}'")) {
    if (mysqli_num_rows($checkCompany) > 0) {
        extract(mysqli_fetch_assoc($checkCompany));
    }
}

?>
<h2 class="heading"><?php echo 'Update company details' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Company details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="updateCompanyForm" method="POST">
                <section class="form">

                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Update company details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="">
                        </div>
                        <div class="form-group">
                            <input id="name" name="name" value="<?php echo $company_title ?>" type="text" />
                            <label class="form-lable" id="Label">Company name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30"
                                rows="10"><?php echo $company_description ?></textarea>
                            <label class="form-lable" id="Label">About company</label>
                        </div>
                        <div class="form-group">
                            <input id="address" name="address" value="<?php echo $address_area ?>" type="text" />
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
                                            echo "<option value='{$state['state_id']}' " . ((strtolower($state['state_name']) == strtolower($state_name)) ? 'selected' : '') . " >{$state['state_name']}</option>";
                                        }
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="form-group" id="cityBox">
                            <p>City<sup>*</sup></p>
                            <select class="selectJS form-control" name="city" id="city">
                                <option value="select">Select city</option>
                                <?php

                                if ($cityList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "city WHERE city_state_id={$address_state_id} AND city_status=1")) {
                                    if (mysqli_num_rows($cityList) > 0) {
                                        while ($city = mysqli_fetch_assoc($cityList)) {
                                            echo "<option value='{$city['city_id']}' " . ((strtolower($city['city_name']) == strtolower($city_name)) ? 'selected' : '') . " >{$city['city_name']}</option>";
                                        }
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input id="pinCode" name="pinCode" value="<?php echo $address_pin_code ?>" type="number"
                                onwheel="this.blur()" />
                            <label class="form-lable" id="Label">Pin code<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="email" name="email" value="<?php echo $company_email ?>" type="email" />
                            <label class="form-lable" id="Label">Email<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="mobile" name="mobile" value="<?php echo $company_mobile ?>" type="number"
                                onwheel="this.blur()" />
                            <label class="form-lable" id="Label">Contact no.<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="companyRegistrationNumber" value="<?php echo $company_cin ?>" name="cin"
                                type="text" />
                            <label class="form-lable" id="Label">Registration no. ( CIN )</label>
                        </div>
                        <div class="form-group">
                            <input id="gstNumber" name="gst" value="<?php echo $company_gst ?>" type="text" />
                            <label class="form-lable" id="Label">GST Number</label>
                        </div>
                        <div class="form-group">
                            <input id="apnNumber" name="pan" value="<?php echo $company_pan ?>" type="text" />
                            <label class="form-lable" id="Label">PAN Number</label>
                        </div>
                        <div class="form-group">
                            <input id="companyCode" name="companyCode" value="<?php echo $company_code ?>"
                                type="text" />
                            <label class="form-lable" id="Label">Company Code or ID</label>
                        </div>
                        <div class="form-group">
                            <p>Company type</p>
                            <select class="selectJS form-control" name="companyType" id="companyType">
                                <option value="select">Select registration type</option>
                                <option value="private" <?php echo (($company_type == 'private') ? 'selected' : '') ?>>
                                    Private Limited Company</option>
                                <option value="public" <?php echo (($company_type == 'public') ? 'selected' : '') ?>>
                                    Public Limited Company</option>
                                <option value="partnerships"
                                    <?php echo (($company_type == 'partnerships') ? 'selected' : '') ?>>Partnerships
                                </option>
                                <option value="llp" <?php echo (($company_type == 'llp') ? 'selected' : '') ?>>LLP
                                    Registration</option>
                                <option value="opc" <?php echo (($company_type == 'opc') ? 'selected' : '') ?>>One
                                    Person
                                    Company ( OPC )</option>
                                <option value="proprietorship"
                                    <?php echo (($company_type == 'proprietorship') ? 'selected' : '') ?>>Sole
                                    Proprietorship</option>
                                <option value="section8"
                                    <?php echo (($company_type == 'section8') ? 'selected' : '') ?>>
                                    Section 8 Company</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <span style="display: none;" id="oldGallery"><?php echo $company_logo ?></span>
                            <p><strong>Company feature image</strong></p>
                            <div id="dropZone">
                                <span class="defaultText">Drag & Drop files here or click to upload</span>
                            </div>
                            <input type="file" id="fileInput" data-limit="1" name="featureImage[]"
                                accept=".jpg, .jpeg, .png" />
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="hidden" name="companyId" value="<?php echo $companyId ?>">
                                <input type="button" class="btn btn-green" id="updateCompany" value="Update company">
                            </div>
                        </div>
                    </div>

                </section>
            </form>
        </div>
    </section>
</div>