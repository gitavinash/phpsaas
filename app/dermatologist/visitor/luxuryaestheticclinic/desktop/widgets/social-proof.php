<!-- 8. INSTAGRAM REELS & YOUTUBE SHORTS -->
<section class="social-proof-section" id="social-proof">
    <div class="container text-center reveal">
        <span class="section-tag">Watch Us In Action</span>
        <h2>Real Results & Expert Insights</h2>
        <p class="max-width-600">
            Step behind the scenes with live treatment journeys, patient diaries,
            and cosmetic advice directly from Dr. Elizabeth Reed:
        </p>
    </div>

    <div class="carousel-outer reveal">
        <!-- Carousel Slider Controls -->
        <button class="carousel-btn btn-prev" aria-label="Slide left">
            <i data-lucide="chevron-left" style="width: 24px; height: 24px"></i>
        </button>
        <button class="carousel-btn btn-next" aria-label="Slide right">
            <i data-lucide="chevron-right" style="width: 24px; height: 24px"></i>
        </button>

        <div class="carousel-container">
            <div class="carousel-track">

                <?php
                if (!function_exists('getLocalVideoUrl')) {
                    function getLocalVideoUrl($url)
                    {
                        if (empty($url)) return '';
                        if (strpos($url, '.mp4') !== false || strpos($url, '/upload/') !== false) {
                            return $url;
                        }

                        $id = '';
                        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
                            if (preg_match('/(?:shorts\/|v=|be\/|embed\/)([^?&#]+)/', $url, $matches)) {
                                $id = $matches[1];
                            }
                        } elseif (strpos($url, 'instagram.com') !== false) {
                            if (preg_match('/(?:reel|reels|p)\/([^\\/?#&]+)/', $url, $matches)) {
                                $id = $matches[1];
                            }
                        }

                        if (!empty($id)) {
                            return "/upload/hb.local/social_proof/" . $id . ".mp4";
                        }

                        return $url;
                    }
                }

                $socialProoofs = getSocialProofsData($connection, 'social_proof', 5, true);
                if (!empty($socialProoofs)) {
                    foreach ($socialProoofs as $proof) {
                        $proofTitle = htmlspecialchars($proof['title']);
                        $proofImage = htmlspecialchars($proof['image']);
                        $proofType = strtolower($proof['sp_type'] ?? 'instagram');
                        $proofUrl = htmlspecialchars($proof['link_url']);
                        $videoUrl = htmlspecialchars($proof['sp_video_url'] ?? '');
                        $resolvedVideoUrl = getLocalVideoUrl($videoUrl);
                ?>
                        <div class="social-card"
                            data-video-url="<?= $resolvedVideoUrl ?>"
                            data-video-type="<?= $proofType ?>"
                            data-title="<?= $proofTitle ?>"
                            data-description="<?= htmlspecialchars($proof['description'] ?? '') ?>"
                            data-ref-type="<?= htmlspecialchars($proof['sp_ref_type'] ?? '') ?>"
                            data-ref-url="<?= htmlspecialchars($proof['ref_url'] ?? '') ?>"
                            data-ref-title="<?= htmlspecialchars($proof['ref_title'] ?? '') ?>"
                            data-ref-image="<?= htmlspecialchars($proof['ref_image'] ?? '') ?>"
                            data-ref-description="<?= htmlspecialchars($proof['ref_description'] ?? '') ?>">
                            <div class="social-video-thumb">
                                <img src="<?= $proofImage ?>" alt="<?= $proofTitle ?>" class="social-video-poster" />
                                <video
                                    data-src="<?= $resolvedVideoUrl ?>"
                                    poster="<?= $proofImage ?>"
                                    class="social-video-img"
                                    loop
                                    muted
                                    playsinline
                                    style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; object-fit: cover; border: none; cursor: pointer;"></video>
                            </div>
                            <div class="social-card-content">
                                <h4 class="social-card-title">
                                    <a href="<?= $proofUrl ?>" style="color: inherit; text-decoration: none;">
                                        <?= $proofTitle ?>
                                    </a>
                                </h4>
                                <?php if (!empty($proof['ref_url'])):
                                    $refName = !empty($proof['ref_title']) ? htmlspecialchars($proof['ref_title']) : 'Related Details';
                                ?>
                                    <div class="social-card-ref">
                                        <a href="<?= htmlspecialchars($proof['ref_url']) ?>" class="ref-link">
                                            <i data-lucide="sparkles" style="width: 12px; height: 12px; margin-right: 6px; vertical-align: middle;"></i>
                                            <span><?= $refName ?></span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                <?php
                    }
                };
                ?>
            </div>
        </div>
    </div>

    <!-- CTA block -->
    <div class="container social-cta-box reveal">
        <a
            href="https://instagram.com"
            class="btn btn-secondary"
            target="_blank"
            rel="noopener">
            <i
                data-lucide="instagram"
                style="width: 16px; margin-right: 8px; vertical-align: middle"></i>
            Follow Instagram
        </a>
        <a
            href="https://youtube.com"
            class="btn btn-secondary"
            target="_blank"
            rel="noopener">
            <i
                data-lucide="youtube"
                style="width: 16px; margin-right: 8px; vertical-align: middle"></i>
            Subscribe YouTube
        </a>
    </div>
</section>