<?php

    require_once LOGIN_CHECK;

    function update($connection, $data) {

        extract($data);

        $coupon_id = base64_decode($coupon_id);
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
   
                                /** Check coupon maximum discount is 100% */
                                
                                if($discountType=='%') {

                                    if($discount<=99) {

                                        $addNewCoupon=TRUE;

                                    } else {

                                        $addNewCoupon=FALSE;
                                        $msg='Maximum 99% coupon discount is allowed';

                                    }

                                } else {

                                    $addNewCoupon=TRUE;

                                }

                                if($addNewCoupon) {

                                    // Check coupon code is unique or not
    
                                    if($checkCouponCode=mysqli_query($connection, "SELECT coupon_id FROM ".TABLE_PREFIX."coupon WHERE coupon_code='{$code}' AND coupon_site_id=".APP_SITE_ID)) {
        
                                        if(mysqli_num_rows($checkCouponCode)==1) {
        
                                            if(mysqli_query($connection, "UPDATE ".TABLE_PREFIX."coupon SET coupon_title='{$title}', coupon_description='{$description}', coupon_code='{$code}', coupon_discount='{$discount}', coupon_discount_type='{$discountType}', coupon_payment_mode='{$paymentMode}', coupon_min_value='{$minCartAmount}', coupon_max_discount='{$maxDiscount}', coupon_limit='{$limit}', coupon_expire='{$couponExpire}'")) {
        
                                                $status=200;
                                                $msg='Coupon code update successfully';
                                                $url='/dashboard/coupon/?type=edit&id='.base64_encode($coupon_id);
        
                                            } else {
        
                                                $msg='Something went wrong while trying to generate new coupon';
        
                                            }
        
                                        } else {
        
                                            $msg='Please enter a unique code. This code is already published';
        
                                        }
        
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