<?php

	$postStatus=mysqli_real_escape_string($connection, $_GET['status']);

	$postStatus=(($postStatus=='all') ? '' : "AND booking_status='{$postStatus}'" );
	
?>
<h2>All <?php echo ucwords($postType.'s') ?></h2>
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
			<input type="submit" class="btn" value="Filter" >
		</form>
	</div>
	<div class="right-box">
		
	</div>
</div>
<table id="table">
	<thead>
		<tr>
			<th style="width: 50px">S No.</th>
			<th style="width: 300px" >Bookind Date</th>
			<th>Bookind Time</th>
			<th>Bookind Name</th>
			<th>Bookind Mobile</th>
			<th>Bookind Details</th>
		</tr>
	</thead>
	<tbody>
		<?php

			if($dataList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."booking INNER JOIN ".TABLE_PREFIX."enquiry ON enquiry_id=booking_enquiry_id ORDER BY enquiry_id DESC")) {

					if(mysqli_num_rows($dataList)!=0) {
						$i=1;
						while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
							<tr>
								<td><?php echo $i; ?></td>
								<td>
									<p><a href="enquiry.php?type=view&enquiry_id=<?php echo $enquiry_id; ?>" ><?php echo date('d-m-Y', strtotime($booking_date)) ?></a></p>
									<div class="hover">
										<!-- <a href="enquiry.php?type=view&editor=true&enquiry_id=<?php echo $enquiry_id; ?>" ><i class="fas fa-eye"></i> View</a> -->
										<a href="enquiry.php?type=confirm&enquiry_id=<?php echo $enquiry_id; ?>"><i class="fas fa-check-circle"></i> Confirm</a>
										<a href="enquiry.php?type=reject&enquiry_id=<?php echo $enquiry_id; ?>"><i class="fas fa-times-circle"></i> Reject</a>
									</div>
								</td>
								<td><?php echo $booking_time; ?></td>
								<td><?php echo $enquiry_first_name.' '.$enquiry_last_name; ?></td>
								<td><?php echo $enquiry_mobile; ?></td>
								<td><?php echo $booking_details; ?></td>
							</tr>
						<?php
						$i++;
						}
					}
			}
			
		?>
	</tbody>
</table>