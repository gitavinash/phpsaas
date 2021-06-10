<?php

    // function enquiry($connection, $data) {

        // Detect prefix enquiry table fields and remove from array

        if(isset($name)) {

            // Remove name varible from array

            unset($_REQUEST['name']);

        }

        if(isset($first_name)) {

            // Remove first name varible from array

            unset($_REQUEST['first_name']);

        }

        if(isset($last_name)) {

            // Remove last name varible from array

            unset($_REQUEST['last_name']);

        }

        if(isset($email)) {

            // Remove email varible from array

            unset($_REQUEST['email']);

        }

        if(isset($mobile)) {

            // Remove mobile varible from array

            unset($_REQUEST['mobile']);

        }

        if(isset($message)) {

            // Remove message varible from array

            unset($_REQUEST['message']);

        }

        if(isset($message)) {

            // Remove enquiry source varible from array

            unset($_REQUEST['source']);

        }

        if(isset($message)) {

            // Remove enquiry medium varible from array

            unset($_REQUEST['medium']);

        }

        if(isset($message)) {

            // Remove enquiry campaign varible from array

            unset($_REQUEST['campaign']);

        }

        // Now assing a action value as a enquiry type 

        if($_SERVER['REQUEST_METHOD']=='POST') {

            $includeFile = "I am POST methood";

        } else {

            $includeFile = "I am GET method";

        }

        echo $includeFile;

    // }