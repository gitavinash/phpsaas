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

</head>

<body>

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
                                                <a href="">Facial Pigmentation</a>
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
                                                <a href="">Stem-Cell therapy</a>
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
                                                <a href="">Freckles / Blemmishes Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/skin-glow-treatment/">Skin
                                                    Glow Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/medi-facials/">Medifacials</a>
                                            </li>
                                            <li>
                                                <a href="">Hydrafacial Md Elite</a>
                                            </li>
                                            <li>
                                                <a href="">Carbon Laser Facial</a>
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
                                                <a href="">Thread Lift</a>
                                            </li>
                                            <li>
                                                <a href="">RF Treatments</a>
                                            </li>
                                            <li>
                                                <a href="">MNRF Treatment</a>
                                            </li>
                                            <li>
                                                <a href="">HIFU Treatment</a>
                                            </li>
                                            <li>
                                                <a href="">Laser resurfacing </a>
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
                                                <a href="">Intimate Part Lightening Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/de-tan-treatment/">De-Taning
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="">Neck Pigmentation</a>
                                            </li>
                                        </ul>
                                        <h4 class="title">Laser Treatments</h4>
                                        <ul>
                                            <li>
                                                <a href="">Laser Hair Reduction</a>
                                            </li>
                                            <li>
                                                <a href="">Laser Tattoo Removal</a>
                                            </li>
                                            <li>
                                                <a href="">Laser For Pigmentation</a>
                                            </li>
                                            <li>
                                                <a href="">Laser For Acne Scars</a>
                                            </li>
                                            <li>
                                                <a href="">Laser For Chickenpox Scars</a>
                                            </li>
                                            <li>
                                                <a href="">Laser For Birth Marks </a>
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
                                                <a href="">Undereye Pigmentation</a>
                                            </li>
                                            <li>
                                                <a href="">Lip Pigmentation</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/service/facial-cosmetic-treatment/chemical-peels-organic-peels/">Chemical
                                                    Peels</a>
                                            </li>
                                            <li>
                                                <a href="">Dull Face / Photopigmentation</a>
                                            </li>
                                            <li>
                                                <a href="">Facial Tanning</a>
                                            </li>
                                            <li>
                                                <a href="">Xanthelesma</a>
                                            </li>
                                            <li>
                                                <a href="">Milia</a>
                                            </li>
                                            <li>
                                                <a href="">Aging Face</a>
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
                                                <a href="">Underarm Pigmentation</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/service/body-cosmetic-treatment/cellulite-treatment/">Cellulite</a>
                                            </li>
                                            <li>
                                                <a href="">Hesitational Marks</a>
                                            </li>
                                            <li>
                                                <a href="">Body Tanning</a>
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
                                                <a href="">Male Patterend Baldness</a>
                                            </li>
                                            <li>
                                                <a href="">Female Hairloss</a>
                                            </li>
                                            <li>
                                                <a href="">Alopecia Areata</a>
                                            </li>
                                            <li>
                                                <a href="">Scarring Alopecia / LPP</a>
                                            </li>
                                            <li>
                                                <a href="">Dandruff</a>
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
                                    </ul>
                                </div>
                            </li>
                            <li class="menu-item-has-children">
                                <a href="#">About Team <i class="fa fa-angle-down ms-2"></i></a>
                                <div class="sub-menu single-column-menu">
                                    <ul class="text-start">
                                        <li><a href="/dr-jangid/">Dr. Jangid</a></li>
                                        <li><a href="/aestheticians/">Aestheticians</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li class="menu-item-has-children">
                                <a href="#">Offers <i class="fa fa-angle-down ms-2"></i></a>
                                <div class="sub-menu single-column-menu">
                                    <ul class="text-start">
                                        <li><a href="/packages/">Packages</a></li>
                                        <li><a href="/wow-inclusive/">WOW inclusive</a></li>
                                        <li><a href="/latest-offers/">Latest Offers</a></li>
                                        <li><a href="/bridal-packages/">Bridal Packages</a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </nav>
                </div>
                <!-- menu end here -->
                <div class="header-item item-right">
                    <a href="" class="btn book_appoint">Book An Appointment</a>
                    <!-- mobile menu trigger -->
                    <div class="mobile-menu-trigger">
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header end -->