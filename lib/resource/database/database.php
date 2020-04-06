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

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."device (
      device_id bigint(20) NOT NULL,
      device_site_id bigint(20) NOT NULL,
      device_token varchar(255) NOT NULL,
      device_type varchar(20) NOT NULL,
      device_os varchar(20) NOT NULL,
      device_browser varchar(20) NOT NULL,
      device_status int(11) NOT NULL,
      device_registration timestamp NOT NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    ADD PRIMARY KEY (device_id),
    ADD UNIQUE KEY device_token (device_token) USING HASH,
    ADD KEY device_site_id (device_site_id),
    ADD KEY device_status (device_status),
    ADD KEY device_type (device_type),
    ADD KEY device_os (device_os),
    ADD KEY device_browser (device_browser),
    ADD KEY device_registration (device_registration),
    ADD KEY device_list (device_site_id,device_type,device_os,device_status) USING BTREE");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."device
    MODIFY device_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Directory Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."directory(
      directory_id int(11) NOT NULL,
      directory_name varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      directory_path varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      directory_status int(1) NOT NULL,
      directory_type varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      directory_creation timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."directory
    ADD PRIMARY KEY (directory_id),
    ADD UNIQUE KEY directory_path (directory_path)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."directory
    MODIFY directory_id bigint(20) NOT NULL AUTO_INCREMENT");

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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."enquiry
    MODIFY enquiry_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Link Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."link(
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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."link
    ADD PRIMARY KEY (link_id),
    ADD UNIQUE KEY link_url (link_url),
    ADD KEY link_status (link_status),
    ADD KEY link_type (link_type),
    ADD KEY link_published (link_published),
    ADD KEY link_relation_id (link_relation_id),
    ADD KEY link_site_id (link_site_id),
    ADD KEY link_owner_id (link_owner_id)");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."link
    MODIFY link_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Media Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."media(
      media_id bigint(20) NOT NULL,
      media_url varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      media_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      media_status int(1) NOT NULL,
      media_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      media_ext varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      media_owner_id bigint(20) NOT NULL,
      media_site_id bigint(20) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."media
    ADD PRIMARY KEY (media_id),
    ADD UNIQUE KEY media_url (media_url),
    ADD INDEX (media_type),
    ADD KEY media_search(media_type, media_site_id, media_owner_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."media
    MODIFY media_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Notification Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."notification (
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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."notification
    ADD PRIMARY KEY (notification_id),
    ADD KEY notification_site_id (notification_site_id),
    ADD KEY notification_status (notification_status),
    ADD KEY notification_time (notification_time),
    ADD KEY notification_list (notification_site_id,notification_type,notification_status,notification_time) USING BTREE");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."notification
    MODIFY notification_id int(11) NOT NULL AUTO_INCREMENT");

  /* Profile Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."profile(
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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."profile
    ADD PRIMARY KEY (profile_id),
    ADD UNIQUE KEY profile_owner_id (profile_owner_id),
    ADD UNIQUE KEY profile_email (profile_email)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."profile
    MODIFY profile_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Site Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."site(
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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    ADD PRIMARY KEY (site_id),
    ADD UNIQUE KEY site_url (site_url),
    ADD INDEX site_owner_id (site_owner_id),
    ADD INDEX (site_domain),
    ADD INDEX (site_expire_date)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site
    MODIFY site_id bigint(20) NOT NULL AUTO_INCREMENT");

  /** Site Access Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."site_access(
      site_access_id bigint(20) NOT NULL,
      site_access_site_id bigint(20) NOT NULL,
      site_access_user_id bigint(20) NOT NULL,
      site_access_status int(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_access
    ADD PRIMARY KEY (site_access_id),
    ADD KEY site_access_user_id (site_access_user_id),
    ADD KEY site_access_site_id (site_access_site_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_access
    MODIFY site_access_id bigint(20) NOT NULL AUTO_INCREMENT");
  
  /** Site Setting Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."site_setting(
      site_setting_id bigint(20) NOT NULL,
      site_custom_domain int(1) NOT NULL,
      site_pagination_limit bigint(20) NOT NULL,
      site_sitemap_limit bigint(20) NOT NULL,
      site_feed_limit bigint(20) NOT NULL,
      site_image_quality bigint(20) NOT NULL,
      site_thumbnail_width varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      site_thumbnail_height varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    ADD PRIMARY KEY (site_setting_id),
    ADD KEY site_custom_domain (site_custom_domain)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site_setting
    MODIFY site_setting_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* User Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."user(
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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."user
    ADD PRIMARY KEY (user_id),
    ADD UNIQUE KEY user_salt_key (user_salt_key),
    ADD UNIQUE KEY user_email (user_email),
    ADD UNIQUE KEY user_username (user_username)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."user
    MODIFY user_id bigint(20) NOT NULL AUTO_INCREMENT");
 

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                         CMS Applications Database Table                                            ##
  ##                                                                                                                    ##
  ########################################################################################################################

  
  /* Category Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."category(
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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."category
    ADD PRIMARY KEY (category_id),
    ADD INDEX (category_type),
    ADD INDEX (category_site_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."category
    MODIFY category_id bigint(20) NOT NULL AUTO_INCREMENT");


  /* Post Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."post(
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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."post
    ADD PRIMARY KEY (post_id),
    ADD KEY post_category (post_category),
    ADD INDEX (post_type)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."post
    MODIFY post_id bigint(20) NOT NULL AUTO_INCREMENT");


  /* Meta Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."meta(
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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."meta
    ADD PRIMARY KEY (meta_id),
    ADD INDEX (meta_relation_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."meta
    MODIFY meta_id bigint(20) NOT NULL AUTO_INCREMENT");


  /* Tag Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."tag(
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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."tag
    ADD PRIMARY KEY (tag_id),
    ADD INDEX (tag_title),
    ADD INDEX (tag_relation_id),
    ADD INDEX (tag_owner_id),
    ADD INDEX (tag_site_id),
    ADD INDEX (tag_type),
    ADD INDEX (tag_time),
    ADD KEY trending (tag_title,tag_type,tag_site_id, tag_time)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."tag
    MODIFY tag_id bigint(20) NOT NULL AUTO_INCREMENT"); 

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                         News CMS Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################


  /* News Article Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."news(
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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."news
    ADD PRIMARY KEY (news_id),
    ADD KEY news_category (news_category),
    ADD INDEX (news_type)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."news
    MODIFY news_id bigint(20) NOT NULL AUTO_INCREMENT");


  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                         Election Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################

  /* Alliance Party Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."alliance_party (
      alliance_id bigint(20) NOT NULL,
      alliance_election bigint(20) NOT NULL,
      alliance_code varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      alliance_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      alliance_slogan varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      alliance_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      alliance_party varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'List of all alliance parties',
      alliance_year year(4) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."alliance_party
    ADD PRIMARY KEY (alliance_id),
    ADD KEY party_code (alliance_code),
    ADD KEY party_name (alliance_name),
    ADD KEY alliance_year (alliance_year),
    ADD KEY alliance_election (alliance_election)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."alliance_party
    MODIFY alliance_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Candidate Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."candidate (
      candidate_id bigint(20) NOT NULL,
      candidate_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      candidate_photo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      candidate_party bigint(20) NOT NULL,
      candidate_election bigint(20) NOT NULL,
      candidate_constituency bigint(20) NOT NULL,
      candidate_result int(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."candidate
    ADD PRIMARY KEY (candidate_id),
    ADD KEY candidate_constituency (candidate_constituency),
    ADD KEY candidate_party (candidate_party),
    ADD KEY candidate_result (candidate_result)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."candidate
    MODIFY candidate_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Constituency Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."constituency (
      constituency_id bigint(20) NOT NULL,
      constituency_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      constituency_state varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      constituency_type varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Election Id of election table',
      constituency_year year(4) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."constituency
    ADD PRIMARY KEY (constituency_id),
    ADD KEY constituency_type (constituency_type),
    ADD KEY constituency_year (constituency_year),
    ADD KEY constituency_state (constituency_state)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."constituency
    MODIFY constituency_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Election Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."election (
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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."election
    ADD PRIMARY KEY (election_id),
    ADD KEY election_type (election_type),
    ADD KEY election_state (election_state),
    ADD KEY election_year (election_year)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."election
    MODIFY election_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Party Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."party (
      party_id bigint(20) NOT NULL,
      party_code varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      party_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      party_slogan varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
      party_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."party
    ADD PRIMARY KEY (party_id),
    ADD KEY party_code (party_code),
    ADD KEY party_name (party_name)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."party
    MODIFY party_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Result Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."result (
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

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result
    ADD PRIMARY KEY (result_id),
    ADD KEY election_result_status (result_status),
    ADD KEY result_election_id (result_election_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result
    MODIFY result_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Result Summary Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."result_summary (
      result_summary_id bigint(20) NOT NULL,
      result_summary_election_id bigint(20) NOT NULL,
      result_summary_constituency bigint(20) NOT NULL,
      result_summary_winner_party bigint(20) NOT NULL,
      result_summary_status int(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result_summary
    ADD PRIMARY KEY (result_summary_id),
    ADD KEY election_result_status (result_summary_status)");
    
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result_summary
    MODIFY result_summary_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* commit all query */

  mysqli_query($connection, "COMMIT");