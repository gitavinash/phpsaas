<h2 class="heading">Membership Renewals
    <span>This page lists all members inactive memberships</span>
</h2>

<div class="main-box">
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
                    <label for="min_price">Membership Price (Min)</label><br>
                    <input type="number" name="min_price" id="min_price" class="form-control" placeholder="Min price"
                        value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                </div>
                <div>
                    <label for="max_price">Membership Price (Max)</label><br>
                    <input type="number" name="max_price" id="max_price" class="form-control" placeholder="Max price"
                        value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                </div>
                <div>
                    <label for="sort_price">Sort by Price</label><br>
                    <select name="sort_price" id="sort_price" class="selectJS2 form-control">
                        <option value="default"
                            <?php if (!isset($_GET['sort_price']) || $_GET['sort_price'] === 'default') echo 'selected'; ?>>
                            Default</option>
                        <option value="asc"
                            <?php if (isset($_GET['sort_price']) && $_GET['sort_price'] === 'asc') echo 'selected'; ?>>
                            Low to High</option>
                        <option value="desc"
                            <?php if (isset($_GET['sort_price']) && $_GET['sort_price'] === 'desc') echo 'selected'; ?>>
                            High to Low</option>
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
        // Only show members with no active visits and status != 1
        $where = ["membership_site_id=" . SITE_ID, "membership_avl_visit = 0", "membership_status != 1"];
        // Membership price filter
        if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
            $where[] = "membership_price >= " . (float)$_GET['min_price'];
        }
        if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
            $where[] = "membership_price <= " . (float)$_GET['max_price'];
        }
        // Sorting
        $order_by = 'membership_profile_name ASC, membership_id DESC';
        if (isset($_GET['sort_price'])) {
            if ($_GET['sort_price'] === 'asc') {
                $order_by = 'membership_price ASC';
            } elseif ($_GET['sort_price'] === 'desc') {
                $order_by = 'membership_price DESC';
            }
        }
        $sql = "SELECT membership_id, membership_profile_name, membership_total_visit, membership_avl_visit, user_mobile, membership_status, membership_price, membership_amount_due
        FROM " . TABLE_PREFIX . "membership
        INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
        INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
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
        // Always calculate metrics from the visible (filtered) $rows
        $summary = [
            'total_renewals' => 0,
            'total_renewal_value' => 0,
            'total_paid' => 0,
            'total_due' => 0
        ];
        foreach ($rows as $r) {
            $price = (float)($r['membership_price'] ?? 0);
            $due = (float)($r['membership_amount_due'] ?? 0);
            $summary['total_renewals']++;
            $summary['total_renewal_value'] += $price;
            $summary['total_due'] += $due;
            $summary['total_paid'] += ($price - $due);
        }
        ?>
        <div class="box-1">
            <div class="dashboard-summary-cards-row">
                <div class="dashboard-summary-card">
                    <span class="icon-bg"><i class="fi fi-rr-users"></i></span>
                    <div class="card-label">Total Renewals</div>
                    <div class="card-value"><?php echo number_format($summary['total_renewals']); ?></div>
                </div>
                <div class="dashboard-summary-card">
                    <span class="icon-bg"><i class="fi fi-rr-coins"></i></span>
                    <div class="card-label">Total Renewal Value</div>
                    <div class="card-value">₹ <?php echo number_format($summary['total_renewal_value']); ?></div>
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
                            <a href="#" class="btn btn-grey dataTableBtn visitHistoryButton"
                                data-id="<?php echo $encoded_id; ?>" title="Visit History">
                                <i class="fi-rr-time-past"></i> History
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
    </div>
</div>