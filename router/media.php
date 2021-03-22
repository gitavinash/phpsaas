<?php

    /** Check link status */

    switch ($link_status) {

        case 301 :  require_once ROUTER.'301.php';
                    break;

        default :   
            
                    /** Fetch data from database table  */

                    if($postData=mysqli_query($connection, "SELECT media_id, media_url, media_title, profile_display_name, user_username, media_type FROM ".TABLE_PREFIX."media INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=media_owner_id INNER JOIN ".TABLE_PREFIX."user ON media_owner_id=user_id WHERE media_id='{$link_relation_id}'")) {

                        if(mysqli_num_rows($postData)!=0) {

                            extract(mysqli_fetch_assoc($postData));

                            if(file_exists(ROUTER.$link_status.'.php')) {

                                /** Include status router file */

                                require_once ROUTER.$link_status.'.php';

                            } else {

                                /** Check category template is avilable or not */

                                $checkCategoryTemplate=USER_PATH.strtolower($link_type).'-media.php';

                                if(file_exists($checkCategoryTemplate)) {

                                    /** Opening category post template */

                                    require_once $checkCategoryTemplate;

                                } else {

                                    /** Display content in default template */

                                    require_once USER_PATH.'media.php';

                                }

                            }

                        } else {

                            echo 'Error';

                        }

                    } else {

                        echo mysqli_error($connection);

                    }
                    break;

    }