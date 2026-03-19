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
        background: #ecfdf5;
        color: #166534;
        font-weight: 700;
        padding: 9px 14px;
    }

    .modern-user-form .btn-green:hover {
        background: #dcfce7;
    }
</style>

<div class="modern-user-form">
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
                        <input type="hidden" name="mobile" value="<?php echo $user_mobile ?>">
                        <input type="hidden" name="email" value="<?php echo $user_email ?>">
                        <input type="submit" class="btn btn-green" value="Update user">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>