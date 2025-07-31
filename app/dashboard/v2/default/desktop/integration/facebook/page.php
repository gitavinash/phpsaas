<h2 class="heading"><?php echo ucwords($name) ?> forms list<span>All facebook forms assign to <?php echo $name ?>
        page</span>
</h2>
<div class="main-box">
    <section class='fb_page_list'>
        <?php

        $pageId = $id;

        if ($checkValidPageId = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "fb_pages WHERE fb_page_id='{$pageId}' AND fb_page_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($checkValidPageId) != 0) {
                extract(mysqli_fetch_assoc($checkValidPageId));
                if ($pageFormList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "fb_forms WHERE fb_forms_status=true AND fb_forms_page_id='{$pageId}' AND fb_forms_site_id=" . SITE_ID)) {

                    if (mysqli_num_rows($pageFormList) != 0) {

                        while ($pageData = mysqli_fetch_assoc($pageFormList)) {

                            extract($pageData);

                            echo "<div class='box'>
                                <div class='widget box-7'>
                                    <p>{$fb_forms_name}</p>
                                </div>
                                <div class='widget box-3'>
                                    <button href='#' class='btn dataTableBtn' >Subscribed</button>
                                </div>
                            </div>";
                        }
                    } else {

                        // Fetch page forms

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://graph.facebook.com/v19.0/{$pageId}/leadgen_forms?access_token={$fb_page_access_token}&?limit=100",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                        ));

                        $pageFormResponse = curl_exec($curl);
                        $pageFormResponse = json_decode($pageFormResponse, TRUE);

                        curl_close($curl);

                        if (count($pageFormResponse['data']) != 0) {

                            for ($i = 0; $i < count($pageFormResponse['data']); $i++) {

                                extract($pageFormResponse['data'][$i]);

                                $status = ((strtolower($status) == 'active') ? true : false);

                                if (!$saveForm = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "fb_forms(fb_forms_id, fb_forms_site_id, fb_forms_page_id, fb_forms_name, fb_forms_status) VALUES('{$id}', '" . SITE_ID . "', '{$pageId}', '{$name}', '{$status}')")) {
                                    echo mysqli_error($connection);
                                }
                            }

                            // subscribe to facebook webhook

                            $curl = curl_init();

                            $webhookURL = "https://app.aleya.in/webhook/facebook/";

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => "https://graph.facebook.com/{$fb_page_id}/subscribed_apps?subscribed_fields=leadgen&access_token={$fb_page_access_token}&link={$webhookURL}",
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

                            echo "<script>location.reload();</script>";
                        } else {
                            echo "No form found";
                        }
                    }
                }
            } else {
                echo "Invalid request received";
            }
        } else {
            echo 'Something went wrong';
        }

        ?>
    </section>
</div>