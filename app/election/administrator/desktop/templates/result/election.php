
<h2 style="margin-bottom: 20px;" >Select Election</h2>

<?php

    if($resultList=mysqli_query($connection, "SELECT result_id, election_name FROM ".TABLE_PREFIX."result INNER JOIN ".TABLE_PREFIX."election ON election_id=result_election_id ORDER BY result_id DESC")) {

        if(mysqli_num_rows($resultList)!=0) {

            while($electionData=mysqli_fetch_assoc($resultList)) {

                extract($electionData); ?>
                
                <a href="result.php?type=constituency&id=<?php echo $result_id ?>" class="constituency" >
                    <?php echo $election_name ?>
                </a>

                <?php

            }


        } else {

            echo 'Election found';

        }

    } else {

        echo mysqli_error($connection);

    }