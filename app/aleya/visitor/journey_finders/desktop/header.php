<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <?php require_once DEFAULT_META ?>
</head>

<body class="bg-white">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center">
                <a href="/" class="mr-8">
                    <img src="https://static.readdy.ai/image/e53293cd76d16da5ebc50b3bdf122b81/9d10c38df657779d6114b355aeab28cc.png"
                        alt="JourneyFinders" class="h-10" />
                </a>
                <div class="hidden lg:flex space-x-6">
                    <a href="/"
                        class="<?php echo ((ACTIVE_URL == '/') ? 'text-primary' : 'text-gray-700') ?> text-gray-700 hover:text-primary font-medium">Home</a>
                    <a href="/destinations/"
                        class="<?php echo ((ACTIVE_URL == '/destinations/') ? 'text-primary' : 'text-gray-700') ?> hover:text-primary font-medium">Destinations</a>
                    <a href="/about-us/"
                        class="<?php echo ((ACTIVE_URL == '/about-us/') ? 'text-primary' : 'text-gray-700') ?> text-gray-700 hover:text-primary font-medium">About
                        Us</a>
                    <a href="/contact-us/"
                        class="<?php echo ((ACTIVE_URL == '/contact-us/') ? 'text-primary' : 'text-gray-700') ?> text-gray-700 hover:text-primary font-medium">Contact
                        Us</a>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <div class="relative">
                    <form action="/search/" method="get">
                        <div
                            class="w-10 h-10 flex items-center justify-center text-gray-500 absolute left-2 top-1/2 transform -translate-y-1/2">
                            <i class="ri-search-line ri-lg"></i>
                        </div>
                        <input type="text" name="q" placeholder="Search destinations..."
                            class="pl-12 pr-4 py-2 w-64 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm" />
                    </form>
                </div>
                <a href="#"
                    class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap font-medium text-sm hover:bg-primary/90 transition">Login
                    / Register</a>
            </div>
            <div class="block md:hidden">
                <button id="menuToggle" class="w-10 h-10 flex items-center justify-center text-gray-700">
                    <i class="ri-menu-line ri-lg"></i>
                </button>
            </div>
            <!-- Mobile Menu (full-screen slide-in) -->
            <div id="mobileMenu"
                class="fixed top-0 left-0 w-full h-full bg-white z-40 transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden">
                <div class="p-6 space-y-4 relative">
                    <!-- Close Button -->
                    <button id="menuClose" class="absolute top-4 right-4 text-2xl text-gray-700 hover:text-primary">
                        <i class="ri-close-line"></i>
                    </button>

                    <a href="/" class="block text-gray-700 text-lg hover:text-primary">Home</a>
                    <a href="/destinations/" class="block text-gray-700 text-lg hover:text-primary">Destinations</a>
                    <a href="/about-us/" class="block text-gray-700 text-lg hover:text-primary">About Us</a>
                    <a href="/contact-us/" class="block text-gray-700 text-lg hover:text-primary">Contact Us</a>
                    <a href="#" class="block text-primary font-semibold text-lg">Login / Register</a>
                </div>
            </div>
        </div>
    </header>