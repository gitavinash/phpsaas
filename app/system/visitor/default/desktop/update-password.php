<?php

  if(isset($_COOKIE['isLogin'])) {
    header("Location: /dashboard/");
    exit();
  }

?>

<!DOCTYPE html>
<html lang="en">

    <head>
    	<base href="<?php echo SITE_URL ?>/" >
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" value="index" >
        <link rel="icon" href="<?php echo SITE_ICON ?>">
        <title>Forgot Password | <?php echo SITE_NAME ?></title>
        <link rel="canonical" href="http://www.shaktipedia.com/durga-chalisa/" />
        <meta name="theme-color" content="#FE6F06">
        <link rel="stylesheet" href="<?php echo SYSTEM_STYLESHEET ?>bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo SYSTEM_STYLESHEET ?>login.css">
        <link rel="stylesheet" href="<?php echo SYSTEM_STYLESHEET ?>all.min.css" >
        <link href="https://fonts.googleapis.com/css?family=Fira+Sans+Condensed:300,400,700" rel="stylesheet">
    </head>

    <body>

       <section>
           <div class="form-box">
               <form id="update" method="POST" >
                     <h1>Update your password.<span>Change password of <?php echo base64_decode($_GET['email']); ?></span></h1>
                     <div class="beforeClick">
                      <div class="form-group">
                        <input type="password" class="form-control" placeholder="Enter new password" name="pass" required >
                      </div>
                      <div class="form-group">
                        <input type="password" class="form-control" placeholder="Confirm passowrd" name="cnf_pass" required >
                      </div>
                      <div class="form-group">
                        <input type="hidden" name="type" value="authentication" >
                        <input type="hidden" name="action" value="updatePassword" >
                        <input type="hidden" name="transaction" value="<?php echo $_GET['transaction'] ?>" >
                        <input type="hidden" name="email" value="<?php echo $_GET['email'] ?>" >
                        <input type="submit" id="updatePassword" class="btn btn-block btn-success" value="Change Password">
                      </div>
                    </div>
                    <div class="afterClick" style='display: none;' >
                      <p><span id="statusMsg"></span> <a href='/login/'>click here to login</a></p>
                    </div>
               </form>
               <p class="footer">By joining ITS Soft World, you agree to our <a href="terms-and-services/" title="Terms of Service">Terms of Service</a> and <a href="privacy-policy/" title="Privacy Policy">Privacy Policy</a></p>
           </div>
        </section>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="<?php echo SYSTEM_PLUGINS ?>sweetalert/sweetalert2.all.min.js"></script>
        <script src="<?php echo SYSTEM_JS ?>app.js"></script>

    </body>

</html>