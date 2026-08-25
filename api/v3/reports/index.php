<?php
// Route API requests for /api/v3/reports/
$reqUri = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($reqUri, PHP_URL_PATH);
$pathParts = array_values(array_filter(explode('/', trim($path, '/'))));

// If accessing /api/v3/reports or /api/v3/reports/employees
$subReport = $pathParts[3] ?? 'employees';

if ($subReport === 'employees' || empty($subReport)) {
    require_once __DIR__ . '/employees.php';
} else {
    header("Content-type: application/json; charset=utf-8");
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Unknown report type requested.'
    ]);
    exit();
}
