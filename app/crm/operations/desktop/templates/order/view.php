<?php

if (isset($id)) {

    $id = base64_decode($id);

    $productList = mysqli_query($connection, "SELECT product_id, product_title FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE link_type='product' AND product_type='product' AND link_status=200 AND link_site_id=" . APP_SITE_ID);

    if ($productList && mysqli_num_rows($productList) != 0) {

        $productListData = array();

        while ($productLists = mysqli_fetch_assoc($productList)) {
            $productListData[] = array(
                'product_id' => $productLists['product_id'],
                'product_title' => $productLists['product_title']
            );
        }

        if ($checkOrder = mysqli_query($connection, "SELECT profile_first_name, profile_last_name, profile_display_name, user_mobile, user_email, order_product_id, order_product_qty, order_currency, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_id, address_pin_code, address_type, address_mobile, order_generate, transaction_status FROM isw_order INNER JOIN isw_address ON order_address_id=address_id INNER JOIN isw_user ON order_user_id=user_id INNER JOIN isw_profile ON profile_user_id=user_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id INNER JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "product ON product_id=order_product_id WHERE order_id='{$id}' AND product_type='product' AND order_site_id=" . APP_SITE_ID)) {

            if (mysqli_num_rows($checkOrder) != 0) {

                extract(mysqli_fetch_assoc($checkOrder));

                $orderStatusValue = trim((string)$transaction_status);
                $numericStatusMap = array(
                    '0' => 'Pending',
                    '1' => 'Confirmed',
                    '2' => 'Waiting',
                    '3' => 'Cancelled',
                    '4' => 'Returned',
                    '5' => 'Delivered',
                    '100' => 'Pending',
                    '101' => 'Confirmed',
                    '102' => 'Waiting',
                    '103' => 'Cancelled',
                    '104' => 'Returned',
                    '105' => 'Delivered',
                    '200' => 'Confirmed',
                    '201' => 'Waiting',
                    '202' => 'Processing',
                    '203' => 'Cancelled',
                    '204' => 'Returned',
                    '205' => 'Delivered'
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
                $orderStatusChipClass = 'order-chip-status-default';

                if (in_array($orderStatusRaw, array('delivered', 'complete', 'completed', 'success', 'confirmed', 'confirm'))) {
                    $orderStatusChipClass = 'order-chip-status-success';
                } elseif (in_array($orderStatusRaw, array('waiting', 'pending', 'processing', 'on_hold', 'in_progress'))) {
                    $orderStatusChipClass = 'order-chip-status-warning';
                } elseif (in_array($orderStatusRaw, array('cancel', 'cancelled', 'return', 'returned', 'failed', 'rejected'))) {
                    $orderStatusChipClass = 'order-chip-status-danger';
                }

?>

                <style>
                    .order-info-ui {
                        --oi-bg: #f8fafc;
                        --oi-surface: #ffffff;
                        --oi-surface-soft: #f9fafb;
                        --oi-border: #e5e7eb;
                        --oi-text: #111827;
                        --oi-muted: #6b7280;
                        --oi-accent: #0f172a;
                        --oi-accent-soft: #eff6ff;
                        background: var(--oi-bg);
                        border: 1px solid var(--oi-border);
                        border-radius: 14px;
                        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
                        padding: 16px;
                        color: var(--oi-text);
                    }

                    .order-info-head {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        margin-bottom: 16px;
                        padding-bottom: 12px;
                        border-bottom: 1px solid var(--oi-border);
                    }

                    .order-info-head h2 {
                        margin: 0;
                        color: var(--oi-accent);
                        letter-spacing: -0.02em;
                        font-weight: 700;
                        font-size: 22px;
                    }

                    .order-info-subtitle {
                        margin: 5px 0 0;
                        color: var(--oi-muted);
                        font-size: 13px;
                    }

                    .order-overview {
                        display: grid;
                        grid-template-columns: 2fr 1fr;
                        gap: 12px;
                        margin-bottom: 12px;
                    }

                    .order-overview-card {
                        background: var(--oi-surface);
                        border: 1px solid var(--oi-border);
                        border-radius: 12px;
                        padding: 14px;
                        box-shadow: none;
                    }

                    .order-overview-title {
                        margin: 0 0 8px;
                        color: var(--oi-muted);
                        text-transform: uppercase;
                        font-size: 11px;
                        letter-spacing: 0.8px;
                        font-weight: 700;
                    }

                    .order-meta-line {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 6px;
                    }

                    .order-chip {
                        display: inline-flex;
                        align-items: center;
                        padding: 6px 10px;
                        border-radius: 999px;
                        border: 1px solid #d1d5db;
                        background: var(--oi-surface-soft);
                        color: #374151;
                        font-size: 11px;
                        font-weight: 600;
                        line-height: 1;
                        max-width: 100%;
                        white-space: normal;
                    }

                    .order-chip.order-chip-accent {
                        border-color: #bfdbfe;
                        background: var(--oi-accent-soft);
                        color: #1e40af;
                    }

                    .order-chip.order-chip-status-success {
                        background: #ecfdf5;
                        border-color: #bbf7d0;
                        color: #166534;
                    }

                    .order-chip.order-chip-status-warning {
                        background: #fffbeb;
                        border-color: #fde68a;
                        color: #92400e;
                    }

                    .order-chip.order-chip-status-danger {
                        background: #fef2f2;
                        border-color: #fecaca;
                        color: #991b1b;
                    }

                    .order-chip.order-chip-status-default {
                        background: #f3f4f6;
                        border-color: #d1d5db;
                        color: #4b5563;
                    }

                    .order-metric-grid {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 10px;
                    }

                    .order-metric {
                        border: 1px solid var(--oi-border);
                        border-radius: 10px;
                        background: #fff;
                        padding: 10px;
                    }

                    .order-metric-label {
                        margin: 0;
                        font-size: 10px;
                        text-transform: uppercase;
                        letter-spacing: 0.7px;
                        color: #9ca3af;
                    }

                    .order-metric-value {
                        margin-top: 4px;
                        font-size: 14px;
                        font-weight: 700;
                        color: #111827;
                    }

                    .order-info-shell {
                        background: transparent;
                        border-radius: 12px;
                        padding: 0;
                        border: 0;
                    }

                    .order-info-ui .box-1 {
                        display: grid;
                        grid-template-columns: 1.6fr 1fr;
                        gap: 12px;
                        float: none;
                    }

                    .order-info-ui .box-1>.box-2 {
                        min-width: 0;
                    }

                    .order-info-ui .box-2 {
                        width: auto;
                        float: none;
                        background: var(--oi-surface);
                        border: 1px solid var(--oi-border);
                        border-radius: 12px;
                        padding: 14px;
                        box-shadow: none;
                    }

                    .order-info-ui .box-2 .box-2 {
                        border: 0;
                        box-shadow: none;
                        background: transparent;
                        border-radius: 0;
                    }

                    .order-summary-sticky {
                        position: sticky;
                        top: 16px;
                        align-self: start;
                    }

                    .order-section-title {
                        margin: 0 0 10px;
                        color: #111827;
                        font-size: 14px;
                        font-weight: 700;
                        text-transform: none;
                        letter-spacing: 0.2px;
                    }

                    .order-item-row {
                        display: grid;
                        grid-template-columns: minmax(0, 1fr) 150px;
                        gap: 10px;
                        margin-bottom: 10px;
                        padding: 10px;
                        border-radius: 10px;
                        background: #f9fafb;
                        border: 1px solid var(--oi-border);
                    }

                    .order-item-row .form-group {
                        margin-bottom: 0;
                    }

                    .order-customer-grid {
                        display: grid;
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                        gap: 10px;
                    }

                    .order-customer-grid .form-group {
                        margin-bottom: 0;
                    }

                    .order-info-ui .form-group {
                        margin-bottom: 12px;
                    }

                    .order-info-ui .form-group label {
                        display: block;
                        margin-bottom: 6px;
                        color: #6b7280;
                        font-size: 12px;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.3px;
                    }

                    .order-info-ui .inside-shadow,
                    .order-info-ui .form-control {
                        border-radius: 8px;
                        border: 1px solid #d1d5db;
                        box-shadow: none;
                        min-height: 40px;
                    }

                    .order-info-ui .inside-shadow:focus,
                    .order-info-ui .form-control:focus {
                        border-color: #93c5fd;
                        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
                    }

                    .order-summary-image {
                        width: 100%;
                        max-width: 230px;
                        height: 200px;
                        object-fit: cover;
                        border-radius: 10px;
                        border: 1px solid var(--oi-border);
                        margin-bottom: 14px;
                        background: #f3f4f6;
                    }

                    .order-collapsible {
                        border: 1px solid var(--oi-border);
                        border-radius: 11px;
                        background: #fff;
                        margin-bottom: 10px;
                        overflow: hidden;
                    }

                    .order-collapse-toggle {
                        width: 100%;
                        border: 0;
                        border-bottom: 1px solid var(--oi-border);
                        background: #f9fafb;
                        color: #1f2937;
                        text-align: left;
                        padding: 10px 12px;
                        font-size: 12px;
                        font-weight: 700;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        cursor: pointer;
                    }

                    .order-collapse-toggle-icon {
                        font-size: 13px;
                        color: #9ca3af;
                        transition: transform 0.2s ease;
                    }

                    .order-collapsible.is-open .order-collapse-toggle-icon {
                        transform: rotate(180deg);
                    }

                    .order-collapse-body {
                        overflow: hidden;
                        max-height: 0;
                        opacity: 0;
                        padding: 0 12px;
                        transition: max-height 0.28s ease, opacity 0.2s ease, padding 0.22s ease;
                    }

                    .order-collapsible.is-open .order-collapse-body {
                        max-height: 4000px;
                        opacity: 1;
                        padding: 12px;
                    }

                    .order-action-row {
                        grid-column: 1 / -1;
                        background: var(--oi-surface);
                        border: 1px solid var(--oi-border);
                        border-radius: 12px;
                        padding: 12px;
                        margin-top: 4px;
                        box-shadow: none;
                        width: 100%;
                    }

                    .order-action-row .btn {
                        margin-right: 8px;
                        margin-bottom: 8px;
                        border-radius: 8px;
                        min-width: 110px;
                        text-align: center;
                        box-shadow: none;
                        transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease;
                    }

                    .order-action-row .btn-green {
                        background: #ecfdf5;
                        border: 1px solid #bbf7d0;
                    }

                    .order-action-row .btn-yellow {
                        background: #fffbeb;
                        border: 1px solid #fde68a;
                    }

                    .order-action-row .btn-red {
                        background: #fef2f2;
                        border: 1px solid #fecaca;
                    }

                    @media (max-width: 991px) {
                        .order-overview,
                        .order-item-row,
                        .order-customer-grid,
                        .order-info-ui .box-1 {
                            grid-template-columns: 1fr;
                        }

                        .order-info-ui .box-2 {
                            padding: 10px;
                        }

                        .order-summary-sticky {
                            position: static;
                        }

                        .order-action-row {
                            width: 100%;
                        }

                        .order-action-row .btn {
                            width: 100%;
                            margin-right: 0;
                        }
                    }
                </style>

                <div class="order-info-ui" data-order-id="<?php echo htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="order-info-head">
                        <div>
                            <h2>Order Info</h2>
                            <p class="order-info-subtitle">Review, update, and process this order in one place.</p>
                        </div>
                    </div>

                    <div class="order-overview">
                        <div class="order-overview-card">
                            <p class="order-overview-title">Order Snapshot</p>
                            <div class="order-meta-line">
                                <span class="order-chip order-chip-accent">Order ID: <?php echo htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="order-chip <?php echo $orderStatusChipClass; ?>">Status: <?php echo htmlspecialchars($orderStatusText, ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="order-chip">Payment: <?php echo htmlspecialchars(ucwords((string)$order_payment_mode), ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="order-chip">Created: <?php echo htmlspecialchars((string)$order_generate, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>
                        <div class="order-overview-card">
                            <p class="order-overview-title">Quick Metrics</p>
                            <div class="order-metric-grid">
                                <div class="order-metric">
                                    <p class="order-metric-label">Items</p>
                                    <div class="order-metric-value"><?php echo sizeof(explode(',', $order_product_id)); ?></div>
                                </div>
                                <div class="order-metric">
                                    <p class="order-metric-label">Delivery</p>
                                    <div class="order-metric-value"><?php echo htmlspecialchars(($order_delivery_charges != '') ? (string)$order_delivery_charges : '0', ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                                <div class="order-metric">
                                    <p class="order-metric-label">Total</p>
                                    <div class="order-metric-value"><?php echo htmlspecialchars(($order_total_price != '') ? (string)$order_total_price : '0', ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-box order-info-shell">
                        <form id="orderData">
                            <div class="box-1">
                                <div class="box-2">
                                    <div class="order-collapsible order-collapsible-edit is-open">
                                        <button type="button" class="order-collapse-toggle" data-target="orderEditCustomerPanel" aria-expanded="true">
                                            Order + Customer Details
                                            <span class="order-collapse-toggle-icon">▾</span>
                                        </button>
                                        <div class="order-collapse-body" id="orderEditCustomerPanel">
                                            <h3 class="order-section-title">Order Items</h3>
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
                                                                <label>Product name (Cart item <?php echo $i + 1 ?>)</label>
                                                                <select name="product_id_<?php echo $i ?>" class="form-control inside-shadow">
                                                                    <option value="">Select product</option>
                                                                    <?php
                                                                    for ($j = 0; $j < sizeof($productListData); $j++) {
                                                                        $selectedAttr = ($productListData[$j]['product_id'] == $productData[$i]) ? 'selected' : '';
                                                                        echo "<option value='" . htmlspecialchars($productListData[$j]['product_id'], ENT_QUOTES, 'UTF-8') . "' {$selectedAttr}>" . htmlspecialchars($productListData[$j]['product_title'], ENT_QUOTES, 'UTF-8') . "</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Qty</label>
                                                                <input type="text" name="qty_<?php echo $i ?>" class="form-control inside-shadow"
                                                                    value="<?php echo htmlspecialchars(str_replace(' ', '', (string)($productQtyData[$i] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
                                                            </div>
                                                        </div>

                                                    <?php } else { ?>

                                                        <div class="order-item-row">
                                                            <div class="form-group">
                                                                <label>Product name (Cart item <?php echo $i + 1 ?>)</label>
                                                                <select name="product_id_<?php echo $i ?>" class="form-control inside-shadow">
                                                                    <option value="">Select product</option>
                                                                    <?php
                                                                    for ($j = 0; $j < sizeof($productListData); $j++) {
                                                                        echo "<option value='" . htmlspecialchars($productListData[$j]['product_id'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($productListData[$j]['product_title'], ENT_QUOTES, 'UTF-8') . "</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Qty</label>
                                                                <input type="text" name="qty_<?php echo $i ?>" class="form-control inside-shadow">
                                                            </div>
                                                        </div>

                                            <?php
                                                    }
                                                }
                                            }
                                            ?>

                                            <h3 class="order-section-title">Customer Details</h3>

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
                                    </div>
                                </div>

                                <div class="box-2 order-summary-sticky">
                                    <div class="order-collapsible order-collapsible-summary is-open">
                                        <button type="button" class="order-collapse-toggle" data-target="orderPaymentReceiverPanel" aria-expanded="true">
                                            Payment + Receiver Details
                                            <span class="order-collapse-toggle-icon">▾</span>
                                        </button>
                                        <div class="order-collapse-body" id="orderPaymentReceiverPanel">
                                            <h3 class="order-section-title">Payment &amp; Receiver</h3>

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

                                            <h3 class="order-section-title">Receiver Details</h3>

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
                                    </div>
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
                            } catch (e) {}
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
                                var savedValue = readState(storagePrefix + targetId);

                                if (savedValue !== null) {
                                    applyState(panel, toggle, savedValue === 'open');
                                }

                                toggle.addEventListener('click', function() {
                                    var tId = this.getAttribute('data-target');
                                    var body = root.querySelector('#' + tId);
                                    if (!body) {
                                        return;
                                    }
                                    var p = this.parentNode;
                                    var nextOpen = !p.classList.contains('is-open');
                                    applyState(p, this, nextOpen);
                                    saveState(storagePrefix + tId, nextOpen ? 'open' : 'closed');
                                });
                            })(toggleList[i]);
                        }
                    })();
                </script>

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
