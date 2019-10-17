<?php

    if($checkUser=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."user, ".TABLE_PREFIX."profile WHERE user_id='".USER_ID."' AND profile_owner_id='".USER_ID."' AND user_status=1")) {
        if(mysqli_num_rows($checkUser)==0) {
            echo '<h1>invalid request found</h1>';
        } else {
            
            extract(mysqli_fetch_assoc($checkUser));

            ?>

            <h2>Manage Profile</h2>

            <div class="form-box">
                <form method="POST" id="profileData" >
                    <div class="box-2">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" id="checkUsername" value='<?php echo $user_username ?>' class="form-control inside-shadow" placeholder="Username" >
                        </div>
                        <div class="form-group">
                            <label>First Name*</label>
                            <input type="text" value="<?php echo $profile_first_name; ?>" name="first_name" class="form-control inside-shadow" placeholder="First Name" required >
                        </div>
                        <div class="form-group">
                            <label>Last Name*</label>
                            <input type="text" value="<?php echo $profile_last_name ?>" name="last_name" class="form-control inside-shadow" placeholder="Last Name" required >
                        </div>
                        <div class="form-group">
                            <label>Display Name*</label>
                            <input type="text" value="<?php echo $profile_display_name ?>" name="display_name" class="form-control inside-shadow" placeholder="Display Name" required >
                        </div>
                        <div class="form-group">
                            <label>Email*</label>
                            <input type="text" value="<?php echo $user_email ?>" class="form-control inside-shadow" placeholder="Email" readonly >
                        </div>
                        <div class="form-group">
                            <label>Alternate Email</label>
                            <input type="email" value="<?php echo $profile_alt_email ?>" name="alt_email" class="form-control inside-shadow" placeholder="Alternate Email">
                        </div>
                        <div class="form-group">
                            <label>Mobile No</label>
                            <input type="number" value="<?php echo $profile_mobile ?>" name="mobile" class="form-control inside-shadow" placeholder="Mobile No.">
                        </div>
                        <div class="form-group">
                            <label>Alternative Mobile No</label>
                            <input type="number" value="<?php echo $profile_alt_mobile ?>" name="alt_mobile" class="form-control inside-shadow" placeholder="Alternate Mobile No.">
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="type" value="<?php echo $postType ?>" >
                            <input type="hidden" name="action" value="profile" >
                            <input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
                            <input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
                            <input type="submit" id="updateProfile" class="btn btn-green" value="Update Profile" >
                        </div>
                    </div>
                </form>
            </div>

       <?php
       }
    }

?>