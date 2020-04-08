<?php

  /* defining sql mode */

  mysqli_query($connection, 'SET SQL_MODE = "NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"');

  /** Setting character set */

  mysqli_set_charset($connection, 'utf8mb4');

  /* define autocommit */

  mysqli_query($connection, 'SET AUTOCOMMIT = 0');

  /** Set Collation */

  mysqli_query($connection, 'SET collation=utf8mb4_unicode_ci');

  /* start transaction */

  mysqli_query($connection, 'START TRANSACTION');

  /* define timezone for database */

  mysqli_query($connection, 'SET time_zone = "'.$time_zone_value.'"');


  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                      Default System Applications Database Table                                    ##
  ##                                                                                                                    ##
  ########################################################################################################################


  /* Device Table */

  $createDeviceTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."device (
    device_id bigint(20) NOT NULL,
    device_site_id bigint(20) NOT NULL,
    device_token varchar(255) NOT NULL,
    device_type varchar(20) NOT NULL,
    device_os varchar(20) NOT NULL,
    device_browser varchar(20) NOT NULL,
    device_status int(11) NOT NULL,
    device_registration timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexDeviceTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    ADD PRIMARY KEY (device_id),
    ADD UNIQUE KEY device_token (device_token) USING HASH,
    ADD KEY device_site_id (device_site_id),
    ADD KEY device_status (device_status),
    ADD KEY device_type (device_type),
    ADD KEY device_os (device_os),
    ADD KEY device_browser (device_browser),
    ADD KEY device_registration (device_registration),
    ADD KEY device_list (device_site_id,device_type,device_os,device_status) USING BTREE");
  
  $alterDeviceTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    CHANGE device_id device_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE device_site_id device_site_id bigint(20) NOT NULL,
    CHANGE device_token device_token varchar(255) NOT NULL,
    CHANGE device_type device_type varchar(20) NOT NULL,
    CHANGE device_os device_os varchar(20) NOT NULL,
    CHANGE device_browser device_browser varchar(20) NOT NULL,
    CHANGE device_status device_status int(11) NOT NULL,
    CHANGE device_registration device_registration timestamp NOT NULL DEFAULT current_timestamp()");

  if( $createDeviceTable && $indexDeviceTable && $alterDeviceTable ) {

    $deviceTable=TRUE;

  } else {
    
    echo mysqli_error($connection);
    exit();

  }

  /* Directory Table */

  $createDirectoryTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."directory (
    directory_id int(11) NOT NULL,
    directory_name varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    directory_path varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    directory_status int(1) NOT NULL,
    directory_type varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    directory_creation timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexDirectoryTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."directory
    ADD PRIMARY KEY (directory_id),
    ADD UNIQUE KEY directory_path (directory_path)");

  $alterDirectoryTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."directory
    CHANGE directory_id directory_id int(11) NOT NULL AUTO_INCREMENT,
    CHANGE directory_name directory_name varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE directory_path directory_path varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE directory_status directory_status int(1) NOT NULL,
    CHANGE directory_type directory_type varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE directory_creation directory_creation timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

  if( $createDirectoryTable && $indexDirectoryTable && $alterDirectoryTable ) {
  
    $directoryTable=TRUE;
  
  } else {
      
    echo mysqli_error($connection);
    exit();
  
  }

  /* Enquiry Table  */

  $createEnquiryTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."enquiry (
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

  $indexEnquiryTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."enquiry
    ADD PRIMARY KEY (enquiry_id),
    ADD INDEX (enquiry_time)");

  $alterEnquiryTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."enquiry
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

  if( $createEnquiryTable && $indexEnquiryTable && $alterEnquiryTable ) {
    
    $enquiryTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Link Table */

  $createLinkTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."link (
    link_id bigint(20) NOT NULL,
    link_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    link_relation_id bigint(20) NOT NULL,
    link_status int(3) NOT NULL,
    link_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    link_is_featured int(1) NOT NULL,
    link_owner_id bigint(20) NOT NULL,
    link_site_id bigint(20) NOT NULL,
    link_creation datetime NOT NULL,
    link_published datetime NOT NULL,
    link_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexLinkTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."link
    ADD PRIMARY KEY (link_id),
    ADD UNIQUE KEY link_url (link_url),
    ADD KEY link_status (link_status),
    ADD KEY link_type (link_type),
    ADD KEY link_published (link_published),
    ADD KEY link_relation_id (link_relation_id),
    ADD KEY link_site_id (link_site_id),
    ADD KEY link_owner_id (link_owner_id)");
  
  $alterLinkTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."link (
    CHANGE link_id link_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE link_url link_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE link_relation_id link_relation_id bigint(20) NOT NULL,
    CHANGE link_status link_status int(3) NOT NULL,
    CHANGE link_type link_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    CHANGE link_is_featured link_is_featured int(1) NOT NULL,
    CHANGE link_owner_id link_owner_id bigint(20) NOT NULL,
    CHANGE link_site_id link_site_id bigint(20) NOT NULL,
    CHANGE link_creation link_creation datetime NOT NULL,
    CHANGE link_published link_published datetime NOT NULL,
    CHANGE link_modified link_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");

  if( $createLinkTable && $indexLinkTable && $alterLinkTable ) {
    
    $linkTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Media Table */

  $createMediaTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."media (
    media_id bigint(20) NOT NULL,
    media_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    media_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    media_status int(1) NOT NULL,
    media_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    media_ext varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    media_owner_id bigint(20) NOT NULL,
    media_site_id bigint(20) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexMediaTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."media
    ADD PRIMARY KEY (media_id),
    ADD UNIQUE KEY media_url (media_url),
    ADD INDEX (media_type),
    ADD KEY media_search(media_type, media_site_id, media_owner_id)");
  
  $alterMediaTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."media (
    CHANGE media_id media_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE media_url media_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE media_title media_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE media_status media_status int(1) NOT NULL,
    CHANGE media_type media_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE media_ext media_ext varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE media_owner_id media_owner_id bigint(20) NOT NULL,
    CHANGE media_site_id media_site_id bigint(20) NOT NULL");

  if( $createMediaTable && $indexMediaTable && $alterMediaTable ) {
    
    $mediaTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Notification Table */

  $createNotificationTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."notification (
    notification_id int(11) NOT NULL,
    notification_site_id bigint(20) NOT NULL,
    notification_icon varchar(255) NOT NULL,
    notification_title varchar(255) NOT NULL,
    notification_image varchar(255) NOT NULL,
    notification_description varchar(255) NOT NULL,
    notification_link varchar(255) NOT NULL,
    notification_type varchar(20) NOT NULL,
    notification_sent bigint(20) NOT NULL,
    notification_droped bigint(20) NOT NULL,
    notification_status int(1) NOT NULL,
    notification_time datetime NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexNotificationTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."notification
    ADD PRIMARY KEY (notification_id),
    ADD KEY notification_site_id (notification_site_id),
    ADD KEY notification_status (notification_status),
    ADD KEY notification_time (notification_time),
    ADD KEY notification_list (notification_site_id,notification_type,notification_status,notification_time) USING BTREE");

  $alterNotificationTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."notification
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

  if( $createNotificationTable && $indexNotificationTable && $alterNotificationTable ) {
    
    $notificationTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Profile Table */

  $createProfileTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."profile (
    profile_id bigint(20) NOT NULL,
    profile_owner_id bigint(20) NOT NULL,
    profile_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    profile_first_name varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    profile_last_name varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    profile_display_name varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    profile_mobile varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    profile_alt_mobile varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    profile_email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    profile_alt_email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexProfileTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."profile
    ADD PRIMARY KEY (profile_id),
    ADD UNIQUE KEY profile_owner_id (profile_owner_id),
    ADD UNIQUE KEY profile_email (profile_email)");

  $alterProfileTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."profile
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

  if( $createProfileTable && $indexProfileTable && $alterProfileTable ) {
    
    $profileTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Site Table */

  $createSiteTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."site (
    site_id bigint(20) NOT NULL,
    site_owner_id bigint(20) NOT NULL,
    site_lang varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
    site_domain varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    site_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    site_icon varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    site_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    site_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    site_description varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    site_keyword LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    site_app varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cms',
    site_theme varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
    site_indexing int(1) NOT NULL,
    site_status int(1) NOT NULL,
    site_expire_date date NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexSiteTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    ADD PRIMARY KEY (site_id),
    ADD UNIQUE KEY site_url (site_url),
    ADD INDEX site_owner_id (site_owner_id),
    ADD INDEX (site_domain),
    ADD INDEX (site_expire_date)");

  $alterSiteTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    CHANGE site_id site_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE site_owner_id site_owner_id bigint(20) NOT NULL,
    CHANGE site_lang site_lang varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
    CHANGE site_domain site_domain varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_url site_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_icon site_icon varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_logo site_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_name site_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_description site_description varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_keyword site_keyword LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_app site_app varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cms',
    CHANGE site_theme site_theme varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
    CHANGE site_indexing site_indexing int(1) NOT NULL,
    CHANGE site_status site_status int(1) NOT NULL,
    CHANGE site_expire_date site_expire_date date NOT NULL");

  if( $createSiteTable && $indexSiteTable && $alterSiteTable ) {
    
    $siteTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /** Site Access Table */

  $createSiteAccessTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."site_access (
    site_access_id bigint(20) NOT NULL,
    site_access_site_id bigint(20) NOT NULL,
    site_access_user_id bigint(20) NOT NULL,
    site_access_status int(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexSiteAccessTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_access
    ADD PRIMARY KEY (site_access_id),
    ADD KEY site_access_user_id (site_access_user_id),
    ADD KEY site_access_site_id (site_access_site_id)");
  
  $alterSiteAccessTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_access
    CHANGE site_access_id site_access_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE site_access_site_id site_access_site_id bigint(20) NOT NULL,
    CHANGE site_access_user_id site_access_user_id bigint(20) NOT NULL,
    CHANGE site_access_status site_access_status int(1) NOT NULL");

  if( $createSiteAccessTable && $indexSiteAccessTable && $alterSiteAccessTable ) {
    
    $siteAccessTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }
  
  /** Site Setting Table */

  $createSiteSettingTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."site_setting (
    site_setting_id bigint(20) NOT NULL,
    site_custom_domain int(1) NOT NULL,
    site_pagination_limit bigint(20) NOT NULL,
    site_sitemap_limit bigint(20) NOT NULL,
    site_feed_limit bigint(20) NOT NULL,
    site_image_quality bigint(20) NOT NULL,
    site_thumbnail_width varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    site_thumbnail_height varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexSiteSettingTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD PRIMARY KEY (site_setting_id),
    ADD KEY site_custom_domain (site_custom_domain)");

  $alterSiteSettingTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    CHANGE site_setting_id site_setting_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE site_custom_domain site_custom_domain int(1) NOT NULL,
    CHANGE site_pagination_limit site_pagination_limit bigint(20) NOT NULL,
    CHANGE site_sitemap_limit site_sitemap_limit bigint(20) NOT NULL,
    CHANGE site_feed_limit site_feed_limit bigint(20) NOT NULL,
    CHANGE site_image_quality site_image_quality bigint(20) NOT NULL,
    CHANGE site_thumbnail_width site_thumbnail_width varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE site_thumbnail_height site_thumbnail_height varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  if( $createSiteSettingTable && $indexSiteSettingTable && $alterSiteSettingTable ) {
    
    $siteSettingTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* User Table */

  $createUserTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."user (
    user_id bigint(20) NOT NULL,
    user_parent_id bigint(20) NOT NULL,
    user_username varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    user_email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    user_password varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    user_salt_key varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    user_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    user_status int(1) NOT NULL DEFAULT 0,
    user_reg_time datetime NOT NULL,
    user_act_time datetime NOT NULL,
    user_reg_ip varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    user_refer_id bigint(20) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexUserTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."user
    ADD PRIMARY KEY (user_id),
    ADD UNIQUE KEY user_salt_key (user_salt_key),
    ADD UNIQUE KEY user_email (user_email),
    ADD UNIQUE KEY user_username (user_username)");

  $alterUserTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."user
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

  if( $createUserTable && $indexUserTable && $alterUserTable ) {
    
    $userTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }
 

  ########################################################################################################################
  ##                                                                                                                  ##
  ##                                         CMS Applications Database Table                                            ##
  ##                                                                                                                    ##
  ########################################################################################################################

  
  /* Category Table */

  $createCategoryTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."category (
    category_id bigint(20) NOT NULL,
    category_parent_id bigint(20) NOT NULL,
    category_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    category_title varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    category_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    category_site_id bigint(20) NOT NULL,
    category_owner_id bigint(20) NOT NULL,
    category_type varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    category_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexCategoryTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."category
    ADD PRIMARY KEY (category_id),
    ADD INDEX (category_type),
    ADD INDEX (category_site_id)");

  $alterCategoryTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."category
    CHANGE category_id category_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE category_parent_id category_parent_id bigint(20) NOT NULL,
    CHANGE category_image category_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE category_title category_title varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE category_description category_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE category_site_id category_site_id bigint(20) NOT NULL,
    CHANGE category_owner_id category_owner_id bigint(20) NOT NULL,
    CHANGE category_type category_type varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    CHANGE category_time category_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()");

  if( $createCategoryTable && $indexCategoryTable && $alterCategoryTable ) {
    
    $categoryTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Post Table */

  $createPostTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."post (
    post_id bigint(20) NOT NULL,
    post_parent_id bigint(20) NOT NULL,
    post_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    post_thumbnail varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    post_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    post_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    post_highlight LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    post_category bigint(20) NOT NULL,
    post_owner_id bigint(20) NOT NULL,
    post_status int(1) NOT NULL,
    post_type varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    post_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexPostTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."post
    ADD PRIMARY KEY (post_id),
    ADD KEY post_category (post_category),
    ADD INDEX (post_type)");

  $alterPostTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."post
    CHANGE post_id post_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE post_parent_id post_parent_id bigint(20) NOT NULL,
    CHANGE post_image post_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE post_thumbnail post_thumbnail varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE post_title post_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE post_description post_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE post_highlight post_highlight LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE post_category post_category bigint(20) NOT NULL,
    CHANGE post_owner_id post_owner_id bigint(20) NOT NULL,
    CHANGE post_status post_status int(1) NOT NULL,
    CHANGE post_type post_type varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    CHANGE post_time post_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

  if( $createPostTable && $indexPostTable && $alterPostTable ) {
    
    $postTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Meta Table */

  $createMetaTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."meta (
    meta_id bigint(20) NOT NULL,
    meta_relation_id bigint(20) NOT NULL,
    meta_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    meta_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    meta_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    meta_canonical varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    meta_keyword LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    meta_robots varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    meta_geo_position varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    meta_geo_place_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    meta_geo_region varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    fb_img varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    fb_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    fb_desc LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    tw_img varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    tw_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    tw_desc LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexMetaTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."meta
    ADD PRIMARY KEY (meta_id),
    ADD INDEX (meta_relation_id)");

  $alterMetaTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."meta
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

  if( $createMetaTable && $indexMetaTable && $alterMetaTable ) {
    
    $metaTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Tag Table */

  $createTagTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."tag (
    tag_id bigint(20) NOT NULL,
    tag_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    tag_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    tag_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL,
    tag_relation_id bigint(20) NOT NULL,
    tag_owner_id bigint(20) NOT NULL,
    tag_site_id bigint(20) NOT NULL,
    tag_type VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    tag_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexTagTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."tag
    ADD PRIMARY KEY (tag_id),
    ADD INDEX (tag_title),
    ADD INDEX (tag_relation_id),
    ADD INDEX (tag_owner_id),
    ADD INDEX (tag_site_id),
    ADD INDEX (tag_type),
    ADD INDEX (tag_time),
    ADD KEY trending (tag_title,tag_type,tag_site_id, tag_time)");

  $alterTagTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."tag
    CHANGE tag_id tag_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE tag_title tag_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE tag_image tag_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE tag_description tag_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL,
    CHANGE tag_relation_id tag_relation_id bigint(20) NOT NULL,
    CHANGE tag_owner_id tag_owner_id bigint(20) NOT NULL,
    CHANGE tag_site_id tag_site_id bigint(20) NOT NULL,
    CHANGE tag_type tag_type VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    CHANGE tag_time tag_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

  if( $createTagTable && $indexTagTable && $alterTagTable ) {
    
    $tagTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                         News CMS Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################


  /* News Article Table */

  $createNewsTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."news (
    news_id bigint(20) NOT NULL,
    news_parent_id bigint(20) NOT NULL,
    news_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    news_thumbnail varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    news_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    news_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    news_highlight LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    news_category bigint(20) NOT NULL,
    news_owner_id bigint(20) NOT NULL,
    news_status int(1) NOT NULL,
    news_type varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'news',
    news_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexNewsTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."news
    ADD PRIMARY KEY (news_id),
    ADD KEY news_category (news_category),
    ADD INDEX (news_type)");

  $alterNewsTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."news
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

  if( $createNewsTable && $indexNewsTable && $alterNewsTable ) {
    
    $newsTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }


  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                         Election Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################

  /* Alliance Party Table */

  $createAlliancePartyTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."alliance_party (
    alliance_id bigint(20) NOT NULL,
    alliance_election bigint(20) NOT NULL,
    alliance_code varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    alliance_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    alliance_slogan varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    alliance_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    alliance_party varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'List of all alliance parties',
    alliance_year year(4) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexAlliancePartyTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."alliance_party
    ADD PRIMARY KEY (alliance_id),
    ADD KEY party_code (alliance_code),
    ADD KEY party_name (alliance_name),
    ADD KEY alliance_year (alliance_year),
    ADD KEY alliance_election (alliance_election)");

  $alterAlliancePartyTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."alliance_party
    CHANGE alliance_id alliance_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE alliance_election alliance_election bigint(20) NOT NULL,
    CHANGE alliance_code alliance_code varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE alliance_name alliance_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE alliance_slogan alliance_slogan varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE alliance_logo alliance_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE alliance_party alliance_party varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'List of all alliance parties',
    CHANGE alliance_year alliance_year year(4) NOT NULL");

  if( $createAlliancePartyTable && $indexAlliancePartyTable && $alterAlliancePartyTable ) {
    
    $alliancePartyTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Candidate Table */

  $createCandidateTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."candidate (
    candidate_id bigint(20) NOT NULL,
    candidate_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    candidate_photo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    candidate_party bigint(20) NOT NULL,
    candidate_election bigint(20) NOT NULL,
    candidate_constituency bigint(20) NOT NULL,
    candidate_result int(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexCandidateTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."candidate
    ADD PRIMARY KEY (candidate_id),
    ADD KEY candidate_constituency (candidate_constituency),
    ADD KEY candidate_party (candidate_party),
    ADD KEY candidate_result (candidate_result)");

  $alterCandidateTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."candidate
    CHANGE candidate_id candidate_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE candidate_name candidate_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE candidate_photo candidate_photo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE candidate_party candidate_party bigint(20) NOT NULL,
    CHANGE candidate_election candidate_election bigint(20) NOT NULL,
    CHANGE candidate_constituency candidate_constituency bigint(20) NOT NULL,
    CHANGE candidate_result candidate_result int(1) NOT NULL");

  if( $createCandidateTable && $indexCandidateTable && $alterCandidateTable ) {
    
    $candidateTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Constituency Table */

  $createConstituencyTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."constituency (
    constituency_id bigint(20) NOT NULL,
    constituency_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    constituency_state varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    constituency_type varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Election Id of election table',
    constituency_year year(4) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexConstituencyTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."constituency
    ADD PRIMARY KEY (constituency_id),
    ADD KEY constituency_type (constituency_type),
    ADD KEY constituency_year (constituency_year),
    ADD KEY constituency_state (constituency_state)");

  $alterConstituencyTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."constituency
    CHANGE constituency_id constituency_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE constituency_name constituency_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE constituency_state constituency_state varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE constituency_type constituency_type varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Election Id of election table',
    CHANGE constituency_year constituency_year year(4) NOT NULL");

  if( $createConstituencyTable && $indexConstituencyTable && $alterConstituencyTable ) {
    
    $constituencyTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Election Table */

  $createElectionTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."election (
    election_id bigint(20) NOT NULL,
    election_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    election_state varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    election_party_1 bigint(20) NOT NULL,
    election_party_2 bigint(20) NOT NULL,
    election_party_3 bigint(20) NOT NULL,
    election_party_4 bigint(20) NOT NULL,
    election_party_5 bigint(20) NOT NULL,
    election_party_all bigint(20) NOT NULL,
    election_total_seats bigint(20) NOT NULL,
    election_type varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    election_date date NOT NULL,
    election_result_date date NOT NULL,
    election_year year(4) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexElectionTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."election
    ADD PRIMARY KEY (election_id),
    ADD KEY election_type (election_type),
    ADD KEY election_state (election_state),
    ADD KEY election_year (election_year)");

  $alterElectionTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."election
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

  if( $createElectionTable && $indexElectionTable && $alterElectionTable ) {
    
    $electionTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Party Table */

  $createPartyTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."party (
    party_id bigint(20) NOT NULL,
    party_code varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    party_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    party_slogan varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    party_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexPartyTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."party
    ADD PRIMARY KEY (party_id),
    ADD KEY party_code (party_code),
    ADD KEY party_name (party_name)");

  $alterPartyTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."party
    CHANGE party_id party_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE party_code party_code varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE party_name party_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE party_slogan party_slogan varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE party_logo party_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  if( $createPartyTable && $indexPartyTable && $alterPartyTable ) {
    
    $partyTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Result Table */

  $createResultTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."result (
    result_id bigint(20) NOT NULL,
    result_election_id bigint(20) NOT NULL,
    result_party_1 int(11) NOT NULL,
    result_party_2 int(11) NOT NULL,
    result_party_3 int(11) NOT NULL,
    result_party_4 int(11) NOT NULL,
    result_party_5 int(11) NOT NULL,
    result_party_all bigint(20) NOT NULL,
    result_status int(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexResultTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result
    ADD PRIMARY KEY (result_id),
    ADD KEY election_result_status (result_status),
    ADD KEY result_election_id (result_election_id)");

  $alterResultTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result
    CHANGE result_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE result_election_id bigint(20) NOT NULL,
    CHANGE result_party_1 int(11) NOT NULL,
    CHANGE result_party_2 int(11) NOT NULL,
    CHANGE result_party_3 int(11) NOT NULL,
    CHANGE result_party_4 int(11) NOT NULL,
    CHANGE result_party_5 int(11) NOT NULL,
    CHANGE result_party_all bigint(20) NOT NULL,
    CHANGE result_status int(1) NOT NULL");

  if( $createResultTable && $indexResultTable && $alterResultTable ) {
    
    $resultPartyTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* Result Summary Table */

  $createResultSummaryTable=mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."result_summary (
    result_summary_id bigint(20) NOT NULL,
    result_summary_election_id bigint(20) NOT NULL,
    result_summary_constituency bigint(20) NOT NULL,
    result_summary_winner_party bigint(20) NOT NULL,
    result_summary_status int(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $indexResultSummaryTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result_summary
    ADD PRIMARY KEY (result_summary_id),
    ADD KEY election_result_status (result_summary_status)");

  $alterResultSummaryTable=mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result_summary
    CHANGE result_summary_id result_summary_id bigint(20) NOT NULL AUTO_INCREMENT,
    CHANGE result_summary_election_id result_summary_election_id bigint(20) NOT NULL,
    CHANGE result_summary_constituency result_summary_constituency bigint(20) NOT NULL,
    CHANGE result_summary_winner_party result_summary_winner_party bigint(20) NOT NULL,
    CHANGE result_summary_status result_summary_status int(1) NOT NULL");

  if( $createResultSummaryTable && $indexResultSummaryTable && $alterResultSummaryTable ) {
    
    $resultSummaryTable=TRUE;
    
  } else {
        
    echo mysqli_error($connection);
    exit();
    
  }

  /* commit all query */

  mysqli_query($connection, "COMMIT");