<?php

$_dashSid = defined('APP_SITE_ID') ? (int)APP_SITE_ID : (int)SITE_ID;
$_dashCur = '';
$_tpfx    = TABLE_PREFIX;

// ── Helpers ───────────────────────────────────────────────────────────────────

function egDbScalar($c, $q, $f = 'total')
{
    if ($r = mysqli_query($c, $q)) {
        if (mysqli_num_rows($r) > 0) {
            $row = mysqli_fetch_assoc($r);
            return isset($row[$f]) ? (float)$row[$f] : 0;
        }
    }
    return 0;
}

function egSanitizeDate($val, $fallback)
{
    if (is_string($val) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $val) && strtotime($val) !== false) {
        return $val;
    }
    return $fallback;
}

function egRevDaily($c, $pfx, $sid, $from, $to)
{
    $labels = array();
    $map    = array();
    $cur    = strtotime($from);
    $end    = strtotime($to);
    while ($cur <= $end) {
        $key       = date('Y-m-d', $cur);
        $labels[]  = date('d M', $cur);
        $map[$key] = 0;
        $cur += 86400;
    }
    $q = "SELECT DATE(order_generate) g, SUM(CAST(order_total_price AS DECIMAL(12,2))) r
          FROM {$pfx}order
          WHERE order_site_id=$sid AND order_product_id!=''
            AND order_total_price!='' AND order_status IN (1,6)
            AND DATE(order_generate) BETWEEN '$from' AND '$to'
          GROUP BY DATE(order_generate)";
    if ($res = mysqli_query($c, $q)) {
        while ($row = mysqli_fetch_assoc($res)) {
            if (array_key_exists($row['g'], $map)) {
                $map[$row['g']] = (float)$row['r'];
            }
        }
    }
    return array('labels' => $labels, 'data' => array_values($map));
}

function egRevMonthly($c, $pfx, $sid, $from, $to)
{
    $labels = array();
    $map    = array();
    $cur    = strtotime(date('Y-m-01', strtotime($from)));
    $end    = strtotime(date('Y-m-01', strtotime($to)));
    while ($cur <= $end) {
        $key      = date('Y-m', $cur);
        $labels[] = date('M Y', $cur);
        $map[$key] = 0;
        $cur = strtotime('+1 month', $cur);
    }
    $q = "SELECT DATE_FORMAT(order_generate,'%Y-%m') m, SUM(CAST(order_total_price AS DECIMAL(12,2))) r
          FROM {$pfx}order
          WHERE order_site_id=$sid AND order_product_id!=''
            AND order_total_price!='' AND order_status IN (1,6)
            AND order_generate BETWEEN '{$from} 00:00:00' AND '{$to} 23:59:59'
          GROUP BY DATE_FORMAT(order_generate,'%Y-%m')";
    if ($res = mysqli_query($c, $q)) {
        while ($row = mysqli_fetch_assoc($res)) {
            if (array_key_exists($row['m'], $map)) {
                $map[$row['m']] = (float)$row['r'];
            }
        }
    }
    return array('labels' => $labels, 'data' => array_values($map));
}

function egOrderLabel($s)
{
    $s = (int)$s;
    $t = array('Pending', 'Confirmed', 'Rejected', 'Cancelled', 'Waiting', 'RTO', 'Delivered');
    $c = array('pending', 'confirm', 'reject', 'cancel', 'waiting', 'rto', 'delivered');
    return array(
        isset($t[$s]) ? $t[$s] : 'Unknown',
        isset($c[$s]) ? $c[$s] : 'unknown'
    );
}

// ── Date filter ───────────────────────────────────────────────────────────────

$_today     = date('Y-m-d');
$_validDp   = array('last7', 'last30', 'thisMonth', 'prevMonth', 'last3m', 'thisYear', 'custom');
$_dp        = (isset($_GET['dp']) && in_array($_GET['dp'], $_validDp)) ? $_GET['dp'] : 'last30';

switch ($_dp) {
    case 'last7':
        $_df = date('Y-m-d', strtotime('-6 days'));
        $_dt = $_today;
        break;
    case 'thisMonth':
        $_df = date('Y-m-01');
        $_dt = $_today;
        break;
    case 'prevMonth':
        $_df = date('Y-m-01', strtotime('first day of last month'));
        $_dt = date('Y-m-t',  strtotime('last day of last month'));
        break;
    case 'last3m':
        $_df = date('Y-m-01', strtotime('-2 months'));
        $_dt = $_today;
        break;
    case 'thisYear':
        $_df = date('Y-01-01');
        $_dt = $_today;
        break;
    case 'custom':
        $_df = egSanitizeDate(isset($_GET['df']) ? $_GET['df'] : '', date('Y-m-d', strtotime('-29 days')));
        $_dt = egSanitizeDate(isset($_GET['dt']) ? $_GET['dt'] : '', $_today);
        if ($_df > $_dt) {
            $_dt = $_df;
        }
        break;
    default: // last30
        $_df = date('Y-m-d', strtotime('-29 days'));
        $_dt = $_today;
}

// Human-readable label for the active filter
$_filterLabel = array(
    'last7'     => 'Last 7 Days',
    'last30'    => 'Last 30 Days',
    'thisMonth' => 'This Month',
    'prevMonth' => 'Previous Month',
    'last3m'    => 'Last 3 Months',
    'thisYear'  => 'This Year',
    'custom'    => date('d M Y', strtotime($_df)) . ' – ' . date('d M Y', strtotime($_dt)),
);
$_activeLabel = $_filterLabel[$_dp];

// SQL clauses (dates already validated — no injection risk)
$_odf  = "DATE(o.order_generate) BETWEEN '$_df' AND '$_dt'";   // orders with alias
$_odf2 = "DATE(order_generate)   BETWEEN '$_df' AND '$_dt'";   // orders without alias

// URL helper — preserves type param, strips old filter params
$_baseUrl = '?';
if (isset($_GET['type'])) {
    $_baseUrl .= 'type=' . urlencode($_GET['type']) . '&';
}
$_baseUrl .= 'dp=';

// ── Currency ──────────────────────────────────────────────────────────────────

if ($_r = mysqli_query($connection, "SELECT inventory_currency_symbol FROM {$_tpfx}inventory WHERE inventory_site_id=$_dashSid AND inventory_currency_symbol!='' LIMIT 1")) {
    if (mysqli_num_rows($_r) > 0) {
        $_row = mysqli_fetch_assoc($_r);
        $_dashCur = $_row['inventory_currency_symbol'];
    }
}
$_jsCur = addslashes($_dashCur);

// ── KPI — all order-based metrics are date-filtered ──────────────────────────

$kpiOrders     = egDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}order WHERE order_site_id=$_dashSid AND order_product_id!='' AND $_odf2");
$kpiPending    = egDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}order WHERE order_site_id=$_dashSid AND order_product_id!='' AND order_status=0 AND $_odf2");
$kpiDelivered  = egDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}order WHERE order_site_id=$_dashSid AND order_product_id!='' AND order_status=6 AND $_odf2");
$kpiRevenue    = egDbScalar($connection, "SELECT IFNULL(SUM(CAST(order_total_price AS DECIMAL(12,2))),0) total FROM {$_tpfx}order WHERE order_site_id=$_dashSid AND order_product_id!='' AND order_total_price!='' AND order_status IN (1,6) AND $_odf2");

// Product/category counts are catalog-wide (not date-bound)
$kpiProducts   = egDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}product p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.product_id WHERE l.link_type='product' AND p.product_type='product' AND l.link_site_id=$_dashSid AND l.link_status!=301");
$kpiPublished  = egDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}product p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.product_id WHERE l.link_type='product' AND p.product_type='product' AND l.link_site_id=$_dashSid AND l.link_status=200");
$kpiOutOfStock = egDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}inventory i INNER JOIN {$_tpfx}product p ON i.inventory_relation_id=p.product_id WHERE i.inventory_site_id=$_dashSid AND i.inventory_type='product' AND i.inventory_status=1 AND p.product_type='product' AND i.inventory_in_stock=0");
$kpiCategories = egDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}category c INNER JOIN {$_tpfx}link l ON l.link_relation_id=c.category_id WHERE l.link_type='category' AND c.category_type='product' AND c.category_site_id=$_dashSid AND l.link_status=200");

// ── Recent orders — date-filtered ─────────────────────────────────────────────

$recentOrders = array();
$_q = "SELECT o.order_id, o.order_total_price, o.order_payment_mode, o.order_generate,
              o.order_status, p.profile_display_name, a.address_mobile
       FROM {$_tpfx}order o
       INNER JOIN {$_tpfx}address a ON a.address_id=o.order_address_id
       INNER JOIN {$_tpfx}profile p ON p.profile_user_id=o.order_user_id
       WHERE o.order_site_id=$_dashSid AND o.order_product_id!='' AND $_odf
       ORDER BY o.order_generate DESC LIMIT 10";
if ($_r = mysqli_query($connection, $_q)) {
    while ($row = mysqli_fetch_assoc($_r)) {
        $recentOrders[] = $row;
    }
}

// ── Recent products — filtered by publish date ────────────────────────────────

$recentProducts = array();
$_q = "SELECT pr.product_id, pr.product_title, pr.product_image, c.category_title,
              i.inventory_currency_symbol, IFNULL(i.inventory_price,0) inventory_price,
              l.link_status
       FROM {$_tpfx}product pr
       INNER JOIN {$_tpfx}category c ON c.category_id=pr.product_category
       INNER JOIN {$_tpfx}link l ON l.link_relation_id=pr.product_id
       INNER JOIN {$_tpfx}inventory i ON i.inventory_relation_id=pr.product_id
       WHERE l.link_type='product' AND pr.product_type='product' AND l.link_site_id=$_dashSid
         AND l.link_status!=301 AND i.inventory_status=1 AND i.inventory_type='product'
         AND DATE(l.link_published) BETWEEN '$_df' AND '$_dt'
       ORDER BY l.link_published DESC LIMIT 6";
if ($_r = mysqli_query($connection, $_q)) {
    while ($row = mysqli_fetch_assoc($_r)) {
        $recentProducts[] = $row;
    }
}

// ── Revenue chart — auto daily/monthly based on range ────────────────────────

$_rangeDays  = max(1, round((strtotime($_dt) - strtotime($_df)) / 86400));
$revenueChart = ($_rangeDays <= 62)
    ? egRevDaily($connection,   $_tpfx, $_dashSid, $_df, $_dt)
    : egRevMonthly($connection, $_tpfx, $_dashSid, $_df, $_dt);

// ── Order status distribution — date-filtered ────────────────────────────────

$statusCounts = array(0, 0, 0, 0, 0, 0, 0);
if ($_r = mysqli_query($connection, "SELECT order_status, COUNT(*) cnt FROM {$_tpfx}order WHERE order_site_id=$_dashSid AND order_product_id!='' AND $_odf2 GROUP BY order_status")) {
    while ($row = mysqli_fetch_assoc($_r)) {
        $idx = (int)$row['order_status'];
        if ($idx >= 0 && $idx <= 6) {
            $statusCounts[$idx] = (int)$row['cnt'];
        }
    }
}

// ── Top-selling products — date-filtered ─────────────────────────────────────

$_pidCounts = array();
if ($_r = mysqli_query($connection, "SELECT order_product_id FROM {$_tpfx}order WHERE order_site_id=$_dashSid AND order_product_id!='' AND $_odf2")) {
    while ($row = mysqli_fetch_assoc($_r)) {
        foreach (explode(',', $row['order_product_id']) as $pid) {
            $pid = trim($pid);
            if (ctype_digit($pid) && (int)$pid > 0) {
                $_pidCounts[$pid] = isset($_pidCounts[$pid]) ? $_pidCounts[$pid] + 1 : 1;
            }
        }
    }
}
arsort($_pidCounts);
$_topPids    = array_slice(array_keys($_pidCounts), 0, 5);
$topProducts = array();
if (!empty($_topPids)) {
    $_inStr = implode(',', array_map('intval', $_topPids));
    if ($_r = mysqli_query($connection, "SELECT product_id, product_title, product_image FROM {$_tpfx}product WHERE product_id IN ($_inStr)")) {
        $_pmap = array();
        while ($row = mysqli_fetch_assoc($_r)) {
            $_pmap[(int)$row['product_id']] = $row;
        }
        foreach ($_topPids as $pid) {
            if (isset($_pmap[(int)$pid])) {
                $topProducts[] = array_merge($_pmap[(int)$pid], array('cnt' => $_pidCounts[$pid]));
            }
        }
    }
}

?>

<?php
// Build custom form target (preserves existing GET params except filter ones)
$_formTarget = '?';
foreach ($_GET as $_k => $_v) {
    if (!in_array($_k, array('dp', 'df', 'dt'))) {
        $_formTarget .= urlencode($_k) . '=' . urlencode($_v) . '&';
    }
}
$_formTarget = rtrim($_formTarget, '?&');

$_chartPayload = array(
    'currency' => $_dashCur,
    'revenueLabels' => $revenueChart['labels'],
    'revenueData' => $revenueChart['data'],
    'statusCounts' => $statusCounts,
);
?>

<div id="egDash">

    <!-- Page header -->
    <div class="dh-head">
        <div>
            <h1 class="dh-title">Dashboard</h1>
            <div class="dh-sub">Overview &mdash; <?php echo date('l, d F Y'); ?></div>
        </div>
        <div class="dh-actions">
            <a href="order/?type=list&amp;status=0" class="dh-btn">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                Pending
            </a>
            <a href="product/?type=add&amp;editor=true" class="dh-btn primary">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Add Product
            </a>
        </div>
    </div>

    <!-- Date Filter Bar -->
    <div class="df-bar" role="region" aria-label="Dashboard date range filter">
        <span class="df-label">Filter</span>
        <div class="df-presets">
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>last7"
                class="df-preset <?php echo $_dp === 'last7'     ? 'active' : ''; ?>"<?php echo $_dp === 'last7' ? ' aria-current="true"' : ''; ?>>Last 7 Days</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>last30"
                class="df-preset <?php echo $_dp === 'last30'    ? 'active' : ''; ?>"<?php echo $_dp === 'last30' ? ' aria-current="true"' : ''; ?>>Last 30 Days</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>thisMonth"
                class="df-preset <?php echo $_dp === 'thisMonth' ? 'active' : ''; ?>"<?php echo $_dp === 'thisMonth' ? ' aria-current="true"' : ''; ?>>This Month</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>prevMonth"
                class="df-preset <?php echo $_dp === 'prevMonth' ? 'active' : ''; ?>"<?php echo $_dp === 'prevMonth' ? ' aria-current="true"' : ''; ?>>Prev Month</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>last3m"
                class="df-preset <?php echo $_dp === 'last3m'    ? 'active' : ''; ?>"<?php echo $_dp === 'last3m' ? ' aria-current="true"' : ''; ?>>3 Months</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>thisYear"
                class="df-preset <?php echo $_dp === 'thisYear'  ? 'active' : ''; ?>"<?php echo $_dp === 'thisYear' ? ' aria-current="true"' : ''; ?>>This Year</a>
        </div>
        <div class="df-divider" aria-hidden="true"></div>
        <form method="get" action="<?php echo htmlspecialchars($_formTarget); ?>" class="df-custom">
            <?php foreach ($_GET as $_k => $_v): if (!in_array($_k, array('dp', 'df', 'dt'))): ?>
            <input type="hidden" name="<?php echo htmlspecialchars($_k); ?>"
                value="<?php echo htmlspecialchars($_v); ?>">
            <?php endif;
            endforeach; ?>
            <input type="hidden" name="dp" value="custom">
            <label for="df-from" class="visually-hidden">From date</label>
            <input id="df-from" type="date" name="df" class="df-date" value="<?php echo htmlspecialchars($_df); ?>" min="2018-01-01"
                max="<?php echo $_today; ?>">
            <span class="df-sep" aria-hidden="true">to</span>
            <label for="df-to" class="visually-hidden">To date</label>
            <input id="df-to" type="date" name="dt" class="df-date" value="<?php echo htmlspecialchars($_dt); ?>" min="2018-01-01"
                max="<?php echo $_today; ?>">
            <button type="submit" class="df-apply">Apply</button>
        </form>
        <span class="df-active-badge">
            <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            <?php echo htmlspecialchars($_activeLabel); ?>
        </span>
    </div>

    <!-- KPI row -->
    <div class="kpi-row">

        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon blue">
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <path d="M16 10a4 4 0 01-8 0" />
                    </svg>
                </div>
                <span
                    class="kpi-badge <?php echo $kpiPending > 0 ? 'down' : 'neu'; ?>"><?php echo number_format($kpiPending); ?>
                    pending</span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiOrders); ?></div>
            <div class="kpi-label">Orders</div>
            <div class="kpi-meta">Delivered: <?php echo number_format($kpiDelivered); ?></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon green">
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                    </svg>
                </div>
                <span class="kpi-badge neu">confirmed + delivered</span>
            </div>
            <div class="kpi-value"><?php echo htmlspecialchars($_dashCur) . number_format($kpiRevenue, 0); ?></div>
            <div class="kpi-label">Revenue</div>
            <div class="kpi-meta"><?php echo htmlspecialchars($_activeLabel); ?></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon amber">
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                    </svg>
                </div>
                <?php if ($kpiOutOfStock > 0): ?>
                <span class="kpi-badge down"><?php echo number_format($kpiOutOfStock); ?> out of stock</span>
                <?php else: ?>
                <span class="kpi-badge up">all in stock</span>
                <?php endif; ?>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiProducts); ?></div>
            <div class="kpi-label">Products</div>
            <div class="kpi-meta">Published: <?php echo number_format($kpiPublished); ?></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon violet">
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z" />
                        <line x1="7" y1="7" x2="7.01" y2="7" />
                    </svg>
                </div>
                <span class="kpi-badge neu">active</span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiCategories); ?></div>
            <div class="kpi-label">Categories</div>
            <div class="kpi-meta">Live product categories</div>
        </div>

    </div>

    <!-- Chart row -->
    <div class="chart-row">

        <section class="dc" aria-labelledby="eg-rev-chart-title">
            <div class="panel-head">
                <h2 id="eg-rev-chart-title" class="panel-title">Revenue</h2>
                <span class="panel-period"><?php echo htmlspecialchars($_activeLabel); ?></span>
            </div>
            <div class="panel-body">
                <figure class="eg-chart-figure">
                    <div class="chart-wrap"><canvas id="egRevChart" role="img" aria-label="Revenue chart for <?php echo htmlspecialchars($_activeLabel); ?>"></canvas></div>
                    <figcaption class="visually-hidden">Total revenue <?php echo htmlspecialchars($_dashCur) . number_format($kpiRevenue, 2); ?> for <?php echo htmlspecialchars($_activeLabel); ?>.</figcaption>
                </figure>
            </div>
        </section>

        <section class="dc" aria-labelledby="eg-status-chart-title">
            <div class="panel-head">
                <h2 id="eg-status-chart-title" class="panel-title">Orders by Status</h2>
                <span class="panel-period"><?php echo htmlspecialchars($_activeLabel); ?></span>
            </div>
            <div class="panel-body">
                <figure class="eg-chart-figure">
                    <div class="chart-wrap-sm"><canvas id="egStatusChart" role="img" aria-label="Orders by status chart for <?php echo htmlspecialchars($_activeLabel); ?>"></canvas></div>
                    <figcaption class="visually-hidden"><?php echo number_format($kpiOrders); ?> orders recorded during <?php echo htmlspecialchars($_activeLabel); ?>.</figcaption>
                </figure>
            </div>
        </section>

    </div>

    <!-- Bottom row -->
    <div class="bottom-row">

        <!-- Recent orders -->
        <section class="dc" aria-labelledby="eg-recent-orders-title">
            <div class="panel-head">
                <h2 id="eg-recent-orders-title" class="panel-title">Recent Orders</h2>
                <a href="order/?type=list&amp;status=all" class="panel-link">View all &rarr;</a>
            </div>
            <div class="panel-body-flush">
                <div class="table-wrap" tabindex="0" role="region" aria-labelledby="eg-recent-orders-title">
                    <table class="dt dt--orders">
                        <caption class="visually-hidden">Recent orders for <?php echo htmlspecialchars($_activeLabel); ?></caption>
                        <thead>
                            <tr>
                                <th scope="col">Order</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentOrders)): foreach ($recentOrders as $ord):
                                    list($sText, $sClass) = egOrderLabel($ord['order_status']);
                            ?>
                            <tr>
                                <th scope="row">
                                    <a href="order/?type=view&amp;id=<?php echo rawurlencode(base64_encode((string)$ord['order_id'])); ?>"
                                        class="oid">#<?php echo (int)$ord['order_id']; ?></a>
                                </th>
                                <td>
                                    <div class="cust-info">
                                        <div class="cust-name"><?php echo htmlspecialchars($ord['profile_display_name']); ?></div>
                                        <div class="cust-ph"><?php echo htmlspecialchars($ord['address_mobile']); ?></div>
                                    </div>
                                </td>
                                <td class="amt-cell">
                                    <?php echo htmlspecialchars($_dashCur) . number_format((float)$ord['order_total_price'], 2); ?>
                                </td>
                                <td><span class="chip chip-<?php echo $sClass; ?>"><?php echo htmlspecialchars($sText); ?></span></td>
                                <td class="time-cell">
                                    <?php echo date('d M, h:i A', strtotime($ord['order_generate'])); ?>
                                </td>
                            </tr>
                            <?php endforeach;
                            else: ?>
                            <tr class="eg-empty-row">
                                <td colspan="5">
                                    <div class="empty-state">No orders in this period.</div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Right column -->
        <div class="right-col">

            <?php if (!empty($topProducts)): ?>
            <section class="dc" aria-labelledby="eg-top-products-title">
                <div class="panel-head">
                    <h2 id="eg-top-products-title" class="panel-title">Top Selling Products</h2>
                    <span class="panel-period"><?php echo htmlspecialchars($_activeLabel); ?></span>
                </div>
                <div class="panel-body panel-body-compact">
                    <div class="top-list">
                        <?php foreach ($topProducts as $i => $tp):
                                $tImg = trim($tp['product_image']);
                                $tHasImg = ($tImg !== '');
                                $rc = $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-n'));
                                $tpUrl = 'product/?type=edit&editor=true&id=' . rawurlencode(base64_encode((string)$tp['product_id']));
                            ?>
                        <a href="<?php echo htmlspecialchars($tpUrl); ?>" class="top-item">
                            <span class="rank <?php echo $rc; ?>"><?php echo $i + 1; ?></span>
                            <?php if ($tHasImg): ?>
                            <img src="<?php echo htmlspecialchars($tImg); ?>" class="top-img" alt="" width="34" height="34" loading="lazy" decoding="async">
                            <?php endif; ?>
                            <span class="prod-thumb-fallback<?php echo $tHasImg ? '' : ' is-visible'; ?>" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                                </svg>
                            </span>
                            <span class="top-name"><?php echo htmlspecialchars($tp['product_title']); ?></span>
                            <span class="top-cnt"><?php echo number_format($tp['cnt']); ?> sold</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php else: ?>
            <section class="dc" aria-labelledby="eg-top-products-title">
                <div class="panel-head">
                    <h2 id="eg-top-products-title" class="panel-title">Top Selling Products</h2>
                </div>
                <div class="empty-state">No sales data in this period.</div>
            </section>
            <?php endif; ?>

            <!-- Recent products -->
            <section class="dc" aria-labelledby="eg-recent-products-title">
                <div class="panel-head">
                    <h2 id="eg-recent-products-title" class="panel-title">Products Added</h2>
                    <a href="product/?type=list&amp;status=all" class="panel-link">View all &rarr;</a>
                </div>
                <div class="panel-body-flush">
                    <div class="table-wrap" tabindex="0" role="region" aria-labelledby="eg-recent-products-title">
                        <table class="dt dt--products">
                            <caption class="visually-hidden">Products added during <?php echo htmlspecialchars($_activeLabel); ?></caption>
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentProducts)): foreach ($recentProducts as $pr):
                                        $img    = trim($pr['product_image']);
                                        $hasImg = ($img !== '');
                                        $isLive = (int)$pr['link_status'] === 200;
                                        $prUrl  = 'product/?type=edit&editor=true&id=' . rawurlencode(base64_encode((string)$pr['product_id']));
                                ?>
                                <tr>
                                    <th scope="row">
                                        <a href="<?php echo htmlspecialchars($prUrl); ?>" class="prod-cell">
                                            <?php if ($hasImg): ?>
                                            <img src="<?php echo htmlspecialchars($img); ?>" class="prod-img" alt="" width="34" height="34" loading="lazy" decoding="async">
                                            <?php endif; ?>
                                            <span class="prod-thumb-fallback<?php echo $hasImg ? '' : ' is-visible'; ?>" aria-hidden="true">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                                                </svg>
                                            </span>
                                            <div class="prod-copy">
                                                <div class="prod-name">
                                                    <?php echo htmlspecialchars($pr['product_title']); ?>
                                                </div>
                                                <div class="prod-cat">
                                                    <?php echo htmlspecialchars($pr['category_title']); ?>
                                                </div>
                                            </div>
                                        </a>
                                    </th>
                                    <td class="amt-cell">
                                        <?php echo htmlspecialchars($pr['inventory_currency_symbol']); ?><?php echo number_format((float)$pr['inventory_price'], 2); ?>
                                    </td>
                                    <td><span
                                            class="chip <?php echo $isLive ? 'chip-live' : 'chip-draft'; ?>"><?php echo $isLive ? 'Live' : 'Draft'; ?></span>
                                    </td>
                                </tr>
                                <?php endforeach;
                                else: ?>
                                <tr class="eg-empty-row">
                                    <td colspan="3">
                                        <div class="empty-state">No products added in this period.</div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Quick actions -->
            <section class="dc" aria-labelledby="eg-quick-actions-title">
                <div class="panel-head">
                    <h2 id="eg-quick-actions-title" class="panel-title">Quick Actions</h2>
                </div>
                <div class="panel-body">
                    <div class="qa-grid">
                        <a href="order/?type=list&amp;status=0" class="qa-a">
                            <span class="qa-icon" aria-hidden="true">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Pending Orders</span>
                                <span class="qa-meta">Review orders to process</span>
                            </span>
                        </a>
                        <a href="order/?type=list&amp;status=6" class="qa-a">
                            <span class="qa-icon" aria-hidden="true">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Delivered</span>
                                <span class="qa-meta">Track completed shipments</span>
                            </span>
                        </a>
                        <a href="product/?type=add&amp;editor=true" class="qa-a">
                            <span class="qa-icon" aria-hidden="true">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Add Product</span>
                                <span class="qa-meta">Create a new listing</span>
                            </span>
                        </a>
                        <a href="product/?type=category" class="qa-a">
                            <span class="qa-icon" aria-hidden="true">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z" />
                                    <line x1="7" y1="7" x2="7.01" y2="7" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Categories</span>
                                <span class="qa-meta">Manage product taxonomy</span>
                            </span>
                        </a>
                        <a href="product/?type=list&amp;status=all" class="qa-a">
                            <span class="qa-icon" aria-hidden="true">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="6" height="6" />
                                    <rect x="15" y="3" width="6" height="6" />
                                    <rect x="15" y="15" width="6" height="6" />
                                    <rect x="3" y="15" width="6" height="6" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">All Products</span>
                                <span class="qa-meta">Browse full product catalog</span>
                            </span>
                        </a>
                        <a href="order/?type=list&amp;status=all" class="qa-a">
                            <span class="qa-icon" aria-hidden="true">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                                    <line x1="3" y1="6" x2="21" y2="6" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">All Orders</span>
                                <span class="qa-meta">Open complete order list</span>
                            </span>
                        </a>
                    </div>
                </div>
            </section>

        </div><!-- /right-col -->

    </div><!-- /bottom-row -->

</div><!-- /egDash -->

<script type="application/json" id="eg-dash-chart-data"><?php echo json_encode($_chartPayload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" crossorigin="anonymous"></script>
