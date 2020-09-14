<?php

	$orderStatus=mysqli_real_escape_string($connection, $_GET['status']);
	$orderStatus=(($orderStatus=='all') ? '' : "WHERE order_status='{$orderStatus}'" );

?>
<h2>All Orders</h2>
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
			<th>Product Name</th>
			<th>Qty.</th>
			<th>Total Cost</th>
			<th>Payment Mode</th>
			<th>Delivery Charges</th>
			<th>Order Status</th>
			<th>Order Time</th>
		</tr>
	</thead>
	<tbody>
		<?php

			if($dataList=mysqli_query($connection, "SELECT order_id, order_product_id, order_product_id, order_product_qty, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, order_generate, order_status FROM ".TABLE_PREFIX."order {$orderStatus} ORDER BY order_id DESC")) {

					if(mysqli_num_rows($dataList)!=0) {
                        $i=1;
						while($data=mysqli_fetch_assoc($dataList)) {
                            extract($data);
                            switch($order_product_id) {

                                case 1 :
                                    $productName='Immunity Vitals';
                                    $productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/immunity-vitals/1.jpg';
                                    break;

                                case 2 :
                                    $productName='Tar Free';
                                    $productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/tar-free/1.jpg';
                                    break;

                                case 3 :
                                    $productName='Gyno Vitals';
                                    $productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/gyno-vitals/1.jpg';
                                    break;

                                case 4 :
                                    $productName='V Liver Vitals';
                                    $productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/v-liver-vitals/1.jpg';
                                    break;

                                default :
                                    $productName='';
                                    $productImage='';
                                    break;

                            }
                            ?>
							<tr>
                                <td><?php echo $i ?></td>
                                <td><?php echo $productName ?></td>
                                <td><?php echo $order_product_qty ?></td>
                                <td><i class="fas fa-rupee-sign"></i> <?php echo $order_product_price ?></td>
                                <td><?php echo $order_payment_mode ?></td>
                                <td><i class="fas fa-rupee-sign"></i> <?php echo $order_delivery_charges ?></td>
                                <td><?php echo $order_status ?></td>
                                <td><?php echo date('d-m-Y h:i A', strtotime($order_generate)) ?></td>
								<td>
									<a href="order.php?type=view&id=<?php echo $order_id; ?>" class="btn btn-sm btn-blue" ><i class="fas fa-eye"></i> View</a>
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