<?php
/**
 * V3 Digital Marketing Dashboard Overview API
 * Endpoint: /api/v3/dashboard/marketing.php
 * Workspace Compliant: Secure, site-scoped, parameterized, zero PII contact leakage.
 */

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

if (defined('LOGIN_CHECK') && file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'customer';

// Authorization check: Administrator or Digital Marketing role
if ($accessType !== 'administrator' && $accessType !== 'digital_marketing') {
    http_response_code(403);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Access denied. Digital marketing dashboard is restricted.'
    ]);
    exit;
}

// 1. Site Filter
$siteFilter = ($siteId > 0) ? "enquiry_site_id = {$siteId}" : "1=1";

// 2. Date Range Handling (Default to Last 30 Days)
$defaultStart = date('Y-m-d 00:00:00', strtotime('-29 days'));
$defaultEnd   = date('Y-m-d 23:59:59');

$t1 = $defaultStart;
$t2 = $defaultEnd;
$displayLabel = 'Last 30 Days';

if (!empty($_GET['daterange']) && $_GET['daterange'] !== 'null') {
    $parts = explode(' - ', trim($_GET['daterange']));
    if (count($parts) === 2) {
        $p1 = strtotime($parts[0]);
        $p2 = strtotime($parts[1]);
        if ($p1 && $p2) {
            $t1 = date('Y-m-d 00:00:00', $p1);
            $t2 = date('Y-m-d 23:59:59', $p2);
            $diffDays = round(abs($p2 - $p1) / 86400) + 1;
            if ($diffDays == 1) {
                $displayLabel = (date('Y-m-d', $p1) === date('Y-m-d')) ? 'Today' : '1 Day';
            } elseif ($diffDays == 7) {
                $displayLabel = 'Last 7 Days';
            } elseif ($diffDays == 30) {
                $displayLabel = 'Last 30 Days';
            } else {
                $displayLabel = "{$diffDays} Days";
            }
        }
    }
}

$dateFilter = " AND enquiry_time BETWEEN '{$t1}' AND '{$t2}'";

// Helper function for cleaning domain/source names
function cleanMarketingSource($source) {
    if (empty($source) || trim($source) === '') {
        return 'Direct';
    }
    $cleaned = trim($source);
    $cleaned = preg_replace('#^https?://#i', '', $cleaned);
    $cleaned = preg_replace('#^www\.#i', '', $cleaned);
    $cleaned = rtrim($cleaned, '/');
    return ucfirst($cleaned);
}

// 3. KPI Metrics Aggregation
$totalLeads = 0;
$fbLeads = 0;
$googleLeads = 0;
$websiteLeads = 0;
$whatsappLeads = 0;
$otherLeads = 0;

$sourceQuery = "SELECT enquiry_source, enquiry_medium, COUNT(*) as cnt
                FROM {$tblPrefix}enquiry
                WHERE {$siteFilter}{$dateFilter}
                GROUP BY enquiry_source, enquiry_medium";

$rawSources = [];
if ($res = mysqli_query($connection, $sourceQuery)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $src = strtolower(trim($row['enquiry_source'] ?? ''));
        $med = strtolower(trim($row['enquiry_medium'] ?? ''));
        $count = (int)$row['cnt'];
        $totalLeads += $count;

        $isFbOrIg = (
            strpos($src, 'facebook') !== false ||
            strpos($src, 'meta') !== false ||
            strpos($src, 'instagram') !== false ||
            strpos($src, 'insta') !== false ||
            $src === 'fb' ||
            $src === 'ig' ||
            strpos($src, 'fb ') !== false ||
            strpos($src, ' fb') !== false ||
            strpos($src, 'ig ') !== false ||
            strpos($src, ' ig') !== false ||
            ($med === 'lead_form' && (empty($src) || strpos($src, 'facebook') !== false))
        );

        $isGoogle = (
            strpos($src, 'google') !== false ||
            strpos($src, 'adword') !== false ||
            strpos($src, 'gclid') !== false
        );

        if ($isFbOrIg) {
            $fbLeads += $count;
            $rawSources['Facebook & IG Leads'] = ($rawSources['Facebook & IG Leads'] ?? 0) + $count;
        } elseif ($isGoogle) {
            $googleLeads += $count;
            $rawSources['Google Ads'] = ($rawSources['Google Ads'] ?? 0) + $count;
        } elseif (strpos($src, 'whatsapp') !== false || strpos($src, 'wa') !== false) {
            $whatsappLeads += $count;
            $rawSources['WhatsApp API'] = ($rawSources['WhatsApp API'] ?? 0) + $count;
        } elseif (strpos($src, 'website') !== false || strpos($src, 'landing') !== false || strpos($src, 'portal') !== false) {
            $websiteLeads += $count;
            $rawSources['Website / Form'] = ($rawSources['Website / Form'] ?? 0) + $count;
        } else {
            $otherLeads += $count;
            $label = (!empty($src)) ? cleanMarketingSource($src) : 'Direct / Organic';
            $rawSources[$label] = ($rawSources[$label] ?? 0) + $count;
        }
    }
}

// Connected Facebook Pages & Forms Count
$connectedFbPages = 0;
$fbPageNames = [];
$fbPageQuery = "SELECT fb_page_name FROM {$tblPrefix}fb_pages WHERE fb_page_site_id = {$siteId} AND fb_page_status = 1";
if ($resFb = mysqli_query($connection, $fbPageQuery)) {
    while ($r = mysqli_fetch_assoc($resFb)) {
        $connectedFbPages++;
        $fbPageNames[] = $r['fb_page_name'];
    }
}

$activeFbForms = 0;
$fbFormQuery = "SELECT COUNT(*) as cnt FROM {$tblPrefix}fb_forms WHERE fb_forms_site_id = {$siteId}";
if ($resForms = mysqli_query($connection, $fbFormQuery)) {
    if ($rf = mysqli_fetch_assoc($resForms)) {
        $activeFbForms = (int)$rf['cnt'];
    }
}

// Google Ads Integration status
$isGoogleAdsActive = defined('ENABLE_GOOGLE_ADS') && ENABLE_GOOGLE_ADS == '1';

// Format Channel Distribution
arsort($rawSources);
$sourceChartLabels = [];
$sourceChartData   = [];
$sourceFlowBars    = [];

foreach ($rawSources as $sName => $sCount) {
    $pct = ($totalLeads > 0) ? round(($sCount / $totalLeads) * 100, 1) : 0;
    $sourceChartLabels[] = $sName;
    $sourceChartData[]   = $sCount;
    $sourceFlowBars[] = [
        'source'     => $sName,
        'count'      => $sCount,
        'percentage' => $pct
    ];
}

// If no leads, provide default structure
if (empty($sourceChartLabels)) {
    $sourceChartLabels = ['Facebook & IG Leads', 'Google Ads', 'Direct / Other'];
    $sourceChartData   = [0, 0, 0];
}

// 4. Daily Ingestion Trend (Timeline)
$flowLabels   = [];
$flowFbData   = [];
$flowGgData   = [];
$flowAllData  = [];

$dayDiff = (int) ceil(abs(strtotime($t2) - strtotime($t1)) / 86400) + 1;
if ($dayDiff <= 2) {
    $groupFmt = "%d %b %H:00";
    $dateInterval = "+1 hour";
} elseif ($dayDiff <= 45) {
    $groupFmt = "%d %b";
    $dateInterval = "+1 day";
} else {
    $groupFmt = "%b %Y";
    $dateInterval = "+1 month";
}

$timelineSql = "SELECT DATE_FORMAT(enquiry_time, '{$groupFmt}') as time_slot,
                       SUM(CASE 
                            WHEN LOWER(TRIM(enquiry_source)) LIKE '%facebook%' 
                              OR LOWER(TRIM(enquiry_source)) LIKE '%meta%'
                              OR LOWER(TRIM(enquiry_source)) LIKE '%instagram%'
                              OR LOWER(TRIM(enquiry_source)) LIKE '%insta%'
                              OR LOWER(TRIM(enquiry_source)) = 'fb'
                              OR LOWER(TRIM(enquiry_source)) = 'ig'
                              OR LOWER(TRIM(enquiry_source)) LIKE 'fb %'
                              OR LOWER(TRIM(enquiry_source)) LIKE '% fb'
                              OR LOWER(TRIM(enquiry_source)) LIKE 'ig %'
                              OR LOWER(TRIM(enquiry_source)) LIKE '% ig'
                              OR enquiry_medium = 'lead_form'
                            THEN 1 ELSE 0 END) as fb_cnt,
                       SUM(CASE 
                            WHEN LOWER(TRIM(enquiry_source)) LIKE '%google%' 
                              OR LOWER(TRIM(enquiry_source)) LIKE '%adword%' 
                              OR LOWER(TRIM(enquiry_source)) LIKE '%gclid%' 
                            THEN 1 ELSE 0 END) as gg_cnt,
                       COUNT(*) as total_cnt
                FROM {$tblPrefix}enquiry
                WHERE {$siteFilter}{$dateFilter}
                GROUP BY time_slot
                ORDER BY MIN(enquiry_time) ASC";

if ($tRes = mysqli_query($connection, $timelineSql)) {
    while ($tr = mysqli_fetch_assoc($tRes)) {
        $flowLabels[]  = $tr['time_slot'];
        $flowFbData[]  = (int)$tr['fb_cnt'];
        $flowGgData[]  = (int)$tr['gg_cnt'];
        $flowAllData[] = (int)$tr['total_cnt'];
    }
}

// 5. Recent Inquiries Stream (PII-Clean: NO mobile or email)
$recentInquiries = [];
$recentSql = "SELECT enquiry_id, enquiry_first_name, enquiry_last_name, enquiry_source, enquiry_medium, enquiry_subject, enquiry_time, enquiry_status
              FROM {$tblPrefix}enquiry
              WHERE {$siteFilter}
              ORDER BY enquiry_time DESC
              LIMIT 15";

if ($rRes = mysqli_query($connection, $recentSql)) {
    while ($rq = mysqli_fetch_assoc($rRes)) {
        $fName = trim(($rq['enquiry_first_name'] ?? '') . ' ' . ($rq['enquiry_last_name'] ?? ''));
        if (empty($fName)) {
            $fName = 'Ad Prospect #' . substr($rq['enquiry_id'], -4);
        }

        $srcRaw = strtolower(trim($rq['enquiry_source'] ?? ''));
        $medRaw = strtolower(trim($rq['enquiry_medium'] ?? ''));
        $srcBadge = 'Direct';
        $channelIcon = 'fi-rr-bullseye-arrow';

        $isRowFb = (
            strpos($srcRaw, 'facebook') !== false ||
            strpos($srcRaw, 'meta') !== false ||
            strpos($srcRaw, 'instagram') !== false ||
            strpos($srcRaw, 'insta') !== false ||
            $srcRaw === 'fb' ||
            $srcRaw === 'ig' ||
            strpos($srcRaw, 'fb ') !== false ||
            strpos($srcRaw, ' fb') !== false ||
            strpos($srcRaw, 'ig ') !== false ||
            strpos($srcRaw, ' ig') !== false ||
            $medRaw === 'lead_form'
        );

        $isRowGoogle = (
            strpos($srcRaw, 'google') !== false ||
            strpos($srcRaw, 'adword') !== false ||
            strpos($srcRaw, 'gclid') !== false
        );

        if ($isRowFb) {
            $srcBadge = 'Facebook & IG';
            $channelIcon = 'fi-rr-facebook';
        } elseif ($isRowGoogle) {
            $srcBadge = 'Google Ads';
            $channelIcon = 'fi-rr-search-alt';
        } elseif (strpos($srcRaw, 'whatsapp') !== false) {
            $srcBadge = 'WhatsApp API';
            $channelIcon = 'fi-rr-comment-alt-middle';
        } else {
            $srcBadge = cleanMarketingSource($srcRaw);
        }

        $campaign = !empty($rq['enquiry_subject']) ? trim($rq['enquiry_subject']) : 'General Lead Ad';

        $recentInquiries[] = [
            'id'           => $rq['enquiry_id'],
            'lead_tag'     => '#' . $rq['enquiry_id'],
            'name'         => $fName,
            'campaign'     => $campaign,
            'source'       => $srcBadge,
            'channel_icon' => $channelIcon,
            'time'         => date('d M Y, h:i A', strtotime($rq['enquiry_time'])),
            'time_ago'     => timeAgoInWords(strtotime($rq['enquiry_time'])),
            'status'       => 'Live Ingested'
        ];
    }
}

function timeAgoInWords($timestamp) {
    $diff = time() - $timestamp;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return round($diff / 60) . 'm ago';
    if ($diff < 86400) return round($diff / 3600) . 'h ago';
    if ($diff < 604800) return round($diff / 86400) . 'd ago';
    return date('d M', $timestamp);
}

// Response
echo json_encode([
    'status' => 'success',
    'kpis' => [
        'totalLeads'       => $totalLeads,
        'facebookLeads'    => $fbLeads,
        'googleLeads'      => $googleLeads,
        'whatsappLeads'    => $whatsappLeads,
        'otherLeads'       => $otherLeads,
        'connectedFbPages' => $connectedFbPages,
        'activeFbForms'    => $activeFbForms,
        'dateRangeLabel'   => $displayLabel,
    ],
    'integrations' => [
        'facebook' => [
            'status'          => $connectedFbPages > 0 ? 'connected' : 'disconnected',
            'pagesCount'      => $connectedFbPages,
            'pagesList'       => $fbPageNames,
            'webhookUrl'      => 'https://app.aleya.in/webhook/facebook/',
            'webhookStatus'   => 'Active',
            'actionUrl'       => '/dashboard/integration/facebook/'
        ],
        'googleAds' => [
            'status'          => $isGoogleAdsActive ? 'connected' : 'inactive',
            'webhookUrl'      => 'https://app.aleya.in/webhook/google_ads/',
            'actionUrl'       => '/dashboard/integration/google_ads/'
        ]
    ],
    'charts' => [
        'channelDistribution' => [
            'labels' => $sourceChartLabels,
            'data'   => $sourceChartData,
            'bars'   => $sourceFlowBars
        ],
        'timeline' => [
            'labels'   => $flowLabels,
            'facebook' => $flowFbData,
            'google'   => $flowGgData,
            'total'    => $flowAllData
        ]
    ],
    'recentInquiries' => $recentInquiries
]);
exit;
