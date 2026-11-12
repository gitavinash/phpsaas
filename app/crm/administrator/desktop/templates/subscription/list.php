<?php

$statusParam = isset($_GET['status']) ? trim((string)$_GET['status']) : 'all';
$typeParam = isset($_GET['type']) ? trim((string)$_GET['type']) : 'list';

$orderStatusSql = '';
if ($statusParam !== 'all' && ctype_digit($statusParam)) {
    $escapedStatus = mysqli_real_escape_string($connection, $statusParam);
    $orderStatusSql = "AND order_status='{$escapedStatus}'";
}

?>
<div class="crm-list-panel">
    <h2>All Subscriptions</h2>
    <div class="options-bar">
        <div class="left-box">
            <form method="GET">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($typeParam, ENT_QUOTES, 'UTF-8'); ?>">
                <select class="form-control" name="status" id="changeStatus">
                    <option value="all" <?php echo (($statusParam == 'all') ? 'selected' : ''); ?>>All <?php echo htmlspecialchars($postType, ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="0" <?php echo (($statusParam == '0') ? 'selected' : ''); ?>>Pending <?php echo htmlspecialchars($postType, ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="1" <?php echo (($statusParam == '1') ? 'selected' : ''); ?>>Confirm <?php echo htmlspecialchars($postType, ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="2" <?php echo (($statusParam == '2') ? 'selected' : ''); ?>>Reject <?php echo htmlspecialchars($postType, ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="3" <?php echo (($statusParam == '3') ? 'selected' : ''); ?>>Cancel <?php echo htmlspecialchars($postType, ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="4" <?php echo (($statusParam == '4') ? 'selected' : ''); ?>>Waiting <?php echo htmlspecialchars($postType, ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="5" <?php echo (($statusParam == '5') ? 'selected' : ''); ?>>RTO <?php echo htmlspecialchars($postType, ENT_QUOTES, 'UTF-8'); ?></option>
                    <option value="6" <?php echo (($statusParam == '6') ? 'selected' : ''); ?>>Delivered <?php echo htmlspecialchars($postType, ENT_QUOTES, 'UTF-8'); ?></option>
                </select>
                <input type="submit" class="btn" value="Filter">
            </form>
        </div>
        <div class="right-box"></div>
    </div>

    <div class="crm-dt-scroll">
        <table id="table" data-empty-message="No subscriptions found for this filter.">
            <thead>
                <tr>
                    <th>S No.</th>
                    <th>Order No.</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Amount</th>
                    <th>Payment Mode</th>
                    <th>Status</th>
                    <th>Date and Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php

                if ($dataList = mysqli_query($connection, "SELECT profile_user_id, profile_display_name, profile_mobile, order_id, order_product_id, order_product_qty, inventory_currency_symbol, order_product_price, order_delivery_charges, order_currency, order_total_price, order_payment_mode, order_generate, order_status, address_mobile FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "address ON address_id=order_address_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=order_user_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=order_product_id INNER JOIN " . TABLE_PREFIX . "product ON order_product_id=product_id WHERE order_site_id=" . APP_SITE_ID . " AND order_product_id!='' AND product_type='subscription' AND inventory_site_id=" . APP_SITE_ID . " {$orderStatusSql} ORDER BY order_id DESC LIMIT 0, 1000")) {

                    if (mysqli_num_rows($dataList) != 0) {
                        $i = 1;
                        while ($data = mysqli_fetch_assoc($dataList)) {
                            $statusInt = (int)$data['order_status'];
                            $statusLabel = 'Pending';
                            switch ($statusInt) {
                                case 0:
                                    $statusLabel = 'Pending';
                                    break;
                                case 1:
                                    $statusLabel = 'Confirm';
                                    break;
                                case 2:
                                    $statusLabel = 'Reject';
                                    break;
                                case 3:
                                    $statusLabel = 'Cancel';
                                    break;
                                case 4:
                                    $statusLabel = 'Waiting';
                                    break;
                                case 5:
                                    $statusLabel = 'RTO';
                                    break;
                                case 6:
                                    $statusLabel = 'Delivered';
                                    break;
                            }
                            $amountText = (($data['order_total_price'] != '') ? $data['inventory_currency_symbol'] . ' ' : '') . $data['order_total_price'];
                ?>
                            <tr>
                                <td data-label="S No."><?php echo $i; ?></td>
                                <td data-label="Order No."><?php echo 'OD' . (int)$data['order_id']; ?></td>
                                <td data-label="Name"><?php echo htmlspecialchars((string)$data['profile_display_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-label="Mobile"><?php echo htmlspecialchars((string)$data['address_mobile'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-label="Amount"><?php echo htmlspecialchars($amountText, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-label="Payment Mode"><?php echo htmlspecialchars((string)$data['order_payment_mode'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-label="Status"><?php echo htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-label="Date and Time"><?php echo date('d-m-Y h:i A', strtotime($data['order_generate'])); ?></td>
                                <td data-label="Action">
                                    <a href="subscription/?type=view&id=<?php echo base64_encode($data['order_id']); ?>" class="btn btn-sm btn-blue"><i class="fas fa-eye" aria-hidden="true"></i> View</a>
                                </td>
                            </tr>
                <?php
                            $i++;
                        }
                    }
                }

                ?>
            </tbody>
        </table>
    </div>
</div>
