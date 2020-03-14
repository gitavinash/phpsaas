<?php

    if($checkUser=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."user, ".TABLE_PREFIX."site WHERE user_id='".USER_ID."' AND site_owner_id='".USER_ID."' AND user_status=1")) {
        if(mysqli_num_rows($checkUser)==0) {
            echo '<h1>invalid request found</h1>';
        } else {
            
            extract(mysqli_fetch_assoc($checkUser));

            ?>

            <h2>Manage Website</h2>

            <div class="form-box">
                <form method="POST" id="websiteData" >
                    <div class="box-2">
                        <div class="form-group">
                            <label>Website Domain <span>( Please do not change. If it is not necessary )</span></label>
                            <input type="text" name="site_domain" value='<?php echo SITE_URL ?>' class="form-control inside-shadow" placeholder="Website Domain" <?php echo (($user_id==1) ? 'readonly' : '' ) ?> >
                        </div>
                        <div class="form-group">
                            <label>Actual Website URL</label>
                            <input type="text" name="site_url" value='<?php echo HTTP_PROTOCOL.(($site_url=='') ? $site_domain : $site_url ) ?>' class="form-control inside-shadow" placeholder="Website URL" <?php echo (($user_id==1) ? 'readonly' : '' ) ?> >
                        </div>
                        <div class="form-group">
                            <label>Website Name</label>
                            <input type="text" value="<?php echo $site_name; ?>" name="name" class="form-control inside-shadow" placeholder="First Name" required >
                        </div>
                        <div class="form-group">
                            <label>About Website</label>
                            <textarea name="description" class="form-control inside-shadow" placeholder="About Website ( Recommendation Max 170 Characters )" ><?php echo $site_description ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Website Keywords ( Use , to seprate Keywords )</label>
                            <textarea name="keyword" class="form-control inside-shadow" placeholder="Website Keywords ( Use , to seprate Keywords )" ><?php echo $site_keyword ?></textarea>
                        </div>
                        <div class="form-group">
							<label>Select website language</label>
							<select name="site_lang" class="form-control inside-shadow" required >
								<option value="">Language list</option>
								<option value="hi_IN" <?php echo (($site_lang=='hi_IN') ? 'selected' : '' ) ?> >हिंदी</option>
								<option value="en" <?php echo (($site_lang=='en') ? 'selected' : '' ) ?>  >English (United States)</option>
								<option value="en_AU" <?php echo (($site_lang=='en_AU') ? 'selected' : '' ) ?>  >English (Australia)</option>
								<option value="en_AU" <?php echo (($site_lang=='en_AU') ? 'selected' : '' ) ?>  >English (South Africa)</option>
								<option value="en_CA" <?php echo (($site_lang=='en_CA') ? 'selected' : '' ) ?>  >English (Canada)</option>
								<option value="en_GB" <?php echo (($site_lang=='en_GB') ? 'selected' : '' ) ?>  >English (UK)</option>
								<option value="en_NZ" <?php echo (($site_lang=='en_NZ') ? 'selected' : '' ) ?>  >English (New Zealand)</option>
							</select>
						</div>
						<div class="form-check" style="margin-bottom: 20px;">
						    <input type="checkbox" name="site_index" value="1" class="form-check-input" <?php echo (($site_indexing==1) ? 'checked' : '' ) ?> >
						    <label class="form-check-label" for="exampleCheck1" >Allow Search Engine to index site</label>
						</div>
                        <div class="form-group">
                            <input type="hidden" name="type" value="<?php echo $postType ?>" >
                            <input type="hidden" name="action" value="website" >
                            <input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
                            <input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
                            <input type="submit" id="updateWebsite" class="btn btn-green" value="Update Website" >
                        </div>
                    </div>
                    <div class='box-2'>
                        Hello
                    </div>
                </form>
            </div>

       <?php
       }
    }

?>