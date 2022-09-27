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
                                <a href="#">Conditions<i class="fa fa-angle-down ms-2"></i></a>
                                <div class="sub-menu mega-menu mega-menu-column-4 text-start">
                                    <div class="list-item">
                                        <h4 class="title">Dermatology</h4>
                                        <ul>
                                            <li>
                                                <a href="/service/dermatology/acne/">Acne</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/acne-scar/">Acne Scar</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/melasma-pigmentation/">Melasma
                                                    Pigmentation</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/allergy-eczema/">Allergy / Eczema</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/psoriasis/">Psoriasis</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/vitiligo-surgery/">Vitiligo</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/tinea/">Tinea</a>
                                            </li>
                                            <li>
                                                <a href="/service/facial-cosmetic-treatment/open-pores/">Open
                                                    Pores</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermato-surgery/mole-removal-surgery/">Moles</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermato-surgery/warts/">Warts</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <ul>
                                            <li>
                                                <a href="/service/dermato-surgery/skin-tag-removal/">Skin
                                                    Tags</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/acne-scar/">Acne
                                                    Scar</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermato-surgery/ear-lobe-repair-surgery/">Ear
                                                    Lobe</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermato-surgery/cyst-removal/">Cyst</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermato-surgery/ingrown-toenail-surgery/">Nail</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/vitiligo-surgery/">Vitiligo</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermato-surgery/lipoma/">Lipoma</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <h4 class="title">Face Cosmetic</h4>
                                        <ul>
                                            <li>
                                                <a href="">Wrinkles</a>
                                            </li>
                                            <li>
                                                <a href="">Crowfeet</a>
                                            </li>
                                            <li>
                                                <a href="/service/facial-cosmetic-treatment/double-chin/">Double
                                                    Chin</a>
                                            </li>
                                            <li>
                                                <a href="/service/facial-cosmetic-treatment/under-eye/">Under
                                                    Eye</a>
                                            </li>
                                            <li>
                                                <a href="">Face Drooping</a>
                                            </li>
                                            <li>
                                                <a href="">Fine Lines</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <h4 class="title">Body Cosmetic</h4>
                                        <ul>
                                            <li>
                                                <a href="/service/body-cosmetic-treatment/stretch-marks/">Strech
                                                    Marks</a>
                                            </li>
                                            <li>
                                                <a href="">Body Pigmentation</a>
                                            </li>
                                            <li>
                                                <a href="">Tanning</a>
                                            </li>
                                            <li>
                                                <a href="">Birthmark</a>
                                            </li>
                                            <li>
                                                <a href="/service/treatment/laser-tattoo-removal/">Tattoo</a>
                                            </li>
                                            <li>
                                                <a href="/service/dermatology/allergy-eczema/">Eczema</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
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
                                                <a href="/treatment/face-cosmetics/acne-scar-treatments/">Acne
                                                    Scar Treatments</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="/treatment/treatment-dermatology/melasma-pigmentation-treatments/">Melasma
                                                    Pigmentation Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/allergy-eczema-treatments/">Allergy
                                                    / Eczema Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/psoriasis-treatment/">Psoriasis
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/skin-biopsy/">Skin
                                                    Biopsy</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/vitiligo-treatments/">Vitiligo
                                                    Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/tinea-treatment/">Tinea
                                                    Treatment</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
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
                                                    Scar Surgeries</a>
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
                                                <a href="/treatment/treatment-dermatology/vitiligo-surgery/">Vitiligo
                                                    Surgery</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/cryosurgery/">Cryosurgery</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/treatment-dermatology/lipoma-treatment/">Lipoma
                                                    Treatment</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <h4 class="title">Face cosmetics</h4>
                                        <ul>
                                            <li>
                                                <a href="/treatment/face-cosmetics/chemical-peels-organic-peels/">Chemical
                                                    Peels / Organic Peels</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/open-pore-treatments/">Open
                                                    Pore Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/under-eye-treatment/">Under
                                                    Eye Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/lip-lightening/">Lip
                                                    Lightening</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/lip-enhancement/">Lip
                                                    Enhancement</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/nose-enhancement/">Nose
                                                    Enhancement</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/medi-facials/">Medi
                                                    Facials</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/skin-glow-treatment/">Skin
                                                    Glow Treatment</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <ul>
                                            <li>
                                                <a href="/treatment/face-cosmetics/double-chin-treatment/">Double
                                                    Chin Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/btx--a-treatment/">Btx-a
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/filler-treatment/">Filler
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/hifu-treatment/">Hifu
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/thread-lift-treatment/">Thread
                                                    Lift Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/vampire-facelift/">Vampire
                                                    Facelift</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/ultracel-treatment/">Ultracel
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/laser-resurfacing/">Laser
                                                    Resurfacing</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/fillers-treatment/">Fillers
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/facelift/">Facelift</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/face-cosmetics/skin-whitening-treatment/">Skin
                                                    Whitening Treatment</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="list-item">
                                        <h4 class="title">Body Cosmetics</h4>
                                        <ul>
                                            <li>
                                                <a href="/treatment/body-cosmetics/stretch-mark-treatments/">Stretch
                                                    Mark Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/de-tan-treatment/">De-tan
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/underarm-treatments/">Underarm
                                                    Treatments</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/birth-mark-removal/">Birth
                                                    Mark Removal</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/cellulite-treatment/">Cellulite
                                                    Treatment</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/hand-rejuvenation/">Hand
                                                    Rejuvenation</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/fat-reduction/">Fat
                                                    Reduction</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/tattoo-removal/">Tattoo
                                                    Removal</a>
                                            </li>
                                            <li>
                                                <a href="/treatment/body-cosmetics/laser-hair-reduction/">Laser
                                                    Hair Reduction</a>
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