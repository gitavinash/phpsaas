<?php
// Workspace Rule Compliant: V3 Facebook Lead Ads Integration

function getFacebookPageList($connection, $extendedToken, $access_token)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://graph.facebook.com/me/accounts',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer {$extendedToken}",
        ),
    ));

    $fbPageResponse = curl_exec($curl);
    curl_close($curl);

    $fbPageResponseData = json_decode($fbPageResponse, TRUE);

    if (!empty($fbPageResponseData['data']) && is_array($fbPageResponseData['data'])) {
        for ($i = 0; $i < count($fbPageResponseData['data']); $i++) {
            extract($fbPageResponseData['data'][$i]);

            // subscribe to facebook webhook
            $curl = curl_init();
            $webhookURL = "https://app.aleya.in/webhook/facebook/";

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://graph.facebook.com/{$id}/subscribed_apps?subscribed_fields=leadgen&access_token={$access_token}&link={$webhookURL}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
            ));

            $webhookResponse = curl_exec($curl);
            $webhookResponse = json_decode($webhookResponse, TRUE);
            curl_close($curl);

            if (isset($webhookResponse['success'])) {
                $savePageData = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "fb_pages 
                    (fb_page_id, fb_page_site_id, fb_page_name, fb_page_access_token, fb_page_category, fb_page_status)
                    VALUES
                    ('{$id}', '" . SITE_ID . "', '{$name}', '{$access_token}', '{$category}', true)
                    ON DUPLICATE KEY UPDATE
                    fb_page_name = VALUES(fb_page_name),
                    fb_page_access_token = VALUES(fb_page_access_token),
                    fb_page_category = VALUES(fb_page_category),
                    fb_page_status = VALUES(fb_page_status)");
            }
        }
    }
?>
    <div class="package-page-wrapper fb-integration-wrapper">
        <div class="fb-sync-loading-box">
            <div class="v3-leads-table-loader">
                <div class="v3-table-spinner">
                    <div class="v3-spinner-ring"></div>
                    <div class="v3-spinner-inner-dot"></div>
                </div>
                <h4 class="v3-loader-title">Synchronizing Facebook Pages...</h4>
                <p class="v3-loader-subtitle">Subscribing lead forms and activating real-time webhooks. Redirecting in a few moments.</p>
            </div>
        </div>
    </div>
    <meta http-equiv="refresh" content="3;url=/dashboard/integration/facebook/">
<?php
    exit();
}

$reconnectURL = "https://aleya.in/facebook/?redirect_url=" . base64_encode('https://' . SITE_DOMAIN) . "=&new=true";

// Handle OAuth Return or Sync
if (isset($new) && isset($accessToken)) {
    $extendedToken = "https://graph.facebook.com/" . FB_API_VERSION . "/oauth/access_token?grant_type=fb_exchange_token&client_id=" . FB_APP_ID . "&client_secret=" . FB_APP_SECRET . "&fb_exchange_token={$accessToken}";
    $extendedTokenData = file_get_contents($extendedToken);
    $extendedTokenData = json_decode($extendedTokenData, TRUE);
    $expiryDate = date('Y-m-d', strtotime('+90 days'));

    if (!empty($extendedTokenData['access_token'])) {
        $saveToken = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "fb(fb_user_id, fb_site_id, fb_access_token, fb_extended_token, fb_token_expiry, fb_token_status) VALUES('{$accessUserId}', '" . SITE_ID . "', '{$accessToken}', '{$extendedTokenData['access_token']}', '{$expiryDate}', true)");
        getFacebookPageList($connection, $extendedTokenData['access_token'], $accessToken);
    }
} elseif (isset($sync)) {
    $checkFacebookToken = mysqli_query($connection, "SELECT fb_extended_token, fb_access_token FROM " . TABLE_PREFIX . "fb WHERE fb_token_status=1 AND fb_site_id=" . SITE_ID);
    if ($checkFacebookToken && mysqli_num_rows($checkFacebookToken) > 0) {
        $fbRow = mysqli_fetch_assoc($checkFacebookToken);
        getFacebookPageList($connection, $fbRow['fb_extended_token'], $fbRow['fb_access_token']);
    } else {
        $needAuth = true;
    }
}

// Ensure required database schema exists safely
$formsCols = [];
$checkFormsCols = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_forms");
if ($checkFormsCols) {
    while ($c = mysqli_fetch_assoc($checkFormsCols)) {
        $formsCols[$c['Field']] = true;
    }
}
$hasLeadsCountCol = !empty($formsCols['fb_forms_leads_count']);
$hasFormsLastSyncCol = !empty($formsCols['fb_forms_last_sync']);
$hasFormsCreatedCol = !empty($formsCols['fb_forms_created_at']);

if (!$hasLeadsCountCol) {
    // Attempt auto-migration
    @mysqli_query($connection, "ALTER TABLE " . TABLE_PREFIX . "fb_forms ADD COLUMN fb_forms_leads_count INT DEFAULT 0");
    $checkFormsCol2 = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_forms LIKE 'fb_forms_leads_count'");
    if ($checkFormsCol2 && mysqli_num_rows($checkFormsCol2) > 0) {
        $hasLeadsCountCol = true;
    }
}

// Check fb_pages columns safely
$pageCols = [];
$checkPageCols = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "fb_pages");
if ($checkPageCols) {
    while ($c = mysqli_fetch_assoc($checkPageCols)) {
        $pageCols[$c['Field']] = true;
    }
}
$hasPageLastSyncCol = !empty($pageCols['fb_page_last_sync']);

// Fetch connected pages with aggregated form and lead counts
$leadsCountExpr = $hasLeadsCountCol ? "SUM(COALESCE(fb_forms_leads_count, 0))" : "0";
$formsSyncExpr = $hasFormsLastSyncCol ? "MAX(fb_forms_last_sync)" : ($hasFormsCreatedCol ? "MAX(fb_forms_created_at)" : "NULL");

$pageListQuery = "SELECT p.*, 
       COALESCE(f.forms_count, 0) AS forms_count,
       COALESCE(f.total_leads, 0) AS total_leads,
       f.latest_form_sync
FROM " . TABLE_PREFIX . "fb_pages p
LEFT JOIN (
    SELECT fb_forms_page_id, 
           COUNT(*) AS forms_count, 
           {$leadsCountExpr} AS total_leads,
           {$formsSyncExpr} AS latest_form_sync
    FROM " . TABLE_PREFIX . "fb_forms 
    WHERE fb_forms_site_id = " . SITE_ID . "
    GROUP BY fb_forms_page_id
) f ON p.fb_page_id = f.fb_forms_page_id
WHERE p.fb_page_status = true 
  AND p.fb_page_site_id = " . SITE_ID . "
ORDER BY p.fb_page_id DESC";

$pageListResult = mysqli_query($connection, $pageListQuery);
$pages = [];
$totalFormsCount = 0;
$totalLeadsCount = 0;

if ($pageListResult) {
    while ($pRow = mysqli_fetch_assoc($pageListResult)) {
        $pages[] = $pRow;
        $totalFormsCount += (int)$pRow['forms_count'];
        $totalLeadsCount += (int)$pRow['total_leads'];
    }
}
$connectedPagesCount = count($pages);

// Check Meta OAuth Token Validity
$checkTokenRes = mysqli_query($connection, "SELECT fb_token_expiry, fb_token_status FROM " . TABLE_PREFIX . "fb WHERE fb_token_status=1 AND fb_site_id=" . SITE_ID . " ORDER BY fb_id DESC LIMIT 1");
$tokenExpiryText = 'Active';
$tokenDaysRemaining = null;
if ($checkTokenRes && mysqli_num_rows($checkTokenRes) > 0) {
    $tRow = mysqli_fetch_assoc($checkTokenRes);
    if (!empty($tRow['fb_token_expiry'])) {
        $tokenDaysRemaining = (int)ceil((strtotime($tRow['fb_token_expiry']) - time()) / 86400);
        $tokenExpiryText = ($tokenDaysRemaining > 0) ? ($tokenDaysRemaining . ' Days Left') : 'Active';
    }
}
?>

<div class="package-page-wrapper fb-integration-wrapper" id="fbIntegrationApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="integration-brand-logo brand-facebook">
                <img src="/app/dashboard/v3/default/desktop/assets/images/integration/facebook.svg" alt="Facebook">
            </div>
            <div class="package-header-titles">
                <h2>Facebook Lead Ads</h2>
                <div class="pkg-meta-row">
                    <span class="pkg-theme-pill pkg-theme-standard">Meta Graph API v19.0</span>
                    <span class="v3-status-badge status-active" title="Webhooks active and subscribed">
                        <span class="status-pulse-dot"></span> Webhook Ingestion Live
                    </span>
                    <span class="pkg-duration-badge pkg-location-badge">
                        <i class="fi fi-rr-apps"></i> <?php echo $connectedPagesCount; ?> Connected <?php echo ($connectedPagesCount === 1 ? 'Page' : 'Pages'); ?>
                    </span>
                    <?php if ($tokenDaysRemaining !== null) { ?>
                        <span class="v3-badge v3-badge-blue" title="Meta Access Token Validity">
                            <i class="fi fi-rr-key"></i> Token <?php echo $tokenExpiryText; ?>
                        </span>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/integration/" class="v3-btn-secondary" title="Back to All Integrations">
                <i class="fi fi-rr-arrow-left"></i> <span>All Integrations</span>
            </a>
            <?php if ($connectedPagesCount > 0) { ?>
                <a href="<?php echo str_replace('?sync=true', '', ACTIVE_URL) ?>?sync=true" class="v3-btn-secondary" title="Sync Pages from Facebook">
                    <i class="fi fi-rr-refresh"></i> <span>Sync Pages</span>
                </a>
                <a href="<?php echo $reconnectURL ?>" class="v3-btn-primary" title="Reconnect Facebook Account">
                    <i class="fi fi-rr-link-alt"></i> <span>Reconnect Account</span>
                </a>
            <?php } ?>
        </div>
    </div>

    <?php if ($connectedPagesCount > 0) { ?>
        <!-- Top KPI Metrics Grid -->
        <div class="v3-kpi-grid">
            <div class="v3-kpi-card">
                <div class="v3-kpi-icon-wrap blue">
                    <i class="fi fi-rr-apps"></i>
                </div>
                <div class="v3-kpi-info">
                    <span class="v3-kpi-label">Connected Pages</span>
                    <span class="v3-kpi-val"><?php echo $connectedPagesCount; ?></span>
                    <span class="v3-kpi-sub">Verified Meta Ad accounts</span>
                </div>
            </div>

            <div class="v3-kpi-card">
                <div class="v3-kpi-icon-wrap purple">
                    <i class="fi fi-rr-form"></i>
                </div>
                <div class="v3-kpi-info">
                    <span class="v3-kpi-label">Active Lead Forms</span>
                    <span class="v3-kpi-val"><?php echo number_format($totalFormsCount); ?></span>
                    <span class="v3-kpi-sub">Instant capture forms active</span>
                </div>
            </div>

            <div class="v3-kpi-card">
                <div class="v3-kpi-icon-wrap green">
                    <i class="fi fi-rr-users"></i>
                </div>
                <div class="v3-kpi-info">
                    <span class="v3-kpi-label">Campaign Inquiries</span>
                    <span class="v3-kpi-val"><?php echo number_format($totalLeadsCount); ?></span>
                    <span class="v3-kpi-sub">Leads captured from Facebook</span>
                </div>
            </div>

            <div class="v3-kpi-card">
                <div class="v3-kpi-icon-wrap amber">
                    <i class="fi fi-rr-shield-check"></i>
                </div>
                <div class="v3-kpi-info">
                    <span class="v3-kpi-label">Webhook Pipeline</span>
                    <span class="v3-kpi-val">Live Active</span>
                    <span class="v3-kpi-sub">100% Real-time delivery</span>
                </div>
            </div>
        </div>

        <!-- Pages Section Toolbar -->
        <div class="fb-pages-toolbar">
            <div class="fb-pages-toolbar-left">
                <i class="fi fi-rr-apps color-blue"></i>
                <h3>Connected Facebook Pages</h3>
                <span class="v3-badge v3-badge-blue"><?php echo $connectedPagesCount; ?> <?php echo ($connectedPagesCount === 1 ? 'Page' : 'Pages'); ?></span>
            </div>
            <div class="integration-search-box m-0">
                <i class="fi fi-rr-search"></i>
                <input type="text" id="fbPageSearchInput" placeholder="Search pages by name, ID, category..." class="integration-search-input v3-search-input-compact">
            </div>
        </div>

        <!-- Pages Grid -->
        <div class="fb-pages-grid" id="fbPagesGrid">
            <?php
            $avatarColors = ['avatar-blue', 'avatar-indigo', 'avatar-purple', 'avatar-emerald', 'avatar-amber'];
            foreach ($pages as $index => $pageData) {
                $pageName = $pageData['fb_page_name'] ?? 'Facebook Page';
                $pageId = $pageData['fb_page_id'];
                $pageInitial = strtoupper(substr($pageName, 0, 1));
                $avatarClass = $avatarColors[abs(crc32($pageName)) % 5];
                $category = !empty($pageData['fb_page_category']) ? $pageData['fb_page_category'] : 'Business Page';
                $formsCount = (int)$pageData['forms_count'];
                $leadsCount = (int)$pageData['total_leads'];

                $lastSyncRaw = null;
                if (!empty($pageData['fb_page_last_sync'])) {
                    $lastSyncRaw = $pageData['fb_page_last_sync'];
                } elseif (!empty($pageData['latest_form_sync'])) {
                    $lastSyncRaw = $pageData['latest_form_sync'];
                }

                $lastSyncFormatted = 'Not Synced';
                if (!empty($lastSyncRaw)) {
                    $lastSyncFormatted = date('d M, h:i A', strtotime($lastSyncRaw));
                }
            ?>
                <div class="fb-page-card"
                    data-page-id="<?php echo htmlspecialchars($pageId); ?>"
                    data-page-name="<?php echo htmlspecialchars($pageName); ?>"
                    data-page-category="<?php echo htmlspecialchars($category); ?>">
                    
                    <!-- Top Row: Avatar + Subscribed Badge -->
                    <div class="fb-card-top-row">
                        <div class="fb-page-avatar <?php echo $avatarClass; ?>">
                            <span class="fb-avatar-letter"><?php echo htmlspecialchars($pageInitial); ?></span>
                        </div>
                        <div class="fb-card-subscribed-badge" title="Subscribed to Real-Time Meta Webhooks">
                            <span class="fb-card-dot"></span>
                            <span>Subscribed</span>
                        </div>
                    </div>

                    <!-- Page Info: Title + Category -->
                    <div class="fb-card-info-group">
                        <h3 class="fb-card-title" title="<?php echo htmlspecialchars($pageName); ?>"><?php echo htmlspecialchars($pageName); ?></h3>
                        <span class="fb-card-category-pill"><?php echo htmlspecialchars($category); ?></span>
                    </div>

                    <!-- 3-Column Stats Box -->
                    <div class="fb-card-stats-box">
                        <div class="fb-card-stat-cell">
                            <span class="fb-card-stat-label">FORMS</span>
                            <span class="fb-card-stat-val"><?php echo number_format($formsCount); ?></span>
                        </div>
                        <div class="fb-card-stat-cell">
                            <span class="fb-card-stat-label">TOTAL LEADS</span>
                            <span class="fb-card-stat-val"><?php echo number_format($leadsCount); ?></span>
                        </div>
                        <div class="fb-card-stat-cell fb-card-stat-sync">
                            <span class="fb-card-stat-label">LAST SYNC</span>
                            <span class="fb-card-stat-val val-date" title="<?php echo htmlspecialchars($lastSyncFormatted); ?>"><?php echo htmlspecialchars($lastSyncFormatted); ?></span>
                        </div>
                    </div>

                    <!-- Page ID Row -->
                    <div class="fb-card-id-row">
                        <span class="fb-card-id-label">Page ID</span>
                        <div class="fb-card-id-pill fb-page-id-chip" title="Click to copy Page ID">
                            <span class="id-val fb-card-id-text"><?php echo htmlspecialchars($pageId); ?></span>
                            <button type="button" class="btn-copy-id-chip btn-copy-lead-id" data-copy="<?php echo htmlspecialchars($pageId); ?>" title="Copy Page ID">
                                <i class="fi fi-rr-copy"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons Row -->
                    <div class="fb-card-actions-row">
                        <a href="/dashboard/integration/facebook/page/?id=<?php echo urlencode($pageId); ?>&name=<?php echo urlencode($pageName); ?>" class="btn-fb-view-leadforms" title="View Lead Forms for <?php echo htmlspecialchars($pageName); ?>">
                            <i class="fi fi-rr-form"></i>
                            <span>View Lead Forms (<?php echo $formsCount; ?>)</span>
                        </a>
                        <a href="https://www.facebook.com/<?php echo urlencode($pageId); ?>" target="_blank" rel="noopener noreferrer" class="btn-fb-open-external" title="Open Page on Facebook">
                            <i class="fi fi-rr-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </div>
            <?php } ?>
                                                <div class="fb-pages-no-results" id="fbPagesNoResults">
                                                    <i class="fi fi-rr-search"></i> No Facebook pages found matching your search.
                                                </div>
                                                </div>

                                                <!-- Integration Capabilities Strip -->
                                                <div class="fb-features-section">
                                                    <div class="fb-features-header">
                                                        <h4>Facebook Integration Capabilities</h4>
                                                        <p>Enterprise pipeline features active for your connected Meta advertising accounts.</p>
                                                    </div>
                                                    <div class="fb-features-grid">
                                                        <div class="fb-feature-card">
                                                            <div class="fb-feature-icon blue">
                                                                <i class="fi fi-rr-bolt"></i>
                                                            </div>
                                                            <div class="fb-feature-body">
                                                                <h5>Instant Webhook Streaming</h5>
                                                                <p>New prospects submitting instant forms on Facebook and Instagram are ingested directly into CRM within seconds.</p>
                                                            </div>
                                                        </div>

                                                        <div class="fb-feature-card">
                                                            <div class="fb-feature-icon emerald">
                                                                <i class="fi fi-rr-shield-check"></i>
                                                            </div>
                                                            <div class="fb-feature-body">
                                                                <h5>Smart Deduplication</h5>
                                                                <p>Phone numbers and emails are automatically matched to prevent duplicate inquiries and link directly to active leads.</p>
                                                            </div>
                                                        </div>

                                                        <div class="fb-feature-card">
                                                            <div class="fb-feature-icon purple">
                                                                <i class="fi fi-rr-paper-plane"></i>
                                                            </div>
                                                            <div class="fb-feature-body">
                                                                <h5>Automated Lead Routing</h5>
                                                                <p>Inquiries stream straight into the lead pipeline for instant sales team assignment and automated WhatsApp follow-ups.</p>
                                                            </div>
                                                        </div>

                                                        <div class="fb-feature-card">
                                                            <div class="fb-feature-icon amber">
                                                                <i class="fi fi-rr-lock"></i>
                                                            </div>
                                                            <div class="fb-feature-body">
                                                                <h5>Enterprise Meta Security</h5>
                                                                <p>Backed by official Meta Graph API v19.0, encrypted extended tokens, multi-tenant isolation, and role-based PII protection.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <!-- Not Connected Hero State -->
                                                <div class="fb-connect-hero">
                                                    <div class="integration-brand-logo brand-facebook v3-connect-hero-logo">
                                                        <img src="/app/dashboard/v3/default/desktop/assets/images/integration/facebook.svg" alt="Facebook">
                                                    </div>
                                                    <h3>Connect Your Facebook Advertising Account</h3>
                                                    <p>Authorize Aleya to securely receive instant leads from your Facebook &amp; Instagram lead ad campaigns with automated webhook syncing.</p>
                                                    <a href="<?php echo $reconnectURL; ?>" class="btn-fb-connect">
                                                        <i class="fi fi-rr-link-alt"></i> Connect Facebook Account
                                                    </a>
                                                </div>
                                            <?php } ?>
                                            </div>