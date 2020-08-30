<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title><?php echo SITE_NAME ?></title>
<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
<link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
</head>
<body>
 <div class="container-fluid">
  <div class="row">
   <div class="col-md-4"></div>
   <div class="col-md-4 bg-sky position-relative main">

    <div class="d-flex justify-content-center w-100">
        <img src="<?php echo SITE_LOGO ?>" class="rounded-circle profile mt-5" alt="">
    </div>
   
    <div class="signup w-100">
    <form>
     <input type="number" name="mobile_no" placeholder="Mobile No." class="form-control mb-4 w-100">
     <button class="btn dark-blue w-100 text-white">Continue with Mobile</button>
    </form>
    </div>
    
   </div>
   <div class="col-md-4"></div>
  </div>
 </div>
</body>
</html>