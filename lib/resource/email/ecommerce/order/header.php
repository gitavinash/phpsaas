<?php

	$textColor='#333';

	$email_header = "
	
	<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

	<html xmlns='http://www.w3.org/1999/xhtml'>

 		<head>

	  		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />

	  		<meta name='viewport' content='width=device-width, initial-scale=1.0'/>
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

  		<body>
				
				<table class='main contenttable' align='center' style='font-weight: normal;border-collapse: collapse;border: 0;margin-left: auto;margin-right: auto;padding: 0;font-family: Arial, sans-serif;color: #555559;background-color: white;font-size: 16px;line-height: 26px;width: 600px;'>

					<tr>

						<td class='border' style='border-collapse: collapse;border: 1px solid #eeeff0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;'>

							<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;'>

								<tr>

									<td colspan='4' valign='top' class='image-section' style='border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background-color: #fff;border-bottom: 4px solid ".SITE_THEME_COLOUR."'>

										<a href='".SITE_URL."'><img class='top-image' src='".SITE_URL.SITE_LOGO."' style='line-height: 1; height: 50px; padding: 5px 0px 5px 10px' ></a>

									</td>

								</tr>

								<tr>

									<td valign='top' class='side title' style='border-collapse: collapse;border: 0;margin: 0;padding: 20px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;vertical-align: top;background-color: white;border-top: none;'>

										<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%'>

												<tr>
													<td>
												";