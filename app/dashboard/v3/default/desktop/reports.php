<?php
/**
 * Reports Route Compatibility Handler
 * File: app/dashboard/v3/default/desktop/reports.php
 * Redirects legacy / plural /dashboard/reports/ to V3 Employee Performance & Conversion Report
 */

$queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: /dashboard/report/employees/" . $queryString, true, 301);
exit();
