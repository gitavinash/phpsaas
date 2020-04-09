<?php

  /* Category Table */

  $categoryTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."category
    CHANGE category_id category_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE category_parent_id category_parent_id bigint(20) NOT NULL,
    CHANGE category_image category_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE category_title category_title varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE category_description category_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE category_site_id category_site_id bigint(20) NOT NULL,
    CHANGE category_owner_id category_owner_id bigint(20) NOT NULL,
    CHANGE category_type category_type varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    CHANGE category_time category_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()");

  /* Device Table */
  
  $deviceTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    CHANGE device_id device_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE device_site_id device_site_id bigint(20) NOT NULL,
    CHANGE device_token device_token varchar(255) NOT NULL,
    CHANGE device_type device_type varchar(20) NOT NULL,
    CHANGE device_os device_os varchar(20) NOT NULL,
    CHANGE device_browser device_browser varchar(20) NOT NULL,
    CHANGE device_status device_status int(11) NOT NULL,
    CHANGE device_registration device_registration timestamp NOT NULL DEFAULT current_timestamp()");

  /* Directory Table */

  $directoryTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."directory
    CHANGE directory_id directory_id int(11) NOT NULL AUTO_INCREMENT,
    CHANGE directory_name directory_name varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE directory_path directory_path varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE directory_status directory_status int(1) NOT NULL,
    CHANGE directory_type directory_type varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE directory_creation directory_creation timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

  /* Enquiry Table  */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."enquiry(
    enquiry_id bigint(20) NOT NULL,
    enquiry_first_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    enquiry_last_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    enquiry_email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    enquiry_mobile varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    enquiry_query varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    enquiry_message LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    enquiry_type varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    enquiry_label varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    enquiry_status int(1) NOT NULL,
    enquiry_time timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."enquiry
    ADD PRIMARY KEY (enquiry_id),
    ADD INDEX (enquiry_time)");

  $enquiryTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."enquiry
    CHANGE enquiry_id enquiry_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE enquiry_first_name enquiry_first_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE enquiry_last_name enquiry_last_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE enquiry_email enquiry_email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE enquiry_mobile enquiry_mobile varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE enquiry_query enquiry_query varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE enquiry_message enquiry_message LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE enquiry_type enquiry_type varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE enquiry_label enquiry_label varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE enquiry_status enquiry_status int(1) NOT NULL,
    CHANGE enquiry_time enquiry_time timestamp NOT NULL DEFAULT current_timestamp()");

  /* Link Table */
  
  $linkTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."link
    CHANGE link_id link_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE link_url link_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE link_relation_id link_relation_id bigint(20) NOT NULL,
    CHANGE link_status link_status int(3) NOT NULL DEFAULT 404,
    CHANGE link_type link_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    CHANGE link_is_featured link_is_featured int(1) NOT NULL DEFAULT 0,
    CHANGE link_owner_id link_owner_id bigint(20) NOT NULL,
    CHANGE link_site_id link_site_id bigint(20) NOT NULL,
    CHANGE link_creation link_creation datetime NOT NULL,
    CHANGE link_published link_published datetime NOT NULL,
    CHANGE link_modified link_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");

  /* Media Table */
  
  $mediaTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."media
    CHANGE media_id media_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE media_url media_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE media_title media_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE media_status media_status int(1) NOT NULL,
    CHANGE media_type media_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE media_ext media_ext varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE media_owner_id media_owner_id bigint(20) NOT NULL,
    CHANGE media_site_id media_site_id bigint(20) NOT NULL");

  /* Meta Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."meta
    ADD KEY meta_type (meta_type)");

  $metaTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."meta
    CHANGE meta_id meta_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE meta_relation_id meta_relation_id bigint(20) NOT NULL,
    CHANGE meta_type meta_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    CHANGE meta_title meta_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE meta_description meta_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE meta_canonical meta_canonical varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE meta_keyword meta_keyword LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE meta_robots meta_robots varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE meta_geo_position meta_geo_position varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE meta_geo_place_name meta_geo_place_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE meta_geo_region meta_geo_region varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE fb_img fb_img varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE fb_title fb_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE fb_desc fb_desc LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE tw_img tw_img varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE tw_title tw_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE tw_desc tw_desc LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  /* Notification Table */

  $notificationTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."notification
    CHANGE notification_id notification_id int(11) NOT NULL AUTO_INCREMENT,
    CHANGE notification_site_id notification_site_id bigint(20) NOT NULL,
    CHANGE notification_icon notification_icon varchar(255) NOT NULL,
    CHANGE notification_title notification_title varchar(255) NOT NULL,
    CHANGE notification_image notification_image varchar(255) NOT NULL,
    CHANGE notification_description notification_description varchar(255) NOT NULL,
    CHANGE notification_link notification_link varchar(255) NOT NULL,
    CHANGE notification_type notification_type varchar(20) NOT NULL,
    CHANGE notification_sent notification_sent bigint(20) NOT NULL,
    CHANGE notification_droped notification_droped bigint(20) NOT NULL,
    CHANGE notification_status notification_status int(1) NOT NULL,
    CHANGE notification_time notification_time datetime NOT NULL");

  /* Profile Table */

  $profileTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."profile
    CHANGE profile_id profile_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE profile_owner_id profile_owner_id bigint(20) NOT NULL,
    CHANGE profile_image profile_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE profile_first_name profile_first_name varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE profile_last_name profile_last_name varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE profile_display_name profile_display_name varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE profile_mobile profile_mobile varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE profile_alt_mobile profile_alt_mobile varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE profile_email profile_email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE profile_alt_email profile_alt_email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  /* Site Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."
    CHANGE site_keyword site_keyword LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  $siteTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    CHANGE site_id site_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE site_owner_id site_owner_id bigint(20) NOT NULL,
    CHANGE site_lang site_lang varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
    CHANGE site_domain site_domain varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_url site_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_icon site_icon varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_logo site_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_name site_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_description site_description varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_app site_app varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cms',
    CHANGE site_theme site_theme varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
    CHANGE site_indexing site_indexing int(1) NOT NULL,
    CHANGE site_status site_status int(1) NOT NULL,
    CHANGE site_expire_date site_expire_date date NOT NULL");

  /** Site Access Table */

  $siteAccessTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_access
    CHANGE site_access_id site_access_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE site_access_site_id site_access_site_id bigint(20) NOT NULL,
    CHANGE site_access_user_id site_access_user_id bigint(20) NOT NULL,
    CHANGE site_access_status site_access_status int(1) NOT NULL");

  /** Site Setting Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD KEY site_custom_domain (site_custom_domain),
    ADD site_custom_domain int(1) NOT NULL AFTER site_setting_id");

  $siteSettingTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    CHANGE site_setting_id site_setting_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE site_pagination_limit site_pagination_limit bigint(20) NOT NULL,
    CHANGE site_sitemap_limit site_sitemap_limit bigint(20) NOT NULL,
    CHANGE site_feed_limit site_feed_limit bigint(20) NOT NULL,
    CHANGE site_image_quality site_image_quality bigint(20) NOT NULL,
    CHANGE site_thumbnail_width site_thumbnail_width varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_thumbnail_height site_thumbnail_height varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    ADD site_ads_txt LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    ADD site_mainfest_json LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    ADD site_robots_txt LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  /* Tag Table */

  $tagTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."tag
    CHANGE tag_id tag_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE tag_title tag_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE tag_image tag_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE tag_description tag_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL,
    CHANGE tag_relation_id tag_relation_id bigint(20) NOT NULL,
    CHANGE tag_owner_id tag_owner_id bigint(20) NOT NULL,
    CHANGE tag_site_id tag_site_id bigint(20) NOT NULL,
    CHANGE tag_type tag_type VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    CHANGE tag_time tag_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

  /* User Table */

  $userTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."user
    CHANGE user_id user_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE user_parent_id user_parent_id bigint(20) NOT NULL,
    CHANGE user_username user_username varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE user_email user_email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE user_password user_password varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE user_salt_key user_salt_key varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE user_type user_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE user_status user_status int(1) NOT NULL DEFAULT 0,
    CHANGE user_reg_time user_reg_time datetime NOT NULL,
    CHANGE user_act_time user_act_time datetime NOT NULL,
    CHANGE user_reg_ip user_reg_ip varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE user_refer_id user_refer_id bigint(20) NOT NULL");
 

  ########################################################################################################################
  ##                                                                                                                  ##
  ##                                         CMS Applications Database Table                                            ##
  ##                                                                                                                    ##
  ########################################################################################################################

  
  /* Post Table */

  $postTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."post
    CHANGE post_id post_id bigint(20) NOT NULL AUTO_INCREMENT");

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                         News CMS Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################


  /* News Article Table */

  $newsTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."news
    CHANGE news_id news_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE news_parent_id news_parent_id bigint(20) NOT NULL,
    CHANGE news_image news_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE news_thumbnail news_thumbnail varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE news_title news_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE news_description news_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE news_highlight news_highlight LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE news_category news_category bigint(20) NOT NULL,
    CHANGE news_owner_id news_owner_id bigint(20) NOT NULL,
    CHANGE news_status news_status int(1) NOT NULL,
    CHANGE news_type news_type varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'news',
    CHANGE news_time news_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");


  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                         Election Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################

  /* Alliance Party Table */

  $alliancePartyTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."alliance_party
    CHANGE alliance_id alliance_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE alliance_election alliance_election bigint(20) NOT NULL,
    CHANGE alliance_code alliance_code varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE alliance_name alliance_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE alliance_slogan alliance_slogan varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE alliance_logo alliance_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE alliance_party alliance_party varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'List of all alliance parties',
    CHANGE alliance_year alliance_year year(4) NOT NULL");

  /* Candidate Table */

  $candidateTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."candidate
    CHANGE candidate_id candidate_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE candidate_name candidate_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE candidate_photo candidate_photo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE candidate_party candidate_party bigint(20) NOT NULL,
    CHANGE candidate_election candidate_election bigint(20) NOT NULL,
    CHANGE candidate_constituency candidate_constituency bigint(20) NOT NULL,
    CHANGE candidate_result candidate_result int(1) NOT NULL");

  /* Constituency Table */

  $constituencyTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."constituency
    CHANGE constituency_id constituency_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE constituency_name constituency_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE constituency_state constituency_state varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE constituency_type constituency_type varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Election Id of election table',
    CHANGE constituency_year constituency_year year(4) NOT NULL");

  /* Election Table */

  $electionTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."election
    CHANGE election_id election_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE election_name election_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE election_state election_state varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE election_party_1 election_party_1 bigint(20) NOT NULL,
    CHANGE election_party_2 election_party_2 bigint(20) NOT NULL,
    CHANGE election_party_3 election_party_3 bigint(20) NOT NULL,
    CHANGE election_party_4 election_party_4 bigint(20) NOT NULL,
    CHANGE election_party_5 election_party_5 bigint(20) NOT NULL,
    CHANGE election_party_all election_party_all bigint(20) NOT NULL,
    CHANGE election_total_seats election_total_seats bigint(20) NOT NULL,
    CHANGE election_type election_type varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE election_date election_date date NOT NULL,
    CHANGE election_result_date election_result_date date NOT NULL,
    CHANGE election_year election_year year(4) NOT NULL");

  /* Party Table */

  $partyTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."party
    CHANGE party_id party_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE party_code party_code varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE party_name party_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE party_slogan party_slogan varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE party_logo party_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  /* Result Table */

  $resultTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result
    CHANGE result_id result_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE result_election_id result_election_id bigint(20) NOT NULL,
    CHANGE result_party_1 result_party_1 int(11) NOT NULL,
    CHANGE result_party_2 result_party_2 int(11) NOT NULL,
    CHANGE result_party_3 result_party_3 int(11) NOT NULL,
    CHANGE result_party_4 result_party_4 int(11) NOT NULL,
    CHANGE result_party_5 result_party_5 int(11) NOT NULL,
    CHANGE result_party_all result_party_all bigint(20) NOT NULL,
    CHANGE result_status result_status int(1) NOT NULL");

  /* Result Summary Table */

  $resultSummaryTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result_summary
    CHANGE result_summary_id result_summary_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE result_summary_election_id result_summary_election_id bigint(20) NOT NULL,
    CHANGE result_summary_constituency result_summary_constituency bigint(20) NOT NULL,
    CHANGE result_summary_winner_party result_summary_winner_party bigint(20) NOT NULL,
    CHANGE result_summary_status result_summary_status int(1) NOT NULL");

  if($categoryTable && $deviceTable && $directoryTable && $enquiryTable && $linkTable && $mediaTable && $metaTable && $notificationTable && $profileTable && $siteTable && $siteAccessTable && $siteSettingTable && $tagTable && $userTable && $postTable && $newsTable && $alliancePartyTable && $candidateTable && $constituencyTable && $electionTable && $partyTable && $resultTable && $resultSummaryTable) {

      /* commit all query */

      mysqli_query($connection, "COMMIT");

      $status=200;
      $msg='Database has been updated successfully';

  } else {

    echo $postTable.' && '.$newsTable.' && '.$alliancePartyTable.' && '.$candidateTable.' && '.$constituencyTable.' && '.$electionTable.' && '.$partyTable.' && '.$resultTable.' && '.$resultSummaryTable;
    exit();

  }

  $result = array(
    'status' => ((isset($status)) ? $status : 206 ),
    'msg' => ((isset($msg)) ? $msg : 'Something is wrong while updating database' )
  );

  echo json_encode($result, TRUE);