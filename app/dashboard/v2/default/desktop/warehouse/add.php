<?php $formName = ((SITE_APP == 'spa' ? 'Branch' : '<?php echo $formName ?>')) ?>
<h2 class="heading">Add New <?php echo $formName ?></span>
</h2>
<div class="main-box">
    <section>
        <?php

        if (SITE_VERSION != 'mobile') { ?>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span><?php echo $formName ?> details</li>
        </ul>
        <?php
        }

        ?>
        <div class="steps-box">
            <form action="#" id="addNew<?php echo $formName ?>Form" method="POST">
                <section class="form">

                    <div class="step-open-box step_1_box">
                        <?php

                        if (SITE_VERSION != 'mobile') { ?>

                        <div class="form-header">
                            <h1>Enter <?php echo $formName ?> details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                        </div>

                        <?php
                        }

                        ?>
                        <div class="form-group">
                            <p><?php echo $formName ?> company</p>
                            <select class="selectJS form-control" name="companyId" id="companyId">
                                <option value="select">Select company</option>
                                <?php

                                if ($compnayList = mysqli_query($connection, "SELECT company_id, company_code, company_title, city_name, state_name FROM " . TABLE_PREFIX . "company INNER JOIN " . TABLE_PREFIX . "address ON company_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id WHERE company_status=1 AND company_site_id=" . SITE_ID)) {
                                    if (mysqli_num_rows($compnayList) > 0) {
                                        while ($row = mysqli_fetch_array($compnayList)) {
                                            echo "<option value='{$row['company_id']}'>{$row['company_title']}</option>";
                                        }
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input id="name" name="name" type="text" />
                            <label class="form-lable" id="Label"><?php echo $formName ?> name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="<?php echo $formName ?>Code" name="<?php echo $formName ?>Code" type="text" />
                            <label class="form-lable" id="Label"><?php echo $formName ?> Code or ID</label>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">About <?php echo $formName ?></label>
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
                            <input id="gst" name="gst" type="text" />
                            <label class="form-lable" id="Label">GST Number</label>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="button" class="btn btn-green" id="add<?php echo $formName ?>"
                                    value="Add new <?php echo $formName ?>">
                            </div>
                        </div>
                    </div>

                </section>
            </form>
        </div>
    </section>
</div>