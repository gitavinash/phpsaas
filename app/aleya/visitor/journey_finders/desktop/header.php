<?php

function buildFilterQuery($filter, $value, $connection)
{
    $conditions = [];

    $filter = strtolower(trim($filter));
    $value = trim($value);

    switch ($filter) {
        case 'duration':
            if (preg_match('/^(\d+)-(\d+)$/', $value, $matches)) {
                $min = (int)$matches[1] - 1;
                $max = (int)$matches[2] - 1;
                $conditions[] = "tour_package_duration BETWEEN {$min} AND {$max}";
            }
            break;

        case 'type':
            $safeValue = mysqli_real_escape_string($connection, $value);
            $conditions[] = "tour_package_theme = '{$safeValue}'";
            break;

        case 'price':
            if (preg_match('/^(\d+)-(\d+)$/', $value, $matches)) {
                $min = (int)$matches[1] - 1;
                $max = (int)$matches[2] - 1;
                $conditions[] = "tour_package_price_selling_adult BETWEEN {$min} AND {$max}";
            }
            break;

        default:
            // Unknown filter — ignore
            break;
    }

    if (!empty($conditions)) {
        return implode(" AND ", $conditions) . ' AND ';
    } else {
        return "";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="<?php echo SITE_ICON ?>" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <?php require_once DEFAULT_META ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo GOOGLE_RECAPTCHA_SITE_KEY ?>"></script>
    <script>
    grecaptcha.ready(function() {
        grecaptcha.execute('<?php echo GOOGLE_RECAPTCHA_SITE_KEY ?>', {
            action: 'submit'
        }).then(function(token) {
            document.getElementById('recaptcha_token').value = token;
        });
    });
    </script><!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YQ4B5BGGGT"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-YQ4B5BGGGT');
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
    })(window, document, "clarity", "script", "s0g6kct87g");
    </script>
</head>

<body class="bg-white">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <!-- Logo + Desktop Menu -->
            <div class="flex items-center">
                <a href="/" class="mr-8">
                    <img src="<?php echo SITE_LOGO ?>" alt="Journey Finders" class="h-10" />
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex space-x-6">
                    <a href="/"
                        class="<?php echo ((ACTIVE_URL == '/') ? 'text-primary' : 'text-gray-700') ?> hover:text-primary font-medium">Home</a>

                    <!-- Domestic Mega -->
                    <div class="relative group">
                        <a class="hover:text-primary cursor-pointer font-medium text-gray-700">Domestic</a>
                        <div
                            class="absolute left-0 top-full mt-2 bg-white shadow-xl rounded-xl p-6 grid grid-cols-2 gap-6 invisible opacity-0 group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 whitespace-nowrap min-w-max">
                            <!-- Columns for Domestic Menu -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Popular Destinations</h4>
                                <ul class="space-y-1 text-sm text-gray-700">
                                    <li><a href="/packages/india/manali/" class="hover:text-primary">Manali</a></li>
                                    <li><a href="/packages/india/kerala/" class="hover:text-primary">Kerala</a></li>
                                    <li><a href="/packages/india/goa/" class="hover:text-primary">Goa</a></li>
                                    <li><a href="/packages/india/kashmir/" class="hover:text-primary">Kashmir</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- International Mega -->
                    <div class="relative group">
                        <a class="hover:text-primary cursor-pointer font-medium text-gray-700">International</a>
                        <div
                            class="absolute left-0 top-full mt-2 bg-white shadow-xl rounded-xl p-6 grid grid-cols-2 gap-6 invisible opacity-0 group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 whitespace-nowrap min-w-max">
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Top Destinations</h4>
                                <ul class="space-y-1 text-sm text-gray-700">
                                    <li><a href="/packages/maldives/" class="hover:text-primary">Maldives</a></li>
                                    <li><a href="/packages/united-arab-emirates/" class="hover:text-primary">United Arab
                                            Emirates</a></li>
                                    <li><a href="/packages/thailand/" class="hover:text-primary">Thailand</a></li>
                                    <li><a href="/packages/mauritius/" class="hover:text-primary">Mauritius</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Desktop Search and Contact -->
            <div class="hidden md:flex items-center space-x-4">
                <form action="/search/" method="get" class="relative">
                    <div
                        class="w-10 h-10 flex items-center justify-center text-gray-500 absolute left-2 top-1/2 transform -translate-y-1/2">
                        <i class="ri-search-line ri-lg"></i>
                    </div>
                    <input type="text" name="q" placeholder="Search..." autocomplete="off"
                        class="pl-12 pr-4 py-2 w-64 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm" />
                </form>
                <a href="/contact-us"
                    class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap font-medium text-sm hover:bg-primary/90 transition">Contact
                    Us</a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="block md:hidden">
                <button id="menuToggle" class="w-10 h-10 flex items-center justify-center text-gray-700">
                    <i class="ri-menu-line ri-lg"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="fixed inset-0 bg-white z-50 hidden md:hidden overflow-y-auto p-6">
            <button id="menuClose" class="absolute top-4 right-4 text-2xl text-gray-700 hover:text-primary">
                <i class="ri-close-line"></i>
            </button>

            <!-- Mobile Search -->
            <form action="/search/" method="get" class="relative mb-4 mt-4">
                <div
                    class="w-10 h-10 flex items-center justify-center text-gray-500 absolute left-2 top-1/2 transform -translate-y-1/2">
                    <i class="ri-search-line ri-lg"></i>
                </div>
                <input type="text" name="q" value="<?php echo ((isset($q)) ? $q : '') ?>" placeholder="Search..."
                    class="pl-12 pr-4 py-2 w-full border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm" />
            </form>

            <!-- Mobile Links -->
            <a href="/" class="block text-gray-700 text-lg hover:text-primary mb-3">Home</a>

            <!-- Domestic Mobile Dropdown -->
            <div class="mb-3">
                <button class="flex justify-between items-center w-full text-lg text-gray-700 hover:text-primary"
                    onclick="toggleDropdown('domesticDropdown')">
                    Domestic
                    <i class="ri-arrow-down-s-line ml-2"></i>
                </button>
                <div id="domesticDropdown" class="hidden pl-4 mt-2 space-y-2 text-sm text-gray-600">
                    <a href="/packages/india/manali/" class="block hover:text-primary">Manali</a>
                    <a href="/packages/india/kerala/" class="block hover:text-primary">Kerala</a>
                    <a href="/packages/india/goa/" class="block hover:text-primary">Goa</a>
                    <a href="/packages/india/kashmir/" class="block hover:text-primary">Kashmir</a>
                </div>
            </div>

            <!-- International Mobile Dropdown -->
            <div class="mb-3">
                <button class="flex justify-between items-center w-full text-lg text-gray-700 hover:text-primary"
                    onclick="toggleDropdown('internationalDropdown')">
                    International
                    <i class="ri-arrow-down-s-line ml-2"></i>
                </button>
                <div id="internationalDropdown" class="hidden pl-4 mt-2 space-y-2 text-sm text-gray-600">
                    <a href="/packages/maldives/" class="block hover:text-primary">Maldives</a>
                    <a href="/packages/united-arab-emirates/" class="block hover:text-primary">United Arab Emirates</a>
                    <a href="/packages/thailand/" class="block hover:text-primary">Thailand</a>
                    <a href="/packages/mauritius/" class="block hover:text-primary">Mauritius</a>
                </div>
            </div>

            <a href="/contact-us" class="block text-gray-700 text-lg hover:text-primary">Contact Us</a>
        </div>
    </header>

    <!-- Mobile JS -->
    <script>
    const menuToggle = document.getElementById("menuToggle");
    const menuClose = document.getElementById("menuClose");
    const mobileMenu = document.getElementById("mobileMenu");

    menuToggle.addEventListener("click", () => {
        mobileMenu.classList.remove("hidden");
    });

    menuClose.addEventListener("click", () => {
        mobileMenu.classList.add("hidden");
    });

    function toggleDropdown(id) {
        const el = document.getElementById(id);
        el.classList.toggle("hidden");
    }
    </script>