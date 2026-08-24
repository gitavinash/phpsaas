<?php
// Workspace Rule Compliant: V3 Facebook Form Leads Viewer

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
?>

<div class="package-page-wrapper fb-integration-wrapper">
    <!-- Header Banner Matching packages/domestic/edit.php -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-facebook">
                <i class="fi fi-rr-form"></i>
            </div>
            <div class="package-header-titles">
                <h2><?php echo htmlspecialchars($formDisplayName); ?></h2>
                <div class="pkg-meta-row">
                    <span class="pkg-theme-pill pkg-theme-standard"><?php echo htmlspecialchars($pageDisplayName); ?></span>
                    <span class="pkg-duration-badge">
                        <i class="fi fi-rr-form"></i> Form ID: <?php echo htmlspecialchars($formId); ?>
                    </span>
                    <span class="pkg-duration-badge pkg-location-badge">
                        <i class="fi fi-rr-users"></i> <?php echo count($leads); ?> Fetched Leads
                    </span>
                </div>
            </div>
        </div>
        <div class="package-header-actions">
            <?php if (!empty($pageId)) { ?>
                <a href="integration/facebook/page/?id=<?php echo urlencode($pageId); ?>" class="btn-gallery-action btn-back-list" title="Back to Page Forms">
                    <i class="fi fi-rr-arrow-left"></i> Forms List
                </a>
            <?php } else { ?>
                <a href="integration/facebook/" class="btn-gallery-action btn-back-list" title="Back to Facebook Pages">
                    <i class="fi fi-rr-arrow-left"></i> All Pages
                </a>
            <?php } ?>
            <a href="<?php echo str_replace('&sync=true', '', ACTIVE_URL); ?>&sync=true" class="btn-gallery-action btn-manage-gallery" title="Refresh Leads from Facebook">
                <i class="fi fi-rr-refresh"></i> Refresh Leads
            </a>
            <a href="/dashboard/leads/list/" class="btn-gallery-action btn-reconnect" title="View All Leads">
                <i class="fi fi-rr-users"></i> View All Leads
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
            <a href="integration/facebook/" class="btn-fb-connect">
                <i class="fi fi-rr-arrow-left"></i> Return to Facebook Integration
            </a>
        </div>
    <?php } elseif (!empty($fetchError)) { ?>
        <div class="fb-sync-banner">
            <div class="fb-sync-banner-left">
                <i class="fi fi-rr-exclamation" style="color: #dc2626;"></i>
                <div>
                    <h4>Facebook Graph API Response</h4>
                    <p><?php echo htmlspecialchars($fetchError); ?></p>
                </div>
            </div>
            <div class="fb-sync-banner-right">
                <a href="<?php echo str_replace('&sync=true', '', ACTIVE_URL); ?>&sync=true" class="btn-gallery-action btn-reconnect">
                    <i class="fi fi-rr-refresh"></i> Retry Fetch
                </a>
            </div>
        </div>
    <?php } elseif (!empty($leads)) { ?>
        <!-- Leads Table Card -->
        <div class="package-table-card">
            <div class="table-responsive">
                <table class="package-data-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Contact Person</th>
                            <th>Contact Info</th>
                            <th>Form Responses & Answers</th>
                            <th>Lead ID</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
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
                                        $emailVal = $fieldValStr;
                                    } elseif (in_array($fieldName, ['phone_number', 'phone', 'mobile_number', 'mobile', 'whatsapp_number'])) {
                                        $phoneVal = $fieldValStr;
                                    } else {
                                        $cleanKey = ucwords(str_replace(['_', '-'], ' ', $fieldName));
                                        $customResponses[] = [
                                            'label' => $cleanKey,
                                            'val'   => $fieldValStr
                                        ];
                                    }
                                }
                            }

                            if (empty($fullName)) {
                                $fullName = 'Facebook Lead #' . substr($leadIdVal, -4);
                            }

                            $initial = strtoupper(substr($fullName, 0, 1));
                            $cleanPhone = preg_replace('/[^0-9]/', '', $phoneVal);
                            ?>
                            <tr>
                                <td>
                                    <div class="fb-lead-datetime">
                                        <strong><?php echo htmlspecialchars(explode(',', $createdTime)[0]); ?></strong>
                                        <span class="fb-lead-time"><?php echo htmlspecialchars(trim(explode(',', $createdTime)[1] ?? '')); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="v3-table-cell-title">
                                        <div class="fb-page-avatar" style="width: 32px; height: 32px; font-size: 13px; margin-right: 8px;">
                                            <?php echo htmlspecialchars($initial); ?>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($fullName); ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 3px;">
                                        <?php if (!empty($phoneVal)) { ?>
                                            <a href="tel:<?php echo htmlspecialchars($phoneVal); ?>" style="color: #0f172a; font-weight: 600; font-size: 12.5px; text-decoration: none;">
                                                <i class="fi fi-rr-phone-call" style="font-size: 11px; color: #64748b; margin-right: 4px;"></i><?php echo htmlspecialchars($phoneVal); ?>
                                            </a>
                                        <?php } ?>
                                        <?php if (!empty($emailVal)) { ?>
                                            <a href="mailto:<?php echo htmlspecialchars($emailVal); ?>" style="color: #64748b; font-size: 12px; text-decoration: none;">
                                                <i class="fi fi-rr-envelope" style="font-size: 11px; margin-right: 4px;"></i><?php echo htmlspecialchars($emailVal); ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-lead-fields-wrap">
                                        <?php if (!empty($customResponses)) { 
                                            foreach ($customResponses as $resp) { ?>
                                                <span class="fb-lead-field-chip">
                                                    <span class="chip-label"><?php echo htmlspecialchars($resp['label']); ?>:</span>
                                                    <span class="chip-val"><?php echo htmlspecialchars($resp['val']); ?></span>
                                                </span>
                                            <?php } 
                                        } else { ?>
                                            <span style="font-size: 12px; color: #94a3b8;">No additional form questions</span>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="fb-page-id-badge"><?php echo htmlspecialchars($leadIdVal); ?></span>
                                </td>
                                <td>
                                    <div class="v3-table-actions">
                                        <?php if (!empty($cleanPhone)) { ?>
                                            <a href="https://wa.me/<?php echo $cleanPhone; ?>" target="_blank" class="btn-gallery-action btn-manage-gallery" title="Chat on WhatsApp">
                                                <i class="fi fi-rr-comment-alt-middle"></i> WhatsApp
                                            </a>
                                        <?php } else { ?>
                                            <a href="/dashboard/leads/list/" class="btn-gallery-action btn-back-list" title="View All Leads">
                                                <i class="fi fi-rr-users"></i> View All Leads
                                            </a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } else { ?>
        <!-- Empty Leads State -->
        <div class="integration-empty-state">
            <i class="fi fi-rr-users empty-icon"></i>
            <h4>No Leads Received Yet</h4>
            <p>Leads captured by this form on Facebook will automatically appear here and sync to your sales pipeline.</p>
            <a href="<?php echo str_replace('&sync=true', '', ACTIVE_URL); ?>&sync=true" class="btn-gallery-action btn-reconnect">
                <i class="fi fi-rr-refresh"></i> Refresh Leads Now
            </a>
        </div>
    <?php } ?>
</div>
