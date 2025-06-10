<?php
function handleFileUpload($file, $allowedTypes = [], $maxSize = null, $multiple = false)
{
    $uploadedFiles = [];

    if (is_array($file['name'])) {
        // Handle multiple files
        foreach ($file['name'] as $index => $name) {
            $singleFile = [
                'name'     => $file['name'][$index],
                'type'     => $file['type'][$index],
                'tmp_name' => $file['tmp_name'][$index],
                'error'    => $file['error'][$index],
                'size'     => $file['size'][$index],
            ];

            $result = processSingleFile($singleFile);
            if ($result !== false) {
                $uploadedFiles[] = $result;
            }
        }
    } else {
        // Handle single file
        $result = processSingleFile($file);
        if ($result !== false) {
            $uploadedFiles[] = $result;
        }
    }

    return $uploadedFiles;
}

function processSingleFile($file)
{
    $maxSize = 5 * 1024 * 1024;
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if (!empty($allowedTypes) && !in_array($file['type'], $allowedTypes)) {
        return false;
    }

    if (!empty($maxSize) && $file['size'] > $maxSize) {
        return false;
    }


    $uploadPath = ROOT . '/upload/' . SITE_DOMAIN . '/' . date('Y/m/');

    // Ensure destination directory exists
    if (!file_exists($uploadPath)) {

        mkdir($uploadPath, 0755, true);
    }

    $unique = uniqid('', true);
    $safeName = time() . '_' . $unique . '_' . basename($file['name']);
    $destination = rtrim($uploadPath, '/') . '/' . $safeName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return str_replace(ROOT, '', $destination);
    }

    return false;
}