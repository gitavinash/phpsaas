<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once DEFAULT_META ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/css/swiper.min.css'>
</head>

<body>
    <header>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 text-center">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <div class="container-fluid">
                            <a class="navbar-brand" href="/"><img src="<?php echo USER_IMAGES ?>logo.png"
                                    class="logo_img"></a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                                    <li class="nav-item">
                                        <a class="nav-link active" aria-current="page" href="/">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/services/">Services</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            About Team
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <li><a class="dropdown-item" href="/our-dermatologists/">Our
                                                    Dermatologists</a></li>
                                            <li><a class="dropdown-item" href="/aestheticians/">Aestheticians</a></li>
                                            <li><a class="dropdown-item" href="/managers/">Managers</a></li>
                                        </ul>
                                    </li>
                                    <!-- <li class="nav-item">
                                        <a class="nav-link" href="/pricing/">Pricing</a>
                                    </li> -->
                                    <li class="nav-item">
                                        <a class="nav-link" href="/why-us/">Why Us</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/wow-inclusive/">WOW inclusive</a>
                                    </li>
                                </ul>
                                <a href="#" class="btn book_appoint">Book An Appointment</a>

                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>