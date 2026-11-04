<?php
// Workspace Rule Compliant: V3 Facebook Lead Forms Manager

$pageId = isset($id) ? $id : (isset($_GET['id']) ? $_GET['id'] : '');
$sync   = (isset($_GET['sync']) && $_GET['sync'] === 'true');

// Check valid page in database
$checkValidPageId = mysqli_query(
    $connection,
    "SELECT * FROM " . TABLE_PREFIX . "fb_pages 
     WHERE fb_page_id='" . mysqli_real_escape_string($connection, $pageId) . "' 
     AND fb_page_site_id=" . SITE_ID
);

$pageRow = ($checkValidPageId && mysqli_num_rows($checkValidPageId) > 0) ? mysqli_fetch_assoc($checkValidPageId) : null;
$pageDisplayName = $pageRow ? $pageRow['fb_page_name'] : (isset($name) ? $name : 'Facebook Page');

if ($pageRow) {
    extract($pageRow);

    function fetchFacebookForms($pageId, $accessToken)
    {
        $allForms = [];
        $url = "https://graph.facebook.com/v19.0/{$pageId}/leadgen_forms?access_token={$accessToken}&fields=id,name,status,created_time,leads_count&limit=50";

        do {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);
            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);

            if (!empty($data['data'])) {
                $allForms = array_merge($allForms, $data['data']);
            }

            $url = isset($data['paging']['next']) ? $data['paging']['next'] : null;
        } while ($url);

        return $allForms;
    }

    // Fetch forms if sync=true OR no local forms exist
    $checkForms = mysqli_query(
        $connection,
        "SELECT fb_forms_id FROM " . TABLE_PREFIX . "fb_forms 
         WHERE fb_forms_page_id='{$pageId}' 
         AND fb_forms_site_id=" . SITE_ID
    );

    if ($sync || ($checkForms && mysqli_num_rows($checkForms) == 0)) {
        $fbForms = fetchFacebookForms($pageId, $fb_page_access_token);

        // Dynamically detect available columns in fb_forms
        $formsCols = [];
        $chkColsRes = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_forms");
        if ($chkColsRes) {
            while ($cRow = mysqli_fetch_assoc($chkColsRes)) {
                $formsCols[$cRow['Field']] = true;
            }
        }
        // Attempt safe auto-migration for missing columns if permissions allow
        if (empty($formsCols['fb_forms_leads_count'])) {
            @mysqli_query($connection, "ALTER TABLE " . TABLE_PREFIX . "fb_forms ADD COLUMN fb_forms_leads_count INT DEFAULT 0");
        }
        if (empty($formsCols['fb_forms_last_sync'])) {
            @mysqli_query($connection, "ALTER TABLE " . TABLE_PREFIX . "fb_forms ADD COLUMN fb_forms_last_sync DATETIME DEFAULT NULL");
        }
        if (empty($formsCols['fb_forms_created_at'])) {
            @mysqli_query($connection, "ALTER TABLE " . TABLE_PREFIX . "fb_forms ADD COLUMN fb_forms_created_at DATETIME DEFAULT NULL");
        }
        // Re-read columns
        $chkColsRes2 = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_forms");
        if ($chkColsRes2) {
            while ($cRow = mysqli_fetch_assoc($chkColsRes2)) {
                $formsCols[$cRow['Field']] = true;
            }
        }

        // Dynamically detect available columns in fb_pages
        $pageCols = [];
        $chkPageColsRes = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_pages");
        if ($chkPageColsRes) {
            while ($pRow = mysqli_fetch_assoc($chkPageColsRes)) {
                $pageCols[$pRow['Field']] = true;
            }
        }
        if (empty($pageCols['fb_page_last_sync'])) {
            @mysqli_query($connection, "ALTER TABLE " . TABLE_PREFIX . "fb_pages ADD COLUMN fb_page_last_sync DATETIME DEFAULT NULL");
        }
        $chkPageColsRes2 = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_pages");
        if ($chkPageColsRes2) {
            while ($pRow = mysqli_fetch_assoc($chkPageColsRes2)) {
                $pageCols[$pRow['Field']] = true;
            }
        }

        if (!empty($fbForms)) {
            foreach ($fbForms as $form) {
                $formId     = mysqli_real_escape_string($connection, $form['id']);
                $formName   = mysqli_real_escape_string($connection, $form['name']);
                $status     = (strtolower($form['status'] ?? '') === 'active') ? 1 : 0;
                $leadsCount = (int)($form['leads_count'] ?? 0);
                $createdAt  = !empty($form['created_time']) ? date('Y-m-d H:i:s', strtotime($form['created_time'])) : date('Y-m-d H:i:s');

                $insertFields = ['fb_forms_id', 'fb_forms_site_id', 'fb_forms_page_id', 'fb_forms_name', 'fb_forms_status'];
                $insertValues = ["'{$formId}'", "'" . SITE_ID . "'", "'{$pageId}'", "'{$formName}'", "'{$status}'"];
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

                mysqli_query(
                    $connection,
                    "INSERT INTO " . TABLE_PREFIX . "fb_forms (" . implode(', ', $insertFields) . ") 
                    VALUES (" . implode(', ', $insertValues) . ") 
                    ON DUPLICATE KEY UPDATE " . implode(', ', $updateFields)
                );
            }

            // Update page last sync timestamp if column exists
            if (!empty($pageCols['fb_page_last_sync'])) {
                mysqli_query(
                    $connection,
                    "UPDATE " . TABLE_PREFIX . "fb_pages 
                     SET fb_page_last_sync = NOW() 
                     WHERE fb_page_id='" . mysqli_real_escape_string($connection, $pageId) . "' 
                     AND fb_page_site_id=" . SITE_ID
                );
            }
            $fb_page_last_sync = date('Y-m-d H:i:s');

            // Subscribe to webhook
            $webhookURL = "https://app.aleya.in/webhook/facebook/";
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://graph.facebook.com/{$fb_page_id}/subscribed_apps?subscribed_fields=leadgen&access_token={$fb_page_access_token}&link={$webhookURL}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
            ]);
            $webhookResponse = curl_exec($curl);
            curl_close($curl);

            if ($sync) {
                $cleanActiveUrl = str_replace(['&sync=true', '?sync=true'], '', ACTIVE_URL);
                echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($cleanActiveUrl) . '">';
                exit;
            }
        }
    }

    // Ensure we check formsCols if sync block didn't run
    if (empty($formsCols)) {
        $formsCols = [];
        $chkColsRes = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_forms");
        if ($chkColsRes) {
            while ($cRow = mysqli_fetch_assoc($chkColsRes)) {
                $formsCols[$cRow['Field']] = true;
            }
        }
    }

    // Load lead counts lookup from CRM enquiry table
    $crmCountsLookup = [];
    $crmGroupRes = mysqli_query(
        $connection,
        "SELECT enquiry_subject, COUNT(*) as c 
         FROM " . TABLE_PREFIX . "enquiry 
         WHERE enquiry_site_id=" . SITE_ID . " 
           AND (enquiry_source='facebook' OR enquiry_medium='lead_form') 
         GROUP BY enquiry_subject"
    );
    if ($crmGroupRes) {
        while ($cg = mysqli_fetch_assoc($crmGroupRes)) {
            $crmCountsLookup[$cg['enquiry_subject']] = (int)$cg['c'];
        }
    }

    // Construct safe ORDER BY clause based on existing columns
    $orderParts = [];
    if (!empty($formsCols['fb_forms_last_sync'])) {
        $orderParts[] = "fb_forms_last_sync DESC";
    }
    if (!empty($formsCols['fb_forms_created_at'])) {
        $orderParts[] = "fb_forms_created_at DESC";
    }
    $orderParts[] = "fb_forms_id DESC";
    $orderClause = implode(', ', $orderParts);

    // Display all forms from DB
    $pageFormList = mysqli_query(
        $connection,
        "SELECT * FROM " . TABLE_PREFIX . "fb_forms 
         WHERE fb_forms_status=true 
         AND fb_forms_page_id='{$pageId}' 
         AND fb_forms_site_id=" . SITE_ID . "
         ORDER BY {$orderClause}"
    );
    $formsCount = ($pageFormList) ? mysqli_num_rows($pageFormList) : 0;
}

// Format Last Synced time for Header
$lastSyncedHeader = 'Not yet synced';
if ($pageRow && !empty($pageRow['fb_page_last_sync'])) {
    $lastSyncedHeader = date('d M Y, h:i A', strtotime($pageRow['fb_page_last_sync']));
}
?>

<div class="package-page-wrapper fb-integration-wrapper" id="fbFormsPageApp" data-page-id="<?php echo htmlspecialchars($pageId); ?>">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="integration-brand-logo brand-facebook">
                <img src="/app/dashboard/v3/default/desktop/assets/images/integration/facebook.svg" alt="Facebook">
            </div>
            <div class="package-header-titles">
                <h2><?php echo htmlspecialchars($pageDisplayName); ?></h2>
                <div class="pkg-meta-row">
                    <span class="pkg-theme-pill pkg-theme-standard">Facebook Page</span>
                    <span class="pkg-duration-badge">
                        <i class="fi fi-rr-shield-check"></i> Webhook Synced
                    </span>
                    <span class="pkg-duration-badge pkg-location-badge">
                        <i class="fi fi-rr-form"></i> <?php echo $formsCount ?? 0; ?> Lead Forms
                    </span>
                    <!-- Header Last Synced Time -->
                    <span class="pkg-duration-badge" id="fbPageLastSyncBadge" title="Last time forms were synchronized from Facebook">
                        <i class="fi fi-rr-refresh"></i> Last Synced: <?php echo htmlspecialchars($lastSyncedHeader); ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/integration/facebook/" class="v3-btn-secondary" title="Back to Facebook Pages">
                <i class="fi fi-rr-arrow-left"></i> <span>All Pages</span>
            </a>
            <?php if ($pageRow) { ?>
                <button type="button" class="v3-btn-primary" id="btnSyncFbForms" data-page-id="<?php echo htmlspecialchars($pageId); ?>" title="Sync Lead Forms from Facebook">
                    <i class="fi fi-rr-refresh"></i> <span>Sync Lead Forms</span>
                </button>
            <?php } ?>
        </div>
    </div>

    <?php if (!$pageRow) { ?>
        <div class="fb-connect-hero">
            <div class="fb-connect-icon">
                <i class="fi fi-rr-exclamation"></i>
            </div>
            <h3>Facebook Page Not Found</h3>
            <p>The requested page identifier could not be verified in your workspace or access is restricted.</p>
            <a href="/dashboard/integration/facebook/" class="btn-fb-connect">
                <i class="fi fi-rr-arrow-left"></i> Return to Pages List
            </a>
        </div>
    <?php } elseif ($formsCount > 0) { ?>
        <!-- Modern Forms Table Card -->
        <div class="v3-table-card">
            <div class="v3-table-card-header">
                <div class="v3-table-card-header-left">
                    <i class="fi fi-rr-form v3-form-card-icon color-blue"></i>
                    <h3>Subscribed Lead Generation Forms</h3>
                    <span class="v3-badge v3-badge-blue" id="formsCountBadge"><?php echo $formsCount; ?> Active</span>
                </div>
                <div>
                    <div class="integration-search-box m-0">
                        <i class="fi fi-rr-search"></i>
                        <input type="text" id="fbFormSearchInput" placeholder="Search forms by name, id..." class="integration-search-input v3-search-input-compact">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="v3-data-table" id="fbFormsTable">
                    <thead>
                        <tr>
                            <th>Lead Form</th>
                            <th>Form ID</th>
                            <th>First Time Sync</th>
                            <th>Total Leads</th>
                            <th>Webhook Integration</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="fbFormsTableBody">
                        <?php while ($form = mysqli_fetch_assoc($pageFormList)) { 
                            $formName = $form['fb_forms_name'];
                            $totalFormLeads = max((int)($form['fb_forms_leads_count'] ?? 0), (int)($crmCountsLookup[$formName] ?? 0));
                            
                            $firstSyncTime = 'Not recorded';
                            if (!empty($form['fb_forms_created_at'])) {
                                $firstSyncTime = date('d M Y, h:i A', strtotime($form['fb_forms_created_at']));
                            }
                        ?>
                            <tr class="fb-form-row">
                                <td>
                                    <div class="v3-table-form-cell">
                                        <div class="v3-table-form-icon">
                                            <i class="fi fi-rr-form"></i>
                                        </div>
                                        <div class="v3-table-form-info">
                                            <h4><?php echo htmlspecialchars($formName); ?></h4>
                                            <span>Instant Lead Capture</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="v3-code-pill"><?php echo htmlspecialchars($form['fb_forms_id']); ?></span>
                                </td>
                                <td>
                                    <div class="fb-lead-date-box" title="When this form was first synchronized to CRM">
                                        <span class="lead-date"><?php echo htmlspecialchars(explode(',', $firstSyncTime)[0]); ?></span>
                                        <span class="lead-time"><i class="fi fi-rr-clock-three"></i> <?php echo htmlspecialchars(trim(explode(',', $firstSyncTime)[1] ?? '')); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="v3-badge v3-badge-blue" title="Total captured submissions">
                                        <i class="fi fi-rr-users"></i> <?php echo number_format($totalFormLeads); ?> Leads
                                    </span>
                                </td>
                                <td>
                                    <span class="v3-status-badge status-active">
                                        <span class="status-pulse-dot"></span> Subscribed
                                    </span>
                                </td>
                                <td>
                                    <span class="v3-status-badge <?php echo (!empty($form['fb_forms_status']) ? 'status-active' : 'status-inactive'); ?>">
                                        <span class="status-pulse-dot"></span> <?php echo (!empty($form['fb_forms_status']) ? 'Active' : 'Inactive'); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="/dashboard/integration/facebook/form-leads/?id=<?php echo urlencode($form['fb_forms_id']); ?>" class="v3-btn-table-action" title="View Form Leads">
                                        <i class="fi fi-rr-users"></i> View Form Leads
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } else { ?>
        <!-- Empty Forms State -->
        <div class="integration-empty-state">
            <i class="fi fi-rr-form empty-icon"></i>
            <h4>No Lead Forms Found</h4>
            <p>No active lead generation forms were found on this Facebook page. Create a form inside Facebook Ads Manager and sync.</p>
            <button type="button" class="v3-btn-primary" id="btnSyncFbFormsEmpty" data-page-id="<?php echo htmlspecialchars($pageId); ?>">
                <i class="fi fi-rr-refresh"></i> Sync Forms Now
            </button>
        </div>
    <?php } ?>
</div>
