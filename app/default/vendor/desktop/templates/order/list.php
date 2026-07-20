<?php

if (VENDOR_ID == 0) {
    require_once $dashboardDirectory . '403.php';
}

$statusParam = isset($_GET['status']) ? $_GET['status'] : 'all';
$typeParam = isset($_GET['type']) ? $_GET['type'] : 'list';
$sortParam = isset($_GET['sort']) ? strtolower(trim($_GET['sort'])) : 'recent';

$allowedSorts = array('recent', 'oldest');
if (!in_array($sortParam, $allowedSorts, true)) {
    $sortParam = 'recent';
}

$sortDirection = ($sortParam === 'oldest') ? 'ASC' : 'DESC';
$orderByClause = "ORDER BY order_generate {$sortDirection}, order_id {$sortDirection}";
$siteId = "IN (" . APP_SITE_ID . ", " . SITE_ID . ")";
$isApp32 = ((int)SITE_APP_ID === 32);

$orderStatus = mysqli_real_escape_string($connection, $statusParam);
$orderStatus = (($orderStatus == 'all') ? "" : "AND order_status='{$orderStatus}'");

?>

<div class="modern-order-list">
    <h2>All Orders</h2>
    <div class="panel">
        <div class="options-bar">
            <div class="left-box">
                <form method="GET">
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($typeParam); ?>">
                    <select class="form-control" name="status" id="changeStatus">
                        <option value="all" <?php echo (($statusParam == 'all') ? 'selected' : '') ?>>All
                            <?php echo $postType ?></option>
                        <option value="0" <?php echo (($statusParam == '0') ? 'selected' : '') ?>>Pending
                            <?php echo $postType ?></option>
                        <option value="1" <?php echo (($statusParam == '1') ? 'selected' : '') ?>>Confirm
                            <?php echo $postType ?></option>
                        <option value="2" <?php echo (($statusParam == '2') ? 'selected' : '') ?>>Reject
                            <?php echo $postType ?></option>
                        <option value="3" <?php echo (($statusParam == '3') ? 'selected' : '') ?>>Cancel
                            <?php echo $postType ?></option>
                        <option value="4" <?php echo (($statusParam == '4') ? 'selected' : '') ?>>Waiting
                            <?php echo $postType ?></option>
                        <option value="5" <?php echo (($statusParam == '5') ? 'selected' : '') ?>>RTO
                            <?php echo $postType ?>
                        </option>
                        <option value="6" <?php echo (($statusParam == '6') ? 'selected' : '') ?>>Delivered
                            <?php echo $postType ?></option>
                    </select>
                    <select class="form-control" name="sort" id="changeSort">
                        <option value="recent" <?php echo (($sortParam == 'recent') ? 'selected' : '') ?>>Newest Received First</option>
                        <option value="oldest" <?php echo (($sortParam == 'oldest') ? 'selected' : '') ?>>Oldest Received First</option>
                    </select>
                    <input type="submit" class="btn" value="Filter">
                </form>
            </div>
            <div class="right-box">

            </div>
        </div>
        <table id="table" data-default-order-column="4" data-default-order-dir="desc">
            <thead>
                <tr>
                    <th style="width: 480px">Order</th>
                    <th style="width: 130px">Amount</th>
                    <th style="width: 120px">Payment</th>
                    <th style="width: 120px">Status</th>
                    <th style="width: 140px">Received Date</th>
                    <th style="width: 110px"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasOrders = false;

                $query = "SELECT profile_user_id, profile_display_name, profile_mobile, order_id, order_product_id, order_product_qty, inventory_currency_symbol, order_product_price, order_delivery_charges, order_currency, order_total_price, order_payment_mode, order_generate, order_status, address_mobile, product_title, product_image FROM " . TABLE_PREFIX . "order LEFT JOIN " . TABLE_PREFIX . "address ON address_id=order_address_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=order_user_id LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=order_product_id AND inventory_site_id " . $siteId . " LEFT JOIN " . TABLE_PREFIX . "product ON order_product_id=product_id" . ($isApp32 ? "" : " AND product_type='product'") . " WHERE order_vendor_id='" . VENDOR_ID . "' AND order_site_id " . $siteId . " " . ($isApp32 ? "" : "AND order_product_id!='' AND product_type='product' ") . "{$orderStatus} {$orderByClause} LIMIT 0, 1000";

                if ($dataList = mysqli_query($connection, $query)) {

                    if (mysqli_num_rows($dataList) != 0) {
                        while ($data = mysqli_fetch_assoc($dataList)) {
                            $hasOrders = true;

                            extract($data);

                            $statusClass = 'status-pending';
                            switch ($order_status) {

                                case 0:
                                    $order_status = 'Pending';
                                    $statusClass = 'status-pending';
                                    break;

                                case 1:
                                    $order_status = 'Confirm';
                                    $statusClass = 'status-confirm';
                                    break;

                                case 2:
                                    $order_status = 'Reject';
                                    $statusClass = 'status-reject';
                                    break;

                                case 3:
                                    $order_status = 'Cancel';
                                    $statusClass = 'status-cancel';
                                    break;

                                case 4:
                                    $order_status = 'Waiting';
                                    $statusClass = 'status-waiting';
                                    break;

                                case 5:
                                    $order_status = 'RTO';
                                    $statusClass = 'status-rto';
                                    break;

                                case 6:
                                    $order_status = 'Delivered';
                                    $statusClass = 'status-delivered';
                                    break;
                            }

                            $orderAmount = (($order_total_price != '') ? $inventory_currency_symbol : '') . ' ' . $order_total_price;
                            $paymentLabel = ucwords(str_replace('_', ' ', trim($order_payment_mode)));
                            if ($paymentLabel == '') {
                                $paymentLabel = 'N/A';
                            }

                            $initials = 'OD';
                            if (trim($profile_display_name) != '') {
                                $nameParts = preg_split('/\s+/', trim($profile_display_name));
                                $firstInitial = strtoupper(substr($nameParts[0], 0, 1));
                                $secondInitial = (isset($nameParts[1]) && $nameParts[1] != '') ? strtoupper(substr($nameParts[1], 0, 1)) : '';
                                $initials = $firstInitial . $secondInitial;
                            }

                            $productBadgeLabel = 'N/A';
                            $productImageSrc = '';
                            $productNameLabel = 'N/A';
                            $hasProduct = (trim((string)$order_product_id) !== '' && trim((string)$order_product_id) !== '0');
                            if ($hasProduct) {
                                if (trim((string)$product_title) !== '') {
                                    $productNameLabel = trim((string)$product_title);
                                }
                                if (trim((string)$product_image) !== '') {
                                    $productImageSrc = trim((string)$product_image);
                                }
                            }

                ?>
                            <tr>
                                <td>
                                    <div class="order-cell">
                                        <div class="order-badge">
                                            <?php if ($productImageSrc != '') { ?>
                                                <img src="<?php echo htmlspecialchars($productImageSrc); ?>" alt="Product image">
                                            <?php } else { ?>
                                                <span class="order-badge-fallback">N/A</span>
                                            <?php } ?>
                                        </div>
                                        <div class="order-meta">
                                            <p>
                                                <a class="order-title-link"
                                                    href="order/?type=view&id=<?php echo base64_encode($order_id); ?>"><?php echo 'OD' . $order_id; ?>
                                                    <span class="order-ids"> |
                                                        <?php echo htmlspecialchars($profile_display_name); ?></span>
                                                </a>
                                            </p>
                                            <div class="order-product-name">
                                                Product: <?php echo htmlspecialchars($productNameLabel); ?>
                                            </div>
                                            <div class="order-tags">
                                                <span class="order-tag"><?php echo htmlspecialchars($address_mobile); ?></span>
                                                <span
                                                    class="order-tag <?php echo $statusClass; ?>"><?php echo $order_status; ?></span>
                                                <span class="order-tag"><?php echo htmlspecialchars($paymentLabel); ?></span>
                                            </div>
                                            <!-- <div class="hover">
                                                <a href="order/?type=view&id=<?php echo base64_encode($order_id); ?>"><i
                                                        class="fas fa-eye"></i>
                                                    View</a>
                                            </div> -->
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-main"><?php echo $orderAmount; ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($paymentLabel); ?></td>
                                <td><span class="order-tag <?php echo $statusClass; ?>"><?php echo $order_status; ?></span></td>
                                <td data-order="<?php echo (int)strtotime($order_generate); ?>"><?php echo date('d/m/Y h:i A', strtotime($order_generate)); ?></td>
                                <td>
                                    <a href="order/?type=view&id=<?php echo base64_encode($order_id); ?>" class="order-view-btn"><i
                                            class="fas fa-eye"></i> View</a>
                                </td>
                            </tr>
                <?php
                        }
                    }
                }

                if (!$hasOrders) {
                    echo '<tr><td colspan="6"><div class="empty-order-state">No orders found for this filter.</div></td></tr>';
                }

                ?>
            </tbody>
        </table>
    </div>
</div>