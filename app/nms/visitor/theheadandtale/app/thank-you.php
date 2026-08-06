<?php

if (!isset($id)) {
    echo "Something went wrong";
    exit();
} else {
    $order_id = $id;
}

$orderType = ((isset($orderType)) ? $orderType : 'subscription');

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

<div class="status-page-wrapper">
    <div class="container">
        <div class="status-card">
            <div class="status-icon-wrapper success">
                <i class="ph ph-check-circle"></i>
            </div>
            <?php if ($orderType == 'news') { ?>
                <h1 class="status-title">Thank You for Your Purchase!</h1>
                <p class="status-desc">Thank you for choosing to read an article from The Head and Tale. We hope you enjoy it!</p>
                <p class="status-desc">We’re glad to have you with us and invite you to explore more of our thought-provoking content, exclusive news, and in-depth reports.</p>
                <p class="status-note">If you have any questions or feedback, feel free to contact us at <a href="mailto:info@theheadandtale.com">info@theheadandtale.com</a>.</p>

                <div class="status-actions">
                    <a href="/" class="auth-btn primary-btn">Explore Stories</a>
                </div>
            <?php } else { ?>
                <h1 class="status-title">Thank You for Subscribing!</h1>
                <p class="status-desc">Welcome to The Head and Tale. Your subscription means the world to us.</p>
                <p class="status-desc">As a valued subscriber, stay tuned for thought-provoking articles, exclusive news, reports, and ecosystem insights.</p>
                <p class="status-note">We appreciate your support. If you have any questions or suggestions, please reach out to us at <a href="mailto:info@theheadandtale.com">info@theheadandtale.com</a>.</p>

                <div class="status-actions">
                    <a href="/" class="auth-btn primary-btn">Start Reading</a>
                    <a href="/manage-subscription/" class="auth-btn primary-btn outline">Manage Subscription</a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php

require_once 'footer.php';

?>