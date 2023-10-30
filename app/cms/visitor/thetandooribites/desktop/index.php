<?php

require_once 'header.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>The Tandoori Bites </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <section class="slider-home">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner">
                <!-- <div class="carousel-item active">
            <img class="d-block w-100 img-fluid" src="" alt="First slide">
            <div class="overlay"></div>
          </div> -->

                <div class="carousel-item active">
                    <img src="<?php echo USER_IMAGES ?>bg_1.jpg" alt="..." class="img-fluid">
                    <div class="carousel-caption  d-md-block">


                        <span class="subheading isfont">The Tandoori Bites</span>
                        <h1 class="mb-4">Best Restaurant</h1>
                    </div>
                    <div class="Slider-overlay"></div>
                </div>


                <div class="carousel-item">
                    <img src="<?php echo USER_IMAGES ?>bg_2.jpg" alt="..." class="img-fluid">
                    <div class="carousel-caption  d-md-block">
                        <span class="subheading isfont">The Tandoori Bites</span>
                        <h1 class="mb-4">Best Restaurant</h1>
                    </div>
                    <div class="Slider-overlay"></div>
                </div>


                <div class="carousel-item ">
                    <img src="<?php echo USER_IMAGES ?>bg_8.jpg" alt="..." class="img-fluid">
                    <div class="carousel-caption  d-md-block">
                        <span class="subheading isfont">The Tandoori Bites</span>
                        <h1 class="mb-4">Best Restaurant</h1>
                    </div>
                    <div class="Slider-overlay"></div>
                </div>

            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>

    </section>


    <!-- <section class="home-slider owl-carousel js-fullheight " >
      <div class="slider-item js-fullheight" style="background-image: url(<?php echo USER_IMAGES ?>bg_1.jpg);">
      	<div class="overlay"></div>
        <div class="container">
          <div class="row slider-text js-fullheight justify-content-center align-items-center" data-scrollax-parent="true">

            <div class="col-md-12 col-sm-12 text-center ftco-animate">
            	<span class="subheading">The Tandoori Bites</span>
              <h1 class="mb-4">Best Restaurant</h1>
            </div>

          </div>
        </div>
      </div>

      <div class="slider-item js-fullheight" style="background-image: url(<?php echo USER_IMAGES ?>bg_2.jpg);">
      	<div class="overlay"></div>
        <div class="container">
          <div class="row slider-text js-fullheight justify-content-center align-items-center" data-scrollax-parent="true">

            <div class="col-md-12 col-sm-12 text-center ftco-animate">
            	<span class="subheading">The Tandoori Bites</span>
              <h1 class="mb-4">Nutritious &amp; Tasty</h1>
            </div>

          </div>
        </div>
      </div>

      <div class="slider-item js-fullheight" style="background-image: url(<?php echo USER_IMAGES ?>bg_8.jpg);">
      	<div class="overlay"></div>
        <div class="container">
          <div class="row slider-text justify-content-center align-items-center" data-scrollax-parent="true">

            <div class="col-md-12 col-sm-12 text-center ftco-animate">
            	<span class="subheading">The Tandoori Bites</span>
              <h1 class="mb-4">Delicious Specialties</h1>
            </div>

          </div>
        </div>
      </div>
    </section>  -->




    <section class="ftco-section ftco-wrap-about">
        <div class="container">
            <div class="row">
                <div class="col-md-7 d-flex">
                    <img src="<?php echo USER_IMAGES ?>undraw_chef_cu-0-r.svg" alt="" class="img-fluid">
                </div>
                <div class="col-md-5 wrap-about pt-5 pt-md-5 pb-md-3 ftco-animate">
                    <div class="heading-section mb-4 my-5 my-md-0">
                        <span class="subheading">About</span>
                        <h2 class="mb-4">The Tandoori Bites Restaurant</h2>
                    </div>
                    <!-- <p>A small river named Duden flows by their place and supplies it with the necessary regelialia. It
                        is a paradisematic country, in which roasted parts of sentences fly into your mouth.</p> -->
                    <pc class="time">
                        <span>Monday - Sunday <strong>06:00 PM - 11:30 PM</strong></span>
                        <span><a href="tel:+91-9310406027">+91 93104 06027</a></span>
                        </p>
                </div>
            </div>
        </div>
    </section>


    <!-- <section class="ftco-section ftco-counter img ftco-no-pt" id="section-counter">
        <div class="container">
            <div class="row d-md-flex">
                <div class="col-md-9">
                    <div class="row d-md-flex align-items-center">
                        <div
                            class="col-6  col-md-6 col-lg-3 mb-4 mb-lg-0 d-flex justify-content-center counter-wrap ftco-animate">
                            <div class="block-18">
                                <div class="text">
                                    <strong class="number" data-number="18">0</strong>
                                    <span>Years of Experienced</span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-6 col-md-6 col-lg-3 mb-4 mb-lg-0 d-flex justify-content-center counter-wrap ftco-animate">
                            <div class="block-18">
                                <div class="text">
                                    <strong class="number" data-number="100">0</strong>
                                    <span>Menus/Dish</span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-6 col-md-6 col-lg-3 mb-4 mb-lg-0 d-flex justify-content-center counter-wrap ftco-animate">
                            <div class="block-18">
                                <div class="text">
                                    <strong class="number" data-number="50">0</strong>
                                    <span>Staffs</span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-6 col-md-6 col-lg-3 mb-4 mb-lg-0 d-flex justify-content-center counter-wrap ftco-animate">
                            <div class="block-18">
                                <div class="text">
                                    <strong class="number" data-number="15000">0</strong>
                                    <span>Happy Customers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-center text-md-left">
                    <p>A small river named Duden flows by their place and supplies it with the necessary regelialia.</p>
                </div>
            </div>
        </div>
    </section> -->
    <!-- 
    <section class="ftco-section bg-light">
        <div class="container">
            <div class="row justify-content-center mb-5 pb-2">
                <div class="col-md-12 text-center heading-section ftco-animate">
                    <span class="subheading">Services</span>
                    <h2 class="mb-4">Catering Services</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 d-flex align-self-stretch ftco-animate text-center">
                    <div class="media block-6 services d-block">
                        <div class="icon d-flex justify-content-center align-items-center">
                            <span class="flaticon-cake"></span>
                        </div>
                        <div class="media-body p-2 mt-3">
                            <h3 class="heading">Birthday Party</h3>
                            <p>Even the all-powerful Pointing has no control about the blind texts it is an almost
                                unorthographic.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-self-stretch ftco-animate text-center">
                    <div class="media block-6 services d-block">
                        <div class="icon d-flex justify-content-center align-items-center">
                            <span class="flaticon-meeting"></span>
                        </div>
                        <div class="media-body p-2 mt-3">
                            <h3 class="heading">Business Meetings</h3>
                            <p>Even the all-powerful Pointing has no control about the blind texts it is an almost
                                unorthographic.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-self-stretch ftco-animate text-center">
                    <div class="media block-6 services d-block">
                        <div class="icon d-flex justify-content-center align-items-center">
                            <span class="flaticon-tray"></span>
                        </div>
                        <div class="media-body p-2 mt-3">
                            <h3 class="heading">Wedding Party</h3>
                            <p>Even the all-powerful Pointing has no control about the blind texts it is an almost
                                unorthographic.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <section class="ftco-section">
        <div class="container">
            <div class="row no-gutters justify-content-center mb-5 pb-2">
                <div class="col-md-12 text-center heading-section ftco-animate">
                    <span class="subheading mb-5">Specialties</span>

                </div>
            </div>
            <div class="row my-5">
                <div class="col-12 col-md-6 col-lg-4 mt-4">
                    <img src="<?php echo USER_IMAGES ?>chicken-seekh-kabab-roll.jpg" alt="" class="img-fluid menu-img">
                    <div class="content">
                        <h4 class="text-center">Chicken Seekh Kabab Roll</h4>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4 mt-4">
                    <img src="<?php echo USER_IMAGES ?>paneer-tikka.jpg" alt="" class="img-fluid menu-img">
                    <div class="content">
                        <h4 class="text-center">Panner Tikka</h4>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4 mt-4">
                    <img src="<?php echo USER_IMAGES ?>surmai-fish-tikka.jpg" alt="" class="img-fluid menu-img">
                    <div class="content">
                        <h4 class="text-center">Surmai Fish Tikka</h4>
                    </div>
                </div>


                <div class="col-12 col-md-6 col-lg-4 mt-4">
                    <img src="<?php echo USER_IMAGES ?>tawa-chicken.jpg" alt="" class="img-fluid menu-img">
                    <div class="content">
                        <h4 class="text-center">Tawa Chicken</h4>
                    </div>
                </div>


                <div class="col-12 col-md-6 col-lg-4 mt-4">
                    <img src="<?php echo USER_IMAGES ?>amritsari-kulcha.jpg" alt="" class="img-fluid menu-img">
                    <div class="content">
                        <h4 class="text-center">Amritsari Kulcha<br />( Coming Soon )</h4>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4 mt-4">
                    <img src="<?php echo USER_IMAGES ?>afgani-chicken.jpg" alt="" class="img-fluid menu-img">
                    <div class="content">
                        <h4 class="text-center">Afgani Chicken</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section img" style="background-image: url(<?php echo USER_IMAGES ?>bg_8.jpg)" data-stellar-background-ratio="0.5">
        <div class="container">
            <div class="row d-flex">
                <div class="col-md-7 ftco-animate makereservation p-4 px-md-5 pb-md-5">
                    <div class="heading-section ftco-animate mb-5 text-center">
                        <span class="subheading">Book a table</span>
                        <h2 class="mb-4">Make Reservation</h2>
                    </div>
                    <form action="#">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Name</label>
                                    <input type="text" class="form-control" placeholder="Your Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input type="text" class="form-control" placeholder="Your Email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Phone</label>
                                    <input type="text" class="form-control" placeholder="Phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Phone</label>
                                    <input type="text" class="form-control" id="book_date" placeholder="Date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Time</label>
                                    <input type="text" class="form-control" id="book_time" placeholder="Time">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Person</label>
                                    <div class="select-wrap one-third">
                                        <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                                        <select name="" id="" class="form-control">
                                            <option value="">Person</option>
                                            <option value="">1</option>
                                            <option value="">2</option>
                                            <option value="">3</option>
                                            <option value="">4+</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="form-group text-center">
                                    <input type="submit" value="Make a Reservation" class="btn btn-primary py-3 px-5">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php

    require_once 'footer.php'

    ?>