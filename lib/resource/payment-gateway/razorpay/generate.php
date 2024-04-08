<?php

require_once 'razorpay.php';

use Razorpay\Api\Api;

$api = new Api(PG_KEY_ID, PG_KEY_SECRET);

/** Start payment gateway code */

$orderData = [
    'receipt' => 'OD' . $order_id,
    'amount' => $order_total_price * 100,
    'currency' => $cart_currency,
    'payment_capture' => 1
];

$transaction = $api->order->create($orderData); // Creates order

// Update transaction table

if (!$updateTransaction = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "transaction SET transaction_reference_id='{$transaction['id']}', transaction_status=2 WHERE transaction_id='{$id}' AND transaction_site_id=" . SITE_ID)) {

    echo 'Something is wrong while generation transaction status';
}

?>

<script src="<?php echo JS_LIBRARY ?>jquery.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script type="text/javascript">
var orderValue = {
    "key": '<?php echo PG_KEY_ID ?>',
    "amount": <?php echo str_replace(',', '', number_format($order_total_price, 2)) * 100 ?>,
    "currency": '<?php echo $cart_currency ?>',
    "name": '',
    "description": '',
    "image": '<?php echo SITE_LOGO ?>',
    "order_id": '<?php echo $transaction['id'] ?>',
    // "callback_url": '<?php echo SITE_URL ?>/payment/status/',
    "prefill": {
        "name": '<?php echo $profile_display_name ?>',
        "email": '<?php echo $profile_email ?>',
        "contact": '<?php echo $profile_mobile ?>'
    },
    "notes": {
        "address": 'Razorpay Corporate Office'
    },
    "theme": {
        "color": "<?php SITE_THEME_COLOUR ?>"
    },
    "handler": function(response) { // After payment done status
        window.location.href =
            '<?php echo SITE_URL ?>/payment/status/?id=<?php echo base64_encode($order_id); ?>&txn_id=<?php echo base64_encode($transaction['id']); ?>&pay_id=' +
            btoa(response.razorpay_payment_id);
    },
    "modal": { // Razorpay pop up activity
        "ondismiss": function(response) {
            window.location.href =
                '<?php echo SITE_URL ?>/payment/status/?id=<?php echo base64_encode($order_id); ?>&txn_id=<?php echo base64_encode($transaction['id']); ?>';
        }
    }
}

var rzpPopUP = new Razorpay(orderValue);

$(document).ready(function(e) {
    openPopUp();
});

function openPopUp() {
    rzpPopUP.open();
}
</script>