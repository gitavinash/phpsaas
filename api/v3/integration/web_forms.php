<?php

/**
 * V3 Landing Page Web Forms Integration API Endpoint
 * Path: /api/v3/integration/web_forms/
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

$siteId = defined('SITE_ID') && (int)SITE_ID > 0 ? (int)SITE_ID : (isset($_SESSION['site_id']) ? (int)$_SESSION['site_id'] : 0);

if ($siteId <= 0 && isset($_COOKIE['user_site_id'])) {
    $siteId = (int)base64_decode($_COOKIE['user_site_id']);
}

if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    $escapedHost = mysqli_real_escape_string($connection, $host);
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM " . TABLE_PREFIX . "site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
    if ($siteQuery && mysqli_num_rows($siteQuery) > 0) {
        $row = mysqli_fetch_assoc($siteQuery);
        $siteId = (int)$row['site_id'];
    }
}

if ($siteId <= 0) {
    $siteId = 1;
}

if (!defined('SITE_ID')) {
    define('SITE_ID', $siteId);
}

// --------------------------------------------------------------------------
// Self-healing database table initialization
// --------------------------------------------------------------------------
$tableName = TABLE_PREFIX . 'web_form';
$createTableSql = "CREATE TABLE IF NOT EXISTS `{$tableName}` (
  `form_id` INT AUTO_INCREMENT PRIMARY KEY,
  `form_site_id` INT NOT NULL DEFAULT 0,
  `form_uid` VARCHAR(64) NOT NULL UNIQUE,
  `form_title` VARCHAR(255) NOT NULL,
  `form_slug` VARCHAR(150) NOT NULL,
  `form_heading` VARCHAR(255) DEFAULT 'Get a Free Quote',
  `form_hide_heading` TINYINT(1) DEFAULT 0,
  `form_subheading` VARCHAR(255) DEFAULT 'Plan your dream vacation with our travel specialists',
  `form_hide_subheading` TINYINT(1) DEFAULT 0,
  `form_submit_btn_text` VARCHAR(100) DEFAULT 'Get Free Quote',
  `form_theme_color` VARCHAR(30) DEFAULT '#4f46e5',
  `form_bg_mode` VARCHAR(20) DEFAULT 'card',
  `form_input_height` VARCHAR(20) DEFAULT '44px',
  `form_fields_json` LONGTEXT NOT NULL,
  `form_tracking_json` TEXT DEFAULT NULL,
  `form_redirect_url` VARCHAR(500) DEFAULT NULL,
  `form_email_to` VARCHAR(500) DEFAULT NULL,
  `form_email_cc` VARCHAR(500) DEFAULT NULL,
  `form_email_bcc` VARCHAR(500) DEFAULT NULL,
  `form_success_message` TEXT DEFAULT NULL,
  `form_submissions_count` INT NOT NULL DEFAULT 0,
  `form_status` TINYINT(1) NOT NULL DEFAULT 1,
  `form_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `form_updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`form_site_id`),
  INDEX (`form_uid`),
  INDEX (`form_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

@mysqli_query($connection, $createTableSql);

$checkHeightCol = mysqli_query($connection, "SHOW COLUMNS FROM `{$tableName}` LIKE 'form_input_height'");
if ($checkHeightCol && mysqli_num_rows($checkHeightCol) === 0) {
    @mysqli_query($connection, "ALTER TABLE `{$tableName}` ADD `form_input_height` VARCHAR(20) DEFAULT '44px' AFTER `form_bg_mode`");
}

$checkHideHeadingCol = mysqli_query($connection, "SHOW COLUMNS FROM `{$tableName}` LIKE 'form_hide_heading'");
if ($checkHideHeadingCol && mysqli_num_rows($checkHideHeadingCol) === 0) {
    @mysqli_query($connection, "ALTER TABLE `{$tableName}` ADD `form_hide_heading` TINYINT(1) DEFAULT 0 AFTER `form_heading`");
}

$checkHideSubheadCol = mysqli_query($connection, "SHOW COLUMNS FROM `{$tableName}` LIKE 'form_hide_subheading'");
if ($checkHideSubheadCol && mysqli_num_rows($checkHideSubheadCol) === 0) {
    @mysqli_query($connection, "ALTER TABLE `{$tableName}` ADD `form_hide_subheading` TINYINT(1) DEFAULT 0 AFTER `form_subheading`");
}

$checkEmailToCol = mysqli_query($connection, "SHOW COLUMNS FROM `{$tableName}` LIKE 'form_email_to'");
if ($checkEmailToCol && mysqli_num_rows($checkEmailToCol) === 0) {
    @mysqli_query($connection, "ALTER TABLE `{$tableName}` ADD `form_email_to` VARCHAR(500) DEFAULT NULL AFTER `form_redirect_url`");
}

$checkEmailCcCol = mysqli_query($connection, "SHOW COLUMNS FROM `{$tableName}` LIKE 'form_email_cc'");
if ($checkEmailCcCol && mysqli_num_rows($checkEmailCcCol) === 0) {
    @mysqli_query($connection, "ALTER TABLE `{$tableName}` ADD `form_email_cc` VARCHAR(500) DEFAULT NULL AFTER `form_email_to`");
}

$checkEmailBccCol = mysqli_query($connection, "SHOW COLUMNS FROM `{$tableName}` LIKE 'form_email_bcc'");
if ($checkEmailBccCol && mysqli_num_rows($checkEmailBccCol) === 0) {
    @mysqli_query($connection, "ALTER TABLE `{$tableName}` ADD `form_email_bcc` VARCHAR(500) DEFAULT NULL AFTER `form_email_cc`");
}

if (!defined('LOGIN_CHECK')) {
    $loginCheckPath = (defined('ROOT') ? ROOT : (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3))) . '/lib/modules/isLogin.php';
    if (file_exists($loginCheckPath)) {
        define('LOGIN_CHECK', $loginCheckPath);
    }
}

if (defined('LOGIN_CHECK') && file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

// --------------------------------------------------------------------------
// Helper: Default Form Fields Definition
// --------------------------------------------------------------------------
function getDefaultFormFields()
{
    return [
        [
            'field_key'   => 'full_name',
            'type'        => 'text',
            'label'       => 'Full Name',
            'placeholder' => 'Enter your full name',
            'required'    => true,
            'is_system'   => true,
            'enabled'     => true,
            'col_span'    => 12
        ],
        [
            'field_key'   => 'mobile',
            'type'        => 'tel',
            'label'       => 'Mobile Number',
            'placeholder' => 'Enter 10-digit mobile number',
            'required'    => true,
            'is_system'   => true,
            'enabled'     => true,
            'col_span'    => 12
        ],
        [
            'field_key'   => 'email',
            'type'        => 'email',
            'label'       => 'Email Address',
            'placeholder' => 'name@example.com',
            'required'    => false,
            'is_system'   => false,
            'enabled'     => true,
            'col_span'    => 12
        ],
        [
            'field_key'   => 'destination',
            'type'        => 'text',
            'label'       => 'Tour Destination',
            'placeholder' => 'e.g. Kashmir, Maldives, Dubai',
            'required'    => false,
            'is_system'   => false,
            'enabled'     => false,
            'col_span'    => 12
        ],
        [
            'field_key'   => 'travel_date',
            'type'        => 'date',
            'label'       => 'Date of Travel',
            'placeholder' => 'Select tentative travel date',
            'required'    => false,
            'is_system'   => false,
            'enabled'     => false,
            'col_span'    => 6
        ],
        [
            'field_key'   => 'duration',
            'type'        => 'text',
            'label'       => 'Duration / Nights',
            'placeholder' => 'e.g. 5 Days / 4 Nights',
            'required'    => false,
            'is_system'   => false,
            'enabled'     => false,
            'col_span'    => 6
        ],
        [
            'field_key'   => 'persons',
            'type'        => 'number',
            'label'       => 'Total Travelers / Persons',
            'placeholder' => 'e.g. 2',
            'required'    => false,
            'is_system'   => false,
            'enabled'     => false,
            'col_span'    => 6
        ],
        [
            'field_key'   => 'adults',
            'type'        => 'number',
            'label'       => 'Adults (12+ yrs)',
            'placeholder' => '2',
            'required'    => false,
            'is_system'   => false,
            'enabled'     => false,
            'col_span'    => 6
        ],
        [
            'field_key'   => 'children',
            'type'        => 'number',
            'label'       => 'Children (2-12 yrs)',
            'placeholder' => '0',
            'required'    => false,
            'is_system'   => false,
            'enabled'     => false,
            'col_span'    => 6
        ],
        [
            'field_key'   => 'infants',
            'type'        => 'number',
            'label'       => 'Infants (< 2 yrs)',
            'placeholder' => '0',
            'required'    => false,
            'is_system'   => false,
            'enabled'     => false,
            'col_span'    => 6
        ],
        [
            'field_key'   => 'pickup_city',
            'type'        => 'text',
            'label'       => 'Departure / Pickup City',
            'placeholder' => 'e.g. Mumbai, Delhi, Bengaluru',
            'required'    => false,
            'is_system'   => false,
            'enabled'     => false,
            'col_span'    => 12
        ],
        [
            'field_key'   => 'budget',
            'type'        => 'text',
            'label'       => 'Approx. Budget per Person',
            'placeholder' => 'e.g. ₹25,000 - ₹40,000',
            'required'    => false,
            'is_system'   => false,
            'enabled'     => false,
            'col_span'    => 6
        ],
        [
            'field_key'   => 'hotel_category',
            'type'        => 'select',
            'label'       => 'Hotel Preference',
            'placeholder' => 'Select star rating',
            'options'     => ['3 Star Standard', '4 Star Deluxe', '5 Star Luxury', 'Boutique / Resort'],
            'required'    => false,
            'is_system'   => false,
            'enabled'     => false,
            'col_span'    => 6
        ],
        [
            'field_key'   => 'message',
            'type'        => 'textarea',
            'label'       => 'Special Requirements / Notes',
            'placeholder' => 'Any specific requests or preferred sightseeing...',
            'required'    => false,
            'is_system'   => false,
            'enabled'     => true,
            'col_span'    => 12
        ]
    ];
}

/**
 * Merge existing fields with all default Aleya inputs to ensure default inputs are never missing
 */
function mergeDefaultFormFields($fields)
{
    $defaults = getDefaultFormFields();
    if (empty($fields) || !is_array($fields)) {
        return $defaults;
    }
    $map = [];
    foreach ($fields as $f) {
        if (!empty($f['field_key'])) {
            $map[$f['field_key']] = $f;
        }
    }
    $merged = [];
    foreach ($defaults as $def) {
        $k = $def['field_key'];
        if (isset($map[$k])) {
            $item = $map[$k];
            if (!isset($item['enabled'])) {
                $item['enabled'] = $def['enabled'];
            }
            $merged[] = $item;
            unset($map[$k]);
        } else {
            $merged[] = $def;
        }
    }
    // Append any custom fields
    foreach ($map as $custom) {
        $merged[] = $custom;
    }
    return $merged;
}

/**
 * Helper: Retrieve Primary Admin Alert Email from site_configuration or system constants
 */
function getPrimaryAdminAlertEmail($connection, $siteId)
{
    $table = (defined('TABLE_PREFIX') && !empty(TABLE_PREFIX)) ? TABLE_PREFIX . 'site_configuration' : 'site_configuration';
    $cfgMap = [];
    try {
        $cfgRes = mysqli_query($connection, "
            SELECT site_configuration_key, site_configuration_value 
            FROM {$table} 
            WHERE site_configuration_site_id = {$siteId}
              AND site_configuration_key IN ('notification_admin_email', 'admin_email', 'notification_enquiry_email', 'enquiry_email')
        ");
        if ($cfgRes) {
            while ($r = mysqli_fetch_assoc($cfgRes)) {
                $cfgMap[$r['site_configuration_key']] = $r['site_configuration_value'];
            }
        }
    } catch (\Throwable $e) {
        $cfgMap = [];
    }

    $primaryEmail = $cfgMap['notification_admin_email']
        ?? ($cfgMap['admin_email']
            ?? ($cfgMap['notification_enquiry_email']
                ?? ($cfgMap['enquiry_email'] ?? '')));

    if (empty($primaryEmail)) {
        if (defined('ADMIN_EMAIL') && !empty(ADMIN_EMAIL)) {
            $primaryEmail = ADMIN_EMAIL;
        } elseif (defined('SITE_EMAIL') && !empty(SITE_EMAIL)) {
            $primaryEmail = SITE_EMAIL;
        }
    }
    return trim((string)$primaryEmail);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');

// Action: Check Slug Availability (supports both GET and POST)
if ($action === 'check_slug') {
    $rawSlug = trim($_GET['slug'] ?? ($postData['slug'] ?? ''));
    $formId = (int)($_GET['form_id'] ?? ($postData['form_id'] ?? 0));

    $slug = preg_replace('/[^a-z0-9_-]/', '', strtolower(str_replace(' ', '-', $rawSlug)));
    $slug = trim($slug, '-_');

    if (empty($slug)) {
        echo json_encode([
            'status'    => 'error',
            'available' => false,
            'msg'       => 'Please enter a valid slug (alphanumeric and hyphens only).'
        ]);
        exit;
    }

    $reserved = ['api', 'admin', 'login', 'dashboard', 'enquiry', 'checkout', 'payment', 'cart', 'form', 'web-form', 'new', 'edit', 'delete', 'update', 'assets', 'lib', 'router'];
    if (in_array($slug, $reserved, true)) {
        $candidate = $slug . '-form';
        echo json_encode([
            'status'     => 'success',
            'available'  => false,
            'slug'       => $slug,
            'suggestion' => $candidate,
            'msg'        => "The slug '{$slug}' is reserved by system routes."
        ]);
        exit;
    }

    $escSlug = mysqli_real_escape_string($connection, $slug);
    $excludeSql = ($formId > 0) ? " AND form_id != {$formId}" : "";

    $checkRes = mysqli_query($connection, "SELECT form_id FROM `{$tableName}` WHERE form_slug = '{$escSlug}' AND form_site_id = {$siteId} {$excludeSql} LIMIT 1");

    $currentHost = $_SERVER['HTTP_HOST'] ?? (defined('SITE_DOMAIN') ? SITE_DOMAIN : 'localhost');
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';

    if ($checkRes && mysqli_num_rows($checkRes) > 0) {
        $counter = 1;
        $candidate = $slug . '-1';
        while ($counter <= 15) {
            $candidate = $slug . '-' . $counter;
            $escCand = mysqli_real_escape_string($connection, $candidate);
            $cRes = mysqli_query($connection, "SELECT form_id FROM `{$tableName}` WHERE form_slug = '{$escCand}' AND form_site_id = {$siteId} {$excludeSql} LIMIT 1");
            if (!$cRes || mysqli_num_rows($cRes) === 0) {
                break;
            }
            $counter++;
        }

        echo json_encode([
            'status'     => 'success',
            'available'  => false,
            'slug'       => $slug,
            'suggestion' => $candidate,
            'preview_url' => "{$scheme}://{$currentHost}/form/{$slug}",
            'msg'        => "URL slug '/form/{$slug}' is already taken."
        ]);
    } else {
        echo json_encode([
            'status'     => 'success',
            'available'  => true,
            'slug'       => $slug,
            'preview_url' => "{$scheme}://{$currentHost}/form/{$slug}",
            'msg'        => "URL slug '/form/{$slug}' is available!"
        ]);
    }
    exit;
}

// --------------------------------------------------------------------------
// 1. GET Requests: List forms or Fetch Single Form
// --------------------------------------------------------------------------
if ($method === 'GET') {
    if ($action === 'get') {
        $formId = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
        $formUid = trim($_GET['form_uid'] ?? '');

        $whereClause = "";
        if ($formId > 0) {
            $whereClause = "form_id = {$formId} AND form_site_id = {$siteId}";
        } elseif (!empty($formUid)) {
            $escUid = mysqli_real_escape_string($connection, $formUid);
            $whereClause = "form_uid = '{$escUid}' AND form_site_id = {$siteId}";
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Form identifier is required.']);
            exit;
        }

        $res = mysqli_query($connection, "SELECT * FROM `{$tableName}` WHERE {$whereClause} LIMIT 1");
        if ($res && mysqli_num_rows($res) > 0) {
            $form = mysqli_fetch_assoc($res);
            $rawFields = json_decode($form['form_fields_json'], true) ?: [];
            $form['fields'] = mergeDefaultFormFields($rawFields);
            $form['form_input_height'] = !empty($form['form_input_height']) ? $form['form_input_height'] : '44px';
            $form['form_hide_heading'] = (int)($form['form_hide_heading'] ?? 0);
            $form['form_hide_subheading'] = (int)($form['form_hide_subheading'] ?? 0);
            $form['form_email_to'] = $form['form_email_to'] ?? '';
            $form['form_email_cc'] = $form['form_email_cc'] ?? '';
            $form['form_email_bcc'] = $form['form_email_bcc'] ?? '';
            $form['tracking'] = json_decode($form['form_tracking_json'], true) ?: [
                'source'   => 'landing_page',
                'medium'   => 'web_form',
                'campaign' => '',
                'subject'  => ''
            ];

            $currentHost = $_SERVER['HTTP_HOST'] ?? (defined('SITE_DOMAIN') ? SITE_DOMAIN : 'localhost');
            $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
            $displaySlug = !empty($form['form_slug']) ? $form['form_slug'] : $form['form_uid'];
            $form['public_url'] = "{$scheme}://{$currentHost}/form/{$displaySlug}";
            $form['iframe_snippet'] = '<iframe src="' . $form['public_url'] . '" width="100%" height="560" frameborder="0" style="border:none;overflow:hidden;max-width:100%;border-radius:12px;" loading="lazy"></iframe>';
            $form['primary_admin_email'] = getPrimaryAdminAlertEmail($connection, $siteId);

            echo json_encode(['status' => 'success', 'data' => $form]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Web form not found.']);
        }
        exit;
    }

    // Default: List All Forms
    $search = trim($_GET['search'] ?? '');
    $searchSql = "";
    if (!empty($search)) {
        $escSearch = mysqli_real_escape_string($connection, $search);
        $searchSql = " AND (form_title LIKE '%{$escSearch}%' OR form_heading LIKE '%{$escSearch}%' OR form_slug LIKE '%{$escSearch}%')";
    }

    $res = mysqli_query($connection, "SELECT * FROM `{$tableName}` WHERE form_site_id = {$siteId} {$searchSql} ORDER BY form_id DESC");
    $forms = [];
    $totalSubmissions = 0;
    $activeCount = 0;

    $currentHost = $_SERVER['HTTP_HOST'] ?? (defined('SITE_DOMAIN') ? SITE_DOMAIN : 'localhost');
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $fields = json_decode($row['form_fields_json'], true) ?: [];
            $enabledFieldsCount = 0;
            foreach ($fields as $f) {
                if (!empty($f['enabled'])) $enabledFieldsCount++;
            }

            $totalSubmissions += (int)$row['form_submissions_count'];
            if ((int)$row['form_status'] === 1) {
                $activeCount++;
            }

            $displaySlug = !empty($row['form_slug']) ? $row['form_slug'] : $row['form_uid'];
            $publicUrl = "{$scheme}://{$currentHost}/form/{$displaySlug}";
            $iframeSnippet = '<iframe src="' . $publicUrl . '" width="100%" height="560" frameborder="0" style="border:none;overflow:hidden;max-width:100%;border-radius:12px;" loading="lazy"></iframe>';

            $forms[] = [
                'form_id'                => (int)$row['form_id'],
                'form_uid'               => $row['form_uid'],
                'form_title'             => $row['form_title'],
                'form_slug'              => $row['form_slug'],
                'form_heading'           => $row['form_heading'],
                'form_hide_heading'      => (int)($row['form_hide_heading'] ?? 0),
                'form_subheading'        => $row['form_subheading'],
                'form_hide_subheading'   => (int)($row['form_hide_subheading'] ?? 0),
                'form_submit_btn_text'   => $row['form_submit_btn_text'],
                'form_theme_color'       => $row['form_theme_color'],
                'form_bg_mode'           => $row['form_bg_mode'],
                'form_redirect_url'      => $row['form_redirect_url'],
                'form_email_to'          => $row['form_email_to'] ?? '',
                'form_email_cc'          => $row['form_email_cc'] ?? '',
                'form_email_bcc'         => $row['form_email_bcc'] ?? '',
                'form_submissions_count' => (int)$row['form_submissions_count'],
                'form_status'            => (int)$row['form_status'],
                'form_created_at'        => $row['form_created_at'],
                'fields_count'           => $enabledFieldsCount,
                'public_url'             => $publicUrl,
                'iframe_snippet'         => $iframeSnippet
            ];
        }
    }

    $primaryAdminEmail = getPrimaryAdminAlertEmail($connection, $siteId);
    echo json_encode([
        'status' => 'success',
        'data' => [
            'forms'               => $forms,
            'total_forms'         => count($forms),
            'active_forms'        => $activeCount,
            'total_submissions'   => $totalSubmissions,
            'suggested_fields'    => getDefaultFormFields(),
            'primary_admin_email' => $primaryAdminEmail
        ],
        'primary_admin_email' => $primaryAdminEmail
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Save, Toggle, Delete, Duplicate
// --------------------------------------------------------------------------
$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true) ?? [];
$postData = !empty($_POST) ? $_POST : $jsonData;

$action = trim($postData['action'] ?? ($action ?? 'save'));

// Action: Toggle Form Status
if ($action === 'toggle') {
    $formId = (int)($postData['form_id'] ?? 0);
    $status = isset($postData['status']) ? (int)$postData['status'] : 1;

    if ($formId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Invalid form identifier.']);
        exit;
    }

    $updateSql = "UPDATE `{$tableName}` SET form_status = {$status} WHERE form_id = {$formId} AND form_site_id = {$siteId}";
    if (mysqli_query($connection, $updateSql)) {
        echo json_encode(['status' => 'success', 'msg' => 'Form status updated successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => 'Failed to update form status.']);
    }
    exit;
}

// Action: Delete Form
if ($action === 'delete') {
    $formId = (int)($postData['form_id'] ?? 0);
    if ($formId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Invalid form identifier.']);
        exit;
    }

    $delSql = "DELETE FROM `{$tableName}` WHERE form_id = {$formId} AND form_site_id = {$siteId}";
    if (mysqli_query($connection, $delSql)) {
        echo json_encode(['status' => 'success', 'msg' => 'Web form deleted successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => 'Failed to delete web form.']);
    }
    exit;
}

// Action: Duplicate Form
if ($action === 'duplicate') {
    $formId = (int)($postData['form_id'] ?? 0);
    if ($formId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Invalid form identifier.']);
        exit;
    }

    $srcRes = mysqli_query($connection, "SELECT * FROM `{$tableName}` WHERE form_id = {$formId} AND form_site_id = {$siteId} LIMIT 1");
    if (!$srcRes || mysqli_num_rows($srcRes) === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'msg' => 'Original form not found.']);
        exit;
    }

    $src = mysqli_fetch_assoc($srcRes);
    $newUid = 'wf_' . substr(md5(uniqid((string)mt_rand(), true)), 0, 14);
    $newTitle = 'Copy of ' . $src['form_title'];
    $newSlug = preg_replace('/[^a-z0-9_-]/', '', strtolower(str_replace(' ', '-', $newTitle))) . '-' . substr(md5($newUid), 0, 4);

    $escNewTitle = mysqli_real_escape_string($connection, $newTitle);
    $escNewSlug  = mysqli_real_escape_string($connection, $newSlug);
    $escHeading  = mysqli_real_escape_string($connection, $src['form_heading']);
    $hideHeading = (int)($src['form_hide_heading'] ?? 0);
    $escSubhead  = mysqli_real_escape_string($connection, $src['form_subheading']);
    $hideSubhead = (int)($src['form_hide_subheading'] ?? 0);
    $escBtnText  = mysqli_real_escape_string($connection, $src['form_submit_btn_text']);
    $escColor    = mysqli_real_escape_string($connection, $src['form_theme_color']);
    $escBgMode   = mysqli_real_escape_string($connection, $src['form_bg_mode']);
    $escHeight   = mysqli_real_escape_string($connection, !empty($src['form_input_height']) ? $src['form_input_height'] : '44px');
    $escFields   = mysqli_real_escape_string($connection, $src['form_fields_json']);
    $escTracking = mysqli_real_escape_string($connection, $src['form_tracking_json']);
    $escRedirect = mysqli_real_escape_string($connection, $src['form_redirect_url']);
    $escEmailTo  = mysqli_real_escape_string($connection, $src['form_email_to'] ?? '');
    $escEmailCc  = mysqli_real_escape_string($connection, $src['form_email_cc'] ?? '');
    $escEmailBcc = mysqli_real_escape_string($connection, $src['form_email_bcc'] ?? '');
    $escSuccess  = mysqli_real_escape_string($connection, $src['form_success_message']);

    $insertSql = "INSERT INTO `{$tableName}` 
        (form_site_id, form_uid, form_title, form_slug, form_heading, form_hide_heading, form_subheading, form_hide_subheading, form_submit_btn_text, form_theme_color, form_bg_mode, form_input_height, form_fields_json, form_tracking_json, form_redirect_url, form_email_to, form_email_cc, form_email_bcc, form_success_message, form_submissions_count, form_status)
        VALUES 
        ({$siteId}, '{$newUid}', '{$escNewTitle}', '{$escNewSlug}', '{$escHeading}', {$hideHeading}, '{$escSubhead}', {$hideSubhead}, '{$escBtnText}', '{$escColor}', '{$escBgMode}', '{$escHeight}', '{$escFields}', '{$escTracking}', '{$escRedirect}', '{$escEmailTo}', '{$escEmailCc}', '{$escEmailBcc}', '{$escSuccess}', 0, 1)";

    if (mysqli_query($connection, $insertSql)) {
        $newId = mysqli_insert_id($connection);
        echo json_encode([
            'status'  => 'success',
            'msg'     => 'Form duplicated successfully.',
            'form_id' => $newId,
            'form_uid' => $newUid
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => 'Failed to duplicate form.']);
    }
    exit;
}

// Action: Save / Update Form
if ($action === 'save') {
    $formId = (int)($postData['form_id'] ?? 0);
    $title = trim($postData['form_title'] ?? '');

    if (empty($title)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Form title is required.']);
        exit;
    }

    $heading     = trim($postData['form_heading'] ?? 'Get a Free Quote');
    $hideHeading = !empty($postData['form_hide_heading']) ? 1 : 0;
    $subheading  = trim($postData['form_subheading'] ?? 'Plan your dream vacation with our travel specialists');
    $hideSubhead = !empty($postData['form_hide_subheading']) ? 1 : 0;
    $btnText    = trim($postData['form_submit_btn_text'] ?? 'Get Free Quote');
    $themeColor  = trim($postData['form_theme_color'] ?? '#4f46e5');
    $bgMode      = in_array($postData['form_bg_mode'] ?? 'card', ['card', 'transparent']) ? $postData['form_bg_mode'] : 'card';
    $inputHeight = trim($postData['form_input_height'] ?? '44px');
    if (!preg_match('/^[0-9]+(px|rem|em)?$/', $inputHeight)) {
        $inputHeight = '44px';
    }
    if (is_numeric($inputHeight)) {
        $inputHeight .= 'px';
    }
    $redirect    = trim($postData['form_redirect_url'] ?? '');
    $emailTo     = trim($postData['form_email_to'] ?? '');
    $emailCc     = trim($postData['form_email_cc'] ?? '');
    $emailBcc    = trim($postData['form_email_bcc'] ?? '');
    $successMsg  = trim($postData['form_success_message'] ?? 'Thank you! We have received your inquiry and will contact you shortly.');

    // If emailTo is empty for a newly created form, default to Primary Admin Alert Email
    if (empty($emailTo) && $formId <= 0) {
        $emailTo = getPrimaryAdminAlertEmail($connection, $siteId);
    }

    // Process fields JSON & ENFORCE required defaults (Full Name & Mobile)
    $fields = $postData['form_fields'] ?? [];
    if (is_string($fields)) {
        $fields = json_decode($fields, true) ?: [];
    }

    $fields = mergeDefaultFormFields($fields);

    // Ensure full_name and mobile ALWAYS exist and are required
    $hasFullName = false;
    $hasMobile = false;

    foreach ($fields as &$f) {
        if (($f['field_key'] ?? '') === 'full_name') {
            $hasFullName = true;
            $f['required'] = true;
            $f['enabled'] = true;
            $f['is_system'] = true;
        }
        if (($f['field_key'] ?? '') === 'mobile') {
            $hasMobile = true;
            $f['required'] = true;
            $f['enabled'] = true;
            $f['is_system'] = true;
        }
    }
    unset($f);

    if (!$hasFullName) {
        array_unshift($fields, [
            'field_key'   => 'full_name',
            'type'        => 'text',
            'label'       => 'Full Name',
            'placeholder' => 'Enter your full name',
            'required'    => true,
            'is_system'   => true,
            'enabled'     => true,
            'col_span'    => 12
        ]);
    }
    if (!$hasMobile) {
        $insertIdx = $hasFullName ? 1 : 0;
        array_splice($fields, $insertIdx, 0, [[
            'field_key'   => 'mobile',
            'type'        => 'tel',
            'label'       => 'Mobile Number',
            'placeholder' => 'Enter 10-digit mobile number',
            'required'    => true,
            'is_system'   => true,
            'enabled'     => true,
            'col_span'    => 12
        ]]);
    }

    $fieldsJson = json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // Tracking metadata
    $tracking = [
        'source'   => trim($postData['tracking_source'] ?? ($postData['tracking']['source'] ?? 'landing_page')),
        'medium'   => trim($postData['tracking_medium'] ?? ($postData['tracking']['medium'] ?? 'web_form')),
        'campaign' => trim($postData['tracking_campaign'] ?? ($postData['tracking']['campaign'] ?? '')),
        'subject'  => trim($postData['tracking_subject'] ?? ($postData['tracking']['subject'] ?? ''))
    ];
    $trackingJson = json_encode($tracking, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // Slug generation
    $slug = trim($postData['form_slug'] ?? '');
    if (empty($slug)) {
        $slug = preg_replace('/[^a-z0-9_-]/', '', strtolower(str_replace(' ', '-', $title)));
    }

    $escTitle    = mysqli_real_escape_string($connection, $title);
    $escSlug     = mysqli_real_escape_string($connection, $slug);
    $escHeading  = mysqli_real_escape_string($connection, $heading);
    $escSubhead  = mysqli_real_escape_string($connection, $subheading);
    $escBtnText  = mysqli_real_escape_string($connection, $btnText);
    $escColor    = mysqli_real_escape_string($connection, $themeColor);
    $escBgMode   = mysqli_real_escape_string($connection, $bgMode);
    $escHeight   = mysqli_real_escape_string($connection, $inputHeight);
    $escFields   = mysqli_real_escape_string($connection, $fieldsJson);
    $escTracking = mysqli_real_escape_string($connection, $trackingJson);
    $escRedirect = mysqli_real_escape_string($connection, $redirect);
    $escEmailTo  = mysqli_real_escape_string($connection, $emailTo);
    $escEmailCc  = mysqli_real_escape_string($connection, $emailCc);
    $escEmailBcc = mysqli_real_escape_string($connection, $emailBcc);
    $escSuccess  = mysqli_real_escape_string($connection, $successMsg);

    if ($formId > 0) {
        // Update existing form
        $updateSql = "UPDATE `{$tableName}` SET 
            form_title = '{$escTitle}',
            form_slug = '{$escSlug}',
            form_heading = '{$escHeading}',
            form_hide_heading = {$hideHeading},
            form_subheading = '{$escSubhead}',
            form_hide_subheading = {$hideSubhead},
            form_submit_btn_text = '{$escBtnText}',
            form_theme_color = '{$escColor}',
            form_bg_mode = '{$escBgMode}',
            form_input_height = '{$escHeight}',
            form_fields_json = '{$escFields}',
            form_tracking_json = '{$escTracking}',
            form_redirect_url = '{$escRedirect}',
            form_email_to = '{$escEmailTo}',
            form_email_cc = '{$escEmailCc}',
            form_email_bcc = '{$escEmailBcc}',
            form_success_message = '{$escSuccess}'
            WHERE form_id = {$formId} AND form_site_id = {$siteId}";

        if (mysqli_query($connection, $updateSql)) {
            // Retrieve current form_uid
            $uidRes = mysqli_query($connection, "SELECT form_uid FROM `{$tableName}` WHERE form_id = {$formId} LIMIT 1");
            $formUid = ($uidRes && $r = mysqli_fetch_assoc($uidRes)) ? $r['form_uid'] : '';

            echo json_encode([
                'status'   => 'success',
                'msg'      => 'Web form updated successfully.',
                'form_id'  => $formId,
                'form_uid' => $formUid
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Failed to update web form: ' . mysqli_error($connection)]);
        }
    } else {
        // Create new form
        $formUid = 'wf_' . substr(md5(uniqid((string)mt_rand(), true)), 0, 14);

        $insertSql = "INSERT INTO `{$tableName}` 
            (form_site_id, form_uid, form_title, form_slug, form_heading, form_hide_heading, form_subheading, form_hide_subheading, form_submit_btn_text, form_theme_color, form_bg_mode, form_input_height, form_fields_json, form_tracking_json, form_redirect_url, form_email_to, form_email_cc, form_email_bcc, form_success_message, form_submissions_count, form_status)
            VALUES 
            ({$siteId}, '{$formUid}', '{$escTitle}', '{$escSlug}', '{$escHeading}', {$hideHeading}, '{$escSubhead}', {$hideSubhead}, '{$escBtnText}', '{$escColor}', '{$escBgMode}', '{$escHeight}', '{$escFields}', '{$escTracking}', '{$escRedirect}', '{$escEmailTo}', '{$escEmailCc}', '{$escEmailBcc}', '{$escSuccess}', 0, 1)";

        if (mysqli_query($connection, $insertSql)) {
            $newId = mysqli_insert_id($connection);
            echo json_encode([
                'status'   => 'success',
                'msg'      => 'Web form created successfully.',
                'form_id'  => $newId,
                'form_uid' => $formUid
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Failed to create web form: ' . mysqli_error($connection)]);
        }
    }
    exit;
}

http_response_code(400);
echo json_encode(['status' => 'error', 'msg' => 'Unrecognized action parameter.']);
exit;
