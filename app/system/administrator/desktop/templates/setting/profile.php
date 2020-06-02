<?php

    if($checkUser=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."user, ".TABLE_PREFIX."profile WHERE user_id='".USER_ID."' AND profile_owner_id='".USER_ID."' AND user_status=1")) {
        if(mysqli_num_rows($checkUser)==0) {
            echo '<h1>invalid request found</h1>';
        } else {
            
            extract(mysqli_fetch_assoc($checkUser));

            ?>

            <h2>Manage Profile</h2>

            <div class="form-box">
                <div class="box-2">
                    <form method="POST" id="profileData" >
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
                    </form>
                </div>
                <div class="box-2" style="float: left; width: 250px; margin-left: 50px;" >
                    <form method="POST" id="profileData" enctype="multipart/form-data"  >
                        <div class="form-group">
                            <h3>Profile Picture</h3>
                            <div class="profilePreviewBox" style="display: <?php echo (($profile_image!='') ? 'block' : 'none') ?>;">
                                <div class="img"><img src="//<?php echo SITE_DOMAIN.$profile_image ?>" id="profilePath" alt=""></div>
                                <input type="hidden" name="type" value="<?php echo $postType ?>" >
                                <input type="hidden" name="action" value="profilePicture" >
                                <input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
                                <input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
                                <input type="button" id="updateprofile" class="btn btn-green btn-sm"  value="Update Website Icon" >
                                <input type="button" id="profileChangeImage" name="updateprofile" class="btn btn-red btn-sm" value="Change Site Icon" >
                            </div>
                            <div class="profileUploadBox" style="display: <?php echo (($profile_image!='') ? 'none' : 'block') ?>;">
                                <span>Add Profile Picture</span>
                                <input type='file' id="profileImage" name='profile' />
                                <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif ( Recommend Size : 512x512 )</p>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

       <?php
       }
    }

?>