<?php

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                         Election Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################

  /* Alliance Party Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."alliance_party (
    alliance_id bigint(20) NOT NULL,
    alliance_election bigint(20) NOT NULL,
    alliance_code varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    alliance_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    alliance_slogan varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    alliance_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    alliance_party varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'List of all alliance parties',
    alliance_year year(4) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."alliance_party
    ADD PRIMARY KEY (alliance_id),
    ADD KEY party_code (alliance_code),
    ADD KEY party_name (alliance_name),
    ADD KEY alliance_year (alliance_year),
    ADD KEY alliance_election (alliance_election)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."alliance_party
    MODIFY alliance_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Candidate Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."candidate (
    candidate_id bigint(20) NOT NULL,
    candidate_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    candidate_photo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    candidate_party bigint(20) NOT NULL,
    candidate_election bigint(20) NOT NULL,
    candidate_constituency bigint(20) NOT NULL,
    candidate_result int(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."candidate
    ADD PRIMARY KEY (candidate_id),
    ADD KEY candidate_constituency (candidate_constituency),
    ADD KEY candidate_party (candidate_party),
    ADD KEY candidate_result (candidate_result)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."candidate
    MODIFY candidate_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Constituency Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."constituency (
    constituency_id bigint(20) NOT NULL,
    constituency_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    constituency_state varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    constituency_type varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Election Id of election table',
    constituency_year year(4) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."constituency
    ADD PRIMARY KEY (constituency_id),
    ADD KEY constituency_type (constituency_type),
    ADD KEY constituency_year (constituency_year),
    ADD KEY constituency_state (constituency_state)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."constituency
    MODIFY constituency_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Election Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."election (
    election_id bigint(20) NOT NULL,
    election_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    election_state varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    election_party_1 bigint(20) NOT NULL,
    election_party_2 bigint(20) NOT NULL,
    election_party_3 bigint(20) NOT NULL,
    election_party_4 bigint(20) NOT NULL,
    election_party_5 bigint(20) NOT NULL,
    election_party_all bigint(20) NOT NULL,
    election_total_seats bigint(20) NOT NULL,
    election_type varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    election_date date NOT NULL,
    election_result_date date NOT NULL,
    election_year year(4) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."election
    ADD PRIMARY KEY (election_id),
    ADD KEY election_type (election_type),
    ADD KEY election_state (election_state),
    ADD KEY election_year (election_year)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."election
    MODIFY election_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Party Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."party (
    party_id bigint(20) NOT NULL,
    party_code varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    party_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    party_slogan varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    party_logo varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."party
    ADD PRIMARY KEY (party_id),
    ADD KEY party_code (party_code),
    ADD KEY party_name (party_name)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."party
    MODIFY party_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Result Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."result (
    result_id bigint(20) NOT NULL,
    result_election_id bigint(20) NOT NULL,
    result_party_1 int(11) NOT NULL,
    result_party_2 int(11) NOT NULL,
    result_party_3 int(11) NOT NULL,
    result_party_4 int(11) NOT NULL,
    result_party_5 int(11) NOT NULL,
    result_party_all bigint(20) NOT NULL,
    result_status int(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result
    ADD PRIMARY KEY (result_id),
    ADD KEY election_result_status (result_status),
    ADD KEY result_election_id (result_election_id)");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result
    MODIFY result_id bigint(20) NOT NULL AUTO_INCREMENT");

  /* Result Summary Table */

  mysqli_query($connection, "CREATE TABLE ".TABLE_PREFIX."result_summary (
    result_summary_id bigint(20) NOT NULL,
    result_summary_election_id bigint(20) NOT NULL,
    result_summary_constituency bigint(20) NOT NULL,
    result_summary_winner_party bigint(20) NOT NULL,
    result_summary_status int(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result_summary
    ADD PRIMARY KEY (result_summary_id),
    ADD KEY election_result_status (result_summary_status)");
    
  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."result_summary
    MODIFY result_summary_id bigint(20) NOT NULL AUTO_INCREMENT");

  ########################################################################################################################
  ##                                                                                                                    ##
  ##                                  Update Election Applications Database Table                                       ##
  ##                                                                                                                    ##
  ########################################################################################################################  

  /** Alliance Party Table */

  mysqli_query($connection, "ALTER TABLE ".TABLE_PREFIX."alliance_party
    CHANGE alliance_year alliance_year year(4) NOT NULL");