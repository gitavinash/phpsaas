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
    cart_relation_id bigint(20) NOT NULL,
    cart_qty int(11) NOT NULL,
    cart_status int(1) NOT NULL,
    cart_generated timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    cart_updated timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    ADD PRIMARY KEY (cart_id),
    ADD KEY cart_relation_id (cart_relation_id),
    ADD KEY cart_user_id (cart_user_id),
    ADD KEY cart_site_id (cart_site_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    MODIFY cart_id bigint(20) NOT NULL AUTO_INCREMENT");

  /** Inventory table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."inventory (
    inventory_id bigint(20) NOT NULL,
    inventory_relation_id bigint(20) NOT NULL,
    inventory_sku varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    inventory_currency varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    inventory_price varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    inventory_mrp varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    inventory_tax_status int(1) NOT NULL,
    inventory_tax_rate int(2) NOT NULL,
    inventory_in_stock int(1) NOT NULL,
    inventory_current_stock varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    inventory_backorder int(1) NOT NULL,
    inventory_stock_notice int(2) NOT NULL,
    inventory_weight varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    inventory_lenght varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    inventory_width varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    inventory_height varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    inventory_status int(1) NOT NULL,
    inventory_update timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."inventory
    ADD PRIMARY KEY (inventory_id),
    ADD KEY inventory_relation_id (inventory_relation_id),
    ADD KEY inventory_sku (inventory_sku),
    ADD KEY inventory_currency (inventory_currency),
    ADD KEY inventory_tax_status (inventory_tax_status),
    ADD KEY inventory_in_stock (inventory_in_stock),
    ADD KEY inventory_current_stock (inventory_current_stock),
    ADD KEY inventory_backorder (inventory_backorder),
    ADD KEY inventory_stock_notice (inventory_stock_notice),
    ADD KEY inventory_status (inventory_status)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."inventory
    MODIFY inventory_id bigint(20) NOT NULL AUTO_INCREMENT");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."inventory
    CHANGE inventory_price inventory_price VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0'");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."inventory
    CHANGE inventory_mrp inventory_mrp VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0'");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."inventory
    ADD inventory_type VARCHAR(10) NOT NULL DEFAULT 'product' AFTER inventory_height");
  
  /** Order Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."order (
    order_id bigint(20) NOT NULL,
    order_site_id bigint(20) NOT NULL,
    order_user_id bigint(20) NOT NULL,
    order_cart_id bigint(20) NOT NULL,
    order_address_id bigint(20) NOT NULL,
    order_product_id varchar(255) NOT NULL,
    order_product_qty varchar(255) NOT NULL,
    order_product_price varchar(255) NOT NULL,
    order_currency varchar(4) NOT NULL DEFAULT 'INR',
    order_total_price varchar(10) NOT NULL,
    order_status int(1) NOT NULL,
    order_payment_mode varchar(10) NOT NULL,
    order_generate timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    order_update timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."order
    ADD PRIMARY KEY (order_id),
    ADD KEY order_site_id (order_site_id),
    ADD KEY order_user_id (order_user_id),
    ADD KEY order_cart_id (order_cart_id),
    ADD KEY order_address_id (order_address_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."order
    MODIFY order_id bigint(20) NOT NULL AUTO_INCREMENT");

  /** Product table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."product (
    product_id bigint(20) NOT NULL,
    product_parent_id bigint(20) NOT NULL,
    product_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    product_thumbnail varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    product_gallery longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    product_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    product_description longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    product_highlight longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    product_category bigint(20) NOT NULL,
    product_owner_id bigint(20) NOT NULL,
    product_status int(1) NOT NULL,
    product_type varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'product',
    product_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."product
    ADD PRIMARY KEY (product_id),
    ADD KEY product_category (product_category),
    ADD KEY product_type (product_type),
    ADD KEY product_parent_id (product_parent_id),
    ADD KEY product_owner_id (product_owner_id),
    ADD KEY product_time (product_time)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."product
    MODIFY product_id bigint(20) NOT NULL AUTO_INCREMENT");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."product
  ADD KEY product_status (product_status)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."product
    ADD product_review INT(1) NOT NULL AFTER product_category");

  /** Transaction Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."transaction (
    transaction_id bigint(11) NOT NULL,
    transaction_site_id bigint(20) NOT NULL,
    transaction_user_id bigint(20) NOT NULL,
    transaction_order_id varchar(100) NOT NULL,
    transaction_method varchar(10) NOT NULL,
    transaction_gateway varchar(10) NOT NULL,
    transaction_reference_id varchar(100) NOT NULL,
    transaction_payment_id varchar(100) NOT NULL,
    transaction_payment_mode varchar(10) NOT NULL,
    transaction_currency varchar(4) NOT NULL,
    transaction_amount varchar(10) NOT NULL,
    transaction_type varchar(100) NOT NULL,
    transaction_status int(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."transaction
    ADD PRIMARY KEY (transaction_id),
    ADD KEY transaction_order_id (transaction_order_id),
    ADD KEY transaction_method (transaction_method),
    ADD KEY transaction_site_id (transaction_site_id),
    ADD KEY transaction_user_id (transaction_user_id),
    ADD KEY transaction_reference_id (transaction_reference_id),
    ADD KEY transaction_payment_id (transaction_payment_id),
    ADD KEY transaction_payment_mode (transaction_payment_mode),
    ADD KEY transaction_type (transaction_type),
    ADD KEY transaction_gateway (transaction_gateway)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."transaction
    MODIFY transaction_id bigint(11) NOT NULL AUTO_INCREMENT");
  
  
  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                    Update E-Commerce Applications Database Table                                    ##
  ##                                                                                                                    ##
  ########################################################################################################################  

  /** Update cart table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    ADD cart_price VARCHAR(10) NOT NULL AFTER cart_qty");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    ADD cart_currency VARCHAR(5) NOT NULL DEFAULT 'INR' AFTER cart_qty");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart ADD cart_session_id VARCHAR(255) NOT NULL AFTER cart_id");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    ADD INDEX cart_session_id (cart_session_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    CHANGE cart_product_id cart_relation_id BIGINT(20) NOT NULL");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    ADD INDEX cart_relation_id (cart_relation_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."cart
    CHANGE cart_product_qty cart_qty BIGINT(20) NOT NULL");

  /** Update Transaction Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."transaction
    ADD transaction_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER transaction_status");

  /** Update order table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."order
    ADD order_coupon VARCHAR(20) NOT NULL AFTER order_total_price");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."order
    CHANGE order_cart_id order_cart_session_id BIGINT(20) NOT NULL");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."order
    CHANGE order_cart_session_id order_cart_session_id VARCHAR(255) NOT NULL");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."order
    ADD order_delivery_charges VARCHAR(3) NOT NULL AFTER order_currency");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."order
    CHANGE order_generate order_generate TIMESTAMP NULL DEFAULT NULL");