<div class="box-1">
    <h1 class="pageHeading">Balance due's report</h1>
</div>

<?php
$summary_total_customers = 0;
$summary_total_due = 0;

$filter_active = false;
if (
    (isset($_GET['visits_filter']) && $_GET['visits_filter'] !== 'all') ||
    (isset($_GET['payment_filter']) && $_GET['payment_filter'] !== 'all') ||
    (isset($_GET['recent_days']) && $_GET['recent_days'] !== 'all' && $_GET['recent_days'] !== '')
) {
    $filter_active = true;
}
?>
<div class="box-1 dashboard-datepicker">
    <div class="form-group right">
        <label>Reporting period <?php echo ((isset($daterange)) ? '' : ' ( All Time )') ?></label>
        <form action="" class="widgetForm">
            <input type="text" name="daterange" class="dateRangePicker" value="" id="dashboardReportingDateRangePicker"
                placeholder="Select date range" readonly autocomplete="off">
            <input type="submit" value="Update report" class="btn btn-green date-btn">
        </form>
    </div>
</div>

<!-- Floating Filter Button and Modal -->
<button id="openFilterModal" type="button" class="btn-floating-filter" aria-label="Open filter">
    <i class="fi fi-rr-filter"></i>
</button>
<div id="filterModal" class="floating-filter-modal">
    <form method="get" id="filterForm" class="floating-filter-form">
        <div>
            <label for="visits_filter">Visits Available</label><br>
            <select name="visits_filter" id="visits_filter" class="selectJS2 form-control">
                <option value="all"
                    <?php if (!isset($_GET['visits_filter']) || $_GET['visits_filter'] === 'all') echo 'selected'; ?>>
                    All</option>
                <option value="with"
                    <?php if (isset($_GET['visits_filter']) && $_GET['visits_filter'] === 'with') echo 'selected'; ?>>
                    With Visits</option>
                <option value="without"
                    <?php if (isset($_GET['visits_filter']) && $_GET['visits_filter'] === 'without') echo 'selected'; ?>>
                    No Visits</option>
            </select>
        </div>
        <div class="floating-filter-actions">
            <button type="submit" class="btn btn-green">Apply</button>
            <button type="button" id="closeFilterModal" class="btn btn-grey">Close</button>
        </div>
    </form>
</div>

<div id="loadingOverlay" class="loading-overlay">
    <div class="loading-spinner"></div>
</div>

<?php

// 1. Fetch all unpaid memberships
$all_rows = [];
$sql_all = "SELECT membership_id, membership_profile_name, membership_total_visit, membership_avl_visit, membership_amount_due, user_mobile, membership_status, membership_site_id, membership_user_id, membership_branch_id
    FROM " . TABLE_PREFIX . "membership
    INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
    INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
    WHERE membership_amount_due!=0 AND membership_site_id=" . SITE_ID;
if ($dataList = mysqli_query($connection, $sql_all)) {
    while ($r = mysqli_fetch_assoc($dataList)) {
        $all_rows[] = $r;
    }
} else if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
    echo "<pre>SQL Error: " . htmlspecialchars(mysqli_error($connection)) . "</pre>";
}

// 2. Apply filters in PHP
$rows = $all_rows;
// Membership ID filter
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $membership_id = (int)base64_decode($_GET['id']);
    if ($membership_id > 0) {
        $rows = array_filter($rows, function ($r) use ($membership_id) {
            return $r['membership_id'] == $membership_id;
        });
    }
}
// Visits filter
$visits_filter = isset($_GET['visits_filter']) ? $_GET['visits_filter'] : 'all';
if ($visits_filter === 'with') {
    $rows = array_filter($rows, function ($r) {
        return $r['membership_avl_visit'] > 0;
    });
} elseif ($visits_filter === 'without') {
    $rows = array_filter($rows, function ($r) {
        return $r['membership_avl_visit'] == 0;
    });
}
// Payment filter
$payment_filter = isset($_GET['payment_filter']) ? $_GET['payment_filter'] : 'all';
if ($payment_filter === 'pending') {
    $rows = array_filter($rows, function ($r) {
        return $r['membership_amount_due'] > 0;
    });
} elseif ($payment_filter === 'clear') {
    $rows = array_filter($rows, function ($r) {
        return $r['membership_amount_due'] == 0;
    });
}

// 3. Re-index $rows for consistent foreach and count
$rows = array_values($rows);

if (!empty($rows)) {
    foreach ($rows as $r) {
        $summary_total_customers++;
        $summary_total_due += (float)($r['membership_amount_due'] ?? 0);
    }
}

?>

<div class="box-1">
    <div class="dashboard-summary-cards-row">
        <div class="dashboard-summary-card">
            <span class="icon-bg"><i class="fi fi-rr-users"></i></span>
            <div class="card-label">Total Customers</div>
            <div class="card-value"><?php echo number_format($summary_total_customers); ?></div>
        </div>
        <div class="dashboard-summary-card">
            <span class="icon-bg"><i class="fi fi-rr-exclamation"></i></span>
            <div class="card-label">Total Due Amount</div>
            <div class="card-value">₹ <?php echo number_format($summary_total_due); ?></div>
        </div>
    </div>
</div>
<section>
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
                ?>
        <article class="widget-setting-info membership-card" data-id="<?php echo $encoded_id; ?>">
            <div class="about-feature">
                <h3 class="heading name-cap">
                    <?php echo $name; ?>
                    <span>#<?php echo $id; ?></span>
                </h3>

                <hr>

                <div class="row">
                    <div class="box-12 info-stack">
                        <p>
                            <strong>Mobile</strong><br>
                            <span><?php echo $mob; ?></span>
                        </p>
                        <p>
                            <strong>Total Visits</strong><br>
                            <span><?php echo $membership_visits; ?></span>
                        </p>
                        <p>
                            <strong>Avl. Visits</strong><br>
                            <span><?php echo $membership_avl_visits; ?></span>
                        </p>
                    </div>
                </div>

                <?php if ($amount_due > 0): ?>
                <div class="membership-due-strip">
                    <div class="due-label">Amount Due</div>
                    <div class="due-value">₹ <?php echo number_format($amount_due); ?></div>
                </div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="action-stack">
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
                <div class="action-stack mt-2">
                    <div class="action-item">
                        <a href="#" class="btn btn-clear dataTableBtn clearDuesButton"
                            data-id="<?php echo $encoded_id; ?>" title="Clear dues">
                            <i class="fi-rr-check-circle"></i> Clear old dues
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </article>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="v3-empty-state">
            <div class="v3-empty-state-icon"><i class="fi fi-rr-box-open"></i></div>
            <div class="v3-empty-state-title">No memberships found</div>
            <div class="v3-empty-state-subtitle">Try adjusting your filters or check for unpaid memberships later.</div>
        </div>
        <?php endif; ?>
    </div>

    <div class="step-open-box step_6_box historyBox d-none">
        <h2 class="sub-heading">
            Visit Logs
        </h2>
        <div class="showVisitorHistory"></div>
        <div class="stepNavigation">
            <button type="button" class="btn btn-primary backToStep2 w-100">
                <i class="fi-rr-arrow-left"></i> Back
            </button>
        </div>
    </div>
    <div class="step-open-box step_7_box clearDueBox d-none">
        <h2 class="sub-heading">
            Collect Due Payment
        </h2>
        <div class="form-group">
            <input id="amount" name="amount" placeholder="1000" type="number" required />
            <label class="form-lable" id="Label">Amount ( In ₹ )<sup>*</sup></label>
        </div>
        <div class="form-group">
            <select class="selectJS2 form-control" id="paymentMode" name="paymentMode" required>
                <option value="">Select mode</option>
                <option value="cash" selected>Cash</option>
                <option value="upi">UPI</option>
                <option value="card">Card</option>
                <option value="other">Other</option>
            </select>
            <label class="form-lable" id="paymentModeLabel">Payment Mode <sup>*</sup></label>
        </div>
        <div class="form-group">
            <textarea name="comment" id="comment" placeholder="Any comment?"></textarea>
            <label class="form-lable" id="Label">Notes (optional)</label>
        </div>
        <div class="box-1">
            <div class="form-group">
                <input type="hidden" id="membershipId" name="membershipId" value="">
                <button type="button" class="btn btn-green w-100" id="collectPaymentBtn">
                    <i class="fi-rr-check"></i> Collect Payment
                </button>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary backToStep2 w-100">
                    <i class="fi-rr-arrow-left"></i> Back
                </button>
            </div>
        </div>
    </div>
</section>