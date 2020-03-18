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
			
		</div>
	</div>
</section>

<?php

	include_once 'footer.php';

?>