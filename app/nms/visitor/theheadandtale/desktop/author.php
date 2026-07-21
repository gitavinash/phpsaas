<?php

require_once 'header.php';

?>
<section>
    <div class="row">
        <div class="container">
            <div class="left-container">
                <!-- Author Card at Top -->
                <?php if (isset($authorInfo) && $authorInfo) { ?>
                    <div class="author-info-card"
                        style="display:flex;align-items:center;padding:28px 36px;background:#fff;border-radius:16px;box-shadow:0 6px 24px 0 rgba(0,0,0,0.07);gap:32px;min-height:120px;">
                        <div style="flex-shrink:0;">
                            <?php if (!empty($authorInfo['profile_image'])) { ?>
                                <img src="<?php echo htmlspecialchars($authorInfo['profile_image']); ?>"
                                    alt="<?php echo htmlspecialchars($authorInfo['profile_display_name'] ?? $authorInfo['user_username']); ?>"
                                    style="width:104px;height:104px;border-radius:50%;object-fit:cover;border:3px solid #e0e0e0;background:#f3f3f3;box-shadow:0 2px 8px 0 rgba(0,0,0,0.06);">
                            <?php } else { ?>
                                <div
                                    style="width:104px;height:104px;border-radius:50%;background:#e0e0e0;display:flex;align-items:center;justify-content:center;font-size:2.7rem;color:#aaa;box-shadow:0 2px 8px 0 rgba(0,0,0,0.06);">
                                    <span><?php echo strtoupper(substr($authorInfo['profile_display_name'] ?? $authorInfo['user_username'], 0, 1)); ?></span>
                                </div>
                            <?php } ?>
                        </div>
                        <div>
                            <h2
                                style="margin:0 0 8px 0;font-size:2.2rem;line-height:1.1;font-weight:600;color:#222;word-break:break-word;">
                                <?php echo htmlspecialchars($authorInfo['profile_display_name'] ?? $authorInfo['user_username']); ?>
                            </h2>
                            <style>
                                .author-social-row {
                                    display: flex;
                                    gap: 18px;
                                    margin-top: 12px;
                                    flex-wrap: nowrap;
                                    justify-content: flex-start;
                                }

                                .author-social-row a {
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    width: 36px;
                                    height: 36px;
                                    border-radius: 50%;
                                    /* background: #f0f0f0;
                            color: #666; */
                                    text-decoration: none;
                                    font-weight: 600;
                                    transition: background 0.2s;
                                }

                                .author-social-row a svg {
                                    filter: grayscale(1);
                                    transition: filter 0.2s;
                                }

                                .author-social-row a:hover svg {
                                    filter: none;
                                }
                            </style>
                            <?php
                            // Social media icons (replace username)
                            $socials = [
                                'facebook' => '<svg width="20" height="20" fill="#1877f3" viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.595 0 0 .592 0 1.326v21.348C0 23.408.595 24 1.325 24h11.495v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.918.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.406 24 24 23.408 24 22.674V1.326C24 .592 23.406 0 22.675 0"/></svg>',
                                'twitter' => '<svg width="20" height="20" fill="#1da1f2" viewBox="0 0 24 24"><path d="M24 4.557a9.93 9.93 0 0 1-2.828.775 4.932 4.932 0 0 0 2.165-2.724c-.951.564-2.005.974-3.127 1.195a4.92 4.92 0 0 0-8.384 4.482C7.691 8.095 4.066 6.13 1.64 3.161c-.542.929-.856 2.01-.857 3.17 0 2.188 1.115 4.117 2.823 5.247a4.904 4.904 0 0 1-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.936 4.936 0 0 1-2.224.084c.627 1.956 2.444 3.377 4.6 3.417A9.867 9.867 0 0 1 0 21.543a13.94 13.94 0 0 0 7.548 2.209c9.058 0 14.009-7.513 14.009-14.009 0-.213-.005-.425-.014-.636A10.012 10.012 0 0 0 24 4.557z"/></svg>',
                                'linkedin' => '<svg width="20" height="20" fill="#0077b5" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.327-.027-3.037-1.849-3.037-1.851 0-2.132 1.445-2.132 2.939v5.667H9.358V9h3.414v1.561h.049c.476-.899 1.637-1.849 3.37-1.849 3.602 0 4.267 2.368 4.267 5.455v6.285zM5.337 7.433a2.062 2.062 0 1 1 0-4.124 2.062 2.062 0 0 1 0 4.124zM7.119 20.452H3.554V9h3.565v11.452zM22.225 0H1.771C.792 0 0 .771 0 1.723v20.549C0 23.229.792 24 1.771 24h20.451C23.2 24 24 23.229 24 22.271V1.723C24 .771 23.2 0 22.225 0z"/></svg>',
                                'youtube' => '<svg width="20" height="20" fill="#ff0000" viewBox="0 0 24 24"><path d="M23.498 6.186a2.994 2.994 0 0 0-2.107-2.117C19.163 3.5 12 3.5 12 3.5s-7.163 0-9.391.569A2.994 2.994 0 0 0 .502 6.186C0 8.414 0 12 0 12s0 3.586.502 5.814a2.994 2.994 0 0 0 2.107 2.117C4.837 20.5 12 20.5 12 20.5s7.163 0 9.391-.569a2.994 2.994 0 0 0 2.107-2.117C24 15.586 24 12 24 12s0-3.586-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
                            ];
                            $socialLinks = [];
                            if (!empty($authorInfo['social_links'])) {
                                if (is_array($authorInfo['social_links'])) {
                                    $socialLinks = $authorInfo['social_links'];
                                } else {
                                    $socialLinks = json_decode($authorInfo['social_links'], true);
                                    if (!is_array($socialLinks)) $socialLinks = [];
                                }
                            }
                            $hasAny = false;
                            foreach ($socials as $platform => $icon) {
                                if (!empty($socialLinks[$platform])) {
                                    $hasAny = true;
                                    break;
                                }
                            }
                            if ($hasAny) {
                                echo '<div class="author-social-row">';
                                foreach ($socials as $platform => $icon) {
                                    $url = $socialLinks[$platform] ?? '';
                                    if (!empty($url)) {
                                        echo '<a href="' . htmlspecialchars($url) . '" target="_blank" title="' . ucfirst($platform) . '">' . $icon . '</a>';
                                    }
                                }
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                <?php } ?>
                <?php

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
                        <p><span>" . (!empty($news_published) ? date('d M Y, h:i A', strtotime($news_published)) : '') .
                            "</span></p>
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
            <?php

            if (SITE_ID == 49) {
                require_once 'sidebar.php';
            }

            ?>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>