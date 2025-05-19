<?php

$subject = "New Order Received: OD{$order_id} | " . SITE_NAME;

include_once 'header.php';

$email_body = "
    <table style='font-weight: normal; border-collapse: collapse; border: 0; margin: 0; padding: 0; font-family: Arial, sans-serif; width: 100%;'>
        <tr>
            <td style='text-align: center; padding: 20px;'>
                <p style='text-align: left; font-size: 14px; line-height: 24px;'>
                    Hi " . SITE_NAME . " team,
                </p>
                <p style='text-align: left; font-size: 14px; line-height: 24px;'>
                    A new order (Order No: <strong>OD{$order_id}</strong>) has been successfully received on your website.
                </p>
                <p style='text-align: left; font-size: 14px; line-height: 24px;'>
                    You can view the full order details by clicking the button below:
                </p>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='" . SITE_URL . "/dashboard/order/?type=view&id=" . base64_encode($order_id) . "' 
                       style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px;'>
                       View Order
                    </a>
                </p>
                <p style='text-align: left; font-size: 14px; line-height: 24px;'>
                    Please log in to your dashboard to process the order as soon as possible.
                </p>
            </td>
        </tr>
    </table>
";

include_once 'footer.php';
exit();