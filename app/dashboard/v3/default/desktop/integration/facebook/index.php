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
    <script>
    setTimeout(function() {
        window.location.href = '/dashboard/integration/facebook/';
    }, 3000);
    </script>
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

// Fetch connected pages
$pageList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "fb_pages WHERE fb_page_status=true AND fb_page_site_id=" . SITE_ID);
$connectedPagesCount = ($pageList) ? mysqli_num_rows($pageList) : 0;
?>

<div class="package-page-wrapper fb-integration-wrapper" id="fbIntegrationApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-facebook">
                <i class="fi fi-rr-bullseye-arrow"></i>
            </div>
            <div class="package-header-titles">
                <h2>Facebook Lead Ads</h2>
                <div class="pkg-meta-row">
                    <span class="pkg-theme-pill pkg-theme-standard">Meta Integration</span>
                    <span class="pkg-duration-badge">
                        <i class="fi fi-rr-shield-check"></i> Webhook Active
                    </span>
                    <span class="pkg-duration-badge pkg-location-badge">
                        <i class="fi fi-rr-apps"></i> <?php echo $connectedPagesCount; ?> Connected Pages
                    </span>
                </div>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/integration/" class="btn-gallery-action btn-back-list" title="Back to All Integrations">
                <i class="fi fi-rr-arrow-left"></i> All Integrations
            </a>
            <?php if ($connectedPagesCount > 0) { ?>
                <a href="<?php echo str_replace('?sync=true', '', ACTIVE_URL) ?>?sync=true" class="btn-gallery-action btn-manage-gallery" title="Sync Pages from Facebook">
                    <i class="fi fi-rr-refresh"></i> Sync Pages
                </a>
                <a href="<?php echo $reconnectURL ?>" class="btn-gallery-action btn-reconnect" title="Reconnect Facebook Account">
                    <i class="fi fi-rr-link-alt"></i> Reconnect Account
                </a>
            <?php } ?>
        </div>
    </div>

    <?php if ($connectedPagesCount > 0) { ?>
        <!-- Webhook Status Banner -->
        <div class="fb-sync-banner">
            <div class="fb-sync-banner-left">
                <i class="fi fi-rr-shield-check"></i>
                <div>
                    <h4>Webhook Ingestion Active</h4>
                    <p>Real-time leadgen webhooks are subscribed. New Facebook and Instagram ad leads will flow into your pipeline instantly.</p>
                </div>
            </div>
            <div class="fb-sync-banner-right">
                <span class="integration-status-badge active"><i class="fi fi-rr-check"></i> <?php echo $connectedPagesCount; ?> Connected Pages</span>
            </div>
        </div>

        <!-- Pages Grid -->
        <div class="fb-pages-grid">
            <?php while ($pageData = mysqli_fetch_assoc($pageList)) { 
                $pageInitial = strtoupper(substr($pageData['fb_page_name'] ?? 'F', 0, 1));
            ?>
                <div class="fb-page-card">
                    <div>
                        <div class="fb-page-top">
                            <div class="integration-card-identity">
                                <div class="fb-page-avatar">
                                    <?php echo htmlspecialchars($pageInitial); ?>
                                </div>
                                <div class="fb-page-meta">
                                    <h3><?php echo htmlspecialchars($pageData['fb_page_name']); ?></h3>
                                    <div class="fb-page-details">
                                        <span class="integration-tag"><?php echo htmlspecialchars($pageData['fb_page_category'] ?: 'Business Page'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <span class="integration-status-badge active"><i class="fi fi-rr-check"></i> Subscribed</span>
                        </div>

                        <div class="fb-page-info-row">
                            <span>Page ID</span>
                            <span class="fb-page-id-badge"><?php echo htmlspecialchars($pageData['fb_page_id']); ?></span>
                        </div>
                    </div>

                    <div class="fb-page-footer">
                        <a href="/dashboard/integration/facebook/page/?id=<?php echo urlencode($pageData['fb_page_id']); ?>&name=<?php echo urlencode($pageData['fb_page_name']); ?>" class="btn-integration-primary">
                            <i class="fi fi-rr-form"></i> View Lead Forms
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <!-- Not Connected Hero State -->
        <div class="fb-connect-hero">
            <div class="fb-connect-icon">
                <i class="fi fi-rr-bullseye-arrow"></i>
            </div>
            <h3>Connect Your Facebook Advertising Account</h3>
            <p>Authorize Aleya to securely receive instant leads from your Facebook & Instagram lead ad campaigns with automated webhook syncing.</p>
            <a href="<?php echo $reconnectURL; ?>" class="btn-fb-connect">
                <i class="fi fi-rr-link-alt"></i> Connect Facebook Account
            </a>
        </div>
    <?php } ?>
</div>
