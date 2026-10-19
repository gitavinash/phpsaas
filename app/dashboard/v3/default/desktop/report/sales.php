<div class="box-1">
    <h1 class="pageHeading">Sales report</h1>
</div>
<div class="box-1 dashboard-datepicker">
    <div class="form-group right"><label>Reporting period
            <?php echo ((isset($daterange)) ? '' : ' ( Last 30 days )') ?></label>
        <form action="" class="widgetForm"><input type="text" name="daterange" class="dateRangePicker" value=""
                id="dashboardReportingDateRangePicker" placeholder="Select date range" readonly
                autocomplete="off"><input type="submit" value="Update report" class="btn btn-green date-btn"></form>
    </div>
</div>
<?php

if (isset($daterange)) {
    $dates = explode(' - ', $daterange);
    $from = date('Y-m-d', strtotime($dates[0])) . ' 00:00:00';
    $to = date('Y-m-d', strtotime($dates[1])) . ' 23:59:59';
} else {
    $from = date('Y-m-d', strtotime('-30 days')) . ' 00:00:00';
    $to = date('Y-m-d') . ' 23:59:59';
}

// Pagination setup
$per_page = 100;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total_count FROM " . TABLE_PREFIX . "membership
    INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
    INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
    WHERE membership_created BETWEEN '$from' AND '$to' AND membership_site_id=" . SITE_ID;
$total_count = 0;
if ($count_res = mysqli_query($connection, $count_query)) {
    $count_row = mysqli_fetch_assoc($count_res);
    $total_count = (int)($count_row['total_count'] ?? 0);
}

$query = "SELECT membership_id, membership_profile_name, membership_total_visit, membership_avl_visit, membership_price, membership_amount_due, user_mobile, membership_status
        FROM " . TABLE_PREFIX . "membership
        INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
        INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
        WHERE membership_created BETWEEN '$from' AND '$to' AND membership_site_id=" . SITE_ID . "
        ORDER BY membership_profile_name ASC, membership_id DESC
        LIMIT $per_page OFFSET $offset";

// Calculate totals
$rows = [];
$total_memberships = 0;
$total_sales_amount = 0;
$total_due_amount = 0;
$total_payment_received = 0;
if ($dataList = mysqli_query($connection, $query)) {
    while ($r = mysqli_fetch_assoc($dataList)) {
        $rows[] = $r;
        $total_memberships++;
        $price = (float)($r['membership_price'] ?? 0);
        $due = (float)($r['membership_amount_due'] ?? 0);
        $total_sales_amount += $price;
        $total_due_amount += $due;
        $total_payment_received += ($price - $due);
    }
} else if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
    echo "<pre>SQL Error: " . htmlspecialchars(mysqli_error($connection)) . "</pre>";
}
?>
<section>
    <div class="box-1">
        <div class="dashboard-summary-cards-row">
            <div class="dashboard-summary-card">
                <i class="fi fi-rr-users icon-bg"></i>
                <div class="card-label">Total Sales</div>
                <div class="card-value"><?php echo number_format($total_memberships); ?></div>
            </div>
            <div class="dashboard-summary-card">
                <i class="fi fi-rr-coins icon-bg"></i>
                <div class="card-label">Total Sales Amount</div>
                <div class="card-value">₹ <?php echo number_format($total_sales_amount); ?></div>
            </div>
            <div class="dashboard-summary-card">
                <i class="fi fi-rr-credit-card icon-bg"></i>
                <div class="card-label">Payment Received</div>
                <div class="card-value">₹ <?php echo number_format($total_payment_received); ?></div>
            </div>
            <div class="dashboard-summary-card">
                <i class="fi fi-rr-exclamation icon-bg"></i>
                <div class="card-label">Due Amount</div>
                <div class="card-value">₹ <?php echo number_format($total_due_amount); ?></div>
            </div>
        </div>
    </div>

    <!-- Pagination Bar -->
    <?php
    // Helper to build URL with updated page param
    function buildPageUrlSales($page)
    {
        $params = $_GET;
        $params['page'] = $page;
        return strtok($_SERVER['REQUEST_URI'], '?') . '?' . http_build_query($params);
    }
    $has_next = ($page * $per_page) < $total_count;
    $has_prev = $page > 1;
    $start_record = $total_count > 0 ? ($offset + 1) : 0;
    $end_record = min($offset + $per_page, $total_count);
    ?>
</section>

<div class="only-mobile membership-card-list step_2_box">
    <?php if (!empty($rows)): ?><?php foreach ($rows as $data): ?><?php
                                                                    $id   = (int)$data['membership_id'];
                                                                    $name = htmlspecialchars(ucwords($data['membership_profile_name'] ?? ''), ENT_QUOTES, 'UTF-8');
                                                                    $mob  = htmlspecialchars($data['user_mobile'] ?? '', ENT_QUOTES, 'UTF-8');
                                                                    $membership_visits  = htmlspecialchars($data['membership_total_visit'] ?? '', ENT_QUOTES, 'UTF-8');
                                                                    $membership_avl_visits  = htmlspecialchars($data['membership_avl_visit'] ?? '', ENT_QUOTES, 'UTF-8');
                                                                    $amount_due = htmlspecialchars($data['membership_amount_due'] ?? '0', ENT_QUOTES, 'UTF-8');
                                                                    $status = htmlspecialchars($data['membership_status'] ?? '', ENT_QUOTES, 'UTF-8');
                                                                    $encoded_id = base64_encode($id);
                                                                    ?>
    <article class="widget-setting-info membership-card" data-id="<?php echo $encoded_id; ?>">
        <div class="about-feature">
            <h3 class="heading name-cap">
                <div class="d-flex justify-content-between align-items-center">
                    <p><strong><?php echo $name; ?></strong> <span>#<?php echo $id; ?></span></p>
                    <p class="v3-status-pill status-approved">
                        ₹ <?php echo number_format($data['membership_price']); ?>
                    </p>
                </div>
            </h3>
            <hr>
            <div class="row">
                <div class="box-12 info-stack">
                    <p><strong>Mobile</strong><br><span><?php echo $mob; ?></span></p>
                    <p><strong>Total Visits</strong><br><span><?php echo $membership_visits; ?></span></p>
                    <p><strong>Avl. Visits</strong><br><span><?php echo $membership_avl_visits; ?></span></p>
                </div>
            </div><?php if ($amount_due > 0): ?><div class="membership-due-strip">
                <div class="due-label">Amount Due</div>
                <div class="due-value">₹ <?php echo number_format($amount_due); ?></div>
            </div><?php endif; ?>
            <!-- Actions -->
            <div class="action-stack">
                <div class="action-item"><a href="/dashboard/membership/update/?id=<?php echo $encoded_id; ?>"
                        class="btn btn-blue dataTableBtn" title="Edit membership"><i class="fi-rr-pencil"></i> Edit</a></div>
                <div class="action-item"><a href="#" class="btn btn-grey dataTableBtn visitHistoryButton"
                        data-id="<?php echo $encoded_id; ?>" title="Visit History"><i
                            class="fi-rr-time-past"></i> History</a></div>
            </div><?php if ($amount_due > 0): ?><div class="action-stack mt-2">
                <div class="action-item"><a href="#" class="btn btn-clear dataTableBtn clearDuesButton"
                        data-id="<?php echo $encoded_id; ?>" title="Clear dues"><i class="fi-rr-check-circle"></i> Clear old dues</a></div>
            </div><?php endif; ?>
        </div>
    </article><?php endforeach; ?><?php else: ?><div class="empty-state"><i class="fi fi-rr-box-open"></i>No memberships found.</div><?php endif; ?>

    <div class="pagination-bar">
        <div class="pagination-left">
            <?php if ($has_prev): ?>
            <a href="<?php echo htmlspecialchars(buildPageUrlSales($page - 1)); ?>" class="btn btn-grey">&laquo; Back</a>
            <?php else: ?>
            <span class="btn btn-grey disabled">&laquo; Back</span>
            <?php endif; ?>
        </div>
        <div class="pagination-center">
            Showing<br /><?php echo number_format($start_record); ?> - <?php echo number_format($end_record); ?> of <?php echo number_format($total_count); ?>
        </div>
        <div class="pagination-right">
            <?php if ($has_next): ?>
            <a href="<?php echo htmlspecialchars(buildPageUrlSales($page + 1)); ?>" class="btn btn-blue">Next &raquo;</a>
            <?php else: ?>
            <span class="btn btn-blue disabled">Next &raquo;</span>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="step-open-box step_6_box historyBox d-none">
    <h2 class="sub-heading">Visit Logs</h2>
    <div class="showVisitorHistory"></div>
    <div class="stepNavigation"><button type="button" class="btn btn-primary backToStep2 w-100"><i class="fi-rr-arrow-left"></i> Back</button></div>
</div>
<div class="step-open-box step_7_box clearDueBox d-none">
    <h2 class="sub-heading">Collect Due Payment</h2>
    <div class="form-group"><input id="amount" name="amount" placeholder="1000" type="number"
            required /><label class="form-lable" id="Label">Amount (In ₹)<sup>*</sup></label></div>
    <div class="form-group"><select class="selectJS2 form-control" id="paymentMode"
            name="paymentMode" required>
            <option value="">Select mode</option>
            <option value="cash" selected>Cash</option>
            <option value="upi">UPI</option>
            <option value="card">Card</option>
            <option value="other">Other</option>
        </select><label class="form-lable" id="paymentModeLabel">Payment Mode <sup>*</sup></label></div>
    <div class="form-group"><textarea name="comment" id="comment"
            placeholder="Any comment?"></textarea><label class="form-lable" id="Label">Notes (optional)</label>
    </div>
    <div class="box-1">
        <div class="form-group"><input type="hidden" id="membershipId" name="membershipId" value=""><button
                type="button" class="btn btn-green w-100" id="collectPaymentBtn"><i
                    class="fi-rr-check"></i> Collect Payment</button></div>
        <div class="form-group"><button type="button" class="btn btn-primary backToStep2 w-100"><i
                    class="fi-rr-arrow-left"></i> Back</button></div>
    </div>
</div>
</section>