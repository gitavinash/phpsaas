<?php

    require_once 'header.php';

?>
<!-- main section -->
<main onclick="remove_all()">
    <section class="banner_section">
        <div id="carouselExampleCaptions" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active first">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="content d-flex justify-content-center flex-column">
                                    <div class="row">
                                        <div class="col-md-6 left">
                                            <h1>
                                                $300
                                            </h1>
                                            <p>USD</p>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="right">
                                                <h2>Acoustic</h2>
                                                <p class="my-3">Music is the universal language of mankind</p>
                                                <a href="">Buy Now</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <img src="" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item second">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="content d-flex justify-content-center flex-column">
                                    <div class="row">
                                        <div class="col-md-6 left">
                                            <h1>
                                                $300
                                            </h1>
                                            <p>USD</p>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="right">
                                                <h2>Acoustic</h2>
                                                <p class="my-3">Music is the universal language of mankind</p>
                                                <a href="">Buy Now</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <img src="" alt="">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="about">
        <div class="containers">
            <div class="row">
                <div class="center">
                    <div class="content">
                        <h4 class="small_head">Who we are</h4>
                        <h2 class="med_head">Sense the <span>Music</span> </h2>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellat tempore libero maiores
                            consequatur culpa voluptate, porro consectetur. Tempora totam cum ut aliquam eaque
                            libero, quos sapiente vel nihil voluptates odit autem praesentium aperiam, tempore
                            laboriosam, minus reiciendis mollitia corrupti! Rerum earum quidem hic unde voluptatum
                            vel! Commodi corrupti necessitatibus doloremque nam quos ea, soluta pariatur minima eos
                            quidem laboriosam odit.</p>

                        <a href="" class="bttn">Know More</a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="knowme">
        <h1 class="large_head">About</h1>
        <div class="containers position-relative">
            <img src="<?php echo USER_IMAGES ?>about.webp" alt="" class="w-100">
            <div class="row">
                <div class="col-md-6 ">
                    <div class="content">
                        <h1 class="small_head mt-5">About us</h1>
                        <h3 class="med_head">Steve Austin</h3>
                        <p>Turpis massa tincidunt dui ut. Pulvinar elementum integer enim neque. Mauris rhoncus
                            aenean vel
                            elit scelerisque mauris pellentesque. Sit amet commodo nulla facilisi nullam vehicula
                            ipsum a.
                            Morbi blandit cursus risus at. A diam sollicitudin.</p>
                        <a href="" class="bttn">read more</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="img">
                        <img src="<?php echo USER_IMAGES ?>ims.webp" alt="" class="w-100">
                    </div>
                </div>
            </div>

        </div>
    </section>


    <section class="brands mx-4">
        <div class="text-center">
            <h1 class="small_head">Our Brands</h1>
            <p class="med_head">Our Unique Collections</p>
            <p>Tempor orci eu lobortis elementum nibh tellus. Tristique senectus et netus et malesuada fames ac.
                Morbi tincidunt ornare massa eget egestas purus viverra accumsan. Vitae purus faucibus ornare
                suspendisse sed nisi. Porttitor leo a diam id eu.</p>
            <!-- <h2 class="mb-5">Brands We <span>Deal </span>In</h2> -->

        </div>
        <div class="row">
            <div class="col-sm-6 col-lg-6 col-xl-3 product">
                <img src="<?php echo USER_IMAGES ?>1.webp" alt="">
                <div class="content">
                    <div class="name">Ligglo</div>
                    <div class="rating"><i class="fas fa-star"></i></div>
                    <div class="rate">Rs. 380.00</div>
                    <a href="" class="bttn">Add to Cart</a>
                </div>
            </div>
            <div class="col-sm-6 col-lg-6 col-xl-3 product">
                <img src="<?php echo USER_IMAGES ?>1.webp" alt="">
                <div class="content">
                    <div class="name">Ligglo</div>
                    <div class="rating"><i class="fas fa-star"></i></div>
                    <div class="rate">Rs. 380.00</div>
                    <a href="" class="bttn">Add to Cart</a>
                </div>
            </div>
            <div class="col-sm-6 col-lg-6 col-xl-3 product">
                <img src="<?php echo USER_IMAGES ?>1.webp" alt="">
                <div class="content">
                    <div class="name">Ligglo</div>
                    <div class="rating"><i class="fas fa-star"></i></div>
                    <div class="rate">Rs. 380.00</div>
                    <a href="" class="bttn">Add to Cart</a>
                </div>
            </div>
            <div class="col-sm-6 col-lg-6 col-xl-3 product">
                <img src="<?php echo USER_IMAGES ?>1.webp" alt="">
                <div class="content">
                    <div class="name">Ligglo</div>
                    <div class="rating"><i class="fas fa-star"></i></div>
                    <div class="rate">Rs. 380.00</div>
                    <a href="" class="bttn">Add to Cart</a>
                </div>
            </div>
        </div>
    </section>



    <div class="banner_video">
        <h5 class="small_head">Oneday</h5>
        <h1 class="med_head">From School Life</h1>
        <i class="fas fa-play"></i>
    </div>

    <section class="newdesign py-5">
        <h1 class="large_head mt-3">Products</h1>
        <div class="text-center">
            <h2 class="small_head">Our Brands</h2>
            <h5 class="med_head">New Design of Guitar</h5>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="name">Builder's Base Guitars</h5>
                    <p class="price">PRICE:$3,999 USD / SERIES: 800</p>
                    <p class="spec">Specifications</p>
                    <div class="row">
                        <div class="col-md-6">
                            <p><b> Scale Length</b> : 24-7/8 </p>
                            <p> <b>Bracing</b> : V-Class </p>
                            <p> <b>Tuners</b> : Gotoh </p>
                            <p><b>Body Length</b> : 20"</p>
                            <p><b>Body Depth</b> : 45/8"</p>
                        </div>
                        <div class="col-md-6">

                            <p> <b>Nut & Saddle</b> : Tuqs Nut </p>
                            <p><b>Truss Rod Cover</b> : Trust African </p>
                            <p><b>Case</b> : Deluxe </p>
                            <p> <b>Body Width</b> : 16 1/4" </p>
                            <p> <b>Number of Frets</b> : 20 </p>

                        </div>
                        <a href="" class="bttn">Buy Now</a>
                    </div>
                    <img src="<?php echo USER_IMAGES ?>guitar-2-1_970x.webp" alt="">
                </div>
                <div class="col-md-6">
                    <img src="<?php echo USER_IMAGES ?>guitar-2-1_970x.webp" alt="" class=" mb-5">
                    <h5 class="name">Builder's Base Guitars</h5>
                    <p class="price">PRICE:$3,999 USD / SERIES: 800</p>
                    <p class="spec">Specifications</p>
                    <div class="row">
                        <div class="col-md-6">
                            <p><b> Scale Length</b> : 24-7/8 </p>
                            <p> <b>Bracing</b> : V-Class </p>
                            <p> <b>Tuners</b> : Gotoh </p>
                            <p><b>Body Length</b> : 20"</p>
                            <p><b>Body Depth</b> : 45/8"</p>
                        </div>
                        <div class="col-md-6">

                            <p> <b>Nut & Saddle</b> : Tuqs Nut </p>
                            <p><b>Truss Rod Cover</b> : Trust African </p>
                            <p><b>Case</b> : Deluxe </p>
                            <p> <b>Body Width</b> : 16 1/4" </p>
                            <p> <b>Number of Frets</b> : 20 </p>

                        </div>
                        <a href="" class="bttn">Buy Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="blog py-5">
        <h1 class="large_head">Blog</h1>
        <div class="container">
            <div class="row">
                <div class="text-center">
                    <h3 class="small_head">our blog</h3>
                    <p class="med_head">Latest Update</p>
                </div>
                <div class="row">
                    <div class="col-md-3 blog_box">
                        <img src="<?php echo USER_IMAGES ?>blog-1.webp" alt="">
                        <div class="date">August 24 , 2022</div>
                        <div class="name">Best string guitar</div>
                    </div>
                    <div class="col-md-3 blog_box">
                        <img src="<?php echo USER_IMAGES ?>blog-2.webp" alt="">
                        <div class="date">August 24 , 2022</div>
                        <div class="name">Best string guitar</div>
                    </div>
                    <div class="col-md-3 blog_box">
                        <img src="<?php echo USER_IMAGES ?>blog-3.webp" alt="">
                        <div class="date">August 24 , 2022</div>
                        <div class="name">Best string guitar</div>
                    </div>
                    <div class="col-md-3 blog_box">
                        <img src="<?php echo USER_IMAGES ?>blog-4.webp" alt="">
                        <div class="date">August 24 , 2022</div>
                        <div class="name">Best string guitar</div>
                    </div>
                </div>
            </div>
        </div>
    </section>



</main>
<?php

    require_once 'footer.php';

?>