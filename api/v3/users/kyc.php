<?php

/**
 * V3 User KYC API Endpoint
 * Path: /api/v3/users/kyc.php
 */

header('Content-Type: application/json');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --------------------------------------------------------------------------
// 1. GET: Fetch User Details & KYC Status & Links
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $encodedId = $_GET['id'] ?? ($_GET['user_id'] ?? '');
    $userId = 0;
    if (!empty($encodedId)) {
        $decoded = base64_decode($encodedId, true);
        $userId = ($decoded && is_numeric($decoded)) ? (int)$decoded : (int)$encodedId;
    }

    if ($userId <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'msg' => 'A valid user ID is required.'
        ]);
        exit;
    }

    // Check user in database
    $userQuery = "SELECT u.user_id, u.user_mobile, u.user_email, u.user_status,
                         sa.site_access_type as role, sa.site_access_site_id,
                         p.profile_first_name, p.profile_last_name, p.profile_display_name
                  FROM " . TABLE_PREFIX . "user u
                  INNER JOIN " . TABLE_PREFIX . "site_access sa ON sa.site_access_user_id = u.user_id
                  LEFT JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id
                  WHERE u.user_id = {$userId}
                  LIMIT 1";

    $userRes = mysqli_query($connection, $userQuery);
    if (!$userRes || mysqli_num_rows($userRes) === 0) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'msg' => 'User account not found.'
        ]);
        exit;
    }

    $userRow = mysqli_fetch_assoc($userRes);
    $firstName = $userRow['profile_first_name'] ?? '';
    $lastName = $userRow['profile_last_name'] ?? '';
    $displayName = $userRow['profile_display_name'] ?? '';
    if (empty($displayName)) {
        $displayName = trim($firstName . ' ' . $lastName);
    }
    if (empty($displayName)) {
        $displayName = 'User #' . $userId;
    }

    $userEmail = $userRow['user_email'] ?? '';
    $userMobile = $userRow['user_mobile'] ?? '';

    // Fetch existing KYC row
    $kycQuery = "SELECT * FROM " . TABLE_PREFIX . "kyc 
                 WHERE kyc_request_by = {$userId} OR kyc_ref_id = {$userId}
                 ORDER BY kyc_id DESC LIMIT 1";
    $kycRes = mysqli_query($connection, $kycQuery);
    $kycData = ($kycRes && mysqli_num_rows($kycRes) > 0) ? mysqli_fetch_assoc($kycRes) : null;

    // Build Public KYC Setup URL
    $siteBaseUrl = defined('SITE_URL') ? rtrim(SITE_URL, '/') : ('https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    $kycPublicUrl = $siteBaseUrl . '/kyc/?setup_id=' . base64_encode($userEmail);

    // Build Pre-formatted WhatsApp Reminder Link
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Our Platform';
    $waMessage = "*" . $siteName . " - KYC Document Verification*\n\n"
        . "Hello " . $displayName . ",\n\n"
        . "Please complete your identity and compliance verification by uploading your KYC documents using the secure link below:\n\n"
        . $kycPublicUrl . "\n\n"
        . "If you have already submitted your documents, please ignore this reminder.\n\n"
        . "Thank you,\n" . $siteName . " Team";

    $cleanMobile = preg_replace('/[^0-9]/', '', str_replace('+91', '', $userMobile));
    $whatsappUrl = "https://wa.me/91" . $cleanMobile . "?text=" . urlencode($waMessage);

    // Check if user requested to send email reminder via GET action
    if (isset($_GET['action']) && $_GET['action'] === 'send_reminder') {
        $emailSent = false;
        $emailErr = '';

        if (!empty($userEmail)) {
            $subject = "Complete Your KYC for " . $siteName . " - Action Required";
            $verifyURL = $kycPublicUrl;
            $profile_display_name = $displayName;
            $email = $userEmail;

            // Include email template if available
            $templateFile = defined('EMAIL_TEMPLATE') ? (EMAIL_TEMPLATE . 'request-kyc.php') : '';
            if ($templateFile && file_exists($templateFile)) {
                try {
                    @require_once $templateFile;
                    $emailSent = true;
                } catch (\Throwable $e) {
                    $emailErr = $e->getMessage();
                }
            } else {
                // Fallback default mail sending
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8\r\n";
                $headers .= "From: " . $siteName . " <no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'example.com') . ">\r\n";

                $bodyHtml = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                        <h2 style='color: #4f46e5;'>KYC Document Verification</h2>
                        <p>Dear {$displayName},</p>
                        <p>To ensure system compliance and verify your account on <strong>{$siteName}</strong>, please complete your KYC document verification.</p>
                        <p style='margin: 24px 0;'>
                            <a href='{$verifyURL}' style='background: #4f46e5; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Complete Your KYC Now</a>
                        </p>
                        <p style='font-size: 13px; color: #64748b;'>Or copy and paste this link in your browser:<br><a href='{$verifyURL}'>{$verifyURL}</a></p>
                        <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                        <p style='font-size: 12px; color: #94a3b8;'>{$siteName} Compliance Team</p>
                    </div>
                ";
                @mail($userEmail, $subject, $bodyHtml, $headers);
                $emailSent = true;
            }
        }

        echo json_encode([
            'status' => $emailSent ? 'success' : 'error',
            'msg' => $emailSent ? "KYC verification email reminder sent successfully to {$userEmail}." : "Could not send email. " . $emailErr,
            'kyc_url' => $kycPublicUrl,
            'whatsapp_url' => $whatsappUrl
        ]);
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'user' => [
            'id' => (int)$userId,
            'encoded_id' => base64_encode((string)$userId),
            'name' => $displayName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $userEmail,
            'mobile' => $userMobile,
            'role' => $userRow['role'] ?? 'agent',
            'status' => (int)($userRow['user_status'] ?? 1)
        ],
        'kyc' => $kycData ? [
            'id' => (int)$kycData['kyc_id'],
            'status' => (int)$kycData['kyc_status'], // 0: Pending, 1: Verified, 2: Rejected
            'aadhar_number' => $kycData['kyc_aadhar'] ?? '',
            'aadhar_file' => $kycData['kyc_aadhar_file'] ?? '',
            'aadhar_remark' => $kycData['kyc_aadhar_password'] ?? '',
            'pan_number' => $kycData['kyc_pan_no'] ?? '',
            'pan_file' => $kycData['kyc_pan_file'] ?? '',
            'pan_remark' => $kycData['kyc_pan_password'] ?? '',
            'gst_number' => $kycData['kyc_gst_number'] ?? '',
            'gst_file' => $kycData['kyc_gst_file'] ?? '',
            'gst_remark' => $kycData['kyc_gst_password'] ?? '',
            'request_time' => $kycData['kyc_request_time'] ?? ''
        ] : null,
        'kyc_url' => $kycPublicUrl,
        'whatsapp_url' => $whatsappUrl
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST: Upload Documents & Save / Update KYC
// --------------------------------------------------------------------------
if ($method === 'POST') {
    // Handle JSON or Form Data
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $encodedId = $data['id'] ?? ($data['user_id'] ?? '');
    $userId = 0;
    if (!empty($encodedId)) {
        $decoded = base64_decode($encodedId, true);
        $userId = ($decoded && is_numeric($decoded)) ? (int)$decoded : (int)$encodedId;
    }

    if ($userId <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'msg' => 'A valid user ID is required.'
        ]);
        exit;
    }

    // Action: Send email reminder
    if (($data['action'] ?? '') === 'send_reminder') {
        // Look up user email
        $uRes = mysqli_query($connection, "SELECT u.user_email, p.profile_display_name, p.profile_first_name, p.profile_last_name 
                                           FROM " . TABLE_PREFIX . "user u
                                           LEFT JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id
                                           WHERE u.user_id = {$userId} LIMIT 1");
        if ($uRes && mysqli_num_rows($uRes) > 0) {
            $uRow = mysqli_fetch_assoc($uRes);
            $userEmail = $uRow['user_email'];
            $displayName = $uRow['profile_display_name'] ?: trim($uRow['profile_first_name'] . ' ' . $uRow['profile_last_name']) ?: 'Team Member';
            $siteName = defined('SITE_NAME') ? SITE_NAME : 'Our Platform';
            $siteBaseUrl = defined('SITE_URL') ? rtrim(SITE_URL, '/') : ('https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
            $kycPublicUrl = $siteBaseUrl . '/kyc/?setup_id=' . base64_encode($userEmail);

            $subject = "Complete Your KYC for " . $siteName . " - Action Required";
            $verifyURL = $kycPublicUrl;
            $profile_display_name = $displayName;
            $email = $userEmail;

            $templateFile = defined('EMAIL_TEMPLATE') ? (EMAIL_TEMPLATE . 'request-kyc.php') : '';
            if ($templateFile && file_exists($templateFile)) {
                @require_once $templateFile;
            } else {
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8\r\n";
                $headers .= "From: " . $siteName . " <no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'example.com') . ">\r\n";
                $bodyHtml = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2>Complete Your KYC</h2>
                        <p>Dear {$displayName},</p>
                        <p>Please complete your KYC document verification on {$siteName}:</p>
                        <p><a href='{$verifyURL}'>Complete Your KYC Now</a></p>
                    </div>
                ";
                @mail($userEmail, $subject, $bodyHtml, $headers);
            }

            echo json_encode([
                'status' => 'success',
                'msg' => "KYC reminder sent successfully to {$userEmail}.",
                'kyc_url' => $kycPublicUrl
            ]);
            exit;
        }
    }

    // Document numbers
    $aadharNo = trim($data['aadhar_number'] ?? '');
    $panNo    = strtoupper(trim($data['pan_number'] ?? ''));
    $gstNo    = strtoupper(trim($data['gst_number'] ?? ''));
    $kycStatus = isset($data['kyc_status']) ? (int)$data['kyc_status'] : 0;
    $remarks  = trim($data['remarks'] ?? '');

    // Existing KYC record
    $existingKycRes = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "kyc WHERE kyc_request_by = {$userId} OR kyc_ref_id = {$userId} ORDER BY kyc_id DESC LIMIT 1");
    $existingKyc = ($existingKycRes && mysqli_num_rows($existingKycRes) > 0) ? mysqli_fetch_assoc($existingKycRes) : null;

    $aadharFile = $existingKyc['kyc_aadhar_file'] ?? '';
    $panFile    = $existingKyc['kyc_pan_file'] ?? '';
    $gstFile    = $existingKyc['kyc_gst_file'] ?? '';

    // Handle File Uploads
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/kyc/' . date('Y/m/');
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0777, true);
    }

    // Aadhaar File
    if (isset($_FILES['aadhar_file']) && $_FILES['aadhar_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['aadhar_file']['name'], PATHINFO_EXTENSION));
        $filename = 'aadhar_' . $userId . '_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['aadhar_file']['tmp_name'], $uploadDir . $filename)) {
            $aadharFile = '/upload/kyc/' . date('Y/m/') . '/' . $filename;
        }
    }

    // PAN File
    if (isset($_FILES['pan_file']) && $_FILES['pan_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['pan_file']['name'], PATHINFO_EXTENSION));
        $filename = 'pan_' . $userId . '_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['pan_file']['tmp_name'], $uploadDir . $filename)) {
            $panFile = '/upload/kyc/' . date('Y/m/') . '/' . $filename;
        }
    }

    // GST File
    if (isset($_FILES['gst_file']) && $_FILES['gst_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['gst_file']['name'], PATHINFO_EXTENSION));
        $filename = 'gst_' . $userId . '_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['gst_file']['tmp_name'], $uploadDir . $filename)) {
            $gstFile = '/upload/kyc/' . date('Y/m/') . '/' . $filename;
        }
    }

    $escapedAadharNo = mysqli_real_escape_string($connection, $aadharNo);
    $escapedAadharFile = mysqli_real_escape_string($connection, $aadharFile);
    $escapedPanNo = mysqli_real_escape_string($connection, $panNo);
    $escapedPanFile = mysqli_real_escape_string($connection, $panFile);
    $escapedGstNo = mysqli_real_escape_string($connection, $gstNo);
    $escapedGstFile = mysqli_real_escape_string($connection, $gstFile);
    $escapedRemarks = mysqli_real_escape_string($connection, $remarks);

    if ($existingKyc) {
        $kycId = (int)$existingKyc['kyc_id'];
        $updateSql = "UPDATE " . TABLE_PREFIX . "kyc SET
                        kyc_aadhar = '{$escapedAadharNo}',
                        kyc_aadhar_file = '{$escapedAadharFile}',
                        kyc_pan_no = '{$escapedPanNo}',
                        kyc_pan_file = '{$escapedPanFile}',
                        kyc_gst_number = '{$escapedGstNo}',
                        kyc_gst_file = '{$escapedGstFile}',
                        kyc_status = {$kycStatus},
                        kyc_aadhar_password = '{$escapedRemarks}'
                      WHERE kyc_id = {$kycId}";
        $exec = mysqli_query($connection, $updateSql);
    } else {
        $insertSql = "INSERT INTO " . TABLE_PREFIX . "kyc(
                        kyc_ref_id, kyc_ref_type, kyc_request_by,
                        kyc_aadhar, kyc_aadhar_file, kyc_aadhar_password,
                        kyc_pan_no, kyc_pan_file, kyc_pan_password,
                        kyc_gst_number, kyc_gst_file, kyc_gst_password,
                        kyc_status, kyc_request_time
                      ) VALUES (
                        {$userId}, 'user', {$userId},
                        '{$escapedAadharNo}', '{$escapedAadharFile}', '{$escapedRemarks}',
                        '{$escapedPanNo}', '{$escapedPanFile}', '',
                        '{$escapedGstNo}', '{$escapedGstFile}', '',
                        {$kycStatus}, NOW()
                      )";
        $exec = mysqli_query($connection, $insertSql);
    }

    if ($exec) {
        echo json_encode([
            'status' => 'success',
            'msg' => 'KYC documents and compliance details saved successfully.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'msg' => 'Database error while saving KYC details: ' . mysqli_error($connection)
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode([
    'status' => 'error',
    'msg' => 'Method not allowed.'
]);
