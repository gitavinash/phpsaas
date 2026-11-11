<?php

$raw_id = isset($id) ? (string) $id : (string) ($_GET['id'] ?? '');
$decoded_user_id = base64_decode($raw_id, true);
$user_id = ($decoded_user_id !== false && ctype_digit((string) $decoded_user_id))
    ? (int) $decoded_user_id
    : (int) base64_decode($raw_id);

$user_mobile = '';
$user_email = '';
$site_access_type = '';
$profile_first_name = '';
$profile_last_name = '';
$userFound = false;

if ($user_id > 0 && ($checkUser = mysqli_query($connection, "SELECT user_mobile, user_email, site_access_type, profile_first_name, profile_last_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id = user_id AND site_access_site_id = " . (int) SITE_ID . " INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id WHERE user_id = {$user_id} LIMIT 1;"))) {

    if (mysqli_num_rows($checkUser) != 0) {
        $userFound = true;
        extract(mysqli_fetch_assoc($checkUser));
    }
}

if (!$userFound) { ?>
<script type="text/javascript">
alert('Invalid action found');
window.location.href = '/dashboard/user/?type=list&status=all';
</script>
<?php
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
                        class="form-control inside-shadow" value="<?php echo htmlspecialchars((string) $profile_first_name, ENT_QUOTES, 'UTF-8'); ?>" autofocus
                        required />
                </div>
                <div class="form-group">
                    <label>Last name</label>
                    <input type="text" name="last_name" id="last_name" placeholder="Last name*"
                        class="form-control inside-shadow" value="<?php echo htmlspecialchars((string) $profile_last_name, ENT_QUOTES, 'UTF-8'); ?>" required />
                </div>
                <div class="form-group">
                    <label>Mobile No.</label>
                    <input type="text" name="mobile" id="mobile" placeholder="Mobile no*"
                        class="form-control inside-shadow" value="<?php echo htmlspecialchars((string) $user_mobile, ENT_QUOTES, 'UTF-8'); ?>" disabled />
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control inside-shadow" id="email" placeholder="Email address*"
                        name="email" value="<?php echo htmlspecialchars((string) $user_email, ENT_QUOTES, 'UTF-8'); ?>" disabled />
                </div>
                <div class="form-group">
                    <label>User role</label>
                    <select name="userRole" class="form-control inside-shadow">
                        <option value="0">Select user role</option>
                        <?php
                        if ($roleList = mysqli_query($connection, "SELECT user_type_name, user_type_code FROM " . TABLE_PREFIX . "user_type WHERE user_type_app_id='" . SITE_APP_ID . "' AND user_type_status=1")) {
                            while ($roleData = mysqli_fetch_assoc($roleList)) { ?>
                        <option value="<?php echo htmlspecialchars((string) $roleData['user_type_code'], ENT_QUOTES, 'UTF-8'); ?>"
                            <?php echo ((strtolower((string) $roleData['user_type_code']) === strtolower((string) $site_access_type)) ? 'selected' : '') ?>>
                            <?php echo htmlspecialchars((string) $roleData['user_type_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars((string) base64_encode((string) $user_id), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="type" value="user">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="mobile" value="<?php echo htmlspecialchars((string) $user_mobile, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars((string) $user_email, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="submit" class="btn btn-green" value="Update user">
                </div>
            </form>
        </div>
    </div>
</div>