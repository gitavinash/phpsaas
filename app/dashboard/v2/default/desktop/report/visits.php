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

$query = "SELECT membership_id, membership_profile_name, membership_total_visit, membership_avl_visit, membership_amount_due, user_mobile, membership_status
        FROM " . TABLE_PREFIX . "membership_visits
        INNER JOIN " . TABLE_PREFIX . "membership ON visit_membership_id=membership_id
        INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
        INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
        WHERE visit_time BETWEEN '$from' AND '$to' AND membership_site_id=" . SITE_ID . "
        ORDER BY membership_profile_name ASC, membership_id DESC";

if ($dataList = mysqli_query($connection, $query)) {
    while ($r = mysqli_fetch_assoc($dataList)) {
        $rows[] = $r;
    }
} else if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
    echo "<pre>SQL Error: " . htmlspecialchars(mysqli_error($connection)) . "</pre>";
}
?>
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
            <div class="about-feature" style="max-width:100%;">
                <h3 class="heading name-cap" style="margin:0;">
                    <?php echo $name; ?>
                    <span>#<?php echo $id; ?></span>
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
        <div class="empty-state"><i class="fi fi-rr-box-open"></i> No memberships found.</div>
        <?php endif; ?>
    </div>

    <div class="step-open-box step_6_box historyBox" style="display:none">
        <h2 style="font-size:24px;font-weight:700;margin-bottom:12px; margin-left: 5px;">
            Visit Logs
        </h2>
        <div class="showVisitorHistory"></div>
        <div class="stepNavigation">
            <button type="button" style="width: 100%;" class="btn btn-primary backToStep2">
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