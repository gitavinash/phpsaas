<h2>Add New <?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?></h2>

<div class="form-box">
	<form method="POST" id="registerFormData" enctype="multipart/form-data" >
        <div class="box-2">
            <div class="form-group">
                <label>First Name*</label>
                <input type="text" name="first_name" class='form-control inside-shadow' autofocus required >
            </div>
            <div class="form-group">
                <label>Last Name*</label>
                <input type="text" name="last_name" class='form-control inside-shadow' required >
            </div>
            <div class="form-group">
                <label>Email*</label>
                <input type="email" name='email' class='form-control inside-shadow' required >
            </div>
            <div class="form-group">
                <label>Mobile No.</label>
                <input type="number" name='mobile' class='form-control inside-shadow' >
            </div>
            <div class="form-group">
                <label>User Type*</label>
                <select name='user_type' class='form-control inside-shadow' required>
                    <option value="">Select User Type</option>
                    <option value="administrator">Administrator</option>
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                    <option value="author">Author</option>
                    <option value="reporter">Reporter</option>
                </select>
            </div>
            <div class="form-group">
				<input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
				<input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
				<input type="hidden" name="type" value="user" >
				<input type="hidden" name="action" value="add" >
                <input type="submit" id="registerBtn" class="btn btn-green" value="Add User">
            </div>
        </div>
	</form>
</div>