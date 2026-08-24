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
        $url = "https://graph.facebook.com/v19.0/{$pageId}/leadgen_forms?access_token={$accessToken}&limit=25";

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
        "SELECT * FROM " . TABLE_PREFIX . "fb_forms 
         WHERE fb_forms_page_id='{$pageId}' 
         AND fb_forms_site_id=" . SITE_ID
    );

    if ($sync || ($checkForms && mysqli_num_rows($checkForms) == 0)) {
        $fbForms = fetchFacebookForms($pageId, $fb_page_access_token);

        if (!empty($fbForms)) {
            foreach ($fbForms as $form) {
                $formId   = $form['id'];
                $formName = mysqli_real_escape_string($connection, $form['name']);
                $status   = (strtolower($form['status'] ?? '') === 'active') ? 1 : 0;

                mysqli_query(
                    $connection,
                    "INSERT INTO " . TABLE_PREFIX . "fb_forms 
                    (fb_forms_id, fb_forms_site_id, fb_forms_page_id, fb_forms_name, fb_forms_status) 
                    VALUES('{$formId}', '" . SITE_ID . "', '{$pageId}', '{$formName}', '{$status}')
                    ON DUPLICATE KEY UPDATE 
                        fb_forms_name='{$formName}', 
                        fb_forms_status='{$status}'"
                );
            }

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
                echo "<script>location.href='" . str_replace('&sync=true', '', ACTIVE_URL) . "';</script>";
                exit;
            }
        }
    }

    // Display all forms from DB
    $pageFormList = mysqli_query(
        $connection,
        "SELECT * FROM " . TABLE_PREFIX . "fb_forms 
         WHERE fb_forms_status=true 
         AND fb_forms_page_id='{$pageId}' 
         AND fb_forms_site_id=" . SITE_ID
    );
    $formsCount = ($pageFormList) ? mysqli_num_rows($pageFormList) : 0;
}
?>

<div class="package-page-wrapper fb-integration-wrapper">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon brand-facebook">
                <i class="fi fi-rr-form"></i>
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
                </div>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="integration/facebook/" class="btn-gallery-action btn-back-list" title="Back to Facebook Pages">
                <i class="fi fi-rr-arrow-left"></i> All Pages
            </a>
            <?php if ($pageRow) { ?>
                <a href="<?php echo str_replace('&sync=true', '', ACTIVE_URL); ?>&sync=true" class="btn-gallery-action btn-reconnect" title="Sync Lead Forms from Facebook">
                    <i class="fi fi-rr-refresh"></i> Sync Lead Forms
                </a>
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
            <a href="integration/facebook/" class="btn-fb-connect">
                <i class="fi fi-rr-arrow-left"></i> Return to Pages List
            </a>
        </div>
    <?php } elseif ($formsCount > 0) { ?>
        <!-- Forms Table Card -->
        <div class="package-table-card">
            <div class="table-responsive">
                <table class="package-data-table">
                    <thead>
                        <tr>
                            <th>Lead Form Name</th>
                            <th>Form ID</th>
                            <th>Webhook Sync</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($form = mysqli_fetch_assoc($pageFormList)) { ?>
                            <tr>
                                <td>
                                    <div class="v3-table-cell-title">
                                        <i class="fi fi-rr-form" style="color: #1877f2; margin-right: 6px;"></i>
                                        <strong><?php echo htmlspecialchars($form['fb_forms_name']); ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="fb-page-id-badge"><?php echo htmlspecialchars($form['fb_forms_id']); ?></span>
                                </td>
                                <td>
                                    <span class="integration-status-badge active">
                                        <i class="fi fi-rr-check"></i> Subscribed
                                    </span>
                                </td>
                                <td>
                                    <span class="v3-badge v3-badge-green">Active</span>
                                </td>
                                <td>
                                    <div class="v3-table-actions">
                                        <a href="/dashboard/integration/facebook/form-leads/?id=<?php echo urlencode($form['fb_forms_id']); ?>" class="btn-gallery-action btn-manage-gallery" title="View Form Leads">
                                            <i class="fi fi-rr-users"></i> View Form Leads
                                        </a>
                                    </div>
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
            <a href="<?php echo str_replace('&sync=true', '', ACTIVE_URL); ?>&sync=true" class="btn-gallery-action btn-reconnect">
                <i class="fi fi-rr-refresh"></i> Sync Forms Now
            </a>
        </div>
    <?php } ?>
</div>