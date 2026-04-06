<?php

$user_id = base64_decode($id);



if ($checkUser = mysqli_query($connection, "SELECT user_mobile, user_email, site_access_type, profile_first_name, profile_last_name, profile_image as profilePicture FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id = user_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id WHERE user_id = {$user_id};")) {
    if (mysqli_num_rows($checkUser) != 0) {
        extract(mysqli_fetch_assoc($checkUser));
        // Improved: Fetch social_links from profile_meta table
        $facebook_link = $youtube_link = $twitter_link = $linkedin_link = '';
        $metaKey = 'social_links';
        $metaQuery = mysqli_query($connection, "SELECT profile_meta_value FROM " . TABLE_PREFIX . "profile_meta WHERE profile_meta_profile_id='{$user_id}' AND profile_meta_key='{$metaKey}' LIMIT 1");
        if ($metaQuery && mysqli_num_rows($metaQuery) > 0) {
            $metaRow = mysqli_fetch_assoc($metaQuery);
            $socialLinksArr = json_decode($metaRow['profile_meta_value'], true);
            $facebook_link = !empty($socialLinksArr['facebook']) ? $socialLinksArr['facebook'] : '';
            $youtube_link = !empty($socialLinksArr['youtube']) ? $socialLinksArr['youtube'] : '';
            $twitter_link = !empty($socialLinksArr['twitter']) ? $socialLinksArr['twitter'] : '';
            $linkedin_link = !empty($socialLinksArr['linkedin']) ? $socialLinksArr['linkedin'] : '';
        } else {
            // No social_links found, initialize empty array
            $socialLinksArr = [];
            if (DEVELOPMENT_MODE == true) {
                echo "No social_links meta found for user_id: {$user_id}";
            }
        }
    } else {
?>
<script type="text/javascript">
alert('Invalid action found');
window.location.href = 'dashboard/user/?type=list&status=all';
</script>
<?php
    }
}

?>

<style>
.modern-user-form {
    --uf-bg: #ffffff;
    --uf-border: #e5e7eb;
    --uf-soft: #f8fafc;
    --uf-text: #111827;
    --uf-subtext: #6b7280;
    margin-top: 8px;
}

.modern-user-form h2 {
    margin: 0 0 12px;
    font-size: 24px;
    font-weight: 700;
    color: var(--uf-text);
    letter-spacing: -0.02em;
}

.modern-user-form .form-box {
    border: 1px solid var(--uf-border);
    border-radius: 14px;
    background: var(--uf-bg);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    padding: 18px;
}

.modern-user-form .form-title {
    margin: 0 0 14px;
    color: var(--uf-text);
    font-size: 16px;
    font-weight: 700;
}

.modern-user-form .form-group {
    margin-bottom: 12px;
}

.modern-user-form .form-group label {
    display: block;
    margin-bottom: 6px;
    color: var(--uf-subtext);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.modern-user-form .form-control {
    min-height: 42px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #fff;
    color: #111827;
    box-shadow: none;
}

.modern-user-form .form-control:focus {
    border-color: #93c5fd;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.modern-user-form .form-control[disabled] {
    background: #f3f4f6;
    color: #6b7280;
}

.modern-user-form .btn-green {
    border-radius: 8px;
    border: 1px solid #bbf7d0;
    background: #16a34a;
    color: #fff;
    border: 1px solid #16a34a;
    font-weight: 700;
    padding: 9px 14px;
    transition: background 0.2s;
}

.modern-user-form .btn-green:hover {
    background: #15803d;
    color: #fff;
}
</style>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="modern-user-form">
    <div class="box-2">
        <h2>Update User</h2>

        <div class="row">
            <div class="category">
                <div class="form-box">
                    <form method="POST" autocomplete="off">
                        <h3 class="form-title"><?php echo ((isset($_GET['id'])) ? 'Update' : 'Add New'); ?> User</h3>
                        <div class="form-group">
                            <label>First name</label>
                            <input type="text" name="first_name" id="first_name" placeholder="First name*"
                                class="form-control inside-shadow" value="<?php echo $profile_first_name ?>" autofocus
                                required />
                        </div>
                        <div class="form-group">
                            <label>Last name</label>
                            <input type="text" name="last_name" id="last_name" placeholder="Last name*"
                                class="form-control inside-shadow" value="<?php echo $profile_last_name ?>" required />
                        </div>
                        <div class="form-group">
                            <label>Mobile No.</label>
                            <input type="text" name="mobile" id="mobile" placeholder="Mobile no*"
                                class="form-control inside-shadow" value="<?php echo $user_mobile ?>" disabled />
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control inside-shadow" id="email"
                                placeholder="Email address*" name="email" value="<?php echo $user_email ?>" disabled />
                        </div>
                        <div class="form-group">
                            <label>User role</label>
                            <select name="userRole" class="form-control inside-shadow">
                                <option value="0">Select user role</option>
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
                        <!-- Social Media Links Section -->
                        <div class="form-group">
                            <label style="font-size:15px; color:#2563eb; font-weight:600;">Social Media Links</label>
                        </div>
                        <div class="form-group">
                            <label for="facebook_link">Facebook</label>
                            <input type="url" name="facebook_link" id="facebook_link" class="form-control inside-shadow"
                                placeholder="Facebook profile/page URL"
                                value="<?php echo isset($facebook_link) ? htmlspecialchars($facebook_link) : '' ?>" />
                        </div>
                        <div class="form-group">
                            <label for="youtube_link">YouTube</label>
                            <input type="url" name="youtube_link" id="youtube_link" class="form-control inside-shadow"
                                placeholder="YouTube channel URL"
                                value="<?php echo isset($youtube_link) ? htmlspecialchars($youtube_link) : '' ?>" />
                        </div>
                        <div class="form-group">
                            <label for="twitter_link">Twitter</label>
                            <input type="url" name="twitter_link" id="twitter_link" class="form-control inside-shadow"
                                placeholder="Twitter profile URL"
                                value="<?php echo isset($twitter_link) ? htmlspecialchars($twitter_link) : '' ?>" />
                        </div>
                        <div class="form-group">
                            <label for="linkedin_link">LinkedIn</label>
                            <input type="url" name="linkedin_link" id="linkedin_link" class="form-control inside-shadow"
                                placeholder="LinkedIn profile/page URL"
                                value="<?php echo isset($linkedin_link) ? htmlspecialchars($linkedin_link) : '' ?>" />
                        </div>
                        <!-- End Social Media Links Section -->
                        <div class="form-group">
                            <input type="hidden" name="type" value="user">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="mobile" value="<?php echo $user_mobile ?>">
                            <input type="hidden" name="email" value="<?php echo $user_email ?>">
                            <input type="submit" class="btn btn-green" value="Update user">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="box-2">
        <h2>Profile Picture</h2>
        <form id="updateProfileImageForm" enctype="multipart/form-data" onsubmit="return false;">
            <div class="form-group profile-upload-theme">
                <div class="profile-image-preview">
                    <img src="<?php echo (($profilePicture == '') ? IMG_LIBRARY . 'default-profile.png' : $profilePicture) ?>"
                        id="profilePath" alt="Profile Picture" />
                </div>
                <label class="profile-upload-label">
                    <span class="profile-upload-btn" style="padding: 5px 10px; background: red; color: #fff;;">Choose
                        Image</span>
                    <input type="file" id="profileImage" name="profile_image"
                        accept="image/png, image/jpeg, image/jpg, image/gif" style="display:none;" />
                </label>
                <div class="profile-upload-info">Supported: .png, .jpg, .jpeg, .gif (512x512 recommended)</div>
                <input type="button" id="updateProfileImage" name="upload_profile_image"
                    class="btn btn-green btn-sm profile-upload-submit" value="Upload" style="display:none;">
            </div>
        </form>
        <div id="profileImageMsg"></div>
        <script>
        // Profile image AJAX upload logic with SweetAlert2
        // (replaces previous msgBox usage)
        document.addEventListener('DOMContentLoaded', function() {
            var fileInput = document.getElementById('profileImage');
            var uploadBtn = document.getElementById('updateProfileImage');
            var imgPreview = document.getElementById('profilePath');

            if (fileInput) {
                fileInput.onchange = function() {
                    if (fileInput.files.length > 0) {
                        uploadBtn.style.display = 'inline-block';
                        imgPreview.src = window.URL.createObjectURL(fileInput.files[0]);
                    } else {
                        uploadBtn.style.display = 'none';
                    }
                };
            }
            if (uploadBtn) {
                uploadBtn.onclick = function() {
                    if (!fileInput.files.length) return;
                    var formData = new FormData();
                    formData.append('profile_image', fileInput.files[0]);
                    formData.append('upload_profile_image', '1');
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', window.location.href, true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            var response = xhr.responseText;
                            // SweetAlert2 for success/error
                            if (response.includes('successfully')) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Profile picture updated successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    html: response,
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Upload failed',
                                text: 'Try again.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    };
                    xhr.send(formData);
                };
            }
        });
        </script>
        <?php
        // Handle profile image upload (AJAX)
        if (isset($_POST['upload_profile_image']) && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $uploadDir = ROOT . '/upload/profile_pics/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $newName = 'user_' . $user_id . '_' . time() . '.' . $ext;
                $uploadFile = $uploadDir . $newName;
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
                    $profilePicUrl = '/upload/profile_pics/' . $newName;
                    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "profile SET profile_image='" . mysqli_real_escape_string($connection, $profilePicUrl) . "' WHERE profile_user_id={$user_id}");
                    echo '<span style="color:green">Profile picture updated successfully.</span>';
                } else {
                    echo '<span style="color:red">Failed to upload image.</span>';
                }
            } else {
                echo '<span style="color:red">Invalid file type. Only JPG, PNG, GIF allowed.</span>';
            }
            exit;
        }
        ?>
    </div>
    </style>

    <style>
    .profile-upload-theme {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: var(--uf-soft);
        border-radius: 14px;
        padding: 18px 0 10px 0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        margin-top: 10px;
    }

    .profile-image-preview {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        background: #f3f4f6;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    .profile-image-preview img {
        width: 96px;
        height: 96px;
        max-width: 96px;
        max-height: 96px;
        object-fit: cover;
        border-radius: 50%;
        background: #fff;
        box-sizing: border-box;
        display: block;
    }

    .profile-upload-label {
        margin-bottom: 10px;
        cursor: pointer;
        display: block;
        width: 100%;
        text-align: center;
    }

    .profile-upload-btn {
        display: inline-block;
        padding: 10px 22px;
        background: #2563eb;
        color: #fff;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        border: none;
        box-shadow: 0 1px 2px rgba(30, 41, 59, 0.04);
        margin-bottom: 8px;
        transition: background 0.2s, box-shadow 0.2s;
    }

    .profile-upload-btn:hover,
    .profile-upload-btn:focus {
        background: #1d4ed8;
        color: #fff;
        box-shadow: 0 2px 8px rgba(30, 41, 59, 0.10);
    }

    .profile-upload-info {
        color: #64748b;
        font-size: 12px;
        margin-bottom: 10px;
    }

    .profile-upload-submit {
        margin-top: 4px;
        min-width: 90px;
    }
    </style>
</div>