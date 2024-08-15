<?php

if (isset($uploadDocument)) {

    /** Valid file extensions and MIME types */
    $validFileExtensionImages = array('png', 'gif', 'jpg', 'jpeg', 'pdf');
    $validFileTypesImages = array('image/png', 'image/gif', 'image/jpeg', 'application/pdf');
    $maxFileSize = 2 * 1024 * 1024;  // Maximum file size: 2MB

    foreach ($_FILES as $fileKey => $fileArray) {
        if ($fileArray["name"]) {
            if ($fileArray["error"] == 0 && $fileArray["size"] <= $maxFileSize) {

                $filename = strtolower(md5(uniqid()) . '.' . pathinfo($fileArray['name'], PATHINFO_EXTENSION));
                $uploadPath = ROOT . '/upload/' . SITE_DOMAIN . '/' . date('Y/m/') . 'documents/';
                $uploadFile = $uploadPath . $filename;
                $uploadMediaPath = str_replace(ROOT, '', $uploadFile);
                $uploadMediaLink = '/attachment' . str_replace('.' . pathinfo($filename, PATHINFO_EXTENSION), '', $uploadMediaPath) . '/';
                $uploadFileName = ucwords(preg_replace('/[_]+/', '_', pathinfo($filename, PATHINFO_FILENAME)));

                // Ensure the file is uploaded via HTTP POST
                if (!is_uploaded_file($fileArray['tmp_name'])) {
                    echo "Possible file upload attack!";
                    continue;
                }

                // MIME type validation
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $fileMimeType = finfo_file($finfo, $fileArray['tmp_name']);
                finfo_close($finfo);

                if (in_array($fileMimeType, $validFileTypesImages)) {

                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    /** File upload and database insertion */
                    if (move_uploaded_file($fileArray['tmp_name'], $uploadFile)) {

                        mysqli_query($connection, 'START TRANSACTION');

                        $mediaFileName = str_replace('_', ' ', $fileKey);

                        $insertPost = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "media(media_url, media_title, media_type, media_ext, media_owner_id, media_status, media_site_id) VALUES('{$uploadMediaPath}', '{$mediaFileName}', '{$fileMimeType}', '" . pathinfo($filename, PATHINFO_EXTENSION) . "', '{$userId}', 1, '" . SITE_ID . "')");
                        $mediaID = mysqli_insert_id($connection);

                        $createLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('{$uploadMediaLink}', '{$mediaID}', 200, 'media', '{$userId}', '" . SITE_ID . "', NOW(), NOW(), NOW())");

                        if ($insertPost && $createLink) {
                            mysqli_query($connection, 'COMMIT');
                        } else {
                            mysqli_query($connection, 'ROLLBACK');
                            echo mysqli_error($connection);
                        }
                    } else {
                        $msg = 'Unable to move file';
                        error_log($msg);  // Log the error
                    }

                    $files[] = array(
                        'name' => $fileKey,
                        'path' => $uploadMediaPath,
                        'type' => $fileMimeType,
                    );
                } else {
                    echo "Invalid file type: " . $fileMimeType;
                }
            } else {
                echo "File error or size exceeds limit.";
            }
        }
    }

    $files = (isset($files)) ? json_encode($files, true) : '';
}