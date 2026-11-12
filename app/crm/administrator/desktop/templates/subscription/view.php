<?php

if (isset($id)) {

    $id = base64_decode($id);

    $productList = mysqli_query($connection, "SELECT product_id, product_title FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE link_type='product' AND product_type='subscription' AND link_status=200 AND link_site_id=" . APP_SITE_ID);

    if ($productList && mysqli_num_rows($productList) != 0) {

        $productListData = array();

        while ($productLists = mysqli_fetch_assoc($productList)) {
            $productListData[] = array(
                'product_id' => $productLists['product_id'],
                'product_title' => $productLists['product_title']
            );
        }

        if ($checkOrder = mysqli_query($connection, "SELECT profile_first_name, profile_last_name, profile_display_name, user_mobile, user_email, order_product_id, order_product_qty, order_currency, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_id, address_pin_code, address_type, address_mobile, order_generate, transaction_status FROM isw_order INNER JOIN isw_address ON order_address_id=address_id INNER JOIN isw_user ON order_user_id=user_id INNER JOIN isw_profile ON profile_user_id=user_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id INNER JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "product ON product_id=order_product_id WHERE order_id='{$id}' AND product_type='subscription' AND order_site_id=" . APP_SITE_ID)) {

            if (mysqli_num_rows($checkOrder) != 0) {

                extract(mysqli_fetch_assoc($checkOrder));

?>
                <div class="order-info-ui crm-subscription-view" data-order-id="<?php echo htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="order-info-head">
                        <div>
                            <h2>Order Info</h2>
                            <p class="order-info-subtitle">Review and update subscription order details.</p>
                        </div>
                    </div>

                    <div class="form-box order-info-shell">
                        <form id="orderData">
                            <div class="box-1">
                                <div class="box-2">
                                    <?php
                                    $productData = explode(',', $order_product_id);
                                    $productQtyData = explode(',', $order_product_qty);

                                    for ($i = 0; $i < sizeof($productData); $i++) {
                                        echo "<input type='hidden' name='totalProducts' value='" . sizeof($productData) . "'>";

                                        if ($checkProduct = mysqli_query($connection, "SELECT product_id, product_image, product_title, inventory_currency_symbol FROM isw_product INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE product_id='{$productData[$i]}' AND inventory_type='product' AND product_site_id=" . APP_SITE_ID . " AND inventory_site_id=" . APP_SITE_ID)) {
                                            if (mysqli_num_rows($checkProduct) != 0) {
                                                extract(mysqli_fetch_assoc($checkProduct)); ?>

                                                <div class="order-item-row">
                                                    <div class="form-group">
                                                        <label>Product name (Cart item no <?php echo $i + 1; ?>)</label>
                                                        <select name="product_id_<?php echo $i; ?>" class="form-control inside-shadow">
                                                            <option value="">Select product</option>
                                                            <?php
                                                            for ($j = 0; $j < sizeof($productListData); $j++) {
                                                                $selected = ($productListData[$j]['product_id'] == $productData[$i]) ? 'selected' : '';
                                                                echo "<option value='" . htmlspecialchars($productListData[$j]['product_id'], ENT_QUOTES, 'UTF-8') . "' {$selected}>" . htmlspecialchars($productListData[$j]['product_title'], ENT_QUOTES, 'UTF-8') . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Product qty.</label>
                                                        <input type="text" name="qty_<?php echo $i; ?>" class="form-control inside-shadow"
                                                            value="<?php echo htmlspecialchars(str_replace(' ', '', (string)($productQtyData[$i] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
                                                    </div>
                                                </div>

                                            <?php } else { ?>

                                                <div class="order-item-row">
                                                    <div class="form-group">
                                                        <label>Product name (Cart item no <?php echo $i + 1; ?>)</label>
                                                        <select name="product_id_<?php echo $i; ?>" class="form-control inside-shadow">
                                                            <option value="">Select product</option>
                                                            <?php
                                                            for ($j = 0; $j < sizeof($productListData); $j++) {
                                                                echo "<option value='" . htmlspecialchars($productListData[$j]['product_id'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($productListData[$j]['product_title'], ENT_QUOTES, 'UTF-8') . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Product qty.</label>
                                                        <input type="text" name="qty_<?php echo $i; ?>" class="form-control inside-shadow">
                                                    </div>
                                                </div>

                                    <?php
                                            }
                                        }
                                    }
                                    ?>

                                    <div class="order-customer-grid">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input type="text" name="first_name" class="form-control inside-shadow"
                                                value="<?php echo htmlspecialchars((string)$profile_first_name, ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" name="last_name" class="form-control inside-shadow"
                                                value="<?php echo htmlspecialchars((string)$profile_last_name, ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" name="email" class="form-control inside-shadow"
                                            value="<?php echo htmlspecialchars((string)$user_email, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Mobile No.</label>
                                        <input type="text" name="mobile" class="form-control inside-shadow"
                                            value="<?php echo htmlspecialchars((string)$user_mobile, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>House No.</label>
                                        <input type="text" name="address_house_no" class="form-control inside-shadow"
                                            value="<?php echo htmlspecialchars((string)$address_house_no, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" name="address_area" class="form-control inside-shadow"
                                            value="<?php echo htmlspecialchars((string)$address_area, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Landmark</label>
                                        <input type="text" class="form-control inside-shadow" name="address_landmark"
                                            value="<?php echo htmlspecialchars((string)$address_landmark, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" class="form-control inside-shadow" name="city"
                                            value="<?php echo htmlspecialchars((string)$city_name, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>State</label>
                                        <select class="form-control inside-shadow" name="state" required>
                                            <?php
                                            if ($stateDetails = mysqli_query($connection, "SELECT state_id, state_name FROM " . TABLE_PREFIX . "state WHERE state_country_id=101")) {
                                                if (mysqli_num_rows($stateDetails) != 0) {
                                                    echo "<option value=''>Select state</option>";
                                                    while ($stateData = mysqli_fetch_assoc($stateDetails)) {
                                                        $selected = ($stateData['state_id'] == $state_id) ? 'selected' : '';
                                                        echo "<option value='" . htmlspecialchars($stateData['state_id'], ENT_QUOTES, 'UTF-8') . "' {$selected}>" . htmlspecialchars($stateData['state_name'], ENT_QUOTES, 'UTF-8') . "</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>No city found</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Pin Code</label>
                                        <input type="text" class="form-control inside-shadow" name="address_pin_code"
                                            value="<?php echo htmlspecialchars((string)$address_pin_code, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Address Type</label>
                                        <select name="address_type" class="form-control inside-shadow">
                                            <option value="">Select address type</option>
                                            <option value="home" <?php echo (($address_type == 'home') ? 'selected' : ''); ?>>Home</option>
                                            <option value="work" <?php echo (($address_type == 'work') ? 'selected' : ''); ?>>Work</option>
                                            <option value="other" <?php echo (($address_type == 'other') ? 'selected' : ''); ?>>Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="box-2 order-summary-sticky">
                                    <?php if (!empty($product_image)) { ?>
                                        <img src="<?php echo htmlspecialchars((string)$product_image, ENT_QUOTES, 'UTF-8'); ?>" class="order-summary-image" alt="">
                                    <?php } ?>

                                    <div class="form-group">
                                        <label>Product price</label>
                                        <input type="text" class="form-control inside-shadow"
                                            value="<?php echo htmlspecialchars((($order_product_price != '') ? $inventory_currency_symbol . ' ' : '') . $order_product_price, ENT_QUOTES, 'UTF-8'); ?>"
                                            readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Delivery charge</label>
                                        <input type="text" class="form-control inside-shadow"
                                            value="<?php echo htmlspecialchars((($order_delivery_charges != '') ? $inventory_currency_symbol . ' ' : '') . $order_delivery_charges, ENT_QUOTES, 'UTF-8'); ?>"
                                            readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Order total</label>
                                        <input type="text" class="form-control inside-shadow"
                                            value="<?php echo htmlspecialchars((($order_total_price != '') ? $inventory_currency_symbol . ' ' : '') . $order_total_price, ENT_QUOTES, 'UTF-8'); ?>"
                                            readonly>
                                    </div>

                                    <h3 class="order-section-title">Receiver details</h3>

                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" class="form-control inside-shadow"
                                            value="<?php echo htmlspecialchars((string)$address_name, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Contact no.</label>
                                        <input type="text" class="form-control inside-shadow"
                                            value="<?php echo htmlspecialchars((string)$address_mobile, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                    </div>

                                    <?php
                                    if ($orderMeta = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id='{$id}' AND order_site_id='" . APP_SITE_ID . "'")) {
                                        if (mysqli_num_rows($orderMeta) != 0) {
                                            while ($orderMetaData = mysqli_fetch_assoc($orderMeta)) {
                                                $metaKey = $orderMetaData['order_meta_key'];
                                                $metaValue = $orderMetaData['order_meta_value'];
                                                if ($metaKey == 'message') {
                                                    echo "<div class='form-group'>
                                                        <label>" . htmlspecialchars(ucwords($metaKey), ENT_QUOTES, 'UTF-8') . "</label>
                                                        <textarea class='form-control inside-shadow' style='min-height: 200px' readonly>" . htmlspecialchars((string)$metaValue, ENT_QUOTES, 'UTF-8') . "</textarea>
                                                    </div>";
                                                } else {
                                                    echo "<div class='form-group'>
                                                        <label>" . htmlspecialchars(ucwords($metaKey), ENT_QUOTES, 'UTF-8') . "</label>
                                                        <input type='text' class='form-control inside-shadow' value='" . htmlspecialchars((string)$metaValue, ENT_QUOTES, 'UTF-8') . "' readonly>
                                                    </div>";
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group order-action-row">
                                    <input type="hidden" name="type" value="order">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>">
                                    <a href="#" id="orderConfirmBtn" class="btn btn-green">Confirm</a>
                                    <a href="#" id="orderWaitingBtn" class="btn btn-yellow">Waiting</a>
                                    <a href="#" id="orderCancelBtn" class="btn btn-red">Cancel</a>
                                    <a href="#" id="orderReturnBtn" class="btn btn-red">Return</a>
                                    <a href="#" id="orderDeliverBtn" class="btn btn-green">Delivered</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

<?php
            } else {
                echo 'no order found';
            }
        } else {
            echo 'no order found';
        }
    } else {
        echo "Invalid data received";
    }
}
?>
