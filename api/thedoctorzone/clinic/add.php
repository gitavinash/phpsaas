<?php

    require_once LOGIN_CHECK;

    function add($connection, $data) {

        extract($data);

        /** Validate clinic/hospotal field value */

        if($name!='') {

            /** Validate mobile field value */

            if($mobile!='') {

                /** Validate house number field value */

                if($house_number!='') {

                    /** Validate area field value */

                    if($area!='') {

                        /** Validate landmark field value */

                        if($landmark!='') {

                            /** Validate country field value */

                            if($country!='') {

                                $country=explode('_', $country);
                                $country=$country[0];

                                if($state!='') {

                                    /** Validate state field value */

                                    $state=explode('_', $state);
                                    $state=$state[0];

                                    if($city!='') {

                                        /** Validate city field value */

                                        $city=explode('_', $city);
                                        $city=$city[0];

                                        if($pincode!='') {

                                            /** Validate pin code field value */

                                            $pincode=explode('_', $pincode);
                                            $updatePinCode=" address_pin_code={$pincode[0]}";

                                            if($building_type!='') {

                                                /** Start process to add data into Database */

                                                mysqli_query($connection, "START TRANSACTION");

                                                $addAddress=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."address(address_site_id, address_user_id, address_house_no, address_area, address_landmark, address_city_id, address_state_id, address_country_id, address_pin_code, address_mobile, address_type, address_status) VALUES(".SITE_ID.", ".USER_ID.", '{$house_number}', '{$area}', '{$landmark}', '{$city}', '{$state}', '{$country}', '{$pincode}', '{$mobile}', '{$building_type}', '1')");
                                                $addressID=mysqli_insert_id($connection);

                                                $addCompany=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."company(company_site_id, company_user_id, company_address_id, company_title, company_type, company_status) VALUES(".SITE_ID.", ".USER_ID.", {$addressID}, '{$name}', '{$building_type}', 1)");

                                                if($addAddress && $addCompany) {

                                                    $status='success';
                                                    $msg='New Building add successully';
                                                    mysqli_query($connection, "COMMIT");

                                                } else {

                                                    $msg='Something is wrong while adding new building';
                                                    mysqli_query($connection, "ROLLBACK");

                                                }

                                                /** End process to add data into database */

                                            } else {

                                                $msg='Please select building type';

                                            }


                                        } else {
                                
                                            $msg='Please enter pin code';
                                
                                        }

                                    } else {
                            
                                        $msg='Please select city';
                            
                                    }

                                } else {
                        
                                    $msg='Please select state';
                        
                                }

                            } else {

                                $msg='Please select country';

                            }                            

                        } else {

                            $msg='Please enter near by landmark of clinic or hospital';

                        }

                    } else {

                        $msg='Please enter area of the clinic or hospital';

                    }

                } else {

                    $msg='Please enter flat or house number';

                }
                

            } else {

                $msg='Please enter mobile number';

            }
        
        } else  {

            $msg='Please enter clinic/hospital name';

        }

        $result=array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while adding new building' )
        );
    
        echo json_encode($result, TRUE);

    }