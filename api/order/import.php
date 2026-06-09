<?php

/**
 * Order Import API
 * Handles file upload and import processing for XLS, XLSX, and CSV files
 */

header('Content-Type: application/json');

// Check if this is a file upload request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => 'No file provided']);
    exit;
}

try {
    $file = $_FILES['file'];
    $fileName = basename($file['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Validate file extension
    $allowedExtensions = ['csv', 'xls', 'xlsx'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        throw new Exception('Invalid file type. Only CSV, XLS, and XLSX files are supported.');
    }

    // Validate file size (10 MB max)
    $maxSize = 10 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds 10 MB limit.');
    }

    // Parse file based on extension
    if ($fileExtension === 'csv') {
        $data = parseCSVFile($file['tmp_name']);
    } else {
        $data = parseExcelFile($file['tmp_name'], $fileExtension);
    }

    // Validate and import data
    $result = importOrders($data);

    echo json_encode([
        'success' => true,
        'message' => 'Orders imported successfully',
        'importedCount' => $result['imported'],
        'failedCount' => $result['failed'],
        'details' => $result['details']
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/**
 * Parse CSV file
 */
function parseCSVFile($filePath)
{
    $data = [];
    $handle = fopen($filePath, 'r');

    if (!$handle) {
        throw new Exception('Unable to open CSV file');
    }

    $headers = null;
    $rowNumber = 0;

    while (($row = fgetcsv($handle, 0, ',')) !== false) {
        $rowNumber++;

        if ($headers === null) {
            $headers = $row;
            continue;
        }

        $record = [];
        foreach ($headers as $index => $header) {
            $record[trim($header)] = isset($row[$index]) ? trim($row[$index]) : '';
        }

        $data[] = $record;
    }

    fclose($handle);
    return $data;
}

/**
 * Parse Excel file (XLS/XLSX)
 * Requires PHPExcel or PhpSpreadsheet library
 */
function parseExcelFile($filePath, $extension)
{
    $data = [];

    try {
        // Try using PhpSpreadsheet if available
        if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $headers = null;
            foreach ($sheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                if ($headers === null) {
                    $headers = array_map('trim', $rowData);
                    continue;
                }

                $record = [];
                foreach ($headers as $index => $header) {
                    $record[$header] = isset($rowData[$index]) ? trim($rowData[$index]) : '';
                }

                if (!empty(array_filter($record))) {
                    $data[] = $record;
                }
            }
        } else {
            throw new Exception('Excel file processing library not found. Please install PhpSpreadsheet.');
        }
    } catch (Exception $e) {
        throw new Exception('Error parsing Excel file: ' . $e->getMessage());
    }

    return $data;
}

/**
 * Import orders from parsed data
 */
function importOrders($data)
{
    $imported = 0;
    $failed = 0;
    $details = [];
    $errors = [];

    // Required fields
    $requiredFields = ['order_id', 'customer_name', 'customer_email', 'order_date', 'total_amount', 'status'];

    foreach ($data as $index => $row) {
        $rowNumber = $index + 2; // +2 because header is row 1, array starts at 0

        try {
            // Validate required fields
            foreach ($requiredFields as $field) {
                if (empty($row[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            // Validate order_id is unique
            if (orderExists($row['order_id'])) {
                throw new Exception("Order ID {$row['order_id']} already exists");
            }

            // Validate email format
            if (!filter_var($row['customer_email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format: {$row['customer_email']}");
            }

            // Validate date format
            if (!isValidDate($row['order_date'])) {
                throw new Exception("Invalid date format: {$row['order_date']} (use YYYY-MM-DD)");
            }

            // Validate amount is numeric
            if (!is_numeric($row['total_amount'])) {
                throw new Exception("Total amount must be numeric");
            }

            // Validate status
            $validStatuses = ['pending', 'completed', 'cancelled', 'processing'];
            if (!in_array(strtolower($row['status']), $validStatuses)) {
                throw new Exception("Invalid status: {$row['status']}");
            }

            // Insert order
            if (insertOrder($row)) {
                $imported++;
            } else {
                throw new Exception("Failed to insert order");
            }
        } catch (Exception $e) {
            $failed++;
            $errors[] = "Row $rowNumber: " . $e->getMessage();
        }
    }

    return [
        'imported' => $imported,
        'failed' => $failed,
        'details' => $errors
    ];
}

/**
 * Check if order already exists
 */
function orderExists($orderId)
{
    // This is a placeholder - implement based on your database structure
    // Example using PDO or mysqli:
    // $query = "SELECT COUNT(*) FROM orders WHERE order_id = ?";
    // $result = $db->execute($query, [$orderId]);
    // return $result->fetchColumn() > 0;
    return false;
}

/**
 * Validate date format
 */
function isValidDate($date, $format = 'Y-m-d')
{
    $d = \DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Insert order into database
 */
function insertOrder($orderData)
{
    try {
        // This is a placeholder - implement based on your database structure
        // Example using PDO:
        /*
        global $pdo;
        $query = "INSERT INTO orders (order_id, customer_name, customer_email, order_date, total_amount, status, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $orderData['order_id'],
            $orderData['customer_name'],
            $orderData['customer_email'],
            $orderData['order_date'],
            $orderData['total_amount'],
            $orderData['status']
        ]);
        return true;
        */
        return true;
    } catch (Exception $e) {
        return false;
    }
}
