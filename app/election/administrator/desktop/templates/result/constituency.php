
<h2 style="margin-bottom: 20px;" >Select Election</h2>

<?php

    $resultId=mysqli_real_escape_string($connection, $_GET['id']);

    if($constituencyList=mysqli_query($connection, "SELECT constituency_id, constituency_name, election_name FROM ".TABLE_PREFIX."constituency INNER JOIN ".TABLE_PREFIX."election ON election_year=constituency_year ORDER BY constituency_name ASC")) {

        if(mysqli_num_rows($constituencyList)!=0) {

            while($electionData=mysqli_fetch_assoc($constituencyList)) {

                extract($electionData); ?>
                
                <a href="result.php?type=counting&id=<?php echo $constituency_id ?>&result_id=<?php echo $resultId ?>" class="constituency-inner" >
                    <?php echo $constituency_name ?>
                </a>

                <?php

            }


        } else {

            echo 'Election found';

        }

    } else {

        echo mysqli_error($connection);

    }