<?php

    include_once 'header.php';

?>

<section>
    <div class="events">
        <h2>Recent Events</h2>
        <div class="event-slider">
            
            <?php
            
            if($eventList=mysqli_query($connection, "SELECT event_title, event_image, link_url FROM ".TABLE_PREFIX."event INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=event_id WHERE link_type='event' AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY link_published DESC LIMIT 3")) {

                if(mysqli_num_rows($eventList)!=0) {

                    while($eventListData=mysqli_fetch_assoc($eventList)) {

                        extract($eventListData);

                        echo "<div class='event-slide'>
                                <a href='{$link_url}'>
                                    <div class='event-box'>
                                        <img src='{$event_image}' alt='{$event_title}'>
                                        <div class='event-title'>
                                            <h3>{$event_title}</h3>
                                        </div>
                                    </div>
                                </a>
                            </div>";

                    }

                } else {

                    echo 'Not found';

                }

            } else {

                echo mysqli_error($connection);

            }

            ?>

        </div>
    </div>
</section>

<?php

    include_once 'footer.php';

?>