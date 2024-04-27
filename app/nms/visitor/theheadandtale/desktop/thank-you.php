<?php

    if(!isset($txn_id)) {
        echo "Something went wrong";
        exit();
    }

    if($checkTransaction=mysqli_query($connection, "SELECT product_id, product_title, transaction_user_id, transaction_type FROM ".TABLE_PREFIX."transaction INNER JOIN ".TABLE_PREFIX."order ON transaction_order_id=order_id INNER JOIN ".TABLE_PREFIX."product ON order_product_id=product_id WHERE transaction_id=".base64_decode($txn_id)." AND transaction_status=1 AND transaction_site_id=".SITE_ID)) {
        if(mysqli_num_rows($checkTransaction)!=0) {
            extract(mysqli_fetch_assoc($checkTransaction));

            if (strpos(strtolower($product_title), "month") !== false) {
                $subscriptionDuration='month';
            } else {
                $subscriptionDuration='year';
            }


            if(!mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."subscription(subscription_site_id, subscription_user_id, subscription_relation_id, subscription_mode, subscription_type, subscription_status) VALUES('".SITE_ID."', '{$transaction_user_id}', '{$product_id}', '{$subscriptionDuration}', '{$transaction_type}', 1)")) {
                if(!mysqli_query($connection, "UPDATE ".TABLE_PREFIX."subscription SET subscription_relation_id={$product_id}, subscription_mode='{$subscriptionDuration}', subscription_time=NOW() WHERE subscription_user_id='{$transaction_user_id}' AND subscription_site_id=".SITE_ID)) {
                    echo mysqli_fetch_assoc($connection);
                    echo "Something went wrong while updating the subscription plan";
                }
            }
        } else {
            echo "Invalid transaction found";
        }
    } else {
        echo mysqli_error($connection);
        echo "Something went wrong";
    }
    
    require_once 'header.php';

?>

<!-- <div class="subscribe_wrapper subscribe_padding" style="padding: 5rem 0px !important;">
    <div class="container">
        <div class="subscribe_wrapper__heading">
            <h6>Thank you</h6>
            <h3>Thank you for subscribing <?php echo SITE_NAME ?></h3>
        </div>

        <div class="subscription_card">
            <div class="subscription_card__box">
                <p>Manage subscription</p>
                <h6><?php echo $inventory_currency_symbol ?> <?php echo $inventory_price ?></h6>
                <div class="continue">
                    <a href='/manage-subscription/' class='button'>continue</a>
                </div>
            </div>
        </div>
    </div>
</div> -->
<section class="thankyou_wrapper">
    <div class="container">
        <div class="thankyou_wrapper__content">
            <h1>Thank You for Subscribing!</h1>
            <p>Welcome to The Head and Tale. Your subscription means the world to us.</p>
            <p>As a valued subscriber, stay tuned for thought-provoking articles, exclusive news, reports, and
                much more.</p>
            <p>We appreciate your support and trust in us. If you have any questions, feedback, or suggestions,
                please don't hesitate to reach out to us at <a href="mailto: info@theheadandtale.com">
                    info@theheadandtale.com</a></p>
        </div>
    </div>
</section>
<?php

    require_once 'footer.php';