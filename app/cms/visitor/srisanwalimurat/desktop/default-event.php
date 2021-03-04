<?php

    include_once 'header.php';

?>

<section>
    <div class="storie-card">
        <div class="story-pg">
            <span class="catg"><a href=""><?php echo $category_title ?></a></span>
            <h1><?php echo $event_title ?></h1>
            <hr class="rod">
            <span class="pub">By <?php echo $profile_display_name ?></span>
            <span class="pub">Event date : <?php echo date('d M Y', strtotime($event_start_date)); ?></span>
        </div>

        <div class="img-story">
            <img src="<?php echo $event_image ?>">
        </div>

        <div class="story-pg">
            <p><?php echo $event_description ?></p>
        </div>

    </div>
</section>

<?php

    include_once 'footer.php';

?>