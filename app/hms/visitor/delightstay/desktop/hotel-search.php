<?php

$location = $urlStructure[0];
$room = ((isset($room)) ? $room : 1);
$guests = ((isset($guests)) ? $guests : 1);

if (isset($date)) {
    $date = explode(" - ", $date);
    $startDate = $date[0];
    $endDate = $date[1];
} else {
    $startDate = date('d-m-Y');
    $endDate = date('d-m-Y', strtotime('+1 Day'));
}

if (!$checkProperty = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "property WHERE property_city LIKE '%{$location}%' AND property_status=1 AND property_site_id=" . SITE_ID)) {
    echo "Something went wrong";
    exit();
}

require_once 'header.php';
?>

<main>

    <section class="search_page_form horizontal_padding ">
        <div class="container">
            <form action="/redirect/" class="searchEngine" method="get">
                <?php

                if (SITE_VERSION == 'desktop') { ?>


                <div class="row where_section p-0 py-2 d-flex align-items-center text-black mt-3 ">
                    <div class="col-6 col-md-3 ps-5">
                        <label for="">Where</label>
                        <input type="text" class="form-control" name="location" id="datepicker"
                            placeholder="Search destinations" value="<?php echo ucwords($location) ?>" required>
                    </div>
                    <div class="col-6 col-md-4 border_left px-5 ">
                        <label for="">Check-In & Check-out</label>
                        <input type="text" name="date" value="<?php echo $date ?>" class="form-control datepicker"
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
    <section class="listing_wrapper">
        <div class="container">
            <div class="row">
                <!-- <div class="col-xl-3 col-lg-4">
                    <div class="listing_wrapper__left">
                        <div class="filter_box__top">
                            <h3>filter</h3>
                            <h6>clear</h6>
                        </div>
                        <div class="filter_box">
                            <div class="filter_box__wrapper">
                                <div class="filter_sub-heading">
                                    <img src="<?php echo USER_IMAGES ?>arrow_right_2.png" class="img-fluid" alt="">
                                    <h3>popular filter</h3>
                                </div>
                                <div class="filter_content">
                                    <div class="filter_check">
                                        <input type="checkbox" name="" id="">
                                        <label for="">Upto ₹2000</label>
                                    </div>
                                    <div class="filter_check">
                                        <input type="checkbox" name="" id="">
                                        <label for="">Upto ₹2000</label>
                                    </div>
                                    <div class="filter_check">
                                        <input type="checkbox" name="" id="">
                                        <label for="">Upto ₹2000</label>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="filter_box">
                            <div class="filter_box__wrapper">
                                <div class="filter_sub-heading">
                                    <img src="<?php echo USER_IMAGES ?>arrow_right_2.png" class="img-fluid" alt="">
                                    <h3>popular filter</h3>
                                </div>
                                <div class="filter_content">
                                    <h6>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quis, quos?</h6>
                                </div>
                            </div>
                        </div>
                        <div class="filter_box">
                            <div class="filter_box__wrapper">
                                <div class="filter_sub-heading">
                                    <img src="<?php echo USER_IMAGES ?>arrow_right_2.png" class="img-fluid" alt="">
                                    <h3>customer rating</h3>
                                </div>
                            </div>
                        </div>

                        <div class="filter_box">
                            <div class="filter_box__wrapper">
                                <div class="filter_sub-heading">
                                    <img src="<?php echo USER_IMAGES ?>arrow_right_2.png" class="img-fluid" alt="">
                                    <h3>top transits</h3>
                                </div>
                            </div>
                        </div>
                        <div class="filter_box">
                            <div class="filter_box__wrapper">
                                <div class="filter_sub-heading">
                                    <img src="<?php echo USER_IMAGES ?>arrow_right_2.png" class="img-fluid" alt="">
                                    <h3>star rating</h3>
                                </div>
                            </div>
                        </div>
                        <div class="filter_box">
                            <div class="filter_box__wrapper">
                                <div class="filter_sub-heading">
                                    <img src="<?php echo USER_IMAGES ?>arrow_right_2.png" class="img-fluid" alt="">
                                    <h3>property type</h3>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="mob_filter">
                        <div class="mob_filter__heading">
                            <h3 class="myFilter"><i class='bx bx-filter'></i>Filters</h3>
                            <h6>clear</h6>
                        </div>
                        <div class="filter_slide">
                            <div class="filter_slide__container">
                                <div class="filter_box__top">
                                    <h3>filter</h3>
                                    <div class="cancel">
                                        <i class='bx bx-x'></i>
                                    </div>
                                </div>
                                <div class="filter_box">
                                    <div class="filter_box__wrapper">
                                        <div class="filter_sub-heading">
                                            <img src="<?php echo USER_IMAGES ?>arrow_right_2.png" class="img-fluid"
                                                alt="">
                                            <h3>popular filter</h3>
                                        </div>
                                        <div class="filter_content">
                                            <div class="filter_check">
                                                <input type="checkbox" name="" id="">
                                                <label for="">Upto ₹2000</label>
                                            </div>
                                            <div class="filter_check">
                                                <input type="checkbox" name="" id="">
                                                <label for="">Upto ₹2000</label>
                                            </div>
                                            <div class="filter_check">
                                                <input type="checkbox" name="" id="">
                                                <label for="">Upto ₹2000</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="filter_box">
                                    <div class="filter_box__wrapper">
                                        <div class="filter_sub-heading">
                                            <img src="<?php echo USER_IMAGES ?>arrow_right_2.png" class="img-fluid"
                                                alt="">
                                            <h3>popular filter</h3>
                                        </div>
                                        <div class="filter_content">
                                            <h6>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quis,
                                                quos?</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter_box">
                                    <div class="filter_box__wrapper">
                                        <div class="filter_sub-heading">
                                            <img src="<?php echo USER_IMAGES ?>arrow_right_2.png" class="img-fluid"
                                                alt="">
                                            <h3>customer rating</h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="filter_box">
                                    <div class="filter_box__wrapper">
                                        <div class="filter_sub-heading">
                                            <img src="<?php echo USER_IMAGES ?>arrow_right_2.png" class="img-fluid"
                                                alt="">
                                            <h3>top transits</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter_box">
                                    <div class="filter_box__wrapper">
                                        <div class="filter_sub-heading">
                                            <img src="<?php echo USER_IMAGES ?>arrow_right_2.png" class="img-fluid"
                                                alt="">
                                            <h3>star rating</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter_box">
                                    <div class="filter_box__wrapper">
                                        <div class="filter_sub-heading">
                                            <img src="<?php echo USER_IMAGES ?>arrow_right_2.png" class="img-fluid"
                                                alt="">
                                            <h3>property type</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="col-xl-12 col-lg-12">
                    <div class="listing_wrapper__right">
                        <div class="row">
                            <?php

                            if (mysqli_num_rows($checkProperty) != 0) {
                                while ($hotelDetails = mysqli_fetch_assoc($checkProperty)) {
                                    extract($hotelDetails); ?>
                            <div class="col-md-4 col-12">
                                <div class="listing_card">
                                    <div class="listing_card__img">
                                        <img src="assest/img/card2.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="listing_details">
                                        <div class="listing_top">
                                            <h3>Tambon Hua Hin, Thailand</h3>
                                            <!-- <p>1.0k <span> rating</span></p> -->
                                        </div>
                                        <div class="listing_star">
                                            <div class="listing_star__left">
                                                <h3>4 <span>-star hotel</span></h3>
                                                <h6><img src="assest/img/map.png" class="img-fluid" alt="">karol
                                                    bagh
                                                </h6>
                                            </div>
                                            <!-- <div class="rating_number">
                                                <p>7.7</p>
                                            </div> -->
                                        </div>
                                        <!-- <div class="listing_star">
                                            <div class="listing_star__left">
                                                <h6><img src="assest/img/pool.png" class="img-fluid" alt="">swimming
                                                    pool</h6>
                                                <h6><img src="assest/img/resturant.png" class="img-fluid"
                                                        alt="">restaurants</h6>
                                            </div>
                                            <div class="more">
                                                <a href="#">+ more</a>
                                            </div>
                                        </div> -->
                                        <div class="price">
                                            <h3>₹ 2,178 <span>/night</span></h3>
                                            <del>₹ 3,545</del>
                                        </div>
                                        <div class="book_now">
                                            <a href="hoteldetails.html" class="global_btn">Book Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                }
                            } else {
                                echo "<div class='not-found'><h3>No property found</h3></div>";
                            }

                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
                            <img src="assest/img/Arrow - Right.png" class="img-fluid" alt="">
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