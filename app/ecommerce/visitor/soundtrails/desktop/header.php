<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Catamaran&family=Dawning+of+a+New+Day&family=Water+Brush&family=ZCOOL+XiaoWei&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>global.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>navbar.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>responsive.css">
</head>

<body class="mainPage">
    <!-- header -->
    <div class="header_section w-100">
        <header class="container">
            <div class="menuContainer">
                <div class="logo">
                    <img src="<?php echo USER_IMAGES ?>logo.png" alt="" class="w-100">
                </div>
                <nav role="navigation">
                    <a href="javascript:void(0);" class="ic menu"><i class="fas fa-bars"></i></a>
                    <!-- <a href="javascript:void(0);" class="ic close"><i class="fas fa-times"></i></a> -->
                    <ul class="main-nav">
                        <li class="top-level-link">
                            <a><span>Home</span></a>
                            <a href="javascript:void(0);" class="ic close newcss"><i class="fas fa-times"></i></a>

                        </li>

                        <li class="top-level-link mega-menu">
                            <a class="subMenu"><span>Products</span></a>
                            <div class="sub-menu-block">
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <h2 class="sub-menu-head">Company</h2>
                                        <ul class="sub-menu-lists">
                                            <li><a href="">About</a></li>
                                            <li><a>Mission</a></li>
                                            <li><a>Community</a></li>
                                            <li><a>Team</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <h2 class="sub-menu-head">Handbags</h2>
                                        <ul class="sub-menu-lists">
                                            <li><a>Wallets</a></li>
                                            <li><a>Athletic bag</a></li>
                                            <li><a>Backpack</a></li>
                                            <li><a>Bucket Bag</a></li>
                                            <li><a>View All</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <h2 class="sub-menu-head">Company</h2>
                                        <ul class="sub-menu-lists">
                                            <li><a href="">About</a></li>
                                            <li><a>Mission</a></li>
                                            <li><a>Community</a></li>
                                            <li><a>Team</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <h2 class="sub-menu-head">Shoes</h2>
                                        <ul class="sub-menu-lists">
                                            <li><a>Mens</a></li>
                                            <li><a>Womens</a></li>
                                            <li><a>Kids</a></li>
                                            <li><a>View All</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="top-level-link">
                            <a><span>Services<span></a>
                        </li>
                        <li class="top-level-link">
                            <a><span>About</span></a>

                        </li>
                        <li class="top-level-link">
                            <a><span>Contact</span></a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="accounts">
                <i class="fas fa-user"></i>
                <i class="fa-solid fa-cart-shopping" onclick="showsidebar();"></i>
                <div class="leftsidebar">
                    <i class="fas fa-times" onclick="removeleftsidebar();"></i>
                    <div class="head">Your Cart</div>
                    <p class="my-5">Your cart is currently empty.</p>
                    <a href="" class="bttn f-flex">Continue Shoping</a>
                </div>
            </div>

        </header>
    </div>
    <!-- header ends -->