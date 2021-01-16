<?php

	$orderStatus=mysqli_real_escape_string($connection, $_GET['status']);
	$orderStatus=(($orderStatus=='all') ? '' : "AND order_status='{$orderStatus}'" );

?>
<h2>All Leads</h2>
<div class="options-bar">
	<div class="left-box">
		<form method="GET">
			<input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" >
			<select class="form-control" name="status" id="changeStatus" >
				<option value="all" <?php echo (($_GET['status']=='all') ? 'selected' : '' ) ?> >All <?php echo $postType ?></option>
				<option value="0" <?php echo (($_GET['status']=='0') ? 'selected' : '' ) ?> >Pending <?php echo $postType ?></option>
				<option value="1" <?php echo (($_GET['status']=='1') ? 'selected' : '' ) ?>  >Confirm <?php echo $postType ?></option>
				<option value="2" <?php echo (($_GET['status']=='2') ? 'selected' : '' ) ?>  >Reject <?php echo $postType ?></option>
				<option value="3" <?php echo (($_GET['status']=='3') ? 'selected' : '' ) ?>  >Cancel <?php echo $postType ?></option>
				<option value="4" <?php echo (($_GET['status']=='4') ? 'selected' : '' ) ?>  >Waiting <?php echo $postType ?></option>
				<option value="5" <?php echo (($_GET['status']=='5') ? 'selected' : '' ) ?>  >RTO <?php echo $postType ?></option>
				<option value="6" <?php echo (($_GET['status']=='6') ? 'selected' : '' ) ?>  >Delivered <?php echo $postType ?></option>
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
			<th>S No.</th>
			<th>Order No.</th>
			<th>Name</th>
			<th>Mobile</th>
			<th>Date and Time</th>
			<th>Payment Mode</th>
			<th>Status</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php

			if($dataList=mysqli_query($connection, "SELECT profile_user_id, profile_display_name, profile_mobile, order_id, order_product_id, order_product_id, order_product_qty, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, order_generate, order_status, address_mobile FROM ".TABLE_PREFIX."order INNER JOIN ".TABLE_PREFIX."address ON address_id=order_address_id INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=order_user_id WHERE order_site_id=".APP_SITE_ID." {$orderStatus} ORDER BY order_id DESC LIMIT 0, 1000")) {

					if(mysqli_num_rows($dataList)!=0) {
                        $i=1;
						while($data=mysqli_fetch_assoc($dataList)) {
							
							extract($data);
															
							switch($order_status) {

								case 0 :
									$order_status='Pending';
									break;

								case 1 :
									$order_status='Confirm';
									break;

								case 2 :
									$order_status='Reject';
									break;

								case 3 :
									$order_status='Cancel';
									break;

								case 4 :
									$order_status='Waiting';
									break;

								case 5 :
									$order_status='RTO';
									break;

								case 6 :
									$order_status='Delivered';
									break;

							}

                            ?>
							<tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo 'OD'.$order_id ?></td>
                                <td><a href="customer/?type=profile&id=<?php echo base64_encode($profile_user_id) ?>"><?php echo $profile_display_name ?></a></td>
                                <td><a href="customer/?type=profile&id=<?php echo base64_encode($profile_user_id) ?>"><?php echo $address_mobile ?></a></td>
                                <td><?php echo date('d-m-Y h:i A', strtotime($order_generate)) ?></td>
                                <td><?php echo $order_payment_mode ?></td>
                                <td><?php echo $order_status ?></td>
								<td>
									<a href="order.php?type=view&id=<?php echo base64_encode($order_id); ?>" class="btn btn-sm btn-blue" ><i class="fas fa-eye"></i> View</a>
								</td>
							</tr>
						<?php
						$i++;
						}
					}
			}
			
		?>
	</tbody>
</table>