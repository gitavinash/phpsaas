<?php

$siteErrorLogPath = ERROR_LOG . SITE_DOMAIN . '/';
if (!is_dir($siteErrorLogPath)) {
    mkdir($siteErrorLogPath, 0755, true);
}

ini_set("log_errors", 1);
ini_set("error_log", $siteErrorLogPath . 'error.log'); // Logs errors to logs/error.log in project root

function custom_log($message, $type = 'INFO')
{
    global $siteErrorLogPath;
    $logFile = $siteErrorLogPath . 'error.log';

    // Format: [timestamp] [type] URL - Message
    $formattedMessage = "[" . date("Y-m-d H:i:s") . "] [$type] ";
    $formattedMessage .= "URL: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . " | ";
    $formattedMessage .= "Message: " . $message . PHP_EOL;
    $formattedMessage .= "----------------------------------------" . PHP_EOL;

    echo $message;

    file_put_contents(
        $logFile,
        $formattedMessage,
        FILE_APPEND
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents('php://input');
    if (!empty($rawData)) {

        $jsonData = json_decode($rawData, true); // Use true to decode as associative array

        if ($jsonData['entry'][0]['changes']) {
            for ($i = 0; $i < count($jsonData['entry'][0]['changes']); $i++) {

                $leadMeta = $jsonData['entry'][0]['changes'][$i]['value'];

                $leadId = $leadMeta['leadgen_id'];
                $pageId = $leadMeta['page_id'];
                $formId = $leadMeta['form_id'];

                if ($checkPage = mysqli_query($connection, "SELECT fb_page_access_token, fb_forms_site_id, fb_forms_name as subject FROM " . TABLE_PREFIX . "fb_forms INNER JOIN " . TABLE_PREFIX . "fb_pages ON fb_forms_page_id=fb_page_id WHERE fb_forms_page_id='{$pageId}' AND fb_forms_id='{$formId}'")) {
                    if (mysqli_num_rows($checkPage) != 0) {
                        extract(mysqli_fetch_assoc($checkPage));

                        $fbSiteQuery = "SELECT 
    sc.site_configuration_key,
    sc.site_configuration_value, 
    s.site_name AS companyName, 
    s.site_domain
FROM 
    " . TABLE_PREFIX . "site_configuration sc
INNER JOIN 
    " . TABLE_PREFIX . "site s ON s.site_id = sc.site_configuration_site_id
WHERE 
    sc.site_configuration_site_id = '{$fb_forms_site_id}'
    AND sc.site_configuration_status = 1
    AND sc.site_configuration_key IN ('site_email', 'site_mobile', 'enable_whatsapp_business_api')";

                        if ($checkEmail = mysqli_query($connection, $fbSiteQuery)) {
                            if (mysqli_num_rows($checkEmail) != 0) {
                                $siteData = [];
                                $companyName = '';
                                $site_domain = '';
                                while ($siteInformation = mysqli_fetch_assoc($checkEmail)) {
                                    // print_r($siteInformation);
                                    $siteData[$siteInformation['site_configuration_key']] = $siteInformation['site_configuration_value'];
                                    $companyName = $siteInformation['companyName'];
                                    $site_domain = $siteInformation['site_domain'];
                                };

                                extract($siteData);

                                // Fetch lead details from facebook

                                $curl = curl_init();

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => "https://graph.facebook.com/" . FB_API_VERSION . "/{$leadId}/?access_token={$fb_page_access_token}",
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'GET',
                                ));

                                $leadResponseCurl = curl_exec($curl);

                                $leadResponse = json_decode($leadResponseCurl, TRUE);

                                curl_close($curl);

                                $formData = $leadResponse['field_data'];

                                $name = '';
                                $email = '';
                                $mobile = '';

                                for ($i = 0; $i < count($formData); $i++) {

                                    switch (true) {
                                        case strpos($formData[$i]['name'], 'name') !== false && strpos($formData[$i]['name'], 'email') === false:
                                            $name = $formData[$i]['values'][0];
                                            break;
                                        case strpos($formData[$i]['name'], 'email') !== false:
                                            $email = $formData[$i]['values'][0];
                                            break;
                                        case preg_match('/phone|phonenumber|phone_number|mobile|whatsapp|whatsaap/i', $formData[$i]['name']):
                                            $mobile = $formData[$i]['values'][0];
                                            break;
                                        default:
                                            break;
                                    }
                                }

                                // Send email and store into database

                                // Check user is exiting or not

                                $findUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}' OR user_mobile='{$mobile}' LIMIT 1");

                                $userId = 0;

                                if (mysqli_num_rows($findUser) != 0) {
                                    $userInfo = mysqli_fetch_assoc($findUser);
                                    $userId = $userInfo['user_id'];
                                }

                                // track traffic resource

                                $trafficSource = 'facebook';
                                $trafficMedium = 'lead_form';
                                $trafficCampaign = '';

                                $first_name = $name;

                                $mobile = str_replace('+91', '', str_replace(' ', '', $mobile));

                                if ($checkDuplicateLead = mysqli_query($connection, "SELECT enquiry_id FROM " . TABLE_PREFIX . "enquiry
                                    WHERE enquiry_first_name = '{$name}'
                                    AND enquiry_mobile = '{$mobile}'
                                    AND enquiry_subject = '{$subject}'
                                    AND enquiry_source = '{$trafficSource}'
                                    AND enquiry_campaign = '{$trafficCampaign}'
                                    AND enquiry_medium = '{$trafficMedium}'
                                    AND enquiry_time >= NOW() - INTERVAL 3 HOUR
                                    LIMIT 1")) {
                                    if (mysqli_num_rows($checkDuplicateLead) == 0) {

                                        if ($addNewEnquiry = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry(enquiry_site_id, enquiry_user_id, enquiry_first_name, enquiry_email, enquiry_mobile, enquiry_message, enquiry_subject, enquiry_type, enquiry_source, enquiry_medium, enquiry_campaign, enquiry_status, enquiry_time) VALUES('{$fb_forms_site_id}', '{$userId}', '{$name}', '{$email}', '{$mobile}', '{$leadResponseCurl}', '{$subject}', 'enquiry', '{$trafficSource}', '{$trafficMedium}', '{$trafficCampaign}', 0, NOW())")) {
                                            $leadId = mysqli_insert_id($connection);

                                            // if (isset($site_email)) {
                                            //     $sender = $site_email;
                                            //     if (!isset($doNotSendEmail)) {
                                            //         require_once APP_EMAIL_TEMPLATE . 'facebook-enquiry.php';
                                            //         require_once APP_EMAIL_TEMPLATE . 'facebook-enquiry-thank-you.php';
                                            //     }
                                            // } else {
                                            //     custom_log('No site_email found');
                                            // }

                                            if (isset($enable_whatsapp_business_api)) {
                                                if ($enable_whatsapp_business_api == 'yes') {
                                                    require_once WHATSAPP_TEMPLATE_ADMIN . 'new_enquiry.php';
                                                    require_once WHATSAPP_TEMPLATE_VISITOR . 'enquiry_thank_you.php';
                                                    custom_log('Whatsapp alert sent');
                                                }
                                            } else {
                                                custom_log('No enable_whatsapp_business_api found');
                                            }

                                            echo json_encode(array(
                                                'status' => 200,
                                                'success' => true
                                            ), TRUE);

                                            exit();
                                        } else {
                                            custom_log("Something went wrong while inserting the enquiry");
                                        }
                                    } else {
                                        custom_log("Dublicate enquiry found");
                                    }
                                } else {
                                    custom_log('Something went wrong while executing find enquiry');
                                }
                            }
                        } else {
                            custom_log("Site configurations is not found in Database  Page id: {$pageId} - Form id: {$formId}", "WARNING");
                        }
                    } else {
                        custom_log("Facebook page is not found in Database Page id: {$pageId} - Form id: {$formId}", "WARNING");
                    }
                } else {
                    custom_log(`Aleya visitor Something went wrong while running the query {$rawData}`, 'ERROR');
                }
            }
        } else {
            custom_log(`Request body:` . $rawData, "WARNING");
        }
    } else {
        custom_log("No 'entry' or 'changes' field found in JSON data", "WARNING");
        custom_log(`Request body:` . $rawData, "WARNING");
    }
} else {

    // Define your verification token (set in the Facebook app settings)
    $verificationToken = "EAANL49oYVsUBO6JuQz6tUkyiPbzZCGOei96dLTf3p3D18GPFA5eeq8cQPVAzwfqEabtre3s31njbO3z7KokfyUUWDuBPfeMCWCgLVkZA9BHYLZAZCZA3W2VjprlK0CKxuLH8hB6y7HR3xVRdNR8mzKZCmfvwmbwtplChgi5h25YGCqcp3KigmSk07E";

    // Handle Facebook verification
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['hub_mode']) && $_GET['hub_mode'] === 'subscribe') {
        // Check if the verify token matches
        if ($_GET['hub_verify_token'] === $verificationToken) {
            // Respond with the hub.challenge token from Facebook to complete verification
            echo $_GET['hub_challenge'];
            exit();
        } else {
            // Return a 403 Forbidden if the tokens do not match
            header('HTTP/1.1 403 Forbidden');
            echo "Verification token mismatch";
            exit();
        }
    }
}