<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>JourneyFinders - Bali Tours & Packages</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: "#ff5722",
                    secondary: "#4a90e2"
                },
                borderRadius: {
                    none: "0px",
                    sm: "4px",
                    DEFAULT: "8px",
                    md: "12px",
                    lg: "16px",
                    xl: "20px",
                    "2xl": "24px",
                    "3xl": "32px",
                    full: "9999px",
                    button: "8px",
                },
            },
        },
    };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
    <style>
    :where([class^="ri-"])::before {
        content: "\f3c2";
    }

    body {
        font-family: "Poppins", sans-serif;
    }

    .custom-scrollbar::-webkit-scrollbar {
        height: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #ccc;
    }

    .custom-checkbox {
        appearance: none;
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #ddd;
        border-radius: 4px;
        outline: none;
        cursor: pointer;
        position: relative;
    }

    .custom-checkbox:checked {
        background-color: #ff5722;
        border-color: #ff5722;
    }

    .custom-checkbox:checked::after {
        content: "";
        position: absolute;
        top: 2px;
        left: 6px;
        width: 4px;
        height: 8px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .tour-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .tour-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .price-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 6px;
        border-radius: 5px;
        background: #e5e7eb;
        outline: none;
    }

    .price-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ff5722;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .price-slider::-moz-range-thumb {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ff5722;
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .custom-radio {
        appearance: none;
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #ddd;
        border-radius: 50%;
        outline: none;
        cursor: pointer;
        position: relative;
    }

    .custom-radio:checked {
        border-color: #ff5722;
    }

    .custom-radio:checked::after {
        content: "";
        position: absolute;
        top: 3px;
        left: 3px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #ff5722;
    }

    .custom-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
    }

    .custom-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #e5e7eb;
        transition: 0.4s;
        border-radius: 34px;
    }

    .switch-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    input:checked+.switch-slider {
        background-color: #ff5722;
    }

    input:checked+.switch-slider:before {
        transform: translateX(18px);
    }

    .category-card {
        transition: transform 0.3s ease;
    }

    .category-card:hover {
        transform: translateX(5px);
    }

    .sticky-sidebar {
        position: sticky;
        top: 100px;
    }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center">
                <a href="#" class="mr-8">
                    <img src="https://static.readdy.ai/image/e53293cd76d16da5ebc50b3bdf122b81/9d10c38df657779d6114b355aeab28cc.png"
                        alt="JourneyFinders" class="h-10" />
                </a>
                <div class="hidden lg:flex space-x-6">
                    <a href="#" class="text-gray-700 hover:text-primary font-medium">Home</a>
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/0c64f721-0262-4256-8e0f-887bc79d6852"
                        data-readdy="true" class="text-gray-700 hover:text-primary font-medium">Destinations</a>
                    <a href="#" class="text-primary font-medium">Tour Packages</a>
                    <a href="#" class="text-gray-700 hover:text-primary font-medium">About Us</a>
                    <a href="#" class="text-gray-700 hover:text-primary font-medium">Contact</a>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <div class="relative">
                    <div
                        class="w-10 h-10 flex items-center justify-center text-gray-500 absolute left-2 top-1/2 transform -translate-y-1/2">
                        <i class="ri-search-line ri-lg"></i>
                    </div>
                    <input type="text" placeholder="Search tours..."
                        class="pl-12 pr-4 py-2 w-64 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm" />
                </div>
                <a href="#"
                    class="bg-primary text-white px-4 py-2 !rounded-button whitespace-nowrap font-medium text-sm hover:bg-primary/90 transition">Login
                    / Register</a>
            </div>
            <div class="block md:hidden">
                <button class="w-10 h-10 flex items-center justify-center text-gray-700">
                    <i class="ri-menu-line ri-lg"></i>
                </button>
            </div>
        </div>
    </header>
    <!-- Destination Banner -->
    <div class="relative h-64 md:h-80 overflow-hidden">
        <img src="#api/search-image?query=beautiful%2520Bali%2520rice%2520terraces%2520with%2520traditional%2520temples%2520in%2520background%252C%2520lush%2520green%2520landscape%252C%2520perfect%2520sunny%2520day%252C%2520aerial%2520view%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=1600&height=600&seq=201&orientation=landscape"
            alt="Bali" class="w-full h-full object-cover object-top" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-6 md:p-10">
            <div class="container mx-auto">
                <div class="flex items-center text-white mb-2">
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/0c64f721-0262-4256-8e0f-887bc79d6852"
                        data-readdy="true" class="flex items-center hover:text-primary transition">
                        <i class="ri-arrow-left-line mr-2"></i>
                        <span>Back to Destinations</span>
                    </a>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                    Bali Tours & Packages
                </h1>
                <div class="flex flex-wrap gap-4 text-white">
                    <div class="flex items-center">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-map-pin-line"></i>
                        </div>
                        <span>Indonesia</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span>Best time: April - October</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-star-fill"></i>
                        </div>
                        <span>4.8/5 (2,456 reviews)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <div class="lg:w-1/4">
                <div class="bg-white rounded-lg shadow-sm p-5 sticky-sidebar">
                    <h3 class="text-lg font-semibold mb-4">Filter Tours</h3>
                    <!-- Duration Filter -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Duration</h4>
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="custom-checkbox" />
                                <span class="ml-2 text-sm">1-3 days</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="custom-checkbox" checked />
                                <span class="ml-2 text-sm">4-7 days</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="custom-checkbox" />
                                <span class="ml-2 text-sm">8-14 days</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="custom-checkbox" />
                                <span class="ml-2 text-sm">15+ days</span>
                            </label>
                        </div>
                    </div>
                    <!-- Price Range -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Price Range</h4>
                        <input type="range" min="0" max="5000" value="2500" class="price-slider mb-2" id="priceRange" />
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>$0</span>
                            <span id="priceValue">$2,500</span>
                            <span>$5,000</span>
                        </div>
                    </div>
                    <!-- Tour Type -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Tour Type</h4>
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="custom-checkbox" checked />
                                <span class="ml-2 text-sm">Adventure</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="custom-checkbox" checked />
                                <span class="ml-2 text-sm">Cultural</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="custom-checkbox" />
                                <span class="ml-2 text-sm">Beach</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="custom-checkbox" />
                                <span class="ml-2 text-sm">Wildlife</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="custom-checkbox" />
                                <span class="ml-2 text-sm">Food & Culinary</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" class="custom-checkbox" />
                                <span class="ml-2 text-sm">Wellness</span>
                            </label>
                        </div>
                    </div>
                    <!-- Group Size -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Group Size</h4>
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="groupSize" class="custom-radio" checked />
                                <span class="ml-2 text-sm">Any</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="groupSize" class="custom-radio" />
                                <span class="ml-2 text-sm">Small Group (1-8)</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="groupSize" class="custom-radio" />
                                <span class="ml-2 text-sm">Medium Group (9-16)</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="groupSize" class="custom-radio" />
                                <span class="ml-2 text-sm">Large Group (17+)</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="groupSize" class="custom-radio" />
                                <span class="ml-2 text-sm">Private Tour</span>
                            </label>
                        </div>
                    </div>
                    <!-- Additional Filters -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Additional Filters</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Free Cancellation</span>
                                <label class="custom-switch">
                                    <input type="checkbox" checked />
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Meals Included</span>
                                <label class="custom-switch">
                                    <input type="checkbox" />
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Hotel Pickup</span>
                                <label class="custom-switch">
                                    <input type="checkbox" checked />
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Instant Confirmation</span>
                                <label class="custom-switch">
                                    <input type="checkbox" />
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <button
                        class="w-full bg-primary text-white py-2 !rounded-button font-medium hover:bg-primary/90 transition">
                        Apply Filters
                    </button>
                </div>
            </div>
            <!-- Tour Listings -->
            <div class="lg:w-3/4">
                <!-- Sort and Results Info -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-xl font-bold">15 Tours Available</h2>
                        <p class="text-gray-600 text-sm">
                            Showing the best tours for Bali
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <select
                                class="pl-4 pr-10 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm appearance-none bg-white">
                                <option value="recommended">Sort: Recommended</option>
                                <option value="price-low">Sort: Price Low-High</option>
                                <option value="price-high">Sort: Price High-Low</option>
                                <option value="duration-short">
                                    Sort: Duration Short-Long
                                </option>
                                <option value="rating">Sort: Highest Rated</option>
                            </select>
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <i class="ri-arrow-down-s-line text-gray-500"></i>
                            </div>
                        </div>
                        <div class="flex border border-gray-200 rounded-lg overflow-hidden">
                            <button id="list-view" class="px-3 py-2 bg-white text-gray-700 hover:bg-gray-50 transition">
                                <i class="ri-list-check-2"></i>
                            </button>
                            <button id="grid-view"
                                class="px-3 py-2 bg-primary text-white hover:bg-primary/90 transition">
                                <i class="ri-layout-grid-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Tour Cards -->
                <div id="tours-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                    <!-- Tour Card 1 -->
                    <div class="tour-card bg-white rounded-lg overflow-hidden shadow-sm">
                        <div class="relative h-48 overflow-hidden">
                            <img src="#api/search-image?query=Bali%2520cultural%2520tour%2520with%2520traditional%2520temple%2520ceremony%252C%2520rice%2520terraces%252C%2520and%2520local%2520village%2520visit%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=202&orientation=landscape"
                                alt="Bali Cultural Tour" class="w-full h-full object-cover object-top" />
                            <div
                                class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-primary text-xs font-medium px-2 py-1 rounded-full">
                                Bestseller
                            </div>
                            <div
                                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                                <i class="ri-heart-line"></i>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-lg">
                                    Bali Cultural Heritage Tour
                                </h3>
                                <div
                                    class="flex items-center bg-primary/10 text-primary px-2 py-1 rounded text-sm font-medium">
                                    <i class="ri-star-fill mr-1"></i>
                                    <span>4.9</span>
                                    <span class="text-xs text-gray-600 ml-1">(348)</span>
                                </div>
                            </div>
                            <div class="flex items-center mb-3 text-sm text-gray-600">
                                <div class="flex items-center mr-4">
                                    <i class="ri-time-line mr-1"></i>
                                    <span>5 days</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="ri-group-line mr-1"></i>
                                    <span>Max 12 people</span>
                                </div>
                            </div>
                            <ul class="mb-4 text-sm text-gray-600 space-y-1">
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Visit 5 UNESCO World Heritage temples</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Traditional dance performance & dinner</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Guided tour of Ubud art villages</span>
                                </li>
                            </ul>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span class="text-xs text-gray-500">From</span>
                                    <div class="font-bold text-lg">$899</div>
                                    <span class="text-xs text-gray-500">per person</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-green-600">
                                        Available
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Next date: Apr 22, 2025
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/c7f8456d-faf3-4470-94ff-fd75d14db67b"
                                    data-readdy="true"
                                    class="flex-1 bg-white text-primary border border-primary text-center px-3 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/5 transition">View
                                    Details</a>
                                <a href="#"
                                    class="flex-1 bg-primary text-white text-center px-3 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">Book
                                    Now</a>
                            </div>
                        </div>
                    </div>
                    <!-- Tour Card 2 -->
                    <div class="tour-card bg-white rounded-lg overflow-hidden shadow-sm">
                        <div class="relative h-48 overflow-hidden">
                            <img src="#api/search-image?query=Bali%2520adventure%2520tour%2520with%2520white%2520water%2520rafting%252C%2520jungle%2520trekking%252C%2520and%2520volcano%2520hiking%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=203&orientation=landscape"
                                alt="Bali Adventure Tour" class="w-full h-full object-cover object-top" />
                            <div
                                class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-green-600 text-xs font-medium px-2 py-1 rounded-full">
                                20% Off
                            </div>
                            <div
                                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                                <i class="ri-heart-line"></i>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-lg">Bali Adventure Explorer</h3>
                                <div
                                    class="flex items-center bg-primary/10 text-primary px-2 py-1 rounded text-sm font-medium">
                                    <i class="ri-star-fill mr-1"></i>
                                    <span>4.7</span>
                                    <span class="text-xs text-gray-600 ml-1">(215)</span>
                                </div>
                            </div>
                            <div class="flex items-center mb-3 text-sm text-gray-600">
                                <div class="flex items-center mr-4">
                                    <i class="ri-time-line mr-1"></i>
                                    <span>3 days</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="ri-group-line mr-1"></i>
                                    <span>Max 8 people</span>
                                </div>
                            </div>
                            <ul class="mb-4 text-sm text-gray-600 space-y-1">
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>White water rafting on Ayung River</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Mt. Batur sunrise trek with breakfast</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Jungle zip-lining adventure</span>
                                </li>
                            </ul>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span class="text-xs text-gray-500">From</span>
                                    <div class="flex items-center">
                                        <span class="line-through text-sm text-gray-400 mr-2">$649</span>
                                        <span class="font-bold text-lg">$519</span>
                                    </div>
                                    <span class="text-xs text-gray-500">per person</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-green-600">
                                        Available
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Next date: Apr 18, 2025
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/c7f8456d-faf3-4470-94ff-fd75d14db67b"
                                    data-readdy="true"
                                    class="flex-1 bg-white text-primary border border-primary text-center px-3 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/5 transition">View
                                    Details</a>
                                <a href="#"
                                    class="flex-1 bg-primary text-white text-center px-3 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">Book
                                    Now</a>
                            </div>
                        </div>
                    </div>
                    <!-- Tour Card 3 -->
                    <div class="tour-card bg-white rounded-lg overflow-hidden shadow-sm">
                        <div class="relative h-48 overflow-hidden">
                            <img src="#api/search-image?query=Bali%2520beach%2520tour%2520with%2520luxury%2520beach%2520clubs%252C%2520sunset%2520sailing%252C%2520and%2520snorkeling%2520at%2520coral%2520reefs%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=204&orientation=landscape"
                                alt="Bali Beach Tour" class="w-full h-full object-cover object-top" />
                            <div
                                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                                <i class="ri-heart-line"></i>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-lg">Bali Beach Paradise</h3>
                                <div
                                    class="flex items-center bg-primary/10 text-primary px-2 py-1 rounded text-sm font-medium">
                                    <i class="ri-star-fill mr-1"></i>
                                    <span>4.8</span>
                                    <span class="text-xs text-gray-600 ml-1">(189)</span>
                                </div>
                            </div>
                            <div class="flex items-center mb-3 text-sm text-gray-600">
                                <div class="flex items-center mr-4">
                                    <i class="ri-time-line mr-1"></i>
                                    <span>4 days</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="ri-group-line mr-1"></i>
                                    <span>Max 10 people</span>
                                </div>
                            </div>
                            <ul class="mb-4 text-sm text-gray-600 space-y-1">
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Visit 5 stunning beaches & hidden coves</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Luxury catamaran sunset cruise</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Snorkeling at Nusa Penida coral reefs</span>
                                </li>
                            </ul>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span class="text-xs text-gray-500">From</span>
                                    <div class="font-bold text-lg">$749</div>
                                    <span class="text-xs text-gray-500">per person</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-green-600">
                                        Available
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Next date: Apr 25, 2025
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/c7f8456d-faf3-4470-94ff-fd75d14db67b"
                                    data-readdy="true"
                                    class="flex-1 bg-white text-primary border border-primary text-center px-3 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/5 transition">View
                                    Details</a>
                                <a href="#"
                                    class="flex-1 bg-primary text-white text-center px-3 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">Book
                                    Now</a>
                            </div>
                        </div>
                    </div>
                    <!-- Tour Card 4 -->
                    <div class="tour-card bg-white rounded-lg overflow-hidden shadow-sm">
                        <div class="relative h-48 overflow-hidden">
                            <img src="#api/search-image?query=Bali%2520wellness%2520retreat%2520with%2520yoga%2520classes%252C%2520spa%2520treatments%252C%2520and%2520meditation%2520sessions%2520in%2520lush%2520tropical%2520setting%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=205&orientation=landscape"
                                alt="Bali Wellness Retreat" class="w-full h-full object-cover object-top" />
                            <div
                                class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-primary text-xs font-medium px-2 py-1 rounded-full">
                                Popular
                            </div>
                            <div
                                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                                <i class="ri-heart-line"></i>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-lg">Bali Wellness Retreat</h3>
                                <div
                                    class="flex items-center bg-primary/10 text-primary px-2 py-1 rounded text-sm font-medium">
                                    <i class="ri-star-fill mr-1"></i>
                                    <span>4.9</span>
                                    <span class="text-xs text-gray-600 ml-1">(276)</span>
                                </div>
                            </div>
                            <div class="flex items-center mb-3 text-sm text-gray-600">
                                <div class="flex items-center mr-4">
                                    <i class="ri-time-line mr-1"></i>
                                    <span>7 days</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="ri-group-line mr-1"></i>
                                    <span>Max 8 people</span>
                                </div>
                            </div>
                            <ul class="mb-4 text-sm text-gray-600 space-y-1">
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Daily yoga & meditation sessions</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Traditional Balinese healing treatments</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Organic cooking classes & nutrition workshops</span>
                                </li>
                            </ul>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span class="text-xs text-gray-500">From</span>
                                    <div class="font-bold text-lg">$1,299</div>
                                    <span class="text-xs text-gray-500">per person</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-orange-600">
                                        Limited spots
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Next date: May 1, 2025
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/c7f8456d-faf3-4470-94ff-fd75d14db67b"
                                    data-readdy="true"
                                    class="flex-1 bg-white text-primary border border-primary text-center px-3 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/5 transition">View
                                    Details</a>
                                <a href="#"
                                    class="flex-1 bg-primary text-white text-center px-3 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">Book
                                    Now</a>
                            </div>
                        </div>
                    </div>
                    <!-- Tour Card 5 -->
                    <div class="tour-card bg-white rounded-lg overflow-hidden shadow-sm">
                        <div class="relative h-48 overflow-hidden">
                            <img src="#api/search-image?query=Bali%2520food%2520tour%2520with%2520cooking%2520classes%252C%2520market%2520visits%252C%2520and%2520authentic%2520local%2520cuisine%2520tastings%252C%2520professional%2520food%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=206&orientation=landscape"
                                alt="Bali Food Tour" class="w-full h-full object-cover object-top" />
                            <div
                                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                                <i class="ri-heart-line"></i>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-lg">Bali Culinary Journey</h3>
                                <div
                                    class="flex items-center bg-primary/10 text-primary px-2 py-1 rounded text-sm font-medium">
                                    <i class="ri-star-fill mr-1"></i>
                                    <span>4.8</span>
                                    <span class="text-xs text-gray-600 ml-1">(152)</span>
                                </div>
                            </div>
                            <div class="flex items-center mb-3 text-sm text-gray-600">
                                <div class="flex items-center mr-4">
                                    <i class="ri-time-line mr-1"></i>
                                    <span>4 days</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="ri-group-line mr-1"></i>
                                    <span>Max 10 people</span>
                                </div>
                            </div>
                            <ul class="mb-4 text-sm text-gray-600 space-y-1">
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Traditional market tours & ingredient shopping</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Hands-on cooking classes with local chefs</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Street food tours in Denpasar & Ubud</span>
                                </li>
                            </ul>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span class="text-xs text-gray-500">From</span>
                                    <div class="font-bold text-lg">$679</div>
                                    <span class="text-xs text-gray-500">per person</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-green-600">
                                        Available
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Next date: Apr 28, 2025
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/c7f8456d-faf3-4470-94ff-fd75d14db67b"
                                    data-readdy="true"
                                    class="flex-1 bg-white text-primary border border-primary text-center px-3 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/5 transition">View
                                    Details</a>
                                <a href="#"
                                    class="flex-1 bg-primary text-white text-center px-3 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">Book
                                    Now</a>
                            </div>
                        </div>
                    </div>
                    <!-- Tour Card 6 -->
                    <div class="tour-card bg-white rounded-lg overflow-hidden shadow-sm">
                        <div class="relative h-48 overflow-hidden">
                            <img src="#api/search-image?query=Bali%2520photography%2520tour%2520with%2520professional%2520guide%252C%2520visits%2520to%2520most%2520photogenic%2520locations%252C%2520rice%2520terraces%252C%2520temples%2520at%2520golden%2520hour%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=207&orientation=landscape"
                                alt="Bali Photography Tour" class="w-full h-full object-cover object-top" />
                            <div
                                class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-blue-600 text-xs font-medium px-2 py-1 rounded-full">
                                New Tour
                            </div>
                            <div
                                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                                <i class="ri-heart-line"></i>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-lg">
                                    Bali Photography Expedition
                                </h3>
                                <div
                                    class="flex items-center bg-primary/10 text-primary px-2 py-1 rounded text-sm font-medium">
                                    <i class="ri-star-fill mr-1"></i>
                                    <span>4.7</span>
                                    <span class="text-xs text-gray-600 ml-1">(68)</span>
                                </div>
                            </div>
                            <div class="flex items-center mb-3 text-sm text-gray-600">
                                <div class="flex items-center mr-4">
                                    <i class="ri-time-line mr-1"></i>
                                    <span>6 days</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="ri-group-line mr-1"></i>
                                    <span>Max 6 people</span>
                                </div>
                            </div>
                            <ul class="mb-4 text-sm text-gray-600 space-y-1">
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Professional photography guidance</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Sunrise & sunset shoots at iconic locations</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                    <span>Portrait sessions with local Balinese people</span>
                                </li>
                            </ul>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span class="text-xs text-gray-500">From</span>
                                    <div class="font-bold text-lg">$1,149</div>
                                    <span class="text-xs text-gray-500">per person</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-green-600">
                                        Available
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Next date: May 5, 2025
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/c7f8456d-faf3-4470-94ff-fd75d14db67b"
                                    data-readdy="true"
                                    class="flex-1 bg-white text-primary border border-primary text-center px-3 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/5 transition">View
                                    Details</a>
                                <a href="#"
                                    class="flex-1 bg-primary text-white text-center px-3 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">Book
                                    Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Pagination -->
                <div class="flex justify-center mt-8">
                    <nav class="flex items-center">
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 text-gray-600 hover:border-primary hover:text-primary transition mr-2">
                            <i class="ri-arrow-left-s-line"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-primary text-white mr-2">1</a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 text-gray-600 hover:border-primary hover:text-primary transition mr-2">2</a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 text-gray-600 hover:border-primary hover:text-primary transition mr-2">3</a>
                        <span class="mx-2 text-gray-400">...</span>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 text-gray-600 hover:border-primary hover:text-primary transition mr-2">8</a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 text-gray-600 hover:border-primary hover:text-primary transition">
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Tour Categories Section -->
    <section class="py-10 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold mb-6">Explore Tour Categories</h2>
            <div class="overflow-x-auto custom-scrollbar">
                <div class="flex space-x-4 py-2 min-w-max">
                    <!-- Category Card 1 -->
                    <div class="category-card bg-white rounded-lg shadow-sm overflow-hidden w-64">
                        <div class="flex items-center p-4">
                            <div
                                class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-lg mr-4">
                                <i class="ri-compass-3-line ri-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Day Tours</h3>
                                <p class="text-sm text-gray-600">12 tours available</p>
                            </div>
                            <div class="ml-auto w-8 h-8 flex items-center justify-center text-gray-400">
                                <i class="ri-arrow-right-s-line"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Category Card 2 -->
                    <div class="category-card bg-white rounded-lg shadow-sm overflow-hidden w-64">
                        <div class="flex items-center p-4">
                            <div
                                class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-lg mr-4">
                                <i class="ri-footprint-line ri-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Multi-day Adventures</h3>
                                <p class="text-sm text-gray-600">18 tours available</p>
                            </div>
                            <div class="ml-auto w-8 h-8 flex items-center justify-center text-gray-400">
                                <i class="ri-arrow-right-s-line"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Category Card 3 -->
                    <div class="category-card bg-white rounded-lg shadow-sm overflow-hidden w-64">
                        <div class="flex items-center p-4">
                            <div
                                class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-lg mr-4">
                                <i class="ri-ancient-gate-line ri-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Cultural Experiences</h3>
                                <p class="text-sm text-gray-600">15 tours available</p>
                            </div>
                            <div class="ml-auto w-8 h-8 flex items-center justify-center text-gray-400">
                                <i class="ri-arrow-right-s-line"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Category Card 4 -->
                    <div class="category-card bg-white rounded-lg shadow-sm overflow-hidden w-64">
                        <div class="flex items-center p-4">
                            <div
                                class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-lg mr-4">
                                <i class="ri-restaurant-line ri-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Food Tours</h3>
                                <p class="text-sm text-gray-600">8 tours available</p>
                            </div>
                            <div class="ml-auto w-8 h-8 flex items-center justify-center text-gray-400">
                                <i class="ri-arrow-right-s-line"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Category Card 5 -->
                    <div class="category-card bg-white rounded-lg shadow-sm overflow-hidden w-64">
                        <div class="flex items-center p-4">
                            <div
                                class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-lg mr-4">
                                <i class="ri-sail-boat-line ri-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Beach Getaways</h3>
                                <p class="text-sm text-gray-600">10 tours available</p>
                            </div>
                            <div class="ml-auto w-8 h-8 flex items-center justify-center text-gray-400">
                                <i class="ri-arrow-right-s-line"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Category Card 6 -->
                    <div class="category-card bg-white rounded-lg shadow-sm overflow-hidden w-64">
                        <div class="flex items-center p-4">
                            <div
                                class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-lg mr-4">
                                <i class="ri-plant-line ri-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Nature & Wildlife</h3>
                                <p class="text-sm text-gray-600">14 tours available</p>
                            </div>
                            <div class="ml-auto w-8 h-8 flex items-center justify-center text-gray-400">
                                <i class="ri-arrow-right-s-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Quick Information Panel -->
    <section class="py-10 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold mb-6">
                Essential Bali Travel Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Info Card 1 -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-10 h-10 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-3">
                            <i class="ri-calendar-check-line"></i>
                        </div>
                        <h3 class="font-semibold text-lg">Best Time to Visit</h3>
                    </div>
                    <p class="text-gray-600 mb-3">
                        The best time to visit Bali is during the dry season from April to
                        October. This period offers sunny days, lower humidity, and ideal
                        conditions for outdoor activities and beach visits.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">April - June (Best)</span>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">July - August (Peak)</span>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">September - October
                            (Good)</span>
                    </div>
                </div>
                <!-- Info Card 2 -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-10 h-10 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-3">
                            <i class="ri-sun-line"></i>
                        </div>
                        <h3 class="font-semibold text-lg">Weather</h3>
                    </div>
                    <p class="text-gray-600 mb-3">
                        Bali has a tropical climate with warm temperatures year-round. The
                        average temperature ranges from 26-33°C (79-91°F) during the day
                        and 22-25°C (72-77°F) at night.
                    </p>
                    <div id="weatherChart" class="h-40 w-full"></div>
                </div>
                <!-- Info Card 3 -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-10 h-10 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-3">
                            <i class="ri-money-dollar-circle-line"></i>
                        </div>
                        <h3 class="font-semibold text-lg">Currency & Costs</h3>
                    </div>
                    <p class="text-gray-600 mb-3">
                        The currency in Bali is the Indonesian Rupiah (IDR). Bali can be
                        affordable or luxurious depending on your preferences.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span><span class="font-medium">Budget:</span> $30-50 per day</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span><span class="font-medium">Mid-range:</span> $50-150 per
                                day</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span><span class="font-medium">Luxury:</span> $150+ per day</span>
                        </li>
                    </ul>
                </div>
                <!-- Info Card 4 -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-10 h-10 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-3">
                            <i class="ri-passport-line"></i>
                        </div>
                        <h3 class="font-semibold text-lg">Visa Requirements</h3>
                    </div>
                    <p class="text-gray-600 mb-3">
                        Many countries receive visa-free entry to Bali for stays up to 30
                        days. Some nationalities require a visa on arrival or pre-arranged
                        visa.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span><span class="font-medium">Visa-free:</span> 30 days,
                                non-extendable</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span><span class="font-medium">Visa on Arrival:</span> 30 days,
                                extendable once</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span><span class="font-medium">e-Visa:</span> Available for some
                                countries</span>
                        </li>
                    </ul>
                </div>
                <!-- Info Card 5 -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-10 h-10 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-3">
                            <i class="ri-translate-2"></i>
                        </div>
                        <h3 class="font-semibold text-lg">Language & Culture</h3>
                    </div>
                    <p class="text-gray-600 mb-3">
                        The official language is Indonesian (Bahasa Indonesia), but
                        Balinese is widely spoken. English is common in tourist areas.
                        Bali has a unique Hindu culture with daily rituals and ceremonies.
                    </p>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Dress modestly when visiting temples</span>
                        </div>
                        <div class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Remove shoes before entering homes/temples</span>
                        </div>
                        <div class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Use right hand for giving/receiving</span>
                        </div>
                    </div>
                </div>
                <!-- Info Card 6 -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-10 h-10 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-3">
                            <i class="ri-information-line"></i>
                        </div>
                        <h3 class="font-semibold text-lg">Travel Tips</h3>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Bring mosquito repellent and sunscreen</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Drink bottled water only</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Rent a scooter for easy transportation</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Bargain at markets but be respectful</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Get travel insurance that covers activities</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Respect local customs and traditions</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- Booking Information -->
    <section class="py-10 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold mb-6">Booking Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Booking Info 1 -->
                <div class="bg-white rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-10 h-10 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-3">
                            <i class="ri-file-list-3-line"></i>
                        </div>
                        <h3 class="font-semibold text-lg">Booking Policies</h3>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Secure your booking with a 20% deposit</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Full payment due 30 days before departure</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Minimum age requirement: 12 years for most tours</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Special requirements must be noted at booking</span>
                        </li>
                    </ul>
                </div>
                <!-- Booking Info 2 -->
                <div class="bg-white rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-10 h-10 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-3">
                            <i class="ri-refund-2-line"></i>
                        </div>
                        <h3 class="font-semibold text-lg">Cancellation Terms</h3>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>60+ days before departure: Full refund minus $50 admin
                                fee</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>30-59 days: 50% refund</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>15-29 days: 25% refund</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>0-14 days: No refund</span>
                        </li>
                        <li class="flex items-start">
                            <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                            <span>Free date changes up to 30 days before departure</span>
                        </li>
                    </ul>
                </div>
                <!-- Booking Info 3 -->
                <div class="bg-white rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-10 h-10 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-3">
                            <i class="ri-bank-card-line"></i>
                        </div>
                        <h3 class="font-semibold text-lg">Payment Options</h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        We accept various payment methods for your convenience:
                    </p>
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="flex items-center justify-center bg-gray-100 rounded p-2">
                            <i class="ri-visa-fill ri-lg"></i>
                        </div>
                        <div class="flex items-center justify-center bg-gray-100 rounded p-2">
                            <i class="ri-mastercard-fill ri-lg"></i>
                        </div>
                        <div class="flex items-center justify-center bg-gray-100 rounded p-2">
                            <i class="ri-paypal-fill ri-lg"></i>
                        </div>
                        <div class="flex items-center justify-center bg-gray-100 rounded p-2">
                            <i class="ri-apple-fill ri-lg"></i>
                        </div>
                        <div class="flex items-center justify-center bg-gray-100 rounded p-2">
                            <i class="ri-google-fill ri-lg"></i>
                        </div>
                        <div class="flex items-center justify-center bg-gray-100 rounded p-2">
                            <i class="ri-bank-fill ri-lg"></i>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">
                        All transactions are secure and encrypted. We never store your
                        full payment details.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <!-- Newsletter -->
    <section class="py-12 bg-primary/5">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-2xl font-bold mb-3">Get Travel Inspiration</h2>
                <p class="text-gray-600 mb-6">
                    Subscribe to our newsletter for exclusive deals and travel tips
                </p>
                <div class="flex flex-col sm:flex-row gap-4 max-w-xl mx-auto">
                    <input type="email" placeholder="Your email address"
                        class="flex-grow px-4 py-3 !rounded-button border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    <button
                        class="bg-primary text-white px-6 py-3 !rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition">
                        Subscribe
                    </button>
                </div>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
                <div>
                    <h3 class="text-xl font-bold mb-4">About Us</h3>
                    <p class="text-gray-400 mb-4">
                        JourneyFinders is your gateway to extraordinary travel experiences
                        around the world. We specialize in creating memorable adventures
                        tailored to your preferences.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-primary transition">
                            <i class="ri-facebook-fill"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-primary transition">
                            <i class="ri-twitter-x-fill"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-primary transition">
                            <i class="ri-instagram-fill"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-primary transition">
                            <i class="ri-youtube-fill"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Popular Destinations</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition">Bali, Indonesia</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition">Santorini, Greece</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition">Maldives</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition">Peru</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition">Japan</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition">Paris, France</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition">Home</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition">About Us</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition">Tour Packages</a>
                        </li>
                        <li>
                            <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/0c64f721-0262-4256-8e0f-887bc79d6852"
                                data-readdy="true" class="text-gray-400 hover:text-white transition">Destinations</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition">Contact Us</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition">FAQs</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Contact Us</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <div class="w-5 h-5 flex items-center justify-center mt-1 mr-3 text-gray-400">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <span class="text-gray-400">123 Travel Street, Adventure City, AC 10001, India</span>
                        </li>
                        <li class="flex items-center">
                            <div class="w-5 h-5 flex items-center justify-center mr-3 text-gray-400">
                                <i class="ri-phone-line"></i>
                            </div>
                            <span class="text-gray-400">+91 92112 87555</span>
                        </li>
                        <li class="flex items-center">
                            <div class="w-5 h-5 flex items-center justify-center mr-3 text-gray-400">
                                <i class="ri-mail-line"></i>
                            </div>
                            <span class="text-gray-400">info@journeyfinders.in</span>
                        </li>
                        <li class="flex items-center">
                            <div class="w-5 h-5 flex items-center justify-center mr-3 text-gray-400">
                                <i class="ri-time-line"></i>
                            </div>
                            <span class="text-gray-400">Mon-Sat: 9:00 AM - 6:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm mb-4 md:mb-0">
                        © 2025 JourneyFinders. All rights reserved.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <div class="w-12 h-8 flex items-center justify-center bg-gray-800 rounded">
                            <i class="ri-visa-fill ri-lg text-white"></i>
                        </div>
                        <div class="w-12 h-8 flex items-center justify-center bg-gray-800 rounded">
                            <i class="ri-mastercard-fill ri-lg text-white"></i>
                        </div>
                        <div class="w-12 h-8 flex items-center justify-center bg-gray-800 rounded">
                            <i class="ri-paypal-fill ri-lg text-white"></i>
                        </div>
                        <div class="w-12 h-8 flex items-center justify-center bg-gray-800 rounded">
                            <i class="ri-amazon-pay-fill ri-lg text-white"></i>
                        </div>
                        <div class="w-12 h-8 flex items-center justify-center bg-gray-800 rounded">
                            <i class="ri-google-pay-fill ri-lg text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Floating Elements -->
    <div class="fixed bottom-6 right-6 z-40 flex flex-col gap-4">
        <button
            class="w-12 h-12 flex items-center justify-center bg-green-500 text-white rounded-full shadow-lg hover:bg-green-600 transition">
            <i class="ri-whatsapp-line ri-lg"></i>
        </button>
        <button id="back-to-top"
            class="w-12 h-12 flex items-center justify-center bg-gray-800 text-white rounded-full shadow-lg hover:bg-gray-700 transition">
            <i class="ri-arrow-up-line ri-lg"></i>
        </button>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const listViewBtn = document.getElementById("list-view");
        const gridViewBtn = document.getElementById("grid-view");
        const toursContainer = document.getElementById("tours-container");

        function setListView() {
            listViewBtn.classList.remove("bg-white", "text-gray-700");
            listViewBtn.classList.add("bg-primary", "text-white");
            gridViewBtn.classList.remove("bg-primary", "text-white");
            gridViewBtn.classList.add("bg-white", "text-gray-700");
            toursContainer.classList.remove(
                "grid-cols-1",
                "md:grid-cols-2",
                "lg:grid-cols-2"
            );
            toursContainer.classList.add("grid-cols-1");
            const tourCards = toursContainer.querySelectorAll(".tour-card");
            tourCards.forEach((card) => {
                card.classList.add("flex", "flex-col", "md:flex-row");
                const imageDiv = card.querySelector(".relative");
                imageDiv.classList.remove("h-48");
                imageDiv.classList.add("md:w-1/3", "h-full", "min-h-[300px]");
                const contentDiv = card.querySelector(".p-4");
                contentDiv.classList.add(
                    "md:w-2/3",
                    "flex",
                    "flex-col",
                    "justify-between"
                );
            });
        }

        function setGridView() {
            gridViewBtn.classList.remove("bg-white", "text-gray-700");
            gridViewBtn.classList.add("bg-primary", "text-white");
            listViewBtn.classList.remove("bg-primary", "text-white");
            listViewBtn.classList.add("bg-white", "text-gray-700");
            toursContainer.classList.remove("grid-cols-1");
            toursContainer.classList.add(
                "grid-cols-1",
                "md:grid-cols-2",
                "lg:grid-cols-2"
            );
            const tourCards = toursContainer.querySelectorAll(".tour-card");
            tourCards.forEach((card) => {
                card.classList.remove("flex", "flex-col", "md:flex-row");
                const imageDiv = card.querySelector(".relative");
                imageDiv.classList.remove("md:w-1/3", "h-full", "min-h-[300px]");
                imageDiv.classList.add("h-48");
                const contentDiv = card.querySelector(".p-4");
                contentDiv.classList.remove(
                    "md:w-2/3",
                    "flex",
                    "flex-col",
                    "justify-between"
                );
            });
        }
        listViewBtn.addEventListener("click", setListView);
        gridViewBtn.addEventListener("click", setGridView);
        // Back to top button
        const backToTopButton = document.getElementById("back-to-top");
        if (backToTopButton) {
            backToTopButton.addEventListener("click", () => {
                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
            });
        }
        // Price range slider
        const priceRange = document.getElementById("priceRange");
        const priceValue = document.getElementById("priceValue");
        if (priceRange && priceValue) {
            priceRange.addEventListener("input", function() {
                const value = this.value;
                priceValue.textContent =
                    "$" + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            });
        }
        // Weather chart
        const weatherChart = document.getElementById("weatherChart");
        if (weatherChart && typeof echarts !== "undefined") {
            const chart = echarts.init(weatherChart);
            const option = {
                animation: false,
                grid: {
                    top: 10,
                    right: 10,
                    bottom: 20,
                    left: 40,
                },
                xAxis: {
                    type: "category",
                    data: [
                        "Jan",
                        "Feb",
                        "Mar",
                        "Apr",
                        "May",
                        "Jun",
                        "Jul",
                        "Aug",
                        "Sep",
                        "Oct",
                        "Nov",
                        "Dec",
                    ],
                    axisLabel: {
                        color: "#1f2937",
                        fontSize: 10,
                    },
                    axisLine: {
                        lineStyle: {
                            color: "#e5e7eb",
                        },
                    },
                },
                yAxis: {
                    type: "value",
                    min: 0,
                    max: 350,
                    axisLabel: {
                        color: "#1f2937",
                        fontSize: 10,
                        formatter: function(value) {
                            return value === 0 ? "0" : value + "mm";
                        },
                    },
                    splitLine: {
                        lineStyle: {
                            color: "#e5e7eb",
                        },
                    },
                },
                tooltip: {
                    trigger: "axis",
                    backgroundColor: "rgba(255, 255, 255, 0.8)",
                    borderColor: "#e5e7eb",
                    textStyle: {
                        color: "#1f2937",
                    },
                    formatter: function(params) {
                        return params[0].name + ": " + params[0].value + "mm rainfall";
                    },
                },
                series: [{
                    data: [70, 100, 90, 70, 60, 40, 30, 30, 50, 90, 150, 140],
                    type: "line",
                    smooth: true,
                    symbol: "none",
                    lineStyle: {
                        color: "rgba(87, 181, 231, 1)",
                        width: 3,
                    },
                    areaStyle: {
                        color: {
                            type: "linear",
                            x: 0,
                            y: 0,
                            x2: 0,
                            y2: 1,
                            colorStops: [{
                                    offset: 0,
                                    color: "rgba(87, 181, 231, 0.2)",
                                },
                                {
                                    offset: 1,
                                    color: "rgba(87, 181, 231, 0.01)",
                                },
                            ],
                        },
                    },
                }, ],
            };
            chart.setOption(option);
            window.addEventListener("resize", function() {
                chart.resize();
            });
        }
    });
    </script>
</body>

</html>