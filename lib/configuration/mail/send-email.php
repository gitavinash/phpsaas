<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

if (EMAIL_ENABLE == 1) {

    /** Include email configuration file */

    require_once 'smtp-configuration.php';

    /** PHP SMTP Email Code */

    $mail = new PHPMailer(true);

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
        $mail->isHTML(true);
        $mail->addReplyTo(REPLY_EMAIL);

        $mail->addAddress($toAddress);

        if (isset($ccAddress)) {
            if (!is_array($ccAddress)) {
                $ccAddresses = explode(',', $ccAddress);
            } else {
                $ccAddresses = $ccAddress;
            }
            foreach ($ccAddresses as $cc) {
                $mail->addCC(trim($cc));
            }
        }
        if (isset($bccAddress)) {
            if (!is_array($bccAddress)) {
                $bccAddresses = explode(',', $bccAddress);
            } else {
                $bccAddresses = $bccAddress;
            }
            foreach ($bccAddresses as $bcc) {
                $mail->addBCC(trim($bcc));
            }
        }

        $mail->Subject = $subject;
        $mail->Body = $email_body;

        if (DEVELOPMENT_MODE == true) {
            if (!$mail->send()) {
                $emailIsNotDelivered = $mail->ErrorInfo;
                echo $emailIsNotDelivered;
                exit();
            }
        } else {
            $mail->send();
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}