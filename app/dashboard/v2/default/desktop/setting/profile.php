<?php

if ($checkUser = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "user, " . TABLE_PREFIX . "profile WHERE user_id='" . USER_ID . "' AND profile_user_id='" . USER_ID . "' AND user_status=1")) {
    if (mysqli_num_rows($checkUser) == 0) {
        echo '<h1>invalid request found</h1>';
    } else {

        extract(mysqli_fetch_assoc($checkUser));

?>

<h2 class="heading">Manage profile<span>Update personal details</span></h2>
<div class="main-box">
    <section>
        <div class="steps-box">
            <form action="#" id="profileData">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Update your profile</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/domain-tlds.png" alt="Add new domain tld">
                        </div>
                        <div class="form-group">
                            <input type="text" name="username" id="checkUsername" value='<?php echo $user_username ?>'
                                class="form-control" placeholder="Username" readonly>
                            <label class="form-lable" id="checkUsernameLabel">Username</label>
                        </div>
                        <div class="form-group">
                            <input type="text" id="firstName" value="<?php echo $profile_first_name; ?>"
                                name="first_name" class="form-control" placeholder="First Name" required>
                            <label class="form-lable" id="firstNameLabel">First Name <sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="text" id="lastName" value="<?php echo $profile_last_name ?>" name="last_name"
                                class="form-control" placeholder="Last Name" required>
                            <label class="form-lable">Last Name <sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="text" id="fullName" value="<?php echo $profile_display_name ?>"
                                name="display_name" class="form-control" placeholder="Full Name" required>
                            <label class="form-lable">Full Name <sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="text" value="<?php echo $user_email ?>" class="form-control"
                                placeholder="Email" readonly>
                            <label class="form-lable">Email <sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="email" value="<?php echo $profile_alt_email ?>" name="alt_email"
                                class="form-control" placeholder="Alternate Email">
                            <label class="form-lable">Alternate Email</label>
                        </div>
                        <div class="form-group">
                            <input type="number" onwheel="this.blur()" id="mobile" value="<?php echo $user_mobile ?>"
                                name="mobile" class="form-control" placeholder="Mobile No.">
                            <label class="form-lable">Mobile No <sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="number" onwheel="this.blur()" value="<?php echo $profile_alt_mobile ?>"
                                name="alt_mobile" class="form-control" placeholder="Alternate Mobile No.">
                            <label class="form-lable">Alternative Mobile No</label>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="type" value="setting">
                            <input type="hidden" name="action" value="profile">
                            <input type="button" id="updateProfile" class="btn btn-green" value="Update Profile">
                        </div>
            </form>
    </section>
    </form>
</div>
</section>
</div>

<?php
    }
}

?>