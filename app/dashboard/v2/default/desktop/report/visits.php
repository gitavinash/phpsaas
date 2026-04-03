<div class="box-1">
    <h1 class="pageHeading">Visit report</h1>
</div>
<div class="box-1 dashboard-datepicker">
    <div class="form-group right">
        <label>Reporting period <?php echo ((isset($daterange)) ? '' : ' ( Last 30 days )') ?></label>
        <form action="" class="widgetForm">
            <input type="text" name="daterange" class="dateRangePicker" value="" id="dashboardReportingDateRangePicker"
                placeholder="Select date range" readonly autocomplete="off">
            <input type="submit" value="Update report" class="btn btn-green date-btn">
        </form>
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
$count_query = "SELECT COUNT(*) as total_count FROM " . TABLE_PREFIX . "membership_visits
    INNER JOIN " . TABLE_PREFIX . "membership ON visit_membership_id=membership_id
    INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
    INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
    WHERE visit_time BETWEEN '$from' AND '$to' AND membership_site_id=" . SITE_ID;
$total_count = 0;
if ($count_res = mysqli_query($connection, $count_query)) {
    $count_row = mysqli_fetch_assoc($count_res);
    $total_count = (int)($count_row['total_count'] ?? 0);
}

$query = "SELECT membership_id, membership_profile_name, membership_total_visit, membership_avl_visit, membership_amount_due, user_mobile, membership_status, visit_time, visit_duration
        FROM " . TABLE_PREFIX . "membership_visits
        INNER JOIN " . TABLE_PREFIX . "membership ON visit_membership_id=membership_id
        INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
        INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
        WHERE visit_time BETWEEN '$from' AND '$to' AND membership_site_id=" . SITE_ID . "
        ORDER BY membership_profile_name ASC, membership_id DESC
        LIMIT $per_page OFFSET $offset";

$rows = [];
$counted_memberships = [];
if ($dataList = mysqli_query($connection, $query)) {
    while ($r = mysqli_fetch_assoc($dataList)) {
        $rows[] = $r;
        $membership_id = (int)($r['membership_id'] ?? 0);
        if ($membership_id > 0 && !isset($counted_memberships[$membership_id])) {
            $counted_memberships[$membership_id] = true;
        }
    }
} else if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
    echo "<pre>SQL Error: " . htmlspecialchars(mysqli_error($connection)) . "</pre>";
}

$total_visit_count = $total_count;
$total_customer_count = count($counted_memberships);
?>
<section>
    <div class="box-1" style="margin-bottom:12px;">
        <div style="display:flex;gap:12px;flex-wrap:nowrap;">
            <div
                style="flex:1;position:relative;overflow:hidden;background:#ffffff;background-image:linear-gradient(135deg, rgba(59,130,246,0.08) 0%, rgba(255,255,255,0) 55%);border:1px solid #e6ebf1;border-radius:12px;padding:12px 16px;min-width:0;box-shadow:0 2px 10px rgba(20, 33, 61, 0.06);">
                <i class="fi fi-rr-chart-line-up"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:56px;color:#111827;opacity:0.11;pointer-events:none;"></i>
                <div
                    style="font-size:11px;line-height:1.2;letter-spacing:.1em;text-transform:uppercase;color:#6b7280;font-weight:600;position:relative;z-index:1;">
                    Total Visits</div>
                <div
                    style="font-size:clamp(26px, 3.2vw, 34px);line-height:1.05;font-weight:800;color:#111827;margin-top:6px;text-align:right;position:relative;z-index:1;">
                    <?php echo number_format($total_visit_count); ?></div>
            </div>
            <div
                style="flex:1;position:relative;overflow:hidden;background:#ffffff;background-image:linear-gradient(135deg, rgba(34,197,94,0.08) 0%, rgba(255,255,255,0) 55%);border:1px solid #e6ebf1;border-radius:12px;padding:12px 16px;min-width:0;box-shadow:0 2px 10px rgba(20, 33, 61, 0.06);">
                <i class="fi fi-rr-users"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:56px;color:#111827;opacity:0.07;pointer-events:none;"></i>
                <div
                    style="font-size:11px;line-height:1.2;letter-spacing:.1em;text-transform:uppercase;color:#6b7280;font-weight:600;position:relative;z-index:1;">
                    No of Customers</div>
                <div
                    style="font-size:clamp(26px, 3.2vw, 34px);line-height:1.05;font-weight:800;color:#111827;margin-top:6px;text-align:right;position:relative;z-index:1;">
                    <?php echo number_format($total_customer_count); ?></div>
            </div>
        </div>
    </div>

    <!-- Pagination Bar -->
    <?php
    // Helper to build URL with updated page param
    function buildPageUrlVisits($page)
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
<style>
.pagination-bar {
    float: left;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 24px 0 12px;
}

.pagination-left {
    flex: 1;
    text-align: left;
}

.pagination-center {
    flex: 1;
    text-align: center;
    font-size: 1.1em;
    font-weight: 500;
    color: #2563eb;
}

.pagination-right {
    flex: 1;
    text-align: right;
}

.btn.disabled {
    opacity: 0.5;
    pointer-events: none;
}
</style>
<div class="only-mobile membership-card-list step_2_box">
    <?php if (!empty($rows)): ?>
    <?php foreach ($rows as $data): ?>
    <?php
            $id   = (int)$data['membership_id'];
            $name = htmlspecialchars(ucwords($data['membership_profile_name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $mob  = htmlspecialchars($data['user_mobile'] ?? '', ENT_QUOTES, 'UTF-8');
            $membership_visits  = htmlspecialchars($data['membership_total_visit'] ?? '', ENT_QUOTES, 'UTF-8');
            $membership_avl_visits  = htmlspecialchars($data['membership_avl_visit'] ?? '', ENT_QUOTES, 'UTF-8');
            $amount_due = htmlspecialchars($data['membership_amount_due'] ?? '0', ENT_QUOTES, 'UTF-8');
            $status = htmlspecialchars($data['membership_status'] ?? '', ENT_QUOTES, 'UTF-8');
            $encoded_id = base64_encode($id);
            $visit_time = htmlspecialchars($data['visit_time'] ?? '', ENT_QUOTES, 'UTF-8');
            $visit_duration = htmlspecialchars($data['visit_duration'] ?? '', ENT_QUOTES, 'UTF-8');
            ?>
    <article class="widget-setting-info membership-card" data-id="<?php echo $encoded_id; ?>"
        style="background-image:linear-gradient(140deg, rgba(17,24,39,0.04) 0%, rgba(255,255,255,0) 45%);border-radius:12px;">
        <div class="about-feature" style="max-width:100%;">
            <h3 class="heading name-cap" style="margin:0;">
                <?php echo $name; ?>
                <span><strong>#<?php echo $id; ?></strong> | <strong>Visit at: </strong>
                    <?php echo date('d-F-Y h:i A', strtotime($visit_time ?? '')); ?> <strong> for
                    </strong><?php echo $visit_duration ?> Mins.</span>
            </h3>

            <hr>

            <div class="row">
                <div class="box-12 info-stack">
                    <p style="margin:0;">
                        <strong>Mobile</strong><br>
                        <span><?php echo $mob; ?></span>
                    </p>
                    <p style="margin:0;">
                        <strong>Total Visits</strong><br>
                        <span><?php echo $membership_visits; ?></span>
                    </p>
                    <p style="margin:0;">
                        <strong>Avl. Visits</strong><br>
                        <span><?php echo $membership_avl_visits; ?></span>
                    </p>
                </div>
            </div>

            <?php if ($amount_due > 0): ?>
            <div class="membership-due-strip">
                <div style="font-size:13px;opacity:0.9;">Amount Due</div>
                <div style="font-size:24px; line-height:1.1; font-weight:800;">₹
                    <?php echo number_format($amount_due); ?></div>
            </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="action-stack" style="margin-top:10px;">
                <div class="action-item">
                    <a href="/dashboard/membership/update/?id=<?php echo $encoded_id; ?>"
                        class="btn btn-blue dataTableBtn" title="Edit membership">
                        <i class="fi-rr-pencil"></i> Edit
                    </a>
                </div>
                <div class="action-item">
                    <a href="#" class="btn btn-grey dataTableBtn visitHistoryButton"
                        data-id="<?php echo $encoded_id; ?>" title="Visit History">
                        <i class="fi-rr-time-past"></i> History
                    </a>
                </div>
            </div>

            <?php if ($amount_due > 0): ?>
            <div class="action-stack" style="margin-top:10px;">
                <div class="action-item">
                    <a href="#" class="btn btn-clear dataTableBtn clearDuesButton" data-id="<?php echo $encoded_id; ?>"
                        title="Clear dues">
                        <i class="fi-rr-check-circle"></i> Clear old dues
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </article>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="empty-state"><i class="fi fi-rr-box-open"></i> No memberships found.</div>
    <?php endif; ?>

    <div class="pagination-bar">
        <div class="pagination-left">
            <?php if ($has_prev): ?>
            <a href="<?php echo htmlspecialchars(buildPageUrlVisits($page - 1)); ?>" class="btn btn-grey">&laquo;
                Back</a>
            <?php else: ?>
            <span class="btn btn-grey disabled">&laquo; Back</span>
            <?php endif; ?>
        </div>
        <div class="pagination-center">
            Showing<br /><?php echo number_format($start_record); ?> - <?php echo number_format($end_record); ?> of
            <?php echo number_format($total_count); ?>
        </div>
        <div class="pagination-right">
            <?php if ($has_next): ?>
            <a href="<?php echo htmlspecialchars(buildPageUrlVisits($page + 1)); ?>" class="btn btn-blue">Next
                &raquo;</a>
            <?php else: ?>
            <span class="btn btn-blue disabled">Next &raquo;</span>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="step-open-box step_6_box historyBox" style="display:none">
    <h2 style="font-size:24px;font-weight:700;margin-bottom:12px; margin-left: 5px;">
        Visit Logs
    </h2>
    <div class="showVisitorHistory"></div>
    <div class="stepNavigation">
        <button type="button" style="width: 100%; color: #fff;" class="btn btn-primary backToStep2">
            <i class="fi-rr-arrow-left"></i> Back
        </button>
    </div>
</div>
<div class="step-open-box step_7_box clearDueBox" style="display:none">
    <h2 style="font-size:24px;font-weight:700;margin-bottom:12px; margin-left: 5px;">
        Collect Due Payment
    </h2>
    <div class="form-group" style="margin-top: 20px;">
        <input id="amount" name="amount" placeholder="1000" type="number" required />
        <label class="form-lable" id="Label">Amount ( In ₹ )<sup>*</sup></label>
    </div>
    <div class="form-group" style="margin-bottom:10px;">
        <select class="selectJS form-control" id="paymentMode" name="paymentMode" required
            style="width:100%;height:44px;border:1px solid #dfe3e8;border-radius:8px;padding:0 10px;font-size:16px;background:#fff;">
            <option value="">Select mode</option>
            <option value="cash" selected>Cash</option>
            <option value="upi">UPI</option>
            <option value="card">Card</option>
            <option value="other">Other</option>
        </select>
        <label class="form-lable" id="paymentModeLabel">Payment Mode <sup>*</sup></label>
    </div>
    <div class="form-group" style="margin-top: 20px; margin-bottom:10px;">
        <textarea name="comment" id="comment" placeholder="Any comment?"></textarea>
        <label class="form-lable" id="Label">Notes (optional)</label>
    </div>
    <div class="box-1">
        <div class="form-group">
            <input type="hidden" id="membershipId" name="membershipId" value="">
            <button type="button" style="width: 100%;" class="btn btn-green" id="collectPaymentBtn">
                <i class="fi-rr-check"></i> Collect Payment
            </button>
        </div>
        <div class="form-group">
            <button type="button" style="width: 100%;" class="btn btn-primary backToStep2">
                <i class="fi-rr-arrow-left"></i> Back
            </button>
        </div>
    </div>
</div>
</section>