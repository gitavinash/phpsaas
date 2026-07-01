<?php

/**
 * Order Import API
 * Supports two-step flow:
 * 1) action=preview -> returns CSV/Excel headers + sample rows
 * 2) action=import  -> imports using provided mapping
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file'])) {
    echo json_encode(array('success' => false, 'message' => 'No file provided'));
    exit;
}

try {
    $action = isset($_POST['action']) ? strtolower(trim($_POST['action'])) : 'import';

    $file = $_FILES['file'];
    $fileName = basename($file['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedExtensions = array('csv', 'xls', 'xlsx');
    if (!in_array($fileExtension, $allowedExtensions, true)) {
        throw new Exception('Invalid file type. Only CSV, XLS, and XLSX files are supported.');
    }

    $maxSize = 10 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds 10 MB limit.');
    }

    if ($fileExtension === 'csv') {
        $parsed = parseCSVFile($file['tmp_name']);
    } else {
        $parsed = parseExcelFile($file['tmp_name']);
    }

    if (empty($parsed['headers'])) {
        throw new Exception('No header row found in file.');
    }

    if ($action === 'preview') {
        echo json_encode(array(
            'success' => true,
            'headers' => $parsed['headers'],
            'sampleRows' => array_slice($parsed['rows'], 0, 5),
            'totalRows' => count($parsed['rows'])
        ));
        exit;
    }

    $mapping = array();
    if (!empty($_POST['mapping'])) {
        $decoded = json_decode($_POST['mapping'], true);
        if (is_array($decoded)) {
            $mapping = $decoded;
        }
    }

    $rowsToImport = applyMapping($parsed['rows'], $mapping);
    $result = importOrders($rowsToImport);

    echo json_encode(array(
        'success' => true,
        'message' => 'Import completed',
        'importedCount' => $result['imported'],
        'failedCount' => $result['failed'],
        'details' => $result['details']
    ));
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array('success' => false, 'message' => $e->getMessage()));
}

function parseCSVFile($filePath)
{
    $rows = array();
    $handle = fopen($filePath, 'r');

    if (!$handle) {
        throw new Exception('Unable to open CSV file');
    }

    $headers = null;
    while (($row = fgetcsv($handle, 0, ',')) !== false) {
        if ($headers === null) {
            $headers = sanitizeHeaders($row);
            continue;
        }

        $record = array();
        foreach ($headers as $index => $header) {
            $record[$header] = isset($row[$index]) ? trim((string)$row[$index]) : '';
        }

        if (!empty(array_filter($record, 'strlen'))) {
            $rows[] = $record;
        }
    }

    fclose($handle);

    return array(
        'headers' => $headers === null ? array() : $headers,
        'rows' => $rows
    );
}

function parseExcelFile($filePath)
{
    if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\IOFactory')) {
        throw new Exception('Excel file processing library not found. Please install PhpSpreadsheet or use CSV.');
    }

    $rows = array();

    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $headers = null;
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = array();
            foreach ($cellIterator as $cell) {
                $rowData[] = trim((string)$cell->getFormattedValue());
            }

            if ($headers === null) {
                $headers = sanitizeHeaders($rowData);
                continue;
            }

            $record = array();
            foreach ($headers as $index => $header) {
                $record[$header] = isset($rowData[$index]) ? trim((string)$rowData[$index]) : '';
            }

            if (!empty(array_filter($record, 'strlen'))) {
                $rows[] = $record;
            }
        }
    } catch (Exception $e) {
        throw new Exception('Error parsing Excel file: ' . $e->getMessage());
    }

    return array(
        'headers' => $headers === null ? array() : $headers,
        'rows' => $rows
    );
}

function sanitizeHeaders($headers)
{
    $clean = array();
    foreach ($headers as $header) {
        $name = trim((string)$header);
        if ($name === '') {
            $name = 'column_' . (count($clean) + 1);
        }

        $baseName = $name;
        $suffix = 1;
        while (in_array($name, $clean, true)) {
            $suffix++;
            $name = $baseName . '_' . $suffix;
        }

        $clean[] = $name;
    }
    return $clean;
}

function applyMapping($rows, $mapping)
{
    if (empty($mapping) || !is_array($mapping)) {
        return $rows;
    }

    $mappedRows = array();
    foreach ($rows as $row) {
        $mapped = $row;
        foreach ($mapping as $targetField => $sourceColumn) {
            $sourceColumn = trim((string)$sourceColumn);
            if ($sourceColumn === '') {
                continue;
            }
            $mapped[$targetField] = isset($row[$sourceColumn]) ? trim((string)$row[$sourceColumn]) : '';
        }
        $mappedRows[] = $mapped;
    }

    return $mappedRows;
}

function importOrders($data)
{
    $imported = 0;
    $failed = 0;
    $errors = array();

    $requiredFields = array('order_id', 'customer_name', 'customer_email', 'order_date', 'total_amount', 'status');

    foreach ($data as $index => $row) {
        $rowNumber = $index + 2;

        try {
            foreach ($requiredFields as $field) {
                if (!isset($row[$field]) || trim((string)$row[$field]) === '') {
                    throw new Exception('Missing required field: ' . $field);
                }
            }

            if (orderExists($row['order_id'])) {
                throw new Exception('Order ID ' . $row['order_id'] . ' already exists');
            }

            if (!filter_var($row['customer_email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format: ' . $row['customer_email']);
            }

            if (!isValidDate($row['order_date'])) {
                throw new Exception('Invalid date format: ' . $row['order_date'] . ' (use YYYY-MM-DD)');
            }

            if (!is_numeric($row['total_amount'])) {
                throw new Exception('Total amount must be numeric');
            }

            if (!isValidStatus($row['status'])) {
                throw new Exception('Invalid status: ' . $row['status']);
            }

            if (insertOrder($row)) {
                $imported++;
            } else {
                throw new Exception('Failed to insert order');
            }
        } catch (Exception $e) {
            $failed++;
            $errors[] = 'Row ' . $rowNumber . ': ' . $e->getMessage();
        }
    }

    return array(
        'imported' => $imported,
        'failed' => $failed,
        'details' => $errors
    );
}

function isValidStatus($status)
{
    $status = trim((string)$status);

    if ($status === '') {
        return false;
    }

    if (ctype_digit($status)) {
        $num = (int)$status;
        return $num >= 0 && $num <= 6;
    }

    $status = strtolower($status);
    $validStatuses = array('pending', 'completed', 'cancelled', 'processing', 'confirmed', 'rejected', 'waiting', 'rto', 'delivered');
    return in_array($status, $validStatuses, true);
}

function orderExists($orderId)
{
    return false;
}

function isValidDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function insertOrder($orderData)
{
    try {
        return true;
    } catch (Exception $e) {
        return false;
    }
}
