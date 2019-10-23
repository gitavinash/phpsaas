<h2>Change Password</h2>

<div class="form-box">
	<form method="POST" id="formData" >
        <div class="box-2">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" class="form-control inside-shadow" name='password' autofocus required >
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" class="form-control inside-shadow" name='cnf_password' required >
            </div>
            <div class="form-group">
				<input type="hidden" name="type" value="<?php echo $postType ?>" >
				<input type="hidden" name="action" value="password" >
				<input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
				<input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
                <input type="submit" value='Change Password' class='btn btn-green'>
            </div>
        </div>
    </form>
</div>