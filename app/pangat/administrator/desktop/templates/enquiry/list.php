<?php

	$postStatus=mysqli_real_escape_string($connection, $_GET['status']);
	$postStatus=(($postStatus=='all') ? '' : "AND booking_status='{$postStatus}'" );

	$selectedMonth=mysqli_real_escape_string($connection, ((isset($_GET['month'])) ? $_GET['month'] : '' ));
	$monthStatus=(($selectedMonth=='') ? '' : "AND month(booking_date) = {$selectedMonth}" );

	$selectedYear=mysqli_real_escape_string($connection, ((isset($_GET['year'])) ? $_GET['year'] : '' ));
	$yearStatus=(($selectedYear=='') ? '' : "AND year(booking_date) = {$selectedYear}" );
	
?>
<h2>All Enquiries</h2>
<div class="options-bar">
	<div class="left-box">
		<form method="GET">
			<input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" >
			<select class="form-control" name="status" id="changeStatus" >
				<option value="all" <?php echo (($_GET['status']=='all') ? 'selected' : '' ) ?> >All <?php echo $postType ?></option>
				<option value="0" <?php echo (($_GET['status']=='0') ? 'selected' : '' ) ?> >Pending <?php echo $postType ?></option>
				<option value="1" <?php echo (($_GET['status']=='1') ? 'selected' : '' ) ?>  >Confirm <?php echo $postType ?></option>
				<option value="2" <?php echo (($_GET['status']=='2') ? 'selected' : '' ) ?>  >Cancel <?php echo $postType ?></option>
			</select>
			<select class="form-control" name="month" id="changeMonth" >
				<option value="">Select Month</option>
				<option value="01" <?php echo (($selectedMonth=='01') ? 'selected' : '' ) ?> >January</option>
				<option value="02" <?php echo (($selectedMonth=='02') ? 'selected' : '' ) ?> >Febuary</option>
				<option value="03" <?php echo (($selectedMonth=='03') ? 'selected' : '' ) ?> >March</option>
				<option value="04" <?php echo (($selectedMonth=='04') ? 'selected' : '' ) ?> >April</option>
				<option value="05" <?php echo (($selectedMonth=='05') ? 'selected' : '' ) ?> >May</option>
				<option value="06" <?php echo (($selectedMonth=='06') ? 'selected' : '' ) ?> >June</option>
				<option value="07" <?php echo (($selectedMonth=='07') ? 'selected' : '' ) ?> >July</option>
				<option value="08" <?php echo (($selectedMonth=='08') ? 'selected' : '' ) ?> >August</option>
				<option value="09" <?php echo (($selectedMonth=='09') ? 'selected' : '' ) ?> >September</option>
				<option value="10" <?php echo (($selectedMonth=='10') ? 'selected' : '' ) ?> >October</option>
				<option value="11" <?php echo (($selectedMonth=='11') ? 'selected' : '' ) ?> >November</option>
				<option value="12" <?php echo (($selectedMonth=='12') ? 'selected' : '' ) ?> >December</option>
			</select>
			<select class="form-control" name="year" id="changeYear" >
				<option value="">Select Year</option>
				<option value="2020" <?php echo (($selectedYear=='2020') ? 'selected' : '' ) ?> >2020</option>
				<option value="2021" <?php echo (($selectedYear=='2021') ? 'selected' : '' ) ?> >2021</option>
				<option value="2022" <?php echo (($selectedYear=='2022') ? 'selected' : '' ) ?> >2022</option>
				<option value="2023" <?php echo (($selectedYear=='2023') ? 'selected' : '' ) ?> >2023</option>
				<option value="2024" <?php echo (($selectedYear=='2024') ? 'selected' : '' ) ?> >2024</option>
				<option value="2025" <?php echo (($selectedYear=='2025') ? 'selected' : '' ) ?> >2025</option>
			</select>
			<input type="submit" class="btn" value="Filter" >
		</form>
	</div>
	<div class="right-box">
		
	</div>
</div>
<table id="table">
	<thead>
		<tr>
			<th>Booking Time</th>
			<th>Booked Date</th>
			<th>Booked Slot</th>
			<th>Name</th>
			<th>Mobile</th>
			<th>Coming?</th>
			<th>Guest?</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php

			if($dataList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."booking INNER JOIN ".TABLE_PREFIX."enquiry ON enquiry_id=booking_enquiry_id WHERE booking_date >= NOW() {$postStatus} {$monthStatus} {$yearStatus} ORDER BY booking_date, FIELD(booking_time, 'breakfats', 'lunch', 'dinner') DESC")) {

					if(mysqli_num_rows($dataList)!=0) {
						
						while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
							<tr>
								<td><?php echo date('d-m-Y h:i A', strtotime($enquiry_time)) ?></td>
								<td><?php echo date('d-m-Y', strtotime($booking_date)) ?></td>
								<td><?php echo $booking_time; ?></td>
								<td><?php echo $enquiry_first_name.' '.$enquiry_last_name; ?></td>
								<td><?php echo $enquiry_mobile; ?></td>
								<td><?php echo $booking_details; ?></td>
								<td><?php echo $enquiry_query; ?></td>
								<td>
									<?php
									
									if($enquiry_status==1) { ?>

										<a href="enquiry.php?type=reject&enquiry_id=<?php echo $enquiry_id; ?>" class="btn btn-sm btn-red"><i class="fas fa-times-circle"></i> Reject</a>

									<?php
									} else { ?>
	
										<a href="enquiry.php?type=confirm&enquiry_id=<?php echo $enquiry_id; ?>&date=<?php echo $booking_date ?>&time=<?php echo $booking_time ?>" class="btn btn-sm btn-green" ><i class="fas fa-check-circle"></i> Confirm</a>
										<a href="enquiry.php?type=reject&enquiry_id=<?php echo $enquiry_id; ?>" class="btn btn-sm btn-red"><i class="fas fa-times-circle"></i> Reject</a>

									<?php
									}

									?>
								</td>
							</tr>
						<?php
						
						}
					}
			}
			
		?>
	</tbody>
</table>