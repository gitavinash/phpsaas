<h2>Website Configuration</h2>

<div class="form-box">
	<div class="configMenu">
        <ul>
            <li id="configurationBtn" class="active" >General</li>
            <li id="adsTab">Ads.txt</li>
            <li id="robotsTab">Robots.txt</li>
            <li id="mainfestTab">Mainfest.json</li>
        </ul>
    </div>
    <div class="configBody">
        <div id="configurationTabBox" >
            
            <!-- General Configuration section -->

        <?php

            if($checkUser=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."user, ".TABLE_PREFIX."site WHERE user_id='".USER_ID."' AND site_owner_id='".USER_ID."' AND user_status=1")) {
                if(mysqli_num_rows($checkUser)==0) {
                    echo '<h1>invalid request found</h1>';
                } else {
                    
                    extract(mysqli_fetch_assoc($checkUser));

                    ?>

                    <div class="form-box" style="margin: 0 auto" >
                        <div class="box-2">
                            <h3>Manage Website Basic Configurations</h3>
                            <form id="websiteData" >
                                <?php
                                
                                if($site_domain!='') { ?>
                                    <div class="form-group">
                                        <label>Website Domain <span>( Please do not change. If it is not necessary )</span></label>
                                        <input type="text" name="site_domain" value='<?php echo $site_domain ?>' class="form-control inside-shadow" placeholder="Website Domain" readonly >
                                    </div>
                                    <!-- <div class="form-group">
                                        <label>Actual Website URL*</label>
                                        <input type="text" name="site_url" value='<?php echo (($site_url=='') ? $site_domain : $site_url ) ?>' class="form-control inside-shadow" placeholder="Website URL" readonly >
                                    </div> -->
                                <?php
                                } else { ?>
                                    <div class="form-group">
                                        <label>Website Domain <span>( Please do not change. If it is not necessary )</span></label>
                                        <input type="text" name="site_domain" value='<?php echo $site_domain ?>' class="form-control inside-shadow" placeholder="Website Domain" readonly >
                                    </div>
                                <?php
                                }

                                ?>
                                <div class="form-group">
                                    <label>Website Name*</label>
                                    <input type="text" id="websiteName" value="<?php echo $site_name; ?>" name="site_name" class="form-control inside-shadow" placeholder="First Name" required >
                                </div>
                                <div class="form-group">
                                    <label>About Website*</label>
                                    <textarea name="site_description" id="websiteDescription" style="height: 100px; background: none" class="form-control inside-shadow" placeholder="About Website ( Recommendation Max 170 Characters )" ><?php echo $site_description ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Website Keywords ( Use , to seprate Keywords )</label>
                                    <textarea name="site_keyword" id="websiteKeyword" style="height: 100px; background: none" class="form-control inside-shadow" placeholder="Website Keywords ( Use , to seprate Keywords )" ><?php echo $site_keyword ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Website Theme Color</label>
                                    <div id="color-drop">
                                        <input type="color" name="site_theme" value="<?php echo SITE_THEME_COLOUR ?>" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Select website language*</label>
                                    <select name="site_lang" id="siteLang" class="form-control inside-shadow" required >
                                        <option value="">Select Language</option>
                                        <option value="hi_IN" <?php echo (($site_lang=='hi_IN') ? 'selected' : '' ) ?> >हिंदी</option>
                                        <option value="en" <?php echo (($site_lang=='en') ? 'selected' : '' ) ?>  >English</option>
                                    </select>
                                </div>
                                <div class="form-check" style="margin-bottom: 20px;">
                                    <input type="checkbox" name="site_index" value="noindex, nofollow" class="form-check-input" <?php echo (($site_index!='') ? 'checked' : '' ) ?> >
                                    <label class="form-check-label" >Allow Search Engine to index site</label>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="type" value="setting" >
                                    <input type="hidden" name="action" value="configuration" >
                                    <input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
                                    <input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
                                    <input type="submit" id="updateWebsite" class="btn btn-green" value="Update Website" >
                                </div>
                            </form>
                        </div>

                        <div class='box-2'>

                            <form id="updateSiteLogoForm">
                                <h3>Website Logo</h3>
                                <div class="siteLogoPreviewBox" style="display: <?php echo ((SITE_LOGO!='') ? 'block' : 'none' ) ?>;" >
                                    <div class="img"><img src="<?php echo ((SITE_LOGO!='') ? SITE_LOGO : '' ) ?>" id="siteLogoPath" alt=""></div>
                                    <input type="hidden" name="type" value="setting" >
                                    <input type="hidden" name="action" value="logo" >
                                    <input type="button" id="updateSiteLogo" class="btn btn-green btn-sm" style="display:none" value="Update Website Logo" >
                                    <input type="button" id="siteLogoChangeImage" name="updateSiteLogo" class="btn btn-red btn-sm" value="Change Site Logo" >
                                </div>
                                <div class="siteLogoUploadBox" id="siteLogo" style="display: <?php echo ((SITE_LOGO=='') ? 'block' : 'none' ) ?>;" >
                                    <span>Add Website logo</span>
                                    <input type='file' id="siteLogoImage" name='siteLogo' />
                                    <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif</p>
                                </div>
                            </form>

                            <form id="updateSiteIconForm">
                                <h3>Website Icon</h3>
                                <div class="siteIconPreviewBox" style="display: <?php echo ((SITE_ICON!='') ? 'block' : 'none' ) ?>;" >
                                    <div class="img"><img src="<?php echo ((SITE_ICON!='') ? SITE_ICON : '' ) ?>" id="siteIconPath" alt=""></div>
                                    <input type="hidden" name="type" value="setting" >
                                    <input type="hidden" name="action" value="icon" >
                                    <input type="button" id="updateSiteIcon" class="btn btn-green btn-sm"  value="Update Website Icon" style="display:none" >
                                    <input type="button" id="siteIconChangeImage" name="updateSiteIcon" class="btn btn-red btn-sm" value="Change Site Icon" >
                                </div>
                                <div class="siteIconUploadBox" style="display: <?php echo ((SITE_ICON=='') ? 'block' : 'none' ) ?>;" >
                                    <span>Add Website Icon</span>
                                    <input type='file' id="siteIconImage" name='siteIcon' />
                                    <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif ( Recommend Size : 512x512 )</p>
                                </div>
                            </form>

                        </div>

                    </div>


            <?php
            }
            }

        ?>
            
        </div>
        <div id="adsTabBox" style="display: none" >

            <!-- Ads.txt section -->
            
            <h3>Manage ads.txt file</h3>
            <p style="font-size: 14px;" ><span>Authorized Digital Seller (abbreviated as Ads.txt) is a list of all partners (including publishers) authorized to sell digital inventory. To know more <a href="https://clearcode.cc/blog/ads-txt/" title="" target="_blank" style="text-decoration: none" >Click Here</a></span></p>
            <form>
                <textarea name="ads_txt" id="ads_txt" ><?php echo $site_ads_txt; ?></textarea>
                <input type="button" id="updateAdsTxt" class="btn btn-green" value="Update File" >
            </form>

        </div>
        <div id="robotsTabBox" style="display: none" >
            
            <!-- Robots.txt section -->

            <h3>Manage robots.txt file</h3>
            <p style="font-size: 14px;" ><span>A <strong>robots.txt</strong> file tells search engine crawlers which pages or files the crawler can or can't request from your site. Know more about robots.txt specifications. <a href="https://developers.google.com/search/reference/robots_txt" target="_blank" style="text-decoration: none" >Click Here</a></span></p>
            <form>
                <textarea name="robots_txt" id="robots_txt" ><?php echo (($site_robots_txt!='') ? $site_robots_txt : DEFAULT_ROBOTS_TXT ); ?></textarea>
                <input type="button" id="updateRobotTxt" class="btn btn-green" value="Update File" >
            </form>
            
        </div>
        <div id="mainfestTabBox" style="display: none" >
            
            <!-- Mainfest.json section -->

            <h3>Manage mainfest.json file</h3>
            <p style="font-size: 14px; color: red" ><span>Please do not touch this section if you dont have knowledge about <strong>Mainfest.json</strong>.</span> <span style="color: #333" >Please use this link to check your code validation <a href="https://jsonlint.com/" target="_blank" style="text-decoration: none" >Click Here</a></span></p>
            <form>
                
                <?php
                
                    if($site_mainfest_json=='') {

                        /** Default mainfest.json */

                        $site_mainfest_json=DEFAULT_MAINFEST_JSON;

                    }

                ?>

                <textarea name="mainfest_json" id="mainfest_json" ><?php echo json_decode($site_mainfest_json, TRUE); ?></textarea>
                <input type="button" id="updateMainfestJson" class="btn btn-green" value="Update File" >
            </form>
            
        </div>
    </div>
</div>