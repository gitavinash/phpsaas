<?php

$vendor_id = base64_decode($id);

$sql = "SELECT c.*, a.*, s.state_name, ct.city_name FROM " . TABLE_PREFIX . "vendor c LEFT JOIN " . TABLE_PREFIX . "address a ON a.address_id = c.vendor_address_id LEFT JOIN " . TABLE_PREFIX . "state s ON s.state_id = a.address_state_id LEFT JOIN " . TABLE_PREFIX . "city ct ON ct.city_id = a.address_city_id WHERE c.vendor_id='{$vendor_id}' AND c.vendor_site_id='" . SITE_ID . "' ORDER BY c.vendor_title ASC";

if ($checkVendor = mysqli_query($connection, $sql)) {
    if (mysqli_num_rows($checkVendor) != 0) {
        extract(mysqli_fetch_assoc($checkVendor));
    } else {
?>
        <script type="text/javascript">
            alert('Invalid action found');
            window.location.href = 'dashboard/vendor/?type=list&status=all';
        </script>
<?php
    }
}

?>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="modern-user-form">
    <div class="box-2">
        <h2>Update Vendor</h2>
        <div class="row">
            <div class="category">
                <div class="form-box">
                    <form method="POST" autocomplete="off" id="updateVendorForm">
                        <h3 class="form-title"><?php echo ((isset($_GET['id'])) ? 'Update' : 'Add New'); ?> Vendor</h3>
                        <div class="form-group">
                            <label>Vendor name<sup>*</sup></label>
                            <input type="text" name="name" value="<?php echo $vendor_title ?>" id="name" placeholder="Vendor name"
                                class="form-control inside-shadow" autofocus required />
                        </div>
                        <div class="form-group">
                            <label>Email<sup>*</sup></label>
                            <input type="email" name="email" id="email" placeholder="Email"
                                class="form-control inside-shadow" value="<?php echo $vendor_email ?>" required />
                        </div>
                        <div class="form-group">
                            <label>Contact No.<sup>*</sup></label>
                            <input type="number" name="mobile" id="mobile" value="<?php echo $vendor_mobile ?>" placeholder="Contact No."
                                class="form-control inside-shadow" required />
                        </div>
                        <div class="form-group">
                            <label>About vendor</label>
                            <textarea name="description" class="form-control inside-shadow" id="description" placeholder="About vendor"><?php echo $vendor_description ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control inside-shadow" id="address" placeholder="Vendor address"><?php echo $address_name . ' ' . $address_house_no . ' ' . $address_area . ' ' . $address_landmark ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <select class="selectJS form-control" name="state" id="state">
                                <option value="select">Select state</option>
                                <?php

                                if ($stateList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "state WHERE state_country_id=101 AND state_status=1")) {
                                    if (mysqli_num_rows($stateList) > 0) {
                                        while ($state = mysqli_fetch_assoc($stateList)) {
                                            echo "<option value='{$state['state_id']}' " . (($state['state_id'] == $address_state_id) ? 'selected' : '') . ">{$state['state_name']}</option>";
                                        }
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="form-group" id="cityBox" style="display: none">
                            <label>City</label>
                            <?php

                            if ($address_state_id != 0) { ?>
                                <select class="selectJS form-control" name="city" id="city">
                                    <?php

                                    if ($cityList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "city WHERE city_state_id={$address_state_id} AND city_status=1")) {
                                        if (mysqli_num_rows($cityList) > 0) {
                                            while ($city = mysqli_fetch_assoc($cityList)) {
                                                echo "<option value='{$city['city_id']}' " . ((strtolower($city['city_name']) == strtolower($city_name)) ? 'selected' : '') . " >{$city['city_name']}</option>";
                                            }
                                        }
                                    }

                                    ?>
                                </select>
                            <?php
                            }

                            ?>
                        </div>
                        <div class="form-group">
                            <label>Pin code</label>
                            <input type="number" name="pinCode" value="<?php echo $address_pin_code ?>" id="pinCode" placeholder="Pin code"
                                class="form-control inside-shadow" />
                        </div>
                        <div class="form-group">
                            <label>Vendor registration number ( CIN )</label>
                            <input type="text" name="cin" id="cin" value="<?php echo $vendor_cin ?>" placeholder="Vendor registration number ( CIN )"
                                class="form-control inside-shadow" />
                        </div>
                        <div class="form-group">
                            <label>Goods and Services Tax ( GST ) </label>
                            <input type="text" name="gst" id="gst" value="<?php echo $vendor_gst ?>" placeholder="Goods and Services Tax ( GST )"
                                class="form-control inside-shadow" />
                        </div>
                        <div class="form-group">
                            <label>Vendor PAN</label>
                            <input type="text" name="pan" id="pan" value="<?php echo $vendor_pan ?>" placeholder="Vendor PAN"
                                class="form-control inside-shadow" />
                        </div>
                        <div class="form-group">
                            <label>Vendor Registration Type</label>
                            <select class="selectJS form-control" name="vendorType" id="vendorType">
                                <option value="select" <?php echo ($vendor_type == 'select') ? 'selected' : '' ?>>Select registration type</option>
                                <option value="private" <?php echo ($vendor_type == 'private') ? 'selected' : '' ?>>Private Limited Vendor</option>
                                <option value="public" <?php echo ($vendor_type == 'public') ? 'selected' : '' ?>>Public Limited Vendor</option>
                                <option value="partnerships" <?php echo ($vendor_type == 'partnerships') ? 'selected' : '' ?>>Partnerships</option>
                                <option value="llp" <?php echo ($vendor_type == 'llp') ? 'selected' : '' ?>>LLP Registration</option>
                                <option value="opc" <?php echo ($vendor_type == 'opc') ? 'selected' : '' ?>>One Person Vendor ( OPC )</option>
                                <option value="proprietorship" <?php echo ($vendor_type == 'proprietorship') ? 'selected' : '' ?>>Sole Proprietorship</option>
                                <option value="section8" <?php echo ($vendor_type == 'section8') ? 'selected' : '' ?>>Section 8 Vendor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="type" value="vendor">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="vendorId" id="vendorId" value="<?php echo $vendor_id ?>">
                            <input type="submit" class="btn btn-green" value="Update vendor">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="box-2">
        <h2>Vendor logo</h2>
        <form id="updateProfileImageForm" enctype="multipart/form-data" onsubmit="return false;">
            <div class="form-group profile-upload-theme">
                <div class="profile-image-preview">
                    <img src="<?php echo (($vendor_logo == '') ? IMG_LIBRARY . 'default-profile.png' : $vendor_logo) ?>"
                        id="profilePath" alt="Vendor Logo" />
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
                $newName = 'vendor_' . $vendor_id . '_' . time() . '.' . $ext;
                $uploadFile = $uploadDir . $newName;
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
                    $profilePicUrl = '/upload/profile_pics/' . $newName;
                    mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "vendor SET vendor_logo='" . mysqli_real_escape_string($connection, $profilePicUrl) . "' WHERE vendor_id={$vendor_id} AND vendor_site_id='" . SITE_ID . "'");
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
</div>