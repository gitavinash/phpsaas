<?php

    require_once 'header.php';

    if(isset($order_id)) {

        $transaction_id=base64_decode($order_id);
        
        if($checkOrder=mysqli_query($connection, "SELECT transaction_order_id FROM ".TABLE_PREFIX."transaction WHERE transaction_id='{$transaction_id}'")) {
            
            if(mysqli_num_rows($checkOrder)!=0) {

                extract(mysqli_fetch_assoc($checkOrder));

                mysqli_query($connection, "START TRANSACTION");

                $updateOrder=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."order SET order_status=1 WHERE order_id='{$transaction_order_id}'");
                $updateTransaction=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."transaction SET transaction_status=1 WHERE transaction_id='{$transaction_id}'");

                if($updateOrder && $updateTransaction) {

                    mysqli_query($connection, "COMMIT"); ?>
                    
                    <section class='continue-shopping' >
                        <h1>Thank You<span>Your order has been successfully received.</span></h1>
                        <a href="/" class="btn btn-success" >Continue Shopping</a>
                    </section>

                    <?php
                } else {

                    mysqli_query($connection, "ROLLBACK");

                }

            } else { echo mysqli_error($connection); ?>

                <section class='continue-shopping' >
                    <h1>Error<span>Something is wrong while generate a order request.</span></h1>
                    <a href="/" class="btn btn-success" >Continue Shopping</a>
                </section>

            <?php
            }
            
        }
        
    }

	require_once 'footer.php';

?>