<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="refresh" content="60">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css">

    <title><?php echo SITE_NAME ?></title>
  </head>
  <body>
    
    <header>
        <h1>Punjab Kesari Election</h1>
    </header>

    <div class="status-box">
        <div class="main-box">
            
            <div class="box">
                <?php
                    $data=mysqli_query($connection, "SELECT result_party_1, party_name, party_code FROM ".TABLE_PREFIX."result INNER JOIN ".TABLE_PREFIX."election ON result_election_id=election_id INNER JOIN ".TABLE_PREFIX."party ON party_id=election_party_1");
                    extract(mysqli_fetch_assoc($data));
                ?>
                <span><?php echo $result_party_1 ?></span>
                <h2><?php echo strtoupper($party_code) ?></h2>
            </div>
            
            <div class="box">
                <?php
                    $data=mysqli_query($connection, "SELECT result_party_2, party_name, party_code FROM ".TABLE_PREFIX."result INNER JOIN ".TABLE_PREFIX."election ON result_election_id=election_id INNER JOIN ".TABLE_PREFIX."party ON party_id=election_party_2");
                    extract(mysqli_fetch_assoc($data));
                ?>
                <span><?php echo $result_party_2 ?></span>
                <h2><?php echo strtoupper($party_code) ?></h2>
            </div>

            <div class="box">
                <?php
                    $data=mysqli_query($connection, "SELECT result_party_3, party_name, party_code FROM ".TABLE_PREFIX."result INNER JOIN ".TABLE_PREFIX."election ON result_election_id=election_id INNER JOIN ".TABLE_PREFIX."party ON party_id=election_party_3");
                    extract(mysqli_fetch_assoc($data));
                ?>
                <span><?php echo $result_party_3 ?></span>
                <h2><?php echo strtoupper($party_code) ?></h2>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <?php require_once NOTIFICATION.'index.php'; ?>

  </body>
</html>