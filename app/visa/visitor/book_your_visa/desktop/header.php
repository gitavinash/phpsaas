<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <?php require_once DEFAULT_META ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
</head>

<body class="bg-white">
    <header class="sticky-header fixed w-full top-0 bg-white shadow-sm z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center">
                    <a href="/" class="text-2xl md:text-3xl font-['Pacifico'] text-primary">logo</a>
                    <nav class="hidden md:block ml-12">
                        <ul class="flex space-x-8">
                            <li class="dropdown relative">
                                <a href="#" class="flex items-center text-gray-700 hover:text-primary">
                                    Visa Services
                                    <i class="ri-arrow-down-s-line ml-1 w-4 h-4 flex items-center justify-center"></i>
                                </a>
                                <div
                                    class="dropdown-content absolute top-full left-0 mt-2 w-48 bg-white shadow-lg rounded">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tourist
                                        Visa</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Business
                                        Visa</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Student
                                        Visa</a>
                                </div>
                            </li>
                            <li><a href="home/da68aad3-a508-4219-bc9a-0f79e66cb4a4/bfd35c08-38de-47ad-9329-be4e37217bf2"
                                    data-readdy="true" class="text-gray-700 hover:text-primary">Track Application</a>
                            </li>
                            <li><a href="#" class="text-gray-700 hover:text-primary">Support</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <button
                        class="text-primary hover:text-primary/80 font-medium !rounded-button px-6 py-2 cursor-pointer">Login</button>
                    <button
                        class="bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-6 py-2 cursor-pointer">Register</button>
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
                        <a href="#" class="block py-2 text-gray-700">Visa Services</a>
                        <div class="pl-4 space-y-2">
                            <a href="#" class="block py-2 text-sm text-gray-700">Tourist Visa</a>
                            <a href="#" class="block py-2 text-sm text-gray-700">Business Visa</a>
                            <a href="#" class="block py-2 text-sm text-gray-700">Student Visa</a>
                        </div>
                    </li>
                    <li><a href="#" class="block py-2 text-gray-700">Track Application</a></li>
                    <li><a href="#" class="block py-2 text-gray-700">Support</a></li>
                    <li class="pt-2 border-t">
                        <button
                            class="w-full text-primary hover:text-primary/80 font-medium !rounded-button px-6 py-2 cursor-pointer mb-2">Login</button>
                        <button
                            class="w-full bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-6 py-2 cursor-pointer">Register</button>
                    </li>
                </ul>
            </nav>
        </div>
    </header>