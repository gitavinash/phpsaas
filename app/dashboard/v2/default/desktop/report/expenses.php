<style>
.expense-card-list {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.expense-card-list .expense-card {
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

.expense-card-list .expense-card .icon-bg {
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
    .expense-card-list {
        flex-direction: column;
        gap: 0;
    }

    .expense-card-list .expense-card {
        flex: 1 1 100%;
        max-width: 100%;
        margin-bottom: 16px;
    }
}
</style>
<h2 class="heading">Expense List
    <span>All expense records will be displayed here</span>
</h2>
<div class="main-box" style="padding: 0px;">
    <div class="shortcut">
        <a href="/dashboard/expenses/add/"><i class="fi fi-rr-plus"></i></a>
    </div>

    <!-- Floating Filter Button and Modal -->
    <button id="openFilterModal" type="button"
        style="position:fixed;left:10px;bottom:10%;z-index:1001;background:#2563eb;color:#fff;border:none;border-radius:50%;width:56px;height:56px;box-shadow:0 2px 8px rgba(0,0,0,0.18);font-size:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
        <i class="fi fi-rr-filter"></i>
    </button>
    <div id="filterModal"
        style="display:none;position:fixed;left:24px;bottom:90px;z-index:1002;background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(20,33,61,0.18);padding:24px 20px 16px 20px;min-width:260px;max-width:90vw;">
        <form method="get" id="filterForm" style="display:flex;flex-direction:column;gap:14px;">
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
                <select name="payment_mode" class="form-control" id="payment_mode">
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
                <select name="category" class="form-control" id="category">
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
                <select name="status" class="form-control" id="status">
                    <option value="">All</option>
                    <option value="1" <?php if (isset($_GET['status']) && $_GET['status'] === '1') echo 'selected'; ?>>
                        Approved</option>
                    <option value="0" <?php if (isset($_GET['status']) && $_GET['status'] === '0') echo 'selected'; ?>>
                        Pending</option>
                    <option value="2" <?php if (isset($_GET['status']) && $_GET['status'] === '2') echo 'selected'; ?>>
                        Reject</option>
                </select>
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
    window.addEventListener('click', function(e) {
        var modal = document.getElementById('filterModal');
        var btn = document.getElementById('openFilterModal');
        if (modal.style.display === 'block' && !modal.contains(e.target) && e.target !== btn && !btn.contains(e
                .target)) {
            modal.style.display = 'none';
        }
    });
    document.getElementById('filterForm').onsubmit = function() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    };
    </script>

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
    <div class="box-1" style="margin-bottom:12px;">
        <div class="dashboard-summary-cards-row">
            <div class="dashboard-summary-card"
                style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#f0f9ff 60%,#bae6fd 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(59,130,246,0.10);position:relative;overflow:hidden;">
                <span class="icon-bg"
                    style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#2563eb;opacity:0.13;pointer-events:none;z-index:0;"><i
                        class="fi fi-rr-list"></i></span>
                <div
                    style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#2563eb;font-weight:700;position:relative;z-index:1;">
                    Total Expense</div>
                <div
                    style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#2563eb;margin-top:6px;position:relative;z-index:1;">
                    <?php echo count($expenses); ?>
                </div>
            </div>
            <div class="dashboard-summary-card"
                style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#fef6e4 60%,#fde68a 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(251,191,36,0.10);position:relative;overflow:hidden;">
                <span class="icon-bg"
                    style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#f59e42;opacity:0.13;pointer-events:none;z-index:0;"><i
                        class="fi fi-rr-money-bill"></i></span>
                <div
                    style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#b26a00;font-weight:700;position:relative;z-index:1;">
                    Expense Amount</div>
                <div
                    style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#b26a00;margin-top:6px;position:relative;z-index:1;">
                    ₹ <?php echo number_format($totalExpense, 2); ?>
                </div>
            </div>
            <div class="dashboard-summary-card"
                style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#fef9c3 60%,#fde68a 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(20,33,61,0.06);position:relative;overflow:hidden;">
                <span class="icon-bg"
                    style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#facc15;opacity:0.13;pointer-events:none;z-index:0;"><i
                        class="fi fi-rr-apps"></i></span>
                <div
                    style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#b45309;font-weight:700;position:relative;z-index:1;">
                    Total Categories</div>
                <div
                    style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#78350f;margin-top:6px;position:relative;z-index:1;">
                    <?php echo $totalCategories; ?> </div>
            </div>
            <div class="dashboard-summary-card"
                style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#dcfce7 60%,#bbf7d0 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(20,33,61,0.06);position:relative;overflow:hidden;">
                <span class="icon-bg"
                    style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#22c55e;opacity:0.13;pointer-events:none;z-index:0;"><i
                        class="fi fi-rr-check"></i></span>
                <div
                    style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#15803d;font-weight:700;position:relative;z-index:1;">
                    Approved</div>
                <div
                    style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#166534;margin-top:6px;position:relative;z-index:1;">
                    ₹ <?php echo number_format($approvedAmt); ?>
                </div>
            </div>
            <div class="dashboard-summary-card"
                style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#fee2e2 60%,#fecaca 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(20,33,61,0.06);position:relative;overflow:hidden;">
                <span class="icon-bg"
                    style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#ef4444;opacity:0.13;pointer-events:none;z-index:0;"><i
                        class="fi fi-rr-exclamation"></i></span>
                <div
                    style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#b91c1c;font-weight:700;position:relative;z-index:1;">
                    Rejected</div>
                <div
                    style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#991b1b;margin-top:6px;position:relative;z-index:1;">
                    ₹ <?php echo number_format($rejectedAmt); ?>
                </div>
            </div>
            <div class="dashboard-summary-card"
                style="flex:0 0 calc(40% - 8px);background:linear-gradient(135deg,#fef6e4 60%,#fde68a 100%);border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(20,33,61,0.06);position:relative;overflow:hidden;">
                <span class="icon-bg"
                    style="position:absolute;right:-18px;top:-18px;font-size:80px;color:#f59e42;opacity:0.13;pointer-events:none;z-index:0;"><i
                        class="fi fi-rr-hourglass"></i></span>
                <div
                    style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#b26a00;font-weight:700;position:relative;z-index:1;">
                    Pending</div>
                <div
                    style="font-size:clamp(22px,3vw,30px);font-weight:900;color:#b26a00;margin-top:6px;position:relative;z-index:1;">
                    ₹ <?php echo number_format($pendingAmt); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    // Only define getExpenseStatus helper, do not re-query $expenses
    function getExpenseStatus($status)
    {
        switch ((int)$status) {
            case 1:
                return '<span style="color:#22c55e;font-weight:600">Approved</span>';
            case 2:
                return '<span style="color:#ef4444;font-weight:600">Reject</span>';
            default:
                return '<span style="color:#f59e42;font-weight:600">Pending</span>';
        }
    }
    ?>
    <?php if (!empty($expenses)): ?>
    <?php foreach ($expenses as $exp):
            extract($exp);
            $encoded_id = base64_encode($expense_id);
        ?>
    <article class="widget-setting-info membership-card" data-id="<?php echo $encoded_id; ?>">
        <div class="about-feature" style="max-width:100%;width:100%;">
            <h3 class="heading name-cap" style="margin:0;">
                <div style="display: flex; justify-content: space-between; width: 100%;">
                    <p style="margin-top: 10px;"><strong><?php echo $expense_category; ?></strong></p>
                    <?php
                            // Set background and text color based on status
                            $status = (int)$expense_status;
                            if ($status === 1) { // Approved
                                $amtBg = '#e6ffed';
                                $amtColor = '#15803d';
                            } elseif ($status === 2) { // Reject
                                $amtBg = '#ffe6e6';
                                $amtColor = '#b91c1c';
                            } else { // Pending
                                $amtBg = '#fef6e4'; // improved: soft orange/peach
                                $amtColor = '#b26a00'; // deeper amber
                            }
                            ?>
                    <p
                        style="background-color: <?php echo $amtBg; ?>; padding: 0px 12px; border-radius: 4px; font-weight: 700; line-height: 44px; font-size: 18px; text-align: center; min-width: 100px; color: <?php echo $amtColor; ?>;">
                        ₹ <?php echo number_format($expense_amount); ?></p>
                </div>
            </h3>
            <hr>
            <div class="row">
                <div class="box-12 info-stack">
                    <p style="margin:0;">
                        <strong>Description</strong><br>
                        <span><?php echo $expense_description; ?></span>
                    </p>
                    <p style="margin:0;">
                        <strong>Payment mode</strong><br>
                        <span><?php echo $expense_payment_mode; ?></span>
                    </p>
                </div>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="empty-state"
        style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 0 32px 0;opacity:0.85;">
        <div style="font-size:54px;color:#38bdf8;margin-bottom:12px;"><i class="fi fi-rr-box-open"></i></div>
        <div style="font-size:1.3rem;font-weight:600;color:#334155;margin-bottom:6px;">No expenses found</div>
        <div style="font-size:1rem;color:#64748b;margin-bottom:18px;">Try adding a new expense to get started.</div>
        <a href="/dashboard/expenses/add/" class="btn btn-blue"
            style="padding:8px 22px;font-size:1rem;border-radius:6px;margin-top:8px;"><i class="fi fi-rr-plus"></i>
            Add Expense</a>
    </div>
    <?php endif; ?>
</div>
</div>