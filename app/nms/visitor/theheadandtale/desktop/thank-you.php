<?php

if (!isset($id)) {
    echo "Something went wrong";
    exit();
} else {
    $order_id = $id;
}

$orderType == ((isset($orderType)) ? $orderType : 'subscription');

switch ($orderType) {
    case 'news':
        $transactionQuery
            = "SELECT {$orderType}_id as product_id, {$orderType}_title as product_title, transaction_user_id, transaction_type FROM " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "news ON order_product_id=news_id WHERE transaction_order_id=" . base64_decode($order_id) . " AND transaction_status=1 AND transaction_site_id=" . SITE_ID;
        break;
    default:
        $transactionQuery
            = "SELECT product_id, product_title, transaction_user_id, transaction_type FROM " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "product ON order_product_id=product_id WHERE transaction_order_id=" . base64_decode($order_id) . " AND transaction_status=1 AND transaction_site_id=" . SITE_ID;
        break;
}

if ($checkTransaction = mysqli_query($connection, $transactionQuery)) {
    if (mysqli_num_rows($checkTransaction) != 0) {
        extract(mysqli_fetch_assoc($checkTransaction));

        if (strpos(strtolower($product_title), "months") !== false) {
            $subscriptionDuration = 'months';
            $updateTimeStamp = "subscription_expiry = DATE_ADD(NOW(), INTERVAL 6 MONTH)";
            $updateTimeStampValue = "DATE_ADD(NOW(), INTERVAL 6 MONTH)";
        } else {
            $subscriptionDuration = 'year';
            $updateTimeStamp = "subscription_expiry = DATE_ADD(NOW(), INTERVAL 1 YEAR)";
            $updateTimeStampValue = "DATE_ADD(NOW(), INTERVAL 1 YEAR)";
        }

        if ($orderType != 'news') {
            if (!mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "subscription (
                subscription_site_id, subscription_user_id, subscription_relation_id,
                subscription_mode, subscription_type, subscription_time,
                subscription_expiry, subscription_status
            ) VALUES (
                '" . SITE_ID . "', '{$transaction_user_id}', '{$product_id}',
                '{$subscriptionDuration}', '{$transaction_type}', NOW(),
                {$updateTimeStampValue}, 1
            ) ON DUPLICATE KEY UPDATE
                subscription_relation_id = VALUES(subscription_relation_id),
                subscription_mode = VALUES(subscription_mode),
                subscription_type = VALUES(subscription_type),
                subscription_expiry = VALUES(subscription_expiry),
                subscription_status = VALUES(subscription_status)")) {
                if (DEVELOPMENT_MODE == true) {
                    echo "Error updating subscription: " . mysqli_error($connection);
                }
            }
        }
    } else {
        echo "Invalid transaction found";
        exit();
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
        <div class="thankyou_wrapper__content" style="margin-bottom: 200px">
            <?php

            if ($orderType == 'news') { ?>
                <h1>Thank You for Purchase!</h1>
                <p>Thank you for choosing to read an article from The Head and Tale. We hope you enjoyed it!</p>
                <p>We’re glad to have you with us and invite you to explore more of our thought-provoking content, exclusive
                    news, and in-depth reports. If you have any questions, feedback, or suggestions, please feel free to
                    contact us at <a href="mailto: info@theheadandtale.com">info@theheadandtale.com</a>.</p>
            <?php
            } else { ?>
                <h1>Thank You for Subscribing!</h1>
                <p>Welcome to The Head and Tale. Your subscription means the world to us.</p>
                <p>As a valued subscriber, stay tuned for thought-provoking articles, exclusive news, reports, and
                    much more.</p>
                <p>We appreciate your support and trust in us. If you have any questions, feedback, or suggestions,
                    please don't hesitate to reach out to us at <a href="mailto: info@theheadandtale.com">
                        info@theheadandtale.com</a></p>
            <?php
            }

            ?>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';
