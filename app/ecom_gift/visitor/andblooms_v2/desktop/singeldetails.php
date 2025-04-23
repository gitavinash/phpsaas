<?php

require_once 'header.php';

?>
<main>
    <!-- single details section start -->
    <section class="single_wrapper">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="./index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Library</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Jet set tulips</li>
                </ol>
            </nav>
            <div class="product_content">
                <div class="row">
                    <div class="col-xl-5 col-lg-6">
                        <div class="product_content__left">
                            <div class="small_page">
                                <div class="img_box">
                                    <img src="assets/images/svg/flowerimg.svg" class="img-fluid" alt=""
                                        onclick="myFunction(this)">
                                </div>
                                <div class="img_box">
                                    <img src="assets/images/svg/flowerimg2.svg" class="img-fluid" alt=""
                                        onclick="myFunction(this)">
                                </div>
                                <div class="img_box">
                                    <img src="assets/images/svg/flowerimg.svg" class="img-fluid" alt=""
                                        onclick="myFunction(this)">
                                </div>
                                <div class="img_box">
                                    <img src="assets/images/svg/flowerimg2.svg" class="img-fluid" alt=""
                                        onclick="myFunction(this)">
                                </div>
                                <div class="img_box">
                                    <img src="assets/images/svg/flowerimg.svg" class="img-fluid" alt=""
                                        onclick="myFunction(this)">
                                </div>

                            </div>
                            <div class="big_img" id="bigImage">
                                <img src="assets/images/svg/big-img.svg" class="img-fluid" alt="" id="imgBox"
                                    onclick="myFunction(this)">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-6">
                        <div class="product_content__right">
                            <h3 class="is-font-26 main_font_family dark-brown">Jet Set Tulips</h3>
                            <div class="product_rating d-flex">
                                <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                <p class="is-font-18 grey-color">928 Reviews</p>
                            </div>
                            <div class="flower_price">
                                <h3 class="main_font_family dark-brown is-font-26">₹6000 /-</h3>
                            </div>
                            <div class="flower_details">
                                <p class="grey-color is-font-18">Lorem ipsum dolor sit amet elit. Integer nec
                                    odio.Praes
                                    amet elit. Integer nec Lorem
                                    ipsum dolor sit amet elit. Integer nec odio.Praesent libero.Lorem ipsum dolor
                                    sit
                                    amet elit. Integer nec odio.Praes amet elit.</p>
                                <p class="grey-color is-font-18 text-capitalize"><span class="dark-brown">Availability
                                        :</span> In
                                    Delhi,Gurugram,faridabad,noida only</p>
                            </div>
                            <div class="quantity">
                                <h3 class="is-font-18">Quantity :</h3>
                                <div class="quantity_no d-flex">
                                    <p id="minus-btn">-</p>
                                    <p id="count">1</p>
                                    <p id="plus-btn">+</p>
                                </div>
                            </div>
                            <div class="flower_cart">
                                <a href="./cart.html" class="global_btn is-font-18 dark-brown">add to cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- single details description -->
    <section class="product_container">
        <div class="container">
            <div class="types_content">

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                            type="button" role="tab" aria-controls="home" aria-selected="true">Discription</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">Review</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                            type="button" role="tab" aria-controls="contact" aria-selected="false">delivery
                            information</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="description_content">
                            <p class="is-font-18 grey-color">Lorem ipsum dolor sit amet, consectetur adipiscing
                                elit, sed do
                                eiusmod
                                tempor incididunt ut labore
                                et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                                laboris
                                nisi
                                ut
                                aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
                                velit
                                esse
                                cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat</p>
                            <div class="types">
                                <p><img src="assets/images/svg/check.svg" class="img-fluid" alt="">Tulips</p>
                                <p><img src="assets/images/svg/check.svg" class="img-fluid" alt="">Glass Vase</p>
                                <p><img src="assets/images/svg/check.svg" class="img-fluid" alt="">message card</p>
                                <p><img src="assets/images/svg/check.svg" class="img-fluid" alt="">Care Card</p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="review_content">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <div class="review_content__left">
                                            <div class="review_flex">
                                                <div class="user_img">
                                                    <img src="assets/images/svg/user1.svg" class="img-fluid" alt="">
                                                </div>
                                                <div class="user_details">
                                                    <p class="is-font-18 grey-color"><span>Ritik gupta</span> - 01
                                                        january 2024
                                                    </p>
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <p class="is-font-18 grey-color">Lorem ipsum dolor sit amet,
                                                        consectetur ad
                                                        elit,
                                                        sed do sedfeiusmod tempor incididunt ut labore et dolore
                                                        magna
                                                        aliqua.
                                                        Ut
                                                        enim
                                                        ad more minim veniam, quis nostrud ullamco laboris nisi ut
                                                        aliquiq
                                                        learn.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="review_flex">
                                                <div class="user_img">
                                                    <img src="assets/images/svg/userimg2.svg" class="img-fluid" alt="">
                                                </div>
                                                <div class="user_details">
                                                    <p class="is-font-18 grey-color"><span>Ritik gupta</span> - 01
                                                        january 2024
                                                    </p>
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <p class="is-font-18 grey-color">Lorem ipsum dolor sit amet,
                                                        consectetur ad
                                                        elit,
                                                        sed do sedfeiusmod tempor incididunt ut labore et dolore
                                                        magna
                                                        aliqua.
                                                        Ut
                                                        enim
                                                        ad more minim veniam, quis nostrud ullamco laboris nisi ut
                                                        aliquiq
                                                        learn.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="review_flex">
                                                <div class="user_img">
                                                    <img src="assets/images/svg/userimg3.svg" class="img-fluid" alt="">
                                                </div>
                                                <div class="user_details">
                                                    <p class="is-font-18 grey-color"><span>Ritik gupta</span> - 01
                                                        january 2024
                                                    </p>
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                                    <p class="is-font-18 grey-color">Lorem ipsum dolor sit amet,
                                                        consectetur ad
                                                        elit,
                                                        sed do sedfeiusmod tempor incididunt ut labore et dolore
                                                        magna
                                                        aliqua.
                                                        Ut
                                                        enim
                                                        ad more minim veniam, quis nostrud ullamco laboris nisi ut
                                                        aliquiq
                                                        learn.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="review_content__right">
                                            <h3 class="is-font-26 main_font_family text-capitalize">Add a review
                                            </h3>
                                            <p class="is-font-18 grey-color">Your email address will not be
                                                published.
                                            </p>
                                            <div class="myRating">
                                                <span class="is-font-18">Your rating :</span>
                                                <div class="rating_star">
                                                    <input type="radio" name="rating" id="rating-5">
                                                    <label for="rating-5"></label>
                                                    <input type="radio" name="rating" id="rating-4">
                                                    <label for="rating-4"></label>
                                                    <input type="radio" name="rating" id="rating-3">
                                                    <label for="rating-3"></label>
                                                    <input type="radio" name="rating" id="rating-2">
                                                    <label for="rating-2"></label>
                                                    <input type="radio" name="rating" id="rating-1">
                                                    <label for="rating-1"></label>
                                                </div>

                                            </div>
                                            <form action="">
                                                <div class="form_wrapper">
                                                    <div class="form_input">
                                                        <label for="">Your review :</label>
                                                        <textarea name="" id="" cols="30" rows="4" required></textarea>
                                                    </div>
                                                    <div class="form_input">
                                                        <label for="">Name :</label>
                                                        <input type="text" required>
                                                    </div>
                                                    <div class="form_input">
                                                        <label for="">Email : </label>
                                                        <input type="email" required>
                                                    </div>
                                                    <div class="submit">
                                                        <button class="global_btn">submit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">...</div>
                </div>


            </div>


        </div>
    </section>

    <!-- testimonial section start -->
    <section class="testimonial_wrapper">
        <div class="container">
            <div class="main--heading">
                <h1 class="main_font_family is-font-45 black-color">our customer review</h1>
            </div>
        </div>
        <div class="testimonial_content">
            <div class="container">
                <div class="testimonial_slider">
                    <div class="testimonial_card">
                        <div class="card_head">
                            <div class="card_head__img">
                                <img src="assets/images/svg/user1.svg" class="img-fluid" alt="">
                            </div>
                            <div class="user_name">
                                <h3 class="dark-brown main_font_family is-font-20">Rahul kumar</h3>
                                <p class="is-font-18">lorem ipsum</p>
                                <div class="rating">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="user_parra">
                            <p class="silver-shade is-font-18">Lorem Ipsum is simply dummy text of the printing and
                                typesetting
                                industry. 1500s, Ipsum
                                is typesetting industry.</p>
                        </div>
                        <div class="read_more">
                            <a href="#" class="dark-brown is-font-18">Read more</a>
                        </div>
                    </div>
                    <div class="testimonial_card">
                        <div class="card_head">
                            <div class="card_head__img">
                                <img src="assets/images/svg/userimg2.svg" class="img-fluid" alt="">
                            </div>
                            <div class="user_name">
                                <h3 class="dark-brown main_font_family is-font-20">Gourav kumar</h3>
                                <p class="is-font-18">lorem ipsum</p>
                                <div class="rating">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="user_parra">
                            <p class="silver-shade is-font-18">Lorem Ipsum is simply dummy text of the printing and
                                typesetting
                                industry. 1500s, Ipsum
                                is typesetting industry.</p>
                        </div>
                        <div class="read_more">
                            <a href="#" class="dark-brown is-font-18">Read more</a>
                        </div>
                    </div>
                    <div class="testimonial_card">
                        <div class="card_head">
                            <div class="card_head__img">
                                <img src="assets/images/svg/userimg3.svg" class="img-fluid" alt="">
                            </div>
                            <div class="user_name">
                                <h3 class="dark-brown main_font_family is-font-20">Ravi kumar</h3>
                                <p class="is-font-18">lorem ipsum</p>
                                <div class="rating">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="user_parra">
                            <p class="silver-shade is-font-18">Lorem Ipsum is simply dummy text of the printing and
                                typesetting
                                industry. 1500s, Ipsum
                                is typesetting industry.</p>
                        </div>
                        <div class="read_more">
                            <a href="#" class="dark-brown is-font-18">Read more</a>
                        </div>
                    </div>
                    <div class="testimonial_card">
                        <div class="card_head">
                            <div class="card_head__img">
                                <img src="assets/images/svg/user1.svg" class="img-fluid" alt="">
                            </div>
                            <div class="user_name">
                                <h3 class="dark-brown main_font_family is-font-20">Rahul kumar</h3>
                                <p class="is-font-18">lorem ipsum</p>
                                <div class="rating">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                    <img src="assets/images/svg/star.svg" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="user_parra">
                            <p class="silver-shade is-font-18">Lorem Ipsum is simply dummy text of the printing and
                                typesetting
                                industry. 1500s, Ipsum
                                is typesetting industry.</p>
                        </div>
                        <div class="read_more">
                            <a href="#" class="dark-brown is-font-18">Read more</a>
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