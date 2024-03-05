<?php

$messageForm = '';

foreach ($_REQUEST as $key => $value) {
    $messageForm .= "<tr><td>{$key}</td><td>$value</td></tr>";
}

$sender=base64_encode("avinashsharma114@gmail.com");

$email_body = "
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
        {$messageForm}
    </table>
    ";
    
    if(isset($sender)) {
        if(!isset($doNotSendEmail)) {
            require SEND_EMAIL;
        }
    }

exit();