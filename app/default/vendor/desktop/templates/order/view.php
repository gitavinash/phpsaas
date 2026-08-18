<?php

if (isset($id)) {

    $id = base64_decode($id);

    $siteId = " IN (" . APP_SITE_ID . ", " . SITE_ID . ")";
    $isApp32 = ((int)SITE_APP_ID === 32);

    $searchrProductSql = "SELECT product_id, product_title, product_image FROM isw_product WHERE product_type='product' AND product_site_id" . $siteId . " ORDER BY product_title ASC";

    $productList = mysqli_query($connection, $searchrProductSql);

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

        $checkOrderSql = "SELECT profile_first_name, profile_last_name, profile_display_name, user_mobile, user_email, order_product_id, order_assign_to, order_vendor_id, order_product_qty, order_currency, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_id, address_pin_code, address_type, address_mobile, order_generate, order_status, product_title, product_image, inventory_currency_symbol FROM isw_order LEFT JOIN isw_address ON order_address_id=address_id LEFT JOIN isw_user ON order_user_id=user_id LEFT JOIN isw_profile ON profile_user_id=user_id LEFT JOIN isw_city ON city_id=address_city_id LEFT JOIN isw_state ON state_id=address_state_id LEFT JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id";

        $checkOrderSql .= " LEFT JOIN " . TABLE_PREFIX . "product ON product_id=order_product_id AND product_type='product' AND product_site_id" . $siteId;
        $checkOrderSql .= " LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=order_product_id AND inventory_type='product' AND inventory_site_id" . $siteId;

        $checkOrderSql .= " WHERE order_id='{$id}' AND order_site_id" . $siteId;

        if ($checkOrder = mysqli_query($connection, $checkOrderSql)) {

            if (mysqli_num_rows($checkOrder) != 0) {

                extract(mysqli_fetch_assoc($checkOrder));

                $orderStatusValue = trim((string)$order_status);

                $numericStatusMap = array(
                    '0' => 'Pending',
                    '1' => 'Order Accepted by Vendor',
                    '2' => 'Assigned to Vendor',
                    '3' => 'Processing',
                    '4' => 'Shipped',
                    '5' => 'Out for Delivery',
                    '6' => 'Delivered',
                    '7' => 'Cancelled',
                    '8' => 'Returned',
                    '9' => 'Rescheduled',
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
                } elseif (in_array($orderStatusRaw, array('waiting', 'pending', 'processing', 'on_hold', 'in_progress', 'out_for_delivery', 'shipped'))) {
                    $orderStatusChipClass = 'badge-pending';
                } elseif (in_array($orderStatusRaw, array('cancel', 'cancelled', 'return', 'returned', 'failed', 'rejected'))) {
                    $orderStatusChipClass = 'badge-cancelled';
                } elseif (in_array($orderStatusRaw, array('rescheduled', 'reschedule'))) {
                    $orderStatusChipClass = 'badge-rescheduled';
                }

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
                            <span class="premium-stat-value" style="display: flex; align-items: center; gap: 8px;">
                                <?php if (!empty($product_image)) { ?>
                                    <a href="<?php echo htmlspecialchars($product_image, ENT_QUOTES); ?>" target="_blank" style="position: relative; display: inline-flex; align-items: center; text-decoration: none;" title="Open full product image">
                                        <img src="<?php echo htmlspecialchars($product_image, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($product_title ?? 'Product', ENT_QUOTES); ?>" style="width: 50px; height: 50px; border-radius: 6px; object-fit: cover; border: 1px solid #cbd5e1;">
                                        <i class="fas fa-external-link-alt" style="position: absolute; bottom: 3px; right: 3px; background: rgba(15, 23, 42, 0.75); color: #ffffff; font-size: 8px; padding: 2px 3px; border-radius: 3px; pointer-events: none;"></i>
                                    </a>
                                <?php } else { ?>
                                    <span style="font-size: 16px;">📦</span>
                                <?php } ?>
                                <span><?php echo htmlspecialchars(ucwords($product_title ?? 'N/A'), ENT_QUOTES); ?></span>
                            </span>
                        </div>
                        <div class="premium-stat-item">
                            <span class="premium-stat-label">Items Count</span>
                            <span class="premium-stat-value"><?php echo sizeof(explode(',', $order_product_id)) ?> items</span>
                        </div>
                        <div class="premium-stat-item">
                            <span class="premium-stat-label">Created Date</span>
                            <span class="premium-stat-value"><?php echo $order_generate ?></span>
                        </div>
                        <div class="premium-stat-item" style="border-right: none;">
                            <span class="premium-stat-label">Total Amount</span>
                            <span class="premium-stat-value"><?php echo (($order_total_price != '') ? $inventory_currency_symbol . ' ' . $order_total_price : '0') ?></span>
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
                                                <input type="text" name="first_name" class="premium-form-control" value="<?php echo $profile_first_name ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="last_name" class="premium-form-control" value="<?php echo $profile_last_name ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Email Address</label>
                                                <input type="text" name="email" class="premium-form-control" value="<?php echo $user_email ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>House No. / Suite</label>
                                                <input type="text" name="address_house_no" class="premium-form-control" value="<?php echo $address_house_no ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Address Line</label>
                                                <input type="text" name="address_area" class="premium-form-control" value="<?php echo $address_area ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Landmark</label>
                                                <input type="text" name="address_landmark" class="premium-form-control" value="<?php echo $address_landmark ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>City</label>
                                                <input type="text" name="city" class="premium-form-control" value="<?php echo $city_name ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>State</label>
                                                <select class="premium-form-control selectJS" name="state" required <?php echo ((USER_TYPE != 'administrator') ? 'disabled' : '') ?>>
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
                                                <input type="text" name="address_pin_code" class="premium-form-control" value="<?php echo $address_pin_code ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group full-width">
                                                <label>Address Type</label>
                                                <select name="address_type" class="premium-form-control selectJS" <?php echo ((USER_TYPE != 'administrator') ? 'disabled' : '') ?>>
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
                                                <select name="assigned_delivery_agent" class="premium-form-control selectJS" style="width: 100%;">
                                                    <option value="">Select delivery partner</option>
                                                    <?php

                                                    $userParentFilter = (USER_TYPE != 'administrator') ? "AND user_parent_id=" . USER_ID : "";

                                                    if ($checkDeliveryAgent = mysqli_query($connection, "SELECT user_id, profile_display_name FROM " . TABLE_PREFIX . "site_access INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=site_access_user_id INNER JOIN " . TABLE_PREFIX . "user ON user_id=site_access_user_id {$userParentFilter} WHERE site_access_type='delivery_agent' AND site_access_status=1 AND site_access_site_id=" . SITE_ID)) {
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

                                    <!-- Card 4: Receiver Details & Meta Information -->
                                    <div class="premium-card">
                                        <h3 class="premium-card-title"><i class="fas fa-user-check"></i> Receiver Details & Order Meta</h3>

                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                            <label>Receiver Name</label>
                                            <input type="text" name="address_name" class="premium-form-control" value="<?php echo htmlspecialchars($address_name ?? '', ENT_QUOTES); ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                        </div>
                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                            <label>Contact Number</label>
                                            <input type="text" name="address_mobile" class="premium-form-control" value="<?php echo htmlspecialchars($address_mobile ?? '', ENT_QUOTES); ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                        </div>

                                        <?php
                                        if (!function_exists('formatDeliverySlotAMPM')) {
                                            function formatDeliverySlotAMPM($slotStr)
                                            {
                                                if (empty($slotStr)) return '';
                                                $slotStr = trim($slotStr);
                                                if (preg_match('/^(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})$/', $slotStr, $matches)) {
                                                    $startTime = strtotime($matches[1]);
                                                    $endTime = strtotime($matches[2]);
                                                    if ($startTime !== false && $endTime !== false) {
                                                        return date('h:i A', $startTime) . ' - ' . date('h:i A', $endTime);
                                                    }
                                                }
                                                return $slotStr;
                                            }
                                        }

                                        $slotAlreadyRendered = false;

                                        if ($orderMeta = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id='{$id}' AND order_site_id" . $siteId)) {
                                            if (mysqli_num_rows($orderMeta) != 0) {
                                                $todayDateStr = date('Y-m-d');

                                                while ($orderMetaData = mysqli_fetch_assoc($orderMeta)) {
                                                    extract($orderMetaData);
                                                    $cleanMetaKey = strtolower(trim(str_replace(['_', ' '], '', (string)$order_meta_key)));

                                                    if ($cleanMetaKey == 'deliverydate') {
                                                        $formattedDate = '';
                                                        if (!empty($order_meta_value)) {
                                                            $time = strtotime($order_meta_value);
                                                            $formattedDate = ($time !== false) ? date('Y-m-d', $time) : $order_meta_value;
                                                        }
                                        ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label>Delivery Date</label>
                                                            <input type="date" name="order_meta[<?php echo htmlspecialchars($order_meta_key, ENT_QUOTES); ?>]" class="premium-form-control order-meta-datepicker" min="<?php echo $todayDateStr; ?>" value="<?php echo htmlspecialchars($formattedDate, ENT_QUOTES); ?>" readonly>
                                                        </div>
                                                    <?php
                                                    } else if (in_array($cleanMetaKey, ['preferredtime', 'deliveryslot', 'timeslot'])) {
                                                        if ($slotAlreadyRendered) continue;
                                                        $slotAlreadyRendered = true;
                                                        $displaySlot = formatDeliverySlotAMPM($order_meta_value ?? '');
                                                    ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label>Delivery Slot</label>
                                                            <input type="text" name="order_meta[<?php echo htmlspecialchars($order_meta_key, ENT_QUOTES); ?>]" class="premium-form-control" value="<?php echo htmlspecialchars($displaySlot, ENT_QUOTES); ?>" readonly>
                                                        </div>
                                                    <?php
                                                    } else if (strpos($cleanMetaKey, 'photo') !== false || strpos($cleanMetaKey, 'image') !== false || preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', (string)$order_meta_value)) {
                                                    ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $order_meta_key)), ENT_QUOTES); ?></label>
                                                            <?php if (!empty($order_meta_value)) { ?>
                                                                <div class="delivery-photo-wrapper" style="margin-top: 6px;">
                                                                    <a href="<?php echo htmlspecialchars($order_meta_value, ENT_QUOTES); ?>" target="_blank" title="Click to view full image">
                                                                        <img src="<?php echo htmlspecialchars($order_meta_value, ENT_QUOTES); ?>" alt="Delivery Photo" style="max-width: 220px; max-height: 220px; border-radius: 10px; border: 1px solid #cbd5e1; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08); display: block; object-fit: cover;">
                                                                    </a>
                                                                </div>
                                                            <?php } else { ?>
                                                                <span class="text-muted" style="font-size: 13px; color: #64748b;">No photo available</span>
                                                            <?php } ?>
                                                        </div>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $order_meta_key)), ENT_QUOTES); ?></label>
                                                            <?php if (strlen($order_meta_value) > 60 || in_array($cleanMetaKey, ['message', 'comment', 'reason', 'note', 'description'])) { ?>
                                                                <textarea name="order_meta[<?php echo htmlspecialchars($order_meta_key, ENT_QUOTES); ?>]" class="premium-form-control" readonly><?php echo htmlspecialchars($order_meta_value ?? '', ENT_QUOTES); ?></textarea>
                                                            <?php } else { ?>
                                                                <input type="text" name="order_meta[<?php echo htmlspecialchars($order_meta_key, ENT_QUOTES); ?>]" class="premium-form-control" value="<?php echo htmlspecialchars($order_meta_value ?? '', ENT_QUOTES); ?>" readonly>
                                                            <?php } ?>
                                                        </div>
                                            <?php       }
                                                }
                                            }
                                        }

                                        if (!$slotAlreadyRendered) {
                                            ?>
                                            <div class="premium-form-group" style="margin-bottom: 16px;">
                                                <label>Delivery Slot</label>
                                                <input type="text" class="premium-form-control" value="Not selected" readonly>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Actions Bar at bottom of cards -->
                            <div class="premium-action-row">
                                <input type="hidden" name="id" value="<?php echo $id ?>">

                                <!-- CTA for Vendors -->
                                <input type="hidden" name="type" value="order">
                                <input type="hidden" name="id" value="<?php echo $id ?>">
                                <a href="#" id="orderConfirmBtn" class="btn-premium-action btn-confirm"><i class="fas fa-check"></i> Accept</a>
                                <a href="#" id="orderCancelBtn" class="btn-premium-action btn-cancel"><i class="fas fa-times"></i> Reject</a>
                                <!-- CTA for Delivery Agents -->
                                <a href="/tracking/?orderid=OD<?php echo $id ?>" class="btn-premium-action btn-track" target="_blank"><i class="fas fa-route"></i> Track order <i class="fas fa-external-link-alt" style="font-size: 11px; margin-left: 4px;"></i></a>

                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Action Modal -->
                <div id="orderActionModal" class="order-action-modal">
                    <div class="order-action-modal-backdrop"></div>
                    <div class="order-action-modal-content">
                        <div class="order-action-modal-header">
                            <h3 id="modalTitle">Process Order Action</h3>
                            <button type="button" class="close-modal-btn" aria-label="Close modal">&times;</button>
                        </div>
                        <form id="orderActionForm" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <input type="hidden" name="action_type" id="actionTypeInput" value="">

                            <div id="returnReasonGroup" class="premium-form-group" style="display: none;">
                                <label for="returnReasonInput">Return Reason <span id="returnReasonRequiredMark" class="required" style="display: none;">*</span></label>
                                <select id="returnReasonInput" name="return_reason" class="premium-form-control selectJS">
                                    <option value="">Select reason</option>
                                    <option value="Damaged Item">Damaged Item</option>
                                    <option value="Wrong Item Delivered">Wrong Item Delivered</option>
                                    <option value="Quality Issue">Quality Issue</option>
                                    <option value="Size Issue">Size Issue</option>
                                    <option value="Late Delivery">Late Delivery</option>
                                    <option value="Changed My Mind">Changed My Mind</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="premium-form-group">
                                <label for="actionComment">Comment <span id="commentRequiredMark" class="required">*</span></label>
                                <textarea id="actionComment" name="comment" class="premium-form-control" placeholder="Enter comments here..."></textarea>
                            </div>

                            <div id="rescheduleFields" style="display: none;">
                                <div class="premium-form-group">
                                    <label for="deliveryDateInput">Delivery Date <span id="deliveryDateRequiredMark" class="required" style="display: none;">*</span></label>
                                    <input type="date" id="deliveryDateInput" name="delivery_date" class="premium-form-control">
                                </div>
                                <div class="premium-form-group">
                                    <label for="deliveryPreferredTimeInput">Delivery Preferred Time <span id="deliveryTimeRequiredMark" class="required" style="display: none;">*</span></label>
                                    <select id="deliveryPreferredTimeInput" name="delivery_preferred_time" class="premium-form-control selectJS">
                                        <option value="">Select time slot</option>
                                        <option value="00:00-01:00">12:00 AM - 01:00 AM</option>
                                        <option value="01:00-02:00">01:00 AM - 02:00 AM</option>
                                        <option value="02:00-03:00">02:00 AM - 03:00 AM</option>
                                        <option value="03:00-04:00">03:00 AM - 04:00 AM</option>
                                        <option value="04:00-05:00">04:00 AM - 05:00 AM</option>
                                        <option value="05:00-06:00">05:00 AM - 06:00 AM</option>
                                        <option value="06:00-07:00">06:00 AM - 07:00 AM</option>
                                        <option value="07:00-08:00">07:00 AM - 08:00 AM</option>
                                        <option value="08:00-09:00">08:00 AM - 09:00 AM</option>
                                        <option value="09:00-10:00">09:00 AM - 10:00 AM</option>
                                        <option value="10:00-11:00">10:00 AM - 11:00 AM</option>
                                        <option value="11:00-12:00">11:00 AM - 12:00 PM</option>
                                        <option value="12:00-13:00">12:00 PM - 01:00 PM</option>
                                        <option value="13:00-14:00">01:00 PM - 02:00 PM</option>
                                        <option value="14:00-15:00">02:00 PM - 03:00 PM</option>
                                        <option value="15:00-16:00">03:00 PM - 04:00 PM</option>
                                        <option value="16:00-17:00">04:00 PM - 05:00 PM</option>
                                        <option value="17:00-18:00">05:00 PM - 06:00 PM</option>
                                        <option value="18:00-19:00">06:00 PM - 07:00 PM</option>
                                        <option value="19:00-20:00">07:00 PM - 08:00 PM</option>
                                        <option value="20:00-21:00">08:00 PM - 09:00 PM</option>
                                        <option value="21:00-22:00">09:00 PM - 10:00 PM</option>
                                        <option value="22:00-23:00">10:00 PM - 11:00 PM</option>
                                        <option value="23:00-24:00">11:00 PM - 12:00 AM</option>
                                    </select>
                                </div>
                            </div>

                            <div class="premium-form-group" id="actionPhotoGroup">
                                <label for="actionPhoto">Upload Photo <span id="photoRequiredMark" class="required" style="display: none;">*</span></label>
                                <div class="photo-upload-container">
                                    <input type="file" id="actionPhoto" name="photo" accept="image/*" class="premium-form-control-file">
                                    <div class="photo-preview-box" id="photoPreviewBox">
                                        <i class="fas fa-camera"></i>
                                        <span>Select or capture photo</span>
                                    </div>
                                </div>
                            </div>

                            <div class="order-action-modal-footer">
                                <button type="button" class="btn-premium-action btn-secondary close-modal-btn" style="background-color: #3b82f6 !important; color: #ffffff !important;">Cancel</button>
                                <button type="submit" class="btn-premium-action btn-primary" id="submitActionBtn">Confirm</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="orderActionFeedback" class="order-action-feedback" aria-live="polite" style="display: none;">
                    <div class="order-action-feedback-header">
                        <div>
                            <span class="order-action-feedback-title" id="orderActionFeedbackTitle">Order Update</span>
                            <div class="order-action-feedback-meta" id="orderActionFeedbackMeta">Status response</div>
                        </div>
                        <button type="button" id="toggleOrderActionFeedback" class="order-action-feedback-toggle" aria-expanded="false">Show</button>
                    </div>
                    <div class="order-action-feedback-body" id="orderActionFeedbackBody"></div>
                </div>

                <!-- Toast Notification Container -->
                <div id="orderToastNotification" class="order-toast-notification"></div>

                <script>
                    window.allProductsList = <?php echo json_encode($productListData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
                </script>
                <script src="<?php echo JS_LIBRARY; ?>order_view.js"></script>

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