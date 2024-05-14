<?php

require_once 'header.php';

?>

<!--Page Header Start-->
<section class="page-header">
    <!-- background-image: url('path_of_image') of event/archivment -->
    <div class="page-header-bg" style="background-image: url(<?php echo $category_image ?>)">
    </div>
    <div class="container">
        <div class="page-header__inner">
            <h2><?php echo $category_title ?></h2>
        </div>
    </div>
</section>
<!--Page Header End-->


<section class="event-details">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="event-details__left">
                    <div class="event-details__top">
                        <h3 class="event-details__title"><?php echo $category_title ?></h3>
                        <p class="event-details__text-1 postDescription">
                            <?php echo $category_description ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!--Events Page Start-->
<section class="events-page">
    <div class="container">
        <div class="row">
            <?php

            if ($eventList = mysqli_query($connection, "SELECT event_title, event_image, event_start_date, event_start_time, event_location, link_url FROM " . TABLE_PREFIX . "event INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=event_id WHERE event_category='{$category_id}' AND link_status=200 AND link_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($eventList) != 0) {
                    while ($eventData = mysqli_fetch_assoc($eventList)) {
                        extract($eventData);
                        echo "<div class='col-xl-4 col-lg-6 col-md-6'>
                <div class='events-one__single'>
                    <div class='events-one__img'>
                        <img src='{$event_image}' alt='{$event_title}'>
            <div class='events-one__date'>
                <p>" . date('D M, Y', strtotime($event_start_date)) . "23 May, 2022</p>
            </div>
            <div class='events-one__content'>
                <ul class='list-unstyled events-one__meta'>
                    <li><i class='fas fa-clock'></i>{$event_start_time}</li>
                    <li><i class='fas fa-map-marker-alt'></i>{$event_location}</li>
                </ul>
                <h3 class='events-one__title'><a href='{$link_url}'>{$event_title}</a></h3>
            </div>
        </div>
    </div>
    </div>";
                    }
                } else {
                    echo "<section class='page-header'>
        <div class='page-header-bg' style='background-image: url('')'>
        </div>
        <div class='container'>
            <div class='page-header__inner'>
                <h2>No events found for {$category_title}</h2>
            </div>
        </div>
    </section>";
                }
            }

            ?>
        </div>
    </div>
</section>
<!--Events Page End-->
<?php

require_once 'footer.php';

?>