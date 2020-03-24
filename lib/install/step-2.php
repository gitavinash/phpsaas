<!DOCTYPE html>
<html lang="en">
    <head>
    	<base href="/" >
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" value="noindex" >
        <title>Application Configuration | Installation</title>
        <link rel="stylesheet" href="/lib/install/assets/css/bootstrap.min.css" >
        <style>
        	body {
        		background: #f9f9f9;
        	}
        	form {
        		float: left;
			    margin: 50px 0px;
			    padding: 20px 5%;
			    border: 1px solid #f7f7f7;
			    box-shadow: 0px 0px 0px 0px #333;
			    width: 100%;
			    background: #fff;
			    border-radius: 4px;
        	}
        	form label {
        		float: left;
        	}
        	h1 {
        		float: left;
        		width: 100%;
        		font-size: 32px;
        		margin-bottom: 30px;
			    text-align: center;
        	}
        	h1 span {
        		float: left;
        		width: 100%;
        		font-size: 14px;
        		margin-top: 20px;
        		color: #555;
			    text-align: center;
        	}
        </style>
	</head>
	<body>

		<main>
			
			<div class="container">
				<div class="row justify-content-md-center">
					<div class="col-12 col-md-4">
						<form action="" method="POST" >
							<a href="//itssoftworld.com" target="_blank" ><img src="/lib/install/assets/images/logo.png" alt="ITS Soft World" style="float: left; width: 100%; padding: 0px 40px; margin-bottom: 50px" ></a>
							<h1>Installation <span>Application Configuration</span></h1>

							<div class="form-group">
								<label>Select Application</label>
								<select name="site_app" class="form-control" required >
									<option value="">Application list</option>
									<?php
										if($appList=mysqli_query($connection, "SELECT directory_name, directory_path FROM ".TABLE_PREFIX."directory WHERE directory_status=1 AND directory_type='app'")) {
											while($app=mysqli_fetch_assoc($appList)) {
												echo "<option value='{$app['directory_path']}'>{$app['directory_name']}</option>";
											}
										}
									?>
								</select>
							</div>

							<div class="form-group">
								<label>Select website language</label>
								<select name="site_lang" class="form-control" required >
									<option value="">Language list</option>
									<option value="hi_IN" >हिंदी</option>
									<option value="en" >English (United States)</option>
									<option value="en_AU" >English (Australia)</option>
									<option value="en_ZA" >English (South Africa)</option>
									<option value="en_CA" >English (Canada)</option>
									<option value="en_GB" >English (UK)</option>
									<option value="en_NZ" >English (New Zealand)</option>
								</select>
							</div>
							
							<div class="form-group" style="float:left; margin-top: 20px">
								<input type="hidden" name="type" value="install" >
								<input type="hidden" name="user_id" value="<?php echo $site_owner_id; ?>" >
								<input type="hidden" name="action" value="application" >
								<input type="submit" class="btn btn-success" value="Install Now" >
							</div>
						</form>
					</div>
				</div>
			</div>

		</main>

	</body>
</html>