<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cal+Sans&family=Kumbh+Sans:wght@100..900&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <?php require_once DEFAULT_META ?>
</head>

<body class="bg-white">
    <?php

    if (BASE_URL == '/visa-application-form/' || BASE_URL == '/start-application/') { ?>
    <div id="page-loader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
    background:rgba(255,255,255,0.8); z-index:9999; text-align:center; padding-top:20%;">
        <div class="spinner"></div>
        <div class="loading-text">Loading...</div>
    </div>
    <?php
    }

    ?>
    <header class="sticky-header fixed w-full top-0 bg-white shadow-sm z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center">
                    <a href="/" class="text-2xl md:text-3xl font-['Pacifico'] text-primary logo"><img
                            src='<?php echo USER_IMAGES ?>logo.png' alt="<?php echo SITE_NAME ?>"></a>
                    <nav class="hidden md:block ml-12">
                        <ul class="flex space-x-8">
                            <li class="dropdown relative">
                                <a href="/services/" class="flex items-center text-gray-700 hover:text-primary">
                                    Visa Services
                                    <i class="ri-arrow-down-s-line ml-1 w-4 h-4 flex items-center justify-center"></i>
                                </a>
                                <div class="dropdown-content absolute top-full left-0 w-48 bg-white shadow-lg rounded">
                                    <a href="/tourist-visa-services/"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tourist
                                        Visa</a>
                                    <a href="/business-visa-services/"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Business
                                        Visa</a>
                                    <a href="/student-visa-services/"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Student
                                        Visa</a>
                                </div>
                            </li>
                            <li><a href="/track-application/" data-readdy="true"
                                    class="text-gray-700 hover:text-primary">Track
                                    Application</a></li>
                            <li><a href="/travel-agent-registration/" data-readdy="true"
                                    class="text-gray-700 hover:text-primary">Travel Agent
                                    Registration</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <?php

                    if (isset($_COOKIE['user_data'])) {
                        echo "
                    <a href='/dashboard/'
                        class='bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-6 py-2 cursor-pointer'>My Account</a>";
                    } else {
                        echo "
                    <a href='/login/?authby=mobile'
                        class='text-primary hover:text-primary/80 font-medium !rounded-button px-6 py-2 cursor-pointer'>Login</a>
                    <a href='/login/?authby=mobile'
                        class='bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-6 py-2 cursor-pointer'>Register</a>";
                    }

                    ?>
                </div>
                <button class="md:hidden w-10 h-10 flex items-center justify-center text-gray-700" id="mobileMenuBtn">
                    <i class="ri-menu-line ri-2x"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div class="md:hidden hidden bg-white" id="mobileMenu">
            <nav class="px-4 py-2">
                <ul class="space-y-2">
                    <li>
                        <a href="/services/" class="block py-2 text-gray-700">Visa Services</a>
                        <div class="pl-4 space-y-2">
                            <a href="/tourist-visa-services/"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tourist
                                Visa</a>
                            <a href="/business-visa-services/"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Business
                                Visa</a>
                            <a href="/student-visa-services/"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Student
                                Visa</a>
                        </div>
                    </li>
                    <li><a href="/track-application/" class="block py-2 text-gray-700">Track Application</a></li>
                    <li><a href="/travel-agent-registraton/" class="block py-2 text-gray-700">Travel Agent
                            Registration</a>
                    </li>
                    <li class="pt-4 pb-4 border-t">
                        <?php

                        if (isset($_COOKIE['user_data'])) {
                            echo "
<a href='/dashboard/'
    class='bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-6 py-2 cursor-pointer'>My Account</a>";
                        } else {
                            echo "
<a href='/login/?authby=mobile'
    class='text-primary hover:text-primary/80 font-medium !rounded-button px-6 py-2 cursor-pointer'>Login</a>
<a href='/login/?authby=mobile'
    class='bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-6 py-2 cursor-pointer'>Register</a>";
                        }

                        ?>
                    </li>
                </ul>
            </nav>
        </div>
    </header>