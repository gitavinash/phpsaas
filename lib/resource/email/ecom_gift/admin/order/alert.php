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

$email_footer = "	
							</td>
						</tr>				
					</table>

				</td>

				</tr>

				<tr bgcolor='#fff'>

				<td valign='top' class='footer' style='border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background: #fff;text-align: center; border-top: 4px solid " . SITE_THEME_COLOUR . ";'>

					<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;'>

						<tr>

							<td class='inside-footer' align='center' valign='middle' style='border-collapse: collapse;border: 0;margin: 0;padding: 20px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 12px;line-height: 16px;vertical-align: middle;text-align: center;width: 580px;'>

								<div id='address' class='mktEditable'>

									<p><strong>" . SITE_NAME . "</strong></p>
									
								</div>

							</td>

						</tr>

					</table>

				</td>

				</tr>

				</table>

				</td>

				</tr>

				</table>

			</body>

		</html>";

$email_body = $email_header . $email_body . $email_footer;

/* Send email */

$toAddress = SITE_EMAIL;

require SEND_EMAIL;