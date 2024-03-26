<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> List<span>All hostings list will be displayed her</span>
</h2>
<div class="main-box">
    <?php

    if(isset($new)) {

        if(isset($accessToken)) {
        
            $extendedToken = "https://graph.facebook.com/".FB_API_VERSION."/oauth/access_token?grant_type=fb_exchange_token&client_id=".FB_APP_ID."&client_secret=".FB_APP_SECRET."&fb_exchange_token={$accessToken}";

            $extendedTokenData=file_get_contents($extendedToken);

            $extendedTokenData=json_decode($extendedTokenData, TRUE);
            $expiryDate=date('Y-m-d', strtotime('+90 days'));

            if($saveToken=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."fb(fb_user_id, fb_site_id, fb_access_token, fb_extended_token, fb_token_expiry, fb_token_status) VALUES('{$accessUserId}', '".SITE_ID."', '{$accessToken}', '{$extendedTokenData['access_token']}', '{$expiryDate}', true)")) {
                
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com//me/accounts',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer {$extendedTokenData['access_token']}",
            ),
            ));

            $fbPageResponse = curl_exec($curl);

            curl_close($curl);
            
            $fbPageResponseData=json_decode($fbPageResponse, TRUE);

            for($i=0; $i<count($fbPageResponseData['data']); $i++) {
                
                extract($fbPageResponseData['data'][$i]);

                $savePageData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."fb_pages(fb_page_id, fb_page_site_id, fb_page_name, fb_page_access_token, fb_page_category, fb_page_status) VALUES('{$id}', '".SITE_ID."', '{$name}', '{$access_token}', '{$category}', true)");

            } ?>

    <script>
    window.location.href = '/dashboard/integration/facebook/';
    </script>

    <?php
            exit();

            } else {
                echo mysqli_error($connection);
            }

            /* Get facebook page list */

        }

    } else {

        if($pageList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."fb_pages WHERE fb_page_status=true AND fb_page_site_id=".SITE_ID)) {

            if(mysqli_num_rows($pageList)!=0) {

                echo "<section class='fb_page_list'>";

                while($pageData=mysqli_fetch_assoc($pageList)) {

                    extract($pageData);

                    echo "<div class='box'>
                        <div class='widget box-7'>
                            <p>{$fb_page_name}<span>{$fb_page_category}</span></p>
                        </div>
                        <div class='widget box-3'>
                            <a href='/dashboard/integration/facebook/page/?id={$fb_page_id}' class='btn dataTableBtn' >View lead forms</a>
                        </div>
                    </div>";

                }

                echo "</section>";

            } else {
                $redirectURL = "https://aleya.in/facebook/?redirect_url=".base64_encode(SITE_DOMAIN)."=&new=true"; ?>
    <script>
    window.location.href = '<?php echo $redirectURL ?>'
    </script>
    <?php
            }

        }

    }

    ?>
</div>