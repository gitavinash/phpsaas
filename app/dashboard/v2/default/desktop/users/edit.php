<?php

$user_id = base64_decode($id);

if ($checkUser = mysqli_query($connection, "SELECT user_mobile, user_email, site_access_type, profile_first_name, profile_last_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id = user_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id WHERE user_id = {$user_id};")) {

    if (mysqli_num_rows($checkUser) != 0) {

        extract(mysqli_fetch_assoc($checkUser));
    } else { ?>
<script type="text/javascript">
alert('Invalid action found');
window.location.href = 'dashboard/user/?type=list&status=all';
</script>
<?php
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
                                value="<?php echo $user_mobile ?>" class="form-control" required />
                            <label class="form-lable">Mobile No.<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" id="email" placeholder="Email address*"
                                name="email" value="<?php echo $user_email ?>" disabled />
                            <label class="form-lable">Email<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p>User role <sup>*</sup></p>
                            <select class="selectJS form-control" name="site_lang" id="select">
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
                        <div class="form-group">
                            <input type="hidden" name="user_id" value="<?php echo base64_encode($user_id) ?>">
                            <input type="hidden" name="type" value="user">
                            <input type="hidden" name="action" value="update">
                            <input type="submit" class="btn btn-green" value="Update user">
                        </div>
                        <section class="form">
                    </div>
            </form>
    </section>
    </form>
</div>
</section>
</div>