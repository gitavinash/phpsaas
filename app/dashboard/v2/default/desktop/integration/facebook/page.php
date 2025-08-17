<h2 class="heading">
    <?php echo ucwords($name) ?> forms list
    <span>All facebook forms assign to <?php echo $name ?> page</span>
</h2>

<div class="main-box">
    <div class="shortcut">
        <a href="<?php echo str_replace('&sync=true', '', ACTIVE_URL) ?>&sync=true">
            <i class="fi fi-rr-refresh"></i>
        </a>
    </div>
    <section class='fb_page_list'>
        <?php
        $pageId = $id;
        $sync   = (isset($_GET['sync']) && $_GET['sync'] === 'true'); // sync param

        // Check valid page
        $checkValidPageId = mysqli_query(
            $connection,
            "SELECT * FROM " . TABLE_PREFIX . "fb_pages 
             WHERE fb_page_id='{$pageId}' 
             AND fb_page_site_id=" . SITE_ID
        );

        if ($checkValidPageId && mysqli_num_rows($checkValidPageId) > 0) {
            $pageRow = mysqli_fetch_assoc($checkValidPageId);
            extract($pageRow);

            /**
             * Fetch all Facebook forms with pagination
             */
            function fetchFacebookForms($pageId, $accessToken)
            {
                $allForms = [];
                $url = "https://graph.facebook.com/v19.0/{$pageId}/leadgen_forms?access_token={$accessToken}&limit=10";

                do {
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
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
            $pageFormList = mysqli_query(
                $connection,
                "SELECT * FROM " . TABLE_PREFIX . "fb_forms 
                 WHERE fb_forms_page_id='{$pageId}' 
                 AND fb_forms_site_id=" . SITE_ID
            );

            if ($sync || mysqli_num_rows($pageFormList) == 0) {
                $fbForms = fetchFacebookForms($pageId, $fb_page_access_token);

                if (!empty($fbForms)) {
                    foreach ($fbForms as $form) {
                        $formId   = $form['id'];
                        $formName = mysqli_real_escape_string($connection, $form['name']);
                        $status   = (strtolower($form['status']) === 'active') ? 1 : 0;

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

                    // Refresh only when sync
                    if ($sync) {
                        echo "<script>location.href='" . str_replace('&sync=true', '', ACTIVE_URL) . "';</script>";
                        exit;
                    }
                } else {
                    echo "<div class='alert warning'>No forms found on Facebook.</div>";
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

            if ($pageFormList && mysqli_num_rows($pageFormList) > 0) {
                while ($pageData = mysqli_fetch_assoc($pageFormList)) {
                    echo "<div class='box'>
                            <div class='widget box-7'>
                                <p>{$pageData['fb_forms_name']}</p>
                            </div>
                            <div class='widget box-3'>
                                <a href='" . str_replace('/page/', '/forms/', BASE_URL) . "?id={$pageData['fb_forms_id']}' class='btn dataTableBtn' target='_blank'>View leads</a>
                                <button class='btn dataTableBtn'>Subscribed</button>
                            </div>
                        </div>";
                }
            }
        } else {
            echo "<div class='alert error'>Invalid request received</div>";
        }
        ?>
    </section>
</div>