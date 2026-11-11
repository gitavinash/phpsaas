<?php

header('Content-Type: application/json; charset=utf-8', true);

define('CRM_ORDER_API_ENTRYPOINT', true);
require_once __DIR__ . '/_repository.php';

$ctx     = crmOrderApiBootstrap('json');
$reqData = crmOrderReadRequestData();
$filters = crmOrderParseFilters($reqData);

$includeFacets = !isset($reqData['include_facets']) || (string)$reqData['include_facets'] !== '0';

$dataset = crmOrderFetchDataset(
    $ctx['connection'],
    $ctx['tblPrefix'],
    $ctx['siteId'],
    $filters,
    $ctx['redactPii'],
    false,
    $includeFacets
);

echo json_encode([
    'status'          => 'success',
    'message'         => 'Orders loaded successfully.',
    'data'            => $dataset['rows'],
    'pagination'      => $dataset['pagination'],
    'summary'         => $dataset['summary'],
    'facets'          => $dataset['facets'],
    'applied_filters' => [
        'status'       => $filters['status'],
        'date_basis'   => $filters['date_basis'],
        'start_date'   => $filters['start_date'],
        'end_date'     => $filters['end_date'],
        'all_dates'    => $filters['all_dates'],
        'payment_mode' => $filters['payment_mode'],
        'city'         => $filters['city'],
        'q'            => $filters['q'],
        'sort_by'      => $filters['sort_by'],
    ],
    'meta' => [
        'site_id'    => $ctx['siteId'],
        'redact_pii' => $ctx['redactPii'],
        'today'      => date('Y-m-d'),
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
