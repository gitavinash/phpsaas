<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
            <a href="/">
                <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"
                    class="h-full max-h-[45px] w-auto object-contain">
            </a>
        </div>
        <!-- Search Bar -->
        <div class="relative w-1/3">
            <div class="absolute inset-y-0 left-0 w-10 h-10 flex items-center justify-center text-gray-500">
                <i class="ri-search-line ri-lg"></i>
            </div>
            <input type="search" placeholder="Search discussions, doctors, topics..."
                class="w-full h-10 pl-10 pr-4 border border-gray-200 rounded text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <!-- User Actions -->
        <div class="flex items-center space-x-4">
            <div class="relative">
                <?php

                if (isset($_COOKIE['user_data'])) {

                    $name = PROFILE_DISPLAY_NAME;
                    $parts = explode(' ', trim($name));
                    $initials = '';

                    foreach ($parts as $p) {
                        if ($p !== '') {
                            $initials .= strtoupper($p[0]);
                        }
                    }

                ?>
                <button id="profileButton"
                    class="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-full !rounded-button whitespace-nowrap cursor-pointer">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                        <span class="text-white font-medium text-sm"><?php echo ucwords($initials) ?></span>
                    </div>
                    <span class="font-medium text-sm"><?php echo PROFILE_DISPLAY_NAME ?></span>
                    <i class="ri-arrow-down-s-line ri-lg"></i>
                </button>
                <div id="profileDropdown" class="dropdown-menu">
                    <!-- <a href="#" class="dropdown-item">
                        <i class="ri-user-line mr-2"></i>My Profile
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="ri-settings-line mr-2"></i>Account Settings
                    </a> -->
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="ri-discuss-line mr-2"></i>My Discussions
                    </a>
                    <!-- <a href="#" class="dropdown-item">
                        <i class="ri-bookmark-line mr-2"></i>Bookmarks
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="ri-notification-line mr-2"></i>Notifications
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="ri-question-line mr-2"></i>Help & Support
                    </a> -->
                    <a href="/logout/" class="dropdown-item">
                        <i class="ri-logout-box-line mr-2"></i>Logout
                    </a>
                </div>
                <?php
                } else { ?>
                <a href="/login/?regType=subscriber"
                    class="bg-primary text-white px-4 py-2 rounded-full hover:bg-primary/90 font-medium text-sm whitespace-nowrap">
                    Get Started
                </a>
                <?php
                }

                ?>

            </div>
        </div>
    </div>
</header>