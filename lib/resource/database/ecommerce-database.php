<?php

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                       E-Commerce Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################

  // Cart table

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."cart (
    cart_id bigint(20) NOT NULL,
    cart_site_id bigint(20) NOT NULL,
    cart_user_id bigint(20) NOT NULL,
    cart_product_id bigint(20) NOT NULL,
    cart_product_qty int(11) NOT NULL,
    cart_status int(1) NOT NULL,
    cart_generated timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    cart_updated timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    ADD PRIMARY KEY (cart_id),
    ADD KEY cart_product_id (cart_product_id),
    ADD KEY cart_user_id (cart_user_id),
    ADD KEY cart_site_id (cart_site_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    MODIFY cart_id bigint(20) NOT NULL AUTO_INCREMENT");
  
  
  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                    Update E-Commerce Applications Database Table                                    ##
  ##                                                                                                                    ##
  ########################################################################################################################  

  /** Update cart table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    ADD cart_price VARCHAR(10) NOT NULL AFTER cart_product_qty");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    ADD cart_currency VARCHAR(5) NOT NULL DEFAULT 'INR' AFTER cart_product_qty");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart ADD cart_session_id VARCHAR(255) NOT NULL AFTER cart_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    ADD INDEX cart_session_id (cart_session_id)");