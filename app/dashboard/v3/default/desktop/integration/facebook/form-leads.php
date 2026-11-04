<?php
// Workspace Rule Compliant: V3 Facebook Form Leads Viewer & KPI Analytics

$formId = isset($id) ? $id : (isset($_GET['id']) ? $_GET['id'] : '');
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 20; // Maximum 20 leads per fetch
$afterCursor = isset($_GET['after']) ? trim($_GET['after']) : null;
$beforeCursor = isset($_GET['before']) ? trim($_GET['before']) : null;

// Check valid form and join parent page
$checkFormQuery = mysqli_query(
    $connection,
    "SELECT f.*, p.fb_page_name, p.fb_page_id, p.fb_page_access_token 
     FROM " . TABLE_PREFIX . "fb_forms f 
     INNER JOIN " . TABLE_PREFIX . "fb_pages p 
        ON f.fb_forms_page_id = p.fb_page_id AND p.fb_page_site_id = " . SITE_ID . " 
     WHERE f.fb_forms_id = '" . mysqli_real_escape_string($connection, $formId) . "' 
     AND f.fb_forms_site_id = " . SITE_ID
);

$formRow = ($checkFormQuery && mysqli_num_rows($checkFormQuery) > 0) ? mysqli_fetch_assoc($checkFormQuery) : null;
$formDisplayName = $formRow ? $formRow['fb_forms_name'] : 'Lead Form';
$pageDisplayName = $formRow ? $formRow['fb_page_name'] : 'Facebook Page';
$pageId = $formRow ? $formRow['fb_page_id'] : '';
$pageAccessToken = $formRow ? $formRow['fb_page_access_token'] : '';

$leads = [];
$fetchError = '';
$nextCursor = null;
$prevCursor = null;
$hasNext = false;
$hasPrev = ($currentPage > 1);

function formatFbQuestionLabel($label)
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

function formatFbResponseVal($val)
{
    $clean = str_replace('_', ' ', (string)$val);
    $clean = preg_replace('/\s+/', ' ', $clean);
    return ucwords(trim($clean));
}

// 1. Get Campaign Total Leads safely
$totalCampaignLeads = (int)($formRow['fb_forms_leads_count'] ?? 0);
if ($formRow && !empty($pageAccessToken)) {
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
        $totalCampaignLeads = max($totalCampaignLeads, (int)$metaData['leads_count']);
        $chkCol = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_forms LIKE 'fb_forms_leads_count'");
        if ($chkCol && mysqli_num_rows($chkCol) > 0) {
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "fb_forms SET fb_forms_leads_count = {$totalCampaignLeads} WHERE fb_forms_id = '" . mysqli_real_escape_string($connection, $formId) . "' AND fb_forms_site_id = " . SITE_ID);
        }
    }
}

// Also get CRM leads count
$escFormDisplayName = mysqli_real_escape_string($connection, $formDisplayName);
$crmCountRes = mysqli_query($connection, "SELECT COUNT(*) as c FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_site_id = " . SITE_ID . " AND (enquiry_subject = '{$escFormDisplayName}' OR enquiry_message LIKE '%{$formId}%')");
$crmCount = ($crmCountRes) ? (int)(mysqli_fetch_assoc($crmCountRes)['c'] ?? 0) : 0;
$totalCampaignLeads = max($totalCampaignLeads, $crmCount);
$totalPages = max(1, (int)ceil($totalCampaignLeads / $limit));

// 2. Fetch maximum 20 leads
if ($formRow && !empty($pageAccessToken)) {
    $params = [
        'access_token' => $pageAccessToken,
        'limit'        => $limit,
        'fields'       => 'id,created_time,field_data'
    ];
    if ($afterCursor) {
        $params['after'] = $afterCursor;
    } elseif ($beforeCursor) {
        $params['before'] = $beforeCursor;
    }

    $url = "https://graph.facebook.com/v19.0/{$formId}/leads?" . http_build_query($params);
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
    ]);
    $response = curl_exec($ch);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if (!$curlErr && !empty($response)) {
        $data = json_decode($response, true);
        if (isset($data['error'])) {
            $fetchError = $data['error']['message'] ?? 'Meta API error';
        } elseif (!empty($data['data']) && is_array($data['data'])) {
            $leads = $data['data'];
            $nextCursor = $data['paging']['cursors']['after'] ?? null;
            $prevCursor = $data['paging']['cursors']['before'] ?? null;
            $hasNext = !empty($data['paging']['next']);
            if (!empty($data['paging']['previous'])) {
                $hasPrev = true;
            }
        }
    } else {
        $fetchError = $curlErr ?: 'Unable to connect to Facebook Graph API.';
    }
}

// Fallback: If FB returned error or empty, but CRM has captured leads for this form
if (empty($leads) && $crmCount > 0) {
    $offset = ($currentPage - 1) * $limit;
    $crmRes = mysqli_query(
        $connection,
        "SELECT * FROM " . TABLE_PREFIX . "enquiry 
         WHERE enquiry_site_id = " . SITE_ID . " 
           AND (enquiry_subject = '{$escFormDisplayName}' OR enquiry_message LIKE '%{$formId}%') 
         ORDER BY enquiry_id DESC 
         LIMIT {$limit} OFFSET {$offset}"
    );
    if ($crmRes) {
        while ($cRow = mysqli_fetch_assoc($crmRes)) {
            $parsedMsg = json_decode($cRow['enquiry_message'], true);
            $lId   = (!empty($parsedMsg['id'])) ? $parsedMsg['id'] : ('crm_' . $cRow['enquiry_id']);
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
                'id'             => $lId,
                'created_time'   => $cTime,
                'field_data'     => $fData,
                'crm_enquiry_id' => (int)$cRow['enquiry_id'],
                'crm_fallback'   => true
            ];
        }
        $hasNext = ($offset + $limit) < $crmCount;
        $fetchError = ''; // Clear error so table displays cached CRM leads cleanly
    }
}

// 3. Match CRM Sync Status
$syncMap = [];
if (!empty($leads)) {
    $leadIds = [];
    $mobiles = [];
    $emails  = [];

    foreach ($leads as $l) {
        if (!empty($l['id'])) {
            $leadIds[] = mysqli_real_escape_string($connection, $l['id']);
        }
        if (!empty($l['field_data']) && is_array($l['field_data'])) {
            foreach ($l['field_data'] as $f) {
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
        $sql = "SELECT enquiry_id, enquiry_mobile, enquiry_email, enquiry_message, enquiry_time 
                FROM " . TABLE_PREFIX . "enquiry 
                WHERE enquiry_site_id = " . SITE_ID . " 
                  AND (" . implode(" OR ", $conditions) . ")
                ORDER BY enquiry_id DESC";
        $res = mysqli_query($connection, $sql);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $enqId = (int)$row['enquiry_id'];
                foreach ($leadIds as $lid) {
                    if (strpos($row['enquiry_message'], $lid) !== false) {
                        $syncMap[$lid] = $enqId;
                    }
                }
                $cleanRowMob = substr(preg_replace('/[^0-9]/', '', $row['enquiry_mobile']), -10);
                if (!empty($cleanRowMob)) {
                    $syncMap['mob_' . $cleanRowMob] = $enqId;
                }
                $cleanRowEmail = strtolower(trim($row['enquiry_email']));
                if (!empty($cleanRowEmail)) {
                    $syncMap['email_' . $cleanRowEmail] = $enqId;
                }
            }
        }
    }
}

// KPI Calculations
$fetchedLeadsCount = count($leads);
$latestLeadTime = 'N/A';
$firstLeadTime  = 'N/A';
$hasPhoneCount  = 0;

if (!empty($leads)) {
    $timestamps = [];
    foreach ($leads as $l) {
        if (!empty($l['created_time'])) {
            $timestamps[] = strtotime($l['created_time']);
        }
        if (!empty($l['field_data']) && is_array($l['field_data'])) {
            foreach ($l['field_data'] as $f) {
                $fName = strtolower($f['name'] ?? '');
                $fVal  = is_array($f['values'] ?? '') ? implode('', $f['values']) : ($f['values'] ?? '');
                if (!empty($fVal) && in_array($fName, ['phone_number', 'phone', 'mobile_number', 'mobile', 'whatsapp_number'])) {
                    $hasPhoneCount++;
                    break;
                }
            }
        }
    }
    if (!empty($timestamps)) {
        rsort($timestamps);
        $latestLeadTime = date('d M Y, h:i A', $timestamps[0]);
        $firstLeadTime  = date('d M Y, h:i A', end($timestamps));
    }
}
$phoneRate = ($fetchedLeadsCount > 0) ? round(($hasPhoneCount / $fetchedLeadsCount) * 100) : 0;

// Pagination range info
$startIdx = $totalCampaignLeads > 0 ? (($currentPage - 1) * $limit) + 1 : 0;
$endIdx   = min($startIdx + $fetchedLeadsCount - 1, $totalCampaignLeads);
if ($startIdx > $endIdx) {
    $startIdx = $endIdx > 0 ? 1 : 0;
}
$isDigitalMarketing = (isset($site_access_type) && $site_access_type === 'digital_marketing');
?>

<div class="package-page-wrapper fb-integration-wrapper" id="fbFormLeadsApp"
    data-form-id="<?php echo htmlspecialchars($formId); ?>"
    data-page-id="<?php echo htmlspecialchars($pageId); ?>"
    data-current-page="<?php echo $currentPage; ?>"
    data-total-leads="<?php echo $totalCampaignLeads; ?>"
    data-limit="<?php echo $limit; ?>"
    data-after="<?php echo htmlspecialchars($nextCursor ?? ''); ?>"
    data-before="<?php echo htmlspecialchars($prevCursor ?? ''); ?>">

    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="integration-brand-logo brand-facebook">
                <img src="/app/dashboard/v3/default/desktop/assets/images/integration/facebook.svg" alt="Facebook">
            </div>
            <div class="package-header-titles">
                <h2><?php echo htmlspecialchars($formDisplayName); ?></h2>
                <div class="pkg-meta-row">
                    <span class="pkg-theme-pill pkg-theme-standard"><?php echo htmlspecialchars($pageDisplayName); ?></span>
                    <span class="pkg-duration-badge">
                        <i class="fi fi-rr-form"></i> Form ID: <?php echo htmlspecialchars($formId); ?>
                    </span>
                    <!-- Total Campaign Leads in Header -->
                    <span class="pkg-duration-badge pkg-location-badge" id="headerCampaignLeadsBadge" title="Total inquiries captured across this campaign">
                        <i class="fi fi-rr-users"></i> <?php echo number_format($totalCampaignLeads); ?> Campaign Leads
                    </span>
                    <span class="v3-status-badge status-active">
                        <span class="status-pulse-dot"></span> Live Ingestion
                    </span>
                </div>
            </div>
        </div>
        <div class="package-header-actions">
            <?php if (!empty($pageId)) { ?>
                <a href="/dashboard/integration/facebook/page/?id=<?php echo urlencode($pageId); ?>" class="v3-btn-secondary" title="Back to Page Forms">
                    <i class="fi fi-rr-arrow-left"></i> <span>All Forms</span>
                </a>
            <?php } else { ?>
                <a href="/dashboard/integration/facebook/" class="v3-btn-secondary" title="Back to Facebook Pages">
                    <i class="fi fi-rr-arrow-left"></i> <span>All Pages</span>
                </a>
            <?php } ?>
            <button type="button" class="v3-btn-primary" id="btnRefreshFbLeads" title="Refresh Latest Leads">
                <i class="fi fi-rr-refresh"></i> <span>Refresh Leads</span>
            </button>
        </div>
    </div>

    <?php if (!$formRow) { ?>
        <div class="fb-connect-hero">
            <div class="fb-connect-icon">
                <i class="fi fi-rr-exclamation"></i>
            </div>
            <h3>Form Not Found</h3>
            <p>The specified Facebook lead generation form could not be verified in your workspace.</p>
            <a href="/dashboard/integration/facebook/" class="btn-fb-connect">
                <i class="fi fi-rr-arrow-left"></i> Return to Facebook Integration
            </a>
        </div>
    <?php } elseif (!empty($fetchError)) { ?>
        <div class="fb-sync-banner">
            <div class="fb-sync-banner-left">
                <i class="fi fi-rr-exclamation color-rose"></i>
                <div>
                    <h4>Facebook Graph API Response</h4>
                    <p><?php echo htmlspecialchars($fetchError); ?></p>
                </div>
            </div>
            <div class="fb-sync-banner-right">
                <button type="button" class="v3-btn-primary" id="btnRetryFetchLeads">
                    <i class="fi fi-rr-refresh"></i> Retry Fetch
                </button>
            </div>
        </div>
    <?php } else { ?>
        <!-- KPI Metrics Grid -->
        <div class="v3-kpi-grid">
            <!-- 1. Total Campaign Leads -->
            <div class="v3-kpi-card">
                <div class="v3-kpi-icon-wrap blue">
                    <i class="fi fi-rr-users"></i>
                </div>
                <div class="v3-kpi-info">
                    <span class="v3-kpi-label">Total Leads in Campaign</span>
                    <span class="v3-kpi-val" id="kpiTotalCampaignLeads"><?php echo number_format($totalCampaignLeads); ?></span>
                    <span class="v3-kpi-sub">Total Facebook Lead Ad submissions</span>
                </div>
            </div>

            <!-- 2. Latest Lead -->
            <div class="v3-kpi-card">
                <div class="v3-kpi-icon-wrap green">
                    <i class="fi fi-rr-time-fast"></i>
                </div>
                <div class="v3-kpi-info">
                    <span class="v3-kpi-label">Latest Lead</span>
                    <span class="v3-kpi-val v3-kpi-val-time" id="kpiLatestLeadTime"><?php echo htmlspecialchars($latestLeadTime); ?></span>
                    <span class="v3-kpi-sub">Most recent submission</span>
                </div>
            </div>

            <!-- 3. Campaign Inception -->
            <div class="v3-kpi-card">
                <div class="v3-kpi-icon-wrap purple">
                    <i class="fi fi-rr-calendar-clock"></i>
                </div>
                <div class="v3-kpi-info">
                    <span class="v3-kpi-label">Campaign First Lead</span>
                    <span class="v3-kpi-val v3-kpi-val-time" id="kpiFirstLeadTime"><?php echo htmlspecialchars($firstLeadTime); ?></span>
                    <span class="v3-kpi-sub">Campaign inception</span>
                </div>
            </div>

            <!-- 4. Contact Validity -->
            <div class="v3-kpi-card">
                <div class="v3-kpi-icon-wrap amber">
                    <i class="fi fi-rr-phone-call"></i>
                </div>
                <div class="v3-kpi-info">
                    <span class="v3-kpi-label">Contact Validity</span>
                    <span class="v3-kpi-val" id="kpiPhoneRate"><?php echo $phoneRate; ?>%</span>
                    <span class="v3-kpi-sub" id="kpiPhoneCount"><?php echo $hasPhoneCount; ?> Verified Phone Numbers</span>
                </div>
            </div>
        </div>

        <!-- Leads Table Card -->
        <div class="v3-table-card">
                <div class="v3-table-card-header">
                    <div class="v3-table-card-header-left">
                        <i class="fi fi-rr-form v3-form-card-icon color-blue"></i>
                        <h3>Captured Form Inquiries</h3>
                        <span class="v3-badge v3-badge-blue" id="tableHeaderLeadCount"><?php echo number_format($totalCampaignLeads); ?> Campaign Leads</span>
                    </div>
                    <div>
                        <div class="integration-search-box m-0">
                            <i class="fi fi-rr-search"></i>
                            <input type="text" id="leadSearchInput" placeholder="<?php echo ($isDigitalMarketing ? 'Search leads by name, id...' : 'Search leads by name, phone, email...'); ?>" class="integration-search-input v3-search-input-compact">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="v3-data-table">
                        <thead>
                            <tr>
                                <th>Date &amp; Time</th>
                                <th>Contact Person</th>
                                <th>Contact Details</th>
                                <th>Form Responses</th>
                                <th>Lead ID</th>
                                <th>Lead Sync to CRM</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="fbLeadsTableBody">
                            <?php
                            if (!empty($leads)) {
                                foreach ($leads as $lead) {
                                $createdTime = !empty($lead['created_time']) ? date('d M Y, h:i A', strtotime($lead['created_time'])) : 'N/A';
                                $leadIdVal   = $lead['id'] ?? 'N/A';

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
                                            $emailVal = $isDigitalMarketing ? '' : $fieldValStr;
                                        } elseif (in_array($fieldName, ['phone_number', 'phone', 'mobile_number', 'mobile', 'whatsapp_number'])) {
                                            $phoneVal = $isDigitalMarketing ? '' : $fieldValStr;
                                        } else {
                                            $cleanKey = formatFbQuestionLabel($fieldName);
                                            $isContactField = (bool)preg_match('/(phone|mobile|email|contact|whatsapp)/i', $fieldName);
                                            $customResponses[] = [
                                                'label' => $cleanKey,
                                                'val'   => ($isDigitalMarketing && $isContactField) ? '[Protected]' : formatFbResponseVal($fieldValStr)
                                            ];
                                        }
                                    }
                                }

                                if (empty($fullName)) {
                                    $fullName = 'Facebook Lead #' . substr($leadIdVal, -4);
                                }

                                $nameWords = preg_split('/\s+/', trim($fullName));
                                if (count($nameWords) >= 2 && !empty($nameWords[0]) && !empty($nameWords[1])) {
                                    $initials = strtoupper(substr($nameWords[0], 0, 1) . substr($nameWords[1], 0, 1));
                                } else {
                                    $initials = strtoupper(substr($fullName, 0, 1));
                                }

                                $avatarVariants = ['avatar-blue', 'avatar-indigo', 'avatar-purple', 'avatar-emerald', 'avatar-amber'];
                                $avatarClass = $avatarVariants[abs(crc32($fullName)) % 5];

                                // Detect Destination if available
                                $leadDest = '';
                                if (!empty($customResponses)) {
                                    foreach ($customResponses as $cr) {
                                        if ($cr['label'] === 'Destination' && !empty($cr['val']) && strlen($cr['val']) <= 25) {
                                            $leadDest = $cr['val'];
                                            break;
                                        }
                                    }
                                }

                                // Determine CRM Sync Status
                                $isSynced = false;
                                $crmEnquiryId = null;

                                if (!empty($lead['crm_enquiry_id'])) {
                                    $isSynced = true;
                                    $crmEnquiryId = (int)$lead['crm_enquiry_id'];
                                } elseif (!empty($syncMap[$leadIdVal])) {
                                    $isSynced = true;
                                    $crmEnquiryId = (int)$syncMap[$leadIdVal];
                                } else {
                                    $cleanMob = substr(preg_replace('/[^0-9]/', '', $phoneVal), -10);
                                    if (!empty($cleanMob) && !empty($syncMap['mob_' . $cleanMob])) {
                                        $isSynced = true;
                                        $crmEnquiryId = (int)$syncMap['mob_' . $cleanMob];
                                    } elseif (!empty($emailVal) && !empty($syncMap['email_' . strtolower(trim($emailVal))])) {
                                        $isSynced = true;
                                        $crmEnquiryId = (int)$syncMap['email_' . strtolower(trim($emailVal))];
                                    }
                                }

                                // WhatsApp Link preparation
                                $cleanWa = preg_replace('/[^0-9]/', '', $phoneVal);
                                $waNum = (strlen($cleanWa) === 10) ? ('91' . $cleanWa) : $cleanWa;
                                $waText = urlencode("Hello " . $fullName . ", thank you for inquiring about " . $formDisplayName . " on Facebook. How may we assist you?");
                                $waUrl = "https://wa.me/{$waNum}?text={$waText}";

                                // JSON payload for modal
                                $leadPayload = [
                                    'id'             => $leadIdVal,
                                    'created_time'   => $createdTime,
                                    'full_name'      => $fullName,
                                    'phone'          => $isDigitalMarketing ? '' : $phoneVal,
                                    'email'          => $isDigitalMarketing ? '' : $emailVal,
                                    'questions'      => $customResponses,
                                    'is_synced'      => $isSynced,
                                    'crm_enquiry_id' => $crmEnquiryId
                                ];
                                $jsonEncodedLead = rawurlencode(json_encode($leadPayload));
                            ?>
                                <tr class="fb-lead-row" data-lead-id="<?php echo htmlspecialchars($leadIdVal); ?>">
                                    <td>
                                        <div class="fb-lead-date-box">
                                            <span class="lead-date"><?php echo htmlspecialchars(explode(',', $createdTime)[0]); ?></span>
                                            <span class="lead-time"><i class="fi fi-rr-clock-three"></i> <?php echo htmlspecialchars(trim(explode(',', $createdTime)[1] ?? '')); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="v3-table-form-cell">
                                            <div class="fb-lead-avatar <?php echo $avatarClass; ?>">
                                                <?php echo htmlspecialchars($initials); ?>
                                            </div>
                                            <div class="v3-table-form-info">
                                                <h4 class="lead-customer-name" title="<?php echo htmlspecialchars($fullName); ?>"><?php echo htmlspecialchars($fullName); ?></h4>
                                                <div class="lead-sub-chips">
                                                    <?php if (!empty($leadDest)) { ?>
                                                        <span class="v3-lead-dest-chip" title="Destination: <?php echo htmlspecialchars($leadDest); ?>">
                                                            <i class="fi fi-rr-map-marker"></i> <?php echo htmlspecialchars($leadDest); ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="v3-lead-source-chip">
                                                            <i class="fi fi-brands-facebook"></i> Meta Lead
                                                        </span>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fb-contact-cell">
                                            <?php if ($isDigitalMarketing) { ?>
                                                <span class="v3-badge v3-badge-gray" title="Contact details protected for digital marketing">Protected</span>
                                            <?php } else { ?>
                                                <?php if (!empty($phoneVal)) { ?>
                                                    <div class="fb-contact-phone-row">
                                                        <a href="tel:<?php echo htmlspecialchars($phoneVal); ?>" class="fb-contact-link phone">
                                                            <i class="fi fi-rr-phone-call"></i> <span><?php echo htmlspecialchars($phoneVal); ?></span>
                                                        </a>
                                                        <a href="<?php echo $waUrl; ?>" target="_blank" rel="noopener noreferrer" class="fb-wa-action-btn" title="Chat on WhatsApp">
                                                            <i class="fi fi-brands-whatsapp"></i>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                                <?php if (!empty($emailVal)) { ?>
                                                    <a href="mailto:<?php echo htmlspecialchars($emailVal); ?>" class="fb-contact-link email" title="<?php echo htmlspecialchars($emailVal); ?>">
                                                        <i class="fi fi-rr-envelope"></i> <span><?php echo htmlspecialchars($emailVal); ?></span>
                                                    </a>
                                                <?php } ?>
                                                <?php if (empty($phoneVal) && empty($emailVal)) { ?>
                                                    <span class="fb-contact-empty">—</span>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="v3-response-pills-wrap">
                                            <?php if (!empty($customResponses)) {
                                                foreach (array_slice($customResponses, 0, 2) as $resp) { ?>
                                                    <span class="v3-response-chip" title="<?php echo htmlspecialchars($resp['label'] . ': ' . $resp['val']); ?>">
                                                        <span class="chip-label"><?php echo htmlspecialchars($resp['label']); ?>:</span>
                                                        <span class="chip-val"><?php echo htmlspecialchars($resp['val']); ?></span>
                                                    </span>
                                                <?php }
                                                if (count($customResponses) > 2) { ?>
                                                    <button type="button" class="v3-chip-more btn-view-lead-modal" data-lead="<?php echo $jsonEncodedLead; ?>" title="Click to view all responses">
                                                        +<?php echo (count($customResponses) - 2); ?> more
                                                    </button>
                                                <?php }
                                            } else { ?>
                                                <span class="v3-response-chip is-default">Default Ad Capture</span>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="v3-lead-id-wrap">
                                            <span class="v3-code-pill" title="Lead ID: <?php echo htmlspecialchars($leadIdVal); ?>"><?php echo htmlspecialchars($leadIdVal); ?></span>
                                            <button type="button" class="v3-copy-chip-btn btn-copy-lead-id" data-copy="<?php echo htmlspecialchars($leadIdVal); ?>" title="Copy Lead ID">
                                                <i class="fi fi-rr-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <!-- Lead Sync to CRM Column -->
                                    <td class="crm-sync-status-cell">
                                        <?php if ($isSynced) { ?>
                                            <div class="v3-crm-sync-status-group">
                                                <span class="v3-sync-badge is-synced" title="Lead successfully ingested into CRM">
                                                    <span class="status-pulse-dot"></span> Synced to CRM
                                                </span>
                                                <?php if (!empty($crmEnquiryId)) { ?>
                                                    <a href="/dashboard/leads/view/?id=<?php echo $crmEnquiryId; ?>" class="v3-crm-link-chip" title="Open Enquiry #<?php echo $crmEnquiryId; ?> in CRM">
                                                        <i class="fi fi-rr-arrow-up-right-from-square"></i>
                                                        <span>Enquiry #<?php echo $crmEnquiryId; ?></span>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        <?php } else { ?>
                                            <div class="v3-crm-sync-status-group">
                                                <span class="v3-sync-badge is-pending" title="Lead pending CRM ingestion">
                                                    <span class="status-pulse-dot dot-amber"></span> Not in CRM
                                                </span>
                                                <button type="button" class="btn-v3-sync-now btn-sync-lead-crm" data-lead-id="<?php echo htmlspecialchars($leadIdVal); ?>" data-form-id="<?php echo htmlspecialchars($formId); ?>" title="Push lead into CRM immediately">
                                                    <i class="fi fi-rr-cloud-upload"></i>
                                                    <span>Push to CRM</span>
                                                </button>
                                            </div>
                                        <?php } ?>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="v3-btn-table-action btn-view-lead-modal" data-lead="<?php echo $jsonEncodedLead; ?>" title="Open Full Lead Profile">
                                            <i class="fi fi-rr-eye"></i> <span>View Details</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php } 
                            } else { ?>
                                <tr>
                                    <td colspan="7" class="v3-table-empty-td">
                                        <div class="v3-table-empty-state">
                                            <div class="v3-table-empty-icon">
                                                <i class="fi fi-rr-users"></i>
                                            </div>
                                            <h4>No Leads Captured in this Form Yet</h4>
                                            <p>This Facebook Lead Ad form has not received any prospect submissions yet. As soon as users submit the form on Facebook or Instagram, leads will arrive here in real time via live webhooks.</p>
                                            <div class="v3-table-empty-actions">
                                                <button type="button" class="v3-btn-secondary" id="btnEmptyRefreshLeads" title="Check for new leads from Facebook">
                                                    <i class="fi fi-rr-refresh"></i> <span>Refresh Leads Now</span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($leads)) { ?>
                    <!-- Table Pagination Bar: Max 20 Leads Per Request -->
                    <div class="v3-table-pagination-bar" id="leadPaginationBar">
                        <div class="v3-pagination-info" id="leadPaginationInfo">
                            Showing <?php echo $startIdx; ?> to <?php echo $endIdx; ?> of <?php echo number_format($totalCampaignLeads); ?> campaign leads
                        </div>
                        <div class="v3-pagination-controls" id="leadPaginationControls">
                            <button type="button" class="v3-page-btn" id="leadPrevPageBtn" <?php echo ($currentPage <= 1 && empty($beforeCursor)) ? 'disabled' : ''; ?> title="Previous 20 Leads">
                                <i class="fi fi-rr-angle-small-left"></i> Previous
                            </button>
                            <span class="v3-page-indicator" id="leadPageIndicator">
                                Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>
                            </span>
                            <button type="button" class="v3-page-btn" id="leadNextPageBtn" <?php echo (!$hasNext && $currentPage >= $totalPages) ? 'disabled' : ''; ?> title="Next 20 Leads">
                                Next <i class="fi fi-rr-angle-small-right"></i>
                            </button>
                        </div>
                    </div>
                <?php } ?>
            </div>
    <?php } ?>

    <!-- Full Lead Details Modal -->
    <div class="v3-modal-overlay" id="fbLeadDetailsModalOverlay">
        <div class="fb-lead-modal-dialog">
            <div class="fb-lead-modal-header">
                <div class="v3-modal-header-brand">
                    <div class="integration-brand-logo brand-facebook v3-modal-brand-badge">
                        <img src="/app/dashboard/v3/default/desktop/assets/images/integration/facebook.svg" alt="Facebook" class="v3-modal-brand-icon">
                    </div>
                    <div>
                        <h3 id="fbModalLeadName" class="v3-modal-lead-title">Lead Details</h3>
                        <span class="v3-modal-sub">Facebook Lead Ad Submission</span>
                    </div>
                </div>
                <button type="button" class="v3-modal-close-btn" id="fbModalCloseBtn" aria-label="Close modal">
                    <i class="fi fi-rr-cross"></i>
                </button>
            </div>

            <div class="fb-lead-modal-body">
                <!-- Meta Info Header Card -->
                <div class="fb-modal-contact-card">
                    <div class="v3-flex-1">
                        <div class="v3-modal-meta-row mb-6">
                            <span class="v3-modal-meta-label">Lead ID</span>
                            <span class="v3-code-pill" id="fbModalLeadId">#</span>
                        </div>
                        <div class="v3-modal-meta-row mb-6">
                            <span class="v3-modal-meta-label">Captured Time</span>
                            <span class="v3-modal-meta-val" id="fbModalCreatedTime">—</span>
                        </div>
                        <div class="v3-modal-meta-row">
                            <span class="v3-modal-meta-label">CRM Sync</span>
                            <span id="fbModalSyncStatus" class="v3-modal-meta-val">—</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Details -->
                <div>
                    <h5 class="v3-modal-section-title">Contact Details</h5>
                    <div id="fbModalContactInfo" class="v3-modal-contact-card"></div>
                </div>

                <!-- Question Answers List -->
                <div>
                    <h5 class="v3-modal-section-title">Form Answers &amp; Questions</h5>
                    <div id="fbModalQAList" class="fb-modal-qa-list"></div>
                </div>
            </div>

            <div class="v3-modal-footer v3-modal-footer-standard">
                <button type="button" class="v3-btn-secondary btn-modal-dismiss">
                    Close
                </button>
                <?php if (!$isDigitalMarketing) { ?>
                    <a href="/dashboard/leads/list/" class="v3-btn-primary" id="fbModalCrmLeadBtn">
                        <i class="fi fi-rr-arrow-right"></i> <span>Open in CRM</span>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>