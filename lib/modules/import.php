<?php
    
    function csv($connection, $data) {

        global $table_prefix;
        extract($data);

        /** CSV function */

        $status=206;
        $msg='Something is wrong while trying to import file';

        if($_FILES['importFile']['error']==0) {

            $extension=explode('.', $_FILES['importFile']['name']);
            $extension=end($extension);

            /** CSV file valid MIME types */
            
            $validCSV=array(
                'text/csv',
                'text/plain',
                'application/csv',
                'text/comma-separated-values',
                'application/excel',
                'application/vnd.ms-excel',
                'application/vnd.msexcel',
                'text/anytext',
                'application/octet-stream',
                'application/txt'
            );
            
            /** Check file extension is csv or not */

            if(($extension=='csv') && (in_array($_FILES['importFile']['type'], $validCSV))) {

                /** Fetch category URL */

                $category=explode('_', $category);
                $categoryID=reset($category);
                $categoryUrl=end($category);

                $openFile=fopen($_FILES['importFile']['tmp_name'], 'r');

                $i=0;
                $success=0;
                $dublicate=0;

                while(($dataLines=fgetcsv($openFile)) !== false) {

                    /** Start MySQL Transaction */

                    mysqli_query($connection, "START TRANSACTION");
                    
                    if($i!=0) {

                        /** Define variable of csv file */
                        
                        $post_title=mysqli_real_escape_string($connection, $dataLines[2]);
                        $post_content=str_replace('\\\"', '"', mysqli_real_escape_string($connection, $dataLines[3]));
                        $post_date=mysqli_real_escape_string($connection, $dataLines[5]);
                        $post_modified=mysqli_real_escape_string($connection, $dataLines[6]);
                        $meta_title=mysqli_real_escape_string($connection, $dataLines[7]);
                        $meta_desc=mysqli_real_escape_string($connection, $dataLines[8]);
                        $tags=mysqli_real_escape_string($connection, $dataLines[9]);
                        $link_url=$categoryUrl.mysqli_real_escape_string($connection, $dataLines[4]).'/';

                        if($checkLink=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."link WHERE link_site_id='".SITE_ID."' AND link_url='{$link_url}'")) {
                            if(mysqli_num_rows($checkLink)!=0) {
                                $link_url=$categoryUrl.$dataLines[4].'/';
                            }
                        }

                        /** Download image from server */
                        
                        $old_post_image=$dataLines[1];

                        /** Find image file name */

                        $imagePath=explode('/', $old_post_image);
                        $imageFileName=end($imagePath);

                        /** Find image extension */

                        $imageFileExtension=explode('.', $imageFileName);
                        $imageFileExtension=str_replace(' ', '_', end($imageFileExtension));

                        $file_name=str_replace('-', ' ', str_replace('_', ' ', str_replace('.'.$imageFileExtension, '', $imageFileName)));
                        
                        $uploadPath=ROOT.'/upload/'.date('Y/m/');
                        
						if(!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0755, true);
                        } else {
                            $msg='Unable to create uplaod path';
                        }
                        
                        $new_image_path=strtolower($uploadPath.time().'_'.$imageFileName);
                        
                        if(file_put_contents($new_image_path, file_get_contents($old_post_image))) {

                            $post_image=strtolower(str_ireplace(ROOT, '', $new_image_path));
                            $insertMedia=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."media(media_url, media_title, media_status, media_type, media_ext, media_owner_id, media_site_id) VALUES('{$post_image}', '$file_name', 1,'image', '{$imageFileExtension}', '".USER_ID."', '".SITE_ID."')");
                            $mediaId=mysqli_insert_id($connection);
                            
                            $post_image_url=strtolower(str_ireplace($uploadPath, '/attachment/', str_replace('.'.$imageFileExtension, '', $new_image_path))).'/';
                            $insertMediaUrl=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('{$post_image_url}', '{$mediaId}', 200, 'media', '".USER_ID."', '".SITE_ID."', '{$post_date}', '{$post_modified}', '{$post_modified}')");

                        }

                        /** Fetching image form content and download on server */

                        $doc = new DOMDocument();
                        @$doc->loadHTML($post_content);

                        $old_post_content_images=array();
                        $new_post_content_images=array();

                        $imageList = $doc->getElementsByTagName('img');

                        foreach ($imageList as $images) {
                            
                            $old_post_content_images[]=str_replace('\"', '', $images->getAttribute('src'));

                            /** Download images from server */

                            $old_post_content_image_path=str_replace('\"', '', $images->getAttribute('src'));

                            $old_post_content_image=explode('/', $old_post_content_image_path);

                            /** Extension of images */

                            $old_post_content_image=end($old_post_content_image);

                            $post_content_image_name=str_replace('-', '_', str_replace(' ', '_', strstr($old_post_content_image, '.', TRUE))); // image name
                            $post_content_image_ext=strtolower(strstr($old_post_content_image, '.')); // image extension

                            $new_post_images=strtolower($uploadPath.time().'_'.(($post_content_image_name=='') ? 'image.' : (($post_content_image_name=='') ? 'image.' : $post_content_image_name ) ).$post_content_image_ext);

                            /** Transfer inside content images */

                            try {

                                if(file_put_contents($new_post_images, file_get_contents(str_replace('%2F', '/', str_replace('%3A', ':', urlencode($old_post_content_image_path)))))) {
                                
                                    $post_image=strtolower(str_ireplace(ROOT, '', $new_post_images));
                                    $insertMedia=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."media(media_url, media_title, media_status, media_type, media_ext, media_owner_id, media_site_id) VALUES('{$post_image}', '$post_content_image_name', 1,'image', '".str_replace('.', '', $post_content_image_ext)."', '".USER_ID."', '".SITE_ID."')");
                                    $mediaId=mysqli_insert_id($connection);
                                    
                                    $post_image_url=strtolower(str_ireplace($uploadPath, '/attachment/', str_replace($post_content_image_ext, '', $new_post_images))).'/';
                                    $insertMediaUrl=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('{$post_image_url}', '{$mediaId}', 200, 'media', '".USER_ID."', '".SITE_ID."', '{$post_date}', '{$post_modified}', '{$post_modified}')");
                                    
                                    $new_post_content_images[]=$post_image;

                                    $post_content=str_replace('\"', '"', str_replace($old_post_content_image, $post_image, $post_content));
        
                                } else {
    
                                    $new_post_content_images[]='';
    
                                }

                            } catch(Exception $e) {

                                /** Creating log for image not transfer */

                            }

                        }

                        $insertPost=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."post(post_image, post_title, post_content, post_category, post_owner_id, post_status, post_type) VALUES('{$post_image}', '{$post_title}', '{$post_content}', '{$categoryID}', '".USER_ID."', 1, 'post')");
                        $postID=mysqli_insert_id($connection);

                        $linkData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('{$link_url}', '{$postID}', '200', 'post', '".USER_ID."', '".SITE_ID."', '{$post_date}', '{$post_modified}', '{$post_modified}')");
                        $metaData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_title, meta_type, meta_description, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$postID}', '{$meta_title}', 'post', '{$meta_desc}', '{$post_image}', '{$meta_title}', '{$meta_desc}', '{$post_image}', '{$meta_title}', '{$meta_desc}')");

                        $updateCategoryData=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."category SET category_total_count=category_total_count+1 WHERE category_id='{$categoryID}'");
                        
                        /** Add Tags */

                        $tags=explode(',', $tags);

                        for($i=0; $i<sizeof($tags); $i++) {
                            $tagData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id) VALUES('{$tags[$i]}', '{$postID}', '".USER_ID."', '".SITE_ID."')");
                        }

                        if($insertMedia && $insertMediaUrl && $insertPost && $linkData && $metaData && $updateCategoryData && $tagData ) {

                            $status=200;
                            $msg="data import successfully";
                            
                            mysqli_query($connection, "COMMIT");
                            $success++;

                        } else {

                            $dublicate++;
                            mysqli_query($connection, "ROLLBACK");

                        }

                    }
                    
                    $i++;

                }

                fclose($openFile);

            } else {

                $msg='Please upload only valid csv file format';

            }

        } else {
            $msg='File is not uploaded on server';
        }

        $result = array(
            'status' => $status,
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while inserting data' )
        );
        
        echo json_encode($result, TRUE);

    }

    function url($connection, $data) {

        extract($data);

        $urlHeader=get_headers($url, 1);

        $headertype=$urlHeader['Content-Type']; // Check URL is JSON or XML

        if(isset($headertype)) {

            if(strpos($headertype, 'json')) {

                $headertype='json';

            } else if(strpos($headertype, 'xml')) {

                $headertype='xml';

            } else {

                echo 'please use only json and xml links';

            }

            /** Check header type import file is available on modules */

            $importFileType=MODULES.'import-'.$headertype.'.php';

            if(file_exists($importFileType)) {

                /** URL content store into fileData variable */

                $fileData=file_get_contents($url);

                /** Fetch category URL */

                $category=explode('_', $category);
                $categoryID=reset($category);
                $categoryUrl=end($category);

                require_once $importFileType;

            } else {

                $msg='Please use only JSON and XML type links';

            }

        } else {

            $msg='Invalid URL';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 206 ),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while inserting data' )
        );

        //echo json_encode($result, TRUE);

    }