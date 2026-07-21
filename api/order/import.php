<?php

/**
 * Order Import API
 * Supports two-step flow:
 * 1) action=preview -> returns CSV/Excel headers + sample rows
 * 2) action=import  -> imports using provided mapping
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('success' => false, 'message' => 'Invalid request method. Use POST.'));
    exit;
}

try {
    if (empty($_FILES) || !isset($_FILES['file'])) {
        $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'unknown';
        throw new Exception('No file provided. Ensure request is multipart/form-data. Content-Type: ' . $contentType);
    }

    $action = isset($_POST['action']) ? strtolower(trim($_POST['action'])) : 'import';
    $companyId = isset($_POST['companyId']) ? (int)$_POST['companyId'] : 0;

    $file = $_FILES['file'];
    $uploadError = isset($file['error']) ? (int)$file['error'] : UPLOAD_ERR_NO_FILE;
    if ($uploadError !== UPLOAD_ERR_OK) {
        throw new Exception('File upload failed: ' . getUploadErrorMessage($uploadError));
    }

    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new Exception('Uploaded file is invalid or missing temporary file.');
    }

    $fileName = basename($file['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedExtensions = array('csv');
    if (!in_array($fileExtension, $allowedExtensions, true)) {
        throw new Exception('Invalid file type. Only CSV files are supported.');
    }

    $maxSize = 10 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds 10 MB limit.');
    }

    $parsed = parseCSVFile($file['tmp_name']);

    if (empty($parsed['headers'])) {
        throw new Exception('No header row found in file.');
    }

    if ($action === 'preview') {
        $tableMeta = getOrderTableColumns();
        echo json_encode(array(
            'success' => true,
            'headers' => $parsed['headers'],
            'sampleRows' => array_slice($parsed['rows'], 0, 5),
            'totalRows' => count($parsed['rows']),
            'tableColumns' => $tableMeta
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

    $tableMeta = getOrderTableColumns();
    $rowsToImport = applyMapping($parsed['rows'], $mapping, $tableMeta);
    $result = importOrders($rowsToImport, $tableMeta, $companyId);

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
    $headerIndexes = array();
    while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
        if ($headers === null) {
            $headerMeta = sanitizeHeaders($row);
            $headers = $headerMeta['headers'];
            $headerIndexes = $headerMeta['indexes'];
            continue;
        }

        $record = array();
        foreach ($headers as $index => $header) {
            $sourceIndex = isset($headerIndexes[$index]) ? (int)$headerIndexes[$index] : $index;
            $record[$header] = isset($row[$sourceIndex]) ? trim((string)$row[$sourceIndex]) : '';
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

function getUploadErrorMessage($code)
{
    switch ((int)$code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize limit.';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE limit.';
        case UPLOAD_ERR_PARTIAL:
            return 'The file was only partially uploaded.';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded.';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder on server.';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk.';
        case UPLOAD_ERR_EXTENSION:
            return 'A PHP extension stopped the file upload.';
        default:
            return 'Unknown upload error (' . (int)$code . ').';
    }
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
        $headerIndexes = array();
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = array();
            foreach ($cellIterator as $cell) {
                $rowData[] = trim((string)$cell->getFormattedValue());
            }

            if ($headers === null) {
                $headerMeta = sanitizeHeaders($rowData);
                $headers = $headerMeta['headers'];
                $headerIndexes = $headerMeta['indexes'];
                continue;
            }

            $record = array();
            foreach ($headers as $index => $header) {
                $sourceIndex = isset($headerIndexes[$index]) ? (int)$headerIndexes[$index] : $index;
                $record[$header] = isset($rowData[$sourceIndex]) ? trim((string)$rowData[$sourceIndex]) : '';
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
    $indexes = array();
    foreach ($headers as $index => $header) {
        $name = trim((string)$header);
        if ($name === '') {
            continue;
        }

        $baseName = $name;
        $suffix = 1;
        while (in_array($name, $clean, true)) {
            $suffix++;
            $name = $baseName . '_' . $suffix;
        }

        $clean[] = $name;
        $indexes[] = (int)$index;
    }
    return array(
        'headers' => $clean,
        'indexes' => $indexes
    );
}

function applyMapping($rows, $mapping, $tableMeta = array())
{
    if (empty($mapping) || !is_array($mapping) || empty($tableMeta)) {
        return array();
    }

    $allowedTargets = array();
    foreach ($tableMeta as $meta) {
        if (!empty($meta['key'])) {
            $allowedTargets[$meta['key']] = true;
        }
    }

    $mappedSourceColumns = array();
    foreach ($mapping as $targetField => $sourceColumn) {
        $sourceColumn = trim((string)$sourceColumn);
        if ($sourceColumn === '' || empty($allowedTargets[$targetField])) {
            continue;
        }
        $mappedSourceColumns[$sourceColumn] = true;
    }

    $mappedRows = array();
    foreach ($rows as $row) {
        $mapped = array();
        foreach ($mapping as $targetField => $sourceColumn) {
            $sourceColumn = trim((string)$sourceColumn);
            if ($sourceColumn === '' || empty($allowedTargets[$targetField])) {
                continue;
            }
            $mapped[$targetField] = isset($row[$sourceColumn]) ? trim((string)$row[$sourceColumn]) : '';
        }

        $mapped['__unmatched_meta'] = getUnmatchedColumnsForMeta($row, $mappedSourceColumns);
        $mappedRows[] = $mapped;
    }

    return $mappedRows;
}

function getUnmatchedColumnsForMeta($row, $mappedSourceColumns)
{
    $meta = array();

    foreach ($row as $sourceColumn => $value) {
        if (isset($mappedSourceColumns[$sourceColumn])) {
            continue;
        }

        if (isSerialNumberColumn($sourceColumn)) {
            continue;
        }

        $value = trim((string)$value);
        if ($value === '') {
            continue;
        }

        $metaKey = normalizeImportedMetaKey($sourceColumn);
        if ($metaKey === '') {
            continue;
        }

        $meta[$metaKey] = $value;
    }

    return $meta;
}

function isSerialNumberColumn($sourceColumn)
{
    $raw = strtolower(trim((string)$sourceColumn));
    if ($raw === '') {
        return false;
    }

    $norm = preg_replace('/[^a-z0-9]+/', '', $raw);
    if ($norm === null) {
        return false;
    }

    $serialAliases = array(
        'srno',
        'srnumber',
        'sno',
        'serial',
        'serialno',
        'serialnumber',
        'rowno',
        'rownumber',
        'index',
        'id'
    );

    return in_array($norm, $serialAliases, true);
}

function normalizeImportedMetaKey($key)
{
    $key = strtolower(trim((string)$key));
    if ($key === '') {
        return '';
    }

    $key = preg_replace('/[^a-z0-9]+/', '_', $key);
    $key = trim((string)$key, '_');

    if ($key === '') {
        return '';
    }

    if (preg_match('/^[0-9]/', $key) === 1) {
        $key = 'field_' . $key;
    }

    return 'import_' . substr($key, 0, 64);
}

function importOrders($data, $tableMeta, $companyId = 0)
{
    global $connection;

    $imported = 0;
    $failed = 0;
    $errors = array();

    if (empty($data)) {
        return array(
            'imported' => 0,
            'failed' => 0,
            'details' => array('No mapped data found. Please map at least required fields.')
        );
    }

    if (empty($connection)) {
        throw new Exception('Database connection not available.');
    }

    $requiredFields = getRequiredFields($tableMeta);
    $orderRows = array();
    $batchDuplicateGuard = array();

    mysqli_query($connection, 'START TRANSACTION');

    foreach ($data as $index => $row) {
        $rowNumber = $index + 2;
        $savepoint = 'sp_import_' . ($index + 1);

        try {
            mysqli_query($connection, 'SAVEPOINT ' . $savepoint);

            foreach ($requiredFields as $field) {
                if (!isset($row[$field]) || trim((string)$row[$field]) === '') {
                    throw new Exception('Missing required field: ' . $field);
                }
            }

            $mobile = normalizeMobile($row['user_mobile']);
            if ($mobile === '') {
                throw new Exception('Invalid mobile number');
            }

            $userId = getOrCreateUserByMobile($mobile, $row);
            $addressId = findOrCreateAddressForUser($userId, $mobile, $row);
            $deliveryDate = normalizeDeliveryDate($row['delivery_date']);

            if (isDuplicateOrder($userId, $addressId, $deliveryDate, $batchDuplicateGuard)) {
                throw new Exception('Duplicate order found for same user, address and delivery date');
            }

            $batchKey = $userId . '|' . $addressId . '|' . $deliveryDate;
            $batchDuplicateGuard[$batchKey] = true;

            $row['product'] = isset($row['product']) ? trim((string)$row['product']) : '';

            $orderRows[] = buildOrderRow($userId, $addressId, $row, $rowNumber, $deliveryDate, $companyId);
        } catch (Exception $e) {
            mysqli_query($connection, 'ROLLBACK TO SAVEPOINT ' . $savepoint);
            $failed++;
            $errors[] = 'Row ' . $rowNumber . ': ' . $e->getMessage();
        }
    }

    try {
        if (!empty($orderRows)) {
            $insertResult = bulkInsertOrders($orderRows);
            $imported = (int)$insertResult['count'];

            bulkInsertOrderMetaForDeliveryDate($orderRows, (int)$insertResult['firstInsertId']);
        }

        mysqli_query($connection, 'COMMIT');
    } catch (Exception $e) {
        mysqli_query($connection, 'ROLLBACK');
        throw new Exception('Bulk insert failed: ' . $e->getMessage());
    }

    return array(
        'imported' => $imported,
        'failed' => $failed,
        'details' => $errors
    );
}

function normalizeMobile($mobile)
{
    $digits = preg_replace('/\D+/', '', (string)$mobile);
    if ($digits === null) {
        return '';
    }

    if (strlen($digits) > 10) {
        $digits = substr($digits, -10);
    }

    if (strlen($digits) !== 10) {
        return '';
    }

    return $digits;
}

function getOrCreateUserByMobile($mobile, $row)
{
    global $connection;

    $mobileEscaped = mysqli_real_escape_string($connection, $mobile);
    $check = mysqli_query($connection, "SELECT profile_user_id FROM " . TABLE_PREFIX . "profile WHERE profile_mobile='{$mobileEscaped}' LIMIT 1");

    if ($check && mysqli_num_rows($check) > 0) {
        $record = mysqli_fetch_assoc($check);
        return (int)$record['profile_user_id'];
    }

    $displayName = trim((string)$row['name']);
    if ($displayName === '') {
        $displayName = 'Imported User';
    }

    $username = 'import_' . $mobile . '_' . time() . mt_rand(100, 999);
    $email = 'import+' . $mobile . '+' . time() . '@local.import';
    $plainPassword = substr(hash('sha256', $username . microtime(true)), 0, 12);
    $saltKey = hash('sha256', uniqid(mt_rand(), true) . $email . time());
    $encPassword = hash('sha256', $saltKey . $plainPassword);

    $usernameEscaped = mysqli_real_escape_string($connection, $username);
    $emailEscaped = mysqli_real_escape_string($connection, $email);
    $displayNameEscaped = mysqli_real_escape_string($connection, $displayName);

    $completeName = explode(' ', $displayNameEscaped, 2);
    $profileFirstName = isset($completeName[0]) ? $completeName[0] : '';
    $profileLastName = isset($completeName[1]) ? $completeName[1] : '';

    $insertUser = mysqli_query(
        $connection,
        "INSERT INTO " . TABLE_PREFIX . "user(user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES('{$usernameEscaped}', '{$emailEscaped}', '{$mobileEscaped}', '{$encPassword}', '{$saltKey}', 1, NOW(), 'import')"
    );

    if (!$insertUser) {
        throw new Exception('Unable to create user: ' . mysqli_error($connection));
    }

    $userId = (int)mysqli_insert_id($connection);

    $insertProfile = mysqli_query(
        $connection,
        "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name, profile_email) VALUES('{$userId}', '{$profileFirstName}', '{$profileLastName}', '{$mobileEscaped}', '{$displayNameEscaped}', '{$emailEscaped}')"
    );

    if (!$insertProfile) {
        throw new Exception('Unable to create profile: ' . mysqli_error($connection));
    }

    return $userId;
}

function findOrCreateAddressForUser($userId, $mobile, $row)
{
    global $connection;

    $name = trim((string)$row['name']);
    $area = trim((string)$row['address']);
    $pinCode = trim((string)$row['pin_code']);
    $city = trim((string)$row['city']);
    $state = trim((string)$row['state']);

    $cityId = resolveCityId($city);
    $stateId = resolveStateId($state);

    $nameEscaped = mysqli_real_escape_string($connection, $name);
    $areaEscaped = mysqli_real_escape_string($connection, $area);
    $mobileEscaped = mysqli_real_escape_string($connection, $mobile);
    $pinCodeEscaped = mysqli_real_escape_string($connection, $pinCode);

    $checkAddress = mysqli_query(
        $connection,
        "SELECT address_id FROM " . TABLE_PREFIX . "address WHERE address_site_id=" . SITE_ID . " AND address_user_id='{$userId}' AND address_area='{$areaEscaped}' AND address_pin_code='{$pinCodeEscaped}' AND address_city_id='{$cityId}' AND address_state_id='{$stateId}' LIMIT 1"
    );

    if ($checkAddress && mysqli_num_rows($checkAddress) > 0) {
        $existing = mysqli_fetch_assoc($checkAddress);
        return (int)$existing['address_id'];
    }

    $insertAddress = mysqli_query(
        $connection,
        "INSERT INTO " . TABLE_PREFIX . "address(address_site_id, address_user_id, address_name, address_area, address_city_id, address_state_id, address_country_id, address_pin_code, address_mobile, address_type, address_status) VALUES(" . SITE_ID . ", '{$userId}', '{$nameEscaped}', '{$areaEscaped}', '{$cityId}', '{$stateId}', 101, '{$pinCodeEscaped}', '{$mobileEscaped}', 'home', 1)"
    );

    if (!$insertAddress) {
        throw new Exception('Unable to create address: ' . mysqli_error($connection));
    }

    return (int)mysqli_insert_id($connection);
}

function isDuplicateOrder($userId, $addressId, $deliveryDate, $batchDuplicateGuard)
{
    global $connection;

    $batchKey = $userId . '|' . $addressId . '|' . $deliveryDate;
    if (isset($batchDuplicateGuard[$batchKey])) {
        return true;
    }

    $deliveryDateEscaped = mysqli_real_escape_string($connection, $deliveryDate);
    $checkQuery = mysqli_query(
        $connection,
        "SELECT order_id FROM " . TABLE_PREFIX . "order WHERE order_site_id=" . SITE_ID . " AND order_user_id='{$userId}' AND order_address_id='{$addressId}' AND order_msg='{$deliveryDateEscaped}' LIMIT 1"
    );

    return ($checkQuery && mysqli_num_rows($checkQuery) > 0);
}

function normalizeDeliveryDate($value)
{
    $raw = trim((string)$value);
    if ($raw === '') {
        throw new Exception('Delivery date is required');
    }

    $currentYear = date('Y');
    $hasYear = preg_match('/\b\d{4}\b/', $raw) === 1;
    $candidates = array($raw);

    if (!$hasYear) {
        $candidates[] = $raw . ' ' . $currentYear;

        if (preg_match('/^(\d{1,2})[\/.\-](\d{1,2})$/', $raw, $m) === 1) {
            $candidates[] = sprintf('%02d-%02d-%04d', (int)$m[1], (int)$m[2], (int)$currentYear);
        }

        if (preg_match('/^(\d{1,2})\s+([A-Za-z]+)$/', $raw, $m) === 1) {
            $candidates[] = $m[1] . ' ' . $m[2] . ' ' . $currentYear;
        }

        if (preg_match('/^([A-Za-z]+)\s+(\d{1,2})$/', $raw, $m) === 1) {
            $candidates[] = $m[1] . ' ' . $m[2] . ' ' . $currentYear;
        }
    }

    $formats = array(
        'Y-m-d',
        'd-m-Y',
        'd/m/Y',
        'd.m.Y',
        'j-n-Y',
        'j/n/Y',
        'j M Y',
        'j F Y',
        'M j Y',
        'F j Y',
        'd M Y',
        'd F Y',
        'M d Y',
        'F d Y'
    );

    foreach ($candidates as $candidate) {
        $candidate = trim((string)$candidate);
        if ($candidate === '') {
            continue;
        }

        foreach ($formats as $format) {
            $dt = DateTime::createFromFormat($format, $candidate);
            if ($dt instanceof DateTime) {
                $errors = DateTime::getLastErrors();
                if (
                    (!isset($errors['warning_count']) || (int)$errors['warning_count'] === 0)
                    && (!isset($errors['error_count']) || (int)$errors['error_count'] === 0)
                ) {
                    return $dt->format('Y-m-d');
                }
            }
        }

        $timestamp = strtotime($candidate);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
    }

    throw new Exception('Invalid delivery date format: ' . $raw);
}

function resolveCityId($city)
{
    global $connection;

    $city = trim((string)$city);
    if ($city === '') {
        return 0;
    }

    if (ctype_digit($city)) {
        return (int)$city;
    }

    $cityEscaped = mysqli_real_escape_string($connection, strtolower($city));
    $query = mysqli_query($connection, "SELECT city_id FROM " . TABLE_PREFIX . "city WHERE LOWER(city_name)='{$cityEscaped}' LIMIT 1");
    if ($query && mysqli_num_rows($query) > 0) {
        $record = mysqli_fetch_assoc($query);
        return (int)$record['city_id'];
    }

    return 0;
}

function resolveStateId($state)
{
    global $connection;

    $state = trim((string)$state);
    if ($state === '') {
        return 0;
    }

    if (ctype_digit($state)) {
        return (int)$state;
    }

    $stateEscaped = mysqli_real_escape_string($connection, strtolower($state));
    $query = mysqli_query($connection, "SELECT state_id FROM " . TABLE_PREFIX . "state WHERE LOWER(state_name)='{$stateEscaped}' LIMIT 1");
    if ($query && mysqli_num_rows($query) > 0) {
        $record = mysqli_fetch_assoc($query);
        return (int)$record['state_id'];
    }

    return 0;
}

function getProductPathFromUrl($url)
{
    $url = trim((string)$url);
    if ($url === '') {
        return '';
    }
    // Parse URL to get the path
    $path = parse_url($url, PHP_URL_PATH);
    if (!$path) {
        $path = $url;
    }

    // Normalize: ensure it starts with / and ends with /
    $path = '/' . trim($path, '/') . '/';

    if ($path === '//') {
        return '';
    }
    return $path;
}

function buildOrderRow($userId, $addressId, $row, $rowNumber, $deliveryDate, $companyId = 0)
{
    global $connection;

    $product = trim((string)$row['product']);
    $remarks = isset($row['remarks']) ? trim((string)$row['remarks']) : '';
    $extraMeta = array();
    if (!empty($row['__unmatched_meta']) && is_array($row['__unmatched_meta'])) {
        $extraMeta = $row['__unmatched_meta'];
    }

    $productId = $product; // fallback to raw product value
    $productPrice = '0';
    $currency = 'INR';
    $found = false;

    // 1. Try URL lookup first
    $productPath = getProductPathFromUrl($product);
    if ($productPath !== '' && strpos($productPath, '/product/') === 0) {
        $pathNoTrailingSlash = rtrim($productPath, '/');
        $pathEscaped = mysqli_real_escape_string($connection, $productPath);
        $pathNoTrailingSlashEscaped = mysqli_real_escape_string($connection, $pathNoTrailingSlash);

        $query = "
            SELECT p.product_id, p.product_title, i.inventory_price, i.inventory_currency
            FROM " . TABLE_PREFIX . "link l
            JOIN " . TABLE_PREFIX . "product p ON l.link_relation_id = p.product_id
            JOIN " . TABLE_PREFIX . "inventory i ON i.inventory_relation_id = p.product_id
            WHERE (l.link_url = '{$pathEscaped}' OR l.link_url = '{$pathNoTrailingSlashEscaped}')
              AND l.link_site_id = " . APP_SITE_ID . "
              AND l.link_type = 'product'
            LIMIT 1
        ";
        $res = mysqli_query($connection, $query);
        if ($res && mysqli_num_rows($res) > 0) {
            $prodDetails = mysqli_fetch_assoc($res);
            $productId = (string)$prodDetails['product_id'];
            $productPrice = (string)$prodDetails['inventory_price'];
            if (!empty($prodDetails['inventory_currency'])) {
                $currency = $prodDetails['inventory_currency'];
            }
            $found = true;
        }
    }

    // 2. Try lookup by Title if not resolved yet
    if (!$found && $product !== '') {
        $productEscaped = mysqli_real_escape_string($connection, $product);
        $query = "
            SELECT p.product_id, p.product_title, i.inventory_price, i.inventory_currency
            FROM " . TABLE_PREFIX . "product p
            JOIN " . TABLE_PREFIX . "inventory i ON i.inventory_relation_id = p.product_id
            WHERE p.product_title = '{$productEscaped}'
              AND p.product_site_id = " . APP_SITE_ID . "
            LIMIT 1
        ";
        $res = mysqli_query($connection, $query);
        if ($res && mysqli_num_rows($res) > 0) {
            $prodDetails = mysqli_fetch_assoc($res);
            $productId = (string)$prodDetails['product_id'];
            $productPrice = (string)$prodDetails['inventory_price'];
            if (!empty($prodDetails['inventory_currency'])) {
                $currency = $prodDetails['inventory_currency'];
            }
            $found = true;
        }
    }

    // 3. Try lookup by SKU if not resolved yet
    if (!$found && $product !== '') {
        $productEscaped = mysqli_real_escape_string($connection, $product);
        $query = "
            SELECT p.product_id, p.product_title, i.inventory_price, i.inventory_currency
            FROM " . TABLE_PREFIX . "product p
            JOIN " . TABLE_PREFIX . "inventory i ON i.inventory_relation_id = p.product_id
            WHERE i.inventory_sku = '{$productEscaped}'
              AND p.product_site_id = " . APP_SITE_ID . "
            LIMIT 1
        ";
        $res = mysqli_query($connection, $query);
        if ($res && mysqli_num_rows($res) > 0) {
            $prodDetails = mysqli_fetch_assoc($res);
            $productId = (string)$prodDetails['product_id'];
            $productPrice = (string)$prodDetails['inventory_price'];
            if (!empty($prodDetails['inventory_currency'])) {
                $currency = $prodDetails['inventory_currency'];
            }
            $found = true;
        }
    }

    // 4. Default fallback product lookup if not found yet (and product value is not blank)
    if (!$found && $product !== '') {
        $query = "
            SELECT p.product_id, p.product_title, i.inventory_price, i.inventory_currency
            FROM " . TABLE_PREFIX . "product p
            JOIN " . TABLE_PREFIX . "inventory i ON i.inventory_relation_id = p.product_id
            WHERE p.product_site_id = " . APP_SITE_ID . "
            ORDER BY p.product_id ASC
            LIMIT 1
        ";
        $res = mysqli_query($connection, $query);
        if ($res && mysqli_num_rows($res) > 0) {
            $prodDetails = mysqli_fetch_assoc($res);
            $productId = (string)$prodDetails['product_id'];
            $productPrice = (string)$prodDetails['inventory_price'];
            if (!empty($prodDetails['inventory_currency'])) {
                $currency = $prodDetails['inventory_currency'];
            }
            $found = true;
        }
    }

    return array(
        'order_site_id' => (int)SITE_ID,
        'order_user_id' => (int)$userId,
        'order_cart_session_id' => 'IMPORT_' . date('YmdHis') . '_' . $rowNumber,
        'order_generate' => date('Y-m-d H:i:s'),
        'order_address_id' => (int)$addressId,
        'order_product_id' => $productId,
        'order_product_type' => 'product',
        'order_product_qty' => '1',
        'order_product_price' => $productPrice,
        'order_currency' => $currency,
        'order_delivery_charges' => '0',
        'order_total_price' => $productPrice,
        'order_coupon' => '',
        'order_gift' => '0',
        'order_msg' => $remarks,
        'order_ref_by' => $companyId > 0 ? (int)$companyId : 0,
        'order_ref_type' => $companyId > 0 ? 'company' : '',
        'order_status' => '0',
        'order_payment_mode' => '',
        '__extra_meta' => $extraMeta,
        'deliveryDate' => $deliveryDate
    );
}

function bulkInsertOrders($orders)
{
    global $connection;

    if (empty($orders)) {
        return 0;
    }

    $columns = array(
        'order_site_id',
        'order_user_id',
        'order_cart_session_id',
        'order_generate',
        'order_address_id',
        'order_product_id',
        'order_product_type',
        'order_product_qty',
        'order_product_price',
        'order_currency',
        'order_delivery_charges',
        'order_total_price',
        'order_coupon',
        'order_gift',
        'order_msg',
        'order_ref_by',
        'order_ref_type',
        'order_status',
        'order_payment_mode'
    );

    $valueChunks = array();
    foreach ($orders as $order) {
        $values = array();
        foreach ($columns as $column) {
            $value = isset($order[$column]) ? (string)$order[$column] : '';
            $values[] = "'" . mysqli_real_escape_string($connection, $value) . "'";
        }
        $valueChunks[] = '(' . implode(', ', $values) . ')';
    }

    $sql = "INSERT INTO " . TABLE_PREFIX . "order(" . implode(', ', $columns) . ") VALUES " . implode(', ', $valueChunks);

    if (!mysqli_query($connection, $sql)) {
        throw new Exception(mysqli_error($connection));
    }

    return array(
        'count' => count($orders),
        'firstInsertId' => (int)mysqli_insert_id($connection)
    );
}

function bulkInsertOrderMetaForDeliveryDate($orders, $firstInsertId)
{
    global $connection;

    if (empty($orders) || $firstInsertId <= 0) {
        return;
    }

    $rows = array();
    $currentOrderId = (int)$firstInsertId;

    foreach ($orders as $index => $order) {
        $orderId = $currentOrderId + $index;
        $deliveryDate = isset($order['deliveryDate']) ? trim((string)$order['deliveryDate']) : '';

        if ($deliveryDate === '') {
            $deliveryDate = '';
        }

        if ($deliveryDate !== '') {
            $deliveryDateEscaped = mysqli_real_escape_string($connection, $deliveryDate);
            $rows[] = "('{$orderId}', '" . SITE_ID . "', 'deliveryDate', '{$deliveryDateEscaped}', 1)";
        }

        $extraMeta = array();
        if (!empty($order['__extra_meta']) && is_array($order['__extra_meta'])) {
            $extraMeta = $order['__extra_meta'];
        }

        foreach ($extraMeta as $metaKey => $metaValue) {
            $metaKey = trim((string)$metaKey);
            $metaValue = trim((string)$metaValue);

            if ($metaKey === '' || $metaValue === '') {
                continue;
            }

            $metaKeyEscaped = mysqli_real_escape_string($connection, $metaKey);
            $metaValueEscaped = mysqli_real_escape_string($connection, $metaValue);
            $rows[] = "('{$orderId}', '" . SITE_ID . "', '{$metaKeyEscaped}', '{$metaValueEscaped}', 1)";
        }
    }

    if (empty($rows)) {
        return;
    }

    $sql = "INSERT INTO " . TABLE_PREFIX . "order_meta(order_relation_id, order_site_id, order_meta_key, order_meta_value, order_meta_status) VALUES " . implode(', ', $rows);

    if (!mysqli_query($connection, $sql)) {
        throw new Exception('Unable to insert order meta: ' . mysqli_error($connection));
    }
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

function getOrderTableColumns()
{
    return array(
        array('key' => 'name', 'label' => 'Name', 'required' => true),
        array('key' => 'email', 'label' => 'Email', 'required' => false),
        array('key' => 'user_mobile', 'label' => 'Mobile No.', 'required' => true),
        array('key' => 'address', 'label' => 'Address', 'required' => true),
        array('key' => 'city', 'label' => 'City', 'required' => true),
        array('key' => 'pin_code', 'label' => 'Pin Code', 'required' => true),
        array('key' => 'state', 'label' => 'State', 'required' => true),
        array('key' => 'product', 'label' => 'Product', 'required' => false),
        array('key' => 'delivery_date', 'label' => 'Delivery Date', 'required' => false),
        array('key' => 'order_status', 'label' => 'Order Status', 'required' => false),
        array('key' => 'remarks', 'label' => 'Remarks', 'required' => false)
    );
}

function getRequiredFields($tableMeta)
{
    $required = array();
    foreach ($tableMeta as $meta) {
        if (!empty($meta['required']) && !empty($meta['key'])) {
            $required[] = $meta['key'];
        }
    }
    return $required;
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
