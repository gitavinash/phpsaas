<?php

    unset($_REQUEST['type']);
    unset($_REQUEST['action']);

    // Define action parameter as form type

    $type=$action;

    // Detect prefix enquiry table fields and remove from array

    if(isset($name)) {

        // Remove name varible from array

        unset($_REQUEST['name']);

    } else {

        if(isset($first_name)) {

            // Remove first name varible from array

            unset($_REQUEST['first_name']);

        }

        if(isset($last_name)) {

            // Remove last name varible from array

            unset($_REQUEST['last_name']);

        }

        $name = $first_name.' '.$last_name;

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

    if(isset($source)) {

        // Remove enquiry source varible from array

        unset($_REQUEST['source']);

    } else {

        $source='';

    }

    if(isset($medium)) {

        // Remove enquiry medium varible from array

        unset($_REQUEST['medium']);

    } else {

        $medium='';

    }

    if(isset($campaign)) {

        // Remove enquiry campaign varible from array

        unset($_REQUEST['campaign']);

    } else {

        $campaign='';

    }

    if(isset($sender)) {

        // Remove enquiry campaign varible from array

        unset($_REQUEST['sender']);
        
        // Admin email id or where email will be send
    
        $toAddress = base64_decode($sender);

    } else {

        echo $msg='Sender email is not found';
        exit();

    }

    if(isset($cc)) {

        // Remove enquiry campaign varible from array

        unset($_REQUEST['cc']);
        
        // Admin email id or where email will be send
    
        $ccAddress = base64_decode($cc);        

    }

    if(isset($bcc)) {
        
        // Remove enquiry campaign varible from array

        unset($_REQUEST['bcc']);
        
        // Admin email id or where email will be send
    
        $bccAddress = base64_decode($bcc);        

    }
    
    if(sizeof($_REQUEST)!=0) {

        foreach($_REQUEST as $key => $value) {

            $enquiryMeta[$key] = $value;

        }

    }

    // Start record enquiry data into table

    // mysqli_query($connection, "START TRANSACTION");

    // $addNewEnquiry=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."enquiry() VALEUS()");
    // $enquiryID=mysqli_insert_id($addNewEnquiry);

    // if(isset($enquiryMeta)) {

                

    // } else {

    //     $addEnquiryMeta=TRUE;

    // }

    $email_body="
    <h2>{$subject}</h2>
    <table>
        <tr>
            <td>Name: </td>
            <td>{$name}</td>
        </tr>
        <tr>
            <td>Email: </td>
            <td>{$email}</td>
        </tr>
        <tr>
            <td>Mobile: </td>
            <td>{$mobile}</td>
        </tr>
        <tr>
            <td>Message: </td>
            <td>{$message}</td>
        </tr>
    </table>
    ";

    require SEND_EMAIL;

    header("Location: ".$redirect_url, TRUE, 301);
    exit();