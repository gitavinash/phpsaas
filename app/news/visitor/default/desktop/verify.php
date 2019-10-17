<?php

  if(isset($_COOKIE['isLogin'])) {
    header("Location: /dashboard/");
    exit();
  }

?>

<!DOCTYPE html>
<html lang="en">

    <head>
    	<base href="<?php echo MAIN_DOMAIN ?>/" >
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" value="index" >
        <link rel="icon" href="https://punjabkesari.com/wp-content/uploads/2018/05/cropped-favicon-1-192x192.png">
        <title>Verification | <?php echo SITE_NAME ?></title>
        <link rel="canonical" href="http://www.shaktipedia.com/durga-chalisa/" />
        <meta name="theme-color" content="#FE6F06">
        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>login.css">
        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>all.min.css" >
        <link href="https://fonts.googleapis.com/css?family=Fira+Sans+Condensed:300,400,700" rel="stylesheet">
    </head>

    <body>

       <section>
           <div class="form-box">
               <form method="POST" >
                     <div class="form-group">
                       <p class="center-link"><?php echo $message ?> <a href='/login/'>click here to login</a></p>
                     </div>
               </form>
               <p class="footer">By joining ITS Soft World, you agree to our <a href="terms-and-services/" title="Terms of Service">Terms of Service</a> and <a href="privacy-policy/" title="Privacy Policy">Privacy Policy</a></p>
           </div>
        </section>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="<?php echo USER_JS ?>app.js"></script>

    </body>

</html>