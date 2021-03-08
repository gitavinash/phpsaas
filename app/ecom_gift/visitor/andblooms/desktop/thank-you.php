<?php

    require_once 'header.php';

    if(isset($id)) {

        $transaction_id=base64_decode($id);

        echo "SELECT transaction_order_id as order_id, transaction_method, order_product_id, order_total_price, order_product_qty, profile_mobile as mobile, profile_email, profile_first_name, profile_last_name, profile_display_name, city_name, state_name, country_name, address_name, address_house_no, address_area, address_landmark, address_pin_code FROM isw_transaction INNER JOIN isw_profile ON transaction_user_id=profile_user_id INNER JOIN isw_order ON transaction_order_id=order_id INNER JOIN isw_address ON address_id=order_address_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id INNER JOIN isw_country ON country_id=address_country_id WHERE transaction_id='{$transaction_id}' AND transaction_site_id=".APP_SITE_ID;
        exit();
        
        if($checkOrder=mysqli_query($connection, "SELECT transaction_order_id as order_id, transaction_method, order_product_id, order_total_price, order_product_qty, profile_mobile as mobile, profile_email, profile_first_name, profile_last_name, profile_display_name, city_name, state_name, country_name, address_name, address_house_no, address_area, address_landmark, address_pin_code FROM isw_transaction INNER JOIN isw_profile ON transaction_user_id=profile_user_id INNER JOIN isw_order ON transaction_order_id=order_id INNER JOIN isw_address ON address_id=order_address_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id INNER JOIN isw_country ON country_id=address_country_id WHERE transaction_id='{$transaction_id}' AND transaction_site_id=".APP_SITE_ID)) {
            
            if(mysqli_num_rows($checkOrder)!=0) {

                extract(mysqli_fetch_assoc($checkOrder));

                mysqli_query($connection, "START TRANSACTION");

                $updateTransaction=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."transaction SET transaction_status=1 WHERE transaction_order_id='{$order_id}'");

                if($updateTransaction) {
                    
                    mysqli_query($connection, "COMMIT");
                    
                    if($transaction_method=='online') {

                        $updateOrder=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."order SET order_status=1 WHERE order_id='{$order_id}'");

                    }

                    if($productDetails=mysqli_query($connection, "SELECT GROUP_CONCAT(product_title) as productName, GROUP_CONCAT(product_image) as productImage FROM isw_product WHERE product_type='product' AND {$order_product_id}")) {

                        $productName='';

                        if(mysqli_num_rows($productDetails)!=0) {

                            extract(mysqli_fetch_assoc($productDetails));

                        }

                    }

                    /** Update cart table */

                    if(!mysqli_query($connection, "UPDATE ".TABLE_PREFIX."cart SET cart_status=1 WHERE cart_session_id='".SESSION_ID."'")) { ?>

                        <section class='continue-shopping' >
                            <h1>Error<span>Something is wrong while updating cart.</span></h1>
                            <a href="/" class="btn btn-success" >Continue Shopping</a>
                        </section>

                    <?php

                    echo mysqli_error($connection);

                    } else {
                        
                        ?>
                    
                        <section class='continue-shopping' >
                            <h1>Thank You<span>Your order number <strong>OD<?php echo $order_id; ?></strong> is successfully received. You may get a confirmation call in a while.</span></h1>
                            <a href="/" class="btn btn-success" >Continue Shopping</a>
                        </section>
                        <?php
                        
                    }

                } else {

                    mysqli_query($connection, "ROLLBACK");

                }

            } else { ?>

                <section class='continue-shopping' >
                    <h1>Error<span>Something is wrong while generate a order request.</span></h1>
                    <a href="/" class="btn btn-success" >Continue Shopping</a>
                </section>

            <?php
            }
            
        } else {

            mysqli_errno($connection);
            exit();

        }
        
    }

	require_once 'footer.php';

?>