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

<h2>Update user</h2>

<div class="row">
    <div class="category">
        <div class="form-box">
            <form method="POST" autocomplete="off">
                <h3><?php echo ((isset($_GET['id'])) ? 'Update' : 'Add new') ?> user</h3>
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
                    <input type="email" class="form-control inside-shadow" id="email" placeholder="Email address*"
                        name="email" value="<?php echo $user_email ?>" disabled />
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
                <div class="form-group">
                    <input type="hidden" name="type" value="user">
                    <input type="hidden" name="action" value="update">
                    <input type="submit" class="btn btn-green" value="Update user">
                </div>
            </form>
        </div>
    </div>
</div>