<h2>Add New <?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?></h2>

<div class="form-box">
	<form method="POST" id="formData" enctype="multipart/form-data" >
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
                <label>Website App*</label>
                <select name='user_type' class='form-control inside-shadow' required>
                    <option value="">Select App</option>
                    <?php

                        if($appList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."app WHERE app_path!='system'")) {

                            if(mysqli_num_rows($appList)!=0) {

                                while($appDetails=mysqli_fetch_assoc($appList)) {

                                    echo "<option value='{$appDetails['app_id']}' >{$appDetails['app_title']}</option>";

                                }

                            }

                        }

                    ?>
                </select>
            </div>
            <div class="form-group">
				<input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
				<input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
				<input type="hidden" name="type" value="user" >
				<input type="hidden" name="action" value="add" >
                <input type="submit" class="btn btn-green" value="Add User">
            </div>
        </div>
	</form>
</div>