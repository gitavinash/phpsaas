<div class="modern-user-form">
    <h2>Add New User</h2>

    <div class="row">
        <div class="category">
            <div class="form-box">
                <form method="POST" autocomplete="off">
                    <h3 class="form-title"><?php echo ((isset($_GET['id'])) ? 'Update' : 'Add New'); ?> User</h3>
                    <div class="form-group">
                        <label>First name</label>
                        <input type="text" name="first_name" id="first_name" placeholder="First name*"
                            class="form-control inside-shadow" autofocus required />
                    </div>
                    <div class="form-group">
                        <label>Last name</label>
                        <input type="text" name="last_name" id="last_name" placeholder="Last name*"
                            class="form-control inside-shadow" required />
                    </div>
                    <div class="form-group">
                        <label>Mobile No.</label>
                        <input type="text" name="mobile" id="mobile" placeholder="Mobile no*"
                            class="form-control inside-shadow" required />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control inside-shadow" id="email" placeholder="Email address*"
                            name="email" required />
                    </div>
                    <div class="form-group">
                        <label>User role</label>
                        <select name="userRole" class="form-control inside-shadow">
                            <option value="0">Select user role</option>
                            <?php
                            if ($roleList = mysqli_query($connection, "SELECT user_type_name, user_type_code FROM " . TABLE_PREFIX . "user_type WHERE user_type_parent='vendor' AND user_type_app_id='" . SITE_APP_ID . "' AND user_type_status=1")) {
                                while ($roleData = mysqli_fetch_assoc($roleList)) { ?>
                                    <option value="<?php echo $roleData['user_type_code']; ?>"
                                        <?php echo (($roleData['user_type_code'] == '') ? 'selected' : '') ?>>
                                        <?php echo $roleData['user_type_name']; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="type" value="user">
                        <input type="hidden" name="action" value="publish">
                        <input type="submit" class="btn btn-green" value="Add new user">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>