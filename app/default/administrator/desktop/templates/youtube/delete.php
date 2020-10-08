<?php

    if($checkVideo=mysqli_query($connection, "SELECT link_id FROM ".TABLE_PREFIX."video INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=video_id WHERE video_id='{$id}'")) {

        if(mysqli_num_rows($checkVideo)) {

            mysqli_query($connection, "START TRANSACTION");

            $deleteVideo=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."video WHERE video_id={$id}");
            $deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."link WHERE link_relation_id={$id}");

            if($deleteVideo && $deleteLink) {

                mysqli_query($connection, "COMMIT");

                ?>

                <script>
                    alert('Video delete successfully');
                    window.location.href='/dashboard/youtube/?type=list&status=all';
                </script>

                <?php

            } else {

                mysqli_query($connection, "ROLLBACK");
                
                ?>

                <script>
                    alert('Something is wrong while deleting the video');
                    window.location.href='/dashboard/youtube/?type=list&status=all';
                </script>
    
                <?php

            }

        } else {

            ?>

            <script>
                alert('Invalid information found');
                window.location.href='/dashboard/youtube/?type=list&status=all';
            </script>

            <?php

        }

    }