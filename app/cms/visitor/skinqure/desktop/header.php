<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>theme-style.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/css/swiper.min.css'>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>lightbox/jquery-rbox.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick-theme.min.css">

    <?php require_once DEFAULT_META; ?>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-155288390-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-155288390-1');
    </script>

    <!-- Google Tag Manager -->
    <script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            https: //www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window, document, 'script', 'dataLayer', 'GTM-WCWMRG9');
    </script>
    <!-- End Google Tag Manager -->

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-X4G9342E27"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-X4G9342E27');
    </script>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WCWMRG9" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <meta name="google-site-verification" content="c_r-yjhDaWEnLcZ4NxrlTnFp42H2Ho_jlkpnFN8l58U" />


</head>

<body>
    <a href="tel:+91-9266555500" class="sticky sticky-phone" target="_blank"><i class="fa fa-phone"
            aria-hidden="true"></i></a>
    <a href="https://wa.me/919266555500?text=Hello%20SkinQure%21%21%20I%20would%20like%20to%20book%20an%20appointment%20with%20dermatologist."
        class="sticky sticky-whatsapp" target="_blank"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
    <!-- header start -->
    <header class="header">
        <div class="container-fluid common_padding">
            <div class="row v-center justify-content-center">
                <div class="header-item item-left">
                    <div class="logo">
                        <a href="/"><img src="<?php echo USER_IMAGES ?>logo.png" class="logo_img"></a>
                    </div>
                </div>
                <!-- menu start here -->
                <div class="header-item item-center text-start text-lg-center">
                    <div class="menu-overlay">
                    </div>
                    <nav class="menu">
                        <div class="mobile-menu-head">
                            <div class="go-back"><i class="fa fa-angle-left"></i></div>
                            <div class="current-menu-title"></div>
                            <div class="mobile-menu-close">&times;</div>
                        </div>
                        <ul class="menu-main">
                            <li class="menu-item-has-children">
                                <a href="/">Home</a>
                            </li>

                            <li class="menu-item-has-children">
                                <a href="#">Treatments<i class="fa fa-angle-down ms-2"></i></a>
                                <div class="sub-menu mega-menu mega-menu-column-4 text-start">
                                    <div class="list-item">
                                        <h4 class="title">Dermatology</h4>
                                        <ul>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/acne-treatments/">Acne
                                                    Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/acne-scar-treatments/">Acne
                                                    Scar Treatments</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/treatment/treatment-dermatology/melasma-pigmentation-treatments/">Melasma
                                                    Pigmentation Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/facial-pigmentation/">Facial
                                                    Pigmentation</a>
                                            </li>
                                        </ul>
                                        <h4 class="title">Hair Treatments</h4>
                                        <ul>
                                            <li>
                                                <a href="">PRP Therapy</a>
                                            </li>
                                            <li>
                                                <a href="">GFC</a>
                                            </li>
                                            <li>
                                                <a href="">LLLT</a>
                                            </li>
                                            <li>
                                                <a href="">Hair Transplant</a>
                                            </li>
                                            <li>
                                                <a href="">Mesotherapy</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/stem-cell-therapy/">Stem-Cell
                                                    therapy</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <h4 class="title">Dermatosurgery</h4>
                                        <ul>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/mole-removal/">Mole
                                                    Removal</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/wart-removal/">Wart
                                                    Removal</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/skin-tag-removal/">Skin
                                                    Tag Removal</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/acne-scar-surgeries/">Acne
                                                    Scar Surgeries
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/ear-lobe-repair/">Ear
                                                    Lobe Repair</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/cyst-removal/">Cyst
                                                    Removal</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/nail-surgery/">Nail
                                                    Surgery</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/lipoma-treatment/">Lipoma
                                                    Treatment</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <h4 class="title">Face Cosmetics</h4>
                                        <ul>
                                            <li>
                                                <a href="/treatment/face-cosmetics/chemical-peels---organic-peels/">Chemical
                                                    Peels</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/open-pore-treatments/">Open
                                                    Pore Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/under-eye-treatment/">Undereye
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/lip-lightening/">Lip
                                                    Lightening Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/freckles-blemmishes-treatment/">Freckles
                                                    / Blemmishes Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/skin-glow-treatment/">Skin
                                                    Glow Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/medi-facials/">Medifacials</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/hydrafacial-md-elite/">Hydrafacial
                                                    Md Elite</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/carbon-laser-toning/">Carbon
                                                    Laser Facial</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/lip-enhancement/">Lip
                                                    Enhancement</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/nose-enhancement/">Nose
                                                    Enhancement</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <h4 class="title">Anti-Aging Treatments</h4>
                                        <ul>
                                            <li>
                                                <a href="/treatment/face-cosmetics/btx--a-treatment/">BTX-A
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/fillers-treatment/">Filler
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/vampire-facelift/">Vampire
                                                    Facelift</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/thread-lift-treatment/">Thread
                                                    Lift</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/rf-treatment/">RF
                                                    Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/mnrf-treatment/">MNRF
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/hifu-treatment/">HIFU
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/laser-resurfacing/">Laser
                                                    resurfacing </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <h4 class="title">Body Cosmetic Treatments</h4>
                                        <ul>
                                            <li>
                                                <a href="/treatment/face-cosmetics/skin-whitening-treatment/">Skin
                                                    Whitening Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/stretch-mark-treatments/">Stretch
                                                    Marks Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/underarm-treatments/">Underarm
                                                    Lightening Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/intimate-part-lightening/">Intimate
                                                    Part Lightening Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/de-tan-treatment/">De-Taning
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/neck-pigmentation/">Neck
                                                    Pigmentation</a>
                                            </li>
                                        </ul>
                                        <h4 class="title">Laser Treatments</h4>
                                        <ul>
                                            <li>
                                                <a href="/treatment/body-cosmetics/laser-hair-reduction/">Laser
                                                    Hair Reduction</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/laser-tattoo-removal/">Laser
                                                    Tattoo Removal</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/laser-for-pigmentation/">Laser
                                                    For Pigmentation</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/laser-for-acne-scars/">Laser
                                                    For Acne Scars</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/laser-for-chicken-pox-scar/">Laser
                                                    For Chickenpox Scars</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/laser-for-birth-marks/">Laser
                                                    For Birth Marks</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>

                            <li class="menu-item-has-children">
                                <a href="#">Conditions<i class="fa fa-angle-down ms-2"></i></a>
                                <div class="sub-menu mega-menu mega-menu-column-4 text-start">
                                    <div class="list-item">
                                        <h4 class="title">Dermatology</h4>
                                        <ul>
                                            <li>
                                                <a href="/service/dermatology/acne/">Acne</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/acne-scar/">Acne
                                                    Scars</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/melasma-pigmentation/">Melasma
                                                    And Pigmentation</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/allergy-eczema/">Allergy</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/psoriasis/">Psoriasis</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/vitiligo-surgery/">Vitiligo</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <h4 class="title">Face Cosmetics</h4>
                                        <ul>
                                            <li>
                                                <a href="/service/facial-cosmetic-treatment/open-pores/">Open
                                                    Pores</a>
                                            </li>
                                            <li>
                                                <a href="/service/facial-cosmetic-treatment/under-eye/">Undereye
                                                    Pigmentation</a>
                                            </li>
                                            <li>
                                                <a href="/service/facial-cosmetic-treatment/lip-lightening/">Lip
                                                    Pigmentation</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/service/facial-cosmetic-treatment/chemical-peels-organic-peels/">Chemical
                                                    Peels</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/service/facial-cosmetic-treatment/dull-face-photo-pigmentation/">Dull
                                                    Face / Photopigmentation</a>
                                            </li>
                                            <li>
                                                <a href="/service/facial-cosmetic-treatment/facial-tanning/">Facial
                                                    Tanning</a>
                                            </li>
                                            <li>
                                                <a href="/service/facial-cosmetic-treatment/xanthelsma/">Xanthelesma</a>
                                            </li>
                                            <li>
                                                <a href="/service/facial-cosmetic-treatment/milia/">Milia</a>
                                            </li>
                                            <li>
                                                <a href="/service/facial-cosmetic-treatment/aging-face/">Aging Face</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <h4 class="title">Body Cosmetics</h4>
                                        <ul>
                                            <li>
                                                <a href="/service/body-cosmetic-treatment/stretch-marks/">Stretch
                                                    Marks</a>
                                            </li>
                                            <li>
                                                <a href="/service/body-cosmetic-treatment/underarm-pigmentation/">Underarm
                                                    Pigmentation</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/service/body-cosmetic-treatment/cellulite-treatment/">Cellulite</a>
                                            </li>
                                            <li>
                                                <a href="/service/body-cosmetic-treatment/hesitational-marks/">Hesitational
                                                    Marks</a>
                                            </li>
                                            <li>
                                                <a href="/service/body-cosmetic-treatment/body-tanning/">Body
                                                    Tanning</a>
                                            </li>
                                            <li>
                                                <a href="/service/body-cosmetic-treatment/birth-mark/">Birth
                                                    Marks</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <h4 class="title">Hair loss</h4>
                                        <ul>
                                            <li>
                                                <a href="/service/body-cosmetic-treatment/male-patterned-baldness/">Male
                                                    Patterend Baldness</a>
                                            </li>
                                            <li>
                                                <a href="/service/body-cosmetic-treatment/female-hair-loss/">Female
                                                    Hairloss</a>
                                            </li>
                                            <li>
                                                <a href="/service/body-cosmetic-treatment/alopecia-areata/">Alopecia
                                                    Areata</a>
                                            </li>
                                            <li>
                                                <a href="/service/body-cosmetic-treatment/scarring-alopecia-lpp/">Scarring
                                                    Alopecia / LPP</a>
                                            </li>
                                            <li>
                                                <a href="/service/body-cosmetic-treatment/dandruff/">Dandruff</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>

                            <li class="menu-item-has-children">
                                <a href="#">Why Us <i class="fa fa-angle-down ms-2"></i></a>
                                <div class="sub-menu single-column-menu">
                                    <ul class="text-start">
                                        <li><a href="/technology/">Technology</a></li>
                                        <li><a href="/media/">Media</a></li>
                                        <li><a href="/why-us/">Why Us</a></li>
                                        <li><a href="/skinqure-gallery/">Gallery</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li class="menu-item-has-children">
                                <a href="#">About Team <i class="fa fa-angle-down ms-2"></i></a>
                                <div class="sub-menu single-column-menu">
                                    <ul class="text-start">
                                        <li><a href="/dr-jangid/">Dr. Jangid</a></li>
                                        <li><a href="/aestheticians/">Aestheticians</a></li>
                                        <li><a href="/career/">Career</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li class="menu-item-has-children">
                                <a href="#">Offers <i class="fa fa-angle-down ms-2"></i></a>
                                <div class="sub-menu single-column-menu">
                                    <ul class="text-start">
                                        <li><a href="/packages/">Packages</a></li>
                                        <!-- <li><a href="/wow-inclusive/">WOW inclusive</a></li> -->
                                        <li><a href="/latest-offers/">Latest Offers</a></li>
                                        <!-- <li><a href="/bridal-packages/">Bridal Packages</a></li> -->
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </nav>
                </div>
                <!-- menu end here -->
                <div class="header-item item-right">
                    <a href="/contact-us/" class="btn book_appoint">Book An Appointment</a>
                    <!-- mobile menu trigger -->
                    <div class="mobile-menu-trigger">
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header end -->