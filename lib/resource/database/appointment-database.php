<?php

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                      Appointment Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################

  /** Booking Table is already managed by pangat app */

  /** appointment Table */

  mysqli_query($connection, "CREATE TABLE IF NOT EXISTS ".TABLE_PREFIX."appointment (
    appointment_id bigint(20) NOT NULL,
    appointment_site_id bigint(20) NOT NULL,
    appointment_days varchar(10) NOT NULL,
    appointment_slots_duration int(2) NOT NULL COMMENT 'slots for duration of booking in json format',
    appointment_slots_details longtext NOT NULL COMMENT 'in json values',
    appointment_status int(1) NOT NULL,
    appointment_update timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."appointment
    ADD PRIMARY KEY (appointment_id),
    ADD KEY appointment_site_id (appointment_site_id),
    ADD KEY appointment_status (appointment_status),
    ADD KEY appointment_days (appointment_days)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."appointment
    MODIFY appointment_id bigint(20) NOT NULL AUTO_INCREMENT");
  
  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                   Update Appointment Applications Database Table                                   ##
  ##                                                                                                                    ##
  ########################################################################################################################  
