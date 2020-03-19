<?php

	include_once 'header.php';

?>

<section>
	<div class="slider">
		<div class="booking">
			<div class="inner-box">
				<form method="get" action="/search/hotel/" >
					<div class="form-group">
						<select class="city"  name="city">
							<option value="">Select City</option>
							<option value="Manali">Manali</option>
							<option value="Shimla">Shimla</option>
							<option value="Delhi">Delhi</option>
							<option value="Jaipur">Jaipur</option>
						</select>
					</div>
					<div class="form-group">
						<input type="text" class="datepicker" name="date" placeholder="<?php echo date('d-M-Y').' - '.date('d-M-Y', strtotime('+2 Days')); ?>" >
					</div>
					<div class="form-group">
						<input type="text" class="room" name="rooms" placeholder="1 Room" >
					</div>
					<div class="form-group">
						<input type="text" class="person" name="person" placeholder="2 Guest" >
					</div>
					<div class="form-group">
						<input type="submit" class="searchBtn" value="Search" >
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<section>
	<div class="inner-box">
		<div class="destination-list">
			<h2>Popular Destinations</h2>
			<div class="left-box">
				<div class="destination">
					<div class="img">
						<img src="<?php echo USER_IMAGES ?>manali-hidimba-devi-temple.jpg" alt="">
					</div>
					<div class="text">
						<h3>Manali <span>10 Hotels</span></h3>
					</div>
				</div>
			</div>
			<div class="right-box">
				<div class="destination">
					<div class="img">
						<img src="<?php echo USER_IMAGES ?>manali-hidimba-devi-temple.jpg" alt="">
					</div>
					<div class="text">
						<h3>Shimla <span>10 Hotels</span></h3>
					</div>
				</div>
				<div class="destination">
					<div class="img">
						<img src="<?php echo USER_IMAGES ?>manali-hidimba-devi-temple.jpg" alt="">
					</div>
					<div class="text">
						<h3>Jailsalmer <span>10 Hotels</span></h3>
					</div>
				</div>
				<div class="destination">
					<div class="img">
						<img src="<?php echo USER_IMAGES ?>manali-hidimba-devi-temple.jpg" alt="">
					</div>
					<div class="text">
						<h3>Rishikesh <span>10 Hotels</span></h3>
					</div>
				</div>
				<div class="destination">
					<div class="img">
						<img src="<?php echo USER_IMAGES ?>manali-hidimba-devi-temple.jpg" alt="">
					</div>
					<div class="text">
						<h3>Jim Corbett <span>10 Hotels</span></h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="bg-404397">
	<div class="inner-box">
		<h2 class="c-fff" >Popular Hotels</h2>
		<div class="hotel-list">
			<div class="img">
				<img src="<?php echo USER_IMAGES ?>manali-hidimba-devi-temple.jpg" alt="">
			</div>
			<div class="text">
				<h3>Jim Corbett <span>10 Hotels</span></h3>
			</div>
		</div>
		<div class="hotel-list">
			<div class="img">
				<img src="<?php echo USER_IMAGES ?>manali-hidimba-devi-temple.jpg" alt="">
			</div>
			<div class="text">
				<h3>Jim Corbett <span>10 Hotels</span></h3>
			</div>
		</div>
		<div class="hotel-list">
			<div class="img">
				<img src="<?php echo USER_IMAGES ?>manali-hidimba-devi-temple.jpg" alt="">
			</div>
			<div class="text">
				<h3>Jim Corbett <span>10 Hotels</span></h3>
			</div>
		</div>
		<div class="hotel-list">
			<div class="img">
				<img src="<?php echo USER_IMAGES ?>manali-hidimba-devi-temple.jpg" alt="">
			</div>
			<div class="text">
				<h3>Jim Corbett <span>10 Hotels</span></h3>
			</div>
		</div>
	</div>
</section>

<?php

	include_once 'footer.php';

?>