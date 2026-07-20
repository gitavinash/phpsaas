<?php

if ($checkUser = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "user, " . TABLE_PREFIX . "profile WHERE user_id='" . USER_ID . "' AND profile_user_id='" . USER_ID . "' AND user_status=1")) {
    if (mysqli_num_rows($checkUser) == 0) {
        echo '<h1>invalid request found</h1>';
    } else {

        extract(mysqli_fetch_assoc($checkUser));

?>

        <div class="premium-header" style="margin-bottom: 24px;">
            <div class="premium-header-left">
                <h2>Manage Profile</h2>
                <p class="order-info-subtitle">Update your personal information and profile picture.</p>
            </div>
        </div>

        <div class="premium-grid">
            <!-- Left Card: Personal Details -->
            <div class="premium-grid-left">
                <div class="premium-card">
                    <h3 class="premium-card-title"><i class="fas fa-user-circle" style="margin-right: 8px; color: var(--primary);"></i> Personal Details</h3>
                    <form id="profileData">
                        <div class="premium-form-grid">
                            <div class="premium-form-group">
                                <label>Username</label>
                                <input type="text" name="username" id="checkUsername" value='<?php echo $user_username ?>' class="premium-form-control" placeholder="Username" readonly>
                            </div>
                            <div class="premium-form-group">
                                <label>Email Address</label>
                                <input type="text" value="<?php echo $user_email ?>" class="premium-form-control" placeholder="Email" readonly>
                            </div>
                            <div class="premium-form-group">
                                <label>First Name*</label>
                                <input type="text" id="firstName" value="<?php echo $profile_first_name; ?>" name="first_name" class="premium-form-control" placeholder="First Name" required>
                            </div>
                            <div class="premium-form-group">
                                <label>Last Name*</label>
                                <input type="text" id="lastName" value="<?php echo $profile_last_name ?>" name="last_name" class="premium-form-control" placeholder="Last Name" required>
                            </div>
                            <div class="premium-form-group">
                                <label>Full Name*</label>
                                <input type="text" id="fullName" value="<?php echo $profile_display_name ?>" name="display_name" class="premium-form-control" placeholder="Full Name" required>
                            </div>
                            <div class="premium-form-group">
                                <label>Alternate Email</label>
                                <input type="email" value="<?php echo $profile_alt_email ?>" name="alt_email" class="premium-form-control" placeholder="Alternate Email">
                            </div>
                            <div class="premium-form-group">
                                <label>Mobile No*</label>
                                <input type="number" onwheel="this.blur()" id="mobile" value="<?php echo $profile_mobile ?>" name="mobile" class="premium-form-control" placeholder="Mobile No.">
                            </div>
                            <div class="premium-form-group">
                                <label>Alternative Mobile No</label>
                                <input type="number" onwheel="this.blur()" value="<?php echo $profile_alt_mobile ?>" name="alt_mobile" class="premium-form-control" placeholder="Alternate Mobile No.">
                            </div>
                        </div>
                        <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
                            <input type="hidden" name="type" value="setting">
                            <input type="hidden" name="action" value="profile">
                            <input type="submit" id="updateProfile" class="btn-premium-action btn-confirm" style="border: none; cursor: pointer;" value="Update Profile">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Card: Profile Image -->
            <div class="premium-grid-right">
                <div class="premium-card">
                    <h3 class="premium-card-title"><i class="fas fa-camera" style="margin-right: 8px; color: var(--primary);"></i> Profile Picture</h3>
                    <form id="updateProfileImageForm">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 16px; padding: 16px 0;">
                            <div class="profilePreviewBox" style="display: <?php echo ((PROFILE_IMAGE != '') ? 'flex' : 'none') ?>; flex-direction: column; align-items: center; gap: 16px; width: 100%;">
                                <div class="img" style="width: 140px; height: 140px; border-radius: 50%; overflow: hidden; border: 3px solid var(--neutral-200); box-shadow: var(--shadow-sm); display: flex; justify-content: center; align-items: center; background-color: var(--neutral-100);">
                                    <img src="<?php echo ((PROFILE_IMAGE == '') ? IMG_LIBRARY . 'default-profile.png' : PROFILE_IMAGE) ?>" id="profilePath" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <input type="hidden" name="type" value="setting">
                                <input type="hidden" name="action" value="profilePicture">
                                <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                                    <input type="button" id="updateProfileImage" class="btn-premium-action btn-confirm" style="display:none; width: 100%; border: none; cursor: pointer;" value="Update Profile Picture">
                                    <input type="button" id="profileChangeImage" name="updateprofile" class="btn-premium-action btn-cancel" style="width: 100%; background-color: var(--neutral-300) !important; color: var(--neutral-700) !important; border: none; cursor: pointer;" value="Change Profile Picture">
                                </div>
                            </div>
                            <div class="profileUploadBox" style="display: <?php echo ((PROFILE_IMAGE == '') ? 'block' : 'none') ?>; width: 100%; text-align: center;">
                                <div style="display: block; border: 2px dashed var(--neutral-300); border-radius: 8px; padding: 32px 16px; background: var(--neutral-50); transition: all 0.2s;">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: var(--neutral-500); margin-bottom: 8px; display: block; margin-left: auto; margin-right: auto;"></i>
                                    <span style="display: inline-block; font-size: 14px; font-weight: 500; cursor: pointer; background: var(--primary) !important; color: white !important; padding: 8px 16px !important; border-radius: 6px !important; margin-bottom: 8px;">Upload Picture</span>
                                    <input type='file' id="profileImage" name='profile' style="display: none;" />
                                </div>
                                <p style="font-size: 11px; color: var(--neutral-500); margin-top: 12px; line-height: 1.4;">Supported formats: .png, .jpg, .jpeg, .gif (Recommended: 512x512)</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

<?php
    }
}

?>