<?php

require_once 'header.php';

?>

<section class="page-header">
    <div class="page-header-bg" style="background-image: url(<?php echo $event_image ?>)">
    </div>
    <div class="container"></div>
    <div class="page-header__inner">
        <ul class="thm-breadcrumb list-unstyled">
            <li><a href="/">Home</a></li>
            <li><span>/</span></li>
            <li><a href="/events/">Events</a></li>
            <li><span>/</span></li>
            <li class="active"><?php echo $event_title ?></li>
        </ul>
        <h2><?php echo $event_title ?></h2>
    </div>
    </div>
</section>

<section class="event-details">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="event-details__left">
                    <div class="event-details__top">
                        <div class="event-details__date">
                            <p><?php echo date('D M, Y', strtotime($event_start_date)) ?></p>
                        </div>
                        <h3 class="event-details__title"><?php echo $event_title ?></h3>
                        <p class="event-details__text-1 postDescription">
                            <?php echo $event_description ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="event-details__right">
                    <div class="event-details__info">
                        <ul class="list-unstyled event-details__info-list">
                            <li>
                                <span>Time:</span>
                                <p><?php echo $event_start_time ?></p>
                            </li>
                            <li>
                                <span>Date:</span>
                                <p><?php echo date('D M, Y', strtotime($event_start_date)); ?></p>
                            </li>
                            <li>
                                <span>Category:</span>
                                <p><?php echo $category_title ?></p>
                            </li>
                            <li>
                                <span>Location:</span>
                                <p><?php echo $event_location ?></p>
                            </li>
                        </ul>
                        <div class="event-details__social">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo SITE_URL . $link_url ?>"><i
                                    class="fab fa-facebook"></i></a>
                            <a
                                href="http://twitter.com/share?text=<?php echo urlencode($event_title) ?>&url=<?php echo SITE_URL . $link_url ?>"><i
                                    class="fab fa-twitter"></i></a>
                            <a
                                href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo SITE_URL . $link_url ?>&title=<?php echo urlencode($event_title) ?>"><i
                                    class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>