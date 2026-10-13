<?php
/**
 * V3 Commercial Quotation Template Dispatcher
 * Path: app/dashboard/v3/default/desktop/templates/quote/index.php
 * Description: Dispatches quote rendering to the requested template (default: 'mit').
 */

$templateName = isset($templateToUse) ? strtolower(trim((string)$templateToUse)) : (isset($quoteTemplate) ? strtolower(trim((string)$quoteTemplate)) : 'mit');
$templateDir = __DIR__ . '/' . $templateName;

if (!is_dir($templateDir) || !file_exists($templateDir . '/index.php')) {
    $templateDir = __DIR__ . '/mit';
}

require $templateDir . '/index.php';
