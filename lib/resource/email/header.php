<?php

if (!defined('SITE_THEME_COLOUR')) {
	define('SITE_THEME_COLOUR', '#2563eb');
}
if (!defined('SITE_NAME')) {
	define('SITE_NAME', 'CRM Platform');
}
if (!defined('SITE_DOMAIN')) {
	define('SITE_DOMAIN', (defined('SITE_URL') ? SITE_URL : ('https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'))));
}
if (!defined('SITE_LOGO')) {
	define('SITE_LOGO', '');
}

$themeColor = SITE_THEME_COLOUR;
$textColor = '#333';

$siteUrlLink = (strpos(SITE_DOMAIN, 'http') === 0) ? SITE_DOMAIN : ('https://' . SITE_DOMAIN);
if (defined('SITE_LOGO') && !empty(SITE_LOGO)) {
	$logoMarkup = "<a href='{$siteUrlLink}' target='_blank'><img class='top-image' src='" . SITE_LOGO . "' alt='" . SITE_NAME . " logo' style='line-height: 1; max-height: 48px; height: auto; padding: 10px 15px; max-width: 200px; display: block; border: 0;' ></a>";
} else {
	$logoMarkup = "<a href='{$siteUrlLink}' target='_blank' style='text-decoration: none; display: block; padding: 12px 18px;'><span style='font-size: 20px; font-weight: 800; color: {$themeColor}; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif; letter-spacing: -0.02em;'>" . SITE_NAME . "</span></a>";
}

$email_header = "
	
	<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

	<html xmlns='http://www.w3.org/1999/xhtml'>

 		<head>

	  		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	  		<meta name='viewport' content='width=device-width, initial-scale=1.0'/>

	  		<title>Notification</title>

	  		<style type='text/css'>

  				@media only screen and (max-width: 600px) {

					.main {

						width: 320px !important;

					}



					.top-image {

						width: 100% !important;

					}

					.inside-footer {

						width: 320px !important;

					}

					table[class='contenttable'] { 

			            width: 320px !important;

			            text-align: left !important;

			        }

			        td[class='force-col'] {

				        display: block !important;

				    }

				     td[class='rm-col'] {

				        display: none !important;

				    }

					.mt {

						margin-top: 15px !important;

					}

					*[class].width300 {width: 255px !important;}

					*[class].block {display:block !important;}

					*[class].blockcol {display:none !important;}

					.emailButton{

			            width: 100% !important;

			        }



			        .emailButton a {

			            display:block !important;

			            font-size:18px !important;

			        }

				}

  			</style>

		</head>

  		<body link='#FF8B38' vlink='#FF8B38' alink='#FF8B38'>
				
				<table class='main contenttable' align='center' style='font-weight: normal;border-collapse: collapse;border: 0;margin-left: auto;margin-right: auto;padding: 0;font-family: Arial, sans-serif;color: #555559;background-color: white;font-size: 16px;line-height: 26px;width: 600px;'>

					<tr>

						<td class='border' style='border-collapse: collapse;border: 1px solid #eeeff0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;'>

							<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%;'>

								<tr>

									<td colspan='4' valign='top' class='image-section' style='border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background-color: #fff;border-bottom: 4px solid {$themeColor}'>

										{$logoMarkup}

									</td>

								</tr>

								<tr>

									<td valign='top' class='side title' style='border-collapse: collapse;border: 0;margin: 0;padding: 20px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;vertical-align: top;background-color: white;border-top: none;'>

										<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%'>

												<tr>
													<td>
												";

