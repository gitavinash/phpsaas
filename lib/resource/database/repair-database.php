<?php

  /** Analytics Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."analytics(
    analytics_id bigint(20) NOT NULL,
    analytics_link_id bigint(20) NOT NULL,
    analytics_site_id bigint(20) NOT NULL,
    analytics_session_id varchar(255) NOT NULL,
    analytics_mode varchar(100) NOT NULL,
    analytics_device varchar(255) NOT NULL,
    analytics_os varchar(255) NOT NULL,
    analytics_browser varchar(255) NOT NULL,
    analytics_country varchar(255) NOT NULL,
    analytics_state varchar(255) NOT NULL,
    analytics_city varchar(255) NOT NULL,
    analytics_source varchar(255) NOT NULL,
    analytics_medium varchar(255) NOT NULL,
    analytics_campaign varchar(255) NOT NULL,
    analytics_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."analytics
    ADD PRIMARY KEY (analytics_id),
    ADD KEY analytics_link_id (analytics_link_id),
    ADD KEY analytics_site_id (analytics_site_id),
    ADD KEY analytics_time (analytics_time)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."analytics
    ADD KEY analytics_session_id (analytics_session_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."analytics
    MODIFY analytics_id bigint(20) NOT NULL AUTO_INCREMENT");

  /** App Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."app(
    app_id bigint(20) NOT NULL,
    app_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    app_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    app_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    app_path varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'directory name of the app',
    app_status int(1) NOT NULL,
    app_launch_time timestamp NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."app
    ADD PRIMARY KEY (app_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."app
    ADD UNIQUE(app_path);");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."app
    MODIFY app_id bigint(20) NOT NULL AUTO_INCREMENT");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."app
    ADD COLUMN app_visiblity INT(1) NOT NULL DEFAULT 1 AFTER app_status");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."app(app_id, app_title, app_description, app_path, app_status, app_launch_time) VALUES(1, 'System', 'All in One Control Panel for SaaS Application', 'system', 1, NOW())");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."app(app_id, app_title, app_description, app_path, app_status, app_launch_time) VALUES(2, 'Content Management System', 'Best suite for blogging website', 'cms', 1, NOW())");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."app(app_id, app_title, app_description, app_path, app_status, app_launch_time) VALUES(3, 'News Management System', 'Best suite for news publishers', 'nms', 1, NOW())");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."app(app_id, app_title, app_description, app_path, app_status, app_launch_time) VALUES(4, 'Customer Relationship Management', 'Best suite all kind of bussiness', 'crm', 1, NOW())");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."app(app_id, app_title, app_description, app_path, app_status, app_visiblity, app_launch_time) VALUES(5, 'The Doctor Zone', 'Customized App for Bharani Clinic', 'thedoctorzone', 1, 1, NOW())");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."app(app_id, app_title, app_description, app_path, app_status, app_visiblity, app_launch_time) VALUES(6, 'Tarot By Sid', 'Customized App for Tarot by Sid', 'tarotbysid', 1, 1, NOW())");

  mysqli_query($connection, "UPDATE ".TABLE_PREFIX."app SET app_visiblity=0 WHERE app_path='system'");

  /** Category Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."category
    ADD COLUMN category_status INT(1) NOT NULL AFTER category_type");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."category
    ADD COLUMN category_deletable INT(1) NOT NULL DEFAULT 1 AFTER category_type");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."category
    CHANGE category_type category_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  /* Device Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    ADD COLUMN device_category BIGINT(20) NOT NULL AFTER device_token");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    ADD COLUMN device_user_id BIGINT(20) NOT NULL AFTER device_site_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    ADD COLUMN device_country varchar(255) NOT NULL AFTER device_browser");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    ADD COLUMN device_state varchar(255) NOT NULL AFTER device_country");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    ADD COLUMN device_city varchar(255) NOT NULL AFTER device_state");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    ADD INDEX device_user_id (device_user_id)");
  
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
    enquiry_source varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    enquiry_medium varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    enquiry_campaign varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    enquiry_status int(1) NOT NULL,
    enquiry_time timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."enquiry
      ADD PRIMARY KEY (enquiry_id),
      ADD INDEX enquiry_type (enquiry_type),
      ADD INDEX enquiry_label (enquiry_label),
      ADD INDEX enquiry_source (enquiry_source),
      ADD INDEX enquiry_medium (enquiry_medium),
      ADD INDEX enquiry_campaign (enquiry_campaign)");

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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."link
  ADD COLUMN link_index varchar(20) NOT NULL AFTER link_is_featured");

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
  
  /** Package Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."package(
    package_id bigint(20) NOT NULL,
    package_app_id bigint(20) NOT NULL,
    package_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    package_description varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    package_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    package_price_inr varchar(6) NOT NULL,
    package_price_usd varchar(6) NOT NULL,
    package_status int(1) NOT NULL,
    package_launch_time timestamp NOT NULL,
    package_update_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package
    ADD PRIMARY KEY (package_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package
    MODIFY package_id bigint(20) NOT NULL AUTO_INCREMENT");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."package(package_id, package_app_id, package_title, package_price_inr, package_price_usd, package_status, package_launch_time) VALUES(1, 1, 'System Default Package', 0, 0, 1, NOW())");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."package(package_id, package_app_id, package_title, package_price_inr, package_price_usd, package_status, package_launch_time) VALUES(2, 2, 'Default Package', 0, 0, 1, NOW())");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."package(package_id, package_app_id, package_title, package_price_inr, package_price_usd, package_status, package_launch_time) VALUES(3, 3, 'Default Package', 0, 0, 1, NOW())");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."package(package_id, package_app_id, package_title, package_price_inr, package_price_usd, package_status, package_launch_time) VALUES(4, 4, 'Default Package', 0, 0, 1, NOW())");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."package(package_id, package_app_id, package_title, package_price_inr, package_price_usd, package_status, package_launch_time) VALUES(5, 5, 'Default Package', 0, 0, 1, NOW())");

  /** Package Features Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."package_feature(
    package_feature_id bigint(20) NOT NULL,
    package_feature_package_id bigint(20) NOT NULL,
    package_feature_pwa int(1) NOT NULL DEFAULT 0,
    package_feature_amp int(1) NOT NULL DEFAULT 0,
    package_feature_android int(1) NOT NULL DEFAULT 0,
    package_feature_ios int(1) NOT NULL DEFAULT 0,
    package_feature_update_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package_feature
    ADD PRIMARY KEY (package_feature_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package_feature
    ADD UNIQUE package_feature_package_id (package_feature_package_id),
    ADD INDEX package_feature_pwa (package_feature_pwa),
    ADD INDEX package_feature_amp (package_feature_amp),
    ADD INDEX package_feature_android (package_feature_android),
    ADD INDEX package_feature_ios (package_feature_ios)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package_feature
    MODIFY package_feature_id bigint(20) NOT NULL AUTO_INCREMENT");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package_feature
    ADD COLUMN package_feature_amp_story int(1) NOT NULL DEFAULT 0 AFTER package_feature_amp");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package_feature
    ADD INDEX package_feature_amp_story (package_feature_amp_story)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package_feature
    ADD COLUMN package_feature_instant_article int(1) NOT NULL DEFAULT 0 AFTER package_feature_amp_story");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package_feature
    ADD INDEX package_feature_instant_article (package_feature_instant_article)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package_feature
    ADD COLUMN package_feature_push_notification int(1) NOT NULL DEFAULT 0 AFTER package_feature_ios");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package_feature
    ADD INDEX package_feature_push_notification (package_feature_push_notification)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package_feature
    ADD COLUMN package_feature_custom_domain int(1) NOT NULL DEFAULT 0 AFTER package_feature_package_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."package_feature
    ADD INDEX package_feature_custom_domain (package_feature_custom_domain)");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."package_feature(package_feature_id, package_feature_package_id, package_feature_pwa, package_feature_amp, package_feature_android, package_feature_ios, package_feature_update_time) VALUES(1, 1, 1, 1, 1, 1, NOW())");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."package_feature(package_feature_id, package_feature_package_id) VALUES(2, 2)");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."package_feature(package_feature_id, package_feature_package_id) VALUES(3, 3)");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."package_feature(package_feature_id, package_feature_package_id) VALUES(4, 4)");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."package_feature(package_feature_id, package_feature_package_id) VALUES(5, 5)");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."package_feature(package_feature_id, package_feature_package_id) VALUES(6, 6)");

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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    CHANGE site_app site_app bigint(20) NOT NULL");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    ADD INDEX site_app (site_app)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    CHANGE site_app site_app_id bigint(20) NOT NULL COMMENT 'Default App ID'");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    DROP INDEX site_app");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    ADD INDEX site_app_id (site_app_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    CHANGE site_theme site_theme bigint(20) NOT NULL");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    ADD INDEX site_theme (site_theme)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    CHANGE site_theme site_theme_id bigint(20) NOT NULL COMMENT 'Default Theme ID'");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    DROP INDEX site_theme");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    ADD INDEX site_theme_id (site_theme_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    CHANGE site_indexing site_index varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    CHANGE site_index site_index VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'noindex, nofollow'");

  /** Site Access Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_access
    ADD COLUMN site_access_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'user type table user access type code' AFTER site_access_user_id ");

  /** Site App Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."site_app(
    site_app_id bigint(20) NOT NULL,
    site_app_app_id bigint(20) NOT NULL,
    site_app_site_id bigint(20) NOT NULL,
    site_app_user_id bigint(20) NOT NULL COMMENT 'User id that has installed the app',
    site_app_theme_id bigint(20) NOT NULL COMMENT 'Theme id of the app',
    site_app_package_id bigint(20) NOT NULL,
    site_app_activation_time date NOT NULL,
    site_app_expiry_time date NOT NULL,
    site_app_status int(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_app
    ADD PRIMARY KEY site_app_id (site_app_id),
    ADD KEY site_app_app_id (site_app_app_id),
    ADD KEY site_app_user_id (site_app_user_id),
    ADD KEY site_app_site_id (site_app_site_id),
    ADD KEY site_app_theme_id (site_app_theme_id),
    ADD KEY site_app_package_id (site_app_package_id),
    ADD KEY site_app_expiry_time (site_app_expiry_time),
    ADD KEY site_app_status (site_app_status)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_app
    MODIFY site_app_id bigint(20) NOT NULL AUTO_INCREMENT");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_app
    CHANGE site_app_activation_time site_app_activation_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_app
    CHANGE site_app_expiry_time site_app_expiry_date DATE NOT NULL;");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_app
    DROP INDEX site_app_expiry_time");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_app
    ADD KEY site_app_expiry_date (site_app_expiry_date)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_app
    ADD KEY site_app_activation_time (site_app_activation_time)");

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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD site_theme_color VARCHAR(100) NOT NULL AFTER site_robots_txt");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD site_ga_id VARCHAR(100) NOT NULL AFTER site_robots_txt");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD site_fb_id VARCHAR(100) NOT NULL AFTER site_ga_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    DROP site_app_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD site_fb_app_id VARCHAR(100) NOT NULL AFTER site_fb_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD site_tw_id VARCHAR(100) NOT NULL AFTER site_fb_app_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    DROP site_custom_domain");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD COLUMN site_package_id BIGINT(20) NOT NULL DEFAULT 1 AFTER site_setting_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD INDEX site_package_id (site_package_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    DROP site_package_id");

  /* Tag Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."tag
    CHANGE tag_title tag_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  /** Theme Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."theme(
    theme_id bigint(20) NOT NULL,
    theme_app_id bigint(20) NOT NULL,
    theme_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    theme_description varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    theme_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    theme_path varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'directory name of the theme',
    theme_version varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    theme_status int(1) NOT NULL,
    theme_publish_time timestamp NOT NULL,
    theme_update_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."theme
    ADD PRIMARY KEY (theme_id),
    ADD INDEX (theme_app_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."theme
    MODIFY theme_id bigint(20) NOT NULL AUTO_INCREMENT");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."theme
    ADD COLUMN theme_package_id INT(1) NOT NULL DEFAULT 0 AFTER theme_app_id");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."theme(theme_id, theme_app_id, theme_package_id, theme_title, theme_description, theme_path, theme_version, theme_status, theme_publish_time, theme_update_time) VALUES(1, 1, 1, 'Default App Theme', 'Dedicated default theme for every application', 'default', '1.0', 1, NOW(), NOW())");

  /* User Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."user
    CHANGE user_email user_email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE user_password user_password varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    CHANGE user_salt_key user_salt_key varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."user
    DROP user_type");

  /** User Type table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."user_type (
    user_type_id bigint(20) NOT NULL,
    user_type_app_id bigint(20) NOT NULL,
    user_type_name varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    user_type_code varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    user_type_status int(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."user_type
  ADD PRIMARY KEY (user_type_id),
  ADD KEY user_type_app_id (user_type_app_id),
  ADD KEY user_type_code (user_type_code)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."user_type
  MODIFY user_type_id int(11) NOT NULL AUTO_INCREMENT");

  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user_type(user_type_app_id, user_type_name, user_type_code, user_type_status) VALUES(1, 'System Administrator', 'administrator', 1)");
 

  ########################################################################################################################
  ##                                                                                                                  ##
  ##                                         CMS Applications Database Table                                            ##
  ##                                                                                                                    ##
  ########################################################################################################################

  
  /* Post Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."post
    CHANGE post_category post_category bigint(20) NOT NULL");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."post
    ADD INDEX post_parent_id (post_parent_id)");
    
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."post
    ADD INDEX post_owner_id (post_owner_id)");
    
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."post
    ADD INDEX post_time (post_time)");
  

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                     Create View Table for all API's                                                ##
  ##                                                                                                                    ##
  ########################################################################################################################



  $result = array(
    'status' => 200,
    'msg' => 'Database has been updated successfully'
  );

  echo json_encode($result, TRUE);