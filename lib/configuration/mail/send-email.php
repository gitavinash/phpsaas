<?php

require_once 'PHPMailer.php';
require_once 'SMTP.php';
require_once 'Exception.php';
require_once 'smtp-configuration.php'; // Include SMTP configuration file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!defined(EMAIL_ENABLE)) {

	if (EMAIL_ENABLE == 1) {

		/** Include email sonfiguration file */

		$mail = new PHPMailer(true); // Enable exceptions

		try {
			$mail->isSMTP();
			$mail->Host = SMTP_HOST;  /* SMTP server */
			$mail->SMTPAuth = true;

			if (SMTP_SSL == 'true') {
				$mail->SMTPSecure = ((SMTP_HOST == 'smtp.gmail.com') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS);
			}

			$mail->Port = SMTP_PORT;
			$mail->Username = SMTP_USERNAME;
			$mail->Password = SMTP_PASSWORD;

			$mail->setFrom(SMTP_EMAIL, SMTP_NAME);
			$mail->addReplyTo(REPLY_EMAIL);
			$mail->isHTML(true);

			$mail->addAddress($toAddress);

			if (isset($ccAddress)) {
				$ccAddresses = is_array($ccAddress) ? $ccAddress : explode(',', $ccAddress);
				foreach ($ccAddresses as $cc) {
					$mail->addCC(trim($cc));
				}
			}

			if (isset($bccAddress)) {
				$bccAddresses = is_array($bccAddress) ? $bccAddress : explode(',', $bccAddress);
				foreach ($bccAddresses as $bcc) {
					$mail->addBCC(trim($bcc));
				}
			}

			$mail->Subject = $subject;
			$mail->Body = $email_body;

			if (DEVELOPMENT_MODE) {
				if (!$mail->send()) {
					echo $mail->ErrorInfo;
					exit();
				}
			} else {
				$mail->send();
			}
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			exit();
		}
	}
}