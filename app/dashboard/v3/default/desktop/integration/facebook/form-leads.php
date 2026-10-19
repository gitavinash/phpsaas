<?php
// Workspace Rule Compliant: V3 Facebook Form Leads Viewer & KPI Analytics

$formId = isset($id) ? $id : (isset($_GET['id']) ? $_GET['id'] : '');
$sync   = (isset($_GET['sync']) && $_GET['sync'] === 'true');

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

$leads = [];
$fetchError = '';

function formatFbQuestionLabel($label)
{
    $label = trim($label, " ?:;,-_");
    $low = strtolower($label);
    if (strpos($low, 'how many people') !== false || strpos($low, 'no of persons') !== false || strpos($low, 'travelling') !== false || strpos($low, 'passengers') !== false) {
        return 'Travelers';
    }
    if (strpos($low, 'when are you planning') !== false || strpos($low, 'travel date') !== false || strpos($low, 'planning') !== false) {
        return 'Timeline';
    }
    if (strpos($low, 'destination') !== false || strpos($low, 'where') !== false) {
        return 'Destination';
    }
    if (strpos($low, 'budget') !== false) {
        return 'Budget';
    }
    if (strpos($low, 'duration') !== false) {
        return 'Duration';
    }
    return ucwords(str_replace(['_', '-'], ' ', $label));
}

function formatFbResponseVal($val)
{
    $clean = str_replace('_', ' ', (string)$val);
    $clean = preg_replace('/\s+/', ' ', $clean);
    return ucwords(trim($clean));
}

if ($formRow && !empty($formRow['fb_page_access_token'])) {
    function fetchFacebookFormLeads($formId, $pageAccessToken, $limit = 100)
    {
        $allLeads   = [];
        $after      = null;
        $fetched    = 0;
        $perRequest = min($limit, 50);

        do {
            $params = [
                'access_token' => $pageAccessToken,
                'limit'        => $perRequest,
                'fields'       => 'id,created_time,field_data'
            ];
            if ($after) {
                $params['after'] = $after;
            }

            $url = "https://graph.facebook.com/v19.0/{$formId}/leads?" . http_build_query($params);

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 30,
            ]);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                $err = curl_error($ch);
                curl_close($ch);
                return ['error' => $err, 'leads' => $allLeads];
            }
            curl_close($ch);

            $data = json_decode($response, true);

            if (isset($data['error'])) {
                return ['error' => $data['error']['message'] ?? 'API error', 'leads' => $allLeads];
            }

            if (!empty($data['data']) && is_array($data['data'])) {
                foreach ($data['data'] as $lead) {
                    if ($fetched >= $limit) break 2;
                    $allLeads[] = $lead;
                    $fetched++;
                }
            }

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && $fetched < $limit);

        return ['error' => null, 'leads' => $allLeads];
    }

    $leadResult = fetchFacebookFormLeads($formId, $formRow['fb_page_access_token'], 100);
    $leads = $leadResult['leads'] ?? [];
    $fetchError = $leadResult['error'] ?? '';
}

// KPI Calculations
$totalLeadsCount = count($leads);
$latestLeadTime = 'N/A';
$firstLeadTime  = 'N/A';
$hasPhoneCount  = 0;
$hasEmailCount  = 0;

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
                if (!empty($fVal)) {
                    if (in_array($fName, ['phone_number', 'phone', 'mobile_number', 'mobile', 'whatsapp_number'])) {
                        $hasPhoneCount++;
                    }
                    if (in_array($fName, ['email', 'email_address', 'your_email'])) {
                        $hasEmailCount++;
                    }
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
$phoneRate = ($totalLeadsCount > 0) ? round(($hasPhoneCount / $totalLeadsCount) * 100) : 0;
?>

<div class="package-page-wrapper fb-integration-wrapper" id="fbFormLeadsApp">
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
            <a href="<?php echo str_replace('&sync=true', '', ACTIVE_URL); ?>&sync=true" class="v3-btn-primary" title="Refresh Leads from Facebook">
                <i class="fi fi-rr-refresh"></i> <span>Refresh Leads</span>
            </a>
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
                <a href="<?php echo str_replace('&sync=true', '', ACTIVE_URL); ?>&sync=true" class="v3-btn-primary">
                    <i class="fi fi-rr-refresh"></i> Retry Fetch
                </a>
            </div>
        </div>
    <?php } else { ?>
        <!-- KPI Metrics Grid -->
        <div class="v3-kpi-grid">
            <!-- 1. Total Leads -->
            <div class="v3-kpi-card">
                <div class="v3-kpi-icon-wrap blue">
                    <i class="fi fi-rr-users"></i>
                </div>
                <div class="v3-kpi-info">
                    <span class="v3-kpi-label">Total Leads Received</span>
                    <span class="v3-kpi-val"><?php echo number_format($totalLeadsCount); ?></span>
                    <span class="v3-kpi-sub">From Facebook Lead Ads</span>
                </div>
            </div>

            <!-- 2. Latest Lead -->
            <div class="v3-kpi-card">
                <div class="v3-kpi-icon-wrap green">
                    <i class="fi fi-rr-time-fast"></i>
                </div>
                <div class="v3-kpi-info">
                    <span class="v3-kpi-label">Latest Lead</span>
                    <span class="v3-kpi-val v3-kpi-val-time"><?php echo htmlspecialchars($latestLeadTime); ?></span>
                    <span class="v3-kpi-sub">Most recent submission</span>
                </div>
            </div>

            <!-- 3. First Lead -->
            <div class="v3-kpi-card">
                <div class="v3-kpi-icon-wrap purple">
                    <i class="fi fi-rr-calendar-clock"></i>
                </div>
                <div class="v3-kpi-info">
                    <span class="v3-kpi-label">Campaign First Lead</span>
                    <span class="v3-kpi-val v3-kpi-val-time"><?php echo htmlspecialchars($firstLeadTime); ?></span>
                    <span class="v3-kpi-sub">Campaign inception</span>
                </div>
            </div>

            <!-- 4. Verified Phone Rate -->
            <div class="v3-kpi-card">
                <div class="v3-kpi-icon-wrap amber">
                    <i class="fi fi-rr-phone-call"></i>
                </div>
                <div class="v3-kpi-info">
                    <span class="v3-kpi-label">Contact Validity</span>
                    <span class="v3-kpi-val"><?php echo $phoneRate; ?>%</span>
                    <span class="v3-kpi-sub"><?php echo $hasPhoneCount; ?> Verified Phone Numbers</span>
                </div>
            </div>
        </div>

        <?php if (!empty($leads)) { ?>
            <!-- Leads Table Card -->
            <div class="v3-table-card">
                <div class="v3-table-card-header">
                    <div class="v3-table-card-header-left">
                        <i class="fi fi-rr-form v3-form-card-icon color-blue"></i>
                        <h3>Captured Form Inquiries</h3>
                        <span class="v3-badge v3-badge-blue"><?php echo $totalLeadsCount; ?> Total</span>
                    </div>
                    <div>
                        <div class="integration-search-box m-0">
                            <i class="fi fi-rr-search"></i>
                            <input type="text" id="leadSearchInput" placeholder="<?php echo ((isset($site_access_type) && $site_access_type === 'digital_marketing') ? 'Search leads by name, id...' : 'Search leads by name, phone, email...'); ?>" class="integration-search-input v3-search-input-compact">
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
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="fbLeadsTableBody">
                            <?php 
                            $isDigitalMarketing = (isset($site_access_type) && $site_access_type === 'digital_marketing');
                            foreach ($leads as $lead) {
                                $createdTime = !empty($lead['created_time']) ? date('d M Y, h:i A', strtotime($lead['created_time'])) : 'N/A';
                                $leadIdVal = $lead['id'] ?? 'N/A';
                                
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
                                            $cleanKey = ucwords(str_replace(['_', '-'], ' ', $fieldName));
                                            $isContactField = preg_match('/(phone|mobile|email|contact|whatsapp)/i', $fieldName);
                                            $customResponses[] = [
                                                'label' => $cleanKey,
                                                'val'   => ($isDigitalMarketing && $isContactField) ? '[Protected]' : $fieldValStr
                                            ];
                                        }
                                    }
                                }

                                if (empty($fullName)) {
                                    $fullName = 'Facebook Lead #' . substr($leadIdVal, -4);
                                }

                                $initial = strtoupper(substr($fullName, 0, 1));
                                
                                // JSON payload for modal
                                $leadPayload = [
                                    'id'           => $leadIdVal,
                                    'created_time' => $createdTime,
                                    'full_name'    => $fullName,
                                    'phone'        => $isDigitalMarketing ? '' : $phoneVal,
                                    'email'        => $isDigitalMarketing ? '' : $emailVal,
                                    'questions'    => $customResponses
                                ];
                                $jsonEncodedLead = rawurlencode(json_encode($leadPayload));
                                ?>
                                <tr class="fb-lead-row">
                                    <td>
                                        <div class="fb-lead-date-box">
                                            <span class="lead-date"><?php echo htmlspecialchars(explode(',', $createdTime)[0]); ?></span>
                                            <span class="lead-time"><i class="fi fi-rr-clock-three"></i> <?php echo htmlspecialchars(trim(explode(',', $createdTime)[1] ?? '')); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="v3-table-form-cell">
                                            <div class="fb-lead-avatar">
                                                <?php echo htmlspecialchars($initial); ?>
                                            </div>
                                            <div class="v3-table-form-info">
                                                <h4><?php echo htmlspecialchars($fullName); ?></h4>
                                                <span>Facebook Prospect</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fb-contact-cell">
                                            <?php if ($isDigitalMarketing) { ?>
                                                <span class="v3-badge v3-badge-gray" title="Contact details protected for digital marketing">Protected</span>
                                            <?php } else { ?>
                                                <?php if (!empty($phoneVal)) { ?>
                                                    <a href="tel:<?php echo htmlspecialchars($phoneVal); ?>" class="fb-contact-link phone">
                                                        <i class="fi fi-rr-phone-call"></i> <span><?php echo htmlspecialchars($phoneVal); ?></span>
                                                    </a>
                                                <?php } ?>
                                                <?php if (!empty($emailVal)) { ?>
                                                    <a href="mailto:<?php echo htmlspecialchars($emailVal); ?>" class="fb-contact-link email">
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
                                        <div class="fb-lead-responses-list">
                                            <?php if (!empty($customResponses)) { 
                                                foreach (array_slice($customResponses, 0, 2) as $resp) { ?>
                                                    <span class="fb-response-tag">
                                                        <span class="tag-q"><?php echo htmlspecialchars(formatFbQuestionLabel($resp['label'])); ?>:</span>
                                                        <span class="tag-a"><?php echo htmlspecialchars(formatFbResponseVal($resp['val'])); ?></span>
                                                    </span>
                                                <?php } 
                                                if (count($customResponses) > 2) { ?>
                                                    <button type="button" class="fb-more-responses-btn btn-view-lead-modal" data-lead="<?php echo $jsonEncodedLead; ?>" title="Click to view all responses">
                                                        +<?php echo (count($customResponses) - 2); ?> more
                                                    </button>
                                                <?php }
                                            } else { ?>
                                                <span class="v3-badge v3-badge-gray">Standard Inquiry</span>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="v3-code-pill"><?php echo htmlspecialchars($leadIdVal); ?></span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="v3-btn-table-action btn-view-lead-modal" data-lead="<?php echo $jsonEncodedLead; ?>" title="Open Full Lead Profile">
                                            <i class="fi fi-rr-eye"></i> View Details
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Table Pagination Footer -->
                <div class="v3-table-pagination-bar">
                    <div class="v3-pagination-info" id="leadPaginationInfo">
                        Showing 1 to 10 of <?php echo $totalLeadsCount; ?> leads
                    </div>
                    <div class="v3-pagination-controls" id="leadPaginationControls"></div>
                </div>
            </div>
        <?php } else { ?>
            <!-- Empty Leads State -->
            <div class="integration-empty-state">
                <i class="fi fi-rr-users empty-icon"></i>
                <h4>No Leads Captured Yet</h4>
                <p>New inquiries submitted on your Facebook ad forms will automatically stream into this list in real-time.</p>
                <a href="<?php echo str_replace('&sync=true', '', ACTIVE_URL); ?>&sync=true" class="v3-btn-primary">
                    <i class="fi fi-rr-refresh"></i> Refresh Leads Now
                </a>
            </div>
        <?php } ?>
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
                        <div class="v3-modal-meta-row">
                            <span class="v3-modal-meta-label">Captured Time</span>
                            <span class="v3-modal-meta-val" id="fbModalCreatedTime">—</span>
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
                <a href="/dashboard/leads/list/" class="v3-btn-primary">
                    <i class="fi fi-rr-arrow-right"></i> <span>Open CRM Leads</span>
                </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

