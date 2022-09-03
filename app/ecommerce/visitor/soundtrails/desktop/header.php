<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.1.1/css/fontawesome.min.css"
        integrity="sha384-zIaWifL2YFF1qaDiAo0JFgsmasocJ/rqu7LKYH8CoBEXqGbb9eO+Xi3s6fQhgFWM" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"
        integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css"
        integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>global.css" />
    <?php require_once DEFAULT_META ?>
</head>

<body>
    <section class="home-hero hero_bottom-padding">
        <!-- navbar -->
        <header>
            <nav class="navbar navbar-expand-lg navbar-dark side-margin nav-padding navbar-global">
                <div class="container-fluid">
                    <a class="navbar-brand" href="/"><img src="<?php echo USER_IMAGES ?>logo.png" alt=""
                            class="img-fluid" /></a>
                    <button class="navbar-toggler mobile-tpggle" type="button" aria-controls="navbarNav"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon " id="navbtn"></span>
                    </button>
                    <!-- 
                  <button>  <span class="navbar-toggler-icon d-none"></span></button> -->
                    <div class="collapse navbar-collapse">
                        <ul class="navbar-nav ms-auto align-items-center">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="/">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/about-us/">About us</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="/products/">Product</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/contact-us/">Contact us</a>
                            </li>
                        </ul>

                        <div class="icons d-flex justify-content-center mb-3 mt-3 mt-xl-0 mb-xl-0">
                            <!-- <a href="#"><i class="fa-solid fa-user"></i></a> -->
                            <a href="/checkout/"><i class="fa-solid fa-cart-shopping"></i></a>
                            <a href=""><i class="fa-solid fa-magnifying-glass"></i></a>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="side-nav ">

                <ul class="navbar-nav ms-3">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about-us/">About us</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/products/">Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact-us/">Contact us</a>
                    </li>
                </ul>

            </div>
        </header>