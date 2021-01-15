<?php

	$orderStatus=mysqli_real_escape_string($connection, $_GET['status']);
	$orderStatus=(($orderStatus=='all') ? '' : "WHERE order_status='{$orderStatus}'" );

?>
<h2>All Customers</h2>
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

			if($dataList=mysqli_query($connection, "SELECT profile_user_id, profile_display_name, profile_mobile, order_id, order_product_id, order_product_id, order_product_qty, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, order_generate, order_status FROM ".TABLE_PREFIX."order INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=order_user_id {$orderStatus} ORDER BY order_id DESC")) {

					if(mysqli_num_rows($dataList)!=0) {
                        $i=1;
						while($data=mysqli_fetch_assoc($dataList)) {
                            extract($data);
                            ?>
							<tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo 'OD'.$order_id ?></td>
                                <td><a href="customer/?type=profile&id=<?php echo base64_encode($profile_user_id) ?>"><?php echo $profile_display_name ?></a></td>
                                <td><a href="customer/?type=profile&id=<?php echo base64_encode($profile_user_id) ?>"><?php echo $profile_mobile ?></a></td>
                                <td><?php echo date('d-m-Y h:i A', strtotime($order_generate)) ?></td>
                                <td><?php echo $order_payment_mode ?></td>
                                <td><?php echo (($order_status==1) ? 'Confirm' : ((($order_status)==0) ? 'Pending' : 'Reject') ); ?></td>
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