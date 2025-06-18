<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'exception.php';
require_once 'php_mailer.php';
require_once 'smtp.php';

if (!defined('EMAIL_ENABLE')) {
    define('EMAIL_ENABLE', 1);
}

if (EMAIL_ENABLE == 1) {

    require_once 'smtp-configuration.php';

    $mail = new PHPMailer(true);

    try {
        // $mail->SMTPDebug = 0; // or use 3 for even more verbose output
        // $mail->Debugoutput = 'html';
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->setFrom(SMTP_EMAIL, SMTP_NAME);
        if (filter_var(REPLY_EMAIL, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo(REPLY_EMAIL, REPLY_EMAIL_NAME);
        } else {
            echo "Invalid reply email address. '" . REPLY_EMAIL . "'";
        }
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
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Exception: {$e->getMessage()} | Mailer Error: {$mail->ErrorInfo}";
    }
}