<?php
/**
 * V3 Commercial Transport Quotation Template: MIT Proposal Layout
 * Path: app/dashboard/v3/default/desktop/templates/quote/mit/index.php
 * Description: MIT-style formal legal passenger transport quotation layout with
 *              A4 printable dimensions, itemized tariff particulars, UPI QR code,
 *              commercial operating terms, dual signatures, and Aleya.in credits.
 */

if (!function_exists('fixQuoteImageUrl')) {
    function fixQuoteImageUrl($url)
    {
        if (empty($url)) return '';
        $url = trim($url);
        if (preg_match('/^https?:\/\//i', $url)) return $url;

        $baseUrl = '';
        if (defined('SITE_URL') && !empty(SITE_URL)) {
            $baseUrl = rtrim(SITE_URL, '/');
        } elseif (defined('SITE_DOMAIN') && !empty(SITE_DOMAIN)) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $baseUrl = $protocol . rtrim(SITE_DOMAIN, '/');
        } elseif (!empty($_SERVER['HTTP_HOST'])) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $baseUrl = $protocol . $_SERVER['HTTP_HOST'];
        }

        if (!empty($baseUrl)) {
            return $baseUrl . '/' . ltrim($url, '/');
        }
        return '/' . ltrim($url, '/');
    }
}

if (!function_exists('formatIndianPrice')) {
    function formatIndianPrice($num)
    {
        $num = (float)$num;
        $rounded = number_format($num, 2, '.', '');
        $parts = explode('.', $rounded);
        $intPart = $parts[0];
        $decPart = isset($parts[1]) ? '.' . $parts[1] : '';

        if (strlen($intPart) <= 3) {
            return $intPart . $decPart;
        }

        $lastThree = substr($intPart, -3);
        $remaining = substr($intPart, 0, -3);
        $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
        return $remaining . ',' . $lastThree . $decPart;
    }
}

if (!function_exists('numberToIndianWords')) {
    function numberToIndianWords($num)
    {
        $num = round((float)$num, 2);
        if ($num <= 0) return 'Rupees Zero Only';

        $words = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty',
            30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy',
            80 => 'Eighty', 90 => 'Ninety'
        ];

        $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];

        $numStr = number_format($num, 2, '.', '');
        list($integer, $fraction) = explode('.', $numStr);
        $intVal = (int)$integer;

        $str = [];
        $crore = floor($intVal / 10000000);
        $intVal %= 10000000;
        $lakh = floor($intVal / 100000);
        $intVal %= 100000;
        $thousand = floor($intVal / 1000);
        $intVal %= 1000;
        $hundred = floor($intVal / 100);
        $rest = $intVal % 100;

        if ($crore > 0) {
            $str[] = numberToIndianWords($crore) . ' Crore';
        }
        if ($lakh > 0) {
            $str[] = ($lakh < 21 ? $words[$lakh] : $words[10 * floor($lakh / 10)] . ($lakh % 10 ? ' ' . $words[$lakh % 10] : '')) . ' Lakh';
        }
        if ($thousand > 0) {
            $str[] = ($thousand < 21 ? $words[$thousand] : $words[10 * floor($thousand / 10)] . ($thousand % 10 ? ' ' . $words[$thousand % 10] : '')) . ' Thousand';
        }
        if ($hundred > 0) {
            $str[] = $words[$hundred] . ' Hundred';
        }
        if ($rest > 0) {
            $str[] = ($rest < 21 ? $words[$rest] : $words[10 * floor($rest / 10)] . ($rest % 10 ? ' ' . $words[$rest % 10] : ''));
        }

        $rupees = implode(' ', array_filter($str));
        $paise = '';
        $point = (int)$fraction;
        if ($point > 0) {
            $pWords = ($point < 21 ? $words[$point] : $words[10 * floor($point / 10)] . ($point % 10 ? ' ' . $words[$point % 10] : ''));
            $paise = ' and ' . trim($pWords) . ' Paise';
        }

        return 'Rupees ' . trim($rupees) . $paise . ' Only';
    }
}

// -----------------------------------------------------------------------------
// Resolve Data Payload & Variables
// -----------------------------------------------------------------------------
$qId = isset($quotationId) ? (int)$quotationId : (isset($quoteId) ? (int)$quoteId : (isset($_GET['id']) ? (int)$_GET['id'] : 1001));
$quotePayload = isset($quotePayload) && is_array($quotePayload) ? $quotePayload : (isset($details) && is_array($details) ? $details : []);

$companyInfo = isset($companyMeta) && is_array($companyMeta) ? $companyMeta : (isset($company) && is_array($company) ? $company : []);

// Customer Details
$customerData = $quotePayload['customer'] ?? [];
$custName    = htmlspecialchars($customerData['name'] ?? 'Valued Customer');
$custMobile  = htmlspecialchars($customerData['mobile'] ?? 'Not Specified');
$custEmail   = htmlspecialchars($customerData['email'] ?? 'Not Specified');
$custAddress = htmlspecialchars($customerData['address'] ?? 'Not Specified');

// Trip & Journey Details
$tripData    = $quotePayload['travel'] ?? ($quotePayload['trip'] ?? []);
$dest        = htmlspecialchars($tripData['destination'] ?? 'Outstation Destination');
$pickup      = htmlspecialchars($tripData['pickup_location'] ?? ($tripData['pickup_point'] ?? 'Garage Base'));
$drop        = htmlspecialchars($tripData['drop_location'] ?? ($tripData['drop_point'] ?? 'Return Garage'));
$tDate       = htmlspecialchars($tripData['travel_date'] ?? date('Y-m-d'));
$duration    = max(1, (int)($tripData['duration_days'] ?? 1));

// Assigned Vehicle
$vehicleData = $quotePayload['vehicle'] ?? [];
$vehTitle    = htmlspecialchars($vehicleData['vehicle_title'] ?? ($vehicleData['title'] ?? 'Commercial Fleet Vehicle'));
$vehType     = htmlspecialchars(ucfirst($vehicleData['vehicle_type'] ?? ($vehicleData['type'] ?? 'Sedan / Commercial')));
$vehSeating  = htmlspecialchars((string)($vehicleData['vehicle_seating'] ?? ($vehicleData['seating'] ?? '')));

// Billing Particulars
$billingData    = $quotePayload['billing'] ?? [];
$sym            = htmlspecialchars($billingData['currency_symbol'] ?? '₹');
$totalCostVal   = (float)($billingData['total_cost'] ?? 0);
$totalCost      = formatIndianPrice($totalCostVal);
$kmRunning      = (float)($billingData['km_running'] ?? 250);
$totalRunningKm = (float)($billingData['total_running_km'] ?? $kmRunning);
if ($totalRunningKm <= 0 && $kmRunning > 0) {
    $totalRunningKm = $kmRunning;
}
$extraKm        = (float)($billingData['extra_km_charge'] ?? 0);
$advPercent     = (float)($billingData['advance_percent'] ?? 25);
$advAmount      = formatIndianPrice($billingData['advance_amount'] ?? ($totalCostVal * $advPercent / 100));
$balAmount      = formatIndianPrice($billingData['balance_amount'] ?? ($totalCostVal - ($totalCostVal * $advPercent / 100)));

$amountInWords  = numberToIndianWords($totalCostVal);

$runningAmountStr = formatIndianPrice($totalCostVal);
$distanceQtyStr = ($totalRunningKm > 0) ? number_format($totalRunningKm, 0) . ' KM (Route Est.)' : ($kmRunning > 0 ? number_format($kmRunning, 0) . ' KM (Daily Threshold)' : 'As per Route');

// Commercial Terms
$termsData       = $quotePayload['terms'] ?? [];
$rawDriver       = strtolower((string)($termsData['driver_allowance'] ?? 'yes'));
$driverText      = ($rawDriver === 'yes' || $rawDriver === 'included') ? 'Included' : 'Extra';

$rawToll         = strtolower((string)($termsData['toll_tax'] ?? 'yes'));
$tollText        = ($rawToll === 'yes' || $rawToll === 'included') ? 'Included' : 'At Actual';

$rawState        = strtolower((string)($termsData['state_tax'] ?? 'yes'));
$stateTaxText    = ($rawState === 'yes' || $rawState === 'included') ? 'Included' : 'At Actual';

$rawParking      = strtolower((string)($termsData['parking_charges'] ?? ($termsData['parking'] ?? 'yes')));
$parkingText     = ($rawParking === 'yes' || $rawParking === 'included') ? 'Included' : 'At Actual';

$rawGarage       = strtolower((string)($termsData['garage_to_garage'] ?? ($termsData['garage_calculation'] ?? 'yes')));
$garageText      = ($rawGarage === 'yes' || $rawGarage === 'garage to garage') ? 'Garage to Garage' : 'City Base';
$customRules     = $quotePayload['custom_rules'] ?? ($termsData['custom_rules'] ?? []);

$driverClass = ($driverText === 'Included') ? 'term-badge-included' : 'term-badge-extra';
$tollClass   = ($tollText === 'Included') ? 'term-badge-included' : 'term-badge-extra';

// Operator / Company Metadata
$compName    = htmlspecialchars($companyInfo['name'] ?? (defined('SITE_NAME') ? SITE_NAME : 'Commercial Fleet Operator'));
$compTagline = htmlspecialchars($companyInfo['tagline'] ?? 'Commercial Tourist Fleet & Transport Operator');
$compPhone   = htmlspecialchars($companyInfo['phone'] ?? (defined('SITE_MOBILE') ? SITE_MOBILE : ''));
$compWhatsapp= htmlspecialchars($companyInfo['whatsapp'] ?? (defined('SITE_WHATSAPP') ? SITE_WHATSAPP : $compPhone));
$compEmail   = htmlspecialchars($companyInfo['email'] ?? (defined('SITE_EMAIL') ? SITE_EMAIL : ''));
$compDomain  = htmlspecialchars($companyInfo['domain'] ?? (defined('SITE_DOMAIN') ? SITE_DOMAIN : ''));
$compAddress = htmlspecialchars($companyInfo['address'] ?? (defined('COMPANY_ADDRESS') ? COMPANY_ADDRESS : ''));
$compGstin   = htmlspecialchars(!empty($companyInfo['gstin']) ? $companyInfo['gstin'] : 'Exempt / Transporter RCM');
$compPan     = htmlspecialchars(!empty($companyInfo['pan']) ? $companyInfo['pan'] : '');

$bankName    = htmlspecialchars($companyInfo['bank_name'] ?? '');
$bankAcName  = htmlspecialchars($companyInfo['bank_ac_name'] ?? $compName);
$bankAcNo    = htmlspecialchars($companyInfo['bank_ac_no'] ?? '');
$bankIfsc    = htmlspecialchars($companyInfo['bank_ifsc'] ?? '');
$bankBranch  = htmlspecialchars($companyInfo['bank_branch'] ?? '');
$upiId       = htmlspecialchars($companyInfo['upi_id'] ?? '');

$quoteDisplayId = 'TQ-' . str_pad((string)$qId, 5, '0', STR_PAD_LEFT);
$currentDate = date('d M, Y');
$validDate   = date('d M, Y', strtotime('+7 days'));

// Dynamic UPI QR Code URL
$upiQrUrl = '';
if (!empty($upiId)) {
    $advanceNum = (float)($billingData['advance_amount'] ?? ($totalCostVal * $advPercent / 100));
    $payAmt = $advanceNum > 0 ? $advanceNum : $totalCostVal;
    $upiPayload = "upi://pay?pa=" . rawurlencode($upiId) . "&pn=" . rawurlencode($bankAcName) . "&am=" . number_format($payAmt, 2, '.', '') . "&cu=INR&tn=" . rawurlencode("Quotation " . $quoteDisplayId);
    $upiQrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=180x180&margin=2&data=" . urlencode($upiPayload);
}

// WhatsApp Share URL
$cleanWhatsappDigits = preg_replace('/[^\d]/', '', (string)$custMobile);
$waShareText = "Hello {$custName}, please review your quotation {$quoteDisplayId} for {$vehTitle} ({$pickup} to {$dest}): Total {$sym} {$totalCost}. Advance: {$sym} {$advAmount}. Valid until {$validDate}.";
$waShareUrl = !empty($cleanWhatsappDigits) ? "https://wa.me/{$cleanWhatsappDigits}?text=" . rawurlencode($waShareText) : "https://wa.me/?text=" . rawurlencode($waShareText);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commercial Quotation <?php echo $quoteDisplayId; ?> - <?php echo $custName; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/app/dashboard/v3/default/desktop/templates/quote/mit/style.css">
    <script src="/app/dashboard/v3/default/desktop/templates/quote/mit/script.js" defer></script>
</head>
<body>
    <!-- Floating Action Bar for Print & WhatsApp -->
    <div class="v3-pdf-actions-bar">
        <button type="button" class="btn-pdf-action btn-pdf-print" id="btnPdfPrint">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Print / Save PDF
        </button>
        <button type="button" class="btn-pdf-action btn-pdf-whatsapp" id="btnPdfWhatsapp" data-wa-url="<?php echo htmlspecialchars($waShareUrl); ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
            Share WhatsApp
        </button>
    </div>

    <!-- Main Quotation Proposal Document (A4 Dimension Page) -->
    <section class="page" id="page-quotation">
        <!-- Corporate Letterhead Header (Valid Legal Format) -->
        <header class="indian-letterhead">
            <div class="company-meta-col">
                <h1 class="company-brand-title"><?php echo $compName; ?></h1>
                <div class="company-tagline"><?php echo $compTagline; ?></div>
                <p class="company-address-line"><?php echo $compAddress; ?></p>
                <p class="company-address-line">
                    <strong>Phone:</strong> <?php echo $compPhone; ?> &bull; 
                    <strong>WhatsApp:</strong> <?php echo $compWhatsapp; ?> &bull; 
                    <strong>Email:</strong> <?php echo $compEmail; ?>
                </p>
                <div class="company-tax-strip">
                    <span class="company-tax-badge">GSTIN: <?php echo $compGstin; ?></span>
                    <?php if (!empty($compPan)): ?>
                        <span class="company-tax-badge">PAN: <?php echo $compPan; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($compDomain)): ?>
                        <span class="company-tax-badge">Website: <?php echo $compDomain; ?></span>
                    <?php endif; ?>
                    <span class="company-tax-badge">Commercial Fleet Operator</span>
                </div>
            </div>

            <div class="invoice-title-col">
                <div class="invoice-type-pill">COMMERCIAL TRANSPORT QUOTATION</div>
                <table class="invoice-meta-table">
                    <tr>
                        <td class="lbl">Quote Ref No:</td>
                        <td class="val"><?php echo $quoteDisplayId; ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">Date of Issue:</td>
                        <td class="val"><?php echo $currentDate; ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">Valid Upto:</td>
                        <td class="val"><?php echo $validDate; ?></td>
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
            <!-- 2-Column Structured Info: Customer & Route Schedule -->
            <div class="indian-info-grid">
                <div class="indian-box">
                    <div class="indian-box-header">
                        <span>Customer Details (Quotation To)</span>
                        <span>Client</span>
                    </div>
                    <div class="indian-box-body">
                        <div class="indian-data-row">
                            <span class="lbl">Client Name:</span>
                            <span class="val"><?php echo $custName; ?></span>
                        </div>
                        <div class="indian-data-row">
                            <span class="lbl">Mobile No:</span>
                            <span class="val"><?php echo $custMobile; ?></span>
                        </div>
                        <div class="indian-data-row">
                            <span class="lbl">Email ID:</span>
                            <span class="val"><?php echo $custEmail; ?></span>
                        </div>
                        <div class="indian-data-row">
                            <span class="lbl">Address / City:</span>
                            <span class="val"><?php echo $custAddress; ?></span>
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
                            <span class="val"><?php echo $dest; ?></span>
                        </div>
                        <div class="indian-data-row">
                            <span class="lbl">Pickup Point:</span>
                            <span class="val"><?php echo $pickup; ?></span>
                        </div>
                        <div class="indian-data-row">
                            <span class="lbl">Drop Point:</span>
                            <span class="val"><?php echo $drop; ?></span>
                        </div>
                        <div class="indian-data-row">
                            <span class="lbl">Travel Date:</span>
                            <span class="val"><?php echo $tDate; ?> (<?php echo $duration; ?> Day(s))</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned Fleet Vehicle Specification Card -->
            <div class="indian-fleet-card">
                <div class="fleet-meta">
                    <h3><?php echo $vehTitle; ?></h3>
                    <div class="fleet-tags">
                        <span class="fleet-tag-pill">Category: <?php echo $vehType; ?></span>
                        <?php if (!empty($vehSeating)): ?>
                            <span class="fleet-tag-pill"><?php echo $vehSeating; ?> Seater</span>
                        <?php endif; ?>
                        <span class="fleet-tag-pill">All India Tourist Permit</span>
                        <span class="fleet-tag-pill">Air-Conditioned</span>
                        <span class="fleet-tag-pill">Chauffeur Driven</span>
                    </div>
                </div>
                <div class="fleet-cost-meta">
                    <span class="fleet-cost-lbl">Estimated Fleet Tariff</span>
                    <strong class="fleet-cost-val"><?php echo $sym; ?> <?php echo $totalCost; ?></strong>
                </div>
            </div>

            <!-- Commercial Tariff & Estimate Table -->
            <div class="indian-table-wrap">
                <table class="indian-tariff-table">
                    <thead>
                        <tr>
                            <th class="center">S.N.</th>
                            <th>Description / Service Particulars</th>
                            <th class="right">Tariff Basis</th>
                            <th class="right">Distance / Qty</th>
                            <th class="right">Total Amount (<?php echo $sym; ?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="center">1.</td>
                            <td>
                                <strong>Vehicle Outstation Running Tariff</strong>
                                <span class="tariff-desc-sub">Route distance tariff estimated based on itinerary (<?php echo $pickup; ?> to <?php echo $dest; ?>)</span>
                            </td>
                            <td class="right"><?php echo $sym; ?> <?php echo number_format($extraKm, 2); ?> / KM</td>
                            <td class="right"><?php echo $distanceQtyStr; ?></td>
                            <td class="right"><strong><?php echo $sym; ?> <?php echo $runningAmountStr; ?></strong></td>
                        </tr>
                        <tr>
                            <td class="center">2.</td>
                            <td>
                                <strong>Daily Minimum Running Allowance (Threshold)</strong>
                                <span class="tariff-desc-sub">Standard operational limit per calendar day</span>
                            </td>
                            <td class="right"><?php echo number_format($kmRunning, 0); ?> KM / Day</td>
                            <td class="right"><?php echo $duration; ?> Day(s)</td>
                            <td class="right"><span class="term-badge-included">Included</span></td>
                        </tr>
                        <tr>
                            <td class="center">3.</td>
                            <td>
                                <strong>Professional Commercial Chauffeur Duty</strong>
                                <span class="tariff-desc-sub">Chauffeur daytime driving duty and outstation allowance</span>
                            </td>
                            <td class="right">Standard</td>
                            <td class="right"><?php echo $duration; ?> Day(s)</td>
                            <td class="right"><span class="<?php echo $driverClass; ?>"><?php echo $driverText; ?></span></td>
                        </tr>
                        <tr>
                            <td class="center">4.</td>
                            <td>
                                <strong>Toll Tax, Express Highway &amp; State Border Permits</strong>
                                <span class="tariff-desc-sub">Commercial border taxes and national expressway toll charges</span>
                            </td>
                            <td class="right">Permit / Plaza</td>
                            <td class="right">As per Route</td>
                            <td class="right"><span class="<?php echo $tollClass; ?>"><?php echo $tollText; ?></span></td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="4" class="right">TOTAL ESTIMATED QUOTATION COST:</td>
                            <td class="right grand-amount"><?php echo $sym; ?> <?php echo $totalCost; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Amount Chargeable in Words -->
            <div class="indian-words-strip">
                <strong>Amount Chargeable in Words:</strong> <?php echo $amountInWords; ?>
            </div>

            <!-- Payment Schedule & Advance Breakdown -->
            <div class="indian-payment-schedule">
                <div class="payment-pill advance">
                    <div class="meta">
                        <div class="lbl">Advance Booking Deposit (<?php echo $advPercent; ?>%)</div>
                        <div class="desc">Required to confirm fleet reservation</div>
                    </div>
                    <div class="amount"><?php echo $sym; ?> <?php echo $advAmount; ?></div>
                </div>
                <div class="payment-pill">
                    <div class="meta">
                        <div class="lbl">Balance Due on Departure</div>
                        <div class="desc">Payable at start of journey / trip</div>
                    </div>
                    <div class="amount"><?php echo $sym; ?> <?php echo $balAmount; ?></div>
                </div>
            </div>

            <!-- Bank & Digital Payment Card with UPI QR -->
            <div class="indian-bank-card">
                <div class="indian-bank-header">
                    <div class="indian-bank-title">Bank &amp; Digital Payment Details (For NEFT / RTGS / IMPS / UPI)</div>
                    <?php if (!empty($upiId)): ?>
                        <span class="indian-upi-badge">UPI / QR Enabled</span>
                    <?php endif; ?>
                </div>
                <div class="indian-bank-content">
                    <div class="indian-bank-grid">
                        <?php if (!empty($bankName)): ?>
                            <div class="indian-bank-cell">
                                <span class="lbl">Bank Name:</span>
                                <strong><?php echo $bankName; ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($bankAcName)): ?>
                            <div class="indian-bank-cell">
                                <span class="lbl">Beneficiary:</span>
                                <strong><?php echo $bankAcName; ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($bankAcNo)): ?>
                            <div class="indian-bank-cell">
                                <span class="lbl">Account No:</span>
                                <strong class="val-mono"><?php echo $bankAcNo; ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($bankIfsc)): ?>
                            <div class="indian-bank-cell">
                                <span class="lbl">IFSC Code:</span>
                                <strong class="val-mono"><?php echo $bankIfsc; ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($bankBranch)): ?>
                            <div class="indian-bank-cell span-2">
                                <span class="lbl">Branch:</span>
                                <span><?php echo $bankBranch; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($upiId)): ?>
                            <div class="indian-bank-cell span-2">
                                <span class="lbl">UPI ID / VPA:</span>
                                <strong class="val-mono val-upi"><?php echo $upiId; ?></strong> (GooglePay, PhonePe, Paytm, BHIM)
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($upiQrUrl)): ?>
                        <div class="indian-qr-col">
                            <img src="<?php echo htmlspecialchars($upiQrUrl); ?>" alt="Scan &amp; Pay via UPI" class="indian-qr-img">
                            <span class="indian-qr-lbl">Scan to Pay Advance</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Commercial Policy & Terms & Conditions -->
            <div class="indian-terms-card">
                <div class="indian-terms-header">
                    <span>Terms &amp; Conditions</span>
                    <span>Standard Outstation Commercial Policy</span>
                </div>
                <ul class="indian-terms-list">
                    <li class="indian-term-item"><strong>Garage to Garage Calculation:</strong> Total running distance (kilometres) and duty timings are calculated starting and closing from our garage base (<?php echo $garageText; ?>).</li>
                    <li class="indian-term-item"><strong>Toll Plaza &amp; Inter-State Taxes:</strong> National highway toll plaza taxes, commercial border entry taxes, and airport/station parking fees are <strong><?php echo $tollText; ?> / <?php echo $stateTaxText; ?> / <?php echo $parkingText; ?></strong>.</li>
                    <li class="indian-term-item"><strong>Chauffeur Duty &amp; Allowance:</strong> Commercial driver day duty and night halt allowances are <strong><?php echo $driverText; ?></strong>.</li>
                    <li class="indian-term-item"><strong>Mountain / Uphill Incline AC Policy:</strong> As per transport safety regulations, the vehicle air conditioner (AC) will remain switched off on steep uphill mountain road sections to ensure engine performance and passenger safety.</li>
                    <li class="indian-term-item"><strong>Night Driving Regulations:</strong> Night driving between 11:00 PM and 06:00 AM requires prior operational confirmation and attracts additional night allowance.</li>
                    <li class="indian-term-item"><strong>Extra Running Tariff:</strong> Any additional running beyond the estimated distance will be charged at <strong><?php echo $sym; ?> <?php echo number_format($extraKm, 2); ?> per KM</strong>.</li>
                    <li class="indian-term-item"><strong>Mechanical Breakdown Backup:</strong> In the rare event of an unavoidable mechanical breakdown, an equivalent replacement vehicle will be arranged promptly to ensure seamless travel.</li>
                </ul>

                <?php if (!empty($customRules)): ?>
                    <div class="indian-custom-rules-wrap">
                        <div class="indian-sub-heading">Special Operating Conditions:</div>
                        <ul class="indian-terms-list">
                            <?php foreach ($customRules as $cr): ?>
                                <?php if (!empty($cr['enabled']) && !empty($cr['title'])): ?>
                                    <li class="indian-term-item">
                                        <strong><?php echo htmlspecialchars($cr['title']); ?></strong>
                                        <?php if (!empty($cr['description'])): ?>
                                            &mdash; <span><?php echo htmlspecialchars($cr['description']); ?></span>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Dual Signatures Block (Customer & Fleet Operations) -->
            <div class="indian-sign-grid">
                <div class="indian-sign-box">
                    <div class="sign-placeholder-line"></div>
                    <div class="sign-title">Customer Acceptance &amp; Signature</div>
                    <div class="sign-subtitle">I accept the quoted pricing, route terms, and commercial policies.</div>
                </div>
                <div class="indian-sign-box right">
                    <div class="sign-placeholder-line"></div>
                    <div class="sign-title">For <?php echo $compName; ?></div>
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
    </section>

    <!-- Page Break Print Running Footer -->
    <div class="print-page-footer">
        Powered by <a href="https://aleya.in" target="_blank" rel="noopener" class="credit-link">Aleya.in</a> &bull; Fleet &amp; Travel Management Platform
    </div>
</body>
</html>
