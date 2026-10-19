<h2 class="heading">Membership List
    <span>All membership list will be displayed here</span>
</h2>

<div class="main-box">
    <div id="loader" class="d-none text-center">
        <img src="<?php echo IMG_LIBRARY ?>circle-loader.gif" alt="Loading..." />
    </div>
    <div class="shortcut">
        <a href="/dashboard/membership/sell/"><i class="fi fi-rr-plus"></i></a>
    </div>

    <div class="list-box">
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
                <div>
                    <label for="mobile_filter">Mobile Number</label><br>
                    <input type="text" name="mobile_filter" id="mobile_filter" class="form-control"
                        placeholder="Enter mobile number"
                        value="<?php echo isset($_GET['mobile_filter']) ? htmlspecialchars($_GET['mobile_filter']) : ''; ?>">
                </div>
                <div>
                    <label for="payment_filter">Payment Status</label><br>
                    <select name="payment_filter" id="payment_filter" class="selectJS2 form-control">
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
        $rows = [];
        $where = ["membership_site_id=" . SITE_ID];
        // Pagination setup
        $per_page = 100;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $per_page;
        // Visits filter
        $visits_filter = isset($_GET['visits_filter']) ? $_GET['visits_filter'] : 'all';
        if ($visits_filter === 'with') {
            $where[] = "membership_avl_visit > 0";
        } elseif ($visits_filter === 'without') {
            $where[] = "membership_avl_visit = 0";
        }
        // Mobile number filter
        if (!empty($_GET['mobile_filter'])) {
            $mobile_val = mysqli_real_escape_string($connection, $_GET['mobile_filter']);
            $where[] = "user_mobile LIKE '%$mobile_val%'";
        }
        // Payment filter
        $payment_filter = isset($_GET['payment_filter']) ? $_GET['payment_filter'] : 'all';
        if ($payment_filter === 'pending') {
            $where[] = "membership_amount_due > 0";
        } elseif ($payment_filter === 'clear') {
            $where[] = "membership_amount_due = 0";
        }

        // Get total count for pagination
        $count_query = "SELECT COUNT(*) as total_count FROM " . TABLE_PREFIX . "membership
            INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
            INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
            WHERE " . implode(' AND ', $where);
        $total_count = 0;
        if ($count_res = mysqli_query($connection, $count_query)) {
            $count_row = mysqli_fetch_assoc($count_res);
            $total_count = (int)($count_row['total_count'] ?? 0);
        }

        $query = "SELECT membership_id, membership_profile_name, membership_total_visit, membership_avl_visit, user_mobile, membership_status, membership_price, membership_amount_due
        FROM " . TABLE_PREFIX . "membership
        INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
        INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY membership_profile_name ASC, membership_id DESC
        LIMIT $per_page OFFSET $offset";

        if ($dataList = mysqli_query($connection, $query)) {
            while ($r = mysqli_fetch_assoc($dataList)) {
                $rows[] = $r;
            }
        } else if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            echo "<pre>SQL Error: " . htmlspecialchars(mysqli_error($connection)) . "</pre>";
        }
        ?>
        <!-- Summary Info Cards -->
        <?php
        // Check if any filter is active
        $filter_active = ($visits_filter !== 'all') || (!empty($_GET['mobile_filter'])) || ($payment_filter !== 'all') || (isset($_GET['recent_days']) && $_GET['recent_days'] !== 'all' && $_GET['recent_days'] !== '');
        if ($filter_active) {
            // Filtered summary based on loaded $rows
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
        <div class="box-1">
            <div class="dashboard-summary-cards-row">
                <div class="dashboard-summary-card">
                    <span class="icon-bg"><i class="fi fi-rr-users"></i></span>
                    <div class="card-label">Total Sales</div>
                    <div class="card-value"><?php echo number_format($summary['total_sales'] ?? 0); ?></div>
                </div>
                <div class="dashboard-summary-card">
                    <span class="icon-bg"><i class="fi fi-rr-coins"></i></span>
                    <div class="card-label">Total Sales Amount</div>
                    <div class="card-value">₹ <?php echo number_format($summary['total_sales_amount'] ?? 0); ?></div>
                </div>
                <div class="dashboard-summary-card">
                    <span class="icon-bg"><i class="fi fi-rr-credit-card"></i></span>
                    <div class="card-label">Payment Received</div>
                    <div class="card-value">₹ <?php echo number_format($summary['total_payment_received'] ?? 0); ?></div>
                </div>
                <div class="dashboard-summary-card">
                    <span class="icon-bg"><i class="fi fi-rr-exclamation"></i></span>
                    <div class="card-label">Due Amount</div>
                    <div class="card-value">₹ <?php echo number_format($summary['total_due_amount'] ?? 0); ?></div>
                </div>
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
                    $membership_price = isset($data['membership_price']) ? $data['membership_price'] : 0;
                    $membership_due = isset($data['membership_amount_due']) ? $data['membership_amount_due'] : 0;
                    $encoded_id = base64_encode($id);
                    ?>

                    <article class="widget-setting-info membership-card" data-id="<?php echo $encoded_id; ?>">
                        <div class="about-feature">
                            <h3 class="heading name-cap">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p><strong><?php echo $name; ?></strong>
                                        <span>#<?php echo $id; ?></span>
                                    </p>
                                    <p class="v3-status-pill status-approved">
                                        ₹ <?php echo number_format($membership_price); ?></p>
                                </div>
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

                            <?php if ($membership_due > 0): ?>
                                <div class="membership-due-strip">
                                    <div class="due-label">Amount Due</div>
                                    <div class="due-value">₹ <?php echo number_format($membership_due); ?></div>
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
                                    <a href="#" class="btn btn-grey dataTableBtn showMemerbershipHistoryButton"
                                        data-id="<?php echo $encoded_id; ?>" title="Visit History">
                                        <i class="fi-rr-time-past"></i> History
                                    </a>
                                </div>
                                <div class="action-item">
                                    <a href="#" data-type="spa/membership" data-id="<?php echo $id; ?>"
                                        title="Delete membership" data-title="Delete membership"
                                        class="btn btn-red dataTableBtn deleteButton">
                                        <i class="fi fi-rr-trash bgRed"></i> Delete
                                    </a>
                                </div>
                            </div>

                            <?php if ($membership_due > 0): ?>
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
                    <div class="v3-empty-state-subtitle">Try adjusting your filters or add a new membership to get started.</div>
                    <a href="/dashboard/membership/sell/" class="btn btn-blue"><i class="fi fi-rr-plus"></i> Add Membership</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination Controls at the very bottom -->
        <?php
        // Helper to build URL with updated page param
        function buildPageUrl($page)
        {
            $params = $_GET;
            $params['page'] = $page;
            return strtok($_SERVER['REQUEST_URI'], '?') . '?' . http_build_query($params);
        }
        $has_next = ($page * $per_page) < $total_count;
        $has_prev = $page > 1;
        ?>
    </div>
</div>

<div class="pagination-bar">

    <!-- Back Button -->
    <div class="pagination-left">
        <?php if ($has_prev): ?>
            <a href="<?php echo htmlspecialchars(buildPageUrl($page - 1)); ?>" class="btn btn-grey">&laquo; Back</a>
        <?php else: ?>
            <span class="btn btn-grey disabled">&laquo; Back</span>
        <?php endif; ?>
    </div>

    <!-- Total Records -->
    <div class="pagination-center">
        <?php
        $start_record = $total_count > 0 ? ($offset + 1) : 0;
        $end_record = min($offset + $per_page, $total_count);
        ?>
        Showing<br /><?php echo number_format($start_record); ?> - <?php echo number_format($end_record); ?> of
        <?php echo number_format($total_count); ?>
    </div>

    <!-- Next Button -->
    <div class="pagination-right">
        <?php if ($has_next): ?>
            <a href="<?php echo htmlspecialchars(buildPageUrl($page + 1)); ?>" class="btn btn-blue">Next &raquo;</a>
        <?php else: ?>
            <span class="btn btn-blue disabled">Next &raquo;</span>
        <?php endif; ?>
    </div>

</div>

<div class="historyBoxMembership d-none">
    <h2 class="sub-heading">
        Visit Logs
    </h2>
    <div class="showVisitorHistory"></div>
    <div class="stepNavigation">
        <button type="button" class="btn btn-primary showListBox w-100">
            <i class="fi-rr-arrow-left"></i> Back
        </button>
    </div>
</div>
</div>