<?php

  include_once 'header.php';

?>
           <div class="form-box">
               <form id="update" method="POST" >
                     <h1>Update your password.<span>Change password of <strong><?php echo base64_decode($_GET['email']); ?></strong></span></h1>
                     <div class="beforeClick">
                      <div class="form-group">
                        <input type="password" class="form-control" id="password" placeholder="Enter new password" name="pass" required >
                      </div>
                      <div class="form-group">
                        <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm passowrd" name="cnf_pass" required >
                      </div>
                      <div class="form-group">
                        <input type="hidden" name="type" value="auth" >
                        <input type="hidden" name="action" value="updatePassword" >
                        <input type="hidden" name="transaction" value="<?php echo $_GET['transaction'] ?>" >
                        <input type="hidden" name="email" value="<?php echo $_GET['email'] ?>" >
                        <input type="submit" id="updatePassword" class="btn btn-block btn-success" value="Change Password">
                      </div>
                    </div>
                    <div class="afterClick" style='display: none;' >
                      <p><span id="statusMsg"></span> <a href='/login/?email=<?php echo $_GET['email'] ?>'>click here to login</a></p>
                    </div>
               </form>
           </div>
<?php

  include_once 'footer.php';

?>