<?php

    require_once 'header.php'

?>
<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center p-0">
                <div class="inner_page_banner about_banner_bg">
                    <div class="inner_content">
                        <p><?php echo ucwords($page_title) ?></p>
                        <div class="text-start"><a href="/">Home</a> <span><i
                                    class="fa fa-chevron-right"></i></span><span><?php echo ucwords($page_title) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section>
    <div class="container-fluid">
        <div class="row justify-content-center section_spacing_top section_spacing_bottom">
            <div class="col-lg-10">
                <div class="row">
                    <div class="section_heading">
                        <h3 class="text-start mb-md-4">Patient Results</h3>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="galleryBox">
                            <a href="" class="mediaGallery"
                                data-rbox-image="https://www.skinqure.in/wp-content/uploads/2019/11/skin-5.jpg"
                                data-rbox-caption="Captions support <strong>HTML</strong>" data-rbox-series="gallery">
                                <img src="https://www.skinqure.in/wp-content/uploads/2019/11/skin-5.jpg"
                                    alt="Example Image" />
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="galleryBox">
                            <a href="" class="mediaGallery"
                                data-rbox-image="https://www.skinqure.in/wp-content/uploads/2019/11/skin-4.jpg"
                                data-rbox-caption="Captions support <strong>HTML</strong>" data-rbox-series="gallery">
                                <img src="https://www.skinqure.in/wp-content/uploads/2019/11/skin-4.jpg"
                                    alt="Example Image" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="container-fluid">
        <div class="row justify-content-center section_spacing_top section_spacing_bottom">
            <div class="col-lg-10">
                <div class="row">
                    <div class="section_heading">
                        <h3 class="text-start mb-md-4">Video Testimonials</h3>
                    </div>
                    <div class="col-12 col-md-4">
                        <iframe width="100%" height="250" src="https://www.youtube.com/embed/49DOThQNXXw"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                    <div class="col-12 col-md-4">
                        <iframe width="100%" height="250" src="https://www.youtube.com/embed/gJz_nZi9uVE"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

    require_once 'footer.php';

?>