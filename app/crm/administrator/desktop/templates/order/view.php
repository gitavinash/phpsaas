<?php

    if(isset($id)) {

        if($checkOrder=mysqli_query($connection, "SELECT profile_display_name, user_mobile, user_email, order_product_id, order_product_qty, order_product_price, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_name, address_pin_code, order_generate FROM isw_order INNER JOIN isw_address ON order_address_id=address_id INNER JOIN isw_user ON order_user_id=user_id INNER JOIN isw_profile ON profile_owner_id=user_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id WHERE order_id='{$id}'")) {

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
                    <form id="orderData" >
                        <div class="box-1">
                            <div class="box-2">
                                <div class="form-group">
                                    <label>Product name</label>
                                    <input type="text" class="form-control inside-shadow" value="<?php echo $productName ?>" >
                                </div>
                                <div class="form-group">
                                    <label>Product Qty.</label>
                                    <input type="text" name="qty" class="form-control inside-shadow" value="<?php echo $order_product_qty ?>" >
                                </div>
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control inside-shadow" value="<?php echo $profile_display_name ?>" >
                                </div>
                                <div class="form-group">
                                    <label>Mobile No.</label>
                                    <input type="text" name="mobile" class="form-control inside-shadow" value="<?php echo $user_mobile ?>" >
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea class="form-control inside-shadow" name="address" ><?php echo $address_house_no.', '.$address_area ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Landmark</label>
                                    <input type="text" class="form-control inside-shadow" name="landmark" value="<?php echo $address_landmark ?>" >
                                </div>
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" class="form-control inside-shadow" name="city" value="<?php echo $city_name ?>" >
                                </div>
                                <div class="form-group">
                                    <label>State</label>
                                    <input type="text" class="form-control inside-shadow" name="state" value="<?php echo $state_name ?>" >
                                </div>
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" class="form-control inside-shadow" name="pincode" value="<?php echo $address_pin_code ?>" >
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="type" value="order" >
                                <input type="hidden" name="order_id" value="<?php echo $id ?>" >
                                <a href="#" id="orderUpdateData" class="btn btn-blue" >Update Data</a>
                                <a href="#" id="orderConfirmBtn" class="btn btn-green" >Confirm</a>
                                <a href="#" id="orderWaitingBtn" class="btn btn-yellow" >Waiting</a>
                                <a href="#" id="orderCancelBtn" class="btn btn-red" >Cancel</a>
                                <a href="#" id="orderReturnBtn" class="btn btn-red" >Return</a>
                                <a href="#" id="orderDeliverBtn" class="btn btn-green" >Delivered</a>
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
