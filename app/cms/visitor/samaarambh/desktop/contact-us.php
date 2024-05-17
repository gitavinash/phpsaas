<?php

require_once 'header.php';

?>

<!--Page Header Start-->
<section class="page-header">
    <div class="page-header-bg" style="background-image: url(<?php echo USER_IMAGES ?>img-1/contact-us.jpg)">
    </div>
    <div class="container">
        <div class="page-header__inner">
            <!-- <ul class="thm-breadcrumb list-unstyled">
                        <li><a href="index.html">Home</a></li>
                        <li><span>/</span></li>
                        <li class="active">Contact</li>
                    </ul> -->
            <h2>Contact us</h2>
        </div>
    </div>
</section>
<!--Page Header End-->

<!--Contact Three Start-->
<section class="contact-three">
    <div class="contact-three-shape"
        style="background-image: url(<?php echo USER_IMAGES ?>shapes/contact-three-shape.png);"></div>
    <div class="container">
        <div class="section-title text-center">
            <span class="section-title__tagline">Contact with us</span>
            <h2 class="section-title__title">Feel free to write us <br> anytime</h2>
        </div>
        <div class="contact-page__form-box">
            <form class="contact-page__form contact-form-validated" novalidate="novalidate">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="contact-form__input-box">
                            <input type="text" placeholder="Your name" name="name">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="contact-form__input-box">
                            <input type="email" placeholder="Email address" name="email">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="contact-form__input-box">
                            <input type="text" placeholder="Phone" name="phone">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="contact-form__input-box">
                            <input type="text" placeholder="Subject" name="subject">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="contact-form__input-box text-message-box">
                            <textarea name="message" placeholder="Write a message"></textarea>
                        </div>
                        <div class="contact-form__btn-box">
                            <button type="submit" class="thm-btn contact-form__btn">Send a message</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!--Contact Three End-->

<!--Contact One Start-->
<section class="contact-one">
    <div class="container">
        <div class="contact-one__inne">
            <ul class="list-unstyled contact-one__list">
                <li class="contact-one__single">
                    <div class="contact-one__icon">
                        <span class="icon-phone-call"></span>
                    </div>
                    <div class="contact-one__content">
                        <p class="contact-one__sub-title">Helpline</p>
                        <h3 class="contact-one__number"><a href="tel:+91-9999419333">+91-9999419333</a></h3>
                        <h3 class="contact-one__number"><a href="tel:+91-9910359103">+91-9910359103</a></h3>
                        <h3 class="contact-one__number"><a href="tel:+1-2049631222">+1-2049631222</a></h3>
                    </div>
                </li>
                <li class="contact-one__single">
                    <div class="contact-one__icon">
                        <span class="icon-message"></span>
                    </div>
                    <div class="contact-one__content">
                        <p class="contact-one__sub-title">Send email</p>
                        <h3 class="contact-one__number"><a
                                href="mailto:mail.samaarambh@gmail.com">mail.samaarambh@gmail.com</a></h3>
                        <h3 class="contact-one__number"><a href="mailto:mayank@smrbh.org">mayank@smrbh.org</a></h3>
                        <h3 class="contact-one__number"><a href="mailto:jackson@smrbh.org">jackson@smrbh.org</a></h3>
                    </div>
                </li>
                <li class="contact-one__single">
                    <div class="contact-one__icon">
                        <span class="icon-location"></span>
                    </div>
                    <div class="contact-one__content">
                        <p class="contact-one__sub-title">
                            Regd. Office:</p>
                        <h3 class="contact-one__number">B4/320 C, Keshav Puram, Lawrence Road,
                            <br />New Delhi - 110007
                        </h3>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</section>
<!--Contact One End-->

<!--Google Map Start-->
<section class="google-map google-map-two">
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d224481.01015924648!2d76.74419989453125!3d28.464009599999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cfde1d73e16af%3A0xf55695522403f4cb!2sSamaarambh%20Foundation!5e0!3m2!1sen!2sin!4v1707040533185!5m2!1sen!2sin"
        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>
<!--Google Map End-->


<?php

require_once 'footer.php';

?>