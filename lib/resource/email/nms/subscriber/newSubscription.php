<?php

include_once 'header.php';

$subject = "Welcome to " . SITE_NAME . " – Your Subscription is Active!";

$email_body = "
<table width='100%' cellpadding='0' cellspacing='0' border='0' style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 40px 0;'>
    <tr>
        <td align='center'>
            <table width='600' cellpadding='0' cellspacing='0' border='0' style='background-color: #ffffff; padding: 30px; border-radius: 8px;>
                <tr>
                    <td align='center' style='font-size: 22px; font-weight: bold; color: #333333; padding-bottom: 20px;'>
                        Welcome to " . SITE_NAME . "!
                    </td>
                </tr>
                <tr>
                    <td style='font-size: 16px; color: #555555; line-height: 1.6;'>
                        <p>We're thrilled to have you on board! Your subscription has been <strong>successfully activated</strong>, and you now have full access to a world of insightful journalism and real-time news updates.</p>
                        
                        <p style='margin-top: 25px;'><strong>Here’s what you can look forward to:</strong></p>
                        
                        <ul style='padding-left: 20px;'>
                            <li><strong>Exclusive Content:</strong> In-depth articles, expert analyses, and special reports just for our subscribers.</li>
                            <li><strong>Real-Time Updates:</strong> Breaking news delivered as it happens.</li>
                        </ul>

                        <p style='margin-top: 25px;'><strong>Need Help?</strong></p>
                        <p>Our friendly support team is ready to assist you. If you have any questions, feel free to <a href='" . SITE_URL . "/contact-us/' style='color: #0073e6; text-decoration: none;'>contact us here</a>.</p>
                        
                        <p style='margin-top: 30px;'>Thanks again for subscribing to <strong>" . SITE_NAME . "</strong>.</p>
                    </td>
                </tr>
                <tr>
                    <td align='center' style='padding-top: 30px;'>
                        <a href='" . SITE_URL . "' style='background-color: #0073e6; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-weight: bold; display: inline-block;'>Visit " . SITE_NAME . "</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
";

include_once 'footer.php';
