<?php

require_once LOGIN_CHECK;

function publish($connection, $data)
{

    extract($data);

    if ($title != '') {

        require_once MODULES . 'imageUploader.php';

        if (!isset($featureImage)) {

            /** if feature imaeg is not upload then set featureImage as blank */

            $featureImage = '';
            $thumbnailImage = '';
        }

        $facebookImage = (((isset($facebookImage)) ? $facebookImage : $featureImage));
        $twitterImage = (((isset($twitterImage)) ? $twitterImage : $featureImage));

        /** career url */

        $url = '/career/' . strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';

        /* define facebook and twitter meta desciption */

        $fb_desc = (($fb_desc == '') ? $meta_desc : $fb_desc);
        $tw_desc = (($tw_desc == '') ? $meta_desc : $tw_desc);

        $page_section = json_encode(array(
            'career' => array(
                'location' => ((isset($location)) ? $location : ''),
                'job_link' => ((isset($job_link)) ? $job_link : ''),
            )
        ), true);

        $page_section = base64_encode(str_replace('\r\n', '<br />', $page_section));

        if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

            if (mysqli_num_rows($checkURL) == 0) {

                require_once EDITOR_CONTENT_FILTER;

                $link_status = 200;

                /** Start uploading the career */

                mysqli_query($connection, "START TRANSACTION");

                /* Insert data into link table */

                $addNewCareer = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "page(page_site_id, page_image, page_thumbnail, page_title, page_description, page_owner_id, page_status, page_type, page_section) VALUES(" . SITE_ID . ", '{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '" . USER_ID . "', '1', 'career', '{$page_section}')");
                $careerID = mysqli_insert_id($connection);

                $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$careerID}', '{$link_status}', 'page', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                $linkID = mysqli_insert_id($connection);

                $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . APP_SITE_ID . ", '{$meta_title}', 'page', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

                if ($addNewCareer && $addNewLink && $addNewMeta) {

                    $status = 200;
                    $msg = 'Career add successfully';
                    $redirect = '/dashboard/career/?type=edit&editor=true&id=' . base64_encode($careerID);
                    mysqli_query($connection, "COMMIT");
                } else {

                    echo mysqli_error($connection);
                    $msg = 'Something is wrong while adding new page';
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {

                $msg = 'Please change url';
            }
        } else {

            $msg = 'Technical errro';
        }
    } else {

        $msg = 'Please enter career name';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}