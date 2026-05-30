<?php

function category($connection, $data)
{

    extract($data);

    if (isset($id)) {

        $id = base64_decode($id);

        if ($checkCategory = mysqli_query($connection, "SELECT category_idFROM " . TABLE_PREFIX . "category WHERE link_type='category' AND category_id='{$id}' AND category_type='testimonial' AND link_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($checkCategory) != 0) {

                extract(mysqli_fetch_assoc($checkCategory));
                if (mysqli_num_rows($checkCategoryLinks) == 0) {

                    // Category is not linked with any other data

                    mysqli_query($connection, "START TRANSACTION");

                    $deleteCategory = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "category WHERE category_id='{$category_id}'");

                    if ($deleteCategory) {

                        $status = 200;
                        $msg = 'Testimonial delete successfully';
                        mysqli_query($connection, "COMMIT");
                    } else {
                        $msg = 'Something is wrong while deleting testimonial';
                        mysqli_query($connection, "ROLLBACK");
                        exit();
                    }
                } else {

                    // Category is link with other data's

                    $status = 'warning';
                    $msg = "Testimonial is link with other data's";
                }
            } else {

                $msg = 'No category found';
            }
        } else {

            $msg = 'Somthing is wrong while fetching category';
        }
    }

    $result = array(
        'status' => (isset($status)) ? $status : 'error',
        'msg' => ((isset($msg)) ? $msg : 'Invalid response')
    );

    echo json_encode($result, TRUE);
}
