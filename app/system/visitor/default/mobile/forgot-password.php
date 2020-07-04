<?php

  include_once 'header.php';

?>
           <div class="form-box">
               <form id="forgot">
                  <h1>Forgot your password?<span>We'll email you instructions on how to reset it</span></h1>
                  <div class="beforeClick">
                    <div class="form-group">
                      <input type="email" class="form-control" id="email" placeholder="Email address" name="email" >
                    </div>
                    <div class="form-group">
                      <input type="hidden" name="type" value="auth" >
                      <input type="hidden" name="action" value="forgotPassword" >
                      <input type="submit" id="forgotPassword" class="btn btn-block btn-success" value="Send Instructions">
                    </div>
                    <div class="form-group">
                      <p class="center-link">Know your password? <a href="login/" class="center-link">Log In</a></p>
                    </div>
                  </div>
                  <div class="afterClick">
                    <p id='statusMsg'></p>
                  </div>
               </form>
           </div>
<?php

  include_once 'footer.php';

?>