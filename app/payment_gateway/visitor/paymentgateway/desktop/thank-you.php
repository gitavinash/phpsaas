<?php

require_once 'header.php';

if (isset($id)) {

    $transaction_id = base64_decode($id);

    if ($checkOrder = mysqli_query($connection, "SELECT transaction_order_id as order_id, order_cart_session_id, transaction_method, order_product_id, order_total_price, order_product_qty, profile_mobile as mobile, profile_email, profile_first_name, profile_last_name, profile_display_name FROM isw_transaction INNER JOIN isw_profile ON transaction_user_id=profile_user_id INNER JOIN isw_order ON transaction_order_id=order_id WHERE transaction_order_id='{$transaction_id}'")) {

        if (mysqli_num_rows($checkOrder) != 0) {
            extract(mysqli_fetch_assoc($checkOrder));
            echo "<h1 class='green' >Thank You</h1><p>Your transaction is successful.</p>";
        } else {
            echo "jkkm";
        }
    }
}

require_once 'footer.php';