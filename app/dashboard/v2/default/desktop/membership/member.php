<h2 class="heading">Membership List
    <span>All membership list will be displayed here</span>
</h2>

<div class="main-box">
    <div class="shortcut">
        <a href="/dashboard/membership/sell/"><i class="fi fi-rr-plus"></i></a>
    </div>

    <div class="list-box">
        <!-- Floating Filter Button and Modal -->
        <button id="openFilterModal" type="button"
            style="position:fixed;left:10px;bottom:10%;z-index:1001;background:#2563eb;color:#fff;border:none;border-radius:50%;width:56px;height:56px;box-shadow:0 2px 8px rgba(0,0,0,0.18);font-size:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
            <i class="fi fi-rr-filter"></i>
        </button>
        <div id="filterModal"
            style="display:none;position:fixed;left:24px;bottom:90px;z-index:1002;background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(20,33,61,0.18);padding:24px 20px 16px 20px;min-width:260px;max-width:90vw;">
            <form method="get" id="filterForm" style="display:flex;flex-direction:column;gap:12px;">
                <div>
                    <label>Visits Available</label><br>
                    <select name="visits_filter" class="form-control">
                        <option value="with"
                            <?php if (!isset($_GET['visits_filter']) || $_GET['visits_filter'] === 'with') echo 'selected'; ?>>
                            With Visits</option>
                        <option value="without"
                            <?php if (isset($_GET['visits_filter']) && $_GET['visits_filter'] === 'without') echo 'selected'; ?>>
                            No Visits</option>
                        <option value="all"
                            <?php if (isset($_GET['visits_filter']) && $_GET['visits_filter'] === 'all') echo 'selected'; ?>>
                            All</option>
                    </select>
                </div>
                <div>
                    <label>Payment Status</label><br>
                    <select name="payment_filter" class="form-control">
                        <option value="all"
                            <?php if (!isset($_GET['payment_filter']) || $_GET['payment_filter'] === 'all') echo 'selected'; ?>>
                            All</option>
                        <option value="pending"
                            <?php if (isset($_GET['payment_filter']) && $_GET['payment_filter'] === 'pending') echo 'selected'; ?>>
                            Pending</option>
                        <option value="clear"
                            <?php if (isset($_GET['payment_filter']) && $_GET['payment_filter'] === 'clear') echo 'selected'; ?>>
                            Clear</option>
                    </select>
                </div>
                <div>
                    <label>Recent Visits (days)</label><br>
                    <input type="number" name="recent_days" class="form-control" min="1" max="365"
                        value="<?php echo isset($_GET['recent_days']) ? (int)$_GET['recent_days'] : '' ?>"
                        placeholder="e.g. 30">
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-green" style="flex:1;">Apply</button>
                    <button type="button" id="closeFilterModal" class="btn btn-grey" style="flex:1;">Close</button>
                </div>
            </form>
        </div>
        <script>
        document.getElementById('openFilterModal').onclick = function() {
            document.getElementById('filterModal').style.display = 'block';
        };
        document.getElementById('closeFilterModal').onclick = function() {
            document.getElementById('filterModal').style.display = 'none';
        };
        // Hide modal on outside click
        window.addEventListener('click', function(e) {
            var modal = document.getElementById('filterModal');
            var btn = document.getElementById('openFilterModal');
            if (modal.style.display === 'block' && !modal.contains(e.target) && e.target !== btn && !btn
                .contains(e.target)) {
                modal.style.display = 'none';
            }
        });
        </script>
        <?php
        $rows = [];
        $where = ["membership_site_id=" . SITE_ID];
        // Visits filter
        $visits_filter = isset($_GET['visits_filter']) ? $_GET['visits_filter'] : 'with';
        if ($visits_filter === 'with') {
            $where[] = "membership_avl_visit > 0";
        } elseif ($visits_filter === 'without') {
            $where[] = "membership_avl_visit = 0";
        }
        // Payment filter
        if (isset($_GET['payment_filter']) && $_GET['payment_filter'] !== 'all') {
            if ($_GET['payment_filter'] === 'pending') {
                $where[] = "membership_amount_due > 0";
            } elseif ($_GET['payment_filter'] === 'clear') {
                $where[] = "membership_amount_due = 0";
            }
        }
        // Recent visits filter
        if (!empty($_GET['recent_days']) && is_numeric($_GET['recent_days'])) {
            $days = (int)$_GET['recent_days'];
            $recent_date = date('Y-m-d', strtotime("-{$days} days"));
            $where[] = "membership_last_visit >= '" . $recent_date . "'";
        }
        $sql = "SELECT membership_id, membership_profile_name, membership_total_visit, membership_avl_visit, user_mobile, membership_status, membership_price, membership_amount_due
        FROM " . TABLE_PREFIX . "membership
        INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
        INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY membership_profile_name ASC, membership_id DESC";

        if ($dataList = mysqli_query($connection, $sql)) {
            while ($r = mysqli_fetch_assoc($dataList)) {
                $rows[] = $r;
            }
        } else if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            echo "<pre>SQL Error: " . htmlspecialchars(mysqli_error($connection)) . "</pre>";
        }
        ?>
        <!-- Summary Info Cards -->
        <?php
        // All-time business summary
        $summary = [
            'total_sales' => 0,
            'total_sales_amount' => 0,
            'total_payment_received' => 0,
            'total_due_amount' => 0
        ];
        $summary_sql = "SELECT COUNT(*) as total_sales, SUM(membership_price) as total_sales_amount, SUM(membership_price-membership_amount_due) as total_payment_received, SUM(membership_amount_due) as total_due_amount FROM " . TABLE_PREFIX . "membership WHERE membership_site_id=" . SITE_ID;
        if ($res = mysqli_query($connection, $summary_sql)) {
            $summary = mysqli_fetch_assoc($res);
        }
        ?>
        <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:18px;">
            <div class="dashboard-summary-card"
                style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#f0f9ff 60%,#e0e7ef 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(20,33,61,0.06);">
                <div style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#0284c7;font-weight:700;">
                    Total Sales</div>
                <div style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#0f172a;margin-top:6px;">
                    <?php echo number_format($summary['total_sales']); ?> </div>
            </div>
            <div class="dashboard-summary-card"
                style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#fef9c3 60%,#fde68a 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(20,33,61,0.06);">
                <div style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#b45309;font-weight:700;">
                    Total Sales Amount</div>
                <div style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#78350f;margin-top:6px;">₹
                    <?php echo number_format($summary['total_sales_amount']); ?> </div>
            </div>
            <div class="dashboard-summary-card"
                style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#dcfce7 60%,#bbf7d0 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(20,33,61,0.06);">
                <div style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#15803d;font-weight:700;">
                    Payment Received</div>
                <div style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#166534;margin-top:6px;">₹
                    <?php echo number_format($summary['total_payment_received']); ?> </div>
            </div>
            <div class="dashboard-summary-card"
                style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#fee2e2 60%,#fecaca 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(20,33,61,0.06);">
                <div style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#b91c1c;font-weight:700;">
                    Due Amount</div>
                <div style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#991b1b;margin-top:6px;">₹
                    <?php echo number_format($summary['total_due_amount']); ?> </div>
            </div>
        </div>
        <div class="only-mobile membership-card-list">
            <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $data): ?>
            <?php
                    $id   = (int)$data['membership_id'];
                    $name = htmlspecialchars(ucwords($data['membership_profile_name'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $mob  = htmlspecialchars($data['user_mobile'] ?? '', ENT_QUOTES, 'UTF-8');
                    $membership_visits  = htmlspecialchars($data['membership_total_visit'] ?? '', ENT_QUOTES, 'UTF-8');
                    $membership_avl_visits  = htmlspecialchars($data['membership_avl_visit'] ?? '', ENT_QUOTES, 'UTF-8');
                    $membership_price = isset($data['membership_price']) ? number_format($data['membership_price']) : '0';
                    $membership_due = isset($data['membership_amount_due']) ? number_format($data['membership_amount_due']) : '0';
                    ?>
            <article class="widget-setting-info membership-card" data-id="<?php echo $id; ?>">
                <div class="about-feature" style="max-width:100%;position:relative;z-index:1;">
                    <h3 class="heading" style="margin:0;display:flex;justify-content:space-between;align-items:center;">
                        <span><?php echo $name; ?></span>
                        <span style="font-size:13px;color:#888;">#<?php echo $id; ?></span>
                    </h3>
                    <hr>
                    <div class="row">
                        <div class="box-12 info-stack">
                            <p style="margin:0;"><strong>Mobile</strong><br><span><?php echo $mob; ?></span></p>
                            <p style="margin:0;"><strong>Total
                                    Visits</strong><br><span><?php echo $membership_visits; ?></span></p>
                            <p style="margin:0;"><strong>Avl.
                                    Visits</strong><br><span><?php echo $membership_avl_visits; ?></span></p>
                        </div>
                    </div>
                    <!-- Actions stacked on new lines -->
                    <div class="action-stack" style="margin-top:10px;">
                        <div class="action-item">
                            <a class="btn btn-light dataTableBtn"
                                href="membership/update/?id=<?php echo base64_encode($id); ?>" title="View membership"
                                data-title="View membership">
                                <i class="fi-rr-pencil"></i> Edit
                            </a>
                        </div>
                        <div class="action-item">
                            <button class="btn btn-red dataTableBtn deleteButton" data-type="membership"
                                data-id="<?php echo $id; ?>" data-title="Delete membership">
                                <i class="fi-rr-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="empty-state"><i class="fi fi-rr-box-open"></i> No memberships found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.membership-card-list {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.membership-card-list .membership-card {
    flex: 0 0 calc(40% - 8px);
    max-width: calc(40% - 8px);
    margin-bottom: 16px;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #f0f9ff 60%, #e0e7ef 100%);
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(20, 33, 61, 0.06);
    border: 1px solid #e6ebf1;
    padding: 16px 18px;
}

.membership-card-list .membership-card .icon-bg {
    position: absolute;
    right: -18px;
    top: -18px;
    font-size: 80px;
    color: #38bdf8;
    opacity: 0.13;
    pointer-events: none;
    z-index: 0;
}

@media (max-width: 700px) {
    .membership-card-list {
        flex-direction: column;
        gap: 0;
    }

    .membership-card-list .membership-card {
        flex: 1 1 100%;
        max-width: 100%;
        margin-bottom: 16px;
    }
}
</style>