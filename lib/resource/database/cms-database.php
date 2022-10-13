<?php

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                            CMS Applications Database Table                                         ##
  ##                                                                                                                    ##
  ########################################################################################################################

  // Gallery table

  mysqli_query($connection, "CREATE TABLE IF NOT EXISTS ".TABLE_PREFIX."gallery (
    gallery_id bigint(20) NOT NULL,
    gallery_parent_id bigint(20) NOT NULL,
    gallery_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    gallery_thumbnail varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    gallery_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    gallery_description longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    gallery_image_list longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    gallery_category bigint(20) NOT NULL,
    gallery_owner_id bigint(20) NOT NULL,
    gallery_status int(1) NOT NULL,
    gallery_type varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gallery',
    gallery_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."gallery
    ADD PRIMARY KEY (gallery_id),
    ADD KEY gallery_category (gallery_category),
    ADD KEY gallery_type (gallery_type),
    ADD KEY gallery_parent_id (gallery_parent_id),
    ADD KEY gallery_owner_id (gallery_owner_id),
    ADD KEY gallery_time (gallery_time)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."gallery
    MODIFY gallery_id bigint(20) NOT NULL AUTO_INCREMENT");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."gallery
    ADD gallery_site_id BIGINT NOT NULL AFTER gallery_parent_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."gallery
    ADD INDEX gallery_site_id (gallery_site_id)");

  // Video table

  mysqli_query($connection, "CREATE TABLE IF NOT EXISTS ".TABLE_PREFIX."video (
    video_id bigint(20) NOT NULL,
    video_parent_id bigint(20) NOT NULL,
    video_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    video_thumbnail varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    video_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    video_description longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    video_category bigint(20) NOT NULL,
    video_owner_id bigint(20) NOT NULL,
    video_status int(1) NOT NULL,
    video_type varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
    video_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."video
    ADD PRIMARY KEY (video_id),
    ADD KEY video_category (video_category),
    ADD KEY video_type (video_type),
    ADD KEY video_parent_id (video_parent_id),
    ADD KEY video_owner_id (video_owner_id),
    ADD KEY video_time (video_time)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."video
    MODIFY video_id bigint(20) NOT NULL AUTO_INCREMENT");

  
  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                        Update CMS Applications Database Table                                      ##
  ##                                                                                                                    ##
  ########################################################################################################################

  /** Update video table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."video
    ADD video_site_id BIGINT NOT NULL AFTER video_parent_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."video
    ADD INDEX video_site_id (video_site_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."video
    ADD INDEX(video_title)");