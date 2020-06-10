<?php

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
  ##                                      Update News Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################  


  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."news
    ADD INDEX news_parent_id (news_parent_id)");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."news
    ADD INDEX news_owner_id (news_owner_id)");
    
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."news
    ADD INDEX news_time (news_time)");