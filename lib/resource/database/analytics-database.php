<?php
    
    // IP Table

    mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."ip(
        ip_id bigint(20) NOT NULL,
        ip_address VARCHAR(50) NOT NULL,
        ip_country VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        ip_state VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        ip_city VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        ip_latitude VARCHAR(10) NOT NULL,
        ip_longitude VARCHAR(10) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");


    mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."ip
        ADD PRIMARY KEY ip_id (ip_id),
        ADD KEY ip_address (ip_address),
        ADD KEY ip_country (ip_country),
        ADD KEY ip_state (ip_state),
        ADD KEY ip_city (ip_city)");

    mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."ip
        ADD ip_continent VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL AFTER ip_address");

    mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."ip
        ADD ip_isp VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL AFTER ip_longitude");

    mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."ip
        ADD ip_company VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL AFTER ip_isp");
        
    mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."ip
        MODIFY ip_id bigint(20) NOT NULL AUTO_INCREMENT");
        
    mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."ip
        ADD ip_postal VARCHAR(10) NOT NULL AFTER ip_city");
        
    mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."ip
        ADD KEY ip_postal (ip_postal)");

    mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."ip
        DROP INDEX ip_address");

    mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."ip
        ADD UNIQUE INDEX ip_address (ip_address)");
    
    