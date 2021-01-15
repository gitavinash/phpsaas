<?php

	$user_id=base64_decode($id);

?>
<h2>Customer profile</h2>

<?php

    if($employeeProfile=mysqli_query($connection, "SELECT profile_display_name, user_email, user_mobile, user_act_time FROM ".TABLE_PREFIX."user INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=user_id WHERE user_id={$user_id}")) {

        if(mysqli_num_rows($employeeProfile)!=0) {

            extract(mysqli_fetch_assoc($employeeProfile)); ?>

            <table style='margin-bottom: 20px;'>
                <tr>
                    <td>Name</td>
                    <td><?php echo $profile_display_name; ?></td>
                    <td>Email</td>
                    <td><?php echo $user_email ?></td>
                </tr>
                <tr>
                    <td>Mobile</td>
                    <td><?php echo $user_mobile; ?></td>
                    <td>Customer From </td>
                    <td><?php echo date('d F Y', strtotime($user_act_time)) ?></td>
                </tr>

            </table>

            <table>
                <tr>
                    <td>
                        <h2>Order History</h2>
                        <?php
                        
                            /** Check order history */

                            if($checkOrder=mysqli_query($connection, "SELECT order_id, order_product_qty, order_generate, product_title FROM ".TABLE_PREFIX."order INNER JOIN ".TABLE_PREFIX."product ON product_id=order_product_id WHERE order_user_id='{$user_id}' AND order_site_id=".APP_SITE_ID)) {

                                echo "<ul>";

                                if(mysqli_num_rows($checkOrder)!=0) {

                                    while($orderData=mysqli_fetch_assoc($checkOrder)) {

                                        extract($orderData);

                                        echo "<li><a href='/dashboard/order/?type=view&id=".base64_encode($order_id)."'>{$product_title}<br />Qty : {$order_product_qty} | Time : ".date("d M Y", strtotime($order_generate))."</a></li>";

                                    }
                
                                } else {
                
                                    echo "No Order Found";
                
                                }

                                echo "</ul>";
                
                            }

                        ?>
                    </td>
                    <td>
                        <h2>Address</h2>
                        <?php
                        
                            /** Check order history */

                            if($checkAddress=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."address INNER JOIN ".TABLE_PREFIX."city ON city_id=address_city_id INNER JOIN ".TABLE_PREFIX."state ON state_id=address_state_id WHERE address_user_id='{$user_id}' AND address_site_id=".APP_SITE_ID)) {

                                if(mysqli_num_rows($checkAddress)!=0) {
                
                                    echo "<ul>";

                                    while($addressData=mysqli_fetch_assoc($checkAddress)) {

                                        extract($addressData);

                                        echo "<li>{$address_name}<br />{$address_house_no}, {$address_area}, {$address_landmark},<br />{$city_name}, {$state_name}, {$address_pin_code}</li>";

                                    }

                                    echo "</ul>";

                                } else {
                
                                    echo "No address Found";
                
                                }
                
                            }

                        ?>
                    </td>
                </tr>
            </table>

            <?php

            

        }
        
    } else {

        echo mysqli_error($connection);

    }

?>