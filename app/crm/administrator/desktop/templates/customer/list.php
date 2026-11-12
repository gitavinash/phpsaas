<?php

$statusParam = isset($_GET['status']) ? $_GET['status'] : 'all';
$typeParam = isset($_GET['type']) ? $_GET['type'] : 'list';

$orderStatus = mysqli_real_escape_string($connection, $statusParam);
$orderStatus = (($orderStatus == 'all') ? '' : "AND order_status='{$orderStatus}'");

?>
<style>
.modern-customer-list {
    --cl-bg: #ffffff;
    --cl-border: #e5e7eb;
    --cl-soft: #f8fafc;
    --cl-text: #111827;
    --cl-subtext: #6b7280;
    --cl-accent: #1d4ed8;
    margin-top: 8px;
}

.modern-customer-list .panel {
    background: var(--cl-bg);
    border: 1px solid var(--cl-border);
    border-radius: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    padding: 16px;
}

.modern-customer-list h2 {
    margin: 0 0 12px;
    font-size: 24px;
    font-weight: 700;
    color: var(--cl-text);
    letter-spacing: -0.02em;
}

.modern-customer-list .options-bar {
    margin: 0 0 12px;
    padding: 12px;
    background: var(--cl-soft);
    border: 1px solid var(--cl-border);
    border-radius: 10px;
}

.modern-customer-list .options-bar form {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.modern-customer-list .form-control {
    min-width: 220px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #fff;
    color: #111827;
    padding: 8px 10px;
}

.modern-customer-list .btn {
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #fff;
    color: #1f2937;
    padding: 8px 12px;
    font-weight: 600;
}

.modern-customer-list .btn:hover {
    border-color: #94a3b8;
    background: #f8fafc;
}

.modern-customer-list #table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border: 1px solid var(--cl-border);
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
}

.modern-customer-list #table thead th {
    background: #f9fafb;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 11px;
    font-weight: 700;
    border-bottom: 1px solid var(--cl-border);
    padding: 12px 14px;
}

.modern-customer-list #table tbody td {
    padding: 14px;
    border-bottom: 1px solid #f1f5f9;
    color: #1f2937;
    vertical-align: middle;
}

.modern-customer-list #table tbody tr:last-child td {
    border-bottom: 0;
}

.modern-customer-list #table tbody tr:hover td {
    background: #fbfdff;
}

.cust-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.cust-avatar {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    border: 1px solid #dbe5f0;
    background: #eef4ff;
    color: #1e3a8a;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.cust-name {
    color: #0f172a;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
}

.cust-name:hover {
    color: var(--cl-accent);
}

.cust-sub {
    margin-top: 2px;
    color: var(--cl-subtext);
    font-size: 12px;
}

.status-chip {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 4px 8px;
    border: 1px solid #d1d5db;
    background: #f3f4f6;
    color: #4b5563;
    font-size: 11px;
    font-weight: 600;
    line-height: 1;
}

.status-chip.status-pending {
    background: #fffbeb;
    border-color: #fde68a;
    color: #92400e;
}

.status-chip.status-confirm {
    background: #ecfdf5;
    border-color: #bbf7d0;
    color: #166534;
}

.status-chip.status-cancel {
    background: #fef2f2;
    border-color: #fecaca;
    color: #991b1b;
}

.view-profile-btn {
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

.view-profile-btn:hover {
    background: #dbeafe;
    color: #1e3a8a;
}

.empty-customer-state {
    text-align: center;
    color: var(--cl-subtext);
    font-size: 13px;
    padding: 28px 16px;
}

@media (max-width: 900px) {
    .modern-customer-list .panel {
        padding: 12px;
    }

    .modern-customer-list .form-control {
        min-width: 100%;
    }
}
</style>

<div class="modern-customer-list">
    <h2>All Customers</h2>
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
                        <option value="2" <?php echo (($statusParam == '2') ? 'selected' : '') ?>>Cancel
                            <?php echo $postType ?></option>
                    </select>
                    <input type="submit" class="btn" value="Filter">
                </form>
            </div>
            <div class="right-box"></div>
        </div>

        <div class="crm-dt-scroll">
        <table id="table">
            <thead>
                <tr>
                    <th style="width: 62px">#</th>
                    <th style="width: 130px">Order</th>
                    <th>Customer</th>
                    <th style="width: 150px">Mobile</th>
                    <th style="width: 120px">Status</th>
                    <th style="width: 120px">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasCustomers = false;

                if ($dataList = mysqli_query($connection, "SELECT profile_user_id, profile_display_name, profile_mobile, order_id, order_product_id, order_product_id, order_product_qty, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, order_generate, order_status FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=order_user_id WHERE order_site_id=" . APP_SITE_ID . " {$orderStatus} GROUP BY order_user_id ORDER BY order_id DESC")) {

                    if (mysqli_num_rows($dataList) != 0) {
                        $i = 1;
                        while ($data = mysqli_fetch_assoc($dataList)) {
                            $hasCustomers = true;
                            extract($data);

                            $statusLabel = 'Pending';
                            $statusClass = 'status-pending';

                            if ((string)$order_status === '1') {
                                $statusLabel = 'Confirm';
                                $statusClass = 'status-confirm';
                            } elseif ((string)$order_status === '2') {
                                $statusLabel = 'Cancel';
                                $statusClass = 'status-cancel';
                            }

                            $initials = 'CU';
                            if (trim($profile_display_name) != '') {
                                $nameParts = preg_split('/\\s+/', trim($profile_display_name));
                                $firstInitial = strtoupper(substr($nameParts[0], 0, 1));
                                $secondInitial = (isset($nameParts[1]) && $nameParts[1] != '') ? strtoupper(substr($nameParts[1], 0, 1)) : '';
                                $initials = $firstInitial . $secondInitial;
                            }
                ?>
                <tr>
                    <td data-label="#"><?php echo $i; ?></td>
                    <td data-label="Order">OD<?php echo (int)$order_id; ?></td>
                    <td data-label="Customer">
                        <div class="cust-cell">
                            <span class="cust-avatar"><?php echo htmlspecialchars($initials); ?></span>
                            <div>
                                <a class="cust-name"
                                    href="customer/?type=profile&id=<?php echo base64_encode($profile_user_id); ?>"><?php echo htmlspecialchars($profile_display_name); ?></a>
                                <div class="cust-sub">Customer Profile</div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Mobile">
                        <a class="cust-name"
                            href="customer/?type=profile&id=<?php echo base64_encode($profile_user_id); ?>"><?php echo htmlspecialchars($profile_mobile); ?></a>
                    </td>
                    <td data-label="Status"><span class="status-chip <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                    <td data-label="Action">
                        <a href="customer/?type=profile&id=<?php echo base64_encode($profile_user_id); ?>"
                            class="view-profile-btn"><i class="fas fa-eye"></i> View</a>
                    </td>
                </tr>
                <?php
                            $i++;
                        }
                    }
                }

                if (!$hasCustomers) {
                    echo '<tr><td colspan="6"><div class="empty-customer-state">No customers found for this filter.</div></td></tr>';
                }
                ?>
            </tbody>
        </table>
        </div>
    </div>
</div>