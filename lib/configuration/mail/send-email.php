<?php

	if($checkSiteConfiguration=mysqli_query($connection, "SELECT site_configuration_name, site_configuration_value FROM ".TABLE_PREFIX."site_configuration WHERE site_configuration_name LIKE 'email_%' AND site_configuration_status=1 AND site_configuration_site_id=".APP_SITE_ID)) {
		
		if(mysqli_num_rows($checkSiteConfiguration)!=0) {
			
			while($siteConfigurationData=mysqli_fetch_assoc($checkSiteConfiguration)) {
				
				extract($siteConfigurationData);
				$configuration[$site_configuration_name]=$site_configuration_value;
				
			}

			extract($configuration);

			if($email_enable==1) {

				/** Include email sonfiguration file */

				require_once 'smtp-configuration.php';

				/** PHP SMTP Email Code */

				require_once 'phpmailer.php';

				$mail = new PHPMailer();

				$mail->IsSMTP();
				$mail->Host = SMTP_HOST;  /* SMTP server */

				$mail->SMTPAuth = true;

				if(SMTP_SSL=='true') {
					$mail->SMTPSecure = 'tls';
				}

				$mail->Port = SMTP_PORT;
				$mail->Username = SMTP_USERNAME;
				$mail->Password = SMTP_PASSWORD;

				$mail->From = SMTP_EMAIL;
				$mail->FromName = SMTP_NAME;
				$mail->IsHTML(true);
				$mail->AddReplyTo(REPLY_EMAIL);

				$mail->AddAddress($toAddress);
				$mail->Subject = $subject;
				$mail->Body = $email_body;

				if(DEVELOPMENT_MODE==true) {

					if(!$mail->Send()) {

						echo "Message could not be sent. <p>";
						echo "Mailer Error: " . $mail->ErrorInfo;
			
					}

				} else {

					$mail->Send();

				}

			}
			
		}

	}
