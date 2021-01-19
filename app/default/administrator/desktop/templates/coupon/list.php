<?php

	$couponStatus=mysqli_real_escape_string($connection, $_GET['status']);
	$couponStatus=(($couponStatus=='all') ? '' : "AND coupon_status='{$couponStatus}'" );
	
?>
<h2>All coupons</h2>
<div class="options-bar">
	<div class="left-box">
		<form method="GET">
			<input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" >
			<select class="form-control" name="status" id="changeStatus" >
				<option value="all" <?php echo (($_GET['status']=='all') ? 'selected' : '' ) ?> >All <?php echo $couponType ?></option>
				<option value="2" <?php echo (($_GET['status']=='0') ? 'selected' : '' ) ?> >Expired <?php echo $couponType ?></option>
				<option value="1" <?php echo (($_GET['status']=='1') ? 'selected' : '' ) ?>  >Active <?php echo $couponType ?></option>
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
			<th width="150px" >Code</th>
			<th>Discount</th>
			<th>Payment mode</th>
			<th>Min. cart value</th>
			<th>Max. discount</th>
			<th>Limit</th>
			<th>Used</th>
			<th>Expiry</th>
		</tr>
	</thead>
	<tbody>
		<?php
            
            if($dataList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."coupon WHERE coupon_site_id=".SITE_ID." AND coupon_status!=3 {$couponStatus}")) {
				if(mysqli_num_rows($dataList)!=0) {
					while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
						<tr style="height: 60px" >
							<td>
								<p><a href="coupon.php?type=edit&id=<?php echo base64_encode($coupon_id); ?>" ><?php echo $coupon_code ?></a></p>
								<div class="hover">
									<a href="coupon.php?type=edit&editor=true&id=<?php echo base64_encode($coupon_id); ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
									<a href="#" id="<?php echo $coupon_id ?>_coupon" class="deletePost" ><i class="fas fa-trash"></i> Delete</a>
								</div>
							</td>
                            <td><?php echo (($coupon_discount_type!='%') ? $coupon_discount_type.' ' : '' ).$coupon_discount.(($coupon_discount_type=='%') ? ' '.$coupon_discount_type : '' ); ?></td>
                            <td><?php echo $coupon_payment_mode; ?></td>
                            <td><?php echo $coupon_min_value; ?></td>
                            <td><?php echo $coupon_max_discount; ?></td>
                            <td><?php echo $coupon_limit; ?></td>
                            <td><?php echo $coupon_used; ?></td>
                            <td><?php echo date('d M Y', strtotime($coupon_expire)) ?></td>
						</tr>
					    <?php
					}
				}
			}
			
		?>
	</tbody>
</table>