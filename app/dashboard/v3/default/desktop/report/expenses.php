<h2 class="heading">Expense List
    <span>All expense records will be displayed here</span>
</h2>
<div class="main-box">
    <div class="shortcut">
        <a href="/dashboard/expenses/add/"><i class="fi fi-rr-plus"></i></a>
    </div>

    <!-- Floating Filter Button and Modal -->
    <button id="openFilterModal" type="button" class="btn-floating-filter" aria-label="Open filter">
        <i class="fi fi-rr-filter"></i>
    </button>
    <div id="filterModal" class="floating-filter-modal">
        <form method="get" id="filterForm" class="floating-filter-form">
            <div>
                <label for="date_from">From Date</label><br>
                <input type="date" name="date_from" id="date_from"
                    value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
            </div>
            <div>
                <label for="date_to">To Date</label><br>
                <input type="date" name="date_to" id="date_to"
                    value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
            </div>
            <?php
            // Get unique payment modes and categories for dropdowns
            $paymentModes = [];
            $categoriesList = [];
            $pmRes = mysqli_query($connection, "SELECT DISTINCT expense_payment_mode FROM " . TABLE_PREFIX . "expense WHERE expense_site_id=" . SITE_ID . " AND expense_payment_mode IS NOT NULL AND expense_payment_mode != ''");
            while ($row = mysqli_fetch_assoc($pmRes)) {
                $paymentModes[] = $row['expense_payment_mode'];
            }
            $catRes = mysqli_query($connection, "SELECT DISTINCT expense_category FROM " . TABLE_PREFIX . "expense WHERE expense_site_id=" . SITE_ID . " AND expense_category IS NOT NULL AND expense_category != ''");
            while ($row = mysqli_fetch_assoc($catRes)) {
                $categoriesList[] = $row['expense_category'];
            }
            ?>
            <div>
                <label for="payment_mode">Payment Mode</label><br>
                <select name="payment_mode" class="selectJS2 form-control" id="payment_mode">
                    <option value="">All</option>
                    <?php foreach ($paymentModes as $pm): ?>
                    <option value="<?php echo htmlspecialchars($pm); ?>"
                        <?php if (isset($_GET['payment_mode']) && $_GET['payment_mode'] === $pm) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($pm); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="category">Category</label><br>
                <select name="category" class="selectJS2 form-control" id="category">
                    <option value="">All</option>
                    <?php foreach ($categoriesList as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>"
                        <?php if (isset($_GET['category']) && $_GET['category'] === $cat) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="status">Status</label><br>
                <select name="status" class="selectJS2 form-control" id="status">
                    <option value="">All</option>
                    <option value="1" <?php if (isset($_GET['status']) && $_GET['status'] === '1') echo 'selected'; ?>>
                        Approved</option>
                    <option value="0" <?php if (isset($_GET['status']) && $_GET['status'] === '0') echo 'selected'; ?>>
                        Pending</option>
                    <option value="2" <?php if (isset($_GET['status']) && $_GET['status'] === '2') echo 'selected'; ?>>
                        Reject</option>
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
    // Build expense filter logic
    $expenses = [];
    $where = ["expense_site_id=" . SITE_ID];
    if (!empty($_GET['date_from'])) {
        $where[] = "DATE(expense_time) >= '" . mysqli_real_escape_string($connection, $_GET['date_from']) . "'";
    }
    if (!empty($_GET['date_to'])) {
        $where[] = "DATE(expense_time) <= '" . mysqli_real_escape_string($connection, $_GET['date_to']) . "'";
    }
    if (!empty($_GET['payment_mode'])) {
        $where[] = "expense_payment_mode LIKE '%" . mysqli_real_escape_string($connection, $_GET['payment_mode']) . "%'";
    }
    if (!empty($_GET['category'])) {
        $where[] = "expense_category LIKE '%" . mysqli_real_escape_string($connection, $_GET['category']) . "%'";
    }
    if (isset($_GET['status']) && $_GET['status'] !== '') {
        $where[] = "expense_status = '" . (int)$_GET['status'] . "'";
    }
    $sql = "SELECT expense_id, expense_amount, expense_category, expense_description, expense_payment_mode, expense_photo, expense_time, expense_status FROM " . TABLE_PREFIX . "expense WHERE " . implode(' AND ', $where) . " ORDER BY expense_time DESC LIMIT 200";
    if ($result = mysqli_query($connection, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $expenses[] = $row;
        }
    }
    // Stats for summary cards
    $totalExpense = 0;
    $categories = [];
    $approved = $pending = $rejected = 0;
    $approvedAmt = $pendingAmt = $rejectedAmt = 0;
    foreach ($expenses as $exp) {
        $amt = (float)$exp['expense_amount'];
        $totalExpense += $amt;
        $categories[$exp['expense_category']] = true;
        if ($exp['expense_status'] == 1) {
            $approved++;
            $approvedAmt += $amt;
        } elseif ($exp['expense_status'] == 2) {
            $rejected++;
            $rejectedAmt += $amt;
        } else {
            $pending++;
            $pendingAmt += $amt;
        }
    }
    $totalCategories = count($categories);
    ?>
    <!-- Summary Info Cards -->
    <div class="box-1">
        <div class="dashboard-summary-cards-row">
            <div class="dashboard-summary-card">
                <span class="icon-bg"><i class="fi fi-rr-list"></i></span>
                <div class="card-label">Total Expense</div>
                <div class="card-value"><?php echo count($expenses); ?></div>
            </div>
            <div class="dashboard-summary-card">
                <span class="icon-bg"><i class="fi fi-rr-money-bill"></i></span>
                <div class="card-label">Expense Amount</div>
                <div class="card-value">₹ <?php echo number_format($totalExpense, 2); ?></div>
            </div>
            <div class="dashboard-summary-card">
                <span class="icon-bg"><i class="fi fi-rr-apps"></i></span>
                <div class="card-label">Total Categories</div>
                <div class="card-value"><?php echo $totalCategories; ?></div>
            </div>
            <div class="dashboard-summary-card">
                <span class="icon-bg"><i class="fi fi-rr-check"></i></span>
                <div class="card-label">Approved</div>
                <div class="card-value">₹ <?php echo number_format($approvedAmt); ?></div>
            </div>
            <div class="dashboard-summary-card">
                <span class="icon-bg"><i class="fi fi-rr-exclamation"></i></span>
                <div class="card-label">Rejected</div>
                <div class="card-value">₹ <?php echo number_format($rejectedAmt); ?></div>
            </div>
            <div class="dashboard-summary-card">
                <span class="icon-bg"><i class="fi fi-rr-hourglass"></i></span>
                <div class="card-label">Pending</div>
                <div class="card-value">₹ <?php echo number_format($pendingAmt); ?></div>
            </div>
        </div>
    </div>
    <?php
    // Helper function with standard status classes
    function getExpenseStatus($status)
    {
        switch ((int)$status) {
            case 1:
                return '<span class="status-text-approved">Approved</span>';
            case 2:
                return '<span class="status-text-rejected">Reject</span>';
            default:
                return '<span class="status-text-pending">Pending</span>';
        }
    }
    ?>
    <?php if (!empty($expenses)): ?>
    <div class="expense-card-list">
    <?php foreach ($expenses as $exp):
            extract($exp);
            $encoded_id = base64_encode($expense_id);
            $status = (int)$expense_status;
            if ($status === 1) {
                $statusClass = 'status-approved';
            } elseif ($status === 2) {
                $statusClass = 'status-rejected';
            } else {
                $statusClass = 'status-pending';
            }
        ?>
    <article class="widget-setting-info membership-card" data-id="<?php echo $encoded_id; ?>">
        <div class="about-feature">
            <h3 class="heading name-cap">
                <div class="d-flex justify-content-between align-items-center">
                    <p><strong><?php echo $expense_category; ?></strong></p>
                    <p class="v3-status-pill <?php echo $statusClass; ?>">
                        ₹ <?php echo number_format($expense_amount); ?>
                    </p>
                </div>
            </h3>
            <hr>
            <div class="row">
                <div class="box-12 info-stack">
                    <p>
                        <strong>Description</strong><br>
                        <span><?php echo $expense_description; ?></span>
                    </p>
                    <p>
                        <strong>Payment mode</strong><br>
                        <span><?php echo $expense_payment_mode; ?></span>
                    </p>
                </div>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="v3-empty-state">
        <div class="v3-empty-state-icon"><i class="fi fi-rr-box-open"></i></div>
        <div class="v3-empty-state-title">No expenses found</div>
        <div class="v3-empty-state-subtitle">Try adding a new expense to get started.</div>
        <a href="/dashboard/expenses/add/" class="btn btn-blue"><i class="fi fi-rr-plus"></i> Add Expense</a>
    </div>
    <?php endif; ?>
</div>