<?php

require_once 'header.php';

?>

<!--Page Header Start-->
<section class="page-header">
    <!-- background-image: url('path_of_image') of event/archivment -->
    <div class="page-header-bg" style="background-image: url(<?php echo USER_IMAGES ?>img-1/49-1-scaled.jpg)">
    </div>
    <div class="container">
        <div class="page-header__inner">
            <h2>Events</h2>
        </div>
    </div>
</section>
<!--Page Header End-->

<!--Events Page Start-->
<section class="events-page">
    <div class="container">
        <div class="row">
            <div class="col-xl-4 col-lg-6 col-md-6">
                <!--Events One Single Start-->
                <div class="events-one__single">
                    <div class="events-one__img">
                        <img src="<?php echo USER_IMAGES ?>img-1/event-1-1.jpg" alt="">
                        <div class="events-one__date">
                            <!-- event/archivment date -->
                            <p>23 May, 2022</p>
                        </div>
                        <div class="events-one__content">
                            <ul class="list-unstyled events-one__meta">
                                <!-- event/archivment time -->
                                <li><i class="fas fa-clock"></i>8:00pm</li>
                                <!-- event/archivment location -->
                                <li><i class="fas fa-map-marker-alt"></i>New York</li>
                            </ul>
                            <!-- event/archivment title -->
                            <h3 class="events-one__title"><a href="event-details/">Play for the
                                    world
                                    with us</a></h3>
                        </div>
                    </div>
                </div>
                <!--Events One Single End-->
            </div>
        </div>
    </div>
</section>
<!--Events Page End-->
<?php

require_once 'footer.php';

?>