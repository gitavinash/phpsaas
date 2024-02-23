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
<h2 class="heading">Lead details<span>Complete information of lead</span>
</h2>
<div class="main-box">
    <section>

    </section>
</div>