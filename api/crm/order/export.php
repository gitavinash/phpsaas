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
} else {
    $filterSummaryParts[] = 'Dates: All Dates';
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

$selectedCols = crmOrderResolveExportColumns($reqData);
$spreadsheetHeaders = [];
foreach ($selectedCols as $colDef) {
    $spreadsheetHeaders[] = $colDef['label'];
}

$buildSpreadsheetRow = static function (array $row) use ($selectedCols): array {
    $outRow = [];
    foreach ($selectedCols as $colDef) {
        $outRow[] = crmOrderExtractColumnSpreadsheetValue($row, (string)$colDef['key']);
    }
    return $outRow;
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

    $boldAccentCols  = [];
    $linkCols        = [];
    $integerCols     = [];
    $numericCols     = [];
    $boldNumericCols = [];
    $statusCol       = null;
    $colWidths       = [];

    foreach ($selectedCols as $colIdx => $colDef) {
        $colWidths[$colIdx] = (int)($colDef['xlsx_width'] ?? 16);
        $xType = (string)($colDef['xlsx_type'] ?? 'text');
        if ($xType === 'bold_accent') {
            $boldAccentCols[] = $colIdx;
        } elseif ($xType === 'status' && $statusCol === null) {
            $statusCol = $colIdx;
        } elseif ($xType === 'link') {
            $linkCols[] = $colIdx;
        } elseif ($xType === 'int') {
            $integerCols[] = $colIdx;
        } elseif ($xType === 'num') {
            $numericCols[] = $colIdx;
        } elseif ($xType === 'bold_num') {
            $boldNumericCols[] = $colIdx;
        }
    }

    $xlsxOptions = [
        'boldAccentCols'  => $boldAccentCols,
        'statusCol'       => $statusCol,
        'linkCols'        => $linkCols,
        'integerCols'     => $integerCols,
        'numericCols'     => $numericCols,
        'boldNumericCols' => $boldNumericCols,
        'colWidths'       => $colWidths,
    ];
    crmOrderStreamXlsx($filename, $spreadsheetHeaders, $sheetRows, 'CRM Orders', $xlsxOptions);
}

$summaryData = $dataset['summary'] ?? [
    'total_orders'     => count($rows),
    'filtered_revenue' => 0,
    'currency_symbol'  => '₹',
    'status_counts'    => [],
];

$selectedColKeys = array_map(static function (array $c): string {
    return (string)$c['key'];
}, $selectedCols);

if ($exportFormat === 'json') {
    header('Content-Type: application/json; charset=utf-8', true);
    echo json_encode([
        'status'  => 'success',
        'message' => 'Export dataset prepared.',
        'meta'    => [
            'site_name'        => $siteName,
            'exported_at'      => date('d/m/Y h:i A'),
            'date_slug'        => $dateSlug,
            'total'            => count($rows),
            'filter_summary'   => $filterSummaryText,
            'redact_pii'       => $ctx['redactPii'],
            'summary'          => $summaryData,
            'selected_columns' => $selectedColKeys,
            'export_columns'   => crmOrderExportColumnCatalog(),
        ],
        'rows' => $rows,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// 'print' or direct-navigation 'pdf' fallback -> printable HTML report honoring selected columns
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

// Build contiguous header groups for the currently selected columns
$headerGroups = [];
foreach ($selectedCols as $colDef) {
    $gKey   = (string)$colDef['group'];
    $gLabel = (string)$colDef['group_label'];
    $lastIdx = count($headerGroups) - 1;
    if ($lastIdx >= 0 && $headerGroups[$lastIdx]['group'] === $gKey) {
        $headerGroups[$lastIdx]['span']++;
    } else {
        $headerGroups[] = [
            'group' => $gKey,
            'label' => $gLabel,
            'span'  => 1,
        ];
    }
}

$colCount = count($selectedCols);
$tableDensityClass = $colCount <= 10 ? 'crm-cols-compact' : ($colCount <= 22 ? 'crm-cols-medium' : 'crm-cols-wide');
$firstOrderKey = $selectedCols[0]['key'] ?? '';

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
            <span><?php echo count($rows); ?> order(s) · <?php echo $colCount; ?> column(s) | <?php echo $esc($filterSummaryText); ?></span>
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
                <p class="crm-print-subtitle"><?php echo $esc($filterSummaryText); ?> · <?php echo $colCount; ?> selected column(s)</p>
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
            <table class="crm-print-table <?php echo $esc($tableDensityClass); ?>">
                <caption id="crmPrintTableCaption" class="crm-print-caption">
                    Detailed Orders Export Report — <?php echo count($rows); ?> order(s), <?php echo $colCount; ?> column(s)
                </caption>
                <thead>
                    <tr class="crm-th-groups">
                        <?php foreach ($headerGroups as $grp) { ?>
                            <th scope="colgroup" colspan="<?php echo (int)$grp['span']; ?>" class="grp-<?php echo $esc($grp['group']); ?>"><?php echo $esc($grp['label']); ?></th>
                        <?php } ?>
                    </tr>
                    <tr class="crm-th-cols">
                        <?php foreach ($selectedCols as $colDef) {
                            $thCls = 'col-k-' . $colDef['key'];
                            if ($colDef['align'] === 'right') {
                                $thCls .= ' th-right';
                            } elseif ($colDef['align'] === 'center') {
                                $thCls .= ' th-center';
                            }
                            ?>
                            <th scope="col" class="<?php echo $esc($thCls); ?>"><?php echo $esc($colDef['label']); ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)) { ?>
                        <tr>
                            <td colspan="<?php echo $colCount; ?>" class="crm-print-empty">No orders matched the active filter criteria.</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($rows as $row) {
                            $items   = $row['product']['items'] ?? [];
                            $isMulti = count($items) > 1;
                            $currSym = $row['product']['currency_symbol'] ?? '₹';
                            ?>
                            <tr>
                                <?php foreach ($selectedCols as $colIdx => $colDef) {
                                    $cKey   = (string)$colDef['key'];
                                    $isHead = ($colIdx === 0);
                                    $tdCls  = 'col-k-' . $cKey;
                                    if ($colDef['align'] === 'right') {
                                        $tdCls .= ' cell-num';
                                    } elseif ($colDef['align'] === 'center') {
                                        $tdCls .= ' cell-center';
                                    }
                                    if ($cKey === 'order_code') {
                                        $tdCls .= ' cell-strong cell-nowrap';
                                    } elseif ($cKey === 'order_price') {
                                        $tdCls .= ' cell-order-price cell-strong';
                                    } elseif (in_array($cKey, ['order_date', 'order_time', 'sender_mobile', 'sender_alt_mobile', 'receiver_mobile', 'receiver_alt_mobile', 'delivery_date'], true)) {
                                        $tdCls .= ' cell-nowrap';
                                    }
                                    $tag = $isHead ? 'th' : 'td';
                                    $scopeAttr = $isHead ? ' scope="row"' : '';
                                    ?>
                                    <<?php echo $tag . $scopeAttr; ?> class="<?php echo $esc($tdCls); ?>">
                                        <?php
                                        switch ($cKey) {
                                            case 'order_code':
                                                echo $esc($row['order_code']);
                                                break;
                                            case 'order_date':
                                                echo $esc($row['created_date_display'] ?: 'N/A');
                                                break;
                                            case 'order_time':
                                                echo $esc($row['created_time_display'] ?: '—');
                                                break;
                                            case 'order_status':
                                                echo '<span class="cell-badge ' . $esc($row['status']['class']) . '">' . $esc($row['status']['label']) . '</span>';
                                                break;
                                            case 'product_name':
                                                if (empty($items)) {
                                                    echo '<span class="cell-muted-inline">N/A</span>';
                                                } else {
                                                    foreach ($items as $idx => $item) {
                                                        echo '<div class="cell-line' . ($idx > 0 ? ' cell-item-sep' : '') . '"><span class="cell-strong">' . $esc($item['title']) . '</span></div>';
                                                    }
                                                }
                                                break;
                                            case 'product_link':
                                                $hasAnyLink = false;
                                                foreach ($items as $idx => $item) {
                                                    $wUrl = trim((string)($item['website_url'] ?? ''));
                                                    if ($wUrl !== '') {
                                                        $hasAnyLink = true;
                                                        echo '<div class="cell-line' . ($idx > 0 ? ' cell-item-sep' : '') . '"><a class="crm-print-link" href="' . $esc($wUrl) . '" target="_blank" rel="noopener noreferrer">' . $renderWrappableUrl($wUrl) . '</a></div>';
                                                    } elseif ($isMulti) {
                                                        echo '<div class="cell-line cell-muted-inline' . ($idx > 0 ? ' cell-item-sep' : '') . '">—</div>';
                                                    }
                                                }
                                                if (!$hasAnyLink) {
                                                    echo '<span class="cell-muted-inline">—</span>';
                                                }
                                                break;
                                            case 'product_sku':
                                                $skus = [];
                                                foreach ($items as $item) {
                                                    $sVal = trim((string)($item['sku'] ?? ''));
                                                    $skus[] = $sVal !== '' ? $esc($sVal) : '<span class="cell-muted-inline">—</span>';
                                                }
                                                echo !empty($skus) ? implode('<br>', $skus) : '<span class="cell-muted-inline">—</span>';
                                                break;
                                            case 'item_qty':
                                                if (empty($items)) {
                                                    echo '1';
                                                } else {
                                                    foreach ($items as $item) {
                                                        echo '<div class="cell-line">' . (int)($item['qty'] ?? 1) . '</div>';
                                                    }
                                                }
                                                break;
                                            case 'total_qty':
                                                echo (int)($row['product']['total_qty'] ?? 1);
                                                break;
                                            case 'unit_price':
                                                if (empty($items)) {
                                                    echo '<span class="cell-muted-inline">—</span>';
                                                } else {
                                                    foreach ($items as $item) {
                                                        $uNum = $item['unit_price_num'] ?? null;
                                                        $uStr = $uNum !== null ? ($currSym . ' ' . crmOrderFormatMoney((float)$uNum)) : ($item['unit_price'] ?: '—');
                                                        echo '<div class="cell-line">' . $esc($uStr) . '</div>';
                                                    }
                                                }
                                                break;
                                            case 'product_subtotal':
                                                $subNum = (float)($row['product']['subtotal_num'] ?? 0);
                                                echo $esc($currSym . ' ' . crmOrderFormatMoney($subNum));
                                                break;
                                            case 'delivery_charges':
                                                $delNum = (float)($row['product']['delivery_charges_num'] ?? 0);
                                                echo $esc($currSym . ' ' . crmOrderFormatMoney($delNum));
                                                break;
                                            case 'order_price':
                                                echo $esc($row['product']['amount_display'] ?: ($currSym . ' 0'));
                                                break;
                                            case 'sender_email':
                                                $em = trim((string)($row['sender']['email'] ?? ''));
                                                echo $em !== '' ? $renderWrappableEmail($em) : '<span class="cell-muted-inline">—</span>';
                                                break;
                                            case 'transaction_ref':
                                                $txRefVal = trim((string)($row['payment']['transaction_ref'] ?? ''));
                                                echo $txRefVal !== '' ? '<code class="cell-code">' . $esc($txRefVal) . '</code>' : '<span class="cell-muted-inline">—</span>';
                                                break;
                                            case 'delivery_photo_link':
                                                $pUrl = trim((string)($row['delivery']['photo_url'] ?? ''));
                                                if ($pUrl !== '') {
                                                    echo '<a class="crm-print-link" href="' . $esc($pUrl) . '" target="_blank" rel="noopener noreferrer">' . $renderWrappableUrl($pUrl) . '</a>';
                                                } else {
                                                    echo '<span class="cell-muted-inline">—</span>';
                                                }
                                                break;
                                            default:
                                                $valStr = trim(crmOrderExtractColumnSpreadsheetValue($row, $cKey));
                                                echo $valStr !== '' ? $esc($valStr) : '<span class="cell-muted-inline">—</span>';
                                                break;
                                        }
                                        ?>
                                    </<?php echo $tag; ?>>
                                <?php } ?>
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
