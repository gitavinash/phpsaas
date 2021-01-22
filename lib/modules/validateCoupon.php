<?php

    if(isset($coupon)) {

        $cartPrice=((isset($cartPrice)) ? (float)$cartPrice: (float)$totalPrice );

        if($checkCoupon=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."coupon WHERE coupon_code='{$coupon}' AND coupon_site_id=".APP_SITE_ID)) {

            if(mysqli_num_rows($checkCoupon)!=0) {

                extract(mysqli_fetch_assoc($checkCoupon));

                if($coupon_status==1) {

                    if(date('Y-m-d') <= date('Y-m-d', strtotime($coupon_expire))) {

                        // Check coupon limit exceed or not

                        if($coupon_used<=$coupon_limit) {

                            // Check payment mode validation

                            if($coupon_payment_mode!='all') {

                                if($coupon_payment_mode==$paymentMode) {

                                    $paymentModeValid=TRUE;

                                } else {

                                    $paymentModeValid=FALSE;
                                    $msg="This coupon is only valid for {$coupon_payment_mode} method";

                                }

                            } else {

                                $paymentModeValid=TRUE;

                            }

                            // Check minimum cart value

                            if($paymentMode) {

                                if($coupon_min_value!=0) {

                                    if($coupon_min_value<$cartPrice) {

                                        $minCartValid=TRUE;

                                    } else {

                                        $minCartValid=FALSE;
                                        $msg="The purchase amount should be {$coupon_min_value} or more";

                                    }

                                } else {

                                    $minCartValid=TRUE;

                                }

                            }

                            // Check coupon maximum discount value

                            if($minCartValid) {

                                if($coupon_discount_type=='%') {

                                    $discountValue=($coupon_discount / 100) * $cartPrice;
    
                                    if($coupon_max_discount!=0) {

                                        if($coupon_max_discount<$discountValue) {
    
                                            $discountValue=$coupon_max_discount;
        
                                        }

                                    }

                                } else {
    
                                    $discountValue=$coupon_discount;
    
                                    if($coupon_max_discount!=0) {

                                        if($coupon_max_discount<$discountValue) {
    
                                            $discountValue=$coupon_max_discount;
        
                                        }

                                    }
    
                                }

                                $status=200;
                                $msg="You get a discount of ₹ {$discountValue}";
                                $couponCodeIsValid=TRUE;

                            }

                        } else {

                            $msg='Coupon code limit exceed';

                        }

                    } else {

                        $msg='Coupon code is expire';

                    }

                } else {

                    $msg='Coupon code is not active';

                }

            } else {

                $msg='Invalid coupon code';

            }

        }

    } else {

        $msg='Please enter coupon code';

    }