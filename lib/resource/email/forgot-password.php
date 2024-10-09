<?php

include_once 'header.php';

$subject = 'Forgot Password';

// $email_body = "

// 		<table style='font-weight: normal; border-collapse: collapse; border: 0; margin: 0; padding: 0; font-family: Arial, sans-serif; width: 100%;'>
//     <tr>
//         <td style='text-align: center; padding: 20px 0;'>
//             <h1 style='color: {$themeColor}; margin: 0;'>{$subject}</h1>
//             <p style='text-align: justify; font-size: 14px; line-height: 26px; margin: 10px 0;'></p>
//         </td>
//     </tr>
//     <tr>
//         <td style='text-align: center; padding: 10px 0;'>
//             <h3 style='margin: 10px 0;'>Looks like you lost your password?</h3>
//             <p style='margin: 0;'>We are here to help. Click on the button below to change your password.</p>
//         </td>
//     </tr>
//     <tr>
//         <td style='text-align: center; padding: 20px 0;'>
//             <a href='{$passwordResetURL}' target='_blank' style='display: inline-block; color: #ffffff; background-color: {$themeColor}; padding: 10px 20px; border-radius: 3px; text-decoration: none; font-size: 16px; border: 2px solid {$themeColor};'>
//                 Reset Your Password
//             </a>
//         </td>
//     </tr>
//     <tr>
//         <td style='text-align: center; padding: 20px 0;'>
//             <p style='font-size: 14px; margin: 0;'>If you did not ask to recover your password, please ignore this email.</p>
//         </td>
//     </tr>
// </table>

// 	";

$email_body = "
<table style='font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif; width: 100%' >
    <tr>
        <td style='text-align: center'>
            <h1 style='color: {$themeColor}'>{$subject}</h1>
            <p style='float:left; width: 100%; text-align: justify; font-size: 14px; line-height: 26px'></p>
        </td>
    </tr>
    <tr>
        <td style='text-align: center'>
            <h3>Looks like you lost your password?</h3>
            <p>We are here to help. Click on the button below to change your password.</p>
        </td>
    </tr>
    <tr>
        <td style='float:left; width: 100%; text-align: center; margin-top: 20px; margin-bottom: 20px'>
            <a href='{$passwordResetURL}' style='color:#ffffff; background-color: {$themeColor}; border: 10px solid {$themeColor}; border-radius: 3px; text-decoration:none; display: inline-block; padding: 10px 20px;'>Reset Your Password</a>
        </td>
    </tr>
    <tr>
        <td style='text-align: center'>
            <h3>Having trouble clicking the Reset Your Password button?</h3>
            <p>Simply copy this URL: {$passwordResetURL}</p>
        </td>
    </tr>
    <tr>
        <td style='text-align: center'>
            <p>If you did not ask to recover your password, please ignore this email.</p>
        </td>
    </tr>
</table>
";


include_once 'footer.php';
