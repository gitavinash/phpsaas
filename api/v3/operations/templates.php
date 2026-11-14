<?php

/**
 * Operations Vendor Booking Request Templates Manager API
 * Supports CRUD, defaults, merge tags and live preview replacement for WhatsApp & Email
 * Path: /api/v3/operations/templates.php
 */

header("Content-Type: application/json; charset=utf-8");

require_once LOGIN_CHECK;

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? (int) SITE_ID : 0;
$currentUserId = defined('USER_ID') ? (int) USER_ID : 0;

$userRole = '';
if (!empty($site_access_type)) {
    $userRole = strtolower(trim($site_access_type));
} elseif (defined('USER_TYPE') && !empty(USER_TYPE)) {
    $userRole = strtolower(trim(USER_TYPE));
} elseif (!empty($_SESSION['user_type'])) {
    $userRole = strtolower(trim($_SESSION['user_type']));
} elseif (!empty($_COOKIE['user_type'])) {
    $userRole = strtolower(trim(base64_decode($_COOKIE['user_type'])));
}

if (!in_array($userRole, ['administrator', 'operations', 'super_administrator'])) {
    echo json_encode([
        'status' => 'error',
        'code'   => 403,
        'message' => 'Access denied. Only Operations and Administrators can manage booking request templates.'
    ]);
    exit();
}

// Ensure table exists
$createTableSql = "CREATE TABLE IF NOT EXISTS `{$tblPrefix}operations_template` (
  `template_id` INT AUTO_INCREMENT PRIMARY KEY,
  `template_site_id` BIGINT NOT NULL DEFAULT 0,
  `template_channel` VARCHAR(20) NOT NULL DEFAULT 'whatsapp',
  `template_vendor_type` VARCHAR(50) NOT NULL DEFAULT 'hotel',
  `template_title` VARCHAR(255) NOT NULL,
  `template_subject` VARCHAR(255) DEFAULT '',
  `template_body` LONGTEXT NOT NULL,
  `template_is_default` TINYINT NOT NULL DEFAULT 0,
  `template_status` TINYINT NOT NULL DEFAULT 1,
  `template_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `template_updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`template_site_id`),
  INDEX (`template_vendor_type`),
  INDEX (`template_channel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
mysqli_query($connection, $createTableSql);

// Seed default templates if empty for this site
$chkSql = "SELECT COUNT(*) as cnt FROM `{$tblPrefix}operations_template` WHERE template_site_id = {$siteId}";
$chkRes = mysqli_query($connection, $chkSql);
$chkRow = $chkRes ? mysqli_fetch_assoc($chkRes) : ['cnt' => 0];

if ((int)$chkRow['cnt'] === 0) {
    $defaultSeeds = [
        // 1. Hotel WhatsApp
        [
            'channel'     => 'whatsapp',
            'vendor_type' => 'hotel',
            'title'       => 'Hotel Booking Request (Standard)',
            'subject'     => '',
            'body'        => "Dear *{vendor_name}*,\n\nGreetings from *{agency_name}*!\n\nKindly confirm availability and reserve room(s) as per following booking request:\n\n🏨 *Property:* {vendor_name}\n👤 *Guest Name:* {guest_name} ({pax_adults} Adults, {pax_children} Children)\n📅 *Check-In:* {check_in}\n📅 *Check-Out:* {check_out} ({nights} Nights)\n🛏️ *Room Type:* {room_category} ({room_count} Rooms)\n🍽️ *Meal Plan:* {meal_plan}\n\n📌 *Special Remarks:* {special_instructions}\n\nPlease share the Confirmation / Booking Reference ID at your earliest.\n\nThank you,\n*{ops_manager_name}* | Operations Desk\n📱 {ops_manager_phone}",
            'is_default'  => 1
        ],
        // 2. Hotel Email
        [
            'channel'     => 'email',
            'vendor_type' => 'hotel',
            'title'       => 'Formal Hotel Reservation Email',
            'subject'     => 'Booking Request: {guest_name} - {nights}N - Check-in {check_in} - {agency_name}',
            'body'        => "<p>Dear Reservations Team (<strong>{vendor_name}</strong>),</p><p>Warm greetings from <strong>{agency_name}</strong>.</p><p>Please find our booking request below and confirm with your reservation reference number:</p><table style=\"width:100%; border-collapse:collapse; margin:16px 0; font-size:13px;\"><tr><td style=\"padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;\">Primary Guest</td><td style=\"padding:8px; border:1px solid #e2e8f0;\">{guest_name} ({pax_adults} Adults, {pax_children} Kids)</td></tr><tr><td style=\"padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;\">Check-In / Out</td><td style=\"padding:8px; border:1px solid #e2e8f0;\">{check_in} to {check_out} ({nights} Nights)</td></tr><tr><td style=\"padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;\">Rooms & Category</td><td style=\"padding:8px; border:1px solid #e2e8f0;\">{room_count} x {room_category}</td></tr><tr><td style=\"padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;\">Meal Plan</td><td style=\"padding:8px; border:1px solid #e2e8f0;\">{meal_plan}</td></tr><tr><td style=\"padding:8px; border:1px solid #e2e8f0; font-weight:bold; background:#f8fafc;\">Special Notes</td><td style=\"padding:8px; border:1px solid #e2e8f0;\">{special_instructions}</td></tr></table><p>Kindly acknowledge receipt and confirm by replying to this email.</p><p>Best regards,<br><strong>{ops_manager_name}</strong><br>Operations Desk | {agency_name}<br>Contact: {ops_manager_phone}</p>",
            'is_default'  => 1
        ],
        // 3. Transporter WhatsApp
        [
            'channel'     => 'whatsapp',
            'vendor_type' => 'transport',
            'title'       => 'Cab / Vehicle Booking Request',
            'subject'     => '',
            'body'        => "Hello *{vendor_name}*,\n\nNew cab booking request from *{agency_name}*:\n\n🚗 *Vehicle Required:* {vehicle_type}\n👤 *Guest Name:* {guest_name}\n📱 *Guest Contact:* {guest_phone}\n👥 *Total Pax:* {pax_adults} Adults, {pax_children} Kids\n📍 *Pickup Location:* {pickup_location}\n📍 *Drop Location:* {drop_location}\n⏰ *Pickup Date & Time:* {pickup_date_time}\n\n📌 *Notes:* {special_instructions}\n\nPlease allocate driver and share vehicle number & driver contact.\n\nRegards,\n*{ops_manager_name}* | {ops_manager_phone}",
            'is_default'  => 1
        ],
        // 4. Activity Guide WhatsApp
        [
            'channel'     => 'whatsapp',
            'vendor_type' => 'activity',
            'title'       => 'Activity & Guide Slot Booking',
            'subject'     => '',
            'body'        => "Dear *{vendor_name}*,\n\nPlease block the following excursion slot for our guests:\n\n🎯 *Activity/Tour:* {room_category}\n👤 *Guest:* {guest_name} ({pax_adults} Adults, {pax_children} Kids)\n📅 *Date:* {check_in}\n⏰ *Slot Time:* {pickup_date_time}\n\nKindly confirm slot availability.\n\n*{agency_name}* Operations Desk",
            'is_default'  => 1
        ]
    ];

    foreach ($defaultSeeds as $seed) {
        $cChan = mysqli_real_escape_string($connection, $seed['channel']);
        $cVend = mysqli_real_escape_string($connection, $seed['vendor_type']);
        $cTitl = mysqli_real_escape_string($connection, $seed['title']);
        $cSubj = mysqli_real_escape_string($connection, $seed['subject']);
        $cBody = mysqli_real_escape_string($connection, $seed['body']);
        $cDef  = (int)$seed['is_default'];

        mysqli_query($connection, "INSERT INTO `{$tblPrefix}operations_template` 
            (template_site_id, template_channel, template_vendor_type, template_title, template_subject, template_body, template_is_default, template_status)
            VALUES ({$siteId}, '{$cChan}', '{$cVend}', '{$cTitl}', '{$cSubj}', '{$cBody}', {$cDef}, 1)");
    }
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = isset($_GET['action']) ? trim($_GET['action']) : '';

// --------------------------------------------------------------------------
// 1. GET: List templates or single template
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $channel = isset($_GET['channel']) ? trim($_GET['channel']) : '';
    $vendorType = isset($_GET['vendor_type']) ? trim($_GET['vendor_type']) : '';
    $templateId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $where = ["template_site_id = {$siteId}", "template_status = 1"];
    if (!empty($channel)) {
        $where[] = "template_channel = '" . mysqli_real_escape_string($connection, $channel) . "'";
    }
    if (!empty($vendorType)) {
        $where[] = "template_vendor_type = '" . mysqli_real_escape_string($connection, $vendorType) . "'";
    }
    if ($templateId > 0) {
        $where[] = "template_id = {$templateId}";
    }

    $whereSql = implode(' AND ', $where);
    $res = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}operations_template` WHERE {$whereSql} ORDER BY template_is_default DESC, template_id ASC");

    $templates = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $templates[] = [
                'id'          => (int)$row['template_id'],
                'channel'     => $row['template_channel'],
                'vendor_type' => $row['template_vendor_type'],
                'title'       => $row['template_title'],
                'subject'     => $row['template_subject'],
                'body'        => $row['template_body'],
                'is_default'  => (int)$row['template_is_default'] === 1,
                'updated_at'  => $row['template_updated_at']
            ];
        }
    }

    echo json_encode([
        'status'    => 'success',
        'templates' => $templates,
        'merge_tags' => [
            '{vendor_name}'         => 'Vendor / Property / Transporter Name',
            '{vendor_contact}'      => 'Vendor Phone / Mobile Number',
            '{guest_name}'          => 'Primary Guest Full Name',
            '{guest_phone}'         => 'Guest Mobile / WhatsApp Number',
            '{pax_adults}'          => 'Number of Adults',
            '{pax_children}'        => 'Number of Children',
            '{check_in}'            => 'Check-in / Start Date (e.g. 15 Nov 2026)',
            '{check_out}'           => 'Check-out / End Date',
            '{nights}'              => 'Total Nights Count',
            '{room_category}'       => 'Room Category (e.g. Deluxe Garden View)',
            '{room_count}'          => 'Number of Rooms',
            '{meal_plan}'           => 'Meal Plan (CP / MAP / AP / EP)',
            '{vehicle_type}'        => 'Vehicle Model (e.g. Innova Crysta / Tempo Traveller)',
            '{pickup_location}'     => 'Pickup Point / City / Airport',
            '{drop_location}'       => 'Drop Point / Final Destination',
            '{pickup_date_time}'    => 'Scheduled Pickup Time & Date',
            '{special_instructions}'=> 'Custom Notes & Special Requests',
            '{agency_name}'         => 'Travel Agency / Brand Name',
            '{ops_manager_name}'    => 'Operations Executive Name',
            '{ops_manager_phone}'   => 'Operations Phone Number'
        ]
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Create, Update, Delete or Set Default
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $subAction = isset($input['action']) ? trim($input['action']) : 'save';

    if ($subAction === 'delete') {
        $delId = isset($input['id']) ? (int)$input['id'] : 0;
        if ($delId > 0) {
            mysqli_query($connection, "DELETE FROM `{$tblPrefix}operations_template` WHERE template_id = {$delId} AND template_site_id = {$siteId}");
            echo json_encode(['status' => 'success', 'message' => 'Template removed successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid template ID.']);
        }
        exit();
    }

    if ($subAction === 'set_default') {
        $defId = isset($input['id']) ? (int)$input['id'] : 0;
        if ($defId > 0) {
            // Fetch channel and vendor type
            $fRes = mysqli_query($connection, "SELECT template_channel, template_vendor_type FROM `{$tblPrefix}operations_template` WHERE template_id = {$defId} AND template_site_id = {$siteId} LIMIT 1");
            if ($fRes && $fRow = mysqli_fetch_assoc($fRes)) {
                $c = $fRow['template_channel'];
                $v = $fRow['template_vendor_type'];
                // Reset others
                mysqli_query($connection, "UPDATE `{$tblPrefix}operations_template` SET template_is_default = 0 WHERE template_site_id = {$siteId} AND template_channel = '{$c}' AND template_vendor_type = '{$v}'");
                mysqli_query($connection, "UPDATE `{$tblPrefix}operations_template` SET template_is_default = 1 WHERE template_id = {$defId} AND template_site_id = {$siteId}");
                echo json_encode(['status' => 'success', 'message' => 'Default template updated.']);
                exit();
            }
        }
        echo json_encode(['status' => 'error', 'message' => 'Could not set template as default.']);
        exit();
    }

    // Save or Update Template
    $tId = isset($input['id']) ? (int)$input['id'] : 0;
    $channel = isset($input['channel']) ? strtolower(trim($input['channel'])) : 'whatsapp';
    $vendorType = isset($input['vendor_type']) ? strtolower(trim($input['vendor_type'])) : 'hotel';
    $title = isset($input['title']) ? trim($input['title']) : '';
    $subject = isset($input['subject']) ? trim($input['subject']) : '';
    $body = isset($input['body']) ? trim($input['body']) : '';
    $isDefault = !empty($input['is_default']) ? 1 : 0;

    if (empty($title) || empty($body)) {
        echo json_encode(['status' => 'error', 'message' => 'Template title and body are required.']);
        exit();
    }

    $cTitle = mysqli_real_escape_string($connection, $title);
    $cSubj  = mysqli_real_escape_string($connection, $subject);
    $cBody  = mysqli_real_escape_string($connection, $body);
    $cChan  = mysqli_real_escape_string($connection, $channel);
    $cVend  = mysqli_real_escape_string($connection, $vendorType);

    if ($isDefault === 1) {
        mysqli_query($connection, "UPDATE `{$tblPrefix}operations_template` SET template_is_default = 0 WHERE template_site_id = {$siteId} AND template_channel = '{$cChan}' AND template_vendor_type = '{$cVend}'");
    }

    if ($tId > 0) {
        $updSql = "UPDATE `{$tblPrefix}operations_template` 
                   SET template_title = '{$cTitle}',
                       template_channel = '{$cChan}',
                       template_vendor_type = '{$cVend}',
                       template_subject = '{$cSubj}',
                       template_body = '{$cBody}',
                       template_is_default = {$isDefault},
                       template_updated_at = CURRENT_TIMESTAMP
                   WHERE template_id = {$tId} AND template_site_id = {$siteId}";
        if (mysqli_query($connection, $updSql)) {
            echo json_encode(['status' => 'success', 'message' => 'Template updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update template: ' . mysqli_error($connection)]);
        }
    } else {
        $insSql = "INSERT INTO `{$tblPrefix}operations_template` 
                   (template_site_id, template_channel, template_vendor_type, template_title, template_subject, template_body, template_is_default, template_status)
                   VALUES ({$siteId}, '{$cChan}', '{$cVend}', '{$cTitle}', '{$cSubj}', '{$cBody}', {$isDefault}, 1)";
        if (mysqli_query($connection, $insSql)) {
            echo json_encode(['status' => 'success', 'message' => 'Template created successfully.', 'id' => mysqli_insert_id($connection)]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create template: ' . mysqli_error($connection)]);
        }
    }
    exit();
}
