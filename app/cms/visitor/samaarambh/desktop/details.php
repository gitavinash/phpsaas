<?php

require_once 'header.php';

?>

<!--Page Header Start-->
<section class="page-header">
    <!-- background-image: url('path_of_image') of event/archivment -->
    <div class="page-header-bg" style="background-image: url(<?php echo USER_IMAGES ?>backgrounds/page-header-bg.jpg)">
    </div>
    <div class="container"></div>
    <div class="page-header__inner">
        <ul class="thm-breadcrumb list-unstyled">
            <li><a href="index/">Home</a></li>
            <li><span>/</span></li>
            <!-- Mention event/archivment heading  -->
            <li class="active">Event details</li>
        </ul>
        <!-- Mention event/archivment heading  -->
        <h2>Event details</h2>
    </div>
    </div>
</section>
<!--Page Header End-->

<!--Event Details Start-->
<section class="event-details">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="event-details__left">
                    <div class="event-details__top">
                        <div class="event-details__date">
                            <!-- Mention event/archivment date -->
                            <p>23 May, 2022</p>
                        </div>
                        <!-- Mention event/archivment heading  -->
                        <h3 class="event-details__title">Play for the world</h3>
                        <!-- Mention event/archivment  description  -->
                        <p class="event-details__text-1">Neque porro est qui dolorem ipsum quia quaed inventore
                            veritatis et quasi architecto beatae vitae dicta sunt explicabo. Aelltes port lacus
                            quis enim var sed efficitur turpis gilla sed sit amet finibus eros. Lorem Ipsum is
                            simply dummy text of the printing and typesetting industry.</p>

                        <!-- Mention event/archivment for new line use
                    
                        <p class="event-details__text-1">
                        </p>

                        -->
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="event-details__right">
                    <div class="event-details__info">
                        <ul class="list-unstyled event-details__info-list">
                            <!-- Mention event/archivment details  -->
                            <li>
                                <span>Time:</span>
                                <p>8:00 am to 12:30 pm</p>
                            </li>
                            <li>
                                <span>Date:</span>
                                <p>23 May, 2022</p>
                            </li>
                            <li>
                                <span>Category:</span>
                                <p>Health, Medical</p>
                            </li>
                            <li>
                                <span>Location:</span>
                                <p>Broklyn, New York</p>
                            </li>
                        </ul>
                        <!-- Mention event/archivment social media page links  -->
                        <div class="event-details__social">
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-pinterest-p"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <!-- Mention event/archivment location  -->
                    <div class="event-details__map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4562.753041141002!2d-118.80123790098536!3d34.152323469614075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80e82469c2162619%3A0xba03efb7998eef6d!2sCostco+Wholesale!5e0!3m2!1sbn!2sbd!4v1562518641290!5m2!1sbn!2sbd"
                            class="event-details-map__one" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Event Details End-->
<?php

require_once 'footer.php';

?>