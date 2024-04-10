<?php

    require_once 'header.php';

?>

<div class="subscribe_wrapper subscribe_padding" style="padding: 5rem 0px !important;">
    <div class="container">
        <div class="subscribe_wrapper__heading">
            <h6>Error</h6>
            <h3>Your payment is declined. Transaction id : <?php echo base64_decode($txn_id) ?></h3>
        </div>
    </div>
</div>
<?php

    require_once 'footer.php';