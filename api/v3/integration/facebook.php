<?php

/**
 * V3 Facebook Lead Ads & Forms REST API Endpoint
 * Path: /api/v3/integration/facebook.php
 */

header('Content-Type: application/json; charset=utf-8');

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 3);
}

global $connection;
if (!isset($connection) || !$connection) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
    if (!isset($connection) || !$connection) {
        $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }
}

$siteId = defined('SITE_ID') ? (int)SITE_ID : 0;

if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    $escapedHost = mysqli_real_escape_string($connection, $host);
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM " . TABLE_PREFIX . "site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
    if ($siteQuery && mysqli_num_rows($siteQuery) > 0) {
        $row = mysqli_fetch_assoc($siteQuery);
        $siteId = (int)$row['site_id'];
    }
}

if ($siteId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Active site identifier could not be resolved.'
    ]);
    exit;
}

if (!defined('SITE_ID')) {
    define('SITE_ID', $siteId);
}

if (!defined('LOGIN_CHECK')) {
    $loginCheckPath = (defined('ROOT') ? ROOT : (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3))) . '/lib/modules/isLogin.php';
    if (file_exists($loginCheckPath)) {
        define('LOGIN_CHECK', $loginCheckPath);
    }
}

if (defined('LOGIN_CHECK') && file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId     = defined('USER_ID')      ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'customer';
$isDigitalMarketing = ($accessType === 'digital_marketing');

// Parse request input
$reqData = $_POST;
// Parse request input (support JSON body, POST fields, and GET query parameters)
$reqData = [];
if (!empty($_GET) && is_array($_GET)) {
    $reqData = array_merge($reqData, $_GET);
}
if (!empty($_POST) && is_array($_POST)) {
    $reqData = array_merge($reqData, $_POST);
}
$rawInput = file_get_contents('php://input');
if (!empty($rawInput)) {
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $reqData = array_merge($reqData, $decoded);
    }
}
$reqData = array_merge($_GET, $_REQUEST, $reqData);
$action = $reqData['action'] ?? 'get_leads';

// Explicitly retrieve action from any source (query param, JSON body, post field)
$action = trim((string)($reqData['action'] ?? ($_GET['action'] ?? ($_POST['action'] ?? ''))));

// Smart fallback: If action is missing, infer from parameters or return error
if (empty($action)) {
    if (!empty($reqData['form_id']) || !empty($_GET['form_id'])) {
        $action = 'get_leads';
    } elseif (!empty($reqData['page_id']) || !empty($_GET['page_id'])) {
        $action = 'sync_forms';
    } else {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'msg' => 'Action parameter is required.'
        ]);
        exit;
    }
}

/**
 * Format FB Question Label
 */
if (!function_exists('v3FormatFbQuestionLabel')) {
    function v3FormatFbQuestionLabel($label)
    {
        $label = trim($label, " ?:;,-_");
        $low = strtolower($label);
        if (strpos($low, 'how many') !== false || strpos($low, 'person') !== false || strpos($low, 'people') !== false || strpos($low, 'travelling') !== false || strpos($low, 'passengers') !== false || strpos($low, 'pax') !== false || strpos($low, 'adult') !== false) {
            return 'Travelers';
        }
        if (strpos($low, 'when') !== false || strpos($low, 'planning') !== false || strpos($low, 'travel date') !== false || strpos($low, 'timeline') !== false || strpos($low, 'month') !== false || strpos($low, 'date') !== false) {
            return 'Travel Date';
        }
        if (strpos($low, 'destination') !== false || strpos($low, 'where') !== false || strpos($low, 'package') !== false || strpos($low, 'place') !== false) {
            return 'Destination';
        }
        if (strpos($low, 'budget') !== false || strpos($low, 'price') !== false || strpos($low, 'cost') !== false) {
            return 'Budget';
        }
        if (strpos($low, 'duration') !== false || strpos($low, 'days') !== false || strpos($low, 'nights') !== false) {
            return 'Duration';
        }
        if (strpos($low, 'city') !== false || strpos($low, 'location') !== false || strpos($low, 'departure') !== false || strpos($low, 'from') !== false) {
            return 'City';
        }
        return ucwords(str_replace(['_', '-'], ' ', $label));
    }
}

if (!function_exists('v3FormatFbResponseVal')) {
    function v3FormatFbResponseVal($val)
    {
        $clean = str_replace('_', ' ', (string)$val);
        $clean = preg_replace('/\s+/', ' ', $clean);
        return ucwords(trim($clean));
    }
}

if (!function_exists('v3CheckLeadsCrmSyncMap')) {
    function v3CheckLeadsCrmSyncMap($connection, $siteId, $leads, $formName)
    {
        $syncMap = [];
        if (empty($leads)) {
            return $syncMap;
        }

        $leadIds = [];
        $mobiles = [];
        $emails = [];

        foreach ($leads as $lead) {
            if (!empty($lead['id'])) {
                $leadIds[] = mysqli_real_escape_string($connection, $lead['id']);
            }
            if (!empty($lead['field_data']) && is_array($lead['field_data'])) {
                foreach ($lead['field_data'] as $f) {
                    $fName = strtolower(trim($f['name'] ?? ''));
                    $fVal  = is_array($f['values'] ?? '') ? implode('', $f['values']) : ($f['values'] ?? '');
                    $fVal  = trim((string)$fVal);
                    if (empty($fVal)) continue;

                    if (in_array($fName, ['phone_number', 'phone', 'mobile_number', 'mobile', 'whatsapp_number'])) {
                        $cleanMob = preg_replace('/[^0-9]/', '', $fVal);
                        if (strlen($cleanMob) >= 10) {
                            $mobiles[] = mysqli_real_escape_string($connection, substr($cleanMob, -10));
                        }
                    }
                    if (in_array($fName, ['email', 'email_address', 'your_email'])) {
                        $emails[] = mysqli_real_escape_string($connection, strtolower($fVal));
                    }
                }
            }
        }

        $conditions = [];
        foreach ($leadIds as $lid) {
            $conditions[] = "enquiry_message LIKE '%" . $lid . "%'";
        }
        if (!empty($mobiles)) {
            $mobilesUnique = array_unique($mobiles);
            $conditions[] = "RIGHT(REPLACE(REPLACE(enquiry_mobile, ' ', ''), '+91', ''), 10) IN ('" . implode("','", $mobilesUnique) . "')";
        }
        if (!empty($emails)) {
            $emailsUnique = array_unique($emails);
            $conditions[] = "LOWER(enquiry_email) IN ('" . implode("','", $emailsUnique) . "')";
        }

        if (!empty($conditions)) {
            $escFormName = mysqli_real_escape_string($connection, $formName);
            $sql = "SELECT enquiry_id, enquiry_mobile, enquiry_email, enquiry_message, enquiry_time 
                    FROM " . TABLE_PREFIX . "enquiry 
                    WHERE enquiry_site_id = {$siteId} 
                      AND (" . implode(" OR ", $conditions) . ")
                    ORDER BY enquiry_id DESC";
            $res = mysqli_query($connection, $sql);
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $enqId   = (int)$row['enquiry_id'];
                    $enqTime = $row['enquiry_time'];

                    foreach ($leadIds as $lid) {
                        if (strpos($row['enquiry_message'], $lid) !== false) {
                            if (!isset($syncMap[$lid])) {
                                $syncMap[$lid] = [
                                    'is_synced'  => true,
                                    'enquiry_id' => $enqId,
                                    'sync_time'  => $enqTime
                                ];
                            }
                        }
                    }

                    $cleanRowMob = substr(preg_replace('/[^0-9]/', '', $row['enquiry_mobile']), -10);
                    if (!empty($cleanRowMob) && !isset($syncMap['mob_' . $cleanRowMob])) {
                        $syncMap['mob_' . $cleanRowMob] = [
                            'is_synced'  => true,
                            'enquiry_id' => $enqId,
                            'sync_time'  => $enqTime
                        ];
                    }

                    $cleanRowEmail = strtolower(trim($row['enquiry_email']));
                    if (!empty($cleanRowEmail) && !isset($syncMap['email_' . $cleanRowEmail])) {
                        $syncMap['email_' . $cleanRowEmail] = [
                            'is_synced'  => true,
                            'enquiry_id' => $enqId,
                            'sync_time'  => $enqTime
                        ];
                    }
                }
            }
        }

        return $syncMap;
    }
}

// =========================================================================
// ACTION: get_leads (Max 20 leads per request, with campaign total & CRM sync)
// =========================================================================
if ($action === 'get_leads') {
    $formId = trim((string)($reqData['form_id'] ?? ''));
    $formId = trim((string)($reqData['form_id'] ?? ($_GET['form_id'] ?? ($_POST['form_id'] ?? ''))));
    $page   = max(1, (int)($reqData['page'] ?? 1));
    $limit  = min(20, max(1, (int)($reqData['limit'] ?? 20))); // Max 20 leads per fetch
    $after  = !empty($reqData['after']) ? trim($reqData['after']) : null;
    $before = !empty($reqData['before']) ? trim($reqData['before']) : null;
    $search = !empty($reqData['search']) ? strtolower(trim($reqData['search'])) : '';

    if (empty($formId)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Form ID is required.']);
        exit;
    }

    // Verify form and get access token
    $checkForm = mysqli_query(
        $connection,
        "SELECT f.*, p.fb_page_name, p.fb_page_id, p.fb_page_access_token 
         FROM " . TABLE_PREFIX . "fb_forms f 
         INNER JOIN " . TABLE_PREFIX . "fb_pages p 
            ON f.fb_forms_page_id = p.fb_page_id AND p.fb_page_site_id = {$siteId} 
         WHERE f.fb_forms_id = '" . mysqli_real_escape_string($connection, $formId) . "' 
           AND f.fb_forms_site_id = {$siteId}"
    );

    if (!$checkForm || mysqli_num_rows($checkForm) === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'msg' => 'Facebook Lead Form not found or access restricted.']);
        exit;
    }

    $formRow = mysqli_fetch_assoc($checkForm);
    $formName = $formRow['fb_forms_name'];
    $pageAccessToken = $formRow['fb_page_access_token'];

    // 1. Fetch total leads in this campaign (from Meta Graph API and CRM)
    $campaignTotalLeads = (int)($formRow['fb_forms_leads_count'] ?? 0);
    $metaCreatedTime = $formRow['fb_forms_created_at'] ?? null;

    if (!empty($pageAccessToken)) {
        $metaUrl = "https://graph.facebook.com/v19.0/{$formId}?fields=id,name,status,leads_count,created_time&access_token=" . urlencode($pageAccessToken);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $metaUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        $metaResp = curl_exec($ch);
        curl_close($ch);
        $metaData = json_decode($metaResp, true);
        if (!empty($metaData['leads_count'])) {
            $campaignTotalLeads = max($campaignTotalLeads, (int)$metaData['leads_count']);
            // update in db cache if column exists
            $chkCol = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_forms LIKE 'fb_forms_leads_count'");
            if ($chkCol && mysqli_num_rows($chkCol) > 0) {
                mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "fb_forms SET fb_forms_leads_count = {$campaignTotalLeads} WHERE fb_forms_id = '" . mysqli_real_escape_string($connection, $formId) . "' AND fb_forms_site_id = {$siteId}");
            }
        }
        if (!empty($metaData['created_time'])) {
            $metaCreatedTime = $metaData['created_time'];
        }
    }

    // Also get CRM leads count for this form
    $escFormName = mysqli_real_escape_string($connection, $formName);
    $crmCountRes = mysqli_query($connection, "SELECT COUNT(*) as c FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_site_id = {$siteId} AND (enquiry_subject = '{$escFormName}' OR enquiry_message LIKE '%{$formId}%')");
    $crmCount = ($crmCountRes) ? (int)(mysqli_fetch_assoc($crmCountRes)['c'] ?? 0) : 0;
    $campaignTotalLeads = max($campaignTotalLeads, $crmCount);

    // 2. Fetch leads (max 20)
    $leads = [];
    $nextCursor = null;
    $prevCursor = null;
    $hasNext = false;
    $hasPrev = ($page > 1);
    $graphError = null;

    if (!empty($pageAccessToken)) {
        $params = [
            'access_token' => $pageAccessToken,
            'limit'        => $limit,
            'fields'       => 'id,created_time,field_data'
        ];
        if ($after) {
            $params['after'] = $after;
        } elseif ($before) {
            $params['before'] = $before;
        }

        $url = "https://graph.facebook.com/v19.0/{$formId}/leads?" . http_build_query($params);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 25,
        ]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if (!$err && !empty($response)) {
            $data = json_decode($response, true);
            if (isset($data['error'])) {
                $graphError = $data['error']['message'] ?? 'Meta API error';
            } elseif (!empty($data['data']) && is_array($data['data'])) {
                $leads = $data['data'];
                $nextCursor = $data['paging']['cursors']['after'] ?? null;
                $prevCursor = $data['paging']['cursors']['before'] ?? null;
                $hasNext = !empty($data['paging']['next']);
                if (!empty($data['paging']['previous'])) {
                    $hasPrev = true;
                }
            }
        }
    }

    // Fallback: If Meta Graph API returned error or empty, but we have leads stored in CRM enquiry table
    if (empty($leads) && $crmCount > 0) {
        $offset = ($page - 1) * $limit;
        $crmLeadsRes = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_site_id = {$siteId} AND (enquiry_subject = '{$escFormName}' OR enquiry_message LIKE '%{$formId}%') ORDER BY enquiry_id DESC LIMIT {$limit} OFFSET {$offset}");
        if ($crmLeadsRes) {
            while ($cRow = mysqli_fetch_assoc($crmLeadsRes)) {
                $parsedMsg = json_decode($cRow['enquiry_message'], true);
                $lId = (!empty($parsedMsg['id'])) ? $parsedMsg['id'] : ('crm_' . $cRow['enquiry_id']);
                $cTime = (!empty($parsedMsg['created_time'])) ? $parsedMsg['created_time'] : $cRow['enquiry_time'];
                $fData = (!empty($parsedMsg['field_data'])) ? $parsedMsg['field_data'] : [];

                if (empty($fData)) {
                    $fData = [
                        ['name' => 'full_name', 'values' => [$cRow['enquiry_first_name']]],
                        ['name' => 'phone_number', 'values' => [$cRow['enquiry_mobile']]],
                        ['name' => 'email', 'values' => [$cRow['enquiry_email']]],
                    ];
                }

                $leads[] = [
                    'id'           => $lId,
                    'created_time' => $cTime,
                    'field_data'   => $fData,
                    'crm_fallback' => true,
                    'crm_enquiry_id' => (int)$cRow['enquiry_id']
                ];
            }
            $hasNext = ($offset + $limit) < $crmCount;
        }
    }

    // 3. Match CRM Sync Status for each lead
    $syncMap = v3CheckLeadsCrmSyncMap($connection, $siteId, $leads, $formName);

    // 4. Format leads for response
    $formattedLeads = [];
    $hasPhoneCount = 0;
    $timestamps = [];

    foreach ($leads as $lead) {
        $leadIdVal   = $lead['id'] ?? '';
        $createdTime = !empty($lead['created_time']) ? date('d M Y, h:i A', strtotime($lead['created_time'])) : 'N/A';
        if (!empty($lead['created_time'])) {
            $timestamps[] = strtotime($lead['created_time']);
        }

        $fullName = '';
        $emailVal = '';
        $phoneVal = '';
        $customResponses = [];

        if (!empty($lead['field_data']) && is_array($lead['field_data'])) {
            foreach ($lead['field_data'] as $field) {
                $fieldName = strtolower(trim($field['name'] ?? ''));
                $fieldValues = $field['values'] ?? [];
                $fieldValStr = is_array($fieldValues) ? implode(', ', $fieldValues) : (string)$fieldValues;

                if (in_array($fieldName, ['full_name', 'name', 'first_name', 'your_name'])) {
                    $fullName = $fieldValStr;
                } elseif (in_array($fieldName, ['email', 'email_address', 'your_email'])) {
                    $emailVal = $fieldValStr;
                } elseif (in_array($fieldName, ['phone_number', 'phone', 'mobile_number', 'mobile', 'whatsapp_number'])) {
                    $phoneVal = $fieldValStr;
                    $hasPhoneCount++;
                } else {
                    $cleanKey = v3FormatFbQuestionLabel($fieldName);
                    $isContactField = (bool)preg_match('/(phone|mobile|email|contact|whatsapp)/i', $fieldName);
                    $customResponses[] = [
                        'label' => $cleanKey,
                        'val'   => ($isDigitalMarketing && $isContactField) ? '[Protected]' : v3FormatFbResponseVal($fieldValStr)
                    ];
                }
            }
        }

        if (empty($fullName)) {
            $fullName = 'Facebook Lead #' . substr($leadIdVal, -4);
        }

        // Determine CRM Sync Status
        $isSynced = false;
        $crmEnquiryId = null;
        $crmSyncTime = null;

        if (!empty($lead['crm_enquiry_id'])) {
            $isSynced = true;
            $crmEnquiryId = (int)$lead['crm_enquiry_id'];
        } elseif (!empty($syncMap[$leadIdVal])) {
            $isSynced = true;
            $crmEnquiryId = $syncMap[$leadIdVal]['enquiry_id'];
            $crmSyncTime = $syncMap[$leadIdVal]['sync_time'];
        } else {
            $cleanMob = substr(preg_replace('/[^0-9]/', '', $phoneVal), -10);
            if (!empty($cleanMob) && !empty($syncMap['mob_' . $cleanMob])) {
                $isSynced = true;
                $crmEnquiryId = $syncMap['mob_' . $cleanMob]['enquiry_id'];
                $crmSyncTime = $syncMap['mob_' . $cleanMob]['sync_time'];
            } elseif (!empty($emailVal) && !empty($syncMap['email_' . strtolower(trim($emailVal))])) {
                $isSynced = true;
                $crmEnquiryId = $syncMap['email_' . strtolower(trim($emailVal))]['enquiry_id'];
                $crmSyncTime = $syncMap['email_' . strtolower(trim($emailVal))]['sync_time'];
            }
        }

        // Apply search filter if passed
        if (!empty($search)) {
            $haystack = strtolower($fullName . ' ' . $phoneVal . ' ' . $emailVal . ' ' . $leadIdVal);
            if (strpos($haystack, $search) === false) {
                continue;
            }
        }

        $nameWords = preg_split('/\s+/', trim($fullName));
        if (count($nameWords) >= 2 && !empty($nameWords[0]) && !empty($nameWords[1])) {
            $initials = strtoupper(substr($nameWords[0], 0, 1) . substr($nameWords[1], 0, 1));
        } else {
            $initials = strtoupper(substr($fullName, 0, 1));
        }

        $leadDest = '';
        if (!empty($customResponses)) {
            foreach ($customResponses as $cr) {
                if ($cr['label'] === 'Destination' && !empty($cr['val']) && strlen($cr['val']) <= 25) {
                    $leadDest = $cr['val'];
                    break;
                }
            }
        }

        $formattedLeads[] = [
            'id'             => $leadIdVal,
            'created_time'   => $createdTime,
            'full_name'      => $fullName,
            'initial'        => $initials,
            'destination'    => $leadDest,
            'phone'          => $isDigitalMarketing ? '' : $phoneVal,
            'email'          => $isDigitalMarketing ? '' : $emailVal,
            'questions'      => $customResponses,
            'is_synced'      => $isSynced,
            'crm_enquiry_id' => $crmEnquiryId,
            'crm_sync_time'  => $crmSyncTime ? date('d M Y, h:i A', strtotime($crmSyncTime)) : null,
        ];
    }

    // Calculate timestamps for KPIs
    $latestLeadTime = 'N/A';
    $firstLeadTime  = 'N/A';
    if (!empty($timestamps)) {
        rsort($timestamps);
        $latestLeadTime = date('d M Y, h:i A', $timestamps[0]);
        $firstLeadTime  = date('d M Y, h:i A', end($timestamps));
    }

    $fetchedCount = count($formattedLeads);
    $startIdx = $campaignTotalLeads > 0 ? (($page - 1) * $limit) + 1 : 0;
    $endIdx   = min($startIdx + $fetchedCount - 1, $campaignTotalLeads);
    if ($startIdx > $endIdx) {
        $startIdx = $endIdx > 0 ? 1 : 0;
    }

    echo json_encode([
        'status'  => 'success',
        'message' => 'Facebook leads retrieved successfully.',
        'data'    => $formattedLeads,
        'pagination' => [
            'total'       => $campaignTotalLeads,
            'page'        => $page,
            'limit'       => $limit,
            'count'       => $fetchedCount,
            'start'       => $startIdx,
            'end'         => $endIdx,
            'after'       => $nextCursor,
            'before'      => $prevCursor,
            'has_next'    => $hasNext,
            'has_prev'    => $hasPrev,
            'total_pages' => max(1, (int)ceil($campaignTotalLeads / $limit))
        ],
        'kpi' => [
            'total_campaign_leads' => $campaignTotalLeads,
            'latest_lead_time'     => $latestLeadTime,
            'first_lead_time'      => $firstLeadTime,
            'contact_validity'     => ($fetchedCount > 0) ? round(($hasPhoneCount / $fetchedCount) * 100) : 0,
            'verified_phone_count' => $hasPhoneCount
        ],
        'graph_error' => $graphError
    ]);
    exit;
}

// =========================================================================
// ACTION: sync_forms (Sync all Lead Forms for a Page from Facebook)
// =========================================================================
if ($action === 'sync_forms') {
    $pageId = trim((string)($reqData['page_id'] ?? ''));
    $pageId = trim((string)($reqData['page_id'] ?? ($_GET['page_id'] ?? ($_POST['page_id'] ?? ''))));

    if (empty($pageId)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Page ID is required.']);
        exit;
    }

    $checkPage = mysqli_query(
        $connection,
        "SELECT * FROM " . TABLE_PREFIX . "fb_pages 
         WHERE fb_page_id = '" . mysqli_real_escape_string($connection, $pageId) . "' 
           AND fb_page_site_id = {$siteId}"
    );

    if (!$checkPage || mysqli_num_rows($checkPage) === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'msg' => 'Facebook Page not found or access restricted.']);
        exit;
    }

    $pageRow = mysqli_fetch_assoc($checkPage);
    $accessToken = $pageRow['fb_page_access_token'];

    // Fetch forms from Meta Graph API
    $allForms = [];
    $url = "https://graph.facebook.com/v19.0/{$pageId}/leadgen_forms?access_token={$accessToken}&fields=id,name,status,created_time,leads_count&limit=50";

    do {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if (!empty($data['data'])) {
            $allForms = array_merge($allForms, $data['data']);
        }
        $url = $data['paging']['next'] ?? null;
    } while ($url);

    $syncedCount = 0;
    if (!empty($allForms)) {
        $formsCols = [];
        $chkCol = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_forms");
        if ($chkCol) {
            while ($c = mysqli_fetch_assoc($chkCol)) {
                $formsCols[$c['Field']] = true;
            }
        }
        if (empty($formsCols['fb_forms_leads_count'])) {
            @mysqli_query($connection, "ALTER TABLE " . TABLE_PREFIX . "fb_forms ADD COLUMN fb_forms_leads_count INT DEFAULT 0");
        }
        if (empty($formsCols['fb_forms_last_sync'])) {
            @mysqli_query($connection, "ALTER TABLE " . TABLE_PREFIX . "fb_forms ADD COLUMN fb_forms_last_sync DATETIME DEFAULT NULL");
        }
        if (empty($formsCols['fb_forms_created_at'])) {
            @mysqli_query($connection, "ALTER TABLE " . TABLE_PREFIX . "fb_forms ADD COLUMN fb_forms_created_at DATETIME DEFAULT NULL");
        }
        $chkCol2 = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_forms");
        $formsCols = [];
        if ($chkCol2) {
            while ($c = mysqli_fetch_assoc($chkCol2)) {
                $formsCols[$c['Field']] = true;
            }
        }

        foreach ($allForms as $form) {
            $formId     = mysqli_real_escape_string($connection, $form['id']);
            $formName   = mysqli_real_escape_string($connection, $form['name']);
            $status     = (strtolower($form['status'] ?? '') === 'active') ? 1 : 0;
            $leadsCount = (int)($form['leads_count'] ?? 0);
            $createdAt  = !empty($form['created_time']) ? date('Y-m-d H:i:s', strtotime($form['created_time'])) : date('Y-m-d H:i:s');

            $insertFields = ['fb_forms_id', 'fb_forms_site_id', 'fb_forms_page_id', 'fb_forms_name', 'fb_forms_status'];
            $insertValues = ["'{$formId}'", "'{$siteId}'", "'{$pageId}'", "'{$formName}'", "'{$status}'"];
            $updateFields = ["fb_forms_name='{$formName}'", "fb_forms_status='{$status}'"];

            if (!empty($formsCols['fb_forms_leads_count'])) {
                $insertFields[] = 'fb_forms_leads_count';
                $insertValues[] = "{$leadsCount}";
                $updateFields[] = "fb_forms_leads_count=IF({$leadsCount} > 0, {$leadsCount}, fb_forms_leads_count)";
            }
            if (!empty($formsCols['fb_forms_created_at'])) {
                $insertFields[] = 'fb_forms_created_at';
                $insertValues[] = "'{$createdAt}'";
            }
            if (!empty($formsCols['fb_forms_last_sync'])) {
                $insertFields[] = 'fb_forms_last_sync';
                $insertValues[] = "NOW()";
                $updateFields[] = "fb_forms_last_sync=NOW()";
            }

            $fieldsStr = implode(', ', $insertFields);
            $valsStr = implode(', ', $insertValues);
            $updatesStr = implode(', ', $updateFields);

            mysqli_query(
                $connection,
                "INSERT INTO " . TABLE_PREFIX . "fb_forms ({$fieldsStr}) 
                VALUES({$valsStr})
                ON DUPLICATE KEY UPDATE {$updatesStr}"
            );
            $syncedCount++;
        }

        // Subscribe to webhook
        $webhookURL = "https://app.aleya.in/webhook/facebook/";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://graph.facebook.com/{$pageId}/subscribed_apps?subscribed_fields=leadgen&access_token={$accessToken}&link={$webhookURL}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    // Update page last sync timestamp safely
    $pageCols = [];
    $chkPg = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_pages");
    if ($chkPg) {
        while ($c = mysqli_fetch_assoc($chkPg)) {
            $pageCols[$c['Field']] = true;
        }
    }
    if (!empty($pageCols['fb_page_last_sync'])) {
        mysqli_query(
            $connection,
            "UPDATE " . TABLE_PREFIX . "fb_pages 
             SET fb_page_last_sync = NOW() 
             WHERE fb_page_id = '" . mysqli_real_escape_string($connection, $pageId) . "' 
               AND fb_page_site_id = {$siteId}"
        );
    }

    echo json_encode([
        'status'           => 'success',
        'message'          => "Synchronized {$syncedCount} Lead Forms from Facebook.",
        'synced_count'     => $syncedCount,
        'last_sync_time'   => date('d M Y, h:i A')
    ]);
    exit;
}

// =========================================================================
// ACTION: sync_single_lead (Push a Facebook lead directly into CRM enquiry)
// =========================================================================
if ($action === 'sync_single_lead') {
    $formId = trim((string)($reqData['form_id'] ?? ''));
    $leadId = trim((string)($reqData['lead_id'] ?? ''));
    $formId = trim((string)($reqData['form_id'] ?? ($_GET['form_id'] ?? ($_POST['form_id'] ?? ''))));
    $leadId = trim((string)($reqData['lead_id'] ?? ($_GET['lead_id'] ?? ($_POST['lead_id'] ?? ''))));
    $leadPayloadRaw = $reqData['lead_data'] ?? null;

    if (empty($formId) || empty($leadId)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Form ID and Lead ID are required.']);
        exit;
    }

    // Get Form & Page details
    $checkForm = mysqli_query(
        $connection,
        "SELECT f.*, p.fb_page_name, p.fb_page_id, p.fb_page_access_token 
         FROM " . TABLE_PREFIX . "fb_forms f 
         INNER JOIN " . TABLE_PREFIX . "fb_pages p 
            ON f.fb_forms_page_id = p.fb_page_id AND p.fb_page_site_id = {$siteId} 
         WHERE f.fb_forms_id = '" . mysqli_real_escape_string($connection, $formId) . "' 
           AND f.fb_forms_site_id = {$siteId}"
    );

    if (!$checkForm || mysqli_num_rows($checkForm) === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'msg' => 'Form not found.']);
        exit;
    }

    $formRow = mysqli_fetch_assoc($checkForm);
    $subject = $formRow['fb_forms_name'];
    $pageAccessToken = $formRow['fb_page_access_token'];

    // Check if already synced
    $checkExisting = mysqli_query(
        $connection,
        "SELECT enquiry_id FROM " . TABLE_PREFIX . "enquiry 
         WHERE enquiry_site_id = {$siteId} 
           AND enquiry_message LIKE '%" . mysqli_real_escape_string($connection, $leadId) . "%' 
         LIMIT 1"
    );

    if ($checkExisting && mysqli_num_rows($checkExisting) > 0) {
        $existingRow = mysqli_fetch_assoc($checkExisting);
        echo json_encode([
            'status'     => 'success',
            'message'    => 'Lead is already synced to CRM.',
            'enquiry_id' => (int)$existingRow['enquiry_id'],
            'already'    => true
        ]);
        exit;
    }

    // Fetch lead details from Facebook if not fully provided
    $leadData = is_array($leadPayloadRaw) ? $leadPayloadRaw : json_decode((string)$leadPayloadRaw, true);
    if (empty($leadData) || empty($leadData['field_data'])) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://graph.facebook.com/v19.0/{$leadId}?access_token={$pageAccessToken}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);
        $fbLeadResp = curl_exec($ch);
        curl_close($ch);
        $leadData = json_decode($fbLeadResp, true);
    }

    $name = '';
    $email = '';
    $mobile = '';
    $formData = $leadData['field_data'] ?? [];

    if (!empty($formData) && is_array($formData)) {
        foreach ($formData as $field) {
            $fName = strtolower(trim($field['name'] ?? ''));
            $fVal  = is_array($field['values'] ?? '') ? implode('', $field['values']) : ($field['values'] ?? '');
            $fVal  = trim((string)$fVal);

            if (strpos($fName, 'name') !== false && strpos($fName, 'email') === false && empty($name)) {
                $name = $fVal;
            } elseif (strpos($fName, 'email') !== false && empty($email)) {
                $email = $fVal;
            } elseif (preg_match('/phone|phonenumber|phone_number|mobile|whatsapp/i', $fName) && empty($mobile)) {
                $mobile = $fVal;
            }
        }
    }

    if (empty($name)) {
        $name = 'Facebook Lead #' . substr($leadId, -4);
    }
    $cleanMobile = str_replace('+91', '', str_replace(' ', '', $mobile));

    // Find existing user if available
    $userId = 0;
    if (!empty($email) || !empty($cleanMobile)) {
        $findUser = mysqli_query(
            $connection,
            "SELECT user_id FROM " . TABLE_PREFIX . "user 
             WHERE (user_email = '" . mysqli_real_escape_string($connection, $email) . "' AND user_email != '') 
                OR (user_mobile = '" . mysqli_real_escape_string($connection, $cleanMobile) . "' AND user_mobile != '') 
             LIMIT 1"
        );
        if ($findUser && mysqli_num_rows($findUser) > 0) {
            $userId = (int)(mysqli_fetch_assoc($findUser)['user_id'] ?? 0);
        }
    }

    $trafficSource   = 'facebook';
    $trafficMedium   = 'lead_form';
    $trafficCampaign = '';
    $leadJsonString  = mysqli_real_escape_string($connection, json_encode($leadData));
    $escName         = mysqli_real_escape_string($connection, $name);
    $escEmail        = mysqli_real_escape_string($connection, $email);
    $escMobile       = mysqli_real_escape_string($connection, $cleanMobile);
    $escSubject      = mysqli_real_escape_string($connection, $subject);

    $insertSql = "INSERT INTO " . TABLE_PREFIX . "enquiry 
        (enquiry_site_id, enquiry_user_id, enquiry_first_name, enquiry_email, enquiry_mobile, enquiry_message, enquiry_subject, enquiry_type, enquiry_source, enquiry_medium, enquiry_campaign, enquiry_status, enquiry_time) 
        VALUES ('{$siteId}', '{$userId}', '{$escName}', '{$escEmail}', '{$escMobile}', '{$leadJsonString}', '{$escSubject}', 'enquiry', '{$trafficSource}', '{$trafficMedium}', '{$trafficCampaign}', 0, NOW())";

    if (mysqli_query($connection, $insertSql)) {
        $newEnquiryId = mysqli_insert_id($connection);
        echo json_encode([
            'status'     => 'success',
            'message'    => 'Lead synced to CRM successfully.',
            'enquiry_id' => (int)$newEnquiryId,
            'sync_time'  => date('d M Y, h:i A')
        ]);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => 'Failed to insert lead into CRM enquiry table.']);
        exit;
    }
}

http_response_code(400);
echo json_encode(['status' => 'error', 'msg' => 'Invalid action specified.']);
exit;
