<?php

$user_id = base64_decode($id);

if ($checkUser = mysqli_query($connection, "SELECT user_mobile, user_email, site_access_type, site_access_meta, profile_first_name, profile_last_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id = user_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id INNER JOIN " . TABLE_PREFIX . "kyc ON kyc_request_by=user_id WHERE  user_id = {$user_id}")) {

    if (mysqli_num_rows($checkUser) != 0) {
        $kycUploaded = true;
        extract(mysqli_fetch_assoc($checkUser));
        $siteAccessMeta = json_decode($site_access_meta, TRUE);
    }
}

?>

<h2 class="heading">Update User<span>Update users personal details</span></h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Create new user</li>
        </ul>
        <div class="steps-box">
            <form method="POST" action="#">
                <section class="form">
                    <?php

                    if (isset($kycUploaded)) { ?>
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Update user's details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/domain-tlds.png" alt="Add new domain tld">
                        </div>
                        <div class="form-group">
                            <input type="text" name="first_name" id="first_name" placeholder="First name*"
                                class="form-control" value="<?php echo $profile_first_name ?>" autofocus required />
                            <label class="form-lable" id="first_nameLabel">First name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="last_name" id="last_name" placeholder="Last name*"
                                class="form-control" value="<?php echo $profile_last_name ?>" required />
                            <label class="form-lable" id="last_nameLable">Last name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="mobile" id="mobile" placeholder="Mobile no*"
                                value="<?php echo $user_mobile ?>" class="form-control" readonly />
                            <label class="form-lable">Mobile No.<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" name='email' id="email"
                                placeholder="Email address*" name="email" value="<?php echo $user_email ?>" readonly />
                            <label class="form-lable">Email<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p>User role <sup>*</sup></p>
                            <select class="selectJS form-control" name="userRole" id="select">
                                <option value="">Select user role</option>
                                <?php
                                    if ($roleList = mysqli_query($connection, "SELECT user_type_name, user_type_code FROM " . TABLE_PREFIX . "user_type WHERE user_type_app_id='" . SITE_APP_ID . "' AND user_type_status=1")) {
                                        while ($roleData = mysqli_fetch_assoc($roleList)) { ?>
                                <option value="<?php echo $roleData['user_type_code']; ?>"
                                    <?php echo (($roleData['user_type_code'] == $site_access_type) ? 'selected' : '') ?>>
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
                                                    echo "<option value='{$company['company_id']}' " . (($company['company_id'] == $siteAccessMeta['company']) ? 'selected' : '') . " >{$company['company_title']}</option>";
                                                }
                                            }
                                        }

                                        ?>
                            </select>
                        </div>
                        <div class="form-group" id="warehouseBox">
                            <p>Warehouse</p>
                            <select class="selectJS form-control" name="warehouse" id="warehouse">
                                <option value="">Select warehouse</option>
                                <?php
                                        if ($warehouseList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "warehouse WHERE warehouse_status=1 AND warehouse_company_id={$siteAccessMeta['company']} AND warehouse_site_id=" . SITE_ID)) {
                                            if (mysqli_num_rows($warehouseList) > 0) {
                                                while ($warehouse = mysqli_fetch_assoc($warehouseList)) {
                                                    echo "<option value='{$warehouse['warehouse_id']}' " . (($warehouse['warehouse_id'] == $siteAccessMeta['warehouse']) ? 'selected' : '') . " >{$warehouse['warehouse_title']}</option>";
                                                }
                                            }
                                        }

                                        ?>
                            </select>
                        </div>
                        <?php
                            }

                            ?>
                        <div class="form-group">
                            <input type="hidden" name="user_id" value="<?php echo base64_encode($user_id) ?>">
                            <input type="hidden" name="type" value="user">
                            <input type="hidden" name="action" value="update">
                            <input type="submit" class="btn btn-green" value="Update user">
                        </div>
                        <section class="form">
                    </div>
                    <?php
                    } else { ?>
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h3>KYC documents are not uploaded by user </h3>
                        </div>
                        <?php
                    }

                        ?>
                </section>
            </form>
    </section>
    </form>
</div>
</section>
</div>