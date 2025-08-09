<?php

$appDashboardDirectory = ((isset($userDashboardDirectory)) ? (($userDashboardDirectory != '' ? $userDashboardDirectory : ADMIN_PATH)) : ADMIN_PATH)  . 'dashboard/app/';

$postType = rtrim(ltrim(ADMIN_BASE_URL, '/'), '/');
$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';
$siteAccessMeta = '';
if (isset($_COOKIE['user_meta'])) {
    if ($_COOKIE['user_meta'] != '') {
        $siteAccessMeta = (json_decode(base64_decode($_COOKIE['user_meta']), true));
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/dashboard/">
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
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <?php
                    // Common button class for consistent height & alignment
                    $btnBase = "font-medium !rounded-button px-6 py-2 cursor-pointer flex items-center justify-center h-11 leading-none";

                    if (isset($_COOKIE['user_data'])) {
                        echo "
        <a href='/logout/?authby=mobile'
            class='bg-red-600 text-white hover:bg-red-500 {$btnBase}'>
            <i class='ri-logout-box-line text-lg mr-2'></i> Logout
        </a>";
                    } else {
                        echo "
        <a href='/login/?authby=mobile'
            class='text-primary hover:text-primary/80 {$btnBase}'>
            Login
        </a>
        <a href='/login/?authby=mobile'
            class='bg-primary text-white hover:bg-primary/90 {$btnBase}'>
            Register
        </a>";
                    }
                    ?>
                </div>

                <button class="md:hidden w-10 h-10 flex items-center justify-center text-gray-700" id="mobileMenuBtn">
                    <i class="ri-menu-line ri-2x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="flex mt-20">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-sm min-h-screen border-r border-gray-200">
            <nav class="p-6">
                <ul class="space-y-2">
                    <li>
                        <a href="/dashboard/"
                            class="flex items-center px-4 py-3 <?php echo ((ADMIN_BASE_URL == '/') ? 'text-primary bg-primary/10' : 'hover:bg-gray-50 text-gray-600') ?> rounded-lg font-medium">
                            <div class="w-5 h-5 flex items-center justify-center mr-3">
                                <i class="ri-dashboard-line text-lg"></i>
                            </div>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="applications/list/"
                            class="flex items-center px-4 py-3 <?php echo ((ADMIN_BASE_URL == '/applications/list/') ? 'text-primary bg-primary/10' : 'hover:bg-gray-50 text-gray-600') ?> hover:bg-gray-50 rounded-lg">
                            <div class="w-5 h-5 flex items-center justify-center mr-3">
                                <i class="ri-file-list-line text-lg"></i>
                            </div>
                            My Applications
                        </a>
                    </li>
                    <li>
                        <a href="/track-application/" target="_blank"
                            class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                            <div class="w-5 h-5 flex items-center justify-center mr-3">
                                <i class="ri-search-line text-lg"></i>
                            </div>
                            Track Application
                        </a>
                    </li>
                    <li>
                        <a href="setting/info/"
                            class="flex items-center px-4 py-3 <?php echo ((ADMIN_BASE_URL == '/setting/info/') ? 'text-primary bg-primary/10' : 'hover:bg-gray-50 text-gray-600') ?> hover:bg-gray-50 rounded-lg"
                            id="settings-link">
                            <div class="w-5 h-5 flex items-center justify-center mr-3">
                                <i class="ri-settings-line text-lg"></i>
                            </div>
                            Settings
                        </a>
                    </li>
                    <li>
                        <a href="/contact-us/" target="_blank"
                            class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                            <div class="w-5 h-5 flex items-center justify-center mr-3">
                                <i class="ri-customer-service-line text-lg"></i>
                            </div>
                            Help & Support
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 bg-gray-50">