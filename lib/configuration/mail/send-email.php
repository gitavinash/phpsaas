<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

if (!defined('EMAIL_ENABLE')) {
    define('EMAIL_ENABLE', 1);
}

if (EMAIL_ENABLE == 1) {

    require_once 'smtp-configuration.php';

    $mail = new PHPMailer(true);

    try {
		$mail->SMTPDebug = 2; // or use 3 for even more verbose output
		$mail->Debugoutput = 'html';
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->setFrom(SMTP_EMAIL, SMTP_NAME);
        $mail->addReplyTo(REPLY_EMAIL);

        if (empty($toAddress)) {
            throw new Exception('Recipient email address is missing.');
        }
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

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $email_body;
        $mail->AltBody = strip_tags($email_body);

        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            exit();
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Exception: {$e->getMessage()} | Mailer Error: {$mail->ErrorInfo}";
    }
}