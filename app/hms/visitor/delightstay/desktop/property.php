<?php

require_once 'header.php';

?>

<main>
    <section class="details_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <div class="details_wrapper__left">
                        <div class="details_top">
                            <div class="row details_top__flex">
                                <div class="col-xl-6">
                                    <div class="details_left">
                                        <div class="details_heading">
                                            <h3><?php echo $property_title ?></h3>
                                        </div>
                                        <div class="listing_star__left details_left__star">
                                            <h3>4 <span>-star hotel</span></h3>
                                            <h6><img src="<?php echo USER_IMAGES ?>map.png" class="img-fluid"
                                                    alt=""><?php echo $property_city ?>
                                            </h6>
                                        </div>
                                        <!-- <div class="details_rating">
                                            <div class="rating_number">
                                                <p>7.7</p>
                                            </div>
                                            <p>Very good</p>
                                            <div class="rating_star">
                                                <img src="<?php echo USER_IMAGES ?>star.png" class="img-fluid" alt="">
                                                <img src="<?php echo USER_IMAGES ?>star.png" class="img-fluid" alt="">
                                                <img src="<?php echo USER_IMAGES ?>star.png" class="img-fluid" alt="">
                                                <img src="<?php echo USER_IMAGES ?>star.png" class="img-fluid" alt="">
                                                <img src="<?php echo USER_IMAGES ?>whiterating.png" class="img-fluid"
                                                    alt="">
                                            </div>
                                        </div> -->
                                        <div class="details_offer" style="margin-top: 100px;">
                                            <div class="details_offer__box">
                                                <div class="offer_icon">
                                                    <img src="<?php echo USER_IMAGES ?>Ticket.png" class="img-fluid"
                                                        alt="">
                                                </div>
                                                <div class="offer_content">
                                                    <h3>Cancellation till check-in available</h3>
                                                    <p>With Cancel For No Reason powered by MGI Hotels</p>
                                                </div>
                                            </div>
                                            <div class="details_offer__box">
                                                <div class="offer_icon">
                                                    <img src="<?php echo USER_IMAGES ?>mug-tea 1.png" class="img-fluid"
                                                        alt="">
                                                </div>
                                                <div class="offer_content">
                                                    <h3>Free breakfast on select plans</h3>
                                                    <p>Some plans include free breakfast</p>
                                                </div>
                                            </div>
                                            <div class="hotel_book__now">
                                                <div class="hotel_price">
                                                    <h3>₹3,919 <span>+ ₹495 tax / night</span></h3>
                                                    <p>₹5,500 <span> 29% off </span>No cost EMI from ₹1,471</p>
                                                </div>
                                                <div class="book_now hotel_book">
                                                    <button class="global_btn">book this now</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="details_right">
                                        <div class="hotel_img">
                                            <img src="<?php echo USER_IMAGES ?>card1.png" class="img-fluid" alt="">
                                        </div>
                                        <div class="hotel_details">
                                            <div class="hotel_price">
                                                <h3>₹3,919 <span>+ ₹495 tax / night</span></h3>
                                                <p>₹5,500 <span> 29% off </span>No cost EMI from ₹1,471</p>
                                            </div>
                                            <div class="book_now hotel_book">
                                                <button class="global_btn" data-bs-toggle="modal"
                                                    data-bs-target="#exampleModal">book this now</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="amenities_wrapper">
                            <div class="amenities_heading">
                                <h3>Amenities</h3>
                            </div>
                            <div class="amenities_content">
                                <div class="amenities_box">
                                    <img src="<?php echo USER_IMAGES ?>pool1.png" class="img-fluid" alt="">
                                    <p>swimming pool</p>
                                </div>
                                <div class="amenities_box">
                                    <img src="<?php echo USER_IMAGES ?>pool1.png" class="img-fluid" alt="">
                                    <p>swimming pool</p>
                                </div>
                                <div class="amenities_box">
                                    <img src="<?php echo USER_IMAGES ?>pool1.png" class="img-fluid" alt="">
                                    <p>swimming pool</p>
                                </div>
                                <div class="amenities_box">
                                    <img src="<?php echo USER_IMAGES ?>pool1.png" class="img-fluid" alt="">
                                    <p>swimming pool</p>
                                </div>
                            </div>
                            <div class="amenities_all">
                                <a href="#">See all amenities</a>
                            </div>
                        </div>
                        <div class="amenities_wrapper map">
                            <div class="amenities_heading">
                                <h3>Location</h3>
                            </div>
                            <div class="map_wrapper">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14009.166186427328!2d77.0802252160038!3d28.621022960733747!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d04ba6b064d0f%3A0xf609cdf712fe603e!2sJanakpuri%2C%20New%20Delhi%2C%20Delhi!5e0!3m2!1sen!2sin!4v1707471444282!5m2!1sen!2sin"
                                    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                        <div class="amenities_wrapper">
                            <div class="amenities_heading">
                                <h3>Rooms</h3>
                            </div>
                            <div class="rooms_container">
                                <div class="room_box">
                                    <div class="room_box__left">
                                        <div class="room_img">
                                            <img src="<?php echo USER_IMAGES ?>card2.png" class="img-fluid" alt="">
                                        </div>
                                        <div class="room_details">
                                            <h3>Deluxe room</h3>
                                            <h6>144.0 sq.ft</h6>
                                            <div class="room_details__facelities">
                                                <p><img src="<?php echo USER_IMAGES ?>bathroom.png" class="img-fluid"
                                                        alt="">bathroom
                                                </p>
                                                <p><img src="<?php echo USER_IMAGES ?>bathroom.png" class="img-fluid"
                                                        alt="">bathroom
                                                </p>
                                                <p><img src="<?php echo USER_IMAGES ?>bathroom.png" class="img-fluid"
                                                        alt="">bathroom
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="room_box__right">
                                        <div class="room_book">
                                            <a href="#" class="global_btn">book now</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="room_box">
                                    <div class="room_box__left">
                                        <div class="room_img">
                                            <img src="<?php echo USER_IMAGES ?>card1.png" class="img-fluid" alt="">
                                        </div>
                                        <div class="room_details">
                                            <h3>Deluxe room</h3>
                                            <h6>144.0 sq.ft</h6>
                                            <div class="room_details__facelities">
                                                <p><img src="<?php echo USER_IMAGES ?>bathroom.png" class="img-fluid"
                                                        alt="">bathroom
                                                </p>
                                                <p><img src="<?php echo USER_IMAGES ?>bathroom.png" class="img-fluid"
                                                        alt="">bathroom
                                                </p>
                                                <p><img src="<?php echo USER_IMAGES ?>bathroom.png" class="img-fluid"
                                                        alt="">bathroom
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="room_box__right">
                                        <div class="room_book">
                                            <a href="#" class="global_btn">book now</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="room_box">
                                    <div class="room_box__left">
                                        <div class="room_img">
                                            <img src="<?php echo USER_IMAGES ?>card_img1.png" class="img-fluid" alt="">
                                        </div>
                                        <div class="room_details">
                                            <h3>Deluxe room</h3>
                                            <h6>144.0 sq.ft</h6>
                                            <div class="room_details__facelities">
                                                <p><img src="<?php echo USER_IMAGES ?>bathroom.png" class="img-fluid"
                                                        alt="">bathroom
                                                </p>
                                                <p><img src="<?php echo USER_IMAGES ?>bathroom.png" class="img-fluid"
                                                        alt="">bathroom
                                                </p>
                                                <p><img src="<?php echo USER_IMAGES ?>bathroom.png" class="img-fluid"
                                                        alt="">bathroom
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="room_box__right">
                                        <div class="room_book">
                                            <a href="#" class="global_btn">book now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-body">
                                    <div class="form_wrapper">
                                        <div class="modal_header">
                                            <h3>Book Your Hotel</h3>
                                            <div class="cross" data-bs-dismiss="modal">
                                                <i class='bx bx-x'></i>
                                            </div>
                                        </div>
                                        <form action="">
                                            <div class="form_wrapper__input">
                                                <input type="text" placeholder="First Name *">
                                                <input type="text" placeholder="Last Name *">
                                            </div>
                                            <div class="form_wrapper__input">
                                                <input type="number" placeholder="Mobile Number *">
                                                <input type="email" placeholder="Email *">
                                            </div>
                                            <div class="form_wrapper__input">
                                                <textarea name="" id="" cols="30" rows="4"
                                                    placeholder="Message *"></textarea>
                                            </div>
                                            <div class="submit_form">
                                                <button class="global_btn text-uppercase">submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="details_wrapper__right">
                        <div class="details_right__box">
                            <a href="#" class="box_active">General</a>
                        </div>
                        <div class="details_right__box">
                            <a href="#">Amenities</a>
                        </div>
                        <div class="details_right__box">
                            <a href="#">Location</a>
                        </div>
                        <div class="details_right__box">
                            <a href="#">Rooms</a>
                        </div>
                        <div class="details_right__box">
                            <a href="#">Rules</a>
                        </div>
                        <div class="details_right__box">
                            <a href="#">Reviews</a>
                        </div>
                        <div class="details_right__box">
                            <a href="#">About</a>
                        </div>
                        <div class="book_now">
                            <button class="global_btn" data-bs-toggle="modal" data-bs-target="#exampleModal">Book
                                Now</button>
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
                            <img src="<?php echo USER_IMAGES ?>Arrow - Right.png" class="img-fluid" alt="">
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