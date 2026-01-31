<?php

require_once LOGIN_CHECK;

$subscriberListCount = 0;
$subscribers = [];
$currentPage = 1;
$totalPages = 1;
if ($checkSubscription = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "corporate_subscription WHERE cp_admin_user_id=" . USER_ID . " AND cp_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkSubscription) != 0) {
        $subscriptionStatus = 'active';
        extract(mysqli_fetch_assoc($checkSubscription));
        $subscriberListFound = false;
        if ($subscriberList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "subscription INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=subscription_user_id WHERE subscription_refer_by='{$cp_admin_user_id}' AND subscription_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($subscriberList) != 0) {
                $subscriberListFound = true;
                $subscriberListCount = mysqli_num_rows($subscriberList);
                while ($subRow = mysqli_fetch_assoc($subscriberList)) {
                    $subscribers[] = $subRow;
                }

                $totalPages = ceil($subscriberListCount / 10);
            }
        }
    } else {
        $subscriptionStatus = 'inactive';
    }
}

require_once 'header.php';

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
    padding: 40px 0 60px;
    font-family: var(--font);
    color: var(--text);
}

.page-wrap {
    width: min(1300px, 100%);
    margin: 0 auto;
    padding: 0 18px;
}

.hero {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    align-items: center;
    margin-bottom: 16px;
}

.hero h1 {
    margin: 0 0 4px;
    font-size: 26px;
    letter-spacing: -0.3px;
    font-weight: 800;
}

.hero p {
    margin: 0;
    color: var(--muted);
    font-size: 13px;
}

.cta-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 13px;
    background: linear-gradient(120deg, #ec1816 0%, #d31412 100%);
    color: #fff;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 700;
    letter-spacing: 0.2px;
    font-size: 14px;
    box-shadow: var(--shadow);
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.panel {
    background: var(--panel);
    border: 1px solid var(--stroke);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.panel-head {
    padding: 14px 16px;
    border-bottom: 1px solid var(--stroke);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.panel-head h3 {
    margin: 0;
    font-size: 16px;
    letter-spacing: -0.1px;
}

.pill {
    padding: 5px 9px;
    border-radius: 999px;
    background: var(--panel-soft);
    border: 1px solid var(--stroke);
    color: var(--muted);
    font-size: 12px;
    font-weight: 600;
}

.subhead {
    margin: 4px 0 0;
    color: var(--muted);
    font-size: 13px;
}

.panel-body {
    padding: 16px;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 10px;
}

.metric {
    background: var(--panel-soft);
    border: 1px solid var(--stroke);
    border-radius: 10px;
    padding: 10px;
    text-align: center;
}

.metric-label {
    font-size: 10px;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 4px;
}

.metric-value {
    font-size: 18px;
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
    grid-template-columns: 1fr 1.2fr;
    gap: 14px;
    margin-top: 14px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 12px;
}

.field label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 4px;
    color: var(--text);
}

.field input {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid var(--stroke);
    background: #fff;
    color: var(--text);
    font-size: 14px;
}

.actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-secondary {
    padding: 9px 12px;
    border-radius: 8px;
    border: 1px solid var(--stroke);
    background: #fff;
    color: var(--text);
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-secondary:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--accent-soft);
}

.cta-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 18px 38px rgba(236, 24, 22, 0.25);
}

.filters {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr;
    gap: 8px;
    margin-bottom: 10px;
}

.filters input,
.filters select {
    width: 100%;
    padding: 9px 10px;
    border-radius: 8px;
    border: 1px solid var(--stroke);
    background: #fff;
    color: var(--text);
    font-size: 13px;
}

.list-stats {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 10px;
    font-size: 12px;
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
    padding: 10px 12px;
    border-bottom: 1px solid var(--stroke);
    text-align: left;
    font-size: 13px;
}

table.subs-table th {
    font-size: 11px;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    color: var(--muted);
    background: var(--panel-soft);
    position: sticky;
    top: 0;
    z-index: 2;
    font-weight: 700;
}

.name-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}

.avatar {
    width: 30px;
    height: 30px;
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
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    display: inline-block;
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
    font-size: 12px;
}

.pager-buttons {
    display: inline-flex;
    gap: 6px;
}

.pager-btn {
    padding: 7px 10px;
    border-radius: 7px;
    border: 1px solid var(--stroke);
    background: #fff;
    color: var(--text);
    font-weight: 700;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.pager-btn:hover:not(:disabled) {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--accent-soft);
}

.pager-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 14px 18px;
    border-radius: 10px;
    background: white;
    border: 1px solid var(--stroke);
    box-shadow: var(--shadow);
    z-index: 1000;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 600;
    animation: slideIn 0.3s ease;
}

.notification.success {
    border-left: 4px solid var(--success);
    color: var(--success);
}

.notification.error {
    border-left: 4px solid var(--accent);
    color: var(--accent);
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }

    to {
        transform: translateX(0);
        opacity: 1;
    }
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
                <h1>Corporate Subscriber Desk</h1>
                <p>Allocate subscribers, invite teammates, and keep your corporate plan healthy.</p>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <div>
                    <h3>Corporate subscription</h3>
                </div>
            </div>
            <div class="panel-body">
                <div class="metrics-grid">
                    <div class="metric">
                        <div class="metric-label">Expiry Date</div>
                        <div class="metric-value"><?php echo date('jS M Y', strtotime($cp_expiry)) ?></div>
                    </div>
                    <div class="metric">
                        <div class="metric-label">Total subscribers</div>
                        <div class="metric-value"><?php echo $cp_limit ?></div>
                    </div>
                    <div class="metric good">
                        <div class="metric-label">Active</div>
                        <div class="metric-value"><?php echo $cp_used ?></div>
                    </div>
                    <div class="metric warn">
                        <div class="metric-label">Inactive</div>
                        <div class="metric-value"><?php echo $cp_limit - $cp_used ?></div>
                    </div>
                    <div class="metric">
                        <div class="metric-label">Utilization</div>
                        <div class="metric-value">
                            <?php
                                $utilization = $cp_limit > 0 ? round(($cp_used / $cp_limit) * 100) : 0;
                                echo $utilization . '%';
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
                    <form id="subscriberForm" method="POST" action="/add-new-corporate-subscriber/">
                        <div class="form-grid">
                            <div class="field">
                                <label for="subscriber_name">Full Name</label>
                                <input type="text" id="subscriber_name" name="name" placeholder="Enter name" required>
                            </div>
                            <div class="field">
                                <label for="subscriber_email">Email</label>
                                <input type="email" id="subscriber_email" name="email" placeholder="name@company.com"
                                    required>
                            </div>
                        </div>
                        <div class="actions">
                            <button class="cta-primary" type="submit" id="inviteBtn">Invite Now</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h3>Subscribers</h3>
                    <span class="pill">Total <?php echo $subscriberListCount ?> </span>
                </div>
                <div class="panel-body">
                    <div class="filters">
                        <input type="search" id="subscriberSearch" name="q" placeholder="Search by name or email">
                        <select id="statusFilter" name="status_filter">
                            <option value="">All statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                        <select id="perPageSelect" name="per_page">
                            <option value="5" selected>5 per page</option>
                            <option value="20">20 per page</option>
                            <option value="50">50 per page</option>
                            <option value="100">100 per page</option>
                        </select>
                    </div>

                    <div class="list-stats">
                        <span class="pill">Active: <?php echo $cp_used ?></span>
                        <span class="pill">Inactive: <?php echo $cp_limit - $cp_used ?></span>
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
                                <?php foreach ($subscribers as $sub):

                                        switch ($sub['subscription_status']) {
                                            case '1':
                                                $statusKey = 'active';
                                                $statusLabel = 'Active';
                                                break;
                                            case '0':
                                                $statusKey = 'inactive';
                                                $statusLabel = 'Inactive';
                                                break;
                                            default:
                                                $statusKey = 'pending';
                                                $statusLabel = 'Pending';
                                        }

                                    ?>
                                <tr>
                                    <td>
                                        <span class="name-cell">
                                            <span
                                                class="avatar"><?php echo strtoupper(substr($sub['profile_display_name'], 0, 1)); ?></span>
                                            <?php echo $sub['profile_display_name']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $sub['profile_email']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $statusKey; ?>"
                                            data-status="<?php echo $statusKey; ?>">
                                            <?php echo $statusLabel; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination">
                        <span>Page <?php echo $currentPage; ?> of
                            <?php echo $totalPages; ?></span>
                        <div class="pager-buttons">
                            <button class="pager-btn" id="prevBtn" onclick="goToPreviousPage()" disabled>← Prev</button>
                            <button class="pager-btn" id="nextBtn" onclick="goToNextPage()" disabled>Next →</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(() => {
    const searchInput = document.getElementById('subscriberSearch');
    const statusFilter = document.getElementById('statusFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableBody = document.querySelector('.subs-table tbody');
    const pageLabel = document.querySelector('.pagination span');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (!tableBody) return;

    const allRows = Array.from(tableBody.querySelectorAll('tr'));
    let filteredRows = [...allRows];
    let currentPage = 1;

    const getPerPage = () => parseInt(perPageSelect.value, 10) || 5;

    const applyFilters = () => {
        const query = (searchInput.value || '').trim().toLowerCase();
        const status = statusFilter.value;

        filteredRows = allRows.filter(row => {
            const name = row.querySelector('.name-cell')?.textContent?.toLowerCase() || '';
            const email = row.children[1]?.textContent?.toLowerCase() || '';
            const rowStatus = row.querySelector('[data-status]')?.getAttribute('data-status') || '';

            const matchesQuery = query === '' || name.includes(query) || email.includes(query);
            const matchesStatus = status === '' || rowStatus === status;
            return matchesQuery && matchesStatus;
        });

        currentPage = 1;
        renderPage();
    };

    const renderPage = () => {
        const perPage = getPerPage();
        const totalPages = Math.max(1, Math.ceil(filteredRows.length / perPage));
        currentPage = Math.min(currentPage, totalPages);

        allRows.forEach(row => row.style.display = 'none');

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        filteredRows.slice(start, end).forEach(row => {
            row.style.display = '';
        });

        if (pageLabel) {
            pageLabel.textContent = `Page ${currentPage} of ${totalPages}`;
        }

        prevBtn.disabled = currentPage <= 1;
        nextBtn.disabled = currentPage >= totalPages;
    };

    window.goToPreviousPage = () => {
        if (currentPage > 1) {
            currentPage -= 1;
            renderPage();
        }
    };

    window.goToNextPage = () => {
        const perPage = getPerPage();
        const totalPages = Math.max(1, Math.ceil(filteredRows.length / perPage));
        if (currentPage < totalPages) {
            currentPage += 1;
            renderPage();
        }
    };

    searchInput.addEventListener('input', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    perPageSelect.addEventListener('change', () => {
        currentPage = 1;
        renderPage();
    });

    applyFilters();
})();
</script>

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