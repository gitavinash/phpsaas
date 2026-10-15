<?php
/**
 * Aleya SaaS — Master Industry Router
 * Serves /industries/travel-agencies/, /industries/travel-agents/, /industries/transporters/, /industries/dms/, /industries/spa-management/
 */

require_once __DIR__ . '/includes/industries-data.php';
require_once __DIR__ . '/includes/industry-template.php';

$industries = get_all_industries();

$requestPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
$parts = explode('/', $requestPath);

$slug = '';
if (isset($parts[1]) && $parts[0] === 'industries') {
    $slug = strtolower($parts[1]);
} elseif (isset($urlStructure[1]) && $urlStructure[0] === 'industries') {
    $slug = strtolower($urlStructure[1]);
} elseif (isset($_GET['industry'])) {
    $slug = strtolower($_GET['industry']);
}

// Normalize common aliases
if ($slug === 'spa' || $slug === 'spa-wellness') {
    $slug = 'spa-management';
} elseif ($slug === 'travel-agency' || $slug === 'travel') {
    $slug = 'travel-agencies';
} elseif ($slug === 'travel-agent') {
    $slug = 'travel-agents';
} elseif ($slug === 'transport' || $slug === 'fleet') {
    $slug = 'transporters';
}

if (!isset($industries[$slug])) {
    // If accessing /industries/ with no slug, redirect to homepage industries section
    if ($slug === '') {
        header('Location: /#industries', true, 301);
        exit();
    }
    // Default fallback
    $slug = 'travel-agencies';
}

render_industry_landing_page($industries[$slug]);
