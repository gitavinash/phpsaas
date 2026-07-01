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

function egOrderDaily($c, $pfx, $sid, $from, $to)
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
    $q = "SELECT DATE(order_generate) g, COUNT(*) c
          FROM {$pfx}order
          WHERE order_site_id=$sid AND order_product_id!=''
            AND DATE(order_generate) BETWEEN '$from' AND '$to'
          GROUP BY DATE(order_generate)";
    if ($res = mysqli_query($c, $q)) {
        while ($row = mysqli_fetch_assoc($res)) {
            if (array_key_exists($row['g'], $map)) {
                $map[$row['g']] = (int)$row['c'];
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

// ── KPI — all order-based metrics are date-filtered ──────────────────────────

$kpiOrders     = egDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}order WHERE order_site_id=$_dashSid AND order_product_id!='' AND $_odf2");
$kpiPending    = egDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}order WHERE order_site_id=$_dashSid AND order_product_id!='' AND order_status=0 AND $_odf2");
$kpiDelivered  = egDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}order WHERE order_site_id=$_dashSid AND order_product_id!='' AND order_status=6 AND $_odf2");

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

// ── Daily order activity chart ────────────────────────────────────────────────

$orderActivityChart = egOrderDaily($connection, $_tpfx, $_dashSid, $_df, $_dt);

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

$stPending   = (int)$statusCounts[0];
$stConfirmed = (int)$statusCounts[1];
$stRejected  = (int)$statusCounts[2];
$stCancelled = (int)$statusCounts[3];
$stWaiting   = (int)$statusCounts[4];
$stRto       = (int)$statusCounts[5];
$stDelivered = (int)$statusCounts[6];

$kpiInTransit      = $stConfirmed + $stWaiting;
$kpiException      = $stRejected + $stCancelled + $stRto;
$kpiFulfillmentPct = ($kpiOrders > 0) ? round(($stDelivered / $kpiOrders) * 100, 1) : 0;

?>

<style>
    /* ── Scoped to #egDash ───────────────────────────────────────────────── */
    #egDash {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, 'Inter', sans-serif;
        padding: 4px 0 48px;
        color: #111827;
        float: left;
        width: 100%;
    }

    #egDash * {
        box-sizing: border-box;
    }

    /* Page header */
    #egDash .dh-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }

    #egDash .dh-title {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.02em;
    }

    #egDash .dh-sub {
        font-size: 13px;
        color: #6b7280;
        margin-top: 3px;
    }

    #egDash .dh-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    #egDash .dh-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        transition: all .15s;
        white-space: nowrap;
    }

    #egDash .dh-btn:hover {
        background: #f9fafb;
        color: #111827;
    }

    #egDash .dh-btn.primary {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }

    #egDash .dh-btn.primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
    }

    /* ── Date filter bar ─────────────────────────────────────────────────── */
    #egDash .df-bar {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, .06);
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    #egDash .df-label {
        font-size: 11px;
        font-weight: 600;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: .06em;
        white-space: nowrap;
        margin-right: 2px;
    }

    #egDash .df-presets {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        flex: 1;
    }

    #egDash .df-preset {
        font-size: 12px;
        font-weight: 600;
        padding: 6px 13px;
        border-radius: 8px;
        text-decoration: none;
        color: #4b5563;
        background: #f3f4f6;
        border: 1px solid transparent;
        transition: all .15s;
        white-space: nowrap;
        line-height: 1;
    }

    #egDash .df-preset:hover {
        background: #e5e7eb;
        color: #111827;
    }

    #egDash .df-preset.active {
        background: #0f172a;
        color: #fff;
        border-color: #0f172a;
    }

    #egDash .df-divider {
        width: 1px;
        height: 24px;
        background: #e5e7eb;
        flex-shrink: 0;
    }

    #egDash .df-custom {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    #egDash .df-date {
        font-size: 12px;
        padding: 6px 10px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #f9fafb;
        color: #374151;
        outline: none;
        transition: border-color .15s;
        width: 138px;
    }

    #egDash .df-date:focus {
        border-color: #2563eb;
        background: #fff;
    }

    #egDash .df-sep {
        font-size: 12px;
        color: #9ca3af;
        font-weight: 500;
    }

    #egDash .df-apply {
        font-size: 12px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 8px;
        background: #2563eb;
        color: #fff;
        border: none;
        cursor: pointer;
        transition: background .15s;
        white-space: nowrap;
    }

    #egDash .df-apply:hover {
        background: #1d4ed8;
    }

    #egDash .df-active-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        border-radius: 99px;
        white-space: nowrap;
        margin-left: auto;
    }

    /* Base card */
    #egDash .dc {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, .06);
        box-shadow: 0 1px 4px rgba(0, 0, 0, .04), 0 0 0 1px rgba(0, 0, 0, .02);
    }

    /* KPI row */
    #egDash .kpi-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    #egDash .kpi-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, .06);
        box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    #egDash .kpi-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
    }

    #egDash .kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    #egDash .kpi-icon.blue {
        background: #eff6ff;
        color: #2563eb;
    }

    #egDash .kpi-icon.green {
        background: #f0fdf4;
        color: #059669;
    }

    #egDash .kpi-icon.amber {
        background: #fffbeb;
        color: #d97706;
    }

    #egDash .kpi-icon.violet {
        background: #f5f3ff;
        color: #7c3aed;
    }

    #egDash .kpi-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 99px;
        white-space: nowrap;
        margin-top: 2px;
    }

    #egDash .kpi-badge.up {
        background: #f0fdf4;
        color: #059669;
    }

    #egDash .kpi-badge.down {
        background: #fef2f2;
        color: #dc2626;
    }

    #egDash .kpi-badge.neu {
        background: #f3f4f6;
        color: #6b7280;
    }

    #egDash .kpi-value {
        font-size: 30px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.03em;
        line-height: 1;
    }

    #egDash .kpi-label {
        font-size: 12px;
        font-weight: 500;
        color: #6b7280;
        margin-top: -2px;
    }

    #egDash .kpi-meta {
        font-size: 11px;
        color: #9ca3af;
        padding-top: 10px;
        border-top: 1px solid #f3f4f6;
    }

    /* Operations status widgets */
    #egDash .ops-widget-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin: 0 0 20px;
    }

    #egDash .ops-widget {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        text-decoration: none;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        transition: all .15s;
    }

    #egDash .ops-widget:hover {
        transform: translateY(-1px);
        border-color: #bfdbfe;
        background: #f2f7ff;
    }

    #egDash .ops-widget-copy {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    #egDash .ops-widget-title {
        font-size: 12px;
        color: #6b7280;
        font-weight: 600;
    }

    #egDash .ops-widget-value {
        font-size: 20px;
        line-height: 1;
        color: #0f172a;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    #egDash .ops-widget-hint {
        font-size: 11px;
        font-weight: 600;
        color: #2563eb;
        white-space: nowrap;
    }

    /* Chart row */
    #egDash .chart-row {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 16px;
        margin-bottom: 20px;
    }

    #egDash .panel-head {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f3f4f6;
        gap: 12px;
        flex-wrap: wrap;
    }

    #egDash .panel-title {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        margin: 0;
        white-space: nowrap;
    }

    #egDash .panel-period {
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        background: #f3f4f6;
        padding: 4px 10px;
        border-radius: 6px;
    }

    #egDash .panel-body {
        padding: 18px 20px 20px;
    }

    #egDash .panel-body-flush {
        padding: 0 6px 10px;
    }

    #egDash .chart-wrap {
        position: relative;
        height: 230px;
    }

    #egDash .chart-wrap-sm {
        position: relative;
        height: 210px;
    }

    /* Bottom row */
    #egDash .bottom-row {
        display: grid;
        grid-template-columns: 1.35fr 1fr;
        gap: 16px;
    }

    #egDash .right-col {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Data tables */
    #egDash .table-wrap {
        overflow-x: auto;
        border-radius: 10px;
    }

    #egDash .dt {
        width: 100%;
        border-collapse: collapse;
        min-width: 540px;
    }

    #egDash .dt thead th {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #9ca3af;
        padding: 12px 12px 10px;
        text-align: left;
        border-bottom: 1px solid #f3f4f6;
        background: #f8fafc;
    }

    #egDash .dt tbody td {
        font-size: 13px;
        color: #374151;
        padding: 11px 12px;
        border-bottom: 1px solid #f9fafb;
        vertical-align: middle;
    }

    #egDash .dt tbody tr {
        transition: background-color .15s ease;
    }

    #egDash .dt tbody tr:nth-child(2n) td {
        background: #fcfdff;
    }

    #egDash .dt tbody tr:last-child td {
        border-bottom: none;
    }

    #egDash .dt tbody tr:hover td {
        background: #f3f7ff;
    }

    #egDash .oid {
        font-size: 12px;
        font-weight: 700;
        color: #2563eb;
        text-decoration: none;
    }

    #egDash .oid:hover {
        text-decoration: underline;
    }

    #egDash .cust-name {
        font-size: 13px;
        font-weight: 500;
        color: #111827;
    }

    #egDash .cust-ph {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 2px;
    }

    #egDash .amt-cell {
        font-weight: 600;
        color: #111827;
        font-size: 13px;
        white-space: nowrap;
    }

    #egDash .time-cell {
        font-size: 11px;
        color: #9ca3af;
        white-space: nowrap;
    }

    /* Product cell */
    #egDash .prod-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #egDash .prod-img {
        width: 34px;
        height: 34px;
        border-radius: 7px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
        background: #f3f4f6;
        flex-shrink: 0;
    }

    #egDash .prod-name {
        font-size: 12px;
        font-weight: 600;
        color: #111827;
        line-height: 1.3;
    }

    #egDash .prod-cat {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 1px;
    }

    /* Status chips */
    #egDash .chip {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        line-height: 1.4;
    }

    #egDash .chip-pending {
        background: #fffbeb;
        color: #b45309;
    }

    #egDash .chip-confirm {
        background: #f0fdf4;
        color: #059669;
    }

    #egDash .chip-delivered {
        background: #ecfdf5;
        color: #047857;
    }

    #egDash .chip-reject {
        background: #fef2f2;
        color: #b91c1c;
    }

    #egDash .chip-cancel {
        background: #fff7ed;
        color: #c2410c;
    }

    #egDash .chip-rto {
        background: #fdf4ff;
        color: #7e22ce;
    }

    #egDash .chip-waiting {
        background: #eff6ff;
        color: #1d4ed8;
    }

    #egDash .chip-unknown {
        background: #f3f4f6;
        color: #6b7280;
    }

    #egDash .chip-live {
        background: #f0fdf4;
        color: #059669;
    }

    #egDash .chip-draft {
        background: #f9fafb;
        color: #9ca3af;
        border: 1px solid #e5e7eb;
    }

    /* Top products */
    #egDash .top-list {
        display: flex;
        flex-direction: column;
    }

    #egDash .top-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #f9fafb;
    }

    #egDash .top-item:last-child {
        border-bottom: none;
    }

    #egDash .rank {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
    }

    #egDash .rank-1 {
        background: #fef9c3;
        color: #854d0e;
    }

    #egDash .rank-2 {
        background: #f3f4f6;
        color: #374151;
    }

    #egDash .rank-3 {
        background: #fff7ed;
        color: #9a3412;
    }

    #egDash .rank-n {
        background: #f3f4f6;
        color: #6b7280;
    }

    #egDash .top-img {
        width: 34px;
        height: 34px;
        border-radius: 7px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    #egDash .top-name {
        font-size: 12px;
        font-weight: 600;
        color: #111827;
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #egDash .top-cnt {
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
        background: #f3f4f6;
        padding: 2px 8px;
        border-radius: 99px;
        white-space: nowrap;
    }

    /* Empty state */
    #egDash .empty-state {
        padding: 28px 12px;
        text-align: center;
        color: #9ca3af;
        font-size: 13px;
    }

    /* Quick actions */
    #egDash .qa-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    #egDash .qa-a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        color: #1f2937;
        text-decoration: none;
        transition: all .15s;
        text-align: left;
        line-height: 1.25;
        min-height: 58px;
    }

    #egDash .qa-a:hover {
        background: #eef4ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
        transform: translateY(-1px);
    }

    #egDash .qa-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #eaf2ff;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    #egDash .qa-copy {
        display: flex;
        flex-direction: column;
        gap: 1px;
        min-width: 0;
    }

    #egDash .qa-title {
        font-size: 12px;
        font-weight: 700;
        color: inherit;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #egDash .qa-meta {
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        #egDash .kpi-row {
            grid-template-columns: repeat(2, 1fr);
        }

        #egDash .ops-widget-row {
            grid-template-columns: repeat(2, 1fr);
        }

        #egDash .chart-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 900px) {
        #egDash .bottom-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        #egDash .kpi-row {
            grid-template-columns: 1fr;
        }

        #egDash .ops-widget-row {
            grid-template-columns: 1fr;
        }

        #egDash .df-bar {
            gap: 8px;
        }

        #egDash .dh-head {
            flex-direction: column;
        }

        #egDash .df-divider {
            display: none;
        }

        #egDash .qa-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php
// Build custom form target (preserves existing GET params except filter ones)
$_formTarget = '?';
foreach ($_GET as $_k => $_v) {
    if (!in_array($_k, array('dp', 'df', 'dt'))) {
        $_formTarget .= urlencode($_k) . '=' . urlencode($_v) . '&';
    }
}
$_formTarget = rtrim($_formTarget, '?&');
?>

<div id="egDash">

    <!-- Page header -->
    <div class="dh-head">
        <div>
            <h2 class="dh-title">Dashboard</h2>
            <div class="dh-sub">Logistics overview &mdash; <?php echo date('l, d F Y'); ?></div>
        </div>
        <div class="dh-actions">
            <a href="order/?type=list&amp;status=0" class="dh-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                Pending
            </a>
            <a href="order/?type=list&amp;status=1" class="dh-btn primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 7h12"></path>
                    <path d="M3 12h18"></path>
                    <path d="M3 17h15"></path>
                </svg>
                Dispatch Queue
            </a>
        </div>
    </div>

    <!-- Date Filter Bar -->
    <div class="df-bar">
        <span class="df-label">Filter</span>
        <div class="df-presets">
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>last7"
                class="df-preset <?php echo $_dp === 'last7'     ? 'active' : ''; ?>">Last 7 Days</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>last30"
                class="df-preset <?php echo $_dp === 'last30'    ? 'active' : ''; ?>">Last 30 Days</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>thisMonth"
                class="df-preset <?php echo $_dp === 'thisMonth' ? 'active' : ''; ?>">This Month</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>prevMonth"
                class="df-preset <?php echo $_dp === 'prevMonth' ? 'active' : ''; ?>">Prev Month</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>last3m"
                class="df-preset <?php echo $_dp === 'last3m'    ? 'active' : ''; ?>">3 Months</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>thisYear"
                class="df-preset <?php echo $_dp === 'thisYear'  ? 'active' : ''; ?>">This Year</a>
        </div>
        <div class="df-divider"></div>
        <form method="get" action="<?php echo htmlspecialchars($_formTarget); ?>" class="df-custom">
            <?php foreach ($_GET as $_k => $_v): if (!in_array($_k, array('dp', 'df', 'dt'))): ?>
                    <input type="hidden" name="<?php echo htmlspecialchars($_k); ?>"
                        value="<?php echo htmlspecialchars($_v); ?>">
            <?php endif;
            endforeach; ?>
            <input type="hidden" name="dp" value="custom">
            <input type="date" name="df" class="df-date" value="<?php echo htmlspecialchars($_df); ?>" min="2018-01-01"
                max="<?php echo $_today; ?>">
            <span class="df-sep">to</span>
            <input type="date" name="dt" class="df-date" value="<?php echo htmlspecialchars($_dt); ?>" min="2018-01-01"
                max="<?php echo $_today; ?>">
            <button type="submit" class="df-apply">Apply</button>
        </form>
        <span class="df-active-badge">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
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
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
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
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7h12"></path>
                        <path d="M3 12h18"></path>
                        <path d="M3 17h15"></path>
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $stConfirmed > 0 ? 'up' : 'neu'; ?>">dispatch queue</span>
            </div>
            <div class="kpi-value"><?php echo number_format($stConfirmed); ?></div>
            <div class="kpi-label">Ready to Dispatch</div>
            <div class="kpi-meta">Waiting Pickup: <?php echo number_format($stWaiting); ?></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon amber">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3h13v13H3z"></path>
                        <path d="M16 8h4l1 2v6h-5z"></path>
                        <circle cx="7.5" cy="17.5" r="1.5"></circle>
                        <circle cx="17.5" cy="17.5" r="1.5"></circle>
                    </svg>
                </div>
                <?php if ($stDelivered > 0): ?>
                    <span class="kpi-badge up"><?php echo number_format($stDelivered); ?> delivered</span>
                <?php else: ?>
                    <span class="kpi-badge neu">no deliveries yet</span>
                <?php endif; ?>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiInTransit); ?></div>
            <div class="kpi-label">In Transit</div>
            <div class="kpi-meta">Confirmed: <?php echo number_format($stConfirmed); ?> | Waiting: <?php echo number_format($stWaiting); ?></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon violet">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z" />
                        <line x1="7" y1="7" x2="7.01" y2="7" />
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $kpiException > 0 ? 'down' : 'up'; ?>"><?php echo number_format($kpiFulfillmentPct, 1); ?>% fulfilled</span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiException); ?></div>
            <div class="kpi-label">Exceptions</div>
            <div class="kpi-meta">Rejected + Cancelled + RTO</div>
        </div>

    </div>

    <div class="ops-widget-row">
        <a class="ops-widget" href="order/?type=list&amp;status=1">
            <span class="ops-widget-copy">
                <span class="ops-widget-title">Ready to Dispatch</span>
                <span class="ops-widget-value"><?php echo number_format($stConfirmed); ?></span>
            </span>
            <span class="ops-widget-hint">Open queue</span>
        </a>
        <a class="ops-widget" href="order/?type=list&amp;status=4">
            <span class="ops-widget-copy">
                <span class="ops-widget-title">Waiting Pickup</span>
                <span class="ops-widget-value"><?php echo number_format($stWaiting); ?></span>
            </span>
            <span class="ops-widget-hint">Monitor now</span>
        </a>
        <a class="ops-widget" href="order/?type=list&amp;status=6">
            <span class="ops-widget-copy">
                <span class="ops-widget-title">Delivered</span>
                <span class="ops-widget-value"><?php echo number_format($stDelivered); ?></span>
            </span>
            <span class="ops-widget-hint">View proofs</span>
        </a>
        <a class="ops-widget" href="order/?type=list&amp;status=5">
            <span class="ops-widget-copy">
                <span class="ops-widget-title">RTO Risk</span>
                <span class="ops-widget-value"><?php echo number_format($stRto); ?></span>
            </span>
            <span class="ops-widget-hint">Resolve issues</span>
        </a>
    </div>

    <!-- Chart row -->
    <div class="chart-row">

        <div class="dc">
            <div class="panel-head">
                <h3 class="panel-title">Order Activity (Daily Received)</h3>
                <span class="panel-period"><?php echo htmlspecialchars($_activeLabel); ?></span>
            </div>
            <div class="panel-body">
                <div class="chart-wrap"><canvas id="egOrderActivityChart"></canvas></div>
            </div>
        </div>

        <div class="dc">
            <div class="panel-head">
                <h3 class="panel-title">Orders by Status</h3>
                <span class="panel-period"><?php echo htmlspecialchars($_activeLabel); ?></span>
            </div>
            <div class="panel-body">
                <div class="chart-wrap-sm"><canvas id="egStatusChart"></canvas></div>
            </div>
        </div>

    </div>

    <!-- Bottom row -->
    <div class="bottom-row">

        <!-- Recent orders -->
        <div class="dc">
            <div class="panel-head">
                <h3 class="panel-title">Recent Orders</h3>
                <a href="order/?type=list&amp;status=all"
                    style="font-size:12px;color:#2563eb;font-weight:500;text-decoration:none;">View all &rarr;</a>
            </div>
            <div class="panel-body-flush">
                <div class="table-wrap">
                    <table class="dt">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentOrders)): foreach ($recentOrders as $ord):
                                    list($sText, $sClass) = egOrderLabel($ord['order_status']);
                            ?>
                                    <tr>
                                        <td><a href="order/?type=view&amp;id=<?php echo base64_encode($ord['order_id']); ?>"
                                                class="oid">#<?php echo (int)$ord['order_id']; ?></a></td>
                                        <td>
                                            <div class="cust-name"><?php echo htmlspecialchars($ord['profile_display_name']); ?>
                                            </div>
                                            <div class="cust-ph"><?php echo htmlspecialchars($ord['address_mobile']); ?></div>
                                        </td>
                                        <td class="amt-cell">
                                            <?php echo htmlspecialchars($_dashCur) . number_format((float)$ord['order_total_price'], 2); ?>
                                        </td>
                                        <td><span class="chip chip-<?php echo $sClass; ?>"><?php echo $sText; ?></span></td>
                                        <td class="time-cell">
                                            <?php echo date('d M, h:i A', strtotime($ord['order_generate'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">No orders in this period.</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right column -->
        <div class="right-col">
            <!-- Quick actions -->
            <div class="dc">
                <div class="panel-head">
                    <h3 class="panel-title">Quick Actions</h3>
                </div>
                <div class="panel-body">
                    <div class="qa-grid">
                        <a href="order/?type=list&amp;status=0" class="qa-a">
                            <span class="qa-icon">
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
                            <span class="qa-icon">
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
                        <a href="order/?type=list&amp;status=1" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 7h12"></path>
                                    <path d="M3 12h18"></path>
                                    <path d="M3 17h15"></path>
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Dispatch Queue</span>
                                <span class="qa-meta">Process confirmed orders</span>
                            </span>
                        </a>
                        <a href="order/?type=list&amp;status=4" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Waiting Pickup</span>
                                <span class="qa-meta">Follow up with transporter</span>
                            </span>
                        </a>
                        <a href="order/?type=list&amp;status=5" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18"></path>
                                    <path d="M8 6v12"></path>
                                    <path d="M16 6v12"></path>
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">RTO Cases</span>
                                <span class="qa-meta">Reduce return-to-origin loss</span>
                            </span>
                        </a>
                        <a href="order/?type=list&amp;status=all" class="qa-a">
                            <span class="qa-icon">
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
                        <a href="order/?type=import" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="17 8 12 3 7 8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Import Orders</span>
                                <span class="qa-meta">Bulk upload order sheet</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

        </div><!-- /right-col -->

    </div><!-- /bottom-row -->

</div><!-- /egDash -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" crossorigin="anonymous"></script>
<script>
    (function() {
        var orderActivityLabels = <?php echo json_encode($orderActivityChart['labels']); ?>;
        var orderActivityData = <?php echo json_encode($orderActivityChart['data']); ?>;
        var statRaw = <?php echo json_encode($statusCounts); ?>;

        /* Daily order activity chart */
        var orderActivityCtx = document.getElementById('egOrderActivityChart');
        if (orderActivityCtx) {
            new Chart(orderActivityCtx, {
                type: 'bar',
                data: {
                    labels: orderActivityLabels,
                    datasets: [{
                        label: 'Orders Received',
                        data: orderActivityData,
                        backgroundColor: 'rgba(37,99,235,0.10)',
                        borderColor: 'rgba(37,99,235,0.85)',
                        borderWidth: 2,
                        borderRadius: 5,
                        borderSkipped: false,
                        hoverBackgroundColor: 'rgba(37,99,235,0.22)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#94a3b8',
                            bodyColor: '#f1f5f9',
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: {
                                display: false
                            },
                            grid: {
                                color: '#f3f4f6'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#9ca3af'
                            }
                        },
                        x: {
                            border: {
                                display: false
                            },
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#9ca3af',
                                maxRotation: 0
                            }
                        }
                    }
                }
            });
        }

        /* Status doughnut */
        var stCtx = document.getElementById('egStatusChart');
        if (stCtx) {
            var sLabels = ['Pending', 'Confirmed', 'Rejected', 'Cancelled', 'Waiting', 'RTO', 'Delivered'];
            var sColors = ['#f59e0b', '#22c55e', '#ef4444', '#f97316', '#3b82f6', '#8b5cf6', '#10b981'];
            var fL = [],
                fD = [],
                fC = [];
            for (var i = 0; i < statRaw.length; i++) {
                if (statRaw[i] > 0) {
                    fL.push(sLabels[i]);
                    fD.push(statRaw[i]);
                    fC.push(sColors[i]);
                }
            }
            if (!fD.length) {
                fL = ['No orders in period'];
                fD = [1];
                fC = ['#e5e7eb'];
            }

            new Chart(stCtx, {
                type: 'doughnut',
                data: {
                    labels: fL,
                    datasets: [{
                        data: fD,
                        backgroundColor: fC,
                        borderWidth: 0,
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 11
                                },
                                color: '#374151',
                                boxWidth: 10,
                                padding: 12,
                                usePointStyle: true,
                                pointStyleWidth: 10
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#94a3b8',
                            bodyColor: '#f1f5f9',
                            padding: 10,
                            cornerRadius: 8
                        }
                    }
                }
            });
        }
    }());
</script>