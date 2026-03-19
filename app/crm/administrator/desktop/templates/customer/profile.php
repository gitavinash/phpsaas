<?php

$user_id = isset($id) ? (int)base64_decode($id) : 0;

if ($user_id <= 0) {
    echo "Invalid customer";
    return;
}

$customer = null;
$orders = array();
$addresses = array();

if ($employeeProfile = mysqli_query($connection, "SELECT profile_display_name, user_email, user_mobile, user_reg_time FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id WHERE user_id={$user_id}")) {
    if (mysqli_num_rows($employeeProfile) != 0) {
        $customer = mysqli_fetch_assoc($employeeProfile);
    }
} else {
    echo mysqli_error($connection);
    return;
}

if (!$customer) {
    echo "No customer found";
    return;
}

if ($checkOrder = mysqli_query($connection, "SELECT order_id, order_product_qty, order_generate, product_title FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "product ON product_id=order_product_id WHERE order_user_id='{$user_id}' AND order_site_id=" . APP_SITE_ID)) {
    while ($orderData = mysqli_fetch_assoc($checkOrder)) {
        $orders[] = $orderData;
    }
}

if ($checkAddress = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "address INNER JOIN " . TABLE_PREFIX . "city ON city_id=address_city_id INNER JOIN " . TABLE_PREFIX . "state ON state_id=address_state_id WHERE address_user_id='{$user_id}' AND address_site_id=" . APP_SITE_ID)) {
    while ($addressData = mysqli_fetch_assoc($checkAddress)) {
        $addresses[] = $addressData;
    }
}

$profileName = trim((string)$customer['profile_display_name']);
$nameParts = preg_split('/\s+/', $profileName);
$initials = 'CU';
if (!empty($nameParts[0])) {
    $initials = strtoupper(substr($nameParts[0], 0, 1));
    if (!empty($nameParts[1])) {
        $initials .= strtoupper(substr($nameParts[1], 0, 1));
    }
}

?>

<style>
.modern-customer-profile {
    --cp-border: #e5e7eb;
    --cp-surface: #ffffff;
    --cp-soft: #f8fafc;
    --cp-text: #111827;
    --cp-sub: #6b7280;
    --cp-accent: #1d4ed8;
}

.modern-customer-profile h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: var(--cp-text);
    letter-spacing: -0.02em;
}

.modern-customer-profile .cp-subtitle {
    margin-top: 6px;
    color: var(--cp-sub);
    font-size: 13px;
}

.modern-customer-profile .cp-panel {
    border: 1px solid var(--cp-border);
    border-radius: 14px;
    background: var(--cp-surface);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    padding: 16px;
    margin-top: 12px;
}

.modern-customer-profile .cp-overview {
    display: grid;
    grid-template-columns: 76px minmax(0, 1fr);
    gap: 14px;
    align-items: center;
}

.modern-customer-profile .cp-avatar {
    width: 76px;
    height: 76px;
    border-radius: 999px;
    background: #eef4ff;
    border: 1px solid #dbe5f0;
    color: #1e3a8a;
    font-weight: 700;
    font-size: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modern-customer-profile .cp-name {
    font-size: 20px;
    font-weight: 700;
    color: var(--cp-text);
}

.modern-customer-profile .cp-grid {
    margin-top: 12px;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.modern-customer-profile .cp-kpi {
    border: 1px solid var(--cp-border);
    border-radius: 10px;
    background: var(--cp-soft);
    padding: 10px;
}

.modern-customer-profile .cp-kpi-label {
    color: #9ca3af;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 700;
}

.modern-customer-profile .cp-kpi-value {
    margin-top: 4px;
    color: var(--cp-text);
    font-size: 13px;
    font-weight: 600;
    word-break: break-word;
}

.modern-customer-profile .cp-sections {
    margin-top: 12px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.modern-customer-profile .cp-card {
    border: 1px solid var(--cp-border);
    border-radius: 12px;
    background: var(--cp-surface);
    overflow: hidden;
}

.modern-customer-profile .cp-card-head {
    padding: 12px;
    border-bottom: 1px solid var(--cp-border);
    background: var(--cp-soft);
    font-size: 13px;
    font-weight: 700;
    color: #1f2937;
}

.modern-customer-profile .cp-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.modern-customer-profile .cp-list-item {
    border-bottom: 1px solid #f1f5f9;
    padding: 12px;
}

.modern-customer-profile .cp-list-item:last-child {
    border-bottom: 0;
}

.modern-customer-profile .cp-order-title {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    text-decoration: none;
}

.modern-customer-profile .cp-order-title:hover {
    color: var(--cp-accent);
}

.modern-customer-profile .cp-order-meta,
.modern-customer-profile .cp-address-meta {
    margin-top: 4px;
    color: var(--cp-sub);
    font-size: 12px;
    line-height: 1.45;
}

.modern-customer-profile .cp-empty {
    padding: 16px 12px;
    color: var(--cp-sub);
    font-size: 13px;
}

@media (max-width: 960px) {
    .modern-customer-profile .cp-grid {
        grid-template-columns: 1fr;
    }

    .modern-customer-profile .cp-sections {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="modern-customer-profile">
    <h2>Customer Profile</h2>
    <div class="cp-subtitle">Customer details, order history, and saved addresses.</div>

    <div class="cp-panel">
        <div class="cp-overview">
            <div class="cp-avatar"><?php echo htmlspecialchars($initials); ?></div>
            <div>
                <div class="cp-name"><?php echo htmlspecialchars($profileName); ?></div>
            </div>
        </div>

        <div class="cp-grid">
            <div class="cp-kpi">
                <div class="cp-kpi-label">Email</div>
                <div class="cp-kpi-value"><?php echo htmlspecialchars((string)$customer['user_email']); ?></div>
            </div>
            <div class="cp-kpi">
                <div class="cp-kpi-label">Mobile</div>
                <div class="cp-kpi-value"><?php echo htmlspecialchars((string)$customer['user_mobile']); ?></div>
            </div>
            <div class="cp-kpi">
                <div class="cp-kpi-label">Customer Since</div>
                <div class="cp-kpi-value"><?php echo date('d F Y', strtotime($customer['user_reg_time'])); ?></div>
            </div>
        </div>
    </div>

    <div class="cp-sections">
        <div class="cp-card">
            <div class="cp-card-head">Order History</div>
            <?php if (!empty($orders)): ?>
            <ul class="cp-list">
                <?php foreach ($orders as $orderData): ?>
                <li class="cp-list-item">
                    <a class="cp-order-title"
                        href="/dashboard/order/?type=view&id=<?php echo base64_encode($orderData['order_id']); ?>">
                        <?php echo htmlspecialchars($orderData['product_title']); ?>
                    </a>
                    <div class="cp-order-meta">
                        Qty: <?php echo htmlspecialchars($orderData['order_product_qty']); ?> |
                        Date: <?php echo date('d M Y', strtotime($orderData['order_generate'])); ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <div class="cp-empty">No orders found.</div>
            <?php endif; ?>
        </div>

        <div class="cp-card">
            <div class="cp-card-head">Addresses</div>
            <?php if (!empty($addresses)): ?>
            <ul class="cp-list">
                <?php foreach ($addresses as $addressData): ?>
                <li class="cp-list-item">
                    <div class="cp-order-title"><?php echo htmlspecialchars($addressData['address_name']); ?></div>
                    <div class="cp-address-meta">
                        <?php echo htmlspecialchars($addressData['address_house_no']); ?>,
                        <?php echo htmlspecialchars($addressData['address_area']); ?>,
                        <?php echo htmlspecialchars($addressData['address_landmark']); ?><br>
                        <?php echo htmlspecialchars($addressData['city_name']); ?>,
                        <?php echo htmlspecialchars($addressData['state_name']); ?>,
                        <?php echo htmlspecialchars($addressData['address_pin_code']); ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <div class="cp-empty">No address found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>