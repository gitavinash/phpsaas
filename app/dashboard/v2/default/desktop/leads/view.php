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

<div class="widget box-7-5">
    <div class="main-box">
        <section>
            <div class="widget box-1">
                <div class="leadProfileCard">
                    <div class="img">
                        <img src="<?php echo IMG_LIBRARY ?>profile_icon/<?php echo $enquiry_first_name[0] ?>.png" alt=""
                            srcset="">
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
            <div class="widget box-1">
                <div class="btn-group leadActions">
                    <a href="#" id="leadDetailsBtn" class="btn btn-green">Details</a>
                    <a href="#" id="noteDetailsBtn" class="btn">Notes</a>
                    <a href="#" id="paymentDetailsBtn" class="btn">Payment history</a>
                </div>
            </div>
            <div class="widget box-1" id="leadDetails">
                <div class="leadInfo"></div>
            </div>
            <div class="widget box-1" id="noteDetails">
                <div class="leadInfo"></div>
            </div>
            <div class="widget box-1" id="paymentDetails">
                <div class="leadInfo"></div>
            </div>
        </section>
    </div>
</div>

<div class="widget box-2-5">
    <div class="main-box">
        <section class="leadQuickLinks">
            <a href="" class="btn btn-green" id="noteBtn">Add final notes</a>
            <a href="" class="btn btn-green" id="paymentBtn">Send payment links</a>
            <a href="" class="btn btn-green" id="followUpBtn">Follow-ups</a>
            <a href="" class="btn btn-green" id="intenaryBtn">Send itinerary</a>
        </section>
    </div>
</div>

<div class="popUp notesBox">
    <div class="smallPopUpBox">
        <div class="widget box-7">
            <div class="contentArea">

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

            </div>
        </div>
        <div class="widget box-3">
            <div class="actionArea">
                <a href="#" class="btn popUpBtn"><i class="fi-rr-check"></i> Submit</a>
                <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i> Cancel</a>
            </div>
        </div>
    </div>
</div>

<div class="popUp followUpBox">
    <div class="smallPopUpBox">
        <div class="widget box-7">
            <div class="contentArea">

            </div>
        </div>
        <div class="widget box-3">
            <div class="actionArea">
                <a href="#" class="btn popUpBtn"><i class="fi-rr-check"></i> Submit</a>
                <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i> Cancel</a>
            </div>
        </div>
    </div>
</div>

<div class="popUp intenaryBox">
    <div class="fullScreenPopUpBox">
        <div class="widget box-8">
            <div class="contentArea">
                <h4 class="heading"><?php echo $enquiry_subject ?></span></h2>
            </div>
        </div>
        <div class="widget box-2">
            <div class="actionArea">
                <a href="#" class="btn popUpBtn"><i class="fi-rr-check"></i> Submit</a>
                <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i> Cancel</a>
            </div>
        </div>
    </div>
</div>