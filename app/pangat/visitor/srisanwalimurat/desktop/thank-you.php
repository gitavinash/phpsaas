<?php

	$bookingID=base64_decode($id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&family=Playfair+Display&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
  <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
  <title>Booking Slot</title>
</head>
<body class="bg-body">
	<header>
		<div class="container">
			<nav class="navbar navbar-expand-lg navbar-light pt-3 pb-0 pt-md-3 pb-md-0 pl-md-0 pr-md-0">
	      <a href="#" class="navbar-brand">
		  	Sri Sanwali Murati
	        <!-- <img src="assets/img/logo.png" alt="" class="img-fluid"> -->
	      </a>
	      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="navbar-toggler-icon"></span>
	      </button>
	      <div class="collapse navbar-collapse position-relative" id="navbarSupportedContent">
	        <ul class="navbar-nav ml-auto borderTheUl position-relative">
	          <li class="nav-item"><a href="#" class="nav-link pl-3 pr-3 pt-2 pb-2">Publication</a></li>
	          <li class="nav-item"><a href="#" class="nav-link pl-3 pr-3 pt-2 pb-2">Gallery</a></li>
	          <li class="nav-item"><a href="#" class="nav-link pl-3 pr-3 pt-2 pb-2">Events</a></li>
	          <li class="nav-item"><a href="#" class="nav-link pl-3 pr-3 pt-2 pb-2">Discourses</a></li>
	          <li class="nav-item"><a href="#" class="nav-link pl-3 pr-3 pt-2 pb-2">Bhajan/Aarti</a></li>
	        </ul>
	        <a href="" class="d-inline-flex align-items-center justify-content-center w-auto navReqButton shadow">
	        	Request for Pangat
	        </a>
	      </div>
	    </nav>
		</div>
	</header>

	<!-- Booking form screen -->

	<section class="pt-5 bookingFormScreen" >
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="headingDate text-center">
						<h2 class="h4 pt-3 font-weight-bold">
							Pangat Booking
						</h2>
						<p class="">Thank your for your payment</p>
					</div>
				</div>
			</div>
	  </div>
		
	</section>

	<?php include_once NOTIFICATION.'index.php' ?>

    <script src="<?php echo JS_LIBRARY ?>popper.min.js"></script>
    <script src="<?php echo JS_LIBRARY ?>bootstrap/bootstrap.min.js"></script>
	<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" ></script>
    <script type="text/javascript" src="<?php echo USER_JS ?>script.min.js"></script>

</body>
</html>