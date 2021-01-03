<?php

	if($checkCart=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."cart WHERE cart_session_id='".SESSION_ID."' AND cart_status=0 AND cart_site_id=".SITE_ID)) {

		if(mysqli_num_rows($checkCart)!=0) { 

			/** Show cart items */

			include_once 'cart-items.php';			

		} else {

			/** Show continue shoping box */

			include_once 'continue-shopping.php';

		}
		
	}