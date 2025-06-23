<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php require_once DEFAULT_META ?>
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
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-7JZRGW9HSZ"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-7JZRGW9HSZ');
    </script>
    <script type="text/javascript">
    (function(c, l, a, r, i, t, y) {
        c[a] = c[a] || function() {
            (c[a].q = c[a].q || []).push(arguments)
        };
        t = l.createElement(r);
        t.async = 1;
        t.src = "https://www.clarity.ms/tag/" + i;
        y = l.getElementsByTagName(r)[0];
        y.parentNode.insertBefore(t, y);
    })(window, document, "clarity", "script", "rysk59v4vz");
    </script>
</head>

<body>
    <!-- Header Section -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" title="<?php echo SITE_NAME ?>">
                    <h1 class="text-2xl font-['Pacifico'] text-primary">HairBud</h1>
                </a>
            </div>
            <!-- Search Bar -->
            <div class="relative w-1/3">
                <div class="absolute inset-y-0 left-0 w-10 h-10 flex items-center justify-center text-gray-500">
                    <i class="ri-search-line ri-lg"></i>
                </div>
                <input type="search" placeholder="Search discussions, doctors, topics..."
                    class="w-full h-10 pl-10 pr-4 border border-gray-200 rounded text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                <div
                    class="absolute inset-y-0 right-0 w-10 h-10 flex items-center justify-center text-gray-500 cursor-pointer">
                    <i class="ri-filter-3-line ri-lg"></i>
                </div>
            </div>
            <!-- User Actions -->
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button
                        class="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-full !rounded-button whitespace-nowrap">
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                            <span class="text-white font-medium text-sm">JD</span>
                        </div>
                        <span class="font-medium text-sm">Avinash</span>
                        <i class="ri-arrow-down-s-line ri-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>