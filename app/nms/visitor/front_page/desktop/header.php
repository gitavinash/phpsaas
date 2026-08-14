<?php
require_once MENU_FUNCTIONS;
require_once DATA_FUNCTIONS;

// AJAX Autocomplete Endpoint for Search Modal (Tags & News Stories)
if (isset($_GET['action']) && $_GET['action'] == 'autocomplete') {
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: private, max-age=30');
  $q = trim((string)($_GET['q'] ?? ''));
  $q = mb_substr($q, 0, 60);
  $results = [];
  if (!empty($q) && mb_strlen($q) >= 2) {
    $siteId = (int)SITE_ID;
    $qPrefix = $q . '%';

    // Query Tag Table (prefix match for index-friendly lookup)
    $tagSql = "SELECT t.tag_title, l.link_url
      FROM " . TABLE_PREFIX . "tag t
      INNER JOIN " . TABLE_PREFIX . "link l
        ON l.link_relation_id = t.tag_id
       AND l.link_type = 'tag'
       AND l.link_status = 200
       AND l.link_site_id = ?
      WHERE t.tag_site_id = ?
        AND t.tag_title LIKE ?
      ORDER BY t.tag_id DESC
      LIMIT 5";

    if ($tagStmt = mysqli_prepare($connection, $tagSql)) {
      mysqli_stmt_bind_param($tagStmt, 'iis', $siteId, $siteId, $qPrefix);
      mysqli_stmt_execute($tagStmt);
      $tagRes = mysqli_stmt_get_result($tagStmt);
      if ($tagRes) {
        while ($row = mysqli_fetch_assoc($tagRes)) {
          $results[] = [
            'type' => 'tag',
            'title' => htmlspecialchars($row['tag_title']),
            'url' => htmlspecialchars($row['link_url'])
          ];
        }
      }
    }

    // Query News Stories using UNION to avoid expensive title/tag OR scan
    $newsSql = "SELECT DISTINCT x.news_title, x.news_image, x.link_url, x.news_id
      FROM (
        SELECT n.news_id, n.news_title, n.news_image, l.link_url
        FROM " . TABLE_PREFIX . "news n
        INNER JOIN " . TABLE_PREFIX . "link l
          ON l.link_relation_id = n.news_id
         AND l.link_type = 'news'
         AND l.link_status = 200
         AND l.link_site_id = ?
        WHERE n.news_site_id = ?
          AND n.news_status = 1
          AND n.news_is_public = 1
          AND n.news_title LIKE ?

        UNION

        SELECT n.news_id, n.news_title, n.news_image, l.link_url
        FROM " . TABLE_PREFIX . "news n
        INNER JOIN " . TABLE_PREFIX . "link l
          ON l.link_relation_id = n.news_id
         AND l.link_type = 'news'
         AND l.link_status = 200
         AND l.link_site_id = ?
        INNER JOIN " . TABLE_PREFIX . "tag t
          ON (t.tag_relation_id = n.news_id OR t.tag_relation_id = l.link_id)
         AND t.tag_type = 'news'
         AND t.tag_site_id = ?
        WHERE n.news_site_id = ?
          AND n.news_status = 1
          AND n.news_is_public = 1
          AND t.tag_title LIKE ?
      ) x
      ORDER BY x.news_id DESC
      LIMIT 4";

    if ($newsStmt = mysqli_prepare($connection, $newsSql)) {
      mysqli_stmt_bind_param(
        $newsStmt,
        'iisiiis',
        $siteId,
        $siteId,
        $qPrefix,
        $siteId,
        $siteId,
        $siteId,
        $qPrefix
      );
      mysqli_stmt_execute($newsStmt);
      $newsRes = mysqli_stmt_get_result($newsStmt);
      if ($newsRes) {
        while ($row = mysqli_fetch_assoc($newsRes)) {
          $img = trim($row['news_image'] ?? '');
          if (!empty($img) && substr($img, 0, 1) !== '/' && !preg_match('/^https?:\/\//i', $img)) {
            $img = '/' . $img;
          }
          $results[] = [
            'type' => 'story',
            'title' => htmlspecialchars($row['news_title']),
            'image' => htmlspecialchars($img),
            'url' => htmlspecialchars($row['link_url'])
          ];
        }
      }
    }
  }
  echo json_encode($results);
  exit();
}

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
        $excluded_paths = ['/manage-active-session/', '/manage-subscription/', '/terms-and-conditions/', '/about-us/', '/privacy-policy/', '/refund-policy/'];
        $is_excluded = false;
        foreach ($excluded_paths as $path) {
          if (strpos(BASE_URL, $path) !== false) {
            $is_excluded = true;
            break;
          }
        }

        if (!$is_excluded) {
          require_once 'manage-active-session.php';
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
    href="https://fonts.googleapis.com/css2?family=Newsreader:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap"
    rel="stylesheet" />
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css" />
  <?php require_once DEFAULT_META ?>
  <!-- JSON-LD Structured Data Schema -->
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "NewsMediaOrganization",
      "name": "<?php echo SITE_NAME ?>",
      "url": "https://theheadandtale.com",
      "logo": "<?php echo SITE_LOGO ?>",
      "sameAs": [
        "https://twitter.com/headandtale",
        "https://linkedin.com/company/headandtale"
      ]
    }
  </script>
  <!-- SweetAlert2 Library & Custom Theme Styling -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-KN6V4VKJN4"></script>
  <script>
    window.dataLayer = window.dataLayer || []

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'G-KN6V4VKJN4');
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
    })(window, document, "clarity", "script", "qicb9eq9ym");
  </script>
</head>

<body>
  <!-- Header -->
  <header class="site-header">
    <!-- Top Row: Logo & Actions -->
    <div class="header-top-bar">
      <div class="container header-top-container">
        <!-- Mobile Left Drawer Toggle (Categories & Navigation) -->
        <button class="mobile-menu-btn" id="openLeftDrawerBtn" aria-label="Open Mobile Categories">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>

        <div class="logo">
          <a href="/">
            <img
              src="<?php echo SITE_LOGO ?>"
              alt="The Head &amp; Tale"
              width="180"
              height="42"
              class="logo-img" />
          </a>
        </div>

        <div class="header-actions">
          <!-- Search Trigger -->
          <button class="search-btn" id="openSearchBtn" aria-label="Open Search">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
          </button>

          <!-- My Account / Sign In Option -->
          <?php
          $isLoggedIn = isset($_COOKIE['isLogin']) || isset($_COOKIE['user_data']);
          $accountButtonLabel = 'My Account';
          if ($isLoggedIn && !empty($user_id)) {
            $accountUserQuery = "SELECT p.profile_display_name, u.user_username
              FROM " . TABLE_PREFIX . "profile p
              LEFT JOIN " . TABLE_PREFIX . "user u ON u.user_id = p.profile_user_id
              WHERE p.profile_user_id = " . (int)$user_id . "
              LIMIT 1";
            if ($accountUserResult = mysqli_query($connection, $accountUserQuery)) {
              if ($accountUserRow = mysqli_fetch_assoc($accountUserResult)) {
                $displayName = trim((string)($accountUserRow['profile_display_name'] ?? ''));
                if ($displayName === '') {
                  $displayName = trim((string)($accountUserRow['user_username'] ?? ''));
                }
                if ($displayName !== '') {
                  $accountButtonLabel = mb_substr($displayName, 0, 18);
                }
              }
              mysqli_free_result($accountUserResult);
            }
          }
          if ($isLoggedIn):
          ?>
            <div class="user-account-dropdown" id="userAccountDropdown">
              <button class="account-btn" id="accountMenuBtn" type="button" aria-haspopup="true" aria-expanded="false">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span><?php echo htmlspecialchars($accountButtonLabel); ?></span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M6 9l6 6 6-6" />
                </svg>
              </button>
              <div class="account-dropdown-menu" id="accountDropdownMenu">
                <a href="/my-profile/" class="account-dropdown-item">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  My Profile
                </a>
                <a href="/manage-subscription/" class="account-dropdown-item">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 7h-9M14 17H5M12 12H3" />
                  </svg>
                  Manage Subscription
                </a>
                <a href="/manage-active-session/" class="account-dropdown-item">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                    <line x1="8" y1="21" x2="16" y2="21" />
                    <line x1="12" y1="17" x2="12" y2="21" />
                  </svg>
                  Active Sessions
                </a>
                <div class="dropdown-divider"></div>
                <a href="/logout/" class="account-dropdown-item logout">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                  </svg>
                  Sign Out
                </a>
              </div>
            </div>
          <?php else: ?>
            <a href="/login/" class="btn-outline-signin">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span>Sign In</span>
            </a>
          <?php endif; ?>

          <!-- Mobile Right Drawer Button -->
          <button class="mobile-menu-btn" id="openRightDrawerBtn" aria-label="Open Account Portal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </button>

          <?php
          if (!$activeSubscription) {
            echo '<a href="/subscribe/" class="btn btn-primary">Subscribe</a>';
          }
          ?>
        </div>
      </div>
    </div>

    <!-- Bottom Row: Dedicated Navigation Bar -->
    <div class="header-nav-bar">
      <div class="container header-nav-container">
        <nav class="main-nav" aria-label="Main Navigation">
          <ul class="nav-menu">
            <?php generateMenu('SITE_NAV_MAIN_HEADER', 'dropdown', true, 'nav-link'); ?>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <!-- Mobile Drawer Overlay -->
  <div class="mobile-drawer-overlay" id="mobileDrawerOverlay"></div>

  <!-- Left Mobile Slide-Out Drawer (Categories & Navigation) -->
  <aside class="mobile-drawer mobile-drawer-left" id="leftMobileDrawer">
    <div class="drawer-header">
      <img src="https://theheadandtale.com//app/nms/visitor/theheadandtale/desktop/assets/images/main_logo.png" alt="Logo" class="drawer-header-logo" />
      <button class="drawer-close-btn" id="closeLeftDrawerBtn">&times;</button>
    </div>
    <div class="drawer-body">
      <ul class="drawer-nav-list">
        <?php generateMenu('SITE_NAV_MAIN_HEADER', 'dropdown', true, 'nav-link'); ?>
      </ul>
    </div>
  </aside>

  <!-- Right Mobile Slide-Out Drawer (Member Portal & Account Actions) -->
  <aside class="mobile-drawer mobile-drawer-right" id="rightMobileDrawer">
    <div class="drawer-header">
      <h3>Member Portal</h3>
      <button class="drawer-close-btn" id="closeRightDrawerBtn">&times;</button>
    </div>
    <div class="drawer-body">
      <?php if ($isLoggedIn): ?>
        <div class="drawer-section">
          <p class="drawer-section-title">Account Options</p>
          <a href="/my-profile/" class="btn btn-outline-secondary btn-block">My Profile</a>
          <a href="/manage-subscription/" class="btn btn-outline-secondary btn-block">Manage Subscription</a>
          <a href="/logout-all-devices/" class="btn btn-outline-secondary btn-block">Active Sessions</a>
          <a href="/logout/" class="btn btn-primary btn-block">Sign Out</a>
        </div>
      <?php else: ?>
        <div class="drawer-login-prompt">
          <p class="drawer-login-desc">Sign in to access your saved articles and subscriber features.</p>
          <a href="/login/" class="btn btn-primary btn-block btn-drawer-login">Member Sign In</a>
          <a href="/subscribe/" class="btn btn-outline-secondary btn-block btn-drawer-sub">View Subscriptions</a>
        </div>
      <?php endif; ?>
    </div>
  </aside>

  <!-- Bookmark App Download Modal Triggered on Bookmark Tap -->
  <div class="search-modal bookmark-app-modal" id="bookmarkAppModal" aria-hidden="true" role="dialog">
    <div class="bookmark-modal-box">
      <button class="close-search-btn bookmark-modal-close" id="closeBookmarkModalBtn">&times;</button>
      <div class="bookmark-modal-icon"><i class="ph ph-bookmark-simple"></i></div>
      <h2>Bookmark Stories on Mobile App</h2>
      <p>Save articles offline, build your reading list, and get real-time breaking news alerts on iOS &amp; Android.</p>

      <div class="app-store-badge-group">
        <a href="/app-download/" class="app-store-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.3c.64-.78 1.08-1.85.96-2.93-.93.04-2.07.62-2.74 1.4-.6.69-1.13 1.79-.98 2.86 1.04.08 2.12-.55 2.76-1.33z" />
          </svg>
          <span>App Store</span>
        </a>
        <a href="/app-download/" class="app-store-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3.6 1.83a2 2 0 0 0-.6 1.44v17.46a2 2 0 0 0 .6 1.44l.08.08L13.4 12.5v-.28L3.68 1.75l-.08.08zm13.11 7.29-3.31-1.9L10.23 10.4l3.17 3.17 3.31-1.9c.94-.54.94-1.42 0-1.96zm-1.04 4.88L12.5 10.83l-8.9 8.9c.31.33.84.38 1.34.1l10.73-5.83zm-8.9-8.9L12.5 13.17l3.17-3.17L4.94 4.17c-.5-.28-1.03-.23-1.34.1l8.17 8.17z" />
          </svg>
          <span>Google Play</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Interactive Search Modal -->
  <div id="searchModal" class="search-modal" aria-hidden="true" role="dialog" aria-label="Site Search">
    <div class="search-modal-content">
      <div class="search-modal-header">
        <h3>Search Stories & Topics</h3>
        <div class="search-header-right">
          <kbd class="kbd-badge">ESC</kbd>
          <button id="closeSearchBtn" class="close-search-btn" aria-label="Close Search">&times;</button>
        </div>
      </div>
      <form action="/search/" method="GET" class="search-modal-form" id="searchForm">
        <div class="search-input-group">
          <svg class="search-icon-svg" viewBox="0 0 24 24">
            <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
          </svg>
          <input type="text" name="q" placeholder="Type to search Fintech, AI, IPOs..." required id="searchInput" autocomplete="off" />
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </form>

      <!-- Popular Quick Topics -->
      <div class="popular-tags-bar">
        <span class="popular-tag-label">Popular:</span>
        <span class="popular-tag-item" data-tag="Fintech">#Fintech</span>
        <span class="popular-tag-item" data-tag="AI">#Artificial Intelligence</span>
        <span class="popular-tag-item" data-tag="IPO">#IPOs</span>
        <span class="popular-tag-item" data-tag="Banking">#Banking</span>
        <span class="popular-tag-item" data-tag="Startups">#Startups</span>
      </div>

      <!-- Recent Searches Block -->
      <div id="recentSearchesBlock" class="search-section-block">
        <div class="search-section-title">
          <span>Recent Searches</span>
          <button type="button" id="clearRecentBtn" class="clear-recent-btn">Clear All</button>
        </div>
        <div id="recentSearchPills" class="recent-pills-list"></div>
      </div>

      <!-- Autocomplete Suggestions Block -->
      <div id="autocompleteBlock" class="search-section-block display-none">
        <div class="search-section-title">
          <span>Suggested Topics & Stories</span>
        </div>
        <div id="autocompleteResults" class="autocomplete-list"></div>
      </div>

    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Click-to-toggle Dropdown Handler for all menu levels
      const dropdownParents = document.querySelectorAll('.main-nav .group, .main-nav .relative, .main-nav .has-dropdown, .main-nav .group\\/fly, .mobile-drawer .group, .mobile-drawer .relative, .mobile-drawer .has-dropdown, .mobile-drawer .group\\/fly');

      dropdownParents.forEach(function(parent) {
        // Check if element has a dropdown child
        const childDropdown = parent.querySelector('.absolute, .dropdown-menu');
        if (!childDropdown) return;

        const directLink = parent.querySelector(':scope > a');
        if (directLink) {
          directLink.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const isOpen = parent.classList.contains('is-open');

            // Close sibling dropdowns at the same level
            if (parent.parentElement) {
              const siblings = parent.parentElement.querySelectorAll(':scope > .group, :scope > .relative, :scope > .has-dropdown, :scope > .group\\/fly');
              siblings.forEach(function(sibling) {
                if (sibling !== parent) {
                  sibling.classList.remove('is-open');
                }
              });
            }

            if (isOpen) {
              parent.classList.remove('is-open');
            } else {
              parent.classList.add('is-open');
            }
          });
        }
      });

      // Close open dropdowns when clicking outside
      document.addEventListener('click', function(e) {
        if (!e.target.closest('.main-nav') && !e.target.closest('.mobile-drawer')) {
          document.querySelectorAll('.is-open').forEach(function(item) {
            item.classList.remove('is-open');
          });
        }
      });
    });
  </script>

  <!-- Main Content -->
  <main>