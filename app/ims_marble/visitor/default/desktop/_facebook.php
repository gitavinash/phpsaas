<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents('php://input');
    if (!empty($rawData)) {
        
        $jsonData = json_decode($rawData, true); // Use true to decode as associative array

        if($jsonData['entry'][0]['changes']) {
            for($i=0; $i<count($jsonData['entry'][0]['changes']); $i++) {

                $leadMeta = $jsonData['entry'][0]['changes'][$i]['value'];

                $leadId = $leadMeta['leadgen_id'];
                $pageId = $leadMeta['page_id'];
                $formId = $leadMeta['form_id'];

                if($checkPage=mysqli_query($connection, "SELECT fb_page_access_token, fb_forms_site_id, fb_forms_name as subject FROM ".TABLE_PREFIX."fb_forms INNER JOIN ".TABLE_PREFIX."fb_pages ON fb_forms_page_id=fb_page_id WHERE fb_forms_page_id='{$pageId}' AND fb_forms_id='{$formId}'")) {
                    if(mysqli_num_rows($checkPage)!=0) {
                        extract(mysqli_fetch_assoc($checkPage));

                        if($checkEmail=mysqli_query($connection, "SELECT site_configuration_value as toAddress FROM ".TABLE_PREFIX."site_configuration WHERE site_configuration_site_id='{$fb_forms_site_id}' AND site_configuration_key='enquiry_email' AND site_configuration_status=1")) {
                            if(mysqli_num_rows($checkEmail)!=0) {
                                extract(mysqli_fetch_assoc($checkEmail));
                                
                                // Fetch lead details from facebook

                                $curl = curl_init();

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => "https://graph.facebook.com/".FB_API_VERSION."/{$leadId}/?access_token={$fb_page_access_token}",
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'GET',
                                ));

                                $leadResponse = curl_exec($curl);

                                $leadResponse = json_decode($leadResponse, TRUE);

                                curl_close($curl);

                                $formData = $leadResponse['field_data'];

                                $name='';
                                $email='';
                                $mobile='';

                                for($i=0; $i<count($formData); $i++) {
                                    
                                    switch($formData[$i]['name']) {
                                        case 'full_name':
                                            $name = $formData[$i]['values'][0];
                                            break;
                                        case 'name':
                                            $name = $formData[$i]['values'][0];
                                            break;
                                        case 'email':
                                            $email = $formData[$i]['values'][0];
                                            break;
                                        case 'phone_number':
                                            $mobile = $formData[$i]['values'][0];
                                            break;
                                        case 'whatsapp_number':
                                            $mobile = $formData[$i]['values'][0];
                                            break;
                                        case 'whatsapp_no':
                                            $mobile = $formData[$i]['values'][0];
                                            break;
                                        default:
                                            break;
                                    }
                                }

                                // Send email and store into database

                                // Check user is exiting or not

                                $findUser = mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_email='{$email}' OR user_mobile='{$mobile}' LIMIT 1");

                                $userId=0;

                                if(mysqli_num_rows($findUser)!=0)  {
                                    $userInfo = mysqli_fetch_assoc($findUser);
                                    $userId = $userInfo['user_id'];
                                }

                                // track traffic resource

                                $trafficSource='facebook';
                                $trafficMedium='lead_form';
                                $trafficCampaign='';

                                $first_name = $name;

                                $addNewEnquiry = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry(enquiry_site_id, enquiry_user_id, enquiry_first_name, enquiry_email, enquiry_mobile, enquiry_subject, enquiry_type, enquiry_source, enquiry_medium, enquiry_campaign, enquiry_status, enquiry_time) VALUES('{$fb_forms_site_id}', '{$userId}', '{$name}', '{$email}', '{$mobile}', '{$subject}', 'enquiry', '{$trafficSource}', '{$trafficMedium}', '{$trafficCampaign}', 0, NOW())");
                                $leadId=mysqli_insert_id($connection);

                                if ($addNewEnquiry) {
                                    mysqli_query($connection, "COMMIT");
                                } else {
                                    mysqli_query($connection, "ROLLBACK");
                                }

                                $email_body = "
                                    <h2>{$subject}</h2>
                                    <table>
                                        <tr>
                                            <td>Name: </td>
                                            <td>{$name}</td>
                                        </tr>
                                        <tr>
                                            <td>Email: </td>
                                            <td>{$email}</td>
                                        </tr>
                                        <tr>
                                            <td>Mobile: </td>
                                            <td>{$mobile}</td>
                                        </tr>
                                    </table>
                                    ";
                                    
                                    $sender = $email;

                                    if(isset($sender)) {
                                        if(!isset($doNotSendEmail)) {
                                            require SEND_EMAIL;
                                            require_once APP_EMAIL_TEMPLATE.'enquiry-thank-you.php';
                                        }
                                    }

                                echo json_encode(array(
                                    'status' => 200,
                                    'success' => true
                                ), TRUE);

                                exit();

                            }
                        }

                    }
                }

            }
        }
        
    }
}