<?php

require_once HOMEPAGE_FUNCTIONS;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>HairBud Forum - Home</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#4F46E5',
                    secondary: '#10B981'
                },
                borderRadius: {
                    'none': '0px',
                    'sm': '4px',
                    DEFAULT: '8px',
                    'md': '12px',
                    'lg': '16px',
                    'xl': '20px',
                    '2xl': '24px',
                    '3xl': '32px',
                    'full': '9999px',
                    'button': '8px'
                }
            }
        }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
    <style>
    :where([class^="ri-"])::before {
        content: "\f3c2";
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f9fafb;
    }

    input[type="search"]::-webkit-search-decoration,
    input[type="search"]::-webkit-search-cancel-button,
    input[type="search"]::-webkit-search-results-button,
    input[type="search"]::-webkit-search-results-decoration {
        display: none;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .custom-switch {
        position: relative;
        display: inline-block;
        width: 3rem;
        height: 1.5rem;
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
        transition: .4s;
        border-radius: 34px;
    }

    .switch-slider:before {
        position: absolute;
        content: "";
        height: 1.1rem;
        width: 1.1rem;
        left: 0.2rem;
        bottom: 0.2rem;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.switch-slider {
        background-color: #4F46E5;
    }

    input:checked+.switch-slider:before {
        transform: translateX(1.5rem);
    }

    .category-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .discussion-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .specialist-card:hover {
        transform: translateY(-4px);
    }

    .success-story-card:hover {
        transform: scale(1.02);
    }

    .activity-item:hover {
        background-color: #f3f4f6;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        min-width: 200px;
        z-index: 50;
        margin-top: 4px;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        display: block;
        padding: 8px 16px;
        text-decoration: none;
        color: #374151;
        font-size: 14px;
        transition: background-color 0.2s;
    }

    .dropdown-item:hover {
        background-color: #f3f4f6;
    }

    .dropdown-divider {
        border-top: 1px solid #e5e7eb;
        margin: 4px 0;
    }
    </style>
</head>

<body>
    <?php

    require_once 'head.php';

    ?>

    <main>
        <!-- Hero Banner -->
        <section class="relative bg-gradient-to-r from-indigo-600 to-primary overflow-hidden">
            <div class="absolute inset-0"
                style="background-image: url('https://readdy.ai/api/search-image?query=modern%2520abstract%2520background%2520with%2520soft%2520blue%2520and%2520purple%2520gradient%2520waves%252C%2520subtle%2520pattern%2520of%2520hair%2520strands%2520in%2520light%2520colors%252C%2520professional%2520clean%2520design%252C%2520soft%2520lighting%252C%2520minimal%2520style%252C%2520perfect%2520for%2520hair%2520forum%2520website%2520banner&width=1600&height=600&seq=1&orientation=landscape'); background-size: cover; background-position: center; opacity: 0.2;">
            </div>
            <div class="container mx-auto px-4 py-16 relative z-10">
                <div class="max-w-3xl">
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Welcome to HairBud Forum</h1>
                    <p class="text-xl text-indigo-100 mb-8">Join our community of hair enthusiasts, specialists, and
                        individuals on their hair restoration journey. Share experiences, get advice, and discover
                        solutions.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="/start-a-discussion/" data-readdy="true"
                            class="px-6 py-3 bg-white text-primary font-medium rounded-md hover:bg-gray-100 shadow-lg !rounded-button whitespace-nowrap">
                            Start a Discussion
                        </a>
                        <a href="/find-a-specialist/"
                            class="px-6 py-3 bg-transparent border-2 border-white text-white font-medium rounded-md hover:bg-white/10 !rounded-button whitespace-nowrap">
                            Find a Specialist
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Quick Stats Section -->
        <section class="container mx-auto px-4 py-8 -mt-10 relative z-20">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                        <i class="ri-user-3-line ri-lg text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">12,458</h3>
                        <p class="text-gray-600">Active Members</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                        <i class="ri-discuss-line ri-lg text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">8,721</h3>
                        <p class="text-gray-600">Total Discussions</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                        <i class="ri-medicine-bottle-line ri-lg text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">342</h3>
                        <p class="text-gray-600">Verified Specialists</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                    <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center mr-4">
                        <i class="ri-award-line ri-lg text-yellow-600"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">1,935</h3>
                        <p class="text-gray-600">Success Stories</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Featured Discussions -->
        <section class="container mx-auto px-4 py-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Trending Discussions</h2>
                <a href="/trending-discussions/" data-readdy="true"
                    class="text-primary font-medium flex items-center hover:underline">
                    View All <i class="ri-arrow-right-line ri-lg ml-1"></i>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Discussion 1 -->
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 transition-all duration-300 discussion-card">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                            <span class="text-indigo-600 font-medium">EM</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Emily Morgan</p>
                            <p class="text-xs text-gray-500">Posted 2 hours ago</p>
                        </div>
                        <div class="ml-auto">
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Hair
                                Loss</span>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">My experience with finasteride after 6 months
                    </h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">I wanted to share my journey with finasteride so
                        far.
                        The first 2 months were challenging with some side effects, but after adjusting the dosage with
                        my
                        doctor's guidance, I'm seeing real improvements in hair density...</p>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <div class="flex items-center space-x-4">
                            <span class="flex items-center"><i class="ri-eye-line mr-1"></i> 1.2k</span>
                            <span class="flex items-center"><i class="ri-message-2-line mr-1"></i> 48</span>
                        </div>
                        <div class="flex items-center">
                            <i class="ri-heart-line mr-1"></i> 86
                        </div>
                    </div>
                </div>
                <!-- Discussion 2 -->
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 transition-all duration-300 discussion-card">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <span class="text-green-600 font-medium">DR</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Dr. Robert Chen</p>
                            <p class="text-xs text-gray-500">Posted yesterday</p>
                        </div>
                        <div class="ml-auto">
                            <span
                                class="px-2.5 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Transplant
                                Procedures</span>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">FUE vs FUT: Which is right for you?</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">As a hair transplant specialist with 15+ years of
                        experience, I'm often asked about the differences between FUE and FUT procedures. Here's a
                        comprehensive breakdown of each method, recovery times, and who makes an ideal candidate...</p>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <div class="flex items-center space-x-4">
                            <span class="flex items-center"><i class="ri-eye-line mr-1"></i> 3.4k</span>
                            <span class="flex items-center"><i class="ri-message-2-line mr-1"></i> 127</span>
                        </div>
                        <div class="flex items-center">
                            <i class="ri-heart-line mr-1"></i> 215
                        </div>
                    </div>
                </div>
                <!-- Discussion 3 -->
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 transition-all duration-300 discussion-card">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <span class="text-purple-600 font-medium">SJ</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Sarah Johnson</p>
                            <p class="text-xs text-gray-500">Posted 3 days ago</p>
                        </div>
                        <div class="ml-auto">
                            <span
                                class="px-2.5 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Treatments
                                & Products</span>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Review: 5 best shampoos for thinning hair in
                        2025
                    </h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">After trying countless products for my thinning
                        hair
                        over the past year, I've finally found 5 shampoos that actually deliver results. I'll share my
                        detailed experience with each, including ingredients to look for and which hair types benefit
                        most...</p>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <div class="flex items-center space-x-4">
                            <span class="flex items-center"><i class="ri-eye-line mr-1"></i> 2.7k</span>
                            <span class="flex items-center"><i class="ri-message-2-line mr-1"></i> 93</span>
                        </div>
                        <div class="flex items-center">
                            <i class="ri-heart-line mr-1"></i> 178
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Category Showcase -->
        <section class="bg-gray-50 py-12">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Explore Categories</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                    <?php

                    $featureCategory = getCategoryWithTotalPost($connection, 'discussion', 6);

                    foreach ($featureCategory as $categoryData) {
                        extract($categoryData);
                        echo "<a href='{$link_url}'
                        class='bg-white rounded-lg shadow-sm p-6 text-center transition-all duration-300 category-card'>
                        <div class='w-14 h-14 mx-auto rounded-full bg-indigo-100 flex items-center justify-center mb-4'>
                            " . (($category_image != '') ? "<img src='{$category_image}' alt='{$category_title}' title='{$category_title}'>" : "<i class='ri-medicine-bottle-line ri-xl text-purple-600'></i>") . "
                        </div>
                        <h3 class='font-medium text-gray-900 mb-1'>{$category_title}</h3>
                        <p class='text-sm text-gray-500'>{$totalCount} posts</p>
                    </a>";
                    }

                    ?>
                </div>
            </div>
        </section>
        <!-- Community Highlights -->
        <section class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Featured Specialists -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Specialists</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Specialist 1 -->
                        <div
                            class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 transition-all duration-300 specialist-card">
                            <div class="flex items-center mb-4">
                                <img src="https://readdy.ai/api/search-image?query=professional%2520headshot%2520of%2520a%2520middle-aged%2520male%2520doctor%2520with%2520short%2520dark%2520hair%2520and%2520glasses%252C%2520wearing%2520a%2520white%2520coat%252C%2520friendly%2520smile%252C%2520clean%2520medical%2520office%2520background%252C%2520professional%2520lighting%252C%2520high%2520quality%2520portrait&width=80&height=80&seq=1&orientation=squarish"
                                    alt="Dr. Michael Thompson"
                                    class="w-16 h-16 rounded-full object-cover object-top mr-4">
                                <div>
                                    <div class="flex items-center">
                                        <h3 class="font-medium text-gray-900">Dr. Michael Thompson</h3>
                                        <div
                                            class="ml-2 w-5 h-5 flex items-center justify-center bg-blue-100 rounded-full">
                                            <i class="ri-verified-badge-fill text-blue-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500">Hair Transplant Surgeon</p>
                                    <div class="flex mt-1">
                                        <div class="flex">
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                        </div>
                                        <span class="text-xs text-gray-500 ml-1">(127)</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Specializing in FUE transplants with 15+ years of
                                experience. Board certified with over 2,000 successful procedures.</p>
                            <button
                                class="w-full py-2 bg-primary text-white rounded-md hover:bg-primary/90 !rounded-button whitespace-nowrap">
                                View Profile
                            </button>
                        </div>
                        <!-- Specialist 2 -->
                        <div
                            class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 transition-all duration-300 specialist-card">
                            <div class="flex items-center mb-4">
                                <img src="https://readdy.ai/api/search-image?query=professional%2520headshot%2520of%2520a%2520female%2520dermatologist%2520with%2520shoulder-length%2520blonde%2520hair%252C%2520wearing%2520a%2520white%2520coat%252C%2520warm%2520smile%252C%2520clean%2520medical%2520office%2520background%252C%2520professional%2520lighting%252C%2520high%2520quality%2520portrait&width=80&height=80&seq=2&orientation=squarish"
                                    alt="Dr. Jennifer Lee" class="w-16 h-16 rounded-full object-cover object-top mr-4">
                                <div>
                                    <div class="flex items-center">
                                        <h3 class="font-medium text-gray-900">Dr. Jennifer Lee</h3>
                                        <div
                                            class="ml-2 w-5 h-5 flex items-center justify-center bg-blue-100 rounded-full">
                                            <i class="ri-verified-badge-fill text-blue-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500">Dermatologist</p>
                                    <div class="flex mt-1">
                                        <div class="flex">
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-half-fill text-yellow-400"></i>
                                        </div>
                                        <span class="text-xs text-gray-500 ml-1">(93)</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Harvard-trained dermatologist specializing in
                                non-surgical
                                hair restoration and scalp health treatments.</p>
                            <button
                                class="w-full py-2 bg-primary text-white rounded-md hover:bg-primary/90 !rounded-button whitespace-nowrap">
                                View Profile
                            </button>
                        </div>
                        <!-- Specialist 3 -->
                        <div
                            class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 transition-all duration-300 specialist-card">
                            <div class="flex items-center mb-4">
                                <img src="https://readdy.ai/api/search-image?query=professional%2520headshot%2520of%2520a%2520middle-aged%2520male%2520doctor%2520with%2520salt%2520and%2520pepper%2520hair%252C%2520wearing%2520a%2520white%2520coat%252C%2520confident%2520expression%252C%2520clean%2520medical%2520office%2520background%252C%2520professional%2520lighting%252C%2520high%2520quality%2520portrait&width=80&height=80&seq=3&orientation=squarish"
                                    alt="Dr. David Wilson" class="w-16 h-16 rounded-full object-cover object-top mr-4">
                                <div>
                                    <div class="flex items-center">
                                        <h3 class="font-medium text-gray-900">Dr. David Wilson</h3>
                                        <div
                                            class="ml-2 w-5 h-5 flex items-center justify-center bg-blue-100 rounded-full">
                                            <i class="ri-verified-badge-fill text-blue-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500">Trichologist</p>
                                    <div class="flex mt-1">
                                        <div class="flex">
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-line text-yellow-400"></i>
                                        </div>
                                        <span class="text-xs text-gray-500 ml-1">(78)</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Certified trichologist with expertise in diagnosing
                                and
                                treating various hair and scalp conditions.</p>
                            <button
                                class="w-full py-2 bg-primary text-white rounded-md hover:bg-primary/90 !rounded-button whitespace-nowrap">
                                View Profile
                            </button>
                        </div>
                        <!-- Specialist 4 -->
                        <div
                            class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 transition-all duration-300 specialist-card">
                            <div class="flex items-center mb-4">
                                <img src="https://readdy.ai/api/search-image?query=professional%2520headshot%2520of%2520a%2520female%2520doctor%2520with%2520dark%2520hair%2520in%2520a%2520bun%252C%2520wearing%2520a%2520white%2520coat%252C%2520friendly%2520smile%252C%2520clean%2520medical%2520office%2520background%252C%2520professional%2520lighting%252C%2520high%2520quality%2520portrait&width=80&height=80&seq=4&orientation=squarish"
                                    alt="Dr. Sophia Rodriguez"
                                    class="w-16 h-16 rounded-full object-cover object-top mr-4">
                                <div>
                                    <div class="flex items-center">
                                        <h3 class="font-medium text-gray-900">Dr. Sophia Rodriguez</h3>
                                        <div
                                            class="ml-2 w-5 h-5 flex items-center justify-center bg-blue-100 rounded-full">
                                            <i class="ri-verified-badge-fill text-blue-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500">Hair Restoration Specialist</p>
                                    <div class="flex mt-1">
                                        <div class="flex">
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-fill text-yellow-400"></i>
                                            <i class="ri-star-half-fill text-yellow-400"></i>
                                        </div>
                                        <span class="text-xs text-gray-500 ml-1">(112)</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Pioneering PRP and stem cell therapies for hair
                                restoration with exceptional patient outcomes.</p>
                            <button
                                class="w-full py-2 bg-primary text-white rounded-md hover:bg-primary/90 !rounded-button whitespace-nowrap">
                                View Profile
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Success Stories -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Recent Success Stories</h2>
                    <div class="space-y-6">
                        <!-- Success Story 1 -->
                        <div
                            class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 success-story-card">
                            <div class="grid grid-cols-2">
                                <div class="p-5">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                            <span class="text-green-600 font-medium">AK</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Andrew Kim</p>
                                            <p class="text-xs text-gray-500">6 months post-procedure</p>
                                        </div>
                                    </div>
                                    <h3 class="font-medium text-gray-900 mb-2">FUE Transplant Journey</h3>
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-3">After struggling with hair loss
                                        for
                                        years, I finally took the plunge with an FUE procedure. The results have
                                        exceeded my
                                        expectations and completely restored my confidence.</p>
                                    <a href="https://readdy.ai/home/0ed8e6fd-1b57-4961-9c40-f04471f53645/c3c7bf13-a36c-4746-bc6f-2aa240169d2f"
                                        data-readdy="true" class="text-primary text-sm font-medium hover:underline">Read
                                        Full Story</a>
                                </div>
                                <div class="bg-gray-100 flex">
                                    <div class="w-1/2 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500 text-xs">Before</span>
                                    </div>
                                    <div class="w-1/2 bg-gray-50 flex items-center justify-center">
                                        <span class="text-gray-500 text-xs">After</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Success Story 2 -->
                        <div
                            class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 success-story-card">
                            <div class="grid grid-cols-2">
                                <div class="p-5">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                            <span class="text-purple-600 font-medium">LT</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Lisa Thompson</p>
                                            <p class="text-xs text-gray-500">1 year of treatment</p>
                                        </div>
                                    </div>
                                    <h3 class="font-medium text-gray-900 mb-2">My Minoxidil Success</h3>
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-3">I was skeptical about minoxidil
                                        at
                                        first, but after consistent use for a year, the difference is remarkable. My
                                        hair is
                                        thicker and I've regained coverage in thinning areas.</p>
                                    <a href="https://readdy.ai/home/0ed8e6fd-1b57-4961-9c40-f04471f53645/c3c7bf13-a36c-4746-bc6f-2aa240169d2f"
                                        data-readdy="true" class="text-primary text-sm font-medium hover:underline">Read
                                        Full Story</a>
                                </div>
                                <div class="bg-gray-100 flex">
                                    <div class="w-1/2 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500 text-xs">Before</span>
                                    </div>
                                    <div class="w-1/2 bg-gray-50 flex items-center justify-center">
                                        <span class="text-gray-500 text-xs">After</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Success Story 3 -->
                        <div
                            class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 success-story-card">
                            <div class="grid grid-cols-2">
                                <div class="p-5">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <span class="text-blue-600 font-medium">RJ</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Robert Johnson</p>
                                            <p class="text-xs text-gray-500">9 months post-treatment</p>
                                        </div>
                                    </div>
                                    <h3 class="font-medium text-gray-900 mb-2">PRP Therapy Results</h3>
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-3">After 4 sessions of PRP therapy,
                                        I've
                                        seen significant improvement in hair density. The procedure was quick with
                                        minimal
                                        discomfort, and the results speak for themselves.</p>
                                    <a href="https://readdy.ai/home/0ed8e6fd-1b57-4961-9c40-f04471f53645/c3c7bf13-a36c-4746-bc6f-2aa240169d2f"
                                        data-readdy="true" class="text-primary text-sm font-medium hover:underline">Read
                                        Full Story</a>
                                </div>
                                <div class="bg-gray-100 flex">
                                    <div class="w-1/2 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500 text-xs">Before</span>
                                    </div>
                                    <div class="w-1/2 bg-gray-50 flex items-center justify-center">
                                        <span class="text-gray-500 text-xs">After</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Community Activity -->
        <section class="bg-gray-50 py-12">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Recent Activity</h2>
                    <div class="flex space-x-2">
                        <button
                            class="px-3 py-1 bg-primary text-white rounded-full text-sm !rounded-button whitespace-nowrap">All</button>
                        <button
                            class="px-3 py-1 bg-white text-gray-700 rounded-full text-sm !rounded-button whitespace-nowrap">Discussions</button>
                        <button
                            class="px-3 py-1 bg-white text-gray-700 rounded-full text-sm !rounded-button whitespace-nowrap">Comments</button>
                        <button
                            class="px-3 py-1 bg-white text-gray-700 rounded-full text-sm !rounded-button whitespace-nowrap">Questions</button>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Activity Item 1 -->
                    <div class="p-4 border-b border-gray-100 transition-all duration-200 activity-item">
                        <div class="flex">
                            <div
                                class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-orange-600 font-medium">JW</span>
                            </div>
                            <div>
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-900">James Wilson</p>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <p class="text-gray-500 text-sm">Started a new discussion</p>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <p class="text-gray-400 text-sm">10 minutes ago</p>
                                </div>
                                <h4 class="font-medium text-gray-800 mt-1">Has anyone tried the new Kerastase Densifique
                                    line?</h4>
                                <p class="text-sm text-gray-600 mt-1">I've been seeing a lot of ads for this new line
                                    claiming to increase hair density. Wondering if anyone here has tried it and can
                                    share
                                    their experience...</p>
                                <div class="flex mt-2">
                                    <span
                                        class="px-2.5 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Treatments
                                        & Products</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Activity Item 2 -->
                    <div class="p-4 border-b border-gray-100 transition-all duration-200 activity-item">
                        <div class="flex">
                            <div
                                class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-green-600 font-medium">DR</span>
                            </div>
                            <div>
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-900">Dr. Robert Chen</p>
                                    <div class="ml-1 w-4 h-4 flex items-center justify-center bg-blue-100 rounded-full">
                                        <i class="ri-verified-badge-fill text-blue-600 text-xs"></i>
                                    </div>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <p class="text-gray-500 text-sm">Replied to a discussion</p>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <p class="text-gray-400 text-sm">32 minutes ago</p>
                                </div>
                                <h4 class="font-medium text-gray-800 mt-1">Re: Is 25 too young for a hair transplant?
                                </h4>
                                <p class="text-sm text-gray-600 mt-1">While there's no strict age minimum for hair
                                    transplants, at 25 it's important to consider that your hair loss pattern may still
                                    be
                                    evolving. I typically recommend stabilizing loss with medications first...</p>
                                <div class="flex mt-2">
                                    <span
                                        class="px-2.5 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Transplant
                                        Procedures</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Activity Item 3 -->
                    <div class="p-4 border-b border-gray-100 transition-all duration-200 activity-item">
                        <div class="flex">
                            <div
                                class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-indigo-600 font-medium">MH</span>
                            </div>
                            <div>
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-900">Michael Harris</p>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <p class="text-gray-500 text-sm">Posted a success story</p>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <p class="text-gray-400 text-sm">1 hour ago</p>
                                </div>
                                <h4 class="font-medium text-gray-800 mt-1">My 2-year finasteride + minoxidil journey
                                    (with
                                    photos)</h4>
                                <p class="text-sm text-gray-600 mt-1">Today marks 2 years since I started the "big 3"
                                    treatment (finasteride, minoxidil, ketoconazole shampoo). I wanted to share my
                                    before
                                    and after photos to give hope to others...</p>
                                <div class="flex mt-2">
                                    <span
                                        class="px-2.5 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Success
                                        Stories</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Activity Item 4 -->
                    <div class="p-4 border-b border-gray-100 transition-all duration-200 activity-item">
                        <div class="flex">
                            <div
                                class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-red-600 font-medium">KL</span>
                            </div>
                            <div>
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-900">Katherine Lee</p>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <p class="text-gray-500 text-sm">Asked a question</p>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <p class="text-gray-400 text-sm">3 hours ago</p>
                                </div>
                                <h4 class="font-medium text-gray-800 mt-1">Female pattern hair loss - what treatments
                                    actually work?</h4>
                                <p class="text-sm text-gray-600 mt-1">I'm a 34-year-old woman experiencing noticeable
                                    thinning at my part line. I've tried several OTC products with no results. What
                                    medical
                                    treatments have proven effective for women?</p>
                                <div class="flex mt-2">
                                    <span
                                        class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Hair
                                        Loss</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Activity Item 5 -->
                    <div class="p-4 transition-all duration-200 activity-item">
                        <div class="flex">
                            <div
                                class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-purple-600 font-medium">TD</span>
                            </div>
                            <div>
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-900">Thomas Davis</p>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <p class="text-gray-500 text-sm">Started a new discussion</p>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <p class="text-gray-400 text-sm">5 hours ago</p>
                                </div>
                                <h4 class="font-medium text-gray-800 mt-1">Financing options for hair transplant in the
                                    US
                                </h4>
                                <p class="text-sm text-gray-600 mt-1">I've been saved for a hair transplant but still
                                    need
                                    financing options. Has anyone used medical loans, payment plans, or other financing
                                    methods they'd recommend?</p>
                                <div class="flex mt-2">
                                    <span
                                        class="px-2.5 py-1 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-full">Cost
                                        & Financing</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-6">
                    <button
                        class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 !rounded-button whitespace-nowrap">
                        Load More <i class="ri-arrow-down-s-line ml-1"></i>
                    </button>
                </div>
            </div>
        </section>
        <!-- Join Banner -->
        <section class="bg-gradient-to-r from-primary to-indigo-700 py-16">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold text-white mb-4">Join Our Growing Community</h2>
                <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">Connect with specialists and others on similar
                    journeys. Share experiences, get advice, and discover the latest hair restoration solutions.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="/start-a-discussion/" data-readdy="true"
                        class="px-6 py-3 bg-white text-primary font-medium rounded-md hover:bg-gray-100 shadow-lg !rounded-button whitespace-nowrap">
                        Start a Discussion
                    </a>
                    <button
                        class="px-6 py-3 bg-transparent border-2 border-white text-white font-medium rounded-md hover:bg-white/10 !rounded-button whitespace-nowrap">
                        Create Account
                    </button>
                </div>
            </div>
        </section>
    </main>

    <?php require_once 'footer.php'; ?>
    <script id="profileDropdownScript">
    document.addEventListener('DOMContentLoaded', function() {
        const profileButton = document.getElementById('profileButton');
        const profileDropdown = document.getElementById('profileDropdown');
        profileButton.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
        });
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    </script>
</body>

</html>