<?php

    $leadId = base64_decode($id);

    $accessFilter='';
    if($site_access_type=='agent') {
        $accessFilter = " AND enquiry_agent='".USER_ID."'";
    }

    $enquiryQuery="SELECT * FROM ".TABLE_PREFIX."enquiry LEFT JOIN ".TABLE_PREFIX."enquiry_profile ON enquiry_profile_ref_id=enquiry_id WHERE enquiry_id='{$leadId}' {$accessFilter} AND status=1 AND enquiry_site_id=".SITE_ID;

    if($checkEnquiry=mysqli_query($connection, $enquiryQuery)) {
        if(mysqli_num_rows($checkEnquiry)!=0) {
            extract(mysqli_fetch_assoc($checkEnquiry));
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
                            <img src="<?php echo IMG_LIBRARY ?>profile_icon/<?php echo strtolower(((trim($enquiry_first_name[0])!='')? $enquiry_first_name[0] : 'unknown')) ?>.png"
                                alt="" srcset="">
                        </div>
                        <div class="info">
                            <h2 class="heading"><?php echo $enquiry_first_name.' '.$enquiry_last_name ?></h2>
                            <p><span><strong>Last Update:
                                    </strong><?php echo date('d M Y H:i:s', strtotime($enquiry_update)) ?></span></p>
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
                        <a href="#" id="leadDetailsBtn" class="btn btn-green"><i class="fi-rr-file-circle-info"></i>
                            Details</a>
                        <a href="#" id="noteDetailsBtn" class="btn"><i class="fi-rr-time-past"></i> Follow up
                            history</a>
                        <a href="#" id="paymentDetailsBtn" class="btn"><i class="fi-rr-money-check"></i> Payment
                            history</a>
                        <a href="#" id="leadEditBtn" class="btn right-btn highlight"><i class="fi-rr-edit"></i> Edit
                            lead</a>
                    </div>
                </div>
                <div class="widget no-padding box-1" id="leadDetailsBox">
                    <div class="leadInfo">
                        <table>
                            <?php
                    
                        if($enquiry_first_name!='') {
                            echo "<tr>
                                <td class='box-3'><strong>First name</strong></td>
                                <td class='box-7'>{$enquiry_first_name}</td>
                            </tr>";
                        }
                    
                        if($enquiry_middle_name!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Middle name</strong></td>
                                <td class='box-7'>{$enquiry_middle_name}</td>
                            </tr>";
                        }
                    
                        if($enquiry_last_name!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Last name</strong></td>
                                <td class='box-7'>{$enquiry_last_name}</td>
                            </tr>";
                        }
                    
                        if($enquiry_email!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Email</strong></td>
                                <td class='box-7'>{$enquiry_email}</td>
                            </tr>";
                        }
                    
                        if($enquiry_mobile!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Mobile</strong></td>
                                <td class='box-7'>{$enquiry_mobile}</td>
                            </tr>";
                        }
                    
                        if($enquiry_subject!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Tour package</strong></td>
                                <td class='box-7'>{$enquiry_subject}</td>
                            </tr>";
                        }
                    
                        if($date_of_travel!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Date of travel</strong></td>
                                <td class='box-7'>{$date_of_travel}</td>
                            </tr>";
                        }
                    
                        if($number_of_days!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Number of days</strong></td>
                                <td class='box-7'>{$number_of_days} Nights</td>
                            </tr>";
                        }
                    
                        if($number_of_adults!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Number of adults</strong></td>
                                <td class='box-7'>{$number_of_adults}</td>
                            </tr>";
                        }
                    
                        if($number_of_childrens!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Number of childrens</strong></td>
                                <td class='box-7'>{$number_of_childrens}</td>
                            </tr>";
                        }
                    
                        if($number_of_infants!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Number of infants</strong></td>
                                <td class='box-7'>{$number_of_infants}</td>
                            </tr>";
                        }

                        if($current_location!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Current location</strong></td>
                                <td class='box-7'>{$current_location}</td>
                            </tr>";
                        }

                        if($pickup_location!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Pickup location</strong></td>
                                <td class='box-7'>{$pickup_location}</td>
                            </tr>";
                        }

                        if($drop_location!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Drop location</strong></td>
                                <td class='box-7'>{$drop_location}</td>
                            </tr>";
                        }

                        if($requirements!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Requirements</strong></td>
                                <td class='box-7'>{$requirements}</td>
                            </tr>";
                        } else {

                            $enquiryMessage = json_decode($enquiry_message, TRUE);
                            
                            if(is_array($enquiryMessage)) {
                                foreach($enquiryMessage as $key => $value)  {
                                    echo "<tr>
                                        <td class='box-3'><strong>".str_replace('_', ' ', ucwords($key))."</strong></td>
                                        <td class='box-7'>{$value}</td>
                                    </tr>";
                                }
                            }
                            
                        }
                    
                        if($enquiry_source!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Enquiry source</strong></td>
                                <td class='box-7'>{$enquiry_source}</td>
                            </tr>";
                        }
                    
                        if($enquiry_medium!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Enquiry medium</strong></td>
                                <td class='box-7'>{$enquiry_medium}</td>
                            </tr>";
                        }
                    
                        if($enquiry_campaign!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Enquiry campaign</strong></td>
                                <td class='box-7'>{$enquiry_campaign}</td>
                            </tr>";
                        }

                    ?>
                        </table>
                    </div>
                </div>
                <div class="widget no-padding box-1" id="noteDetailsBox">
                    <div class="leadInfo">
                        <div class="historyBox">
                            <ol class="timeline">
                                <?php
                                
                                    if($leadHistory=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."enquiry_notes WHERE enquiry_notes_ref_id='{$leadId}' ORDER BY enquiry_notes_update DESC")) {
                                        if(mysqli_num_rows($leadHistory)!=0) {
                                            while($leadHistoryData = mysqli_fetch_array($leadHistory)) {
                                                extract($leadHistoryData); 
                                                $commentBox='';
                                                
                                                switch(strtolower($enquiry_notes_meta_type)) {
                                                    case "assign" :
                                                        $icon='link';
                                                        $msg=str_replace('Lead assign', '<a href="#">Lead assign</a>', $enquiry_notes_meta_value);
                                                        break;
                                                        
                                                    case "payment" :
                                                        $icon='indian-rupee-sign';
                                                        $msg='<a href="#">Payment</a>'.$enquiry_notes_meta_value;
                                                        break;
                                                        
                                                    case "generate" :
                                                        $icon='plus';
                                                        $msg='<a href="#">Added </a>by '.$enquiry_notes_meta_value;
                                                        break;
                                                        
                                                    case "update" :
                                                        $icon='refresh';
                                                        $msg='<a href="#">Update </a>by '.$enquiry_notes_meta_value;
                                                        break;

                                                    default:
                                                        $icon='comment-alt';
                                                        $msg= "<a href='#'>{$enquiry_notes_meta_type}</a>";
                                                        if($enquiry_notes_meta_value!='') {
                                                            $commentBox="<div class='comment'><p>{$enquiry_notes_meta_value}</p></div>";
                                                        }
                                                        break;
                                                }

                                                echo "<li class='timeline-item ".(($commentBox!='') ? ' | extra-space' : '')."'>
                                                    <span class='timeline-item-icon | faded-icon'>
                                                        <i class='fi-rr-{$icon}'></i>
                                                    </span>
                                                    <div class='timeline-item-description'>
                                                        <span>{$msg}
                                                            on <time>".date('d M Y, H:i:s', strtotime(($enquiry_notes_update)))."</time></span>
                                                    </div>
                                                    {$commentBox}
                                                </li>";

                                            }
                                        }
                                    } else {
                                        echo "Something went wrong";
                                    }

                                ?>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="widget no-padding box-1" id="paymentDetailsBox">
                    <div class="leadInfo">
                    </div>
                </div>
                <div class="widget no-padding box-1" id="leadEditBox">
                    <div class="leadInfo">
                        <h3 class="heading">Update <?php echo ucfirst(strtolower($enquiry_subject)) ?> - lead details
                        </h3>
                        <table>
                            <form action="#" id="updateLeadForm" autocomplete="off">
                                <tr>
                                    <td class="box-3"><strong>First name<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="first_name"
                                            id="firstName" value="<?php echo $enquiry_first_name ?>" required></td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Middle name</strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="middle_name"
                                            id="middleName" value="<?php echo $enquiry_middle_name ?>"></td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Last name</strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="last_name"
                                            id="lastName" value="<?php echo $enquiry_last_name ?>"></td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Email</strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="email" id="email"
                                            value="<?php echo $enquiry_email ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Mobile<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="mobile" id="mobile"
                                            value="<?php echo $enquiry_mobile ?>" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Tour package<sup>*</sup></strong></td>
                                    <td class="box-7">
                                        <select class="selectJS form-control" name="package" id="select">
                                            <option value="">Select destination</option>
                                            <option value="himachal"
                                                <?php echo (($enquiry_subject=='himachal') ? 'selected': '') ?>>Himachal
                                            </option>
                                            <option value="uttarakhand"
                                                <?php echo (($enquiry_subject=='uttarakhand') ? 'selected': '') ?>>
                                                Uttarakhand
                                            </option>
                                            <option value="jammu & kashmir"
                                                <?php echo (($enquiry_subject=='jammu & kashmir') ? 'selected': '') ?>>
                                                Jammu & Kashmir
                                            </option>
                                            <option value="ladakh"
                                                <?php echo (($enquiry_subject=='ladakh') ? 'selected': '') ?>>Ladakh
                                            </option>
                                            <option value="punjab"
                                                <?php echo (($enquiry_subject=='punjab') ? 'selected': '') ?>>Punjab
                                            </option>
                                            <option value="" <?php echo (($enquiry_subject=='') ? 'selected': '') ?>>
                                            </option>
                                            <option value="" <?php echo (($enquiry_subject=='') ? 'selected': '') ?>>
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Date of travel<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control datepickerForFutureDate"
                                            name="date_of_travel" id="date_of_travel"
                                            value="<?php echo date('d-m-Y', strtotime($date_of_travel)) ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Travel duration<span class="secondLine">( in Nights
                                                )</span><sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="number_of_days"
                                            id="number_of_days" placeholder="3" value="<?php echo $number_of_days ?>"
                                            required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Number of adults<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="number_of_adults"
                                            id="number_of_adults" placeholder="0"
                                            value="<?php echo $number_of_adults ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Number of children<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="number_of_childrens"
                                            id="number_of_childrens" placeholder="0"
                                            value="<?php echo $number_of_childrens ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Number of infants<span class="secondLine">( Below 5
                                                years)</span><sup>*</sup></strong>
                                    </td>
                                    <td class="box-7"><input type="text" class="form-control" name="number_of_infants"
                                            id="number_of_infants" placeholder="0"
                                            value="<?php echo $number_of_infants ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Current location<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="current_location"
                                            id="current_location" value="<?php echo $current_location ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Pickup location<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="pickup_location"
                                            id="pickup_location" value="<?php echo $pickup_location ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Drop location<span class="secondLine">( ignore if same
                                                )</span></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="drop_location"
                                            id="drop_location" value="<?php echo $drop_location ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Any requirements</strong></td>
                                    <td class="box-7">
                                        <textarea name="requirements" class="form-control"
                                            id="requirements"><?php echo (($requirements=='') ? $enquiry_message : $requirements) ?></textarea>
                                    </td>
                                </tr>
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

    <div class="widget no-padding box-2-5">
        <div class="main-box">
            <section class="leadQuickLinks">
                <a href="" class="btn btn-green" id="noteBtn"><i class="fi-rr-note"></i> Final Interaction Note</a>
                <a href="" class="btn btn-green" id="paymentBtn"><i class="fi-rr-link-alt"></i> Send payment links</a>
                <a href="" class="btn btn-green" id="followUpBtn"><i class="fi-rr-alarm-clock"></i> Follow-ups</a>
                <a href="" class="btn btn-green" id="itineraryBtn"><i class="fi-rr-track"></i> Send itinerary</a>
                <a href="" class="btn btn-green" id="voucherBtn"><i class="fi-rr-ticket"></i> Send Vouchers</a>
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
                                    <p>Title <sup>*</sup></p>
                                    <select class="selectJS form-control" id="notesTitle" name="title">
                                        <option value=""></option>
                                        <option value="Not interested">Not Interested</option>
                                        <option value="Purchased Elsewhere">Purchased Elsewhere</option>
                                        <option value="Unqualified Lead">Unqualified Lead</option>
                                        <option value="Fake Enquiry">Fake Enquiry</option>
                                        <option value="Client is not Picking call">Client is not Picking call</option>
                                        <option value="other">Other</option>
                                    </select>
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
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="updateNotes"><i class="fi-rr-check"></i> Submit</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
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
                                <div class="form-group-box">
                                    <div class="box-1">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <input type="radio" id="fullPaymentBtn" name="paymentLink"
                                                    value="yes" />
                                                <label class="radio-button">Full Payment</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-1">
                                        <div class="form-group">
                                            <div class="input-box">
                                                <input type="radio" id="minPaymentLink" name="paymentLink" value="yes"
                                                    checked />
                                                <label class="radio-button">Min payment</label>
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
                    <a href="#" class="btn popUpBtn"><i class="fi-rr-check"></i> Submit</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
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
                                    <p>Title <sup>*</sup></p>
                                    <select class="selectJS form-control" name="title">
                                        <option value=""></option>
                                        <option value="Not interested">Not Interested</option>
                                        <option value="Purchased Elsewhere">Purchased Elsewhere</option>
                                        <option value="Unqualified Lead">Unqualified Lead</option>
                                        <option value="Fake Enquiry">Fake Enquiry</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="first_name" id="first_name" placeholder="First name*"
                                        class="form-control" required></textarea>
                                    <label class="form-lable" id="first_nameLabel">First name<sup>*</sup></label>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn"><i class="fi-rr-check"></i> Set reminder</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="popUp itineraryBox">
        <div class="fullScreenPopUpBox">
            <div class="widget box-8">
                <div class="contentArea">
                    <h4 class="heading"><?php echo $enquiry_subject ?></span></h2>
                </div>
            </div>
            <div class="widget box-2">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn"><i class="fi-rr-paper-plane"></i> Send itinerary</a>
                    <a href="#" class="btn popUpBtn"><i class="fi-rr-file-pdf"></i> Donwload itinerary</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>