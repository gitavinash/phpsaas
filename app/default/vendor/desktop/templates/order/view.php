<?php

if (isset($id)) {

    $id = base64_decode($id);

    $siteId = " IN (" . APP_SITE_ID . ", " . SITE_ID . ")";
    $isApp32 = ((int)SITE_APP_ID === 32);

    $productList = mysqli_query($connection, "SELECT product_id, product_title, product_image FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE link_type='product' AND product_type='product' AND link_status=200 AND link_site_id" . $siteId);

    if ((mysqli_num_rows($productList) != 0) || $isApp32) {

        $productListData = array();

        while ($productLists = mysqli_fetch_assoc($productList)) {

            extract($productLists);

            $productListData[] = array(
                'product_id' => $product_id,
                'product_title' => $product_title,
                'product_image' => $product_image
            );
        }

        $checkOrderSql = "SELECT profile_first_name, profile_last_name, profile_display_name, user_mobile, user_email, order_product_id, order_vendor_id, order_product_qty, order_currency, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_id, address_pin_code, address_type, address_mobile, order_generate, transaction_status, product_title, product_image, inventory_currency_symbol, order_assign_to FROM isw_order LEFT JOIN isw_address ON order_address_id=address_id LEFT JOIN isw_user ON order_user_id=user_id LEFT JOIN isw_profile ON profile_user_id=user_id LEFT JOIN isw_city ON city_id=address_city_id LEFT JOIN isw_state ON state_id=address_state_id LEFT JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id";

        $checkOrderSql .= " LEFT JOIN " . TABLE_PREFIX . "product ON product_id=order_product_id AND product_type='product' AND product_site_id" . $siteId;
        $checkOrderSql .= " LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=order_product_id AND inventory_type='product' AND inventory_site_id" . $siteId;

        $checkOrderSql .= " WHERE order_id='{$id}' AND order_site_id" . $siteId;

        if ($checkOrder = mysqli_query($connection, $checkOrderSql)) {

            if (mysqli_num_rows($checkOrder) != 0) {

                extract(mysqli_fetch_assoc($checkOrder));

                $orderStatusValue = trim((string)$transaction_status);
                $numericStatusMap = array(
                    '' => 'Pending',
                    '0' => 'Pending',
                    '1' => 'Confirmed',
                    '2' => 'Waiting',
                    '3' => 'Cancelled',
                    '4' => 'Returned',
                    '5' => 'Delivered',
                );

                if (isset($numericStatusMap[$orderStatusValue])) {
                    $orderStatusText = $numericStatusMap[$orderStatusValue];
                } elseif (is_numeric($orderStatusValue)) {
                    $orderStatusText = 'Status Updated';
                } else {
                    $orderStatusText = ucwords(str_replace(array('_', '-'), ' ', $orderStatusValue));
                }

                $orderStatusRaw = strtolower(trim((string)$orderStatusText));
                $orderStatusRaw = str_replace(array(' ', '-'), '_', $orderStatusRaw);
                $orderStatusChipClass = 'badge-default';

                if (in_array($orderStatusRaw, array('delivered', 'complete', 'completed', 'success', 'confirmed', 'confirm'))) {
                    $orderStatusChipClass = 'badge-delivered';
                } elseif (in_array($orderStatusRaw, array('waiting', 'pending', 'processing', 'on_hold', 'in_progress'))) {
                    $orderStatusChipClass = 'badge-pending';
                } elseif (in_array($orderStatusRaw, array('cancel', 'cancelled', 'return', 'returned', 'failed', 'rejected'))) {
                    $orderStatusChipClass = 'badge-cancelled';
                }

                // Fetch order meta details

                if ($orderMetaExits = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id={$id} AND order_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($orderMetaExits) != 0) {
                        while ($orderMetaData = mysqli_fetch_assoc($orderMetaExits)) {
                            extract($orderMetaData);
                            if (!empty($order_meta_key)) {
                                $variableName = 'order' . ucfirst($order_meta_key);
                                $$variableName = $order_meta_value;
                            }
                        }
                    }
                }

                $orderDeliveryDate = ((isset($orderDeliveryDate)) ? $orderDeliveryDate : '');

?>
                <div class="order-info-ui" data-order-id="<?php echo $id ?>">

                    <div class="premium-header">
                        <div class="premium-header-left">
                            <h2>Order #<?php echo $id ?></h2>
                            <p class="order-info-subtitle">Review, update customer profile, and process the lifecycle of this order.</p>
                        </div>
                        <div>
                            <span class="premium-badge <?php echo $orderStatusChipClass ?>">
                                <i class="fas fa-circle" style="font-size: 8px; margin-right: 5px;"></i> <?php echo $orderStatusText ?>
                            </span>
                        </div>
                    </div>

                    <div class="premium-stats-bar">
                        <div class="premium-stat-item">
                            <span class="premium-stat-label">Product</span>
                            <span class="premium-stat-value"><?php echo ucwords((($product_title != '') ? $product_title : 'N/A')) ?></span>
                        </div>
                        <div class="premium-stat-item">
                            <span class="premium-stat-label">Items Count</span>
                            <span class="premium-stat-value"><?php echo sizeof(explode(',', $order_product_id)) ?> items</span>
                        </div>
                        <div class="premium-stat-item">
                            <span class="premium-stat-label">Receiving Date</span>
                            <span class="premium-stat-value"><?php echo $order_generate ?></span>
                        </div>
                        <div class="premium-stat-item" style="border-right: none;">
                            <span class="premium-stat-label">Delivery Date</span>
                            <span class="premium-stat-value"><?php echo date('Y-m-d', strtotime($orderDeliveryDate)); ?></span>
                        </div>
                    </div>

                    <div class="order-info-shell">
                        <form id="orderData">
                            <div class="premium-grid">
                                <!-- LEFT PANEL: Items and Billing info -->
                                <div class="premium-grid-left">

                                    <!-- Card 2: Customer Shipping Details -->
                                    <div class="premium-card">
                                        <h3 class="premium-card-title"><i class="fas fa-user-circle"></i> Customer & Shipping Details</h3>
                                        <div class="premium-form-grid">
                                            <div class="premium-form-group">
                                                <label>First Name</label>
                                                <input type="text" name="first_name" class="premium-form-control" value="<?php echo $profile_first_name ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="last_name" class="premium-form-control" value="<?php echo $profile_last_name ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Email Address</label>
                                                <input type="text" name="email" class="premium-form-control" value="<?php echo $user_email ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Mobile Number</label>
                                                <input type="text" name="mobile" class="premium-form-control" value="<?php echo $user_mobile ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>House No. / Suite</label>
                                                <input type="text" name="address_house_no" class="premium-form-control" value="<?php echo $address_house_no ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Address Line</label>
                                                <input type="text" name="address_area" class="premium-form-control" value="<?php echo $address_area ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Landmark</label>
                                                <input type="text" name="address_landmark" class="premium-form-control" value="<?php echo $address_landmark ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>City</label>
                                                <input type="text" name="city" class="premium-form-control" value="<?php echo $city_name ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>State</label>
                                                <select class="premium-form-control" name="state" required>
                                                    <?php
                                                    if ($stateDetails = mysqli_query($connection, "SELECT state_id, state_name FROM " . TABLE_PREFIX . "state WHERE state_country_id=101")) {
                                                        if (mysqli_num_rows($stateDetails) != 0) {
                                                            echo "<option value='' >Select state</option>";
                                                            while ($stateData = mysqli_fetch_assoc($stateDetails)) {
                                                                echo "<option value='{$stateData['state_id']}' " . (($stateData['state_id'] == $state_id) ? 'selected' : '') . " >{$stateData['state_name']}</option>";
                                                            }
                                                        } else {
                                                            echo "<option value=''>No state found</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Pin Code</label>
                                                <input type="text" name="address_pin_code" class="premium-form-control" value="<?php echo $address_pin_code ?>">
                                            </div>
                                            <div class="premium-form-group full-width">
                                                <label>Address Type</label>
                                                <select name="address_type" class="premium-form-control">
                                                    <option value="">Select address type</option>
                                                    <option value="home" <?php echo (($address_type == 'home') ? 'selected' : '') ?>>Home</option>
                                                    <option value="work" <?php echo (($address_type == 'work') ? 'selected' : '') ?>>Work</option>
                                                    <option value="other" <?php echo (($address_type == 'other') ? 'selected' : '') ?>>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT PANEL: Billing summary and Receiver Info -->
                                <div class="premium-grid-right">


                                    <!-- Card: Fulfillment Partner / Vendor Details -->
                                    <div class="premium-card">
                                        <h3 class="premium-card-title"><i class="fas fa-truck"></i> Delivery Partner</h3>
                                        <div id="deliveryAgentEditMode" style="display: block;">
                                            <div class="premium-form-group">
                                                <?php

                                                if ($order_assign_to == 0) { ?>
                                                    <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 8px; padding: 12px; margin-bottom: 12px; display: flex; gap: 8px; align-items: flex-start;">
                                                        <i class="fas fa-exclamation-triangle" style="color: var(--warning); margin-top: 2px;"></i>
                                                        <div style="font-size: 12px; color: #78350f; line-height: 1.5;">
                                                            <strong>No Delivery Partner Assigned</strong><br>
                                                            Please assign a delivery partner to process this order.
                                                        </div>
                                                    </div>
                                                <?php
                                                }

                                                ?>
                                                <label><?php echo ($order_assign_to == 0) ? 'Select' : 'Change'; ?> Delivery Partner</label>
                                                <select name="assigned_delivery_agent" class="premium-form-control" style="width: 100%;">
                                                    <option value="">Select delivery partner</option>
                                                    <?php

                                                    if ($checkDeliveryAgent = mysqli_query($connection, "SELECT user_id, profile_display_name FROM " . TABLE_PREFIX . "site_access INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=site_access_user_id INNER JOIN " . TABLE_PREFIX . "user ON user_id=site_access_user_id AND user_parent_id=" . USER_ID . " WHERE site_access_type='delivery_agent' AND site_access_status=1 AND site_access_site_id=" . SITE_ID)) {
                                                        if (mysqli_num_rows($checkDeliveryAgent) != 0) {
                                                            while ($deliveryAgentData = mysqli_fetch_assoc($checkDeliveryAgent)) {
                                                                echo "<option value='{$deliveryAgentData['user_id']}' " . ($order_assign_to == $deliveryAgentData['user_id'] ? "selected" : "") . ">{$deliveryAgentData['profile_display_name']}</option>";
                                                            }
                                                        }
                                                    }

                                                    ?>
                                                </select>
                                            </div>
                                            <div style="margin-top: 12px;">
                                                <button type="button" class="btn-premium-action btn-save" id="saveDeliveryPartnerBtn" style="height: 32px; font-size: 12px; padding: 0 10px;"><i class="fas fa-check"></i> <?php echo ($order_assign_to == 0) ? 'Assign' : 'Change'; ?> Delivery Partner</button>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Card 4: Receiver Details -->
                                    <div class="premium-card">
                                        <h3 class="premium-card-title"><i class="fas fa-map-marker-alt"></i> Receiver Details</h3>
                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                            <label>Receiver Name</label>
                                            <input type="text" class="premium-form-control" value="<?php echo $address_name ?>" readonly>
                                        </div>
                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                            <label>Contact Number</label>
                                            <input type="text" class="premium-form-control" value="<?php echo $address_mobile ?>" readonly>
                                        </div>

                                        <?php
                                        if ($orderMeta = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id='{$id}' AND order_site_id=" . SITE_ID)) {
                                            if (mysqli_num_rows($orderMeta) != 0) {
                                                while ($orderMetaData = mysqli_fetch_assoc($orderMeta)) {
                                                    extract($orderMetaData);
                                                    if ($order_meta_key == 'message') { ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label><?php echo ucwords(str_replace('_', ' ', $order_meta_key)) ?></label>
                                                            <textarea class="premium-form-control" readonly><?php echo $order_meta_value ?></textarea>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label><?php echo ucwords(str_replace('_', ' ', $order_meta_key)) ?></label>
                                                            <input type="text" class="premium-form-control" value="<?php echo $order_meta_value ?>" readonly>
                                                        </div>
                                        <?php }
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Actions Bar at bottom of cards -->
                            <div class="premium-action-row">
                                <input type="hidden" name="type" value="order">
                                <input type="hidden" name="id" value="<?php echo $id ?>">
                                <a href="#" id="orderConfirmBtn" class="btn-premium-action btn-confirm"><i class="fas fa-check"></i> Confirm</a>
                                <a href="#" id="orderWaitingBtn" class="btn-premium-action btn-waiting"><i class="fas fa-clock"></i> Waiting</a>
                                <a href="#" id="orderCancelBtn" class="btn-premium-action btn-cancel"><i class="fas fa-times"></i> Cancel</a>
                                <a href="#" id="orderReturnBtn" class="btn-premium-action btn-return"><i class="fas fa-undo"></i> Return</a>
                                <a href="#" id="orderDeliverBtn" class="btn-premium-action btn-delivered"><i class="fas fa-shipping-fast"></i> Delivered</a>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    (function() {
                        var root = document.querySelector('.order-info-ui');

                        if (!root) {
                            return;
                        }

                        var orderId = root.getAttribute('data-order-id') || 'default';
                        var storagePrefix = 'order-ui-collapse-' + orderId + '-';
                        var toggleList = root.querySelectorAll('.order-collapse-toggle');

                        function saveState(key, value) {
                            try {
                                localStorage.setItem(key, value);
                            } catch (e) {
                                // Ignore storage errors silently.
                            }
                        }

                        function readState(key) {
                            try {
                                return localStorage.getItem(key);
                            } catch (e) {
                                return null;
                            }
                        }

                        function applyState(panel, toggle, isOpen) {
                            panel.classList.toggle('is-open', isOpen);
                            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                        }

                        for (var i = 0; i < toggleList.length; i++) {
                            (function(toggle) {
                                var targetId = toggle.getAttribute('data-target');
                                var panel = toggle.parentNode;
                                var storageKey = storagePrefix + targetId;
                                var savedValue = readState(storageKey);

                                if (savedValue !== null) {
                                    applyState(panel, toggle, savedValue === 'open');
                                }

                                toggle.addEventListener('click', function() {
                                    var targetId = this.getAttribute('data-target');
                                    var body = root.querySelector('#' + targetId);

                                    if (!body) {
                                        return;
                                    }

                                    var panel = this.parentNode;
                                    var isOpen = panel.classList.contains('is-open');
                                    var nextOpen = !isOpen;

                                    applyState(panel, this, nextOpen);
                                    saveState(storagePrefix + targetId, nextOpen ? 'open' : 'closed');
                                });
                            })(toggleList[i]);
                        }
                    })();
                </script>

                <script>
                    window.allProductsList = <?php echo json_encode($productListData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
                </script>
                <script>
                    (function() {
                        var root = document.querySelector('.order-info-ui');

                        if (!root) {
                            return;
                        }

                        var orderId = root.getAttribute('data-order-id') || 'default';
                        document.addEventListener('click', function(e) {
                            // 5. Save Delivery Partner Edit Button Click
                            var saveDeliveryPartnerBtn = e.target.closest('#saveDeliveryPartnerBtn');
                            if (saveDeliveryPartnerBtn) {
                                e.preventDefault();
                                var selectEl = document.querySelector('#deliveryAgentEditMode select[name="assigned_delivery_agent"]');
                                if (selectEl) {
                                    var vendorCode = selectEl.value;
                                    if (!vendorCode || vendorCode.trim() === '') {
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'error',
                                                type: 'error',
                                                title: 'Delivery Partner Required',
                                                text: 'Please select a delivery partner.',
                                                timer: 2000,
                                                showConfirmButton: false
                                            });
                                        } else {
                                            alert('Please select a delivery partner.');
                                        }
                                        return;
                                    }

                                    $.ajax({
                                        type: 'POST',
                                        url: '/api/order/assign_delivery_agent/',
                                        data: {
                                            id: orderId,
                                            assigned_delivery_agent: vendorCode
                                        },
                                        dataType: 'json',
                                        success: function(response) {
                                            if (response.status == 200) {
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        type: 'success',
                                                        title: 'Success',
                                                        text: response.msg || 'Delivery Agent assigned successfully.',
                                                        timer: 1500,
                                                        showConfirmButton: false
                                                    });
                                                } else {
                                                    alert(response.msg || 'Delivery Agent assigned successfully.');
                                                }
                                                setTimeout(function() {
                                                    window.location.reload();
                                                }, 1500);
                                            } else {
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        type: 'error',
                                                        title: 'Error',
                                                        text: response.msg || 'Something went wrong.',
                                                        timer: 2000,
                                                        showConfirmButton: false
                                                    });
                                                } else {
                                                    alert(response.msg || 'Something went wrong.');
                                                }
                                            }
                                        },
                                        error: function() {
                                            if (typeof Swal !== 'undefined') {
                                                Swal.fire({
                                                    icon: 'error',
                                                    type: 'error',
                                                    title: 'Error',
                                                    text: 'Failed to assign delivery agent.',
                                                    timer: 2000,
                                                    showConfirmButton: false
                                                });
                                            } else {
                                                alert('Failed to assign delivery agent.');
                                            }
                                        }
                                    });
                                }
                                return;
                            }
                        });
                    })();
                </script>

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