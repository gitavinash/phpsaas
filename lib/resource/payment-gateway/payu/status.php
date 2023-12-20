<?php

if ($_POST['status'] != 'success') {

    $endPoint = base64_encode($_POST['email'] . '&txn=' . $_POST['txnid'] . '&=mobile=' . $_POST['phone'] . '&product' . $_POST['productinfo'] . '&amount=' . $_POST['amount']);

    header("Location: /payment-status/?status=failed&email={$endPoint}" . base64_encode($orderId), TRUE, 301);
} else {
    header("Location: /payment-status/?status=success&email={$endPoint}" . base64_encode($orderId), TRUE, 301);
}

exit();
