<?php

  include_once 'header.php';

?>
           <div class="form-box">
				<form action="" method="POST" >
                    <a href="//itssoftworld.com" target="_blank" ><img src="<?php echo IMG_LIBRARY ?>logo.png" alt="ITS Soft World" style="float: left; width: 100%; padding: 0px 40px; margin-bottom: 50px" ></a>
                    <p><?php echo $message ?> <a href="/login/">click here to login</a></p>
				</form>
				<p class="footer">By joining <?php echo CREDIT ?>, you agree to our <a href="terms-and-services/" title="Terms of Service">Terms of Service</a> and <a href="privacy-policy/" title="Privacy Policy">Privacy Policy</a></p>
			</div>
<?php

  include_once 'footer.php';

?>