<?php

    if(isset($id)) {

        if($checkOrder=mysqli_query($connection, "SELECT profile_first_name, profile_last_name, user_mobile, user_email, order_product_id, order_product_qty, order_product_price, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_id, address_pin_code, address_type, order_generate FROM isw_order INNER JOIN isw_address ON order_address_id=address_id INNER JOIN isw_user ON order_user_id=user_id INNER JOIN isw_profile ON profile_user_id=user_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id WHERE order_id='{$id}' AND order_site_id=".APP_SITE_ID)) {

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
                                    <input type="text" class="form-control inside-shadow" value="<?php echo $productName ?>" readonly >
                                </div>
                                <div class="form-group">
                                    <label>Product Qty.</label>
                                    <input type="text" name="qty" class="form-control inside-shadow" value="<?php echo $order_product_qty ?>" readonly >
                                </div>
                                <div class="box-2" style="padding:0">
                                    <div class="form-group" >
                                        <label>First Name</label>
                                        <input type="text" name="first_name" class="form-control inside-shadow" value="<?php echo $profile_first_name ?>" >
                                    </div>
                                </div>
                                <div class="box-2" style="padding:0" >
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" name="last_name" class="form-control inside-shadow" value="<?php echo $profile_last_name ?>" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Mobile No.</label>
                                    <input type="text" name="mobile" class="form-control inside-shadow" value="<?php echo $user_mobile ?>" >
                                </div>
                                <div class="form-group">
                                    <label>House No.</label>
                                    <input type="text" name="address_house_no" class="form-control inside-shadow" value="<?php echo $address_house_no ?>" >
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" name="address_area" class="form-control inside-shadow" value="<?php echo $address_area ?>" >
                                </div>
                                <div class="form-group">
                                    <label>Landmark</label>
                                    <input type="text" class="form-control inside-shadow" name="address_landmark" value="<?php echo $address_landmark ?>" >
                                </div>
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" class="form-control inside-shadow" name="city" value="<?php echo $city_name ?>" >
                                </div>
                                <div class="form-group">
                                    <label>State</label>
                                    <select class="form-control inside-shadow" name="state" required>
                                    <?php
                                    
                                        if($stateDetails=mysqli_query($connection, "SELECT state_id, state_name FROM ".TABLE_PREFIX."state WHERE state_country_id=101")) {

                                            if(mysqli_num_rows($stateDetails)!=0) {

                                                echo "<option value='' >Select state</option>";

                                                while($stateData=mysqli_fetch_assoc($stateDetails)) {

                                                    echo "<option value='{$stateData['state_id']}' ".(($stateData['state_id']==$state_id) ? 'selected' : '' )." >{$stateData['state_name']}</option>";

                                                }

                                            } else {

                                                echo "<option value=''>No city found</option>";

                                            }

                                        }

                                    ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" class="form-control inside-shadow" name="address_pin_code" value="<?php echo $address_pin_code ?>" >
                                </div>
                                <div class="form-group">
                                    <label>Address yype</label>
                                    <select name="address_type" class="form-control inside-shadow">
                                        <option value="">Select address type</option>
                                        <option value="home" <?php echo (($address_type=='home') ? 'selected' : '' ) ?> >Home</option>
                                        <option value="work" <?php echo (($address_type=='work') ? 'selected' : '' ) ?>>Work</option>
                                        <option value="other" <?php echo (($address_type=='other') ? 'selected' : '' ) ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="type" value="order" >
                                <input type="hidden" name="id" value="<?php echo $id ?>" >
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

                echo "SELECT profile_first_name, profile_last_name, user_mobile, user_email, order_product_id, order_product_qty, order_product_price, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_id, address_pin_code, address_type, order_generate FROM isw_order INNER JOIN isw_address ON order_address_id=address_id INNER JOIN isw_user ON order_user_id=user_id INNER JOIN isw_profile ON profile_user_id=user_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id WHERE order_id='{$id}'";

            }

        }
        
    }

?>
