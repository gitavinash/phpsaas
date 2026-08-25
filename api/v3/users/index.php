<?php

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

switch ($method) {
    case 'POST':
        require_once __DIR__ . '/create.php';
        break;
    case 'PUT':
    case 'PATCH':
        require_once __DIR__ . '/update.php';
        break;
    case 'DELETE':
        require_once __DIR__ . '/delete.php';
        break;
    case 'GET':
    default:
        require_once __DIR__ . '/list.php';
        break;
}
