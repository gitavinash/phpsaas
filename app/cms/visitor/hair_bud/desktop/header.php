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
    </style>
</head>

<body>
    <!-- Header Section -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center">
                <h1 class="text-2xl font-['Pacifico'] text-primary">logo</h1>
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
                <button
                    class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200">
                    <i class="ri-notification-3-line ri-lg"></i>
                </button>
                <div class="relative">
                    <button
                        class="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-full !rounded-button whitespace-nowrap">
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                            <span class="text-white font-medium text-sm">JD</span>
                        </div>
                        <span class="font-medium text-sm">John Doe</span>
                        <i class="ri-arrow-down-s-line ri-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>