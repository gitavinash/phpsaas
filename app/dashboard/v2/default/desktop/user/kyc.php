<?php

$user_id = base64_decode($id);

if ($checkUser = mysqli_query($connection, "SELECT user_mobile, user_email, site_access_type, site_access_meta, profile_first_name, profile_last_name, profile_display_name, kyc_status FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id = user_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id LEFT JOIN " . TABLE_PREFIX . "kyc ON kyc_request_by=user_id WHERE  user_id = {$user_id}")) {

    if (mysqli_num_rows($checkUser) != 0) {
        extract(mysqli_fetch_assoc($checkUser));
        if ($kyc_status != '') {
            $kycUploaded = true;
        }
        $siteAccessMeta = json_decode($site_access_meta, TRUE);
    }
}

?>

<h2 class="heading">KYC<span>Users KYC details</span></h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Check users KYC</li>
        </ul>
        <div class="steps-box">
            <form method="POST" action="#">
                <section class="form">
                    <?php

                    if (isset($kycUploaded)) { ?>
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Update user's details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/domain-tlds.png" alt="Add new domain tld">
                        </div>
                        <div class="form-group">
                            <input type="text" name="first_name" id="first_name" placeholder="First name*"
                                class="form-control" value="<?php echo $profile_first_name ?>" autofocus required />
                            <label class="form-lable" id="first_nameLabel">First name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="first_name" id="first_name" placeholder="First name*"
                                class="form-control" value="<?php echo $profile_first_name ?>" autofocus required />
                            <label class="form-lable" id="first_nameLabel">First name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="user_id" value="<?php echo base64_encode($user_id) ?>">
                            <input type="hidden" name="type" value="user">
                            <input type="hidden" name="action" value="update">
                        </div>
                        <section class="form">
                    </div>
                    <?php
                    } else { ?>
                    <div class="step-open-box step_1_box">
                        <div class="form-header" style="    text-align: center;
    float: left;
    width: 100%;
    line-height: 38px;
    font-size: 24px;
    margin: 50px 0px">
                            <p style="padding: 50px 0px;">KYC documents are not uploaded by user</p>
                            <a style="float: left; width: 100%; text-align: center" class="btn btn-green"
                                id="sendKYCRequest" data-id="<?php echo $id ?>">Send KYC
                                request on Email</a>
                            <?php

                                // Define your variables
                                $companyName = "Your Company Name";
                                $kycLink = SITE_URL . "/kyc/?setup_id=" . base64_encode($user_email);
                                $phoneNumber = "1234567890"; // Phone number in international format, without "+" or leading zeros

                                // Create the message
                                $message = "*" . SITE_NAME . " Reminder:* Complete your KYC for a smoother experience! Click the link to update your details: $kycLink\n\nThank you!";

                                // Encode the message for URL
                                $encodedMessage = urlencode($message);

                                // Generate the WhatsApp link
                                $whatsappLink = "https://wa.me/{$user_mobile}?text=$encodedMessage";

                                ?>
                            <a href="<?php echo $whatsappLink ?>" target="_blank"
                                style="margin-top: 50px;float: left; width: 100%; text-align: center"
                                class="btn btn-green">Send
                                KYC
                                request on Whatsapp</a>
                        </div>
                        <?php
                    }

                        ?>
                </section>
            </form>
    </section>
    </form>
</div>
</section>
</div>