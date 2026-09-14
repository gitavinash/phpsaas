<?php

require_once 'header.php';

// Prepare media variables safely
$mediaTitle = !empty($media_title) ? $media_title : 'Media File';
$rawMediaType = strtolower(trim((string)($media_type ?? 'image')));
$mediaUrlTrimmed = trim((string)($media_url ?? ''));

// Resolve asset URL with local/production fallback
$mediaImgUrl = '';
if (!empty($mediaUrlTrimmed)) {
    if (strpos($mediaUrlTrimmed, 'http://') === 0 || strpos($mediaUrlTrimmed, 'https://') === 0) {
        $mediaImgUrl = $mediaUrlTrimmed;
    } else {
        $localPath = defined('ROOT') ? ROOT . '/' . ltrim($mediaUrlTrimmed, '/') : '';
        if (file_exists($localPath)) {
            $mediaImgUrl = rtrim(SITE_URL, '/') . '/' . ltrim($mediaUrlTrimmed, '/');
        } elseif (defined('SITE_DOMAIN') && (strpos(SITE_DOMAIN, '.local') !== false || SITE_DOMAIN === 'localhost')) {
            $mediaImgUrl = 'https://theheadandtale.com/' . ltrim($mediaUrlTrimmed, '/');
        } else {
            $mediaImgUrl = rtrim(SITE_URL, '/') . '/' . ltrim($mediaUrlTrimmed, '/');
        }
    }
}

// Determine file extension
$fileExtension = !empty($media_ext) ? strtolower($media_ext) : strtolower(pathinfo($mediaUrlTrimmed, PATHINFO_EXTENSION));
if ($fileExtension === '') {
    $fileExtension = ($rawMediaType === 'video') ? 'mp4' : 'jpg';
}

// Check local file stats if available on disk
$localFilePath = defined('ROOT') ? ROOT . '/' . ltrim($mediaUrlTrimmed, '/') : '';
$imageDimensions = '';

if ($localFilePath !== '' && file_exists($localFilePath)) {
    $dimInfo = @getimagesize($localFilePath);
    if (!empty($dimInfo[0]) && !empty($dimInfo[1])) {
        $imageDimensions = $dimInfo[0] . ' × ' . $dimInfo[1] . ' px';
    }
}

$isVideo = (strpos($rawMediaType, 'video') !== false || in_array($fileExtension, ['mp4', 'webm', 'mov', 'mkv', 'ogg']));

// Fetch 3 latest stories for engagement section
$latestStories = [];
$latestStoriesSql = "SELECT n.news_id, n.news_title, n.news_image, n.news_published, l.link_url, c.category_title
    FROM " . TABLE_PREFIX . "news n
    INNER JOIN " . TABLE_PREFIX . "link l
        ON l.link_relation_id = n.news_id
       AND l.link_type = 'news'
       AND l.link_status = 200
       AND l.link_site_id = " . (int)SITE_ID . "
    LEFT JOIN " . TABLE_PREFIX . "category c
        ON c.category_id = n.news_category
       AND c.category_type = 'news'
       AND c.category_site_id = " . (int)SITE_ID . "
    WHERE n.news_site_id = " . (int)SITE_ID . "
      AND n.news_status = 1
      AND n.news_is_public = 1
    ORDER BY n.news_published DESC
    LIMIT 3";

if ($latestStoriesRes = mysqli_query($connection, $latestStoriesSql)) {
    while ($storyRow = mysqli_fetch_assoc($latestStoriesRes)) {
        $rawImg = $storyRow['news_image'] ?? '';
        if (!empty($rawImg) && strpos($rawImg, 'http') !== 0) {
            $localStoryPath = defined('ROOT') ? ROOT . '/' . ltrim($rawImg, '/') : '';
            if (!file_exists($localStoryPath) && defined('SITE_DOMAIN') && (strpos(SITE_DOMAIN, '.local') !== false || SITE_DOMAIN === 'localhost')) {
                $storyRow['img_url'] = 'https://theheadandtale.com/' . ltrim($rawImg, '/');
            } else {
                $storyRow['img_url'] = rtrim(SITE_URL, '/') . '/' . ltrim($rawImg, '/');
            }
        } else {
            $storyRow['img_url'] = $rawImg;
        }
        $latestStories[] = $storyRow;
    }
    mysqli_free_result($latestStoriesRes);
}
?>

<article class="article-detail-page">
    <div class="container article-reading-container">

        <!-- Article / Media Header -->
        <header class="article-header-section">
            <nav class="article-breadcrumb" aria-label="Breadcrumb">
                <a href="/">Home</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Media Attachment</span>
            </nav>

            <h1><?php echo ucwords(htmlspecialchars($mediaTitle)); ?></h1>

            <div class="article-meta-bar">
                <div class="article-meta-left">
                    <span class="article-meta-author">Attachment</span>
                    <span class="separator">•</span>
                    <span class="article-meta-date"><?php echo strtoupper(htmlspecialchars($fileExtension)); ?></span>
                    <?php if (!empty($imageDimensions)): ?>
                        <span class="separator">•</span>
                        <span class="article-meta-date"><?php echo htmlspecialchars($imageDimensions); ?></span>
                    <?php endif; ?>
                </div>
                <div class="article-meta-right">
                    <a href="<?php echo htmlspecialchars($mediaImgUrl); ?>" target="_blank" rel="noopener noreferrer" class="read-time desktop-read-time">
                        <i class="ph ph-arrow-square-out" aria-hidden="true"></i> Open Original
                    </a>
                </div>
            </div>
        </header>

        <!-- Media Display Canvas -->
        <div class="article-hero-media media-hero-media">
            <?php if ($isVideo): ?>
                <video controls playsinline preload="metadata">
                    <source src="<?php echo htmlspecialchars($mediaImgUrl); ?>" type="video/<?php echo htmlspecialchars($fileExtension); ?>">
                    Your browser does not support the video tag.
                </video>
            <?php else: ?>
                <img src="<?php echo htmlspecialchars($mediaImgUrl); ?>"
                    alt="<?php echo htmlspecialchars($mediaTitle); ?>"
                    fetchpriority="high"
                    decoding="async" />
            <?php endif; ?>
        </div>

        <!-- Social Share & Download Bar -->
        <div class="social-share-container">
            <span class="social-share-label">Share Asset</span>
            <div class="social-share-icons">
                <button type="button" class="native-share-btn media-copy-btn" data-url="<?php echo htmlspecialchars($mediaImgUrl); ?>" aria-label="Copy Asset Link">
                    <i class="ph ph-copy" aria-hidden="true"></i>
                    <span>Copy Link</span>
                </button>
                <a href="<?php echo htmlspecialchars($mediaImgUrl); ?>" download class="native-share-btn" aria-label="Download Media">
                    <i class="ph ph-download-simple" aria-hidden="true"></i>
                    <span>Download</span>
                </a>
                <div class="desktop-social-icons">
                    <a href="http://twitter.com/share?text=<?php echo urlencode($mediaTitle); ?>&url=<?php echo urlencode($mediaImgUrl); ?>" target="_blank" class="social-share-btn" aria-label="Share on X">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($mediaImgUrl); ?>&title=<?php echo urlencode($mediaTitle); ?>" target="_blank" class="social-share-btn" aria-label="Share on LinkedIn">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z" />
                        </svg>
                    </a>
                    <a href="https://wa.me/?text=<?php echo urlencode($mediaTitle . ' ' . $mediaImgUrl); ?>" target="_blank" class="social-share-btn" aria-label="Share on WhatsApp">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12.04 2.5c-5.2 0-9.42 4.22-9.42 9.42 0 1.66.43 3.28 1.25 4.7L2.02 21.5l5.06-1.33a9.42 9.42 0 0 0 4.56 1.14c5.2 0 9.42-4.22 9.42-9.42 0-2.52-1.03-4.9-2.9-6.67A9.36 9.36 0 0 0 12.04 2.5zm0 16.9c-1.47 0-2.9-.4-4.15-1.14l-.3-.18-3 .79.81-2.93-.2-.31a7.84 7.84 0 0 1 1.24-9.33A7.83 7.83 0 0 1 12.04 4.2c4.33 0 7.84 3.51 7.84 7.84 0 4.33-3.51 7.84-7.84 7.84zm4.49-5.88c-.24-.12-1.43-.7-1.65-.78-.22-.08-.38-.12-.54.12-.16.24-.63.78-.77.94-.14.16-.28.18-.53.06-.24-.12-1.03-.38-1.96-1.22-.72-.64-1.2-1.43-1.34-1.67-.14-.24-.01-.37.11-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.77-.19-.47-.39-.41-.54-.42-.14-.01-.3-.01-.46-.01-.16 0-.42.06-.64.29-.22.24-.85.83-.85 2.03 0 1.2.87 2.35.99 2.51.12.16 1.71 2.61 4.15 3.66.58.25 1.02.4 1.38.5.58.18 1.11.16 1.53.1.47-.06 1.43-.58 1.64-1.14.21-.56.21-1.04.15-1.14-.06-.1-.22-.17-.47-.29z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Latest Stories Section -->
        <?php if (!empty($latestStories)): ?>
            <div class="related-stories-section">
                <div class="section-header">
                    <h2>Latest Stories</h2>
                    <a href="/" class="view-all">View all stories <span class="arrow">→</span></a>
                </div>
                <div class="related-stories-grid">
                    <?php foreach ($latestStories as $story): ?>
                        <article class="related-story-card">
                            <div class="related-card-img">
                                <a href="<?php echo htmlspecialchars($story['link_url'] ?? '#'); ?>">
                                    <img src="<?php echo htmlspecialchars($story['img_url'] ?? ''); ?>"
                                        alt="<?php echo htmlspecialchars($story['news_title'] ?? ''); ?>"
                                        loading="lazy" />
                                </a>
                            </div>
                            <div class="related-card-content">
                                <?php if (!empty($story['category_title'])): ?>
                                    <span class="tag"><?php echo htmlspecialchars($story['category_title']); ?></span>
                                <?php endif; ?>
                                <h3>
                                    <a href="<?php echo htmlspecialchars($story['link_url'] ?? '#'); ?>">
                                        <?php echo htmlspecialchars($story['news_title'] ?? ''); ?>
                                    </a>
                                </h3>
                                <span class="date">
                                    <?php echo !empty($story['news_published']) ? date('M d, Y', strtotime($story['news_published'])) : ''; ?>
                                </span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</article>

<script src="<?php echo USER_JS; ?>media.min.js"></script>

<?php require_once 'footer.php'; ?>