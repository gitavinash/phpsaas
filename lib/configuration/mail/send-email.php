<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/exception.php';
require_once __DIR__ . '/php_mailer.php';
require_once __DIR__ . '/smtp.php';

if (!defined('EMAIL_ENABLE')) {
    define('EMAIL_ENABLE', 1);
}

if (EMAIL_ENABLE == 1) {

    require_once __DIR__ . '/smtp-configuration.php';

    $mail = new PHPMailer(true);

    try {
        // $mail->SMTPDebug = 0; // or use 3 for even more verbose output
        // $mail->Debugoutput = 'html';

        $portNum = defined('SMTP_PORT') ? (int)SMTP_PORT : 587;
        $sslSetting = defined('SMTP_SSL') ? strtolower((string)SMTP_SSL) : 'tls';

        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;

        if ($portNum === 465 || $sslSetting === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($portNum === 25 || $sslSetting === 'false' || $sslSetting === '0' || $sslSetting === 'none') {
            $mail->SMTPSecure = false;
            $mail->SMTPAutoTLS = false;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->Port = $portNum;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->setFrom(SMTP_EMAIL, SMTP_NAME);
        if (filter_var(REPLY_EMAIL, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo(REPLY_EMAIL, REPLY_EMAIL_NAME);
        }
        if (empty($toAddress)) {
            throw new Exception('Recipient email address is missing.');
        }
        $toAddresses = is_array($toAddress) ? $toAddress : explode(',', $toAddress);
        $hasValidTo = false;
        foreach ($toAddresses as $to) {
            $to = trim($to);
            if (!empty($to) && filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($to);
                $hasValidTo = true;
            }
        }
        if (!$hasValidTo) {
            $mail->addAddress(trim(is_array($toAddress) ? ($toAddress[0] ?? '') : $toAddress));
        }

        if (isset($ccAddress)) {
            $ccAddresses = is_array($ccAddress) ? $ccAddress : explode(',', $ccAddress);
            foreach ($ccAddresses as $cc) {
                $cc = trim($cc);
                if (!empty($cc) && filter_var($cc, FILTER_VALIDATE_EMAIL)) {
                    $mail->addCC($cc);
                }
            }
        }

        if (isset($bccAddress)) {
            $bccAddresses = is_array($bccAddress) ? $bccAddress : explode(',', $bccAddress);
            foreach ($bccAddresses as $bcc) {
                $bcc = trim($bcc);
                if (!empty($bcc) && filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
                    $mail->addBCC($bcc);
                }
            }
        }

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $email_body;
        $mail->AltBody = strip_tags($email_body);

        if (!$mail->send()) {
            $emailIsNotDelivered = 'Mailer Error: ' . $mail->ErrorInfo;
            error_log('Mailer Error: ' . $mail->ErrorInfo);
        }
    } catch (Exception $e) {
        if (SITE_ID == 17) {
            echo SMTP_PASSWORD . ' -> ' . SMTP_USERNAME . ' -> ' . SMTP_HOST . ' -> ' . SMTP_PORT . ' -> ' . $mail->SMTPSecure;
            exit();
        }
        $emailIsNotDelivered = "Message could not be sent: " . $e->getMessage();
        error_log("Message could not be sent. Exception: {$e->getMessage()} | Mailer Error: {$mail->ErrorInfo}");
    }
}
