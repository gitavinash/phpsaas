<?php
/**
 * V3 Countries List API
 * Endpoint: /api/v3/countries/ or /api/v3/countries.php
 * Method: GET
 */

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 2);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$search = trim($_GET['search'] ?? ($_GET['q'] ?? ''));
$escapedSearch = mysqli_real_escape_string($connection, $search);

$whereClause = "1=1";
if (!empty($escapedSearch)) {
    $whereClause = "(country_name LIKE '%{$escapedSearch}%' OR country_code LIKE '%{$escapedSearch}%')";
}

$query = "SELECT country_id, country_name, country_code, country_phonecode FROM {$tblPrefix}country WHERE {$whereClause} ORDER BY country_name ASC";
$result = mysqli_query($connection, $query);

$countries = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $countries[] = [
            'id'        => (int)$row['country_id'],
            'name'      => stripslashes($row['country_name']),
            'code'      => $row['country_code'] ?? '',
            'phonecode' => $row['country_phonecode'] ?? ''
        ];
    }
}

echo json_encode([
    'status'    => 'success',
    'total'     => count($countries),
    'countries' => $countries,
    'results'   => array_map(function($c) {
        return [
            'id'   => $c['name'],
            'text' => $c['name']
        ];
    }, $countries)
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
