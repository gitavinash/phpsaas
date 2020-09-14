<?php

    if(isset($id)) {

        if($checkOrder=mysqli_query($connection, "SELECT profile_display_name, user_mobile, user_email, order_product_id, order_product_qty, order_product_price, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_name, address_pin_code, order_generate FROM isw_order INNER JOIN isw_address ON order_address_id=address_id INNER JOIN isw_user ON order_user_id=user_id INNER JOIN isw_profile ON profile_owner_id=user_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id WHERE order_payment_mode='cash'")) {

            if(mysqli_num_rows($checkOrder)!=0) {
                
                extract(mysqli_fetch_assoc($checkOrder));

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

                <h2>Order Info</h2>

                <div class="form-box">
                    <form id="updatePassword" >
                        <div class="box-2">
                            <div class="form-group">
                                <label>Product name</label>
                                <input type="text" class="form-control inside-shadow" value="<?php echo $productName ?>" readonly >
                            </div>
                            <div class="form-group">
                                <label>Product Qty.</label>
                                <input type="text" class="form-control inside-shadow" value="<?php echo $order_product_qty ?>" readonly >
                            </div>
                            <div class="form-group">
                                <label>User Name</label>
                                <input type="text" class="form-control inside-shadow" value="<?php echo $profile_display_name ?>" readonly >
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control inside-shadow" readonly ><?php echo $address_house_no.', '.$address_area ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Landmark</label>
                                <input type="text" class="form-control inside-shadow" value="<?php echo $address_landmark ?>" readonly >
                            </div>
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" class="form-control inside-shadow" value="<?php echo $city_name ?>" readonly >
                            </div>
                            <div class="form-group">
                                <label>State</label>
                                <input type="text" class="form-control inside-shadow" value="<?php echo $state_name ?>" readonly >
                            </div>
                            <div class="form-group">
                                <label>Pin Code</label>
                                <input type="text" class="form-control inside-shadow" value="<?php echo $address_pin_code ?>" readonly >
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="type" value="order" >
                                <input type="hidden" name="action" value="update" >
                                <input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
                                <input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
                                <!-- <input type="submit" id="updatePasswordBtn" value='Change Password' class='btn btn-green'> -->
                            </div>
                        </div>
                    </form>
                </div>

            <?php
            } else {



            }

        }
        
    }

?>
