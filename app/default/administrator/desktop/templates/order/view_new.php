<?php

if (isset($id)) {

    $id = base64_decode($id);
    $isApp32 = ((int)SITE_APP_ID === 32);

    $productList = mysqli_query($connection, "SELECT product_id, product_title FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE link_type='product' AND product_type='product' AND link_status=200 AND link_site_id=" . APP_SITE_ID);

    if ((mysqli_num_rows($productList) != 0) || $isApp32) {

        $productListData = array();

        while ($productLists = mysqli_fetch_assoc($productList)) {

            extract($productLists);

            $productListData[] = array(
                'product_id' => $product_id,
                'product_title' => $product_title
            );
        }

        $vendorListData = array();
        if ($isApp32) {
            $vendorSql = "SELECT user_type_name, user_type_code FROM " . TABLE_PREFIX . "user_type WHERE user_type_app_id='" . SITE_APP_ID . "' AND user_type_status=1 AND (LOWER(user_type_name) LIKE '%vendor%' OR LOWER(user_type_code) LIKE '%vendor%' OR LOWER(user_type_name) LIKE '%supplier%' OR LOWER(user_type_code) LIKE '%supplier%' OR LOWER(user_type_name) LIKE '%seller%' OR LOWER(user_type_code) LIKE '%seller%') ORDER BY user_type_name ASC";
            if ($vendorList = mysqli_query($connection, $vendorSql)) {
                if (mysqli_num_rows($vendorList) != 0) {
                    while ($vendorData = mysqli_fetch_assoc($vendorList)) {
                        $vendorListData[] = $vendorData;
                    }
                }
            }

            if (empty($vendorListData)) {
                $fallbackVendorSql = "SELECT user_type_name, user_type_code FROM " . TABLE_PREFIX . "user_type WHERE user_type_app_id='" . SITE_APP_ID . "' AND user_type_status=1 ORDER BY user_type_name ASC";
                if ($fallbackVendorList = mysqli_query($connection, $fallbackVendorSql)) {
                    if (mysqli_num_rows($fallbackVendorList) != 0) {
                        while ($vendorData = mysqli_fetch_assoc($fallbackVendorList)) {
                            $vendorListData[] = $vendorData;
                        }
                    }
                }
            }
        }

        $checkOrderSql = "SELECT profile_first_name, profile_last_name, profile_display_name, user_mobile, user_email, order_product_id, order_product_qty, order_currency, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_id, address_pin_code, address_type, address_mobile, order_generate, transaction_status, product_title, product_image, inventory_currency_symbol FROM isw_order INNER JOIN isw_address ON order_address_id=address_id INNER JOIN isw_user ON order_user_id=user_id INNER JOIN isw_profile ON profile_user_id=user_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id INNER JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id";

        if ($isApp32) {
            $checkOrderSql .= " LEFT JOIN " . TABLE_PREFIX . "product ON product_id=order_product_id AND product_type='product' LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=order_product_id AND inventory_type='product' AND inventory_site_id=" . APP_SITE_ID . " AND product_site_id=" . APP_SITE_ID;
        } else {
            $checkOrderSql .= " INNER JOIN " . TABLE_PREFIX . "product ON product_id=order_product_id";
        }

        $checkOrderSql .= " WHERE order_id='{$id}' AND order_site_id=" . APP_SITE_ID;

        if (!$isApp32) {
            $checkOrderSql .= " AND product_type='product'";
        }

        if ($checkOrder = mysqli_query($connection, $checkOrderSql)) {

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

                $orderLogisticsPayload = trim("Order: #{$id}\nCustomer: {$profile_display_name}\nMobile: {$address_mobile}\nCity: {$city_name}\nState: {$state_id}\nDelivery: {$order_delivery_charges}\nTotal: {$order_total_price}\nCreated: {$order_generate}");
                $orderQrUrl = '';

                if (defined('QR_CODE') && file_exists(QR_CODE . 'qrlib.php')) {
                    require_once QR_CODE . 'qrlib.php';

                    $qrDirectory = ROOT . '/upload/' . SITE_DOMAIN . '/qrcode/';

                    if (!is_dir($qrDirectory)) {
                        @mkdir($qrDirectory, 0755, true);
                    }

                    $qrFileName = 'order_' . $id . '.png';
                    $qrFilePath = $qrDirectory . $qrFileName;

                    if (!file_exists($qrFilePath) && trim($orderLogisticsPayload) !== '') {
                        QRcode::png($orderLogisticsPayload, $qrFilePath, 'H', 8, 2);
                    }

                    if (file_exists($qrFilePath)) {
                        $orderQrUrl = SITE_URL . '/upload/' . SITE_DOMAIN . '/qrcode/' . $qrFileName;
                    }
                }

?>

                <style>
                    .order-info-ui {
                        --oi-bg: transparent;
                        --oi-surface: #ffffff;
                        --oi-surface-soft: #f8fafc;
                        --oi-border: #e7edf3;
                        --oi-text: #111827;
                        --oi-muted: #6b7280;
                        --oi-accent: #0f172a;
                        --oi-accent-soft: #eef4ff;
                        background: var(--oi-bg);
                        border: 0;
                        border-radius: 0;
                        box-shadow: none;
                        padding: 0;
                        color: var(--oi-text);
                    }

                    .order-info-subtitle {
                        margin: 0;
                        color: var(--oi-muted);
                        font-size: 13px;
                        line-height: 1.45;
                    }

                    .order-quick-action {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                        padding: 8px 12px;
                        border-radius: 10px;
                        border: 1px solid #dbe3ea;
                        background: #fff;
                        color: #1f2937;
                        font-size: 12px;
                        font-weight: 700;
                        text-decoration: none;
                        transition: all 0.16s ease;
                        cursor: pointer;
                    }

                    .order-quick-action:hover {
                        border-color: #cbd5e1;
                        background: #f8fafc;
                        color: #0f172a;
                    }

                    .order-quick-action.primary {
                        background: #0f172a;
                        border-color: #0f172a;
                        color: #fff;
                    }

                    .order-quick-action.primary:hover {
                        background: #111827;
                        border-color: #111827;
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
                        border: 1px solid #dbe3ea;
                        background: var(--oi-surface-soft);
                        color: #334155;
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

                    .order-info-ui .box-2 .box-2 {
                        border: 0;
                        box-shadow: none;
                        background: transparent;
                        border-radius: 0;
                    }

                    .order-summary-sticky {
                        position: sticky;
                        top: 12px;
                        align-self: start;
                    }

                    .order-info-shell {
                        background: transparent;
                        border: 0;
                        border-radius: 0;
                        padding: 0;
                        min-height: 100vh;
                    }

                    .order-info-ui .box-1 {
                        display: grid;
                        grid-template-columns: minmax(0, 1fr) 320px;
                        gap: 18px;
                        float: none;
                        margin-top: 6px;
                    }

                    .order-info-ui .box-1>.box-2 {
                        min-width: 0;
                    }

                    .order-info-ui .box-2 {
                        width: auto;
                        float: none;
                        background: transparent;
                        border: 0;
                        border-radius: 0;
                        padding: 0;
                        box-shadow: none;
                    }

                    .order-summary-rail {
                        background: #fff;
                        border: 1px solid #eef2f7;
                        border-radius: 14px;
                        padding: 14px;
                        box-shadow: none;
                    }

                    .order-section-title {
                        margin: 0 0 10px;
                        color: #111827;
                        font-size: 14px;
                        font-weight: 700;
                        text-transform: none;
                        letter-spacing: 0.2px;
                    }

                    .order-assignment-card {
                        border: 0;
                        border-bottom: 1px solid #edf2f7;
                        border-radius: 0;
                        background: transparent;
                        padding: 0 0 14px;
                        margin-bottom: 14px;
                    }

                    .order-assignment-head {
                        display: flex;
                        justify-content: space-between;
                        gap: 12px;
                        align-items: flex-start;
                        margin-bottom: 12px;
                    }

                    .order-assignment-head strong {
                        display: block;
                        font-size: 14px;
                        color: #0f172a;
                        line-height: 1.25;
                    }

                    .order-assignment-head span {
                        display: block;
                        margin-top: 4px;
                        font-size: 12px;
                        color: var(--oi-muted);
                        line-height: 1.4;
                    }

                    .order-assignment-grid {
                        display: grid;
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                        gap: 12px;
                    }

                    .order-assignment-grid .form-group {
                        margin-bottom: 0;
                    }

                    .order-select-note {
                        display: block;
                        margin-top: 6px;
                        font-size: 11px;
                        color: var(--oi-muted);
                        line-height: 1.4;
                    }

                    .order-item-row {
                        display: grid;
                        grid-template-columns: minmax(0, 1fr) 150px;
                        gap: 10px;
                        margin-bottom: 10px;
                        padding: 0 0 12px;
                        border-radius: 0;
                        background: transparent;
                        border: 0;
                        border-bottom: 1px solid #edf2f7;
                    }

                    .order-item-row .form-group {
                        margin-bottom: 0;
                    }

                    .order-product-thumb {
                        width: 92px;
                        height: 92px;
                        border-radius: 12px;
                        overflow: hidden;
                        border: 0;
                        background: #f8fafc;
                        margin-bottom: 8px;
                    }

                    .order-product-thumb img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        display: block;
                    }

                    .order-help-text {
                        display: block;
                        margin-top: 6px;
                        font-size: 11px;
                        color: var(--oi-muted);
                        line-height: 1.4;
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
                        border: 1px solid #d8e0ea;
                        box-shadow: none;
                        min-height: 40px;
                        background: #ffffff;
                        color: #0f172a;
                    }

                    .order-info-ui .inside-shadow:focus,
                    .order-info-ui .form-control:focus {
                        border-color: #7aa7ff;
                        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
                    }

                    .order-summary-image {
                        width: 100%;
                        max-width: 230px;
                        height: 200px;
                        object-fit: cover;
                        border-radius: 14px;
                        border: 0;
                        margin-bottom: 14px;
                        background: #f3f4f6;
                    }

                    .order-collapsible {
                        border: 0;
                        border-radius: 0;
                        background: transparent;
                        margin-bottom: 0;
                        overflow: hidden;
                    }

                    .order-collapsible.order-collapsible-edit {
                        background: transparent;
                    }

                    .order-collapsible.order-collapsible-summary {
                        background: transparent;
                    }

                    .order-collapse-toggle {
                        width: 100%;
                        border: 0;
                        border-bottom: 1px solid #edf2f7;
                        background: transparent;
                        color: #1f2937;
                        text-align: left;
                        padding: 10px 0;
                        font-size: 12px;
                        font-weight: 700;
                        text-transform: none;
                        letter-spacing: 0;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        cursor: pointer;
                    }

                    .order-collapsible.order-collapsible-edit .order-collapse-toggle {
                        background: transparent;
                        color: #1f2937;
                    }

                    .order-collapsible.order-collapsible-summary .order-collapse-toggle {
                        background: transparent;
                        color: #1f2937;
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
                        padding: 0;
                        transition: max-height 0.28s ease, opacity 0.2s ease, padding 0.22s ease;
                    }

                    .order-collapsible.is-open .order-collapse-body {
                        max-height: 4000px;
                        opacity: 1;
                        padding: 12px 0 0;
                    }

                    .order-action-row {
                        grid-column: 1 / -1;
                        background: transparent;
                        border: 0;
                        border-radius: 0;
                        padding: 10px 0 0;
                        margin-top: 8px;
                        box-shadow: none;
                        width: 100%;
                        display: flex;
                        flex-wrap: wrap;
                        gap: 8px;
                        align-items: center;
                    }

                    .order-action-row .btn {
                        margin: 0;
                        border-radius: 10px;
                        min-width: 104px;
                        text-align: center;
                        box-shadow: none;
                        transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease;
                    }

                    .order-action-row .btn:hover {
                        transform: none;
                        box-shadow: none;
                    }

                    .order-tabs {
                        margin-top: 0;
                    }

                    .order-tab-nav {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 8px;
                        margin-bottom: 12px;
                        padding-bottom: 12px;
                        border-bottom: 1px solid #eef2f7;
                    }

                    .order-tab-button {
                        border: 1px solid #e2e8f0;
                        background: #fff;
                        color: #475569;
                        border-radius: 999px;
                        padding: 9px 14px;
                        font-size: 12px;
                        font-weight: 700;
                        cursor: pointer;
                        transition: all 0.16s ease;
                    }

                    .order-tab-button:hover {
                        border-color: #cbd5e1;
                        color: #0f172a;
                    }

                    .order-tab-button.is-active {
                        background: #0f172a;
                        border-color: #0f172a;
                        color: #fff;
                    }

                    .order-tab-panel {
                        display: none;
                        background: #fff;
                        border: 1px solid #eef2f7;
                        border-radius: 14px;
                        padding: 16px;
                        margin-bottom: 12px;
                        box-shadow: none;
                    }

                    .order-tab-panel.is-active {
                        display: block !important;
                    }

                    .order-tab-panel-title {
                        margin: 0 0 12px;
                        font-size: 14px;
                        font-weight: 700;
                        color: #0f172a;
                    }

                    .order-overview-grid {
                        display: grid;
                        grid-template-columns: repeat(4, minmax(0, 1fr));
                        gap: 10px;
                    }

                    .order-overview-card {
                        border: 1px solid #eef2f7;
                        border-radius: 12px;
                        padding: 12px;
                        background: #f8fafc;
                    }

                    .order-overview-card-label {
                        margin: 0 0 6px;
                        font-size: 10px;
                        text-transform: uppercase;
                        letter-spacing: 0.08em;
                        color: #94a3b8;
                        font-weight: 700;
                    }

                    .order-overview-card-value {
                        margin: 0;
                        font-size: 14px;
                        font-weight: 700;
                        color: #0f172a;
                    }

                    .order-logistics-grid {
                        display: grid;
                        grid-template-columns: minmax(0, 1fr) 220px;
                        gap: 14px;
                        align-items: start;
                    }

                    .order-qr-card {
                        border: 1px solid #eef2f7;
                        border-radius: 14px;
                        background: #fbfdff;
                        padding: 14px;
                        text-align: center;
                    }

                    .order-qr-card img {
                        display: block;
                        width: 100%;
                        max-width: 180px;
                        margin: 0 auto 10px;
                        border-radius: 12px;
                    }

                    .order-qr-note {
                        margin: 0;
                        font-size: 12px;
                        color: var(--oi-muted);
                        line-height: 1.45;
                    }

                    .order-sticky-actions {
                        position: fixed;
                        bottom: 10px;
                        z-index: 9999;
                        margin-top: 0;
                        background: #fff;
                        border: 1px solid #e5edf5;
                        border-radius: 16px;
                        padding: 12px;
                        backdrop-filter: none;
                        box-shadow: none;
                        float: left;
                        width: 100%;
                    }

                    .order-sticky-actions-inner {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 8px;
                        align-items: center;
                        justify-content: space-between;
                    }

                    .order-sticky-actions-title {
                        margin: 0;
                        font-size: 12px;
                        font-weight: 700;
                        color: #475569;
                        float: left;
                        width: 100%;
                    }

                    .order-sticky-actions-buttons {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 8px;
                    }

                    .order-sticky-actions-buttons .btn {
                        border-radius: 10px;
                        min-width: 108px;
                        margin: 0;
                    }

                    .select2-container {
                        width: 100% !important;
                    }

                    .select2-container--default .select2-selection--single {
                        min-height: 40px;
                        border: 1px solid #d8e0ea;
                        border-radius: 8px;
                        background: #fff;
                    }

                    .select2-container--default .select2-selection--single .select2-selection__rendered {
                        line-height: 38px;
                        color: #0f172a;
                        padding-left: 12px;
                        padding-right: 34px;
                    }

                    .select2-container--default .select2-selection--single .select2-selection__arrow {
                        height: 38px;
                        right: 6px;
                    }

                    .select2-dropdown {
                        border-color: #d8e0ea;
                    }

                    @media (max-width: 991px) {
                        .order-item-row {
                            grid-template-columns: 1fr;
                        }

                        .order-customer-grid {
                            grid-template-columns: 1fr;
                        }

                        .order-info-ui .box-1 {
                            grid-template-columns: 1fr;
                        }

                        .order-info-ui .box-2 {
                            padding: 10px;
                        }

                        .order-summary-sticky {
                            position: static;
                        }

                        .order-action-row .btn {
                            width: 100%;
                        }

                        .order-assignment-grid {
                            grid-template-columns: 1fr;
                        }

                        .order-overview-grid,
                        .order-logistics-grid {
                            grid-template-columns: 1fr;
                        }

                        .order-sticky-actions-inner {
                            justify-content: flex-start;
                        }

                        .order-sticky-actions-buttons .btn {
                            width: 100%;
                        }
                    }
                </style>

                <div class="order-info-ui" data-order-id="<?php echo $id ?>">
                    <p class="order-info-subtitle">Use tabs to review and update order details section by section.</p>

                    <div class="form-box order-info-shell">
                        <form id="orderData">
                            <input type="hidden" name="type" value="order">
                            <input type="hidden" name="id" value="<?php echo $id ?>">

                            <div class="order-tabs" data-order-tabs>
                                <div class="order-tab-nav" role="tablist" aria-label="Order sections">
                                    <button type="button" class="order-tab-button is-active" data-order-tab="overview">Overview</button>
                                    <button type="button" class="order-tab-button" data-order-tab="product">Product info</button>
                                    <button type="button" class="order-tab-button" data-order-tab="customer">Customer info</button>
                                    <button type="button" class="order-tab-button" data-order-tab="vendor">Vendor info</button>
                                    <button type="button" class="order-tab-button" data-order-tab="logistics">Logistics</button>
                                    <button type="button" class="order-tab-button" data-order-tab="other">Other</button>
                                </div>

                                <div class="order-tab-panels">
                                    <section class="order-tab-panel is-active" data-order-panel="overview">
                                        <h3 class="order-tab-panel-title">Overview</h3>
                                        <div class="order-overview-grid">
                                            <div class="order-overview-card">
                                                <p class="order-overview-card-label">Order</p>
                                                <p class="order-overview-card-value">#<?php echo $id ?></p>
                                            </div>
                                            <div class="order-overview-card">
                                                <p class="order-overview-card-label">Status</p>
                                                <p class="order-overview-card-value"><?php echo $orderStatusText ?></p>
                                            </div>
                                            <div class="order-overview-card">
                                                <p class="order-overview-card-label">Customer</p>
                                                <p class="order-overview-card-value"><?php echo htmlspecialchars($profile_display_name) ?></p>
                                            </div>
                                            <div class="order-overview-card">
                                                <p class="order-overview-card-label">Created</p>
                                                <p class="order-overview-card-value"><?php echo $order_generate ?></p>
                                            </div>
                                        </div>
                                        <div style="margin-top:14px" class="order-meta-line">
                                            <span class="order-chip order-chip-accent">Order ID: <?php echo $id ?></span>
                                            <span class="order-chip <?php echo $orderStatusChipClass; ?>">Status: <?php echo $orderStatusText ?></span>
                                            <span class="order-chip">Payment: <?php echo ucwords($order_payment_mode) ?></span>
                                            <span class="order-chip">City: <?php echo htmlspecialchars($city_name) ?></span>
                                        </div>
                                    </section>

                                    <section class="order-tab-panel" data-order-panel="product">
                                        <h3 class="order-tab-panel-title">Product info</h3>
                                        <?php
                                        $productData = array_filter(array_map('trim', explode(',', (string)$order_product_id)), 'strlen');
                                        $productQtyData = array_map('trim', explode(',', (string)$order_product_qty));

                                        if (!$isApp32 && empty($productData)) { ?>
                                            <div class="order-assignment-card">
                                                <div class="order-assignment-head">
                                                    <div>
                                                        <strong>No product assigned</strong>
                                                        <span>This order does not have a product linked to it yet.</span>
                                                    </div>
                                                    <span class="order-chip order-chip-status-default">Not assigned</span>
                                                </div>
                                            </div>
                                        <?php } elseif ($isApp32 && empty($productData)) { ?>
                                            <div class="order-assignment-card" id="orderAssignmentPanel">
                                                <div class="order-assignment-head">
                                                    <div>
                                                        <strong>Order needs assignment</strong>
                                                        <span>Choose a product and vendor before processing this order.</span>
                                                    </div>
                                                    <span class="order-chip order-chip-status-warning">Unassigned</span>
                                                </div>

                                                <div class="order-assignment-grid">
                                                    <div class="form-group">
                                                        <label>Product</label>
                                                        <select name="assigned_product_id" class="form-control inside-shadow">
                                                            <option value="">Select product</option>
                                                            <?php for ($p = 0; $p < sizeof($productListData); $p++) { ?>
                                                                <option value="<?php echo htmlspecialchars($productListData[$p]['product_id']); ?>"><?php echo htmlspecialchars($productListData[$p]['product_title']); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <small class="order-select-note">No product is assigned to this order yet.</small>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Vendor</label>
                                                        <select name="assigned_vendor" class="form-control inside-shadow">
                                                            <option value="">Select vendor</option>
                                                            <?php if (!empty($vendorListData)) { ?>
                                                                <?php foreach ($vendorListData as $vendorData) { ?>
                                                                    <option value="<?php echo htmlspecialchars($vendorData['user_type_code']); ?>"><?php echo htmlspecialchars($vendorData['user_type_name']); ?></option>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <option value="">No vendor found</option>
                                                            <?php } ?>
                                                        </select>
                                                        <small class="order-select-note">Pick the vendor responsible for fulfillment.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } else {
                                            for ($i = 0; $i < sizeof($productData); $i++) {
                                                echo "<input type='hidden' name='totalProducts' value='" . sizeof($productData) . "'>";

                                                if ($checkProduct = mysqli_query($connection, "SELECT product_id, product_image, product_title, inventory_currency_symbol FROM isw_product INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE product_id='{$productData[$i]}' AND inventory_type='product' AND product_site_id=" . APP_SITE_ID . " AND inventory_site_id=" . APP_SITE_ID)) {
                                                    if (mysqli_num_rows($checkProduct) != 0) {
                                                        extract(mysqli_fetch_assoc($checkProduct)); ?>

                                                        <div class="order-item-row">
                                                            <div class="form-group">
                                                                <label>Product name (Cart item <?php echo $i + 1 ?>)</label>
                                                                <?php if (trim((string)$product_image) != '') { ?>
                                                                    <div class="order-product-thumb"><img src="<?php echo htmlspecialchars($product_image, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($product_title, ENT_QUOTES); ?>"></div>
                                                                <?php } ?>
                                                                <input type="hidden" name="product_id_<?php echo $i ?>" value="<?php echo $productData[$i] ?>">
                                                                <input type="text" class="form-control inside-shadow" value="<?php echo htmlspecialchars($product_title, ENT_QUOTES) ?>" readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Qty</label>
                                                                <input type="text" name="qty_<?php echo $i ?>" class="form-control inside-shadow" value="<?php echo isset($productQtyData[$i]) ? str_replace(' ', '', $productQtyData[$i]) : '' ?>">
                                                            </div>
                                                        </div>

                                                    <?php } else { ?>

                                                        <div class="order-item-row">
                                                            <div class="form-group">
                                                                <label>Product name (Cart item <?php echo $i + 1 ?>)</label>
                                                                <?php
                                                                $fallbackProductTitle = 'Unknown Product';
                                                                for ($j = 0; $j < sizeof($productListData); $j++) {
                                                                    if ((string)$productListData[$j]['product_id'] == (string)$productData[$i]) {
                                                                        $fallbackProductTitle = $productListData[$j]['product_title'];
                                                                        break;
                                                                    }
                                                                }
                                                                ?>
                                                                <input type="hidden" name="product_id_<?php echo $i ?>" value="<?php echo $productData[$i] ?>">
                                                                <input type="text" class="form-control inside-shadow" value="<?php echo htmlspecialchars($fallbackProductTitle, ENT_QUOTES) ?>" readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Qty</label>
                                                                <input type="text" name="qty_<?php echo $i ?>" class="form-control inside-shadow">
                                                            </div>
                                                        </div>

                                        <?php }
                                                }
                                            }
                                        } ?>
                                    </section>

                                    <section class="order-tab-panel" data-order-panel="customer">
                                        <h3 class="order-tab-panel-title">Customer info</h3>
                                        <div class="order-customer-grid">
                                            <div class="form-group"><label>First Name</label><input type="text" name="first_name" class="form-control inside-shadow" value="<?php echo $profile_first_name ?>"></div>
                                            <div class="form-group"><label>Last Name</label><input type="text" name="last_name" class="form-control inside-shadow" value="<?php echo $profile_last_name ?>"></div>
                                        </div>
                                        <div class="form-group"><label>Email</label><input type="text" name="email" class="form-control inside-shadow" value="<?php echo $user_email ?>"></div>
                                        <div class="form-group"><label>Mobile No.</label><input type="text" name="mobile" class="form-control inside-shadow" value="<?php echo $user_mobile ?>"></div>
                                        <div class="form-group"><label>House No.</label><input type="text" name="address_house_no" class="form-control inside-shadow" value="<?php echo $address_house_no ?>"></div>
                                        <div class="form-group"><label>Address</label><input type="text" name="address_area" class="form-control inside-shadow" value="<?php echo $address_area ?>"></div>
                                        <div class="form-group"><label>Landmark</label><input type="text" class="form-control inside-shadow" name="address_landmark" value="<?php echo $address_landmark ?>"></div>
                                        <div class="form-group"><label>City</label><input type="text" class="form-control inside-shadow" name="city" value="<?php echo $city_name ?>"></div>
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
                                        <div class="form-group"><label>Pin Code</label><input type="text" class="form-control inside-shadow" name="address_pin_code" value="<?php echo $address_pin_code ?>"></div>
                                        <div class="form-group">
                                            <label>Address Type</label>
                                            <select name="address_type" class="form-control inside-shadow">
                                                <option value="">Select address type</option>
                                                <option value="home" <?php echo (($address_type == 'home') ? 'selected' : '') ?>>Home</option>
                                                <option value="work" <?php echo (($address_type == 'work') ? 'selected' : '') ?>>Work</option>
                                                <option value="other" <?php echo (($address_type == 'other') ? 'selected' : '') ?>>Other</option>
                                            </select>
                                        </div>
                                    </section>

                                    <section class="order-tab-panel" data-order-panel="vendor">
                                        <h3 class="order-tab-panel-title">Vendor info</h3>
                                        <?php if ($isApp32) { ?>
                                            <div class="order-assignment-card">
                                                <div class="order-assignment-head">
                                                    <div>
                                                        <strong>Vendor assignment</strong>
                                                        <span>Choose the vendor responsible for this order.</span>
                                                    </div>
                                                    <span class="order-chip order-chip-status-warning">App 32</span>
                                                </div>
                                                <div class="order-assignment-grid">
                                                    <div class="form-group">
                                                        <label>Vendor</label>
                                                        <select name="assigned_vendor" class="form-control inside-shadow">
                                                            <option value="">Select vendor</option>
                                                            <?php if (!empty($vendorListData)) { ?>
                                                                <?php foreach ($vendorListData as $vendorData) { ?>
                                                                    <option value="<?php echo htmlspecialchars($vendorData['user_type_code']); ?>"><?php echo htmlspecialchars($vendorData['user_type_name']); ?></option>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <option value="">No vendor found</option>
                                                            <?php } ?>
                                                        </select>
                                                        <small class="order-select-note">Select the current fulfillment partner.</small>
                                                    </div>
                                                    <div class="order-assignment-card" style="margin:0; padding:12px; border-bottom:0; background:#fbfdff;">
                                                        <strong>Assignment note</strong>
                                                        <span>The vendor selection here is kept separate from product assignment for clarity.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="order-assignment-card">
                                                <div class="order-assignment-head">
                                                    <div>
                                                        <strong>Vendor information not required</strong>
                                                        <span>This app does not use vendor assignment on this order screen.</span>
                                                    </div>
                                                    <span class="order-chip order-chip-status-default">Not used</span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </section>

                                    <section class="order-tab-panel" data-order-panel="logistics">
                                        <h3 class="order-tab-panel-title">Logistics</h3>
                                        <div class="order-logistics-grid">
                                            <div>
                                                <div class="order-assignment-card">
                                                    <div class="order-assignment-head">
                                                        <div>
                                                            <strong>Delivery summary</strong>
                                                            <span>Order #<?php echo $id ?> for <?php echo htmlspecialchars($profile_display_name) ?></span>
                                                        </div>
                                                        <span class="order-chip <?php echo $orderStatusChipClass; ?>"><?php echo $orderStatusText ?></span>
                                                    </div>
                                                    <div class="order-meta-line">
                                                        <span class="order-chip">Delivery: <?php echo (($order_delivery_charges != '') ? $order_delivery_charges : '0') ?></span>
                                                        <span class="order-chip">Total: <?php echo (($order_total_price != '') ? $order_total_price : '0') ?></span>
                                                        <span class="order-chip">City: <?php echo htmlspecialchars($city_name) ?></span>
                                                    </div>
                                                </div>

                                                <div class="order-assignment-card">
                                                    <div class="order-assignment-head">
                                                        <div>
                                                            <strong>Receiver details</strong>
                                                            <span><?php echo $address_name ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="order-customer-grid">
                                                        <div class="form-group"><label>Contact no.</label><input type="text" class="form-control inside-shadow" value="<?php echo $address_mobile ?>" readonly></div>
                                                        <div class="form-group"><label>Address type</label><input type="text" class="form-control inside-shadow" value="<?php echo ucwords($address_type) ?>" readonly></div>
                                                    </div>
                                                    <div class="form-group"><label>Address</label><input type="text" class="form-control inside-shadow" value="<?php echo $address_house_no . ' ' . $address_area . ' ' . $address_landmark . ' ' . $city_name . ' ' . $address_pin_code ?>" readonly></div>
                                                    <div class="form-group"><label>Print</label><a href="#" class="btn btn-blue order-print-logistics">Print logistics label</a></div>
                                                </div>
                                            </div>

                                            <div class="order-qr-card">
                                                <?php if (trim((string)$orderQrUrl) != '') { ?>
                                                    <img src="<?php echo htmlspecialchars($orderQrUrl, ENT_QUOTES); ?>" alt="Order QR code">
                                                <?php } else { ?>
                                                    <div class="order-chip order-chip-status-default" style="display:block; margin-bottom:10px;">QR unavailable</div>
                                                <?php } ?>
                                                <p class="order-qr-note">Scan to read order and delivery details for labeling and dispatch.</p>
                                            </div>
                                        </div>
                                    </section>

                                    <section class="order-tab-panel" data-order-panel="other">
                                        <h3 class="order-tab-panel-title">Other</h3>
                                        <?php
                                        if ($orderMeta = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id='{$id}' AND order_site_id='" . APP_SITE_ID . "'")) {
                                            if (mysqli_num_rows($orderMeta) != 0) {
                                                while ($orderMetaData = mysqli_fetch_assoc($orderMeta)) {
                                                    extract($orderMetaData);

                                                    if ($order_meta_key == 'message') {
                                                        echo "<div class='form-group'><label>" . ucwords($order_meta_key) . "</label><textarea class='form-control inside-shadow' style='min-height: 200px' readonly>{$order_meta_value}</textarea></div>";
                                                    } else {
                                                        echo "<div class='form-group'><label>" . ucwords($order_meta_key) . "</label><input type='text' class='form-control inside-shadow' value='{$order_meta_value}' readonly></div>";
                                                    }
                                                }
                                            } else {
                                                echo "<div class='order-assignment-card'><strong>No extra details</strong><span>There are no order meta fields available for this order.</span></div>";
                                            }
                                        }
                                        ?>
                                    </section>
                                </div>
                            </div>

                            <div class="order-sticky-actions" data-order-actions>
                                <div class="order-sticky-actions-inner">
                                    <p class="order-sticky-actions-title">Quick actions</p>
                                    <div class="order-sticky-actions-buttons">
                                        <a href="#" id="orderUpdateData" class="btn btn-blue">Update Order</a>
                                        <a href="#" id="printLogisticsBtn" class="btn btn-blue">Print Logistics</a>
                                        <a href="#" id="orderConfirmBtn" class="btn btn-green">Confirm</a>
                                        <a href="#" id="orderWaitingBtn" class="btn btn-yellow">Waiting</a>
                                        <a href="#" id="orderCancelBtn" class="btn btn-red">Cancel</a>
                                        <a href="#" id="orderReturnBtn" class="btn btn-red">Return</a>
                                        <a href="#" id="orderDeliverBtn" class="btn btn-green">Delivered</a>
                                    </div>
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

                        var tabButtons = root.querySelectorAll('[data-order-tab]');
                        var tabPanels = root.querySelectorAll('[data-order-panel]');
                        var printLogisticsButtons = root.querySelectorAll('#printLogisticsBtn, .order-print-logistics');

                        function activateTab(tabName) {
                            for (var i = 0; i < tabButtons.length; i++) {
                                tabButtons[i].classList.toggle('is-active', tabButtons[i].getAttribute('data-order-tab') === tabName);
                            }

                            for (var j = 0; j < tabPanels.length; j++) {
                                tabPanels[j].classList.toggle('is-active', tabPanels[j].getAttribute('data-order-panel') === tabName);
                            }
                        }

                        for (var i = 0; i < tabButtons.length; i++) {
                            tabButtons[i].addEventListener('click', function(event) {
                                event.preventDefault();
                                activateTab(this.getAttribute('data-order-tab'));
                            });
                        }

                        function ensureSelect2() {
                            if (typeof window.jQuery === 'undefined') {
                                return;
                            }

                            function initSelect2() {
                                if (!window.jQuery.fn || !window.jQuery.fn.select2) {
                                    return;
                                }

                                window.jQuery(root).find('select').each(function() {
                                    var $select = window.jQuery(this);

                                    if ($select.hasClass('select2-hidden-accessible')) {
                                        return;
                                    }

                                    $select.select2({
                                        width: '100%',
                                        placeholder: $select.find('option:first').text() || 'Select option'
                                    });
                                });
                            }

                            if (window.jQuery.fn && window.jQuery.fn.select2) {
                                initSelect2();
                                return;
                            }

                            var select2Css = '<?php echo SITE_URL . '/' . PLUGIN_LIBRARY; ?>select2/css/select2.min.css';
                            var select2Js = '<?php echo SITE_URL . '/' . PLUGIN_LIBRARY; ?>select2/js/select2.min.js';

                            if (!document.querySelector('link[data-order-select2]')) {
                                var link = document.createElement('link');
                                link.rel = 'stylesheet';
                                link.href = select2Css;
                                link.setAttribute('data-order-select2', 'true');
                                document.head.appendChild(link);
                            }

                            if (!document.querySelector('script[data-order-select2]')) {
                                var script = document.createElement('script');
                                script.src = select2Js;
                                script.async = true;
                                script.setAttribute('data-order-select2', 'true');
                                script.onload = initSelect2;
                                document.body.appendChild(script);
                            }
                        }

                        ensureSelect2();
                        activateTab('overview');

                        for (var k = 0; k < printLogisticsButtons.length; k++) {
                            printLogisticsButtons[k].addEventListener('click', function(event) {
                                event.preventDefault();
                                activateTab('logistics');
                                window.print();
                            });
                        }
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