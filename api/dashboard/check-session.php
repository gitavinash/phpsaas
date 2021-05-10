<?php

    if($_SERVER['REQUEST_METHOD']=="POST") {

        /** Accept header request */
			
		foreach(getallheaders() as $name => $value) {
				
            ${'header_'.$name} = $value;
		
        }

        if(isset($header_user_token)) {

            if($checkUserIsReal=mysqli_query($connection, "SELECT user_id, user_email, user_salt_key, user_status, site_domain, site_url, site_name, site_icon, site_status, site_app_id, site_access_site_id, site_access_type FROM ".TABLE_PREFIX."site_access INNER JOIN ".TABLE_PREFIX."user ON site_access_user_id=user_id INNER JOIN ".TABLE_PREFIX."site ON site_access_site_id=site_id WHERE user_salt_key='".base64_decode($header_user_token)."' AND site_access_site_id=".SITE_ID)) {
			
                if(mysqli_num_rows($checkUserIsReal)!=0) {
    
                    extract(mysqli_fetch_assoc($checkUserIsReal));
    
                    $encryptEmail=base64_encode($user_email);
    
                    if($user_status!=1) {
                        
                        $status=204;
                        $msg='Account is not verified';
    
                    } else {
    
                        /** Define new constant variable */
                    
                        define('USER_ID', $user_id);
    
                        /** Define new cookie for logged in user */
    
                        if(!isset($_COOKIE['site_app'])) {
    
                            setcookie('site_app', base64_encode($site_app), time()+30*24*60*60, '/', SITE_DOMAIN);
                            $status=200;
                            $msg='valid user';
    
                        } else {
    
                            /** Check website status */
    
                            if($site_status!=1) {
    
                                if($site_access_type=='administrator') {
    
                                    /** if user type is Administrator Redirect to website setup page */
                                        
                                    $status=200;
                                    $msg='Complete website setup to continue';
    
                                } else {
    
                                    /** Show Access Forbidden page to the use */
    
                                    $status=403;
                                    $msg='Unauthoried access';
    
                                }
                            
                            } else {
    
                                $msg='Dounbtfull area';
    
                            }
    
                            /** Redirect to complete profile page */
    
                            if($site_name=='' && $site_icon=='' && ACTIVE_URL!='/dashboard/setting/?type=configuration') {
    
                                $status=204;
                                $msg='Profile incomplete';
                                $redirect='/dashboard/setting/?type=configuration';
    
                            }
    
    
                            /** Set login is valid */
    
                            setcookie('isLogin', 1, time()+30*24*60*60, '/', MAIN_DOMAIN);

                        }
    
                    }
    
                } else {
    
                    $status=404;
                    $msg='No user found';
                    
                }
    
            } else {
    
                $status=502;
                $msg='something is wrong while checking user session';
    
            }

        } else {

            $status=404;
            $msg='No token found';

        }

    } else {

        $msg='GET method is not support with API';

    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error' ),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
        'domain_list' => ((isset($checkDomain['domains'])) ? $checkDomain['domains'] : '' )
    );

    echo json_encode($result, TRUE);