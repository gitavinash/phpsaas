<!DOCTYPE html>
<html lang="en">

<head>
    <title>Redirecting to payment gateway</title>
    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
</head>

<body>
    <h1>Redirecting to payment gateway</h1>
    <?php


    /** Start payment gateway code */

    $curl = curl_init();

    if (DEVELOPMENT_MODE == TRUE) {

        $requestUrl = 'https://sandbox.cashfree.com/pg/orders';
    } else {

        $requestUrl = 'https://api.cashfree.com/pg/orders';
    }

    $data = [
        "customer_details" => [
            "customer_id" => strval($userId),
            "customer_email" => strval($profile_email),
            "customer_phone" => strval((($profile_mobile == "") ? time() : $profile_mobile)),
            "customer_name" => strval($profile_display_name)
        ],
        "order_id" => $order_id . '_' . time(),
        "order_amount" => strval($paymentAmount),
        "order_currency" => "INR",
        "order_note" => $cartProduct,
        "order_meta" => array(
            "return_url" => SITE_URL . '/payment/status/?orderId=' . base64_encode($order_id . '_' . time())
        )
    ];

    curl_setopt_array($curl, [
        CURLOPT_URL => $requestUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
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

    // print_r($response);

    if (isset($response["order_status"])) {
        if ($response['order_status'] == 'ACTIVE') {

            if ($updateTransaction = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "transaction SET transaction_reference_id='{$response['cf_order_id']}', transaction_payment_id='{$response['payment_session_id']}',transaction_status=2 WHERE transaction_id='{$id}' AND transaction_site_id=" . SITE_ID)) { ?>

                <script>
                    window.onload = function() {
                        const cashfree = Cashfree({
                            mode: "<?php echo ((DEVELOPMENT_MODE == true) ? 'sandbox' : 'production') ?>"
                        });
                        cashfree.checkout({
                            paymentSessionId: "<?php echo $response['payment_session_id']; ?>"
                        });
                    }
                </script>
    <?php
            } else {

                echo '<h2>Something is wrong while generation transaction status</h2>';
            }
        }
    } else {

        echo "<h2>Error: {$response['message']}</h2>";
    }

    ?>

</body>

</html>