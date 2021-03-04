<?php

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                           Event Applications Database Table                                        ##
  ##                                                                                                                    ##
  ########################################################################################################################

  // Event table

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."event (
    event_id bigint(20) NOT NULL,
    event_parent_id bigint(20) NOT NULL,
    event_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    event_thumbnail varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    event_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    event_description longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    event_image_list longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    event_category bigint(20) NOT NULL,
    event_owner_id bigint(20) NOT NULL,
    event_status int(1) NOT NULL,
    event_type varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'event',
    event_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."event
    ADD PRIMARY KEY (event_id),
    ADD KEY event_category (event_category),
    ADD KEY event_type (event_type),
    ADD KEY event_parent_id (event_parent_id),
    ADD KEY event_owner_id (event_owner_id),
    ADD KEY event_time (event_time)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."event
    MODIFY event_id bigint(20) NOT NULL AUTO_INCREMENT");
  
  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                       Update Event Applications Database Table                                     ##
  ##                                                                                                                    ##
  ########################################################################################################################

  // Update event table

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."event
    ADD event_start_date DATE NOT NULL AFTER event_category");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."event
    ADD event_start_time TIME NOT NULL AFTER event_start_date");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."event
    ADD INDEX event_start_date (event_start_date)");