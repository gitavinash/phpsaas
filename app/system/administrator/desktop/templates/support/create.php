<?php

    if($checkUser=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."user, ".TABLE_PREFIX."profile WHERE user_id='".USER_ID."' AND profile_user_id='".USER_ID."' AND user_status=1")) {
        if(mysqli_num_rows($checkUser)==0) {
            echo '<h1>invalid request found</h1>';
        } else {
            
            extract(mysqli_fetch_assoc($checkUser));

            ?>

            <h2>Create New Support Ticket</h2>

            <div class="form-box">
                <div class="box-2">
                    <form method="POST" id="profileData" >
                        <div class="form-group">
                            <label>Reason*</label>
                            <input type="text" name="title" class="form-control inside-shadow" placeholder="Reason for creating ticket" required >
                        </div>
                        <div class="form-group">
                            <label>Describe Problem in Detail*</label>
                            <textarea name="message" class="form-control inside-shadow" placeholder="Mention problem details" style="height:300px" required ></textarea>
                        </div>
                        <div class="form-group">
                            <label>Attach Screenshot</label>
                            <input type="file" name="screenshot" class="form-control inside-shadow" >
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="type" value="<?php echo $postType ?>" >
                            <input type="hidden" name="action" value="create" >
                            <input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
                            <input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
                            <input type="submit" id="updateProfile" class="btn btn-green" value="Create Ticket" >
                        </div>
                    </form>
                </div>
                <div class="box-2" style="float: left; width: 250px; margin-left: 50px;" >
                   
                   
                </div>

            </div>

       <?php
       }
    }

?>