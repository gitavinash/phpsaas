<?php
require_once MENU_FUNCTIONS;
require_once DATA_FUNCTIONS;

$activeSubscription = false;
if (isset($_COOKIE['user_data'])) {
  require_once LOGIN_CHECK;
  $userSaltKey = base64_decode($_COOKIE['user_data']);

  if ($checkUser = mysqli_query($connection, "SELECT subscription_status, subscription_relation_id, subscription_type, subscription_mode, subscription_time, subscription_expiry FROM " . TABLE_PREFIX . "user LEFT JOIN " . TABLE_PREFIX . "subscription ON user_id = subscription_user_id WHERE user_salt_key = '{$userSaltKey}' AND CURDATE() <= subscription_expiry AND subscription_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkUser) != 0) {
      extract(mysqli_fetch_assoc($checkUser));
      if ($subscription_status == 1) {
        $activeSubscription = true;

        // Fetch subscription details

        if ($subsriptionDetails = mysqli_query($connection, "SELECT {$subscription_type}_image, {$subscription_type}_title, {$subscription_type}_description, {$subscription_type}_highlight, {$subscription_type}_type, inventory_currency_symbol, inventory_price, inventory_mrp FROM " . TABLE_PREFIX . "{$subscription_type} INNER JOIN " . TABLE_PREFIX . "inventory ON {$subscription_type}_id=inventory_relation_id WHERE {$subscription_type}_id='{$subscription_relation_id}' AND {$subscription_type}_type='subscription' AND {$subscription_type}_site_id=" . SITE_ID)) {
          if (mysqli_num_rows($subsriptionDetails) != 0) {
            extract(mysqli_fetch_assoc($subsriptionDetails));
          }
        }
      }
    }
  }

  // Check user active session

  if (!in_array($user_id, [9423, 9473])) {
    if ($checkUserSession = mysqli_query($connection, "SELECT us_active_device FROM " . TABLE_PREFIX . "user_sessions WHERE us_status=1 AND us_user_id='{$user_id}' AND us_site_id=" . SITE_ID)) {
      if (mysqli_num_rows($checkUserSession) > 2) {
        $showLogoutBtn = 0;
        $excluded_paths = ['/manage-subscription/', '/terms-and-conditions/', '/about-us/', '/terms-and-conditions/', '/privacy-policy/', '/refund-policy/'];
        $is_excluded = false;
        foreach ($excluded_paths as $path) {
          if (strpos(BASE_URL, $path) !== false) {
            $is_excluded = true;
            break;
          }
        }

        if (!$is_excluded) {
          require_once 'user-suspend.php';
          exit();
        }
      } else {
        $showLogoutBtn = 1;
      }
    }
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css" />
  <?php require_once DEFAULT_META ?>
</head>

<body>
  <!-- Header -->
  <header class="site-header">
    <div class="container header-container">
      <div class="logo">
        <a href="/">
          <img
            src="https://theheadandtale.com//app/nms/visitor/theheadandtale/desktop/assets/images/main_logo.png"
            alt="The Head & Tale"
            class="logo-img" />
        </a>
      </div>
      <nav class="main-nav">
        <ul>
          <li><a href="#fintech">Fintech</a></li>
          <li><a href="#ai">AI & Tech</a></li>
          <li><a href="#ipo">IPO Tales</a></li>
          <li><a href="#news">News</a></li>
        </ul>
      </nav>
      <div class="header-actions">
        <button class="search-btn" aria-label="Search">
          <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </button>
        <a href="#subscribe" class="btn btn-primary">Subscribe</a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main>