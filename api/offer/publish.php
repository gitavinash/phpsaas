<?php

    require_once LOGIN_CHECK;
    require_once FUNCTIONS . 'duplicate.php';

    function publish($connection, $data) {

        extract($data);
        
        if(isset($title) && $title!='') {

            $image = array();
            $thumbnailImage = '';
            require_once MODULES.'imageUploader.php';

            $resolvedImages = iswResolveDuplicateImages(
                $connection,
                $data,
                (isset($image) && is_array($image)) ? $image : array(),
                isset($thumbnailImage) ? (string)$thumbnailImage : ''
            );
            $featureImage = $resolvedImages['featureImage'];
            $thumbnailImage = $resolvedImages['thumbnailImage'];
            $facebookImage = $resolvedImages['facebookImage'];
            $twitterImage = $resolvedImages['twitterImage'];
            $description = isset($description) ? $description : '';

            /** offer url */

            $slugClean = trim((string)preg_replace('~[^\p{M}\w-]+~u', '-', trim((string)($url ?? ''), '/')), '-');
            if ($slugClean === '') {
                $slugClean = trim((string)preg_replace('~[^\p{M}\w-]+~u', '-', trim((string)$title)), '-');
            }
            $url = '/offer/' . strtolower($slugClean) . '/';
                    
            /* define facebook and twitter meta desciption */

            $meta_title = isset($meta_title) ? $meta_title : '';
            $meta_desc = isset($meta_desc) ? $meta_desc : '';
            $meta_key = isset($meta_key) ? $meta_key : '';
            $canonical = isset($canonical) ? $canonical : '';
            $robots = isset($robots) ? $robots : 'index, follow';
            $fb_title = isset($fb_title) ? $fb_title : '';
            $fb_desc = ((!isset($fb_desc) || $fb_desc=='') ? $meta_desc : $fb_desc );
            $tw_title = isset($tw_title) ? $tw_title : '';
            $tw_desc = ((!isset($tw_desc) || $tw_desc=='') ? $meta_desc : $tw_desc );

            if($checkURL=mysqli_query($connection, "SELECT link_relation_id FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {

                if(mysqli_num_rows($checkURL)==0) {

                    require_once EDITOR_CONTENT_FILTER;

                    $link_status=200;

                    /** Start uploading the offer */

                    mysqli_query($connection, "START TRANSACTION");

                    /* Insert data into link table */

                    $addNewOffer=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."page(page_site_id, page_image, page_thumbnail, page_title, page_description, page_owner_id, page_status, page_type) VALUES(".SITE_ID.", '{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '".USER_ID."', '1', 'offer')");
                    $offerID=mysqli_insert_id($connection);

                    $addNewLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$offerID}', '{$link_status}', 'page', ".USER_ID.", '".SITE_ID."', NOW() ,NOW())");
                    $linkID=mysqli_insert_id($connection);
                    
                    $addNewMeta=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', ".APP_SITE_ID.", '{$meta_title}', 'page', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

                    if($addNewOffer && $addNewLink && $addNewMeta) {

                        $status=200;
                        $msg='offer add successfully';
                        $redirect='/dashboard/offer/?type=edit&editor=true&id='.base64_encode($offerID);
                        mysqli_query($connection, "COMMIT");

                    } else {

                        $msg='Something is wrong while adding new page';
                        mysqli_query($connection, "ROLLBACK");

                    }

                } else {

                    $msg='Please change url';

                }

            } else {

                $msg='Technical errro';

            }

        } else {

            $msg='Please enter offer name';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'redirect' => ((isset($redirect)) ? $redirect : '' )
        );
    
        echo json_encode($result, TRUE);

    }