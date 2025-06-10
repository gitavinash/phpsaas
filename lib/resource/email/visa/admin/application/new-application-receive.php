<?php

$subject = 'New Visa Application Request';

include_once EMAIL_TEMPLATE . 'header.php';

$visaApplicationButton = SITE_URL . '/dashboard/leads/view/?id=' . base64_encode($applicationId);

$email_body = "
        
        <table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
            <tr>
                <td style='text-align: center'>
                    <h1 style='color: {$themeColor}'>{$subject}</h1>
                    <table style='padding: 20px 0px; text-align: left' >
                        <tr>
                            <td>Applicant name</td>
                            <td style='float: left; margin-left: 20px;' >{$visaData['firstName']}</td>
                        </tr>
                        <tr>
                            <td>Application ID</td>
                            <td style='float: left; margin-left: 20px;' >BMV00{$applicationId}</td>
                        </tr>
                        <tr>
                            <td style='float:left; width: 100%; text-align: center; margin-top: 20px; margin-bottom: 20px'>
                                <a href='{$visaApplicationButton}' style='color:#ffffff; background-color: {$themeColor}; border: 10px solid {$themeColor}; border-radius: 3px; text-decoration:none; display: inline-block; padding: 10px 20px;'>View Application Request</a>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align: center'>
                                <h3>Having trouble clicking the View Application Request button?</h3>
                                <p>Simply copy this URL: {$visaApplicationButton}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
    
";

$email = SITE_EMAIL_ALERT;

include_once EMAIL_TEMPLATE . 'footer.php';