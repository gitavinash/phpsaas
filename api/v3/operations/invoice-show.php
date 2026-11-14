<?php

/**
 * Public & Client-Facing Proforma / Tax Invoice View & Print Template
 * Path: /api/v3/operations/invoice-show.php
 */

header("Content-Type: text/html; charset=utf-8");

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
$siteDomain = SITE_URL;
$siteName = defined('SITE_NAME') ? SITE_NAME : 'Aleya Holidays';

$rawId = isset($_GET['id']) ? trim($_GET['id']) : '';
if (empty($rawId)) {
    echo '<h1>Invalid Invoice Link</h1><p>Invoice reference identifier is missing.</p>';
    exit();
}

$decoded = base64_decode($rawId, true);
$leadId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (is_numeric($rawId) ? (int)$rawId : 0);

if ($leadId <= 0) {
    echo '<h1>Invoice Not Found</h1><p>The requested invoice could not be located.</p>';
    exit();
}

$siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
$lRes = mysqli_query($connection, "SELECT * FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadId}{$siteClause} LIMIT 1");
if (!$lRes || mysqli_num_rows($lRes) === 0) {
    echo '<h1>Invoice Not Found</h1><p>This invoice does not exist or has been removed.</p>';
    exit();
}
$lead = mysqli_fetch_assoc($lRes);

// Customer details
$fName = trim($lead['enquiry_first_name'] ?? '');
$lName = trim($lead['enquiry_last_name'] ?? '');
$guestName = trim("{$fName} {$lName}") ?: 'Valued Client';
$guestMobile = trim($lead['enquiry_mobile'] ?? '');
$guestEmail  = trim($lead['enquiry_email'] ?? '');
$destination = !empty($lead['enquiry_subject']) ? $lead['enquiry_subject'] : 'Custom Tour Package';

// Financials
$packageCost = (float)($lead['enquiry_expected_value'] ?? 0);
$qSql = "SELECT quotation_price FROM {$tblPrefix}quotation WHERE quotation_ref_id = {$leadId} ORDER BY quotation_id DESC LIMIT 1";
if ($qRes = mysqli_query($connection, $qSql)) {
    if ($qRow = mysqli_fetch_assoc($qRes)) {
        if ((float)$qRow['quotation_price'] > 0) {
            $packageCost = (float)$qRow['quotation_price'];
        }
    }
}

// Verified payments
$totalPaid = 0.0;
$paySql = "SELECT enquiry_notes_meta_value FROM {$tblPrefix}enquiry_notes WHERE enquiry_notes_ref_id = {$leadId} AND enquiry_notes_meta_type = 'payment_record'";
if ($pRes = mysqli_query($connection, $paySql)) {
    while ($pRow = mysqli_fetch_assoc($pRes)) {
        $pData = json_decode($pRow['enquiry_notes_meta_value'], true);
        if (is_array($pData) && (!isset($pData['status']) || in_array($pData['status'], ['verified', 'approved']))) {
            $totalPaid += (float)($pData['amount'] ?? 0);
        }
    }
}

$subTotal = $packageCost > 0 ? round($packageCost / 1.05, 2) : 0;
$gstAmount = round($packageCost - $subTotal, 2);
$cgst = round($gstAmount / 2, 2);
$sgst = round($gstAmount / 2, 2);
$balanceDue = max(0, $packageCost - $totalPaid);

$isTaxInvoice = in_array((int)$lead['enquiry_status'], [1, 12, 14, 15]);
$docTitle = $isTaxInvoice ? 'Tax Invoice' : 'Proforma Invoice';
$invPrefix = $isTaxInvoice ? 'INV-' : 'PI-';
$invoiceNumber = $invPrefix . date('Y') . '-' . str_pad($leadId, 5, '0', STR_PAD_LEFT);
$invoiceDate = date('d M Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $docTitle; ?> - <?php echo htmlspecialchars($guestName); ?> (<?php echo $invoiceNumber; ?>)</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <style>
        :root {
            --brand-primary: #0f172a;
            --brand-accent: #2563eb;
            --emerald: #059669;
            --rose: #e11d48;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-600: #475569;
            --slate-800: #1e293b;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            color: var(--slate-800);
            line-height: 1.5;
            font-size: 13px;
            padding: 24px 16px 60px;
        }

        .invoice-wrapper {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid var(--slate-200);
            overflow: hidden;
            padding: 40px;
        }

        .invoice-actions-bar {
            max-width: 820px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-inv-action {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid var(--slate-200);
            background: #ffffff;
            color: var(--slate-800);
            transition: all 0.15s ease;
        }

        .btn-inv-action:hover {
            background: var(--slate-100);
        }

        .btn-inv-action.primary {
            background: var(--brand-accent);
            color: #ffffff;
            border-color: var(--brand-accent);
        }

        .btn-inv-action.whatsapp {
            background: #25d366;
            color: #ffffff;
            border-color: #25d366;
        }

        .inv-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 2px solid var(--slate-100);
            padding-bottom: 24px;
            margin-bottom: 24px;
        }

        .inv-logo-box h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--brand-primary);
            letter-spacing: -0.5px;
        }

        .inv-logo-box p {
            font-size: 11.5px;
            color: var(--slate-600);
            margin-top: 2px;
        }

        .inv-meta-box {
            text-align: right;
        }

        .inv-doc-badge {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .inv-num {
            font-size: 18px;
            font-weight: 800;
            color: var(--slate-800);
        }

        .inv-date {
            font-size: 11.5px;
            color: #64748b;
        }

        .inv-parties-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }

        .party-col h4 {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .party-col p {
            font-size: 13px;
            line-height: 1.5;
            color: var(--slate-800);
        }

        .inv-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .inv-table th {
            background: var(--slate-50);
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--slate-600);
            padding: 10px 14px;
            border-bottom: 2px solid var(--slate-200);
            border-top: 1px solid var(--slate-200);
            text-align: left;
        }

        .inv-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--slate-100);
            vertical-align: top;
        }

        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }

        .inv-calc-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 24px;
            margin-top: 16px;
        }

        .inv-bank-card {
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 8px;
            padding: 14px 18px;
            font-size: 12px;
        }

        .inv-bank-card h5 {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--slate-800);
        }

        .inv-totals-card {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .totals-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
        }

        .totals-row.grand {
            border-top: 2px solid var(--slate-200);
            padding-top: 8px;
            font-size: 15px;
            font-weight: 800;
        }

        .totals-row.paid {
            color: var(--emerald);
            font-weight: 700;
        }

        .totals-row.balance {
            border-top: 1px solid var(--slate-200);
            padding-top: 8px;
            font-size: 16px;
            font-weight: 800;
            color: var(--rose);
        }

        .inv-terms {
            margin-top: 32px;
            padding-top: 18px;
            border-top: 1px solid var(--slate-200);
            font-size: 11.5px;
            color: #64748b;
            line-height: 1.5;
        }

        @media print {
            body { background: #ffffff; padding: 0; }
            .invoice-actions-bar { display: none !important; }
            .invoice-wrapper { box-shadow: none; border: none; padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

    <div class="invoice-actions-bar">
        <div>
            <a href="/dashboard/operations/" class="btn-inv-action">
                <i class="fi fi-rr-arrow-left"></i> Return to Operations
            </a>
        </div>
        <div style="display:flex; gap:10px;">
            <?php
            $waInvMsg = "Dear " . $guestName . ", please find your official " . $docTitle . " (" . $invoiceNumber . ") from " . $siteName . ":\n" . $siteDomain . "/view-invoice/" . base64_encode((string)$leadId) . "/\n\nBalance Due: ₹ " . number_format($balanceDue, 2);
            $cleanPhone = preg_replace('/[^0-9]/', '', $guestMobile);
            $targetWa = (strlen($cleanPhone) === 10) ? '91' . $cleanPhone : $cleanPhone;
            $waInvUrl = !empty($targetWa) ? "https://wa.me/{$targetWa}?text=" . rawurlencode($waInvMsg) : '#';
            ?>
            <?php if ($waInvUrl !== '#') { ?>
            <a href="<?php echo $waInvUrl; ?>" target="_blank" class="btn-inv-action whatsapp">
                <i class="fi fi-brands-whatsapp"></i> Share on WhatsApp
            </a>
            <?php } ?>
            <button type="button" class="btn-inv-action primary" onclick="window.print()">
                <i class="fi fi-rr-print"></i> Print / Save PDF
            </button>
        </div>
    </div>

    <div class="invoice-wrapper">
        <div class="inv-header">
            <div class="inv-logo-box">
                <h1><?php echo htmlspecialchars($siteName); ?></h1>
                <p>Licensed Inbound &amp; Domestic Tour Operator</p>
                <p>GSTIN: <strong>01AABCT1234F1Z8</strong> &bull; PAN: AABCT1234F</p>
            </div>
            <div class="inv-meta-box">
                <span class="inv-doc-badge"><?php echo $docTitle; ?></span>
                <div class="inv-num"><?php echo $invoiceNumber; ?></div>
                <div class="inv-date">Date: <?php echo $invoiceDate; ?></div>
            </div>
        </div>

        <div class="inv-parties-grid">
            <div class="party-col">
                <h4>Billed To (Client / Traveler):</h4>
                <p>
                    <strong><?php echo htmlspecialchars($guestName); ?></strong><br>
                    Phone: <?php echo htmlspecialchars($guestMobile ?: 'N/A'); ?><br>
                    Email: <?php echo htmlspecialchars($guestEmail ?: 'N/A'); ?><br>
                    Tour Ref: <?php echo htmlspecialchars($destination); ?>
                </p>
            </div>
            <div class="party-col text-right">
                <h4>Issued By:</h4>
                <p>
                    <strong><?php echo htmlspecialchars($siteName); ?> Private Limited</strong><br>
                    Boulevard Complex, Dal Lake View<br>
                    Helpline: +91 98765 43210<br>
                    Email: accounts@aleyaholidays.com
                </p>
            </div>
        </div>

        <table class="inv-table">
            <thead>
                <tr>
                    <th width="40">#</th>
                    <th>Service &amp; Package Description</th>
                    <th class="text-center" width="80">SAC / HSN</th>
                    <th class="text-right" width="130">Rate / Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <strong>Tour Package Itinerary Fulfillment</strong><br>
                        <span class="text-muted"><?php echo htmlspecialchars($destination); ?> (Accommodation, Transfers &amp; Sightseeing Services)</span>
                    </td>
                    <td class="text-center">998555</td>
                    <td class="text-right"><?php echo number_format($subTotal, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="inv-calc-grid">
            <div class="inv-bank-card">
                <h5><i class="fi fi-rr-bank"></i> Remittance &amp; Bank Details</h5>
                <p>Bank: <strong>HDFC Bank</strong></p>
                <p>Account Name: <strong><?php echo htmlspecialchars($siteName); ?> Pvt Ltd</strong></p>
                <p>Account Number: <strong>50200088991122</strong></p>
                <p>IFSC Code: <strong>HDFC0001234</strong> &bull; Branch: Srinagar</p>
                <p>UPI ID: <strong>aleya@hdfcbank</strong></p>
            </div>

            <div class="inv-totals-card">
                <div class="totals-row">
                    <span>Taxable Subtotal:</span>
                    <span>₹ <?php echo number_format($subTotal, 2); ?></span>
                </div>
                <div class="totals-row">
                    <span>CGST (2.5%):</span>
                    <span>₹ <?php echo number_format($cgst, 2); ?></span>
                </div>
                <div class="totals-row">
                    <span>SGST (2.5%):</span>
                    <span>₹ <?php echo number_format($sgst, 2); ?></span>
                </div>
                <div class="totals-row grand">
                    <span>Total Package Value:</span>
                    <span>₹ <?php echo number_format($packageCost, 2); ?></span>
                </div>
                <div class="totals-row paid">
                    <span>Verified Advance Paid:</span>
                    <span>- ₹ <?php echo number_format($totalPaid, 2); ?></span>
                </div>
                <div class="totals-row balance">
                    <span>Balance Amount Due:</span>
                    <span>₹ <?php echo number_format($balanceDue, 2); ?></span>
                </div>
            </div>
        </div>

        <div class="inv-terms">
            <strong>Terms &amp; Conditions:</strong><br>
            1. All payment remittances must cite the invoice number <strong><?php echo $invoiceNumber; ?></strong>.<br>
            2. Any outstanding balance must be settled prior to check-in or commencement of ground transportation.<br>
            3. Computer-generated electronic invoice, valid without physical endorsement.
        </div>
    </div>

</body>
</html>
