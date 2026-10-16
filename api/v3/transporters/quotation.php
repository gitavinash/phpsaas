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

if (!defined('TRANSPORTER_QUOTATION_HELPERS_ONLY') || !TRANSPORTER_QUOTATION_HELPERS_ONLY) {
    $action = $_GET['action'] ?? ($_POST['action'] ?? '');
    $hasPublicId = isset($_GET['id']) || isset($_GET['quotation_id']) || isset($_GET['encoded_id']);
    $isPublicView = ($method === 'GET' && $hasPublicId && empty($action));
    if (!$isPublicView) {
        if (!defined('LOGIN_CHECK')) {
            define('LOGIN_CHECK', $docRoot . '/lib/modules/isLogin.php');
        }
        if (file_exists(LOGIN_CHECK)) {
            require_once LOGIN_CHECK;
        }
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : (int)($_SESSION['site_id'] ?? 0);
$userId    = defined('USER_ID')      ? (int) USER_ID : (int)($_SESSION['user_id'] ?? 0);

if ($siteId <= 0) {
    $siteId = 1;
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function formatIndianNumber(float $num, int $decimals = 2): string {
    $isNegative = $num < 0;
    $num = abs($num);
    $parts = explode('.', number_format($num, $decimals, '.', ''));
    $intPart = $parts[0];
    $decPart = isset($parts[1]) ? '.' . $parts[1] : '';
    if (strlen($intPart) > 3) {
        $lastThree = substr($intPart, -3);
        $remaining = substr($intPart, 0, -3);
        $remaining = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $remaining);
        $formatted = $remaining . ',' . $lastThree . $decPart;
    } else {
        $formatted = $intPart . $decPart;
    }
    return ($isNegative ? '-' : '') . $formatted;
}

function getIndianCurrencyWords(float $amount): string {
    $amount = round($amount, 2);
    $no = (int)floor($amount);
    $point = (int)round(($amount - $no) * 100);
    $words = [
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty',
        30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy',
        80 => 'Eighty', 90 => 'Ninety'
    ];
    $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
    
    $str = [];
    $i = 0;
    while ($no > 0) {
        $divider = ($i == 2) ? 10 : 100;
        $number = $no % $divider;
        $no = (int)floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number > 0) {
            $counter = count($str);
            if ($number < 20) {
                $chunk = $words[$number];
            } else {
                $chunk = $words[10 * (int)floor($number / 10)] . ($number % 10 ? ' ' . $words[$number % 10] : '');
            }
            $unit = isset($digits[$counter]) && !empty($digits[$counter]) ? ' ' . $digits[$counter] : '';
            $str[] = trim($chunk . $unit);
        } else {
            $str[] = '';
        }
    }
    $rupees = implode(' ', array_reverse(array_filter($str)));
    $rupees = trim(preg_replace('/\s+/', ' ', $rupees));
    if (empty($rupees)) $rupees = 'Zero';
    $paise = '';
    if ($point > 0) {
        if ($point < 20) $pWords = $words[$point];
        else $pWords = $words[10 * (int)floor($point / 10)] . ($point % 10 ? ' ' . $words[$point % 10] : '');
        $paise = ' and ' . trim($pWords) . ' Paise';
    }
    return 'Rupees ' . $rupees . $paise . ' Only';
}

function getTransporterCompanyMeta($connection, $siteId, $tblPrefix) {
    $meta = [
        'name'        => 'Aleya Travel & Fleet Logistics',
        'tagline'     => 'Commercial Tourist Fleet & Transport Operator',
        'mobile'      => '',
        'whatsapp'    => '',
        'email'       => '',
        'address'     => '',
        'domain'      => '',
        'gstin'       => '',
        'pan'         => '',
        'bank_name'   => '',
        'bank_ac_name'=> '',
        'bank_ac_no'  => '',
        'bank_ifsc'   => '',
        'bank_branch' => '',
        'upi_id'      => ''
    ];
    if (!$connection) return $meta;
    $sId = (int)$siteId;
    $clause = ($sId > 0) ? "WHERE site_configuration_site_id = {$sId} OR site_configuration_site_id = 0" : "";
    $res = @mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value FROM {$tblPrefix}site_configuration {$clause}");
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $k = $r['site_configuration_key'];
            $v = trim((string)$r['site_configuration_value']);
            if (empty($v)) continue;
            if ($k === 'company_name') $meta['name'] = $v;
            else if ($k === 'company_mobile' || $k === 'site_mobile') {
                if (empty($meta['mobile'])) $meta['mobile'] = $v;
            }
            else if ($k === 'company_whatsapp_number' || $k === 'company_whatsapp') {
                if (empty($meta['whatsapp'])) $meta['whatsapp'] = $v;
            }
            else if ($k === 'company_email' || $k === 'site_email') {
                if (empty($meta['email'])) $meta['email'] = $v;
            }
            else if ($k === 'company_address') $meta['address'] = $v;
            else if ($k === 'company_site_domain') $meta['domain'] = $v;
            else if ($k === 'company_gst' || $k === 'gst_number' || $k === 'gstin') $meta['gstin'] = $v;
            else if ($k === 'company_pan' || $k === 'pan_number') $meta['pan'] = $v;
            else if ($k === 'bank_name' && empty($meta['bank_name'])) $meta['bank_name'] = $v;
            else if ($k === 'bank_ac_name' && empty($meta['bank_ac_name'])) $meta['bank_ac_name'] = $v;
            else if ($k === 'bank_ac_number' && empty($meta['bank_ac_no'])) $meta['bank_ac_no'] = $v;
            else if ($k === 'bank_ifsc' && empty($meta['bank_ifsc'])) $meta['bank_ifsc'] = $v;
            else if ($k === 'bank_address' && empty($meta['bank_branch'])) $meta['bank_branch'] = $v;
            else if (($k === 'upi_details' || $k === 'pg_upi_address') && empty($meta['upi_id'])) {
                $parts = explode(',', $v);
                $meta['upi_id'] = trim($parts[0]);
            }
            else if ($k === 'setting_company_profile') {
                $cp = json_decode($v, true);
                if (is_array($cp)) {
                    if (!empty($cp['companyName']) && $meta['name'] === 'Aleya Travel & Fleet Logistics') $meta['name'] = $cp['companyName'];
                    if (!empty($cp['companyMobile']) && empty($meta['mobile'])) $meta['mobile'] = $cp['companyMobile'];
                    if (!empty($cp['companyWhatsapp']) && empty($meta['whatsapp'])) $meta['whatsapp'] = $cp['companyWhatsapp'];
                    if (!empty($cp['companyEmail']) && empty($meta['email'])) $meta['email'] = $cp['companyEmail'];
                    if (!empty($cp['companyAddress']) && empty($meta['address'])) $meta['address'] = $cp['companyAddress'];
                }
            }
        }
    }
    if (empty($meta['mobile']) && !empty($meta['whatsapp'])) $meta['mobile'] = $meta['whatsapp'];
    if (empty($meta['whatsapp']) && !empty($meta['mobile'])) $meta['whatsapp'] = $meta['mobile'];
    if (empty($meta['bank_ac_name'])) $meta['bank_ac_name'] = $meta['name'];
    return $meta;
}

function buildTransporterProposalHtml($quoteId, $details, $company, $selectedTemplate = '') {
    // Modular Template Hook: Render using V3 Quote Template if available
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : (defined('ROOT') ? ROOT : realpath(__DIR__ . '/../../../'));
    $templateName = !empty($selectedTemplate) ? strtolower(trim($selectedTemplate)) : (!empty($details['template']) ? strtolower(trim($details['template'])) : 'mit');
    $templateFile = $docRoot . '/app/dashboard/v3/default/desktop/templates/quote/' . $templateName . '/index.php';
    if (!file_exists($templateFile)) {
        $templateFile = dirname(__DIR__, 3) . '/dashboard/v3/default/desktop/templates/quote/' . $templateName . '/index.php';
    }
    if (!file_exists($templateFile)) {
        $templateFile = $docRoot . '/app/dashboard/v3/default/desktop/templates/quote/mit/index.php';
    }
    if (!file_exists($templateFile)) {
        $templateFile = dirname(__DIR__, 3) . '/dashboard/v3/default/desktop/templates/quote/mit/index.php';
    }
    if (file_exists($templateFile)) {
        $quotationId = $quoteId;
        $quotePayload = $details;
        $companyMeta = $company;
        ob_start();
        include $templateFile;
        $templateRendered = ob_get_clean();
        if (!empty($templateRendered)) {
            return $templateRendered;
        }
    }

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

    $sym              = htmlspecialchars($bill['currency_symbol'] ?? '₹');
    $totalCostNum     = (float)($bill['total_cost'] ?? 0);
    $totalRunningKmNum= (float)($bill['total_running_km'] ?? 0);
    $extraKmNum       = (float)($bill['extra_km_charge'] ?? 0);
    $kmRunningNum     = (float)($bill['km_running'] ?? 250);
    $advPercent       = (float)($bill['advance_percent'] ?? 25);
    $advAmountNum     = (float)($bill['advance_amount'] ?? round(($totalCostNum * $advPercent) / 100, 2));
    $balAmountNum     = (float)($bill['balance_amount'] ?? max(0, $totalCostNum - $advAmountNum));

    $totalCost   = formatIndianNumber($totalCostNum);
    $extraKm     = formatIndianNumber($extraKmNum);
    $kmRunning   = formatIndianNumber($kmRunningNum, 0);
    $advAmount   = formatIndianNumber($advAmountNum);
    $balAmount   = formatIndianNumber($balAmountNum);
    $totalRunningKmStr = ($totalRunningKmNum > 0) ? formatIndianNumber($totalRunningKmNum, ($totalRunningKmNum == (int)$totalRunningKmNum ? 0 : 2)) : '';
    $runningAmountStr  = ($totalRunningKmNum > 0 && $extraKmNum > 0) ? formatIndianNumber($totalRunningKmNum * $extraKmNum) : $totalCost;
    $distanceQtyStr    = !empty($totalRunningKmStr) ? $totalRunningKmStr . ' KM' : 'As per Route';
    $amountInWords     = getIndianCurrencyWords($totalCostNum);

    $parking  = strtolower($term['parking_charges'] ?? 'no') === 'yes';
    $garage   = strtolower($term['garage_to_garage'] ?? 'yes') === 'yes';
    $toll     = strtolower($term['toll_tax'] ?? 'yes') === 'yes';
    $stateTax = strtolower($term['state_tax'] ?? 'yes') === 'yes';
    $driver   = strtolower($term['driver_allowance'] ?? 'yes') === 'yes';
    $customRules = is_array($term['custom_rules'] ?? null) ? $term['custom_rules'] : [];

    $garageText    = $garage ? 'Garage to Garage Applicable' : 'City Base to Base';
    $tollClass     = $toll ? 'term-badge-included' : 'term-badge-excluded';
    $tollText      = $toll ? 'Included in Quote' : 'Excluded (At Actuals)';
    $stateTaxClass = $stateTax ? 'term-badge-included' : 'term-badge-excluded';
    $stateTaxText  = $stateTax ? 'Included in Quote' : 'Excluded (At Actuals)';
    $driverClass   = $driver ? 'term-badge-included' : 'term-badge-excluded';
    $driverText    = $driver ? 'Included in Quote' : 'Excluded';
    $parkingClass  = $parking ? 'term-badge-included' : 'term-badge-excluded';
    $parkingText   = $parking ? 'Included in Quote' : 'Excluded (Payable by Guest)';

    $compName    = htmlspecialchars($company['name'] ?? 'Aleya Fleet Logistics');
    $compTagline = htmlspecialchars($company['tagline'] ?? 'Commercial Tourist Fleet & Transport Operator');
    $compPhone   = htmlspecialchars($company['mobile'] ?? '');
    $compWhatsapp= htmlspecialchars($company['whatsapp'] ?? $compPhone);
    $compEmail   = htmlspecialchars($company['email'] ?? '');
    $compDomain  = htmlspecialchars($company['domain'] ?? '');
    $compAddress = htmlspecialchars($company['address'] ?? '');
    $compGstin   = htmlspecialchars(!empty($company['gstin']) ? $company['gstin'] : 'Exempt / Transporter RCM');
    $compPan     = htmlspecialchars(!empty($company['pan']) ? $company['pan'] : '');
    $compWebsiteHtml = !empty($compDomain) ? '<span class="company-tax-badge">Website: ' . $compDomain . '</span>' : '';
    $compPanHtml     = !empty($compPan) ? '<span class="company-tax-badge">PAN: ' . $compPan . '</span>' : '';
    $vehSeatingHtml  = !empty($vehSeating) ? '<span class="fleet-tag-pill">' . htmlspecialchars($vehSeating) . ' Seater</span>' : '';

    $bankName    = htmlspecialchars($company['bank_name'] ?? '');
    $bankAcName  = htmlspecialchars($company['bank_ac_name'] ?? $compName);
    $bankAcNo    = htmlspecialchars($company['bank_ac_no'] ?? '');
    $bankIfsc    = htmlspecialchars($company['bank_ifsc'] ?? '');
    $bankBranch  = htmlspecialchars($company['bank_branch'] ?? '');
    $upiId       = htmlspecialchars($company['upi_id'] ?? '');

    $quoteDisplayId = 'TQ-' . str_pad((string)$quoteId, 5, '0', STR_PAD_LEFT);
    $currentDate = date('d M, Y');
    $validDate   = date('d M, Y', strtotime('+7 days'));

    $customRulesHtml = '';
    if (!empty($customRules)) {
        foreach ($customRules as $cr) {
            if (!empty($cr['enabled']) && !empty($cr['title'])) {
                $t = htmlspecialchars($cr['title']);
                $d = !empty($cr['description']) ? ' &mdash; <span style="color:#64748b;">' . htmlspecialchars($cr['description']) . '</span>' : '';
                $customRulesHtml .= '<li class="indian-term-item"><strong>' . $t . '</strong>' . $d . '</li>';
            }
        }
    }

    $customRulesBlock = '';
    if (!empty($customRulesHtml)) {
        $customRulesBlock = '<div class="indian-custom-rules-wrap"><div class="indian-sub-heading">Special Operating Conditions:</div><ul class="indian-terms-list">' . $customRulesHtml . '</ul></div>';
    }

    $bankDetailsHtml = '';
    if (!empty($bankName) || !empty($bankAcNo) || !empty($upiId)) {
        $bankDetailsHtml = '
        <div class="indian-bank-card">
            <div class="indian-bank-header">
                <div class="indian-bank-title"><i class="bank-icon">🏦</i> Bank &amp; Digital Payment Details (For NEFT / RTGS / IMPS / UPI)</div>
                ' . (!empty($upiId) ? '<span class="indian-upi-badge">UPI / QR Enabled</span>' : '') . '
            </div>
            <div class="indian-bank-grid">
                ' . (!empty($bankName) ? '<div class="indian-bank-cell"><span class="lbl">Bank Name:</span> <strong>' . $bankName . '</strong></div>' : '') . '
                ' . (!empty($bankAcName) ? '<div class="indian-bank-cell"><span class="lbl">Beneficiary:</span> <strong>' . $bankAcName . '</strong></div>' : '') . '
                ' . (!empty($bankAcNo) ? '<div class="indian-bank-cell"><span class="lbl">Account No:</span> <strong class="val-mono">' . $bankAcNo . '</strong></div>' : '') . '
                ' . (!empty($bankIfsc) ? '<div class="indian-bank-cell"><span class="lbl">IFSC Code:</span> <strong class="val-mono">' . $bankIfsc . '</strong></div>' : '') . '
                ' . (!empty($bankBranch) ? '<div class="indian-bank-cell span-2"><span class="lbl">Branch:</span> <span>' . $bankBranch . '</span></div>' : '') . '
                ' . (!empty($upiId) ? '<div class="indian-bank-cell span-2"><span class="lbl">UPI ID / VPA:</span> <strong class="val-mono val-upi">' . $upiId . '</strong> (GooglePay, PhonePe, Paytm, BHIM)</div>' : '') . '
            </div>
        </div>';
    } else {
        $bankDetailsHtml = '
        <div class="indian-bank-card">
            <div class="indian-bank-header">
                <div class="indian-bank-title"><i class="bank-icon">🏦</i> Bank &amp; UPI Payment</div>
                <span class="indian-upi-badge">Instant Booking</span>
            </div>
            <p style="font-size:12.5px;color:#475569;margin-top:4px;">Official bank transfer and UPI QR details will be provided along with booking confirmation voucher.</p>
        </div>';
    }

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporter Quotation {$quoteDisplayId} - {$custName}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 30px 16px;
            font-size: 13px;
            line-height: 1.5;
        }
        .proposal-wrap {
            max-width: 880px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1px solid #94a3b8;
            overflow: hidden;
            position: relative;
        }
        /* Header / Letterhead - Formal Legal Document Format */
        .indian-letterhead {
            padding: 24px 30px 18px 30px;
            border-bottom: 2px solid #0f172a;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            background: #ffffff;
        }
        .company-meta-col {
            flex: 1;
        }
        .company-brand-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.02em;
            line-height: 1.2;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .company-tagline {
            font-size: 11px;
            color: #475569;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .company-address-line {
            font-size: 12px;
            color: #334155;
            line-height: 1.45;
            margin-bottom: 3px;
        }
        .company-tax-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
            font-size: 11px;
            font-weight: 600;
            color: #1e293b;
        }
        .company-tax-badge {
            background: #f8fafc;
            padding: 2px 8px;
            border-radius: 3px;
            border: 1px solid #cbd5e1;
            font-size: 11px;
            color: #334155;
        }
        .invoice-title-col {
            text-align: right;
            min-width: 250px;
        }
        .invoice-type-pill {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 3px;
            margin-bottom: 8px;
        }
        .invoice-meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .invoice-meta-table td {
            padding: 2px 0;
            font-size: 12px;
        }
        .invoice-meta-table td.lbl {
            color: #64748b;
            text-align: right;
            padding-right: 8px;
        }
        .invoice-meta-table td.val {
            font-weight: 700;
            color: #0f172a;
            text-align: right;
        }

        /* Body Section */
        .proposal-body {
            padding: 24px 30px 30px 30px;
        }
        /* 2-Column Info Grid: Customer & Trip */
        .indian-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        .indian-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            overflow: hidden;
        }
        .indian-box-header {
            background: #0f2942;
            color: #ffffff;
            padding: 6px 14px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .indian-box-body {
            padding: 12px 14px;
        }
        .indian-data-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 12.5px;
        }
        .indian-data-row:last-child { margin-bottom: 0; }
        .indian-data-row .lbl { color: #64748b; font-size: 12px; }
        .indian-data-row .val { font-weight: 600; color: #0f172a; text-align: right; }

        /* Vehicle Specification Banner - Neutral Legal Format */
        .indian-fleet-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #0f172a;
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .fleet-meta h3 {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .fleet-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .fleet-tag-pill {
            background: #ffffff;
            color: #334155;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 3px;
            border: 1px solid #cbd5e1;
        }
        .fleet-cost-meta {
            text-align: right;
            min-width: 140px;
        }
        .fleet-cost-lbl {
            font-size: 10.5px;
            color: #64748b;
            font-weight: 700;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .fleet-cost-val {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
        }

        /* Indian Itemized Tariff Table */
        .indian-table-wrap {
            border: 1px solid #0f2942;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 16px;
        }
        .indian-tariff-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }
        .indian-tariff-table th {
            background: #0f2942;
            color: #ffffff;
            font-weight: 700;
            font-size: 11.5px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 9px 12px;
            border-right: 1px solid #1e3a5f;
            text-align: left;
        }
        .indian-tariff-table th:last-child { border-right: none; }
        .indian-tariff-table th.center, .indian-tariff-table td.center { text-align: center; }
        .indian-tariff-table th.right, .indian-tariff-table td.right { text-align: right; }
        .indian-tariff-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            vertical-align: middle;
            color: #1e293b;
        }
        .indian-tariff-table td:last-child { border-right: none; }
        .indian-tariff-table tbody tr:nth-child(even) { background-color: #f8fafc; }
        .tariff-desc-sub {
            display: block;
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }
        .indian-tariff-table tr.total-row {
            background: #f1f5f9;
            font-weight: 700;
        }
        .indian-tariff-table tr.total-row td {
            border-top: 2px solid #0f2942;
            font-size: 14px;
            color: #0f172a;
            padding: 10px 12px;
        }
        .indian-tariff-table tr.total-row td.grand-amount {
            font-size: 17px;
            color: #0f2942;
        }

        /* Amount in Words Box */
        .indian-words-strip {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #0f172a;
            padding: 8px 14px;
            border-radius: 4px;
            font-size: 12px;
            color: #0f172a;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* Payment Milestones Card */
        .indian-payment-schedule {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        .payment-pill {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .payment-pill.advance {
            background: #f8fafc;
            border-color: #94a3b8;
            border-left: 3px solid #0f172a;
        }
        .payment-pill .meta .lbl {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #475569;
            letter-spacing: 0.04em;
        }
        .payment-pill .meta .desc {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }
        .payment-pill .amount {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }
        .payment-pill.advance .amount {
            color: #0f172a;
        }

        /* Indian Bank & UPI Box */
        .indian-bank-card {
            background: #ffffff;
            border: 1px dashed #0f2942;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 20px;
        }
        .indian-bank-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .indian-bank-title {
            font-size: 12px;
            font-weight: 700;
            color: #0f2942;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .indian-upi-badge {
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #bfdbfe;
        }
        .indian-bank-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 16px;
            font-size: 12px;
        }
        .indian-bank-cell {
            display: flex;
            gap: 6px;
        }
        .indian-bank-cell.span-2 {
            grid-column: 1 / -1;
        }
        .indian-bank-cell .lbl { color: #64748b; min-width: 90px; }
        .val-mono { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #0f172a; }
        .val-upi { color: #2563eb; font-weight: 700; }

        /* Terms & Conditions Section */
        .indian-terms-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 14px 18px;
            margin-bottom: 24px;
        }
        .indian-terms-header {
            font-size: 12px;
            font-weight: 700;
            color: #0f2942;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .indian-terms-list {
            list-style: none;
            padding-left: 0;
        }
        .indian-term-item {
            position: relative;
            padding-left: 20px;
            margin-bottom: 6px;
            font-size: 11.5px;
            color: #334155;
            line-height: 1.45;
        }
        .indian-term-item:last-child { margin-bottom: 0; }
        .indian-term-item::before {
            content: "•";
            position: absolute;
            left: 4px;
            color: #0f2942;
            font-weight: 800;
            font-size: 14px;
        }
        .term-badge-included {
            background: #dcfce7;
            color: #15803d;
            padding: 1px 6px;
            border-radius: 3px;
            font-weight: 700;
            font-size: 10.5px;
        }
        .term-badge-excluded {
            background: #fee2e2;
            color: #b91c1c;
            padding: 1px 6px;
            border-radius: 3px;
            font-weight: 700;
            font-size: 10.5px;
        }
        .indian-sub-heading {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            margin: 10px 0 6px 0;
        }

        /* Signatures Section */
        .indian-sign-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #cbd5e1;
        }
        .indian-sign-box {
            font-size: 12px;
        }
        .indian-sign-box.right {
            text-align: right;
        }
        .sign-placeholder-line {
            height: 48px;
            border-bottom: 1px dashed #94a3b8;
            margin-bottom: 6px;
        }
        .sign-title {
            font-weight: 700;
            color: #0f172a;
        }
        .sign-subtitle {
            font-size: 11px;
            color: #64748b;
        }

        /* Floating Action Bar */
        .action-floating-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .btn-print-action {
            background: #0f2942;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(15, 41, 66, 0.3);
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .btn-print-action:hover { background: #1e3a5f; transform: translateY(-1px); }

        /* Document Footer & Aleya.in Credit */
        .indian-doc-footer {
            margin-top: 24px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: #64748b;
        }
        .indian-doc-footer .footer-credit {
            font-weight: 600;
            color: #334155;
            text-align: right;
        }
        .indian-doc-footer .credit-link {
            color: #2563eb;
            font-weight: 700;
            text-decoration: none;
        }
        .indian-doc-footer .credit-link:hover {
            text-decoration: underline;
        }
        .print-page-footer {
            display: none;
        }

        /* Print Rules */
        @media print {
            body { background: #ffffff !important; padding: 0 !important; color: #000000 !important; }
            .proposal-wrap { box-shadow: none !important; border: none !important; max-width: 100% !important; border-radius: 0 !important; }
            .action-floating-bar { display: none !important; }
            .indian-tariff-table th, .indian-box-header, .invoice-type-pill { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .indian-terms-card, .indian-sign-grid, .indian-doc-footer, .indian-bank-card, .indian-payment-schedule { page-break-inside: avoid; break-inside: avoid; }
            .print-page-footer {
                display: block !important;
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                text-align: center !important;
                font-size: 9.5px !important;
                color: #64748b !important;
                background: #ffffff !important;
                border-top: 1px solid #e2e8f0 !important;
                padding: 4px 10px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                margin: 10mm 12mm 14mm 12mm;
                size: A4 portrait;
                @bottom-center {
                    content: "Powered by Aleya.in • Fleet & Mobility Solutions";
                    font-size: 9px;
                    color: #64748b;
                }
            }
        }
        @media (max-width: 768px) {
            .indian-letterhead { flex-direction: column; }
            .invoice-title-col { text-align: left; }
            .invoice-meta-table td.lbl { text-align: left; }
            .indian-info-grid, .indian-payment-schedule, .indian-bank-grid, .indian-sign-grid { grid-template-columns: 1fr; }
            .indian-sign-box.right { text-align: left; }
        }
    </style>
</head>
<body>
    <div class="proposal-wrap">
        <!-- Corporate Letterhead Header (Valid Legal Format) -->
        <header class="indian-letterhead">
            <div class="company-meta-col">
                <h1 class="company-brand-title">{$compName}</h1>
                <div class="company-tagline">{$compTagline}</div>
                <p class="company-address-line">{$compAddress}</p>
                <p class="company-address-line"><strong>Phone:</strong> {$compPhone} &bull; <strong>WhatsApp:</strong> {$compWhatsapp} &bull; <strong>Email:</strong> {$compEmail}</p>
                <div class="company-tax-strip">
                    <span class="company-tax-badge">GSTIN: {$compGstin}</span>
                    {$compPanHtml}
                    {$compWebsiteHtml}
                    <span class="company-tax-badge">Commercial Fleet Operator</span>
                </div>
            </div>

            <div class="invoice-title-col">
                <div class="invoice-type-pill">COMMERCIAL TRANSPORT QUOTATION</div>
                <table class="invoice-meta-table">
                    <tr>
                        <td class="lbl">Quote Ref No:</td>
                        <td class="val">{$quoteDisplayId}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Date of Issue:</td>
                        <td class="val">{$currentDate}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Valid Upto:</td>
                        <td class="val">{$validDate}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Duty Nature:</td>
                        <td class="val">Passenger Transport</td>
                    </tr>
                </table>
            </div>
        </header>

        <!-- Document Main Body -->
        <main class="proposal-body">
            <!-- 2-Column Structured Info: Customer & Itinerary -->
            <div class="indian-info-grid">
                <div class="indian-box">
                    <div class="indian-box-header">
                        <span>Customer Details (Quotation To)</span>
                        <span>Client</span>
                    </div>
                    <div class="indian-box-body">
                        <div class="indian-data-row">
                            <span class="lbl">Client Name:</span>
                            <span class="val">{$custName}</span>
                        </div>
                        <div class="indian-data-row">
                            <span class="lbl">Mobile No:</span>
                            <span class="val">{$custMobile}</span>
                        </div>
                        <div class="indian-data-row">
                            <span class="lbl">Email ID:</span>
                            <span class="val">{$custEmail}</span>
                        </div>
                        <div class="indian-data-row">
                            <span class="lbl">Address / City:</span>
                            <span class="val">{$custAddress}</span>
                        </div>
                    </div>
                </div>

                <div class="indian-box">
                    <div class="indian-box-header">
                        <span>Tour &amp; Journey Route Schedule</span>
                        <span>Itinerary</span>
                    </div>
                    <div class="indian-box-body">
                        <div class="indian-data-row">
                            <span class="lbl">Destination:</span>
                            <span class="val">{$dest}</span>
                        </div>
                        <div class="indian-data-row">
                            <span class="lbl">Pickup Point:</span>
                            <span class="val">{$pickup}</span>
                        </div>
                        <div class="indian-data-row">
                            <span class="lbl">Drop Point:</span>
                            <span class="val">{$drop}</span>
                        </div>
                        <div class="indian-data-row">
                            <span class="lbl">Travel Date:</span>
                            <span class="val">{$tDate} ({$duration} Day(s))</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned Fleet Vehicle Specification Card -->
            <div class="indian-fleet-card">
                <div class="fleet-meta">
                    <h3>{$vehTitle}</h3>
                    <div class="fleet-tags">
                        <span class="fleet-tag-pill">Category: {$vehType}</span>
                        {$vehSeatingHtml}
                        <span class="fleet-tag-pill">All India Tourist Permit</span>
                        <span class="fleet-tag-pill">Air-Conditioned</span>
                        <span class="fleet-tag-pill">Chauffeur Driven</span>
                    </div>
                </div>
                <div class="fleet-cost-meta">
                    <span class="fleet-cost-lbl">Estimated Fleet Tariff</span>
                    <strong class="fleet-cost-val">{$sym} {$totalCost}</strong>
                </div>
            </div>

            <!-- Commercial Tariff & Estimate Table (Indian Format) -->
            <div class="indian-table-wrap">
                <table class="indian-tariff-table">
                    <thead>
                        <tr>
                            <th class="center" style="width: 46px;">S.N.</th>
                            <th>Description / Service Particulars</th>
                            <th class="right" style="width: 140px;">Tariff Basis</th>
                            <th class="right" style="width: 130px;">Distance / Qty</th>
                            <th class="right" style="width: 150px;">Total Amount ({$sym})</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="center">1.</td>
                            <td>
                                <strong>Vehicle Outstation Running Tariff</strong>
                                <span class="tariff-desc-sub">Route distance tariff estimated based on itinerary ({$pickup} to {$dest})</span>
                            </td>
                            <td class="right">{$sym} {$extraKm} / KM</td>
                            <td class="right">{$distanceQtyStr}</td>
                            <td class="right"><strong>{$sym} {$runningAmountStr}</strong></td>
                        </tr>
                        <tr>
                            <td class="center">2.</td>
                            <td>
                                <strong>Daily Minimum Running Allowance (Threshold)</strong>
                                <span class="tariff-desc-sub">Standard operational limit per calendar day</span>
                            </td>
                            <td class="right">{$kmRunning} KM / Day</td>
                            <td class="right">{$duration} Day(s)</td>
                            <td class="right"><span class="term-badge-included">Included</span></td>
                        </tr>
                        <tr>
                            <td class="center">3.</td>
                            <td>
                                <strong>Professional Commercial Chauffeur Duty</strong>
                                <span class="tariff-desc-sub">Chauffeur daytime driving duty and outstation allowance</span>
                            </td>
                            <td class="right">Standard</td>
                            <td class="right">{$duration} Day(s)</td>
                            <td class="right"><span class="{$driverClass}">{$driverText}</span></td>
                        </tr>
                        <tr>
                            <td class="center">4.</td>
                            <td>
                                <strong>Toll Tax, Express Highway &amp; State Border Permits</strong>
                                <span class="tariff-desc-sub">Commercial border taxes and national expressway toll charges</span>
                            </td>
                            <td class="right">Permit / Plaza</td>
                            <td class="right">As per Route</td>
                            <td class="right"><span class="{$tollClass}">{$tollText}</span></td>
                        </tr>
                        <!-- Grand Total Summary Row -->
                        <tr class="total-row">
                            <td colspan="4" class="right">TOTAL ESTIMATED QUOTATION COST:</td>
                            <td class="right grand-amount">{$sym} {$totalCost}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Amount Chargeable in Words (Indian Standard) -->
            <div class="indian-words-strip">
                <strong>Amount Chargeable in Words:</strong> {$amountInWords}
            </div>

            <!-- Payment Schedule & Advance Breakdown -->
            <div class="indian-payment-schedule">
                <div class="payment-pill advance">
                    <div class="meta">
                        <div class="lbl">Advance Booking Deposit ({$advPercent}%)</div>
                        <div class="desc">Required to confirm fleet reservation</div>
                    </div>
                    <div class="amount">{$sym} {$advAmount}</div>
                </div>
                <div class="payment-pill">
                    <div class="meta">
                        <div class="lbl">Balance Due on Departure</div>
                        <div class="desc">Payable at start of journey / trip</div>
                    </div>
                    <div class="amount">{$sym} {$balAmount}</div>
                </div>
            </div>

            <!-- Bank & Digital Payment Details (Indian Format) -->
            {$bankDetailsHtml}

            <!-- Commercial Policy & Terms & Conditions -->
            <div class="indian-terms-card">
                <div class="indian-terms-header">
                    <span>Terms &amp; Conditions</span>
                    <span style="font-size:11px;color:#64748b;text-transform:none;">Standard Outstation Commercial Policy</span>
                </div>
                <ul class="indian-terms-list">
                    <li class="indian-term-item"><strong>Garage to Garage Calculation:</strong> Total running distance (kilometres) and duty timings are calculated starting and closing from our garage base ({$garageText}).</li>
                    <li class="indian-term-item"><strong>Toll Plaza &amp; Inter-State Taxes:</strong> National highway toll plaza taxes, commercial border entry taxes, and airport/station parking fees are <strong>{$tollText} / {$stateTaxText} / {$parkingText}</strong>.</li>
                    <li class="indian-term-item"><strong>Chauffeur Duty &amp; Allowance:</strong> Commercial driver day duty and night halt allowances are <strong>{$driverText}</strong>.</li>
                    <li class="indian-term-item"><strong>Mountain / Uphill Incline AC Policy:</strong> As per transport safety regulations, the vehicle air conditioner (AC) will remain switched off on steep uphill mountain road sections to ensure engine performance and passenger safety.</li>
                    <li class="indian-term-item"><strong>Night Driving Regulations:</strong> Night driving between 11:00 PM and 06:00 AM requires prior operational confirmation and attracts additional night allowance.</li>
                    <li class="indian-term-item"><strong>Extra Running Tariff:</strong> Any additional running beyond the estimated distance will be charged at <strong>{$sym} {$extraKm} per KM</strong>.</li>
                    <li class="indian-term-item"><strong>Mechanical Breakdown Backup:</strong> In the rare event of an unavoidable mechanical breakdown, an equivalent replacement vehicle will be arranged promptly to ensure seamless travel.</li>
                </ul>

                {$customRulesBlock}
            </div>

            <!-- Dual Signatures Block (Indian Format) -->
            <div class="indian-sign-grid">
                <div class="indian-sign-box">
                    <div class="sign-placeholder-line"></div>
                    <div class="sign-title">Customer Acceptance &amp; Signature</div>
                    <div class="sign-subtitle">I accept the quoted pricing, route terms, and commercial policies.</div>
                </div>
                <div class="indian-sign-box right">
                    <div class="sign-placeholder-line"></div>
                    <div class="sign-title">For {$compName}</div>
                    <div class="sign-subtitle">Authorized Signatory / Fleet Operations (Official Stamp)</div>
                </div>
            </div>

            <!-- Document Footer with Aleya.in Credit -->
            <footer class="indian-doc-footer">
                <div class="footer-meta-text">
                    Computer generated commercial estimate &bull; Valid as per terms &amp; conditions stated above.
                </div>
                <div class="footer-credit">
                    Powered by <a href="https://aleya.in" target="_blank" rel="noopener" class="credit-link">Aleya.in</a> &bull; Enterprise Fleet &amp; Travel Solutions
                </div>
            </footer>
        </main>
    </div>

    <!-- Page Break Print Running Footer -->
    <div class="print-page-footer">
        Powered by <a href="https://aleya.in" target="_blank" rel="noopener" style="color:#2563eb;text-decoration:none;font-weight:700;">Aleya.in</a> &bull; Fleet &amp; Travel Management Platform
    </div>

    <!-- Floating Action Bar for Print & Download -->
    <div class="action-floating-bar">
        <button type="button" class="btn-print-action" onclick="window.print()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Print / Save as PDF
        </button>
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

if (defined('TRANSPORTER_QUOTATION_HELPERS_ONLY') && TRANSPORTER_QUOTATION_HELPERS_ONLY) {
    return;
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
    $requestedTemplate = trim((string)($_GET['template'] ?? ($desc['template'] ?? 'mit')));
    $renderedHtml = buildTransporterProposalHtml((int)$row['quotation_id'], $desc, $companyMeta, $requestedTemplate);

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

    $action = $_GET['action'] ?? ($data['action'] ?? '');
    if ($action === 'save_pdf') {
        $quoteId = (int)($data['quotation_id'] ?? ($_POST['quotation_id'] ?? $_GET['quotation_id'] ?? 0));
        $pdfData = $data['pdf_data'] ?? ($_POST['pdf_data'] ?? '');

        if ($quoteId <= 0 || empty($pdfData)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Quotation ID and valid PDF data are required.']);
            exit;
        }

        if (strpos($pdfData, 'base64,') !== false) {
            $pdfData = substr($pdfData, strpos($pdfData, 'base64,') + 7);
        }
        $decoded = base64_decode($pdfData);
        if ($decoded === false || strlen($decoded) < 10) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid PDF binary data provided.']);
            exit;
        }

        $uploadDir = $docRoot . '/upload/quotations';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $fileName = 'quotation_' . $quoteId . '_' . substr(md5('isw_quote_' . $quoteId), 0, 8) . '.pdf';
        $filePath = $uploadDir . '/' . $fileName;
        file_put_contents($filePath, $decoded);

        $relUrl = '/upload/quotations/' . $fileName;
        $host = $_SERVER['HTTP_HOST'] ?? (defined('SITE_DOMAIN') ? SITE_DOMAIN : '');
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
        $protocol = $isHttps ? 'https://' : 'http://';
        $fullUrl = (!empty($host) ? $protocol . $host : '') . $relUrl;

        echo json_encode([
            'status'    => 'success',
            'message'   => 'Quotation PDF saved successfully.',
            'pdf_url'   => $relUrl,
            'full_url'  => $fullUrl,
            'file_name' => 'Quotation_TQ-' . str_pad((string)$quoteId, 5, '0', STR_PAD_LEFT) . '.pdf'
        ]);
        exit;
    }

    // Action: Send Quotation Proposal via Email & WhatsApp
    if ($action === 'send') {
        $quoteId = (int)($data['quotation_id'] ?? ($_POST['quotation_id'] ?? 0));
        $sendEmail = isset($data['send_email']) ? (!empty($data['send_email']) && $data['send_email'] !== 'false' && $data['send_email'] !== '0') : false;
        $sendWhatsApp = isset($data['send_whatsapp']) ? (!empty($data['send_whatsapp']) && $data['send_whatsapp'] !== 'false' && $data['send_whatsapp'] !== '0') : false;
        $pdfUrl = trim((string)($data['pdf_url'] ?? ''));

        if ($quoteId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Valid Quotation ID is required.']);
            exit;
        }

        $qRes = mysqli_query($connection, "SELECT * FROM {$tblPrefix}quotation WHERE quotation_id = {$quoteId} LIMIT 1");
        if (!$qRes || mysqli_num_rows($qRes) === 0) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Quotation not found.']);
            exit;
        }
        $qRow = mysqli_fetch_assoc($qRes);
        $leadId = (int)$qRow['quotation_ref_id'];
        $quotePayload = !empty($qRow['quotation_description']) ? json_decode($qRow['quotation_description'], true) : [];

        $customerName   = trim((string)($quotePayload['customer']['name'] ?? ''));
        $customerMobile = trim((string)($quotePayload['customer']['mobile'] ?? ''));
        $customerEmail  = trim((string)($quotePayload['customer']['email'] ?? ''));
        $vehicleTitle   = trim((string)($quotePayload['vehicle']['vehicle_title'] ?? 'Vehicle'));
        $vehicleSeating = trim((string)($quotePayload['vehicle']['vehicle_seating'] ?? ''));
        $destination    = trim((string)($quotePayload['travel']['destination'] ?? ''));
        $pickupLocation = trim((string)($quotePayload['travel']['pickup_location'] ?? ''));
        $dropLocation   = trim((string)($quotePayload['travel']['drop_location'] ?? ''));
        $travelDate     = trim((string)($quotePayload['travel']['travel_date'] ?? ''));
        $travelDuration = trim((string)($quotePayload['travel']['duration_days'] ?? '1'));
        $passengers     = trim((string)($quotePayload['travel']['passengers'] ?? ($quotePayload['passengers'] ?? ($quotePayload['travel']['persons'] ?? ($quotePayload['travel']['pax'] ?? '')))));
        $totalCost      = (float)($quotePayload['billing']['total_cost'] ?? $qRow['quotation_price']);
        $currencySymbol = trim((string)($quotePayload['billing']['currency_symbol'] ?? '₹'));

        if (empty($passengers) && $leadId > 0) {
            $profRes = mysqli_query($connection, "SELECT number_of_adults, number_of_childrens FROM {$tblPrefix}enquiry_profile WHERE enquiry_profile_ref_id = {$leadId} LIMIT 1");
            if ($profRes && mysqli_num_rows($profRes) > 0) {
                $pRow = mysqli_fetch_assoc($profRes);
                $paxArr = [];
                if (!empty($pRow['number_of_adults'])) {
                    $paxArr[] = (int)$pRow['number_of_adults'] . ' Adult' . ((int)$pRow['number_of_adults'] > 1 ? 's' : '');
                }
                if (!empty($pRow['number_of_childrens'])) {
                    $paxArr[] = (int)$pRow['number_of_childrens'] . ' Child' . ((int)$pRow['number_of_childrens'] > 1 ? 'ren' : '');
                }
                if (!empty($paxArr)) {
                    $passengers = implode(', ', $paxArr);
                }
            }
        }

        $companyMeta = getTransporterCompanyMeta($connection, $siteId, $tblPrefix);
        $compName = $companyMeta['name'] ?? 'Aleya Fleet Logistics';
        $quoteDisplayId = 'TQ-' . str_pad((string)$quoteId, 5, '0', STR_PAD_LEFT);

        $encodedId = base64_encode((string)$quoteId);
        $host = $_SERVER['HTTP_HOST'] ?? (defined('SITE_DOMAIN') ? SITE_DOMAIN : '');
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
        $protocol = $isHttps ? 'https://' : 'http://';

        // Ensure WhatsApp device helper is loaded early
        require_once dirname(__DIR__) . '/whatsapp/device_helper.php';

        // Check if there is an actual physical PDF file on disk
        $localPdfRel = '';
        $isPhysicalPdf = false;
        if (!empty($pdfUrl)) {
            $parsedPath = parse_url($pdfUrl, PHP_URL_PATH);
            if (!empty($parsedPath) && strpos($parsedPath, '/upload/') !== false) {
                $diskFile = $docRoot . '/' . ltrim($parsedPath, '/');
                if (file_exists($diskFile) && filesize($diskFile) > 500) {
                    $fp = @fopen($diskFile, 'rb');
                    $head = $fp ? fread($fp, 5) : '';
                    if ($fp) fclose($fp);
                    if (strpos($head, '%PDF') === 0) {
                        $isPhysicalPdf = true;
                        $localPdfRel = '/' . ltrim($parsedPath, '/');
                    }
                }
            }
        }

        $uploadDir = $docRoot . '/upload/quotations';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        $quoteHash = substr(md5('isw_quote_' . $quoteId), 0, 8);
        $fileName = 'quotation_' . $quoteId . '_' . $quoteHash . '.pdf';
        $diskFile = $uploadDir . '/' . $fileName;

        // If client provided a saved PDF file that exists on disk, check it
        $needsGeneration = true;
        if ($isPhysicalPdf && !empty($localPdfRel)) {
            $checkPath = $docRoot . '/' . ltrim($localPdfRel, '/');
            if (file_exists($checkPath) && filesize($checkPath) > 1000) {
                $needsGeneration = false;
            }
        }

        // Generate the PDF directly using the selected transporter quotation format
        if ($needsGeneration && function_exists('generateHeadlessPdf')) {
            $selectedFormat = $quotePayload['template'] ?? ($data['template'] ?? ($_POST['template'] ?? ($_GET['template'] ?? 'mit')));
            $proposalHtml = buildTransporterProposalHtml($quoteId, $quotePayload, $companyMeta, $selectedFormat);
            $cleanProposalHtml = preg_replace('/<div\s+class=["\']v3-pdf-actions-bar["\'].*?<\/div>/is', '', $proposalHtml);

            $tmpHtmlFile = $uploadDir . '/tmp_quote_' . $quoteId . '_' . $quoteHash . '.html';
            file_put_contents($tmpHtmlFile, $cleanProposalHtml);

            generateHeadlessPdf('file://' . $tmpHtmlFile, $diskFile);
            @unlink($tmpHtmlFile);

            if (file_exists($diskFile) && filesize($diskFile) > 500) {
                $isPhysicalPdf = true;
                $localPdfRel = '/upload/quotations/' . $fileName;
            }
        }

        $fullPdfUrl = $pdfUrl;
        if (!empty($localPdfRel) && !empty($host)) {
            $fullPdfUrl = $protocol . $host . $localPdfRel;
        } elseif (empty($fullPdfUrl) && !empty($host)) {
            $fullPdfUrl = $protocol . $host . "/quote/{$encodedId}/?download=true";
        }

        $whatsappSent = false;
        $whatsappError = null;
        $emailSent = false;
        $emailError = null;
        $sentChannels = [];

        // 1. Dispatch Email
        if ($sendEmail) {
            if (!empty($customerEmail) && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                $toAddress = $customerEmail;
                $subject = "Your Fleet Quotation {$quoteDisplayId} for {$vehicleTitle} | {$compName}";

                $safeCustName = htmlspecialchars($customerName ?: 'Customer', ENT_QUOTES, 'UTF-8');
                $safeCompName = htmlspecialchars($compName, ENT_QUOTES, 'UTF-8');
                $safeVeh = htmlspecialchars($vehicleTitle, ENT_QUOTES, 'UTF-8');
                $costFmt = number_format($totalCost, 2);
                $safeRoute = htmlspecialchars((!empty($pickupLocation) ? $pickupLocation . ' to ' : '') . $destination, ENT_QUOTES, 'UTF-8');

                $email_body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 24px; color: #1e293b; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px;'>
                        <div style='border-bottom: 2px solid #059669; padding-bottom: 12px; margin-bottom: 20px;'>
                            <h2 style='color: #0f172a; margin: 0; font-size: 20px;'>{$safeCompName}</h2>
                            <p style='color: #64748b; margin: 4px 0 0 0; font-size: 13px;'>Trip Estimate &amp; Commercial Fleet Quotation (#{$quoteDisplayId})</p>
                        </div>
                        <p style='font-size: 14px; line-height: 22px;'>Dear <strong>{$safeCustName}</strong>,</p>
                        <p style='font-size: 14px; line-height: 22px;'>Thank you for considering <strong>{$safeCompName}</strong>. We are pleased to share your customized commercial quotation for <strong>{$safeVeh}</strong>.</p>
                        
                        <div style='background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px; margin: 20px 0;'>
                            <p style='margin: 4px 0; font-size: 14px;'><strong>Fleet:</strong> {$safeVeh}</p>
                            " . (!empty($safeRoute) ? "<p style='margin: 4px 0; font-size: 14px;'><strong>Route:</strong> {$safeRoute}</p>" : "") . "
                            " . (!empty($travelDate) ? "<p style='margin: 4px 0; font-size: 14px;'><strong>Date:</strong> {$travelDate} ({$travelDuration} Days)</p>" : "") . "
                            <p style='margin: 8px 0 4px 0; font-size: 16px; color: #059669;'><strong>Total Tariff:</strong> {$currencySymbol} {$costFmt}</p>
                        </div>

                        " . (!empty($fullPdfUrl) ? "
                        <div style='margin: 25px 0;'>
                            <a href='{$fullPdfUrl}' style='display: inline-block; background: #059669; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 14px;'>Download Quotation PDF</a>
                        </div>
                        " : "") . "

                        <p style='font-size: 13px; line-height: 20px; color: #64748b; margin-top: 24px;'>
                            Please reply directly to this email or message us on WhatsApp to confirm your booking.<br><br>
                            —<br>
                            <em>Powered by Aleya.in</em>
                        </p>
                    </div>
                ";

                if (defined('SEND_EMAIL') && file_exists(SEND_EMAIL)) {
                    ob_start();
                    try {
                        require SEND_EMAIL;
                        $emailSent = true;
                        $sentChannels[] = "Email";
                    } catch (\Throwable $e) {
                        $emailError = $e->getMessage();
                    }
                    $emailOut = ob_get_clean();
                    if (!empty($emailOut) && (stripos($emailOut, 'Mailer Error') !== false || stripos($emailOut, 'Exception') !== false)) {
                        $emailError = trim(strip_tags($emailOut));
                        $emailSent = false;
                    }
                } else {
                    $emailError = 'Mailer service script not found.';
                }
            } else {
                $emailError = 'Customer email address is invalid or missing.';
            }
        } else {
            $emailError = 'Skipped by user toggle.';
        }

        // 2. Dispatch WhatsApp
        if ($sendWhatsApp) {
            $cleanPhone = preg_replace('/[^0-9]/', '', (string)$customerMobile);
            if (!empty($cleanPhone)) {
                require_once dirname(__DIR__) . '/whatsapp/device_helper.php';

                // Format WhatsApp text with Powered by Aleya.in footer (pure caption without download links)
                $waText = !empty($data['whatsapp_text']) ? trim($data['whatsapp_text']) : '';
                if (empty($waText)) {
                    $waLines = [];
                    $waLines[] = "🚗 *{$compName}*";
                    $waLines[] = "📋 *TRIP ESTIMATE & FLEET QUOTATION* (#{$quoteDisplayId})";
                    $waLines[] = "━━━━━━━━━━━━━━━━━━━━━━";
                    if (!empty($customerName)) {
                        $waLines[] = "👤 *Customer:* " . $customerName;
                    }
                    if (!empty($destination)) {
                        $waLines[] = "📍 *Destination:* " . $destination;
                        $routeStr = (!empty($pickupLocation) ? $pickupLocation . " ➔ " : "") . $destination . (!empty($dropLocation) && $dropLocation !== $destination ? " ➔ " . $dropLocation : "");
                        if ($routeStr !== $destination) {
                            $waLines[] = "🗺️ *Route:* " . $routeStr;
                        }
                    }
                    if (!empty($travelDate)) {
                        $formattedDate = date('d M, Y', strtotime($travelDate));
                        $durationLabel = (!empty($travelDuration) ? " ({$travelDuration} " . ($travelDuration == '1' ? 'Day' : 'Days') . ")" : "");
                        $waLines[] = "📅 *Travel Date:* " . $formattedDate . $durationLabel;
                    }
                    if (!empty($passengers)) {
                        $waLines[] = "👥 *No. of Persons:* " . $passengers . (is_numeric($passengers) ? " Pax" : "");
                    } elseif (!empty($vehicleSeating)) {
                        $waLines[] = "👥 *Seating Capacity:* " . $vehicleSeating . " Seater";
                    }
                    if (!empty($vehicleTitle)) {
                        $waLines[] = "🚘 *Fleet:* " . $vehicleTitle . (!empty($vehicleSeating) ? " (" . $vehicleSeating . " Seater AC)" : "");
                    }
                    $waLines[] = "";
                    $waLines[] = "💵 *Total Cost:* {$currencySymbol} " . number_format($totalCost, 2);
                    $advanceAmount = (float)($quotePayload['billing']['advance_amount'] ?? 0);
                    if ($advanceAmount > 0) {
                        $waLines[] = "💳 *Advance to Book:* {$currencySymbol} " . number_format($advanceAmount, 2);
                    }
                    $waLines[] = "";
                    $waLines[] = "Thank you for choosing {$compName}! Please reply with 'CONFIRM' to reserve your vehicle.";
                    $waLines[] = "";
                    $waLines[] = "—";
                    $waLines[] = "_Powered by Aleya.in_";
                    $waText = implode("\n", $waLines);
                } else {
                    // Strictly strip any links / download text from whatsapp text
                    $waText = preg_replace('/(?:📄|🌐)?\s*\*(?:Download|View)[^*]*\*\s*\n\S+/u', '', $waText);
                    $waText = preg_replace('/https?:\/\/\S+/i', '', $waText);
                    $waText = preg_replace('/www\.\S+/i', '', $waText);
                    $waText = trim(preg_replace("/\n{3,}/", "\n\n", $waText));
                }

                $waMsgType = $isPhysicalPdf ? 'document' : 'text';
                $waMediaUrl = $isPhysicalPdf ? $localPdfRel : '';

                $waRes = sendWhatsAppDirectMessage(
                    $connection,
                    $siteId,
                    $userId,
                    $cleanPhone,
                    $waText,
                    [
                        'lead_id'   => $leadId,
                        'type'      => $waMsgType,
                        'media_url' => $waMediaUrl,
                        'file_name' => "Quotation_{$quoteDisplayId}.pdf"
                    ]
                );

                if (isset($waRes['status']) && $waRes['status'] === 'success') {
                    $whatsappSent = true;
                    $sentChannels[] = "WhatsApp";
                } else {
                    $whatsappError = $waRes['msg'] ?? 'WhatsApp device error.';
                }
            } else {
                $whatsappError = 'Customer mobile number is missing.';
            }
        } else {
            $whatsappError = 'Skipped by user toggle.';
        }

        // Record delivery metadata in quotation_description
        $quotePayload['delivery'] = [
            'whatsapp'       => $whatsappSent,
            'whatsapp_error' => $whatsappError,
            'email'          => $emailSent,
            'email_error'    => $emailError,
            'channels'       => $sentChannels,
            'sent_at'        => date('Y-m-d H:i:s')
        ];
        $updDescJson = mysqli_real_escape_string($connection, json_encode($quotePayload, JSON_UNESCAPED_UNICODE));
        mysqli_query($connection, "UPDATE {$tblPrefix}quotation SET quotation_description = '{$updDescJson}' WHERE quotation_id = {$quoteId}");

        // Record timeline note
        if (!empty($sentChannels)) {
            $summaryNote = "Transporter quotation #{$quoteId} sent to customer via " . implode(' & ', $sentChannels);
            $escSummaryNote = mysqli_real_escape_string($connection, $summaryNote);
            @mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes (
                enquiry_notes_ref_id,
                enquiry_notes_user_id,
                enquiry_notes_meta_type,
                enquiry_notes_meta_value
            ) VALUES (
                {$leadId},
                {$userId},
                'itinerary',
                '{$escSummaryNote}'
            )");
            @mysqli_query($connection, "UPDATE {$tblPrefix}enquiry SET enquiry_status = 9, enquiry_update = NOW() WHERE enquiry_id = {$leadId}");
        }

        echo json_encode([
            'status'         => 'success',
            'message'        => !empty($sentChannels) ? ("Quotation proposal sent via " . implode(' & ', $sentChannels) . "!") : "Delivery preferences recorded.",
            'sent_channels'  => $sentChannels,
            'whatsapp_sent'  => $whatsappSent,
            'whatsapp_error' => $whatsappError,
            'email_sent'     => $emailSent,
            'email_error'    => $emailError
        ]);
        exit;
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
    $passengers     = trim((string)($travel['passengers'] ?? ($travel['persons'] ?? ($travel['pax'] ?? ($travel['adults'] ?? '')))));

    if (empty($passengers) && $leadId > 0) {
        $profRes = mysqli_query($connection, "SELECT number_of_adults, number_of_childrens FROM {$tblPrefix}enquiry_profile WHERE enquiry_profile_ref_id = {$leadId} LIMIT 1");
        if ($profRes && mysqli_num_rows($profRes) > 0) {
            $pRow = mysqli_fetch_assoc($profRes);
            $paxArr = [];
            if (!empty($pRow['number_of_adults'])) {
                $paxArr[] = (int)$pRow['number_of_adults'] . ' Adult' . ((int)$pRow['number_of_adults'] > 1 ? 's' : '');
            }
            if (!empty($pRow['number_of_childrens'])) {
                $paxArr[] = (int)$pRow['number_of_childrens'] . ' Child' . ((int)$pRow['number_of_childrens'] > 1 ? 'ren' : '');
            }
            if (!empty($paxArr)) {
                $passengers = implode(', ', $paxArr);
            }
        }
    }

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
    $totalRunningKm = (float)($billing['total_running_km'] ?? 0);
    if ($kmRunning > 0 && $totalRunningKm < $kmRunning) {
        $totalRunningKm = $kmRunning;
    }
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

    $selectedTemplate = trim((string)($input['template'] ?? ($input['format'] ?? 'mit')));
    if (empty($selectedTemplate)) $selectedTemplate = 'mit';

    // Construct Complete Proposal Payload
    $quotePayload = [
        'type'        => 'transporter_quotation',
        'template'    => $selectedTemplate,
        'title'       => 'Fleet Quotation - ' . (!empty($vehicleTitle) ? $vehicleTitle : 'Vehicle'),
        'destination' => $destination,
        'duration'    => $travelDuration . ($travelDuration == '1' ? ' Day' : ' Days'),
        'passengers'  => $passengers,
        'customer'    => [
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
            'passengers'      => $passengers,
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
            'total_running_km' => $totalRunningKm,
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
    $companyMeta = getTransporterCompanyMeta($connection, $siteId, $tblPrefix);
    $compName = $companyMeta['name'] ?? 'Aleya Fleet Logistics';
    $quoteDisplayId = 'TQ-' . str_pad((string)$quotationId, 5, '0', STR_PAD_LEFT);
    $formattedTotal = formatIndianNumber($totalCost);
    $formattedAdv = formatIndianNumber($advanceAmount);
    $formattedBal = formatIndianNumber($balanceAmount);
    $waWords = getIndianCurrencyWords($totalCost);

    $waLines = [];
    $waLines[] = "🚗 *{$compName}*";
    $waLines[] = "📋 *TRIP ESTIMATE & FLEET QUOTATION* (#{$quoteDisplayId})";
    $waLines[] = "━━━━━━━━━━━━━━━━━━━━━━";
    if (!empty($customerName)) {
        $waLines[] = "👤 *Customer:* " . $customerName;
    }
    if (!empty($destination)) {
        $waLines[] = "📍 *Destination:* " . $destination;
        $routeStr = (!empty($pickupLocation) ? $pickupLocation . " ➔ " : "") . $destination . (!empty($dropLocation) && $dropLocation !== $destination ? " ➔ " . $dropLocation : "");
        if ($routeStr !== $destination) {
            $waLines[] = "🗺️ *Route:* " . $routeStr;
        }
    }
    if (!empty($travelDate)) {
        $formattedDate = date('d M, Y', strtotime($travelDate));
        $durationLabel = (!empty($travelDuration) ? " ({$travelDuration} " . ($travelDuration == '1' ? 'Day' : 'Days') . ")" : "");
        $waLines[] = "📅 *Travel Date:* " . $formattedDate . $durationLabel;
    }
    if (!empty($passengers)) {
        $waLines[] = "👥 *No. of Persons:* " . $passengers . (is_numeric($passengers) ? " Pax" : "");
    } elseif (!empty($vehicleSeating)) {
        $waLines[] = "👥 *Seating Capacity:* " . $vehicleSeating . " Seater";
    }
    if (!empty($vehicleTitle)) {
        $waLines[] = "🚘 *Fleet:* " . $vehicleTitle . (!empty($vehicleSeating) ? " (" . $vehicleSeating . " Seater AC)" : "");
    }
    $waLines[] = "";
    $waLines[] = "💵 *TARIFF & COMMERCIAL BILLING*";
    $waLines[] = "• *Total Cost:* {$currencySymbol} {$formattedTotal}";
    $waLines[] = "• *Amount in Words:* " . $waWords;
    if ($totalRunningKm > 0) {
        $formattedWaKm = formatIndianNumber($totalRunningKm, ($totalRunningKm == (int)$totalRunningKm ? 0 : 2));
        $waLines[] = "• *Estimated Running:* " . $formattedWaKm . " KM";
    }
    if ($extraKmCharge > 0) {
        $waLines[] = "• *Tariff Rate / Extra KM:* {$currencySymbol} " . formatIndianNumber($extraKmCharge) . " / KM";
    }
    $waLines[] = "• *Advance Booking Deposit ({$advancePercent}%):* {$currencySymbol} {$formattedAdv}";
    $waLines[] = "• *Balance on Departure:* {$currencySymbol} {$formattedBal}";
    $waLines[] = "";
    $waLines[] = "📜 *KEY POLICIES & TERMS*";
    $waLines[] = "• Garage to Garage: " . ($garageToGarage === 'yes' ? "Applicable" : "City Base");
    $waLines[] = "• Toll Taxes & Expressways: " . ($tollTax === 'yes' ? "Included in Quote" : "Excluded (At Actuals)");
    $waLines[] = "• State Commercial Entry Tax: " . ($stateTax === 'yes' ? "Included in Quote" : "Excluded (At Actuals)");
    $waLines[] = "• Driver Allowance: " . ($driverAllowance === 'yes' ? "Included in Quote" : "Excluded");
    $waLines[] = "• Parking Charges: " . ($parkingCharges === 'yes' ? "Included in Quote" : "Excluded (Paid by Guest)");
    $waLines[] = "• Mountain Section: AC remains switched off on steep uphill climbs as per safety norms.";

    if (!empty($customRules)) {
        foreach ($customRules as $cr) {
            if (!empty($cr['enabled']) && !empty($cr['title'])) {
                $waLines[] = "• " . trim($cr['title']) . (!empty($cr['description']) ? ": " . trim($cr['description']) : "");
            }
        }
    }

    if (!empty($companyMeta['upi_id'])) {
        $waLines[] = "";
        $waLines[] = "💳 *PAYMENT DETAILS*";
        $waLines[] = "UPI ID: " . $companyMeta['upi_id'] . " (GPay/PhonePe/Paytm)";
    }
    $waLines[] = "";
    $waLines[] = "Thank you for choosing {$compName}! Please reply with 'CONFIRM' to reserve your vehicle.";
    $waLines[] = "";
    $waLines[] = "—";
    $waLines[] = "_Powered by Aleya.in_";
    $whatsappText = implode("\n", $waLines);

    $companyMeta = getTransporterCompanyMeta($connection, $siteId, $tblPrefix);
    $renderedHtml = buildTransporterProposalHtml($quotationId, $quotePayload, $companyMeta, $selectedTemplate);

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
