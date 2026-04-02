<h2 class="heading">Membership List
    <span>All membership list will be displayed here</span>
</h2>

<div class="main-box" style="padding: 0px;">
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
            <form method="get" id="filterForm" style="display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label for="visits_filter">Visits Available</label><br>
                    <select name="visits_filter" id="visits_filter" class="form-control">
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
                <div>
                    <label for="payment_filter">Payment Status</label><br>
                    <select name="payment_filter" id="payment_filter" class="form-control">
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
                    <label for="recent_days">Recent Visits</label><br>
                    <select name="recent_days" id="recent_days" class="form-control"
                        onchange="document.getElementById('customRecentDays').style.display = (this.value === 'custom' ? 'block' : 'none');">
                        <option value="all"
                            <?php if (!isset($_GET['recent_days']) || $_GET['recent_days'] === 'all' || $_GET['recent_days'] === '') echo 'selected'; ?>>
                            All</option>
                        <option value="7"
                            <?php if (isset($_GET['recent_days']) && $_GET['recent_days'] == '7') echo 'selected'; ?>>
                            Last 7 days</option>
                        <option value="15"
                            <?php if (isset($_GET['recent_days']) && $_GET['recent_days'] == '15') echo 'selected'; ?>>
                            Last 15 days</option>
                        <option value="30"
                            <?php if (isset($_GET['recent_days']) && $_GET['recent_days'] == '30') echo 'selected'; ?>>
                            Last 30 days</option>
                        <option value="60"
                            <?php if (isset($_GET['recent_days']) && $_GET['recent_days'] == '60') echo 'selected'; ?>>
                            Last 60 days</option>
                        <option value="custom"
                            <?php if (isset($_GET['recent_days']) && !in_array($_GET['recent_days'], ['all', '7', '15', '30', '60'])) echo 'selected'; ?>>
                            Custom</option>
                    </select>
                    <input type="number" name="custom_recent_days" id="customRecentDays" class="form-control" min="1"
                        max="365" placeholder="Enter days"
                        style="margin-top:6px;display:<?php echo (isset($_GET['recent_days']) && !in_array($_GET['recent_days'], ['all', '7', '15', '30', '60'])) ? 'block' : 'none'; ?>;"
                        value="<?php echo (isset($_GET['recent_days']) && !in_array($_GET['recent_days'], ['all', '7', '15', '30', '60'])) ? (int)$_GET['recent_days'] : ''; ?>">
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-green" style="flex:1;">Apply</button>
                    <button type="button" id="closeFilterModal" class="btn btn-grey" style="flex:1;">Close</button>
                </div>
            </form>
        </div>
        <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s;
        }

        .loading-spinner {
            width: 56px;
            height: 56px;
            border: 6px solid #2563eb;
            border-top: 6px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
        </style>
        <div id="loadingOverlay" class="loading-overlay" style="display:none;">
            <div class="loading-spinner"></div>
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
        // Show loading overlay on filter apply
        document.getElementById('filterForm').onsubmit = function() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        };
        </script>
        <?php
        $rows = [];
        $where = ["membership_site_id=" . SITE_ID];
        // Visits filter
        $visits_filter = isset($_GET['visits_filter']) ? $_GET['visits_filter'] : 'all';
        if ($visits_filter === 'with') {
            $where[] = "membership_avl_visit > 0";
        } elseif ($visits_filter === 'without') {
            $where[] = "membership_avl_visit = 0";
        }
        // Payment filter
        $payment_filter = isset($_GET['payment_filter']) ? $_GET['payment_filter'] : 'all';
        if ($payment_filter === 'pending') {
            $where[] = "membership_amount_due > 0";
        } elseif ($payment_filter === 'clear') {
            $where[] = "membership_amount_due = 0";
        }
        // Recent visits filter
        $recent_days = isset($_GET['recent_days']) ? $_GET['recent_days'] : 'all';
        if ($recent_days === 'custom' && isset($_GET['custom_recent_days']) && is_numeric($_GET['custom_recent_days'])) {
            $days = (int)$_GET['custom_recent_days'];
        } elseif (is_numeric($recent_days)) {
            $days = (int)$recent_days;
        } else {
            $days = false;
        }
        $join_visits = '';
        $order_by = 'membership_profile_name ASC, membership_id DESC';
        if ($days) {
            $recent_date = date('Y-m-d', strtotime("-{$days} days"));
            $join_visits = "LEFT JOIN " . TABLE_PREFIX . "membership_visits ON membership_id = visit_membership_id";
            $where[] = "visit_time >= '" . $recent_date . "'";
            $order_by = 'visit_time DESC';
        }
        $sql = "SELECT membership_id, membership_profile_name, membership_total_visit, membership_avl_visit, user_mobile, membership_status, membership_price, membership_amount_due
        FROM " . TABLE_PREFIX . "membership
        INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
        INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
        $join_visits
        WHERE " . implode(' AND ', $where) . "
        ORDER BY $order_by";

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
        // Calculate summary based on filter
        $filter_active = (
            (isset($_GET['visits_filter']) && $_GET['visits_filter'] !== 'all') ||
            (isset($_GET['payment_filter']) && $_GET['payment_filter'] !== 'all') ||
            (isset($_GET['recent_days']) && $_GET['recent_days'] !== 'all' && $_GET['recent_days'] !== '')
        );
        if ($filter_active) {
            // Calculate from filtered $rows
            $summary = [
                'total_sales' => count($rows),
                'total_sales_amount' => 0,
                'total_payment_received' => 0,
                'total_due_amount' => 0
            ];
            foreach ($rows as $r) {
                $price = (float)($r['membership_price'] ?? 0);
                $due = (float)($r['membership_amount_due'] ?? 0);
                $summary['total_sales_amount'] += $price;
                $summary['total_due_amount'] += $due;
                $summary['total_payment_received'] += ($price - $due);
            }
        } else {
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
        }
        ?>
        <div class="box-1" style="margin-bottom:12px;">
            <div class="dashboard-summary-cards-row">
                <div class="dashboard-summary-card"
                    style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#f0f9ff 60%,#e0e7ef 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(20,33,61,0.06);position:relative;overflow:hidden;<?php if ($filter_active) echo 'border:2px solid #2563eb;'; ?>">
                    <span class="icon-bg"
                        style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#38bdf8;opacity:0.13;pointer-events:none;z-index:0;"><i
                            class="fi fi-rr-users"></i></span>
                    <div
                        style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#0284c7;font-weight:700;position:relative;z-index:1;">
                        Total Sales</div>
                    <div
                        style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#0f172a;margin-top:6px;position:relative;z-index:1;">
                        <?php echo number_format($summary['total_sales']); ?> </div>
                </div>
                <div class="dashboard-summary-card"
                    style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#fef9c3 60%,#fde68a 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(20,33,61,0.06);position:relative;overflow:hidden;<?php if ($filter_active) echo 'border:2px solid #facc15;'; ?>">
                    <span class="icon-bg"
                        style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#facc15;opacity:0.13;pointer-events:none;z-index:0;"><i
                            class="fi fi-rr-coins"></i></span>
                    <div
                        style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#b45309;font-weight:700;position:relative;z-index:1;">
                        Total Sales Amount</div>
                    <div
                        style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#78350f;margin-top:6px;position:relative;z-index:1;">
                        ₹
                        <?php echo number_format($summary['total_sales_amount']); ?> </div>
                </div>
                <div class="dashboard-summary-card"
                    style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#dcfce7 60%,#bbf7d0 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(20,33,61,0.06);position:relative;overflow:hidden;<?php if ($filter_active) echo 'border:2px solid #22c55e;'; ?>">
                    <span class="icon-bg"
                        style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#22c55e;opacity:0.13;pointer-events:none;z-index:0;"><i
                            class="fi fi-rr-credit-card"></i></span>
                    <div
                        style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#15803d;font-weight:700;position:relative;z-index:1;">
                        Payment Received</div>
                    <div
                        style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#166534;margin-top:6px;position:relative;z-index:1;">
                        ₹
                        <?php echo number_format($summary['total_payment_received']); ?> </div>
                </div>
                <div class="dashboard-summary-card"
                    style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#fee2e2 60%,#fecaca 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(20,33,61,0.06);position:relative;overflow:hidden;<?php if ($filter_active) echo 'border:2px solid #ef4444;'; ?>">
                    <span class="icon-bg"
                        style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#ef4444;opacity:0.13;pointer-events:none;z-index:0;"><i
                            class="fi fi-rr-exclamation"></i></span>
                    <div
                        style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#b91c1c;font-weight:700;position:relative;z-index:1;">
                        Due Amount</div>
                    <div
                        style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#991b1b;margin-top:6px;position:relative;z-index:1;">
                        ₹
                        <?php echo number_format($summary['total_due_amount']); ?> </div>
                </div>
            </div>
        </div>
        <div class="only-mobile membership-card-list" style="float: left;">
            <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $data): ?>
            <?php
                    $id   = (int)$data['membership_id'];
                    $name = htmlspecialchars(ucwords($data['membership_profile_name'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $mob  = htmlspecialchars($data['user_mobile'] ?? '', ENT_QUOTES, 'UTF-8');
                    $membership_visits  = htmlspecialchars($data['membership_total_visit'] ?? '', ENT_QUOTES, 'UTF-8');
                    $membership_avl_visits  = htmlspecialchars($data['membership_avl_visit'] ?? '', ENT_QUOTES, 'UTF-8');
                    $membership_price = isset($data['membership_price']) ? $data['membership_price'] : 0;
                    $membership_due = isset($data['membership_amount_due']) ? $data['membership_amount_due'] : 0;
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
                                ₹ <?php echo number_format($membership_price); ?></p>
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

                    <?php if ($membership_due > 0): ?>
                    <div class="membership-due-strip">
                        <div style="font-size:13px;opacity:0.9;">Amount Due</div>
                        <div style="font-size:24px; line-height:1.1; font-weight:800;">₹
                            <?php echo number_format($membership_due); ?></div>
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

                    <?php if ($membership_due > 0): ?>
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
            <div class="empty-state"
                style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 0 32px 0;opacity:0.85;">
                <div style="font-size:54px;color:#38bdf8;margin-bottom:12px;"><i class="fi fi-rr-box-open"></i></div>
                <div style="font-size:1.3rem;font-weight:600;color:#334155;margin-bottom:6px;">No memberships found
                </div>
                <div style="font-size:1rem;color:#64748b;margin-bottom:18px;">Try adjusting your filters or add a new
                    membership to get started.</div>
                <a href="/dashboard/membership/sell/" class="btn btn-blue"
                    style="padding:8px 22px;font-size:1rem;border-radius:6px;margin-top:8px;"><i
                        class="fi fi-rr-plus"></i> Add Membership</a>
            </div>
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