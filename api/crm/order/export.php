<?php

define('CRM_ORDER_API_ENTRYPOINT', true);
require_once __DIR__ . '/_repository.php';

$reqData      = crmOrderReadRequestData();
$exportFormat = strtolower(trim((string)($reqData['export_format'] ?? ($reqData['format'] ?? 'csv'))));
if (!in_array($exportFormat, ['csv', 'xlsx', 'json', 'print', 'pdf'], true)) {
    $exportFormat = 'csv';
}

$errorFormat = ($exportFormat === 'print' || $exportFormat === 'pdf') ? 'html' : 'json';
$ctx         = crmOrderApiBootstrap($errorFormat);
$filters     = crmOrderParseFilters($reqData);

$dataset = crmOrderFetchDataset(
    $ctx['connection'],
    $ctx['tblPrefix'],
    $ctx['siteId'],
    $filters,
    $ctx['redactPii'],
    true,
    false
);

$rows     = $dataset['rows'];
$siteName = defined('SITE_NAME') && trim((string)SITE_NAME) !== '' ? trim((string)SITE_NAME) : 'CRM Orders';
$dateSlug = date('Y-m-d');

// Human-readable active filters summary
$filterSummaryParts = [];
if ($filters['status'] !== 'all' && !empty($filters['status_codes'])) {
    $stNames = [];
    foreach ($filters['status_codes'] as $code) {
        $stNames[] = crmOrderStatusMeta($code)['label'];
    }
    $filterSummaryParts[] = 'Status: ' . implode(', ', $stNames);
} else {
    $filterSummaryParts[] = 'Status: All';
}
if ($filters['start_date'] !== '' && $filters['end_date'] !== '') {
    $basisLabel = ($filters['date_basis'] === 'delivery') ? 'Delivery Date' : 'Created Date';
    $filterSummaryParts[] = $basisLabel . ': ' . date('d/m/Y', strtotime($filters['start_date'])) . ' - ' . date('d/m/Y', strtotime($filters['end_date']));
}
if ($filters['payment_mode'] !== 'all') {
    $filterSummaryParts[] = 'Payment: ' . crmOrderPaymentLabel($filters['payment_mode'] === 'unspecified' ? '' : $filters['payment_mode']);
}
if ($filters['city'] !== 'all') {
    $filterSummaryParts[] = 'City: ' . $filters['city'];
}
if ($filters['q'] !== '') {
    $filterSummaryParts[] = 'Search: "' . $filters['q'] . '"';
}
$filterSummaryText = implode(' | ', $filterSummaryParts);

// Spreadsheet atomic columns (CSV & XLSX): separate column for each data value type
$spreadsheetHeaders = [
    'Order ID',               // 0
    'Order Date',             // 1
    'Order Time',             // 2
    'Order Status',           // 3
    'Product Name',           // 4
    'Product Website Link',   // 5
    'Product SKU',            // 6
    'Item Qty',               // 7
    'Total Qty',              // 8
    'Product Unit Price',     // 9
    'Product Subtotal',       // 10
    'Delivery Charges',       // 11
    'Coupon Code',            // 12
    'Order Price',            // 13
    'Currency',               // 14
    'Payment Mode',           // 15
    'Payment Status',         // 16
    'Transaction Ref',        // 17
    'Sender Name',            // 18
    'Sender Mobile',          // 19
    'Sender Alt Mobile',      // 20
    'Sender Email',           // 21
    'Receiver Name',          // 22
    'Receiver Mobile',        // 23
    'Receiver Alt Mobile',    // 24
    'Receiver Street Address',// 25
    'Receiver City',          // 26
    'Receiver State',         // 27
    'Receiver Country',       // 28
    'Receiver Pin Code',      // 29
    'Address Type',           // 30
    'Delivery Date',          // 31
    'Delivery Slot',          // 32
    'Delivery Time',          // 33
    'Occasion',               // 34
    'Gift Message',           // 35
    'Delivery Note',          // 36
    'Return Reason',          // 37
    'Return Comment',         // 38
    'Delivery Photo Status',  // 39
    'Delivery Photo Link',    // 40
];

$buildSpreadsheetRow = static function (array $row): array {
    $prod    = $row['product'] ?? [];
    $payment = $row['payment'] ?? [];
    $sender  = $row['sender'] ?? [];
    $rec     = $row['receiver'] ?? [];
    $deliv   = $row['delivery'] ?? [];

    return [
        $row['order_code'] ?? ('OD' . ($row['order_id'] ?? '')),
        $row['created_date_display'] ?? '',
        $row['created_time_display'] ?? '',
        $row['status']['label'] ?? '',
        $prod['names_text'] ?? ($prod['primary_title'] ?? ''),
        $prod['links_text'] ?? ($prod['primary_url'] ?? ''),
        $prod['skus_text'] ?? '',
        $prod['qtys_text'] ?? (string)($prod['total_qty'] ?? 1),
        (string)($prod['total_qty'] ?? 1),
        $prod['unit_prices_text'] ?? '',
        $prod['subtotal'] ?? '0',
        $prod['delivery_charges'] ?? '0',
        $prod['coupon'] ?? '',
        $prod['total_amount'] ?? '0',
        $prod['currency_code'] ?? 'INR',
        $payment['mode_label'] ?? '',
        $payment['transaction_status_label'] ?? '',
        $payment['transaction_ref'] ?? '',
        $sender['name'] ?? '',
        $sender['mobile'] ?? '',
        $sender['alt_mobile'] ?? '',
        $sender['email'] ?? '',
        $rec['name'] ?? '',
        $rec['mobile'] ?? '',
        $rec['alt_mobile'] ?? '',
        $rec['street_address'] ?? '',
        $rec['city'] ?? '',
        $rec['state'] ?? '',
        $rec['country'] ?? '',
        $rec['pin_code'] ?? '',
        $rec['address_type'] ?? '',
        $deliv['date_display'] ?? '',
        $deliv['slot'] ?? '',
        $deliv['time'] ?? '',
        $deliv['occasion'] ?? '',
        $deliv['message'] ?? '',
        $deliv['comment'] ?? '',
        $deliv['return_reason'] ?? '',
        $deliv['return_comment'] ?? '',
        $deliv['photo_status'] ?? 'Not Uploaded',
        $deliv['photo_url'] ?? '',
    ];
};

if ($exportFormat === 'csv') {
    $filename = 'crm-orders-' . $dateSlug . '.csv';
    header('Content-Type: text/csv; charset=utf-8', true);
    header('Content-Disposition: attachment; filename="' . $filename . '"', true);
    header('Cache-Control: no-store, no-cache, must-revalidate', true);

    $out = fopen('php://output', 'wb');
    // UTF-8 BOM for clean Excel/Numbers rendering of currency symbols and Unicode names
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, $spreadsheetHeaders, ',', '"', '\\');

    foreach ($rows as $row) {
        $cells = $buildSpreadsheetRow($row);
        $safeCells = [];
        foreach ($cells as $cell) {
            $safeCells[] = crmOrderSafeSpreadsheetCell($cell);
        }
        fputcsv($out, $safeCells, ',', '"', '\\');
    }

    fclose($out);
    exit;
}

if ($exportFormat === 'xlsx') {
    $filename = 'crm-orders-' . $dateSlug . '.xlsx';
    $sheetRows = [];
    foreach ($rows as $row) {
        $sheetRows[] = $buildSpreadsheetRow($row);
    }
    $xlsxOptions = [
        'boldAccentCols'  => [0],
        'statusCol'       => 3,
        'linkCols'        => [5, 40],
        'integerCols'     => [7, 8],
        'numericCols'     => [9, 10, 11],
        'boldNumericCols' => [13],
        'colWidths'       => [
            0  => 12,
            1  => 13,
            2  => 11,
            3  => 13,
            4  => 30,
            5  => 44,
            6  => 16,
            7  => 11,
            8  => 11,
            9  => 16,
            10 => 16,
            11 => 15,
            12 => 14,
            13 => 16,
            14 => 10,
            15 => 15,
            16 => 14,
            17 => 24,
            18 => 22,
            19 => 15,
            20 => 16,
            21 => 28,
            22 => 22,
            23 => 15,
            24 => 16,
            25 => 34,
            26 => 16,
            27 => 16,
            28 => 14,
            29 => 14,
            30 => 13,
            31 => 14,
            32 => 15,
            33 => 18,
            34 => 15,
            35 => 34,
            36 => 26,
            37 => 20,
            38 => 24,
            39 => 16,
            40 => 40,
        ],
    ];
    crmOrderStreamXlsx($filename, $spreadsheetHeaders, $sheetRows, 'CRM Orders', $xlsxOptions);
}

$summaryData = $dataset['summary'] ?? [
    'total_orders'     => count($rows),
    'filtered_revenue' => 0,
    'currency_symbol'  => '₹',
    'status_counts'    => [],
];

if ($exportFormat === 'json') {
    header('Content-Type: application/json; charset=utf-8', true);
    echo json_encode([
        'status'  => 'success',
        'message' => 'Export dataset prepared.',
        'meta'    => [
            'site_name'      => $siteName,
            'exported_at'    => date('d/m/Y h:i A'),
            'date_slug'      => $dateSlug,
            'total'          => count($rows),
            'filter_summary' => $filterSummaryText,
            'redact_pii'     => $ctx['redactPii'],
            'summary'        => $summaryData,
        ],
        'rows' => $rows,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// 'print' or direct-navigation 'pdf' fallback -> printable A3 landscape HTML report
$autoPrintAttr = ($exportFormat === 'pdf' || !empty($reqData['auto_print'])) ? '1' : '0';
header('Content-Type: text/html; charset=utf-8', true);
header('Cache-Control: no-store, no-cache, must-revalidate', true);

$esc = static function ($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
};

$renderWrappableUrl = static function (string $url) use ($esc): string {
    // Allow clean wrapping at path slashes without altering actual href or copied text
    return str_replace('/', '/<wbr>', $esc($url));
};

$renderWrappableEmail = static function (string $email) use ($esc): string {
    $pos = strpos($email, '@');
    if ($pos !== false && $pos > 0) {
        return $esc(substr($email, 0, $pos + 1)) . '<wbr>' . $esc(substr($email, $pos + 1));
    }
    return $esc($email);
};

$statusCounts     = $summaryData['status_counts'] ?? [];
$confirmedCount   = (int)($statusCounts['1'] ?? 0);
$deliveredCount   = (int)($statusCounts['6'] ?? 0);
$pendingWaitCount = (int)($statusCounts['0'] ?? 0) + (int)($statusCounts['4'] ?? 0);
$summaryCurr      = (string)($summaryData['currency_symbol'] ?? '₹');
$summaryTotalVal  = $summaryCurr . ' ' . crmOrderFormatMoney((float)($summaryData['filtered_revenue'] ?? 0));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $esc($siteName); ?> — Orders Report (<?php echo $esc($dateSlug); ?>)</title>
    <link rel="stylesheet" type="text/css" href="/lib/resource/ui/css/app/crm/orders-print.css">
</head>
<body class="crm-print-body" data-auto-print="<?php echo $esc($autoPrintAttr); ?>">
    <header class="crm-print-toolbar no-print">
        <div class="crm-print-toolbar-info">
            <strong><?php echo $esc($siteName); ?> — Order Export Report</strong>
            <span><?php echo count($rows); ?> order(s) | <?php echo $esc($filterSummaryText); ?></span>
        </div>
        <div class="crm-print-toolbar-actions">
            <button type="button" class="crm-print-btn crm-print-btn-primary" id="crmPrintNowBtn">Print / Save as PDF</button>
            <button type="button" class="crm-print-btn" id="crmPrintCloseBtn">Close</button>
        </div>
    </header>

    <main class="crm-print-sheet">
        <section class="crm-print-header">
            <div>
                <h1><?php echo $esc($siteName); ?> — Orders Report</h1>
                <p class="crm-print-subtitle"><?php echo $esc($filterSummaryText); ?></p>
            </div>
            <div class="crm-print-meta">
                <div><strong>Generated:</strong> <?php echo $esc(date('d/m/Y h:i A')); ?></div>
                <div><strong>Exported Orders:</strong> <?php echo number_format(count($rows)); ?></div>
            </div>
        </section>

        <section class="crm-print-kpis" aria-label="Order report summary">
            <div class="crm-print-kpi">
                <span class="crm-print-kpi-label">Total Orders</span>
                <strong class="crm-print-kpi-val"><?php echo number_format(count($rows)); ?></strong>
            </div>
            <div class="crm-print-kpi is-highlight">
                <span class="crm-print-kpi-label">Total Order Value</span>
                <strong class="crm-print-kpi-val"><?php echo $esc($summaryTotalVal); ?></strong>
            </div>
            <div class="crm-print-kpi">
                <span class="crm-print-kpi-label">Delivered / Confirmed</span>
                <strong class="crm-print-kpi-val"><?php echo number_format($deliveredCount); ?> / <?php echo number_format($confirmedCount); ?></strong>
            </div>
            <div class="crm-print-kpi">
                <span class="crm-print-kpi-label">Pending / Waiting</span>
                <strong class="crm-print-kpi-val"><?php echo number_format($pendingWaitCount); ?></strong>
            </div>
        </section>

        <div tabindex="0" role="region" aria-labelledby="crmPrintTableCaption" class="crm-print-table-wrap">
            <table class="crm-print-table">
                <caption id="crmPrintTableCaption" class="crm-print-caption">
                    Detailed Orders Export Report — <?php echo count($rows); ?> order(s)
                </caption>
                <colgroup>
                    <col class="col-oid">
                    <col class="col-date">
                    <col class="col-time">
                    <col class="col-status">
                </colgroup>
                <colgroup>
                    <col class="col-pname">
                    <col class="col-plink">
                    <col class="col-qty">
                    <col class="col-uprice">
                </colgroup>
                <colgroup>
                    <col class="col-oprice">
                    <col class="col-pay">
                </colgroup>
                <colgroup>
                    <col class="col-sname">
                    <col class="col-smob">
                    <col class="col-semail">
                </colgroup>
                <colgroup>
                    <col class="col-rname">
                    <col class="col-rmob">
                    <col class="col-raddr">
                    <col class="col-rcity">
                </colgroup>
                <colgroup>
                    <col class="col-ddate">
                    <col class="col-dslot">
                    <col class="col-docc">
                    <col class="col-dmsg">
                </colgroup>
                <thead>
                    <tr class="crm-th-groups">
                        <th scope="colgroup" colspan="4" class="grp-order">Order</th>
                        <th scope="colgroup" colspan="4" class="grp-product">Product</th>
                        <th scope="colgroup" colspan="2" class="grp-price">Pricing &amp; Payment</th>
                        <th scope="colgroup" colspan="3" class="grp-sender">Sender</th>
                        <th scope="colgroup" colspan="4" class="grp-receiver">Receiver</th>
                        <th scope="colgroup" colspan="4" class="grp-delivery">Delivery</th>
                    </tr>
                    <tr class="crm-th-cols">
                        <th scope="col" class="col-oid">Order ID</th>
                        <th scope="col" class="col-date">Order Date</th>
                        <th scope="col" class="col-time">Order Time</th>
                        <th scope="col" class="col-status">Status</th>
                        <th scope="col" class="col-pname">Product Name</th>
                        <th scope="col" class="col-plink">Product Website Link</th>
                        <th scope="col" class="col-qty">Qty</th>
                        <th scope="col" class="col-uprice">Prod. Price</th>
                        <th scope="col" class="col-oprice">Order Price</th>
                        <th scope="col" class="col-pay">Payment</th>
                        <th scope="col" class="col-sname">Sender Name</th>
                        <th scope="col" class="col-smob">Sender Mobile</th>
                        <th scope="col" class="col-semail">Sender Email</th>
                        <th scope="col" class="col-rname">Receiver Name</th>
                        <th scope="col" class="col-rmob">Receiver Mobile</th>
                        <th scope="col" class="col-raddr">Street Address</th>
                        <th scope="col" class="col-rcity">City / Pin</th>
                        <th scope="col" class="col-ddate">Deliv. Date</th>
                        <th scope="col" class="col-dslot">Slot / Time</th>
                        <th scope="col" class="col-docc">Occasion</th>
                        <th scope="col" class="col-dmsg">Gift Message / Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)) { ?>
                        <tr>
                            <td colspan="21" class="crm-print-empty">No orders matched the active filter criteria.</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($rows as $row) {
                            $items    = $row['product']['items'] ?? [];
                            $isMulti  = count($items) > 1;
                            $currSym  = $row['product']['currency_symbol'] ?? '₹';
                            $delivChg = (float)($row['product']['delivery_charges_num'] ?? 0);
                            $coupon   = trim((string)($row['product']['coupon'] ?? ''));
                            $txStat   = trim((string)($row['payment']['transaction_status_label'] ?? ''));
                            $slotStr  = trim((string)($row['delivery']['slot'] ?? ''));
                            $timeStr  = trim((string)($row['delivery']['time'] ?? ''));
                            $msgStr   = trim((string)($row['delivery']['message'] ?? ''));
                            $noteStr  = trim((string)($row['delivery']['comment'] ?? ''));
                            $retStr   = trim(implode(' — ', array_filter([
                                $row['delivery']['return_reason'] ?? '',
                                $row['delivery']['return_comment'] ?? ''
                            ])));
                            $cityPin  = trim(implode(', ', array_filter([
                                $row['receiver']['city'] ?? '',
                                $row['receiver']['state'] ?? ''
                            ])));
                            $pinCode  = trim((string)($row['receiver']['pin_code'] ?? ''));
                            ?>
                            <tr>
                                <th scope="row" class="col-oid cell-strong"><?php echo $esc($row['order_code']); ?></th>
                                <td class="col-date cell-nowrap"><?php echo $esc($row['created_date_display'] ?: 'N/A'); ?></td>
                                <td class="col-time cell-nowrap cell-muted-inline"><?php echo $esc($row['created_time_display'] ?: '—'); ?></td>
                                <td class="col-status">
                                    <span class="cell-badge <?php echo $esc($row['status']['class']); ?>"><?php echo $esc($row['status']['label']); ?></span>
                                </td>
                                <td class="col-pname">
                                    <?php foreach ($items as $idx => $item) { ?>
                                        <div class="cell-line<?php echo $idx > 0 ? ' cell-item-sep' : ''; ?>">
                                            <span class="cell-strong"><?php echo $esc($item['title']); ?></span>
                                            <?php if (!empty($item['sku'])) { ?>
                                                <span class="cell-muted-inline"> [<?php echo $esc($item['sku']); ?>]</span>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </td>
                                <td class="col-plink">
                                    <?php
                                    $hasAnyLink = false;
                                    foreach ($items as $idx => $item) {
                                        $wUrl = trim((string)($item['website_url'] ?? ''));
                                        if ($wUrl !== '') {
                                            $hasAnyLink = true;
                                            ?>
                                            <div class="cell-line<?php echo $idx > 0 ? ' cell-item-sep' : ''; ?>">
                                                <a class="crm-print-link" href="<?php echo $esc($wUrl); ?>" target="_blank" rel="noopener noreferrer"><?php echo $renderWrappableUrl($wUrl); ?></a>
                                            </div>
                                            <?php
                                        } elseif ($isMulti) {
                                            ?>
                                            <div class="cell-line cell-muted-inline<?php echo $idx > 0 ? ' cell-item-sep' : ''; ?>">—</div>
                                            <?php
                                        }
                                    }
                                    if (!$hasAnyLink) {
                                        echo '<span class="cell-muted-inline">—</span>';
                                    }
                                    ?>
                                </td>
                                <td class="col-qty cell-num">
                                    <?php foreach ($items as $idx => $item) { ?>
                                        <div class="cell-line"><?php echo (int)$item['qty']; ?></div>
                                    <?php } ?>
                                    <?php if ($isMulti) { ?>
                                        <div class="cell-muted">Sum: <?php echo (int)$row['product']['total_qty']; ?></div>
                                    <?php } ?>
                                </td>
                                <td class="col-uprice cell-num">
                                    <?php foreach ($items as $item) {
                                        $uNum = $item['unit_price_num'] ?? null;
                                        $uStr = $uNum !== null ? ($currSym . ' ' . crmOrderFormatMoney((float)$uNum)) : ($item['unit_price'] ?: '—');
                                        ?>
                                        <div class="cell-line"><?php echo $esc($uStr); ?></div>
                                    <?php } ?>
                                </td>
                                <td class="col-oprice cell-num cell-order-price">
                                    <div class="cell-strong"><?php echo $esc($row['product']['amount_display']); ?></div>
                                    <?php if ($delivChg > 0) { ?>
                                        <div class="cell-muted">+ Deliv: <?php echo $esc($currSym . ' ' . crmOrderFormatMoney($delivChg)); ?></div>
                                    <?php } ?>
                                    <?php if ($coupon !== '') { ?>
                                        <div class="cell-muted">Coupon: <?php echo $esc($coupon); ?></div>
                                    <?php } ?>
                                </td>
                                <td class="col-pay">
                                    <div><?php echo $esc($row['payment']['mode_label']); ?></div>
                                    <?php if ($txStat !== '') { ?>
                                        <div class="cell-muted"><?php echo $esc($txStat); ?></div>
                                    <?php } ?>
                                </td>
                                <td class="col-sname"><?php echo $esc($row['sender']['name']); ?></td>
                                <td class="col-smob cell-nowrap">
                                    <div><?php echo $esc($row['sender']['mobile'] ?: '—'); ?></div>
                                    <?php if (!empty($row['sender']['alt_mobile'])) { ?>
                                        <div class="cell-muted">Alt: <?php echo $esc($row['sender']['alt_mobile']); ?></div>
                                    <?php } ?>
                                </td>
                                <td class="col-semail">
                                    <?php if (!empty($row['sender']['email'])) { ?>
                                        <?php echo $renderWrappableEmail($row['sender']['email']); ?>
                                    <?php } else { ?>
                                        <span class="cell-muted-inline">—</span>
                                    <?php } ?>
                                </td>
                                <td class="col-rname"><?php echo $esc($row['receiver']['name']); ?></td>
                                <td class="col-rmob cell-nowrap">
                                    <div><?php echo $esc($row['receiver']['mobile'] ?: '—'); ?></div>
                                    <?php if (!empty($row['receiver']['alt_mobile'])) { ?>
                                        <div class="cell-muted">Alt: <?php echo $esc($row['receiver']['alt_mobile']); ?></div>
                                    <?php } ?>
                                </td>
                                <td class="col-raddr"><?php echo $esc($row['receiver']['street_address'] ?: '—'); ?></td>
                                <td class="col-rcity">
                                    <div><?php echo $esc($cityPin !== '' ? $cityPin : '—'); ?></div>
                                    <?php if ($pinCode !== '') { ?>
                                        <div class="cell-muted">PIN: <?php echo $esc($pinCode); ?></div>
                                    <?php } ?>
                                </td>
                                <td class="col-ddate cell-nowrap"><?php echo $esc($row['delivery']['date_display']); ?></td>
                                <td class="col-dslot">
                                    <?php if ($slotStr !== '') { ?>
                                        <div><?php echo $esc($slotStr); ?></div>
                                    <?php } ?>
                                    <?php if ($timeStr !== '' && strcasecmp($timeStr, $slotStr) !== 0) { ?>
                                        <div class="cell-muted"><?php echo $esc($timeStr); ?></div>
                                    <?php } ?>
                                    <?php if ($slotStr === '' && $timeStr === '') { ?>
                                        <span class="cell-muted-inline">—</span>
                                    <?php } ?>
                                </td>
                                <td class="col-docc"><?php echo $esc($row['delivery']['occasion'] ?: '—'); ?></td>
                                <td class="col-dmsg">
                                    <?php if ($msgStr !== '') { ?>
                                        <div><?php echo $esc($msgStr); ?></div>
                                    <?php } ?>
                                    <?php if ($noteStr !== '') { ?>
                                        <div class="cell-muted">Note: <?php echo $esc($noteStr); ?></div>
                                    <?php } ?>
                                    <?php if ($retStr !== '') { ?>
                                        <div class="cell-muted">Return: <?php echo $esc($retStr); ?></div>
                                    <?php } ?>
                                    <?php if ($msgStr === '' && $noteStr === '' && $retStr === '') { ?>
                                        <span class="cell-muted-inline">—</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="/lib/resource/ui/js/app/crm/desktop/orders-print.js"></script>
</body>
</html>
<?php
exit;
