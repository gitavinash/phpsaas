<?php

$id = base64_decode($id);

include_once 'header.php';

?>


<!-- section global platform start -->
<section class="banner-section pro-galery">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="brdcrum">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Shipping</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="col-md-8">

                <!-- Cart items -->

                <div id="checkOut" class="cart-area">
                    <h2 class="heading-1">My Cart</h2>
                    <h3 class="heading-2">Express Delivery Products</h3>

                    <?php

                    if ($checkOrder = mysqli_query($connection, "SELECT order_id, order_currency, SUM(order_delivery_charges) as order_delivery_charges, order_total_price, order_coupon, order_payment_mode, cart_relation_id, cart_relation_type, GROUP_CONCAT(cart_qty) as cart_qty, cart_currency, SUM(cart_price) as cart_price, cart_status, GROUP_CONCAT(product_image) as product_image, GROUP_CONCAT(product_title) as product_title, inventory_currency, inventory_price, profile_first_name, profile_last_name, profile_display_name, profile_mobile, profile_email, address_house_no, address_landmark, address_area, city_name, state_name, address_pin_code, address_mobile FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "cart ON cart_session_id=order_cart_session_id INNER JOIN " . TABLE_PREFIX . "product ON product_id=cart_relation_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "user ON order_user_id=user_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id INNER JOIN " . TABLE_PREFIX . "address ON order_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON city_id=address_city_id INNER JOIN " . TABLE_PREFIX . "state ON state_id=address_state_id WHERE inventory_type='product' AND order_id='{$id}' AND order_cart_session_id='" . SESSION_ID . "' AND order_status=0 AND cart_status=2 AND cart_site_id=" . SITE_ID)) {

                        if (mysqli_num_rows($checkOrder) != 0) {

                            extract(mysqli_fetch_assoc($checkOrder));

                            if ($checkOrderMeta = mysqli_query($connection, "SELECT order_meta_key, order_meta_value FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id='{$order_id}' AND order_meta_status=1")) {

                                if (mysqli_num_rows($checkOrderMeta) != 0) {

                                    $orderMetas = array();

                                    while ($orderMeta = mysqli_fetch_assoc($checkOrderMeta)) {

                                        extract($orderMeta);

                                        $orderMetas[$order_meta_key] = $order_meta_value;
                                    }
                                }
                            }

                            extract($orderMetas);

                    ?>

                    <div class="ad-card">
                        <div class="paymnet-section">
                            <h3 class="heading-2">Contact information</h3>
                            <!-- Payment Gateway or Shipping Confirmation -->

                            <form class="shipping-box">

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                    </div>
                                    <table class="table ship-table">

                                        <tbody>
                                            <tr>
                                                <th scope="row">Name</th>
                                                <td id='shippingName'><?php echo $profile_display_name ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Contact</th>
                                                <td id='shippingName'><?php echo $address_mobile ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Email</th>
                                                <td id='shippingName'><?php echo $profile_email ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Delivery date</th>
                                                <td id='shippingName'>
                                                    <?php echo date('l, jS F Y', strtotime($deliveryDate)) ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Delivery time</th>
                                                <td id='shippingName'>
                                                    <?php echo ucwords($deliverySlot) . ' - ' . $deliveryTime ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Occasion</th>
                                                <td id='shippingName'>
                                                    <?php echo ucwords('<strong>' . $occasion . '</strong>') ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Message</th>
                                                <td id='shippingName'>
                                                    <?php echo ucwords($message) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Ship to</th>
                                                <td id='shippingAddress'>
                                                    <?php echo $address_house_no . ', ' . $address_landmark . ', ' . $address_area . ',<br />' . $city_name . ', ' . $state_name . ' - ' . $address_pin_code; ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <h3 class="heading-2">Order summary</h3>

                                <?php

                                        $productNameList = explode(',', $product_title);
                                        $productImageList = explode(',', $product_image);
                                        $productQtyList = explode(',', $cart_qty);

                                        for ($i = 0; $i < sizeof($productNameList); $i++) {

                                            echo "<div class='row' style='margin-bottom: 30px' >
														<div class='col-md-3 col-xs-12'>
															<img src='{$productImageList[$i]}' width='100px' height='100px' alt=''>
														</div>
														<div class='col-md-9 col-xs-12'><strong>{$productNameList[$i]}</strong><br />Qty: {$productQtyList[$i]}</div>
													</div>";
                                        }



                                        ?>

                        </div>
                    </div>

                    <?php

                            // Send event to third party services

                            if (defined('ENABLE_CONTLO')) {

                                require_once CONTLO . 'cart-summary.php';
                            }
                        } else {

                            echo '<h3 class="heading-2">Somthing is wrong</h3>';
                        }
                    }

                    ?>

                </div>

            </div>
            <div class="col-md-4">

                <div class="cart-purchase">
                    <h2>Cart summary</h2>
                    <div>
                        <div class="row">
                            <div class="col-8">Subtotal</div>
                            <div class="col-4"><i class="fas fa-rupee-sign"></i>
                                <span id="cartPrice"> <?php echo $cart_price ?></span>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 10px">
                            <div class="col-8">Delivery charges</div>
                            <div class="col-4"><i class="fas fa-rupee-sign"></i> <?php echo $order_delivery_charges ?>
                            </div>
                        </div>
                    </div>
                    <strong>Have a Coupon Code?</strong>
                    <div class="row">
                        <form class="form-row">
                            <div class="form-group col-12 cod-ap">
                                <input type="text" class="form-control couponCode" id="couponCode"
                                    placeholder="Discount Code">
                            </div>
                            <div class="form-group col-md-4 ap-btn">
                                <button type="submit" class="btn btn-success checkCoupon btn">Apply</button>
                            </div>
                            <span class="couponMsg"></span>
                        </form>
                    </div>
                    <h4>Grand Total</h4>
                    <h5 style="margin-bottom: 10px"><img src="<?php echo USER_IMAGES ?>cart-rupees-icon.svg">
                        <span id="finalPrice"><?php echo $cart_price + $order_delivery_charges ?></span>
                    </h5>
                    <form>
                        <input type="hidden" name="order" id="orderId" value=" <?php echo $id ?>">
                        <input type="hidden" name="paymentMode" value="online">
                        <input type="hidden" name="recaptcha_token" class="recaptcha_token">
                        <a href='#' id="continueToPayment" class="btn-bloom purchase-btn">Proceed to payment</a>
                    </form>
                </div>

                <div class="site-feature">
                    <ul>
                        <li><span class="material-icons">money_off</span> No Hidden Charges</li>
                        <li><span class="material-icons">security</span> 100% Secure payments</li>
                        <li><span class="material-icons">tag_faces</span> 2 lakh Smiles Delivered</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

include_once 'footer.php';

?>