<?php

if (isset($orderId)) {

    $orderId = base64_decode($orderId);

    if ($checkTransaction = mysqli_query($connection, "SELECT transaction_id, transaction_order_id, transaction_type FROM " . TABLE_PREFIX . "transaction WHERE transaction_order_id={$orderId} AND transaction_site_id=" . SITE_ID)) {

        if (mysqli_num_rows($checkTransaction) != 0) {

            extract(mysqli_fetch_assoc($checkTransaction));

            $curl = curl_init();

            if (DEVELOPMENT_MODE == TRUE) {

                $requestUrl = "https://sandbox.cashfree.com/pg/orders/{$orderId}";
            } else {

                $requestUrl = "https://api.cashfree.com/pg/orders/{$orderId}";
            }

            curl_setopt_array($curl, [
                CURLOPT_URL => $requestUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => [
                    "accept: application/json",
                    "content-type: application/json",
                    "x-api-version: " . PG_API_VERSION,
                    "x-client-id: " . PG_APP_ID,
                    "x-client-secret: " . PG_SECRET_KEY
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
                exit();
            }

            $response = json_decode($response, true);

            if (isset($response["order_status"])) {

                if ($response["order_status"] == 'PAID') {
                    $transactionStatus = 1;
                } else {
                    $transactionStatus = 3;
                }

                mysqli_query($connection, "START TRANSACTION");

                $updateTransaction = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "transaction SET transaction_status={$transactionStatus} WHERE transaction_order_id={$orderId} AND transaction_site_id=" . SITE_ID);

                $updateOrderStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status=1 WHERE order_id={$orderId}");

                $updateCartStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_status=1 WHERE cart_session_id='" . SESSION_ID . "'");

                if ($updateTransaction && $updateOrderStatus && $updateCartStatus) {
                    mysqli_query($connection, "COMMIT");
                    $orderId = base64_encode($orderId);
                    if ($transactionStatus == 1) {
                        header("Location: /thank-you/?order_id={$orderId}", TRUE, 301);
                    } else {
                        header("Location: /error/?order_id={$orderId}", TRUE, 301);
                    }
                } else {
                    echo "<h1>Something went wrong while trying to update the payment status into the database</h1>";
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {
                echo "<h1>Something went wrong while trying to fetch payment status</h1>";
            }
        } else {
            echo "<h1>Invalid order id</h1>";
        }
    } else {
        echo mysqli_error($connection);
    }
}