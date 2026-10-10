<?php
/**
 * V3 Transporter Quotation API
 * Path: /api/v3/transporters/quotation.php
 * Handles creation, retrieval, and WhatsApp formatting of transporter fleet proposals.
 */

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
@ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

$docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
if (!isset($connection)) {
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

if (!defined('LOGIN_CHECK')) {
    define('LOGIN_CHECK', $docRoot . '/lib/modules/isLogin.php');
}
if (file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : (int)($_SESSION['site_id'] ?? 0);
$userId    = defined('USER_ID')      ? (int) USER_ID : (int)($_SESSION['user_id'] ?? 0);

if ($siteId <= 0) {
    $siteId = 1;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

function getTransporterCompanyMeta($connection, $siteId, $tblPrefix) {
    $meta = [
        'name'    => 'Aleya Travel & Fleet Logistics',
        'mobile'  => '',
        'email'   => '',
        'address' => '',
        'domain'  => ''
    ];
    if (!$connection) return $meta;
    $sId = (int)$siteId;
    $clause = ($sId > 0) ? "WHERE site_configuration_site_id = {$sId}" : "";
    $res = @mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value FROM {$tblPrefix}site_configuration {$clause}");
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $k = $r['site_configuration_key'];
            $v = trim((string)$r['site_configuration_value']);
            if (empty($v)) continue;
            if ($k === 'company_name') $meta['name'] = $v;
            else if ($k === 'site_mobile' || $k === 'company_mobile' || $k === 'company_whatsapp_number') {
                if (empty($meta['mobile'])) $meta['mobile'] = $v;
            }
            else if ($k === 'site_email' || $k === 'company_email') {
                if (empty($meta['email'])) $meta['email'] = $v;
            }
            else if ($k === 'company_address') $meta['address'] = $v;
            else if ($k === 'company_site_domain') $meta['domain'] = $v;
        }
    }
    return $meta;
}

function buildTransporterProposalHtml($quoteId, $details, $company) {
    $cust = $details['customer'] ?? [];
    $trav = $details['travel'] ?? [];
    $veh  = $details['vehicle'] ?? [];
    $bill = $details['billing'] ?? [];
    $term = $details['terms'] ?? [];

    $custName    = htmlspecialchars($cust['name'] ?? 'Valued Customer');
    $custMobile  = htmlspecialchars($cust['mobile'] ?? '-');
    $custEmail   = htmlspecialchars($cust['email'] ?? '-');
    $custAddress = htmlspecialchars($cust['address'] ?? '-');

    $dest     = htmlspecialchars($trav['destination'] ?? 'Outstation Trip');
    $pickup   = htmlspecialchars($trav['pickup_location'] ?? '-');
    $drop     = htmlspecialchars($trav['drop_location'] ?? '-');
    $tDate    = htmlspecialchars($trav['travel_date'] ?? date('Y-m-d'));
    $duration = htmlspecialchars((string)($trav['duration_days'] ?? '1'));

    $vehTitle   = htmlspecialchars($veh['vehicle_title'] ?? 'Commercial Fleet Vehicle');
    $vehType    = htmlspecialchars(ucfirst($veh['vehicle_type'] ?? 'Sedan / SUV'));
    $vehSeating = htmlspecialchars((string)($veh['vehicle_seating'] ?? ''));

    $sym         = htmlspecialchars($bill['currency_symbol'] ?? '₹');
    $totalCost   = number_format((float)($bill['total_cost'] ?? 0), 2);
    $kmRunning   = htmlspecialchars((string)($bill['km_running'] ?? 250));
    $extraKm     = number_format((float)($bill['extra_km_charge'] ?? 0), 2);
    $advPercent  = (float)($bill['advance_percent'] ?? 25);
    $advAmount   = number_format((float)($bill['advance_amount'] ?? 0), 2);
    $balAmount   = number_format((float)($bill['balance_amount'] ?? 0), 2);

    $parking  = strtolower($term['parking_charges'] ?? 'no') === 'yes';
    $garage   = strtolower($term['garage_to_garage'] ?? 'yes') === 'yes';
    $toll     = strtolower($term['toll_tax'] ?? 'yes') === 'yes';
    $stateTax = strtolower($term['state_tax'] ?? 'yes') === 'yes';
    $driver   = strtolower($term['driver_allowance'] ?? 'yes') === 'yes';
    $customRules = is_array($term['custom_rules'] ?? null) ? $term['custom_rules'] : [];

    $garageText    = $garage ? 'Applicable' : 'Standard Base';
    $tollClass     = $toll ? 'term-badge-included' : 'term-badge-excluded';
    $tollText      = $toll ? 'Included' : 'Excluded';
    $stateTaxClass = $stateTax ? 'term-badge-included' : 'term-badge-excluded';
    $stateTaxText  = $stateTax ? 'Included' : 'Excluded';
    $driverClass   = $driver ? 'term-badge-included' : 'term-badge-excluded';
    $driverText    = $driver ? 'Included' : 'Excluded';
    $parkingClass  = $parking ? 'term-badge-included' : 'term-badge-excluded';
    $parkingText   = $parking ? 'Included' : 'Excluded (Guest Pays Direct)';

    $compName = htmlspecialchars($company['name'] ?? 'Aleya Fleet Logistics');
    $compPhone = htmlspecialchars($company['mobile'] ?? '');
    $compEmail = htmlspecialchars($company['email'] ?? '');
    $compDomain = htmlspecialchars($company['domain'] ?? '');
    $compAddress = htmlspecialchars($company['address'] ?? '');

    $quoteDisplayId = 'TQ-' . str_pad((string)$quoteId, 5, '0', STR_PAD_LEFT);
    $currentDate = date('d M, Y');

    $customRulesHtml = '';
    if (!empty($customRules)) {
        foreach ($customRules as $cr) {
            if (!empty($cr['enabled']) && !empty($cr['title'])) {
                $t = htmlspecialchars($cr['title']);
                $d = !empty($cr['description']) ? ' &mdash; <span style="color:#64748b;">' . htmlspecialchars($cr['description']) . '</span>' : '';
                $customRulesHtml .= '<div style="display:flex;align-items:flex-start;gap:8px;padding:6px 0;border-bottom:1px solid #f1f5f9;"><span style="color:#2563eb;font-weight:700;">&bull;</span><div><strong>' . $t . '</strong>' . $d . '</div></div>';
            }
        }
    }

    $customRulesBlock = '';
    if (!empty($customRulesHtml)) {
        $customRulesBlock = '<div style="margin-top:16px;padding-top:14px;border-top:1px dashed #cbd5e1;"><div style="font-size:11.5px;font-weight:700;text-transform:uppercase;color:#64748b;margin-bottom:8px;">Additional Commercial Terms &amp; Policies</div>' . $customRulesHtml . '</div>';
    }

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal {$quoteDisplayId} - {$custName}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 30px 16px;
            font-size: 13.5px;
            line-height: 1.5;
        }
        .proposal-wrap {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06), 0 8px 10px -6px rgba(0,0,0,0.04);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .proposal-header {
            padding: 32px 36px 24px 36px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            border-bottom: 3px solid #2563eb;
        }
        .company-brand-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin-bottom: 6px;
        }
        .company-meta-line {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 3px;
        }
        .proposal-ref-box {
            text-align: right;
        }
        .proposal-badge {
            display: inline-block;
            background: rgba(37, 99, 235, 0.25);
            color: #60a5fa;
            border: 1px solid rgba(96, 165, 250, 0.3);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .proposal-ref-number {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.01em;
        }
        .proposal-date-line {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }
        .proposal-body {
            padding: 32px 36px;
        }
        .grid-two-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px 20px;
        }
        .info-card-title {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .info-card-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 7px;
            font-size: 13px;
        }
        .info-card-row:last-child { margin-bottom: 0; }
        .info-label { color: #64748b; }
        .info-val { font-weight: 600; color: #0f172a; text-align: right; }
        .vehicle-hero-card {
            background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%);
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 18px 22px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .vehicle-meta h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 4px;
        }
        .vehicle-tag {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11.5px;
            font-weight: 600;
            margin-right: 6px;
        }
        .tariff-section {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .tariff-header {
            background: #f8fafc;
            padding: 12px 20px;
            font-size: 12.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
        }
        .tariff-table {
            width: 100%;
            border-collapse: collapse;
        }
        .tariff-table th, .tariff-table td {
            padding: 12px 20px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
        }
        .tariff-table th { background: #fafafa; font-weight: 600; color: #64748b; font-size: 12px; }
        .tariff-table td.val-highlight { font-weight: 700; color: #2563eb; }
        .tariff-total-row {
            background: #f8fafc;
            font-weight: 700;
        }
        .tariff-total-row td {
            font-size: 16px;
            color: #0f172a;
            border-top: 2px solid #cbd5e1;
            padding: 14px 20px;
        }
        .terms-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 10px;
        }
        .term-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 12.5px;
        }
        .term-badge-included {
            background: #dcfce7;
            color: #15803d;
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 11px;
        }
        .term-badge-excluded {
            background: #fee2e2;
            color: #b91c1c;
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 11px;
        }
        .proposal-footer-notes {
            margin-top: 24px;
            padding: 16px 20px;
            border-left: 3px solid #2563eb;
            background: #f8fafc;
            border-radius: 0 8px 8px 0;
            font-size: 12px;
            color: #475569;
        }
        .action-floating-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .btn-print-action {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print-action:hover { background: #1d4ed8; }
        @media print {
            body { background: #ffffff !important; padding: 0 !important; color: #000000 !important; }
            .proposal-wrap { box-shadow: none !important; border: none !important; max-width: 100% !important; border-radius: 0 !important; }
            .action-floating-bar { display: none !important; }
            .proposal-header { background: #1e293b !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .vehicle-hero-card { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { margin: 10mm; size: A4; }
        }
        @media (max-width: 768px) {
            .grid-two-cards, .terms-grid { grid-template-columns: 1fr; }
            .proposal-header { flex-direction: column; }
            .proposal-ref-box { text-align: left; }
        }
    </style>
</head>
<body>
    <div class="proposal-wrap">
        <header class="proposal-header">
            <div>
                <h1 class="company-brand-title">{$compName}</h1>
                <p class="company-meta-line">{$compAddress}</p>
                <p class="company-meta-line">Phone: {$compPhone} &bull; Email: {$compEmail}</p>
                <p class="company-meta-line">{$compDomain}</p>
            </div>
            <div class="proposal-ref-box">
                <span class="proposal-badge">Fleet Proposal</span>
                <div class="proposal-ref-number">{$quoteDisplayId}</div>
                <div class="proposal-date-line">Date: {$currentDate}</div>
            </div>
        </header>

        <main class="proposal-body">
            <div class="grid-two-cards">
                <div class="info-card">
                    <div class="info-card-title">Customer Identity</div>
                    <div class="info-card-row">
                        <span class="info-label">Client Name:</span>
                        <span class="info-val">{$custName}</span>
                    </div>
                    <div class="info-card-row">
                        <span class="info-label">Mobile Number:</span>
                        <span class="info-val">{$custMobile}</span>
                    </div>
                    <div class="info-card-row">
                        <span class="info-label">Email Address:</span>
                        <span class="info-val">{$custEmail}</span>
                    </div>
                    <div class="info-card-row">
                        <span class="info-label">Address / Location:</span>
                        <span class="info-val">{$custAddress}</span>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-title">Travel &amp; Route Logistics</div>
                    <div class="info-card-row">
                        <span class="info-label">Destination:</span>
                        <span class="info-val">{$dest}</span>
                    </div>
                    <div class="info-card-row">
                        <span class="info-label">Pickup Point:</span>
                        <span class="info-val">{$pickup}</span>
                    </div>
                    <div class="info-card-row">
                        <span class="info-label">Drop Point:</span>
                        <span class="info-val">{$drop}</span>
                    </div>
                    <div class="info-card-row">
                        <span class="info-label">Travel Date:</span>
                        <span class="info-val">{$tDate} ({$duration} Days)</span>
                    </div>
                </div>
            </div>

            <div class="vehicle-hero-card">
                <div class="vehicle-meta">
                    <h3>{$vehTitle}</h3>
                    <div>
                        <span class="vehicle-tag">{$vehType}</span>
                        <span class="vehicle-tag">{$vehSeating} Seater</span>
                        <span class="vehicle-tag" style="background:#dcfce7;color:#15803d;">Dedicated Chauffeur</span>
                    </div>
                </div>
                <div style="text-align:right;">
                    <span style="font-size:12px;color:#64748b;display:block;">Package Price</span>
                    <strong style="font-size:22px;color:#0f172a;">{$sym} {$totalCost}</strong>
                </div>
            </div>

            <div class="tariff-section">
                <div class="tariff-header">Commercial Billing &amp; Tariff Summary</div>
                <table class="tariff-table">
                    <tr>
                        <td>Daily Running Limit / Allowance</td>
                        <td class="val-highlight" style="text-align:right;">{$kmRunning} KM / Day</td>
                    </tr>
                    <tr>
                        <td>Extra Running Charge (Beyond Limit)</td>
                        <td class="val-highlight" style="text-align:right;">{$sym} {$extraKm} / KM</td>
                    </tr>
                    <tr>
                        <td>Advance Booking Deposit Required ({$advPercent}%)</td>
                        <td class="val-highlight" style="text-align:right;color:#15803d;">{$sym} {$advAmount}</td>
                    </tr>
                    <tr>
                        <td>Balance Payable on Trip</td>
                        <td class="val-highlight" style="text-align:right;">{$sym} {$balAmount}</td>
                    </tr>
                    <tr class="tariff-total-row">
                        <td>Total Quotation Cost</td>
                        <td style="text-align:right;color:#2563eb;">{$sym} {$totalCost}</td>
                    </tr>
                </table>
            </div>

            <div class="info-card" style="margin-bottom:24px;">
                <div class="info-card-title">Policy &amp; Standard Inclusions</div>
                <div class="terms-grid">
                    <div class="term-pill">
                        <span>Garage to Garage Calculation</span>
                        <span class="term-badge-included">{$garageText}</span>
                    </div>
                    <div class="term-pill">
                        <span>Toll Express Plaza Taxes</span>
                        <span class="{$tollClass}">{$tollText}</span>
                    </div>
                    <div class="term-pill">
                        <span>Inter-State Commercial Taxes</span>
                        <span class="{$stateTaxClass}">{$stateTaxText}</span>
                    </div>
                    <div class="term-pill">
                        <span>Chauffeur / Driver Allowance</span>
                        <span class="{$driverClass}">{$driverText}</span>
                    </div>
                    <div class="term-pill" style="grid-column: 1 / -1;">
                        <span>Airport, Station &amp; Monument Parking</span>
                        <span class="{$parkingClass}">{$parkingText}</span>
                    </div>
                </div>

                {$customRulesBlock}
            </div>

            <div class="proposal-footer-notes">
                <strong>Booking Instructions:</strong> Vehicle slot is held temporarily. Advance booking deposit of {$sym} {$advAmount} is required to confirm reservation. Toll, state taxes, and parking fees (if excluded) are to be settled as per actuals during the journey.
            </div>
        </main>
    </div>

    <script>
        window.downloadQuotationPDF = function() {
            window.print();
        };
    </script>
</body>
</html>
HTML;
}

// -----------------------------------------------------------------------------
// GET: Retrieve Transporter Quotation
// -----------------------------------------------------------------------------
if ($method === 'GET') {
    $rawId = isset($_GET['id']) ? trim((string)$_GET['id']) : (isset($_GET['quotation_id']) ? trim((string)$_GET['quotation_id']) : '');
    if (empty($rawId) && isset($_GET['encoded_id'])) {
        $decoded = base64_decode(trim((string)$_GET['encoded_id']), true);
        if ($decoded !== false && is_numeric($decoded)) {
            $rawId = $decoded;
        }
    }
    $quoteId = is_numeric($rawId) ? (int)$rawId : 0;
    
    $rawLeadId = isset($_GET['lead_id']) ? trim((string)$_GET['lead_id']) : '';
    $leadId = is_numeric($rawLeadId) ? (int)$rawLeadId : 0;
    if ($leadId <= 0 && !empty($rawLeadId)) {
        $decodedLead = base64_decode($rawLeadId, true);
        if ($decodedLead !== false && is_numeric($decodedLead)) {
            $leadId = (int)$decodedLead;
        }
    }

    if ($quoteId <= 0 && $leadId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Quotation ID or Lead ID is required.']);
        exit;
    }

    $whereClause = ($quoteId > 0)
        ? "q.quotation_id = {$quoteId}"
        : "q.quotation_ref_id = '{$leadId}' AND q.quotation_ref_type = 'enquiry' AND q.quotation_type = 'transporter'";

    $siteClause = ($siteId > 0) ? " AND (q.quotation_site_id = {$siteId} OR q.quotation_site_id = 0)" : "";

    $sql = "SELECT q.*, v.vehicle_title, v.vehicle_type, v.vehicle_seating, v.vehicle_thumbnail
            FROM {$tblPrefix}quotation q
            LEFT JOIN {$tblPrefix}vehicles v ON v.vehicle_id = q.quotation_type_id
            WHERE {$whereClause} {$siteClause}
            ORDER BY q.quotation_id DESC
            LIMIT 1";

    $res = mysqli_query($connection, $sql);
    if (!$res || mysqli_num_rows($res) === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Transporter quotation not found.']);
        exit;
    }

    $row = mysqli_fetch_assoc($res);
    $desc = json_decode($row['quotation_description'] ?? '{}', true) ?: [];
    $companyMeta = getTransporterCompanyMeta($connection, $siteId, $tblPrefix);
    $renderedHtml = buildTransporterProposalHtml((int)$row['quotation_id'], $desc, $companyMeta);

    if (isset($_GET['format']) && ($_GET['format'] === 'html' || $_GET['format'] === 'pdf')) {
        header('Content-Type: text/html; charset=utf-8');
        echo $renderedHtml;
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'data' => [
            'id'                  => (int)$row['quotation_id'],
            'encoded_id'          => base64_encode((string)$row['quotation_id']),
            'lead_id'             => (int)$row['quotation_ref_id'],
            'price'               => (float)$row['quotation_price'],
            'vehicle_id'          => (int)$row['quotation_type_id'],
            'vehicle_title'       => $row['vehicle_title'] ?? ($desc['vehicle']['vehicle_title'] ?? ''),
            'vehicle_type'        => $row['vehicle_type'] ?? ($desc['vehicle']['vehicle_type'] ?? ''),
            'vehicle_seating'     => $row['vehicle_seating'] ?? ($desc['vehicle']['vehicle_seating'] ?? ''),
            'vehicle_thumbnail'   => $row['vehicle_thumbnail'] ?? '',
            'details'             => $desc,
            'status'              => (int)$row['quotation_status'],
            'created_at'          => $row['quotation_time']
        ],
        'rendered_html' => $renderedHtml
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// POST: Create / Save Transporter Quotation
// -----------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);
    if (!is_array($data) || empty($data)) {
        $data = !empty($_POST) ? $_POST : [];
    }

    $rawLeadId = $data['lead_id'] ?? ($data['leadId'] ?? ($data['refId'] ?? ''));
    $leadId = 0;
    if (is_numeric($rawLeadId) && (int)$rawLeadId > 0) {
        $leadId = (int)$rawLeadId;
    } else if (!empty($rawLeadId)) {
        $decoded = base64_decode((string)$rawLeadId, true);
        if ($decoded !== false && is_numeric($decoded)) {
            $leadId = (int)$decoded;
        }
    }

    if ($leadId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Valid Lead ID is required.']);
        exit;
    }

    // Extract Sections
    $customer = $data['customer'] ?? [];
    $travel   = $data['travel'] ?? [];
    $vehicle  = $data['vehicle'] ?? [];
    $billing  = $data['billing'] ?? [];
    $terms    = $data['terms'] ?? [];

    $customerName    = trim((string)($customer['name'] ?? ''));
    $customerMobile  = trim((string)($customer['mobile'] ?? ''));
    $customerEmail   = trim((string)($customer['email'] ?? ''));
    $customerAddress = trim((string)($customer['address'] ?? ''));

    $destination    = trim((string)($travel['destination'] ?? ''));
    $pickupLocation = trim((string)($travel['pickup_location'] ?? ''));
    $dropLocation   = trim((string)($travel['drop_location'] ?? ''));
    $travelDate     = trim((string)($travel['travel_date'] ?? ''));
    if (empty($travelDate) || strtotime($travelDate) < strtotime(date('Y-m-d'))) {
        $travelDate = date('Y-m-d');
    }
    $travelDuration = trim((string)($travel['duration_days'] ?? ($travel['travel_duration'] ?? '1')));

    $vehicleId      = (int)($vehicle['vehicle_id'] ?? ($vehicle['model_id'] ?? 0));
    $vehicleType    = trim((string)($vehicle['vehicle_type'] ?? ''));
    $vehicleTitle   = trim((string)($vehicle['vehicle_title'] ?? ''));
    $vehicleSeating = trim((string)($vehicle['vehicle_seating'] ?? ''));

    // Fetch vehicle title if not passed
    if ($vehicleId > 0 && empty($vehicleTitle)) {
        $vRes = mysqli_query($connection, "SELECT vehicle_title, vehicle_type, vehicle_seating FROM {$tblPrefix}vehicles WHERE vehicle_id = {$vehicleId} LIMIT 1");
        if ($vRes && mysqli_num_rows($vRes) > 0) {
            $vRow = mysqli_fetch_assoc($vRes);
            $vehicleTitle   = $vRow['vehicle_title'];
            if (empty($vehicleType)) $vehicleType = $vRow['vehicle_type'];
            if (empty($vehicleSeating)) $vehicleSeating = $vRow['vehicle_seating'];
        }
    }

    $currencySymbol = trim((string)($billing['currency_symbol'] ?? '₹'));
    if (empty($currencySymbol)) $currencySymbol = '₹';

    $totalCost      = (float)($billing['total_cost'] ?? 0);
    $kmRunning      = (float)($billing['km_running'] ?? 250);
    $extraKmCharge  = (float)($billing['extra_km_charge'] ?? 0);
    $advancePercent = (float)($billing['advance_percent'] ?? 25);
    $advanceAmount  = (float)($billing['advance_amount'] ?? round(($totalCost * $advancePercent) / 100, 2));
    $balanceAmount  = (float)($billing['balance_amount'] ?? max(0, $totalCost - $advanceAmount));

    if ($totalCost <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Please provide a valid Total Cost for the quote.']);
        exit;
    }

    // Standard Terms
    $parkingCharges  = strtolower(trim((string)($terms['parking_charges'] ?? 'no'))) === 'yes' ? 'yes' : 'no';
    $garageToGarage  = strtolower(trim((string)($terms['garage_to_garage'] ?? 'yes'))) === 'no' ? 'no' : 'yes';
    $tollTax         = strtolower(trim((string)($terms['toll_tax'] ?? 'yes'))) === 'no' ? 'no' : 'yes';
    $stateTax        = strtolower(trim((string)($terms['state_tax'] ?? 'yes'))) === 'no' ? 'no' : 'yes';
    $driverAllowance = strtolower(trim((string)($terms['driver_allowance'] ?? 'yes'))) === 'no' ? 'no' : 'yes';
    $customRules     = is_array($terms['custom_rules'] ?? null) ? $terms['custom_rules'] : [];

    // Construct Complete Proposal Payload
    $quotePayload = [
        'type' => 'transporter_quotation',
        'customer' => [
            'name'    => $customerName,
            'mobile'  => $customerMobile,
            'email'   => $customerEmail,
            'address' => $customerAddress,
        ],
        'travel' => [
            'destination'     => $destination,
            'pickup_location' => $pickupLocation,
            'drop_location'   => $dropLocation,
            'travel_date'     => $travelDate,
            'duration_days'   => $travelDuration,
        ],
        'vehicle' => [
            'vehicle_id'      => $vehicleId,
            'vehicle_type'    => $vehicleType,
            'vehicle_title'   => $vehicleTitle,
            'vehicle_seating' => $vehicleSeating,
        ],
        'billing' => [
            'currency_symbol' => $currencySymbol,
            'total_cost'      => $totalCost,
            'km_running'      => $kmRunning,
            'extra_km_charge' => $extraKmCharge,
            'advance_percent' => $advancePercent,
            'advance_amount'  => $advanceAmount,
            'balance_amount'  => $balanceAmount,
        ],
        'terms' => [
            'parking_charges'  => $parkingCharges,
            'garage_to_garage' => $garageToGarage,
            'toll_tax'         => $tollTax,
            'state_tax'        => $stateTax,
            'driver_allowance' => $driverAllowance,
            'custom_rules'     => $customRules,
        ],
        'created_at' => date('Y-m-d H:i:s'),
    ];

    $descJson = mysqli_real_escape_string($connection, json_encode($quotePayload, JSON_UNESCAPED_UNICODE));

    // Archive prior active quotes for this lead
    mysqli_query($connection, "UPDATE {$tblPrefix}quotation SET quotation_status = 2 WHERE quotation_ref_id = '{$leadId}' AND quotation_ref_type = 'enquiry' " . ($siteId > 0 ? "AND quotation_site_id = {$siteId}" : ""));

    // Insert new Transporter Quotation
    $insertQuery = "INSERT INTO {$tblPrefix}quotation (
        quotation_parent_id,
        quotation_ref_id,
        quotation_ref_type,
        quotation_type_id,
        quotation_type,
        quotation_site_id,
        quotation_sent_by,
        quotation_mrp,
        quotation_price,
        quotation_description,
        quotation_status,
        quotation_time
    ) VALUES (
        0,
        '{$leadId}',
        'enquiry',
        {$vehicleId},
        'transporter',
        {$siteId},
        {$userId},
        {$totalCost},
        {$totalCost},
        '{$descJson}',
        0,
        NOW()
    )";

    if (!mysqli_query($connection, $insertQuery)) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database error while saving quotation: ' . mysqli_error($connection)]);
        exit;
    }

    $quotationId = (int)mysqli_insert_id($connection);
    $encodedId   = base64_encode((string)$quotationId);

    // Timeline Log
    $noteText = "Generated Transporter Quotation #{$quotationId} for {$vehicleTitle} (Total: {$currencySymbol} " . number_format($totalCost, 2) . ")";
    $noteTextEsc = mysqli_real_escape_string($connection, $noteText);
    mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes (
        enquiry_notes_ref_id,
        enquiry_notes_user_id,
        enquiry_notes_meta_type,
        enquiry_notes_meta_value
    ) VALUES (
        {$leadId},
        {$userId},
        'itinerary',
        '{$quotationId}'
    )");

    // Update Lead Status to 'Itinerary / Quotation Sent' (status 9)
    mysqli_query($connection, "UPDATE {$tblPrefix}enquiry SET enquiry_status = 9, enquiry_update = NOW() WHERE enquiry_id = {$leadId} " . ($siteId > 0 ? "AND enquiry_site_id = {$siteId}" : ""));

    // Build WhatsApp Message Text
    $waLines = [];
    $waLines[] = "*TRIP QUOTATION & FLEET PROPOSAL*";
    $waLines[] = "--------------------------------";
    if (!empty($customerName)) {
        $waLines[] = "*Customer:* " . $customerName;
    }
    if (!empty($destination)) {
        $waLines[] = "*Destination:* " . $destination;
    }
    if (!empty($pickupLocation)) {
        $waLines[] = "*Pickup:* " . $pickupLocation;
    }
    if (!empty($dropLocation)) {
        $waLines[] = "*Drop:* " . $dropLocation;
    }
    if (!empty($travelDate)) {
        $waLines[] = "*Travel Date:* " . $travelDate . (!empty($travelDuration) ? " ({$travelDuration} Days)" : "");
    }
    if (!empty($vehicleTitle)) {
        $waLines[] = "*Vehicle:* " . $vehicleTitle . (!empty($vehicleSeating) ? " ({$vehicleSeating} Seater)" : "");
    }
    $waLines[] = "";
    $waLines[] = "*BILLING DETAILS*";
    $waLines[] = "*Total Cost:* {$currencySymbol} " . number_format($totalCost, 2);
    $waLines[] = "*KM Allowance:* " . $kmRunning . " KM";
    if ($extraKmCharge > 0) {
        $waLines[] = "*Extra KM Rate:* {$currencySymbol} " . number_format($extraKmCharge, 2) . " / KM";
    }
    $waLines[] = "*Advance Booking Deposit:* {$currencySymbol} " . number_format($advanceAmount, 2) . " ({$advancePercent}%)";
    $waLines[] = "*Balance Due:* {$currencySymbol} " . number_format($balanceAmount, 2);
    $waLines[] = "";
    $waLines[] = "*TERMS & POLICIES*";
    $waLines[] = "• Garage to Garage Calculation: " . ($garageToGarage === 'yes' ? "Applicable" : "Not Applicable");
    $waLines[] = "• Toll Tax: " . ($tollTax === 'yes' ? "Included" : "Excluded (As per actual)");
    $waLines[] = "• State Border Entry Tax: " . ($stateTax === 'yes' ? "Included" : "Excluded (As per actual)");
    $waLines[] = "• Driver Allowance: " . ($driverAllowance === 'yes' ? "Included" : "Excluded");
    $waLines[] = "• Parking Charges: " . ($parkingCharges === 'yes' ? "Included" : "Excluded (Paid directly by guest)");

    if (!empty($customRules)) {
        foreach ($customRules as $cr) {
            if (!empty($cr['enabled']) && !empty($cr['title'])) {
                $waLines[] = "• " . trim($cr['title']) . (!empty($cr['description']) ? ": " . trim($cr['description']) : "");
            }
        }
    }

    $waLines[] = "";
    $waLines[] = "Thank you for choosing us! Please reply to confirm this proposal.";
    $whatsappText = implode("\n", $waLines);

    $companyMeta = getTransporterCompanyMeta($connection, $siteId, $tblPrefix);
    $renderedHtml = buildTransporterProposalHtml($quotationId, $quotePayload, $companyMeta);

    echo json_encode([
        'status'          => 'success',
        'message'         => 'Transporter quotation created successfully.',
        'quotation_id'    => $quotationId,
        'encoded_id'      => $encodedId,
        'whatsapp_text'   => $whatsappText,
        'total_cost'      => $totalCost,
        'currency_symbol' => $currencySymbol,
        'advance_amount'  => $advanceAmount,
        'balance_amount'  => $balanceAmount,
        'rendered_html'   => $renderedHtml
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
exit;
