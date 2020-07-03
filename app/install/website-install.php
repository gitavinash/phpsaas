<?php

	include_once 'header.php';

?>

<div class="container">
	<div class="row justify-content-md-center">
		<div class="col-12 col-md-4">
			<div id="loginBox">
				<form id="installWebsite" >
					<a href="//itssoftworld.com" target="_blank" ><img src="/lib/resource/ui/img/logo.png" alt="ITS Soft World" style="float: left; width: 100%; padding: 0px 40px; margin-bottom: 50px" ></a>
					<h1>Installation <span>Site Configuration</span></h1>
					<div class="form-group">
						<label>Site Name</label>
						<input type="text" id="siteName" class="form-control" name="site_name" >
					</div>
					<div class="form-group">
						<label >About Site</label>
						<textarea name="site_desc" id="siteDesc" class="form-control" ></textarea>
					</div>
					<div class="form-group">
						<label>Email</label>
						<input type="email" id="email" class="form-control" name="email" >
					</div>
					<div class="form-group">
						<label>Password</label>
						<input type="password" id="password" class="form-control" name="password" >
					</div>
					<div class="form-group">
						<label>Confirm Password</label>
						<input type="password" id="confirmPassword" class="form-control" name="cnf_password" >
					</div>
					<div class="form-group">
						<label>Language</label>
						<select name="site_lang" id="siteLang" class="form-control" required >
							<option value="">Salect language</option>
							<option value="en" selected >English</option>
							<option value="hi" >हिंदी</option>
						</select>
					</div>
					<div class="form-group" style="float:left; margin-top: 20px">
						<input type="hidden" name="site_index" value="noindex, nofollow" >
						<input type="hidden" name="type" value="install" >
						<input type="hidden" name="action" value="website" >
						<input type="submit" id="installBtn" class="btn btn-success" value="Install Now" >
					</div>
				</form>
			</div>
			<div id="thankYouBox" style="display:none" >
				<form action="" method="POST" >
					<a href="//itssoftworld.com" target="_blank" ><img src="/lib/resource/ui/img/logo.png" alt="ITS Soft World" style="float: left; width: 100%; padding: 0px 40px; margin-bottom: 50px" ></a>
					<h1>Installation <span>Installation Complete</span></h1>
                    <p>The installation process is now complete and verification mail has been sent to your email kindly verify your email.</p>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

	include_once 'footer.php';

?>