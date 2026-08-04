<?php

// --- AJAX Handler for Infinite Scroll Related & Next News ---
if (isset($_GET['ajax_related']) && !empty($_GET['news_id'])) {
    if (defined('MENU_FUNCTIONS')) {
        require_once MENU_FUNCTIONS;
    }
    if (defined('DATA_FUNCTIONS')) {
        require_once DATA_FUNCTIONS;
    }

    $seedNewsId = (int)$_GET['news_id'];

    // Parse exclude IDs
    $excludeRaw = isset($_GET['exclude']) ? $_GET['exclude'] : '';
    $excludeArr = array_filter(array_map('intval', explode(',', $excludeRaw)));
    if (!in_array($seedNewsId, $excludeArr)) {
        $excludeArr[] = $seedNewsId;
    }
    $excludeCsv = implode(',', array_unique($excludeArr));

    // Active subscription & user check
    $activeSubscription = false;
    $currentUserId = 0;
    if (isset($_COOKIE['user_data'])) {
        require_once LOGIN_CHECK;
        $userSaltKey = base64_decode($_COOKIE['user_data']);
        if ($checkUser = mysqli_query($connection, "SELECT user_id, subscription_status FROM " . TABLE_PREFIX . "user LEFT JOIN " . TABLE_PREFIX . "subscription ON user_id = subscription_user_id WHERE user_salt_key = '{$userSaltKey}' AND CURDATE() <= subscription_expiry AND subscription_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkUser) != 0) {
                $uData = mysqli_fetch_assoc($checkUser);
                $currentUserId = (int)$uData['user_id'];
                if ($uData['subscription_status'] == 1) {
                    $activeSubscription = true;
                }
            }
        }
    }

    $nextNewsId = 0;

    // Phase 1: Similar articles by tag match
    $similarResult = similarArticles($connection, $seedNewsId, 'news', 50, 0);
    if (!empty($similarResult['articles'])) {
        foreach ($similarResult['articles'] as $candidate) {
            $cId = (int)$candidate['id'];
            if (!in_array($cId, $excludeArr)) {
                $nextNewsId = $cId;
                break;
            }
        }
    }

    // Phase 2: Same Category fallback if tag matches exhausted
    if ($nextNewsId == 0) {
        $catRes = mysqli_query($connection, "SELECT news_category FROM " . TABLE_PREFIX . "news WHERE news_id = {$seedNewsId} AND news_site_id = " . SITE_ID . " LIMIT 1");
        if ($catRes && mysqli_num_rows($catRes) > 0) {
            $catRow = mysqli_fetch_assoc($catRes);
            $seedCatId = (int)$catRow['news_category'];

            $catNewsRes = mysqli_query($connection, "
                SELECT news_id FROM " . TABLE_PREFIX . "news 
                INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = news_id AND link_type = 'news' AND link_status = 200 AND link_site_id = " . SITE_ID . "
                WHERE news_category = {$seedCatId} AND news_site_id = " . SITE_ID . " AND news_id NOT IN ({$excludeCsv})
                ORDER BY link_published DESC LIMIT 1
            ");
            if ($catNewsRes && mysqli_num_rows($catNewsRes) > 0) {
                $catNewsRow = mysqli_fetch_assoc($catNewsRes);
                $nextNewsId = (int)$catNewsRow['news_id'];
            }
        }
    }

    // Phase 3: Global latest news fallback if same category exhausted
    if ($nextNewsId == 0) {
        $latestRes = mysqli_query($connection, "
            SELECT news_id FROM " . TABLE_PREFIX . "news 
            INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = news_id AND link_type = 'news' AND link_status = 200 AND link_site_id = " . SITE_ID . "
            WHERE news_site_id = " . SITE_ID . " AND news_id NOT IN ({$excludeCsv})
            ORDER BY link_published DESC LIMIT 1
        ");
        if ($latestRes && mysqli_num_rows($latestRes) > 0) {
            $latestRow = mysqli_fetch_assoc($latestRes);
            $nextNewsId = (int)$latestRow['news_id'];
        }
    }

    // Fetch details for the next article
    if ($nextNewsId > 0) {
        $qNews = mysqli_query($connection, "
            SELECT news_id, news_owner_id, news_title, news_image, news_description, news_paid, news_category, category_title, link_url, link_published, link_relation_id, profile_display_name, profile_image 
            FROM " . TABLE_PREFIX . "news 
            INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = news_id AND link_type = 'news' AND link_site_id = " . SITE_ID . " 
            INNER JOIN " . TABLE_PREFIX . "category ON news_category = category_id AND category_site_id = " . SITE_ID . " 
            INNER JOIN " . TABLE_PREFIX . "profile ON news_owner_id = profile_user_id 
            WHERE news_id = '{$nextNewsId}' AND news_site_id = " . SITE_ID . " LIMIT 1
        ");

        if ($qNews && mysqli_num_rows($qNews) > 0) {
            $rData = mysqli_fetch_assoc($qNews);
            extract($rData);

            $authorDetails = newsAuthorInfo(
                $connection,
                $news_id,
                [
                    'id'    => $news_owner_id,
                    'name'  => $profile_display_name,
                    'image' => $profile_image,
                ]
            );

            $showFullNews = false;
            $showSubscriptionSection = false;
            $showSignUpSection = false;

            if (isset($_COOKIE['user_data'])) {
                if ($activeSubscription == true) {
                    $showFullNews = true;
                } else {
                    if ($news_paid == 1) {
                        if ($checkPurchase = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id WHERE order_product_id={$link_relation_id} AND order_status=1 AND order_user_id=" . (int)$currentUserId . " AND order_site_id=" . SITE_ID . " AND transaction_status=1 AND transaction_site_id=" . SITE_ID)) {
                            if (mysqli_num_rows($checkPurchase) != 0) {
                                $showFullNews = true;
                            } else {
                                $showSubscriptionSection = true;
                            }
                        }
                    } else {
                        $showFullNews = true;
                    }
                }
            } else {
                if ($news_paid == 1) {
                    $showSignUpSection = true;
                } else {
                    $showFullNews = false;
                }
            }

            $userAccessType = base64_decode(((isset($_COOKIE['user_data'])) ? $_COOKIE['user_data'] : ''));
            if ($userAccessType == 'author' || $userAccessType == 'administrator') {
                $showFullNews = true;
                $showSignUpSection = false;
                $showSubscriptionSection = false;
            }

            // Append site_version to link_url if present
            $siteVersionParam = (isset($_GET['site_version']) && $_GET['site_version'] !== '') ? $_GET['site_version'] : '';
            if (!empty($siteVersionParam) && strpos($link_url, 'site_version=') === false) {
                $link_url .= (strpos($link_url, '?') !== false ? '&' : '?') . 'site_version=' . urlencode($siteVersionParam);
            }

            $excludeArr[] = $nextNewsId;
            $updatedExcludeCsv = implode(',', array_unique($excludeArr));

            ob_start();
?>
            <div class="next-story-divider">
                <span class="divider-label"><i class="ph ph-arrow-down"></i> Next Story</span>
            </div>

            <div class="article-body-container related-infinite-article" data-article-id="<?php echo $news_id; ?>">
                <div class="article-hero-img" style="background-image: url(<?php echo htmlspecialchars($news_image); ?>);">
                    <a href="#" id="bookmarkNews_<?php echo $news_id; ?>" data-id="<?php echo $news_id; ?>" class="icon-btn bookmark-floating-btn" aria-label="Bookmark">
                        <i class="ph ph-bookmark-simple"></i>
                    </a>
                </div>

                <div class="article-content" style="padding: 24px; position: relative">
                    <div class="category-tag"><?php echo htmlspecialchars($category_title); ?></div>
                    <h2 class="article-headline" style="font-family: var(--font-serif); font-size: 1.8rem; line-height: 1.3; margin-bottom: 16px;">
                        <a href="<?php echo htmlspecialchars($link_url); ?>" style="color: inherit; text-decoration: none;">
                            <?php echo htmlspecialchars($news_title); ?>
                        </a>
                    </h2>

                    <div class="article-byline">
                        <div class="author-avatar<?php echo (count($authorDetails['authors']) > 1) ? ' multi-authors' : ''; ?>">
                            <?php if (count($authorDetails['authors']) > 1) { ?>
                                <?php foreach ($authorDetails['authors'] as $author) { ?>
                                    <div class="avatar-circle" title="<?php echo htmlspecialchars($author['name']); ?>">
                                        <?php if (!empty($author['image'])) { ?>
                                            <img src="<?php echo htmlspecialchars($author['image']); ?>" alt="<?php echo htmlspecialchars($author['name']); ?>">
                                        <?php } else { ?>
                                            <span><?php echo htmlspecialchars($author['character']); ?></span>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <?php if (!empty($authorDetails['primary']['image'])) { ?>
                                    <img src="<?php echo htmlspecialchars($authorDetails['primary']['image']); ?>" alt="<?php echo htmlspecialchars($authorDetails['primary']['name']); ?>">
                                <?php } else { ?>
                                    <span><?php echo htmlspecialchars($authorDetails['primary']['character']); ?></span>
                                <?php } ?>
                            <?php } ?>
                        </div>

                        <div class="author-info">
                            <span class="author-name">
                                <?php foreach ($authorDetails['authors'] as $index => $author) { ?>
                                    <?php if ($index > 0) {
                                        echo ', ';
                                    } ?>
                                    <a href="<?php echo htmlspecialchars($author['link']); ?>">
                                        <?php echo htmlspecialchars($author['name']); ?>
                                    </a>
                                <?php } ?>
                            </span>

                            <span class="byline-sep"></span>

                            <span class="author-date">
                                <?php echo date('M d, Y', strtotime($link_published)); ?>
                                &bull;
                                <?php echo timeToRead($news_description); ?>
                            </span>
                        </div>
                    </div>

                    <div class="editorial-body">
                        <p class="drop-cap">
                            <?php
                            if ($showFullNews == true) {
                                echo removeInlineStyles($news_description, true);
                            } else {
                                echo removeInlineStyles(trimParagraph($news_description, 300), true);
                            }
                            ?>
                        </p>

                        <?php
                        if ($showSignUpSection == true || $showSubscriptionSection == true) {
                            require 'subscription-now-section.php';
                        }
                        ?>
                    </div>
                </div>
            </div>
<?php
            $renderedHtml = ob_get_clean();

            header('Content-Type: application/json');
            echo json_encode([
                'success'    => true,
                'html'       => $renderedHtml,
                'url'        => $link_url,
                'title'      => $news_title,
                'id'         => $news_id,
                'excludeCsv' => $updatedExcludeCsv
            ]);
            exit();
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No more news articles']);
    exit();
}

require_once 'header.php';

$authorDetails = newsAuthorInfo(
    $connection,
    $news_id,
    [
        'id'    => $news_owner_id,
        'name'  => $profile_display_name,
        'image' => $profile_image,
    ]
);

?>
<!-- SINGLE ARTICLE PAGE CONTENT -->
<div id="view-article" class="page-view active">
    <div class="article-body-container">
        <div
            class="article-hero-img" style="background-image: url(<?php echo $news_image ?>);">
            <a href="#" id="bookmarkNews" data-id="<?php echo $news_id ?>"
                class="icon-btn bookmark-floating-btn"
                aria-label="Bookmark">
                <i class="ph ph-bookmark-simple"></i>
            </a>
        </div>

        <div
            class="article-content"
            style="padding: 24px; position: relative">
            <div class="category-tag"><?php echo $category_title ?></div>
            <h1
                class="article-headline"
                style="
                  font-family: var(--font-serif);
                  font-size: 1.8rem;
                  line-height: 1.3;
                  margin-bottom: 16px;
                ">
                <?php echo $news_title ?>
            </h1>

            <div class="article-byline">

                <div class="author-avatar<?php echo (count($authorDetails['authors']) > 1) ? ' multi-authors' : ''; ?>">
                    <?php if (count($authorDetails['authors']) > 1) { ?>
                        <?php foreach ($authorDetails['authors'] as $author) { ?>
                            <div class="avatar-circle" title="<?php echo htmlspecialchars($author['name']); ?>">
                                <?php if (!empty($author['image'])) { ?>
                                    <img src="<?php echo htmlspecialchars($author['image']); ?>" alt="<?php echo htmlspecialchars($author['name']); ?>">
                                <?php } else { ?>
                                    <span><?php echo htmlspecialchars($author['character']); ?></span>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if (!empty($authorDetails['primary']['image'])) { ?>
                            <img src="<?php echo htmlspecialchars($authorDetails['primary']['image']); ?>" alt="<?php echo htmlspecialchars($authorDetails['primary']['name']); ?>">
                        <?php } else { ?>
                            <span><?php echo htmlspecialchars($authorDetails['primary']['character']); ?></span>
                        <?php } ?>
                    <?php } ?>
                </div>

                <div class="author-info">

                    <span class="author-name">

                        <?php foreach ($authorDetails['authors'] as $index => $author) { ?>

                            <?php if ($index > 0) { ?>
                                ,
                            <?php } ?>

                            <a href="<?php echo $author['link']; ?>">
                                <?php echo htmlspecialchars($author['name']); ?>
                            </a>

                        <?php } ?>

                    </span>

                    <span class="byline-sep"></span>

                    <span class="author-date">
                        <?php echo date('M d, Y', strtotime($link_published)); ?>
                        &bull;
                        <?php echo timeToRead($news_description); ?>
                    </span>

                </div>

            </div>

            <div class="editorial-body">
                <p class="drop-cap">

                    <?php


                    $showFullNews = false;
                    $showSubscriptionSection = false;
                    $showSignUpSection = false;

                    if (isset($_COOKIE['user_data'])) {
                        if ($activeSubscription == true) {
                            $showFullNews = true;
                        } else {
                            if ($news_paid == 1) {

                                // Check news is paid?

                                if ($checkPurchase = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id WHERE order_product_id={$link_relation_id} AND order_status=1 AND order_user_id=" . USER_ID . " AND order_site_id=" . SITE_ID . " AND transaction_status=1 AND transaction_site_id=" . SITE_ID)) {
                                    if (mysqli_num_rows($checkPurchase) != 0) {
                                        $showFullNews = true;
                                    } else {
                                        $showSubscriptionSection = true;
                                    }
                                }
                            } else {
                                $showFullNews = true;
                            }
                        }
                    } else {
                        if ($news_paid == 1) {
                            $showSignUpSection = true;
                        } else {
                            $showFullNews = false;
                        }
                    }

                    $userAccessType = base64_decode(((isset($_COOKIE['user_data'])) ? $_COOKIE['user_data'] : ''));

                    if ($userAccessType == 'author' || $userAccessType == 'administrator') {
                        $showFullNews = true;
                        $showSignUpSection = false;
                        $showSubscriptionSection = false;
                    }

                    if ($showFullNews == true) {
                        echo removeInlineStyles($news_description, true);
                    } else {
                        echo removeInlineStyles(trimParagraph($news_description, 300), true);
                    }

                    ?>
                </p>

                <?php

                if ($showSignUpSection == true) {
                    require_once 'subscription-now-section.php';
                }
                if ($showSubscriptionSection == true) {
                    require_once 'subscription-now-section.php';
                }

                ?>
            </div>
        </div>
    </div>

    <!-- INFINITE SCROLL RELATED NEWS SECTION -->
    <div id="infinite-related-wrapper" data-current-id="<?php echo $news_id; ?>">
        <div id="infinite-related-container"></div>
        <div id="infinite-scroll-loader" class="infinite-scroll-loader">
            <div class="spinner"></div>
            <span>Loading next story...</span>
        </div>
        <div id="infinite-scroll-end" class="infinite-scroll-end" style="display: none;">
            <p>You've reached the end of stories.</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const wrapper = document.getElementById("infinite-related-wrapper");
        const container = document.getElementById("infinite-related-container");
        const loader = document.getElementById("infinite-scroll-loader");
        const endMsg = document.getElementById("infinite-scroll-end");

        if (!wrapper || !container || !loader) return;

        const seedNewsId = wrapper.getAttribute("data-current-id");
        let excludeIds = [seedNewsId];
        let isLoading = false;
        let isEnded = false;

        // Detect actual scrollable app container
        const scrollContainer = document.getElementById("main-content") || document.querySelector(".app-body") || window;

        function fetchNextStory() {
            if (isLoading || isEnded) return;
            isLoading = true;
            loader.style.display = "flex";

            const fetchUrl = window.location.pathname + "?ajax_related=1&news_id=" + seedNewsId + "&exclude=" + excludeIds.join(",");

            fetch(fetchUrl, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    loader.style.display = "none";
                    isLoading = false;

                    if (data.success && data.html) {
                        const articleDiv = document.createElement("div");
                        articleDiv.className = "infinite-story-item";
                        articleDiv.innerHTML = data.html;
                        container.appendChild(articleDiv);

                        if (data.id) {
                            excludeIds.push(data.id);
                        }
                        if (data.excludeCsv) {
                            excludeIds = data.excludeCsv.split(",");
                        }

                        // Track URL and document title updates when article is scrolled into view
                        observeStoryScroll(articleDiv, data.url, data.title);

                        // Check if scroll container is still near bottom after append
                        setTimeout(checkScrollPosition, 200);
                    } else {
                        isEnded = true;
                        if (endMsg) endMsg.style.display = "block";
                    }
                })
                .catch(err => {
                    console.error("Infinite scroll error:", err);
                    loader.style.display = "none";
                    isLoading = false;
                });
        }

        function checkScrollPosition() {
            if (isLoading || isEnded) return;
            const containerHeight = (scrollContainer === window) ? window.innerHeight : scrollContainer.clientHeight;
            const scrollTop = (scrollContainer === window) ? window.scrollY : scrollContainer.scrollTop;
            const scrollHeight = (scrollContainer === window) ? document.documentElement.scrollHeight : scrollContainer.scrollHeight;

            if (scrollHeight - (scrollTop + containerHeight) < 800) {
                fetchNextStory();
            }
        }

        // Attach scroll event listeners
        if (scrollContainer !== window) {
            scrollContainer.addEventListener("scroll", checkScrollPosition, {
                passive: true
            });
        }
        window.addEventListener("scroll", checkScrollPosition, {
            passive: true
        });

        // IntersectionObserver with correct scrollContainer root
        const scrollObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isLoading && !isEnded) {
                    fetchNextStory();
                }
            });
        }, {
            root: (scrollContainer !== window) ? scrollContainer : null,
            rootMargin: "600px 0px 600px 0px"
        });

        scrollObserver.observe(loader);

        // Initial check on page load
        setTimeout(checkScrollPosition, 300);

        // Observe active story to dynamically update URL and document title
        const activeStoryObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && entry.intersectionRatio >= 0.25) {
                    let targetUrl = entry.target.getAttribute("data-url");
                    const targetTitle = entry.target.getAttribute("data-title");

                    const urlParams = new URLSearchParams(window.location.search);
                    const siteVersion = urlParams.get("site_version");
                    if (siteVersion && targetUrl && !targetUrl.includes("site_version=")) {
                        targetUrl += (targetUrl.includes("?") ? "&" : "?") + "site_version=" + encodeURIComponent(siteVersion);
                    }

                    if (targetUrl && (window.location.pathname + window.location.search) !== targetUrl && window.location.href !== targetUrl) {
                        history.replaceState(null, targetTitle, targetUrl);
                        document.title = targetTitle;
                    }
                }
            });
        }, {
            root: (scrollContainer !== window) ? scrollContainer : null,
            threshold: 0.25
        });

        function observeStoryScroll(el, url, title) {
            el.setAttribute("data-url", url);
            el.setAttribute("data-title", title);
            activeStoryObserver.observe(el);
        }
    });
</script>

<?php

require_once 'footer.php';

?>