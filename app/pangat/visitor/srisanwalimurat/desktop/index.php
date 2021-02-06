<?php

	/** Check month range setting is already defined or not */

	if($checkSetting=mysqli_query($connection, "SELECT site_configuration_value as calender_month_visible FROm ".TABLE_PREFIX."site_configuration WHERE site_configuration_site_id=".APP_SITE_ID." AND site_configuration_name='calender_month_visible'")) {

		if(mysqli_num_rows($checkSetting)!=0) {

			extract(mysqli_fetch_assoc($checkSetting));

		} else {

			$calender_month_visible=2;

		}

	}


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
		  	Sri Sanwali Murat
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
					  <div class="calendar-body-wraper d-none">
					  	<ul class="calendar-dates vanilla-calendar-body m-0 p-0" id="calendardates">
							</ul>
					  </div>
						<div class="retryButton mb-3 mt-3 working text-center">
							<!-- <p class="mb-1">Click to Load again</p> -->
							<i class="fa fa-refresh display-4"></i>
						</div>
						<div class="dtl-box mt-4">
							<span class="d-inline-block slot-booked-not-confirmed"></span>
							<span>Request received</span>
							<span class="d-inline-block date-blocked"></span>
							<span>Booking date blocked by admin</span>
							<p>
								Note: In Case of any issue, please call Mandir at 011-2125452
							</p>
						</div>
					</div>
				</div>
				<div class="col-lg-5 col-md-6 pl-md-0 pr-md-0">
					<div class="no-booking">Booking Not Available / Pending for the <span></span></div>
					<div class="bookingSlots active pl-3 pr-3">
						<div class="row">
				  		<div class="col-md-4 pl-1 pr-1">
						  	<div class="breakfast-btn com-sch bor-rounded" booking-for="breakfast">
						  		<div class="d-flex justify-content-center align-items-center">
						  			<div class="d-inline-block left-icon pl-1 pr-1">
									  	<i class="fa fa-utensils"></i>
						  			</div>
						  			<div class="d-inline-block pl-1 pr-1">
						  				<p class="sch mb-0 text-white">Breakfast</p>
						  				<p class="time-sch mb-0">8:00 - 10:00</p>
						  			</div>
						  		</div>
						  	</div>
				  		</div>
				  		<div class="col-md-4 pl-1 pr-1">
						  	<div class="lunch-btn com-sch bor-rounded" booking-for="lunch">
						  		<div class="d-flex justify-content-center align-items-center">
						  			<div class="d-inline-block left-icon pl-1 pr-1">
									  	<i class="far fa-sun"></i>
						  			</div>
						  			<div class="d-inline-block pl-1 pr-1">
						  				<p class="sch mb-0 text-white">Lunch</p>
						  				<p class="time-sch mb-0">8:00 - 10:00</p>
						  			</div>
						  		</div>
						  	</div>
				  		</div>
				  		<div class="col-md-4 pl-1 pr-1">
						  	<div class="dinner-btn com-sch bor-rounded" booking-for="dinner">
						  		<div class="d-flex justify-content-center align-items-center">
						  			<div class="d-inline-block left-icon pl-1 pr-1">
									  	<i class="far fa-moon"></i>
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
					  	<div class="booking-select text-danger d-none">Select any booking Schedule</div>
					  	<div class="form-cont">
					  		<p class="color-red">Name is Required</p>
					  		<input type="text" name="fname" class="firstName inputClass reqInput" placeholder="First Name*" required>
					  	</div>
					  	<div class="form-cont">
					  		<input type="text" name="lname" class="lastName inputClass" placeholder="Last Name">
					  	</div>
					  	<div class="form-cont">
					  		<p class="color-red">Email is Required</p>
					  		<input type="email" name="ename" class="emailName inputClass reqInput" placeholder="Email" required>
					  	</div>
					  	<div class="form-cont">
					  		<p class="color-red">Enter a valid mobile number</p>
					  		<input type="text" name="mname" class="mobName inputClass phoneVerify reqInput" placeholder="Mobile Number" required min="10" max="10" pattern="[6-9]{1}[0-9]{9}" title="Enter Your 10 Digit Mobile Number only">
					  	</div>
					  	<div class="form-cont mb-4">
					  		<input type="checkbox" id="guest" name="box" class="inputBox" checked >
					  		<spn class="d-inline-block">I will be present in pangat.</span>
					  	</div>
					  	<div id="checkBox" class="form-cont">
					  		<p class="color-red">Enter number of guests</p>
					  		<input type="text" name="guest" class="inputClass guestVal reqInput" placeholder="Number of guest">
					  	</div>
						<input type="hidden" name="acceptPayment" value="1" id="acceptPayment" >
						<input type="hidden" name="amount" value="1000" id="pangatAmount" >
					  	<button class="submit-data">Send request</button>
					  	<div class="serverError d-none text-danger">Something went wrong Please try again</div>
					  </div>
				  </div>
				</div>
			</div>
	  </div>
		
	</section>

	<!-- Booking confirmation screen -->

	<section class="pt-5 bookingConfirmationScreen " style="display:none" >
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="headingDate text-center">
						<h2 class="h4 pt-3 font-weight-bold">
							Pangat Booking Confirmation
						</h2>
						<p class="">Please confirm your booking details</p>
					</div>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-md-8 col-xs-12 bookingConfirmation ">
					<div class="row">
						<div class="col-4">
							<p>Name</p>
						</div>
						<div class="col-8">
							<p id="nameConfirmation"></p>
						</div>
					</div>
					<div class="row">
						<div class="col-4">
							<p>Email</p>
						</div>
						<div class="col-8">
							<p id="emailConfirmation"></p>
						</div>
					</div>
					<div class="row">
						<div class="col-4">
							<p>Mobile</p>
						</div>
						<div class="col-8">
							<p id="mobileConfirmation"></p>
						</div>
					</div>
					<div class="row">
						<div class="col-4">
							<p>Booking date</p>
						</div>
						<div class="col-8">
							<p id="bookingDateConfirmation"></p>
						</div>
					</div>
					<div class="row">
						<div class="col-4">
							<p>Booking time</p>
						</div>
						<div class="col-8">
							<p id="bookingTimeConfirmation"></p>
						</div>
					</div>
					<div class="row">
						<div class="col-4">
							<p>Are you coming</p>
						</div>
						<div class="col-8">
							<p id="comingConfirmation"></p>
						</div>
					</div>
					<div class="row guestComing" style="display: none" >
						<div class="col-4">
							<p>No of guest</p>
						</div>
						<div class="col-8">
							<p id="guestConfirmation"></p>
						</div>
					</div>
					<div class="row" style="margin-bottom: 10px" >
						<div class="col-4">
							<p>Donation amount</p>
						</div>
						<div class="col-8">
							<input type="text" name="donation" id="donationAmount" class="form-control" placeholder="If you want to donate any amount." >
						</div>
					</div>
					<div class="row justify-content-center">
						<div class="col-md-4 col-xs-12">
							<a href="#" id="editForm" class="btn btn-primary btn-block" >Edit</a>
						</div>
						<div class="col-2" style="height: 10px" ></div>
						<div class="col-md-4 col-xs-12">
							<input type="hidden" id="calenderMonthLimit" value="<?php echo $calender_month_visible ?>" >
							<input type="submit" id="submitForm" class="btn btn-success btn-block" value="Continue" >
						</div>
					</div>
				</div>
			</div>
	  </div>
		
	</section>

    <script src="<?php echo JS_LIBRARY ?>jquery.min.js"></script>
    <script src="<?php echo JS_LIBRARY ?>popper.min.js"></script>
    <script src="<?php echo JS_LIBRARY ?>bootstrap/bootstrap.min.js"></script>
	<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" ></script>
    <script type="text/javascript" src="<?php echo USER_JS ?>script.min.js"></script>

</body>
</html>