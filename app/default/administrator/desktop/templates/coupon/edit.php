<?php
	
	$coupon_id=base64_decode(mysqli_real_escape_string($connection, $_GET['id']));
    
	if($couponDetails=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."coupon WHERE coupon_id='{$coupon_id}' AND coupon_site_id=".APP_SITE_ID)) {
		if(mysqli_num_rows($couponDetails)!=0) {
			extract(mysqli_fetch_assoc($couponDetails));
		} else { exit(); ?>
			<script type="text/javascript">
				alert('Invalid action found');
				window.location.href='dashboard/coupon/?type=list&status=all';
			</script>
		<?php
		}
	}

?>