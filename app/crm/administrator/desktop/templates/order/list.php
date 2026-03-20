<?php

$statusParam = isset($_GET['status']) ? $_GET['status'] : 'all';
$typeParam = isset($_GET['type']) ? $_GET['type'] : 'list';

$orderStatus = mysqli_real_escape_string($connection, $statusParam);
$orderStatus = (($orderStatus == 'all') ? "" : "AND order_status='{$orderStatus}'");

?>
<style>
.modern-order-list {
    --ol-bg: #ffffff;
    --ol-border: #e5e7eb;
    --ol-soft: #f8fafc;
    --ol-text: #111827;
    --ol-subtext: #6b7280;
    --ol-accent: #1d4ed8;
    margin-top: 8px;
}

.modern-order-list .panel {
    background: var(--ol-bg);
    border: 1px solid var(--ol-border);
    border-radius: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    padding: 16px;
}

.modern-order-list h2 {
    margin: 0 0 12px;
    font-size: 24px;
    font-weight: 700;
    color: var(--ol-text);
    letter-spacing: -0.02em;
}

.modern-order-list .options-bar {
    margin: 0 0 12px;
    padding: 12px;
    background: var(--ol-soft);
    border: 1px solid var(--ol-border);
    border-radius: 10px;
}

.modern-order-list .options-bar form {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.modern-order-list .form-control {
    min-width: 220px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #fff;
    color: #111827;
    padding: 8px 10px;
}

.modern-order-list .btn {
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #fff;
    color: #1f2937;
    padding: 8px 12px;
    font-weight: 600;
}

.modern-order-list .btn:hover {
    border-color: #94a3b8;
    background: #f8fafc;
}

.modern-order-list #table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border: 1px solid var(--ol-border);
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
}

.modern-order-list #table thead th {
    background: #f9fafb;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 11px;
    font-weight: 700;
    border-bottom: 1px solid var(--ol-border);
    padding: 12px 14px;
}

.modern-order-list #table tbody td {
    padding: 14px;
    border-bottom: 1px solid #f1f5f9;
    color: #1f2937;
    vertical-align: middle;
}

.modern-order-list #table tbody tr:last-child td {
    border-bottom: 0;
}

.modern-order-list #table tbody tr:hover td {
    background: #fbfdff;
}

.order-cell {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.order-badge {
    width: 58px;
    height: 58px;
    border-radius: 8px;
    overflow: hidden;
    background: #eef3f7;
    flex-shrink: 0;
    border: 1px solid #d7e0e8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: #4c5f71;
}

.order-meta {
    min-width: 0;
}

.order-meta .hover {
    display: block !important;
    min-height: 18px;
    margin-top: 8px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.15s ease;
}

#table tbody tr:hover .order-meta .hover,
#table tbody tr:focus-within .order-meta .hover {
    opacity: 1;
    visibility: visible;
}

.order-meta p {
    margin: 0;
}

.order-title-link {
    font-size: 13px;
    font-weight: 600;
    line-height: 1.35;
    display: inline-block;
    max-width: 100%;
    word-break: break-word;
    color: #0f172a;
    text-decoration: none;
}

.order-title-link:hover {
    color: var(--ol-accent);
}

.order-ids {
    margin-top: 4px;
    color: #637282;
    font-size: 12px;
}

.order-tags {
    margin-top: 6px;
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.order-tag {
    background: #f2f6fa;
    border: 1px solid #d9e2ea;
    color: #41505f;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 999px;
    line-height: 1.25;
}

.order-tag.status-delivered,
.order-tag.status-confirm {
    background: #e9f9ef;
    border-color: #b8e7c6;
    color: #1e7a37;
}

.order-tag.status-pending,
.order-tag.status-waiting {
    background: #fff6e8;
    border-color: #f0d7ad;
    color: #8c5a0a;
}

.order-tag.status-reject,
.order-tag.status-cancel,
.order-tag.status-rto {
    background: #ffeff2;
    border-color: #f4c8d0;
    color: #9e2f46;
}

.amount-main {
    font-weight: 700;
    color: #0f172a;
}

.amount-sub {
    margin-top: 2px;
    color: #6d7b88;
    font-size: 12px;
}

.order-view-btn {
    font-size: 12px;
    font-weight: 600;
    border-radius: 8px;
    padding: 7px 10px;
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
    text-decoration: none;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.order-view-btn:hover {
    background: #dbeafe;
    color: #1e3a8a;
}

.empty-order-state {
    text-align: center;
    color: var(--ol-subtext);
    font-size: 13px;
    padding: 28px 16px;
}

@media (max-width: 900px) {
    .modern-order-list .panel {
        padding: 12px;
    }

    .modern-order-list .form-control {
        min-width: 100%;
    }
}
</style>
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
                    <input type="submit" class="btn" value="Filter">
                </form>
            </div>
            <div class="right-box">

            </div>
        </div>
        <table id="table">
            <thead>
                <tr>
                    <th style="width: 480px">Order</th>
                    <th style="width: 130px">Amount</th>
                    <th style="width: 120px">Payment</th>
                    <th style="width: 120px">Status</th>
                    <th style="width: 140px">Date</th>
                    <th style="width: 110px"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasOrders = false;

                if ($dataList = mysqli_query($connection, "SELECT profile_user_id, profile_display_name, profile_mobile, order_id, order_product_id, order_product_qty, inventory_currency_symbol, order_product_price, order_delivery_charges, order_currency, order_total_price, order_payment_mode, order_generate, order_status, address_mobile FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "address ON address_id=order_address_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=order_user_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=order_product_id INNER JOIN " . TABLE_PREFIX . "product ON order_product_id=product_id WHERE order_site_id=" . APP_SITE_ID . " AND order_product_id!='' AND product_type='product' AND inventory_site_id=" . APP_SITE_ID . " {$orderStatus} ORDER BY order_id DESC LIMIT 0, 1000")) {

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

                ?>
                <tr>
                    <td>
                        <div class="order-cell">
                            <div class="order-badge"><?php echo htmlspecialchars($initials); ?></div>
                            <div class="order-meta">
                                <p>
                                    <a class="order-title-link"
                                        href="order/?type=view&id=<?php echo base64_encode($order_id); ?>"><?php echo 'OD' . $order_id; ?>
                                        <span class="order-ids"> |
                                            <?php echo htmlspecialchars($profile_display_name); ?></span>
                                    </a>
                                </p>
                                <div class="order-tags">
                                    <span class="order-tag"><?php echo htmlspecialchars($address_mobile); ?></span>
                                    <span
                                        class="order-tag <?php echo $statusClass; ?>"><?php echo $order_status; ?></span>
                                    <span class="order-tag"><?php echo htmlspecialchars($paymentLabel); ?></span>
                                </div>
                                <div class="hover">
                                    <a href="order/?type=view&id=<?php echo base64_encode($order_id); ?>"><i
                                            class="fas fa-eye"></i>
                                        View</a>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="amount-main"><?php echo $orderAmount; ?></div>
                    </td>
                    <td><?php echo htmlspecialchars($paymentLabel); ?></td>
                    <td><span class="order-tag <?php echo $statusClass; ?>"><?php echo $order_status; ?></span></td>
                    <td data-order="<?php echo strtotime($order_generate); ?>">
                        <?php echo date('d/m/Y h:i A', strtotime($order_generate)); ?></td>
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