<?php
function getFacebookLeads($connection, $formId, $pageAccessToken, $limit = 100)
{
    $allLeads   = [];
    $after      = null;
    $fetched    = 0;
    $perRequest = min($limit, 100);
    do {
        $params = [
            'access_token' => $pageAccessToken,
            'limit'        => $perRequest,
            'fields'       => 'field_data,created_time'
        ];
        if ($after) $params['after'] = $after;

        $url = "https://graph.facebook.com/v19.0/{$formId}/leads?" . http_build_query($params);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log("cURL error: " . curl_error($ch));
            curl_close($ch);
            break;
        }
        curl_close($ch);

        $data = json_decode($response, true);
        error_log("Requested URL: " . $url);
        error_log("Fetched items in this call: " . (isset($data['data']) ? count($data['data']) : 0));

        if (!empty($data['data'])) {
            foreach ($data['data'] as $lead) {
                if ($fetched >= $limit) break 2; // stop outer loop too
                $allLeads[] = $lead;
                $fetched++;
            }
        }

        // set next cursor (if present)
        $after = $data['paging']['cursors']['after'] ?? null;
    } while ($after && $fetched < $limit);

    return $allLeads;
}

if ($checkFormId = mysqli_query($connection, "SELECT fb_page_access_token, fb_forms_name FROM " . TABLE_PREFIX . "fb_forms INNER JOIN " . TABLE_PREFIX . "fb_pages ON fb_forms_page_id=fb_page_id AND fb_page_site_id=" . SITE_ID . " WHERE fb_forms_id='{$id}' AND fb_forms_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkFormId) != 0) {
        extract(mysqli_fetch_assoc($checkFormId));

        $leads = getFacebookLeads($connection, $id, $fb_page_access_token);

        echo "<pre>";
        print_r($leads);
        echo "</pre>";
        exit();
        $showTemplate = true;
    } else {
        echo "Invalid id received";
    }
}

?>
<h2 class="heading">
    <?php echo htmlspecialchars(ucwords($fb_forms_name)); ?> forms list
    <span>All Facebook forms assigned to <?php echo htmlspecialchars($fb_forms_name); ?> page</span>
</h2>

<div class="main-box">
    <div class="shortcut">
        <a href="<?php echo str_replace('&sync=true', '', ACTIVE_URL) ?>&sync=true">
            <i class="fi fi-rr-refresh"></i>
        </a>
    </div>
    <?php

    if (isset($showTemplate)) { ?>
    <section class="fb_page_list">
        <?php
            $pageId = $id ?? null;
            $sync   = (isset($_GET['sync']) && $_GET['sync'] === 'true');

            if (!$pageId) {
                echo "<div class='alert error'>Invalid Page ID.</div>";
                exit;
            }

            // 1. Validate page in DB
            $checkPage = mysqli_query(
                $connection,
                "SELECT * FROM " . TABLE_PREFIX . "fb_pages 
             WHERE fb_page_id='" . mysqli_real_escape_string($connection, $pageId) . "' 
             AND fb_page_site_id=" . SITE_ID
            );

            if (!$checkPage || mysqli_num_rows($checkPage) === 0) {
                echo "<div class='alert error'>Page not found or not assigned to this site.</div>";
                exit;
            }

            $pageRow = mysqli_fetch_assoc($checkPage);
            $fb_page_access_token = $pageRow['fb_page_access_token'];

            /**
             * Fetch Facebook forms with pagination
             */
            function fetchFacebookForms($pageId, $accessToken)
            {
                $allForms = [];
                $url = "https://graph.facebook.com/v19.0/{$pageId}/leadgen_forms?access_token={$accessToken}&limit=25";

                while ($url) {
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT => 20,
                    ]);
                    $response = curl_exec($ch);
                    $error    = curl_error($ch);
                    curl_close($ch);

                    if ($error) {
                        error_log("cURL Error while fetching forms: $error");
                        break;
                    }

                    $data = json_decode($response, true);
                    if (isset($data['data']) && is_array($data['data'])) {
                        $allForms = array_merge($allForms, $data['data']);
                    }

                    $url = $data['paging']['next'] ?? null;
                }
                return $allForms;
            }

            // 2. Check DB forms
            $dbForms = mysqli_query(
                $connection,
                "SELECT * FROM " . TABLE_PREFIX . "fb_forms 
             WHERE fb_forms_page_id='" . mysqli_real_escape_string($connection, $pageId) . "' 
             AND fb_forms_site_id=" . SITE_ID
            );

            if ($sync || mysqli_num_rows($dbForms) === 0) {
                $fbForms = fetchFacebookForms($pageId, $fb_page_access_token);

                if (!empty($fbForms)) {
                    foreach ($fbForms as $form) {
                        $formId   = mysqli_real_escape_string($connection, $form['id']);
                        $formName = mysqli_real_escape_string($connection, $form['name']);
                        $status   = (strtolower($form['status']) === 'active') ? 1 : 0;

                        $query = "
                        INSERT INTO " . TABLE_PREFIX . "fb_forms 
                            (fb_forms_id, fb_forms_site_id, fb_forms_page_id, fb_forms_name, fb_forms_status) 
                        VALUES 
                            ('{$formId}', '" . SITE_ID . "', '{$pageId}', '{$formName}', '{$status}')
                        ON DUPLICATE KEY UPDATE 
                            fb_forms_name='{$formName}', 
                            fb_forms_status='{$status}'
                    ";
                        mysqli_query($connection, $query);
                    }

                    // Subscribe page to leadgen webhook
                    $webhookURL = "https://app.aleya.in/webhook/facebook/";
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => "https://graph.facebook.com/{$pageId}/subscribed_apps?subscribed_fields=leadgen&access_token={$fb_page_access_token}&link={$webhookURL}",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                    ]);
                    curl_exec($ch);
                    curl_close($ch);

                    // Reload to clean ?sync=true
                    if ($sync) {
                        echo "<script>location.href='" . str_replace('&sync=true', '', ACTIVE_URL) . "';</script>";
                        exit;
                    }
                } else {
                    echo "<div class='alert warning'>No forms found for this page.</div>";
                }
            }

            // 3. Display forms
            $formList = mysqli_query(
                $connection,
                "SELECT * FROM " . TABLE_PREFIX . "fb_forms 
             WHERE fb_forms_status=true 
             AND fb_forms_page_id='" . mysqli_real_escape_string($connection, $pageId) . "' 
             AND fb_forms_site_id=" . SITE_ID
            );

            if ($formList && mysqli_num_rows($formList) > 0) {
                while ($form = mysqli_fetch_assoc($formList)) {
                    echo "<div class='box'>
                        <div class='widget box-7'>
                            <p>" . htmlspecialchars($form['fb_forms_name']) . "</p>
                        </div>
                        <div class='widget box-3'>
                            <a href='/dashboard/integration/facebook/leads/?form_id={$form['fb_forms_id']}&page_id={$pageId}' 
                               class='btn dataTableBtn'>Sync Leads</a>
                        </div>
                    </div>";
                }
            } else {
                echo "<div class='alert info'>No active forms found.</div>";
            }
            ?>
    </section>
    <?php }

    ?>
</div>