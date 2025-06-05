<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>JourneyFinders - Search Results</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: { primary: "#ff5722", secondary: "#4a90e2" },
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
    <link
      href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"
      rel="stylesheet"
    />
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
      input[type="number"]::-webkit-inner-spin-button,
      input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }
      .date-input::-webkit-calendar-picker-indicator {
        opacity: 0;
        position: absolute;
        width: 100%;
        height: 100%;
        cursor: pointer;
      }
      .price-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 4px;
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
        border: 2px solid white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      }
      .price-slider::-moz-range-thumb {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ff5722;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
      .tour-card:hover {
        transform: translateY(-5px);
      }
      .filter-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
      }
      .filter-switch input {
        opacity: 0;
        width: 0;
        height: 0;
      }
      .filter-slider {
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
      .filter-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
      }
      input:checked + .filter-slider {
        background-color: #ff5722;
      }
      input:checked + .filter-slider:before {
        transform: translateX(20px);
      }
    </style>
  </head>
  <body class="bg-gray-50">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white shadow-sm">
      <div
        class="container mx-auto px-4 py-3 flex items-center justify-between"
      >
        <div class="flex items-center">
          <a
            href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/4410b286-d3a7-473c-8dba-f26e720f14d8"
            data-readdy="true"
            class="mr-8"
          >
            <img
              src="https://static.readdy.ai/image/e53293cd76d16da5ebc50b3bdf122b81/9d10c38df657779d6114b355aeab28cc.png"
              alt="JourneyFinders"
              class="h-10"
            />
          </a>
          <div class="hidden lg:flex space-x-6">
            <a
              href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/4410b286-d3a7-473c-8dba-f26e720f14d8"
              data-readdy="true"
              class="text-gray-700 hover:text-primary font-medium"
              >Home</a
            >
            <a
              href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/0c64f721-0262-4256-8e0f-887bc79d6852"
              data-readdy="true"
              class="text-gray-700 hover:text-primary font-medium"
              >Destinations</a
            >
            <a href="#" class="text-primary font-medium">Tour Packages</a>
            <a href="#" class="text-gray-700 hover:text-primary font-medium"
              >About Us</a
            >
            <a href="#" class="text-gray-700 hover:text-primary font-medium"
              >Contact</a
            >
          </div>
        </div>
        <div class="hidden md:flex items-center space-x-4">
          <div class="relative">
            <div
              class="w-10 h-10 flex items-center justify-center text-gray-500 absolute left-2 top-1/2 transform -translate-y-1/2"
            >
              <i class="ri-search-line ri-lg"></i>
            </div>
            <input
              type="text"
              placeholder="Search destinations..."
              class="pl-12 pr-4 py-2 w-64 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm"
            />
          </div>
          <a
            href="#"
            class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap font-medium text-sm hover:bg-primary/90 transition"
            >Login / Register</a
          >
        </div>
        <div class="block md:hidden">
          <button
            class="w-10 h-10 flex items-center justify-center text-gray-700"
          >
            <i class="ri-menu-line ri-lg"></i>
          </button>
        </div>
      </div>
    </header>
    <!-- Search Parameters Bar -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="container mx-auto px-4 py-4">
        <div
          class="flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
          <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-6">
            <div class="flex items-center">
              <div
                class="w-8 h-8 flex items-center justify-center text-primary bg-primary/10 rounded-full mr-2"
              >
                <i class="ri-map-pin-line"></i>
              </div>
              <div>
                <p class="text-xs text-gray-500">Destination</p>
                <p class="font-medium">Bali, Indonesia</p>
              </div>
            </div>
            <div class="flex items-center">
              <div
                class="w-8 h-8 flex items-center justify-center text-primary bg-primary/10 rounded-full mr-2"
              >
                <i class="ri-calendar-line"></i>
              </div>
              <div>
                <p class="text-xs text-gray-500">Travel Date</p>
                <p class="font-medium">May 15 - May 22, 2025</p>
              </div>
            </div>
            <div class="flex items-center">
              <div
                class="w-8 h-8 flex items-center justify-center text-primary bg-primary/10 rounded-full mr-2"
              >
                <i class="ri-user-line"></i>
              </div>
              <div>
                <p class="text-xs text-gray-500">Travelers</p>
                <p class="font-medium">2 Adults</p>
              </div>
            </div>
          </div>
          <div class="flex gap-3">
            <a
              href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/4410b286-d3a7-473c-8dba-f26e720f14d8"
              data-readdy="true"
              class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-button whitespace-nowrap text-sm hover:bg-gray-50 transition"
            >
              <i class="ri-arrow-left-line mr-1"></i>
              Back to Search
            </a>
            <button
              class="flex items-center justify-center px-4 py-2 bg-primary/10 text-primary rounded-button whitespace-nowrap text-sm hover:bg-primary/20 transition"
            >
              <i class="ri-edit-line mr-1"></i>
              Modify Search
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Quick Filters -->
    <div class="bg-white border-b border-gray-200">
      <div class="container mx-auto px-4 py-3">
        <div class="flex items-center overflow-x-auto custom-scrollbar pb-1">
          <span class="text-gray-700 font-medium mr-3 whitespace-nowrap"
            >Quick Filters:</span
          >
          <div class="flex gap-2">
            <button
              class="px-3 py-1.5 bg-primary text-white rounded-full text-sm whitespace-nowrap"
            >
              Best Sellers
            </button>
            <button
              class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
            >
              Special Deals
            </button>
            <button
              class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
            >
              5-7 Days
            </button>
            <button
              class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
            >
              Adventure
            </button>
            <button
              class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
            >
              Cultural
            </button>
            <button
              class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
            >
              Beach
            </button>
            <button
              class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
            >
              Family Friendly
            </button>
            <button
              class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
            >
              Small Groups
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="w-full lg:w-72 shrink-0">
          <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
            <div class="mb-6">
              <h3 class="font-semibold text-lg mb-4">Filter Results</h3>
              <button class="text-primary text-sm font-medium hover:underline">
                Clear All Filters
              </button>
            </div>
            <!-- Price Range -->
            <div class="border-t border-gray-100 py-5">
              <h4 class="font-medium mb-4">Price Range</h4>
              <div class="mb-4">
                <input
                  type="range"
                  min="0"
                  max="3000"
                  value="1500"
                  class="price-slider"
                  id="priceRange"
                />
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">$0</span>
                <span class="text-sm font-medium"
                  >Up to $<span id="priceValue">1500</span></span
                >
                <span class="text-sm text-gray-600">$3000+</span>
              </div>
            </div>
            <!-- Duration -->
            <div class="border-t border-gray-100 py-5">
              <h4 class="font-medium mb-4">Duration</h4>
              <div class="space-y-3">
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="custom-checkbox" checked />
                  <span class="ml-2 text-sm">1-3 Days (12)</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="custom-checkbox" checked />
                  <span class="ml-2 text-sm">4-7 Days (28)</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="custom-checkbox" />
                  <span class="ml-2 text-sm">8-14 Days (16)</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="custom-checkbox" />
                  <span class="ml-2 text-sm">15+ Days (8)</span>
                </label>
              </div>
            </div>
            <!-- Tour Type -->
            <div class="border-t border-gray-100 py-5">
              <h4 class="font-medium mb-4">Tour Type</h4>
              <div class="space-y-3">
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="custom-checkbox" checked />
                  <span class="ml-2 text-sm">Adventure (23)</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="custom-checkbox" />
                  <span class="ml-2 text-sm">Cultural (19)</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="custom-checkbox" checked />
                  <span class="ml-2 text-sm">Beach & Relaxation (15)</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="custom-checkbox" />
                  <span class="ml-2 text-sm">Wildlife & Nature (12)</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="custom-checkbox" />
                  <span class="ml-2 text-sm">Food & Culinary (8)</span>
                </label>
              </div>
            </div>
            <!-- Rating -->
            <div class="border-t border-gray-100 py-5">
              <h4 class="font-medium mb-4">Rating</h4>
              <div class="space-y-3">
                <label class="flex items-center cursor-pointer">
                  <input type="radio" name="rating" class="custom-radio" />
                  <div class="ml-2 flex items-center">
                    <div class="flex text-yellow-400">
                      <i class="ri-star-fill"></i>
                      <i class="ri-star-fill"></i>
                      <i class="ri-star-fill"></i>
                      <i class="ri-star-fill"></i>
                      <i class="ri-star-fill"></i>
                    </div>
                    <span class="ml-1 text-sm">(18)</span>
                  </div>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input
                    type="radio"
                    name="rating"
                    class="custom-radio"
                    checked
                  />
                  <div class="ml-2 flex items-center">
                    <div class="flex text-yellow-400">
                      <i class="ri-star-fill"></i>
                      <i class="ri-star-fill"></i>
                      <i class="ri-star-fill"></i>
                      <i class="ri-star-fill"></i>
                      <i class="ri-star-line"></i>
                    </div>
                    <span class="ml-1 text-sm">& up (42)</span>
                  </div>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="radio" name="rating" class="custom-radio" />
                  <div class="ml-2 flex items-center">
                    <div class="flex text-yellow-400">
                      <i class="ri-star-fill"></i>
                      <i class="ri-star-fill"></i>
                      <i class="ri-star-fill"></i>
                      <i class="ri-star-line"></i>
                      <i class="ri-star-line"></i>
                    </div>
                    <span class="ml-1 text-sm">& up (64)</span>
                  </div>
                </label>
              </div>
            </div>
            <!-- Group Size -->
            <div class="border-t border-gray-100 py-5">
              <h4 class="font-medium mb-4">Group Size</h4>
              <div class="space-y-3">
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="custom-checkbox" />
                  <span class="ml-2 text-sm">Small (1-8 people) (26)</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="custom-checkbox" checked />
                  <span class="ml-2 text-sm">Medium (9-15 people) (32)</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="custom-checkbox" />
                  <span class="ml-2 text-sm">Large (16+ people) (14)</span>
                </label>
              </div>
            </div>
            <!-- Special Features -->
            <div class="border-t border-gray-100 py-5">
              <h4 class="font-medium mb-4">Special Features</h4>
              <div class="space-y-3">
                <label class="flex items-center justify-between cursor-pointer">
                  <span class="text-sm">Free Cancellation</span>
                  <label class="filter-switch">
                    <input type="checkbox" checked />
                    <span class="filter-slider"></span>
                  </label>
                </label>
                <label class="flex items-center justify-between cursor-pointer">
                  <span class="text-sm">Instant Confirmation</span>
                  <label class="filter-switch">
                    <input type="checkbox" />
                    <span class="filter-slider"></span>
                  </label>
                </label>
                <label class="flex items-center justify-between cursor-pointer">
                  <span class="text-sm">Special Deals</span>
                  <label class="filter-switch">
                    <input type="checkbox" checked />
                    <span class="filter-slider"></span>
                  </label>
                </label>
                <label class="flex items-center justify-between cursor-pointer">
                  <span class="text-sm">Private Tour</span>
                  <label class="filter-switch">
                    <input type="checkbox" />
                    <span class="filter-slider"></span>
                  </label>
                </label>
              </div>
            </div>
          </div>
        </div>
        <!-- Results Content -->
        <div class="flex-1">
          <!-- Sort Controls -->
          <div
            class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-col sm:flex-row justify-between items-center gap-4"
          >
            <div class="text-gray-700">
              <span class="font-medium">42 tours found</span> for Bali,
              Indonesia
            </div>
            <div class="flex items-center">
              <span class="text-gray-600 text-sm mr-2">Sort by:</span>
              <div class="relative">
                <select
                  class="pl-4 pr-8 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm appearance-none bg-white"
                >
                  <option value="recommended">Recommended</option>
                  <option value="price-low">Price: Low to High</option>
                  <option value="price-high">Price: High to Low</option>
                  <option value="rating">Rating</option>
                  <option value="duration">Duration</option>
                </select>
                <div
                  class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none"
                >
                  <i class="ri-arrow-down-s-line text-gray-500"></i>
                </div>
              </div>
            </div>
          </div>
          <!-- Tour Results Grid -->
          <div
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6"
          >
            <!-- Tour Card 1 -->
            <div
              class="tour-card bg-white rounded-lg overflow-hidden shadow-sm transition duration-300"
            >
              <div class="relative h-48 overflow-hidden">
                <img
                  src="#api/search-image?query=beautiful%2520Bali%2520rice%2520terraces%2520with%2520traditional%2520temples%2520in%2520background%252C%2520lush%2520green%2520landscape%252C%2520perfect%2520sunny%2520day%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=21&orientation=landscape"
                  alt="Bali Cultural Adventure"
                  class="w-full h-full object-cover object-top"
                />
                <div
                  class="absolute top-4 left-4 bg-primary text-white text-xs font-medium px-2 py-1 rounded-full"
                >
                  Best Seller
                </div>
                <div
                  class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition"
                >
                  <i class="ri-heart-line"></i>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-center text-gray-600 text-sm mb-1">
                  <i class="ri-map-pin-line mr-1"></i>
                  <span>Bali, Indonesia</span>
                </div>
                <h3 class="font-semibold text-lg mb-2">
                  Bali Cultural Adventure
                </h3>
                <div class="flex items-center mb-3">
                  <div class="flex text-yellow-400 mr-2">
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-half-fill"></i>
                  </div>
                  <span class="text-gray-600 text-sm">4.8 (256 reviews)</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-3">
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >5 Days</span
                  >
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >Max 12 people</span
                  >
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >Cultural</span
                  >
                </div>
                <ul class="text-gray-600 text-sm mb-4 space-y-1">
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Visit to ancient temples & rice terraces</span>
                  </li>
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Traditional dance performance</span>
                  </li>
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Guided village tour & cooking class</span>
                  </li>
                </ul>
                <div class="flex justify-between items-center">
                  <div>
                    <span class="text-gray-500 text-sm line-through">$899</span>
                    <span class="text-primary font-semibold ml-1">$749</span>
                    <span class="text-gray-600 text-xs">/ person</span>
                  </div>
                  <a
                    href="#"
                    class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition"
                    >View Details</a
                  >
                </div>
              </div>
            </div>
            <!-- Tour Card 2 -->
            <div
              class="tour-card bg-white rounded-lg overflow-hidden shadow-sm transition duration-300"
            >
              <div class="relative h-48 overflow-hidden">
                <img
                  src="#api/search-image?query=beautiful%2520Bali%2520beach%2520sunset%2520with%2520traditional%2520boats%252C%2520golden%2520light%2520reflecting%2520on%2520water%252C%2520palm%2520trees%2520silhouettes%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=22&orientation=landscape"
                  alt="Bali Beach Escape"
                  class="w-full h-full object-cover object-top"
                />
                <div
                  class="absolute top-4 left-4 bg-green-500 text-white text-xs font-medium px-2 py-1 rounded-full"
                >
                  20% Off
                </div>
                <div
                  class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition"
                >
                  <i class="ri-heart-line"></i>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-center text-gray-600 text-sm mb-1">
                  <i class="ri-map-pin-line mr-1"></i>
                  <span>Bali, Indonesia</span>
                </div>
                <h3 class="font-semibold text-lg mb-2">Bali Beach Paradise</h3>
                <div class="flex items-center mb-3">
                  <div class="flex text-yellow-400 mr-2">
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-line"></i>
                  </div>
                  <span class="text-gray-600 text-sm">4.6 (182 reviews)</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-3">
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >6 Days</span
                  >
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >Max 10 people</span
                  >
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >Beach</span
                  >
                </div>
                <ul class="text-gray-600 text-sm mb-4 space-y-1">
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Explore Kuta, Seminyak & Nusa Dua beaches</span>
                  </li>
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Sunset dinner cruise experience</span>
                  </li>
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Snorkeling at coral reefs</span>
                  </li>
                </ul>
                <div class="flex justify-between items-center">
                  <div>
                    <span class="text-gray-500 text-sm line-through">$999</span>
                    <span class="text-primary font-semibold ml-1">$799</span>
                    <span class="text-gray-600 text-xs">/ person</span>
                  </div>
                  <a
                    href="#"
                    class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition"
                    >View Details</a
                  >
                </div>
              </div>
            </div>
            <!-- Tour Card 3 -->
            <div
              class="tour-card bg-white rounded-lg overflow-hidden shadow-sm transition duration-300"
            >
              <div class="relative h-48 overflow-hidden">
                <img
                  src="#api/search-image?query=beautiful%2520Bali%2520adventure%2520scene%2520with%2520jungle%2520trekking%252C%2520waterfalls%252C%2520and%2520lush%2520tropical%2520forest%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=23&orientation=landscape"
                  alt="Bali Adventure Trek"
                  class="w-full h-full object-cover object-top"
                />
                <div
                  class="absolute top-4 left-4 bg-blue-500 text-white text-xs font-medium px-2 py-1 rounded-full"
                >
                  New Tour
                </div>
                <div
                  class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition"
                >
                  <i class="ri-heart-line"></i>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-center text-gray-600 text-sm mb-1">
                  <i class="ri-map-pin-line mr-1"></i>
                  <span>Bali, Indonesia</span>
                </div>
                <h3 class="font-semibold text-lg mb-2">Bali Adventure Trek</h3>
                <div class="flex items-center mb-3">
                  <div class="flex text-yellow-400 mr-2">
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                  </div>
                  <span class="text-gray-600 text-sm">4.9 (124 reviews)</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-3">
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >4 Days</span
                  >
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >Max 8 people</span
                  >
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >Adventure</span
                  >
                </div>
                <ul class="text-gray-600 text-sm mb-4 space-y-1">
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Jungle trekking & waterfall exploration</span>
                  </li>
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>White water rafting experience</span>
                  </li>
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Mountain biking through villages</span>
                  </li>
                </ul>
                <div class="flex justify-between items-center">
                  <div>
                    <span class="text-gray-500 text-sm line-through">$849</span>
                    <span class="text-primary font-semibold ml-1">$699</span>
                    <span class="text-gray-600 text-xs">/ person</span>
                  </div>
                  <a
                    href="#"
                    class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition"
                    >View Details</a
                  >
                </div>
              </div>
            </div>
            <!-- Tour Card 4 -->
            <div
              class="tour-card bg-white rounded-lg overflow-hidden shadow-sm transition duration-300"
            >
              <div class="relative h-48 overflow-hidden">
                <img
                  src="#api/search-image?query=beautiful%2520Bali%2520spiritual%2520retreat%2520with%2520yoga%2520sessions%252C%2520meditation%2520spaces%252C%2520and%2520tranquil%2520settings%2520surrounded%2520by%2520nature%252C%2520professional%2520travel%2520photography%2520with%2520serene%2520atmosphere&width=600&height=400&seq=24&orientation=landscape"
                  alt="Bali Spiritual Retreat"
                  class="w-full h-full object-cover object-top"
                />
                <div
                  class="absolute top-4 left-4 bg-purple-500 text-white text-xs font-medium px-2 py-1 rounded-full"
                >
                  Wellness
                </div>
                <div
                  class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition"
                >
                  <i class="ri-heart-line"></i>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-center text-gray-600 text-sm mb-1">
                  <i class="ri-map-pin-line mr-1"></i>
                  <span>Bali, Indonesia</span>
                </div>
                <h3 class="font-semibold text-lg mb-2">
                  Bali Spiritual Retreat
                </h3>
                <div class="flex items-center mb-3">
                  <div class="flex text-yellow-400 mr-2">
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-half-fill"></i>
                  </div>
                  <span class="text-gray-600 text-sm">4.7 (156 reviews)</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-3">
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >7 Days</span
                  >
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >Max 15 people</span
                  >
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >Wellness</span
                  >
                </div>
                <ul class="text-gray-600 text-sm mb-4 space-y-1">
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Daily yoga & meditation sessions</span>
                  </li>
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Healing water temple ceremony</span>
                  </li>
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Balinese healing treatments</span>
                  </li>
                </ul>
                <div class="flex justify-between items-center">
                  <div>
                    <span class="text-gray-500 text-sm line-through"
                      >$1,299</span
                    >
                    <span class="text-primary font-semibold ml-1">$1,099</span>
                    <span class="text-gray-600 text-xs">/ person</span>
                  </div>
                  <a
                    href="#"
                    class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition"
                    >View Details</a
                  >
                </div>
              </div>
            </div>
            <!-- Tour Card 5 -->
            <div
              class="tour-card bg-white rounded-lg overflow-hidden shadow-sm transition duration-300"
            >
              <div class="relative h-48 overflow-hidden">
                <img
                  src="#api/search-image?query=beautiful%2520Bali%2520culinary%2520experience%2520with%2520traditional%2520food%2520markets%252C%2520cooking%2520classes%252C%2520and%2520authentic%2520local%2520dishes%252C%2520professional%2520food%2520travel%2520photography%2520with%2520vibrant%2520colors&width=600&height=400&seq=25&orientation=landscape"
                  alt="Bali Culinary Journey"
                  class="w-full h-full object-cover object-top"
                />
                <div
                  class="absolute top-4 left-4 bg-yellow-500 text-white text-xs font-medium px-2 py-1 rounded-full"
                >
                  Food Tour
                </div>
                <div
                  class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition"
                >
                  <i class="ri-heart-line"></i>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-center text-gray-600 text-sm mb-1">
                  <i class="ri-map-pin-line mr-1"></i>
                  <span>Bali, Indonesia</span>
                </div>
                <h3 class="font-semibold text-lg mb-2">
                  Bali Culinary Journey
                </h3>
                <div class="flex items-center mb-3">
                  <div class="flex text-yellow-400 mr-2">
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-line"></i>
                  </div>
                  <span class="text-gray-600 text-sm">4.5 (98 reviews)</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-3">
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >3 Days</span
                  >
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >Max 10 people</span
                  >
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >Culinary</span
                  >
                </div>
                <ul class="text-gray-600 text-sm mb-4 space-y-1">
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Local market tours & ingredient shopping</span>
                  </li>
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Hands-on cooking classes with local chefs</span>
                  </li>
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Street food exploration tours</span>
                  </li>
                </ul>
                <div class="flex justify-between items-center">
                  <div>
                    <span class="text-gray-500 text-sm line-through">$649</span>
                    <span class="text-primary font-semibold ml-1">$549</span>
                    <span class="text-gray-600 text-xs">/ person</span>
                  </div>
                  <a
                    href="#"
                    class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition"
                    >View Details</a
                  >
                </div>
              </div>
            </div>
            <!-- Tour Card 6 -->
            <div
              class="tour-card bg-white rounded-lg overflow-hidden shadow-sm transition duration-300"
            >
              <div class="relative h-48 overflow-hidden">
                <img
                  src="#api/search-image?query=beautiful%2520Bali%2520luxury%2520villa%2520with%2520private%2520pool%252C%2520stunning%2520ocean%2520views%252C%2520and%2520elegant%2520accommodations%252C%2520professional%2520travel%2520photography%2520with%2520luxurious%2520atmosphere&width=600&height=400&seq=26&orientation=landscape"
                  alt="Bali Luxury Escape"
                  class="w-full h-full object-cover object-top"
                />
                <div
                  class="absolute top-4 left-4 bg-indigo-500 text-white text-xs font-medium px-2 py-1 rounded-full"
                >
                  Luxury
                </div>
                <div
                  class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition"
                >
                  <i class="ri-heart-line"></i>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-center text-gray-600 text-sm mb-1">
                  <i class="ri-map-pin-line mr-1"></i>
                  <span>Bali, Indonesia</span>
                </div>
                <h3 class="font-semibold text-lg mb-2">Bali Luxury Escape</h3>
                <div class="flex items-center mb-3">
                  <div class="flex text-yellow-400 mr-2">
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                  </div>
                  <span class="text-gray-600 text-sm">5.0 (87 reviews)</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-3">
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >8 Days</span
                  >
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >Private Tour</span
                  >
                  <span
                    class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                    >Luxury</span
                  >
                </div>
                <ul class="text-gray-600 text-sm mb-4 space-y-1">
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>5-star villa accommodations with private pool</span>
                  </li>
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Private chauffeur & personalized itinerary</span>
                  </li>
                  <li class="flex items-start">
                    <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                    <span>Exclusive spa treatments & fine dining</span>
                  </li>
                </ul>
                <div class="flex justify-between items-center">
                  <div>
                    <span class="text-gray-500 text-sm line-through"
                      >$2,499</span
                    >
                    <span class="text-primary font-semibold ml-1">$1,999</span>
                    <span class="text-gray-600 text-xs">/ person</span>
                  </div>
                  <a
                    href="#"
                    class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition"
                    >View Details</a
                  >
                </div>
              </div>
            </div>
          </div>
          <!-- Pagination -->
          <div class="mt-10 flex justify-between items-center">
            <div class="text-gray-600 text-sm">
              Showing <span class="font-medium">1-6</span> of
              <span class="font-medium">42</span> results
            </div>
            <div class="flex items-center space-x-2">
              <button
                class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 text-gray-500 hover:border-primary hover:text-primary transition"
              >
                <i class="ri-arrow-left-s-line"></i>
              </button>
              <button
                class="w-10 h-10 flex items-center justify-center rounded bg-primary text-white"
              >
                1
              </button>
              <button
                class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 hover:border-primary hover:text-primary transition"
              >
                2
              </button>
              <button
                class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 hover:border-primary hover:text-primary transition"
              >
                3
              </button>
              <button
                class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 hover:border-primary hover:text-primary transition"
              >
                4
              </button>
              <button
                class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 text-gray-500 hover:border-primary hover:text-primary transition"
              >
                <i class="ri-arrow-right-s-line"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Newsletter -->
    <section class="py-12 bg-primary/5">
      <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
          <h2 class="text-2xl font-bold mb-3">Get Travel Inspiration</h2>
          <p class="text-gray-600 mb-6">
            Subscribe to our newsletter for exclusive deals and travel tips
          </p>
          <div class="flex flex-col sm:flex-row gap-4 max-w-xl mx-auto">
            <input
              type="email"
              placeholder="Your email address"
              class="flex-grow px-4 py-3 rounded-button border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/20"
            />
            <button
              class="bg-primary text-white px-6 py-3 rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition"
            >
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
              <a
                href="#"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-primary transition"
              >
                <i class="ri-facebook-fill"></i>
              </a>
              <a
                href="#"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-primary transition"
              >
                <i class="ri-twitter-x-fill"></i>
              </a>
              <a
                href="#"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-primary transition"
              >
                <i class="ri-instagram-fill"></i>
              </a>
              <a
                href="#"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-primary transition"
              >
                <i class="ri-youtube-fill"></i>
              </a>
            </div>
          </div>
          <div>
            <h3 class="text-xl font-bold mb-4">Popular Destinations</h3>
            <ul class="space-y-2">
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >Bali, Indonesia</a
                >
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >Santorini, Greece</a
                >
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >Maldives</a
                >
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >Peru</a
                >
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >Japan</a
                >
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >Paris, France</a
                >
              </li>
            </ul>
          </div>
          <div>
            <h3 class="text-xl font-bold mb-4">Quick Links</h3>
            <ul class="space-y-2">
              <li>
                <a
                  href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/4410b286-d3a7-473c-8dba-f26e720f14d8"
                  data-readdy="true"
                  class="text-gray-400 hover:text-white transition"
                  >Home</a
                >
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >About Us</a
                >
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >Tour Packages</a
                >
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >Destinations</a
                >
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >Contact Us</a
                >
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >FAQs</a
                >
              </li>
            </ul>
          </div>
          <div>
            <h3 class="text-xl font-bold mb-4">Contact Us</h3>
            <ul class="space-y-3">
              <li class="flex items-start">
                <div
                  class="w-5 h-5 flex items-center justify-center mt-1 mr-3 text-gray-400"
                >
                  <i class="ri-map-pin-line"></i>
                </div>
                <span class="text-gray-400"
                  >123 Travel Street, Adventure City, AC 10001, India</span
                >
              </li>
              <li class="flex items-center">
                <div
                  class="w-5 h-5 flex items-center justify-center mr-3 text-gray-400"
                >
                  <i class="ri-phone-line"></i>
                </div>
                <span class="text-gray-400">+91 (123) 456-7890</span>
              </li>
              <li class="flex items-center">
                <div
                  class="w-5 h-5 flex items-center justify-center mr-3 text-gray-400"
                >
                  <i class="ri-mail-line"></i>
                </div>
                <span class="text-gray-400">info@journeyfinders.in</span>
              </li>
              <li class="flex items-center">
                <div
                  class="w-5 h-5 flex items-center justify-center mr-3 text-gray-400"
                >
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
              <div
                class="w-12 h-8 flex items-center justify-center bg-gray-800 rounded"
              >
                <i class="ri-visa-fill ri-lg text-white"></i>
              </div>
              <div
                class="w-12 h-8 flex items-center justify-center bg-gray-800 rounded"
              >
                <i class="ri-mastercard-fill ri-lg text-white"></i>
              </div>
              <div
                class="w-12 h-8 flex items-center justify-center bg-gray-800 rounded"
              >
                <i class="ri-paypal-fill ri-lg text-white"></i>
              </div>
              <div
                class="w-12 h-8 flex items-center justify-center bg-gray-800 rounded"
              >
                <i class="ri-amazon-pay-fill ri-lg text-white"></i>
              </div>
              <div
                class="w-12 h-8 flex items-center justify-center bg-gray-800 rounded"
              >
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
        class="w-12 h-12 flex items-center justify-center bg-green-500 text-white rounded-full shadow-lg hover:bg-green-600 transition"
      >
        <i class="ri-whatsapp-line ri-lg"></i>
      </button>
      <button
        id="back-to-top"
        class="w-12 h-12 flex items-center justify-center bg-gray-800 text-white rounded-full shadow-lg hover:bg-gray-700 transition"
      >
        <i class="ri-arrow-up-line ri-lg"></i>
      </button>
    </div>
    <script>
      // Back to top button
      document.getElementById("back-to-top").addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
      });
      // Price range slider
      document.addEventListener("DOMContentLoaded", function () {
        const priceRange = document.getElementById("priceRange");
        const priceValue = document.getElementById("priceValue");
        if (priceRange && priceValue) {
          priceRange.addEventListener("input", function () {
            priceValue.textContent = this.value;
          });
        }
      });
      // Mobile filter toggle
      document.addEventListener("DOMContentLoaded", function () {
        const filterToggles = document.querySelectorAll(".filter-switch input");
        filterToggles.forEach((toggle) => {
          toggle.addEventListener("change", function () {
            // Toggle functionality would go here
          });
        });
      });
    </script>
  </body>
</html>
