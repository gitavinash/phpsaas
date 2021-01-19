<?php

    require_once LOGIN_CHECK;

    function publish($connection, $data) {

        extract($data);

        $discount=(($discount!='') ? $discount : 0 );
        $maxDiscount=(($maxDiscount!='') ? $maxDiscount : 0 );
        $minCartAmount=(($minCartAmount!='') ? $minCartAmount : 0 );
        $limit=(($limit!='') ? $limit : 0 );

        if($title!='') {

            if($code!='') {

                if($discount>=0 && is_numeric($discount)) {

                    if($maxDiscount>=0 && is_numeric($maxDiscount)) {


                        if($minCartAmount>=0 && is_numeric($minCartAmount)) {

    
                            if($limit>=0 && is_numeric($limit)) {
    
                                $expiryDate=str_replace('/', '-', $expiryDate);
                                $couponExpire=date("Y-m-d", strtotime($expiryDate));
    
                                // Check coupon code is unique or not
    
                                if($checkCouponCode=mysqli_query($connection, "SELECT coupon_id FROM ".TABLE_PREFIX."coupon WHERE coupon_code='{$code}' AND coupon_site_id=".APP_SITE_ID)) {
    
                                    if(mysqli_num_rows($checkCouponCode)==0) {
    
                                        if(mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."coupon(coupon_site_id, coupon_owner_id, coupon_title, coupon_description, coupon_code, coupon_discount, coupon_discount_type, coupon_payment_mode, coupon_min_value, coupon_max_discount, coupon_limit, coupon_used, coupon_status, coupon_expire) VALUES('".APP_SITE_ID."', '".USER_ID."', '{$title}', '{$description}', '{$code}', '{$discount}', '{$discountType}', '{$paymentMode}', '{$minCartAmount}', '{$maxDiscount}', '{$limit}', 0, 1, '{$couponExpire}')")) {
    
                                            $status=200;
                                            $msg='Coupon code generate successfully';
                                            $url='/dashboard/coupon/?type=edit&id='.base64_encode(mysqli_insert_id($connection));
    
                                        } else {
    
                                            $msg='Something went wrong while trying to generate new coupon';
    
                                        }
    
                                    } else {
    
                                        $msg='Please enter a unique code. This code is already published';
    
                                    }
    
                                }
    
                            } else {
            
                                $msg='Limit cannot be a negative value';
            
                            }
    
                        } else {
        
                            $msg='Minimum cart cannot be a negative amount';
        
                        }
    
                    } else {
    
                        $msg='Maximum discount cannot be a negative amount';
    
                    }

                } else {

                    $msg='Discount php cannot be a negative value';

                }

            } else {

                $msg='Please enter a coupon code';

            }

        } else {

            $msg='Please enter a title';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'url' => ((isset($url)) ? $url : '' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );
    
        echo json_encode($result, TRUE);

    }