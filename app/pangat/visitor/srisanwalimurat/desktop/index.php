<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&family=Playfair+Display&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
  <title>Booking Slot</title>
</head>
<body class="bg-body">
	<header>
		<div class="container">
			<nav class="navbar navbar-expand-lg navbar-light pt-3 pb-0 pt-md-3 pb-md-0 pl-md-0 pr-md-0">
	      <a href="#" class="navbar-brand">
	      	AreaOfLogo
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
	<section class="pt-5">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="headingDate text-center">
						<h2 class="h4 pt-3 font-weight-bold">
							Select Date and Time 
						</h2>
						<p class="">Choose a date below to place a request for pangat</p>
					</div>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-lg-7 col-md-6">
					<div id="myCalendar" class="vanilla-calendar">
				  	<div class="vanilla-calendar-header">
				      <button class="vanilla-calendar-btn" id="prearrow" disabled="">
				      	<i class="fa fa-chevron-left"></i>
				      </button>
				      <div class="vanilla-calendar-header__label" id="month"></div>
				      <button class="vanilla-calendar-btn" id="nexarrow">
				      	<i class="fa fa-chevron-right"></i>
				      </button>
					  </div>
					  <div class="vanilla-calendar-week">
				      <span>S</span>
				      <span>M</span>
				      <span>T</span>
				      <span>W</span>
				      <span>T</span>
				      <span>F</span>
				      <span>S</span>
					  </div>
						<ul class="calendar-dates vanilla-calendar-body m-0 p-0" id="calendardates">
						</ul>
						<div class="dtl-box mt-4">
							<span class="d-inline-block slot-booked-not-confirmed"></span>
							<span>Request received</span>
							<p>
								Note: In Case of any issue, please call Mandir at 011-2125452
							</p>
						</div>
					</div>
				</div>
				<div class="col-lg-5 col-md-6 pl-md-0 pr-md-0">
					<div class="no-booking">No Booking Available for the <span></span></div>
					<div class="bookingSlots pl-3 pr-3">
						<div class="row">
				  		<div class="col-md-4 pl-1 pr-1">
						  	<div class="breakfast com-sch bor-rounded" booking-for="breakfast">
						  		<div class="d-flex justify-content-center align-items-center">
						  			<div class="d-inline-block pl-1 pr-1">
						  				<i class="fa fa-cutlery"></i>
						  			</div>
						  			<div class="d-inline-block pl-1 pr-1">
						  				<p class="sch mb-0 text-white">Breakfast</p>
						  				<p class="time-sch mb-0">8:00 - 10:00</p>
						  			</div>
						  		</div>
						  	</div>
				  		</div>
				  		<div class="col-md-4 pl-1 pr-1">
						  	<div class="lunch com-sch bor-rounded" booking-for="lunch">
						  		<div class="d-flex justify-content-center align-items-center">
						  			<div class="d-inline-block pl-1 pr-1">
						  				<i class="fa fa-sun-o"></i>
						  			</div>
						  			<div class="d-inline-block pl-1 pr-1">
						  				<p class="sch mb-0 text-white">Lunch</p>
						  				<p class="time-sch mb-0">8:00 - 10:00</p>
						  			</div>
						  		</div>
						  	</div>
				  		</div>
				  		<div class="col-md-4 pl-1 pr-1">
						  	<div class="dinner com-sch bor-rounded" booking-for="dinner">
						  		<div class="d-flex justify-content-center align-items-center">
						  			<div class="d-inline-block pl-1 pr-1">
						  				<i class="fa fa-moon-o"></i>
						  			</div>
						  			<div class="d-inline-block pl-1 pr-1">
						  				<p class="sch mb-0 text-white">Dinner</p>
						  				<p class="time-sch mb-0">8:00 - 10:00</p>
						  			</div>
						  		</div>
						  	</div>
				  		</div>
						</div>
					  <div class="forms-data">
					  	<div class="bookedForText">
					  		<div class="forbreakfast justify-content-between align-items-center">
					  			<div class="d-inline-block pl-1 pr-1">
					  				<p class="sch mb-0"><i class="fa fa-cutlery"></i> Breakfast</p>
					  			</div>
					  			<div class="d-inline-block pl-1 pr-1">
					  				<p class="time-sch mb-0">13-10-2020</p>
					  			</div>
					  		</div>
					  		<div class="forlunch justify-content-between align-items-center">
					  			<div class="d-inline-block pl-1 pr-1">
					  				<p class="sch mb-0"><i class="fa fa-sun-o"></i> Lunch</p>
					  			</div>
					  			<div class="d-inline-block pl-1 pr-1">
					  				<p class="time-sch mb-0">13-10-2020</p>
					  			</div>
					  		</div>
					  		<div class="fordinner justify-content-between align-items-center">
					  			<div class="d-inline-block pl-1 pr-1">
					  				<p class="sch mb-0"><i class="fa fa-moon-o"></i> Dinner</p>
					  			</div>
					  			<div class="d-inline-block pl-1 pr-1">
					  				<p class="time-sch mb-0">13-10-2020</p>
					  			</div>
					  		</div>
					  	</div>
					  	<div class="font-weight-bold mb-3">Enter Details to Confirmed</div>
					  	<div class="form-cont">
					  		<p class="color-red">Name is Required</p>
					  		<input type="text" name="fname" class="firstName inputClass reqInput" placeholder="First Name*">
					  	</div>
					  	<div class="form-cont">
					  		<input type="text" name="fname" class="lastName inputClass" placeholder="Last Name">
					  	</div>
					  	<div class="form-cont">
					  		<p class="color-red">Email is Required</p>
					  		<input type="text" name="fname" class="emailName inputClass reqInput" placeholder="Email">
					  	</div>
					  	<div class="form-cont">
					  		<p class="color-red">Mobile is Required</p>
					  		<input type="text" name="fname" class="MobName inputClass reqInput" placeholder="Mobile Number">
					  	</div>
					  	<div class="form-cont mb-4">
					  		<input type="checkbox" name="">
					  		<spn class="d-inline-block">I will be great in pangat.</span>
					  	</div>
					  	<button class="submit-data">Submit</button>
					  </div>
				  </div>
				</div>
			</div>
	  </div>
		
	</section>






  <script src="<?php echo JS_LIBRARY ?>jquery.min.js"></script>
  <script src="<?php echo JS_LIBRARY ?>popper.min.js"></script>
  <script src="<?php echo JS_LIBRARY ?>bootstrap/bootstrap.min.js"></script>
  <script type="text/javascript" src="<?php echo USER_JS ?>script.min.js"></script>

</body>
</html>