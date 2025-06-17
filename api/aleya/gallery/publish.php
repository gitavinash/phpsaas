<?php

require_once LOGIN_CHECK;
$responseCode = 500;

$packageId = base64_decode($packageId);

// Step 1: Fetch tour package and its gallery info
$query = "
    SELECT g.gallery_image_list, g.gallery_id
    FROM " . TABLE_PREFIX . "tour_package AS tp
    LEFT JOIN " . TABLE_PREFIX . "gallery AS g
        ON g.gallery_category = tp.tour_package_id
        AND g.gallery_site_id = " . SITE_ID . "
        AND g.gallery_type = 'tour_package'
        AND g.gallery_status = 1
    WHERE tp.tour_package_id = '{$packageId}'
    AND tp.tour_package_site_id = " . SITE_ID;

if ($resultQuery = mysqli_query($connection, $query)) {
    if (mysqli_num_rows($resultQuery) > 0) {
        $row = mysqli_fetch_assoc($resultQuery);
        $existingGallery = json_decode($row['gallery_image_list'] ?? '[]', true) ?? [];

        // Step 2: Handle uploaded files
        if (isset($_FILES['gallery'])) {
            require_once MODULES . 'galleryUploader.php'; // sets $images
            $newGallery = $images ?? [];

            // Step 3: Merge old and new images
            $mergedGallery = array_merge($existingGallery, $newGallery);
            $mergedGalleryJson = mysqli_real_escape_string($connection, json_encode($mergedGallery, JSON_UNESCAPED_SLASHES));

            // Step 4: Update or Insert based on gallery existence
            if (!empty($row['gallery_id'])) {
                // Update existing gallery
                $updateQuery = "
                    UPDATE " . TABLE_PREFIX . "gallery
                    SET gallery_image_list = '{$mergedGalleryJson}'
                    WHERE gallery_id = '{$row['gallery_id']}'
                    AND gallery_site_id = " . SITE_ID;
                $executeQuery = mysqli_query($connection, $updateQuery);
            } else {
                // Insert new gallery
                $insertQuery = "
                    INSERT INTO " . TABLE_PREFIX . "gallery
                    (gallery_image, gallery_thumbnail, gallery_title, gallery_category, gallery_site_id, gallery_type, gallery_image_list, gallery_owner_id, gallery_status)
                    VALUES
                    ('{$newGallery[0]['path']}','{$newGallery[0]['path']}', '{$newGallery[0]['name']}', '{$packageId}', " . SITE_ID . ", 'tour_package', '{$mergedGalleryJson}', '" . USER_ID . "', 1)";
                $executeQuery = mysqli_query($connection, $insertQuery);
            }

            if ($executeQuery) {
                $status = 'success';
                $responseCode = 200;
                $msg = "Images have been successfully saved to the gallery.";
            } else {
                $msg = "Database error while storing the gallery.";
            }
        } else {
            $msg = "No gallery images uploaded.";
        }
    } else {
        $responseCode = 404;
        $msg = 'Tour package not found.';
    }
} else {
    $msg = "Query execution failed: " . mysqli_error($connection);
}

http_response_code($responseCode);

echo json_encode([
    'status' => $status ?? 'error',
    'msg'    => $msg ?? 'Unexpected error occurred',
]);