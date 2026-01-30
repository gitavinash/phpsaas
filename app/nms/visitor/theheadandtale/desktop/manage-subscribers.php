<?php

require_once LOGIN_CHECK;

if ($checkSubscription = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "corporate_subscription WHERE cp_admin_user_id=" . USER_ID . " AND cp_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkSubscription) != 0) {
        $subscriptionStatus = 'active';
        extract(mysqli_fetch_assoc($checkSubscription));
    } else {
        $subscriptionStatus = 'inactive';
    }
}

require_once 'header.php';

// Sample data; replace with real data source.
$planSummary = [
    'plan_name' => 'Corporate Annual',
    'total_licenses' => 120,
    'active_licenses' => 96,
    'inactive_licenses' => 24,
    'expiry_date' => '2026-12-31',
];

$subscribers = [
    ['name' => 'Aisha Verma', 'email' => 'aisha.verma@example.com', 'status' => 'Active'],
    ['name' => 'Ravi Kumar', 'email' => 'ravi.kumar@example.com', 'status' => 'Inactive'],
    ['name' => 'Meera Iyer', 'email' => 'meera.iyer@example.com', 'status' => 'Active'],
];

$currentPage = 1;
$perPage = 20;
$totalSubscribers = count($subscribers);
$totalPages = max(1, (int)ceil($totalSubscribers / $perPage));

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

?>

<style>
:root {
    --bg: #f6f8fb;
    --panel: #ffffff;
    --panel-soft: #f9fbff;
    --stroke: #e5e9f0;
    --text: #0f172a;
    --muted: #5b6475;
    --accent: #ec1816;
    --accent-soft: #ffe9e7;
    --success: #1fa37c;
    --warn: #c56b12;
    --shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
    --radius: 14px;
    --font: "Inter", "Segoe UI", -apple-system, sans-serif;
}

.page-shell {
    background: radial-gradient(circle at 18% 18%, rgba(47, 111, 237, 0.08), transparent 32%),
        radial-gradient(circle at 82% 8%, rgba(31, 163, 124, 0.06), transparent 26%),
        linear-gradient(180deg, #f8fbff 0%, #f5f7fb 70%, #f6f8fb 100%);
    min-height: 100vh;
    padding: 60px 0 90px;
    font-family: var(--font);
    color: var(--text);
}

.page-wrap {
    width: min(1180px, 100%);
    margin: 0 auto;
    padding: 0 22px;
}

.hero {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    align-items: center;
    margin-bottom: 22px;
}

.hero h1 {
    margin: 0 0 6px;
    font-size: 30px;
    letter-spacing: -0.4px;
    font-weight: 800;
}

.hero p {
    margin: 0;
    color: var(--muted);
    font-size: 15px;
}

.cta-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 8px 15px;
    background: linear-gradient(120deg, #ec1816 0%, #d31412 100%);
    color: #fff;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 800;
    letter-spacing: 0.2px;
    box-shadow: var(--shadow);
}

.panel {
    background: var(--panel);
    border: 1px solid var(--stroke);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.panel-head {
    padding: 18px 20px;
    border-bottom: 1px solid var(--stroke);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.panel-head h3 {
    margin: 0;
    font-size: 18px;
    letter-spacing: -0.2px;
}

.pill {
    padding: 8px 10px;
    border-radius: 999px;
    background: var(--panel-soft);
    border: 1px solid var(--stroke);
    color: var(--muted);
    font-size: 13px;
}

.subhead {
    margin: 4px 0 0;
    color: var(--muted);
    font-size: 13px;
}

.panel-body {
    padding: 20px;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 12px;
}

.metric {
    background: var(--panel-soft);
    border: 1px solid var(--stroke);
    border-radius: 12px;
    padding: 12px;
}

.metric-label {
    font-size: 11px;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 6px;
}

.metric-value {
    font-size: 20px;
    font-weight: 800;
}

.metric.good {
    color: var(--success);
}

.metric.warn {
    color: var(--warn);
}

.grid-2 {
    display: grid;
    grid-template-columns: 1.05fr 0.95fr;
    gap: 18px;
    margin-top: 18px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 14px;
}

.field label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 6px;
    color: var(--text);
}

.field input {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid var(--stroke);
    background: #fff;
    color: var(--text);
    font-size: 15px;
}

.actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-secondary {
    padding: 11px 14px;
    border-radius: 10px;
    border: 1px solid var(--stroke);
    background: #fff;
    color: var(--text);
    font-weight: 700;
    cursor: pointer;
}

.btn-secondary:hover {
    border-color: var(--accent);
    color: var(--accent);
}

.cta-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 18px 38px rgba(236, 24, 22, 0.25);
}

.filters {
    display: grid;
    grid-template-columns: 1fr 0.38fr 0.38fr;
    gap: 10px;
    margin-bottom: 12px;
}

.filters input,
.filters select {
    width: 100%;
    padding: 12px 12px;
    border-radius: 10px;
    border: 1px solid var(--stroke);
    background: #fff;
    color: var(--text);
    font-size: 14px;
}

.list-stats {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

.table-wrap {
    border: 1px solid var(--stroke);
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
}

table.subs-table {
    width: 100%;
    border-collapse: collapse;
    color: var(--text);
    font-size: 14px;
}

table.subs-table th,
table.subs-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--stroke);
    text-align: left;
}

table.subs-table th {
    font-size: 12px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: var(--muted);
    background: var(--panel-soft);
    position: sticky;
    top: 0;
    z-index: 2;
}

.name-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--accent-soft);
    border: 1px solid var(--stroke);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: var(--accent);
}

.badge {
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}

.badge.active {
    background: #e8f7f1;
    color: var(--success);
}

.badge.inactive {
    background: #fff4e8;
    color: var(--warn);
}

.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
    color: var(--muted);
    font-size: 13px;
}

.pager-buttons {
    display: inline-flex;
    gap: 8px;
}

.pager-btn {
    padding: 9px 12px;
    border-radius: 9px;
    border: 1px solid var(--stroke);
    background: #fff;
    color: var(--text);
    font-weight: 700;
    cursor: pointer;
}

.pager-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

@media (max-width: 1100px) {
    .metrics-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .grid-2 {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 780px) {
    .hero {
        flex-direction: column;
        align-items: flex-start;
    }

    .metrics-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .filters {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 540px) {
    .metrics-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php

if ($subscriptionStatus == 'active') { ?>
<section class="page-shell">
    <div class="page-wrap">
        <div class="hero">
            <div>
                <h1>Subscriber Desk</h1>
                <p>Allocate seats, invite teammates, and keep your corporate plan healthy.</p>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <div>
                    <h3>Plan Snapshot</h3>
                    <p class="subhead">Plan: <?php echo h($planSummary['plan_name']); ?> · Expires
                        <?php echo h($planSummary['expiry_date']); ?></p>
                </div>
                <span class="pill">Expiry <?php echo h($planSummary['expiry_date']); ?></span>
            </div>
            <div class="panel-body">
                <div class="metrics-grid">
                    <div class="metric">
                        <div class="metric-label">Expiry Date</div>
                        <div class="metric-value"><?php echo h($planSummary['expiry_date']); ?></div>
                    </div>
                    <div class="metric">
                        <div class="metric-label">Total Seats</div>
                        <div class="metric-value"><?php echo h((string)$planSummary['total_licenses']); ?></div>
                    </div>
                    <div class="metric good">
                        <div class="metric-label">Active</div>
                        <div class="metric-value"><?php echo h((string)$planSummary['active_licenses']); ?></div>
                    </div>
                    <div class="metric warn">
                        <div class="metric-label">Inactive</div>
                        <div class="metric-value"><?php echo h((string)$planSummary['inactive_licenses']); ?></div>
                    </div>
                    <div class="metric">
                        <div class="metric-label">Utilization</div>
                        <div class="metric-value">
                            <?php
                                $util = $planSummary['total_licenses'] > 0
                                    ? round(($planSummary['active_licenses'] / $planSummary['total_licenses']) * 100)
                                    : 0;
                                echo h((string)$util) . '%';
                                ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-2">
            <div class="panel">
                <div class="panel-head">
                    <h3>Add Subscriber</h3>
                    <span class="pill">Seat available</span>
                </div>
                <div class="panel-body">
                    <form method="POST" action="">
                        <div class="form-grid">
                            <div class="field">
                                <label for="subscriber_name">Full Name</label>
                                <input type="text" id="subscriber_name" name="subscriber_name" placeholder="Enter name"
                                    required>
                            </div>
                            <div class="field">
                                <label for="subscriber_email">Email</label>
                                <input type="email" id="subscriber_email" name="subscriber_email"
                                    placeholder="name@company.com" required>
                            </div>
                        </div>
                        <div class="actions">
                            <button class="cta-primary" type="submit">Invite Now</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h3>Subscribers</h3>
                    <span class="pill">Total <?php echo h((string)$totalSubscribers); ?></span>
                </div>
                <div class="panel-body">
                    <div class="filters">
                        <input type="search" name="q" placeholder="Search by name or email">
                        <select name="status_filter">
                            <option value="">All statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <select name="per_page">
                            <option value="20">20 per page</option>
                            <option value="50">50 per page</option>
                            <option value="100">100 per page</option>
                        </select>
                    </div>

                    <div class="list-stats">
                        <span class="pill">Active: <?php echo h((string)$planSummary['active_licenses']); ?></span>
                        <span class="pill">Inactive: <?php echo h((string)$planSummary['inactive_licenses']); ?></span>
                    </div>

                    <div class="table-wrap">
                        <table class="subs-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subscribers as $sub): ?>
                                <tr>
                                    <td>
                                        <span class="name-cell">
                                            <span
                                                class="avatar"><?php echo h(strtoupper(substr($sub['name'], 0, 1))); ?></span>
                                            <?php echo h($sub['name']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo h($sub['email']); ?></td>
                                    <td>
                                        <?php $isActive = strtolower($sub['status']) === 'active'; ?>
                                        <span
                                            class="badge <?php echo $isActive ? 'active' : 'inactive'; ?>"><?php echo $isActive ? 'Active' : 'Inactive'; ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination">
                        <span>Page <?php echo h((string)$currentPage); ?> of
                            <?php echo h((string)$totalPages); ?></span>
                        <div class="pager-buttons">
                            <button class="pager-btn" <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>>Prev</button>
                            <button class="pager-btn"
                                <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>>Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
} else {
?>
<section class="page-shell">
    <div class="page-wrap">
        <div class="panel" style="text-align: center; padding: 60px 20px;">
            <h2>No Active Corporate Subscription</h2>
            <br />
            <p style="color: var(--muted); margin-bottom: 20px;">It seems you do not have an active corporate
                subscription. To access all features, please consider purchasing a subscription. You will be redirected
                to the subscription page shortly...</p>
        </div>
    </div>
</section>
<script>
setTimeout(() => {
    window.location.href = '/corporate-subscription/';
}, 3000);
</script>
<?php
}
require_once 'footer.php';