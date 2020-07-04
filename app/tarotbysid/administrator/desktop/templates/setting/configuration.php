<h2>Website Configuration</h2>

<div class="form-box">
	<div class="configMenu">
        <ul>
            <li id="pos1" class="active" >General</li>
            <li id="pos2">Ads.txt</li>
            <li id="pos3">Robots.txt</li>
            <li id="pos4">Mainfest.json</li>
        </ul>
    </div>
    <div class="configBody">
        <div id="pos1Box">
            
            <!-- General Configuration section -->

        <?php

            if($checkUser=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."user, ".TABLE_PREFIX."site WHERE user_id='".USER_ID."' AND site_owner_id='".USER_ID."' AND user_status=1")) {
                if(mysqli_num_rows($checkUser)==0) {
                    echo '<h1>invalid request found</h1>';
                } else {
                    
                    extract(mysqli_fetch_assoc($checkUser));

                    ?>

                    <div class="form-box" style="margin: 0 auto" >
                        <h3>Manage Website Basic Configurations</h3>
                        <form method="POST" id="websiteData" enctype="multipart/form-data" >
                            <div class="box-2">
                                <div class="form-group">
                                    <label>Website Domain <span>( Please do not change. If it is not necessary )</span></label>
                                    <input type="text" name="site_domain" value='<?php echo HTTP_PROTOCOL.$site_domain ?>' class="form-control inside-shadow" placeholder="Website Domain" <?php echo (($user_id==1) ? 'readonly' : '' ) ?> >
                                </div>
                                <div class="form-group">
                                    <label>Actual Website URL</label>
                                    <input type="text" name="site_url" value='<?php echo HTTP_PROTOCOL.(($site_url=='') ? $site_domain : $site_url ) ?>' class="form-control inside-shadow" placeholder="Website URL" <?php echo (($user_id==1) ? 'readonly' : '' ) ?> >
                                </div>
                                <div class="form-group">
                                    <label>Website Name</label>
                                    <input type="text" value="<?php echo $site_name; ?>" name="name" class="form-control inside-shadow" placeholder="First Name" required >
                                </div>
                                <div class="form-group">
                                    <label>About Website</label>
                                    <textarea name="description" style="height: 100px;" class="form-control inside-shadow" placeholder="About Website ( Recommendation Max 170 Characters )" ><?php echo $site_description ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Website Keywords ( Use , to seprate Keywords )</label>
                                    <textarea name="keyword" style="height: 100px;" class="form-control inside-shadow" placeholder="Website Keywords ( Use , to seprate Keywords )" ><?php echo $site_keyword ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Select website language</label>
                                    <select name="site_lang" class="form-control inside-shadow" required >
                                        <option value="">Language list</option>
                                        <option value="hi_IN" <?php echo (($site_lang=='hi_IN') ? 'selected' : '' ) ?> >हिंदी</option>
                                        <option value="en" <?php echo (($site_lang=='en') ? 'selected' : '' ) ?>  >English (United States)</option>
                                        <option value="en_AU" <?php echo (($site_lang=='en_AU') ? 'selected' : '' ) ?>  >English (Australia)</option>
                                        <option value="en_AU" <?php echo (($site_lang=='en_AU') ? 'selected' : '' ) ?>  >English (South Africa)</option>
                                        <option value="en_CA" <?php echo (($site_lang=='en_CA') ? 'selected' : '' ) ?>  >English (Canada)</option>
                                        <option value="en_GB" <?php echo (($site_lang=='en_GB') ? 'selected' : '' ) ?>  >English (UK)</option>
                                        <option value="en_NZ" <?php echo (($site_lang=='en_NZ') ? 'selected' : '' ) ?>  >English (New Zealand)</option>
                                    </select>
                                </div>
                                <div class="form-check" style="margin-bottom: 20px;">
                                    <input type="checkbox" name="site_index" value="1" class="form-check-input" <?php echo (($site_index==1) ? 'checked' : '' ) ?> >
                                    <label class="form-check-label" for="exampleCheck1" >Allow Search Engine to index site</label>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="type" value="<?php echo $postType ?>" >
                                    <input type="hidden" name="action" value="website" >
                                    <input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
                                    <input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
                                    <input type="submit" id="updateWebsite" class="btn btn-green" value="Update Website" >
                                </div>
                            </div>
                            <div class='box-2'>
                                <form id="updateSiteLogoForm" >
                                    <h3>Website Logo</h3>
                                    <div class="siteLogoPreviewBox" style="display: <?php echo (($site_logo=='') ? 'none' : 'block' ) ?>;">
                                        <div class="img"><img src="<?php echo $site_logo ?>" id="siteLogoPath" alt=""></div>
                                        <input type="hidden" name="type" value="api" >
                                        <input type="hidden" name="action" value="logo" >
                                        <input type="button" id="updateSiteLogo" class="btn btn-green btn-sm"  value="Update Website Logo" >
                                        <input type="button" id="siteLogoChangeImage" name="updateSiteLogo" class="btn btn-red btn-sm" value="Change Site Logo" >
                                    </div>
                                    <div class="siteLogoUploadBox" id="siteLogo" style="display: <?php echo (($site_logo!='') ? 'none' : 'block' ) ?>;">
                                        <span>Add Website logo</span>
                                        <input type='file' id="siteLogoImage" name='siteLogo' />
                                        <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif</p>
                                    </div>
                                </form>
                                <form id="updateSiteIconForm" >
                                    <h3>Website Icon</h3>
                                    <div class="siteIconPreviewBox" style="display: <?php echo (($site_icon=='') ? 'none' : 'block' ) ?>;">
                                        <div class="img"><img src="<?php echo $site_icon ?>" id="siteIconPath" alt=""></div>
                                        <input type="button" id="updateSiteIcon" class="btn btn-green btn-sm"  value="Update Website Icon" >
                                        <input type="button" id="siteIconChangeImage" name="updateSiteIcon" class="btn btn-red btn-sm" value="Change Site Icon" >
                                    </div>
                                    <div class="siteIconUploadBox" style="display: <?php echo (($site_icon!='') ? 'none' : 'block' ) ?>;">
                                        <span>Add Website Icon</span>
                                        <input type='file' id="siteIconImage" name='siteIcon' />
                                        <input type="hidden" name="type" value="api" >
                                        <input type="hidden" name="action" value="setting" >
                                        <input type="hidden" name="siteAction" value="site_icon" >
                                        <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif ( Recommend Size : 512x512 )</p>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>

            <?php
            }
            }

        ?>
            
        </div>
        <div id="pos2Box" style="display: none" >

            <!-- Ads.txt section -->
            
            <h3>Manage ads.txt file</h3>
            <form enctype="multipart/form-data" >
                <textarea name="ads_txt" id="ads_txt" ><?php echo $site_ads_txt; ?></textarea>
                <input type="button" id="adsTXT" class="btn btn-green" value="Update File" >
            </form>

        </div>
        <div id="pos3Box" style="display: none" >
            
            <!-- Robots.txt section -->

            <h3>Manage robots.txt file</h3>
            <form enctype="multipart/form-data" >
                <textarea name="robots_txt" id="robots_txt" ><?php echo $site_robots_txt; ?></textarea>
                <input type="button" id="robotsTXT" class="btn btn-green" value="Update File" >
            </form>
            
        </div>
        <div id="pos4Box" style="display: none" >
            
            <!-- Mainfest.json section -->

            <h3>Manage mainfest.json file</h3>
            <form enctype="multipart/form-data" >
                <textarea name="mainfest_json" id="mainfest_json" ><?php echo json_decode($site_mainfest_json); ?></textarea>
                <input type="button" id="mainfestJSON" class="btn btn-green" value="Update File" >
            </form>
            
        </div>
    </div>
</div>