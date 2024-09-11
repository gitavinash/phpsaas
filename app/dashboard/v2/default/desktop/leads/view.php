<?php

$leadId = base64_decode($id);

$accessFilter = '';
if ($site_access_type == 'agent') {
    $accessFilter = " AND enquiry_agent='" . USER_ID . "'";
}

$enquiryQuery = "SELECT * FROM " . TABLE_PREFIX . "enquiry LEFT JOIN " . TABLE_PREFIX . "enquiry_profile ON enquiry_profile_ref_id=enquiry_id WHERE enquiry_id='{$leadId}' {$accessFilter} AND enquiry_site_id=" . SITE_ID . " ORDER BY status DESC LIMIT 0, 1";

if ($checkEnquiry = mysqli_query($connection, $enquiryQuery)) {
    if (mysqli_num_rows($checkEnquiry) != 0) {
        extract(mysqli_fetch_assoc($checkEnquiry));
        extract(json_decode($enquiry_message, true));
    } else {
        echo "Invalid request";
        exit();
    }
} else {
    echo "Something went wrong";
    exit();
}

?>
<h2 class="heading">Lead details<span>Complete information of lead</span></h2>
<div class="box-1">

    <div class="widget box-7-5 no-padding">
        <div class="main-box">
            <section>
                <div class="widget no-padding box-1">
                    <div class="leadProfileCard">
                        <div class="img">
                            <img src="<?php echo IMG_LIBRARY ?>profile_icon/<?php echo strtolower(((trim($enquiry_first_name[0]) != '') ? $enquiry_first_name[0] : 'unknown')) ?>.png"
                                alt="" srcset="">
                        </div>
                        <div class="info">
                            <h2 class="heading"
                                <?php echo (($enquiry_status == 4 || $enquiry_status == 5) ? "style='color:#f44336'" : "") ?>>
                                <?php echo $customerFullName = ucwords($enquiry_first_name . ' ' . $enquiry_last_name . (($enquiry_status == 4) ? ' ( Closed ) ' : (($enquiry_status == 5) ? '( Duplicate )' : ''))) ?>
                            </h2>
                            <p>
                                <?php

if (isset($date_of_birth)) {
    echo "<span><strong>Date of Birth:</strong> " . date('d M Y', strtotime($date_of_birth)) . "</span>";
}

?>
                                <span><strong>Last Update:
                                    </strong><?php echo date('d-m-Y h:i A', strtotime($enquiry_update)) ?></span>
                            </p>
                        </div>
                        <div class="verifications">

                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="main-box">
            <section>
                <div class="widget no-padding box-1">
                    <div class="btn-group leadActions">
                        <a id="leadDetailsBtn" class="btn detailsBtn btn-green"><i class="fi-rr-file-circle-info"></i>
                            Details</a>
                        <a id="noteDetailsBtn" class="btn detailsBtn"><i class="fi-rr-time-past"></i> Follow up
                            history</a>
                        <?php
if (SITE_APP == 'astro_client') {
    echo "<a id='upayDetailsBtn' class='btn detailsBtn'><i class='fi-rr-story-book'></i> Upay
                            history</a>";
}
?>
                        <a id="paymentDetailsBtn" class="btn detailsBtn"><i class="fi-rr-money-check"></i> Payment
                            history</a>
                        <?php

if ($enquiry_status != 5) {?>
                        <a id="leadEditBtn" class="btn right-btn highlight" style="margin-top:20px" ><i class="fi-rr-edit"></i> Edit
                            lead</a>
                        <?php
}
?>
                    </div>
                </div>
                <div class="widget openBtnBox no-padding box-1" id="leadDetailsBox">
                    <div class="leadInfo">
                        <table>
                            <?php

if ($enquiry_first_name != '') {
    echo "<tr>
                                <td class='box-3'><strong>First name</strong></td>
                                <td class='box-7'>{$enquiry_first_name}</td>
                            </tr>";
}

if ($enquiry_middle_name != '') {
    echo "<tr>
                                <td class='box-3'><strong>Middle name</strong></td>
                                <td class='box-7'>{$enquiry_middle_name}</td>
                            </tr>";
}

if ($enquiry_last_name != '') {
    echo "<tr>
                                <td class='box-3'><strong>Last name</strong></td>
                                <td class='box-7'>{$enquiry_last_name}</td>
                            </tr>";
}

if ($enquiry_email != '') {
    echo "<tr>
                                <td class='box-3'><strong>Email</strong></td>
                                <td class='box-7'>{$enquiry_email}</td>
                            </tr>";
}

if ($enquiry_mobile != '') {
    echo "<tr>
                                <td class='box-3'><strong>Mobile</strong></td>
                                <td class='box-7'>{$enquiry_mobile}</td>
                            </tr>";
}

if ($enquiry_alt_mobile != '') {
    echo "<tr>
                                <td class='box-3'><strong>Altenate Mobile</strong></td>
                                <td class='box-7'>{$enquiry_alt_mobile}</td>
                            </tr>";
}

if ($enquiry_subject != '') {
    echo "<tr>
                                <td class='box-3'><strong>" . ((SITE_APP == 'aleay') ? 'Tour package' : 'Subject') . "</strong></td>
                                <td class='box-7'>{$enquiry_subject}</td>
                            </tr>";
}

if ($date_of_travel != '1970-01-01') {
    echo "<tr>
                                <td class='box-3'><strong>Date of travel</strong></td>
                                <td class='box-7'>{$date_of_travel}</td>
                            </tr>";
}

if ($number_of_days != 0) {
    echo "<tr>
                                <td class='box-3'><strong>Number of days</strong></td>
                                <td class='box-7'>{$number_of_days} Nights</td>
                            </tr>";
}

if ($number_of_adults != 0) {
    echo "<tr>
                                <td class='box-3'><strong>Number of adults</strong></td>
                                <td class='box-7'>{$number_of_adults}</td>
                            </tr>";
}

if ($number_of_childrens != 0) {
    echo "<tr>
                                <td class='box-3'><strong>Number of childrens</strong></td>
                                <td class='box-7'>{$number_of_childrens}</td>
                            </tr>";
}

if ($number_of_infants != 0) {
    echo "<tr>
                                <td class='box-3'><strong>Number of infants</strong></td>
                                <td class='box-7'>{$number_of_infants}</td>
                            </tr>";
}

if ($current_location != '') {
    echo "<tr>
                                <td class='box-3'><strong>Current location</strong></td>
                                <td class='box-7'>{$current_location}</td>
                            </tr>";
}

if ($pickup_location != '') {
    echo "<tr>
                                <td class='box-3'><strong>Pickup location</strong></td>
                                <td class='box-7'>{$pickup_location}</td>
                            </tr>";
}

if ($drop_location != '') {
    echo "<tr>
                                <td class='box-3'><strong>Drop location</strong></td>
                                <td class='box-7'>{$drop_location}</td>
                            </tr>";
}

if ($requirements != '') {
    echo "<tr>
                                <td class='box-3'><strong>Requirements</strong></td>
                                <td class='box-7'>{$requirements}</td>
                            </tr>";
} else {

    $enquiryMessage = json_decode($enquiry_message, true);

    if (is_array($enquiryMessage)) {
        foreach ($enquiryMessage as $key => $value) {
            echo "<tr>
                                        <td class='box-3'><strong>" . str_replace('_', ' ', ucwords($key)) . "</strong></td>
                                        <td class='box-7'>{$value}</td>
                                    </tr>";
        }
    }
}

if ($site_access_type != 'agent') {
    if ($enquiry_source != '') {
        echo "<tr>
                                    <td class='box-3'><strong>Enquiry source</strong></td>
                                    <td class='box-7'>{$enquiry_source}</td>
                                </tr>";
    }

    if ($enquiry_medium != '') {
        echo "<tr>
                                    <td class='box-3'><strong>Enquiry medium</strong></td>
                                    <td class='box-7'>{$enquiry_medium}</td>
                                </tr>";
    }

    if ($enquiry_campaign != '') {
        echo "<tr>
                                    <td class='box-3'><strong>Enquiry campaign</strong></td>
                                    <td class='box-7'>{$enquiry_campaign}</td>
                                </tr>";
    }
}

?>
                        </table>
                    </div>
                </div>
                <div class="widget openBtnBox no-padding box-1" id="noteDetailsBox">
                    <div class="leadInfo">
                        <div class="historyBox">
                            <?php

if ($leadHistory = mysqli_query($connection, "SELECT enquiry_notes_meta_type, enquiry_notes_meta_value, profile_display_name, enquiry_notes_update FROM " . TABLE_PREFIX . "enquiry_notes INNER JOIN " . TABLE_PREFIX . "profile ON enquiry_notes_user_id=profile_user_id WHERE enquiry_notes_ref_id='{$leadId}' AND enquiry_notes_meta_type!='upay' AND  enquiry_notes_meta_type!='payment' ORDER BY enquiry_notes_update DESC")) {
    if (mysqli_num_rows($leadHistory) != 0) {
        echo "<ol class='timeline'>";
        while ($leadHistoryData = mysqli_fetch_array($leadHistory)) {
            extract($leadHistoryData);
            $commentBox = '';

            switch (strtolower($enquiry_notes_meta_type)) {
                case "assign":
                    $icon = 'link';
                    $msg = str_replace('Lead assign', '<a>Lead assign</a>', $enquiry_notes_meta_value);
                    break;

                case "payment":
                    $icon = 'indian-rupee-sign';
                    $msg = '<a>Payment</a>' . $profile_display_name;
                    break;

                case "generate":
                    $icon = 'plus';
                    $msg = '<a>Added </a>by ' . $profile_display_name;
                    break;

                case "update":
                    $icon = 'refresh';
                    $msg = '<a>Lead update </a>by ' . $profile_display_name;
                    break;

                case "duplicate":
                    $icon = 'refresh';
                    $msg = '<a>Lead mark Duplicate </a> updated by ' . $profile_display_name;
                    break;

                default:
                    $icon = 'comment-alt';
                    $msg = "<a>{$enquiry_notes_meta_type}</a> updated by " . $profile_display_name;
                    if ($enquiry_notes_meta_value != '') {
                        $commentBox = "<div class='comment'><p>{$enquiry_notes_meta_value}</p></div>";
                    }
                    break;
            }

            echo "<li class='timeline-item " . (($commentBox != '') ? ' | extra-space' : '') . "'>
                                                    <span class='timeline-item-icon | faded-icon'>
                                                        <i class='fi-rr-{$icon}'></i>
                                                    </span>
                                                    <div class='timeline-item-description'>
                                                        <span>{$msg}
                                                            on <time>" . date('d-m-Y h:i A', strtotime(($enquiry_notes_update))) . "</time></span>
                                                    </div>
                                                    {$commentBox}
                                                </li>";
        }
        echo "</ol>";
    }
} else {
    echo "Something went wrong";
}

?>
                        </div>
                    </div>
                </div>
                <div class="widget openBtnBox no-padding box-1" id="upayDetailsBox" style='display:none' >
                    <div class="leadInfo">
                        <div class="historyBox">
                            <?php

if ($leadHistory = mysqli_query($connection, "SELECT enquiry_notes_meta_type, enquiry_notes_meta_value, profile_display_name, enquiry_notes_update FROM " . TABLE_PREFIX . "enquiry_notes INNER JOIN " . TABLE_PREFIX . "profile ON enquiry_notes_user_id=profile_user_id WHERE enquiry_notes_ref_id='{$leadId}' AND enquiry_notes_meta_type='upay' ORDER BY enquiry_notes_update DESC")) {
    if (mysqli_num_rows($leadHistory) != 0) {
        echo "<ol class='timeline'>";
        while ($leadHistoryData = mysqli_fetch_array($leadHistory)) {
            extract($leadHistoryData);
            $commentBox = '';

            switch (strtolower($enquiry_notes_meta_type)) {
                case "upay":
                    $enquiry_notes_meta_value = json_decode($enquiry_notes_meta_value, true);
                    if (is_array($enquiry_notes_meta_value)) {
                        extract($enquiry_notes_meta_value);
                    } else {
                        $upayMsg = '';
                        $upayImages = '';
                    }
                    $icon = 'story-book';
                    $msg = "<a>{$enquiry_notes_meta_type}</a> added by " . $profile_display_name;
                    if ($enquiry_notes_meta_value != '') {
                        $commentBox = "<div class='comment'><p>{$upayMsg}</p>";
                        $upayImages = (json_decode($upayImages, true));
                        if (!empty($upayImages)) {
                            $commentBox .= "<div class='imagesList' >";
                            $upayImages = json_decode($upayImages, true);
                            for ($i = 0; $i < sizeof($upayImages); $i++) {
                                $commentBox .= "<a href='{$upayImages[$i]['path']}' target='_blank'><img class='leadInfoImage' src='{$upayImages[$i]['path']}' ></a>";
                            }
                            $commentBox .= "</div>";
                        }
                        $commentBox .= "</div>";
                    }
                    break;
                default:
                    $icon = 'times-hexagon';
                    $msg = '';
                    break;
            }

            echo "<li class='timeline-item " . (($commentBox != '') ? ' | extra-space' : '') . "'>
                                                    <span class='timeline-item-icon | faded-icon'>
                                                        <i class='fi-rr-{$icon}'></i>
                                                    </span>
                                                    <div class='timeline-item-description'>
                                                        <span>{$msg}
                                                            on <time>" . date('d-m-Y h:i A', strtotime(($enquiry_notes_update))) . "</time></span>
                                                    </div>
                                                    {$commentBox}
                                                </li>";
        }
        echo "</ol>";
    }
} else {
    echo "Something went wrong";
}

?>
                        </div>
                    </div>
                </div>
                <div class="widget openBtnBox no-padding box-1" id="paymentDetailsBox">
                    <div class="leadInfo"><div class="historyBox">
                            <?php

if ($leadHistory = mysqli_query($connection, "SELECT enquiry_notes_meta_type, enquiry_notes_meta_value, profile_display_name, enquiry_notes_update FROM " . TABLE_PREFIX . "enquiry_notes INNER JOIN " . TABLE_PREFIX . "profile ON enquiry_notes_user_id=profile_user_id WHERE enquiry_notes_ref_id='{$leadId}' AND enquiry_notes_meta_type='payment' ORDER BY enquiry_notes_update DESC")) {
    if (mysqli_num_rows($leadHistory) != 0) {
        echo "<ol class='timeline'>";
        while ($leadHistoryData = mysqli_fetch_array($leadHistory)) {
            extract($leadHistoryData);
            $commentBox = '';

            switch (strtolower($enquiry_notes_meta_type)) {
                case "payment":
                    $enquiry_notes_meta_value = json_decode($enquiry_notes_meta_value, true);
                    if (is_array($enquiry_notes_meta_value)) {
                        extract($enquiry_notes_meta_value);
                    } else {
                        $paymentAmount = '';
                        $paymentImages = '';
                        $paymentMethod = '';
                        $paymentDate = '';
                        $paymentReference = '';
                        $paymentMsg = '';
                        $paymentByCustomer = '';
                    }
                    $icon = 'indian-rupee-sign';

                    if ($paymentByCustomer == true) {
                        $msg = "<a>Payment</a> received by " . $customerFullName;
                    } else {
                        $msg = "<a>{$enquiry_notes_meta_type}</a> added by " . $profile_display_name;
                    }

                    if ($enquiry_notes_meta_value != '') {
                        $commentBox = "<div class='comment'>
                                                        <p><strong>Amount:</strong> {$paymentAmount}</p>
                                                        <p><strong>Payment method:</strong> {$paymentMethod}</p>
                                                        <p><strong>Date:</strong> {$paymentDate}</p>
                                                        <p><strong>Reference number:</strong> {$paymentReference}</p>
                                                        <p><strong>Comment:</strong> {$paymentMsg}</p>
                                                    ";
                        $paymentImages = (json_decode($paymentImages, true));
                        $paymentImages = (json_decode($paymentImages, true));
                        if ($paymentImages != '') {
                            $commentBox .= "<div class='imagesList' >";
                            $commentBox .= "<a href='{$paymentImages['path']}' target='_blank'><img class='leadInfoImage' src='{$paymentImages['path']}' ></a>";
                            $commentBox .= "</div>";
                        }
                        $commentBox .= "</div>";
                    }
                    break;
                default:
                    $icon = 'times-hexagon';
                    $msg = '';
                    break;
            }

            echo "<li class='timeline-item " . (($commentBox != '') ? ' | extra-space' : '') . "'>
                                                    <span class='timeline-item-icon | faded-icon'>
                                                        <i class='fi-rr-{$icon}'></i>
                                                    </span>
                                                    <div class='timeline-item-description'>
                                                        <span>{$msg}
                                                            on <time>" . date('d-m-Y h:i A', strtotime(($enquiry_notes_update))) . "</time></span>
                                                    </div>
                                                    {$commentBox}
                                                </li>";
        }
        echo "</ol>";
    }
} else {
    echo "Something went wrong";
}

?>
                        </div>
                    </div>
                </div>
                <div class="widget openBtnBox no-padding box-1" id="leadEditBox">
                    <div class="leadInfo">
                        <h3 class="heading">Update <?php echo ucfirst(strtolower($enquiry_subject)) ?> - lead details
                        </h3>
                        <table>
                            <form action="#" id="updateLeadForm" autocomplete="off">
                                <?php

$leadFormPath = $dashboardDirectory . '/dashboard/app/form/' . SITE_APP;
$leadForm = $leadFormPath . '/edit-lead.php';
if (!file_exists($leadForm)) {
    $leadForm =
        $dashboardDirectory . '/dashboard/app/form/default/edit-lead.php';
}

require_once $leadForm;

?>
                                <tr>
                                    <td class="box-3">
                                    </td>
                                    <td class="box-7">
                                        <input type="hidden" name="leadId" value="<?php echo $enquiry_id ?>">
                                        <input type="submit" id="updateEnquiry" class="btn btn-green"
                                            value="Update lead">
                                    </td>
                                </tr>
                            </form>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="widget no-padding box-2-5" id="fixedDiv">
        <div class="main-box">
            <section class="leadQuickLinks">
                <?php

if ($enquiry_status != 5) {
    if ($enquiry_status != 4) {
        if (SITE_APP_ID == 22 && ($enquiry_status == 2 || $enquiry_status == 1)) {
            echo "<a href='/dashboard/sales/add/?ref=enquiry&leadId=" . $id . "' class='btn btn-green'><i class='fi-rr-point-of-sale-bill'></i> Convert to sales</a>";
        }
        ?>
                <a href="" class="btn btn-green" id="followUpBtn"><i class="fi-rr-alarm-clock"></i> Follow-ups</a>
                <?php

        if (SITE_APP == 'astro_client') {
            echo "<a href='' class='btn btn-green' id='upayBtn'><i class='fi-rr-story-book'></i> Add Upay</a>";
            echo "
                <a href='' class='btn btn-green' id='updatePaymentBtn'><i class='fi-rr-notes'></i> Update payment
                    records</a>";
        }

        if (SITE_ID == 57) {?>
                <a href="" class="btn btn-green" id="paymentBtn"><i class="fi-rr-indian-rupee-sign"></i> Send payment
                    links</a>
                <a href="" class="btn btn-green" id="itineraryBtn"><i class="fi-rr-track"></i> Send itinerary</a>
                <a href="" class="btn btn-green" id="voucherBtn"><i class="fi-rr-ticket"></i> Send Vouchers</a>
                <?php
}

        ?>
                <a href="" class="btn btn-red" id="noteBtn"><i class="fi-rr-circle-xmark"></i> Close this lead</a>

                <?php
} else {?>

                <form method="POST" class="">
                    <!-- Design a form to assign lead to intern -->
                </form>

                <?php
}
}

?>
            </section>
        </div>
    </div>

    <div class="popUp notesBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" action="#">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Conclusion & Summary</h4>
                                </div>
                                <div class="form-group">
                                    <select class="selectJS form-control" id="notesTitle" name="title">
                                        <option value=""></option>
                                        <option value="Not interested">Not Interested</option>
                                        <option value="Purchased Elsewhere">Purchased Elsewhere</option>
                                        <option value="Unqualified Lead">Unqualified Lead</option>
                                        <option value="Fake Enquiry">Fake Enquiry</option>
                                        <option value="Client is not Picking call">Client is not Picking call</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <label for="">Title <sup>*</sup></label>
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="noteMsg" id="noteMsg" placeholder="Message*"
                                        class="form-control" required></textarea>
                                    <label class="form-lable" id="noteMsgLabel">Message</label>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea bgRed">
                    <a class="btn popUpBtn" id="updateNotes"><i class="fi-rr-check"></i> Mark as closed</a>
                    <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>

    <div class="popUp paymentLinkBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" action="#">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Send payment link</h4>
                                </div>
                                <div class="sendPaymentLinkBox">
                                    <div class="form-group-box">
                                        <div class="box-1">
                                            <div class="form-group">
                                                <div class="input-box">
                                                    <input type="radio" class="paymentLink" id="fullPaymentBtn"
                                                        name="paymentLink" value="yes" />
                                                    <label class="radio-button"><i class="fi-rr-indian-rupee-sign"></i>
                                                        Full
                                                        Payment</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-1">
                                            <div class="form-group">
                                                <div class="input-box">
                                                    <input type="radio" class="paymentLink" id="minPaymentLink"
                                                        name="paymentLink" value="yes" checked />
                                                    <label class="radio-button"><i class="fi-rr-indian-rupee-sign"></i>
                                                        Minimum payment</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-1">
                                            <div class="form-group">
                                                <div class="input-box">
                                                    <input type="radio" class="paymentLink" id="customPaymentLink"
                                                        name="paymentLink" value="0" />
                                                    <label class="radio-button"><i class="fi-rr-indian-rupee-sign"></i>
                                                        Custom
                                                        amount</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-1" id="minPaymentLinkBox" style="display:none">
                                            <div class="form-group">
                                                <input type="number" id="paymentValue" name="paymentLink" />
                                                <label class="form-lable" id="paymentAmount">Enter
                                                    amount<sup>*</sup></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a class="btn popUpBtn" id="sendPaymentLink"><i class="fi-rr-paper-plane"></i> Send payment
                        link</a>
                    <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="popUp updatePaymentBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" action="#" id="updatePaymentForm">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <form method="POST" action="#" id="addPaymentRecordForm" >
                                    <section class="form">
                                        <div class="step-open-box step_1_box">
                                            <div class="form-header">
                                                <h4 class="heading">Update payment details</h4>
                                            </div>
                                            <div class="form-group">
                                                <input type="number" id="paymentAmount" name="amount" />
                                                <label class="form-lable" id="paymentAmount">Enter amount<sup>*</sup></label>
                                            </div>
                                            <div class="form-group">
                                                <!-- <p>Payment method <sup>*</sup></p> -->
                                                <select class="selectJS form-control" id="paymentRecordTitle"
                                                    name="payment_method">
                                                    <option value=""></option>
                                                    <option value="cash">Cash</option>
                                                    <option value="upi">UPI</option>
                                                    <option value="net_banking">Net banking</option>
                                                    <option value="credit_card">Credit Card</option>
                                                    <option value="debit_card">Debit Card</option>
                                                    <option value="wallet">Wallet</option>
                                                    <option value="other">Other</option>
                                                </select>
                                                <label class="form-lable">Payment method <sup>*</sup></label>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" class="datepicker" placeholder="<?php echo date('d-m-Y'); ?>" id="paymentDate" name="payment_date" />
                                                <label class="form-lable">Payment Date</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" placeholder="85NDR1ZD840V" id="paymentRefNumber" name="reference_number" />
                                                <label class="form-lable">Reference number</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="file" id="fileUpload" name="files" accept=".jpg, .jpeg, .png, .gif, .pdf, .doc, .docx">
                                                <label class="form-lable" id="upayPhoto">Upload photo</label>
                                            </div>
                                            <div class="form-group">
                                                <textarea type="text" name="payment_notes" id="noteMsg"
                                                    placeholder="Payment details" class="form-control"
                                                    required></textarea>
                                                <label class="form-lable">Payment
                                                    details</label>
                                            </div>
                                        </div>
                                </form>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a class="btn popUpBtn" id="upatePaymentRecord"><i class="fi-rr-check"></i> Update
                        records</a>
                    <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="popUp followUpBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" action="#">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Add Follow-Up Reminder</h4>
                                </div>
                                <div class="form-group">
                                    <select class="selectJS form-control" id="reason" name="reason">
                                        <option value=""></option>
                                        <option value="Busy">Busy</option>
                                        <option value="Mobile is not reachable">Mobile is not reachable</option>
                                        <option value="Client is not Picking call">Client is not Picking call</option>
                                        <option value="Interested">Interested</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <label>Reason <sup>*</sup></label>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="datepickerForFutureDateWithTime" name="followupTime"
                                        id="followUpTime">
                                    <label class="form-lable">Follow up time</label>
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="comment" id="comment"
                                        placeholder="Add your comment"></textarea>
                                    <label class="form-lable" id="commentLable">Any comment</label>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <span class="btn popUpBtn" id="addFollowUp"><i class="fi-rr-check"></i> Set reminder</span>
                    <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>

    <?php

if (SITE_APP == 'astro_client') {?>

    <div class="popUp upayBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" action="#" id="addUpayForm" >
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Add new upay</h4>
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="upayText" id="upayComment"
                                        placeholder="Add your comment"></textarea>
                                    <label class="form-lable" id="commentLable">Any comment</label>
                                </div>
                                <div class="form-group">
                                    <input type="file" id="fileUpload" name="files[]" accept=".jpg, .jpeg, .png, .gif, .pdf, .doc, .docx" multiple>
                                    <label class="form-lable" id="upayPhoto">Upload photo</label>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <span class="btn popUpBtn" id="addUpayBtn"><i class="fi-rr-check"></i> Save Upay</span>
                    <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>
    <?php
}

?>

    <div class="popUp itineraryBox">
        <div class="fullScreenPopUpBox">
            <div class="widget box-8">
                <div class="contentArea">
                    <h4 class="heading"><?php echo $enquiry_subject ?> Itinerary</span></h2>
                </div>
            </div>
            <div class="widget box-2">
                <div class="actionArea">
                    <a class="btn popUpBtn"><i class="fi-rr-paper-plane"></i> Send itinerary</a>
                    <a class="btn popUpBtn"><i class="fi-rr-file-pdf"></i> Donwload itinerary</a>
                    <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="popUp voucherBox">
        <div class="fullScreenPopUpBox">
            <div class="widget box-8">
                <div class="contentArea">
                    <h4 class="heading"><?php echo $enquiry_subject ?> Vouchers</span></h2>
                </div>
            </div>
            <div class="widget box-2">
                <div class="actionArea">
                    <a class="btn popUpBtn"><i class="fi-rr-paper-plane"></i> Send vouchers</a>
                    <a class="btn popUpBtn"><i class="fi-rr-file-pdf"></i> Donwload vouchers</a>
                    <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>