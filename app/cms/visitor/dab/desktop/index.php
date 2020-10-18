<?php

    include_once 'header.php';

?>

<section id="home" class="homepageSlider" >
    <div class="slide" style="background: yellow;" >
        <div class="slider-img">
            <img src="<?php echo USER_IMAGES ?>banner/slider-1.jpg" alt="">
        </div>
        <div class="slider-text">
            <p>Extraordinary projects deserve extraordinary work</p>
        </div>
    </div>
    <div class="slide" style="background: green;" >
        <div class="slider-img">
            <img src="<?php echo USER_IMAGES ?>banner/slider-2.jpg" alt="">
        </div>
        <div class="slider-text">
            <p>Extraordinary projects deserve extraordinary work</p>
        </div>
    </div>
    <div class="slide" style="background: black;" >
        <div class="slider-img">
            <img src="<?php echo USER_IMAGES ?>banner/slider-3.jpg" alt="">
        </div>
        <div class="slider-text">
            <p>Extraordinary projects deserve extraordinary work</p>
        </div>
    </div>
</section>

<section id="ourstory" class="ourstory padding-100-0" >
    <div class="container">
        <div class="row">
            <h1>Our Story</h1>
        </div>
        <div class="row">
            <div class="col-2">
                <div class="img">
                    <img src="<?php echo USER_IMAGES ?>our-story.jpg" alt="">
                </div>
            </div>
            <div class="col-2">
                <p>D.A.B. aka Dev Architects and Builders is a multi faceted design and build firm based in Delhi NCR, India. We believe in an integrated process for a seamless experience. Built by seasoned professionals with experience in construction of over 20 years and designed by young architects graduated from the prestigious school of planning and architecture, our projects speak for themselves. Our flawless and solid construction methodologies engaging various stakeholders to produce efficient and aesthetic spaces have resulted in beautiful projects and happy homeowners.</p>
                <div class="btn-center our-team">
                    <a href="#">Meet Our Team</a>
                </div>
                <p><strong></strong></p>
                <div class="philosophy">
                    <p class="left"><sup>"</sup>Simple Living,</p>
                    <p class="right">High Thinking<sup>"</sup></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="services" class="services padding-100-0" >
    <div class="container-without-padding">
        <div class="row">
            <h2>One stop shop for all your building needs</h2>
        </div>
        <div class="row remove-margin">
            <div class="box">
                <div class="img"><img src="<?php echo USER_IMAGES ?>we-design.jpg" alt=""></div>
                <div class="text">We Design</div>
            </div>
            <div class="box">
                <div class="img"><img src="<?php echo USER_IMAGES ?>we-build.jpg" alt=""></div>
                <div class="text">We Build</div>
            </div>
            <div class="box">
                <div class="img"><img src="<?php echo USER_IMAGES ?>we-maintain.jpg" alt=""></div>
                <div class="text">We Maintain</div>
            </div>
        </div>
    </div>
<section>
<section id="services" class="services" >
    <div class="container">
        <div class="row gap-50 why" >
            <h3>Here’s why we are better</h3>
            <p>Our clients are more like family whom we take care in the utmost manner. Since real estate is one of the most priced possession of one’s life, so are our clients for us. We connect on a personal level and take you on a journey of self discovery while guiding you for the best possible outcome.</p>
        </div>
        <div class="row gap-50" >
            <div class="service-feature">
                <div class="box-img"><img src="<?php echo USER_IMAGES ?>icon/integrity.png" alt=""></div><div class="box-text" ><p>Act with<strong>INTEGRITY</strong></p></div>
            </div>
            <div class="service-feature">
                <div class="box-img"><img src="<?php echo USER_IMAGES ?>icon/respect.png" alt=""></div><div class="box-text" ><p>Treat others with<strong>RESPECT</strong></p></div>
            </div>
            <div class="service-feature">
                <div class="box-img"><img src="<?php echo USER_IMAGES ?>icon/service.png" alt=""></div><div class="box-text" ><p>Provide high standard of<strong>SERVICE</strong></p></div>
            </div>
            <div class="service-feature">
                <div class="box-img"><img src="<?php echo USER_IMAGES ?>icon/trust.png" alt=""></div><div class="box-text" ><p>Promote<strong>TRUST</strong></p></div>
            </div>
            <div class="service-feature">
                <div class="box-img"><img src="<?php echo USER_IMAGES ?>icon/responsibilty.png" alt=""></div><div class="box-text" ><p>We take<strong>RESPONSIBILTY</strong></p></div>
            </div>
        </div>
    </div>
</section>

<section id="portfolio" class="portfolio" >
    <div class="container">
        <div class="row">
            <h2>Our Portfolio</h2>
        </div>
    </div>
    <div class="container-without-padding">
        <div class="row">
            <div class="text-box" style="background: url('<?php echo USER_IMAGES ?>portfolio-upcoming.jpg')" >
                <div class="left">
                    <h3>Upcoming Project</h3>
                    <a href="/category/upcoming-project/">View projects</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="text-box" style="background: url('<?php echo USER_IMAGES ?>portfolio-concept.jpg')" >
                <div class="right">
                    <h3>Our Concepts</h3>
                    <a href="/category/our-concepts/">View concepts</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="text-box" style="background: url('<?php echo USER_IMAGES ?>portfolio-finished.jpg')" >
                <div class="left">
                    <h3>Finished Project</h3>
                    <a href="/category/finished-project/">View project</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- <section class="testimonials" >
    <div class="container">
        <h2>Happy customer is equal to success!</h2>
        <div class="testimonial-slider">
            <div class="slide">
                <div class="testimonials-box">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam quis semper turpis. Nulla quis ante sodales, malesuada libero ac, mollis eros.</p>
                    <p><strong>Test Name 1,</strong> Hello Company</p>
                </div>
            </div>
            <div class="slide">
                <div class="testimonials-box">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam quis semper turpis. Nulla quis ante sodales, malesuada libero ac, mollis eros.</p>
                    <p><strong>Test Name 2,</strong> Hello Company</p>
                </div>
            </div>
            <div class="slide">
                <div class="testimonials-box">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam quis semper turpis. Nulla quis ante sodales, malesuada libero ac, mollis eros.</p>
                    <p><strong>Test Name 3,</strong> Hello Company</p>
                </div>
            </div>
        </div>
    </div>
</section> -->

<section id="blog" class="blog" >
    <div class="container">
        <h2>Blog</h2>
        <div class="blogSlider">

            <?php
            
                if($checkBlogs=mysqli_query($connection, "SELECT post_title, post_thumbnail, link_url FROM ".TABLE_PREFIX."post, ".TABLE_PREFIX."link, ".TABLE_PREFIX."category WHERE link_type='post' AND link_relation_id=post_id AND link_site_id='".SITE_ID."' AND category_id=post_category AND link_status=200 ORDER BY link_published DESC LIMIT 0, 3")) {

                    if(mysqli_num_rows($checkBlogs)!=0) {

                        while($blogData=mysqli_fetch_assoc($checkBlogs)) {

                            extract($blogData);

                            echo "
                            <a href='{$link_url}'>
                                <div class='slide'>
                                    <div class='img'>
                                        <img src='{$post_thumbnail}' alt='{$post_title}' >
                                    </div>
                                    <div class='text'>
                                        <h3>{$post_title}</h3>
                                    </div>
                                </div>
                            </a>";

                        }

                    }

                }
            
            ?>

        </div>
    </div>
</section>

<?php

    include_once 'footer.php';

?>