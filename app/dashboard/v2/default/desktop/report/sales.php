<div class="box-1">
    <h1 class="pageHeading">Sales report</h1>
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

$query = "SELECT membership_id, membership_profile_name, membership_total_visit, membership_avl_visit, membership_price, membership_amount_due, user_mobile, membership_status
        FROM " . TABLE_PREFIX . "membership
        INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
        INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
        WHERE membership_created BETWEEN '$from' AND '$to' AND membership_site_id=" . SITE_ID . "
        ORDER BY membership_profile_name ASC, membership_id DESC";

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
    <div class="box-1" style="margin-bottom:12px;">
        <div class="dashboard-summary-cards-row">
            <div class="dashboard-summary-card"
                style="position:relative;overflow:hidden;background:linear-gradient(135deg,#f0f9ff 60%,#e0e7ef 100%);border:1px solid #e6ebf1;border-radius:16px;padding:18px 20px;min-width:0;box-shadow:0 4px 16px rgba(20,33,61,0.09);">
                <i class="fi fi-rr-shopping-bag"
                    style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#38bdf8;opacity:0.13;pointer-events:none;z-index:0;"></i>
                <div
                    style="font-size:11px;line-height:1.2;letter-spacing:.1em;text-transform:uppercase;color:#0284c7;font-weight:700;position:relative;z-index:1;">
                    Total Sales</div>
                <div
                    style="font-size:clamp(28px,3.2vw,36px);line-height:1.05;font-weight:900;color:#0f172a;margin-top:8px;position:relative;z-index:1;">
                    <?php echo number_format($total_memberships); ?></div>
            </div>
            <div class="dashboard-summary-card"
                style="position:relative;overflow:hidden;background:linear-gradient(135deg,#fef9c3 60%,#fde68a 100%);border:1px solid #e6ebf1;border-radius:16px;padding:18px 20px;min-width:0;box-shadow:0 4px 16px rgba(20,33,61,0.09);">
                <i class="fi fi-rr-wallet"
                    style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#facc15;opacity:0.13;pointer-events:none;z-index:0;"></i>
                <div
                    style="font-size:11px;line-height:1.2;letter-spacing:.1em;text-transform:uppercase;color:#b45309;font-weight:700;position:relative;z-index:1;">
                    Total Sales Amount</div>
                <div
                    style="font-size:clamp(28px,3.2vw,36px);line-height:1.05;font-weight:900;color:#78350f;margin-top:8px;position:relative;z-index:1;">
                    ₹ <?php echo number_format($total_sales_amount); ?></div>
            </div>
            <div class="dashboard-summary-card"
                style="position:relative;overflow:hidden;background:linear-gradient(135deg,#dcfce7 60%,#bbf7d0 100%);border:1px solid #e6ebf1;border-radius:16px;padding:18px 20px;min-width:0;box-shadow:0 4px 16px rgba(20,33,61,0.09);">
                <i class="fi fi-rr-credit-card"
                    style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#22c55e;opacity:0.13;pointer-events:none;z-index:0;"></i>
                <div
                    style="font-size:11px;line-height:1.2;letter-spacing:.1em;text-transform:uppercase;color:#15803d;font-weight:700;position:relative;z-index:1;">
                    Payment Received</div>
                <div
                    style="font-size:clamp(28px,3.2vw,36px);line-height:1.05;font-weight:900;color:#166534;margin-top:8px;position:relative;z-index:1;">
                    ₹ <?php echo number_format($total_payment_received); ?></div>
            </div>
            <div class="dashboard-summary-card"
                style="position:relative;overflow:hidden;background:linear-gradient(135deg,#fee2e2 60%,#fecaca 100%);border:1px solid #e6ebf1;border-radius:16px;padding:18px 20px;min-width:0;box-shadow:0 4px 16px rgba(20,33,61,0.09);">
                <i class="fi fi-rr-receipt"
                    style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#ef4444;opacity:0.13;pointer-events:none;z-index:0;"></i>
                <div
                    style="font-size:11px;line-height:1.2;letter-spacing:.1em;text-transform:uppercase;color:#b91c1c;font-weight:700;position:relative;z-index:1;">
                    Due Amount</div>
                <div
                    style="font-size:clamp(28px,3.2vw,36px);line-height:1.05;font-weight:900;color:#991b1b;margin-top:8px;position:relative;z-index:1;">
                    ₹ <?php echo number_format($total_due_amount); ?></div>
            </div>
        </div>
    </div>
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
                    <div style="display: flex; justify-content: space-between; width: 100%;">
                        <p><strong><?php echo $name; ?></strong>
                            <span>#<?php echo $id; ?></span>
                        </p>
                        <p
                            style="background-color: #d4edda; padding: 0px 12px; border-radius: 4px; font-weight: 700; line-height: 44px; font-size: 18px; text-align: center; min-width: 100px; color: #155724;">
                            ₹ <?php echo number_format($data['membership_price']); ?></p>
                    </div>
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

<style>
@media (min-width: 600px) {
    .dashboard-summary-cards-row {
        display: flex !important;
        flex-wrap: wrap;
        gap: 16px;
    }

    .dashboard-summary-card {
        flex: 0 0 calc(50% - 8px) !important;
        max-width: calc(50% - 8px) !important;
        margin-bottom: 16px;
    }
}

@media (max-width: 599px) {
    .dashboard-summary-cards-row {
        display: block !important;
    }

    .dashboard-summary-card {
        width: 37% !important;
        max-width: 50% !important;
        margin-bottom: 16px;
        float: left;
        margin-left: 1%;
        margin-right: 1%;
        box-shadow: none !important;
    }
}
</style>