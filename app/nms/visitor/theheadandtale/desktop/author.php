<?php

require_once 'header.php';

?>
<section>
    <div class="row">
        <div class="container">
            <div class="left-container">
                <!-- Author Card at Top -->
                <?php if (isset($authorInfo) && $authorInfo): ?>
                <div class="author-info-card"
                    style="display:flex;align-items:center;padding:28px 36px;background:#fff;border-radius:16px;box-shadow:0 6px 24px 0 rgba(0,0,0,0.07);gap:32px;min-height:120px;">
                    <div style="flex-shrink:0;">
                        <?php if (!empty($authorInfo['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($authorInfo['profile_image']); ?>"
                            alt="<?php echo htmlspecialchars($authorInfo['profile_display_name'] ?? $authorInfo['user_username']); ?>"
                            style="width:104px;height:104px;border-radius:50%;object-fit:cover;border:3px solid #e0e0e0;background:#f3f3f3;box-shadow:0 2px 8px 0 rgba(0,0,0,0.06);">
                        <?php else: ?>
                        <div
                            style="width:104px;height:104px;border-radius:50%;background:#e0e0e0;display:flex;align-items:center;justify-content:center;font-size:2.7rem;color:#aaa;box-shadow:0 2px 8px 0 rgba(0,0,0,0.06);">
                            <span><?php echo strtoupper(substr($authorInfo['profile_display_name'] ?? $authorInfo['user_username'], 0, 1)); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <h2
                            style="margin:0 0 8px 0;font-size:2.2rem;line-height:1.1;font-weight:600;color:#222;word-break:break-word;">
                            <?php echo htmlspecialchars($authorInfo['profile_display_name'] ?? $authorInfo['user_username']); ?>
                        </h2>
                        <?php if (!empty($authorInfo['user_username'])): ?>
                        <div style="color:#b0b0b0;font-size:15px;margin-bottom:10px;">
                            @<?php echo htmlspecialchars($authorInfo['user_username']); ?>
                        </div>
                        <?php endif; ?>
                        <div style="color:#ff6600;font-size:16px;font-weight:500;margin-top:2px;letter-spacing:0.2px;">
                            <?php
                                $newsCount = defined('TOTAL_RESULT') ? (int)TOTAL_RESULT : (isset($total_result) ? (int)$total_result : 0);
                                echo $newsCount . ' Article' . ($newsCount === 1 ? '' : 's') . ' Published';
                                ?>
                        </div>
                    </div>
                </div>
                <?php endif;

                if (!empty($authorArticles)) {
                    foreach ($authorArticles as $article) {
                        $news_title = $article['news_title'] ?? '';
                        $news_image = $article['news_image'] ?? '';
                        $news_paid = $article['news_paid'] ?? 0;
                        $news_published = $article['news_published'] ?? '';
                        $meta_description = $article['meta_description'] ?? '';
                        $link_url = $article['link_url'] ?? '#';
                        echo "<div class='news_box'><a href='{$link_url}'>
                                <div class='feature_image'>
                                    <img src='{$news_image}' alt='" . str_replace("'", "", str_replace('"', '', $news_title)) . "'>
                                    " . (($news_paid == 1) ? "<img src='" . USER_IMAGES . "premium.png' />" : "") . "
                                </div>
                                <div class='news_intro'>
                                    <h2>{$news_title}</h2>
                                    <p><span>" . (!empty($news_published) ? date('d M Y, H:i A', strtotime($news_published)) : '') . "</span></p>
                                    <p>{$meta_description}</p>
                                </div>
                            </a></div>";
                    }
                } else {
                    echo '<p>No articles found for this author.</p>';
                }
                ?>
                <?php require_once 'pagination.php'; ?>
            </div>
            <?php require_once 'sidebar.php'; ?>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>