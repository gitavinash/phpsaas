<?php

    if(!isset($txn_id)) {
        echo "Something went wrong";
        exit();
    }

    require_once 'header.php';

?>

<div class="subscribe_wrapper subscribe_padding" style="padding: 5rem 0px !important;">
    <div class="container">
        <div class="subscribe_wrapper__heading">
            <h6>Thank you</h6>
            <h3>Thank you for subscribing <?php echo SITE_NAME ?></h3>
        </div>

        <div class="subscription_card">
            <div class="subscription_card__box">
                <p>Manage subscription</p>
                <!-- <h6><?php echo $inventory_currency_symbol ?> <?php echo $inventory_price ?></h6> -->
                <div class="continue">
                    <a href='/manage-subscription/' class='button'>continue</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

    require_once 'footer.php';