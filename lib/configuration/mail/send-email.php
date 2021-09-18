<?php

	if(!defined(EMAIL_ENABLE)) {

		if(EMAIL_ENABLE==1) {

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

					// echo "Message could not be sent. <p>";
					// echo "Mailer Error: " . $mail->ErrorInfo;

				}

			} else {

				$mail->Send();

			}

		}
	
	}