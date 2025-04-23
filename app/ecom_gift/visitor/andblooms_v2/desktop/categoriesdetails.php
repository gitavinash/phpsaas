<?php

require_once 'header.php';

?>
<main>
    <section class="categories_banner">
        <div class="categories_content">
            <h1 class="main_font_family dark-brown is-font-55 d-flex"><img src="assets/images/svg/left-arrow.svg"
                    class="img-fluid" alt="">Lorem ipsum</h1>
        </div>
    </section>

    <!-- categories details section start -->
    <section class="categories_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="categories_wrapper__left">
                        <form action="">
                            <div class="categories_filter">
                                <h3 class="is-font-20 dark-brown main_font_family">Filter by price</h3>
                                <div class="wrapper">
                                    <div class="slider">
                                        <div class="progress"></div>
                                    </div>
                                    <div class="range-input">
                                        <input type="range" class="range-min" min="0" max="10000" value="2500"
                                            step="100">
                                        <input type="range" class="range-max" min="0" max="10000" value="7500"
                                            step="100">
                                    </div>
                                    <div class="price-input">
                                        <div class="field">
                                            <span>Min</span>
                                            <input type="number" class="input-min" value="2500">
                                        </div>
                                        <div class="separator">-</div>
                                        <div class="field">
                                            <span>Max</span>
                                            <input type="number" class="input-max" value="7500">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- filter by color -->
                            <div class="filter categories_filter">
                                <h3 class="is-font-20 dark-brown main_font_family">Filter by Colour</h3>
                                <div class="color_section">
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <div class="color_left">
                                                <span class="beige"></span>
                                                <p class="is-font-18">beige</p>
                                            </div>
                                            <div class="number">
                                                <p class="is-font-18">9</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <div class="color_left">
                                                <span class="red"></span>
                                                <p class="is-font-18">red</p>
                                            </div>
                                            <div class="number">
                                                <p class="is-font-18">5</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <div class="color_left">
                                                <span class="blue"></span>
                                                <p class="is-font-18">Blue</p>
                                            </div>
                                            <div class="number">
                                                <p class="is-font-18">8</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <div class="color_left">
                                                <span class="green"></span>
                                                <p class="is-font-18">green</p>
                                            </div>
                                            <div class="number">
                                                <p class="is-font-18">4</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <div class="color_left">
                                                <span class="silver"></span>
                                                <p class="is-font-18">grey</p>
                                            </div>
                                            <div class="number">
                                                <p class="is-font-18">2</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- filter by size -->
                            <div class="filter categories_filter">
                                <h3 class="is-font-20 dark-brown main_font_family">Filter by size</h3>
                                <div class="color_section">
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <h6 class="is-font-18 dark-brown">s</h6>
                                            <div class="number">
                                                <p class="is-font-18">9</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <h6 class="is-font-18 dark-brown">m</h6>
                                            <div class="number">
                                                <p class="is-font-18">4</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <h6 class="is-font-18 dark-brown">l</h6>
                                            <div class="number">
                                                <p class="is-font-18">5</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <h6 class="is-font-18 dark-brown">xl</h6>
                                            <div class="number">
                                                <p class="is-font-18">4</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <h6 class="is-font-18 dark-brown">xxl</h6>
                                            <div class="number">
                                                <p class="is-font-18">2</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="mob_filter">
                        <div class="filter_btn">
                            <h3 class="is-font-26 dark-brown main_font_family"><i class='bx bx-filter-alt'></i>
                                Filter
                            </h3>
                        </div>
                        <div class="filter_overlay">
                            <form action="">
                                <div class="filter_slider">
                                    <div class="categories_filter">
                                        <h3 class="is-font-20 dark-brown main_font_family">Filter by price</h3>
                                        <div class="wrapper">
                                            <div class="slider-range">
                                                <div class="progress-bar"></div>
                                            </div>
                                            <div class="input-range">
                                                <input type="range" class="range-min" min="0" max="10000" value="2500"
                                                    step="100">
                                                <input type="range" class="range-max" min="0" max="10000" value="7500"
                                                    step="100">
                                            </div>
                                            <div class="input-price">
                                                <div class="field">
                                                    <span>Min</span>
                                                    <input type="number" class="input-min" value="2500">
                                                </div>
                                                <div class="separator">-</div>
                                                <div class="field">
                                                    <span>Max</span>
                                                    <input type="number" class="input-max" value="7500">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="filter_cancel">
                                        <i class='bx bxs-x-circle'></i>
                                    </div>
                                    <!-- filter by color -->
                                    <div class="filter categories_filter">
                                        <h3 class="is-font-20 dark-brown main_font_family">Filter by Colour</h3>
                                        <div class="color_section">
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <div class="color_left">
                                                        <span class="beige"></span>
                                                        <p class="is-font-18">beige</p>
                                                    </div>
                                                    <div class="number">
                                                        <p class="is-font-18">9</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <div class="color_left">
                                                        <span class="red"></span>
                                                        <p class="is-font-18">red</p>
                                                    </div>
                                                    <div class="number">
                                                        <p class="is-font-18">5</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <div class="color_left">
                                                        <span class="blue"></span>
                                                        <p class="is-font-18">Blue</p>
                                                    </div>
                                                    <div class="number">
                                                        <p class="is-font-18">8</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <div class="color_left">
                                                        <span class="green"></span>
                                                        <p class="is-font-18">green</p>
                                                    </div>
                                                    <div class="number">
                                                        <p class="is-font-18">4</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <div class="color_left">
                                                        <span class="silver"></span>
                                                        <p class="is-font-18">grey</p>
                                                    </div>
                                                    <div class="number">
                                                        <p class="is-font-18">2</p>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- filter by size -->
                                    <div class="filter categories_filter">
                                        <h3 class="is-font-20 dark-brown main_font_family">Filter by size</h3>
                                        <div class="color_section">
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <h6 class="is-font-18 dark-brown">s</h6>
                                                    <div class="number">
                                                        <p class="is-font-18">9</p>
                                                    </div>
                                                </label>

                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <h6 class="is-font-18 dark-brown">m</h6>
                                                    <div class="number">
                                                        <p class="is-font-18">4</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <h6 class="is-font-18 dark-brown">l</h6>
                                                    <div class="number">
                                                        <p class="is-font-18">5</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <h6 class="is-font-18 dark-brown">xl</h6>
                                                    <div class="number">
                                                        <p class="is-font-18">4</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <h6 class="is-font-18 dark-brown">xxl</h6>
                                                    <div class="number">
                                                        <p class="is-font-18">2</p>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="categories_wrapper__right">

                        <div class="row">
                            <div class="col-xl-4 col-md-4 col-6">
                                <a href="./singeldetails.html">
                                    <div class="flower_img">
                                        <div class="img_card">
                                            <img src="assets/images/svg/flower-1.svg" class="img-fluid" alt="">
                                        </div>
                                        <div class="flower_content">
                                            <h6 class="is-font-20 dark-brown">Lorem ipsum dol</h6>
                                            <span class="is-font-18 grey-color">consectetur</span>
                                            <p class="dark-brown main_font_family is-font-26">Rs 9000.0</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-4 col-md-4 col-6">
                                <a href="./singeldetails.html">
                                    <div class="flower_img">
                                        <div class="img_card">
                                            <img src="assets/images/svg/flowerimg3.svg" class="img-fluid" alt="">
                                        </div>
                                        <div class="flower_content">
                                            <h6 class="is-font-20 dark-brown">Lorem ipsum dol</h6>
                                            <span class="is-font-18 grey-color">consectetur</span>
                                            <p class="dark-brown main_font_family is-font-26">Rs 9000.0</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-4 col-md-4 col-6">
                                <a href="./singeldetails.html">
                                    <div class="flower_img">
                                        <div class="img_card">
                                            <img src="assets/images/svg/flowerimg4.svg" class="img-fluid" alt="">
                                        </div>
                                        <div class="flower_content">
                                            <h6 class="is-font-20 dark-brown">Lorem ipsum dol</h6>
                                            <span class="is-font-18 grey-color">consectetur</span>
                                            <p class="dark-brown main_font_family is-font-26">Rs 9000.0</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-4 col-md-4 col-6">
                                <a href="./singeldetails.html">
                                    <div class="flower_img">
                                        <div class="img_card">
                                            <img src="assets/images/svg/flower-1.svg" class="img-fluid" alt="">
                                        </div>
                                        <div class="flower_content">
                                            <h6 class="is-font-20 dark-brown">Lorem ipsum dol</h6>
                                            <span class="is-font-18 grey-color">consectetur</span>
                                            <p class="dark-brown main_font_family is-font-26">Rs 9000.0</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-4 col-md-4 col-6">
                                <a href="./singeldetails.html">
                                    <div class="flower_img">
                                        <div class="img_card">
                                            <img src="assets/images/svg/flowerimg3.svg" class="img-fluid" alt="">
                                        </div>
                                        <div class="flower_content">
                                            <h6 class="is-font-20 dark-brown">Lorem ipsum dol</h6>
                                            <span class="is-font-18 grey-color">consectetur</span>
                                            <p class="dark-brown main_font_family is-font-26">Rs 9000.0</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-4 col-md-4 col-6">
                                <a href="./singeldetails.html">
                                    <div class="flower_img">
                                        <div class="img_card">
                                            <img src="assets/images/svg/flowerimg4.svg" class="img-fluid" alt="">
                                        </div>
                                        <div class="flower_content">
                                            <h6 class="is-font-20 dark-brown">Lorem ipsum dol</h6>
                                            <span class="is-font-18 grey-color">consectetur</span>
                                            <p class="dark-brown main_font_family is-font-26">Rs 9000.0</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-4 col-md-4 col-6">
                                <a href="./singeldetails.html">
                                    <div class="flower_img">
                                        <div class="img_card">
                                            <img src="assets/images/svg/flower-1.svg" class="img-fluid" alt="">
                                        </div>
                                        <div class="flower_content">
                                            <h6 class="is-font-20 dark-brown">Lorem ipsum dol</h6>
                                            <span class="is-font-18 grey-color">consectetur</span>
                                            <p class="dark-brown main_font_family is-font-26">Rs 9000.0</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-4 col-md-4 col-6">
                                <a href="./singeldetails.html">
                                    <div class="flower_img">
                                        <div class="img_card">
                                            <img src="assets/images/svg/flowerimg3.svg" class="img-fluid" alt="">
                                        </div>
                                        <div class="flower_content">
                                            <h6 class="is-font-20 dark-brown">Lorem ipsum dol</h6>
                                            <span class="is-font-18 grey-color">consectetur</span>
                                            <p class="dark-brown main_font_family is-font-26">Rs 9000.0</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-4 col-md-4 col-6">
                                <a href="./singeldetails.html">
                                    <div class="flower_img">
                                        <div class="img_card">
                                            <img src="assets/images/svg/flowerimg4.svg" class="img-fluid" alt="">
                                        </div>
                                        <div class="flower_content">
                                            <h6 class="is-font-20 dark-brown">Lorem ipsum dol</h6>
                                            <span class="is-font-18 grey-color">consectetur</span>
                                            <p class="dark-brown main_font_family is-font-26">Rs 9000.0</p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                        </div>

                        <div class="page">
                            <a href="#" class="page_active">1</a>
                            <a href="#">2</a>
                            <a href="#">3</a>
                            <a href="#">4</a>
                            <a href="#">...</a>
                            <a href="#">25</a>
                        </div>
                    </div>
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