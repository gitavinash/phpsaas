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
			<th>Bookind Date</th>
			<th>Bookind Time</th>
			<th>Bookind Name</th>
			<th>Bookind Mobile</th>
			<th>Bookind Details</th>
		</tr>
	</thead>
	<tbody>
		<?php

				if($dataList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."booking")) {
				if(mysqli_num_rows($dataList)!=0) {
					while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
						<tr>
							<td>
								<p><a href="page.php?type=edit&editor=true&page=<?php echo $booking_id; ?>" ><?php echo $booking_title.(($link_status!='200') ? '<strong> — Draft </strong>' : '' ); ?></a></p>
								<div class="hover">
									<a href="page.php?type=edit&editor=true&booking_id=<?php echo $booking_id; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
									<a href="page.php?type=post&booking_id=<?php echo $booking_id; ?>"><i class="fas fa-trash"></i> Delete</a>
									<a href="<?php echo $link_url; ?>" target='_blank' ><i class="fas fa-eye"></i> View</a>
								</div>
							</td>
							<td><?php echo $profile_display_name; ?></td>
							<td><?php echo date('d/m/Y', strtotime($link_creation)); ?></td>
						</tr>
					<?php
					}
				}
			}
			
		?>
	</tbody>
</table>