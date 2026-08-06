<?php
require_once MENU_FUNCTIONS;
require_once DATA_FUNCTIONS;

// AJAX Autocomplete Endpoint for Search Modal (Tags & News Stories)
if (isset($_GET['action']) && $_GET['action'] == 'autocomplete') {
    header('Content-Type: application/json; charset=utf-8');
    $q = mysqli_real_escape_string($connection, trim($_GET['q'] ?? ''));
    $results = [];
    if (!empty($q)) {
        // Query Tag Table
        $tagSql = "SELECT tag_title, link_url FROM " . TABLE_PREFIX . "tag INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=tag_id WHERE link_type='tag' AND link_status=200 AND tag_title LIKE '%{$q}%' LIMIT 5";
        if ($res = mysqli_query($connection, $tagSql)) {
            while ($row = mysqli_fetch_assoc($res)) {
                $results[] = [
                    'type' => 'tag',
                    'title' => htmlspecialchars($row['tag_title']),
                    'url' => htmlspecialchars($row['link_url'])
                ];
            }
        }
        // Query News Titles
        $newsSql = "SELECT news_title, link_url FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE link_type='news' AND link_status=200 AND news_title LIKE '%{$q}%' LIMIT 4";
        if ($res = mysqli_query($connection, $newsSql)) {
            while ($row = mysqli_fetch_assoc($res)) {
                $results[] = [
                    'type' => 'story',
                    'title' => htmlspecialchars($row['news_title']),
                    'url' => htmlspecialchars($row['link_url'])
                ];
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
  <!-- JSON-LD Structured Data Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "NewsMediaOrganization",
    "name": "The Head & Tale",
    "url": "https://theheadandtale.com",
    "logo": "https://theheadandtale.com/app/nms/visitor/theheadandtale/desktop/assets/images/main_logo.png",
    "sameAs": [
      "https://twitter.com/headandtale",
      "https://linkedin.com/company/headandtale"
    ]
  }
  </script>
</head>

<body>
  <!-- Header -->
  <header class="site-header">
    <div class="container header-container">
      <!-- Mobile Left Drawer Toggle (Categories & Navigation) -->
      <button class="mobile-menu-btn" id="openLeftDrawerBtn" aria-label="Open Mobile Categories">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
      </button>

      <div class="logo">
        <a href="/">
          <img
            src="https://theheadandtale.com//app/nms/visitor/theheadandtale/desktop/assets/images/main_logo.png"
            alt="The Head &amp; Tale"
            width="180"
            height="42"
            class="logo-img" />
        </a>
      </div>

      <!-- Main Navigation with Mega-Menu (All items visible at a glance) -->
      <nav class="main-nav" aria-label="Main Navigation">
        <ul class="nav-menu">
          <li><a href="/">Home</a></li>
          
          <!-- Mega Menu Dropdown -->
          <li class="has-mega-menu">
            <a href="/fintech-news/">
              Categories <span class="caret">▾</span>
            </a>
            <div class="mega-menu-dropdown">
              <div class="mega-menu-grid">
                <div class="mega-column">
                  <span class="mega-col-title">Fintech &amp; Banking</span>
                  <ul>
                    <li><a href="/fintech-news/">Digital Banking</a></li>
                    <li><a href="/fintech-news/">Payments &amp; Wallets</a></li>
                    <li><a href="/fintech-news/">Crypto &amp; Web3</a></li>
                    <li><a href="/fintech-news/">Insurtech &amp; Credit</a></li>
                  </ul>
                </div>
                <div class="mega-column">
                  <span class="mega-col-title">AI &amp; Emerging Tech</span>
                  <ul>
                    <li><a href="/ai-and-emerging-tech/">Generative AI</a></li>
                    <li><a href="/ai-and-emerging-tech/">Enterprise AI</a></li>
                    <li><a href="/ai-and-emerging-tech/">Machine Learning</a></li>
                    <li><a href="/ai-and-emerging-tech/">Robotics &amp; Automation</a></li>
                  </ul>
                </div>
                <div class="mega-column">
                  <span class="mega-col-title">Startups &amp; Markets</span>
                  <ul>
                    <li><a href="/money-and-machines/">Venture Capital</a></li>
                    <li><a href="/money-and-machines/">Unicorn Tales</a></li>
                    <li><a href="/ipo-tales/">IPO Tales</a></li>
                    <li><a href="/money-and-machines/">Founders Corner</a></li>
                  </ul>
                </div>
                <div class="mega-column featured-col">
                  <span class="mega-col-title">Executive Coverage</span>
                  <p>In-depth intelligence and proprietary market analysis for fintech leaders.</p>
                  <a href="/subscribe/" class="mega-cta-btn">View Membership &rarr;</a>
                </div>
              </div>
            </div>
          </li>

          <!-- Coverage Dropdown -->
          <li class="has-dropdown">
            <a href="/subscribe/">
              Coverage <span class="caret">▾</span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="/subscribe/">Executive Briefings</a></li>
              <li><a href="/subscribe/">Proprietary Reports</a></li>
            </ul>
          </li>

          <li><a href="/about-us/">About Us</a></li>
        </ul>
      </nav>

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
        if ($isLoggedIn):
        ?>
          <div class="user-account-dropdown" id="userAccountDropdown">
            <button class="account-btn" id="accountMenuBtn" type="button" aria-haspopup="true" aria-expanded="false">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              <span>My Account</span>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="account-dropdown-menu" id="accountDropdownMenu">
              <a href="/manage-subscription/" class="account-dropdown-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7h-9M14 17H5M12 12H3"/></svg>
                Manage Subscription
              </a>
              <a href="/logout-all-devices/" class="account-dropdown-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                Active Sessions
              </a>
              <div class="dropdown-divider"></div>
              <a href="/logout/" class="account-dropdown-item logout">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Sign Out
              </a>
            </div>
          </div>
        <?php else: ?>
          <a href="/login/" class="btn-outline-signin">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <span>Sign In</span>
          </a>
        <?php endif; ?>

        <!-- Mobile Right Drawer Button -->
        <button class="mobile-menu-btn" id="openRightDrawerBtn" aria-label="Open Account Portal" style="margin-left: 4px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </button>

        <a href="/subscribe/" class="btn btn-primary">Subscribe</a>
      </div>
    </div>
  </header>

  <!-- Mobile Drawer Overlay -->
  <div class="mobile-drawer-overlay" id="mobileDrawerOverlay"></div>

  <!-- Left Mobile Slide-Out Drawer (Categories & Navigation) -->
  <aside class="mobile-drawer mobile-drawer-left" id="leftMobileDrawer">
    <div class="drawer-header">
      <img src="https://theheadandtale.com//app/nms/visitor/theheadandtale/desktop/assets/images/main_logo.png" alt="Logo" style="height: 28px; width: auto;" />
      <button class="drawer-close-btn" id="closeLeftDrawerBtn">&times;</button>
    </div>
    <div class="drawer-body">
      <ul class="drawer-nav-list">
        <li><a href="/">Home</a></li>
        <li>
          <a href="/fintech-news/">Fintech &amp; Banking</a>
          <ul class="drawer-sub-list">
            <li><a href="/fintech-news/">Digital Banking</a></li>
            <li><a href="/fintech-news/">Payments &amp; Wallets</a></li>
            <li><a href="/fintech-news/">Crypto &amp; Web3</a></li>
          </ul>
        </li>
        <li>
          <a href="/ai-and-emerging-tech/">AI &amp; Emerging Tech</a>
          <ul class="drawer-sub-list">
            <li><a href="/ai-and-emerging-tech/">Generative AI</a></li>
            <li><a href="/ai-and-emerging-tech/">Enterprise AI</a></li>
          </ul>
        </li>
        <li>
          <a href="/money-and-machines/">Startup Ecosystem</a>
          <ul class="drawer-sub-list">
            <li><a href="/money-and-machines/">Venture Capital</a></li>
            <li><a href="/money-and-machines/">Unicorn Tales</a></li>
            <li><a href="/ipo-tales/">IPO Tales</a></li>
          </ul>
        </li>
        <li><a href="/about-us/">About Us</a></li>
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
        <div style="margin-bottom: 24px;">
          <p style="font-weight: 700; color: var(--primary-color); margin-bottom: 12px;">Account Options</p>
          <a href="/manage-subscription/" class="btn btn-outline-secondary btn-block" style="margin-bottom: 10px; display: block; text-align: center;">Manage Subscription</a>
          <a href="/logout-all-devices/" class="btn btn-outline-secondary btn-block" style="margin-bottom: 10px; display: block; text-align: center;">Active Sessions</a>
          <a href="/logout/" class="btn btn-primary btn-block" style="display: block; text-align: center;">Sign Out</a>
        </div>
      <?php else: ?>
        <div style="margin-bottom: 24px; text-align: center;">
          <p style="font-size: 0.95rem; color: var(--text-secondary); margin-bottom: 16px;">Sign in to access your saved articles and subscriber features.</p>
          <a href="/login/" class="btn btn-primary btn-block" style="display: block; width: 100%; text-align: center; margin-bottom: 12px;">Member Sign In</a>
          <a href="/subscribe/" class="btn btn-outline-secondary btn-block" style="display: block; width: 100%; text-align: center;">View Subscriptions</a>
        </div>
      <?php endif; ?>

      <!-- App Download Drawer Promo -->
      <div class="drawer-app-promo">
        <div class="app-promo-icon">🔖</div>
        <h4>Bookmark Stories on Mobile</h4>
        <p>Save articles offline and receive instant news notifications on iOS &amp; Android.</p>
        <a href="/app-download/" class="btn-app-download">Download App &rarr;</a>
      </div>
    </div>
  </aside>

  <!-- Floating Mobile Smart App Banner -->
  <div class="mobile-app-banner" id="mobileAppBanner">
    <div class="app-banner-info">
      <div class="app-banner-icon">📱</div>
      <div class="app-banner-text">
        <strong>Get The Head &amp; Tale App</strong>
        <span>Bookmark stories &amp; read offline</span>
      </div>
    </div>
    <div class="app-banner-actions">
      <a href="/app-download/" class="btn-app-get">GET APP</a>
      <button class="app-banner-dismiss" id="dismissAppBannerBtn" aria-label="Close Banner">&times;</button>
    </div>
  </div>

  <!-- Bookmark App Download Modal Triggered on Bookmark Tap -->
  <div class="search-modal bookmark-app-modal" id="bookmarkAppModal" aria-hidden="true" role="dialog">
    <div class="bookmark-modal-box">
      <button class="close-search-btn bookmark-modal-close" id="closeBookmarkModalBtn">&times;</button>
      <div class="bookmark-modal-icon">🔖</div>
      <h2>Bookmark Stories on Mobile App</h2>
      <p>Save articles offline, build your reading list, and get real-time breaking news alerts on iOS &amp; Android.</p>
      
      <div class="app-store-badge-group">
        <a href="/app-download/" class="app-store-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.3c.64-.78 1.08-1.85.96-2.93-.93.04-2.07.62-2.74 1.4-.6.69-1.13 1.79-.98 2.86 1.04.08 2.12-.55 2.76-1.33z"/></svg>
          <span>App Store</span>
        </a>
        <a href="/app-download/" class="app-store-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M3.6 1.83a2 2 0 0 0-.6 1.44v17.46a2 2 0 0 0 .6 1.44l.08.08L13.4 12.5v-.28L3.68 1.75l-.08.08zm13.11 7.29-3.31-1.9L10.23 10.4l3.17 3.17 3.31-1.9c.94-.54.94-1.42 0-1.96zm-1.04 4.88L12.5 10.83l-8.9 8.9c.31.33.84.38 1.34.1l10.73-5.83zm-8.9-8.9L12.5 13.17l3.17-3.17L4.94 4.17c-.5-.28-1.03-.23-1.34.1l8.17 8.17z"/></svg>
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
          <svg class="search-icon-svg" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
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
      <div id="autocompleteBlock" class="search-section-block" style="display: none;">
        <div class="search-section-title">
          <span>Suggested Topics & Stories</span>
        </div>
        <div id="autocompleteResults" class="autocomplete-list"></div>
      </div>

    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const openBtn = document.getElementById('openSearchBtn');
      const closeBtn = document.getElementById('closeSearchBtn');
      const modal = document.getElementById('searchModal');
      const input = document.getElementById('searchInput');
      const searchForm = document.getElementById('searchForm');
      const recentBlock = document.getElementById('recentSearchesBlock');
      const recentPills = document.getElementById('recentSearchPills');
      const clearRecentBtn = document.getElementById('clearRecentBtn');
      const autoBlock = document.getElementById('autocompleteBlock');
      const autoResults = document.getElementById('autocompleteResults');

      const STORAGE_KEY = 'isw_recent_searches';

      // Load & Render Recent Searches
      const getRecentSearches = function() {
        try {
          return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        } catch (e) {
          return [];
        }
      };

      const saveRecentSearch = function(query) {
        if (!query || !query.trim()) return;
        let searches = getRecentSearches();
        searches = searches.filter(q => q.toLowerCase() !== query.toLowerCase().trim());
        searches.unshift(query.trim());
        if (searches.length > 6) searches = searches.slice(0, 6);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(searches));
        renderRecentSearches();
      };

      const removeRecentSearch = function(query) {
        let searches = getRecentSearches();
        searches = searches.filter(q => q.toLowerCase() !== query.toLowerCase().trim());
        localStorage.setItem(STORAGE_KEY, JSON.stringify(searches));
        renderRecentSearches();
      };

      const renderRecentSearches = function() {
        if (!recentBlock || !recentPills) return;
        const searches = getRecentSearches();
        if (searches.length === 0) {
          recentBlock.style.display = 'none';
          return;
        }
        recentBlock.style.display = 'block';
        recentPills.innerHTML = searches.map(q => `
          <div class="recent-pill" data-query="${q.replace(/"/g, '&quot;')}">
            <span>${q}</span>
            <span class="recent-pill-remove" data-remove="${q.replace(/"/g, '&quot;')}">&times;</span>
          </div>
        `).join('');
      };

      if (clearRecentBtn) {
        clearRecentBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          localStorage.removeItem(STORAGE_KEY);
          renderRecentSearches();
        });
      }

      if (recentPills) {
        recentPills.addEventListener('click', function(e) {
          const removeTarget = e.target.closest('[data-remove]');
          if (removeTarget) {
            e.stopPropagation();
            removeRecentSearch(removeTarget.getAttribute('data-remove'));
            return;
          }
          const pill = e.target.closest('.recent-pill');
          if (pill) {
            const query = pill.getAttribute('data-query');
            if (input) {
              input.value = query;
              if (searchForm) searchForm.submit();
            }
          }
        });
      }

      if (searchForm) {
        searchForm.addEventListener('submit', function() {
          if (input && input.value) {
            saveRecentSearch(input.value);
          }
        });
      }

      // Autocomplete Search Logic
      let debounceTimer = null;
      if (input) {
        input.addEventListener('input', function() {
          const q = input.value.trim();
          clearTimeout(debounceTimer);

          if (q.length < 2) {
            if (autoBlock) autoBlock.style.display = 'none';
            renderRecentSearches();
            return;
          }

          debounceTimer = setTimeout(() => {
            fetch('/?action=autocomplete&q=' + encodeURIComponent(q))
              .then(res => res.json())
              .then(items => {
                if (!autoBlock || !autoResults) return;
                if (!items || items.length === 0) {
                  autoBlock.style.display = 'none';
                  return;
                }
                autoBlock.style.display = 'block';
                if (recentBlock) recentBlock.style.display = 'none';

                autoResults.innerHTML = items.map(item => `
                  <a href="${item.url}" class="autocomplete-item">
                    <span>${item.type === 'tag' ? '#' : ''}${item.title}</span>
                    <span class="autocomplete-badge ${item.type}">${item.type === 'tag' ? 'Topic Tag' : 'Story'}</span>
                  </a>
                `).join('');
              })
              .catch(err => {
                console.error('Autocomplete fetch error:', err);
              });
          }, 200);
        });
      }

      // Popular Quick Tags Listener
      document.querySelectorAll('.popular-tag-item').forEach(tag => {
        tag.addEventListener('click', function() {
          const val = this.getAttribute('data-tag');
          if (input && val) {
            input.value = val;
            if (searchForm) searchForm.submit();
          }
        });
      });

      // ESC key listener to close modal
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
          modal.classList.remove('active');
          modal.setAttribute('aria-hidden', 'true');
          if (autoBlock) autoBlock.style.display = 'none';
        }
      });

      // Account Dropdown Toggle
      const acctDropdown = document.getElementById('userAccountDropdown');
      const acctBtn = document.getElementById('accountMenuBtn');
      if (acctDropdown && acctBtn) {
        acctBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          acctDropdown.classList.toggle('active');
        });
        document.addEventListener('click', function(e) {
          if (!acctDropdown.contains(e.target)) {
            acctDropdown.classList.remove('active');
          }
        });
      }

      // Left & Right Dual Mobile Slide-Out Drawers
      const overlay = document.getElementById('mobileDrawerOverlay');
      const openLeftBtn = document.getElementById('openLeftDrawerBtn');
      const closeLeftBtn = document.getElementById('closeLeftDrawerBtn');
      const leftDrawer = document.getElementById('leftMobileDrawer');

      const openRightBtn = document.getElementById('openRightDrawerBtn');
      const closeRightBtn = document.getElementById('closeRightDrawerBtn');
      const rightDrawer = document.getElementById('rightMobileDrawer');

      const closeAllDrawers = function() {
        if (leftDrawer) leftDrawer.classList.remove('active');
        if (rightDrawer) rightDrawer.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
      };

      if (openLeftBtn && leftDrawer) {
        openLeftBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          closeAllDrawers();
          leftDrawer.classList.add('active');
          if (overlay) overlay.classList.add('active');
        });
      }

      if (closeLeftBtn) closeLeftBtn.addEventListener('click', closeAllDrawers);

      if (openRightBtn && rightDrawer) {
        openRightBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          closeAllDrawers();
          rightDrawer.classList.add('active');
          if (overlay) overlay.classList.add('active');
        });
      }

      if (closeRightBtn) closeRightBtn.addEventListener('click', closeAllDrawers);
      if (overlay) overlay.addEventListener('click', closeAllDrawers);

      // Mobile App Banner Dismissal State (localStorage)
      const appBanner = document.getElementById('mobileAppBanner');
      const dismissBannerBtn = document.getElementById('dismissAppBannerBtn');
      if (appBanner) {
        if (!localStorage.getItem('ht_app_banner_dismissed')) {
          setTimeout(function() {
            appBanner.classList.add('visible');
          }, 1500);
        }
        if (dismissBannerBtn) {
          dismissBannerBtn.addEventListener('click', function() {
            appBanner.classList.remove('visible');
            localStorage.setItem('ht_app_banner_dismissed', 'true');
          });
        }
      }

      // Bookmark App Download Modal
      const bookmarkModal = document.getElementById('bookmarkAppModal');
      const closeBookmarkModalBtn = document.getElementById('closeBookmarkModalBtn');

      document.addEventListener('click', function(e) {
        if (e.target.closest('.trigger-bookmark-app')) {
          e.preventDefault();
          if (bookmarkModal) {
            bookmarkModal.classList.add('active');
            bookmarkModal.setAttribute('aria-hidden', 'false');
          }
        }
      });

      if (closeBookmarkModalBtn && bookmarkModal) {
        closeBookmarkModalBtn.addEventListener('click', function() {
          bookmarkModal.classList.remove('active');
          bookmarkModal.setAttribute('aria-hidden', 'true');
        });
        bookmarkModal.addEventListener('click', function(e) {
          if (e.target === bookmarkModal) {
            bookmarkModal.classList.remove('active');
            bookmarkModal.setAttribute('aria-hidden', 'true');
          }
        });
      }

      document.addEventListener('click', function(e) {
        if (e.target.closest('#openSearchBtn, .open-search-trigger, [href="#openSearch"]')) {
          e.preventDefault();
          if (modal) {
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            renderRecentSearches();
            if (input) input.focus();
          }
        }
      });

      if (closeBtn && modal) {
        closeBtn.addEventListener('click', function() {
          modal.classList.remove('active');
          modal.setAttribute('aria-hidden', 'true');
          if (autoBlock) autoBlock.style.display = 'none';
        });

        modal.addEventListener('click', function(e) {
          if (e.target === modal) {
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            if (autoBlock) autoBlock.style.display = 'none';
          }
        });
      }
    });
  </script>

  <!-- Main Content -->
  <main>