<?php

    $leadId = base64_decode($id);

    if($site_access_type=='agent') {
        $enquiryQuery = "SELECT * FROM ".TABLE_PREFIX."enquiry WHERE enquiry_id='{$leadId}' AND enquiry_agent=".USER_ID." AND enquiry_site_id=".SITE_ID;
    } else {
        $enquiryQuery = "SELECT * FROM ".TABLE_PREFIX."enquiry WHERE enquiry_id='{$leadId}' AND enquiry_site_id=".SITE_ID;
    }
    

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
                        <a href="#" id="leadDetailsBtn" class="btn btn-green">Details</a>
                        <a href="#" id="noteDetailsBtn" class="btn">Notes</a>
                        <a href="#" id="paymentDetailsBtn" class="btn">Payment history</a>
                        <a href="#" id="leadEditBtn" class="btn right-btn highlight"><i class="fi-rr-edit"></i> Edit
                            lead</a>
                    </div>
                </div>
                <div class="widget no-padding box-1" id="leadDetailsBox">
                    <div class="leadInfo">
                        <h3 class="heading"><?php echo ucfirst(strtolower($enquiry_subject)) ?> - lead details</h3>
                        <table>
                            <?php
                    
                        if($enquiry_first_name!='') {
                            echo "<tr>
                                <td class='box-3'><strong>First name</strong></td>
                                <td class='box-7'>{$enquiry_first_name}</td>
                            </tr>";
                        }
                    
                        if($enquiry_last_name!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Last name</strong></td>
                                <td class='box-7'>{$enquiry_last_name}</td>
                            </tr>";
                        }
                    
                        if($enquiry_mobile!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Mobile</strong></td>
                                <td class='box-7'>{$enquiry_mobile}</td>
                            </tr>";
                        }
                    
                        if($enquiry_email!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Email</strong></td>
                                <td class='box-7'>{$enquiry_email}</td>
                            </tr>";
                        }
                    
                        if($enquiry_source!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Source</strong></td>
                                <td class='box-7'>{$enquiry_source}</td>
                            </tr>";
                        }
                    
                        if($enquiry_medium!='') {
                            echo "<tr>
                                <td class='box-3'><strong>Medium</strong></td>
                                <td class='box-7'>{$enquiry_medium}</td>
                            </tr>";
                        }
                    
                        $enquiryMessage = json_decode($enquiry_message, TRUE);
                        
                        foreach($enquiryMessage as $key => $value)  {
                            echo "<tr>
                                <td class='box-3'><strong>".str_replace('_', ' ', ucwords($key))."</strong></td>
                                <td class='box-7'>{$value}</td>
                            </tr>";
                        }

                    ?>
                        </table>
                    </div>
                </div>
                <div class="widget no-padding box-1" id="noteDetailsBox">
                    <div class="leadInfo">
                        <h3 class="heading">Follow ups history</h3>
                        <div class="historyBox">
                            <ol class="timeline">
                                <?php
                                
                                    if($leadHistory=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."enquiry_notes WHERE enquiry_notes_ref_id='{$leadId}'")) {
                                        if(mysqli_num_rows($leadHistory)!=0) {
                                            while($leadHistoryData = mysqli_fetch_array($leadHistory)) {
                                                extract($leadHistoryData); 
                                                $commentBox='';
                                                
                                                switch($enquiry_notes_meta_type) {
                                                    case "assign" :
                                                        $icon='link';
                                                        $msg=str_replace('Lead assign', '<a href="#">Lead assign</a>', $enquiry_notes_meta_value);
                                                        break;
                                                    case "payment" :
                                                        $icon='indian-rupee-sign';
                                                        $msg='<a href="#">Payment</a>'.$enquiry_notes_meta_value;
                                                        break;

                                                    default:
                                                        $msg= "<a href='#'>{$enquiry_notes_meta_type}</a>";
                                                        $icon='comment-alt';
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
                        <h3 class="heading">Payment history</h3>
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
                                    <td class="box-7"><input type="text" class="form-control" name="firstName"
                                            id="firstName" value="<?php echo $enquiry_first_name ?>" required></td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Middle name</strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="middleName"
                                            id="middleName" value="<?php echo $enquiry_middle_name ?>"></td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Last name</strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="lastName"
                                            id="lastName" value="<?php echo $enquiry_last_name ?>"></td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Email<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="email" id="email"
                                            value="<?php echo $enquiry_email ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Mobile<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="mobile" id="mobile"
                                            value="<?php echo $enquiry_mobile ?>" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Destination<sup>*</sup></strong></td>
                                    <td class="box-7">
                                        <select class="selectJS form-control" name="site_lang" id="select">
                                            <option value="">Select destination</option>
                                            <option value="himachal">Himachal</option>
                                            <option value="uttarakhand">Uttarakhand</option>
                                            <option value="jammu & kashmir">Jammu & Kashmir</option>
                                            <option value="ladakh">Ladakh</option>
                                            <option value="punjab">Punjab</option>
                                            <option value=""></option>
                                            <option value=""></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Date of travel<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control datepickerForFutureDate"
                                            name="dateOfTravel" id="dateOfTravel" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Duration of travel<span class="secondLine">( in Nights
                                                )</span><sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="travelDuration"
                                            id="travelDuration" placeholder="3" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Number of adults<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="noOfAdults"
                                            id="noOfAdults" placeholder="0" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Number of children<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="noOfChildrens"
                                            id="noOfChildrens" placeholder="0" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Number of infants<span class="secondLine">( Below 12
                                                years)</span><sup>*</sup></strong>
                                    </td>
                                    <td class="box-7"><input type="text" class="form-control" name="noOfInfants"
                                            id="noOfInfants" placeholder="0" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Current location<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="currentLocation"
                                            id="currentLocation" value="" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Pickup location<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="pickUpLocation"
                                            id="pickUpLocation" value="" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Drop location<span class="secondLine">( ignore if same
                                                )</span></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="dropLocation"
                                            id="dropLocation" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Any requirments</strong></td>
                                    <td class="box-7">
                                        <textarea name="requirments" class="form-control"
                                            id="requirments"><?php echo $enquiry_message ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3">
                                    </td>
                                    <td class="box-7">
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
                <a href="" class="btn btn-green" id="noteBtn">Final Interaction Note</a>
                <a href="" class="btn btn-green" id="paymentBtn">Send payment links</a>
                <a href="" class="btn btn-green" id="followUpBtn">Follow-ups</a>
                <a href="" class="btn btn-green" id="itineraryBtn">Send itinerary</a>
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