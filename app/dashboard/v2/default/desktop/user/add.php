<h2 class="heading">Add new Users<span>Allow access to new users</span></h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Create new user</li>
        </ul>
        <div class="steps-box">
            <form method="POST" action="#">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter user's details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/domain-tlds.png" alt="Add new domain tld">
                        </div>
                        <div class="form-group">
                            <input type="text" name="first_name" id="first_name" placeholder="First name*"
                                class="form-control" autofocus required />
                            <label class="form-lable" id="first_nameLabel">First name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="last_name" id="last_name" placeholder="Last name*"
                                class="form-control" required />
                            <label class="form-lable" id="last_nameLable">Last name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="mobile" id="mobile" placeholder="Mobile no*" class="form-control"
                                required />
                            <label class="form-lable">Mobile No.<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" id="email" placeholder="Email address*"
                                name="email" required />
                            <label class="form-lable">Email<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="password" placeholder="Password*"
                                name="password" required />
                            <label class="form-lable">Password<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="confirmPassword"
                                placeholder="Confirm Password*" name="confirm_password" required />
                            <label class="form-lable">Confirm password<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p>User role <sup>*</sup></p>
                            <select class="selectJS form-control" name="userRole" id="select">
                                <option value="">Select user role</option>
                                <?php
                                if ($roleList = mysqli_query($connection, "SELECT user_type_name, user_type_code FROM " . TABLE_PREFIX . "user_type WHERE user_type_app_id='" . SITE_APP_ID . "' AND user_type_status=1")) {
                                    while ($roleData = mysqli_fetch_assoc($roleList)) { ?>
                                <option value="<?php echo $roleData['user_type_code']; ?>"
                                    <?php echo (($roleData['user_type_code'] == '') ? 'selected' : '') ?>>
                                    <?php echo $roleData['user_type_name']; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <?php

                        if (SITE_APP == 'ims_marble') { ?>
                        <div class="form-group" id="companyDropDown">
                            <p>Company<sup>*</sup></p>
                            <select class="selectJS form-control" name="company" id="company">
                                <option value="">Select company</option>
                                <?php

                                    if ($companyList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "company WHERE company_status=1 AND company_address_id!=0 AND company_site_id=" . SITE_ID)) {
                                        if (mysqli_num_rows($companyList) > 0) {
                                            while ($company = mysqli_fetch_assoc($companyList)) {
                                                echo "<option value='{$company['company_id']}' >{$company['company_title']}</option>";
                                            }
                                        }
                                    }

                                    ?>
                            </select>
                        </div>
                        <div class="form-group" id="warehouseBox" style="display: none">
                            <p>Warehouse</p>
                            <select class="selectJS form-control" name="warehouse" id="warehouse">
                            </select>
                        </div>
                        <?php
                        }

                        ?>
                        <div class="form-group">
                            <input type="hidden" name="type" value="user">
                            <input type="hidden" name="action" value="publish">
                            <input type="submit" class="btn btn-green" value="Add user">
                        </div>
                    </div>
            </form>
    </section>
    </form>
</div>
</section>
</div>