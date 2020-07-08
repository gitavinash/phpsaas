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

                    <form id="profileData" >
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" id="checkUsername" value='<?php echo $user_username ?>' class="form-control inside-shadow" placeholder="Username" readonly >
                        </div>
                        <div class="form-group">
                            <label>First Name*</label>
                            <input type="text" id="firstName" value="<?php echo $profile_first_name; ?>" name="first_name" class="form-control inside-shadow" placeholder="First Name" required >
                        </div>
                        <div class="form-group">
                            <label>Last Name*</label>
                            <input type="text" id="lastName" value="<?php echo $profile_last_name ?>" name="last_name" class="form-control inside-shadow" placeholder="Last Name" required >
                        </div>
                        <div class="form-group">
                            <label>Full Name*</label>
                            <input type="text" id="fullName" value="<?php echo $profile_display_name ?>" name="display_name" class="form-control inside-shadow" placeholder="Full Name" required >
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
                            <label>Mobile No*</label>
                            <input type="number" id="mobile" value="<?php echo $profile_mobile ?>" name="mobile" class="form-control inside-shadow" placeholder="Mobile No.">
                        </div>
                        <div class="form-group">
                            <label>Alternative Mobile No</label>
                            <input type="number" value="<?php echo $profile_alt_mobile ?>" name="alt_mobile" class="form-control inside-shadow" placeholder="Alternate Mobile No.">
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="type" value="setting" >
                            <input type="hidden" name="action" value="profile" >
                            <input type="submit" id="updateProfile" class="btn btn-green" value="Update Profile" >
                        </div>
                    </form>

                </div>

                <div class="box-2" style="float: left; width: 250px; margin-left: 50px;" >
                    
                    <form id="updateProfileImageForm" >
                        <div class="form-group">
                            <h3>Profile Picture</h3>
                            <div class="profilePreviewBox" style="display: <?php echo ((PROFILE_IMAGE!='') ? 'block' : 'none') ?>;" >
                                <div class="img"><img src="<?php echo ((PROFILE_IMAGE=='') ? IMG_LIBRARY.'default-profile.png' : PROFILE_IMAGE ) ?>" id="profilePath" alt=""></div>
                                <input type="hidden" name="type" value="setting" >
                                <input type="hidden" name="action" value="profilePicture" >
                                <input type="button" id="updateProfileImage" class="btn btn-green btn-sm" style="display:none" value="Update Profile Picture" >
                                <input type="button" id="profileChangeImage" name="updateprofile" class="btn btn-red btn-sm" value="Change Profile Picture" >
                            </div>
                            <div class="profileUploadBox" style="display: <?php echo ((PROFILE_IMAGE=='') ? 'block' : 'none') ?>;" >
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