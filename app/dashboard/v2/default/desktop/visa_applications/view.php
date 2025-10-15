<?php

$leadId = base64_decode($id);

$accessFilter = '';
if ($site_access_type == 'agent') {
    $accessFilter = " AND visa_application_agent='" . USER_ID . "'";
}

$visa_applicationQuery = "SELECT * FROM " . TABLE_PREFIX . "visa_application WHERE visa_application_id='{$leadId}' {$accessFilter} AND visa_application_site_id=" . SITE_ID . " ORDER BY visa_application_status DESC LIMIT 0, 1";

if ($checkVisaApplication = mysqli_query($connection, $visa_applicationQuery)) {
    if (mysqli_num_rows($checkVisaApplication) != 0) {
        extract(mysqli_fetch_assoc($checkVisaApplication));

        $personsDataObject = json_decode($visa_application_data, TRUE);
        extract($personsDataObject['personsData'][0]);
    } else {
        echo "Invalid request";
        exit();
    }
} else {
    echo "Something went wrong";
    exit();
}

?>
<h2 class="heading">VISA Application details<span>Complete information of lead</span></h2>
<div class="box-1">

    <div class="widget box-7-5 no-padding">
        <div class="main-box">
            <section>
                <div class="widget no-padding box-1">
                    <div class="leadProfileCard">
                        <div class="img">
                            <img src="<?php echo IMG_LIBRARY ?>profile_icon/<?php echo strtolower(((trim($first_name[0]) != '') ? $first_name[0] : 'unknown')) ?>.png"
                                alt="" srcset="">
                        </div>
                        <div class="info">
                            <h2 class="heading"
                                <?php echo (($visa_application_status == 4 || $visa_application_status == 5) ? "style='color:#f44336'" : "") ?>>
                                <?php echo $customerFullName = ucwords($first_name . ' ' . $last_name . (($visa_application_status == 4) ? ' ( Closed ) ' : (($visa_application_status == 5) ? '( Duplicate )' : ''))) ?>
                            </h2>
                            <p>
                                <?php

                                if (isset($date_of_birth)) {
                                    echo "<span><strong>Date of Birth:</strong> " . date('d M Y', strtotime($date_of_birth)) . "</span>";
                                }

                                ?>
                                <span><strong>Last Update:
                                    </strong><?php echo date('d-m-Y h:i A', strtotime($visa_application_update)) ?></span>
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
                        <a id="additionalPassengerBtn" class="btn detailsBtn"><i class="fi-rr-user"></i> Additional
                            Passengers</a>
                        <a id="documentBtn" class="btn detailsBtn"><i class="fi-rr-document"></i> Documents</a>
                        <?php

                        if ($visa_application_status != 5) { ?>
                        <a id="leadEditBtn" class="btn right-btn highlight" style="margin-top:20px"><i
                                class="fi-rr-edit"></i> Edit
                            Application</a>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="widget openBtnBox no-padding box-1" id="leadDetailsBox">
                    <div class="leadInfo">
                        <table>
                            <?php
                            if (isset($personsDataObject['personsData'][0])) {
                                $person = $personsDataObject['personsData'][0];

                                foreach ($person as $key => $value) {

                                    // ✅ Skip 'media' or any array type
                                    if ($key === 'media' || is_array($value)) {
                                        continue;
                                    }

                                    if (trim($value) !== '') {
                                        $key_name = ucwords(str_replace('_', ' ', $key));
                                        echo "<tr>
                    <td class='box-3'><strong>{$key_name}</strong></td>
                    <td class='box-7'>{$value}</td>
                  </tr>";
                                    }
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

                            if ($leadHistory = mysqli_query($connection, "SELECT visa_application_notes_id, visa_application_notes_meta_type, visa_application_notes_meta_value, profile_display_name, visa_application_notes_update FROM " . TABLE_PREFIX . "visa_application_notes INNER JOIN " . TABLE_PREFIX . "profile ON visa_application_notes_user_id=profile_user_id WHERE visa_application_notes_ref_id='{$leadId}' AND visa_application_notes_meta_type!='upay' AND  visa_application_notes_meta_type!='payment' ORDER BY visa_application_notes_update DESC")) {
                                if (mysqli_num_rows($leadHistory) != 0) {
                                    echo "<ol class='timeline'>";
                                    while ($leadHistoryData = mysqli_fetch_array($leadHistory)) {
                                        extract($leadHistoryData);
                                        $commentBox = '';

                                        switch (strtolower($visa_application_notes_meta_type)) {
                                            case "assign":
                                                $icon = 'link';
                                                $msg = str_replace('Lead assign', '<a>Lead assign</a>', $visa_application_notes_meta_value);
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

                                            case "sales":
                                                $icon = 'trophy-star';
                                                $msg = '<a>Convert to sales </a> updated by ' . $profile_display_name;
                                                break;

                                            case "duplicate":
                                                $icon = 'refresh';
                                                $msg = '<a>Lead mark Duplicate </a> updated by ' . $profile_display_name;
                                                break;

                                            default:
                                                $icon = 'comment-alt';
                                                $msg = "<a>{$visa_application_notes_meta_type}</a> updated by " . $profile_display_name;
                                                if ($visa_application_notes_meta_value != '') {
                                                    $commentBox = "<div class='comment'><p>{$visa_application_notes_meta_value}</p></div>";
                                                }
                                                break;
                                        }

                                        echo "<li class='timeline-item " . (($commentBox != '') ? ' | extra-space' : '') . "'>
                                                    <span class='timeline-item-icon | faded-icon'>
                                                        <i class='fi-rr-{$icon}'></i>
                                                    </span>
                                                    <div class='timeline-item-description'>
                                                        <span>{$msg}
                                                            on <time>" . date('d-m-Y h:i A', strtotime(($visa_application_notes_update))) . "</time></span>
                                                    </div>
                                                    {$commentBox}
                                                    ";
                                        echo "
                                                </li>";
                                        if ($site_access_type == 'administrator' || $site_access_type == 'receiption') {
                                            echo "<a href='#' style='float:left; margin-right: 75%;' data-type='leads/follow_up' data-id='{$visa_application_notes_id}' title='Delete follow up' data-title='Delete follow up' class='btn dataTableBtn btn-red deleteButton' data-reload='true' data-redirect='false'><i class='fi fi-rr-trash'></i> Delete follow up</a>";
                                        }
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
                <div class="widget openBtnBox no-padding box-1" id="additionalPassengerBox" style="display: none;">
                    <div class="leadInfo">
                        <table>
                            <?php

                            $addPersonData = $personsDataObject['personsData'];
                            for ($i = 1; $i < sizeof($addPersonData); $i++) {
                                $person = $addPersonData[$i];
                                echo "<tr><td colspan=2><h3>Passenger " . ($i + 1) . "</h3></td></tr>";

                                foreach ($person as $key => $value) {
                                    if (trim($value) !== '') {
                                        // Make key name readable, e.g., first_name → First Name
                                        $key_name = ucwords(str_replace('_', ' ', $key));

                                        echo "<tr>
                    <td class='box-3'><strong>{$key_name}</strong></td>
                    <td class='box-7'>{$value}</td>
                  </tr>";
                                    }
                                }
                            }
                            ?>
                        </table>
                    </div>
                </div>
                <div class="widget openBtnBox no-padding box-1" id="documentBox" style="display: none;">
                    <div class="leadInfo">
                        <table>
                            <?php
                            $addPersonData = $personsDataObject['personsData'];

                            for ($i = 0; $i < sizeof($addPersonData); $i++) {
                                echo "<tr><td colspan='2'><h3>Passenger " . ($i + 1) . "</h3></td></tr>";

                                if (isset($addPersonData[$i]['media']) && is_array($addPersonData[$i]['media'])) {
                                    $mediaData = $addPersonData[$i]['media'];

                                    foreach ($mediaData as $mediaType => $files) {
                                        if (!empty($files) && is_array($files)) {
                                            foreach ($files as $filePath) {
                                                if (trim($filePath) !== '') {
                                                    $key_name = ucwords(str_replace('_', ' ', $mediaType));
                                                    $fileUrl = htmlspecialchars($filePath, ENT_QUOTES);

                                                    echo "<tr>
                                <td class='box-3'><strong>{$key_name}</strong></td>
                                <td class='box-7'>
                                   <a href='{$fileUrl}' target='_blank' style='color:#007bff;text-decoration:underline;'>
    Click here to view <i class='fi-rr-external-link-square' style='font-size:13px;margin-left:4px;'></i>
</a>
                                </td>
                              </tr>";
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    echo "<tr><td colspan='2'>No media available</td></tr>";
                                }
                            }
                            ?>
                        </table>

                    </div>
                </div>
                <div class="widget openBtnBox no-padding box-1" id="paymentDetailsBox">
                    <div class="leadInfo">
                        <div class="historyBox">
                            <?php

                            if ($leadHistory = mysqli_query($connection, "SELECT visa_application_notes_id, visa_application_notes_meta_type, visa_application_notes_meta_value, profile_display_name, visa_application_notes_update FROM " . TABLE_PREFIX . "visa_application_notes INNER JOIN " . TABLE_PREFIX . "profile ON visa_application_notes_user_id=profile_user_id WHERE visa_application_notes_ref_id='{$leadId}' AND visa_application_notes_meta_type='payment' ORDER BY visa_application_notes_update DESC")) {
                                if (mysqli_num_rows($leadHistory) != 0) {
                                    echo "<ol class='timeline'>";
                                    while ($leadHistoryData = mysqli_fetch_array($leadHistory)) {
                                        extract($leadHistoryData);
                                        $commentBox = '';

                                        switch (strtolower($visa_application_notes_meta_type)) {
                                            case "payment":
                                                $visa_application_notes_meta_value = json_decode($visa_application_notes_meta_value, true);
                                                if (is_array($visa_application_notes_meta_value)) {
                                                    extract($visa_application_notes_meta_value);
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
                                                    $msg = "<a>{$visa_application_notes_meta_type}</a> added by " . $profile_display_name;
                                                }

                                                if ($visa_application_notes_meta_value != '') {
                                                    $commentBox = "<div class='comment'>
                                                        <p><strong>Amount:</strong> {$paymentAmount}</p>
                                                        <p><strong>Payment method:</strong> {$paymentMethod}</p>
                                                        <p><strong>Date:</strong> {$paymentDate}</p>
                                                        <p><strong>Reference number:</strong> {$paymentReference}</p>
                                                        <p><strong>Comment:</strong> " . str_replace('\r\n', '<br />', $paymentMsg) . "</p>
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
                                                            on <time>" . date('d-m-Y h:i A', strtotime(($visa_application_notes_update))) . "</time></span>
                                                    </div>
                                                    {$commentBox}
                                                </li>";
                                        if ($site_access_type == 'administrator' || $site_access_type == 'receiption') {
                                            echo "<a href='#' data-type='leads/payment' data-id='{$visa_application_notes_id}' title='Delete payment record' data-title='Delete payment record' class='btn dataTableBtn btn-red deleteButton' data-reload='true' data-redirect='false'><i class='fi fi-rr-trash'></i> Delete payment record</a>";
                                        }
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
                        <h3 class="heading">Update application details</h3>
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
                                <!-- <tr>
                                    <td class="box-3">
                                    </td>
                                    <td class="box-7">
                                        <input type="hidden" name="leadId" value="<?php echo $visa_application_id ?>">
                                        <input type="button" id="updateVISAApplication" class="btn btn-green"
                                            value="Update lead">
                                    </td>
                                </tr> -->
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

                if ($visa_application_status != 5) {
                    if ($visa_application_status != 4) {
                        echo (($visa_application_status != 1) ? "<a href='#' class='btn btn-natural-green' id='convertToSale' data-id='" . base64_encode($visa_application_id) . "'.><i class='fi-rr-octagon-check'></i> Convert to Sale</a>" : '');
                ?>

                <a href="" class="btn btn-green" id="followUpBtn"><i class="fi-rr-alarm-clock"></i> Follow-ups</a>

                <?php
                    }

                        ?><?php

                            if ($visa_application_status != 1) {
                                echo '<a href="" class="btn btn-red" id="noteBtn"><i class="fi-rr-circle-xmark"></i> Close this lead</a>';
                            }

                            ?>


                <?php
                } else { ?>

                <form method="POST" class="">
                    <!-- Design a form to assign lead to intern -->
                </form>

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
                                        <option value="Fake VISA Application">Fake VISA Application</option>
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
                                                <input type="number" onwheel="this.blur()" id="paymentValue"
                                                    name="paymentLink" />
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
                                <form method="POST" action="#" id="addPaymentRecordForm">
                                    <section class="form">
                                        <div class="step-open-box step_1_box">
                                            <div class="form-header">
                                                <h4 class="heading">Update payment details</h4>
                                            </div>
                                            <div class="form-group">
                                                <input type="number" onwheel="this.blur()" id="paymentAmount"
                                                    name="amount" />
                                                <label class="form-lable" id="paymentAmount">Enter
                                                    amount<sup>*</sup></label>
                                            </div>
                                            <div class="form-group">
                                                <select class="selectJS form-control" id="paymentRecordTitle"
                                                    name="payment_method">
                                                    <option value=""></option>
                                                    <option value="free">Free</option>
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
                                                <input type="text" class="datepicker"
                                                    placeholder="<?php echo date('d-m-Y'); ?>" id="paymentDate"
                                                    name="payment_date" />
                                                <label class="form-lable">Payment Date</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" placeholder="85NDR1ZD840V" id="paymentRefNumber"
                                                    name="reference_number" />
                                                <label class="form-lable">Reference number</label>
                                            </div>
                                            <div class="form-group">
                                                <input type="file" id="fileUpload" name="files"
                                                    accept=".jpg, .jpeg, .png, .gif, .pdf, .doc, .docx">
                                                <label class="form-lable" id="paymentPhoto">Upload photo</label>
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
                }

?>

    <div class="popUp itineraryBox">
        <div class="fullScreenPopUpBox">
            <div class="widget box-8">
                <div class="contentArea">
                    <div id="itinerarySeachBox">
                        <h4 class="heading" style="margin-bottom: 2rem">Search Itinenary</h4>
                        <div class="box-1">
                            <div class="box-5">
                                <h4>Select destination<sup>*</sup></h4>
                                <select class="internationalCityAutoComplete form-control" id="itineraryDestination">
                                </select>
                            </div>
                            <div class="box-5">
                                <h4>Travel Style</h4>
                                <select class="selectJS form-control" name="travelStyle" id="itineraryStyle">
                                    <option value="select">Select travel style</option>
                                    <option value="adventure">Adventure Travel</option>
                                    <option value="cultural">Cultural Travel</option>
                                    <option value="luxury">Luxury Travel</option>
                                    <option value="solo">Solo Travel</option>
                                    <option value="group">Group Travel</option>
                                    <option value="eco">Eco/Responsible Travel</option>
                                    <option value="honeymoon">Honeymoon Travel</option>
                                    <option value="couple">Couple Travel</option>
                                    <option value="family">Family Travel</option>
                                    <option value="wellness">Wellness/Retreat Travel</option>
                                    <option value="budget">Budget Travel</option>
                                    <option value="roadtrip">Road Trip</option>
                                    <option value="cruise">Cruise Travel</option>
                                    <option value="backpacking">Backpacking Travel</option>
                                    <option value="religious">Religious Travel</option>
                                    <option value="heritage">Heritage Travel</option>
                                </select>
                            </div>
                            <div class="box-1">
                                <a href="#" class="btn" id="fetchItinerary"
                                    style="float: right; padding:0.5rem 2rem; margin-top:10px">Find</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="itineraryListBox" style="display: none;">
                        <h4 class="heading" style="margin-bottom: 2rem">Select Itinenary<sup>*</sup></h4>
                        <select class="selectJS form-control" id="itineraryList"></select>
                        <div class="box-1">
                            <a href="#" class="btn" id="showItinerary"
                                style="float: left; padding:0.5rem 2rem; margin-top:10px">Show Itinerary</a>
                        </div>
                    </div>
                    <div class="form-group" id="editorBox" style="display: none;">
                        <textarea name="itinerary" id="itineraryEditorBox"></textarea>
                    </div>
                </div>
            </div>
            <div class="widget box-2">
                <div class="actionArea">
                    <input type="hidden" id="itinenaryId" value="">
                    <input type="hidden" id="itinenaryMrp" value="">
                    <input type="hidden" id="itinenaryRefId" value="<?php echo $visa_application_id ?>">
                    <input type="hidden" id="itinenaryRefType" value="visa_application">
                    <input type="hidden" id="itinenaryType" value="tour_package">
                    <a class="btn popUpBtn hidden" id="sendItinerary"><i class="fi-rr-paper-plane"></i> Send
                        itinerary</a>
                    <a href="#" class="btn popUpBtn hidden" id="downloadItinerary" target="_blank"><i
                            class="fi-rr-file-pdf"></i> Download itinerary</a>
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
                    <h4 class="heading"><?php echo $visa_application_subject ?> Vouchers</span></h2>
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