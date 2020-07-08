<?php

  include_once 'header.php';

?>
           <div class="form-box">
               <form id="register">
                     <h1>Register<span>Get your free account now</span></h1>
                     <div class="form-group">
                       <input type="text" name="first_name" id="first_name" placeholder="First name*" class="form-control" autofocus required />
                     </div>
                     <div class="form-group">
                       <input type="text" name="last_name" id="last_name" placeholder="Last name*" class="form-control" required />
                     </div>
                     <div class="form-group">
                       <input type="text" name="mobile" id="mobile" placeholder="Mobile no*" class="form-control" required />
                     </div>
                     <div class="form-group">
                       <input type="email" class="form-control" id="email" placeholder="Email address*" name="email" required />
                     </div>
                     <div class="form-group">
                       <input type="password" class="form-control" id="password" placeholder="Password*" name="password" required />
                     </div>
                     <div class="form-group">
                       <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm Password*" name="confirm_password" required />
                     </div>
                     <div class="form-group" >
                      <input type="hidden" name="type" value="auth" >
                      <input type="hidden" name="action" value="register" >
                      <input type="submit" id="registerBtn" class="btn btn-block btn-success" value="Register Now">
                     </div>
                     <div class="form-group">
                       <p class="center-link">Already have an Account? <a href="login/" class="center-link">Log In</a></p>
                     </div>
               </form>
               <p class="footer">By joining ITS Soft World, you agree to our <a href="terms-and-services/" title="Terms of Service">Terms of Service</a> and <a href="privacy-policy/" title="Privacy Policy">Privacy Policy</a></p>
           </div>
<?php

  include_once 'footer.php';

?>