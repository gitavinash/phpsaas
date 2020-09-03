<?php

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

	if(!$mail->Send()) {

		echo "Message could not be sent. <p>";
		echo "Mailer Error: " . $mail->ErrorInfo;

	}

