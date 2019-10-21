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

  /* category table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."category(category_id bigint(20) NOT NULL, category_parent_id bigint(20) NOT NULL, category_title varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, category_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, category_site_id bigint(20) NOT NULL, category_owner_id bigint(20) NOT NULL, category_type varchar(10) NOT NULL DEFAULT 'post', category_total_count bigint(20) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."category ADD PRIMARY KEY (category_id), ADD INDEX (category_type)");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."category MODIFY category_id bigint(20) NOT NULL AUTO_INCREMENT");
  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."category(category_title, category_site_id, category_owner_id, category_type, category_total_count) VALUES ('Uncategorized', '1', '1', 'post', '0')");

  /* directory table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."directory(directory_id int(11) NOT NULL, directory_name varchar(50) NOT NULL, directory_path varchar(20) NOT NULL, directory_status int(1) NOT NULL, directory_type varchar(10) NOT NULL, directory_creation timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."directory ADD PRIMARY KEY (directory_id), ADD UNIQUE KEY directory_path (directory_path)");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."directory MODIFY directory_id bigint(20) NOT NULL AUTO_INCREMENT");
  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."directory(directory_name, directory_path, directory_status, directory_type, directory_creation) VALUES('Content Management System', 'cms', '1', 'app', NOW())");

  /** Key Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."key(key_id bigint(20) NOT NULL, key_user_id bigint(20) NOT NULL, key_value varchar(255) NOT NULL, key_medium varchar(10) NOT NULL, key_type varchar(10) NOT NULL, key_expire_time datetime NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."key ADD PRIMARY KEY (key_id)");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."key MODIFY key_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* link table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."link(link_id bigint(20) NOT NULL, link_url varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, link_relation_id int(11) NOT NULL, link_is_featured int(1) NOT NULL, link_status varchar(3) NOT NULL, link_type varchar(20) DEFAULT 'post', link_owner_id bigint(20) NOT NULL, link_site_id bigint(20) NOT NULL, link_creation datetime NOT NULL, link_published datetime NOT NULL, link_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."link ADD PRIMARY KEY (link_id), ADD UNIQUE KEY link_url (link_url), ADD KEY link_status (link_status), ADD KEY link_type (link_type), ADD KEY link_published (link_published), ADD KEY link_relation_id (link_relation_id), ADD KEY link_site_id (link_site_id), ADD KEY link_owner_id (link_owner_id)");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."link MODIFY link_id bigint(20) NOT NULL AUTO_INCREMENT");
  mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('/category/uncategorized/', '1', '200', 'category', '1', '1', NOW(), NOW(), NOW())");

  /* media table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."media(media_id bigint(20) NOT NULL, media_url varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, media_title varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, media_status int(1) NOT NULL, media_type varchar(20) NOT NULL, media_ext varchar(6) NOT NULL, media_owner_id bigint(20) NOT NULL, media_site_id bigint(20) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."media ADD PRIMARY KEY (media_id), ADD UNIQUE KEY media_url (media_url)");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."media MODIFY media_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* meta table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."meta(meta_id bigint(20) NOT NULL, meta_relation_id bigint(20) NOT NULL, meta_type varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'post', meta_title TEXT NOT NULL, meta_description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, meta_canonical varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, meta_keyword LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, meta_robots varchar(20) NOT NULL, meta_geo_position varchar(100) NOT NULL, meta_geo_place_name varchar(191) NOT NULL, meta_geo_region varchar(191) NOT NULL, fb_img varchar(191) NOT NULL, fb_title TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, fb_desc varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, tw_img varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, tw_title TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, tw_desc LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."meta ADD PRIMARY KEY (meta_id), ADD INDEX (meta_relation_id)");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."meta MODIFY meta_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* post table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."post(post_id bigint(20) NOT NULL, post_parent_id bigint(20) NOT NULL, post_image longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, post_title TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, post_description longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, post_category varchar(11) NOT NULL, post_owner_id bigint(20) NOT NULL, post_status int(1) NOT NULL, post_type varchar(30) NOT NULL DEFAULT 'post') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."post ADD PRIMARY KEY (post_id), ADD KEY post_category (post_category), ADD INDEX (post_type)");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."post MODIFY post_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* profile table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."profile(profile_id bigint(20) NOT NULL, profile_owner_id bigint(20) NOT NULL, profile_first_name varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL, profile_last_name varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL, profile_display_name varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, profile_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, profile_mobile varchar(20) NOT NULL, profile_alt_mobile varchar(20) NOT NULL, profile_email varchar(191) NOT NULL, profile_alt_email varchar(191) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."profile ADD PRIMARY KEY (profile_id), ADD UNIQUE KEY profile_owner_id (profile_owner_id), ADD UNIQUE KEY profile_email (profile_email)");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."profile MODIFY profile_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* site table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."site(site_id bigint(20) NOT NULL, site_owner_id bigint(20) NOT NULL, site_lang varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en', site_domain varchar(191) NOT NULL, site_url varchar(191) NOT NULL, site_icon varchar(191) NOT NULL, site_logo varchar(191) NOT NULL, site_name varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, site_description varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, site_app varchar(20) NOT NULL DEFAULT 'cms', site_theme varchar(20) NOT NULL DEFAULT 'default', site_indexing int(1) NOT NULL, site_status int(1) NOT NULL, site_expire_date date NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site ADD PRIMARY KEY (site_id), ADD UNIQUE KEY site_id (site_id), ADD UNIQUE KEY site_owner_id (site_owner_id), ADD UNIQUE KEY site_url (site_url)");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."site MODIFY site_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* tag table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."tag(tag_id bigint(20) NOT NULL, tag_name varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, tag_relation_id bigint(20) NOT NULL, tag_owner_id bigint(20) NOT NULL, tag_site_id bigint(20) NOT NULL, tag_type VARCHAR(10) NOT NULL DEFAULT 'post', tag_time TIMESTAMP NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."tag ADD PRIMARY KEY (tag_id), ADD INDEX (tag_name), ADD INDEX (tag_relation_id), ADD INDEX (tag_owner_id), ADD INDEX (tag_site_id), ADD INDEX (tag_type)");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."tag MODIFY tag_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* user table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."user(user_id bigint(20) NOT NULL, user_parent_id bigint(20) NOT NULL, user_username varchar(20) NOT NULL, user_email varchar(191) NOT NULL, user_password varchar(191) NOT NULL, user_salt_key varchar(191) NOT NULL, user_app varchar(50) NOT NULL, user_type varchar(20) NOT NULL, user_status int(1) NOT NULL DEFAULT '0', user_reg_time datetime NOT NULL, user_act_time datetime NOT NULL, user_reg_ip varchar(40) NOT NULL, user_refer_id bigint(20) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."user ADD PRIMARY KEY (user_id), ADD UNIQUE KEY user_salt_key (user_salt_key), ADD UNIQUE KEY user_email (user_email), ADD UNIQUE KEY user_username (user_username)");
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."user MODIFY user_id bigint(20) NOT NULL AUTO_INCREMENT");
 
  /* commit all query */

  mysqli_query($connection, "COMMIT");
