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
                            <label>Facebook ID <span>( <a href='https://findmyfbid.com/' target='_blank' style='color: blue; text-decoration: none' >click here to find your page or profile id</a> )</span></label>
                            <input type="text" name="site_domain" value='<?php echo HTTP_PROTOCOL.$site_domain ?>' class="form-control inside-shadow" placeholder="Website Domain" >
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="type" value="<?php echo $postType ?>" >
                            <input type="hidden" name="action" value="social" >
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