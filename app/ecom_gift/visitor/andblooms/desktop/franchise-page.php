<?php

    include_once 'header.php';

?>


<!-- header end -->
<section class="banner-section pro-galery">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="brdcrum">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $page_title ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="account-backend">
                    <h2 class="inter-heading cont-hed" style="float:left; width: 100%; text-align: center">
                        <?php echo $page_title ?></h2>
                    <?php echo $page_description; ?>
                    <form class="franchise" action="/enquiry/" method="POST">
                        <div class="form-group">
                            <label>Full Name*</label>
                            <input type="text" name="name" placeholder="Enter your name*" required>
                        </div>
                        <div class="form-group">
                            <label>Email*</label>
                            <input type="text" name="email" placeholder="Enter your email*" required>
                        </div>
                        <div class="form-group">
                            <label>Mobile No.</label>
                            <input type="text" name="mobile" placeholder="Enter your mobile*" required>
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" placeholder="Message"></textarea>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="type" value="enquiry">
                            <input type="hidden" name="action" value="franchise">
                            <input type="hidden" name="redirect_url" value="/thank-you/?form_type=franchise_form">
                            <!-- <input type="hidden" name="sender" value="<?php echo base64_encode('sd@andblooms.com') ?>"> -->
                            <input type="hidden" name="sender"
                                value="<?php echo base64_encode('avinashsharma114@gmail.com') ?>">
                            <input type="submit" class="btn-bloom purchase-btn" value="Send">
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>

<?php

    include_once 'footer.php';

?>