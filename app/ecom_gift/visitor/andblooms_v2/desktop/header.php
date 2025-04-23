<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- or -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>global.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>home.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>navbar.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>footer.css">
    <?php require_once DEFAULT_META ?>
</head>

<body>
    <header>
        <nav>
            <div class="my_nav">
                <div class="container-fluid">
                    <div class="top_header">
                        <div class="header_logo">
                            <a href="/"><img src="assets/images/svg/andbloom.svg" class="img-fluid" alt=""></a>
                        </div>
                        <div class="header_right">
                            <div class="search">
                                <input type="text" placeholder="Search">
                                <div class="search_icon">
                                    <button><img src="assets/images/svg/search.svg" class="img-fluid" alt=""></button>
                                </div>
                            </div>
                            <div class="mobile_search header_cart">
                                <a href="#"><img src="assets/images/svg/search.svg" class="img-fluid" alt=""></a>
                            </div>
                            <div class="search_overlay">
                                <div class="search_input">
                                    <div class="mob--search">
                                        <input type="text" placeholder="Search...">
                                        <button><i class='bx bx-search'></i></button>
                                    </div>

                                    <div class="cancel_search__input">
                                        <i class='bx bxs-x-circle'></i>
                                    </div>
                                </div>

                            </div>

                            <div class="header_cart cart_add">
                                <a href="/cart.html" class="cart"><img src="assets/images/svg/lock.svg"
                                        class="img-fluid" alt=""></a>
                                <div class="cart_no">
                                    <span>2</span>
                                </div>
                            </div>
                            <div class="user header_cart">
                                <a href="/contact.html"><img src="assets/images/svg/user.svg" class="img-fluid"
                                        alt=""></a>
                            </div>
                            <div class="top_menu header_cart">
                                <i class='bx bx-menu'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="menu_header">
                <div class="container-fluid">
                    <div class="menu_wrapper">
                        <ul class="menu_flex">
                            <li> <a href="/">Home</a> </li>
                            <li> <a href="/tag/luxury/">Luxury Collection</a> </li>
                            <li> <a href="/tag/occasions/">Occasions</a> </li>
                            <li> <a href="/category/planters/">Planters</a> </li>
                            <li> <a href="/category/cake/">cakes & Hampers</a> </li>
                            <li> <a href="/tag/blooms-of-<?php echo strtolower(date('F')) ?>/">Blooms of
                                    <?php echo ucwords(date('F')) ?></a> </li>
                            <li> <a href="/tag/valentine/">Valentine’s Day</a> </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- responsive nav -->
        <nav>
            <div class="overlay">
                <div class="mobile_nav">
                    <div class="close">
                        <i class='bx bxs-x-circle'></i>
                    </div>
                    <ul class="mobile_nav__flex">
                        <li> <a href="/">Home</a> </li>
                        <li> <a href="/tag/luxury/">Luxury Collection</a> </li>
                        <li> <a href="/tag/occasions/">Occasions</a> </li>
                        <li> <a href="/category/planters/">Planters</a> </li>
                        <li> <a href="/category/cake/">cakes & Hampers</a> </li>
                        <li> <a href="/tag/blooms-of-<?php echo strtolower(date('F')) ?>/">Blooms of
                                <?php echo ucwords(date('F')) ?></a> </li>
                        <li> <a href="/tag/valentine/">Valentine’s Day</a> </li>
                    </ul>
                </div>
            </div>
        </nav>

    </header>