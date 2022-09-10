<?php

  // Setup id is refer to email of the user

  if(!isset($_GET['setup_id'])) {
    echo "Something is wrong. <a href='/logout/'>Click here to try again</a>";
    exit();
  }

  // check setup has some value

  if($_GET['setup_id']=='') {
    echo "Invalid setup id found. <a href='/logout/'>Click here to try again</a>";
    exit();
  }

  /** Find the website setup details to relavant account */

  $websiteSetupEmail=mysqli_real_escape_string($connection, base64_decode($_GET['setup_id']));

  if($checkUser=mysqli_query($connection, "SELECT user_id, user_username, site_id, site_status FROM ".TABLE_PREFIX."user INNER JOIN ".TABLE_PREFIX."site_access ON site_access_user_id=user_id INNER JOIN ".TABLE_PREFIX."site ON site_id=site_access_site_id WHERE user_email='{$websiteSetupEmail}'")) {

    if(mysqli_num_rows($checkUser)==0) {
      
      echo "Invalid setup id found. <a href='/logout/'>Click here to try again</a>";
      exit();

    } else {

      extract(mysqli_fetch_assoc($checkUser));

      /** Check website setup is complete or not */

      if($site_status!=0) {

        header("Location: /login/", TRUE, 301);
        exit();

      }

    }

  }
  
  include_once 'header.php';

?>
           <div class="form-box">
               <form id="websiteSetup" >
                     <h1>Website Setup<span>Finish your website setup</span></h1>
                     <div class="form-group username">
                       <label for="username">Username*</label>
                       <input type="text" class="form-control" id="username" placeholder="Username*" name="username" value="<?php echo $user_username ?>" readonly >
                     </div>
                     <div class="form-group">
                       <label for="domain" >Domain*</label>
                       <input type="text" class="form-control" id="domain" placeholder="Domain*" name="domain" autofocus required >
                     </div>
                     <div class="form-group">
                       <label for="app">Application*</label>
                       <select name="app" class="form-control" id="app">
                         <option value="">Select Application*</option>
                         <?php
                          if($appList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."app WHERE app_status=1 AND app_visiblity=1")) {
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
                      <input type="hidden" name="type" value="install" >
                      <input type="hidden" name="action" value="domain" >
                      <input type="hidden" name="username" value="<?php echo $user_username ?>" >
                      <input type="submit" id="websiteSetupBtn" class="btn btn-block btn-success" value="Finish Setup">
                     </div>
               </form>
           </div>
<?php

  include_once 'footer.php';

?>