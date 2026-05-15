 <?php

    if ($checkUser = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "site WHERE site_status=1")) {
        if (mysqli_num_rows($checkUser) == 0) {
            echo '<h1>invalid request found</h1>';
        } else {
            extract(mysqli_fetch_assoc($checkUser));
        }
    }

    ?>
 <h2 class="heading">Setting Configuration<span>Manage basic configurations</span></h2>
 <div class="main-box">
     <section>
         <ul class="steps-tab">
             <li class="step-box active" id="step_1"><span>1</span>Website configuration</li>
         </ul>
         <div class="steps-box">
             <form action="#" id="websiteData">
                 <section class="form">
                     <div class="step-open-box step_1_box">
                         <div class="form-header">
                             <h1>Configuration</h1>
                             <img src="<?php echo IMG_LIBRARY ?>storyset/domain-tlds.png" alt="Add new domain tld">
                         </div>
                         <div class="form-group">
                             <input class="form-control" id="websiteName" value="<?php echo $site_name; ?>"
                                 name="site_name" type="text" />
                             <label class="form-lable" id="websiteNameLabel">Website Name <sup>*</sup></label>
                         </div>
                         <div class="form-group">
                             <textarea class="form-control" id="websiteDescription" name="site_description"
                                 type="text"><?php echo $site_description ?></textarea>
                             <label class="form-lable" id="websiteDescriptionLabel">About Website Name
                                 <sup>*</sup></label>
                         </div>
                         <div class="form-group">
                             <textarea class="form-control" id="websiteKeyword" name="site_keyword"
                                 type="text"><?php echo $site_keyword ?></textarea>
                             <label class="form-lable" id="websiteKeywordLabel">Website Keywords ( Use , to seprate
                                 Keywords )
                                 <sup>*</sup></label>
                         </div>
                         <div class="form-group">
                             <p>Select website language<sup>*</sup></p>
                             <select class="selectJS form-control" name="site_lang" id="select">
                                 <option value="">Select language</option>
                                 <option value="hi_IN" <?php echo (($site_lang == 'hi_IN') ? 'selected' : '') ?>>हिंदी
                                 </option>
                                 <option value="en" <?php echo (($site_lang == 'en') ? 'selected' : '') ?>>English
                                 </option>
                             </select>
                         </div>
                         <div class="form-group-box">
                             <p>Allow Search Engine to index site</p>
                             <div class="box-5 left p-right-10">
                                 <div class="form-group">
                                     <div class="input-box">
                                         <input type="radio" id="radio1" name="site_index" value="index, follow"
                                             <?php echo (($site_index == 'index, follow') ? 'checked' : '') ?> />
                                         <label class="radio-button" for="radio1Lable">Yes</label>
                                     </div>
                                 </div>
                             </div>
                             <div class="box-5 left p-left-10">
                                 <div class="form-group">
                                     <div class="input-box">
                                         <input type="radio" id="radio2" name="site_index" value="no"
                                             <?php echo (($site_index != 'index, follow') ? 'checked' : '') ?> />
                                         <label class="radio-button" for="radio2Lable">No</label>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="form-group">
                             <input type="hidden" name="type" value="setting">
                             <input type="hidden" name="action" value="configuration">
                             <input type="hidden" name="site_theme" value="#00657B">
                             <input type="hidden" name="website_id" value="<?php echo SITE_ID ?>">
                             <input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>">
                             <input type="button" id="updateWebsite" class="btn btn-green" value="Update Website">
                         </div>
             </form>
     </section>
     </form>
 </div>
 </section>
 </div>