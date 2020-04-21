<?php

  /** Category Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."category
    ADD COLUMN category_status INT(1) NOT NULL AFTER category_type");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."category
    CHANGE category_type category_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  /* Device Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    ADD COLUMN device_category BIGINT(20) NOT NULL AFTER device_token");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    ADD COLUMN device_user_id BIGINT(20) NOT NULL AFTER device_site_id");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    ADD INDEX device_user_id (device_user_id)");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    DROP INDEX device_token,
    ADD INDEX device_token (device_token)");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    DROP INDEX device_category");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    ADD INDEX device_category (device_category)");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    CHANGE device_status device_status INT(1) NOT NUll");

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
      ADD INDEX enquiry_type (enquiry_type),
      ADD INDEX enquiry_time (enquiry_time)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."enquiry
    CHANGE enquiry_id enquiry_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Link Table */
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."link
    CHANGE link_url link_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE link_status link_status int(3) NOT NULL DEFAULT 404");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."link
    DROP INDEX link_status,
    DROP INDEX link_type,
    DROP INDEX link_published,
    DROP INDEX link_relation_id,
    DROP INDEX link_site_id,
    DROP INDEX link_owner_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."link
    ADD INDEX link_filter (link_relation_id, link_status, link_type, link_site_id, link_published)");

  /* Media Table */
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."media
    CHANGE media_url media_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE media_title media_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  /* Meta Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."meta
    DROP INDEX meta_type");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."meta
    CHANGE meta_canonical meta_canonical varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE meta_geo_place_name meta_geo_place_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE meta_geo_region meta_geo_region varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE fb_img fb_img varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE tw_img tw_img varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    ADD KEY meta_type (meta_type)");

  /** Notification Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."notification
    ADD COLUMN notification_owner_id bigint(20) NOT NULL AFTER notification_site_id,
    ADD COLUMN notification_group_key varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER notification_owner_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."notification
    CHANGE notification_sent notification_success bigint(20) NOT NULL,
    CHANGE notification_droped notification_failed bigint(20) NOT NULL");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."notification
    DROP INDEX notification_owner_id,
    DROP INDEX notification_group_key");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."notification
    ADD INDEX (notification_owner_id),
    ADD INDEX (notification_group_key)");

  /* Page Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."page(
    page_id bigint(20) NOT NULL,
    page_parent_id bigint(20) NOT NULL,
    page_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    page_thumbnail varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    page_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    page_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    page_highlight LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    page_category bigint(20) NOT NULL,
    page_owner_id bigint(20) NOT NULL,
    page_status int(1) NOT NULL,
    page_type varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'page',
    page_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."page
    DROP INDEX page_category,
    DROP INDEX page_type");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."page
    ADD PRIMARY KEY (page_id)");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."page
    ADD KEY page_category (page_category),
    ADD INDEX (page_type)");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."page
    MODIFY page_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Profile Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."profile
    CHANGE profile_email profile_email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE profile_alt_email profile_alt_email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  /* Site Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    CHANGE site_domain site_domain varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_url site_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_icon site_icon varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_logo site_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_name site_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_description site_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_keyword site_keyword varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  /** Site Setting Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD site_custom_domain int(1) NOT NULL AFTER site_setting_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    DROP INDEX site_custom_domain");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD site_ads_txt LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    ADD site_mainfest_json LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    ADD site_robots_txt LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD KEY site_custom_domain (site_custom_domain)");

  /* Tag Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."tag
    CHANGE tag_title tag_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  /* User Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."user
    CHANGE user_email user_email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE user_password user_password varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE user_salt_key user_salt_key varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
 

  ########################################################################################################################
  ##                                                                                                                  ##
  ##                                         CMS Applications Database Table                                            ##
  ##                                                                                                                    ##
  ########################################################################################################################

  
  /* Post Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."post
    CHANGE post_category post_category bigint(20) NOT NULL");

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                         News CMS Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################


  


  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                         Election Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################

  

  

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                     Create View Table for all API's                                                ##
  ##                                                                                                                    ##
  ########################################################################################################################





  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."alliance_party
  CHANGE alliance_year alliance_year year(4) NOT NULL");

  $result = array(
    'status' => 200,
    'msg' => 'Database has been updated successfully'
  );

  echo json_encode($result, TRUE);