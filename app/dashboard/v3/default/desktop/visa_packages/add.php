<h2 class="heading"><?php echo 'Add New VISA package' ?><span>Country that we are offering VISA services</span></h2>
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
                            <h1>Enter package details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hosting.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <p>VISA country<sup>*</sup></p>
                            <select class="selectJS2 form-control" name="country" id="country">
                                <option value="">Select country</option>
                                <?php

                                if ($checkCountry = mysqli_query($connection, "SELECT visa_country_id, visa_country_code, visa_country_title, visa_country_services FROM {$tableNamePrefix}country WHERE visa_country_status=1 AND visa_country_site_id=" . SITE_ID . " ORDER BY visa_country_title ASC")) {
                                    while ($checkCountryData = mysqli_fetch_assoc($checkCountry)) {
                                        echo "<option value='{$checkCountryData['visa_country_id']}'>{$checkCountryData['visa_country_title']}</option>";
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input id="title" name="title" type="text" />
                            <label class="form-lable">Title<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="promoText" name="promoText" type="text"
                                value="Get your visa appointment date picked within 3 working days or get a full refund." />
                            <label class="form-lable">Promo text<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="appointment" name="appointment" type="text" value="72" />
                            <label class="form-lable">Appointment gurantee ( in Hours )<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p>VISA type<sup>*</sup></p>
                            <select class="selectJS2 form-control" name="visaType" id="visaType">
                                <option value="">Select type</option>
                                <option value='tourist' selected>Tourist</option>
                                <option value='business'>Business</option>
                                <option value='transit'>Transit</option>
                                <option value='student'>Student</option>
                                <option value='renewal'>Renewal</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input id="stayUpto" name="stayUpto" type="number" placeholder="30" />
                            <label class="form-lable">Stay upto ( In number of Days )<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="validity" name="validity" type="number" placeholder="90" />
                            <label class="form-lable">Validity upto ( In number of Days )<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p>Entry type<sup>*</sup></p>
                            <select class="selectJS2 form-control" name="entryType" id="entryType">
                                <option value="">Select type</option>
                                <option value="single">Single</option>
                                <option value="multiple">Multiple</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-lable">Plan Disclaimer</label>
                            <div class="editableBox" id="planDisclaimer" contenteditable="true"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-lable">Visa Schedule (Processing Time)</label>
                            <div class="editableBox" id="visaProcessingTime" contenteditable="true"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-lable">Visa Schedule (Application Centers)</label>
                            <div class="editableBox" id="visaApplicationCenters" contenteditable="true"></div>
                        </div>
                        <div class="form-group">
                            <input id="govermentFees" name="goverment_fees" type="number" placeholder="90" />
                            <label class="form-lable">Goverment VISA fees<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="priceMrp" name="price_mrp" type="number" placeholder="90" />
                            <label class="form-lable">Service Charge MRP ( without Discount )<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="price" name="price" type="text" placeholder="90" />
                            <label class="form-lable">Service Charges Price ( with Discount )<sup>*</sup></label>
                        </div>
                        <div class="form-group-box">
                            <p>Mark featured<sup>*</sup></p>
                            <div class="box-5 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="radio" id="isFeatured1" name="isFeatured" value="1" />
                                        <label class="radio-button" for="visasType1Label">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="box-5 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="radio" id="isFeatured2" name="isFeatured" value="0" checked />
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
                                accept=".jpg, .jpeg, .png" />
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <a href="#" class="btn popUpBtn" id="addNewPackage"><i class="fi-rr-check"></i> <span
                                        id="btnText">Add package</span>
                                    <span id="loadingSpinner"> <i
                                            class="fi-rr-spinner fi-spin"></i> </span></a>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>