<?php

$order_total_price = number_format($order_total_price, 2, '.', ',');

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pay to <?php echo SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="icon" href="<?php echo SITE_ICON ?>">
    <style>
    body {
        padding: 10% 0%;
    }

    p {
        padding: 0px;
        margin: 0px;
        font-size: 18px;
        line-height: 18px
    }

    .logo {
        float: left;
        width: 100%;
        text-align: center;
    }

    .logo img {
        height: auto;
        max-width: 60%;
    }

    .pg_logo {
        height: auto;
        width: 18px;
        margin-right: 20px
    }

    span {
        font-size: 14px;
        float: left;
        width: 100%;
        text-align: center;
    }

    h1 {
        font-weight: 700;
        float: left;
        width: 100%;
        text-align: center;
        padding: 10px 0px;
    }

    h2 {
        float: left;
        width: 100%;
        text-align: center;
        font-size: 48px
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="card" style="width: 100%; border: none">
            <div class="d-flex justify-content-center" style="padding: 20px">
                <div class="row-full">
                    <div class="logo">
                        <img src="<?php echo SITE_LOGO ?>" alt="">
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center" style="padding: 20px">
                <div class="row-full">
                    <span>Pay to</span>
                    <h1><?php echo SITE_NAME ?></h1>
                    <h2>₹ <?php echo $order_total_price ?></h2>
                </div>
            </div>
        </div>
        <div class="card" style="width: 100%;">
            <ul class="list-group list-group-flush">
                <li class="list-group-item" style="padding:0px"><a
                        href="phonepe://pay?pa=<?php echo PG_UPI_ADDRESS ?>&pn=<?php echo urlencode(SITE_NAME) ?>&tr=<?php echo $order_id ?>&cu=INR&mc=0000&am=<?php echo $amount ?>&url=<?php echo SITE_URL . '/payment/status/' ?>"
                        class="btn" style="float: left; width: 100%; text-align: left; padding: 15px 20px">
                        <p><img src="<?php echo IMG_LIBRARY ?>/payment/phonepe.png" class="pg_logo" alt=""> Phone pe
                        </p>
                    </a></li>
                <li class="list-group-item" style="padding:0px"><a
                        href="paytm://pay?pa=<?php echo PG_UPI_ADDRESS ?>&pn=<?php echo urlencode(SITE_NAME) ?>&tr=<?php echo $order_id ?>&cu=INR&mc=0000&am=<?php echo $amount ?>&url=<?php echo SITE_URL . '/payment/status/' ?>"
                        class="btn" style="float: left; width: 100%; text-align: left; padding: 15px 20px">
                        <p><img src="<?php echo IMG_LIBRARY ?>/payment/paytm.png" class="pg_logo" alt=""> PayTM</p>
                    </a></li>
                <li class="list-group-item" style="padding:0px"><a
                        href="gpay://pay?pa=<?php echo PG_UPI_ADDRESS ?>&pn=<?php echo urlencode(SITE_NAME) ?>&tr=<?php echo $order_id ?>&cu=INR&mc=0000&am=<?php echo $amount ?>&url=<?php echo SITE_URL . '/payment/status/' ?>"
                        class="btn" style="float: left; width: 100%; text-align: left; padding: 15px 20px">
                        <p><img src="<?php echo IMG_LIBRARY ?>/payment/google-pay.png" class="pg_logo" alt=""> G Pay</p>
                    </a></li>
                <li class="list-group-item" style="padding:0px"><a
                        href="upi://pay?pa=<?php echo PG_UPI_ADDRESS ?>&pn=<?php echo urlencode(SITE_NAME) ?>&tr=<?php echo $order_id ?>&cu=INR&mc=0000&am=<?php echo $amount ?>&url=<?php echo SITE_URL . '/payment/status/' ?>"
                        class="btn" style="float: left; width: 100%; text-align: left; padding: 15px 20px">
                        <p><img src="<?php echo IMG_LIBRARY ?>/payment/upi.png" class="pg_logo" alt=""> Bhim UPI</p>
                    </a></li>
            </ul>
        </div>
    </div>
</body>

</html>