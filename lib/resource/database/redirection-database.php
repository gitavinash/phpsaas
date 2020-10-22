<?php

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                         Redirection Applications Database Table                                    ##
  ##                                                                                                                    ##
  ########################################################################################################################
  
  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."redirection (
    redirection_id bigint(20) NOT NULL,
    redirection_code varchar(10) NOT NULL,
    redirection_parent_id bigint(20) NOT NULL,
    redirection_from varchar(255) NOT NULL,
    redirection_to varchar(255) NOT NULL,
    redirection_http_status varchar(4) NOT NULL,
    redirection_owner_id bigint(20) NOT NULL,
    redirection_site_id bigint(20) NOT NULL,
    redirection_status int(1) NOT NULL,
    redirection_generated timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    redirection_update timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."redirection
    ADD PRIMARY KEY (redirection_id),
    ADD KEY redirection_parent_id (redirection_parent_id),
    ADD KEY redirection_site_id (redirection_site_id),
    ADD KEY redirection_from (redirection_from),
    ADD KEY redirection_http_status (redirection_http_status),
    ADD KEY redirection_owner_id (redirection_owner_id),
    ADD KEY redirection_generated (redirection_generated),
    ADD KEY redirection_update (redirection_update)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."redirection
    MODIFY redirection_id bigint(20) NOT NULL AUTO_INCREMENT");

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                     Update Redirection Applications Database Table                                  ##
  ##                                                                                                                    ##
  ########################################################################################################################  
