<?php

    $resultId=mysqli_real_escape_string($connection, $_GET['id']);

    if($resultList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."result INNER JOIN ".TABLE_PREFIX."election ON election_id=result_election_id WHERE result_id='{$resultId}'")) {

        if(mysqli_num_rows($resultList)!=0) {

            extract(mysqli_fetch_assoc($resultList));


        } else {

            echo 'Unable to find election data';

        }

    }

?>

<h2 style="margin-bottom: 20px" >Manage <?php echo ucwords($election_name) ?> Result</h2>

<?php

    if($election_party_1!=0) {

        if($partyData=mysqli_query($connection, "SELECT party_code, party_name FROM ".TABLE_PREFIX."party WHERE party_id='{$election_party_1}'")) {

            extract(mysqli_fetch_assoc($partyData)); ?>

                <div class="resultBox">
                    <span id='seats' ><?php echo $result_party_1 ?></span>
                    <h1><?php echo $party_name ?></h1>
                    <div class="btn-group">
                        <span class="btn-green addSeat" id="1_<?php echo $result_id ?>" >+</span>
                        <span class="btn-red removeSeat" id="1_<?php echo $result_id ?>" >-</span>
                    </div>
                </div>

            <?php

        }

    }
    if($election_party_2!=0) {

        if($partyData=mysqli_query($connection, "SELECT party_code, party_name FROM ".TABLE_PREFIX."party WHERE party_id='{$election_party_2}'")) {

            extract(mysqli_fetch_assoc($partyData)); ?>

                <div class="resultBox">
                    <span id='seats' ><?php echo $result_party_2 ?></span>
                    <h1><?php echo $party_name ?></h1>
                    <div class="btn-group">
                        <span class="btn-green addSeat" id="2_<?php echo $result_id ?>" >+</span>
                        <span class="btn-red removeSeat" id="2_<?php echo $result_id ?>" >-</span>
                    </div>
                </div>

            <?php

        }

    }
    if($election_party_3!=0) {

        if($partyData=mysqli_query($connection, "SELECT party_code, party_name FROM ".TABLE_PREFIX."party WHERE party_id='{$election_party_3}'")) {

            extract(mysqli_fetch_assoc($partyData)); ?>

                <div class="resultBox">
                    <span id='seats' ><?php echo $result_party_3 ?></span>
                    <h1><?php echo $party_name ?></h1>
                    <div class="btn-group">
                        <span class="btn-green addSeat" id="3_<?php echo $result_id ?>" >+</span>
                        <span class="btn-red removeSeat" id="3_<?php echo $result_id ?>" >-</span>
                    </div>
                </div>

            <?php

        }

    }
    if($election_party_4!=0) {

        if($partyData=mysqli_query($connection, "SELECT party_code, party_name FROM ".TABLE_PREFIX."party WHERE party_id='{$election_party_4}'")) {

            extract(mysqli_fetch_assoc($partyData)); ?>

                <div class="resultBox">
                    <span id='seats' ><?php echo $result_party_4 ?></span>
                    <h1><?php echo $party_name ?></h1>
                    <div class="btn-group">
                        <span class="btn-green addSeat addSeat" id="4_<?php echo $result_id ?>" >+</span>
                        <span class="btn-red removeSeat" id="4_<?php echo $result_id ?>" >-</span>
                    </div>
                </div>

            <?php

        }

    }
    if($election_party_5!=0) {

        if($partyData=mysqli_query($connection, "SELECT party_code, party_name FROM ".TABLE_PREFIX."party WHERE party_id='{$election_party_4}'")) {

            extract(mysqli_fetch_assoc($partyData)); ?>

                <div class="resultBox">
                    <span id='seats' ><?php echo $result_party_5 ?></span>
                    <h1><?php echo $party_name ?></h1>
                    <div class="btn-group">
                        <span class="btn-green addSeat addSeat" id="5_<?php echo $result_id ?>" >+</span>
                        <span class="btn-red removeSeat" id="5_<?php echo $result_id ?>" >-</span>
                    </div>
                </div>

            <?php

        }

    }

?>
    
    <!-- All Other political party result will me merge on others -->

    <div class="resultBox">
        <span id='seats' ><?php echo $result_party_all ?></span>
        <h1>Others</h1>
        <div class="btn-group">
            <span class="btn-green addSeat addSeat" id="all_<?php echo $result_id ?>" >+</span>
            <span class="btn-red removeSeat" id="all_<?php echo $result_id ?>" >-</span>
        </div>
    </div>