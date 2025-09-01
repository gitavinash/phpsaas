<?php

$appDashboardDirectory = ADMIN_PATH . 'dashboard/app/';
$postType = rtrim(ltrim(ADMIN_BASE_URL, '/'), '/');
$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';
$siteAccessMeta = '';
if (isset($_COOKIE['user_meta'])) {
    if ($_COOKIE['user_meta'] != '') {
        $siteAccessMeta = (json_decode(base64_decode($_COOKIE['user_meta']), true));
    }
}

$loggedInUserMeta = (isset($_COOKIE['user_meta']) ? json_decode(base64_decode($_COOKIE['user_meta']), true) : '');

if (isset($daterange)) {
    $dates = explode(" - ", $daterange);
    $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
    $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));
} else {
    $startDate = date("Y-m-d 00:00:00", strtotime("-1 month"));
    $endDate = date("Y-m-d 23:59:59");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo str_replace('_', ' ', $dashboardPageTitle . ' | ' . SITE_NAME); ?></title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#3B82F6',
                    secondary: '#8B5CF6'
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
    <style>
    :where([class^="ri-"])::before {
        content: "\f3c2";
    }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Sidebar -->
    <div id="sidebar"
        class="fixed left-0 top-0 h-full w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-20">
        <div class="p-6 border-b border-gray-200">
            <div class="w-full flex items-center justify-center">
                <img src="<?php echo $app_image ?>" alt="<?php echo SITE_NAME ?> logo" title="<?php echo SITE_NAME ?>"
                    class="h-10 sm:h-12 w-auto object-contain" loading="lazy" decoding="async" />
            </div>
        </div>
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-primary bg-blue-50 rounded-lg">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-dashboard-line"></i>
                        </div>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="https://readdy.ai/home/58b9fc47-6ca0-46cd-a8a8-e7fd045b11c5/981b5349-2ff8-4bd7-81d2-5d88517c950e"
                        data-readdy="true"
                        class="flex items-center gap-3 px-3 py-2.5 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-archive-line"></i>
                        </div>
                        <span class="font-medium">Inventory</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2.5 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-user-star-line"></i>
                        </div>
                        <span class="font-medium">Lead Management</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2.5 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-bar-chart-line"></i>
                        </div>
                        <span class="font-medium">Analytics</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2.5 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-team-line"></i>
                        </div>
                        <span class="font-medium">Customers</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2.5 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-file-text-line"></i>
                        </div>
                        <span class="font-medium">Reports</span>
                    </a>
                </li>
                <li>
                    <a href="https://readdy.ai/home/58b9fc47-6ca0-46cd-a8a8-e7fd045b11c5/92bcc176-f06d-4179-873a-b18efedc686d"
                        data-readdy="true"
                        class="flex items-center gap-3 px-3 py-2.5 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-settings-line"></i>
                        </div>
                        <span class="font-medium">Settings</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="absolute bottom-4 left-4 right-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <img src="<?php echo ((PROFILE_IMAGE != '') ? PROFILE_IMAGE : IMG_LIBRARY . 'default-profile.png'); ?>"
                        alt="User" class="w-10 h-10 rounded-full object-cover">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            <?php echo ucwords(PROFILE_DISPLAY_NAME) ?>
                        </p>
                        <p class="text-xs text-gray-500"><?php echo ucwords($site_access_type) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden lg:hidden"></div>
    <!-- Main Content -->
    <div class="lg:ml-64 min-h-screen transition-all duration-300">
        <!-- Top Header -->
        <header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle"
                        class="lg:hidden w-8 h-8 flex items-center justify-center text-gray-600 hover:text-primary">
                        <i class="ri-menu-line"></i>
                    </button>
                    <div class="hidden sm:block">
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                        <p class="text-sm text-gray-600">Welcome back, Here's what's
                            happening today.</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex relative">
                        <input type="text" placeholder="Search..."
                            class="w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                        <div
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 flex items-center justify-center text-gray-400">
                            <i class="ri-search-line"></i>
                        </div>
                    </div>
                    <button id="notificationButton"
                        class="relative w-10 h-10 flex items-center justify-center text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="ri-notification-line"></i>
                        <span id="notificationBadge"
                            class="absolute top-2 right-2 px-1 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-xs rounded-full">3</span>
                    </button>
                    <div id="notificationDropdown"
                        class="hidden absolute right-0 top-full mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-100 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900">Notifications</h3>
                                <button id="markAllAsRead" class="text-sm text-primary hover:text-blue-700">Mark all as
                                    read</button>
                            </div>
                        </div>
                        <div class="max-h-[480px] overflow-y-auto">
                            <div class="notification-item unread">
                                <div
                                    class="flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-l-4 border-primary">
                                    <div
                                        class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="ri-shopping-cart-line text-primary"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900">New order #ORD-2024-0156 received from TechCorp
                                            Solutions worth $12,450</p>
                                        <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                                    </div>
                                    <button class="mark-as-read text-gray-400 hover:text-gray-600">
                                        <i class="ri-mail-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="notification-item unread">
                                <div
                                    class="flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-l-4 border-primary">
                                    <div
                                        class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="ri-alert-line text-orange-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900">Low stock alert: Wireless Headphones Pro (12
                                            units remaining)</p>
                                        <p class="text-xs text-gray-500 mt-1">15 minutes ago</p>
                                    </div>
                                    <button class="mark-as-read text-gray-400 hover:text-gray-600">
                                        <i class="ri-mail-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="notification-item unread">
                                <div
                                    class="flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-l-4 border-primary">
                                    <div
                                        class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="ri-user-add-line text-green-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900">New lead: Jennifer Martinez from Global
                                            Industries (Score: 85)</p>
                                        <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                                    </div>
                                    <button class="mark-as-read text-gray-400 hover:text-gray-600">
                                        <i class="ri-mail-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer">
                                    <div
                                        class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="ri-check-line text-secondary"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900">Deal closed with Innovate Systems - $45,000
                                            contract signed</p>
                                        <p class="text-xs text-gray-500 mt-1">3 hours ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-2 border-t border-gray-100">
                            <a href="#" class="block text-center text-sm text-primary hover:text-blue-700">View All
                                Notifications</a>
                        </div>
                    </div>
                    <button id="helpButton"
                        class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors relative">
                        <i class="ri-question-line"></i>
                    </button>
                    <div id="helpDropdownMenu"
                        class="hidden absolute right-0 top-full mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="ri-customer-service-line"></i>
                            <span>Help Center</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="ri-file-text-line"></i>
                            <span>Documentation</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="ri-video-line"></i>
                            <span>Video Tutorials</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="ri-message-2-line"></i>
                            <span>Contact Support</span>
                        </a>
                        <div class="h-px bg-gray-200 my-1"></div>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="ri-keyboard-line"></i>
                            <span>Keyboard Shortcuts</span>
                        </a>
                    </div>
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200 relative">
                        <div id="userMenuButton" class="float-left flex items-center gap-2">
                            <img src="https://readdy.ai/api/search-image?query=professional%20business%20person%20avatar%20headshot%20with%20clean%20background%20modern%20corporate%20style&width=32&height=32&seq=user-avatar-2&orientation=squarish"
                                alt="User" class="w-8 h-8 rounded-full object-cover">
                            <button class="w-4 h-4 flex items-center justify-center text-gray-400">
                                <i class="ri-arrow-down-s-line"></i>
                            </button>
                        </div>
                        <div id="userDropdownMenu"
                            class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                            <a href="#"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="ri-user-line"></i>
                                <span>View Profile</span>
                            </a>
                            <a href="#"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="ri-settings-line"></i>
                                <span>Account Settings</span>
                            </a>
                            <a href="#"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="ri-question-line"></i>
                                <span>Help & Support</span>
                            </a>
                            <div class="h-px bg-gray-200 my-1"></div>
                            <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                <i class="ri-logout-box-line"></i>
                                <span>Sign Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Dashboard Content -->
        <main class="p-4 sm:p-6">
            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <div class="bg-white rounded-lg p-4 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="ri-archive-line text-primary"></i>
                            <span class="text-xs font-medium text-green-600">+12.5%</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-xl font-semibold text-gray-900">$2.4M</h3>
                        <p class="text-gray-500 text-sm">Total Inventory Value</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="ri-user-star-line text-secondary"></i>
                            <span class="text-xs font-medium text-green-600">+8.2%</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-xl font-semibold text-gray-900">1,247</h3>
                        <p class="text-gray-500 text-sm">Active Leads</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="ri-money-dollar-circle-line text-green-600"></i>
                            <span class="text-xs font-medium text-green-600">+15.3%</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-xl font-semibold text-gray-900">$847K</h3>
                        <p class="text-gray-500 text-sm">Monthly Revenue</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="ri-line-chart-line text-orange-600"></i>
                            <span class="text-xs font-medium text-red-600">-2.1%</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-xl font-semibold text-gray-900">24.8%</h3>
                        <p class="text-gray-500 text-sm">Conversion Rate</p>
                    </div>
                </div>
            </div>
            <!-- Charts and Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-8">
                <!-- Sales Pipeline Chart -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Sales Pipeline</h3>
                        <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 pr-8">
                            <option>Last 30 days</option>
                            <option>Last 90 days</option>
                            <option>Last year</option>
                        </select>
                    </div>
                    <div id="salesChart" class="h-60 sm:h-80"></div>
                </div>
                <!-- Inventory Status -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Inventory Status</h3>
                        <button class="text-sm text-primary hover:text-blue-700 font-medium">View All</button>
                    </div>
                    <div id="inventoryChart" class="h-60 sm:h-80"></div>
                </div>
            </div>
            <!-- Recent Activities and Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Recent Activities -->
                <div class="lg:col-span-2 bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
                        <button class="text-sm text-primary hover:text-blue-700 font-medium">View All</button>
                    </div>
                    <div class="space-y-3 sm:space-y-4">
                        <div class="flex items-start gap-4 p-4 hover:bg-gray-50 rounded-lg transition-colors">
                            <div
                                class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="ri-shopping-cart-line text-primary"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">New order received from TechCorp Solutions
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Order #ORD-2024-0156 • $12,450</p>
                                <p class="text-xs text-gray-400 mt-1">2 minutes ago</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-4 hover:bg-gray-50 rounded-lg transition-colors">
                            <div
                                class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="ri-user-add-line text-green-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">New lead: Jennifer Martinez from Global
                                    Industries</p>
                                <p class="text-xs text-gray-500 mt-1">Lead Score: 85 • High Priority</p>
                                <p class="text-xs text-gray-400 mt-1">15 minutes ago</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-4 hover:bg-gray-50 rounded-lg transition-colors">
                            <div
                                class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="ri-alert-line text-orange-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">Low stock alert: Wireless Headphones Pro
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Only 12 units remaining • Reorder recommended</p>
                                <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-4 hover:bg-gray-50 rounded-lg transition-colors">
                            <div
                                class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="ri-check-line text-secondary"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">Deal closed with Innovate Systems</p>
                                <p class="text-xs text-gray-500 mt-1">Contract value: $45,000 • 6-month agreement</p>
                                <p class="text-xs text-gray-400 mt-1">3 hours ago</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Quick Actions</h3>
                    <div class="space-y-3">
                        <button
                            class="w-full flex items-center gap-3 p-3 text-left hover:bg-gray-50 rounded-lg transition-colors !rounded-button whitespace-nowrap">
                            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                                <i class="ri-add-line text-white text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Add New Product</span>
                        </button>
                        <a href="https://readdy.ai/home/58b9fc47-6ca0-46cd-a8a8-e7fd045b11c5/b68545a9-55bf-4088-ba0a-8d0c14dc626c"
                            data-readdy="true"
                            class="w-full flex items-center gap-3 p-3 text-left hover:bg-gray-50 rounded-lg transition-colors !rounded-button whitespace-nowrap">
                            <div class="w-8 h-8 bg-secondary rounded-lg flex items-center justify-center">
                                <i class="ri-user-add-line text-white text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Create Lead</span>
                        </a>
                        <button
                            class="w-full flex items-center gap-3 p-3 text-left hover:bg-gray-50 rounded-lg transition-colors !rounded-button whitespace-nowrap">
                            <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                                <i class="ri-file-text-line text-white text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Generate Report</span>
                        </button>
                        <button
                            class="w-full flex items-center gap-3 p-3 text-left hover:bg-gray-50 rounded-lg transition-colors !rounded-button whitespace-nowrap">
                            <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center">
                                <i class="ri-mail-send-line text-white text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Send Campaign</span>
                        </button>
                    </div>
                    <div class="mt-8 p-4 bg-gradient-to-r from-primary to-secondary rounded-lg">
                        <h4 class="text-white font-semibold mb-2">Upgrade to Pro</h4>
                        <p class="text-blue-100 text-sm mb-3">Get advanced analytics and unlimited storage</p>
                        <button
                            class="bg-white text-primary px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors !rounded-button whitespace-nowrap">
                            Learn More
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script id="sidebarToggle">
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }

        function handleResize() {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        }
        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);
        window.addEventListener('resize', handleResize);
        handleResize();
    });
    </script>
    <script id="salesChart">
    document.addEventListener('DOMContentLoaded', function() {
        const salesChart = echarts.init(document.getElementById('salesChart'));
        const salesOption = {
            animation: false,
            grid: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0,
                containLabel: true
            },
            tooltip: {
                trigger: 'axis',
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                borderColor: '#e5e7eb',
                textStyle: {
                    color: '#1f2937'
                }
            },
            xAxis: {
                type: 'category',
                data: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                axisLine: {
                    lineStyle: {
                        color: '#e5e7eb'
                    }
                },
                axisTick: {
                    show: false
                },
                axisLabel: {
                    color: '#6b7280'
                }
            },
            yAxis: {
                type: 'value',
                axisLine: {
                    show: false
                },
                axisTick: {
                    show: false
                },
                axisLabel: {
                    color: '#6b7280'
                },
                splitLine: {
                    lineStyle: {
                        color: '#f3f4f6'
                    }
                }
            },
            series: [{
                data: [420, 532, 601, 734, 890, 1230],
                type: 'line',
                smooth: true,
                lineStyle: {
                    color: 'rgba(87, 181, 231, 1)',
                    width: 3
                },
                itemStyle: {
                    color: 'rgba(87, 181, 231, 1)'
                },
                areaStyle: {
                    color: {
                        type: 'linear',
                        x: 0,
                        y: 0,
                        x2: 0,
                        y2: 1,
                        colorStops: [{
                            offset: 0,
                            color: 'rgba(87, 181, 231, 0.1)'
                        }, {
                            offset: 1,
                            color: 'rgba(87, 181, 231, 0.01)'
                        }]
                    }
                },
                showSymbol: false
            }]
        };
        salesChart.setOption(salesOption);
    });
    </script>
    <script id="dropdowns">
    document.addEventListener('DOMContentLoaded', function() {
        const userMenuButton = document.getElementById('userMenuButton');
        const userDropdownMenu = document.getElementById('userDropdownMenu');
        const helpButton = document.getElementById('helpButton');
        const helpDropdownMenu = document.getElementById('helpDropdownMenu');
        const notificationButton = document.getElementById('notificationButton');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationBadge = document.getElementById('notificationBadge');
        const markAllAsRead = document.getElementById('markAllAsRead');

        function toggleDropdown(menu) {
            menu.classList.toggle('hidden');
        }

        function closeDropdowns(event) {
            if (!userMenuButton.contains(event.target) && !userDropdownMenu.contains(event.target)) {
                userDropdownMenu.classList.add('hidden');
            }
            if (!helpButton.contains(event.target) && !helpDropdownMenu.contains(event.target)) {
                helpDropdownMenu.classList.add('hidden');
            }
            if (!notificationButton.contains(event.target) && !notificationDropdown.contains(event.target)) {
                notificationDropdown.classList.add('hidden');
            }
        }

        function updateNotificationCount() {
            const unreadCount = document.querySelectorAll('.notification-item.unread').length;
            if (unreadCount === 0) {
                notificationBadge.classList.add('hidden');
            } else {
                notificationBadge.classList.remove('hidden');
                notificationBadge.textContent = unreadCount;
            }
        }

        function markAsRead(notificationItem) {
            notificationItem.classList.remove('unread');
            notificationItem.querySelector('.border-l-4').classList.remove('border-primary');
            notificationItem.querySelector('.mark-as-read').remove();
            updateNotificationCount();
        }
        document.querySelectorAll('.mark-as-read').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const notificationItem = button.closest('.notification-item');
                markAsRead(notificationItem);
            });
        });
        markAllAsRead.addEventListener('click', () => {
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                markAsRead(item);
            });
        });
        userMenuButton.addEventListener('click', () => toggleDropdown(userDropdownMenu));
        helpButton.addEventListener('click', () => toggleDropdown(helpDropdownMenu));
        notificationButton.addEventListener('click', () => toggleDropdown(notificationDropdown));
        document.addEventListener('click', closeDropdowns);
        updateNotificationCount();
    });
    </script>
    <script id="inventoryChart">
    document.addEventListener('DOMContentLoaded', function() {
        const inventoryChart = echarts.init(document.getElementById('inventoryChart'));
        const inventoryOption = {
            animation: false,
            grid: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0,
                containLabel: true
            },
            tooltip: {
                trigger: 'item',
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                borderColor: '#e5e7eb',
                textStyle: {
                    color: '#1f2937'
                },
                formatter: '{a} <br/>{b}: {c} ({d}%)'
            },
            series: [{
                name: 'Inventory Status',
                type: 'pie',
                radius: ['40%', '70%'],
                center: ['50%', '50%'],
                itemStyle: {
                    borderRadius: 8
                },
                data: [{
                        value: 1048,
                        name: 'In Stock',
                        itemStyle: {
                            color: 'rgba(87, 181, 231, 1)'
                        }
                    },
                    {
                        value: 735,
                        name: 'Low Stock',
                        itemStyle: {
                            color: 'rgba(251, 191, 114, 1)'
                        }
                    },
                    {
                        value: 580,
                        name: 'Out of Stock',
                        itemStyle: {
                            color: 'rgba(252, 141, 98, 1)'
                        }
                    },
                    {
                        value: 484,
                        name: 'Incoming',
                        itemStyle: {
                            color: 'rgba(141, 211, 199, 1)'
                        }
                    }
                ],
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }]
        };
        inventoryChart.setOption(inventoryOption);
    });
    </script>
</body>

</html>