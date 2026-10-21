<?php

/**
 * V3 Master Data Index Router
 * Path: /api/v3/master/index.php
 */

$action = strtolower(trim($_GET['action'] ?? ($_POST['action'] ?? '')));

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $action === 'copy') {
    require_once __DIR__ . '/copy.php';
} else {
    require_once __DIR__ . '/catalog.php';
}
