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
    $types = array('treatment', 'technology', 'post', 'page');
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
				AND link_type IN ('treatment', 'technology', 'post', 'page')
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
        'treatment' => array_values($maps['treatment']),
        'technology' => array_values($maps['technology']),
        'post' => array_values($maps['post']),
        'page' => array_values($maps['page']),
    );
}

function nmsActivityMonthly($connection, $prefix, $siteId, $from, $to)
{
    $types = array('treatment', 'technology', 'post', 'page');
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
				AND link_type IN ('treatment', 'technology', 'post', 'page')
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
        'treatment' => array_values($maps['treatment']),
        'technology' => array_values($maps['technology']),
        'post' => array_values($maps['post']),
        'page' => array_values($maps['page']),
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

$kpiPages = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}page p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.page_id WHERE l.link_type='page' AND p.page_type='page' AND l.link_site_id={$_dashSid} AND l.link_status!=301");
$kpiPagesLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}page p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.page_id WHERE l.link_type='page' AND p.page_type='page' AND l.link_site_id={$_dashSid} AND l.link_status=200");
$kpiPagesDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}page p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.page_id WHERE l.link_type='page' AND p.page_type='page' AND l.link_site_id={$_dashSid} AND l.link_status=206");

// Blogs
$kpiBlogPosts = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id WHERE l.link_type='post' AND p.post_type='post' AND l.link_site_id={$_dashSid} AND l.link_status!=301");
$kpiBlogPostsLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id WHERE l.link_type='post' AND p.post_type='post' AND l.link_site_id={$_dashSid} AND l.link_status=200");
$kpiBlogPostsDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id WHERE l.link_type='post' AND p.post_type='post' AND l.link_site_id={$_dashSid} AND l.link_status=206");

// Gallery
$kpiGallery = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}gallery g INNER JOIN {$_tpfx}link l ON l.link_relation_id=g.gallery_id WHERE l.link_type='gallery' AND l.link_site_id={$_dashSid} AND l.link_status!=301");
$kpiGalleryLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}gallery g INNER JOIN {$_tpfx}link l ON l.link_relation_id=g.gallery_id WHERE l.link_type='gallery' AND l.link_site_id={$_dashSid} AND l.link_status=200");
$kpiGalleryDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}gallery g INNER JOIN {$_tpfx}link l ON l.link_relation_id=g.gallery_id WHERE l.link_type='gallery' AND l.link_site_id={$_dashSid} AND l.link_status=206");

// Media
$kpiMedia = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}media WHERE media_site_id={$_dashSid}");
$kpiMediaImages = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}media WHERE media_site_id={$_dashSid} AND media_type='image'");
$kpiMediaVideos = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}media WHERE media_site_id={$_dashSid} AND media_type='video'");

// Treatments
$kpiTreatments = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}treatment s INNER JOIN {$_tpfx}link l ON l.link_relation_id=s.treatment_id AND l.link_type='treatment' WHERE s.treatment_site_id={$_dashSid} AND l.link_status!=301");
$kpiTreatmentsLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}treatment s INNER JOIN {$_tpfx}link l ON l.link_relation_id=s.treatment_id AND l.link_type='treatment' WHERE s.treatment_site_id={$_dashSid} AND l.link_status=200");
$kpiTreatmentsDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}treatment s INNER JOIN {$_tpfx}link l ON l.link_relation_id=s.treatment_id AND l.link_type='treatment' WHERE s.treatment_site_id={$_dashSid} AND l.link_status=206");

// Technology
$kpiTechnology = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}technology t INNER JOIN {$_tpfx}link l ON l.link_relation_id=t.technology_id AND l.link_type='technology' WHERE t.technology_site_id={$_dashSid} AND l.link_status!=301");
$kpiTechnologyLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}technology t INNER JOIN {$_tpfx}link l ON l.link_relation_id=t.technology_id AND l.link_type='technology' WHERE t.technology_site_id={$_dashSid} AND l.link_status=200");
$kpiTechnologyDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}technology t INNER JOIN {$_tpfx}link l ON l.link_relation_id=t.technology_id AND l.link_type='technology' WHERE t.technology_site_id={$_dashSid} AND l.link_status=206");

// Packages
$kpiPackages = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id WHERE l.link_type='post' AND p.post_type='package' AND l.link_site_id={$_dashSid} AND l.link_status!=301");
$kpiPackagesLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id WHERE l.link_type='post' AND p.post_type='package' AND l.link_site_id={$_dashSid} AND l.link_status=200");
$kpiPackagesDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id WHERE l.link_type='post' AND p.post_type='package' AND l.link_site_id={$_dashSid} AND l.link_status=206");

// Offers
$kpiOffers = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}page p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.page_id WHERE l.link_type='page' AND p.page_type='offer' AND l.link_site_id={$_dashSid} AND l.link_status!=301");
$kpiOffersLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}page p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.page_id WHERE l.link_type='page' AND p.page_type='offer' AND l.link_site_id={$_dashSid} AND l.link_status=200");
$kpiOffersDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}page p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.page_id WHERE l.link_type='page' AND p.page_type='offer' AND l.link_site_id={$_dashSid} AND l.link_status=206");

// Questions (FAQ)
$kpiQuestions = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id WHERE l.link_type='post' AND p.post_type='question' AND l.link_site_id={$_dashSid} AND l.link_status!=301");
$kpiQuestionsLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id WHERE l.link_type='post' AND p.post_type='question' AND l.link_site_id={$_dashSid} AND l.link_status=200");
$kpiQuestionsDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id WHERE l.link_type='post' AND p.post_type='question' AND l.link_site_id={$_dashSid} AND l.link_status=206");

// Testimonials
$kpiTestimonials = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}category WHERE category_type='testimonial' AND category_site_id={$_dashSid}");
$kpiTestimonialsLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}category WHERE category_type='testimonial' AND category_site_id={$_dashSid} AND category_status=1");
$kpiTestimonialsDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}category WHERE category_type='testimonial' AND category_site_id={$_dashSid} AND category_status=0");

// Social Proof
$kpiSocialProof = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}social_proof sp INNER JOIN {$_tpfx}link l ON l.link_relation_id=sp.sp_id WHERE l.link_type='social_proof' AND l.link_site_id={$_dashSid} AND l.link_status!=301");
$kpiSocialProofLive = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}social_proof sp INNER JOIN {$_tpfx}link l ON l.link_relation_id=sp.sp_id WHERE l.link_type='social_proof' AND l.link_site_id={$_dashSid} AND l.link_status=200");
$kpiSocialProofDraft = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}social_proof sp INNER JOIN {$_tpfx}link l ON l.link_relation_id=sp.sp_id WHERE l.link_type='social_proof' AND l.link_site_id={$_dashSid} AND l.link_status=206");

$teamUsers = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}site_access WHERE site_access_site_id={$_dashSid}");
$activeUsers = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}user u INNER JOIN {$_tpfx}site_access sa ON sa.site_access_user_id=u.user_id WHERE sa.site_access_site_id={$_dashSid} AND u.user_status=1");
$newsletterContacts = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}newsletter WHERE newsletter_site_id={$_dashSid} AND newsletter_status=1");

// Period counters for doughnut chart
$periodTreatments = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}treatment s INNER JOIN {$_tpfx}link l ON l.link_relation_id=s.treatment_id AND l.link_type='treatment' WHERE s.treatment_site_id={$_dashSid} AND l.link_status!=301 AND {$_linkDateWhere}");
$periodTechnology = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}technology t INNER JOIN {$_tpfx}link l ON l.link_relation_id=t.technology_id AND l.link_type='technology' WHERE t.technology_site_id={$_dashSid} AND l.link_status!=301 AND {$_linkDateWhere}");
$periodPackages = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id WHERE l.link_type='post' AND p.post_type='package' AND l.link_site_id={$_dashSid} AND l.link_status!=301 AND {$_linkDateWhere}");
$periodOffers = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}page p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.page_id WHERE l.link_type='page' AND p.page_type='offer' AND l.link_site_id={$_dashSid} AND l.link_status!=301 AND {$_linkDateWhere}");
$periodBlogPosts = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id WHERE l.link_type='post' AND p.post_type='post' AND l.link_site_id={$_dashSid} AND l.link_status!=301 AND {$_linkDateWhere}");
$periodQuestions = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}post p INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id WHERE l.link_type='post' AND p.post_type='question' AND l.link_site_id={$_dashSid} AND l.link_status!=301 AND {$_linkDateWhere}");
$periodSocialProof = nmsDbScalar($connection, "SELECT COUNT(*) total FROM {$_tpfx}social_proof sp INNER JOIN {$_tpfx}link l ON l.link_relation_id=sp.sp_id WHERE l.link_type='social_proof' AND l.link_site_id={$_dashSid} AND l.link_status!=301 AND {$_linkDateWhere}");

$publishedContent = $kpiTreatmentsLive + $kpiTechnologyLive + $kpiPackagesLive + $kpiOffersLive + $kpiQuestionsLive + $kpiBlogPostsLive + $kpiSocialProofLive;
$draftContent = $kpiTreatmentsDraft + $kpiTechnologyDraft + $kpiPackagesDraft + $kpiOffersDraft + $kpiQuestionsDraft + $kpiBlogPostsDraft + $kpiSocialProofDraft;

$recentContent = array();
$_recentContentQuery = "SELECT * FROM (
		SELECT s.treatment_id AS item_id, s.treatment_title AS item_title, 'treatment' AS module_type, l.link_status, l.link_published, l.link_url
		FROM {$_tpfx}treatment s
		INNER JOIN {$_tpfx}link l ON l.link_relation_id=s.treatment_id
		WHERE l.link_type='treatment' AND l.link_site_id={$_dashSid} AND l.link_status!=301

		UNION ALL

		SELECT t.technology_id AS item_id, t.technology_title AS item_title, 'technology' AS module_type, l.link_status, l.link_published, l.link_url
		FROM {$_tpfx}technology t
		INNER JOIN {$_tpfx}link l ON l.link_relation_id=t.technology_id
		WHERE l.link_type='technology' AND l.link_site_id={$_dashSid} AND l.link_status!=301

		UNION ALL

		SELECT p.post_id AS item_id, p.post_title AS item_title, p.post_type AS module_type, l.link_status, l.link_published, l.link_url
		FROM {$_tpfx}post p
		INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.post_id
		WHERE l.link_type='post' AND p.post_type IN ('post', 'package', 'question') AND l.link_site_id={$_dashSid} AND l.link_status!=301

		UNION ALL

		SELECT p.page_id AS item_id, p.page_title AS item_title, p.page_type AS module_type, l.link_status, l.link_published, l.link_url
		FROM {$_tpfx}page p
		INNER JOIN {$_tpfx}link l ON l.link_relation_id=p.page_id
		WHERE l.link_type='page' AND p.page_type IN ('page', 'offer') AND l.link_site_id={$_dashSid} AND l.link_status!=301

		UNION ALL

		SELECT g.gallery_id AS item_id, g.gallery_title AS item_title, 'gallery' AS module_type, l.link_status, l.link_published, l.link_url
		FROM {$_tpfx}gallery g
		INNER JOIN {$_tpfx}link l ON l.link_relation_id=g.gallery_id
		WHERE l.link_type='gallery' AND l.link_site_id={$_dashSid} AND l.link_status!=301

		UNION ALL

		SELECT sp.sp_id AS item_id, sp.sp_title AS item_title, 'social_proof' AS module_type, l.link_status, l.link_published, l.link_url
		FROM {$_tpfx}social_proof sp
		INNER JOIN {$_tpfx}link l ON l.link_relation_id=sp.sp_id
		WHERE l.link_type='social_proof' AND l.link_site_id={$_dashSid} AND l.link_status!=301

		UNION ALL

		SELECT category_id AS item_id, category_title AS item_title, 'testimonial' AS module_type, (CASE WHEN category_status=1 THEN 200 ELSE 206 END) AS link_status, category_time AS link_published, '' AS link_url
		FROM {$_tpfx}category
		WHERE category_type='testimonial' AND category_site_id={$_dashSid}
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
        clear: both;
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

    #egDash .kpi-icon.cyan {
        background: #ecfeff;
        color: #0891b2;
    }

    #egDash .kpi-icon.rose {
        background: #fff1f2;
        color: #e11d48;
    }

    #egDash .kpi-icon.orange {
        background: #fff7ed;
        color: #ea580c;
    }

    #egDash .kpi-icon.teal {
        background: #f0fdf4;
        color: #0f766e;
    }

    #egDash a.kpi-card {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    #egDash a.kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    #egDash .section-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        margin: 24px 0 12px 2px;
        display: flex;
        align-items: center;
        gap: 6px;
        clear: both;
    }

    #egDash .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    #egDash .section-title:first-of-type {
        margin-top: 8px;
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
        border: 1px solid #dbeafe;
    }

    #egDash .chip-post {
        background: #f0fdf4;
        color: #047857;
        border: 1px solid #dcfce7;
    }

    #egDash .chip-gallery {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #ffedd5;
    }

    #egDash .chip-media {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #e9d5ff;
    }

    #egDash .chip-treatment {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #dbeafe;
    }

    #egDash .chip-technology {
        background: #ecfeff;
        color: #0891b2;
        border: 1px solid #cffafe;
    }

    #egDash .chip-package {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #e9d5ff;
    }

    #egDash .chip-offer {
        background: #fff1f2;
        color: #e11d48;
        border: 1px solid #ffe4e6;
    }

    #egDash .chip-question {
        background: #fef9c3;
        color: #a16207;
        border: 1px solid #fef08a;
    }

    #egDash .chip-testimonial {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #dcfce7;
    }

    #egDash .chip-social_proof {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #ffedd5;
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

    <h3 class="section-title">Catalog Overview</h3>
    <div class="kpi-row">
        <a href="treatment/?type=list&amp;status=all" class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon blue">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $kpiTreatmentsDraft > 0 ? 'down' : 'up'; ?>">
                    <?php echo number_format($kpiTreatmentsLive); ?> live
                </span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiTreatments); ?></div>
            <div class="kpi-label">Treatments</div>
            <div class="kpi-meta">Drafts: <?php echo number_format($kpiTreatmentsDraft); ?></div>
        </a>

        <a href="technology/?type=list&amp;status=all" class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon cyan">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="4" width="16" height="16" rx="2" />
                        <rect x="9" y="9" width="6" height="6" />
                        <path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 15h3M1 9h3M1 15h3" />
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $kpiTechnologyDraft > 0 ? 'down' : 'up'; ?>">
                    <?php echo number_format($kpiTechnologyLive); ?> live
                </span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiTechnology); ?></div>
            <div class="kpi-label">Technology</div>
            <div class="kpi-meta">Drafts: <?php echo number_format($kpiTechnologyDraft); ?></div>
        </a>

        <a href="package/?type=list&amp;status=all" class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon violet">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                        <line x1="12" y1="22.08" x2="12" y2="12" />
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $kpiPackagesDraft > 0 ? 'down' : 'up'; ?>">
                    <?php echo number_format($kpiPackagesLive); ?> live
                </span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiPackages); ?></div>
            <div class="kpi-label">Packages</div>
            <div class="kpi-meta">Drafts: <?php echo number_format($kpiPackagesDraft); ?></div>
        </a>

        <a href="offer/?type=list&amp;status=all" class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon rose">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                        <line x1="7" y1="7" x2="7.01" y2="7" />
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $kpiOffersDraft > 0 ? 'down' : 'up'; ?>">
                    <?php echo number_format($kpiOffersLive); ?> live
                </span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiOffers); ?></div>
            <div class="kpi-label">Latest Offers</div>
            <div class="kpi-meta">Drafts: <?php echo number_format($kpiOffersDraft); ?></div>
        </a>
    </div>

    <h3 class="section-title">Engagement &amp; Trust</h3>
    <div class="kpi-row">
        <a href="question/?type=list&amp;status=all" class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon amber">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $kpiQuestionsDraft > 0 ? 'down' : 'up'; ?>">
                    <?php echo number_format($kpiQuestionsLive); ?> live
                </span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiQuestions); ?></div>
            <div class="kpi-label">Questions (FAQ's)</div>
            <div class="kpi-meta">Drafts: <?php echo number_format($kpiQuestionsDraft); ?></div>
        </a>

        <a href="testimonial/?type=list&amp;status=all" class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon green">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $kpiTestimonialsDraft > 0 ? 'down' : 'up'; ?>">
                    <?php echo number_format($kpiTestimonialsLive); ?> live
                </span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiTestimonials); ?></div>
            <div class="kpi-label">Testimonials</div>
            <div class="kpi-meta">Drafts: <?php echo number_format($kpiTestimonialsDraft); ?></div>
        </a>

        <a href="social_proof/?type=list&amp;status=all" class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon orange">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="23 7 16 12 23 17 23 7" />
                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $kpiSocialProofDraft > 0 ? 'down' : 'up'; ?>">
                    <?php echo number_format($kpiSocialProofLive); ?> live
                </span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiSocialProof); ?></div>
            <div class="kpi-label">Social Video Hub</div>
            <div class="kpi-meta">Drafts: <?php echo number_format($kpiSocialProofDraft); ?></div>
        </a>

        <a href="post/?type=list&amp;status=all" class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon teal">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                    </svg>
                </div>
                <span class="kpi-badge <?php echo $kpiBlogPostsDraft > 0 ? 'down' : 'up'; ?>">
                    <?php echo number_format($kpiBlogPostsLive); ?> live
                </span>
            </div>
            <div class="kpi-value"><?php echo number_format($kpiBlogPosts); ?></div>
            <div class="kpi-label">Blog Posts</div>
            <div class="kpi-meta">Drafts: <?php echo number_format($kpiBlogPostsDraft); ?></div>
        </a>
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
                                                class="chip chip-<?php echo htmlspecialchars($item['module_type']); ?>"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $item['module_type']))); ?></span>
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
                <div class="panel-body">                    <div class="qa-grid">
                        <a href="treatment/?type=add&amp;editor=true" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Add Treatment</span>
                                <span class="qa-meta">Define clinic service</span>
                            </span>
                        </a>
                        <a href="technology/?type=add&amp;editor=true" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="4" y="4" width="16" height="16" rx="2" />
                                    <rect x="9" y="9" width="6" height="6" />
                                    <path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 15h3M1 9h3M1 15h3" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Add Technology</span>
                                <span class="qa-meta">Add clinic equipment</span>
                            </span>
                        </a>
                        <a href="package/?type=add&amp;editor=true" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                                    <line x1="12" y1="22.08" x2="12" y2="12" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Add Package</span>
                                <span class="qa-meta">Create bundled offer</span>
                            </span>
                        </a>
                        <a href="offer/?type=add&amp;editor=true" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                                    <line x1="7" y1="7" x2="7.01" y2="7" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Add Offer</span>
                                <span class="qa-meta">Create promotion tag</span>
                            </span>
                        </a>
                        <a href="post/?type=add&amp;editor=true" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Add Blog Post</span>
                                <span class="qa-meta">Write fresh article</span>
                            </span>
                        </a>
                        <a href="testimonial/?type=add&amp;editor=true" class="qa-a">
                            <span class="qa-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                </svg>
                            </span>
                            <span class="qa-copy">
                                <span class="qa-title">Add Review</span>
                                <span class="qa-meta">Add patient feedback</span>
                            </span>
                        </a>
                    </div>>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" crossorigin="anonymous"></script>
<script>
    (function() {
        var activityLabels = <?php echo json_encode($activityChart['labels']); ?>;
        var treatmentData = <?php echo json_encode($activityChart['treatment']); ?>;
        var technologyData = <?php echo json_encode($activityChart['technology']); ?>;
        var postData = <?php echo json_encode($activityChart['post']); ?>;
        var pageData = <?php echo json_encode($activityChart['page']); ?>;

        var mixLabels = ['Treatments', 'Technology', 'Packages', 'Offers', 'Blog Posts', 'FAQs', 'Social Videos'];
        var mixData = [
            <?php echo (int) $periodTreatments; ?>,
            <?php echo (int) $periodTechnology; ?>,
            <?php echo (int) $periodPackages; ?>,
            <?php echo (int) $periodOffers; ?>,
            <?php echo (int) $periodBlogPosts; ?>,
            <?php echo (int) $periodQuestions; ?>,
            <?php echo (int) $periodSocialProof; ?>
        ];

        var activityCtx = document.getElementById('nmsActivityChart');
        if (activityCtx) {
            new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: activityLabels,
                    datasets: [{
                            label: 'Treatments',
                            data: treatmentData,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37,99,235,0.10)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 2,
                            pointHoverRadius: 4
                        },
                        {
                            label: 'Technology',
                            data: technologyData,
                            borderColor: '#0891b2',
                            backgroundColor: 'rgba(8,145,178,0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 2,
                            pointHoverRadius: 4
                        },
                        {
                            label: 'Blogs/Posts',
                            data: postData,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16,185,129,0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 2,
                            pointHoverRadius: 4
                        },
                        {
                            label: 'Pages',
                            data: pageData,
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
            var mixColors = ['#2563eb', '#0891b2', '#7c3aed', '#e11d48', '#10b981', '#a16207', '#ea580c'];

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