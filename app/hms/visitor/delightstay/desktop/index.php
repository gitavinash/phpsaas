<?php

require_once 'header.php';

?>
<main>
    <section class="hero_img horizontal_padding ">
        <div class="container">
            <div class="row text-white ">
                <h1 class="font_size_70 font_family_chonburi text-center mt-5 text-capitalize">Perfect getaway
                    awaits at MGI Hotels.</h1>
                <p class="text-center font_size_18 text-capitalize py-3">Find bliss in every stay. From cozy corners to
                    lavish suites, MGI Hotels ensures your comfort.<br />Book now for an unforgettable experience.</p>
            </div>
            <form action="/redirect/" class="searchEngine" method="get">
                <?php

                if (SITE_VERSION == 'desktop') { ?>


                <div class="row where_section p-0 py-2 d-flex align-items-center text-black mt-3 ">
                    <div class="col-6 col-md-3 ps-5">
                        <label for="">Where</label>
                        <input type="text" class="form-control" name="location" id="datepicker"
                            placeholder="Search destinations" required>
                    </div>
                    <div class="col-6 col-md-4 border_left px-5 ">
                        <label for="">Check-In & Check-out</label>
                        <input type="text" name="date" value="" class="form-control datepicker"
                            placeholder="Check-in date">
                    </div>
                    <div class="col-6 col-md-2 border_left px-5">
                        <label for="">No. of guests</label>
                        <select class="form-control" name="guests" id="">
                            <option value="1">1 person</option>
                            <option value="2">2 persons</option>
                            <option value="3">3 persons</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2 border_left px-5">
                        <label for="">No. of rooms</label>
                        <select class="form-control" name="room" id="">
                            <option value="1">1 room</option>
                            <option value="2">2 rooms</option>
                            <option value="3">3 rooms</option>
                        </select>
                    </div>
                    <div class="col-1 ps-2 ps-md-2 ps-lg-0 ps-xl-3 ps-xxl-4 ">
                        <button class="search_border bg_orange d-flex justify-content-center align-items-center"><i
                                class="bx bx-search"></i></button>
                    </div>
                </div>

                <?php
                }

                if (SITE_VERSION == 'mobile') { ?>

                <div class="row where_section_mobile m-auto py-4 mt-5">
                    <div class="col-12">
                        <label for="">Where</label>
                        <input type="text" class="form-control py-3 " id="datepicker" placeholder="Search destinations"
                            required>
                    </div>
                    <div class="col-12 py-2">
                        <label for="">Check-In & Check-out</label>
                        <input type="text" name="birthday" value="" class="form-control datepicker py-3" placeholder="">
                    </div>
                    <div class="col-12 py-4">
                        <label for="">No. of guests</label>
                        <select class="form-control" name="guests" id="">
                            <option value="1">1 person</option>
                            <option value="2">2 persons</option>
                            <option value="3">3 persons</option>
                        </select>
                    </div>
                    <div class="col-12 py-4">
                        <label for="">No. of rooms</label>
                        <select class="form-control" name="room" id="">
                            <option value="1">1 room</option>
                            <option value="2">2 rooms</option>
                            <option value="3">3 rooms</option>
                        </select>
                    </div>
                    <div class="search_btn bg_orange  py-3  text-center mt-4 ">
                        <!-- <a href="" class="font_size_20 text-white">Search</a> -->
                        <button class="font_size_20 text-white"><i class="bx bx-search"></i></button>
                        <!-- <input type="submit" class="font_size_20 text-white" value="Search"> -->
                    </div>
                </div>
                <?php
                }

                ?>

            </form>
        </div>
        </div>
    </section>

    <section class="bodyy_background_color vertical_padding horizontal_padding">
        <div class="container">
            <div class="row py-5 py-md-5 py-lg-3  ">
                <div class="col-12 col-md-12 col-lg-3 d-flex align-items-center justify-content-center">
                    <h3 class="font_size_30 pb-2 pb-md-4 pb-lg-0 text-capitalize">Find and book <br class="brek_none_1">
                        your perfect stay</h3>
                </div>
                <div class="col-12 col-md-4 col-lg-3 book_img position-relative ">
                    <span class="font_size_15 save_position text-white ">Ease and Convenience</span>
                    <img src="<?php echo USER_IMAGES ?>book_img1.png" alt="" class="img-fluid">
                </div>
                <div class="col-12 col-md-4 col-lg-3 book_img position-relative">
                    <span class="font_size_15 save_position text-white">Tailored Options</span>
                    <img src="<?php echo USER_IMAGES ?>book_img2.png" alt="" class="img-fluid">
                </div>
                <div class="col-12 col-md-4 col-lg-3 book_img position-relative">
                    <span class="font_size_15 save_position text-white">Trust and Assurance</span>
                    <img src="<?php echo USER_IMAGES ?>book_img3.png" alt="" class="img-fluid">
                </div>
            </div>

            <div class=" bg-white  find_margin ">
                <h2 class="font_size_35 ps-4">Explore India</h2>

                <div class="row gx-0 justify-content-md-between p-0 explore">
                    <div class="col-6 col-md-3 flex-column flex-md-column flex-lg-row d-lg-flex justify-content-around">
                        <div class="d-flex flex-column align-items-center">
                            <img src="<?php echo USER_IMAGES ?>icon/explore_img1.svg" alt="" class="img-fluid  pt-5">
                            <p class="font_size_18 py-3 text-center">Jim Corbett</p>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <img src="<?php echo USER_IMAGES ?>icon/explore_img7.svg" alt="" class="img-fluid  pt-5">
                            <p class="font_size_18 py-3 text-center">Nainital</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 flex-column flex-md-column flex-lg-row d-lg-flex justify-content-around">
                        <div class="d-flex flex-column align-items-center">
                            <img src="<?php echo USER_IMAGES ?>icon/explore_img3.svg" alt="" class="img-fluid  pt-5">
                            <p class="font_size_18 py-3 text-center">Agra</p>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <img src="<?php echo USER_IMAGES ?>icon/explore_img2.svg" alt="" class="img-fluid  pt-5">
                            <p class="font_size_18 py-3 text-center">Delhi</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 flex-column flex-md-column flex-lg-row d-lg-flex justify-content-around">
                        <div class="d-flex flex-column align-items-center">
                            <img src="<?php echo USER_IMAGES ?>icon/explore_img6.svg" alt="" class="img-fluid  pt-5">
                            <p class="font_size_18 py-3 text-center">Jaipur</p>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <img src="<?php echo USER_IMAGES ?>icon/explore_img4.svg" alt="" class="img-fluid  pt-5">
                            <p class="font_size_18 py-3 text-center">Amritsar</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 flex-column flex-md-column flex-lg-row d-lg-flex justify-content-around">
                        <div class="d-flex flex-column align-items-center">
                            <img src="<?php echo USER_IMAGES ?>icon/explore_img4.svg" alt="" class="img-fluid  pt-5">
                            <p class="font_size_18 py-3 text-center">Rishikesh</p>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <img src="<?php echo USER_IMAGES ?>icon/explore_img4.svg" alt="" class="img-fluid  pt-5">
                            <p class="font_size_18 py-3 text-center">Haridwar</p>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <!-- -------------------card---------------- -->

    <section class="body_background_color vertical_padding horizontal_padding">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="font_size_35">Offers</h2>
                <div class="d-flex align-items-center arrow font_size_18">
                    <a href="">View all</a>
                    <i class='bx bx-right-arrow-alt'></i>
                </div>
            </div>
            <div class="row offer_slider overflow-hidden">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card_manage mt-5 ">
                        <img src="<?php echo USER_IMAGES ?>card_img1.png" alt="" class="img-fluid ">
                        <p class="font_size_18 pt-3">Tambon Hua Hin, Thailand</p>
                        <p class="font_size_18 opacity-50 py-2">2,953 kilometres away <br>
                            15–20 Jan
                        </p>
                        <div class="d-flex align-items-center justify-content-between py-2">
                            <h4 class="font_size_22 ">₹30,425 <span class="font_size_18 opacity-50">night</span>
                            </h4>
                            <div>
                                <i class='bx bxs-star font_size_18 orange'></i> <span>4.85</span>
                            </div>
                        </div>
                        <div class="button d-flex align-items-center justify-content-between py-2">
                            <a href="" class="btn_book font_size_18 orange ">BOOK NOW</a>
                            <i class='bx bx-right-arrow-alt d-flex align-items-center justify-content-center'></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 ">
                    <div class="card_manage mt-5">
                        <img src="<?php echo USER_IMAGES ?>card_img2.png" alt="" class="img-fluid">
                        <p class="font_size_18 pt-3">Tambon Hua Hin, Thailand</p>
                        <p class="font_size_18 opacity-50 py-2">2,953 kilometres away <br>
                            15–20 Jan
                        </p>
                        <div class="d-flex align-items-center justify-content-between py-2">
                            <h4 class="font_size_22 ">₹30,425 <span class="font_size_18 opacity-50">night</span>
                            </h4>
                            <div>
                                <i class='bx bxs-star font_size_18 orange'></i> <span>4.85</span>
                            </div>
                        </div>
                        <div class="button d-flex align-items-center justify-content-between py-2">
                            <a href="" class="btn_book font_size_18 orange ">BOOK NOW</a>
                            <i class='bx bx-right-arrow-alt d-flex align-items-center justify-content-center'></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card_manage mt-5">
                        <img src="<?php echo USER_IMAGES ?>card_img3.png" alt="" class="img-fluid">
                        <p class="font_size_18 pt-3">Tambon Hua Hin, Thailand</p>
                        <p class="font_size_18 opacity-50 py-2">2,953 kilometres away <br>
                            15–20 Jan
                        </p>
                        <div class="d-flex align-items-center justify-content-between py-2">
                            <h4 class="font_size_22 ">₹30,425 <span class="font_size_18 opacity-50">night</span>
                            </h4>
                            <div>
                                <i class='bx bxs-star font_size_18 orange'></i> <span>4.85</span>
                            </div>
                        </div>
                        <div class="button d-flex align-items-center justify-content-between py-2">
                            <a href="" class="btn_book font_size_18 orange ">BOOK NOW</a>
                            <i class='bx bx-right-arrow-alt d-flex align-items-center justify-content-center'></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card_manage mt-5">
                        <img src="<?php echo USER_IMAGES ?>card_img4.png" alt="" class="img-fluid">
                        <p class="font_size_18 pt-3">Tambon Hua Hin, Thailand</p>
                        <p class="font_size_18 opacity-50 py-2">2,953 kilometres away <br>
                            15–20 Jan
                        </p>
                        <div class="d-flex align-items-center justify-content-between py-2">
                            <h4 class="font_size_22 ">₹30,425 <span class="font_size_18 opacity-50">night</span>
                            </h4>
                            <div>
                                <i class='bx bxs-star font_size_18 orange'></i> <span>4.85</span>
                            </div>
                        </div>
                        <div class="button d-flex align-items-center justify-content-between py-2">
                            <a href="" class="btn_book font_size_18 orange ">BOOK NOW</a>
                            <i class='bx bx-right-arrow-alt d-flex align-items-center justify-content-center'></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card_manage mt-5">
                        <img src="<?php echo USER_IMAGES ?>card_img3.png" alt="" class="img-fluid">
                        <p class="font_size_18 pt-3">Tambon Hua Hin, Thailand</p>
                        <p class="font_size_18 opacity-50 py-2">2,953 kilometres away <br>
                            15–20 Jan
                        </p>
                        <div class="d-flex align-items-center justify-content-between py-2">
                            <h4 class="font_size_22 ">₹30,425 <span class="font_size_18 opacity-50">night</span>
                            </h4>
                            <div>
                                <i class='bx bxs-star font_size_18 orange'></i> <span>4.85</span>
                            </div>
                        </div>
                        <div class="button d-flex align-items-center justify-content-between py-2">
                            <a href="" class="btn_book font_size_18 orange ">BOOK NOW</a>
                            <i class='bx bx-right-arrow-alt d-flex align-items-center justify-content-center'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <section class="body_background_color vertical_padding horizontal_padding feel_wrapper">
        <!-- <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="font_size_35">Feel at home wherever you go</h2>
                <div class="d-flex align-items-center arrow font_size_18">
                    <a href="">View all</a>
                    <i class='bx bx-right-arrow-alt'></i>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card_manage mt-5">
                        <img src="<?php echo USER_IMAGES ?>feel_img.png" alt="" class="img-fluid">

                        <div class="jim_padding pt-3   ">
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <h4 class="font_size_22 py-2">Jim Corbett</h4>
                                <div>
                                    <i class='bx bxs-star font_size_18 orange'></i> <span>4.85</span>
                                </div>
                            </div>
                            <p class="font_size_18 opacity-50 pb-3">166,514 holiday rentals</p>
                            <div class="button text-center py-2">
                                <a href="" class="btn_book font_size_18 orange w-100">BOOK NOW</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card_manage mt-5">
                        <img src="<?php echo USER_IMAGES ?>feel_img1.png" alt="" class="img-fluid">

                        <div class="jim_padding pt-3   ">
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <h4 class="font_size_22 py-2">Jim Corbett</h4>
                                <div>
                                    <i class='bx bxs-star font_size_18 orange'></i> <span>4.85</span>
                                </div>
                            </div>
                            <p class="font_size_18 opacity-50 pb-3">166,514 holiday rentals</p>
                            <div class="button text-center py-2">
                                <a href="" class="btn_book font_size_18 orange w-100">BOOK NOW</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card_manage mt-5">
                        <img src="<?php echo USER_IMAGES ?>feel_img2.png" alt="" class="img-fluid">

                        <div class="jim_padding pt-3   ">
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <h4 class="font_size_22 py-2">Jim Corbett</h4>
                                <div>
                                    <i class='bx bxs-star font_size_18 orange'></i> <span>4.85</span>
                                </div>
                            </div>
                            <p class="font_size_18 opacity-50 pb-3">166,514 holiday rentals</p>
                            <div class="button text-center py-2">
                                <a href="" class="btn_book font_size_18 orange w-100">BOOK NOW</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card_manage mt-5">
                        <img src="<?php echo USER_IMAGES ?>feel_img3.png" alt="" class="img-fluid">

                        <div class="jim_padding pt-3   ">
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <h4 class="font_size_22 py-2">Jim Corbett</h4>
                                <div>
                                    <i class='bx bxs-star font_size_18 orange'></i> <span>4.85</span>
                                </div>
                            </div>
                            <p class="font_size_18 opacity-50 pb-3">166,514 holiday rentals</p>
                            <div class="button text-center py-2">
                                <a href="" class="btn_book font_size_18 orange w-100">BOOK NOW</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </section>

    <!-- ------------------testimonial-------------------------- -->
    <section class="slider_bg">
        <div class="container">
            <div class="row align-items-center justify-content-center overflow-hidden">
                <div class="col-12 col-md-4 col-lg-4">
                    <h2
                        class="font_size_35 text-white d-flex align-items-center justify-content-center justify-content-md-start justify-content-lg-start mt-5 mt-md-0 mt-lg-0">
                        Testimonials
                    </h2>
                </div>
                <div class="col-12 col-md-8 col-lg-8 ">
                    <div class="row testimonial ">
                        <div class="col-6 slider_2">
                            <div class="slider mx-5 mx-md-2 mx-lg-2  ">
                                <p class="review">From the moment I checked in until my departure, every interaction
                                    with the staff at MGI Hotels was marked by professionalism and warmth. Their
                                    attention to detail and willingness to accommodate special requests made my stay
                                    truly memorable.</p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                    </div>
                                    <div>
                                        <img src="<?php echo USER_IMAGES ?>icon/coma.svg" alt="">
                                    </div>
                                </div>
                                <h5 class="font_size_20 py-2">Ravi Patel</h5>
                            </div>

                        </div>
                        <div class="col-6">
                            <div class="slider mx-5 mx-md-2 mx-lg-2 ">
                                <p class="review">MGI Hotels exceeded all my expectations. The room was spacious,
                                    elegantly
                                    furnished, and offered breathtaking views. The attention to detail and personalized
                                    service made me feel like a VIP throughout my stay. Highly recommend!</p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                    </div>
                                    <div>
                                        <img src="<?php echo USER_IMAGES ?>icon/coma.svg" alt="">
                                    </div>
                                </div>
                                <h5 class="font_size_20 py-2">Jessica Stevens</h5>
                            </div>

                        </div>
                        <div class="col-6">
                            <div class="slider mx-5 mx-md-2 mx-lg-2  ">
                                <p class="review">My stay at MGI Hotels was truly exceptional. From the moment I
                                    arrived, I was
                                    greeted with warmth and hospitality. The room was impeccably clean, the amenities
                                    were top-notch, and the staff went above and beyond to ensure my comfort. I can't
                                    wait to return!
                                </p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                        <i class='bx bxs-star font_size_18 orange'></i>
                                    </div>
                                    <div>
                                        <img src="<?php echo USER_IMAGES ?>icon/coma.svg" alt="">
                                    </div>
                                </div>
                                <h5 class="font_size_20 py-2">Daniel Hayes</h5>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- subscribe -->
    <section class="subscribe_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="subscribe_wrapper__left">
                        <h3>Subscribe to our Newsletter</h3>
                        <p>Come and travel with us for a truly memorable holiday.</p>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="subscribe_wrapper__right">
                        <input type="email" placeholder="Email Address" name="" id="">
                        <div class="email_arrow">
                            <img src="<?php echo USER_IMAGES ?>arrow_right.png" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
<?php

require_once 'footer.php';

?>