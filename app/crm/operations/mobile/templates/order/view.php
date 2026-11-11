<?php

if (isset($id)) {

    $id = base64_decode($id);

    $productList = mysqli_query($connection, "SELECT product_id, product_title FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE link_type='product' AND product_type='product' AND link_status=200 AND link_site_id=" . APP_SITE_ID);

    if (mysqli_num_rows($productList) != 0) {

        $productListData = array();

        while ($productLists = mysqli_fetch_assoc($productList)) {

            extract($productLists);

            $productListData[] = array(
                'product_id' => $product_id,
                'product_title' => $product_title
            );
        }

        if ($checkOrder = mysqli_query($connection, "SELECT profile_first_name, profile_last_name, profile_display_name, user_mobile, user_email, order_product_id, order_product_qty, order_currency, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_id, address_pin_code, address_type, address_mobile, order_generate, transaction_status FROM isw_order INNER JOIN isw_address ON order_address_id=address_id INNER JOIN isw_user ON order_user_id=user_id INNER JOIN isw_profile ON profile_user_id=user_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id INNER JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "product ON product_id=order_product_id WHERE order_id='{$id}' AND product_type='product' AND order_site_id=" . APP_SITE_ID)) {

            if (mysqli_num_rows($checkOrder) != 0) {

                extract(mysqli_fetch_assoc($checkOrder));

?>

<h2>Order Info</h2>

<div class="form-box">
    <form id="orderData">
        <div class="box-1">
            <div class="box-2">
                <?php

                                // Fetch Product information from database

                                $productData = explode(',', $order_product_id);
                                $productQtyData = explode(',', $order_product_qty);

                                for ($i = 0; $i < sizeof($productData); $i++) {

                                    echo "<input type='hidden' name='totalProducts' value='" . sizeof($productData) . "'>";

                                    if ($checkProduct = mysqli_query($connection, "SELECT product_id, product_image, product_title, inventory_currency_symbol FROM isw_product INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE product_id='{$productData[$i]}' AND inventory_type='product' AND product_site_id=" . APP_SITE_ID . " AND inventory_site_id=" . APP_SITE_ID)) {

                                        if (mysqli_num_rows($checkProduct) != 0) {

                                            extract(mysqli_fetch_assoc($checkProduct)); ?>

                <div class="form-group">
                    <label>Product name ( Cart item no <?php echo $i + 1 ?> )</label>
                    <!-- <input type="text" class="form-control inside-shadow" value="<?php echo $productName ?>" > -->
                    <select name="product_id_<?php echo $i ?>" class="form-control inside-shadow">
                        <option value="">Select product</option>
                        <?php

                                                    for ($j = 0; $j < sizeof($productListData); $j++) {

                                                        echo "<option value='{$productListData[$j]['product_id']}' " . (($productListData[$j]['product_id'] == $productData[$i]) ? 'selected' : '') . " >{$productListData[$j]['product_title']}</option>";
                                                    }

                                                    ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Product qty.</label>
                    <input type="text" name="qty_<?php echo $i ?>" class="form-control inside-shadow"
                        value="<?php echo str_replace(' ', '', $productQtyData[$i]) ?>">
                </div>

                <?php

                                        } else { ?>

                <div class="form-group">
                    <label>Product name ( Cart item no <?php echo $i + 1 ?> )</label>
                    <!-- <input type="text" class="form-control inside-shadow" value="<?php echo $productName ?>" > -->
                    <select name="product_id_<?php echo $i ?>" class="form-control inside-shadow">
                        <option value="">Select product</option>
                        <?php

                                                    for ($j = 0; $j < sizeof($productListData); $j++) {
                                                        echo "<option value='{$productListData[$j]['product_id']}' >{$productListData[$j]['product_title']}</option>";
                                                    }

                                                    ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Product qty.</label>
                    <input type="text" name="qty_<?php echo $i ?>" class="form-control inside-shadow">
                </div>

                <?php
                                        }
                                    }
                                }

                                ?>
                <div class="box-2" style="padding:0">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control inside-shadow"
                            value="<?php echo $profile_first_name ?>">
                    </div>
                </div>
                <div class="box-2" style="padding:0">
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control inside-shadow"
                            value="<?php echo $profile_last_name ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control inside-shadow"
                        value="<?php echo $user_email ?>">
                </div>
                <div class="form-group">
                    <label>Mobile No.</label>
                    <input type="text" name="mobile" class="form-control inside-shadow"
                        value="<?php echo $user_mobile ?>">
                </div>
                <div class="form-group">
                    <label>House No.</label>
                    <input type="text" name="address_house_no" class="form-control inside-shadow"
                        value="<?php echo $address_house_no ?>">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address_area" class="form-control inside-shadow"
                        value="<?php echo $address_area ?>">
                </div>
                <div class="form-group">
                    <label>Landmark</label>
                    <input type="text" class="form-control inside-shadow" name="address_landmark"
                        value="<?php echo $address_landmark ?>">
                </div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" class="form-control inside-shadow" name="city" value="<?php echo $city_name ?>">
                </div>
                <div class="form-group">
                    <label>State</label>
                    <select class="form-control inside-shadow" name="state" required>
                        <?php

                                        if ($stateDetails = mysqli_query($connection, "SELECT state_id, state_name FROM " . TABLE_PREFIX . "state WHERE state_country_id=101")) {

                                            if (mysqli_num_rows($stateDetails) != 0) {

                                                echo "<option value='' >Select state</option>";

                                                while ($stateData = mysqli_fetch_assoc($stateDetails)) {

                                                    echo "<option value='{$stateData['state_id']}' " . (($stateData['state_id'] == $state_id) ? 'selected' : '') . " >{$stateData['state_name']}</option>";
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
                        value="<?php echo $address_pin_code ?>">
                </div>
                <div class="form-group">
                    <label>Address yype</label>
                    <select name="address_type" class="form-control inside-shadow">
                        <option value="">Select address type</option>
                        <option value="home" <?php echo (($address_type == 'home') ? 'selected' : '') ?>>Home</option>
                        <option value="work" <?php echo (($address_type == 'work') ? 'selected' : '') ?>>Work</option>
                        <option value="other" <?php echo (($address_type == 'other') ? 'selected' : '') ?>>Other
                        </option>
                    </select>
                </div>
            </div>

            <div class="box-2">

                <img src="<?php echo $product_image ?>" style='height: 200px; margin-bottom: 20px' alt="">

                <div class="form-group">
                    <label>Product price</label>
                    <input type='text' class='form-control inside-shadow'
                        value='<?php echo (($order_product_price != '') ? $inventory_currency_symbol . ' ' : '') . $order_product_price ?>'
                        readonly>
                </div>

                <div class="form-group">
                    <label>Delivery charge</label>
                    <input type='text' class='form-control inside-shadow'
                        value='<?php echo (($order_delivery_charges != '') ? $inventory_currency_symbol . ' ' : '') . $order_delivery_charges ?>'
                        readonly>
                </div>

                <div class="form-group">
                    <label>Order total</label>
                    <input type='text' class='form-control inside-shadow'
                        value='<?php echo (($order_total_price != '') ? $inventory_currency_symbol . ' ' : '') . $order_total_price ?>'
                        readonly>
                </div>

                <h2>Receiver details</h2>

                <div class="form-group">
                    <label>Name</label>
                    <input type='text' class='form-control inside-shadow' value='<?php echo $address_name ?>' readonly>
                </div>

                <div class="form-group">
                    <label>Contact no.</label>
                    <input type='text' class='form-control inside-shadow' value='<?php echo $address_mobile ?>'
                        readonly>
                </div>

                <?php

                                if ($orderMeta = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id='{$id}' AND order_site_id='" . APP_SITE_ID . "'")) {

                                    if (mysqli_num_rows($orderMeta) != 0) {

                                        while ($orderMetaData = mysqli_fetch_assoc($orderMeta)) {

                                            extract($orderMetaData);

                                            if ($order_meta_key == 'message') {

                                                echo "<div class='form-group'>
                                                            <label>" . ucwords($order_meta_key) . "</label>
                                                            <textarea class='form-control inside-shadow' style='min-height: 200px' readonly >{$order_meta_value}</textarea>
                                                        </div>";
                                            } else {

                                                echo "<div class='form-group'>
                                                            <label>" . ucwords($order_meta_key) . "</label>
                                                            <input type='text' class='form-control inside-shadow' value='{$order_meta_value}' readonly >
                                                        </div>";
                                            }
                                        }
                                    }
                                }

                                ?>
            </div>

            <div class="form-group">
                <input type="hidden" name="type" value="order">
                <input type="hidden" name="id" value="<?php echo $id ?>">
                <!-- <a href="#" id="orderUpdateData" class="btn btn-blue">Update Data</a> -->
                <a href="#" id="orderConfirmBtn" class="btn btn-green">Confirm</a>
                <a href="#" id="orderWaitingBtn" class="btn btn-yellow">Waiting</a>
                <a href="#" id="orderCancelBtn" class="btn btn-red">Cancel</a>
                <a href="#" id="orderReturnBtn" class="btn btn-red">Return</a>
                <a href="#" id="orderDeliverBtn" class="btn btn-green">Delivered</a>
            </div>
        </div>
    </form>
</div>

<?php
            }
        } else {

            echo 'no order found';
        }
    } else {

        echo "Invalid data received";
    }
}

?>