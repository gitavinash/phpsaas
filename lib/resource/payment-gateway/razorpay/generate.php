<?php

    require_once 'razorpay.php';

    use Razorpay\Api\Api;

    $api = new Api($pg_key_id, $pg_key_secret);

    /** Check request is valid or not */

    if(isset($id)) {

        $id=base64_decode($id);

        $orderType=((isset($orderType)) ? $orderType : 'product' );

        switch ($orderType) {

            case 'booking' :
                $query = "SELECT order_id, order_total_price, profile_mobile, profile_email, profile_display_name, cart_currency, cart_currency_symbol FROM ".TABLE_PREFIX."cart INNER JOIN ".TABLE_PREFIX."transaction INNER JOIN ".TABLE_PREFIX."order ON transaction_order_id=order_id INNER JOIN ".TABLE_PREFIX."{$orderType} ON cart_relation_id={$orderType}_id INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=transaction_user_id WHERE transaction_id='{$id}' AND transaction_site_id=".SITE_ID." AND order_cart_session_id=cart_session_id";
                $cartProduct='Online booking';
                break;

            default : 
                $query = "SELECT order_id, order_total_price, profile_mobile, profile_email, profile_display_name, cart_currency, cart_currency_symbol,  GROUP_CONCAT({$orderType}_title, ',') as cartProduct FROM ".TABLE_PREFIX."cart INNER JOIN ".TABLE_PREFIX."transaction INNER JOIN ".TABLE_PREFIX."order ON transaction_order_id=order_id INNER JOIN ".TABLE_PREFIX."{$orderType} ON cart_relation_id={$orderType}_id INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=transaction_user_id WHERE transaction_id='{$id}' AND transaction_site_id=".SITE_ID." AND order_cart_session_id=cart_session_id";
                break;

        }

        if($checkStatus=mysqli_query($connection, $query)) {

            if(mysqli_num_rows($checkStatus)!=0) {

                extract(mysqli_fetch_assoc($checkStatus));
                
                if($checkOrderMeta=mysqli_query($connection, "SELECT order_meta_key, order_meta_value FROM ".TABLE_PREFIX."order_meta WHERE order_relation_id='{$order_id}' AND order_meta_status=1 AND order_meta_site_id=".APP_SITE_ID)) {

                    if(mysqli_num_rows($checkOrderMeta)!=0) {

                        $orderMetas=array();

                        while($orderMeta=mysqli_fetch_assoc($checkOrderMeta)) {

                            extract($orderMeta);

                            $orderMetas[$order_meta_key] = $order_meta_value;

                        }

                        extract($orderMetas);

                    }

                }

                $paymentAmount=$order_total_price + ((isset($deliveryFees)) ? $deliveryFees : 0 );

                /** Start payment gateway code */

                $orderData= [
                    'receipt' => 'OD'.$order_id,
                    'amount' => $order_total_price*100,
                    'currency' => $cart_currency,
                    'payment_capture' => 1
                ];

                $order  =   $api->order->create($orderData); // Creates order

                ?>

                <script src="<?php echo JS_LIBRARY ?>jquery.min.js" ></script>
                <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                <script type="text/javascript">
                    var orderValue = {
                        "key": '<?php echo $pg_key_id ?>',
                        "amount": <?php echo str_replace(',', '', number_format($order_total_price, 2))*100 ?>,
                        "currency": '<?php echo $cart_currency ?>',
                        "name": '',
                        "description": '',
                        "image": '<?php echo SITE_LOGO ?>',
                        "order_id": '<?php echo $order['id'] ?>',
                        // "callback_url": '<?php echo SITE_URL ?>/payment/status/',
                        "prefill" : {
                            "name": '<?php echo $profile_display_name ?>',
                            "email": '<?php echo $profile_email ?>',
                            "contact": '<?php echo $profile_mobile ?>'
                        },
                        "notes": {
                            "address": 'Razorpay Corporate Office'
                        },
                        "theme": {
                            "color": "#3399cc"
                        },
                        "handler": function (response){ // After payment done status
                            window.location.href='<?php echo SITE_URL ?>/payment/status/?id=<?php echo base64_encode($order_id); ?>&txn_id=<?php echo base64_encode($order['id']); ?>';
                        },
                        "modal": {  // Razorpay pop up activity
                            "ondismiss": function(){
                                window.location.href='<?php echo SITE_URL ?>/payment/status/?id=<?php echo base64_encode($order_id); ?>&txn_id=<?php echo base64_encode($order['id']); ?>';
                            }
                        }
                    }

                    var rzpPopUP = new Razorpay(orderValue);

                    $(document).ready(function(e) {
                        // e.preventDefault();
                        openPopUp();
                    });

                    function openPopUp() {
                        rzpPopUP.open();
                    }

                </script>

                <?php
                exit();
                if($response['success']==1) {

                    if($updateTransaction=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."transaction SET transaction_gateway='{$pg_name}', transaction_reference_id='{$response['payment_request']['id']}', transaction_status=2 WHERE transaction_id='{$id}' AND transaction_site_id=".SITE_ID)) {

                        header("Location: {$response['payment_request']['longurl']}", TRUE, 301);
                        exit();
    
                    } else {
    
                        echo 'Something is wrong while generation transaction status';
    
                    }

                } else {

                    print_r($response['message']).' of '.SITE_DOMAIN;

                }

            } else {

                echo 'Invalid Transactional ID';

            }

        } else {

            echo 'Something is wrong while fetching order details';

        }

    }