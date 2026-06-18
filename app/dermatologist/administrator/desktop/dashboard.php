<?php

$_dashSid = defined('APP_SITE_ID') ? (int) APP_SITE_ID : (int) SITE_ID;
$_tpfx = TABLE_PREFIX;

function nmsDbScalar($connection, $query, $field = 'total')
{
    if ($result = mysqli_query($connection, $query)) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return isset($row[$field]) ? (float) $row[$field] : 0;
        }
    }

    return 0;
}

function nmsSanitizeDate($value, $fallback)
{
    if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) && strtotime($value) !== false) {
        return $value;
    }

    return $fallback;
}

function nmsActivityDaily($connection, $prefix, $siteId, $from, $to)
{
    $types = array('page', 'post', 'gallery', 'media');
    $labels = array();
    $maps = array();

    foreach ($types as $type) {
        $maps[$type] = array();
    }

    $cursor = strtotime($from);
    $end = strtotime($to);

    while ($cursor <= $end) {
        $key = date('Y-m-d', $cursor);
        $labels[] = date('d M', $cursor);

        foreach ($types as $type) {
            $maps[$type][$key] = 0;
        }

        $cursor += 86400;
    }

    $query = "SELECT DATE(link_published) published_on, link_type, COUNT(*) total
			  FROM {$prefix}link
			  WHERE link_site_id={$siteId}
				AND link_type IN ('page', 'post', 'gallery', 'media')
				AND link_status!=301
				AND DATE(link_published) BETWEEN '{$from}' AND '{$to}'
			  GROUP BY DATE(link_published), link_type";

    if ($result = mysqli_query($connection, $query)) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (isset($maps[$row['link_type']][$row['published_on']])) {
                $maps[$row['link_type']][$row['published_on']] = (int) $row['total'];
            }
        }
    }

    return array(
        'labels' => $labels,
        'page' => array_values($maps['page']),
        'post' => array_values($maps['post']),
        'gallery' => array_values($maps['gallery']),
        'media' => array_values($maps['media']),
    );
}

function nmsActivityMonthly($connection, $prefix, $siteId, $from, $to)
{
    $types = array('page', 'post', 'gallery', 'media');
    $labels = array();
    $maps = array();

    foreach ($types as $type) {
        $maps[$type] = array();
    }

    $cursor = strtotime(date('Y-m-01', strtotime($from)));
    $end = strtotime(date('Y-m-01', strtotime($to)));

    while ($cursor <= $end) {
        $key = date('Y-m', $cursor);
        $labels[] = date('M Y', $cursor);

        foreach ($types as $type) {
            $maps[$type][$key] = 0;
        }

        $cursor = strtotime('+1 month', $cursor);
    }

    $query = "SELECT DATE_FORMAT(link_published, '%Y-%m') published_on, link_type, COUNT(*) total
			  FROM {$prefix}link
			  WHERE link_site_id={$siteId}
				AND link_type IN ('page', 'post', 'gallery', 'media')
				AND link_status!=301
				AND link_published BETWEEN '{$from} 00:00:00' AND '{$to} 23:59:59'
			  GROUP BY DATE_FORMAT(link_published, '%Y-%m'), link_type";

    if ($result = mysqli_query($connection, $query)) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (isset($maps[$row['link_type']][$row['published_on']])) {
                $maps[$row['link_type']][$row['published_on']] = (int) $row['total'];
            }
        }
    }

    return array(
        'labels' => $labels,
        'page' => array_values($maps['page']),
        'post' => array_values($maps['post']),
        'gallery' => array_values($maps['gallery']),
        'media' => array_values($maps['media']),
    );
}

function nmsStatusMeta($status)
{
    $status = (int) $status;

    if ($status === 200) {
        return array('Live', 'live');
    }

    if ($status === 206) {
        return array('Draft', 'draft');
    }

    if ($status === 301) {
        return array('Archived', 'archived');
    }

    return array('Pending', 'pending');
}

$_today = date('Y-m-d');
$_validDp = array('last7', 'last30', 'thisMonth', 'prevMonth', 'last3m', 'thisYear', 'custom');
$_dp = (isset($_GET['dp']) && in_array($_GET['dp'], $_validDp, true)) ? $_GET['dp'] : 'last30';

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
        $_dt = date('Y-m-t', strtotime('last day of last month'));
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
        $_df = nmsSanitizeDate(isset($_GET['df']) ? $_GET['df'] : '', date('Y-m-d', strtotime('-29 days')));
        $_dt = nmsSanitizeDate(isset($_GET['dt']) ? $_GET['dt'] : '', $_today);
        if ($_df > $_dt) {
            $_dt = $_df;
        }
        break;
    default:
        $_df = date('Y-m-d', strtotime('-29 days'));
        $_dt = $_today;
        break;
}

$_filterLabel = array(
    'last7' => 'Last 7 Days',
    'last30' => 'Last 30 Days',
    'thisMonth' => 'This Month',
    'prevMonth' => 'Previous Month',
    'last3m' => 'Last 3 Months',
    'thisYear' => 'This Year',
    'custom' => date('d M Y', strtotime($_df)) . ' to ' . date('d M Y', strtotime($_dt)),
);
$_activeLabel = $_filterLabel[$_dp];

$_baseUrl = '?';
if (isset($_GET['type'])) {
    $_baseUrl .= 'type=' . urlencode($_GET['type']) . '&';
}
$_baseUrl .= 'dp=';

$_linkDateWhere = "DATE(link_published) BETWEEN '{$_df}' AND '{$_dt}'";

$kpiPages = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}page p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.page_id WHERE l.link_type='page' AND l.link_site_id={$_dashSid} AND l.link_status!=301");
$kpiPagesLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}page p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.page_id WHERE l.link_type='page' AND l.link_site_id={$_dashSid} AND l.link_status=200");
$kpiPagesDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}page p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.page_id WHERE l.link_type='page' AND l.link_site_id={$_dashSid} AND l.link_status=206");

$kpiPost = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post n INNER JOIN {$_tpfx}link l ON l.link_relation_id=n.post_id WHERE l.link_type='post' AND l.link_site_id={$_dashSid} AND l.link_status!=301");
$kpiPostLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post n INNER JOIN {$_tpfx}link l ON l.link_relation_id=n.post_id WHERE l.link_type='post' AND l.link_site_id={$_dashSid} AND l.link_status=200");
$kpiPostDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post n INNER JOIN {$_tpfx}link l ON l.link_relation_id=n.post_id WHERE l.link_type='post' AND l.link_site_id={$_dashSid} AND l.link_status=206");

$kpiGallery = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}gallery g INNER JOIN {$_tpfx}link l ON l.link_relation_id=g.gallery_id WHERE l.link_type='gallery' AND l.link_site_id={$_dashSid} AND l.link_status!=301");
$kpiGalleryLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}gallery g INNER JOIN {$_tpfx}link l ON l.link_relation_id=g.gallery_id WHERE l.link_type='gallery' AND l.link_site_id={$_dashSid} AND l.link_status=200");
$kpiGalleryDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}gallery g INNER JOIN {$_tpfx}link l ON l.link_relation_id=g.gallery_id WHERE l.link_type='gallery' AND l.link_site_id={$_dashSid} AND l.link_status=206");

$kpiMedia = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}media WHERE media_site_id={$_dashSid}");
$kpiMediaImages = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}media WHERE media_site_id={$_dashSid} AND media_type='image'");
$kpiMediaVideos = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}media WHERE media_site_id={$_dashSid} AND media_type='video'");

$teamUsers = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}site_access WHERE site_access_site_id={$_dashSid}");
$activeUsers = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}user u INNER JOIN {$_tpfx}site_access sa ON sa.site_access_user_id=u.user_id WHERE sa.site_access_site_id={$_dashSid} AND u.user_status=1");
$newsletterContacts = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}newsletter WHERE newsletter_site_id={$_dashSid} AND newsletter_status=1");

$periodPages = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}link WHERE link_type='page' AND link_site_id={$_dashSid} AND link_status!=301 AND {$_linkDateWhere}");
$periodPost = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}link WHERE link_type='post' AND link_site_id={$_dashSid} AND link_status!=301 AND {$_linkDateWhere}");
$periodGalleries = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}link WHERE link_type='gallery' AND link_site_id={$_dashSid} AND link_status!=301 AND {$_linkDateWhere}");
$periodMedia = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}link WHERE link_type='media' AND link_site_id={$_dashSid} AND link_status!=301 AND {$_linkDateWhere}");

$publishedContent = $kpiPagesLive + $kpiPostLive + $kpiGalleryLive;
$draftContent = $kpiPagesDraft + $kpiPostDraft + $kpiGalleryDraft;

$recentContent = array();
$_recentContentQuery = "SELECT * FROM (
		SELECT p.page_id AS item_id, p.page_title AS item_title, 'page' AS module_type, l.link_status, l.link_published, l.link_url
		FROM {$_tpfx}page p
		INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.page_id
		WHERE l.link_type='page' AND l.link_site_id={$_dashSid} AND l.link_status!=301

		UNION ALL

		SELECT n.post_id AS item_id, n.post_title AS item_title, 'post' AS module_type, l.link_status, l.link_published, l.link_url
		FROM {$_tpfx}post n
		INNER JOIN {$_tpfx}link l ON l.link_relation_id=n.post_id
		WHERE l.link_type='post' AND l.link_site_id={$_dashSid} AND l.link_status!=301

		UNION ALL

		SELECT g.gallery_id AS item_id, g.gallery_title AS item_title, 'gallery' AS module_type, l.link_status, l.link_published, l.link_url
		FROM {$_tpfx}gallery g
		INNER JOIN {$_tpfx}link l ON l.link_relation_id=g.gallery_id
		WHERE l.link_type='gallery' AND l.link_site_id={$_dashSid} AND l.link_status!=301
	) recent_content
	ORDER BY link_published DESC
	LIMIT 10";

if ($_recentResult = mysqli_query($connection, $_recentContentQuery)) {
    while ($row = mysqli_fetch_assoc($_recentResult)) {
        $recentContent[] = $row;
    }
}

$recentMedia = array();
$_recentMediaQuery = "SELECT media_id, media_title, media_type, media_url,
		(SELECT link_published FROM {$_tpfx}link WHERE link_relation_id=media_id AND link_type='media' AND link_site_id={$_dashSid} ORDER BY link_published DESC LIMIT 1) AS uploaded_at
	FROM {$_tpfx}media
	WHERE media_site_id={$_dashSid}
	ORDER BY media_id DESC
	LIMIT 6";

if ($_recentMediaResult = mysqli_query($connection, $_recentMediaQuery)) {
    while ($row = mysqli_fetch_assoc($_recentMediaResult)) {
        $recentMedia[] = $row;
    }
}

$_rangeDays = max(1, round((strtotime($_dt) - strtotime($_df)) / 86400));
$activityChart = ($_rangeDays <= 62)
    ? nmsActivityDaily($connection, $_tpfx, $_dashSid, $_df, $_dt)
    : nmsActivityMonthly($connection, $_tpfx, $_dashSid, $_df, $_dt);

$_formTarget = '?';
foreach ($_GET as $_key => $_value) {
    if (!in_array($_key, array('dp', 'df', 'dt'), true)) {
        $_formTarget .= urlencode($_key) . '=' . urlencode($_value) . '&';
    }
}
$_formTarget = rtrim($_formTarget, '?&');

?>

<style>
    #egDash {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        padding: 4px 0 48px;
        color: #111827;
        float: left;
        width: 100%;
    }

    #egDash * {
        box-sizing: border-box;
    }

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
        flex-wrap: wrap;
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

    #egDash .dc {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, .06);
        box-shadow: 0 1px 4px rgba(0, 0, 0, .04), 0 0 0 1px rgba(0, 0, 0, .02);
    }

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

    #egDash .panel-link {
        font-size: 12px;
        color: #2563eb;
        font-weight: 500;
        text-decoration: none;
    }

    #egDash .panel-body {
        padding: 18px 20px 20px;
    }

    #egDash .panel-body-flush {
        padding: 0 6px 10px;
    }

    #egDash .chart-wrap {
        position: relative;
        height: 240px;
    }

    #egDash .chart-wrap-sm {
        position: relative;
        height: 220px;
    }

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

    #egDash .dt tbody tr:nth-child(2n) td {
        background: #fcfdff;
    }

    #egDash .dt tbody tr:last-child td {
        border-bottom: none;
    }

    #egDash .dt tbody tr:hover td {
        background: #f3f7ff;
    }

    #egDash .content-title {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
        text-decoration: none;
        line-height: 1.35;
    }

    #egDash .content-title:hover {
        color: #2563eb;
    }

    #egDash .content-sub {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 2px;
    }

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

    #egDash .chip-live {
        background: #ecfdf5;
        color: #047857;
    }

    #egDash .chip-draft {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    #egDash .chip-archived {
        background: #fff7ed;
        color: #c2410c;
    }

    #egDash .chip-pending {
        background: #eff6ff;
        color: #1d4ed8;
    }

    #egDash .chip-page {
        background: #eff6ff;
        color: #1d4ed8;
    }

    #egDash .chip-post {
        background: #f0fdf4;
        color: #047857;
    }

    #egDash .chip-gallery {
        background: #fff7ed;
        color: #c2410c;
    }

    #egDash .chip-media {
        background: #f5f3ff;
        color: #6d28d9;
    }

    #egDash .time-cell {
        font-size: 11px;
        color: #9ca3af;
        white-space: nowrap;
    }

    #egDash .mini-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    #egDash .mini-card {
        border: 1px solid #eef2f7;
        border-radius: 12px;
        padding: 14px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    #egDash .mini-value {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1;
    }

    #egDash .mini-label {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-top: 10px;
    }

    #egDash .mini-meta {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 4px;
    }

    #egDash .media-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    #egDash .media-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f8fafc;
    }

    #egDash .media-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    #egDash .media-item:first-child {
        padding-top: 0;
    }

    #egDash .media-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        flex-shrink: 0;
    }

    #egDash .media-icon.image {
        background: #eff6ff;
        color: #2563eb;
    }

    #egDash .media-icon.video {
        background: #fff7ed;
        color: #ea580c;
    }

    #egDash .media-icon.file {
        background: #f3f4f6;
        color: #4b5563;
    }

    #egDash .media-copy {
        min-width: 0;
        flex: 1;
    }

    #egDash .media-title {
        font-size: 12px;
        font-weight: 600;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #egDash .media-meta {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 2px;
    }

    #egDash .empty-state {
        padding: 28px 12px;
        text-align: center;
        color: #9ca3af;
        font-size: 13px;
    }

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

    @media (max-width: 1200px) {
        #egDash .kpi-row {
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

        #egDash .kpi-row,
        #egDash .mini-grid,
        #egDash .qa-grid {
            grid-template-columns: 1fr;
        }

        #egDash .dh-head {
            flex-direction: column;
        }

        #egDash .df-divider {
            display: none;
        }
    }
</style>

<div id="egDash">
    <div class="dh-head">
        <div>
            <h2 class="dh-title">Dashboard</h2>
            <div class="dh-sub">Content overview - <?php echo date('l, d F Y'); ?></div>
        </div>
        <div class="dh-actions">
            <a href="post/?type=list&amp;status=all" class="dh-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 3h7v7" />
                    <path d="M10 14L21 3" />
                    <path d="M21 14v7h-7" />
                    <path d="M3 10L14 21" />
                    <path d="M10 3H3v7" />
                    <path d="M21 10L10 21" />
                </svg>
                Latest blogs
            </a>
            <a href="page/?type=add&amp;editor=true" class="dh-btn primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Add Page
            </a>
        </div>
    </div>

    <div class="df-bar">
        <span class="df-label">Filter</span>
        <div class="df-presets">
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>last7"
                class="df-preset <?php echo $_dp === 'last7' ? 'active' : ''; ?>">Last 7 Days</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>last30"
                class="df-preset <?php echo $_dp === 'last30' ? 'active' : ''; ?>">Last 30 Days</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>thisMonth"
                class="df-preset <?php echo $_dp === 'thisMonth' ? 'active' : ''; ?>">This Month</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>prevMonth"
                class="df-preset <?php echo $_dp === 'prevMonth' ? 'active' : ''; ?>">Prev Month</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>last3m"
                class="df-preset <?php echo $_dp === 'last3m' ? 'active' : ''; ?>">3 Months</a>
            <a href="<?php echo htmlspecialchars($_baseUrl); ?>thisYear"
                class="df-preset <?php echo $_dp === 'thisYear' ? 'active' : ''; ?>">This Year</a>
        </div>
        <div class="df-divider"></div>
        <form method="get" action="<?php echo htmlspecialchars($_formTarget); ?>" class="df-custom">
            <?php foreach ($_GET as $_key => $_value): ?>
                <?php if (!in_array($_key, array('dp', 'df', 'dt'), true)): ?>
                    <input type="hidden" name="<?php echo htmlspecialchars($_key); ?>"
                        value="<?php echo htmlspecialchars($_value); ?>">
                <?php endif; ?>
            <?php endforeach; ?>
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

    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon blue">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="8" y1="13" x2="16" y2="13" />
                        <line x1="8" y1="17" x2="13" y2="17" />
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $kpiPagesDraft > 0 ? 'down' : 'up'; ?>">
                    <?php echo number_format($kpiPagesLive); ?> live
                </span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiPages); ?></div>
            <div class="kpi-label">Pages</div>
            <div class="kpi-meta">Drafts: <?php echo number_format($kpiPagesDraft); ?></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon green">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $kpiPostDraft > 0 ? 'down' : 'up'; ?>">
                    <?php echo number_format($kpiPostLive); ?> live
                </span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiPost); ?></div>
            <div class="kpi-label">Blog posts</div>
            <div class="kpi-meta">Drafts: <?php echo number_format($kpiPostDraft); ?></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon amber">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $kpiGalleryDraft > 0 ? 'neu' : 'up'; ?>">
                    <?php echo number_format($kpiGalleryLive); ?> live
                </span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiGallery); ?></div>
            <div class="kpi-label">Galleries</div>
            <div class="kpi-meta">Drafts: <?php echo number_format($kpiGalleryDraft); ?></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon violet">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="17 8 12 3 7 8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                    </svg>
                </div>
                <span class="kpi-badge neu"><?php echo number_format($kpiMediaImages); ?> images</span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiMedia); ?></div>
            <div class="kpi-label">Media Files</div>
            <div class="kpi-meta">Videos: <?php echo number_format($kpiMediaVideos); ?></div>
        </div>
    </div>

    <div class="chart-row">
        <div class="dc">
            <div class="panel-head">
                <h3 class="panel-title">Publishing Activity</h3>
                <span class="panel-period"><?php echo htmlspecialchars($_activeLabel); ?></span>
            </div>
            <div class="panel-body">
                <div class="chart-wrap"><canvas id="nmsActivityChart"></canvas></div>
            </div>
        </div>

        <div class="dc">
            <div class="panel-head">
                <h3 class="panel-title">Content Mix</h3>
                <span class="panel-period"><?php echo htmlspecialchars($_activeLabel); ?></span>
            </div>
            <div class="panel-body">
                <div class="chart-wrap-sm"><canvas id="nmsMixChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="bottom-row">
        <div class="dc">
            <div class="panel-head">
                <h3 class="panel-title">Recent Content</h3>
                <a href="post/?type=list&amp;status=all" class="panel-link">View all</a>
            </div>
            <div class="panel-body-flush">
                <div class="table-wrap">
                    <table class="dt">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Module</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentContent)): ?>
                                <?php foreach ($recentContent as $item): ?>
                                    <?php
                                    list($statusText, $statusClass) = nmsStatusMeta($item['link_status']);
                                    $encodedId = base64_encode($item['item_id']);
                                    $editPath = $item['module_type'] . '/?type=edit&editor=true&id=' . $encodedId;
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo htmlspecialchars($editPath); ?>" class="content-title">
                                                <?php echo htmlspecialchars($item['item_title']); ?>
                                            </a>
                                            <div class="content-sub"><?php echo htmlspecialchars($item['link_url']); ?></div>
                                        </td>
                                        <td><span
                                                class="chip chip-<?php echo htmlspecialchars($item['module_type']); ?>"><?php echo htmlspecialchars(ucwords($item['module_type'])); ?></span>
                                        </td>
                                        <td><span
                                                class="chip chip-<?php echo htmlspecialchars($statusClass); ?>"><?php echo htmlspecialchars($statusText); ?></span>
                                        </td>
                                        <td class="time-cell">
                                            <?php echo !empty($item['link_published']) ? date('d M, h:i A', strtotime($item['link_published'])) : '-'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">
                                        <div class="empty-state">No recent content found.</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="right-col">
            <div class="dc">
                <div class="panel-head">
                    <h3 class="panel-title">Team and Audience</h3>
                </div>
                <div class="panel-body">
                    <div class="mini-grid">
                        <div class="mini-card">
                            <div class="mini-value"><?php echo number_format($teamUsers); ?></div>
                            <div class="mini-label">Users</div>
                            <div class="mini-meta"><?php echo number_format($activeUsers); ?> active accounts</div>
                        </div>
                        <div class="mini-card">
                            <div class="mini-value"><?php echo number_format($newsletterContacts); ?></div>
                            <div class="mini-label">Postletters</div>
                            <div class="mini-meta">Subscriber records</div>
                        </div>
                        <div class="mini-card">
                            <div class="mini-value"><?php echo number_format($publishedContent); ?></div>
                            <div class="mini-label">Published</div>
                            <div class="mini-meta">Live pages, post and galleries</div>
                        </div>
                        <div class="mini-card">
                            <div class="mini-value"><?php echo number_format($draftContent); ?></div>
                            <div class="mini-label">Drafts</div>
                            <div class="mini-meta">Items pending publication</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dc">
                <div class="panel-head">
                    <h3 class="panel-title">Recent Media</h3>
                    <a href="media/?type=list" class="panel-link">Open library</a>
                </div>

                <div class="panel-body">
                    <?php if (!empty($recentMedia)): ?>
                        <div class="media-list">

                            <?php foreach ($recentMedia as $media): ?>
                                <?php
                                $rawType = strtolower(trim($media['media_type'] ?? ''));
                                $type = explode('/', $rawType)[0];

                                $mediaType = match ($type) {
                                    'image' => 'image',
                                    'video' => 'video',
                                    'audio' => 'audio',
                                    default => 'file',
                                };

                                $mediaTitle = trim($media['media_title']) ?: 'Untitled media';

                                $icons = [
                                    'image' => 'fa fa-image',
                                    'video' => 'fa fa-video',
                                    'audio' => 'fa fa-music',
                                    'file'  => 'fa fa-file',
                                ];

                                $iconPath = $icons[$mediaType] ?? $icons['file'];
                                ?>

                                <div class="media-item">

                                    <!-- LEFT ICON -->
                                    <div class="media-icon <?php echo $mediaType; ?>">
                                        <i class="<?php echo $iconPath; ?>"></i>
                                    </div>

                                    <!-- CONTENT -->
                                    <div class="media-content">

                                        <div class="media-top">
                                            <div class="media-title">

                                                <a href="<?php echo htmlspecialchars($media['media_url']); ?>"
                                                    target="_blank">
                                                    <?php echo htmlspecialchars($mediaTitle); ?> <i class="fa fa-external-link-alt"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="media-meta">
                                            <?php echo ucfirst($mediaType); ?>
                                            <?php if (!empty($media['uploaded_at'])): ?>
                                                · <?php echo date('d M Y', strtotime($media['uploaded_at'])); ?>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>

                            <?php endforeach; ?>

                        </div>
                    <?php else: ?>
                        <div class="empty-state">No media uploaded yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dc">
                <div class="panel-head">
                    <h3 class="panel-title">Quick Actions</h3>
                </div>
                <div class="panel-body">
                    <div class="qa-grid">
                        <a href="page/?type=add&amp;editor=true" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Add Page</span>
                                <span class="qa-meta">Create a new page</span>
                            </span>
                        </a>
                        <a href="post/?type=add&amp;editor=true" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Add new blog</span>
                                <span class="qa-meta">Publish a fresh update</span>
                            </span>
                        </a>
                        <a href="gallery/?type=add&amp;editor=true" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                    <circle cx="8.5" cy="8.5" r="1.5" />
                                    <polyline points="21 15 16 10 5 21" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Add Gallery</span>
                                <span class="qa-meta">Create a visual collection</span>
                            </span>
                        </a>
                        <a href="media/?type=list" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Media Library</span>
                                <span class="qa-meta">Browse uploaded files</span>
                            </span>
                        </a>
                        <a href="user/?type=list&amp;status=all" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Manage Users</span>
                                <span class="qa-meta">Review team access</span>
                            </span>
                        </a>
                        <a href="setting/?type=configuration" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3" />
                                    <path
                                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09A1.65 1.65 0 0 0 15 4.6a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.36.52.56 1.14.6 1.77.04.63-.09 1.26-.37 1.82.28.56.41 1.19.37 1.82A3.7 3.7 0 0 1 19.4 15z" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Settings</span>
                                <span class="qa-meta">Open configuration</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" crossorigin="anonymous"></script>
<script>
    (function() {
        var activityLabels = <?php echo json_encode($activityChart['labels']); ?>;
        var pageData = <?php echo json_encode($activityChart['page']); ?>;
        var newsData = <?php echo json_encode($activityChart['post']); ?>;
        var galleryData = <?php echo json_encode($activityChart['gallery']); ?>;
        var mediaData = <?php echo json_encode($activityChart['media']); ?>;

        var mixLabels = ['Pages', 'Blog', 'Galleries', 'Media'];
        var mixData = [<?php echo (int) $periodPages; ?>, <?php echo (int) $periodPost; ?>,
            <?php echo (int) $periodGalleries; ?>, <?php echo (int) $periodMedia; ?>
        ];

        var activityCtx = document.getElementById('nmsActivityChart');
        if (activityCtx) {
            new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: activityLabels,
                    datasets: [{
                            label: 'Pages',
                            data: pageData,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37,99,235,0.10)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 2,
                            pointHoverRadius: 4
                        },
                        {
                            label: 'Blog',
                            data: newsData,
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5,150,105,0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 2,
                            pointHoverRadius: 4
                        },
                        {
                            label: 'Galleries',
                            data: galleryData,
                            borderColor: '#ea580c',
                            backgroundColor: 'rgba(234,88,12,0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 2,
                            pointHoverRadius: 4
                        },
                        {
                            label: 'Media',
                            data: mediaData,
                            borderColor: '#7c3aed',
                            backgroundColor: 'rgba(124,58,237,0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 2,
                            pointHoverRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                color: '#475569',
                                font: {
                                    size: 11,
                                    weight: '600'
                                }
                            }
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
                                color: '#f1f5f9'
                            },
                            ticks: {
                                precision: 0,
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                }
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
                                color: '#94a3b8',
                                maxRotation: 0,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }

        var mixCtx = document.getElementById('nmsMixChart');
        if (mixCtx) {
            var filteredLabels = [];
            var filteredData = [];
            var filteredColors = [];
            var mixColors = ['#2563eb', '#10b981', '#f97316', '#8b5cf6'];

            for (var i = 0; i < mixData.length; i++) {
                if (mixData[i] > 0) {
                    filteredLabels.push(mixLabels[i]);
                    filteredData.push(mixData[i]);
                    filteredColors.push(mixColors[i]);
                }
            }

            if (!filteredData.length) {
                filteredLabels = ['No content in period'];
                filteredData = [1];
                filteredColors = ['#e5e7eb'];
            }

            new Chart(mixCtx, {
                type: 'doughnut',
                data: {
                    labels: filteredLabels,
                    datasets: [{
                        data: filteredData,
                        backgroundColor: filteredColors,
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