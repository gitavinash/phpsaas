<?php

$subject = 'Visa Application Received';

include_once EMAIL_TEMPLATE . 'header.php';

$visaApplicationButton = SITE_URL . '/dashboard/visa_applications/list/?id=' . base64_encode($applicationId);

$email_body = "
        
        <table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%'>
            <tr>
                <td style='text-align: center'>
                    <h1 style='color: {$themeColor}'>{$subject}</h1>
                    <p>Dear {$visaData['firstName']}},</p>
                    <p>Thank you for submitting your visa application. Here are the details:</p>
                    
                    <table style='padding: 20px 0px; text-align: left; margin: 0 auto;'>
                        <tr>
                            <td style='padding: 5px 0;'><strong>Application Reference:</strong></td>
                            <td style='padding: 5px 0; padding-left: 20px;'>BMV00{$applicationId}</td>
                        </tr>
                        <tr>
                            <td style='padding: 5px 0;'><strong>Current Status:</strong></td>
                            <td style='padding: 5px 0; padding-left: 20px;'>Pending</td>
                        </tr>
                    </table>
                    
                    <p style='margin-top: 20px;'>You can track the status of your application using the button below:</p>
                    <p style='margin: 20px 0;'>
                        <a href='{$visaApplicationButton}' style='background-color: {$themeColor}; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                            Track My Application
                        </a>
                    </p>
                    <tr>
                            <td style='text-align: center'>
                                <h3>Having trouble clicking the Track My Application button?</h3>
                                <p>Simply copy this URL: {$visaApplicationButton}</p>
                            </td>
                        </tr>
                    
                    <p>We will notify you once your application has been processed. For any questions, please contact our support team.</p>
                    <p>Thank you for choosing our services.</p>
                </td>
            </tr>
";

$email = $visaData['email'];

require_once EMAIL_TEMPLATE . 'footer.php';