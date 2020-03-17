<?php

    function password($connection, $data) {

        global $table_prefix;
        extract($data);

        if($password!=$cnf_password) {
            header("Location: /dashboard/setting/?type=password&msg=Password does not matched&error=error");
            exit();
        }

        if($checkUser=mysqli_query($connection, "SELECT user_salt_key FROM ".TABLE_PREFIX."user WHERE user_id='{$website_user_id}' AND user_site_id='{$website_id}'")) {

            if(mysqli_num_rows($checkUser)!=0) {

                extract(mysqli_fetch_assoc($checkUser));

                /** Encrypt passwor entered by user */
                $newPassword=hash('sha256', $user_salt_key.$password);

                /** Update user password */

                if(mysqli_query($connection, "UPDATE ".TABLE_PREFIX."user SET user_password='{$newPassword}' WHERE user_id='{$website_user_id}' AND user_site_id='{$website_id}'")) {

                    header("Location: /dashboard/setting/?type=password&msg=Password update successfully&error=success");

                }

            } else {

                header("Location: /dashboard/setting/?type=password&msg=User Not Found&error=error");

            }

        }

        exit();

    }

    function profile($connection, $data) {

        global $table_prefix;
        extract($data);

        $status=500;
        $msg='';

        /** Start Transaction */

        if($checkUsername=mysqli_query($connection, "SELECT user_username FROM ".TABLE_PREFIX."user WHERE user_username='{$username}'")) {
            
            if(mysqli_num_rows($checkUsername)!=0) {

                $status=404;
                $msg='Username is not available';

            }
        }

        /** Update username */

        $updateUsername=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."user SET user_username='{$username}' WHERE user_id='{$website_user_id}'");

        $updateProfile=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."profile SET profile_first_name='{$first_name}', profile_last_name='{$last_name}', profile_display_name='{$display_name}', profile_mobile='{$mobile}', profile_alt_mobile='{$alt_mobile}', profile_alt_email='{$alt_email}' WHERE profile_owner_id='{$website_user_id}'");

        if($updateUsername && $updateProfile) {

            $status=200;
            $msg='Profile updated successfully';

            setcookie('user_name', $display_name, time()+30*24*60*60, '/');

            /** Run all query */

            mysqli_query($connection, 'COMMIT');

        } else {

            /** Rollback all query */

            mysqli_query($connection, 'ROLLBACK');

        }

        $result=array(
            'status' => $status,
            'msg' => $msg
        );

        echo json_encode($result, TRUE);
        


    }

    function profilePicture($connection, $data) {

        global $table_prefix;
        extract($data);

        if($_FILES['featureImage']['error']==0) {

            $validFileExtension = array( 'png', 'gif', 'jpg', 'jpeg' );
            $validFileType = array('image/png', 'image/gif', 'image/jpeg');
            
            $filename=strtolower(str_replace("'", "_", str_replace('-', '_', str_replace(' ', '_', $_FILES['featureImage']['name']))));
			$filetype=$_FILES['featureImage']['type'];
			$extension=explode('.', $filename);
            $fileExtension=end($extension);
            
            if(in_array($fileExtension, $validFileExtension)) {
                if(in_array($filetype, $validFileType)) {

                    $uploadPath=ROOT.'/upload/'.date('Y/m/');

					if(!is_dir($uploadPath)) {
						mkdir($uploadPath, 0755, true);
                    }

                    $uploadPath=$uploadPath.time().'_'.str_replace(' ', '_', strtolower($filename));
					$uploadFile=str_replace('-', '_',str_replace('.', ' ',str_replace('_', ' ', str_replace('.'.$fileExtension, '', $filename))));
					$uploadURL=str_replace(ROOT, '', $uploadPath);
                    $link=str_replace(' ', '_', '/attachment/'.time().'_'.strtolower($uploadFile).'/');

                    if(move_uploaded_file($_FILES['featureImage']['tmp_name'], $uploadPath)) {

                        /** Insert into Database */

                        mysqli_query($connection, 'START TRANSACTION');

                        /** Insert into media table */

                        $insertMedia=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."media(media_url, media_title, media_type, media_ext, media_owner_id, media_status, media_site_id) VALUES('{$uploadURL}', '{$uploadFile}', '{$filetype}', '{$fileExtension}', '{$website_user_id}', 1, '{$website_id}')");
                        $mediaID=mysqli_insert_id($connection);

                        $createLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('{$link}', '{$mediaID}', 200, 'media', '{$website_user_id}', '{$website_id}', NOW(), NOW(), NOW())");

                        $updateProfile=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."profile SET profile_image='{$uploadURL}' WHERE profile_owner_id='{$website_user_id}'");

                        if($insertMedia && $createLink && $updateProfile) {

                            mysqli_query($connection, 'COMMIT');
                            header("Location: /dashboard/setting/?type=profile&msg=Profile photo update successfully&error=success");

                        } else {
                            
                            mysqli_query($connection, 'ROLLBACK');
                            header("Location: /dashboard/setting/?type=profile&msg=Error while uploading photo&error=error");
                            
                        }

                    } else {

                        header("Location: /dashboard/setting/?type=profile&msg=Photo is not uploading&error=error");

                    }

                }
            }

        } else {

            header("Location: /dashboard/setting/?type=profile");

        }

        exit();

    }

    function website($connection, $data) {

        global $table_prefix;
        extract($data);

        if($checkDomain=mysqli_query($connection, "SELECT site_id FROM ".TABLE_PREFIX."site WHERE site_id='{$website_id}' AND site_domain='".SITE_DOMAIN."'")) {

            if(mysqli_num_rows($checkDomain)!=0) {

                if($updateWebsite=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site SET site_name='{$name}', site_description='{$description}', site_keyword='{$keyword}', site_lang='{$site_lang}' WHERE site_id='{$website_id}'")) {

                    $status=200;
                    $msg='Website update successfully';

                    header("Location: /dashboard/setting/?type=website&msg=Website update successfully");

                }

            }

        }

        $result=array(
            'status' => ((isset($status)) ? $status : 206 ),
            'msg' => ((isset($msg)) ? $msg : 'Unable to update website information' )
        );

        echo json_encode($result, TRUE);
        exit();

    }