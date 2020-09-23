<?php

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                         Pangat Applications Database Table                                         ##
  ##                                                                                                                    ##
  ########################################################################################################################

  /** Booking Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."booking (
    booking_id int(255) NOT NULL,
    booking_site_id bigint(20) NOT NULL,
    booking_user_id bigint(20) NOT NULL,
    booking_date date NOT NULL,
    booking_time time NOT NULL,
    booking_service varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
    booking_details longtext NOT NULL,
    booking_patient_type varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
    booking_type varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
    booking_check_in int(1) NOT NULL,
    booking_check_out int(1) NOT NULL,
    booking_status int(1) NOT NULL,
    booking_update timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."booking
    ADD PRIMARY KEY (booking_id),
    ADD KEY booking_user_id (booking_user_id),
    ADD KEY booking_date (booking_date),
    ADD KEY booking_time (booking_time),
    ADD KEY booking_patient_type (booking_patient_type),
    ADD KEY booking_type (booking_type),
    ADD KEY booking_update (booking_update),
    ADD KEY booking_service (booking_service) USING BTREE,
    ADD KEY booking_check_in (booking_check_in),
    ADD KEY booking_check_out (booking_check_out),
    ADD KEY booking_status (booking_status),
    ADD KEY booking_site_id (booking_site_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."booking
    MODIFY booking_id int(255) NOT NULL AUTO_INCREMENT");

  /** Change column booking_patient_type to booking_for */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."booking
    CHANGE booking_patient_type booking_for VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'self'");
  
  // Change booking time slot data type

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."booking
    CHANGE booking_time booking_time VARCHAR(10) NOT NULL");

  /** Add new column for emquiry id */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."booking
    ADD booking_enquiry_id BIGINT NOT NULL AFTER booking_user_id, ADD INDEX (booking_enquiry_id)");
    
  /** Pangat Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."pangat (
    pangat_id bigint(20) NOT NULL,
    pangat_site_id bigint(20) NOT NULL,
    pangat_days varchar(10) NOT NULL,
    pangat_slots_duration int(2) NOT NULL COMMENT 'slots for duration of booking in json format',
    pangat_slots_details longtext NOT NULL COMMENT 'in json values',
    pangat_status int(1) NOT NULL,
    pangat_update timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."pangat
    ADD PRIMARY KEY (pangat_id),
    ADD KEY pangat_site_id (pangat_site_id),
    ADD KEY pangat_status (pangat_status),
    ADD KEY pangat_days (pangat_days)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."pangat
    MODIFY pangat_id bigint(20) NOT NULL AUTO_INCREMENT");
  
  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                     Update Pangat Applications Database Table                                      ##
  ##                                                                                                                    ##
  ########################################################################################################################  
