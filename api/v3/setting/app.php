<?php

/**
 * V3 App & Feature Configuration API Endpoint
 * Path: /api/v3/setting/app.php
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/lib/configuration/index.php')) {
        require_once $docRoot . '/lib/configuration/index.php';
    }
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') && (int)SITE_ID > 0 ? (int)SITE_ID : (isset($_SESSION['site_id']) ? (int)$_SESSION['site_id'] : 0);

if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    $escapedHost = mysqli_real_escape_string($connection, $host);
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
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

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Supported company natures and titles
$natureTitles = [
    'travel_agency' => 'Travel Agency & Tour Operator',
    'transporter'   => 'Transporter & Fleet Operator',
    'dmc'           => 'Destination Management Company (DMC)',
    'hotel_resort'  => 'Hotel, Resort & Homestay',
    'spa_wellness'  => 'Spa, Wellness & Retreat Center',
    'general'       => 'General Business & Custom Services'
];

$managedKeys = [
    'company_nature',
    'company_nature_title',
    'currency_default',
    'currency_symbol',
    'tax_mode',
    'quotation_validity_days',
    'advance_payment_percent',
    'lead_assignment_mode',
    'lead_default_followup_hours',
    'lead_sla_inactivity_hours',
    'proposal_delivery_channel',
    'quotation_price_display',
    'transporter_tariff_mode',
    'transporter_toll_policy',
    'transporter_parking_policy',
    'transporter_garage_policy',
    'transporter_driver_allowance',
    'transporter_billing_terms',
    'lead_view_show_payment_link',
    'lead_view_show_record_payment',
    'lead_view_show_convert_sale',
    'lead_view_show_call_button',
    'lead_view_show_whatsapp_button',
    'lead_view_show_send_itinerary',
    'lead_view_show_send_quotation'
];

// --------------------------------------------------------------------------
// 1. GET: Fetch Current App Configurations
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $keyListStr = "'" . implode("','", $managedKeys) . "'";
    $sql = "SELECT site_configuration_key, site_configuration_value 
            FROM {$tblPrefix}site_configuration 
            WHERE site_configuration_site_id = {$siteId} 
              AND site_configuration_key IN ({$keyListStr}) 
              AND site_configuration_status = 1";

    $res = mysqli_query($connection, $sql);
    $data = [];

    // Sensible defaults
    $data['company_nature']               = 'travel_agency';
    $data['company_nature_title']         = $natureTitles['travel_agency'];
    $data['currency_default']             = 'INR';
    $data['currency_symbol']              = '₹';
    $data['tax_mode']                     = 'exclusive';
    $data['quotation_validity_days']      = '7';
    $data['advance_payment_percent']      = '25';
    $data['lead_assignment_mode']         = 'manual';
    $data['lead_default_followup_hours']  = '24';
    $data['lead_sla_inactivity_hours']    = '24';
    $data['proposal_delivery_channel']    = 'whatsapp';
    $data['quotation_price_display']      = 'lump_sum';
    $data['transporter_tariff_mode']      = 'per_km';
    $data['transporter_toll_policy']      = 'included';
    $data['transporter_parking_policy']   = 'extra';
    $data['transporter_garage_policy']    = 'included';
    $data['transporter_driver_allowance'] = 'included';
    $data['lead_view_show_payment_link']  = '1';
    $data['lead_view_show_record_payment'] = '1';
    $data['lead_view_show_convert_sale']  = '1';
    $data['lead_view_show_call_button']   = '1';
    $data['lead_view_show_whatsapp_button'] = '1';
    $data['lead_view_show_send_itinerary'] = '1';
    $data['lead_view_show_send_quotation'] = '1';

    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $data[$row['site_configuration_key']] = $row['site_configuration_value'];
        }
    }

    if (empty($data['company_nature'])) {
        $data['company_nature'] = 'travel_agency';
    }
    if (empty($data['company_nature_title'])) {
        $data['company_nature_title'] = $natureTitles[$data['company_nature']] ?? 'Travel Agency & Tour Operator';
    }

    // Site package identification
    $packageId = defined('SITE_PACKAGE_ID') ? (int)SITE_PACKAGE_ID : (isset($site_package_id) ? (int)$site_package_id : 7);
    if ($packageId <= 0 && $siteId > 0 && isset($connection)) {
        $pkgQ = mysqli_query($connection, "SELECT site_package_id FROM {$tblPrefix}site WHERE site_id = {$siteId} LIMIT 1");
        if ($pkgQ && mysqli_num_rows($pkgQ) > 0) {
            $pkgRow = mysqli_fetch_assoc($pkgQ);
            $packageId = (int)($pkgRow['site_package_id'] ?? 7);
        }
    }
    $data['site_package_id'] = $packageId;
    $data['is_package_six']   = ($packageId === 6);

    // Computed effective lead proposal action based on nature
    $data['effective_lead_action'] = ($data['company_nature'] === 'transporter') ? 'quotation' : 'itinerary';

    echo json_encode([
        'status' => 'success',
        'data'   => $data
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Save App Configurations
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $nature = strtolower(trim($data['company_nature'] ?? 'travel_agency'));
    if (!array_key_exists($nature, $natureTitles)) {
        $nature = 'travel_agency';
    }

    $natureTitle = $natureTitles[$nature];

    $currencyDefault = strtoupper(trim($data['currency_default'] ?? 'INR'));
    $validCurrencies = ['INR', 'USD', 'EUR', 'AED', 'GBP', 'THB', 'SGD', 'CAD', 'AUD'];
    if (!in_array($currencyDefault, $validCurrencies)) {
        $currencyDefault = 'INR';
    }

    $currencySymbol = trim($data['currency_symbol'] ?? '');
    if ($currencySymbol === '') {
        $currencySymbols = [
            'INR' => '₹', 'USD' => '$', 'EUR' => '€', 'AED' => 'د.إ',
            'GBP' => '£', 'THB' => '฿', 'SGD' => 'S$', 'CAD' => 'C$', 'AUD' => 'A$'
        ];
        $currencySymbol = $currencySymbols[$currencyDefault] ?? '₹';
    }

    $taxMode = strtolower(trim($data['tax_mode'] ?? 'exclusive'));
    if (!in_array($taxMode, ['inclusive', 'exclusive', 'exempt'])) {
        $taxMode = 'exclusive';
    }

    $quotationValidityDays = (int)($data['quotation_validity_days'] ?? 7);
    if ($quotationValidityDays <= 0) $quotationValidityDays = 7;

    $advancePaymentPercent = (int)($data['advance_payment_percent'] ?? 25);
    if ($advancePaymentPercent <= 0 || $advancePaymentPercent > 100) $advancePaymentPercent = 25;

    $leadAssignmentMode = strtolower(trim($data['lead_assignment_mode'] ?? 'manual'));
    if (!in_array($leadAssignmentMode, ['manual', 'round_robin', 'claim'])) {
        $leadAssignmentMode = 'manual';
    }

    $leadDefaultFollowupHours = (int)($data['lead_default_followup_hours'] ?? 24);
    if ($leadDefaultFollowupHours <= 0) $leadDefaultFollowupHours = 24;

    $leadSlaInactivityHours = (int)($data['lead_sla_inactivity_hours'] ?? 24);

    $proposalDeliveryChannel = strtolower(trim($data['proposal_delivery_channel'] ?? 'whatsapp'));
    if (!in_array($proposalDeliveryChannel, ['whatsapp', 'email', 'web_link'])) {
        $proposalDeliveryChannel = 'whatsapp';
    }

    $quotationPriceDisplay = strtolower(trim($data['quotation_price_display'] ?? 'lump_sum'));
    if (!in_array($quotationPriceDisplay, ['lump_sum', 'itemized'])) {
        $quotationPriceDisplay = 'lump_sum';
    }

    $transporterTariffMode = strtolower(trim($data['transporter_tariff_mode'] ?? 'per_km'));
    if (!in_array($transporterTariffMode, ['per_km', 'fixed_route', 'daily_hire'])) {
        $transporterTariffMode = 'per_km';
    }

    $transporterTollPolicy = strtolower(trim($data['transporter_toll_policy'] ?? 'included'));
    if (!in_array($transporterTollPolicy, ['included', 'extra'])) {
        $transporterTollPolicy = 'included';
    }

    $transporterParkingPolicy = strtolower(trim($data['transporter_parking_policy'] ?? 'extra'));
    if (!in_array($transporterParkingPolicy, ['extra', 'included'])) {
        $transporterParkingPolicy = 'extra';
    }

    $transporterGaragePolicy = strtolower(trim($data['transporter_garage_policy'] ?? 'included'));
    if (!in_array($transporterGaragePolicy, ['included', 'pickup_point'])) {
        $transporterGaragePolicy = 'included';
    }

    $transporterDriverAllowance = strtolower(trim($data['transporter_driver_allowance'] ?? 'included'));
    if (!in_array($transporterDriverAllowance, ['included', 'extra'])) {
        $transporterDriverAllowance = 'included';
    }

    // Lead View Visibility Toggles
    $leadViewPaymentLink    = (!empty($data['lead_view_show_payment_link']) && $data['lead_view_show_payment_link'] !== '0') ? '1' : '0';
    $leadViewRecordPayment  = (!empty($data['lead_view_show_record_payment']) && $data['lead_view_show_record_payment'] !== '0') ? '1' : '0';
    $leadViewConvertSale    = (!empty($data['lead_view_show_convert_sale']) && $data['lead_view_show_convert_sale'] !== '0') ? '1' : '0';
    $leadViewCallButton     = (!empty($data['lead_view_show_call_button']) && $data['lead_view_show_call_button'] !== '0') ? '1' : '0';
    $leadViewWhatsappButton = (!empty($data['lead_view_show_whatsapp_button']) && $data['lead_view_show_whatsapp_button'] !== '0') ? '1' : '0';
    $leadViewSendItinerary  = (!empty($data['lead_view_show_send_itinerary']) && $data['lead_view_show_send_itinerary'] !== '0') ? '1' : '0';
    $leadViewSendQuotation  = (!empty($data['lead_view_show_send_quotation']) && $data['lead_view_show_send_quotation'] !== '0') ? '1' : '0';

    $valuesToSave = [
        'company_nature'               => $nature,
        'company_nature_title'         => $natureTitle,
        'currency_default'             => $currencyDefault,
        'currency_symbol'              => $currencySymbol,
        'tax_mode'                     => $taxMode,
        'quotation_validity_days'      => (string)$quotationValidityDays,
        'advance_payment_percent'      => (string)$advancePaymentPercent,
        'lead_assignment_mode'         => $leadAssignmentMode,
        'lead_default_followup_hours'  => (string)$leadDefaultFollowupHours,
        'lead_sla_inactivity_hours'    => (string)$leadSlaInactivityHours,
        'proposal_delivery_channel'    => $proposalDeliveryChannel,
        'quotation_price_display'      => $quotationPriceDisplay,
        'transporter_tariff_mode'      => $transporterTariffMode,
        'transporter_toll_policy'      => $transporterTollPolicy,
        'transporter_parking_policy'   => $transporterParkingPolicy,
        'transporter_garage_policy'    => $transporterGaragePolicy,
        'transporter_driver_allowance' => $transporterDriverAllowance,
        'lead_view_show_payment_link'  => $leadViewPaymentLink,
        'lead_view_show_record_payment'=> $leadViewRecordPayment,
        'lead_view_show_convert_sale'  => $leadViewConvertSale,
        'lead_view_show_call_button'   => $leadViewCallButton,
        'lead_view_show_whatsapp_button'=> $leadViewWhatsappButton,
        'lead_view_show_send_itinerary'=> $leadViewSendItinerary,
        'lead_view_show_send_quotation'=> $leadViewSendQuotation
    ];

    mysqli_query($connection, "START TRANSACTION");

    $success = true;
    foreach ($valuesToSave as $cfgKey => $cfgVal) {
        $escVal = mysqli_real_escape_string($connection, (string)$cfgVal);
        $escKey = mysqli_real_escape_string($connection, (string)$cfgKey);

        $check = mysqli_query($connection, "SELECT site_configuration_id FROM {$tblPrefix}site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = '{$escKey}' LIMIT 1");
        if ($check && mysqli_num_rows($check) > 0) {
            $upd = mysqli_query($connection, "UPDATE {$tblPrefix}site_configuration SET site_configuration_value = '{$escVal}', site_configuration_status = 1 WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = '{$escKey}'");
            if (!$upd) $success = false;
        } else {
            $ins = mysqli_query($connection, "INSERT INTO {$tblPrefix}site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) VALUES ({$siteId}, '{$escKey}', '{$escVal}', 1)");
            if (!$ins) $success = false;
        }
    }

    if ($success) {
        mysqli_query($connection, "COMMIT");

        $effectiveAction = ($nature === 'transporter') ? 'quotation' : 'itinerary';

        echo json_encode([
            'status' => 'success',
            'msg'    => 'App and business configuration updated successfully.',
            'data'   => array_merge($valuesToSave, [
                'effective_lead_action' => $effectiveAction
            ])
        ]);
    } else {
        mysqli_query($connection, "ROLLBACK");
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Failed to update app configuration: ' . mysqli_error($connection)
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
