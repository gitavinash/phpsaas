<?php

    $orderId=base64_decode($id);

    if($checkDetails=mysqli_query($connection, "SELECT order_cart_session_id, order_total_price, order_currency, cart_relation_type FROM ".TABLE_PREFIX."order INNER JOIN ".TABLE_PREFIX."cart ON cart_session_id=order_cart_session_id WHERE order_id='{$orderId}' AND order_status=0 AND order_site_id=".SITE_ID)) {
        if(mysqli_num_rows($checkDetails)!=0) {
            extract(mysqli_fetch_assoc($checkDetails));

            $cartQuery = "SELECT * FROM ".TABLE_PREFIX."cart INNER JOIN ".TABLE_PREFIX."{$cart_relation_type} ON cart_relation_id= {$cart_relation_type}_id INNER JOIN ".TABLE_PREFIX."inventory ON {$cart_relation_type}_id =inventory_relation_id WHERE cart_relation_type='{$cart_relation_type}' AND cart_session_id='{$order_cart_session_id}' AND inventory_type='{$cart_relation_type}' AND cart_site_id=".SITE_ID." ORDER BY {$cart_relation_type}_time DESC";

            if($cartInfo = mysqli_query($connection, $cartQuery)) {
                if(mysqli_num_rows($cartInfo)==0) {
                    echo "Empty cart";
                }
            } else {
                echo "Something went wrong while trying to fetch cart data";
            }

        }
    } else {
        echo "Something went wrong";
        exit();
    }

    require_once 'header.php';

?>
<section class="checkout-box">
    <div class="container">
        <div class="row">
            <div class="col-7 m-col">
                Payme t
            </div>
            <div class="col-3 m-col float-right">
                <div class="shadow-box">
                    <h2>Payment summary<span>test contents</span></h2>
                    <ul>
                        <li>
                            <p><strong>Subtotal</strong><span><?php echo $inventory_currency_symbol.' '.$subTotal ?></span>
                            </p>
                        </li>
                        <?php
                        
                            $taxAmount=0;
                            if($inventory_tax_status==1) {

                                $taxAmount = number_format((float)($inventory_tax_rate / 100) * $inventory_price, 2, '.', '');

                                echo "<li>
                                    <p><strong>Tax</strong><span>{$inventory_currency_symbol} {$taxAmount}</span></p>
                                </li>";
                            }

                            $totalPrice = $subTotal + $taxAmount

                        ?>
                    </ul>
                    <div class="totalBox">
                        <p><strong>Total</strong> <span><?php echo $inventory_currency_symbol.' '.$totalPrice ?></span>
                        </p>
                    </div>
                    <input type="hidden" name="order" id="orderId" value=" <?php echo $orderId ?>">
                    <input type="hidden" name="paymentMode" id="paymentMode" value="online">
                    <a href="#" id="processToPayment" class="button">Proceed to payment</a>
                </div>
            </div>
        </div>
        <div class="creditBox">
            <div class="row">
                <div class="col-4">
                    <p><a href="/terms-and-conditions">Terms &amp; Conditions</a> and <a href="/privacy-policy/">Privacy
                            Policy</a></p>
                </div>
                <div class="col-6">
                    <p> <span>Powered by <a href="https://cartt.in" title="Build online e-commerce store">Cartt -
                                Craft Your Online Success with
                                Cartt.</a></span></p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php

    require_once 'footer.php';