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
                            <img src="<?php echo IMG_LIBRARY ?>profile_icon/<?php echo strtolower((($enquiry_first_name[0]!=' ')? $enquiry_first_name[0] : 'unknown')) ?>.png"
                                alt="" srcset="">
                        </div>
                        <div class="info">
                            <h2 class="heading"><?php echo $enquiry_first_name.' '.$enquiry_last_name ?></h2>
                            <p><span>New client</span></p>
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
                            profile</a>
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
                    </div>
                </div>
                <div class="widget no-padding box-1" id="paymentDetailsBox">
                    <div class="leadInfo">
                        <h3 class="heading">Payment history</h3>
                    </div>
                </div>
                <div class="widget no-padding box-1" id="leadEditBox">
                    <div class="leadInfo">
                        <h3 class="heading">Edit lead details</h3>
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
                                    <select class="selectJS form-control" name="title">
                                        <option value="Not interested">Not Interested</option>
                                        <option value="Purchased Elsewhere">Purchased Elsewhere</option>
                                        <option value="Unqualified Lead">Unqualified Lead</option>
                                        <option value="Fake Enquiry">Fake Enquiry</option>
                                        <option value="Client is not Picking call">Client is not Picking call</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="noteMsg" id="noteMsg" placeholder="Message*"
                                        class="form-control" required></textarea>
                                    <label class="form-lable" id="noteMsgLabel">Message<sup>*</sup></label>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn"><i class="fi-rr-check"></i> Submit</a>
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