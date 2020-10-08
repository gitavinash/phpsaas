<?php

    require_once 'header.php';

?>

<section>

    <div class="video">
    
        <h1><?php echo $video_title ?></h1>

        <?php echo $video_description ?>

    </div>

</section>

<section>

    <div class="previos-video">
        <h2>Previous videos</h2>

        <?php
            
            if($checkVideos=mysqli_query($connection, "SELECT link_url, video_title, video_thumbnail FROM ".TABLE_PREFIX."video INNER JOIN ".TABLE_PREFIX."link ON video_id=link_relation_id WHERE video_status=1 AND link_type='video' ORDER BY link_published DESC LIMIT 0,4")) {

                if(mysqli_num_rows($checkVideos)!=0) {

                    while($videoData=mysqli_fetch_assoc($checkVideos)) {

                        extract($videoData);

                        echo "<a href='{$link_url}' title='{$video_title}' ><div class='video-preview'><img src='{$video_thumbnail}' alt='{$video_title}' ><h3>{$video_title}</h3></div></a>";

                    }

                }

            }

        ?>

    </div>

</section>

<?php

    require_once 'footer.php';

?>