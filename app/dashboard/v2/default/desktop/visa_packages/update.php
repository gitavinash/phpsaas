<?php
ob_start();
if (!isset($id)) { ?>
<script type="text/javascript">
alert('Invalid request');
window.location.href = '/dashboard/visa_packages/';
</script>
<?php
    exit();
}

$countryId = base64_decode($id);

if ($checkCountry = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}package WHERE visa_package_id='{$countryId}' AND visa_package_status='1' AND visa_package_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkCountry) != 0) {
        extract(mysqli_fetch_assoc($checkCountry));
    } else { ?>
<script type="text/javascript">
alert('No package details found');
window.location.href = '/dashboard/country/list/';
</script>
<?php
        exit();
    }
} else {
    echo "<script>alert('Error fetching country data');</script>";
    exit();
}

?>
<h2 class="heading"><?php echo 'Update VISA package' ?><span>Country that we are offering VISA services</span></h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Visa package details</li>
        </ul>
        <div class="steps-box">
            <form action="#" method="POST" id="addNewForm">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Update package details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hosting.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <p>VISA country<sup>*</sup></p>
                            <select class="selectJS form-control" name="country" id="country">
                                <option value="">Select country</option>
                                <?php

                                if ($checkCountry = mysqli_query($connection, "SELECT visa_country_id, visa_country_code, visa_country_title, visa_country_services FROM {$tableNamePrefix}country WHERE visa_country_status=1 AND visa_country_site_id=" . SITE_ID . " ORDER BY visa_country_title ASC")) {
                                    while ($checkCountryData = mysqli_fetch_assoc($checkCountry)) {
                                        echo "<option value='{$checkCountryData['visa_country_id']}' " . (($visa_package_country_id == $checkCountryData['visa_country_id']) ? 'selected' :
                                            '') . ">{$checkCountryData['visa_country_title']}</option>";
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input id="title" name="title" type="text" value="<?php echo $visa_package_title ?>" />
                            <label class="form-lable">Title<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="promoText" name="promoText" type="text"
                                value="<?php echo $visa_package_promo_text ?>" />
                            <label class="form-lable">Promo text<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="appointment" name="appointment" type="text"
                                value="<?php echo $visa_package_appointment_time ?>" />
                            <label class="form-lable">Appointment gurantee ( in Hours )<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p>VISA type<sup>*</sup></p>
                            <select class="selectJS form-control" name="visaType" id="visaType">
                                <option value="">Select type</option>
                                <option value='tourist'
                                    <?php echo (($visa_package_visa_type == 'tourist') ? 'selected' : '') ?>>Tourist
                                </option>
                                <option value='business'
                                    <?php echo (($visa_package_visa_type == 'business') ? 'selected' : '') ?>>Business
                                </option>
                                <option value='transit'
                                    <?php echo (($visa_package_visa_type == 'transit') ? 'selected' : '') ?>>Transit
                                </option>
                                <option value='student'
                                    <?php echo (($visa_package_visa_type == 'student') ? 'selected' : '') ?>>Student
                                </option>
                                <option value='renewal'
                                    <?php echo (($visa_package_visa_type == 'renewal') ? 'selected' : '') ?>>Renewal
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input id="stayUpto" name="stayUpto" type="number" placeholder="30"
                                value="<?php echo $visa_package_stay_duration ?>" />
                            <label class="form-lable">Stay upto ( In number of Days )<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="validity" name="validity" type="number" placeholder="90"
                                value="<?php echo $visa_package_validity_duration ?>" />
                            <label class="form-lable">Validity upto ( In number of Days )<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p>Entry type<sup>*</sup></p>
                            <select class="selectJS form-control" name="entryType" id="entryType">
                                <option value="">Select type</option>
                                <option value="single"
                                    <?php echo (($visa_package_entry_type == 'single') ? 'selected' : '') ?>>Single
                                </option>
                                <option value="multiple"
                                    <?php echo (($visa_package_entry_type == 'multiple') ? 'selected' : '') ?>>Multiple
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-lable">Plan Disclaimer</label>
                            <div class="editableBox" id="planDisclaimer" contenteditable="true">
                                <?php echo $visa_package_disclaimer ?></div>
                        </div>
                        <div class="form-group">
                            <label class="form-lable">Visa Schedule (Processing Time)</label>
                            <div class="editableBox" id="visaProcessingTime" contenteditable="true">
                                <?php echo $visa_package_schedule ?></div>
                        </div>
                        <div class="form-group">
                            <label class="form-lable">Visa Schedule (Application Centers)</label>
                            <div class="editableBox" id="visaApplicationCenters" contenteditable="true">
                                <?php echo $visa_package_center ?></div>
                        </div>
                        <div class="form-group">
                            <input id="priceMrp" name="price_mrp" type="number" placeholder="90"
                                value="<?php echo $visa_package_mrp ?>" />
                            <label class="form-lable">Price ( without Discount )<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="price" name="price" type="text" placeholder="90"
                                value="<?php echo $visa_package_price ?>" />
                            <label class="form-lable">Price ( with Discount )<sup>*</sup></label>
                        </div>
                        <div class="form-group-box">
                            <p>Mark featured<sup>*</sup></p>
                            <div class="box-5 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="radio" id="isFeatured1" name="isFeatured" value="1"
                                            <?php echo (($visa_packages_is_featured == 1) ? 'checked' : '') ?> />
                                        <label class="radio-button" for="visasType1Label">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="box-5 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="radio" id="isFeatured2" name="isFeatured" value="0"
                                            <?php echo (($visa_packages_is_featured == 0) ? 'checked' : '') ?> />
                                        <label class="radio-button" for="visasType2Label">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <p><strong>Country feature image<sup>*</sup></strong></p>
                            <div id="dropZone">
                                <span class="defaultText">Drag & Drop files here or click to upload</span>
                            </div>
                            <input type="file" id="fileInput" name="featureImage[]" data-limit="1"
                                accept=".jpg, .jpeg, .png" style="display: none;" />
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <a href="#" class="btn popUpBtn" id="addNewPackage"><i class="fi-rr-check"></i> <span
                                        id="btnText">Add package</span>
                                    <span id="loadingSpinner" style="display:none;"> <i
                                            class="fi-rr-spinner fi-spin"></i> </span></a>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>