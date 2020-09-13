<?php

  include_once 'header.php';

?>
           <div class="form-box">
               <form id="login" >
                     <h1>Login<span>Sign in your account</span></h1>
                     <div class="form-group">
                       <input type="email" class="form-control" id="email" placeholder="Email address*" name="email" autofocus required >
                     </div>
                     <div class="form-group">
                       <input type="password" class="form-control" id="pass" placeholder="Password*" name="pass" required >
                     </div>
                     <div class="form-group">
                       <a href="forgot-password/" class="link">Forgot Password?</a>
                     </div>
                     <div class="form-group">
                      <input type="hidden" name="type" value="auth" >
                      <input type="hidden" name="action" value="login" >
                      <input type="hidden" name="url" value="<?php echo $redirectUrl ?>" >
                      <input type="submit" id="loginBtn" class="btn btn-block btn-success" value="Log In">
                     </div>
                     <div class="form-group">
                       <!-- <p class="center-link">Don't have an Account? <a href="register/" class="center-link">Join Now</a></p> -->
                     </div>
               </form>
               <!-- <p class="footer">By joining <?php echo CREDIT ?>, you agree to our <a href="//itssoftworld.com/terms-and-services/" title="Terms of Service">Terms of Service</a> and <a href="//itssoftworld.com/privacy-policy/" title="Privacy Policy">Privacy Policy</a></p> -->
           </div>
<?php

  include_once 'footer.php';

?>