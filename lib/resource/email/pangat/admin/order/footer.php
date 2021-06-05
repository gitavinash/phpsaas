<?php

	$email_footer="	
							</td>
						</tr>				
					</table>

				</td>

				</tr>

				<tr bgcolor='#fff'>

				<td valign='top' class='footer' style='border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background: #fff;text-align: center; border-top: 4px solid {$themeColor};'>

					<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;'>

						<tr>

							<td class='inside-footer' align='center' valign='middle' style='border-collapse: collapse;border: 0;margin: 0;padding: 20px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 12px;line-height: 16px;vertical-align: middle;text-align: center;width: 580px;'>

								<div id='address' class='mktEditable'>

									<b>".SITE_NAME."</b>

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

	$email_body = $email_header.$email_body.$email_footer;

	/* Send email */

	$toAddress = ADMIN_EMAIL; // Email receiver id
	
	require SEND_EMAIL;