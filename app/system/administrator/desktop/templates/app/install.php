<h2>Add New <?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?></h2>

<div class="form-box">
	<form id="installNewAppForm" enctype="multipart/form-data" >
        <div class="box-2">
            <div class="form-group">
                <label>Website Title*</label>
                <input type="text" name="website_title" class='form-control inside-shadow' autofocus required >
            </div>
            <div class="form-group">
                <label>Domain*</label>
                <input type="text" name="domain" class='form-control inside-shadow' autofocus required >
            </div>
            <div class="form-group">
                <label>Website description</label>
                <textarea name="website_description" class="form-control inside-shadow" cols="30" rows="10"></textarea>
            </div>
            <div class="form-group">
                <label>Select language*</label>
                <select name="lang" id="lang" class='form-control inside-shadow' required>
                    <option value="">Select language</option>
                    <option value="en" selected >English</option>
                    <option value="hi">Hindi</option>
                </select>
            </div>
            <div class="form-group">
                <label>Install App*</label>
                <select name='app' class='form-control inside-shadow' required>
                    <option value="">Select Application*</option>
                    <?php
                        if($appList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."app WHERE app_status=1 ORDER BY app_title ASC")) {
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
				<input type="hidden" name="type" value="website" >
				<input type="hidden" name="action" value="add" >
                <input type="button" id="installNewApp" class="btn btn-green" value="Install New App">
            </div>
        </div>
	</form>
</div>